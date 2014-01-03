<?php

/*This file contains functions that are used by support-calculator.php and allowance-calculator.php  and maybe useful elsewhere*/

//get the current user's spouse or null if no spouse

$function_spouse = null;

function getSpouse(){
	//gets spouse's wb id 
	// if no spouse return -1
	global $function_spouse;
	
	
	if (is_null($function_spouse)){
		global $current_user_id, $wpdb;
		$ID = $current_user_id;
		$user_login =  wp_get_current_user()->user_login;
		$sql = 'SELECT `ID` FROM `wp_users` JOIN `employee` AS user ON wp_users.user_login = user.user_login JOIN `employee` AS spouse ON user.external_id = spouse.spouse_id WHERE `ID`!='.$ID.' AND spouse.user_login="'.$user_login.'"';	
		$id = $wpdb->get_row($sql)->ID;
		if ($id == "" || is_null($id)){
			$function_spouse = -1;
		}
		else {
			$function_spouse = $id;
		}
	}
	return $function_spouse;
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

//returns wether the current user is an admin
function isAdmin(){
	global $current_user_id;
	$ID = $current_user_id;
	$result = get_user_meta($ID, 'support_calculator_admin', true);			
	return $result == 1;
}

function getConstant($field){
	global $wpdb;
	$sql = "SELECT `value` FROM `support_calculator_constants` WHERE `key` = '". $field. "'";
	$result = $wpdb->get_row($sql)->value;
	if ($result == ""){
		return null;
	}
	return $result;
}

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



function getName($id=null, $andSpouse=false){
	
	global $current_user_id, $wpdb;
	if ($id == null){
		$id = $current_user_id;
	}
	
	//todo what if different last names
	$name = getFieldEmployee("first_name", $id);
	
	$spouse = getSpouse();
	if ($spouse != -1 and $andSpouse){
		$name .= " and ";
		$sql = 'SELECT `first_name` FROM `employee` JOIN `wp_users` ON  employee.user_login = wp_users.user_login WHERE wp_users.ID ='. $spouse;
		$name .= $wpdb->get_row($sql)->first_name;
	}
	$name .= " ".getFieldEmployee("last_name", $id);
	return $name;
}

function changeNL($string){
	$out = "";
	for ($i = 0; $i < strlen($string); $i ++){
		if (ord ($string{$i}) == 10){
			$out.= "<BR>";
			if ($i + 1 < strlen($string) and ord ($string{$i + 1}) == 13){
				$i ++;
			}
		}
		else if (ord ($string{$i}) == 13){
			$out.= "<BR>";
			if ($i + 1 < strlen($string) and ord ($string{$i + 1}) == 10){
				$i ++;
			}
		}
		else {
			$out .= $string{$i};
		}
	}
	return $out;
}

?>