<?php

require_once('HTTP/Request.php');  // PEAR Library for making HTTP Requests

function produceSQLReport($sqlReportName, $employeeNumber, $reportMonth) {
	$WEB_SERVICE_URL = "http://hqiis/RunSQLReport/";

	// Create the request
	$request = new HTTP_Request($WEB_SERVICE_URL . $sqlReportName . ".aspx?APIToken=" . SQL_REPORT_API_TOKEN . "&EmployeeNumber=$employeeNumber&ReportMonth=$reportMonth");
	$request->setMethod(HTTP_REQUEST_METHOD_GET);

	// Send the request
	$result = $request->sendRequest();
	
	// Handle an error in the process of sending the HTTP request
	if (PEAR::isError($result)) {
		echo "ERROR: " . $result->getMessage();
		return "ERROR: " . $result->getMessage();
		exit;
	}

	// Handle an error on the SQL side, embedded in the response we receive
	$response = $request->getResponseBody();
	if(substr($response, 0, 5) == "ERROR"){
		return $response;
	}	

	echo $response;
}

?>