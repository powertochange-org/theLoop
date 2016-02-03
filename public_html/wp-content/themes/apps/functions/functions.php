<?php

/*This file contains functions that are used by support-calculator.php and allowance-calculator.php  and maybe useful elsewhere.
Functions for general admin access in the loop has been added.
*/

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
	return AdminLevel(0);
}

function AdminLevel($l){
	global $current_user_id;
	return (pow(2, $l) & intval(get_user_meta($current_user_id, 'support_calculator_admin', true))) > 0;
}

/*
 *Checks if the user is an admin for a certain program.
 *@param $accessLevel - The level of access to check. 0 is admin, 
 *						1 - see function AdminLevel($l) - this is what it replicates
 *@param $app - The application name to check. For example: support_calculator_admin **do not add the loopadmin_ part
 */
function isAppAdmin($app, $accessLevel) {
	$current_user_id = wp_get_current_user()->id;
	return (pow(2, $accessLevel) & intval(get_user_meta($current_user_id, ('loopadmin_'.$app), true))) > 0;
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

/*
* Gets an array of all the different apps that use the loop admin functionality
*/
function getAppList() {
	global $wpdb;
	$values = array();
	
	$sql = "SELECT meta_key
			FROM wp_usermeta
			WHERE meta_key LIKE '%loopadmin_%'
			GROUP BY meta_key
			ORDER BY meta_key ASC";
	
	$result = $wpdb->get_results($sql, ARRAY_A);
	
	foreach($result as $row) {
		$tmp = split('loopadmin_', $row['meta_key']);
		$values[] = array($tmp[1]); 
	}
	
	return $values;
}

/*
* Gets all the users in the loop.
*/
function getAllUsers() {
	global $wpdb;
	$values = array();
	
	$sql = "SELECT ID, display_name
			FROM wp_users
			ORDER BY display_name ASC";
	
	$result = $wpdb->get_results($sql, ARRAY_A);
	
	foreach($result as $row) {
		$values[] = array($row['ID'], $row['display_name']);
	}
	
	return $values;
}

/*
*Gets a list of all the apps that users have access to.
*/
function getMemberAppAccess() {
	global $wpdb;
	$values = array();
	
	$sql = "SELECT user_id, meta_key, meta_value, display_name
			FROM wp_usermeta 
			LEFT OUTER JOIN wp_users ON wp_users.ID = wp_usermeta.user_id
			WHERE meta_key LIKE '%loopadmin_%' AND user_id != '0' AND meta_value != '0'
			ORDER BY display_name, umeta_id ASC  ";
	
	$result = $wpdb->get_results($sql, ARRAY_A);
	
	foreach($result as $row) {
		$tmp = split('loopadmin_', $row['meta_key']);
		$values[] = array('ROLE'.$tmp[1].'USER'.$row['user_id'], $row['display_name'], $tmp[1]);
	}
	
	return $values;
}

/*
* Stores a new app name in the database. It can then be used to add users to that app, thus giving them admin access.
* To use this new access level, just use the code isAppAdmin('appname', 0) to check if they have admin access.
*/
function storeApp($appname) {
	global $wpdb;
	$sql = "SELECT COUNT(*) AS ENTRYCOUNT
			FROM wp_usermeta
			WHERE user_id = '0' AND meta_key = 'loopadmin_".$appname."'";
	
	$result = $wpdb->get_results($sql, ARRAY_A);
	
	if($result[0]['ENTRYCOUNT'] == '0') {
		$sql = "INSERT INTO wp_usermeta (user_id, meta_key)
				VALUES ('0', 'loopadmin_".$appname."')";
		$result = $wpdb->query($sql);
		return $result;
	}
	return 0;
}

/*
* Gives a user access to an app. Stores it in the db.
*/
function storeMember($appname, $member) {
	return update_user_meta($member, 'loopadmin_'.$appname, '1');
}

/*
* Removes user access from an app. Updates it in the db.
*/
function removeMember($appname, $member) {
	return delete_user_meta($member, 'loopadmin_'.$appname); 
}

?>