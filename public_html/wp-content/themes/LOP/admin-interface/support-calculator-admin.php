<?php

include('functions-admin.php');

$constants = array("cpp_rate", "cpp_max", "cpp_exempt", "ei_rate1", "ei_rate2", "ei_max", "ehc_single", "ehc_couple", "ehc_family", "ehc_MB", "health_tax_AB", "health_tax_BC", "health_tax_MB", "health_tax_NB", "health_tax_NL", "health_tax_NT", "health_tax_NS", "health_tax_NU", "health_tax_ON", "health_tax_PE", "health_tax_QC", "health_tax_SK", "health_tax_YT", "health_tax_FR", "part_time", "add_rate", "life_rate", "life_max", "dept_life_single", "dept_life_couple", "dept_life_family", "medical_ON", "medical_QC", "medical_BC_single", "medical_BC_couple", "medical_BC_family", "workers_rate_AB", "workers_rate_BC", "workers_rate_MB", "workers_rate_NB", "workers_rate_NL", "workers_rate_NT", "workers_rate_NS", "workers_rate_NU", "workers_rate_ON", "workers_rate_PE", "workers_rate_QC", "workers_rate_SK", "workers_rate_YT", "workers_rate_FR", "cr_charge");

parseConstantInput();

function parseConstantInput(){
	global $constants;
	if (isAdmin()){

		//todo error handling
		$data = explode('+',  mysql_real_escape_string(htmlspecialchars($_GET["constants"])));
		if (count($data) < 51){
			return; //no data
		}
		for ($i = 0; $i < 51; $i ++){
			setConstant($constants[$i], $data[$i]);
		}
		echo '<BR>Constants Saved!<BR>';
	}
}

printAdmin();
function printAdmin(){
	if (isAdmin()){	
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
				Canada Pension Plan maximum pensionable earnings (annual)
			</td>
			<td>
				<input type="text" name="set_cpp_max" id="set_cpp_max" value="'.getConstant("cpp_max").'">
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
				Employment Insurance rate (EE rate)
			</td>
			<td>
				<input type="text" name="set_ei_rate1" id="set_ei_rate1" value="'.getConstant("ei_rate1").'">
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
				Employment Insurance maximum insurable earnings (annual)
			</td>
			<td>
				<input type="text" name="set_ei_max" id="set_ei_max" value="'.getConstant("ei_max").'">
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
<form name="sendConstants" id="sendConstants" action="" method="get">
	<input type="hidden" name="constants" id="constants" value="">
</form>
<script type="text/javascript">
	
	function storeValues(){
		cpp_rate = get_value_float("set_cpp_rate");
		cpp_max = get_value_float("set_cpp_max");
		cpp_exempt = get_value_float("set_cpp_exempt");
		ei_rate1 = get_value_float("set_ei_rate1");
		ei_rate2 = get_value_float("set_ei_rate2");
		ei_max = get_value_float("set_ei_max");
		ehc = new Array(get_value_float("set_ehc_single"), get_value_float("set_ehc_couple"), get_value_float("set_ehc_family"));
		ehc_MB = get_value_float("set_ehc_MB");
		health_tax = new Array('.printHealthTaxSet().' get_value_float("set_health_tax_FR"));
		part_time = get_value_float("set_part_time");
		add_rate = get_value_float("set_add_rate");
		life_rate = get_value_float("set_life_rate");
		life_max = get_value_float("set_life_max");
		dept_life = new Array(get_value_float("set_dept_life_single"), get_value_float("set_dept_life_couple"), get_value_float("set_dept_life_family")); 
		medical_ON = get_value_float("set_medical_ON");
		medical_QC = get_value_float("set_medical_QC");
		medical_BC = new Array(get_value_float("set_medical_BC_single"), get_value_float("set_medical_BC_couple"), get_value_float("set_medical_BC_family"));
		workers_rate = new Array('.printWorkersSet().' get_value_float("set_workers_rate_FR"));
		cr_charge = get_value_float("set_cr_charge");
	}
	
	function saveConstants(){
		storeValues();
		var data = cpp_rate + "+" + cpp_max + "+" + cpp_exempt + "+" + ei_rate1 + "+" + ei_rate2 + "+" + ei_max + "+" + ehc.join("+") + "+" + ehc_MB + "+" + health_tax.join("+") + "+" + part_time + "+" + add_rate + "+" + life_rate + "+" + life_max + "+" + dept_life.join("+") + "+" + medical_ON + "+" + medical_QC + "+" + medical_BC.join("+") + "+" + workers_rate.join("+") + "+" + cr_charge;
		document.getElementById("constants").value = data;
		sendConstants.submit();
	}
	
	function demoteUser(user){
		document.getElementById("input_remove_admin").value = user;
		remove_admin.submit();
	}
	</script>
	';
	}
}
?>

