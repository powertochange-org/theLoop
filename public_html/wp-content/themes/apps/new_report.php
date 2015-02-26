<?php
/*
*Template Name: zNew_report
*
*Base page for the financial reporting system. When a reportform is submitted, the report
*producer is called in rs_functions.php and the report is downloaded or an error is displayed.
*
*/

/*

(note: AccountBalance is different than the rest of the reports)

to an another report you need to:

	>add it to reports array
		(note: the param list has the ids of div that have the input element or a constant)
	if you need another param type
		> create a new div of class var
			contain the elment to display for the parameter
		> add to the javascript function "getValue" get the value of parameter from the input elements
		> add another hidden input at the end of the form

*/

$current_user = wp_get_current_user();
$user_id = $current_user->user_login;

require('financialreports/rs_functions.php');

$reports = array(
	
	array ('name' => '','display_name' => '--DONATION REPORTS--'),
	
	array (
		'name' => 'DonorReport',
		'display_name' => 'Monthly Donation Report',
		'path' => '/Donors/MonthlyDonationReport',
		'server' => null,
		'comment' => '',
		'param' => array (
				'StartDate' => '#datestart',
				'EndDate' => '#dateend',
				'ProjectCode' => '#staffaccount'
				)
	),
	
	array (
		'name' => 'InvestorReport',
		'display_name' => '13 Month Donor Report',
		'path' => '/Donors/13MonthDonorReport',
		'server' => null,
		'comment' => '',
		'param' => array (
				'SelectMonthYear' => '#monthyear',
				'ProjectCode' => '#staffaccount'
				)
	),
	
	array (
		'name' => 'MonthlyDonors',
		'display_name' => 'Recurring Monthly Donors',
		'path' => '/Donors/ProjectMonthlyDonorst',
		'server' => null,
		'comment' => '',
		'param' => array (
				'ReportLevel1' => '',
				'ProjectCode' => '#staffaccount'
				)
		
	),
	
	array (
		'name' => 'AccountDonors',
		'display_name' => 'Account Donors',
		'path' => '/Donors/AccountDonors',
		'server' => null,
		'comment' => '',
		'param' => array (
				'StartDate' => '#datestart',
				'EndDate' => '#dateend',
				'ProjectCode' => '#staffaccount'
				)
	),
	
	array ('name' => '','display_name' => '--FINANCIAL REPORTS--'),
	
	array (
		'name' => 'DetailedRangeReport',
		'display_name' => 'Detailed Income and Expense',
		'path' => '/Financial/Detailed Income and Expense',
		'server' => null,
		'comment' => '',
		'param' => array (
				'StartDate' => '#datestart',
				'EndDate' => '#dateend',
				'ProjectCode' => '#staffaccount'
				)
	),
	
	array (
		'name' => 'SummaryReport',
		'display_name' => 'Summary Income and Expense',
		'path' => '/Financial/Summary Income and Expense',
		'server' => $SERVER_SQL2012,
		'comment' => '',
		'param' => array (
				'StartDate' => '#datestart',
				'EndDate' => '#dateend',
				'ProjectCode' => '#staffaccount'
				)
	),
	
	array (
		'name' => 'AccountBalance',
		'display_name' => 'Account Balance',
		'comment' => '',
		'param' => array (
				'ProjectCode' => '#staffaccount'
				)
	),
	
	array ('name' => '','display_name' => '--STAFF REPORTS--'),
	
	array (
		'name' => 'StaffList',
		'display_name' => 'Staff List',
		'path' => '/General/Staff List',
		'server' => $SERVER_SQL2012,
		'comment' => '',
		'param' => array (
				'ReportToMeOnly' => '#reportToMe_opt',
				'ShowFinancials' => '#financials_opt',
				)
	),
	
	array (
		'name' => 'StaffVacation',
		'display_name' => 'Staff Vacation and Wellness',
		'path' => '/General/Staff Vacation and Wellness',
		'server' => $SERVER_SQL2012,
		'comment' => '',
		'param' => array (
				'ReportToMeOnly' => '#reportToMe_opt',
				'Year' => '#year_options',
				'Category' => 3
				)
	)

) ;

//Set proper output format for preview
if(isset($_POST['previewBtn'])){
	$_POST['output_form'] = "HTML4.0";
}

//Check for proper account number length
unset($error);


if (isset($_POST['REPORT'])){
	if ( $_POST['REPORT'] == "AccountBalance") {
	  //Check for returned error message
	  $reportReturn = accountBalance($_POST['staffaccount_form'], $user_id);
	  if(substr($reportReturn, 0, 5) == "ERROR"){
			$error = $reportReturn;
	  } 
	  else {
			$reportResult = $reportReturn;
	  }
}
	else {
		//get selected report;
		foreach ($reports as $r){
			if ($r['name'] == $_POST['REPORT']){
				$selected_report = $r;
				break;
			}
		}
		foreach ($selected_report['param'] as $key => $value){
			if (substr($value, 0, 1) == '#'){
				$reportParams[$key] = $_POST[substr($value, 1)."_form"];
			}
			else {
				$reportParams[$key]= $value;
			}
		}
		$reportParams['ExecuteAsUser'] = $user_id;
		
		 //Check for returned error message
		$errorMsg = produceRSReport($selected_report['path'], $_POST['output_form'], $reportParams, true, $selected_report['server']);
		if(!isset($errorMsg)){
			exit;
		}
		$error = $errorMsg;
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

$RPTMONTH = $_POST["RPTMONTH"] ? $_POST["RPTMONTH"] : date("m");
$RPTYEAR = $_POST["RPTYEAR"] ? $_POST["RPTYEAR"] : date("Y");
$RPTSTARTMONTH = $_POST["RPTSTARTMONTH"] ? $_POST["RPTSTARTMONTH"] : date("m");
$RPTSTARTYEAR = $_POST["RPTSTARTYEAR"] ? $_POST["RPTSTARTYEAR"] : date("Y");
$RPTENDMONTH = $_POST["RPTENDMONTH"] ? $_POST["RPTENDMONTH"] : date("m");
$RPTENDYEAR = $_POST["RPTENDYEAR"] ? $_POST["RPTENDYEAR"] : date("Y");
$RPTPERIOD = $_POST["RPTPERIOD"] ? $_POST["RPTPERIOD"] : 'MONTH';

get_header(); ?>
	<div id="content">
		<div id="main-content">	
			<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><!-- ?php the_title(); ? --></a></h1>
			<style type="text/css">
				.var{
					display:none;
				}
			</style>
			<script src="https://code.jquery.com/jquery-latest.js"></script>
			<?php
			if (isset($error)) {
                 echo "<span style=\"color: red\">$error</span><p>";
				}
             ?>
			<FORM METHOD="post" ACTION="<?php 
				if ($_SERVER['HTTPS'] == 'on') {
					echo "https://";
				} else {
					echo "http://";
				}
				echo $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
			?>">
				<div>
					<P>Choose Your Report:<BR>
					<select ID="repchoice" NAME="REPORT" onChange="showReport();">
						  <?php foreach ($reports as $r){
							echo "<option value='$r[name]'>$r[display_name]</option>\n";
						  }
						  ?>
					</select>
				
				</div>
				<div id='comment'>
					
				</div>
				<DIV ID="staffaccount" class='var'>
					<P>Please enter your ministry/staff account number:<BR>  
						<INPUT TYPE="text" ID="DESGCODE" MAXLENGTH="1000" SIZE="10">
					</P>
				</DIV>
				<DIV ID="monthyear" class='var'>
					<P>Choose the month and year to report on.<BR>
					<SELECT id="RPTMONTH">
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
					<SELECT id="RPTYEAR">
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
				<DIV ID="datestart" class='var'>
					<P>
					<SPAN STYLE="width:50px; float:left">START:</SPAN>
					<SELECT id="RPTSTARTMONTH">
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
					<SELECT id="RPTSTARTYEAR">
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
				</div>
				<div ID="dateend" class='var'>
					<P>
					<BR><SPAN STYLE="width:50px; float:left">END:</SPAN>
					<SELECT id="RPTENDMONTH">
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
					<SELECT id="RPTENDYEAR">
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
				<div id='reportToMe_opt' class='var'><p><input type='checkbox' id='reportToMe' checked ><label for='reportToMe'>Report To Me Only</label></p></div><BR>
				<div id='financials_opt' class='var'><p><input type='checkbox' id='financials'><label for='financials'>Show Financials</label></p></div>
				<div id='year_options' class='var'><p>
					YEAR:
					<SELECT id="vac_year">
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
				</p></div>
				<DIV ID="output" class='var'>
					<P>
					Output Format:
					<select id="OutputFormat">
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
				<BR>
				<input type="hidden" disabled name="staffaccount_form" id="staffaccount_form" value="">
				<input type="hidden" disabled name="monthyear_form" id="monthyear_form" value="">
				<input type="hidden" disabled name="datestart_form" id="datestart_form" value="">
				<input type="hidden" disabled name="dateend_form" id="dateend_form" value="">
				<input type="hidden" disabled name="reportToMe_opt_form" id="reportToMe_opt_form" value="">
				<input type="hidden" disabled name="financials_opt_form" id="financials_opt_form" value="">
				<input type="hidden" disabled name="year_options_form" id="year_options_form" value="">
				<input type="hidden" name="output_form" id="output_form" value="">
			</FORM>
			<div id="reportOutput"><p>
				<?php if ($reportResult) { print $reportResult; } ?>
			</p></div>
			<script type="text/javascript">
			 
			var reports = <?php 
			echo " new Array ( \n";
			foreach ($reports as $r){
			
				//skipping the labels;
				if ($r['name'] == ""){
					continue;
				}
				echo "{ name :'$r[name]', \n";
				echo "comment: '$r[comment]', \n";
				echo "param  : new Array (\n";
				foreach ($r['param'] as $x => $p){
					if (substr($p, 0, 1) == '#'){
						echo "\t'$p',\n";
					}
				}
				echo "\tnull)},\n";
			 }
			 echo "null);";
			?>
			
			var selected_report = null;
			
			function getValue(e){
				if (e == 'staffaccount'){
					return document.getElementById('DESGCODE').value;
				}
				if (e == 'monthyear'){
					return document.getElementById('RPTYEAR').value + "-" + document.getElementById('RPTMONTH').value + "-01";
				}
				if (e == 'datestart'){
					return document.getElementById('RPTSTARTYEAR').value + "-" + document.getElementById('RPTSTARTMONTH').value + "-01";
				}
				if (e == 'dateend'){
					var d = new Date(document.getElementById('RPTENDYEAR').value, document.getElementById('RPTENDMONTH').value, 0).getDate();
					return document.getElementById('RPTENDYEAR').value + "-" + document.getElementById('RPTENDMONTH').value + "-" + d;
				}
				if (e == 'reportToMe_opt'){
					if(document.getElementById('reportToMe').checked){
						return 'true';
					}
					else {
						return 'false';
					}
				}
				if (e == 'financials_opt'){
					if(document.getElementById('financials').checked){
						return 'true';
					}
					else {
						return 'false';
					}
				}
				if (e == 'year_options'){
					return document.getElementById('vac_year').value;
				}
				if (e == 'output'){
					return document.getElementById('OutputFormat').value;
				}
				console.log("not found: " + e);
			}
			
			function showReport(){
				var r = document.getElementById('repchoice').value;
				$(".var").hide();
				
				selected_report = null;
				for (var i = 0; i < reports.length; i ++){
					if(reports[i] == null){
						continue;
					}
					if (reports[i].name == r){
						selected_report = reports[i];
						break;
					}
				}
				
				if (selected_report == null){
					document.getElementById('comment').innerHTML = "<p></p>";
					return;
				}
				
				document.getElementById('comment').innerHTML = "<p>" + selected_report.comment + "</p>";
				
				if (r == 'AccountBalance'){
					$('#staffaccount').slideDown();
					$("#DESGCODE").attr("title",'Indicate multiple accounts by separating them using commas');
					$("#buttonsDownloadPreview").slideUp();
					$("#buttonsCheck").slideDown();
					$("#reportOutput").slideDown();
				}
				else {
					$("#DESGCODE").removeAttr("title");
					$("#buttonsDownloadPreview").slideDown();
					$("#buttonsCheck").slideUp();
					$("#reportOutput").slideUp();
					
					for (var j = 0;  j < selected_report.param.length; j ++){
						if(selected_report.param[j] == null){
							continue;
						}
						$(selected_report.param[j]).slideDown();
					}
					$('#output').slideDown();
				}
			}
			
			function CheckForm(form, subType){
				if(selected_report == null){
					alert('Please select report');
					return false;
				}
				if(subType == "download" || form.REPORT.value == "AccountBalance") { 
					form.target = "_self";
				}
				else {
					form.target = "_blank";
				}
				
				for (var j = 0;  j < selected_report.param.length; j ++){
					if(selected_report.param[j] == null){
						continue;
					}
					document.getElementById(selected_report.param[j].substr(1) + "_form").disabled = false;
					if (getValue(selected_report.param[j].substr(1)) == ""){
						alert('Please fill in fields'); 
						return false;
					}
					document.getElementById(selected_report.param[j].substr(1) + "_form").value = getValue(selected_report.param[j].substr(1));
				}
				document.getElementById("output_form").value = getValue('output');
				return true;
			}
			</script>
	</div>
<!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>			
<?php get_footer(); 
?>
