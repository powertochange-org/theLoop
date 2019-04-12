<?php
/*
*Template Name: zApp Financial_Reports
*
*Base page for the financial reporting system. When a reportform is submitted, the report
*producer is called in rs_functions.php and the report is downloaded or an error is displayed.
*
*/

/*
to an another report you need to:

	> add another elif block at "%elif-block"
	> if it does not use staff account then add the report to the list at "%list"
	> add the report to the dropdown with the id of "repchoice"
	> add to the repchoice change function to display the right option
	> add new option if necessary
	> modify check form if necessary
*/

require('functions/functions.php');
require('financialreports/report_page_functions.php');


$current_user = wp_get_current_user();
$user_id = $current_user->user_login;


//Get list of employees for the staff financial health report
$admin = isAppAdmin('staffhealth', 0);
$reportsToMeResults = getEmployeesWhoReportToUser($user_id, $admin);

//Set proper output format for preview
if(isset($_POST['previewBtn'])){
	$_POST['OutputFormat'] = "HTML4.0";
}

//////////////// VALIDATION CHECKS ////////////////
//Check for proper account number length
unset($error);
if (isset($_POST['REPORT']) 
	&& (($_POST['REPORT'] == "AccountBalance" && strlen($_POST['DESGCODE']) < 6)
	||  ($_POST['REPORT'] != "AccountBalance" && strlen($_POST['DESGCODE']) != 6))
	&& (!in_array($_POST['REPORT'] , 
        array ("StaffList", "StaffVacation", "StaffFinancialHealth", "Graph12MonthActualBudget", '12MonthActuals'))
    || in_array($_POST['REPORT'], array('Graph12MonthActualBudget', '12MonthActuals')) && strlen($_POST['DESGCODE']) != 0)) //%list  (just a label)
	{
	$error = "Your account number must be 6 digits in length\n";
} 
// Check for missing Org/Min code
if (isset($_POST['REPORT']) && ($_POST['REPORT'] == 'Graph12MonthActualBudget' || $_POST['REPORT'] == '12MonthActuals') 
  && $_POST['ORGMINCODE'] == '' && $_POST['DESGCODE'] == '') {
  $error = 'You must enter a valid Org Area and Ministry Code in the Ministry/Department field.';
}
// Check for missing start/end dates
if(isset($_POST['REPORT']) && ($_POST['REPORT'] == "DonorReport" 
                              || $_POST['REPORT'] == "AccountDonors" 
                              || $_POST['REPORT'] == "DetailedRangeReport" 
                              || $_POST['REPORT'] == "SummaryReport")) {
  if($_POST['RPTSTARTYEARMONTH'] == '' || $_POST['RPTENDYEARMONTH'] == '') {
    $error = 'One of the report dates is blank. Please select a value from the drop down.';
  }
}
// Check for missing report date
if(isset($_POST['REPORT']) && ($_POST['REPORT'] == "InvestorReport"
                              || $_POST['REPORT'] == "Graph12MonthActualBudget"
                              || $_POST['REPORT'] == "12MonthActuals")) {
  if($_POST['RPTYEARMONTH'] == '') {
    $error = 'The report date is blank. Please select a value from the drop down.';
  }
}

//////////////// GENERATE REPORTS ////////////////
//Code for Monthly Donation Report
if (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "DonorReport") {
  require('financialreports/rs_functions.php');

  $endYear = substr($_POST['RPTENDYEARMONTH'], 0, 4);
  $endMonth = substr($_POST['RPTENDYEARMONTH'], 5, 2);
	
  $lastday=31;
  while (!checkdate(intval($endMonth),$lastday,$endYear)) {
    $lastday = $lastday-1;
  }

  $reportParams['StartDate']= $_POST['RPTSTARTYEARMONTH'].'-01';
  $reportParams['EndDate'] = $endYear."-".$endMonth."-".$lastday;
  $reportParams['ProjectCode'] = $_POST['DESGCODE'];
  $reportParams['ExecuteAsUser'] = $user_id;

  //Check for returned error message
  $errorMsg = produceRSReport('/Donors/MonthlyDonationReport', $_POST['OutputFormat'], $reportParams);
  if(!isset($errorMsg)){
	exit;
  }
  $error = $errorMsg;
}
//Code for 13 Month Donor Report
elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "InvestorReport") {
  require('financialreports/rs_functions.php');

  $reportParams['ProjectCode'] = $_POST['DESGCODE'];
  $reportParams['SelectMonthYear']=$_POST['RPTYEARMONTH']."-01";
  $reportParams['ExecuteAsUser'] = $user_id;

  //Check for returned error message
  $errorMsg = produceRSReport('/Donors/13MonthDonorReport', $_POST['OutputFormat'], $reportParams);
  if(!isset($errorMsg)){
	exit;
  }
  $error = $errorMsg;
}
//Code for Recurring Monthly Donors Report
elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "MonthlyDonors") {
  require('financialreports/rs_functions.php');

  $reportParams['ProjectCode'] = $_POST['DESGCODE'];
  $reportParams['ReportLevel1'] = '';
  $reportParams['ExecuteAsUser'] = $user_id;

  $errorMsg = produceRSReport('/Donors/ProjectMonthlyDonors', $_POST['OutputFormat'], $reportParams);
  if(!isset($errorMsg)){
	exit;
  }
  $error = $errorMsg;
}
//Code for Detailed Income And Expense Report
elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "DetailedRangeReport") {
  require('financialreports/rs_functions.php');

  $reportParams['ProjectCode'] = $_POST['DESGCODE']; 
  $reportParams['StartDate'] = $_POST['RPTSTARTYEARMONTH'].'-01';
  $reportParams['EndDate'] = $_POST['RPTENDYEARMONTH'].'-10';
  $reportParams['ExecuteAsUser'] = $user_id;

  //Check for returned error message  
  $errorMsg = produceRSReport('/Financial/Detailed Income and Expense', $_POST['OutputFormat'], $reportParams, true, $SERVER_SQL2012);
  if(!isset($errorMsg)){
	exit;
  }
  $error = $errorMsg;
}
//Code for Summary Income And Expense Report
elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "SummaryReport") {
  require('financialreports/rs_functions.php');

  $reportParams['ProjectCode'] = $_POST['DESGCODE'];
  $reportParams['StartDate'] = $_POST['RPTSTARTYEARMONTH'].'-01';
  $reportParams['EndDate'] = $_POST['RPTENDYEARMONTH'].'-10';
  $reportParams['ExecuteAsUser'] = $user_id;

  //Check for returned error message
  $errorMsg = produceRSReport('/Financial/Summary Income and Expense', $_POST['OutputFormat'], $reportParams, true, $SERVER_SQL2012);
  if(!isset($errorMsg)){
	exit;
  }
  $error = $errorMsg;
}
//Code for Account Balance
elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "AccountBalance") {
  require('financialreports/rs_functions.php');
  
  //Check for returned error message
  $reportReturn = accountBalance($_POST['DESGCODE'], $user_id);
  if(substr($reportReturn, 0, 5) == "ERROR"){
	$error = $reportReturn;
  } else {
	$reportResult = $reportReturn;
  }
}
//Code for Account Donors (with address, email, phone)
elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "AccountDonors") {
  require('financialreports/rs_functions.php');

  $reportParams['ProjectCode'] = $_POST['DESGCODE']; 
  $reportParams['StartDate'] = $_POST['RPTSTARTYEARMONTH'].'-01';
  $LastDay = date('t',strtotime($_POST['RPTENDYEARMONTH'].'-01'));
  $reportParams['EndDate'] = $_POST['RPTENDYEARMONTH'].'-'.$LastDay;
  $reportParams['ExecuteAsUser'] = $user_id;

  //Check for returned error message  
  $errorMsg = produceRSReport('/Donors/AccountDonors', $_POST['OutputFormat'], $reportParams);
  if(!isset($errorMsg)){
	exit;
  }
  $error = $errorMsg;
}
//Code for Staff List Report
elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "StaffList") {
  require('financialreports/rs_functions.php');

  $reportParams['ExecuteAsUser'] = $user_id;
  $reportParams['ReportToMeOnly'] = (isset($_POST['reportToMe']) ? 'true' : 'false');
  $reportParams['ShowFinancials'] = (isset($_POST['financials']) ? 'true' : 'false');

  //Check for returned error message 
  $errorMsg = produceRSReport('/General/Staff List', $_POST['OutputFormat'], $reportParams, true, $SERVER_SQL2012);
  if(!isset($errorMsg)){
	exit;
  }
  $error = $errorMsg;
}
// Code for Staff Vacation report
elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "StaffVacation") {
  /*require('financialreports/rs_functions.php');

  $reportParams['ExecuteAsUser'] = $user_id;
  $reportParams['ReportToMeOnly'] = (isset($_POST['reportToMe']) ? 'true' : 'false');
  $reportParams['Year'] = $_POST['vac_year'];
  $reportParams['Category'] = '3';

  //Check for returned error message 
  $errorMsg = produceRSReport('/General/Staff Vacation and Wellness', $_POST['OutputFormat'], $reportParams, true, $SERVER_SQL2012);*/
  if(!isset($errorMsg)){
	exit;
  }
  $error = $errorMsg;
}
// Code for Staff Financial Health report
elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "StaffFinancialHealth") {
  require('financialreports/sql_report_functions.php');
  
  /* Before running the report, double check that the person actually has access */
  $hasAccess = false;
  foreach($reportsToMeResults as $result) {
	  if ($result->employee_number == $_POST['employee_number']) {
		  $hasAccess = true;
		  break;
	  }
  }
  
  if ($hasAccess) {
	  $errorMsg = produceSQLReport( 'StaffFinancialHealth', 
						$_POST['employee_number'], 
						$_POST['RPTYEARMONTH'].'-01' );
  } else {
	  echo "You don't have access to run this report for this staff member.";
  }
  if(!isset($errorMsg)){
    exit;
  }
  $error = $errorMsg;
}
//Code for Graph 12 Month Actual vs Budget Report
elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "Graph12MonthActualBudget") {
  require('financialreports/rs_functions.php');

  $reportParams['ProjectCode'] = $_POST['DESGCODE'];
  $reportParams['ReportYear'] = substr($_POST['RPTYEARMONTH'], 0, 4);
  $reportParams['ReportMonth'] = substr($_POST['RPTYEARMONTH'], 5, 2);
  if($_POST['DESGCODE'] == '')
    $reportParams['OrgAndMinistry'] = $_POST['ORGMINCODE'];
  $reportParams['Cumulative'] = $_POST['RPTCUMULATIVE'];
  $reportParams['ExecuteAsUser'] = $user_id;
  //Check for returned error message  
  $errorMsg = produceRSReport('/Financial/Graph 12 Month Actual vs Budget', $_POST['OutputFormat'], $reportParams, true, $SERVER_SQL2012);
  if(!isset($errorMsg)){
    exit;
  }
  $error = $errorMsg;
}
//Code for 12 Month (by Month) Actuals by Ministry
elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "12MonthActuals") {
  require('financialreports/rs_functions.php');

  $reportParams['ProjectCode'] = $_POST['DESGCODE'];
  $reportParams['EndMonth'] = $_POST['RPTYEARMONTH'].'-01';//$_POST['RPTMONTH'].'/'.'1/'.$_POST['RPTYEAR'];
  $parts = explode('-', $_POST['ORGMINCODE']);
  $reportParams['OrgArea'] = $parts[0];
  $reportParams['ExecuteAsUser'] = $user_id;
  if($_POST['DESGCODE'] != '') {
    if($reportParams['OrgArea'] == '')
      $reportParams['OrgArea'] = '40'; //Default to 40 if they have not entered in an OrgArea
  } else {
    $reportParams['Ministry'] = $parts[1];
  }
  //Check for returned error message  
  $errorMsg = produceRSReport('/Financial/Organization/12 Month (by Month) Actuals by Ministry', $_POST['OutputFormat'], $reportParams, true, $SERVER_SQL2012);
  if(!isset($errorMsg)){
    exit;
  }
  $error = $errorMsg;
}

//%elif-block (just a label - see instructions for adding a new report at top of file)

//Send an email to SQL Administrators if there is an error
if($error) {
  $sendErrReport = 1;
  //Ignore error messages that are not actually errors
  if(strpos($error, 'permission to view') !== false ||
    strpos($error, 'be 6 digits') !== false ||
    strpos($error, 'must enter a valid Org') !== false) {
    $sendErrReport = 0;
  }
  if($sendErrReport) {
    ini_set('SMTP','smtp.powertochange.org');
    ini_set('smtp_port',25);
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= 'From: Loop Reports Error <noreply@powertochange.org>' . "\r\n";
    $params = '';
    if(count($reportParams) > 0) {
      foreach ($reportParams as $key => $value) {
        $params .= 'Param: '.$key.' Value: '.$value.'<br>';
      }
      
    }
    $msg = '<b>Report:</b> '.$_POST['REPORT'].'<br><b>Requested by:</b> '.$user_id.'<br><b>Error Message:</b> '.$error.'<br><b>Parameters:</b><br>'.$params;
    mail(REPORT_ERRORS_EMAIL, 'Loop Reports Error - '.$_POST['REPORT'], $msg, $headers);
  }
}

//If there is an error with a preview, do not display the whole page
if(isset($_POST['previewBtn']) && isset($error)){
?>
	<HTML><HEAD></HEAD><BODY>
		<SPAN style="color: red"><?php echo $error?></SPAN>
	</BODY></HTML>
<?php
	exit;
}


//Values used to set the selected options
$REPORT = $_POST["REPORT"]; 
$RPTYEARMONTH = isset($_POST["RPTYEARMONTH"]) ? $_POST["RPTYEARMONTH"] : date('Y').'-'.date('m');
$RPTSTARTYEARMONTH = isset($_POST["RPTSTARTYEARMONTH"]) ? $_POST["RPTSTARTYEARMONTH"] : date('Y').'-'.date('m');
$RPTENDYEARMONTH = isset($_POST["RPTENDYEARMONTH"]) ? $_POST["RPTENDYEARMONTH"] : date('Y').'-'.date('m');
$RPTPERIOD = $_POST["RPTPERIOD"] ? $_POST["RPTPERIOD"] : 'MONTH';
$RPTCUMULATIVE = isset($_POST['RPTCUMULATIVE']) ? $_POST['RPTCUMULATIVE'] : '';
$reportToMe = 1; // Default to yes
if (isset($_POST['reportToMe'])) { // But, if POST variable is set, override with that
	$reportToMe = $_POST['reportToMe'];
}	
$financials = $_POST['financials'];
$vac_year = $_POST['vac_year'];
$OUTPUTFRMT = $_POST['OutputFormat'];

/*
 * Allow the report to be selected and certain parameters to be set using query string variables.
 * All the needed information is packed into 1 query string variable, because the CAS server seems
 * to strip off everything after the first one.
 *
 * Query string typically follows this format:
 *
 *   ?reportlink=RRRPPPPPPMMYYYY
 *
 * where:
 *    RRR is a 3-character report identifier
 *    PPPPPP is the 6-character project code (staff / ministry account)
 *    MM is the month
 *    YYYY is the year
 */
if(isset($_GET["reportlink"])) {
    $reportlink = $_GET["reportlink"];
    
    $report = substr($reportlink, 0, 3);
    $actnum = substr($reportlink, 3, 6);
    
    
    $_POST["DESGCODE"] = $actnum;
    
    if($report == "mdr") {
        $REPORT = "DonorReport";
		$RPTYEARMONTH = substr($reportlink, 11, 4).'-'.substr($reportlink, 9, 2);
    } else if($report == "rmd") {
        $REPORT = "MonthlyDonors";
    } else if($report == "13m") {// reports/?reportlink=13m823457201506
        $REPORT = "InvestorReport";
		$RPTYEARMONTH = substr($reportlink, 11, 4).'-'.substr($reportlink, 9, 2);
    } else if($report == "die") {
        $REPORT = "DetailedRangeReport";
		$RPTSTARTYEARMONTH = substr($reportlink, 11, 4).'-'.substr($reportlink, 9, 2);
		$RPTENDYEARMONTH = substr($reportlink, 17, 4).'-'.substr($reportlink, 15, 2);
    } else if ($report == "stl") {
		$REPORT = "StaffList";
		$reportToMe = substr($reportlink, 3, 1);
		$financials = substr($reportlink, 4, 1);
	}
    
}


get_header(); ?>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
  <script src="<?php echo get_stylesheet_directory_uri(); ?>/js/chosen/chosen.jquery.js" type="text/javascript"></script>
  <script src="<?php echo get_stylesheet_directory_uri(); ?>/js/loopfunctions.js" type="text/javascript"></script>
  <script src="<?php echo get_stylesheet_directory_uri(); ?>/js/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/js/chosen/docsupport/prism.css">
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/js/chosen/chosen.css">
  <style>
     .chosen-container {width: 220px !important}
  </style>
	<div id="content">
		<div id="main-content">	
			<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><!-- ?php the_title(); ? --></a></h1>
			<?php
			if (isset($error)) {
                 echo "<span style=\"color: red;word-break: break-word;\">$error</span><p>";
				}
             ?>
			<FORM NAME="report" METHOD="post" ACTION="<?php 
				if ($_SERVER['HTTPS'] == 'on') {
					echo "https://";
				} else {
					echo "http://";
				}
				echo $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
			?>">
			<?php
			//added to include the page content so as to put a button there
				the_post();
				the_content();
			?>
			<P>Choose Your Report:<BR>
			<SELECT ID="repchoice" NAME="REPORT" onchange="updateDefaultDates(this.value);">
                  <OPTION VALUE="">--DONATION REPORTS--</OPTION>
                  <OPTION VALUE="DonorReport" <?php if($REPORT == 'DonorReport'){echo("selected='selected'");}?>>Monthly Donation Report</OPTION>
                  <OPTION VALUE="InvestorReport" <?php if($REPORT == 'InvestorReport'){echo("selected='selected'");}?>>13 Month Donor Report</OPTION>
                  <OPTION VALUE="MonthlyDonors" <?php if($REPORT == 'MonthlyDonors'){echo("selected='selected'");}?>>Recurring Monthly Donors</OPTION>
				  <OPTION VALUE="AccountDonors" <?php if($REPORT == 'AccountDonors'){echo("selected='selected'");}?>>Account Donors (with address, email, phone)</OPTION>
	              <OPTION VALUE="">--FINANCIAL REPORTS--</OPTION>
                  <OPTION VALUE="DetailedRangeReport" <?php if($REPORT == 'DetailedRangeReport'){echo("selected='selected'");}?>>Detailed Income and Expense</OPTION>
                  <OPTION VALUE="SummaryReport" <?php if($REPORT == 'SummaryReport'){echo("selected='selected'");}?>>Summary Income and Expense</OPTION>
                  <OPTION VALUE="AccountBalance" <?php if($REPORT == 'AccountBalance'){echo("selected='selected'");}?>>Account Balance</OPTION>
                  <option value="Graph12MonthActualBudget" <?php if($REPORT == 'Graph12MonthActualBudget'){echo("selected='selected'");}?>>Graph 12 Month Actual vs Budget</option>
                  <option value="12MonthActuals" <?php if($REPORT == '12MonthActuals'){echo("selected='selected'");}?>>12 Month (by Month) Actuals by Ministry</option>
                  <OPTION VALUE="">--STAFF REPORTS--</OPTION>
				  <OPTION VALUE="StaffList" <?php if($REPORT == 'StaffList'){echo("selected='selected'");}?>>Staff List</OPTION>
				  <!--<OPTION VALUE="StaffVacation" <?php if($REPORT == 'StaffVacation'){echo("selected='selected'");}?>>Staff Vacation</OPTION>-->
				  <OPTION VALUE="StaffFinancialHealth" <?php if($REPORT == 'StaffFinancialHealth'){echo("selected='selected'");} ?>>Staff Financial Health</OPTION>
            </SELECT>
			<BUTTON TYPE="button" ID="dieHelpButton" style="display:none" onClick="window.open('/reports/detailed-income-and-expense-help/')">Help on this report</BUTTON>
			</P>
      <div id="ministrydept" style="display:none;">
        <p>Ministry/Department
          <select name="ORGMINCODE">
            <option value="">--Select a Ministry--</option>
            <option value="01-001">Capital - Capital</option>
            <option value="01-002">Capital - Capital - GAiN</option>
            <option value="10-106">President &amp; EDM - EDM Office</option>
            <option value="10-100">President &amp; EDM - General and Administrative</option>
            <option value="10-102">President &amp; EDM - President's Office</option>
            <option value="10-104">President &amp; EDM - Projects</option>
            <option value="20-200">Ministry Services - EDMS Office</option>
            <option value="20-204">Ministry Services - Finance</option>
            <option value="20-202">Ministry Services - Headquarters</option>
            <option value="20-206">Ministry Services - HR</option>
            <option value="20-208">Ministry Services - IT</option>
            <option value="20-212">Ministry Services - Project Services</option>
            <option value="20-210">Ministry Services - Resource Centre</option>
            <option value="30-304">Advancement - Communications</option>
            <option value="30-300">Advancement - EDA Office</option>
            <option value="30-302">Advancement - Major Gifts</option>
            <option value="30-308">Advancement - Marketing</option>
            <option value="30-310">Advancement - Partner Care Centre</option>
            <option value="30-312">Advancement - Zone Teams</option>
            <option value="40-402">Ministry - Athletes in Action</option>
            <option value="40-406">Ministry - Christian Embassy</option>
            <option value="40-412">Ministry - Connecting Streams</option>
            <option value="40-414">Ministry - DRIME</option>
            <option value="40-416">Ministry - FamilyLife</option>
            <option value="40-469">Ministry - GAiN</option>
            <option value="40-426">Ministry - Jesus Film Project</option>
            <option value="40-430">Ministry - LeaderImpact</option>
            <option value="40-460">Ministry - Other Ministry</option>
            <option value="40-436">Ministry - Students Division</option>
            <option value="40-438">Ministry - The Life Project</option>
            <option value="40-424">Ministry - WHEN</option>
            <option value="80-402">Staff - Athletes in Action</option>
            <option value="80-404">Staff - Breakthrough Prayer</option>
            <option value="80-406">Staff - Christian Embassy</option>
            <option value="80-408">Staff - Church Relations</option>
            <option value="80-304">Staff - Communications</option>
            <option value="80-412">Staff - Connecting Streams</option>
            <option value="80-414">Staff - DRIME</option>
            <option value="80-106">Staff - EDM Office</option>
            <option value="80-200">Staff - EDMS Office</option>
            <option value="80-416">Staff - FamilyLife</option>
            <option value="80-204">Staff - Finance</option>
            <option value="80-418">Staff - Fund Development</option>
            <option value="80-469">Staff - GAiN</option>
            <option value="80-100">Staff - General and Administrative</option>
            <option value="80-202">Staff - Headquarters</option>
            <option value="80-206">Staff - HR</option>
            <option value="80-802">Staff - International</option>
            <option value="80-208">Staff - IT</option>
            <option value="80-426">Staff - Jesus Film Project</option>
            <option value="80-430">Staff - LeaderImpact</option>
            <option value="80-302">Staff - Major Gifts</option>
            <option value="80-308">Staff - Marketing</option>
            <option value="80-432">Staff - Oasis</option>
            <option value="80-460">Staff - Other Ministry</option>
            <option value="80-310">Staff - Partner Care Centre</option>
            <option value="80-102">Staff - President's Office</option>
            <option value="80-212">Staff - Project Services</option>
            <option value="80-210">Staff - Resource Centre</option>
            <option value="80-436">Staff - Students Division</option>
            <option value="80-438">Staff - The Life Project</option>
            <option value="80-440">Staff - Vision 360</option>
            <option value="80-424">Staff - WHEN</option>
            <option value="80-312">Staff - Zone Teams</option>
            <option value="90-902">International - International Ministry</option>
            <option value="90-900">International - International Staff</option>
          </select>
        </p>
      </div>
			<DIV ID="staffaccount" STYLE="display:none">
				<P>Ministry/staff account (Use the search box and click add account or type your account number directly in the box below)<BR>  
          <select id="staffaccountadd" name="staffaccountadd" class="chosen-select chosen-select-width" data-placeholder=" ">
            <option> </option>
          </select>
          <button type="button" onclick="addProjectCode();">Add Account ►</button>
          <INPUT TYPE="text" ID="DESGCODE" NAME="DESGCODE" MAXLENGTH="1000" SIZE="15" VALUE="<?php echo $_POST["DESGCODE"];?>">
				</P>
        
			</DIV>
			<DIV id='staffHealth_options' style='display:none'>
				For Staff member:
				<SELECT NAME="employee_number" class="chosen-select">
				<?php
				foreach($reportsToMeResults as $result) {
					echo "<OPTION VALUE='" . $result->employee_number . "'>" . $result->full_name . "</OPTION>\r\n";
				}
				?>
				</SELECT>
			</DIV>
			<DIV ID="monthyear" STYLE="display:none">
				<P>Choose the month and year to report on.<BR>
				<?php generateYearMonthDropDown("RPTYEARMONTH", $RPTYEARMONTH);	?>
				<BR>
				</P>
			</DIV>
			<DIV ID="daterange" STYLE="display:none">
				<P>
				<SPAN STYLE="width:50px; float:left">START:</SPAN>
				<?php generateYearMonthDropDown("RPTSTARTYEARMONTH", $RPTSTARTYEARMONTH); ?>
				<BR>
				<SPAN STYLE="width:50px; float:left">END:</SPAN>
				<?php generateYearMonthDropDown("RPTENDYEARMONTH", $RPTENDYEARMONTH); ?>
				</P>
			</DIV>
      <div id="cumulative" style="display:none;">
        Cumulative: 
        <select name="RPTCUMULATIVE">
          <option value="Yes">Yes</option>
          <option value="No">No</option>
        </select>
      </div>
      <div id='reportToMe_opt' style='display:none'><input type='checkbox' id='reportToMe' name='reportToMe' <?php if($reportToMe){echo "checked";}?> ><label for='reportToMe'>Include only my direct reports (uncheck to include staff further down the reporting chain)</label><BR></div>
			<div id='financials_opt' style='display:none'><input type='checkbox' id='financials' name='financials' <?php if($financials){echo "checked";}?> ><label for='financials'>Show financials (salaries and account balances)</label></div>
			<div id='staffVaction_options'  style='display:none'>
				YEAR:
				<SELECT NAME="vac_year">
					  <OPTION VALUE="">--Year--</OPTION>
					  <?php $CurrYear = date("Y");
					     $x = 0;
						 WHILE ($x < 3){
						 ?>
						 <OPTION VALUE='<?php echo $CurrYear-$x;?>' 
								     <?php if($vac_year == $CurrYear-$x){echo("selected");}?>>
									 <?php echo $CurrYear-$x;?></OPTION>
						 <?php
						 $x++;
						 }
						 ?>
				</SELECT>		
			</div>
			<DIV ID="output" STYLE="display:none">
				<P>
				Output Format:
				<select name="OutputFormat">
					<option value="PDF" <?php if($OUTPUTFRMT == 'PDF'){echo("selected");}?>>PDF</option>
					<option value="Excel" <?php if($OUTPUTFRMT == 'Excel'){echo("selected");}?>>Excel</option>
					<option value="CSV" <?php if($OUTPUTFRMT == 'CSV'){echo("selected");}?>>CSV</option>
				</select>
				</P>
			</DIV>
			<DIV ID="message" STYLE="display:none"></DIV>
			<DIV ID="buttonsDownload" STYLE="display:none;float:left;margin-right:5px;">
				<INPUT TYPE="submit" ID="actionBtn" NAME="actionBtn" VALUE="Download" onClick="return CheckForm(this.form, 'download')">
			</DIV>
			<DIV ID="buttonsPreview" STYLE="display:none;float:left;margin-right:5px;">
				<INPUT TYPE="submit" ID="previewBtn" NAME="previewBtn" VALUE="View Online" onClick="return CheckForm(this.form, 'preview')">
			</DIV>
			<DIV ID="buttonsCheck" STYLE="display:none;float:left;">
				<INPUT TYPE="submit" ID="checkBtn" NAME="checkBtn" VALUE="Check Balance" onClick="return CheckForm(this.form, 'check')">
			</DIV>
			</FORM>
			<BR>
			<div id="reportOutput">
				<?php if ($reportResult) { print $reportResult; } ?>
			</div>
			<div id="preview" STYLE="display:none">
				
			</div>
		<script type="text/javascript">
		
		// Set focus to the project code field
		//document.getElementById('DESGCODE').focus();
    
		
    function addProjectCode() {
      var projCode = document.getElementById('staffaccountadd').value;
      if(projCode != null && projCode != '') {
        var currentEntry = document.getElementById('DESGCODE').value;
        if(currentEntry != '') {
          document.getElementById('DESGCODE').value += ',' + projCode;
        } else {
          document.getElementById('DESGCODE').value += projCode;
        }
      }
    }
    
		function CheckForm(form, subType){
			if($("#repchoice").val() == "") {
				alert("Please select a report from the drop-down list");
				return false;
			}
			if(!form.DESGCODE.value && $("#repchoice").val() != "StaffList"  && $("#repchoice").val() != "StaffVacation" && $("#repchoice").val() != "StaffFinancialHealth" && $("#repchoice").val() != "Graph12MonthActualBudget" && $("#repchoice").val() != "12MonthActuals"){
				alert("Please enter your ministry/staff account number before running the report");
				return false;
			}
			if(subType == "download" || form.REPORT.value == "AccountBalance")
				form.target = "_self";
			else {
				form.target = "_blank";
				/*
				form.target = "preview";
				$("#preview").slideDown();
				*/
			}
			return true;
		}//end of CheckForm
		
		//Display only the relevant form fields
		$("#repchoice").change(function () {
			$("#repchoice option:selected").each(function () {
				/* Form fields */
				if($(this).val() == "DonorReport"){
					showHideFields(["#staffaccount","#daterange","#output","#buttonsDownload","#buttonsPreview"]);
				} else if($(this).val() == "InvestorReport"){
					showHideFields(["#staffaccount","#monthyear","#output","#buttonsDownload","#buttonsPreview"]);
				} else if($(this).val() == "MonthlyDonors"){
					showHideFields(["#staffaccount","#output","#buttonsDownload","#buttonsPreview"]);
				} else if($(this).val() == "DetailedRangeReport"){				
					showHideFields(["#staffaccount","#daterange","#output","#dieHelpButton","#buttonsDownload","#buttonsPreview"]);
				} else if($(this).val() == "SummaryReport"){
					showHideFields(["#staffaccount","#daterange","#output","#buttonsDownload","#buttonsPreview"]);
				} else if($(this).val() == "AccountBalance"){
					showHideFields(["#staffaccount","#buttonsCheck"]);
					
					/* Add custom help text to the DESGCODE field */
					$("#DESGCODE").attr("title",'Indicate multiple accounts by separating them using commas');
				} else if($(this).val() == "AccountDonors"){
					showHideFields(["#staffaccount","#daterange","#output","#buttonsDownload","#buttonsPreview"]);					
				} else if($(this).val() == "StaffList"){
					showHideFields(["#reportToMe_opt","#financials_opt","#output","#buttonsDownload","#buttonsPreview"]);
				}  else if($(this).val() == "StaffVacation"){
					showHideFields(["#reportToMe_opt","#staffVaction_options","#output","#buttonsDownload","#buttonsPreview"]);
				} else if ($(this).val() == "StaffFinancialHealth"){
					<?php if ($admin || count($reportsToMeResults) > 0) { ?>
						showHideFields(["#staffHealth_options","#monthyear","#buttonsPreview"]);
					<?php } else { ?>
						$("#message").html("You don't have access to run this report for any staff. If you believe you should have access, please email <a href='mailto:helpdesk@p2c.com'>helpdesk@p2c.com</a>");
						showHideFields(["#message"]);
					<?php } ?>
				} else if ($(this).val() == "Graph12MonthActualBudget") {
          showHideFields(["#staffaccount","#output","#buttonsDownload", "#monthyear", "#ministrydept", "#cumulative"]);
        } else if ($(this).val() == "12MonthActuals") {
          showHideFields(["#staffaccount","#output", "#buttonsPreview", "#buttonsDownload", "#monthyear", "#ministrydept"]);
        }
			});			
		}).change();
		
		function showHideFields(show) {
			/* The list of field groupings that can be shown or hidden for different reports */
			var fields = ["#staffaccount", "#monthyear", "#output", "#daterange", "#financials_opt", 
						"#reportToMe_opt", "#staffVaction_options", "#staffHealth_options", "#message",
						"#dieHelpButton","#buttonsDownload","#buttonsPreview","#buttonsCheck", "#ministrydept", "#cumulative"];
			
			/* Iterate through the list of field groupings */
			for (var i = 0; i < fields.length; i++) {
				/* If it is not in the list to show, do a slideUp; if it IS in the list to show,
				 * do a slideDown to reveal it */
				if (show.indexOf(fields[i]) == -1) {
					$(fields[i]).slideUp();
				} else {
					$(fields[i]).slideDown();
				}
			}
			
			/* For one specific report, we add custom help text to this field; for all the rest,
			 * we don't want it. So, always remove it and let that one report option re-add it */
			$("#DESGCODE").removeAttr("title");
		}
    
    function updateDefaultDates(value) {
      var d = new Date();
      var year = d.getFullYear();
      var month = d.getMonth() + 1;
      
      if(value == "Graph12MonthActualBudget" || value == "12MonthActuals") {
        //Select the end of the fiscal year
        if(month > 6) {
          year += 1;
        }
        month = 6;
      } else if(value == "DonorReport" || value == "InvestorReport" || value == "StaffFinancialHealth") { 
        //Use the current date
      } else {
        return;
      }
      
      document.getElementById("RPTYEARMONTH").value = year + "-" + (month < 10 ? "0" : "") + month;
    }
		
    $(document).ready(function() {
        $.ajax({
            url: "/wp-content/themes/apps/financialreports/getAllAccounts.php",
            dataType: "json",
            type: "GET",
            success: function (data) {
                var optionList = '';
                for (var i=0; i<data.length; i++) {
                  try {
                      var text = data[i].Text.substr(data[i].Text.indexOf(':')+1).replace('Ministry of ','');
                      var value = data[i].Value;
                      var option = '<option value="'+value+'">'+text+'</option>';
                      optionList += option;
                  } catch(e) {}
                }
                
                if(!detectIE()) {
                  $('select#staffaccountadd').append(optionList);
                } else {
                  $('select#staffaccountadd').append('<option value="0">Internet Explorer does not support this feature.</option>');
                }
                
                //Create filter for the list of names
                var config = {
                  '.chosen-select'           : {},
                  '.chosen-select-deselect'  : {allow_single_deselect:true},
                  '.chosen-select-no-single' : {disable_search_threshold:10},
                  '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
                  '.chosen-select-width'     : {width:"95%"}
                }
                for (var selector in config) {
                  $(selector).chosen(config[selector]);
                }//End of filter creation
            },
            error: function(a,b,c) {
                console.error(a);
                console.error(b);
                console.error(c);
            }
        });
    });
		</SCRIPT>
	</div>
<!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>			
<?php get_footer(); 
?>