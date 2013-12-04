<?php
require('pdf/fpdf.php');
include('functions/functions.php');

/*
*Template Name: Allowance Calculator
*
*todo description

req:
	tables-allowance-questiom/answser
		  -support constant
		  -string constant?
	
	ueses wordpress users_meta




choose_way change to button (done)
show name on form-(done) test
have blurb- test (store different way?)
format total dollar currecny (done) test
format question and answer (done)
-have headers (done)
-question over answer (question bolded) (done)
field in only 3-6;
another gestion 
-hours/ percent


total disable salary and affiliate
if no field like accpted disable  intern  (no comp level)
enable asscosate commissoned
	(done) test

change hidden question (done)ishish

todo test calculations (done in brief)

-save (done), print (done)

//second feedback!!!!!!!!!!!!!!!!!

-back button (done)
-defualt 100 (done)(tested)
-onchange (done)
-to top of page (done)
-side ways radio (done)

-todo hide first header (done)


//third feedback!!!!!!!!!!!!!!
annal monthly (done)
--role type (done))

hanging indent (done)
space before bullet (done)
default you (done)

image confindat pdf (done)

intersal see (remark:if hours)


FIX_ME
dump

*
*/

/*(todo: out of date!!!) there is one spot that has hardcoded numbers since, for a question, a specific person you can not choose 
		but for anyone you can choose
	from the admin interface you are able to change things that will need the hardcoded values to change
		such as who can see a question
	-in: setUserValues() and showSomeFor()
*/

$allowance_constant = array(
	'noAccess' => 0,
	'partAccess' => 1,
	'fullAccess' => 2,

	'noAccess_involvementType' => array (
		"Salaried", 
		"Affiliate"
	),

	'roleType' => array(
		'Field Individual', 
		'Field Leader',
		'Corporate Individual',
		'Corporate Leader'
	),
	
	'levelName' => array(
		null, 
		array ( 
			7 => 'Ministry Leader',
			8=> 'Ministry Director',
			9=> 'Domain Leader'
		),
		null,
		array(
			7=> 'Manager / Other Dept. Leader',
			8=> 'Department Director'
		)
	),

	'fieldIndividual' => 0,
	'fieldLeader' => 1,
	'corporateIndividual' => 2,
	'corporateLeader' => 3
);

$current_user = wp_get_current_user();
		
parseUserValuesInput();

include('functions/js_functions.php');

?>
<?php get_header(); ?>
<div id="content">
	<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
	<hr>
    <div id="content-left">
	<div id="main-content">
		<script src="https://code.jquery.com/jquery-latest.js"></script>
		<style type="text/css">
			table {
				border-collapse: separate;
				border-spacing:10px;
			}
			
			th {
				background-color:#eeeeee;
				padding:5px 0;
			}
		
			td {
				text-align:left;
				vertical-align:middle;
			}
			.button {
				width:auto;
				border:0;
				background-color:rgba(0,0,0,0);
			}
			
			#main-content h2, #main-content h3 , #main-content form , #main-content lu, #main-content h1, #content h1{
				margin-left:0;
			}
			
			#main-content h2{
				font-size:150%;
				margin-bottom:10px;
			}
			
			#admin_view hr{
				margin:0;
			}
		</style>
		<?php 
		
		
		function printClasses($roles){
			global $allowance_constant;
			$out = "r".$roles."r ";
			for ($i = 0; $i < count($allowance_constant['roleType']); $i ++){
				if (((1 << $i) & $roles) > 0){
					$out .= "role".$i." ";
				}
			}
			return $out;
		}
		
		function getQuestions(){
			global $wpdb;
			$sql = "SELECT * FROM `allowance_question` ORDER BY  `order` ASC";
			$results = $wpdb->get_results($sql);
			foreach ($results as $result){
				echo "<div class='".printClasses(intval($result->role)).( $result->pull_data ? 'hidden' : '')."' id='tr-".$result->id."'>\n";
				$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$result->id;
				$sub_result = $wpdb->get_results($sql);
				$sub_result = $sub_result[0];
				$current = $sub_result->first_sub;
				switch($result->type){
				case 'dropdown':
					echo "<strong>".$result->label."</strong><BR>\n";
					echo "<select name='form-".$result->id."' id='form-".$result->id."'>";
					break;
				case 'radiobutton': //same for all three
				case 'checkbox':
				case 'radiobutton_sdw':
					echo "<strong>".$result->label."</strong><BR>\n";
					break;
				case 'header':
					echo "<h2>".$result->label."</h2>\n";
					break;
				}
				while($current != '0' && $current !=  NULL){
					$sql = "SELECT * FROM `allowance_answer` WHERE `id`=".$current;
					$sub_result = $wpdb->get_results($sql);
					$sub_result = $sub_result[0];
					switch($result->type){
					case 'dropdown':
						echo "<option value='".$sub_result->id."'>".$sub_result->label."</option>\n";
						break;
					case 'radiobutton':
						echo "<input style='vertical-align:top' type='radio' name='form-".$result->id."' id='form-".$result->id."-".$sub_result->id."' value='".$sub_result->id."'><label for='form-".$result->id."-".$sub_result->id."'  style='margin-left: 5px; display:inline-block; width:510px; margin-top:5px'>".$sub_result->label."</label><BR>\n";
						break;
					case 'radiobutton_sdw':
						echo "<span style='white-space:nowrap;'><input type='radio' name='form-".$result->id."' id='form-".$result->id."-".$sub_result->id."' value='".$sub_result->id."'><label for='form-".$result->id."-".$sub_result->id."'>".$sub_result->label."</label></span>\n";
						break;
					case 'checkbox':
						echo "<input type='checkbox' name='form-".$result->id."-".$sub_result->id."' id='form-".$result->id."-".$sub_result->id."' value='".$sub_result->id."'><label for='form-".$result->id."-".$sub_result->id."'>&nbsp;&nbsp;&nbsp;".$sub_result->label."</label><BR>\n";
						break;
					case 'header': //has no answer, just a label
						break;
					}
					$current = $sub_result->next;
				}
				switch($result->type){
				case 'dropdown':
					echo "</select><BR><BR>\n";
					break;
				case 'radiobutton_sdw':
					echo "<BR>"; //extra break (fall through)
				case 'radiobutton': //same
				case 'checkbox':
					echo "<BR>";
					break;
				case 'header':
					break;
				}
				echo "</div>\n";
			}
			?>
			<input type='button' class= 'role0 role1 role2 role3'  value='Calculate' onclick='calculate();'>
			<?php
		}
		
		function getPoints(){
			global $wpdb;
			echo "{";
			$sql = "SELECT  `id`,  `points` FROM  `allowance_answer`";
			$results = $wpdb->get_results($sql);
			foreach ($results as $result){
				echo $result->id.":".$result->points.",";
			}
			echo "0:0}";
		}
		
		function getMaxPoints(){
			global $wpdb, $allowance_constant;
			echo "new Array(";
			$maxs = array(0,0,0,0);
			$sql = "SELECT * FROM `allowance_question`";
			$results = $wpdb->get_results($sql);
			foreach ($results as $result){
				for($i=0; $i < count($allowance_constant['roleType']); $i ++){
					if ((intval($result->role) & ( 1 << $i)) > 0){
						$maxs[$i] += intval($result->max_points);
					}
				}
			}
			foreach ($maxs as $max){
				echo $max.", ";
			}
			echo "0)";
		}
		
		function getMinMax(){
			echo "new Array(";
			echo "{'min':".getConstant("role_0_0_min").",'max':".getConstant("role_0_0_max")."},";
			
			echo "{ 7 : {'min':".getConstant("role_1_7_min").",'max':".getConstant("role_1_7_max")."},";
			echo "8 : {'min':".getConstant("role_1_8_min").",'max':".getConstant("role_1_8_max")."},";
			echo "9 : {'min':".getConstant("role_1_9_min").",'max':".getConstant("role_1_9_max")."}},";
			
			echo "{'min':".getConstant("role_2_0_min").",'max':".getConstant("role_2_0_max")."},";
			
			echo "{ 7 : {'min':".getConstant("role_3_7_min").",'max':".getConstant("role_3_7_max")."},";
			echo "8 : {'min':".getConstant("role_3_8_min").",'max':".getConstant("role_3_8_max")."}},";
			
			
			echo "0)";
		}
		
		function getPointsEquation($role){
			global $wpdb;
			$sql = "SELECT * FROM `allowance_question` WHERE role & (1 << ".$role.") ORDER BY  `order` ASC";
			$results = $wpdb->get_results($sql);
			foreach ($results as $result){
				echo " printV('".$result->label."', Math.min(".$result->max_points.",";
				switch($result->type){
				case 'dropdown':
					echo  "POINTS[parseInt(document.getElementById('form-".$result->id."').value)])) +";
					break;
				case 'radiobutton': //same for both
				case 'radiobutton_sdw':
					echo "getRadioTotal( new Array(";
					$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$result->id;
					$sub_result = $wpdb->get_results($sql);
					$sub_result = $sub_result[0];
					$current = $sub_result->first_sub;
					while($current != '0' && $current !=  NULL){
						$sql = "SELECT * FROM `allowance_answer` WHERE `id`=".$current;
						$sub_result = $wpdb->get_results($sql);
						$sub_result = $sub_result[0];
						echo "'form-".$result->id."-".$sub_result->id."', ";
						$current = $sub_result->next;
					}
					echo "'null')))) + ";
					break;
				case 'checkbox':
					echo "getCheckTotal( new Array(";
					$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$result->id;
					$sub_result = $wpdb->get_results($sql);
					$sub_result = $sub_result[0];
					$current = $sub_result->first_sub;
					while($current != '0' && $current !=  NULL){
						$sql = "SELECT * FROM `allowance_answer` WHERE `id`=".$current;
						$sub_result = $wpdb->get_results($sql);
						$sub_result = $sub_result[0];
						echo "'form-".$result->id."-".$sub_result->id."', ";
						$current = $sub_result->next;
					}
					echo "'null')))) + ";
					break;
				case 'header': //just a label, no answers
					echo "0)) +";
					break;
				}
			}
			echo "0;";
		}
		
		function getReset(){
		//todo update
			global $wpdb;
			$sql = "SELECT * FROM `allowance_question`";
			$results = $wpdb->get_results($sql);
			foreach ($results as $result){
				$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$result->id;
				$sub_result = $wpdb->get_results($sql);
				$sub_result = $sub_result[0];
				$current = $sub_result->first_sub;
				switch($result->type){
				// reset for all of the question for all role which is why there is a null check
				case 'dropdown':
					if ($current != 0){
						echo "if (document.getElementById('form-".$result->id."') != null) {document.getElementById('form-".$result->id."').value = ".$current.";} \n";
					}
					break;
				case 'radiobutton': //same for all three
				case 'radiobutton_sdw':
				case 'checkbox':
					while($current != '0' && $current !=  NULL){
						$sql = "SELECT * FROM `allowance_answer` WHERE `id`=".$current;
						$sub_result = $wpdb->get_results($sql);
						$sub_result = $sub_result[0];
						echo "if (document.getElementById('form-".$result->id."-".$sub_result->id."') != null) {document.getElementById('form-".$result->id."-".$sub_result->id."').checked = false;}\n";
						$current = $sub_result->next;
					}
					break;
				case 'header': // just a label, no answers
					break;
				}
			}
		}
		
		function getRole($id){
			global $allowance_constant;
			$isField = intVal(getFieldEmployee("is_field_staff", $id));
			$level = intVal(getFieldEmployee("compensation_level", $id));
			
			
			//levels 1 - 5 = individual
			//levels 6 - 10 = leader
			if ($isField == 1){
				if ($level < 6){
					return $allowance_constant['fieldIndividual'];
				}
				else{
					return $allowance_constant['fieldLeader'];
				}
			}
			else {
				if ($level < 6){
					return $allowance_constant['corporateIndividual'];
				}
				else {
					return $allowance_constant['corporateLeader'];
				}
			}
		}
		
		function getAccess($id){
			global $allowance_constant, $current_user;
			if (isAdmin() && $id == $current_user->id){
				return $allowance_constant['partAccess'];
			}
			$involvment_type = getFieldEmployee("involvement_type", $id);
			if (in_array($involvment_type, $allowance_constant['noAccess_involvementType'])){
				return $allowance_constant['noAccess'];
			}
			if (getFieldEmployee("compensation_level", $id) == null){
				return $allowance_constant['partAccess'];
			}
			return $allowance_constant['fullAccess'];
			
		}
		
		function setUserValues($id){
			global $wpdb, $allowance_constant;
			echo "function() {reset();";
			echo "document.getElementById('hour_precentage').value ='".getFieldEmployee("percent_of_fulltime", $id)."';\n";
			$level = intVal(getFieldEmployee("compensation_level", $id));
			//** HARDCODED **// this is to set the preset user values;
			//if clean_tree() in allowance-calculator-admin is run $q may have to change
			//if the sturture of this two question change (the ones with pulled data) this code may need to be changed
			
			switch(getRole($id)){
			case $allowance_constant['fieldIndividual']:
				//levels 3-5 are mapped to the the three answers respectively (levels 1, 2 will not be using this.  if they do, result is undefined.)
				$q = 11;
				
				$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$q;
				$result = $wpdb->get_results($sql);
				$offset = intVal($result[0]->first_sub) - 3;
				echo "document.getElementById('form-".$q."-".($offset + $level)."').check = true;\n";
				break;
			case $allowance_constant['fieldLeader']:
				//at and above level 8 is the same catergory
				echo "document.getElementById('extra-field-".min($level,8)."').checked = true;\n";
				break;
			case $allowance_constant['corporateIndividual']:
				//levels 1-5 are mapped to the five answers
				$q = 12;
				$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$q;
				$result = $wpdb->get_results($sql);
				$offset = intVal($result[0]->first_sub - 1);
				echo "document.getElementById('form-".$q."-".($offset + $level)."').check = true;\n";
				break;
			case $allowance_constant['corporateLeader']:
				//at and above level 7 is the same catergory
				echo "document.getElementById('extra-corp-".min($level,7)."').checked = true;\n";
				break;
			}
			//** END HARDCODED **//
			
			$result = get_user_meta($id, 'allowance_calculator_user_values', true);
			if ($result == ""){
				echo "}";
				return;
			}
			foreach (explode(',', $result) as $value){
				if ($value == "Null" || explode(":", $value) == "userIs"){
					continue;
				}
				
				$parts = explode(":",$value);
				$parts = explode("-",  $parts[0]);
				$quest_id = $parts[1];
				$sql = "SELECT `type` , `pull_data`, `role` FROM `allowance_question` WHERE `id` =".$quest_id;
				$results = $wpdb->get_results($sql);
				if ($results[0]->pull_data or ((intval($results[0]->role)) & (1 << intval(getRole($id)))) == 0){
					continue; //overrides stored values (they are not stored most likey but just in case);
				}
				$parts = explode(":", $value);
				$part_0 = $parts[0];
				$part_1 = $parts[1];
				switch ($results[0]->type){
				case 'dropdown':
					echo "document.getElementById('".$part_0."').value = ".$part_1.";\n";
					break;
				case 'radiobutton': //same for both
				case 'radiobutton_sdw':
					echo "document.getElementById('".$part_0."-".$part_1."').checked = true;\n";
					break;
				case 'checkbox':
					echo "document.getElementById('".$part_0."').checked = true;\n";
					break;
				case 'header': //just a label, no answers
					break;
				}
			}
			echo "}";
		}
		
		if (isAdmin()){
			include('admin-interface/allowance-calculator-admin.php');
		}
		
		function parseUserValuesInput(){
			global $current_user, $wpdb, $allowance_constant;
			dump($_POST);
			if (isset($_POST['print']) and $_POST['print'] == 'true'){
				$pdf = new FPDF();
				$pdf->AddPage();
				$pdf->Image(get_stylesheet_directory_uri(). '/res/footer-logo.png'); //todo change fix!!
				$pdf->SETXY(60, 15);
				$pdf->SetFont('Arial','b',16);
				$pdf->Write(5,'Allowance Calculator');
				$pdf->LN();
				$pdf->LN();
				$pdf->SetFont('Arial','',12);
			
				switch($_POST['userIs']){
				case 'you':
					$pdf->Write(5, getName());$pdf->LN();
					break;
				case 'spouse':
					$pdf->Write(5, getName(getSpouse()));$pdf->LN();
					break;
				case 'free':
					break;
				}
				switch($_POST['role']){
				case $allowance_constant['fieldIndividual']:
				case $allowance_constant['corporateIndividual']:
					$pdf->Write(5, "Role: ".$allowance_constant['roleType'][$_POST['role']]);$pdf->LN();
					break;
				case $allowance_constant['fieldLeader']:
				case $allowance_constant['corporateLeader']:
					$pdf->Write(5, "Role: ".$allowance_constant['roleType'][$_POST['role']]." - ".$allowance_constant['levelName'][$_POST['role']][$_POST['extra_level']]);$pdf->LN();
					break;
				default:
					$pdf->Write(5, 'default');$pdf->LN();
				}
				$pdf->LN();
				$pdf->SetFont('Arial','b',12);
				$pdf->Write(5, getStringConstant("hour_label"));$pdf->LN();
				$pdf->SetFont('Arial','',12);
				$pdf->Write(5, "       ".$_POST['hour_precentage']."%");$pdf->LN();
				$pdf->LN();
				
				
				$array_key = array_keys($_POST);
				$sql = "SELECT * FROM `allowance_question` WHERE `role` & (1 <<".$_POST['role'].") ORDER BY  `order` ASC";
				$results = $wpdb->get_results($sql);
				foreach ($results as $result){
					$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$result->id;
					$sub_result = $wpdb->get_results($sql);
					$sub_result = $sub_result[0];
					$current = $sub_result->first_sub;
					switch($result->type){
						case 'radiobutton': //same for all four
						case 'radiobutton_sdw':
						case 'dropdown':
						case 'checkbox':
							$pdf->SetFont('Arial','b',12);
							$pdf->Write(5, $result->label);$pdf->LN();
							$pdf->SetFont('Arial','',12);
							foreach ($array_key as $key){
								$parts = explode("-", $key);
								$part_0 = $parts[0];
								$part_1 = $parts[1];
								if ($part_0 == 'form' and $part_1 == $result->id){
									$sql = "SELECT * FROM `allowance_answer` WHERE `id`=".$_POST[$key];
									$sub_result = $wpdb->get_results($sql);
									$sub_result = $sub_result[0];
									$pdf->Write(5, "       ".$sub_result->label);$pdf->LN();
									unset($_POST[$key]);
								}
							}
							break;
						case 'header':
							$pdf->SetFont('Arial','b',16);
							$pdf->Write(5, $result->label);$pdf->LN();
							$pdf->SetFont('Arial','',12);
							break;
					}
					$pdf->LN();
				}
				$pdf->LN();
				$widthL = Max($pdf->GetStringWidth("Recommended Minimum:"), $pdf->GetStringWidth("Staff Member's Personal Maximum:")) + 5;
				$widthV = Max($pdf->GetStringWidth($_POST['minimum']), $pdf->GetStringWidth($_POST['maximum']), $pdf->GetStringWidth('Annual')) + 5;
				$widthM = Max($pdf->GetStringWidth($_POST['minimum_month']), $pdf->GetStringWidth($_POST['maximum_month']), $pdf->GetStringWidth('Monthly'));
				
				$pdf->Cell($widthL,5, "");
				$pdf->Cell($widthV,5, 'Annual');
				$pdf->Cell($widthM,5, 'Monthly', 0, 1, "R");
				
				$pdf->Cell($widthL,5, "Recommended Minimum:");
				$pdf->Cell($widthV,5, $_POST['minimum']);
				$pdf->Cell($widthM,5, $_POST['minimum_month'], 0, 1, "R");
				
				$pdf->Cell($widthL,5, "Staff Member's Personal Maximum:");
				$pdf->Cell($widthV,5, $_POST['maximum']);
				$pdf->Cell($widthM,5, $_POST['maximum_month'], 0, 1, "R");
				$pdf->LN();
				$pdf->Write(5,'Confidential');
				$pdf->Output();
				exit;
			}
			if (isset($_POST['userIs'])){
				
				if($_POST['userIs'] == 'you' and  getAccess($current_user->id) == $allowance_constant['fullAccess']){
					$id = $current_user->id;
				}
				else if ($_POST['userIs'] == 'spouse' and getSpouse() != -1 and getAccess(getSpouse()) == $allowance_constant['fullAccess']){	
					$id = getSpouse();
				}
				else {
					return;
				}
				$data = "";
				$array_key = array_keys($_POST);
				for ($i = 0; $i < count($_POST); $i ++){
					$key = $array_key[$i];
					$parts = explode("-", $key);
					if ($parts[0] == 'form'){
						$data .= $key.":".$_POST[$key].",";
					}
				}
				$data .= "Null";
				update_user_meta($id, 'allowance_calculator_user_values', $data);
				
				//set values
				echo setUserValues($id);
				exit;
			}
		}
		
		function dump($d){
			global $wpdb;
			$sql = "INSERT INTO  `var_dump` (`id` ,`dump` ,`time`) VALUES (NULL ,'".mysql_real_escape_string(var_export($d, true))."', NULL)";
			//todo var_dump($_POST);
			//echo $sql;
			//$wpdb->get_results($sql);
		}
		
		if (getAccess($current_user->id) == $allowance_constant['noAccess']){
			?>
			No ACCESS
			<?php
		}
		else {
		
			?>
			
			<script type="text/javascript">
				
				var YOU = 0;
				var SPOUSE = 1;
				var FREE = 2;
				
				var FIELD_INDIVIDUAL = <?php  echo $allowance_constant['fieldIndividual'] ?> ;
				var FIELD_LEADER = <?php  echo $allowance_constant['fieldLeader'] ?> ;
				var CORPORATE_INDIVIDUAL = <?php  echo $allowance_constant['corporateIndividual'] ?> ;
				var CORPORATE_LEADER = <?php  echo $allowance_constant['corporateLeader'] ?> ;
				
				var POINTS = <?php getPoints() ?>;
				var MAX_POINTS = <?php getMaxPoints() ?>;
				var MIN_MAX = <?php getMinMax() ?>;
				
				function getMinMax(role, level){
					if (role == FIELD_INDIVIDUAL || role == CORPORATE_INDIVIDUAL){
						return MIN_MAX[role];
					}
					else {
						return MIN_MAX[role][level];
					}
				}
			
				var BLURBS = {whichWay: '<?php echo changeNL(getStringConstant("blurb_0")) ?>',
						result: '<?php echo changeNL(getStringConstant("blurb_2"))?>'};
						
			
				function showSection(section){
					$('html,body').scrollTop(0);
				
					//hide all
					document.getElementById('section_whichWay').style.display  = "none";
					//document.getElementById('section_enterAll').style.display  = "none";
					document.getElementById('section_result').style.display  = "none";
					//show the one
					document.getElementById('section_' + section).style.display = "block";
					document.getElementById('blurb').innerHTML = BLURBS[section];
					 console.log("Show:" + section);
				}
				
				function showQuestions(role){
					//hide all
					$(".role0").hide();
					$(".role1").hide();
					$(".role2").hide();
					$(".role3").hide();
					
					//show stuff
					if (role > -1){
						$(".role" + role).show();
					}
					else {
						document.getElementById('hours').style.display = "none";
						document.getElementById('role_type_field').style.display = "none";
						document.getElementById('role_type_corp').style.display = "none";
					}
				}
				
				<?php if(getAccess($current_user->id) == $allowance_constant['fullAccess']) { ?>
				var you = {role:<?php echo getRole($current_user->id) ?>, name:'<?php echo getName() ?>', setValues: <?php setUserValues($current_user->id) ?>};
				<?php }
				if (getSpouse() != -1 and getAccess(getSpouse()) == $allowance_constant['fullAccess']) { ?>
				var spouse = {role:<?php echo getRole(getSpouse());?>, name:'<?php echo getName(getSpouse()) ?>', setValues: <?php setUserValues(getSpouse()) ?> };
				<?php } ?>
				
				var chooseWay = -1;
				
				function proceed(cw){
					chooseWay = cw
					switch(chooseWay){
					case YOU:
						showSomeFor(you);
						break;
					case SPOUSE:
						showSomeFor(spouse);
						break;
					case FREE:
						//show choose_role
						document.getElementById('user_name').innerHTML = "";
						document.getElementById('choose_role_div').style.display = "block";
						showQuestions(-1);
					break;
					}
				}
				
				function showSomeFor(who){
					document.getElementById('choose_role_div').style.display = "none";
					
					document.getElementById('hours').style.display = "none";
					document.getElementById('role_type_field').style.display = "none";
					document.getElementById('role_type_corp').style.display = "none";
					
					who.setValues();
					document.getElementById('user_name').innerHTML = who.name;
					showQuestions(who.role);
					$(".hidden").hide();
				}
				
				function select_role(){
					var role = parseInt(document.getElementById('choose_role').value);
					document.getElementById('hours').style.display = "block";
					document.getElementById('role_type_field').style.display = "none";
					document.getElementById('role_type_corp').style.display = "none";
					if (role == FIELD_LEADER){
						document.getElementById('role_type_field').style.display = "block";
					}
					else if (role == CORPORATE_LEADER){
						document.getElementById('role_type_corp').style.display = "block";
					}
					
					reset();
					showQuestions(role)
				}
				
				function getCheckTotal(checks){
					var total = 0;
					for(c in checks){
						if (checks[c] == "null"){
							continue;
						}
						else if(document.getElementById(checks[c]).checked){
							total += POINTS[document.getElementById(checks[c]).value];
						}
					}
					return total;
				}
				
				function getRadioTotal(radios){
					for(r in radios){
						if (radios[r] == "null"){
							continue;
						}
						else if(document.getElementById(radios[r]).checked){
							return POINTS[document.getElementById(radios[r]).value];
						}
					}
					return 0;
				}
				
				function calculate(){
					displayResult();
				}
				
				function calculatePoints(role){
					document.getElementById('debug').innerHTML = "";
					switch(role){
					case FIELD_INDIVIDUAL:
						return <?php getPointsEquation($allowance_constant['fieldIndividual']) ?>
						break;
					case FIELD_LEADER:
						return <?php getPointsEquation($allowance_constant['fieldLeader']) ?>
						break;
					case CORPORATE_INDIVIDUAL:
						return <?php getPointsEquation($allowance_constant['corporateIndividual']) ?>
						break;
					case CORPORATE_LEADER:
						return <?php getPointsEquation($allowance_constant['corporateLeader']) ?>
						break;
					}
					showSelection('result');
				}
				
				function printV(who, v){
					console.log("%" + who + "%" + v);
					<?php if(isAdmin()) { ?>
					document.getElementById('debug').innerHTML += who + " " + v + "<BR>";
					<?php } ?>
					return v;
				}
				
				function displayResult(){
					switch(chooseWay){ 
					case YOU:
						var role = you.role;
						document.getElementById('buttonSave').style.display = "block";
						break;
					case SPOUSE:
						var role = spouse.role;
						document.getElementById('buttonSave').style.display = "block";
						break;
					case FREE:
						var role = parseInt(document.getElementById('choose_role').value);
						document.getElementById('buttonSave').style.display = "none";
						break;
					}
					var minMax = getMinMax(role, $('input[name=extra_level]:checked').val())
					var h =  get_value_float('hour_precentage');
					console.log(h);
					document.getElementById('output_minimum').innerHTML = number2currency(minMax.min * h / 100);
					document.getElementById('output_minimum_month').innerHTML = number2currency(minMax.min * h / 100 / 12);
					console.log(role);
					var p = calculatePoints(role);
					console.log(p);
					console.log(minMax.max - minMax.min);
					console.log((minMax.max - minMax.min) *  p / MAX_POINTS[role]);
					document.getElementById('output_maximum').innerHTML = number2currency((minMax.min + (minMax.max - minMax.min) *  p / MAX_POINTS[role]) * h / 100);
					document.getElementById('output_maximum_month').innerHTML = number2currency((minMax.min + (minMax.max - minMax.min) *  p / MAX_POINTS[role]) * h / 100 /12);
					showSection('result');
				}
				
				function saveUserValues(){
					switch(chooseWay){
					case YOU:
						document.getElementById('userIs').value = 'you';
						$.post( "",  $("#saveUserValues_form" ).serialize(), function( data ) {eval("you['setValues'] = " + data + ";"); alert("Saved!");});
						break;
					case SPOUSE:
						document.getElementById('userIs').value = 'spouse';
						$.post( "", $("#saveUserValues_form" ).serialize(), function( data ) { eval("spouse['setValues'] = " + data + ";"); alert("Saved!");});
						break;
					case FREE:
						break;
					}
				}
				
				function reset(){
					document.getElementById('user_name').innerHTML = "";
					document.getElementById('extra-field-7').checked = false;
					document.getElementById('extra-field-8').checked = false;
					document.getElementById('extra-field-9').checked = false;
					document.getElementById('extra-corp-7').checked = false;
					document.getElementById('extra-corp-8').checked = false;
					document.getElementById('hour_precentage').value = "100";
					<?php getReset() ?>
				}
				
				function download(){
					document.getElementById('print').value = true;
					switch(chooseWay){
					case YOU:
						document.getElementById('userIs').value = 'you';
						document.getElementById('role').value = you.role;
						break;
					case SPOUSE:
						document.getElementById('role').value = spouse.role;
						document.getElementById('userIs').value = 'spouse';
						break;
					case FREE:
						document.getElementById('role').value = document.getElementById('choose_role').value;
						document.getElementById('userIs').value = 'free';
						break;
					}
					document.getElementById('minimum').value = document.getElementById('output_minimum').innerHTML;
					document.getElementById('maximum').value = document.getElementById('output_maximum').innerHTML;
					document.getElementById('minimum_month').value = document.getElementById('output_minimum_month').innerHTML;
					document.getElementById('maximum_month').value = document.getElementById('output_maximum_month').innerHTML;
					document.getElementById('saveUserValues_form').target = "_blank";
					saveUserValues_form.submit();
				}
				
				function backTo(section){ //could be more
					showSection(section);
				}
				
				
				
				function window_load(){
					$("input[name='whichWay']").change(function(){
						if (document.getElementById('show_you') != null && document.getElementById('show_you').checked){
							proceed(0);
						}
						if (document.getElementById('show_spouse') != null && document.getElementById('show_spouse').checked){
							proceed(1);
						}
						if (document.getElementById('show_anyone') != null && document.getElementById('show_anyone').checked){
							proceed(2);
						}
					});
					
					<?php if(getAccess($current_user->id) == $allowance_constant['fullAccess']) { ?>
						document.getElementById('show_you').checked = true;
						proceed(0);
					<?php } else if (getSpouse() != -1 and getAccess(getSpouse()) == $allowance_constant['fullAccess']) { ?>
						document.getElementById('show_spouse').checked = true;
						proceed(1)
					<?php } else { ?>
						document.getElementById('show_anyone').checked = true;
						proceed(2)
					<?php } ?>
				}
				
				
				window.onload = window_load;
				
			</script>
			<div id='blurb'><?php echo changeNL(getStringConstant("blurb_0")) ?></div>
			<BR>
			<div id='section_whichWay'>
				Please select an option:<BR>
				<?php if(getAccess($current_user->id) == $allowance_constant['fullAccess']) { ?>
				<input type='radio' name='whichWay' id='show_you' value='0'><label for='show_yourself'>Calculate for yourself</label>
				<?php }
				if (getSpouse() != -1 and getAccess(getSpouse()) == $allowance_constant['fullAccess']) { // hides the option if there is no spouse ?>
				<input type='radio' name='whichWay' id='show_spouse' value='1' ><label for='show_spouse'>Calculate for spouse</label>
				<?php } ?>
				<option value="2">Calculate for anyone</option>
				<input type='radio' name='whichWay' id='show_anyone' value='2'><label for='show_anyone'>Calculate for anyone</label>
				<BR>
				<BR>
				<div style='font-size:125%' id='user_name'></div>
				<div id='section_enterAll' style=' /* display:none; */'>
					<div id='choose_role_div'><select id="choose_role" onchange='select_role();'>
					<?php
						global $allowance_constant;
					for($i = 0; $i < count($allowance_constant['roleType']); $i ++){
						echo "<option value='".$i."'>".$allowance_constant['roleType'][$i]."</option>";
					}?>
					</select>
					<input type='button' value='Select' onclick='select_role();'></div>
					<!--  this are the extra special questions-->
					<form name="saveUserValues_form" id="saveUserValues_form" action="" method="post">
						<div id='questions'>
							<BR>
							<div id='hours'>
								<h2><?php echo getStringConstant("first_header") ?></h2><BR>
								<strong><?php echo getStringConstant("hour_label") ?></strong><BR>
								<input type='text' size='5' name='hour_precentage' id='hour_precentage' value='100'><BR><BR>
							</div>
							<div id='role_type_field'>
								<strong>Role Type</strong><BR>
								<input type='radio' name='extra_level' id='extra-field-7' value='7'><label for='extra-field-7'>Ministry Leader (all other types)</label><BR>
								<input type='radio' name='extra_level' id='extra-field-8' value='8'><label for='extra-field-8'>Ministry Director</label><BR>
								<input type='radio' name='extra_level' id='extra-field-9' value='9'><label for='extra-field-9'>Domain Leader</label><BR><BR>
							</div>
							<div id='role_type_corp'>
								<strong>Role Type</strong><BR>
								<input type='radio' name='extra_level' id='extra-corp-7' value='7'><label for='extra-corp-7'>Manager / Other Dept. Leader</label><BR>
								<input type='radio' name='extra_level' id='extra-corp-8' value='8'><label for='extra-corp-8'>Department Director</label><BR><BR>
							</div>
							
							<input type='hidden' name='userIs' id='userIs'>
							<input type='hidden' name='print' id='print'>
							<input type='hidden' name='minimum' id='minimum'>
							<input type='hidden' name='maximum' id='maximum'>
							<input type='hidden' name='minimum_month' id='minimum_month'>
							<input type='hidden' name='maximum_month' id='maximum_month'>
							<input type='hidden' name='role' id='role'>
							<?php getQuestions($allowance_constant['fieldIndividual']) ?>
						</div>
					</form>
				</div>
			</div>
			<div id='section_result' style='display:none;'>
				<?php if(isAdmin()){ ?>
					<div id='debug'>
					</div>
				<?php } ?>
				<table>
					<tr>
						<td></td>
						<td>Annual</td>
						<td>Monthly</td>
					</tr>
					<tr>
						<td>Recommended Minimum:</td>
						<td id='output_minimum'></td>
						<td id='output_minimum_month'></td>
					</tr>
					<tr>
						<td>Staff Member's Personal Maximum:</td>
						<td id='output_maximum'></td>
						<td id='output_maximum_month'></td>
					</tr>
				</table>
				<table class='button'><tr>
					<td class='button'><input type='button' value='Restart' onclick='reset();showSection("whichWay");'></td>
					<td class='button'><input type='button' id='buttonSave' value='Save' onclick='saveUserValues();'></td>
					<td class='button'><input type='button' value='Download' onclick='download();'></td>
					<td class='button'><input type='button' value='Back' onclick='backTo("whichWay");'></td>
				</tr></table>
			</div>
		<?php } ?>
    </div>
    </div>
    <div id="content-right"><?php get_sidebar(''); ?></div><div style='clear:both;'></div>
</div>
<!--content end-->
<!--Popup window-->
</div>
<!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>		
<?php get_footer(); ?>