<?php

/*
*Template Name: Support_Calculator
*
*todo description
*
*
*/


$provinces = array (
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
			"YT" => 12);

			
$coverage = array ("single", "couple", "family");
$data = explode('+',  htmlspecialchars($_GET["download"]));

//echos back the data for the user to download

//todo style not user stored data\
// todo remove var_dump($data);
if (count($data) == 27){
	echo "<html>";
	
	//styling
	echo "<style type='text/css'>

table {
}

table td {
	padding:2px 10px;
}


table.head {
	border-collapse: collapse;
}

table.head td {
	border: 1px solid #000000;
}

table.body td {
	border: 0px solid #000000;
}

table.body {
    margin-left: auto;
    margin-right: auto;
}


</style>";
	
	
	if ($data[26]=="download"){
		//downdloads instead of displaying
		header("Content-disposition: attachment; filename=support_calculator.html");
		header("Content-type: text/html:");
	}
	else if ($data[26]=="print"){
		echo "<script type='text/javascript'>
			/*todo enable */ window.setTimeout(function() {window.print()}, 1000);/**/
		</script>";
	}
	echo"<body><table class='head'>
			<caption>Support Goal Calculator</caption>
			<tr>
				<td><p>Name:</p></td>
				<td><p>".$data[0]."</p></td>
				<td><p>Hours per week:</p></td>
				<td>".$data[1]."</td>
			</tr>
			<tr>
				<td><p>Province:</p></td>
				<td><p>".getProvinceString($data[2])."</p></td>
				<td><p>Hours per week (spouse):</p></td>
				<td>".$data[3]."</td>
			</tr>
			<tr>
				<td><p>Staff Account:</p></td>
				<td><p>".$data[4]."</p></td>
				<td><p>Benefit Coverage:</p></td>
				<td>".$coverage[$data[5]]."</td>
			</tr>
			<tr>
				<td><p>Ministry:</p></td>
				<td><p>".$data[6]."</p></td>
				<td><p>Decline Benefits:</p></td>
				<td>".$data[7]."</td>
			</tr>
			<tr><td colspan='4' style='border: 0px solid #000000;'>
				<table class='body'>
					<tr>
						<td><p>Monthly Allowance/Salary</p></td>
						<td style='text-align:right;'>".$data[8]."</td>
					</tr>
					<tr>
						<td><p>Employer Paid CPP/EI</p></td>
						<td style='text-align:right;'>".$data[9]."</td>
					</tr>
					<tr>
						<td><p>Monthly Allowance/Salary - Spouse</p></td>
						<td style='text-align:right;'>".$data[10]."</td>
					</tr>
					<tr>
						<td><p>Employer Paid CPP/EI - Spouse</p></td>
						<td style='text-align:right;'>".$data[11]."</td>
					</tr>
					<tr>
						<td><p>Extended Health</p></td>
						<td style='text-align:right;'>".$data[12]."</td>
					</tr>
					<tr>
						<td><p>Provincial Medical</p></td>
						<td style='text-align:right;'>".$data[13]."</td>
					</tr>
					<tr>
						<td><p>Health Care Spending Account</p></td>
						<td style='text-align:right;'>".$data[14]."</td>
					</tr>
					<tr>
						<td><p>Worker&rsquo;s Compensation</p></td>
						<td style='text-align:right;'>".$data[15]."</td>
					</tr>
					<tr>
						<td><p>Staff Conference</p></td>
						<td style='text-align:right;'>".$data[16]."</td>
					</tr>
					<tr>
						<td><p>MPD correspondence</p></td>
						<td style='text-align:right;'>".$data[17]."</td>
					</tr>
					<tr>
						<td><p>Reimbursable Ministry Expenses</p></td>
						<td style='text-align:right;'>".$data[18]."</td>
					</tr>
					<tr>
						<td><p>Subtotal</p></td>
						<td style='text-align:right;'>".$data[19]."</td>
					</tr>
					<tr>
						<td><p>Central Resource Charge</p></td>
						<td style='text-align:right;'>".$data[20]."</td>
					</tr>
					<tr style='background-color:d9d9d9'>
						<td><p>Monthly Support Goal</p></td>
						<td style='text-align:right; text-decoration:underline;'>".$data[21]."</td>
					</tr>
					<tr>
						<td><p>Solid Monthly Support</p></td>
						<td style='text-align:right;'>".$data[22]."</td>
					</tr>
					<tr style='background-color:d9d9d9'>
						<td><p>Total Funds Yet to be Raised</p></td>
						<td style='text-align:right; text-decoration:underline;'>".$data[23]."</td>
					</tr>
					<tr>
						<td><p>Bridge Amount</p></td>
						<td style='text-align:right;'>".$data[24]."</td>
					</tr>
					<tr>
						<td><p>Percent Supported</p></td>
						<td style='text-align:right;'>".$data[25]."</td>
					</tr>
				</table>
			</td></tr>
		</table></body></html>";
	exit;
}
?>
<?php get_header(); ?>
<div id="content">
    <div id="content-left">
	<div id="main-content">
		<h1 class="replace" style="float:left"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
		<BR><BR>
		<script type="text/javascript">
		//todo:
		//error handeling

		<?php
			
		$constants = array("cpp_rate", "cpp_max", "cpp_exempt", "ei_rate1", "ei_rate2", "ei_max", "ehc_single", "ehc_couple", "ehc_family", "ehc_MB", "health_tax_AB", "health_tax_BC", "health_tax_MB", "health_tax_NB", "health_tax_NL", "health_tax_NT", "health_tax_NS", "health_tax_NU", "health_tax_ON", "health_tax_PE", "health_tax_QC", "health_tax_SK", "health_tax_YT", "health_tax_FR", "part_time", "add_rate", "life_rate", "life_max", "dept_life_single", "dept_life_couple", "dept_life_family", "medical_ON", "medical_QC", "medical_BC_single", "medical_BC_couple", "medical_BC_family", "workers_rate_AB", "workers_rate_BC", "workers_rate_MB", "workers_rate_NB", "workers_rate_NL", "workers_rate_NT", "workers_rate_NS", "workers_rate_NU", "workers_rate_ON", "workers_rate_PE", "workers_rate_QC", "workers_rate_SK", "workers_rate_YT", "workers_rate_FR", "cr_charge");
		
		$current_user = wp_get_current_user();
		
		function getName(){
			global $wpdb;
			//todo what if different last names
			$name = getFieldEmployee("first_name");
			$spouse = getSpouse();
			if ($spouse != Null){
				$name .= " and ";
				$sql = 'SELECT `first_name` FROM `employee` JOIN `wp_users` ON  employee.user_login = wp_users.user_login WHERE wp_users.ID ='. $spouse;
				$result = $wpdb->get_results($sql);
				$name .= get_object_vars($result[0])['first_name'];
			}
			$name .= " ".getFieldEmployee("last_name");
			return $name;
		}
		
		function getSpouse(){
			//gets spouse's wb id
			global $current_user, $wpdb;
			$ID = $current_user->ID;
			$user_login = $current_user->user_login;
			$sql = 'SELECT `ID` FROM `wp_users` JOIN `employee` AS user ON wp_users.user_login = user.user_login JOIN `employee` AS spouse ON user.external_id = spouse.spouse_id WHERE `ID` != '. $ID.' AND spouse.user_login = "'.$user_login.'"';
			$result = $wpdb->get_results($sql);
			if ($result[0] == ""){
				return null;
			}
			return get_object_vars($result[0])['ID'];
		}
		
		function getFieldEmployee($field){
			global $current_user, $wpdb;
			$user_login = $current_user->user_login;
			$sql = "SELECT `".$field."` FROM `employee` WHERE `user_login` = '". $user_login . "'";
			$result = $wpdb->get_results($sql);
			return get_object_vars($result[0])[$field];
		}
		
		function setProvince(){
			global $provinces;
			//if foreign
			if (getFieldEmployee("country") == "CA"){
				return $provinces[getFieldEmployee("province")];
			}
			return 13;
		}
		
		function getProvinceString($prov){
			global $provinces;
			if ($prov == 13){
				return 'Foreign';
			} 
			return array_keys($provinces)[$prov];
		}
		
		$dataID = null;
		parseDataInput();
		
		function parseDataInput(){
			global $wpdb,$current_user, $dataID;
			$ID = $current_user->ID;
			//todo error handling
			$data = explode('+',  htmlspecialchars($_GET["data"]));	
			
			$dataID = getDataID($ID);
			if ($dataID == null){
				$spouse = getSpouse();
				if($spouse != null){
					$dataID = getDataID($spouse);
					if ($dataID == null){
						//user has spouse but neither has id
						$sql = "INSERT INTO `support_calculator`(`id`, `hours`, `hours_s`, `coverage`, `decline`, `salary`, `salary_s`, `hcsa`, `conference`, `mpd`, `expenses`, `support`) VALUES (0,0,0,0,0,0,0,0,90,100,100,0)"; //defaulting mpd and expenses to 100 and conference to 90
						$wpdb->get_results($sql);
						$dataID = $wpdb->insert_id;
						update_user_meta( $ID, 'support_calculator_id', $dataID);
						update_user_meta( $spouse, 'support_calculator_id', $dataID);
					}
					else{
						//spouse has id but not user
						update_user_meta( $ID, 'support_calculator_id', $dataID);
					}
				}
				else {
					//user has no spouse or id
					$sql = "INSERT INTO `support_calculator`(`id`, `hours`, `hours_s`, `coverage`, `decline`, `salary`, `salary_s`, `hcsa`, `conference`, `mpd`, `expenses`, `support`) VALUES (0,0,0,0,0,0,0,0,90,100,100,0)"; //defaulting mpd and expenses to 100 and conference to 90
					$wpdb->get_results($sql);
					$dataID = $wpdb->insert_id;
					update_user_meta( $ID, 'support_calculator_id', $dataID);
				}
			}
			if (count($data) < 11){
				return; //no data
			}
			//save data in table
			$sql = "UPDATE `support_calculator` SET `hours`=".$data[0].",`hours_s`=".$data[1].",`coverage`=".$data[2].",`decline`=".$data[3].",`salary`=".$data[4].",`salary_s`=".$data[5].",`hcsa`=".$data[6].",`conference`=".$data[7].",`mpd`=".$data[8].",`expenses`=".$data[9].",`support`=".$data[10]." WHERE `id`=".$dataID;
			$wpdb->get_results($sql);
			echo '</script><BR>Data Saved!<BR><script type="text/javascript">';
		}
		
		//gets the dataID of a user only not also their spouse
		function getDataID($ID){
			$result = get_user_meta($ID, 'support_calculator_id', true);
			if ($result == ""){
				return null;
			}
			return $result;
		}
		
		function getData($field){
			//parseDataInput() must run before this function
			global $wpdb, $dataID;
			$sql = "SELECT `".$field."` FROM `support_calculator` WHERE `id` = '". $dataID. "'";
			$result = $wpdb->get_results($sql);
			return get_object_vars($result[0])[$field];
		}
		
		function isAdmin(){
			global $current_user;
			$ID = $current_user->ID;
			$result = get_user_meta($ID, 'support_calculator_admin', true);			
			return $result == 1;
		}
		
		//admin interfaces (changing the constants / adding admins)
		
		parseConstantInput();
		
		function parseConstantInput(){
			global $wpdb, $constants;
			if (isAdmin()){

				//todo error handling
				$data = explode('+',  htmlspecialchars($_GET["constants"]));
				if (count($data) < 51){
					return; //no data
				}
				for ($i = 0; $i < 51; $i ++){
				
					if(getConstant($constants[$i]) == null){ //if no row already
						$sql = "INSERT INTO `support_calculator_constants`(`id`, `key`, `value`) VALUES (0,'".$constants[$i]."',".$data[$i].")";
						$wpdb->get_results($sql);
					}
					else{
						$sql = "UPDATE `support_calculator_constants` SET `value`=".$data[$i]." WHERE `key`='".$constants[$i]."'";
						$wpdb->get_results($sql);
					}
				}
				echo '</script><BR>Constants Saved!<BR><script type="text/javascript">';
			}
		}
		
		parseAdminInput();
		function parseAdminInput(){
			global $wpdb;
			if (isAdmin()){
				$admin = htmlspecialchars($_GET["input_add_admin"]);
				$user = get_user_by('login', $admin );
				if ($admin==""){
					return;
				}
				if (!$user){
					echo '</script><BR>No user: '. $admin.'<BR><script type="text/javascript">';
				}
				else{
					$ID = $user->ID;
					update_user_meta( $ID, 'support_calculator_admin', 1);
					echo '</script><BR>Made user: '. $admin. ' an administer<BR><script type="text/javascript">';
				}
				
			}
		}
		
		parseAdminRemove();
		function parseAdminRemove(){
			global $wpdb;
			if (isAdmin()){
				$admin = htmlspecialchars($_GET["input_remove_admin"]);
				$user = get_user_by('login', $admin );
				if ($admin==""){
					return;
				}
				if (!$user){
					echo '</script><BR>No user: '. $admin.'<BR><script type="text/javascript">';
				}
				else{
					$ID = $user->ID;
					update_user_meta( $ID, 'support_calculator_admin', 0);
					echo '</script><BR>Removed user: '. $admin. ' as administer<BR><script type="text/javascript">';
				}
				
			}
		}
		
		function getConstant($field){
			global $wpdb;
			$sql = "SELECT `value` FROM `support_calculator_constants` WHERE `key` = '". $field. "'";
			$result = $wpdb->get_results($sql);
			if ($result[0] == ""){
				return null;
			}
			return get_object_vars($result[0])['value'];
		}
		
		
		function getAdmins(){
			$string="";
			$admins = get_users(array('meta_key' => 'support_calculator_admin', 'meta_value' => '1'));
			foreach($admins as $user){
				$string .= "<li>".$user->user_login."<input type='button' value='Remove' onclick='demoteUser(\"".$user->user_login."\");'></li>";
			}
			return $string;
		}
		
		//these next six functions just help generate the table
		function printHealthTax(){
			global $provinces;
			foreach (array_keys($provinces) as $pro){
				$out .= '<label for="set_health_tax_'.$pro.'">'.$pro.'<input type="text" name="set_health_tax_'.$pro.'" id="set_health_tax_'.$pro.'" title='.$pro.' value="'.getConstant("health_tax_".$pro).'"></label><BR>';
			}
			return $out;
		}

		function printHealthTaxSet(){
			global $provinces;
			foreach (array_keys($provinces) as $pro){
				$out .= 'get_value_float("set_health_tax_'.$pro.'"),';
			}
			return $out;
		}
		
		function printHealthPreSet(){
			global $provinces;
			foreach (array_keys($provinces) as $pro){
				$out .= getConstant("health_tax_".$pro).", ";
			}
			return $out;
		}
		
		function printWorkers(){
			global $provinces;
			foreach (array_keys($provinces) as $pro){
				$out .= '<label for="set_workers_tax_'.$pro.'">'.$pro.'<input type="text" name="set_workers_rate_'.$pro.'" id="set_workers_rate_'.$pro.'" title='.$pro.' value="'.getConstant("workers_rate_".$pro).'"></label><BR>';
			}
			return $out;
		}
			
		function printWorkersSet(){
			global $provinces;
			foreach (array_keys($provinces) as $pro){
				$out .= 'get_value_float("set_workers_rate_'.$pro.'"),';
			}
			return $out;
		}
		
		function printWorkersPreSet(){
			global $provinces;
			foreach (array_keys($provinces) as $pro){
				$out .= getConstant("workers_rate_".$pro).", ";
			}
			return $out;
		}
		
		printAdmin();
		function printAdmin(){
			if (isAdmin()){	
				echo '
		var show_admin = false;
		
		function toggle_admin(){
			var block = document.getElementById("admin_view");
			var button = document.getElementById("admin_view_button");
			show_admin = !show_admin;
			if (show_admin){
				block.style.display = "block";
				button.value="Hide Administrative Option";
			}
			else{
				block.style.display = "none";
				button.value="Show Administrative Options";
			}
		}
		</script>
		<input type="button" name="admin_view_button" id="admin_view_button" value="Show Administrative Options" onclick="toggle_admin();" />
		<div name="admin_view" id="admin_view" style="display:none">
			<table><tr>
			<td>Make administer:</td>
			<td><form name="add_admin" id="add_admin" action="" method="get">
					<input type="text" name="input_add_admin" id="input_add_admin" />
			</form></td>
			<td><input type="button" value="Promote" onclick="add_admin.submit();"></td>
			</tr></table>
			<form name="remove_admin" id="remove_admin" action="" method="get">
					<input type="hidden" name="input_remove_admin" id="input_remove_admin" />
			</form>
			Administers : 
			<ul>
			'.getAdmins().'
			</ul>
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
			
			';
			}
		}
		
		
		//end of admin interface
		?>
		
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
		
		
		var province = <?php echo setProvince(); ?>;
		
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
		
		//these are the calculated values
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
		
		function get_value_float(element){
			var value = document.getElementById(element).value;
			if (value == ""){
				return 0;
			}
			if (isNaN(parseFloat(value))){
				//TODO throw exception?
				return 0;
			}
			return Math.max(parseFloat(value), 0);
		}
		
		function set_value(element, value){
			//todo look nice
			document.getElementById(element).innerHTML = value.toFixed(2);
		}
		
		function calculate(){
			//todo error check :(
			
			province = parseInt(document.getElementById("input_province").value);
			hours = get_value_float("input_hours");
			hours_s = get_value_float("input_hours_s");
			coverage = parseInt(document.getElementById("input_coverage").value);
			decline = document.getElementById("input_decline").checked;
			salary = get_value_float("input_salary");
			cpp = get_cpp_ei(salary);
			set_value("output_cpp", cpp);
			
			salary_s = get_value_float("input_salary_s");
			cpp_s = get_cpp_ei(salary_s);
			set_value("output_cpp_s", cpp_s);
			
			health = get_benefit_cost();
			set_value("output_health", health);
			
			medical = get_medical();
			set_value("output_medical", medical);
			
			hcsa = get_value_float("input_hcsa");
			
			workers = get_workers();
			set_value("output_workers", workers);
			
			conference = get_value_float("input_conference");
			mpd = get_value_float("input_mpd");
			expenses = get_value_float("input_expenses");
			
			subtotal = salary + cpp + salary_s + cpp_s + health + medical + hcsa + workers + conference + mpd + expenses;
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
			set_value("summ_tax", cpp + cpp_s + health + medical + hcsa + workers);
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
				"+" + percent.toFixed(2) + "+"; //must have the extra "+"
			return data;
			input_province
		}
		
		function printData(){
			document.getElementById("download").value=getDownloadData() + "print";
			document.getElementById("downloadDataForm").submit();
		}
		
		function downloadData(){
			document.getElementById("download").value=getDownloadData() + "download";
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
					Please enter the monthly amount you would like to contribute to a Health Care Spending Account
				</td>
				<td name='table_hcsa' id='table_hcsa'>
					<select name="simple_hcsa" id="simple_hcsa" title="Please enter the monthly amount you would like to contribute to a HCSA." value="2">
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
					<input type="text" name="input_name" id="input_name" value="<?php echo getName(); ?>">
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
						Health Care Spending Account
					</p>
				</td>
				<td>
					<select name="input_hcsa" id="input_hcsa" onchange="calculate();" title="Please enter the monthly amount you would like to contribute to a HCSA." value="2">
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
				<td>
					<input type="button" value="Calculate" onclick="calculate();">
				</td>
				<td>
					<input type="button" value="Save" onclick="saveData();">
				</td>
				<td>
					<input type="button" value="Download" onclick="downloadData();">
				</td>
				<td>
					<input type="button" value="Print" onclick="printData();">
				</td>
				<td>
					<input type="button" value="Clear Fields" onclick="clearFields();">
				</td>
				<td>
					<input type="button" value="Back" onclick="back();">
				</td>
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