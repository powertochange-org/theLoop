<style type='text/css'>
#admin_view td, .border{
	border: 1px solid #808080;
	padding: 5px;
	margin: 3px;
}

.label{
	width:100%;
}

</style>
<?php

include('functions-admin.php');


		// var data = cpp_rate + "+" + cpp_rate_QC + "+" + cpp_max + "+" + cpp_max_QC + "+" + cpp_exempt + "+" + cpp_exempt_QC + "+" + ei_rate1 + "+" + ei_rate1_QC + "+" + ei_rate2 + "+" + ei_rate2_QC + "+" + ei_max + "+" + ehc.join("+") + "+" + ehc_int.join("+") + "+" + dental.join("+") + "+" + dental_int.join("+") + "+" + ehc_MB + "+" + health_tax.join("+") + "+" + part_time + "+" + add_rate + "+" + life_rate + "+" + life_max + "+" + dept_life.join("+") + "+" + add_rate_int + "+" + life_rate_int + "+" + life_max_int + "+" + dept_life_int.join("+") + "+" + medical_ON + "+" + medical_QC + "+" + medical_BC.join("+") + "+" + workers_rate.join("+") + "+" + cr_charge;



$constants = array("cpp_rate", "cpp_rate_QC", "cpp_max", "cpp_max_QC", "cpp_exempt", "cpp_exempt_QC", "ei_rate1", "ei_rate1_QC", "ei_rate2", "ei_rate2_QC", "ei_max", "qpip_annual_ei_max", "qpip_rate_QC_ER", "ehc_single", "ehc_couple", "ehc_family", "ehc_single_int", "ehc_couple_int", "ehc_family_int", "dental_single", "dental_couple", "dental_family", "dental_single_int", "dental_couple_int", "dental_family_int", "ehc_MB", "health_tax_AB", "health_tax_BC", "health_tax_MB", "health_tax_NB", "health_tax_NL", "health_tax_NT", "health_tax_NS", "health_tax_NU", "health_tax_ON", "health_tax_PE", "health_tax_QC", "health_tax_SK", "health_tax_YT", "health_tax_FR", "part_time", "add_rate", "life_rate", "life_max", "dept_life_single", "dept_life_couple", "dept_life_family", "add_rate_int", "life_rate_int", "life_max_int", "dept_life_single_int", "dept_life_couple_int", "dept_life_family_int", "medical_ON", "medical_QC", "medical_BC_single", "medical_BC_couple", "medical_BC_family", "workers_rate_AB", "workers_rate_BC", "workers_rate_MB", "workers_rate_NB", "workers_rate_NL", "workers_rate_NT", "workers_rate_NS", "workers_rate_NU", "workers_rate_ON", "workers_rate_PE", "workers_rate_QC", "workers_rate_SK", "workers_rate_YT", "workers_rate_FR", "cr_charge");

parseConstantInput();

function parseConstantInput(){
	global $constants;
	global $wpdb;
	if (isAppAdmin('support_calculator_admin', 0)) {

		//todo error handling
		$data = explode('+',  mysqli_real_escape_string($wpdb->dbh, htmlspecialchars($_GET["constants"])));
		if (count($data) < count($constants)){ //make the number the length of the array
			return; //no data
		}
		for ($i = 0; $i < count($constants); $i ++){
			setConstant($constants[$i], $data[$i]);
		}
		echo '<BR>Constants Saved!<BR>';
	}
}

printAdmin();
function printAdmin(){
	if (isAppAdmin('support_calculator_admin', 0)) {
		printAdminChangeInterface();
		echo '
	<table>
		<tr>
			<td>
				Canada Pension Plan rate
			</td>
			<td>
				<input type="text" name="set_cpp_rate" id="set_cpp_rate" value="'.getConstant("cpp_rate").'">
			</td>
		</tr>

		<tr>
			<td>
				Quebec Pension Plan rate
			</td>
			<td>
				<input type="text" name="set_cpp_rate_QC" id="set_cpp_rate_QC" value="'.getConstant("cpp_rate_QC").'">
			</td>
		</tr>

		<tr>
			<td>
				Canada Pension Plan maximum pensionable earnings (annual)
			</td>
			<td>
				<input type="text" name="set_cpp_max" id="set_cpp_max" value="'.getConstant("cpp_max").'">
			</td>
		</tr>

		<tr>
			<td>
				Quebec Pension Plan maximum pensionable earnings (annual)
			</td>
			<td>
				<input type="text" name="set_cpp_max_QC" id="set_cpp_max_QC" value="'.getConstant("cpp_max_QC").'">
			</td>
		</tr>

		<tr>
			<td>
				Canada Pension Plan exemption (annual)
			</td>
			<td>
				<input type="text" name="set_cpp_exempt" id="set_cpp_exempt" value="'.getConstant("cpp_exempt").'">
			</td>
		</tr>

		<tr>
			<td>
				Quebec Pension Plan exemption (annual)
			</td>
			<td>
				<input type="text" name="set_cpp_exempt_QC" id="set_cpp_exempt_QC" value="'.getConstant("cpp_exempt_QC").'">
			</td>
		</tr>

		<tr>
			<td>
				Employment Insurance rate (EE rate)
			</td>
			<td>
				<input type="text" name="set_ei_rate1" id="set_ei_rate1" value="'.getConstant("ei_rate1").'">
			</td>
		</tr>

		<tr>
			<td>
				Employment Insurance rate (EE rate) - Quebec
			</td>
			<td>
				<input type="text" name="set_ei_rate1_QC" id="set_ei_rate1_QC" value="'.getConstant("ei_rate1_QC").'">
			</td>
		</tr>

		<tr>
			<td>
				Employment Insurance rate (ER rate)
			</td>
			<td>
				<input type="text" name="set_ei_rate2" id="set_ei_rate2" value="'.getConstant("ei_rate2").'">
			</td>
		</tr>

		<tr>
			<td>
				Employment Insurance rate (ER rate) - Quebec
			</td>
			<td>
				<input type="text" name="set_ei_rate2_QC" id="set_ei_rate2_QC" value="'.getConstant("ei_rate2_QC").'">
			</td>
		</tr>

		<tr>
			<td>
				Employment Insurance maximum insurable earnings (annual)
			</td>
			<td>
				<input type="text" name="set_ei_max" id="set_ei_max" value="'.getConstant("ei_max").'">
			</td>
		</tr>

		<tr>
			<td>
				QPIP Maxiumum (annual)
			</td>
			<td>
				<input type="text" name="set_qpip_annual_ei_max" id="set_qpip_annual_ei_max" value="'.getConstant("qpip_annual_ei_max").'">
			</td>
		</tr>

		<tr>
			<td>
				QPIP ER
			</td>
			<td>
				<input type="text" name="set_qpip_rate_QC_ER" id="set_qpip_rate_QC_ER" value="'.getConstant("qpip_rate_QC_ER").'">
			</td>
		</tr>

		<tr>
			<td colspan=2 style="text-align:center;">
				<b>Extended Health Rates</b>
			</td>
		</tr>
		<tr>
			<td>
				Extended Health Coverage (single, couple, family)
			</td>
			<td>
				<input type="text" name="set_ehc_single" id="set_ehc_single" value="'.getConstant("ehc_single").'" title="single">
				<input type="text" name="set_ehc_couple" id="set_ehc_couple" value="'.getConstant("ehc_couple").'" title="couple">
				<input type="text" name="set_ehc_family" id="set_ehc_family" value="'.getConstant("ehc_family").'" title="family">
			</td>
		</tr>
		<tr>
			<td>
				Dental Coverage (single, couple, family)
			</td>
			<td>
				<input type="text" name="set_dental_single" id="set_dental_single" value="'.getConstant("dental_single").'" title="single">
				<input type="text" name="set_dental_couple" id="set_dental_couple" value="'.getConstant("dental_couple").'" title="couple">
				<input type="text" name="set_dental_family" id="set_dental_family" value="'.getConstant("dental_family").'" title="family">
			</td>
		</tr>
		<tr>
			<td colspan=2 style="text-align:center;">
				<b>International Extended Health Rates</b>
			</td>
		</tr>
		<tr>
			<td>
				<b>International</b> Extended Health Coverage (single, couple, family)
			</td>
			<td>
				<input type="text" name="set_ehc_single_int" id="set_ehc_single_int" value="'.getConstant("ehc_single_int").'" title="single_int">
				<input type="text" name="set_ehc_couple_int" id="set_ehc_couple_int" value="'.getConstant("ehc_couple_int").'" title="couple_int">
				<input type="text" name="set_ehc_family_int" id="set_ehc_family_int" value="'.getConstant("ehc_family_int").'" title="family_int">
			</td>
		</tr>
		<tr>
			<td>
				<b>International</b> Dental Coverage (single, couple, family)
			</td>
			<td>
				<input type="text" name="set_dental_single_int" id="set_dental_single_int" value="'.getConstant("dental_single_int").'" title="single">
				<input type="text" name="set_dental_couple_int" id="set_dental_couple_int" value="'.getConstant("dental_couple_int").'" title="couple">
				<input type="text" name="set_dental_family_int" id="set_dental_family_int" value="'.getConstant("dental_family_int").'" title="family">
			</td>
		</tr>
		<tr>
			<td>
				Manitoba tax on EHC
			</td>
			<td>
				<input type="text" name="set_ehc_MB" id="set_ehc_MB" value="'.getConstant("ehc_MB").'">
			</td>
		</tr>
		<tr>
			<td>
				Total Benefit Costs Tax
			</td>
			<td>
				'.printHealthTax().'
				<label for="set_health_tax_FR">Foreign<input type="text" name="set_health_tax_FR" id="set_health_tax_FR" title="Foreign" value="'.getConstant("health_tax_FR").'"></label>
			</td>
		</tr>
		<tr>
			<td>
				Part time
			</td>
			<td>
				<input type="text" name="set_part_time" id="set_part_time" value="'.getConstant("part_time").'">
			</td>
		</tr>
		<tr>
			<td colspan=2 style="text-align:center;">
				<b>National Rates</b>
			</td>
		</tr>
		<tr>
			<td>
				AD&D rate per $1000
			</td>
			<td>
				<input type="text" name="set_add_rate" id="set_add_rate" value="'.getConstant("add_rate").'">
			</td>
		</tr>
		<tr>
			<td>
				Life rate per $1000
			</td>
			<td>
				<input type="text" name="set_life_rate" id="set_life_rate" value="'.getConstant("life_rate").'">
			</td>
		</tr>
		<tr>
			<td>
				Life maximum 2 x annual salary
			</td>
			<td>
				<input type="text" name="set_life_max" id="set_life_max" value="'.getConstant("life_max").'">
			</td>
		</tr>
		<tr>
			<td>
				Depedents life (single, couple, family)
			</td>
			<td>
				<input type="text" name="set_dept_life_single" id="set_dept_life_single" value="'.getConstant("dept_life_single").'" title="single">
				<input type="text" name="set_dept_life_couple" id="set_dept_life_couple" value="'.getConstant("dept_life_couple").'" title="couple">
				<input type="text" name="set_dept_life_family" id="set_dept_life_family" value="'.getConstant("dept_life_family").'" title="family">
			</td>
		</tr>
		<tr>
			<td colspan=2 style="text-align:center;">
				<b>International Rates</b>
			</td>
		</tr>
		<tr>
			<td>
				Intl AD&D rate per $1000
			</td>
			<td>
				<input type="text" name="set_add_rate_int" id="set_add_rate_int" value="'.getConstant("add_rate_int").'">
			</td>
		</tr>
		<tr>
			<td>
				Intl Life rate per $1000
			</td>
			<td>
				<input type="text" name="set_life_rate_int" id="set_life_rate_int" value="'.getConstant("life_rate_int").'">
			</td>
		</tr>
		<tr>
			<td>
				Intl Life maximum 2 x annual salary
			</td>
			<td>
				<input type="text" name="set_life_max_int" id="set_life_max_int" value="'.getConstant("life_max_int").'">
			</td>
		</tr>
		<tr>
			<td>
				Intl Depedents life (single, couple, family)
			</td>
			<td>
				<input type="text" name="set_dept_life_single_int" id="set_dept_life_single_int" value="'.getConstant("dept_life_single_int").'" title="single">
				<input type="text" name="set_dept_life_couple_int" id="set_dept_life_couple_int" value="'.getConstant("dept_life_couple_int").'" title="couple">
				<input type="text" name="set_dept_life_family_int" id="set_dept_life_family_int" value="'.getConstant("dept_life_family_int").'" title="family">
			</td>
		</tr>
		<tr>
			<td colspan=2 style="text-align:center;">
				<b>Provincal Rates</b>
			</td>
		</tr>
		<tr>
			<td>
				Ontario medical rate
			</td>
			<td>
				<input type="text" name="set_medical_ON" id="set_medical_ON" value="'.getConstant("medical_ON").'">
			</td>
		</tr>
		<tr>
			<td>
				Quebec medical rate
			</td>
			<td>
				<input type="text" name="set_medical_QC" id="set_medical_QC" value="'.getConstant("medical_QC").'">
			</td>
		</tr>
		<tr>
			<td>
				British Columbia medical is a flat rate (single, couple, family)
			</td>
			<td>
				<input type="text" name="set_medical_BC_single" id="set_medical_BC_single" value="'.getConstant("medical_BC_single").'" title="single">
				<input type="text" name="set_medical_BC_couple" id="set_medical_BC_couple" value="'.getConstant("medical_BC_couple").'" title="couple">
				<input type="text" name="set_medical_BC_family" id="set_medical_BC_family" value="'.getConstant("medical_BC_family").'" title="family">
			</td>
		</tr>

		<tr>
			<td>
				 Workers compensation (rate per $100)
			</td>
			<td>
				'.printWorkers().'
				<label for="set_health_tax_FR">Foreign<input type="text" name="set_workers_rate_FR" id="set_workers_rate_FR" title="Foreign" value="'.getConstant("workers_rate_FR").'"></label>
			</td>
		</tr>
		<tr>
			<td>
				Central Resource Charge
			</td>
			<td>
				<input type="text" name="set_cr_charge" id="set_cr_charge" value="'.getConstant("cr_charge").'">
			</td>
		</tr>
		<tr>
			<td>

			</td>
			<td>
				<input type="button" value="Save" onclick="saveConstants();">
			</td>
		</tr>
	</table>
</div>
<BR>
<form name="sendConstants" id="sendConstants" action="" method="get">
	<input type="hidden" name="constants" id="constants" value="">
</form>
<script type="text/javascript">

	function storeValues(){
		cpp_rate = get_value_float("set_cpp_rate");
		cpp_rate_QC = get_value_float("set_cpp_rate_QC");

		cpp_max = get_value_float("set_cpp_max");
		cpp_max_QC = get_value_float("set_cpp_max_QC");

		cpp_exempt = get_value_float("set_cpp_exempt");
		cpp_exempt_QC = get_value_float("set_cpp_exempt_QC");

		ei_rate1 = get_value_float("set_ei_rate1");
		ei_rate1_QC = get_value_float("set_ei_rate1_QC");

		ei_rate2 = get_value_float("set_ei_rate2");
		ei_rate2_QC = get_value_float("set_ei_rate2_QC");

		ei_max = get_value_float("set_ei_max");
		qpip_annual_ei_max = get_value_float("set_qpip_annual_ei_max");

		qpip_rate_QC_ER = get_value_float("set_qpip_rate_QC_ER");


		ehc = new Array(get_value_float("set_ehc_single"), get_value_float("set_ehc_couple"), get_value_float("set_ehc_family"));
		ehc_int = new Array(get_value_float("set_ehc_single_int"), get_value_float("set_ehc_couple_int"), get_value_float("set_ehc_family_int"));
		dental = new Array(get_value_float("set_dental_single"), get_value_float("set_dental_couple"), get_value_float("set_dental_family"));
		dental_int = new Array(get_value_float("set_dental_single_int"), get_value_float("set_dental_couple_int"), get_value_float("set_dental_family_int"));
		ehc_MB = get_value_float("set_ehc_MB");
		health_tax = new Array('.printHealthTaxSet().' get_value_float("set_health_tax_FR"));
		part_time = get_value_float("set_part_time");
		add_rate = get_value_float("set_add_rate");
		life_rate = get_value_float("set_life_rate");
		life_max = get_value_float("set_life_max");
		dept_life = new Array(get_value_float("set_dept_life_single"), get_value_float("set_dept_life_couple"), get_value_float("set_dept_life_family"));
		add_rate_int = get_value_float("set_add_rate_int");
		life_rate_int = get_value_float("set_life_rate_int");
		life_max_int = get_value_float("set_life_max_int");
		dept_life_int = new Array(get_value_float("set_dept_life_single_int"), get_value_float("set_dept_life_couple_int"), get_value_float("set_dept_life_family_int"));
		medical_ON = get_value_float("set_medical_ON");
		medical_QC = get_value_float("set_medical_QC");
		medical_BC = new Array(get_value_float("set_medical_BC_single"), get_value_float("set_medical_BC_couple"), get_value_float("set_medical_BC_family"));
		workers_rate = new Array('.printWorkersSet().' get_value_float("set_workers_rate_FR"));
		cr_charge = get_value_float("set_cr_charge");
	}

	function saveConstants(){
		storeValues();
		var data = cpp_rate + "+" + cpp_rate_QC + "+" + cpp_max + "+" + cpp_max_QC + "+" + cpp_exempt + "+" + cpp_exempt_QC + "+" + ei_rate1 + "+" + ei_rate1_QC + "+" + ei_rate2 + "+" + ei_rate2_QC + "+" + ei_max + "+" + qpip_annual_ei_max + "+" + qpip_rate_QC_ER + "+" + ehc.join("+") + "+" + ehc_int.join("+") + "+" + dental.join("+") + "+" + dental_int.join("+") + "+" + ehc_MB + "+" + health_tax.join("+") + "+" + part_time + "+" + add_rate + "+" + life_rate + "+" + life_max + "+" + dept_life.join("+") + "+" + add_rate_int + "+" + life_rate_int + "+" + life_max_int + "+" + dept_life_int.join("+") + "+" + medical_ON + "+" + medical_QC + "+" + medical_BC.join("+") + "+" + workers_rate.join("+") + "+" + cr_charge;
		document.getElementById("constants").value = data;
		sendConstants.submit();
	}
	</script>
	';
	}
}
?>
