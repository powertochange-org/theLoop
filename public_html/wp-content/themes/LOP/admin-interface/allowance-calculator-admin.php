<?php

include('functions-admin.php');

/*see allowance-calculator.php for info about hardcoded numbers*/

?>
<style type='text/css'>
.border{
	border: 1px solid #808080;
	padding: 5px;
	margin: 3px;
}

.label{
	width:100%;
}

</style>
<?php

$question_types = Array('radiobutton', 'checkbox', 'dropdown', 'header', 'radiobutton_sdw');

/*To add a new question types there are several steps:\
	-in: allowance-calculator-admin.php (this file)
		-in clean_tree() 			specify how to update user data
	-in: allowance-calculator
		-in: getQuestions() 		specify how to display the type of question
		-in: getPointsEquation()	specify how to calculate the number of points of the question
		-in: getReset() 			specify how to reset the question to default value
		-in: setUserValues() 		specify how to set the user's stored data
		-in: parseUserValuesInput()	specify how to generate the question in the pdf file.
	
	there is no option for storing user's data because it is automatic sp 
	all the information is the form is stored;
	
From question types it may be possible to switch between them without user data corruption (such radiobutton and dropdown)
*/

$points_width = 5; //specify the width of the points and max_points textboxes

parseInput();

//returns a string of the checkbox to be displayed
function printCheckbox($pre, $id, $check_id, $checked, $disabled=false){
	global $allowance_constant;
	$string = "<input type ='hidden' name='".$pre."-".$id."-".$check_id."-role' value='off'>";
	$string .= "<label for='".$pre."-".$id."-".$check_id."-role'>".$allowance_constant['roleType'][$check_id].":</label>";
	$string .= "<input type='checkbox' name='".$pre."-".$id."-".$check_id."-role' id='".$pre."-".$id."-".$check_id."-role' ";
	if ($checked > 0){
		$string .= " checked";
	}
	if ($disabled){
		$string .= " disabled";
	}
	$string .= ">";
	return $string;
}

//returns a dropbox of the different question types
function printSelectType($pre, $id, $type){
	global $question_types;
	$string = "<select name= '".$pre."-".$id."-type'>";
	foreach ($question_types as $t){
		$string .= "<option value='".$t."'";
		if ($t == $type){
			$string .= " selected";
		}
		$string .= ">".$t."</option>";
	}
	$string .= "</select>\n";
	return $string;
}

//returns a string of row display the min and max of a role
function printMinMax($role){
	global $allowance_constant;
	return "<tr><td>".$allowance_constant['roleType'][$role]."</td><td><input type='text' name='m-".$role."-0-min' value='".getConstant("role_".$role."_0_min")."'></td><td><input type='text' name='m-".$role."-0-max' value='".getConstant("role_".$role."_0_max")."'></td></tr>\n";
}

//this is for displaying and hiden the table
function getElements($role, $all=0){
	global $wpdb;
	echo "new Array(";
	$sql = "SELECT `id` FROM `allowance_question` WHERE ".$all." OR role & (1 << ".$role.")";
	$results = $wpdb->get_results($sql);
	foreach ($results as $result){
		echo "'quest_".$result->id."', ";
	}
	echo "'null');\n";
}

function printQuestionsDropdown($show_all=0){
	global $wpdb;
	$sql = "SELECT * FROM `allowance_question` WHERE `pull_data` = 0 or ".$show_all." ORDER BY  `order` ASC";
	$results = $wpdb->get_results($sql);
	$first = true;
	foreach ($results as $result){
		if ($first){
			$first = false;
			echo "<option value='".$result->id."' selected >Question".$result->id."</option>";
		}
		else{
			echo "<option value='".$result->id."'>Question".$result->id."</option>";
		}
	}
}

function printAnswersDropdown($quest=null, $show_all=0){
	global $wpdb;
	if ($quest == null){
		$sql = "SELECT `id` FROM `allowance_question` WHERE `pull_data` = 0 or ".$show_all." ORDER BY  `order` ASC";
		$results = $wpdb->get_results($sql);
		$quest = $results[0]->id;
	}
	$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$quest;
	$sub_result = $wpdb->get_results($sql);
	$sub_result = $sub_result[0];
	$current = $sub_result->first_sub;
	while($current != '0' && $current !=  NULL){
		$sql = "SELECT * FROM `allowance_answer` WHERE `id`=".$current;
		$sub_result = $wpdb->get_results($sql);
		$sub_result = $sub_result[0];
		echo "<option value='".$sub_result->id."'>Answer ".$sub_result->id."</option>";
		$current = $sub_result->next;
	}
}

function printQuestions(){
	global $allowance_constant, $wpdb, $points_width;
	echo "<table>";
	echo "<caption>Questions</caption>";
			$sql = "SELECT * FROM `allowance_question` ORDER BY  `order` ASC";
			$results = $wpdb->get_results($sql);
			foreach ($results as $result){
				echo "<tr class='quest_".$result->id."'><th class='title' colspan='3'>Question ID:".$result->id."</th></tr>\n";
				if ($result->pull_data){
					echo "<tr class='quest_".$result->id."'><td colspan='3'>This question pulls data from the database.  Deleteing the question and change the numbers of answers and changing the roles for the question have been disable.</td></tr>\n";
				}
				echo "<tr class='quest_".$result->id."'><th colspan='3'><input type='text 'class='label' name='q-".$result->id."-label' value='".$result->label."'></th></tr>\n";
				echo "<tr class='quest_".$result->id."'><td colspan='3'>".
					printCheckbox("q", $result->id,$allowance_constant['fieldIndividual'], intval($result->role) & (1 << $allowance_constant['fieldIndividual']), $result->pull_data).
					printCheckbox("q", $result->id,$allowance_constant['fieldLeader'], intval($result->role) & (1 << $allowance_constant['fieldLeader']), $result->pull_data).
					printCheckbox("q", $result->id,$allowance_constant['corporateIndividual'], intval($result->role) & (1 << $allowance_constant['corporateIndividual']), $result->pull_data).
					printCheckbox("q", $result->id,$allowance_constant['corporateLeader'], intval($result->role) & (1 << $allowance_constant['corporateLeader']), $result->pull_data)."<BR>".
					"Max Points: <input size='".$points_width."' type='text' name='q-".$result->id."-max_points' value='".$result->max_points."'>".
					"Type: ".printSelectType("q",$result->id, $result->type).
					"</td></tr>\n";
				$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$result->id;
				$sub_result = $wpdb->get_results($sql);
				$sub_result = $sub_result[0];
				$current = $sub_result->first_sub;
				echo "<tr class='quest_".$result->id." answer'><th>ID</th><th>Answer</th><th>Points</th>";
				while($current != '0' && $current !=  NULL){
					$sql = "SELECT * FROM `allowance_answer` WHERE `id`=".$current;
					$sub_result = $wpdb->get_results($sql);
					$sub_result = $sub_result[0];
					echo "<tr class='quest_".$result->id." answer'><td>".$sub_result->id."</td><td><input type='text' class='label' name='a-".$sub_result->id."-label' value='".$sub_result->label."'></td>
						<td><input size='".$points_width."' type='text' name='a-".$sub_result->id."-points' value='".$sub_result->points."'></td></tr>\n";
					$current = $sub_result->next;
				}
			}
		?>
	<tr><td colspan='3'><input type="submit" value="Save"></td></tr>
	</table><?php
}

function resetRoles(){
	global $wpdb;
	$sql = "UPDATE `allowance_question` SET `role`=0";
	$wpdb->get_results($sql);
}

function changeRole($quest, $role, $change){
	global $wpdb;
	$quest = intval($quest);
	$role = intval($role);
	if ($change == 'on'){
		$sql = "UPDATE `allowance_question` SET `role`= `role` | (1 << ".$role.") WHERE id=".$quest;
	} else{
		$sql = "UPDATE `allowance_question` SET `role`= `role` & (15 ^ (1 << ".$role.")) WHERE id=".$quest;
	}
	$wpdb->get_results($sql);
}

function isPullData($quest){
	global $wpdb;
	$sql = "SELECT `pull_data` FROM `allowance_question` WHERE `id`=".$quest;
	$result = $wpdb->get_results($sql);
	return $result[0]->pull_data;
}

//handles the post from the admin interface
function parseInput(){
	// var_dump($_POST); // todo
	if (isAdmin()){
		global $wpdb;
		$array_key = array_keys($_POST);
		for ($i = 0; $i < count($_POST); $i ++){
			$fullKey = $array_key[$i];
			$key = explode("-",$fullKey);
			switch($key[0]){
				case "a": //answer
					$wpdb->update( 
						'allowance_answer', 
						array( $key[2] => $_POST[$fullKey]), 
						array( 'id' => $key[1] ), 
						array( '%s'), 
						array( '%s' ) 
					);
					
					
					break;
				case "q": //question
					switch($key[count($key) - 1]){
						case 'label':
						case 'max_points':
						case 'type':
							$wpdb->update( 
								'allowance_question', 
								array( $key[2] => $_POST[$fullKey]), 
								array( 'id' => $key[1] ), 
								array( '%s'), 
								array( '%s' ) 
							);
							break;
						case 'role':
							if (isPullData($key[1])){
								break;
							}
							changeRole($key[1], $key[2], $_POST[$fullKey]);
							break;
					}
					break;
				case "m": //minMax range
					setConstant("role_".$key[1]."_".$key[2]."_".$key[3], $_POST[$fullKey]);
					break;
				case "e": //edit sturcture of questions and answers todo sp
					switch($key[1]){
						case "add_answer":
							if (isPullData($_POST['e-add_answer-question'])){
								return;
							}
							add_answer($_POST['e-add_answer-question'], $_POST['e-add_answer-label'], $_POST['e-add_answer-points'], $_POST['e-add_answer-answer']);
							return;
						case "remove":
							if (isPullData($_POST['e-remove-question'])){
								return;
							}
							if($_POST['e-remove-answer'] == 0){
								remove_question($_POST['e-remove-question']);
							}
							else{
								remove_answer($_POST['e-remove-question'], $_POST['e-remove-answer']);
							}
							return;
						case "move":
							if($_POST['e-move-from-answer'] == 0){
								move_question($_POST['e-move-from-question'], $_POST['e-move-to-question']);
							}
							else{
								move_answer($_POST['e-move-from-question'], $_POST['e-move-from-answer'], $_POST['e-move-to-answer']);
							}
							return;
						case "add_question":
							$answers = array();
							for ($sub_i = 0; $sub_i < count($_POST); $sub_i ++){
								$sub_fullKey = $array_key[$sub_i];
								$sub_key = explode("-",$sub_fullKey);
								if ($sub_key[2] == "answer"){
									$answers[$sub_key[4]][$sub_key[3]] = $_POST[$sub_fullKey];
								}
							}
							$role = 0;
							for ($sub_i = 0; $sub_i < count($_POST); $sub_i ++){
								$sub_fullKey = $array_key[$sub_i];
								$sub_key = explode("-",$sub_fullKey);
								echo "{".$_POST[$sub_fullKey]."}";
								if ($sub_key[3] == "role" and $_POST[$sub_fullKey] == 'on'){
									echo intval($sub_key[2])."-".$sub_key[2];
									$role |= 1 << intval($sub_key[2]);
								}
								echo $role;
							}
							add_question($_POST['e-add_question-label'], $_POST['e-add_question-max_points'], $_POST['e-add_question-type'], $role, $answers, $_POST['e-add_question-question']);
							return;
					}
					break;
				case b: //blurb messages
					setStringConstant("blurb_".$key[1], $_POST[$fullKey]);
					break;
				case h: //hour question
					setStringConstant("hour_label", $_POST[$fullKey]);
					break;
				case f: //first header (before prefilled questions);
					setStringConstant("first_header",  $_POST[$fullKey]);
					break;
			}
		}
	}
}

//modify stucture function todo sp test todo move to last
//test
// add answer (first, middle, last)   ... good!
// remove answer (first, middle/last) ... good!
// move answer (middle to first)      ....good!
// add question (in parts)            ....good!
// move question (first, middle, last)....good!
// remove question (middle)           ....good!
// 
//test roles for adding questions todo ...good!

function get_new_order($quest_id, $after){
	global $wpdb;
	$quest_id = intval($quest_id);
	$after = intval($after);
	if($after == 0){ //if first answer
		$sql = "SELECT `order` FROM `allowance_question` WHERE id!=".$quest_id." ORDER BY  `order` ASC";
		$result = $wpdb->get_results($sql);
		if (count($result) == 0){ //no questions
			$new_order = 0;
		}
		else {
			$new_order = floatval($result[0]->order) - 1;
		}
	}
	else{
		$sql = "SELECT `order` FROM `allowance_question` WHERE id=".$after;
		$result = $wpdb->get_results($sql);
		$min_order = floatval($result[0]->order);
		$sql = "SELECT `order` FROM `allowance_question` WHERE `order` >".$min_order." AND id!=".$quest_id." ORDER BY  `order` ASC";
		$result = $wpdb->get_results($sql);
		if (count($result) == 0){
			$new_order = $min_order + 1;
		}
		else {
			$max_order = floatval($result[0]->order);
			$new_order = ($min_order + $max_order) / 2;
		}
	}
	return $new_order;
}

function add_question($label, $max_points, $type, $role, $answers, $after){
	global $wpdb;
	$next = 0;
	$answers = array_reverse($answers);
	foreach($answers as $answer){
		$wpdb->insert( 
			'allowance_answer', 
			array( 
				'id' => 'NULL', 
				'label' => $answer['label'],
				'next' => $next,
				'points' => $answer['points']
			), 
			array( 
				'%d', 
				'%s',
				'%s',
				'%s'
			) 
		);
		$next = $wpdb->insert_id;
	}
	$wpdb->insert( 
			'allowance_question', 
			array( 
				'id' => 'NULL', 
				'label' => $label,
				'type' => $type,
				'order' => get_new_order(0, $after),
				'max_points' => $max_points,
				'first_sub' => $next,
				'role' => $role,
				'pull_data' => '0'
			), 
			array( 
				'%d', 
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s'
			) 
		);
}

function remove_question($quest_id){
	global $wpdb;
	$quest_id = intval($quest_id);
	//remove the answer (otherwise they will be orphans :( )
	$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$quest_id;
	$sub_result = $wpdb->get_results($sql);
	$sub_result = $sub_result[0];
	$current = $sub_result->first_sub;
	while($current != '0' && $current !=  NULL){
		$sql = "SELECT `next` FROM `allowance_answer` WHERE `id`=".$current;
		$sub_result = $wpdb->get_results($sql);
		$sub_result = $sub_result[0];
		
		$sql = "DELETE FROM `allowance_answer` WHERE `id`=".$current;
		$wpdb->get_results($sql);
		$current = $sub_result->next;
	}
	$sql = "DELETE FROM `allowance_question` WHERE `id`=".$quest_id;
	$wpdb->get_results($sql);
	
	//delete user data as well;
	$users =  get_users( array('meta_key' => 'allowance_calculator_user_values'));
	foreach ($users as $user){
		$id = $user->id;
		$result = get_user_meta($id, 'allowance_calculator_user_values', true);
		if ($result == ""){
			continue;
		}
		$data = "";
		foreach (explode(',', $result) as $value){
			if ($value == "Null" || explode(":", $value) == "userIsYou"){
				continue;
			}
			if ($quest_id == explode("-", explode(":",$value)[0])[1]){
				continue;
			}
			$data .= $value.",";
		}
		$data .= "Null";
		update_user_meta($id, 'allowance_calculator_user_values', $data);
	}
}

function move_question($quest_id , $after){
	global $wpdb;
	$quest_id = intval($quest_id);
	$after = intval($after);
	$sql = "UPDATE `allowance_question` SET `order`=".get_new_order($quest_id, $after)." WHERE `id`=".$quest_id;
	$wpdb->get_results($sql);
}

function add_answer($quest_id, $label, $points, $after, $id='NULL'){
	global $wpdb;
	$quest_id = intval($quest_id);
	$after = intval($after);
	if ($after == 0){ //first answer to insert
		$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$quest_id;
		$result = $wpdb->get_results($sql);
		$next = $result[0]->first_sub;
		$wpdb->insert( 
			'allowance_answer', 
			array( 
				'id' => $id, 
				'label' => $label,
				'next' => $next,
				'points' => $points
			), 
			array( 
				'%d', 
				'%s',
				'%s',
				'%s'
			) 
		);
		$id = $wpdb->insert_id;
		$sql = "UPDATE `allowance_question` SET `first_sub`=".$id." WHERE `id`=".$quest_id;
		$wpdb->get_results($sql);
		return;
	}
	$sql = "SELECT `next` FROM `allowance_answer` WHERE `id`=".$after;
	$result = $wpdb->get_results($sql);
	$next = $result[0]->next;
	if ($next == 0){ //if last answer
		$wpdb->insert( 
			'allowance_answer', 
			array( 
				'id' => $id, 
				'label' => $label,
				'next' => '0',
				'points' => $points
			), 
			array( 
				'%d', 
				'%s',
				'%s',
				'%s'
			) 
		);
		$id = $wpdb->insert_id;
		$sql = "UPDATE `allowance_answer` SET `next`=".$id." WHERE `id`=".$after;
		
		$wpdb->get_results($sql);
		return;
	}
	//inserting in the middle
	$sql = "INSERT INTO `allowance_answer`(`id`, `label`, `next`, `points`) VALUES (".$id.",'".$label."',".$next.",".$points.")";
	$wpdb->insert( 
			'allowance_answer', 
			array( 
				'id' => $id, 
				'label' => $label,
				'next' => $next,
				'points' => $points
			), 
			array( 
				'%d', 
				'%s',
				'%s',
				'%s'
			) 
		);
	$id = $wpdb->insert_id;
	$sql = "UPDATE `allowance_answer` SET `next`=".$id." WHERE `id`=".$after;
	$wpdb->get_results($sql);
}

function remove_answer($quest_id, $answer_id){
	global $wpdb;
	$quest_id = intval($quest_id);
	$answer_id = intval($answer_id);
	$sql = "SELECT `next` FROM `allowance_answer` WHERE `id`=".$answer_id;
	$result = $wpdb->get_results($sql);
	$next = $result[0]->next;
	$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$quest_id;
	$result = $wpdb->get_results($sql);
	
	if ($answer_id == $result[0]->first_sub){ //if ansewer is first
		$sql = "UPDATE `allowance_question` SET `first_sub`=".$next." WHERE `id`=".$quest_id;
		$wpdb->get_results($sql);
		$sql = "DELETE FROM `allowance_answer` WHERE `id`=".$answer_id;
		$wpdb->get_results($sql);
		return;
	}
	//get previous
	$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$quest_id;
	$sub_result = $wpdb->get_results($sql);
	$sub_result = $sub_result[0];
	$current = $sub_result->first_sub;
	while($current != $answer_id){
		$sql = "SELECT `id`, `next` FROM `allowance_answer` WHERE `id`=".$current;
		$sub_result = $wpdb->get_results($sql);
		$sub_result = $sub_result[0];
		$current = $sub_result->next;
	}
	$previous = $sub_result->id;
	$sql = "UPDATE `allowance_answer` SET `next`=".$next." WHERE `id`=".$previous;
	$wpdb->get_results($sql);
	$sql = "DELETE FROM `allowance_answer` WHERE `id`=".$answer_id;
	$wpdb->get_results($sql);
	
	
	//delete user data as well
	$users =  get_users( array('meta_key' => 'allowance_calculator_user_values'));
	foreach ($users as $user){
		$id = $user->id;
		$result = get_user_meta($id, 'allowance_calculator_user_values', true);
		if ($result == ""){
			continue;
		}
		$data = "";
		foreach (explode(',', $result) as $value){
			if ($value == "Null" || explode(":", $value) == "userIsYou"){
				continue;
			}
			if ($quest_id == explode("-", explode(":",$value)[0])[1] and $answer_id == explode(":", $value)){
				continue;
			}
			$data .= $value.",";
		}
		$data .= "Null";
		update_user_meta($id, 'allowance_calculator_user_values', $data);
	}
}

function move_answer($quest_id, $answer_id, $after){
	global $wpdb;
	$quest_id = intval($quest_id);
	$answer_id = intval($answer_id);
	$after = intval($after);
	$sql = "SELECT * FROM `allowance_answer` WHERE id=".$answer_id;
	$result = $wpdb->get_results($sql);
	remove_answer($quest_id, $answer_id);
	add_answer($quest_id, $result[0]->label, $result[0]->points, $after, $answer_id);
}

printAdmin();
function printAdmin(){
	global $allowance_constant;
	if (isAdmin()){	
		printAdminChangeInterface();?>
		<form action="" method="post">
			<table>
			<tr><th></th><th>Recommended<BR>Minimum</th><th>Absolute<BR>Maximum</th></tr>
			<?php echo printMinMax($allowance_constant['fieldIndividual']); ?>  
			<tr><td><?php echo $allowance_constant['roleType'][$allowance_constant['fieldLeader']] ?><BR>Ministry Leader (all other types)</td><td><input type='text' name='m-<?php echo $allowance_constant['fieldLeader'] ?>-7-min' value='<?php echo getConstant("role_".$allowance_constant['fieldLeader']."_7_min")?>'></td><td><input type='text' name='m-<?php echo $allowance_constant['fieldLeader'] ?>-7-max' value='<?php echo getConstant("role_".$allowance_constant['fieldLeader']."_7_max")?>'></tr>
			<tr><td><?php echo $allowance_constant['roleType'][$allowance_constant['fieldLeader']] ?><BR>Ministry Director</td><td><input type='text' name='m-<?php echo $allowance_constant['fieldLeader'] ?>-8-min' value='<?php echo getConstant("role_".$allowance_constant['fieldLeader']."_8_min")?>'></td><td><input type='text' name='m-<?php echo $allowance_constant['fieldLeader'] ?>-8-max' value='<?php echo getConstant("role_".$allowance_constant['fieldLeader']."_8_max")?>'></tr>
			<tr><td><?php echo $allowance_constant['roleType'][$allowance_constant['fieldLeader']] ?><BR>Domain Leader</td><td><input type='text' name='m-<?php echo $allowance_constant['fieldLeader'] ?>-9-min' value='<?php echo getConstant("role_".$allowance_constant['fieldLeader']."_9_min")?>'></td><td><input type='text' name='m-<?php echo $allowance_constant['fieldLeader'] ?>-9-max' value='<?php echo getConstant("role_".$allowance_constant['fieldLeader']."_9_max")?>'></tr>
			<?php echo printMinMax($allowance_constant['corporateIndividual']); ?>
			<tr><td><?php echo $allowance_constant['roleType'][$allowance_constant['corporateLeader']] ?><BR>Manager / Other Dept. Leader</td><td><input type='text' name='m-<?php echo $allowance_constant['corporateLeader'] ?>-7-min' value='<?php echo getConstant("role_".$allowance_constant['corporateLeader']."_7_min")?>'></td><td><input type='text' name='m-<?php echo $allowance_constant['corporateLeader'] ?>-7-max' value='<?php echo getConstant("role_".$allowance_constant['corporateLeader']."_7_max")?>'></tr>
			<tr><td><?php echo $allowance_constant['roleType'][$allowance_constant['corporateLeader']] ?><BR>Department Director</td><td><input type='text' name='m-<?php echo $allowance_constant['corporateLeader'] ?>-8-min' value='<?php echo getConstant("role_".$allowance_constant['corporateLeader']."_8_min")?>'></td><td><input type='text' name='m-<?php echo $allowance_constant['corporateLeader'] ?>-8-max' value='<?php echo getConstant("role_".$allowance_constant['corporateLeader']."_8_max")?>'></tr>
			
			<tr><td colspan='3'><input type="submit" value="Save"></td></tr>
			</table>
		</form>
		<form action="" method="post">
			Start Blurb
			<textarea name="b-0" id="b-0" class='label' value='' rows="6" cols="35" maxlength="1000"><?php echo getStringConstant("blurb_0") ?></textarea><BR>
			Filling out Blurb
			<textarea name="b-1" id="b-1" class='label' value='' rows="6" cols="35" maxlength="1000"><?php echo getStringConstant("blurb_1") ?></textarea><BR>
			Result Blurb
			<textarea name="b-2" id="b-2" class='label' value='' rows="6" cols="35" maxlength="1000"><?php echo getStringConstant("blurb_2") ?></textarea><BR>
			<input type="submit" value="Save">
		</form>
		<BR>
		<form action="" method="post">
			First Header:
			<input type='text' name="f-head" class='label' value='<?php echo getStringConstant("first_header") ?>' >
			<input type="submit" value="Save">
		</form>
		<BR>
		<form action="" method="post">
			Number of hours question label:
			<input type='text' name="h-label" class='label' value='<?php echo getStringConstant("hour_label") ?>' >
			<input type="submit" value="Save">
		</form>
		<BR>
		View: 
		<select id="chooseView" onchange="view_quest();">
			<option value="-1">All</option>
			<option value="<?php echo $allowance_constant['fieldIndividual'] ?>"><?php echo $allowance_constant['roleType'][$allowance_constant['fieldIndividual']] ?></option>
			<option value="<?php echo $allowance_constant['fieldLeader'] ?>"><?php echo $allowance_constant['roleType'][$allowance_constant['fieldLeader']] ?></option>
			<option value="<?php echo $allowance_constant['corporateIndividual'] ?>"><?php echo $allowance_constant['roleType'][$allowance_constant['corporateIndividual']] ?></option>
			<option value="<?php echo $allowance_constant['corporateLeader'] ?>"><?php echo $allowance_constant['roleType'][$allowance_constant['corporateLeader']] ?></option>
		</select>
		<input type='button' value='Toggle Answers' onclick='toggleAnswers();' >
		<form action="" method="post">
			<?php
			printQuestions();
			?>
		</form>
			<div class='border'><form action="" method="post"><form action="" method="post">
				<strong>Remove Question:</strong>
					<select name='e-remove-question' id='e-remove-question' onchange='resfreshAnswers("e-remove-question", "e-remove-answer", "Entire Question");'>
						<?php printQuestionsDropdown(); ?>
					</select>
					Answer:
					<select name='e-remove-answer' id='e-remove-answer'>
						<option value='0'>Entire Question</option>
						<?php printAnswersDropdown(); ?>
					</select>
					<input type='submit' value='Remove' /><BR>
			</form></div>
			<div class='border'><form action="" method="post"><form action="" method="post">
				<strong>Move Question:</strong>
					<select name='e-move-from-question' id='e-move-from-question' onchange='resfreshAnswers("e-move-from-question", "e-move-from-answer", "Entire Question");resfreshAnswers("e-move-from-question", "e-move-to-answer", "First Answer");'>
						<?php printQuestionsDropdown(); ?>
					</select>
					Answer:
					<select name='e-move-from-answer' id='e-move-from-answer' onchange='restrictTo();'>
						<option value='0'>Entire Question</option>
						<?php printAnswersDropdown(); ?>
					</select><BR>
					After
					<select name='e-move-to-question' id='e-move-to-question'>
						<option value='0'>First Question</option>";
						<?php printQuestionsDropdown(1); ?>
					</select>
					Answer:
					<select name='e-move-to-answer' id='e-move-to-answer' disabled='true'>
						<option value='0'>First Answer</option>
						<?php printAnswersDropdown(null, 1); ?>
					</select>
					<input type='submit' value='Move' /><BR>
			</form></div>
			<div class='border'><form action="" method="post"><form action="" method="post">
				<strong>Add Answer:</strong>
					<select name='e-add_answer-question' id='e-add_answer-question' onchange='resfreshAnswers("e-add_answer-question", "e-add_answer-answer", "First Answer");'>
						<?php printQuestionsDropdown(); ?>
					</select>
					After Answer
					<select name='e-add_answer-answer' id='e-add_answer-answer'>
						<option value='0'>First Answer</option>
						<?php printAnswersDropdown(); ?>
					</select><BR>
					Label
					<input type='text' name='e-add_answer-label'>
					Points
					<input size='<?php global $points_width; echo $points_width ?>' type='text' name='e-add_answer-points'>
					<input type='submit' value='Add Answer' /><BR>
			</form></div>
			<div class='border'><form action="" method="post">
				<strong>Add Question</strong>
					After
					<select name='e-add_question-question' id='e-add_question-question' onchange='resfreshAnswers("e-add_question-question", "e-add_question-answer", "First Answer");'>
						<option value='0'>First Question</option>
						<?php printQuestionsDropdown(1); ?>
					</select>
					Max Points:<input size='<?php global $points_width; echo $points_width ?>' type='text' name='e-add_question-max_points'>
					Type:<?php echo printSelectType("e", "add_question", '') ?><BR>
					<input type='text' class='label' name='e-add_question-label'>
					<table>
						<td><?php echo printCheckbox("e", "add_question", $allowance_constant['fieldIndividual'], false); ?></td>
						<td><?php echo printCheckbox("e", "add_question", $allowance_constant['fieldLeader'], false); ?></td>
						<td><?php echo printCheckbox("e", "add_question", $allowance_constant['corporateIndividual'], false); ?></td>
						<td><?php echo printCheckbox("e", "add_question", $allowance_constant['corporateLeader'], false); ?></td>
					</table>
					<table id='new_answers'>
					<tr><th>Answer</th><th>Points</th><th><input type='button' value='Add another answer' onclick='add_row()'></th>
					</table>
					<input type='submit' value='Add Question'>
			</form></div>
		
		</div>
		<script type="text/javascript">
			var next_id = 0;
			function get_unique_id(){
				return next_id ++;
			}
			function resfreshAnswers(quest_id, answer_id, firstEntry){
				var element = document.getElementById(answer_id);
				console.log(document.getElementById(quest_id).value);
				switch(parseInt(document.getElementById(quest_id).value)){
					<?php
						global $wpdb;
						$sql = "SELECT * FROM `allowance_question` ORDER BY  `order` ASC";
						$results = $wpdb->get_results($sql);
						foreach ($results as $result){
							echo "\ncase ".$result->id.":\n\t element.innerHTML=\"<option value='0'>\"+firstEntry+\"</option>";
							printAnswersDropdown(intval($result->id));
							echo "\";\n";
							echo "\tbreak;\n";
						}
					?>
				}
			}
			
			function add_row(){
				var id = get_unique_id();
				var table = document.getElementById('new_answers');
	 
				var rowCount = table.rows.length;
				var row = table.insertRow(rowCount);
				row.id = "row_" + id;
	 
				var cell1 = row.insertCell(0);
				var element = document.createElement("input");
				element.type = "text";
				element.className = "label";
				element.name="e-add_question-answer-label-" + id;
				cell1.appendChild(element);
	 
				var cell2 = row.insertCell(1);
				var element = document.createElement("input");
				element.type = "text";
				element.name="e-add_question-answer-points-" + id;
				element.size=<?php echo $points_width ?>;
				cell2.appendChild(element);
	 
				var cell3 = row.insertCell(2);
				var element = document.createElement("input");
				element.type = "button";
				element.value = "Remove";
				element.onclick = function(){delete_row(id)};
				element.name="e-add_question-answer-button-" + id;
				cell3.appendChild(element);
			}
			
			function delete_row(id){
				var table = document.getElementById('new_answers');
				var rowCount = table.rows.length;
				for(var i=0; i<rowCount; i++) {
					var row = table.rows[i];
					if (row.id == "row_" + id){
						table.deleteRow(i);
						return;
					}
				}
			}
			
			function restrictTo(){
				var answer_from_value = document.getElementById('e-move-from-answer').value;
				var answer_to = document.getElementById('e-move-to-answer');
				var quest_to = document.getElementById('e-move-to-question');
				console.log(answer_from_value);
				if (answer_from_value == '0'){
					answer_to.disabled = true;
					answer_to.value = answer_from_value;
					quest_to.disabled = false;
				}
				else {
					answer_to.disabled = false;
					quest_to.value = document.getElementById('e-move-from-question').value;
					quest_to.disabled = true;
				}
			}
			
			var show_answers = true;
			
			function toggleAnswers(){
				show_answers = !show_answers
				view_quest();
			}
			
			function view_quest(){
				var view_role = document.getElementById('chooseView').value;
				var elements = <?php getElements(-1, true); ?>
				for (i in elements){
					if (elements[i] == "null"){
						continue;
					}
					$("." +elements[i]).hide();
				}
				switch (parseInt(view_role)) {
					case FIELD_INDIVIDUAL:
						elements = <?php getElements($allowance_constant['fieldIndividual']); ?>
						break;
					case FIELD_LEADER:
						elements = <?php getElements($allowance_constant['fieldLeader']); ?>
						break;
					case CORPORATE_INDIVIDUAL:
						elements = <?php getElements($allowance_constant['corporateIndividual']); ?>
						break;
					case CORPORATE_LEADER:
						elements = <?php getElements($allowance_constant['corporateLeader']); ?>
						break;
				}
				for (i in elements){
					if (elements[i] == "null"){
						continue;
					}
					$("."+ elements[i]).show();
				}
				if (!show_answers){
					$('.answer').hide();
				}
			}
		</script>
		<?php
	}
}

/*this function is to orgainzed the allowance_question and allowance_answer tables
after adding, moving and removing questions and answers their ids maybe all over the place;
this function re-ids the tables so that in the allowance_question table the question with the least order value has the smallest id
and the next question in order would have the next smallesst id and so on.  It is similar with the allowance_answer table

since the ids of the question and answer may change, the hardcoded values in allowance-calculator.php may need to be changed

the function creates two new table and appends "_old" to the old tables.  The two tables are back up incase something goes wrong
	if the old table are there then they need to be dropped before running function
the order and structure of the question and answers should be preversed

the userdata is also converted but it is not backed up
*/
function clean_tree(){
	global  $wpdb;
	$questions = array();
	$answers = array();
	$sql = "SELECT * FROM `allowance_question` ORDER BY  `order` ASC";
	$results = $wpdb->get_results($sql);
	$a_key = array(0 => 0);
	$q_key = array();
	foreach ($results as $result){
		$sql = "SELECT `first_sub` FROM `allowance_question` WHERE allowance_question.id=".$result->id;
		$sub_result = $wpdb->get_results($sql);
		$sub_result = $sub_result[0];
		$current = $sub_result->first_sub;
		array_push($questions, $result);
		$q_key[$result->id] = count($questions);
		while($current != '0' && $current !=  NULL){
			$sql = "SELECT * FROM `allowance_answer` WHERE `id`=".$current;
			$sub_result = $wpdb->get_results($sql);
			$sub_result = $sub_result[0];
			array_push($answers, $sub_result);
			$a_key[$sub_result->id] = count($answers);
			$current = $sub_result->next;
		}
	}
	//droptableish
	$sql = "RENAME TABLE allowance_question TO allowance_question_old"; 
	$wpdb->get_results($sql);
	$sql = "CREATE TABLE allowance_question LIKE allowance_question_old"; 
	$wpdb->get_results($sql);
	$sql = "RENAME TABLE allowance_answer TO allowance_answer_old"; 
	$wpdb->get_results($sql);
	$sql = "CREATE TABLE allowance_answer LIKE allowance_answer_old"; 
	$wpdb->get_results($sql);
	
	for ($i = 0; $i < count($questions); $i ++){
		$sql = "INSERT INTO `allowance_question`(`id`, `label`, `type`, `order`, `max_points`, `first_sub`, `role` , `pull_data`) VALUES (".($i + 1).", '".$questions[$i]->label."', '".$questions[$i]->type."', ".$i.", ".$questions[$i]->max_points.", ".$a_key[intval($questions[$i]->first_sub)].", ".$questions[$i]->role.", ".$questions[$i]->pull_data.")";
		$wpdb->get_results($sql);
	}
	for ($i = 0; $i < count($answers); $i ++){
		$sql = "INSERT INTO `allowance_answer`(`id`, `label`, `next`, `points`) VALUES (".($i + 1).",'".$answers[$i]->label."',".$a_key[intval($answers[$i]->next)].",".$answers[$i]->points.")"; 
		$wpdb->get_results($sql);
	}
	
	//updates userdata as well
	$users =  get_users( array('meta_key' => 'allowance_calculator_user_values'));
	foreach ($users as $user){
		$id = $user->id;
		$result = get_user_meta($id, 'allowance_calculator_user_values', true);
		if ($result == ""){
			continue;
		}
		$data = "";
		foreach (explode(',', $result) as $value){
			if ($value == "Null" || explode(":", $value) == "userIsYou"){
				continue;
			}
			$quest_id = explode("-", explode(":",$value)[0])[1];
			$sql = "SELECT `type` FROM `allowance_question` WHERE `id` =".$q_key[intval($quest_id)];
			$results = $wpdb->get_results($sql);
			switch ($results[0]->type){
				case 'dropdown': //same for both
				case 'radiobutton':
				case 'radiobutton_sdw':
					$data .= "form-".$q_key[intval($quest_id)].":".$a_key[intval(explode(":", $value)[1])].",";
					break;
				case 'checkbox':
					$data .= "form-".$q_key[intval($quest_id)]."-".$a_key[intval(explode(":", $value)[1])].":".$a_key[intval(explode(":", $value)[1])].",";
					break;
				case 'header': //this does not or rather should have answer to be update (it is just a label);
					break;
				}
		}
		$data .= "Null";
		update_user_meta($id, 'allowance_calculator_user_values', $data);
	}
}
?>