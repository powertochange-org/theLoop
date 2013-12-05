<?php
/*
*Template Name: Financial_Reports
*
*Base page for the financial reporting system. When a reportform is submitted, the report
*producer is called in rs_functions.php and the report is downloaded or an error is displayed.
*
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
	&& $_POST['REPORT'] != "StaffList")
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

  //Check for returned error message 
  $errorMsg = produceRSReport('/General/Staff List', $_POST['OutputFormat'], $reportParams, true, $SERVER_SQL2012);
  if(!isset($errorMsg)){
	exit;
  }
  $error = $errorMsg;
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
$RPTMONTH = $_POST["RPTMONTH"] ? $_POST["RPTMONTH"] : date("m");
$RPTYEAR = $_POST["RPTYEAR"] ? $_POST["RPTYEAR"] : date("Y");
$RPTSTARTMONTH = $_POST["RPTSTARTMONTH"] ? $_POST["RPTSTARTMONTH"] : date("m");
$RPTSTARTYEAR = $_POST["RPTSTARTYEAR"] ? $_POST["RPTSTARTYEAR"] : date("Y");
$RPTENDMONTH = $_POST["RPTENDMONTH"] ? $_POST["RPTENDMONTH"] : date("m");
$RPTENDYEAR = $_POST["RPTENDYEAR"] ? $_POST["RPTENDYEAR"] : date("Y");
$RPTPERIOD = $_POST["RPTPERIOD"] ? $_POST["RPTPERIOD"] : 'MONTH';
$OUTPUTFRMT = $_POST['OutputFormat'];

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
				echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; 
			?>">
			<P>Choose Your Report:<BR>
			<SELECT ID="repchoice" NAME="REPORT" onChange="showHelpButton(this.selectedIndex == 4);">
                  <OPTION VALUE="">--Report Type--</OPTION>
                  <OPTION VALUE="DonorReport" <?php if($REPORT == 'DonorReport'){echo("selected");}?>>Monthly Donation Report</OPTION>
                  <OPTION VALUE="InvestorReport" <?php if($REPORT == 'InvestorReport'){echo("selected");}?>>13 Month Donor Report</OPTION>
                  <OPTION VALUE="MonthlyDonors" <?php if($REPORT == 'MonthlyDonors'){echo("selected");}?>>Recurring Monthly Donors</OPTION>
                  <OPTION VALUE="DetailedRangeReport" <?php if($REPORT == 'DetailedRangeReport'){echo("selected");}?>>Detailed Income and Expense</OPTION>
                  <OPTION VALUE="SummaryReport" <?php if($REPORT == 'SummaryReport'){echo("selected");}?>>Summary Income and Expense</OPTION>
                  <OPTION VALUE="AccountBalance" <?php if($REPORT == 'AccountBalance'){echo("selected");}?>>Account Balance</OPTION>
				  <OPTION VALUE="AccountDonors" <?php if($REPORT == 'AccountDonors'){echo("selected");}?>>Account Donors</OPTION>
				  <OPTION VALUE="StaffList" <?php if($REPORT == 'StaffList'){echo("selected");}?>>Staff List</OPTION>
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
                  <OPTION VALUE="01" <?php if($RPTMONTH == '01'){echo("selected");}?>>January</OPTION>
                  <OPTION VALUE="02" <?php if($RPTMONTH == '02'){echo("selected");}?>>February</OPTION>
                  <OPTION VALUE="03" <?php if($RPTMONTH == '03'){echo("selected");}?>>March</OPTION>
                  <OPTION VALUE="04" <?php if($RPTMONTH == '04'){echo("selected");}?>>April</OPTION>
                  <OPTION VALUE="05" <?php if($RPTMONTH == '05'){echo("selected");}?>>May</OPTION>
                  <OPTION VALUE="06" <?php if($RPTMONTH == '06'){echo("selected");}?>>June</OPTION>
                  <OPTION VALUE="07" <?php if($RPTMONTH == '07'){echo("selected");}?>>July</OPTION>
                  <OPTION VALUE="08" <?php if($RPTMONTH == '08'){echo("selected");}?>>August</OPTION>
                  <OPTION VALUE="09" <?php if($RPTMONTH == '09'){echo("selected");}?>>September</OPTION>
                  <OPTION VALUE="10" <?php if($RPTMONTH == '10'){echo("selected");}?>>October</OPTION>
                  <OPTION VALUE="11" <?php if($RPTMONTH == '11'){echo("selected");}?>>November</OPTION>
                  <OPTION VALUE="12" <?php if($RPTMONTH == '12'){echo("selected");}?>>December</OPTION>
                </SELECT>
				<SELECT NAME="RPTYEAR">
					  <OPTION VALUE="">--Year--</OPTION>
					  <?php $CurrYear = date("Y");
					     $x = 0;
						 WHILE ($CurrYear-$x >= 1989){
						 ?>
						 <OPTION VALUE='<?php echo $CurrYear-$x;?>' 
								     <?php if($RPTYEAR == $CurrYear-$x){echo("selected");}?>>
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
					  <OPTION VALUE="01" <?php if($RPTSTARTMONTH == '01'){echo("selected");}?>>January</OPTION>
					  <OPTION VALUE="02" <?php if($RPTSTARTMONTH == '02'){echo("selected");}?>>February</OPTION>
					  <OPTION VALUE="03" <?php if($RPTSTARTMONTH == '03'){echo("selected");}?>>March</OPTION>
					  <OPTION VALUE="04" <?php if($RPTSTARTMONTH == '04'){echo("selected");}?>>April</OPTION>
					  <OPTION VALUE="05" <?php if($RPTSTARTMONTH == '05'){echo("selected");}?>>May</OPTION>
					  <OPTION VALUE="06" <?php if($RPTSTARTMONTH == '06'){echo("selected");}?>>June</OPTION>
					  <OPTION VALUE="07" <?php if($RPTSTARTMONTH == '07'){echo("selected");}?>>July</OPTION>
					  <OPTION VALUE="08" <?php if($RPTSTARTMONTH == '08'){echo("selected");}?>>August</OPTION>
					  <OPTION VALUE="09" <?php if($RPTSTARTMONTH == '09'){echo("selected");}?>>September</OPTION>
					  <OPTION VALUE="10" <?php if($RPTSTARTMONTH == '10'){echo("selected");}?>>October</OPTION>
					  <OPTION VALUE="11" <?php if($RPTSTARTMONTH == '11'){echo("selected");}?>>November</OPTION>
					  <OPTION VALUE="12" <?php if($RPTSTARTMONTH == '12'){echo("selected");}?>>December</OPTION>
				</SELECT>
				<SELECT NAME="RPTSTARTYEAR">
					  <OPTION VALUE="">--Year--</OPTION>
					  <?php $CurrYear = date("Y");
					     $x = 0;
						 WHILE ($CurrYear-$x >= 1989){
						 ?>
						 <OPTION VALUE='<?php echo $CurrYear-$x;?>' 
								     <?php if($RPTSTARTYEAR == $CurrYear-$x){echo("selected");}?>>
									 <?php echo $CurrYear-$x;?></OPTION>
						 <?php
						 $x++;
						 }
						 ?>
				</SELECT>
				<BR><SPAN STYLE="width:50px; float:left">END:</SPAN>
				<SELECT NAME="RPTENDMONTH">
					  <OPTION VALUE="">--Month--</OPTION>
					  <OPTION VALUE="01" <?php if($RPTENDMONTH == '01'){echo("selected");}?>>January</OPTION>
					  <OPTION VALUE="02" <?php if($RPTENDMONTH == '02'){echo("selected");}?>>February</OPTION>
					  <OPTION VALUE="03" <?php if($RPTENDMONTH == '03'){echo("selected");}?>>March</OPTION>
					  <OPTION VALUE="04" <?php if($RPTENDMONTH == '04'){echo("selected");}?>>April</OPTION>
					  <OPTION VALUE="05" <?php if($RPTENDMONTH == '05'){echo("selected");}?>>May</OPTION>
					  <OPTION VALUE="06" <?php if($RPTENDMONTH == '06'){echo("selected");}?>>June</OPTION>
					  <OPTION VALUE="07" <?php if($RPTENDMONTH == '07'){echo("selected");}?>>July</OPTION>
					  <OPTION VALUE="08" <?php if($RPTENDMONTH == '08'){echo("selected");}?>>August</OPTION>
					  <OPTION VALUE="09" <?php if($RPTENDMONTH == '09'){echo("selected");}?>>September</OPTION>
					  <OPTION VALUE="10" <?php if($RPTENDMONTH == '10'){echo("selected");}?>>October</OPTION>
					  <OPTION VALUE="11" <?php if($RPTENDMONTH == '11'){echo("selected");}?>>November</OPTION>
					  <OPTION VALUE="12" <?php if($RPTENDMONTH == '12'){echo("selected");}?>>December</OPTION>
				</SELECT>
				<SELECT NAME="RPTENDYEAR">
					  <OPTION VALUE="">--Year--</OPTION>
					  <?php $CurrYear = date("Y");
					     $x = 0;
						 WHILE ($CurrYear-$x >= 1989){
						 ?>
						 <OPTION VALUE='<?php echo $CurrYear-$x;?>' 
								     <?php if($RPTENDYEAR == $CurrYear-$x){echo("selected");}?>>
									 <?php echo $CurrYear-$x;?></OPTION>
						 <?php
						 $x++;
						 }
						 ?>
				</SELECT>
				</P>
			</DIV>			
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
		document.getElementById('DESGCODE').focus();

		function showHelpButton(visible) {
			if (visible) {
				$("#dieHelpButton").show();
			} else {
				$("#dieHelpButton").hide();
			}
		}
		
		function CheckForm(form, subType){
			if(subType == "download" || form.REPORT.value == "AccountBalance")
				form.target = "_self";
			else {
				form.target = "_blank";
				/*
				form.target = "preview";
				$("#preview").slideDown();
				*/
			}
			if(!form.DESGCODE.value && $("#repchoice").val() != "StaffList"){
				alert("Please enter your ministry/staff account number, then click Run Report");
				return false;
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
					$("#DESGCODE").removeAttr("title");
				} else if($(this).val() == "InvestorReport"){
					$("#staffaccount").slideDown();
					$("#monthyear").slideDown();
					$("#output").slideDown();
					$("#daterange").slideUp();
					$("#DESGCODE").removeAttr("title");
				} else if($(this).val() == "MonthlyDonors"){
					$("#staffaccount").slideDown();
					$("#monthyear").slideUp();
					$("#output").slideDown();
					$("#daterange").slideUp();
					$("#DESGCODE").removeAttr("title");
				} else if($(this).val() == "DetailedRangeReport"){
					$("#staffaccount").slideDown();
					$("#monthyear").slideUp();
					$("#output").slideDown();
					$("#daterange").slideDown();
					$("#DESGCODE").removeAttr("title");
				} else if($(this).val() == "SummaryReport"){
					$("#staffaccount").slideDown();
					$("#monthyear").slideUp();
					$("#output").slideDown();
					$("#daterange").slideDown();
					$("#DESGCODE").removeAttr("title");
				} else if($(this).val() == "AccountBalance"){
					$("#staffaccount").slideDown();
					$("#monthyear").slideUp();
					$("#output").slideUp();
					$("#daterange").slideUp();				
					$("#DESGCODE").attr("title",'Indicate multiple accounts by separating them using commas');
				} else if($(this).val() == "AccountDonors"){
					$("#staffaccount").slideDown();
					$("#monthyear").slideUp();
					$("#output").slideDown();
					$("#daterange").slideDown();				
					$("#DESGCODE").removeAttr("title");
				} else if($(this).val() == "StaffList"){
					$("#staffaccount").slideUp();
					$("#monthyear").slideUp();
					$("#output").slideDown();
					$("#daterange").slideUp();				
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
