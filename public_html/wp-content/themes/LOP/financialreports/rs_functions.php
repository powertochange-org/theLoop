<?php

/*******************************************************************
 * rs_functions.php - Reporting Services functions
 *
 * Author: Jason Brink
 * Create Date: 2008-03-20
 *
 ******************************************************************/

require_once('HTTP/Request.php');  // PEAR Library for making HTTP Requests


/*******************************************************************
 * produceRSReport - This function calls reporting services to 
 *     generate a report, and returns the result directly back to
 *     the client. It even produces a header line if necessary, so
 *     it should be called before producing any output on a page.
 *
 * Parameters:
 *   reportPath - the path to the reporting services report, like
 *       /Donations/13MonthDonationHistory
 *   renderFormat - the format to return the report in. Valid
 *       values are PDF, Excel, or CSV.
 *   reportParams - a key-value hash containing report parameters.
 *       So, if the report expects a parameter like ProjectCode,
 *       you should set $reportParams['ProjectCode'] = '<value>'
 ******************************************************************/
 
$SERVER_FIRST_CHRONICLES = "https://1Chronicles/CallRptServices/CallRpt.aspx";
$SERVER_SQL2012 = "https://1chronicles/CallRptServices2012/CallRpt.aspx";
 
function produceRSReport($reportPath, $renderFormat, $reportParams, $sendData = true, $server = null)
{
global $SERVER_FIRST_CHRONICLES, $SERVER_SQL2012;

  if ($server == null){
    $REPORT_WEB_SERVICE = $SERVER_FIRST_CHRONICLES;
  }
  else{
	$REPORT_WEB_SERVICE = $server;
  }
  
  // Check that the reportPath was set
  if (!isset($reportPath) || ($reportPath == '')) {
    echo "Error: Missing report path";
    return;
  }
  
  // Use reportPath to create a more descriptive filename
  $customfilename = "";
  if (isset($reportParams['ProjectCode'])){
	$customfilename .= $reportParams['ProjectCode']. "_";
  }
  $customfilename .=preg_replace("#/.*?/#","",$reportPath);
	
  if (isset($reportParams['StartDate']) and $reportParams['StartDate'] != "") {
	  $customfilename = $customfilename."-".$reportParams['StartDate'];
  }

  // Check that the render format is valid
  if ($renderFormat != 'PDF' &&
      $renderFormat != 'Excel' &&
      $renderFormat != 'EXCELOPENXML' &&
      $renderFormat != 'CSV' &&
      $renderFormat != 'HTML4.0' && 
      $renderFormat != 'MHTML') {
    echo "Error: Render format is not valid";
    return;
  }

  // Set up the request
  $request = new HTTP_Request($REPORT_WEB_SERVICE);
  $request->setMethod(HTTP_REQUEST_METHOD_POST);

  $request->addPostData('_reportPath', $reportPath);
  $request->addPostData('_renderFormat', $renderFormat);
  // API token, so we can run reports on behalf of other users.
  // Runs as "report-test" user
  
  include('constant.php');

  // If there are report parameters
  if (isset($reportParams) && $reportParams) {

    reset($reportParams);

    while (list($key, $val) = each($reportParams)) {
      // Pass along the request data
      $request->addPostData($key, $val);
    }
  }

  // Send the request
  $result = $request->sendRequest();
  if (PEAR::isError($result)) {
    echo "Error: " . $result->getMessage();
    exit;
  }

  // Get the response data
  $response = $request->getResponseBody();

  // Return error message returned by reporting services
  if(substr($response, 0, 5) == "ERROR"){
	return $response;
  }
  
  // If we aren't actually streaming the data back to the browser, then just return
  // the data to the caller
  if (!$sendData) {
    return $response;
  }
  
  // Generate headers to let the client know what to do with the data
  if ($renderFormat == 'PDF') {
    // This line usually gets the PDF opened right in the browser, but doesn't
    // work in all browsers. So, we'll do the download instead.
    header("Content-type: application/pdf");
    header('Content-Disposition: attachment; filename="'.$customfilename.'.pdf"');
  } else if ($renderFormat == 'Excel') {
    header('Content-Disposition: attachment; filename="'.$customfilename.'.xls"');
  } else if ($renderFormat == 'EXCELOPENXML') {
    header('Content-Disposition: attachment; filename="'.$customfilename.'.xlsx"');
  } else if ($renderFormat == 'CSV') {
    header('Content-Disposition: attachment; filename="'.$customfilename.'.csv"');
  } else if ($renderFormat == 'MHTML') {
    // Find 2 line returns in a row, meaning a blank line. Everything before that
    // should be headers.
    /*$blankLine = strpos($response, "\r\n\r\n");
    if ($blankLine !== false) {
      $headers = substr($response, 0, $blankLine);
      $response = substr($response, $blankLine+4);
      header($headers);
    }*/
    header($response);
    $response = "";
  } else if ($renderFormat == 'HTML4.0') {
	/*
	// Remove all the outer tags to display in a div
	$response = preg_replace('#\!DOCTYPE.*?<body.*?>#s','',$response);
	$response = preg_replace('#</body>.*?<html>#s','',$response);
	*/
	//Get rid of the little + image on the Detailed I&E report
	$response = preg_replace('#<a[^>]*><IMG BORDER="0".*?/></a>#','',$response); 
	
	//Fix the logo image in SSRS 2005
	$response = preg_replace('#<IMG SRC.*?/>#','<img src="/wp-content/P2C_LOGO_WEB.gif" />',$response);
	
	//Fix the logo image in SSRS 2012
	$response = preg_replace('#<IMG onerror\=\"this\.errored\=true\;\" SRC[^>]*>#','<img src="/wp-content/P2C_LOGO_WEB.gif" />',$response);
	
	//Get rid of the extra column that causes the content to get crunched to the left
	$response = str_ireplace('<td width="100%" height="0"></td>',"",$response);
	
	//Fix the styling on the page div in SSRS 2012 that causes pages to end up overlapping when viewed in Chrome
	$response = str_ireplace('<div style="HEIGHT:100%;WIDTH:100%;" class="ap">', '<div style="WIDTH:100%;" class="ap">', $response);
  }

  // Echo the result data to the client
  echo $response;

} // end function produceRSReport

function accountBalance($ProjCode, $user_id) {
  $reportParams['ProjectCodeSearch'] = $ProjCode;
  $reportParams['ReportYear'] = date("Y"); 
  $reportParams['ReportMonth'] = date("n");
  $reportParams['ExecuteAsUser'] = $user_id;

  $result = produceRSReport('/General/Account Balance', 'CSV', $reportParams, false);

  // Return error message returned by reporting services
  if(substr($result, 0, 5) == "ERROR"){
	return $result;
  }
  else {
	// For some reason, their are some weird unprintable characters at the start of the data. Get rid of them!
	while (ord($result) > 128) {
		$result = substr($result, 1);
	}
	$rows= explode("\r\n", trim($result));
	$headers = CsvSplit(trim($rows[0]));

	date_default_timezone_set("America/Vancouver");
	
	$currDate = date("M j, Y");
	$returnStr = "Result for $currDate:<br />";
	
	for ($i = 1; $i < count($rows); $i++) {
		$data = CsvSplit(trim($rows[$i]), $headers);
			//$returnStr .= "$data[AccountCode] - $data[AccountDescription]: $data[Balance]<br />";
		if ($data['AccountCode']) {
			$returnStr .= "$data[AccountCode] - $data[AccountDescription]: \$ $data[Balance]<br />";
		}
	}

	return $returnStr;
/*
	if (!isset($data['BusinessUnit']) || !isset($data['MainBalance'])) {
      return "No data found";
	} else {
	  return "Balance on $currDate for $data[BusinessUnit] - $data[Description] is $data[MainBalance]";
	}
*/
  }
}


//---------------------------CsvSplit BEGINS--------------------------
// This function splits the given string into an array as if it were
// a line in a CSV file. So, it basically splits on commas, but it
// also honours fields delimited by double quotes. If headers is
// supplied, it builds an associative array using the header names
// as keys. Otherwise, it just builds a regular integer indexed array
function CsvSplit($str,$headers=false) {
  $result = array();

  $done = false;
  $i = 0;

  while(true) {
    ltrim($str);
    if (strlen($str) == 0)
      break;

    if (strncmp($str, '"', 1) == 0) {

      $end = strpos($str, '"', 1);
      if ($end === false) {
        $to_add = substr($str, 1);
        $done = true;
      } else {
        $to_add = substr($str, 1, $end - 1);

        $end = strpos($str, ',', $end);
        if ($end === false) {
          $done = true;
        }
      }
    } else {

      $end = strpos($str, ',');
      if ($end === false) {
        $end = strlen($str);
        $done = true;
      }

      $to_add = substr($str, 0, $end);

    }

    if ($headers) {
      $result[$headers[$i]] = $to_add;
    } else {
      $result[] = $to_add;
    }

    $i++;

    if ($done) {
      break;
    }

    $str = substr($str, $end + 1);
  }

  return $result;
}//end of CsvSplit
//------------------------------CsvSplit ENDS----------------------------

?>