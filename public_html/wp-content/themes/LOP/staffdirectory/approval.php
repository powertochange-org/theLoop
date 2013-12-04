<?php
	$isAdministrator = get_user_meta($current_user->ID, "wp_capabilities", true);
	//anything above the if statement on the next line will be visible to any schmuck that stumbles upon this URL
	if('true'==get_user_meta($current_user->ID, "directory_admin", true) //if user is flagged as as having access to directory
	|| '1'==$isAdministrator['administrator'] ){ //or if user is wp admin
		
		if(isset($_POST)){
		
			//var_dump($_POST);
			if(isset($_POST['id'])){
				//echo $_POST[$_POST['id']] . strcmp($_POST[$_POST['id']], "Approve");
				if(strcmp($_POST[$_POST['id']], "Approve")==0){
					$approved=true;
				}
				else if($_POST[$_POST['id']] == 'Reject'){
					$approved=false;
				}
				
				$approve = $wpdb->get_row('SELECT * FROM approval_address_change where id="' . $_POST['id'] . '"'); 
				$old = $wpdb->get_row('SELECT * FROM employee where user_login="' . $approve->user_login . '"');
				if($approved){
					if($_POST['new_address_line1'] != $old->address_line1){
						$wpdb->insert('sync', array(
											'table_name' =>'employee',
											'record_id'  =>$old->external_id,
											'field_changed' => 'address_line1',
											'old_data' => $old->address_line1,
											'new_data' => $_POST['new_address_line1'],
											'changed_date' => $approve->changed_date,
											'user_login' => $approve->user_login,
											'sync_action' => 'update')); //need a way to distinguish between update/delete
					}
					if($_POST['new_address_line2'] != $old->address_line2){
						$wpdb->insert('sync', array(
											'table_name' =>'employee',
											'record_id'  =>$old->external_id,
											'field_changed' => 'address_line2',
											'old_data' => $old->address_line2,
											'new_data' => $_POST['new_address_line2'],
											'changed_date' => $approve->changed_date,
											'user_login' => $approve->user_login,
											'sync_action' => 'insert')); 

					}
					if($_POST['new_address_line3'] != $old->address_line3){
						$wpdb->insert('sync', array(
											'table_name' =>'employee',
											'record_id'  =>$old->external_id,
											'field_changed' => 'address_line3',
											'old_data' => $old->address_line3,
											'new_data' => $_POST['new_address_line3'],
											'changed_date' => $approve->changed_date,
											'user_login' => $approve->user_login,
											'sync_action' => 'insert')); //need a way to distinguish between update/delete

					}
					if($_POST['new_city'] != $old->city){
						$wpdb->insert('sync', array(
											'table_name' =>'employee',
											'record_id'  =>$old->external_id,
											'field_changed' => 'city',
											'old_data' => $old->city,
											'new_data' => $_POST['new_city'],
											'changed_date' => $approve->changed_date,
											'user_login' => $approve->user_login,
											'sync_action' => 'insert')); //need a way to distinguish between update/delete
					}
					if($_POST['new_province'] != $old->province){
						$wpdb->insert('sync', array(
											'table_name' =>'employee',
											'record_id'  =>$old->external_id,
											'field_changed' => 'province',
											'old_data' => $old->province,
											'new_data' => $_POST['new_province'],
											'changed_date' => $approve->changed_date,
											'user_login' => $approve->user_login,
											'sync_action' => 'insert')); //need a way to distinguish between update/delete
					}
					if($_POST['postal_code'] != $old->postal_code){
						$wpdb->insert('sync', array(
											'table_name' =>'employee',
											'record_id'  =>$old->external_id,
											'field_changed' => 'postal_code',
											'old_data' => $old->postal_code,
											'new_data' => $_POST['postal_code'],
											'changed_date' => $approve->changed_date,
											'user_login' => $approve->user_login,
											'sync_action' => 'insert')); //need a way to distinguish between update/delete
					}
					echo $approve->user_login . " this is the user login" . $_POST['new_address_line1'];
					
					$wpdb->update( 
						'employee', 
						 
						array( 
							'address_line1' => $_POST['new_address_line1'],	// string
							'address_line2' => $_POST['new_address_line2'],	// integer (number)
							'address_line3' => $_POST['new_address_line3'],
							'city' => $_POST['new_city'],
							'province' => $_POST['new_province'],
							'postal_code' => $_POST['postal_code']
							
						),
						array( 
							'user_login' => $approve->user_login
							
						),
						array( 
							'%s',	// value1
							'%s',	// value2
							'%s',
							'%s',
							'%s',
							'%s'
						) 
						 
					);
					
				}//approved
				$wpdb->query(
						"DELETE from approval_address_change WHERE id='". $_POST['id'] . "'"
						);
			}
		}

		$results = $wpdb->get_results('SELECT * FROM approval_address_change');
		foreach($results as $result){
			$name = $wpdb->get_var('SELECT CONCAT(first_name, " ", last_name) FROM employee WHERE user_login = "' . $result -> user_login . '"');
			$old = $wpdb->get_row('SELECT address_line1, address_line2, address_line3, city, province, country, postal_code FROM employee WHERE user_login = "' . $result -> user_login . '"');
			$new = $result;
			?>
			<br>  
			<?php echo $name; ?>
			<table>
				<tr>
					<th width=100></th>
					<th>Address</th>
					<th>City</th>
					<th>Province</th>
					<th>Postal Code</th>
					<th>Date Changed</th>
				</tr>
				<tr>
					<th>Old</th>
					<td><?php echo $old->address_line1 . '<br>' . $old->address_line2 . '<br>' . $old->address_line3;?></td>
					<td><?php echo $old->city; ?></td>
					<td><?php echo $old->province;?></td>
					<td><?php echo $old->postal_code;?></td>
					<td><?php echo 'don\'t need this date? either way, I don\'t have it';?></td>
				</tr>
				<form action="../staff-directory/?page=approval" method="post" name="" >
				<tr>
					
					<th>New</th>
					<td>
						<input name="new_address_line1" type="textbox" value="<?php echo $new->new_address_line1;?>">
						<input name="new_address_line2" type="textbox" value="<?php echo $new->new_address_line2;?>"><br>
						<input name="new_address_line3" type="textbox" value="<?php echo $new->new_address_line3;?>"><br>
					</td>
					<td><input name="new_city" type="textbox" value="<?php echo $new->new_city;?>"></td>
					<td><input name="new_province" type="textbox" value="<?php echo $new->new_province;?>"></td>
					<td><input name="postal_code" type="textbox" value="<?php echo $new->postal_code;?>"></td>
					<td><input name="changed_date" type="textbox" value="<?php echo $new->changed_date;?>"></td>	
				</tr>
			</table>
			<?php	
					echo '<input type="submit" name="'. $result->id . '" value="Approve" />';
					echo '<input type="submit" name="'. $result->id . '" value="Reject" />';
					echo '<input type="hidden" name="id" value="'. $result->id . '" />';
				echo '</form>';

				
	}	?>
	

	


<?php		
	} //if
?>