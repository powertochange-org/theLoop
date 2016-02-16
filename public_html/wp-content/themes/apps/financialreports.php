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

$current_user = wp_get_current_user();
$user_id = $current_user->user_login;


//Admin for financial health report
include('functions/functions.php');
$admin = 0;
if(isAppAdmin('staffhealth', 0)) {
	$admin = 1;
	$reportsToMeResults = $wpdb->get_results(
		$wpdb->prepare( 
		/* Get Everyone instead of the people that really report to the user*/
		"SELECT e.employee_number, e.user_login, CONCAT(e.first_name,' ',e.last_name) AS 'full_name'
		FROM employee e
		WHERE e.staff_account IS NOT NULL   
		ORDER BY CASE WHEN user_login = %s THEN 1 ELSE 2 END, full_name", $user_id));
} else {
	/* For the Staff List report, we need the current user's employee number, and also a list
	 * of the staff who report to the current user */
	$reportsToMeResults = $wpdb->get_results(
		$wpdb->prepare( 
		/* First select is for the current user; next is for everyone who reports to the current user */
		"SELECT e.employee_number, e.user_login, '                           ' AS 'supervisor_login', CONCAT(e.first_name,' ',e.last_name) AS 'full_name'
		FROM employee e
		WHERE e.user_login = %s
		  AND e.staff_account IS NOT NULL
		  
		UNION
		
		SELECT e.employee_number, e.user_login, s1.user_login AS 'supervisor_login', CONCAT(e.first_name,' ',e.last_name) AS 'full_name'
		FROM employee e
		LEFT JOIN employee s1 ON s1.employee_number = e.supervisor
		LEFT JOIN employee s2 ON s2.employee_number = s1.supervisor
		LEFT JOIN employee s3 ON s3.employee_number = s2.supervisor
		LEFT JOIN employee s4 ON s4.employee_number = s3.supervisor
		LEFT JOIN employee s5 ON s5.employee_number = s4.supervisor
		LEFT JOIN employee s6 ON s6.employee_number = s5.supervisor
		LEFT JOIN employee s7 ON s7.employee_number = s6.supervisor
		WHERE %s IN (s1.user_login, s2.user_login, s3.user_login, s4.user_login, s5.user_login, s6.user_login, s7.user_login)
		  AND e.staff_account IS NOT NULL		

		ORDER BY CASE WHEN user_login = %s THEN 1
				      WHEN supervisor_login = %s THEN 2
					  ELSE 3 END,
			full_name", /* Order by current user first, then direct reports, then everyone else */
		$user_id, $user_id, $user_id, $user_id));	
  
  //Add on the extra entries from the employee manager table
  for($z = 0; $z < 7; $z++) { //Up to 7 levels
    if($z == 0) {
      $extrareportsToMeResults = $wpdb->get_results($wpdb->prepare( 
        "SELECT 
        employee_manager.employee_number, 
        employee.user_login,
        s1.user_login AS 'supervisor_login',
        CONCAT(employee.first_name,' ', employee.last_name  ) AS full_name
        FROM employee_manager
        LEFT OUTER JOIN employee ON employee.employee_number = employee_manager.employee_number
        LEFT OUTER JOIN employee s1 ON s1.employee_number = employee_manager.manager_employee_number
        WHERE s1.user_login = %s AND employee.staff_account IS NOT NULL", $user_id));
    } else {
      $sql = "SELECT 
        employee_manager.employee_number, 
        employee.user_login,
        s1.user_login AS 'supervisor_login',
        CONCAT(employee.first_name,' ', employee.last_name  ) AS full_name
        FROM employee_manager
        LEFT OUTER JOIN employee ON employee.employee_number = employee_manager.employee_number
        LEFT OUTER JOIN employee s1 ON s1.employee_number = employee_manager.manager_employee_number
        WHERE s1.employee_number IN( %s ) AND employee.staff_account IS NOT NULL";
        
        $params = '';
        foreach($extrareportsToMeResults as $extra) {
          if(!empty($params))
            $params .= ', ';
          $params .= $extra->employee_number;
        }
      $extrareportsToMeResults = $wpdb->get_results($wpdb->prepare($sql, $params));
    }
    //Check for duplicates
    foreach($extrareportsToMeResults as $extra) {
      $duplicate = 0;
      foreach($reportsToMeResults as $duplicateCheck) {
        if($duplicateCheck->employee_number == $extra->employee_number) {
          $duplicate = 1;
          break;
        }
      }
      //Add the person if they are not a duplicate
      if(!$duplicate)
        array_push($reportsToMeResults, $extra);
    }
  }
}


//Set proper output format for preview
if(isset($_POST['previewBtn'])){
	$_POST['OutputFormat'] = "HTML4.0";
}

//Check for proper account number length
unset($error);
if (isset($_POST['REPORT']) 
	&& (($_POST['REPORT'] == "AccountBalance" && strlen($_POST['DESGCODE']) < 6)
	||  ($_POST['REPORT'] != "AccountBalance" && strlen($_POST['DESGCODE']) != 6))
	&& !in_array($_POST['REPORT'] , array ("StaffList", "StaffVacation", "StaffFinancialHealth"))) //%list  (just a label)
	{
	$error = "Your account number must be 6 digits in length\n";
} 

//Code for Monthly Donation Report
if (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "DonorReport") {
  require('financialreports/rs_functions.php');

  $month = $_POST['RPTMONTH'];
  $year = $_POST['RPTYEAR'];
	
  $lastday=31;
  while (!checkdate($month,$lastday,$year)) {
    $lastday = $lastday-1;
  }

  $reportParams['StartDate']= $year."-".$month."-01";
  $reportParams['EndDate'] = $year."-".$month."-".$lastday;
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
  $reportParams['SelectMonthYear']=$_POST['RPTYEAR']."-".$_POST['RPTMONTH']."-01";
  $reportParams['ExecuteAsUser'] = $user_id;

  //Check for returned error message
  $errorMsg = produceRSReport('/Donors/13MonthDonorReport', $_POST['OutputFormat'], $reportParams);
  if(!isset($errorMsg)){
	exit;
  }
  $error = $errorMsg;
}
//Code for Monthly Donors Report
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
  $reportParams['StartDate'] = $_POST['RPTSTARTYEAR'].'-'.$_POST['RPTSTARTMONTH'].'-01';
  $reportParams['EndDate'] = $_POST['RPTENDYEAR'].'-'.$_POST['RPTENDMONTH'].'-10';
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
  $reportParams['StartDate'] = $_POST['RPTSTARTYEAR'].'-'.$_POST['RPTSTARTMONTH'].'-01';
  $reportParams['EndDate'] = $_POST['RPTENDYEAR'].'-'.$_POST['RPTENDMONTH'].'-10';
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
//Code for Account Donors Report
elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "AccountDonors") {
  require('financialreports/rs_functions.php');

  $reportParams['ProjectCode'] = $_POST['DESGCODE']; 
  $reportParams['StartDate'] = $_POST['RPTSTARTYEAR'].'-'.$_POST['RPTSTARTMONTH'].'-01';
  $LastDay = date('t',strtotime($_POST['RPTENDYEAR'].'-'.$_POST['RPTENDMONTH'].'-01'));
  $reportParams['EndDate'] = $_POST['RPTENDYEAR'].'-'.$_POST['RPTENDMONTH'].'-'.$LastDay;
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
// Code for Staff Vacation and Wellness report
elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "StaffVacation") {
  require('financialreports/rs_functions.php');

  $reportParams['ExecuteAsUser'] = $user_id;
  $reportParams['ReportToMeOnly'] = (isset($_POST['reportToMe']) ? 'true' : 'false');
  $reportParams['Year'] = $_POST['vac_year'];
  $reportParams['Category'] = '3';

  //Check for returned error message 
  $errorMsg = produceRSReport('/General/Staff Vacation and Wellness', $_POST['OutputFormat'], $reportParams, true, $SERVER_SQL2012);
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
	  produceSQLReport( 'StaffFinancialHealth', 
						$_POST['employee_number'], 
						$_POST['RPTYEAR'].'-'.$_POST['RPTMONTH'].'-01' );
  } else {
	  echo "You don't have access to run this report for this staff member.";
  }
  exit;
}

//%elif-block (just a label - see instructions for adding a new report at top of file)


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
$RPTMONTH = $_POST["RPTMONTH"] ? $_POST["RPTMONTH"] : date("m");
$RPTYEAR = $_POST["RPTYEAR"] ? $_POST["RPTYEAR"] : date("Y");
$RPTSTARTMONTH = $_POST["RPTSTARTMONTH"] ? $_POST["RPTSTARTMONTH"] : date("m");
$RPTSTARTYEAR = $_POST["RPTSTARTYEAR"] ? $_POST["RPTSTARTYEAR"] : date("Y");
$RPTENDMONTH = $_POST["RPTENDMONTH"] ? $_POST["RPTENDMONTH"] : date("m");
$RPTENDYEAR = $_POST["RPTENDYEAR"] ? $_POST["RPTENDYEAR"] : date("Y");
$RPTPERIOD = $_POST["RPTPERIOD"] ? $_POST["RPTPERIOD"] : 'MONTH';
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
        $RPTMONTH = substr($reportlink, 9, 2);
        $RPTYEAR = substr($reportlink, 11, 4);
    } else if($report == "rmd") {
        $REPORT = "MonthlyDonors";
    } else if($report == "13m") {// reports/?reportlink=13m823457201506
        $REPORT = "InvestorReport";
        $RPTMONTH = substr($reportlink, 9, 2);
        $RPTYEAR = substr($reportlink, 11, 4);
    } else if($report == "die") {
        $REPORT = "DetailedRangeReport";
        $RPTSTARTMONTH = substr($reportlink, 9, 2);
        $RPTSTARTYEAR = substr($reportlink, 11, 4);
        $RPTENDMONTH = substr($reportlink, 15, 2);
        $RPTENDYEAR = substr($reportlink, 17, 4);
    } else if ($report == "stl") {
		$REPORT = "StaffList";
		$reportToMe = substr($reportlink, 3, 1);
		$financials = substr($reportlink, 4, 1);
	}
    
}


get_header(); ?>
	<div id="content">
		<div id="main-content">	
			<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><!-- ?php the_title(); ? --></a></h1>
			<?php
			if (isset($error)) {
                 echo "<span style=\"color: red\">$error</span><p>";
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
			<P>Choose Your Report:<BR>
			<SELECT ID="repchoice" NAME="REPORT">
                  <OPTION VALUE="">--DONATION REPORTS--</OPTION>
                  <OPTION VALUE="DonorReport" <?php if($REPORT == 'DonorReport'){echo("selected='selected'");}?>>Monthly Donation Report</OPTION>
                  <OPTION VALUE="InvestorReport" <?php if($REPORT == 'InvestorReport'){echo("selected='selected'");}?>>13 Month Donor Report</OPTION>
                  <OPTION VALUE="MonthlyDonors" <?php if($REPORT == 'MonthlyDonors'){echo("selected='selected'");}?>>Recurring Monthly Donors</OPTION>
				  <OPTION VALUE="AccountDonors" <?php if($REPORT == 'AccountDonors'){echo("selected='selected'");}?>>Account Donors</OPTION>
	              <OPTION VALUE="">--FINANCIAL REPORTS--</OPTION>
                  <OPTION VALUE="DetailedRangeReport" <?php if($REPORT == 'DetailedRangeReport'){echo("selected='selected'");}?>>Detailed Income and Expense</OPTION>
                  <OPTION VALUE="SummaryReport" <?php if($REPORT == 'SummaryReport'){echo("selected='selected'");}?>>Summary Income and Expense</OPTION>
                  <OPTION VALUE="AccountBalance" <?php if($REPORT == 'AccountBalance'){echo("selected='selected'");}?>>Account Balance</OPTION>
                  <OPTION VALUE="">--STAFF REPORTS--</OPTION>
				  <OPTION VALUE="StaffList" <?php if($REPORT == 'StaffList'){echo("selected='selected'");}?>>Staff List</OPTION>
				  <OPTION VALUE="StaffVacation" <?php if($REPORT == 'StaffVacation'){echo("selected='selected'");}?>>Staff Vacation and Wellness</OPTION>
				  <OPTION VALUE="StaffFinancialHealth" <?php if($REPORT == 'StaffFinancialHealth'){echo("selected='selected'");} ?>>Staff Financial Health</OPTION>
            </SELECT>
			<BUTTON TYPE="button" ID="dieHelpButton" style="display:none" onClick="window.open('/reports/detailed-income-and-expense-help/')")>Help on this report</BUTTON>
			</P>
			<DIV ID="staffaccount" STYLE="display:none">
				<P>Please enter your ministry/staff account number:<BR>  
					<INPUT TYPE="text" ID="DESGCODE" NAME="DESGCODE" MAXLENGTH="1000" SIZE="10" VALUE="<?php echo $_POST["DESGCODE"];?>">
				</P>
			</DIV>
			<DIV id='staffHealth_options' style='display:none'>
				For Staff member:
				<SELECT NAME="employee_number">
				<?php
				foreach($reportsToMeResults as $result) {
					echo "<OPTION VALUE='" . $result->employee_number . "'>" . $result->full_name . "</OPTION>\r\n";
				}
				?>
				</SELECT>
			</DIV>
			<DIV ID="monthyear" STYLE="display:none">
				<P>Choose the month and year to report on.<BR>
				<SELECT NAME="RPTMONTH">
                  <OPTION VALUE="">--Month--</OPTION>
                  <OPTION VALUE="01" <?php if($RPTMONTH == '01'){echo("selected='selected'");}?>>January</OPTION>
                  <OPTION VALUE="02" <?php if($RPTMONTH == '02'){echo("selected='selected'");}?>>February</OPTION>
                  <OPTION VALUE="03" <?php if($RPTMONTH == '03'){echo("selected='selected'");}?>>March</OPTION>
                  <OPTION VALUE="04" <?php if($RPTMONTH == '04'){echo("selected='selected'");}?>>April</OPTION>
                  <OPTION VALUE="05" <?php if($RPTMONTH == '05'){echo("selected='selected'");}?>>May</OPTION>
                  <OPTION VALUE="06" <?php if($RPTMONTH == '06'){echo("selected='selected'");}?>>June</OPTION>
                  <OPTION VALUE="07" <?php if($RPTMONTH == '07'){echo("selected='selected'");}?>>July</OPTION>
                  <OPTION VALUE="08" <?php if($RPTMONTH == '08'){echo("selected='selected'");}?>>August</OPTION>
                  <OPTION VALUE="09" <?php if($RPTMONTH == '09'){echo("selected='selected'");}?>>September</OPTION>
                  <OPTION VALUE="10" <?php if($RPTMONTH == '10'){echo("selected='selected'");}?>>October</OPTION>
                  <OPTION VALUE="11" <?php if($RPTMONTH == '11'){echo("selected='selected'");}?>>November</OPTION>
                  <OPTION VALUE="12" <?php if($RPTMONTH == '12'){echo("selected='selected'");}?>>December</OPTION>
                </SELECT>
				<SELECT NAME="RPTYEAR">
					  <OPTION VALUE="">--Year--</OPTION>
					  <?php $CurrYear = date("Y");
					     $x = 0;
						 WHILE ($CurrYear-$x >= 1989){
						 ?>
						 <OPTION VALUE='<?php echo $CurrYear-$x;?>' 
								     <?php if($RPTYEAR == $CurrYear-$x){echo("selected='selected'");}?>>
									 <?php echo $CurrYear-$x;?></OPTION>
						 <?php
						 $x++;
						 }
						 ?>				  										  								 	 <?php echo (date("Y") -4);?></OPTION>
				</SELECT>			
				<BR>
				</P>
			</DIV>
			<DIV ID="daterange" STYLE="display:none">
				<P>
				<SPAN STYLE="width:50px; float:left">START:</SPAN>
				<SELECT NAME="RPTSTARTMONTH">
					  <OPTION VALUE="">--Month--</OPTION>
					  <OPTION VALUE="01" <?php if($RPTSTARTMONTH == '01'){echo("selected='selected'");}?>>January</OPTION>
					  <OPTION VALUE="02" <?php if($RPTSTARTMONTH == '02'){echo("selected='selected'");}?>>February</OPTION>
					  <OPTION VALUE="03" <?php if($RPTSTARTMONTH == '03'){echo("selected='selected'");}?>>March</OPTION>
					  <OPTION VALUE="04" <?php if($RPTSTARTMONTH == '04'){echo("selected='selected'");}?>>April</OPTION>
					  <OPTION VALUE="05" <?php if($RPTSTARTMONTH == '05'){echo("selected='selected'");}?>>May</OPTION>
					  <OPTION VALUE="06" <?php if($RPTSTARTMONTH == '06'){echo("selected='selected'");}?>>June</OPTION>
					  <OPTION VALUE="07" <?php if($RPTSTARTMONTH == '07'){echo("selected='selected'");}?>>July</OPTION>
					  <OPTION VALUE="08" <?php if($RPTSTARTMONTH == '08'){echo("selected='selected'");}?>>August</OPTION>
					  <OPTION VALUE="09" <?php if($RPTSTARTMONTH == '09'){echo("selected='selected'");}?>>September</OPTION>
					  <OPTION VALUE="10" <?php if($RPTSTARTMONTH == '10'){echo("selected='selected'");}?>>October</OPTION>
					  <OPTION VALUE="11" <?php if($RPTSTARTMONTH == '11'){echo("selected='selected'");}?>>November</OPTION>
					  <OPTION VALUE="12" <?php if($RPTSTARTMONTH == '12'){echo("selected='selected'");}?>>December</OPTION>
				</SELECT>
				<SELECT NAME="RPTSTARTYEAR">
					  <OPTION VALUE="">--Year--</OPTION>
					  <?php $CurrYear = date("Y");
					     $x = 0;
						 WHILE ($CurrYear-$x >= 1989){
						 ?>
						 <OPTION VALUE='<?php echo $CurrYear-$x;?>' 
								     <?php if($RPTSTARTYEAR == $CurrYear-$x){echo("selected='selected'");}?>>
									 <?php echo $CurrYear-$x;?></OPTION>
						 <?php
						 $x++;
						 }
						 ?>
				</SELECT>
				<BR><SPAN STYLE="width:50px; float:left">END:</SPAN>
				<SELECT NAME="RPTENDMONTH">
					  <OPTION VALUE="">--Month--</OPTION>
					  <OPTION VALUE="01" <?php if($RPTENDMONTH == '01'){echo("selected='selected'");}?>>January</OPTION>
					  <OPTION VALUE="02" <?php if($RPTENDMONTH == '02'){echo("selected='selected'");}?>>February</OPTION>
					  <OPTION VALUE="03" <?php if($RPTENDMONTH == '03'){echo("selected='selected'");}?>>March</OPTION>
					  <OPTION VALUE="04" <?php if($RPTENDMONTH == '04'){echo("selected='selected'");}?>>April</OPTION>
					  <OPTION VALUE="05" <?php if($RPTENDMONTH == '05'){echo("selected='selected'");}?>>May</OPTION>
					  <OPTION VALUE="06" <?php if($RPTENDMONTH == '06'){echo("selected='selected'");}?>>June</OPTION>
					  <OPTION VALUE="07" <?php if($RPTENDMONTH == '07'){echo("selected='selected'");}?>>July</OPTION>
					  <OPTION VALUE="08" <?php if($RPTENDMONTH == '08'){echo("selected='selected'");}?>>August</OPTION>
					  <OPTION VALUE="09" <?php if($RPTENDMONTH == '09'){echo("selected='selected'");}?>>September</OPTION>
					  <OPTION VALUE="10" <?php if($RPTENDMONTH == '10'){echo("selected='selected'");}?>>October</OPTION>
					  <OPTION VALUE="11" <?php if($RPTENDMONTH == '11'){echo("selected='selected'");}?>>November</OPTION>
					  <OPTION VALUE="12" <?php if($RPTENDMONTH == '12'){echo("selected='selected'");}?>>December</OPTION>
				</SELECT>
				<SELECT NAME="RPTENDYEAR">
					  <OPTION VALUE="">--Year--</OPTION>
					  <?php $CurrYear = date("Y");
					     $x = 0;
						 WHILE ($CurrYear-$x >= 1989){
						 ?>
						 <OPTION VALUE='<?php echo $CurrYear-$x;?>' 
								     <?php if($RPTENDYEAR == $CurrYear-$x){echo("selected='selected'");}?>>
									 <?php echo $CurrYear-$x;?></OPTION>
						 <?php
						 $x++;
						 }
						 ?>
				</SELECT>
				</P>
			</DIV>
			<div id='reportToMe_opt' style='display:none'><input type='checkbox' id='reportToMe' name='reportToMe' <?php if($reportToMe){echo "checked";}?> ><label for='reportToMe'>Report To Me Only</label><BR></div>
			<div id='financials_opt' style='display:none'><input type='checkbox' id='financials' name='financials' <?php if($financials){echo "checked";}?> ><label for='financials'>Show Financials</label></div>
			<div id='staffVaction_options'  style='display:none'>
				YEAR:
				<SELECT NAME="vac_year">
					  <OPTION VALUE="">--Year--</OPTION>
					  <?php $CurrYear = date("Y");
					     $x = 0;
						 WHILE ($x < 3){
						 ?>
						 <OPTION VALUE='<?php echo $CurrYear-$x;?>' 
								     <?php if($RPTYEAR == $CurrYear-$x){echo("selected");}?>>
									 <?php echo $CurrYear-$x;?></OPTION>
						 <?php
						 $x++;
						 }
						 ?>	<?php echo (date("Y") -4);?></OPTION>
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
		<!--
		// Set focus to the project code field
		//document.getElementById('DESGCODE').focus();
		
		function CheckForm(form, subType){
			if($("#repchoice").val() == "") {
				alert("Please select a report from the drop-down list");
				return false;
			}
			if(!form.DESGCODE.value && $("#repchoice").val() != "StaffList"  && $("#repchoice").val() != "StaffVacation" && $("#repchoice").val() != "StaffFinancialHealth"){
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
					showHideFields(["#staffaccount","#monthyear","#output","#buttonsDownload","#buttonsPreview"]);
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
				}			
			});			
		}).change();
		
		function showHideFields(show) {
			/* The list of field groupings that can be shown or hidden for different reports */
			var fields = ["#staffaccount", "#monthyear", "#output", "#daterange", "#financials_opt", 
						"#reportToMe_opt", "#staffVaction_options", "#staffHealth_options", "#message",
						"#dieHelpButton","#buttonsDownload","#buttonsPreview","#buttonsCheck"];
			
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
		//-->
		</SCRIPT>
		</div>
	</div>
<!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>			
<?php get_footer(); 
?>