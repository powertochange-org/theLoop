<?php

parseAdminInput();

parseAdminRemove();

function parseAdminInput(){
	global $wpdb;
	if (isAdmin()){
		$admin = mysql_real_escape_string(htmlspecialchars($_GET["input_add_admin"]));
		$user = get_user_by('login', $admin );
		if ($admin==""){
			return;
		}
		if (!$user){
			echo '<BR>No user: '. $admin.'<BR>';
		}
		else{
			$ID = $user->ID;
			update_user_meta( $ID, 'support_calculator_admin', 1);
			echo '<BR>Made user: '. $admin. ' an administrator<BR>';
		}
		
	}
}

function parseAdminRemove(){
	global $wpdb;
	if (isAdmin()){
		$admin = mysql_real_escape_string(htmlspecialchars($_GET["input_remove_admin"]));
		$user = get_user_by('login', $admin );
		if ($admin==""){
			return;
		}
		if (!$user){
			echo '<BR>No user: '. $admin.'<BR>';
		}
		else{
			$ID = $user->ID;
			update_user_meta( $ID, 'support_calculator_admin', 0);
			echo '<BR>Removed user: '. $admin. ' as administrator<BR>';
		}
		
	}
}

function getAdmins(){
	$string="";
	$admins = get_users(array('meta_key' => 'support_calculator_admin', 'meta_value' => '1'));
	foreach($admins as $user){
		$string .= "<li>".$user->user_login."<input type='button' value='Remove' onclick='demoteUser(\"".$user->user_login."\");'></li>";
	}
	return $string;
}

function setConstant($field, $data){
	global $wpdb;
	if(getConstant($field) == null){ //if no row already
		$wpdb->insert( 
			'support_calculator_constants', 
			array( 
				'id' => 'NULL', 
				'key' => $field,
				'value' => $data
			), 
			array( 
				'%d', 
				'%s',
				'%s'
			) 
		);
	}
	else{
		$wpdb->update( 
			'support_calculator_constants', 
			array( 'value' => $data), 
			array( 'key' => $field ), 
			array( 	'%s'), 
			array( '%s' ) 
		);
	}
}	


function setStringConstant($field, $data){
	global $wpdb;
	if(getStringConstant($field) == null){ //if no row already
		$wpdb->insert( 
			'string_store', 
			array( 
				'id' => 'NULL', 
				'key' => $field,
				'value' => $data
			), 
			array( 
				'%d', 
				'%s',
				'%s'
			) 
		);
	}
	else{
		$wpdb->update( 
			'string_store', 
			array( 'value' => $data), 
			array( 'key' => $field ), 
			array( 	'%s'), 
			array( '%s' ) 
		);
	}
}	

function printAdminChangeInterface(){
	echo '<script type="text/javascript">
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
		<td>Make administrator:</td>
		<td><form name="add_admin" id="add_admin" action="" method="get">
				<input type="text" name="input_add_admin" id="input_add_admin" />
		</form></td>
		<td><input type="button" value="Promote" onclick="add_admin.submit();"></td>
		</tr></table>
		<form name="remove_admin" id="remove_admin" action="" method="get">
				<input type="hidden" name="input_remove_admin" id="input_remove_admin" />
		</form>
		Administrators : 
		<ul>
		'.getAdmins().'
		</ul>
	';
}
	
?>