<?php

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
		if ($id == $current_user_id  && (isAppAdmin('support_calculator_admin', 0) || isAppAdmin('support_calculator_admin', 1))){
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

?>