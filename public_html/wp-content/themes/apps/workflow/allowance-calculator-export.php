<?php
/*
*Used to adapt the Allowance Calculator form to be used with workflow forms.
*Compiles the data as HTML text and passes it to the workflow form 15 to use.
*
* author: gerald.becker
*
*/


//require('/wp-content/themes/apps/functions/functions.php');

echo '<div id="screen-blackout" style="display:initial;">
	<div style="width: 500px;margin-top: 200px;margin-left: auto; margin-right: auto;
    border: 3px solid black;background-color: rgba(220, 220, 220, 1);text-align: center;
    font-size:25px;">Creating a submission. Please wait. </div></div>';

function getStringConstant($field, $e=null){
	global $wpdb;
	$sql = "SELECT `value` FROM `string_store` WHERE `key` = '". $field. "'";
	$result = $wpdb->get_row($sql)->value;
	if ($result == ""){
		return null;
	}
	$result = stripcslashes ($result);
	if ($e== null){
		return $result;
	}
	return str_replace ($e , "\\$e" ,$result);
	
}	

//get a field from the employee table of the current user
function getFieldEmployee($field, $id=null){
	global $current_user_id, $wpdb;
	if ($id == null){
		$id = $current_user_id;
	}
	$sql = "SELECT `".$field."` FROM `employee` JOIN wp_users ON employee.user_login = wp_users.user_login WHERE wp_users.id = ". $id;
	$result = $wpdb->get_row($sql)->$field;
	return $result;
}

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





function parseUserValuesInput(){
	global $current_user_id, $wpdb, $allowance_constant;
	
	if (isset($_POST['print']) and $_POST['print'] == 'true'){
		$prevSub = '';
		if(isset($_GET['sbid'])) {
			$prevSub = '&sbid='.$_GET['sbid'];
		}
		$completemessage = '<form action="?page=workflowentry&wfid=133'.$prevSub.'" method="post" id="allowanceform">'; //Allowance test #1 = 15 #2 = 38 #3 = 43
		//echo '<form action="?page=workflowentry&wfid=15" method="post" id="allowanceform">';
		$response = '<div style="font-family:Arial;">';
		
		$response .= '<h2>Allowance Calculator</h2>';
		//$response .= 'Effective Date: '.(($_POST['effective'] == "") ? '_____________' : $_POST['effective']);
		
		$response .= '<br><br>';
		
		$response .= 'Name: '.$_POST['person_name'];
		$response .= '<br>';
		switch($_POST['userIs']){
		case 'you':
			$response .= 'Ministry/Department: '.getFieldEmployee('ministry');
			$response .= '<br>';
			$response .= 'Position Title: '.getFieldEmployee('role_title');
			$response .= '<br>';
			break;
		case 'spouse':
			$response .= 'Ministry/Department: '.getFieldEmployee('ministry', getSpouse());
			$response .= '<br>';
			$response .= 'Position Title: '.getFieldEmployee('role_title', getSpouse());
			$response .= '<br>';
			break;
		case 'free':
			break;
		}
		$response .= 'Project Code: '.$_POST['projectCode'];
		$response .= '<br>';
		switch($_POST['role']){
		case $allowance_constant['fieldIndividual']:
		case $allowance_constant['corporateIndividual']:
			$response .= 'Role: '.$allowance_constant['roleType'][$_POST['role']];
			break;
		case $allowance_constant['fieldLeader']:
		case $allowance_constant['corporateLeader']:
			$response .= 'Role: '.$allowance_constant['roleType'][$_POST['role']].' - '.$allowance_constant['levelName'][$_POST['role']][$_POST['extra_level']];
			break;
		default:
			$response .= 'default';
		}
		$response .= '<br><br>';
		$response .= '<h3>'.getStringConstant("first_header").'</h3>';
		$response .= '<br>';
		$response .= '<b>'.getStringConstant("hour_label").'</b>';
		$response .= '<br>';
		
		$response .= '<span style="padding: 0 10px;"> </span>'.$_POST['hour_percentage'].'%';
		$response .= '<br>';
		
		
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
					
					$response .= '<b>'.$result->label.'</b>';
					$response .= '<br>';
					foreach ($array_key as $key){
						$parts = explode("-", $key);
						$part_0 = $parts[0];
						$part_1 = $parts[1];
						if ($part_0 == 'form' and $part_1 == $result->id){
							$sql = "SELECT * FROM `allowance_answer` WHERE `id`=".$_POST[$key];
							$sub_result = $wpdb->get_results($sql);
							$sub_result = $sub_result[0];
							$response .= '<span style="padding: 0 10px;"> </span>'.$sub_result->label;
							unset($_POST[$key]);
						}
					}
					break;
				case 'header':
					$response .= $result->label;
					$response .= '<br>';
					break;
			}
			$response .= '<br>';
		}
		
		
		/*$widthL = Max($pdf->GetStringWidth("Recommended Minimum:"), $pdf->GetStringWidth("Staff Member's Personal Maximum:")) + 5;
		$widthV = Max($pdf->GetStringWidth($_POST['minimum']), $pdf->GetStringWidth($_POST['maximum']), $pdf->GetStringWidth('Annual')) + 5;
		$widthM = Max($pdf->GetStringWidth($_POST['minimum_month']), $pdf->GetStringWidth($_POST['maximum_month']), $pdf->GetStringWidth('Monthly'));*/
		
		$response .= ''; //TODO
		$response .= '<br>';
		
		$response .= '<table><tr>';
		$response .= '<th></th>';
		$response .= '<th>Annual</th>';
		$response .= '<th>Monthly</th></tr>';
		
		$response .= '<tr>';
		$response .= '<td>Recommended Minimum:</td>';
		$response .= '<td style="padding: 0 10px;">'.$_POST['minimum'].'</td>';
		$response .= '<td style="padding: 0 10px;">'.$_POST['minimum_month'].'</td>';
		$response .= '</tr>';
		
		$response .= '<tr>';
		$response .= '<td>Staff Member\'s Personal Maximum:</td>';
		$response .= '<td style="padding: 0 10px;">'.$_POST['maximum'].'</td>';
		$response .= '<td style="padding: 0 10px;">'.$_POST['maximum_month'].'</td>';
		$response .= '</tr></table>';
		
		$lineDrop = 4;
		$response .= '<br>';
		$response .= '<hr>';
		/*$response .= 'Change in Allowance or Hours';
		$response .= '<br>';
		
		if ($_POST['preAllowance'] == ""){
			$response .= 'Previous Allowance:__________________________________';
		}
		else {
			$response .= 'Previous Allowance: '.$_POST['preAllowance'];
		}
		$response .= '<span style="padding: 0 5px;"> </span>';
		if ($_POST['newAllowance'] == ""){
			$response .= 'New Allowance: _____________________________';
		}
		else {
			$response .= 'New Allowance:  '.$_POST['newAllowance'];
		}
		$response .= '<br>';
		
		if ($_POST['preHours'] == ""){
			$response .= 'Previous number of hours:_____________________________';
		}
		else {
			$response .= 'Previous number of hours: '.$_POST['preHours'];
		}
		$response .= '<span style="padding: 0 5px;"> </span>';
		if ($_POST['newHours'] == ""){
			$response .= 'New number of hours: ________________________';
		}
		else {
			$response .= 'New number of hours:  '.$_POST['newHours'];
		}
		
		$response .= '<br>';
		$response .= '<b><i>** If schedule is less than 40 hours per week enter normal days/ hours worked</i></b>';
		$response .= '<br><br>';
		
		$line = ' ________';
		
		$response .= "Monday: ".(($_POST['mon'] == "") ? $line : $_POST['mon']).
				"    Tuesday: ".(($_POST['tues'] == "") ? $line : $_POST['tues']).
				"    Wednesday: ".(($_POST['wed'] == "") ? $line : $_POST['wed']).
				"    Thursday: ".(($_POST['thurs'] == "") ? $line : $_POST['thurs']).
				"    Friday: ".(($_POST['fri'] == "") ? $line : $_POST['fri']);
		$response .= '<br><hr>';
		
		
		/*$response .= 'Staff Member Signature: __________________________________________________';
		
		$response .= 'Date: '.(($_POST['date'] == "") ? $line : $_POST['date']);
		$response .= '<br>';
		
		$response .= 'Ministry/Department Director Signature: _______________________________________';
		
		$response .= 'Date: '.(($_POST['date'] == "") ? $line : $_POST['date']);
		$response .= '<br>';
		
		$response .= 'HR Authorizing Agent: _____________________________________________________ ';
		
		$response .= 'Date: '.(($_POST['date'] == "") ? $line : $_POST['date']);
		$response .= '<br>';*/
		$response .= '</div>';
		//to counter act the wp-minify plugin (ob_start(array($this, 'modify_buffer'));)
		//ob_end_clean();
		//$pdf->Output('allowance_calculator.pdf', 'I');
		//echo $response;
		//exit;
		
		//echo '<textarea hidden name="misc_content" rows="1" cols="1">'.$response.'</textarea></form>';
		$completemessage .='<input type="hidden" name="export" value="1">';
		$completemessage .= ('<textarea hidden name="misc_content" rows="1" cols="1">'.stripslashes($response).'</textarea></form>');
		echo $completemessage;
	}
	
}
?>
<script type="text/javascript">
    document.getElementById('allowanceform').submit(); // SUBMIT FORM
</script>
