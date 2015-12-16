<?php
require('pdf/fpdf.php');
include('functions/functions.php');

/*
*Template Name: zApp Allowance Calculator
*
*Author: matthew.chell
*
*Description: A calculator to find out how much allowance a person has to raise.

req:
	tables-allowance-question/answer
		  -support constant
		  -string constant
	
	ueses wordpress users_meta


*
*/

/* ****NOTE: HARDCODED VALUES****
there is one spot that has hardcoded numbers since, for two questions
		(such as What is the scope of ministry operations for which the staff member is  responsible? 
		(which what level a person is)), for a specific person (such as for you or your spouse), you can not choose the answer
		but for anyone you can choose the answer for the question.
	since the question uses hardcoded numbers then some of the edit capabilities have been disable in the admin interface
		from the admin interface you should not be able to change anything that would need the hardcoded numbers to change
	the hardcoded values are in function setUserValues()
	
	
	
the number of hours question is different from the rest of the questions.  From the admin interface you can
	only change the label of the question.  The answer is open ended.  It does not have predefined answer
	like the other question.  Also it is used to pro rate the result calculated by the other questions.
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
			6 => 'Managers and Other Ministry Leaders',
			7=> 'Ministry Director',
			8=> 'Domain Leader'
		),
		null,
		array(
			6=> 'Manager / Other Dept. Leader',
			7=> 'Department Director'
		)
	),

	'fieldIndividual' => 0,
	'fieldLeader' => 1,
	'corporateIndividual' => 2,
	'corporateLeader' => 3
);

$current_user_id = wp_get_current_user()->id;
		
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
			
			#main-content * {
				font-size: 12pt;
			}
			
			#main-content strong {
				margin-bottom:10px;
				display:block;
			}
			@media (max-width: 767px) {
				#main-content div {
					margin: 0 10px;
				}
				
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
					echo "<strong>".$result->label."</strong>\n";
					echo "<select name='form-".$result->id."' id='form-".$result->id."'>";
					break;
				case 'radiobutton': //same for all three
				case 'checkbox':
				case 'radiobutton_sdw':
					echo "<strong>".$result->label."</strong>\n";
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
						echo "<input style='vertical-align: top; position: relative; top: 6px;' type='radio' name='form-".$result->id."' id='form-".$result->id."-".$sub_result->id."' value='".$sub_result->id."'><label for='form-".$result->id."-".$sub_result->id."'  style='margin-left: 5px; display:inline-block; width:90%; margin-top:5px'>".$sub_result->label."</label><BR>\n"; //510px width previously
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
		
		function getAnswers(){
			global $wpdb;
			echo "{";
			$sql = "SELECT  `id`,  `label` FROM  `allowance_answer`";
			$results = $wpdb->get_results($sql);
			foreach ($results as $result){
				echo $result->id.":'".$result->label."',";
			}
			echo "0:0}";
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
			
			echo "{ 6 : {'min':".getConstant("role_1_6_min").",'max':".getConstant("role_1_6_max")."},";
			echo "7 : {'min':".getConstant("role_1_7_min").",'max':".getConstant("role_1_7_max")."},";
			echo "8 : {'min':".getConstant("role_1_8_min").",'max':".getConstant("role_1_8_max")."}},";
			
			echo "{'min':".getConstant("role_2_0_min").",'max':".getConstant("role_2_0_max")."},";
			
			echo "{ 6 : {'min':".getConstant("role_3_6_min").",'max':".getConstant("role_3_6_max")."},";
			echo "7 : {'min':".getConstant("role_3_7_min").",'max':".getConstant("role_3_7_max")."}},";
			
			
			echo "0)";
		}
		
		function getSelectAnswers($role){
			global $wpdb;
			echo "'<strong>".getStringConstant("first_header")."</strong>' + ";
			$sql = "SELECT * FROM `allowance_question` WHERE role & (1 << ".$role.") ORDER BY  `order` ASC";
			$results = $wpdb->get_results($sql);
			foreach ($results as $result){
				echo "'<strong>".$result->label."</strong>' + ";
				switch($result->type){
				case 'dropdown':
					echo  "ANSWERS[parseInt(document.getElementById('form-".$result->id."').value)] + '<BR>' +";
					echo "'<BR>' + ";
					break;
				case 'radiobutton': //same for both
				case 'radiobutton_sdw':
					echo "getRadioAnswers( new Array(";
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
					echo "'null')) + ";
					echo "'<BR>' + ";
					break;
				case 'checkbox':
					echo "getCheckAnswers( new Array(";
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
					echo "'null')) + ";
					echo "'<BR>' + ";
					break;
				case 'header': //just a label, no answers
					break;
				}
			}
			echo "'';";
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
			global $allowance_constant, $current_user_id;
			$involvment_type = getFieldEmployee("involvement_type", $id);
			if (in_array($involvment_type, $allowance_constant['noAccess_involvementType'])){
				if ($id == $current_user_id  && false && (isAdmin() || AdminLevel(1))){
					return $allowance_constant['partAccess'];
				}
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
			/* Jason B  2015-02-26: Commented out as per request from Jordan Tarr. They would like to force staff to enter this,
			 *                      rather than pre-populating it for them.
			echo "document.getElementById('hour_percentage').value ='".getFieldEmployee("percent_of_fulltime", $id)."';\n";
			*/
			
			//** HARDCODED **// this is to set the preset user values;
			//if clean_tree() in allowance-calculator-admin is run $q may have to change
			//if the sturture of these two question change (the ones with pulled data) this code may need to be changed
			
			$level = intVal(getFieldEmployee("compensation_level", $id));
			switch(getRole($id)){
			case $allowance_constant['fieldIndividual']:
				//levels 3-5 are mapped to the the three answers respectively (levels 1, 2 will not be using this.  if they do, result is undefined.)
				$q = 11;
				
				$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$q;
				$result = $wpdb->get_results($sql);
				$offset = intVal($result[0]->first_sub) - 3;
				echo "document.getElementById('form-".$q."-".($offset + $level)."').checked = true;\n";
				break;
			case $allowance_constant['fieldLeader']:
				//at and above level 8 are in the same catergory
				echo "document.getElementById('extra-field-".min($level,8)."').checked = true;\n";
				break;
			case $allowance_constant['corporateIndividual']:
				//levels 3-5 are mapped to the five answers
				$q = 12;
				$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$q;
				$result = $wpdb->get_results($sql);
				$offset = intVal($result[0]->first_sub - 3);
				echo "document.getElementById('form-".$q."-".($offset + $level)."').checked = true;\n";
				break;
			case $allowance_constant['corporateLeader']:
				//at and above level 7 are in the same catergory
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
					continue; //overrides stored values (they are not stored, most likey, but just in case);
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
			global $current_user_id, $wpdb, $allowance_constant;
			dump($_POST);
			if (isset($_POST['print']) and $_POST['print'] == 'true'){
				$pdf = new FPDF();
				$pdf->AddPage();
				
				$pdf->Image(get_stylesheet_directory_uri(). '/res/footer-logo.png'); //todo change fix!!
				$pdf->SETXY(60, 15);
				$pdf->SetFont('Arial','b',14);
				$pdf->Write(5,'Allowance Calculator');
				$pdf->SETX(140);
				$pdf->SetFont('Arial','',10);
				$pdf->Write(5,'Effective Date: '.(($_POST['effective'] == "") ? '_____________' : $_POST['effective']));
				$pdf->SetFont('Arial','b',16);
				$pdf->LN();
				$pdf->Write(5,'');
				$pdf->LN();
				$pdf->LN();
				$pdf->SetFont('Arial','',10);
				$pdf->Write(5, "Name: $_POST[person_name]");$pdf->LN();
				switch($_POST['userIs']){
				case 'you':
					$pdf->Write(5, "Ministry/Department: ".getFieldEmployee('ministry'));$pdf->LN();
					$pdf->Write(5, "Position Title: ".getFieldEmployee('role_title'));$pdf->LN();
					break;
				case 'spouse':
					$pdf->Write(5, "Ministry/Department: ".getFieldEmployee('ministry', getSpouse()));$pdf->LN();
					$pdf->Write(5, "Position Title: ".getFieldEmployee('role_title', getSpouse()));$pdf->LN();
					break;
				case 'free':
					break;
				}
				$pdf->Write(5, "Project Code: $_POST[projectCode]");$pdf->LN();
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
				$pdf->SetFont('Arial','b',13);
				$pdf->Write(5, getStringConstant("first_header"));$pdf->LN();$pdf->LN();
				$pdf->SetFont('Arial','b',11);
				$pdf->Write(5, getStringConstant("hour_label"));$pdf->LN();
				$pdf->SetFont('Arial','',10);
				$pdf->Write(5, "       ".$_POST['hour_percentage']."%");$pdf->LN();
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
							$pdf->SetFont('Arial','b',11);
							$pdf->Write(5, $result->label);$pdf->LN();
							$pdf->SetFont('Arial','',10);
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
							$pdf->SetFont('Arial','b',13);
							$pdf->Write(5, $result->label);$pdf->LN();
							$pdf->SetFont('Arial','',10);
							break;
					}
					$pdf->LN();
				}
				
				$pdf->setY($pdf->getY() - 5);
				
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
				
				$lineDrop = 4;
				$pdf->Line(10, $pdf->GetY(), 195, $pdf->GetY());
				$pdf->LN();
				$pdf->SetFont('Arial','b',10);
				$pdf->Write(5,'Change in Allowance or Hours');
				$pdf->SetFont('Arial','',10);
				$pdf->LN();
				if ($_POST['preAllowance'] == ""){
					$pdf->Write(5,'Previous Allowance:__________________________________'); 
				}
				else {
					$pdf->Write(5,"Previous Allowance: $_POST[preAllowance]"); 
				}
				$pdf->SETX(112);
				if ($_POST['newAllowance'] == ""){
					$pdf->Write(5,'New Allowance: _____________________________');
				}
				else {
					$pdf->Write(5,"New Allowance:  $_POST[newAllowance]");
				}
				$pdf->LN();
				$pdf->LN();
				
				if ($_POST['preHours'] == ""){
					$pdf->Write(5,'Previous number of hours:_____________________________'); 
				}
				else {
					$pdf->Write(5,"Previous number of hours: $_POST[preHours]"); 
				}
				$pdf->SETX(112);
				if ($_POST['newHours'] == ""){
					$pdf->Write(5,'New number of hours: ________________________');
				}
				else {
					$pdf->Write(5,"New number of hours:  $_POST[newHours]");
				}
				$pdf->LN();
				$pdf->SetFont('Arial','bi',10);
				$pdf->Write(5,'** If schedule is less than 40 hours per week enter normal days/ hours worked');
				$pdf->SetFont('Arial','',10);
				$pdf->LN();
				$pdf->LN();
				$line = ' ________';
				$pdf->Write(5,"Monday: ".(($_POST['mon'] == "") ? $line : $_POST['mon']).
						"    Tuesday: ".(($_POST['tues'] == "") ? $line : $_POST['tues']).
						"    Wednesday: ".(($_POST['wed'] == "") ? $line : $_POST['wed']).
						"    Thursday: ".(($_POST['thurs'] == "") ? $line : $_POST['thurs']).
						"    Friday: ".(($_POST['fri'] == "") ? $line : $_POST['fri']));
				
				$pdf->Line(10, $pdf->GetY() + 8, 195, $pdf->GetY() + 8);
				$pdf->LN();
				
				$pdf->LN();
				$pdf->LN();
				$pdf->Write(5,'Staff Member Signature: __________________________________________________');
				$pdf->SETX(152);
				$pdf->Write(5,'Date: '.(($_POST['date'] == "") ? $line : $_POST['date']));
				$pdf->LN();
				$pdf->LN();
				$pdf->Write(5,'Ministry/Department Director Signature: _______________________________________');
				$pdf->SETX(152);
				$pdf->Write(5,'Date: '.(($_POST['date'] == "") ? $line : $_POST['date']));
				$pdf->LN();
				$pdf->LN();
				$pdf->Write(5,'HR Authorizing Agent: _____________________________________________________ ');
				$pdf->SETX(152);
				$pdf->Write(5,'Date: '.(($_POST['date'] == "") ? $line : $_POST['date']));
				
				//to counter act the wp-minify plugin (ob_start(array($this, 'modify_buffer'));)
				ob_end_clean();
				$pdf->Output('allowance_calculator.pdf', 'I');
				exit;
			}
			if (isset($_POST['userIs'])){
				
				if($_POST['userIs'] == 'you' and  getAccess($current_user_id) == $allowance_constant['fullAccess']){
					$id = $current_user_id;
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
			//echo $sql;
			//$wpdb->get_results($sql);
		}
		
		if (getAccess($current_user_id) == $allowance_constant['noAccess']){
			?>
			The Allowance Calculator is only available for Commissioned and Associate staff.
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
				
				var ANSWERS = <?php getAnswers() ?>;
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
			
				var BLURBS = {whichWay: '<?php echo changeNL(getStringConstant("blurb_0", "'")) ?>',
						result: '<?php echo changeNL(getStringConstant("blurb_2", "'"))?>'};
						
			
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
						document.getElementById('name_project_code').style.display = "none";
						document.getElementById('hours').style.display = "none";
						document.getElementById('role_type_field').style.display = "none";
						document.getElementById('role_type_corp').style.display = "none";
					}
				}
				
				<?php if(getAccess($current_user_id) == $allowance_constant['fullAccess']) { ?>
				var you = {role:<?php echo getRole($current_user_id) ?>,
							name:"<?php echo getName() ?>", 
							min: '<?php echo getFieldEmployee('ministry') ?>', 
							title: '<?php echo getFieldEmployee('role_title') ?>', 
							projectCode: '<?php echo getFieldEmployee('staff_account') ?>', 
							setValues: <?php setUserValues($current_user_id) ?>};
				<?php }
				if (getSpouse() != -1 and getAccess(getSpouse()) == $allowance_constant['fullAccess']) { ?>
				var spouse = {role:<?php echo getRole(getSpouse());?>, 
								name:"<?php echo getName(getSpouse()) ?>", 
								min: '<?php echo getFieldEmployee('ministry', getSpouse()) ?>', 
								title: '<?php echo getFieldEmployee('role_title', getSpouse()) ?>', 
								projectCode: '<?php echo getFieldEmployee('staff_account', getSpouse()) ?>', 
								setValues: <?php setUserValues(getSpouse()) ?> };
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
					document.getElementById('name_project_code').style.display = "none";
					document.getElementById('hours').style.display = "block";
					document.getElementById('role_type_field').style.display = "none";
					document.getElementById('role_type_corp').style.display = "none";
					
					who.setValues();
					document.getElementById('user_name').innerHTML = getNameBlurb(who);
					showQuestions(who.role);
					$(".hidden").hide();
				}
				
				function getNameBlurb(who){
					var html = "Name: " + who.name + "<BR>";
					html += "Ministry/Department: " + who.min + "<BR>";
					html += "Project Code: " + who.projectCode + "<BR>";
					html += "Position Title: " + who.title + "<BR>";
					return html;
				}
				
				function select_role(){
					var role = parseInt(document.getElementById('choose_role').value);
					document.getElementById('name_project_code').style.display = "block";
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
				
				function getCheckAnswers(checks){
					var a = "";
					for(c in checks){
						if (checks[c] == "null"){
							continue;
						}
						else if(document.getElementById(checks[c]).checked){
							a += ANSWERS[document.getElementById(checks[c]).value] + "<BR>";
						}
					}
					return a;
				}
				
				function getRadioAnswers(radios){
					for(r in radios){
						if (radios[r] == "null"){
							continue;
						}
						else if(document.getElementById(radios[r]).checked){
							return ANSWERS[document.getElementById(radios[r]).value] + "<BR>";
						}
					}
					return '';
				}
				
				function calculate(){
					if ($('#hour_percentage').val() == '') {
						alert('Percentage of hours is a required field.');
						$('#hour_percentage').focus();
					} else {
						displayResult();
					}
				}
				
				function calculatePoints(role){
					<?php if(isAdmin()){ ?>
					document.getElementById('debug').innerHTML = "";
					<?php } ?>
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
					// showSelection('result');
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
						printResults(you, role);
						break;
					case SPOUSE:
						var role = spouse.role;
						document.getElementById('buttonSave').style.display = "block";
						printResults(spouse, role);
						break;
					case FREE:
						var role = parseInt(document.getElementById('choose_role').value);
						document.getElementById('buttonSave').style.display = "none";
						printResults(null, role);
						break;
					}
					var minMax = getMinMax(role, $('input[name=extra_level]:checked').val())
					var h =  get_value_float('hour_percentage');
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
				
				function printResults(who, role){
					html = "";
					if (who != null){
						html += getNameBlurb(who) + "<BR>";
					}
					else {
						html += "Name: " + document.getElementById('person_name').value + "<BR>";
						html += "Project Code: " + document.getElementById('projectCode').value + "<BR><BR>";
					}
					switch(role){
					case FIELD_INDIVIDUAL:
						html += <?php getSelectAnswers($allowance_constant['fieldIndividual']) ?>
						break;
					case FIELD_LEADER:
						html += <?php getSelectAnswers($allowance_constant['fieldLeader']) ?>
						break;
					case CORPORATE_INDIVIDUAL:
						html += <?php getSelectAnswers($allowance_constant['corporateIndividual']) ?>
						break;
					case CORPORATE_LEADER:
						html += <?php getSelectAnswers($allowance_constant['corporateLeader']) ?>
						break;
					}
					document.getElementById('printResult').innerHTML = html;
					
				
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
					document.getElementById('person_name').value = "";
					document.getElementById('projectCode').value = "";
					document.getElementById('extra-field-6').checked = false;
					document.getElementById('extra-field-7').checked = false;
					document.getElementById('extra-field-8').checked = false;
					document.getElementById('extra-corp-6').checked = false;
					document.getElementById('extra-corp-7').checked = false;
					document.getElementById('hour_percentage').value = "";
					<?php getReset() ?>
				}
				
				function download(){
					if (document.getElementById('input_effective').value == ""){
						alert("Please enter an effective date.")
					} else {
						document.getElementById('print').value = true;
						switch(chooseWay){
						case YOU:
							document.getElementById('userIs').value = 'you';
							document.getElementById('role').value = you.role;
							document.getElementById('person_name').value = you.name;
							document.getElementById('projectCode').value = you.projectCode;
							break;
						case SPOUSE:
							document.getElementById('userIs').value = 'spouse';
							document.getElementById('role').value = spouse.role;
							document.getElementById('person_name').value = spouse.name;
							document.getElementById('projectCode').value = spouse.projectCode;
							break;
						case FREE:
							document.getElementById('userIs').value = 'free';
							document.getElementById('role').value = document.getElementById('choose_role').value;
							break;
						}
						document.getElementById('minimum').value = document.getElementById('output_minimum').innerHTML;
						document.getElementById('maximum').value = document.getElementById('output_maximum').innerHTML;
						document.getElementById('minimum_month').value = document.getElementById('output_minimum_month').innerHTML;
						document.getElementById('maximum_month').value = document.getElementById('output_maximum_month').innerHTML;
						document.getElementById('preAllowance').value = document.getElementById('input_preAllowance').value;
						document.getElementById('newAllowance').value = document.getElementById('input_newAllowance').value;
						document.getElementById('preHours').value = document.getElementById('input_preHours').value;
						document.getElementById('newHours').value = document.getElementById('input_newHours').value;
						document.getElementById('mon').value = document.getElementById('input_mon').value;
						document.getElementById('tues').value = document.getElementById('input_tues').value;
						document.getElementById('wed').value = document.getElementById('input_wed').value;
						document.getElementById('thurs').value = document.getElementById('input_thurs').value;
						document.getElementById('fri').value = document.getElementById('input_fri').value;
						document.getElementById('effective').value = document.getElementById('input_effective').value;
						document.getElementById('saveUserValues_form').target = "_blank";
						saveUserValues_form.submit();
					}
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
					
					<?php if(getAccess($current_user_id) == $allowance_constant['fullAccess']) { ?>
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
				<?php if(getAccess($current_user_id) == $allowance_constant['fullAccess']) { ?>
				<input type='radio' name='whichWay' id='show_you' value='0'><label for='show_you'>Calculate for yourself</label>
				<?php }
				if (getSpouse() != -1 and getAccess(getSpouse()) == $allowance_constant['fullAccess']) { // hides the option if there is no spouse ?>
				<input type='radio' name='whichWay' id='show_spouse' value='1' ><label for='show_spouse'>Calculate for spouse</label>
				<?php } ?>
				<input type='radio' name='whichWay' id='show_anyone' value='2'><label for='show_anyone'>Calculate for anyone</label>
				<BR>
				<BR>
				<div id='user_name'></div>
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
							<div id='name_project_code'>
								Name: <input type='text' name='person_name' id='person_name'><BR>
								Project Code: <input type='text' name='projectCode' id='projectCode' maxlength='6'><BR><BR>
							</div>
							<div id='hours'>
							    <h2><?php echo getStringConstant("first_header") ?></h2>
								<strong><?php echo getStringConstant("hour_label") ?></strong>
								<input type='text' size='3' name='hour_percentage' id='hour_percentage'>%<BR><BR>
							</div>
							<div id='role_type_field'>
								<strong>Role Type</strong><BR>
								<input type='radio' name='extra_level' id='extra-field-6' value='6'><label for='extra-field-6'>Managers and Other Ministry Leaders</label><BR>
								<input type='radio' name='extra_level' id='extra-field-7' value='7'><label for='extra-field-7'>Ministry Director</label><BR>
								<input type='radio' name='extra_level' id='extra-field-8' value='8'><label for='extra-field-8'>Domain Leader</label><BR><BR>
							</div>
							<div id='role_type_corp'>
								<strong>Role Type</strong><BR>
								<input type='radio' name='extra_level' id='extra-corp-6' value='6'><label for='extra-corp-6'>Manager / Other Dept. Leader</label><BR>
								<input type='radio' name='extra_level' id='extra-corp-7' value='7'><label for='extra-corp-7'>Department Director</label><BR><BR>
							</div>
							
							<input type='hidden' name='userIs' id='userIs'>
							<input type='hidden' name='print' id='print'>
							<input type='hidden' name='minimum' id='minimum'>
							<input type='hidden' name='maximum' id='maximum'>
							<input type='hidden' name='minimum_month' id='minimum_month'>
							<input type='hidden' name='maximum_month' id='maximum_month'>
							<input type='hidden' name='preAllowance' id='preAllowance'>
							<input type='hidden' name='newAllowance' id='newAllowance'>
							<input type='hidden' name='preHours' id='preHours'>
							<input type='hidden' name='newHours' id='newHours'>
							<input type='hidden' name='mon' id='mon'>
							<input type='hidden' name='tues' id='tues'>
							<input type='hidden' name='wed' id='wed'>
							<input type='hidden' name='thurs' id='thurs'>
							<input type='hidden' name='fri' id='fri'>
							<input type='hidden' name='date' id='date' value="">
							<input type='hidden' name='effective' id='effective'>
							<input type='hidden' name='role' id='role'>
							<?php getQuestions($allowance_constant['fieldIndividual']) ?>
						</div>
					</form>
				</div>
			</div>
			<div id='section_result' style='display:none;'>
				<div id='printResult'></div>
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
				<hr>
				<strong>Change in Allowance or Hours</strong>
				<table><tr>
				<td>Previous Allowance: <input type='text' id='input_preAllowance'></td>
				<td>New Allowance: <input type='text' id='input_newAllowance'></td>
				</tr><tr>
				<td>Previous number of hours: <input type='text' style="width:40px" id='input_preHours'></td>
				<td>New Number of Hours: <input type='text' style="width:40px" id='input_newHours'></td>
				</tr><tr>
				<td colspan='2'><strong>** If schedule is less than 40 hours per week enter normal days/ hours worked</strong></td>
				</tr></table>
				<table><tr>
				<td>Monday: <input type='text' style="width:40px" id='input_mon'></td>
				<td>Tuesday: <input type='text' style="width:40px" id='input_tues'></td>
				<td>Wednesday: <input type='text' style="width:40px" id='input_wed'></td>
				<td>Thursday: <input type='text' style="width:40px" id='input_thurs'></td>
				<td>Friday: <input type='text' style="width:40px" id='input_fri'></td>
				</tr></table>
				Effective Date: <input type='text' id='input_effective'>
				<hr>
				<table class='button'><tr>
					<td class='button'><input type='button' value='Restart' onclick='reset();showSection("whichWay");'></td>
					<td class='button'><input type='button' id='buttonSave' value='Save' onclick='saveUserValues();'></td>
					<td class='button'><input type='button' value='Download/Print' onclick='download();'></td>
					<td class='button'><input type='button' value='Back' onclick='backTo("whichWay");'></td>
				</tr></table>
				<?php if(isAdmin()){ ?>
					<input type='button' value='More Info' onclick='$("#t").toggle();'>
					<div id='t' style='display:none;'>
					The selection below will only show if user is an administer.<BR><BR>
					<div id='debug'></div></div>
				<?php } ?>
			</div>
		<?php } ?>
    </div>
    </div>
    <div id="content-right" class="mobile-off"><?php get_sidebar(''); ?></div><div style='clear:both;'></div>
</div>
<!--content end-->
<!--Popup window-->
</div>
<!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>		
<?php get_footer(); ?>