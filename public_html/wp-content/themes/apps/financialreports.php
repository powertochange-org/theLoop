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

elseif (!isset($error) && isset($_POST['REPORT']) && $_POST['REPORT'] == "StaffFinancialHealth") {
  require('financialreports/sql_report_functions.php');
  
  produceSQLReport('StaffFinancialHealth', $_POST['employee_number'], $_POST['report_month'] . '-01');
  exit;
}

//%elif-block (just a label)

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
$reportToMe = $_POST['reportToMe'];
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
			<SELECT ID="repchoice" NAME="REPORT" onChange="showHelpButton(this.selectedIndex == 4);">
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
				  <?php
				  if (in_array($user_id, array('jasonb','matthewc','annf'))) {
					  echo '<OPTION VALUE="">--TEST REPORTS--</OPTION>';
					  echo '<OPTION VALUE="StaffFinancialHealth" ';
					  if($REPORT == 'StaffFinancialHealth'){echo("selected='selected'");}
					  echo '>Staff Financial Health</OPTION>';
				  }
				  ?>
            </SELECT>
			<BUTTON TYPE="button" ID="dieHelpButton" style="display:none" onClick="window.open('/reports/detailed-income-and-expense-help/')")>Help on this report</BUTTON>
			</P>
			<DIV ID="staffaccount" STYLE="display:none">
				<P>Please enter your ministry/staff account number:<BR>  
					<INPUT TYPE="text" ID="DESGCODE" NAME="DESGCODE" MAXLENGTH="1000" SIZE="10" VALUE="<?php echo $_POST["DESGCODE"];?>">
				</P>
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
			<div id='reportToMe_opt' style='display:none'><input type='checkbox' id='reportToMe' name='reportToMe' checked ><label for='reportToMe'>Report To Me Only</label></div><BR>
			<div id='financials_opt' style='display:none'><input type='checkbox' id='financials' name='financials'><label for='financials'>Show Financials</label></div>
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
						 ?>				  										  								 	 <?php echo (date("Y") -4);?></OPTION>
				</SELECT>		
			</div>
			<div id='staffHealth_options' style='display:none'>
				Employee Number:
				<input type="text" name="employee_number" />
				<br />
				Report Month:
				<input type="text" name="report_month" value="2015-10" /> (in YYYY-MM format)
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
			<DIV ID="buttonsDownloadPreview" <?php if (isset($_POST['REPORT']) && $_POST['REPORT'] == "AccountBalance") { echo 'STYLE="display:none"'; } ?>>
				<INPUT TYPE="submit" ID="actionBtn" NAME="actionBtn" VALUE="Download" onClick="return CheckForm(this.form, 'download')">
				<INPUT TYPE="submit" ID="previewBtn" NAME="previewBtn" VALUE="View Online" onClick="return CheckForm(this.form, 'preview')">
			</DIV>
			<DIV ID="buttonsCheck" <?php if (!isset($_POST['REPORT']) || $_POST['REPORT'] != "AccountBalance") { echo 'STYLE="display:none"'; } ?>>
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

		function showHelpButton(visible) {
			if (visible) {
				$("#dieHelpButton").show();
			} else {
				$("#dieHelpButton").hide();
			}
		}
		
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
					$("#staffaccount").slideDown();
					$("#monthyear").slideDown();
					$("#output").slideDown();
					$("#daterange").slideUp();
					$("#reportToMe_opt").slideUp();
					$("#financials_opt").slideUp();
					$("#staffVaction_options").slideUp();
					$("#DESGCODE").removeAttr("title");
				} else if($(this).val() == "InvestorReport"){
					$("#staffaccount").slideDown();
					$("#monthyear").slideDown();
					$("#output").slideDown();
					$("#daterange").slideUp();
					$("#reportToMe_opt").slideUp();
					$("#financials_opt").slideUp();
					$("#staffVaction_options").slideUp();
					$("#DESGCODE").removeAttr("title");
				} else if($(this).val() == "MonthlyDonors"){
					$("#staffaccount").slideDown();
					$("#monthyear").slideUp();
					$("#output").slideDown();
					$("#daterange").slideUp();
					$("#reportToMe_opt").slideUp();
					$("#financials_opt").slideUp();
					$("#staffVaction_options").slideUp();
					$("#DESGCODE").removeAttr("title");
				} else if($(this).val() == "DetailedRangeReport"){
					$("#staffaccount").slideDown();
					$("#monthyear").slideUp();
					$("#output").slideDown();
					$("#daterange").slideDown();
					$("#reportToMe_opt").slideUp();
					$("#financials_opt").slideUp();
					$("#staffVaction_options").slideUp();
					$("#DESGCODE").removeAttr("title");
				} else if($(this).val() == "SummaryReport"){
					$("#staffaccount").slideDown();
					$("#monthyear").slideUp();
					$("#output").slideDown();
					$("#daterange").slideDown();
					$("#reportToMe_opt").slideUp();
					$("#financials_opt").slideUp();
					$("#staffVaction_options").slideUp();
					$("#DESGCODE").removeAttr("title");
				} else if($(this).val() == "AccountBalance"){
					$("#staffaccount").slideDown();
					$("#monthyear").slideUp();
					$("#output").slideUp();
					$("#daterange").slideUp();
					$("#reportToMe_opt").slideUp();
					$("#financials_opt").slideUp();
					$("#staffVaction_options").slideUp();
					$("#DESGCODE").attr("title",'Indicate multiple accounts by separating them using commas');
				} else if($(this).val() == "AccountDonors"){
					$("#staffaccount").slideDown();
					$("#monthyear").slideUp();
					$("#output").slideDown();
					$("#daterange").slideDown();				
					$("#reportToMe_opt").slideUp();
					$("#financials_opt").slideUp();
					$("#staffVaction_options").slideUp();
					$("#DESGCODE").removeAttr("title");
				} else if($(this).val() == "StaffList"){
					$("#staffaccount").slideUp();
					$("#monthyear").slideUp();
					$("#output").slideDown();
					$("#daterange").slideUp();
					$("#reportToMe_opt").slideDown();
					$("#financials_opt").slideDown();
					$("#staffVaction_options").slideUp();
				}  else if($(this).val() == "StaffVacation"){
					$("#staffaccount").slideUp();
					$("#monthyear").slideUp();
					$("#output").slideDown();
					$("#daterange").slideUp();
					$("#financials_opt").slideUp();
					$("#reportToMe_opt").slideDown();
					$("#staffVaction_options").slideDown();
				} else if ($(this).val() == "StaffFinancialHealth"){
					$("#staffaccount").slideUp();
					$("#monthyear").slideUp();
					$("#daterange").slideUp();
					$("#financials_opt").slideUp();
					$("#reportToMe_opt").slideUp();
					$("#staffVaction_options").slideUp();
					$("#output").slideUp();
					$("#staffHealth_options").slideDown();
				}
				
				/* Buttons */
				if ($(this).val() == "AccountBalance"){
					$("#buttonsDownloadPreview").slideUp();
					$("#buttonsCheck").slideDown();
				} else {
					$("#buttonsDownloadPreview").slideDown();
					$("#buttonsCheck").slideUp();
				}
				
			});
		}).change();
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