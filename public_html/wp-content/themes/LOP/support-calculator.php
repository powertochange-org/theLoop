<?php

include('functions/functions.php');
require('pdf/fpdf.php');


/*
*Template Name: Support_Calculator
*
*Author: matthew.chell
*
*Description: A calculator to find out how much support a person has to raise.
*	
*Note: HCSA has been changed to Medical Allowance
*/

$support_constant = array (
	"provinces" => array (
		"AB" => 0,
		"BC" => 1,
		"MB" => 2,
		"NB" => 3,
		"NL" => 4,
		"NT" => 5,
		"NS" => 6,
		"NU" => 7,
		"ON" => 8,
		"PE" => 9,
		"QC" => 10,
		"SK" => 11,
		"YT" => 12
	),

	"coverage" => array (
		"single", 
		"couple",
		"family"
	)
);

$support_dataID = null;


generate_pdf();

function generate_pdf(){

	$data = explode('+',  htmlspecialchars($_GET["download"]));

	//echos back the data for the user to download

	//todo style not user stored data\
	// todo remove var_dump($data);
	if (count($data) == 26){
		$label = Array('Name:', 'Hours per week:', 'Province:', 'Hours per week (spouse):', 'Staff Account:', 'Benefit Coverage:', 'Ministry:', 'Decline Benefits:', 'Monthly Allowance/Salary', 'Employer Paid CPP/EI', 'Monthly Allowance/Salary - Spouse', 'Employer Paid CPP/EI - Spouse', 'Extended Health', 'Provincial Medical', 'Medical Allowance', 'Worker\'s Compensation', 'Staff Conference', 'MPD correspondence', 'Reimbursable Ministry Expenses', 'Subtotal', 'Central Resource Charge', 'Monthly Support Goal', 'Solid Monthly Support', 'Total Funds Yet to be Raised', 'Bridge Amount', 'Percent Supported');
		$pdf = new FPDF();
		$pdf->AddPage();
		$pdf->Image(get_template_directory().'\res\footer-logo.png'); //todo change
		$pdf->SETXY(60, 15);
		$pdf->SetFont('Arial','',16);
		$pdf->Write(5,'Support Goal Calculator');
		$pdf->SETY(25);
		$pdf->LN();
		$pdf->LN();
		$pdf->SetFont('Arial','',12);
		$dataMaxWidth = 0;
		foreach ($data as $d){
			if ($dataMaxWidth < $pdf->GetStringWidth($d)){
				$dataMaxWidth = $pdf->GetStringWidth($d);
			}
		}
		$labelTopMaxWidth = 0;
		for ($i = 0; $i < 8; $i ++){
			if ($labelTopMaxWidth < $pdf->GetStringWidth($label[$i])){
				$labelTopMaxWidth = $pdf->GetStringWidth($label[$i]);
			}
		}
		$labelBotMaxWidth = 0;
		for ($i = 8; $i < count($label); $i ++){
			if ($labelBotMaxWidth < $pdf->GetStringWidth($label[$i])){
				$labelBotMaxWidth = $pdf->GetStringWidth($label[$i]);
			}
		}
		for ($i = 0; $i < count($label); $i ++){
			if ($i < 8){
				$border = 1;
				$pdf->Cell($labelTopMaxWidth + 2, 6,  $label[$i], $border);
				if ($i == 2){
					$pdf->Cell($dataMaxWidth + 2, 6,  getProvinceString($data[2]), $border);
				}
				else {
					$pdf->Cell($dataMaxWidth + 2, 6,  $data[$i], $border);
				}
			}
			else {
				if ($i == 21 || $i == 23){
					$pdf->SetFillColor(217);
					$fill = true;
				}
				else{
					$fill = false;
				}
				$border = 0;
				$pdf->Cell($labelBotMaxWidth, 6,  $label[$i], $border, 0, 'L', $fill);
				$pdf->Cell($dataMaxWidth, 6,  $data[$i], $border, 0, 'R', $fill);
			}
			
			if (($i + 1) % 2 == 0 or $i > 7){
				$pdf->LN();
			}
			if ($i == 7){
				$pdf->LN();
			}
		}
		$pdf->LN();
		$pdf->Write(5,'Confidential');
		$pdf->Output();
		exit;
	}
}
?>
<?php get_header();
include('functions/js_functions.php'); ?>
<div id="content">
    <div id="content-left">
	<div id="main-content">
		<h1 class="replace" style="float:left"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
		<BR><BR>

		<?php
		//todo:
		//error handeling
		//couples joining and unjoining
		
		$current_user = wp_get_current_user();
		
		function setProvince(){
			global $support_constant;
			//if foreign
			if (getFieldEmployee("country") == "CA"){
				return $support_constant['provinces'][getFieldEmployee("province")];
			}
			return 13;
		}
		
		function getProvinceString($prov){
			global $support_constant;
			if ($prov == 13){
				return 'Foreign';
			} 
			return array_keys($support_constant['provinces'])[$prov];
		}
		
		
		parseDataInput();
		
		function parseDataInput(){
			global $wpdb,$current_user, $support_dataID;
			$ID = $current_user->ID;
			//todo error handling
			$data = explode('+',  mysql_real_escape_string(htmlspecialchars($_GET["data"])));	
			
			$support_dataID = getDataID($ID);
			if ($support_dataID == null){
				$spouse = getSpouse();
				if($spouse != -1){
					$support_dataID = getDataID($spouse);
					if ($support_dataID == null){
						//user has spouse but neither has id
						$sql = "INSERT INTO `support_calculator`(`id`, `hours`, `hours_s`, `coverage`, `decline`, `salary`, `salary_s`, `hcsa`, `conference`, `mpd`, `expenses`, `support`) VALUES (0,0,0,0,0,0,0,0,90,100,100,0)"; //defaulting mpd and expenses to 100 and conference to 90
						$wpdb->get_results($sql);
						$support_dataID = $wpdb->insert_id;
						update_user_meta( $ID, 'support_calculator_id', $support_dataID);
						update_user_meta( $spouse, 'support_calculator_id', $support_dataID);
					}
					else{
						//spouse has id but not user
						update_user_meta( $ID, 'support_calculator_id', $support_dataID);
					}
				}
				else {
					//user has no spouse or id
					$sql = "INSERT INTO `support_calculator`(`id`, `hours`, `hours_s`, `coverage`, `decline`, `salary`, `salary_s`, `hcsa`, `conference`, `mpd`, `expenses`, `support`) VALUES (0,0,0,0,0,0,0,0,90,100,100,0)"; //defaulting mpd and expenses to 100 and conference to 90
					$wpdb->get_results($sql);
					$support_dataID = $wpdb->insert_id;
					update_user_meta( $ID, 'support_calculator_id', $support_dataID);
				}
			}
			if (count($data) < 11){
				return; //no data
			}
			//save data in table
			
			for($i = 0; $i < count($i); $i ++){
				$data[$i] = parseFloat($data[$i]);
			}
			
			$sql = "UPDATE `support_calculator` SET `hours`=".$data[0].",`hours_s`=".$data[1].",`coverage`=".$data[2].",`decline`=".$data[3].",`salary`=".$data[4].",`salary_s`=".$data[5].",`hcsa`=".$data[6].",`conference`=".$data[7].",`mpd`=".$data[8].",`expenses`=".$data[9].",`support`=".$data[10]." WHERE `id`=".$dataID;
			$wpdb->get_results($sql);
			
			
			
			
			echo '<BR>Data Saved!<BR>';
		}
		
		//gets the support_dataID of a user only not also their spouse
		function getDataID($ID){
			$result = get_user_meta($ID, 'support_calculator_id', true);
			if ($result == ""){
				return null;
			}
			return $result;
		}
		
		function getData($field){
			//parseDataInput() must run before this function
			global $wpdb, $support_dataID;
			$sql = "SELECT `".$field."` FROM `support_calculator` WHERE `id` = '". $support_dataID. "'";
			$result = $wpdb->get_results($sql);
			return $result[0]->$field;
		}
		
		if (isAdmin()){
			include('admin-interface/support-calculator-admin.php');
		}
		
		//these next six functions just help generate the table
		function printHealthTax(){
			global $support_constant;
			foreach (array_keys($support_constant['provinces']) as $pro){
				$out .= '<label for="set_health_tax_'.$pro.'">'.$pro.'<input type="text" name="set_health_tax_'.$pro.'" id="set_health_tax_'.$pro.'" title='.$pro.' value="'.getConstant("health_tax_".$pro).'"></label><BR>';
			}
			return $out;
		}

		function printHealthTaxSet(){
			global $support_constant;
			foreach (array_keys($support_constant['provinces']) as $pro){
				$out .= 'get_value_float("set_health_tax_'.$pro.'"),';
			}
			return $out;
		}

		function printHealthPreSet(){
			global $support_constant;
			foreach (array_keys($support_constant['provinces']) as $pro){
				$out .= getConstant("health_tax_".$pro).", ";
			}
			return $out;
		}

		function printWorkers(){
			global $support_constant;
			foreach (array_keys($support_constant['provinces']) as $pro){
				$out .= '<label for="set_workers_tax_'.$pro.'">'.$pro.'<input type="text" name="set_workers_rate_'.$pro.'" id="set_workers_rate_'.$pro.'" title='.$pro.' value="'.getConstant("workers_rate_".$pro).'"></label><BR>';
			}
			return $out;
		}
			
		function printWorkersSet(){
			global $support_constant;
			foreach (array_keys($support_constant['provinces']) as $pro){
				$out .= 'get_value_float("set_workers_rate_'.$pro.'"),';
			}
			return $out;
		}

		function printWorkersPreSet(){
			global $support_constant;
			foreach (array_keys($support_constant['provinces']) as $pro){
				$out .= getConstant("workers_rate_".$pro).", ";
			}
			return $out;
}
		?>
		<script type="text/javascript">
		//constants for the provinces
		var AB = 0;
		var BC = 1;
		var MB = 2;
		var NB = 3;
		var NL = 4;
		var NT = 5;
		var NS = 6;
		var NU = 7;
		var ON = 8;
		var PE = 9;
		var QC = 10;
		var SK = 11;
		var YT = 12;
		var FR = 13; //foreign
	
		//constants for the coverage
		var SINGLE = 0;
		var COUPLE = 1;
		var FAMILY = 2;
		
		//constants for calculations the original value are beside the description of the variable
		var cpp_rate = <?php echo getConstant("cpp_rate") ?> //0.0495; //Canada Pension Plan rate
		var cpp_max = <?php echo getConstant("cpp_max") ?> //51100;   //Canada Pension Plan 
									//maximum pensionable earnings (annual)
		var cpp_exempt = <?php echo getConstant("cpp_exempt") ?> //3500; //Canada Pension Plan exemption (annual)
		
		var ei_rate1  = <?php echo getConstant("ei_rate1") ?> //0.0188; //Employment Insurance rate (EE rate)
		var ei_rate2 = <?php echo getConstant("ei_rate2") ?> //1.201;  //Employment Insurance rate (ER rate)
		var ei_max = <?php echo getConstant("ei_max") ?> //47400;    //Employment Insurance maximum insurable earnings (annual)
		
		//Extended Health Coverage
		var ehc = new Array(<?php echo getConstant("ehc_single") ?>, <?php echo getConstant("ehc_couple") ?>, <?php echo getConstant("ehc_family") ?>);
			//= new Array(100, 220, 260); //single, couple, family
		var ehc_MB = <?php echo getConstant("ehc_MB") ?> //1; //Manitoba tax on EHC
		
		// only Ontario and Quebec have a tax on Benefit Costs right now
		var health_tax = new Array(<?php echo printHealthPreSet().getConstant("health_tax_FR") ?>);
			//= new Array(1, 1, 1, 1, 1, 1, 1, 1, 1.08, 1, 1.09, 1, 1, 1);
		
		var part_time = <?php echo getConstant("part_time") ?> //25;  //Less this number of hours is part time
		
		var add_rate = <?php echo getConstant("add_rate") ?> //0.04 //AD&D rate per $1000
		var life_rate = <?php echo getConstant("life_rate") ?> //0.215 //Life rate per $1000
		var life_max = <?php echo getConstant("life_max") ?> //150000 // Life maximum 2 x annual salary
		
		//Depedents life
		var dept_life = new Array(<?php echo getConstant("dept_life_single") ?>, <?php echo getConstant("dept_life_couple") ?>, <?php echo getConstant("dept_life_family") ?>);
			//= new Array(0, 9.7, 19.4); //single, couple, family
		
		//Province Medical
		var medical_ON = <?php echo getConstant("medical_ON") ?> //0.0195; //Ontario medical rate
		var medical_QC = <?php echo getConstant("medical_QC") ?> //0.027; //Quebec medical rate
		var medical_BC = new Array(<?php echo getConstant("medical_BC_single") ?>, <?php echo getConstant("medical_BC_couple") ?>, <?php echo getConstant("medical_BC_family") ?>);
			//= new Array(66.50, 120.50, 133.00);
								//British Columbia medical is a flat rate
								//single, couple, family
		
		//Right now only BC has workers compensation
		//rate per $100
		var workers_rate = new Array(<?php echo printWorkersPreSet().getConstant("workers_rate_FR") ?>);
			//= new Array(0, 0.25, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
			
		var cr_charge = <?php echo getConstant("cr_charge") ?> //0.14; //Central Resource Charge
		
		
		var province = <?php echo setProvince(); ?>; //not stored
		
		//these are the values inputed by the users and will be stored
		var hours = <?php echo getData("hours"); ?>;
		var hours_s = <?php echo getData("hours_s"); ?>;
		var coverage = <?php echo getData("coverage"); ?>;
		var decline = <?php echo getData("decline"); ?>;
		var salary = <?php echo getData("salary"); ?>;
		var salary_s = <?php echo getData("salary_s"); ?>;
		var hcsa = <?php echo getData("hcsa"); ?>;
		var conference = <?php echo getData("conference"); ?>;
		var mpd = <?php echo getData("mpd"); ?>;
		var expenses = <?php echo getData("expenses"); ?>;
		var support = <?php echo getData("support"); ?>;
		
		//these are the calculated values (not stored)
		var cpp;
		var cpp_s;
		var health;
		var medical;
		var workers;
		var subtotal;
		var charge;
		var total;
		var tobe_raised;
		var bridge;
		var percent;
		
		//monthly salary
		function get_cpp_ei(salary) {
			var cpp_ei = (Math.min(salary, cpp_max) - cpp_exempt / 24) 
				* cpp_rate; //24 months
			cpp_ei += Math.min(salary, cpp_max) * ei_rate1 * ei_rate2;
			return Math.max(0, cpp_ei);
		}

		function get_ehc(){
			if (province == FR || decline){
				return 0;
			}
			if (province == MB){
				console.log("MB");
				return ehc[coverage] * ehc_MB;
			}
			return ehc[coverage];
		}
		
		function get_benefit_cost(){
			if (hours < part_time){
				return 0;
			}
			var total = get_ehc();
			total += salary * 24 * add_rate / 1000; //24 months, per $1000
			total += Math.min(life_max, salary * 24) * life_rate / 1000; //24 months, per $1000
			total += dept_life[coverage];
			
			//spouse
			total += salary_s * 24 * add_rate / 1000; //24 months, per $1000
			total += Math.min(life_max, salary_s * 24) * life_rate / 1000; //24 months, per $1000
			total += dept_life[coverage];
			return total * health_tax[province];						
		}
		
		function get_medical(){
			//Ontario and Quebec medical percentage of salary while
			//British Columbia it is a flat rate
			if (hours < part_time){
				return 0;
			}
			if (province == ON){
				return (salary + salary_s) * medical_ON;
			}
			if (province == QC){
				return (salary + salary_s) * medical_QC;
			}
			if (province == BC){
				return medical_BC[coverage];
			}
			return 0;
		}
		
		function get_workers(){
			return salary * workers_rate[province] / 100; //per $100
		}
		
		function set_value(element, value){
			//todo look nice
			document.getElementById(element).innerHTML = value.toFixed(2);
		}
		
		function calculate(){
			
			province = parseInt(document.getElementById("input_province").value);
			hours = get_value_float("input_hours");
			hours_s = get_value_float("input_hours_s");
			coverage = parseInt(document.getElementById("input_coverage").value);
			decline = document.getElementById("input_decline").checked;
			
			hcsa = get_value_float("input_hcsa");
			
			salary = get_value_float("input_salary");
			cpp = get_cpp_ei(salary + hcsa);
			set_value("output_cpp", cpp);
			
			salary_s = get_value_float("input_salary_s");
			cpp_s = get_cpp_ei(salary_s);
			set_value("output_cpp_s", cpp_s);
			
			health = get_benefit_cost();
			set_value("output_health", health);
			
			medical = get_medical();
			set_value("output_medical", medical);
			
			workers = get_workers();
			set_value("output_workers", workers);
			
			conference = get_value_float("input_conference");
			mpd = get_value_float("input_mpd");
			expenses = get_value_float("input_expenses");
			
			subtotal = salary + cpp + salary_s + cpp_s + health + medical + /*hcsa +*/ workers + conference + mpd + expenses;
			set_value("output_subtotal", subtotal);
			total = subtotal / (1 - cr_charge);
			set_value("output_total", total);
			charge = total - subtotal;
			set_value("output_charge", charge);
			
			support = get_value_float("input_support");
			tobe_raised = total - support;
			set_value("output_tobe_raised", tobe_raised);
			bridge = tobe_raised * (1 - cr_charge);
			set_value("output_bridge", bridge);
			
			if (total == 0){
				percent = 0;
			}
			else {
				percent = support / total;
			}
			//todo look nice
			document.getElementById("output_percent").innerHTML = Math.round(percent * 100) + "%";
			
			
			//summary table
			set_value("summ_salary", salary);
			set_value("summ_salary_s", salary_s);
			set_value("summ_tax", cpp + cpp_s + health + medical + /*hcsa + */workers);
			set_value("summ_expenses", conference + mpd + expenses + charge);
			set_value("summ_total", total);
			set_value("summ_tobe_raised", tobe_raised);
			
		}
		
		function saveData(){
			calculate(); //this stores what the user inputs into the variables
			var data = hours + "+" + hours_s + "+" + coverage + "+" + decline + "+" + salary + "+" + salary_s + "+" + hcsa + "+" + conference + "+" + mpd  + "+" + expenses + "+" +  support;
			document.getElementById("data").value = data;
			sendData.submit();
		}
		
		function getDownloadData(){
			//todo look nice
			var data = document.getElementById("input_name").value + 
				"+" + Math.round(hours * 10) / 10 + 
				"+" + document.getElementById("input_province").value +
				"+" + Math.round(hours_s * 10) /10 + 
				"+" + document.getElementById("input_account").value + 
				"+" + coverage +
				"+" + document.getElementById("input_ministry").value + 
				"+" + decline + "+" + salary.toFixed(2) + 
				"+" + cpp.toFixed(2) + 
				"+" + salary_s.toFixed(2) + 
				"+" + cpp_s.toFixed(2) +
				"+" + health.toFixed(2) +
				"+" + medical.toFixed(2) +
				"+" + workers.toFixed(2) + 
				"+" + hcsa.toFixed(2) + 
				"+" + conference.toFixed(2) +
				"+" + mpd.toFixed(2)  +
				"+" + expenses.toFixed(2) +
				"+" +  subtotal.toFixed(2) +
				"+" + charge.toFixed(2) +
				"+" + total.toFixed(2) + 
				"+" + support.toFixed(2) + 
				"+" + tobe_raised.toFixed(2) + 
				"+" + bridge.toFixed(2) + 
				"+" + percent.toFixed(2);
			return data;
		}
		
		function downloadData(){
			document.getElementById("download").value=getDownloadData();
			document.getElementById("downloadDataForm").submit();
		}
		
		function clearFields(){
			//province and coverage are not cleared because they do not have a "null" value
			document.getElementById("input_name").value = "";
			document.getElementById("input_account").value = "";
			document.getElementById("input_ministry").value = "";
			document.getElementById("input_hours").value = 0;
			document.getElementById("input_hours_s").value = 0;
			document.getElementById("input_decline").checked = false;
			document.getElementById("input_salary").value = 0;
			document.getElementById("input_salary_s").value = 0;
			document.getElementById("input_hcsa").value = 0;
			document.getElementById("input_conference").value = 0;
			document.getElementById("input_mpd").value = 0;
			document.getElementById("input_expenses").value = 0;
			document.getElementById("input_support").value = 0;
			calculate();
		}
		
		var default_hcsa = '<select name="simple_hcsa" id="simple_hcsa" title="Please enter the monthly amount you would like to contribute to a HCSA." value="2"><option value="0" <?php if (getData("hcsa") == 0) echo "selected"; ?>>0</option><option value="25" <?php if (getData("hcsa") == 25) echo "selected"; ?>>25</option><option value="50" <?php if (getData("hcsa") == 50) echo "selected"; ?>>50</option><option value="100" <?php if (getData("hcsa") == 100) echo "selected"; ?>>100</option><option value="150" <?php if (getData("hcsa") == 150) echo "selected"; ?>>150</option><option value="200" <?php if (getData("hcsa") == 200) echo "selected"; ?>>200</option><option value="300" <?php if (getData("hcsa") == 300) echo "selected"; ?>>300</option><option value="350" <?php if (getData("hcsa") == 350) echo "selected"; ?>>350</option><option value="400" <?php if (getData("hcsa") == 400) echo "selected"; ?>>400</option><option value="500" <?php if (getData("hcsa") == 500) echo "selected"; ?>>500</option></select>';
		var default_coverage = '<select name="simple_coverage" id="simple_coverage" title="Please select the type of benefits you require." value="2"><option value="0" <?php if (getData("coverage") == 0) echo "selected"; ?>>Single</option><option value="1" <?php if (getData("coverage") == 1) echo "selected"; ?>>Couple</option><option value="2" <?php if (getData("coverage") == 2) echo "selected"; ?>>Family</option></select>';
		
		function eligible(){
			if (get_value_float("simple_hours") < part_time && get_value_float("simple_hours_s") < part_time){
				document.getElementById("table_coverage").innerHTML = 'ineligible';
				document.getElementById("table_hcsa").innerHTML = 'ineligible';
			}
			else {
				document.getElementById("table_coverage").innerHTML = default_coverage;
				document.getElementById("table_hcsa").innerHTML = default_hcsa;
			}
		}
		
		function goForm(){
			if (get_value_float("simple_hours") < part_time && get_value_float("simple_hours_s") < part_time){
				document.getElementById("input_coverage").value = 0;
				document.getElementById("input_hcsa").value = 0;
			}
			else {
				document.getElementById("input_coverage").value = parseInt(document.getElementById("simple_coverage").value);
				document.getElementById("input_hcsa").value = get_value_float("simple_hcsa");
			}
			document.getElementById("input_hours").value = get_value_float("simple_hours");
			document.getElementById("input_hours_s").value = get_value_float("simple_hours_s");
			document.getElementById("input_decline").checked = document.getElementById("simple_decline").checked;
			document.getElementById("input_salary").value = get_value_float("simple_salary");
			document.getElementById("input_salary_s").value = get_value_float("simple_salary_s");
			document.getElementById("input_conference").value = get_value_float("simple_conference");
			document.getElementById("input_expenses").value = get_value_float("simple_expenses");
			document.getElementById("input_support").value = get_value_float("simple_support");
			calculate();
			skip();
		}
		
		function skip(){
			document.getElementById("simple_form").style.display = "none";
			document.getElementById("table_form").style.display = "block";
		}
		
		function back(){
			document.getElementById("table_form").style.display = "none";
			document.getElementById("simple_form").style.display = "block";
		}
		
		function window_load(){
			calculate();
			eligible();
		}
		
		var show_detail = false;
		
		function toggle_detail(){
			var block = document.getElementById("table_full");
			var button = document.getElementById("detail_view");
			show_detail = !show_detail;
			if (show_detail){
				block.style.display = "block";
				button.value="Hide Details";
			}
			else{
				block.style.display = "none";
				button.value="Show Details";
			}
		}
		
		window.onload = window_load;
    </script>
	<form name="downloadDataForm" id="downloadDataForm" action="" method="get">
		<input type="hidden" name="download" id="download" value="">
	</form>
	
	<form name="sendData" id="sendData" action="" method="get">
		<input type="hidden" name="data" id="data" value="">
	</form>
	<div name="simple_form" id="simple_form">
		<table>
			<tr>
				<td>
					Please enter the number of hours that you work per week:
				</td>
				<td>
					<input type="number" name="simple_hours" id="simple_hours" onchange='eligible();' title="Enter the number of hours per week that you will be working." min="0" max="40" step="0.5" value="<?php echo getData("hours"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					Please enter the number of hours that your spouse works per week (if applicable):
				</td>
				<td>
					<input type="number" name="simple_hours_s" id="simple_hours_s" onchange='eligible();' title="Enter the number of hours per week that you will be working." min="0" max="40" step="0.5" value="<?php echo getData("hours_s"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					Please enter the benefit coverage that you need (single, couple or family):
				</td>
				<td name='table_coverage' id='table_coverage'>
					<select name="simple_coverage" id="simple_coverage" title="Please select the type of benefits you require." value="2">
						<option value="0" <?php if (getData("coverage") == 0) echo "selected"; ?>>Single</option>
						<option value="1" <?php if (getData("coverage") == 1) echo "selected"; ?>>Couple</option>
						<option value="2" <?php if (getData("coverage") == 2) echo "selected"; ?>>Family</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					If you are covered under another benefit plan, you may choose to refuse portions of the Power to Change plan. Do you want to decline benefits?
				</td>
				<td>
					<input type="checkbox" name="simple_benefits" id="simple_decline" <?php if (getData("decline")) echo "checked"; ?>>
				</td>
			</tr>
			
			<tr>
				<td>
					Please enter your monthly allowance/salary:
				</td>
				<td>
					<input type="text" name="simple_salary" id="simple_salary" title="Enter your salary." value="<?php echo getData("salary"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					Please enter your spouse's monthly allowance/salary (if applicable):
				</td>
				<td>
					<input type="text" name="simple_salary_s" id="simple_salary_s" title="Enter the salary for your spouse (if applicable)." value="<?php echo getData("salary_s"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					Please enter the monthly amount you would like to contribute to Medical Allowance
				</td>
				<td name='table_hcsa' id='table_hcsa'>
					<select name="simple_hcsa" id="simple_hcsa" title="Please enter the monthly amount you would like to contribute to Medical Allowance." value="2">
						<option value="0" <?php if (getData("hcsa") == 0) echo "selected"; ?>>0</option>
						<option value="25" <?php if (getData("hcsa") == 25) echo "selected"; ?>>25</option>
						<option value="50" <?php if (getData("hcsa") == 50) echo "selected"; ?>>50</option>
						<option value="100" <?php if (getData("hcsa") == 100) echo "selected"; ?>>100</option>
						<option value="150" <?php if (getData("hcsa") == 150) echo "selected"; ?>>150</option>
						<option value="200" <?php if (getData("hcsa") == 200) echo "selected"; ?>>200</option>
						<option value="300" <?php if (getData("hcsa") == 300) echo "selected"; ?>>300</option>
						<option value="350" <?php if (getData("hcsa") == 350) echo "selected"; ?>>350</option>
						<option value="400" <?php if (getData("hcsa") == 400) echo "selected"; ?>>400</option>
						<option value="500" <?php if (getData("hcsa") == 500) echo "selected"; ?>>500</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					For one person enter $90 or for two people enter $180 for Staff Conference
				</td>
				<td>
					<input type="text" name="simple_conference" id="simple_conference" title="For one person enter $90 or for two people enter $180" value="<?php echo getData("conference"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					 Please enter the amount for ministry partner develop. Most staff should enter $100, if your support base will be further from your ministry assignment, you may wish to take a higher amount.  Consider expenses like newsletters, gifts to ministry partners, support trips, etc.
				</td>
				<td>
					<input type="text" name="simple_mpd" id="simple_mpd" value="<?php echo getData("mpd"); ?>">
				</td>
				
			</tr>
			<tr>
				<td>
					Please enter the amount for reimbursable ministry expenses. These are expenses that you will be reimbursing from your staff account that are related to your ministry.  All staff should enter a minimum of $100.<BR><BR>When selecting an amount, consider expenses like cell phone, conferences, training, ministry trips, etc
				</td>
				<td>
					<input type="text" name="simple_expenses" id="simple_expenses" value="<?php echo getData("expenses"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					Please enter the amount of solid monthly support that you have.  To calculate your monthly support, add all recurring donations from Canadian, US and International sources.  Do NOT count one-time gifts or pledges.
				</td>
				<td>
					<input type="text" name="simple_support" id="simple_support"  value="<?php echo getData("support"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					<input type="button" value="Skip" onclick="skip();">
				</td>
				<td>
					<input type="button" value="Calculate Support" onclick="goForm();">
				</td>
			</tr>
		</table>
	</div>
	<div name="table_form" id="table_form" style="display:none">
		<table>
			<tr>
				<td>
					<p>
						Name:
					</p>
				</td>
				<td>
					<input type="text" name="input_name" id="input_name" value="<?php echo getName(null, true); ?>">
				</td>
				<td>
					<p>
						Hours per week:
					</p>
				</td>
				<td>
					<input type="number" name="input_hours" id="input_hours" onchange="calculate();" title="Enter the number of hours per week that you will be working." min="0" max="40" step="0.5" value="<?php echo getData("hours"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Province:
					</p>
				</td>
				<td>
					<select name="input_province" id="input_province" title="Please enter your tax province.  In most cases, this is the province where you live.">
					<option value="0" <?php if (setProvince() == 0) echo "selected"; ?>>Alberta</option>
					<option value="1" <?php if (setProvince() == 1) echo "selected"; ?>>British Columbia</option>
					<option value="2" <?php if (setProvince() == 2) echo "selected"; ?>>Manitoba</option>
					<option value="3" <?php if (setProvince() == 3) echo "selected"; ?>>New Brunswick</option>
					<option value="4" <?php if (setProvince() == 4) echo "selected"; ?>>Newfoundland & Labrador</option>
					<option value="5" <?php if (setProvince() == 5) echo "selected"; ?>>North West Territories</option>
					<option value="6" <?php if (setProvince() == 6) echo "selected"; ?>>Nova Scotia</option>
					<option value="7" <?php if (setProvince() == 7) echo "selected"; ?>>Nunavut</option>
					<option value="8" <?php if (setProvince() == 8) echo "selected"; ?>>Ontario</option>
					<option value="9" <?php if (setProvince() == 9) echo "selected"; ?>>Prince Edward Island</option>
					<option value="10" <?php if (setProvince() == 10) echo "selected"; ?>>Quebec</option>
					<option value="11" <?php if (setProvince() == 11) echo "selected"; ?>>Saskatchewan</option>
					<option value="12" <?php if (setProvince() == 12) echo "selected"; ?>>Yukon</option>
					<option value="13" <?php if (setProvince() == 13) echo "selected"; ?>>Foreign</option>
				</select>
				</td>
				<td>
					<p>
						Hours per week (spouse):
					</p>
				</td>
				<td>
					<input type="number" name="input_hours_s" id="input_hours_s" onchange="calculate();" title="Enter the number of hours per week that you will be working." min="0" max="40" step="0.5" value="<?php echo getData("hours_s"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Staff Account:
					</p>
				</td>
				<td>
					<input type="text" name="input_account" id="input_account" maxlength='6' value="<?php echo getFieldEmployee("staff_account"); ?>"> 
				</td>
				<td>
					<p>
						Benefit Coverage:
					</p>
				</td>
				<td>
					<select name="input_coverage" id="input_coverage" onchange="calculate();" title="Please select the type of benefits you require." value="2">
						<option value="0" <?php if (getData("coverage") == 0) echo "selected"; ?>>Single</option>
						<option value="1" <?php if (getData("coverage") == 1) echo "selected"; ?>>Couple</option>
						<option value="2" <?php if (getData("coverage") == 2) echo "selected"; ?>>Family</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Ministry:
					</p>
				</td>
				<td>
					<input type="text" name="input_ministry" id="input_ministry" value="<?php echo getFieldEmployee("ministry"); ?>">
				</td>
				<td>
					<p>
						Decline Benefits:
					</p>
				</td>
				<td>
					<input type="checkbox" name="input_benefits" id="input_decline" onchange="calculate();" title="If you are covered under another benefit plan, you may choose to refuse portions of the Power to Change plan." <?php if (getData("decline")) echo "checked"; ?>>
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td>Monthly Allowance/Salary</td>
				<td name='summ_salary' id='summ_salary'></td>
			</tr>
			<tr>
				<td>Monthly Allowance/Salary - Spouse</td>
				<td name='summ_salary_s' id='summ_salary_s'></td>
			</tr>
			<tr>
				<td>Tax/Benefits</td>
				<td name='summ_tax' id='summ_tax'></td>
			</tr>
			<tr>
				<td>Other expenses</td>
				<td name='summ_expenses' id='summ_expenses'></td>
			</tr>
			<tr>
				<td>Monthly Support Goal</td>
				<td name='summ_total' id='summ_total'></td>
			</tr>
			<tr>
				<td>Total Funds Yet to be Raised</td>
				<td name='summ_tobe_raised' id='summ_tobe_raised'></td>
			</tr>
		</table>
		<input type="button" name="detail_view" id="detail_view" value="Show Details" onclick="toggle_detail();" /><BR>
		<table name='table_full' id='table_full' style="display:none">
			<tr>
				<td>
					<p>
						Monthly Allowance/Salary
					</p>
				</td>
				<td>
					<input type="text" name="input_salary" id="input_salary" onchange="calculate();" title="Enter your salary." value="<?php echo getData("salary"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Employer Paid CPP/EI
					</p>
				</td>
				<td>
					<p name="output_cpp" id="output_cpp"></p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Monthly Allowance/Salary - Spouse
					</p>
				</td>
				<td>
					<input type="text" name="input_salary_s" id="input_salary_s" onchange="calculate();" title="Enter the salary for your spouse (if applicable)." value="<?php echo getData("salary_s"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Employer Paid CPP/EI - Spouse
					</p>
				</td>
				<td>
					<p name="output_cpp_s" id="output_cpp_s"></p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Extended Health
					</p>
				</td>
				<td>
					<p name="output_health" id="output_health"></p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Provincial Medical
					</p>
				</td>
				<td>
					<p name="output_medical" id="output_medical"></p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Medical Allowance
					</p>
				</td>
				<td>
					<select name="input_hcsa" id="input_hcsa" onchange="calculate();" title="Please enter the monthly amount you would like to contribute to Medical Allowance." value="2">
						<option value="0" <?php if (getData("hcsa") == 0) echo "selected"; ?>>0</option>
						<option value="25" <?php if (getData("hcsa") == 25) echo "selected"; ?>>25</option>
						<option value="50" <?php if (getData("hcsa") == 50) echo "selected"; ?>>50</option>
						<option value="100" <?php if (getData("hcsa") == 100) echo "selected"; ?>>100</option>
						<option value="150" <?php if (getData("hcsa") == 150) echo "selected"; ?>>150</option>
						<option value="200" <?php if (getData("hcsa") == 200) echo "selected"; ?>>200</option>
						<option value="300" <?php if (getData("hcsa") == 300) echo "selected"; ?>>300</option>
						<option value="350" <?php if (getData("hcsa") == 350) echo "selected"; ?>>350</option>
						<option value="400" <?php if (getData("hcsa") == 400) echo "selected"; ?>>400</option>
						<option value="500" <?php if (getData("hcsa") == 500) echo "selected"; ?>>500</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Worker&rsquo;s Compensation
					</p>
				</td>
				<td>
					<p name="output_workers" id="output_workers"></p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Staff Conference
					</p>
				</td>
				<td>
					<input type="text" name="input_conference" id="input_conference" onchange="calculate();" title="For one person enter $90 or for two people enter $180" value="<?php echo getData("conference"); ?>">
				</td>
			</tr>
				<td>
					<p>
						MPD correspondence
					</p>
				</td>
				<td>
					<input type="text" name="input_mpd" id="input_mpd" onchange="calculate();" title="Most staff should enter $100, if your support base will be further from your ministry assignment, you may wish to take a higher amount.  Consider expenses like newsletters, gifts to ministry partners, support trips, etc." value="<?php echo getData("mpd"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Reimbursable Ministry Expenses
					</p>
				</td>
				<td>
					<input type="text" name="input_expenses" id="input_expenses" onchange="calculate();" title="These are expenses that you will be reimbursing from your staff account that are related to your ministry.  All staff should enter a minimum of $100.
		
When selecting an amount, consider expenses like cell phone, conferences, training, ministry trips, etc" value="<?php echo getData("expenses"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Subtotal
					</p>
				</td>
				<td>
					<p name="output_subtotal" id="output_subtotal"></p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Central Resource Charge
					</p>
				</td>
				<td>
					<p name="output_charge" id="output_charge"></p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Monthly Support Goal
					</p>
				</td>
				<td>
					<p name="output_total" id="output_total"></p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Solid Monthly Support
					</p>
				</td>
					<td>
					<input type="text" name="input_support" id="input_support"  onchange="calculate();" title="To calculate your monthly support, add all recurring donations from Canadian, US and International sources.  Do NOT count one-time gifts or pledges." value="<?php echo getData("support"); ?>">
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Total Funds Yet to be Raised
					</p>
				</td>
				<td>
					<p name="output_tobe_raised" id="output_tobe_raised"></p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Bridge Amount
					</p>
				</td>
				<td>
					<p name="output_bridge" id="output_bridge"></p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						Percent Supported
					</p>
				</td>
				<td>
					<p name="output_percent_raised" id="output_percent"></p>
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td><input type="button" value="Calculate" onclick="calculate();"></td>
				<td><input type="button" value="Save" onclick="saveData();"></td>
				<td><input type="button" value="Download" onclick="downloadData();"></td>
				<td><input type="button" value="Clear Fields" onclick="clearFields();"></td>
				<td><input type="button" value="Back" onclick="back();"></td>
			</tr>
		</table>
	</div>
	</div>
    </div>
    <div id="content-right"><?php get_sidebar(''); ?></div>
</div>
<!--content end-->
<!--Popup window-->
<?php include(TEMPLATEPATH.'/popup.php') ?>
</div>
<!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>		
<?php get_footer(); ?>