<?php include 'countryToNumber.php';
		//$test = $wpdb->get_row('SELECT * from approval_address_change where user_login="' . $user->user_login . '"');
		//if(!isset($test->user_login)){ //we use updates to update the sync table. so if they don't have a sync record just make a blank one
		//	$wpdb->insert( 'approval_address_change', array( 'user_login' => $user->user_login)); 
		//}
		
		//all these ifs check if the user changed something. then it updates the database (including to a sync table so we can send it back to HRIS
		// Remove Photo
		if(isset($_POST['remove'])){ //user clicked remove photo button
			$wpdb->update( 'employee', 
							array( 'share_photo' => 0 	),
							array( 'user_login' => $current_user->user_login  ),
							array('%d')
						);
		}
		
		//Ministry Address
		
		//checking if anything is different
		if (strip_tags($_POST['ministryAddress']['line1']) != $user->ministry_address_line1 
				|| strip_tags($_POST['ministryAddress']['line2']) != $user->ministry_address_line2
				|| strip_tags($_POST['ministryAddress']['city']) != $user->ministry_city
				|| strip_tags($_POST['ministryAddress']['pr']) != $user->ministry_province
				|| strip_tags($_POST['ministryAddress']['country']) != $user->ministry_country
				|| strip_tags($_POST['ministryAddress']['pc']) != $user->ministry_postal_code){
				
			$wpdb->insert( 'sync',
				array(  'table_name'    => 'employee',
						'record_id'     => $user->external_id,
						'sync_action'   => 'update',
						'field_changed' => 'ministry_address',
						'changed_date'	=>	date('Y-m-d H-i-s'),
						'user_login'	=> $user->user_login
				));
			$wpdb->update( 'employee', 
				array( 'ministry_address_line1' => strip_tags($_POST['ministryAddress']['line1']),
						'ministry_address_line2' => strip_tags($_POST['ministryAddress']['line2']),
						'ministry_city' => strip_tags($_POST['ministryAddress']['city']),
						'ministry_province' => strip_tags($_POST['ministryAddress']['pr']),
						'ministry_country' => strip_tags($_POST['ministryAddress']['country']),
						'ministry_postal_code' => strip_tags($_POST['ministryAddress']['pc'])),
				array( 'user_login' => $current_user->user_login  ) 
				);
		}
		
		//Phone
		foreach($_POST['phone'] as $key => $value){
		
			//if add new
			if ($key < 0){
			
				//if dirty
				if ($value['area'] != ''){
					
					$share = $value['share'];
					if($share=='personalnotshare'){
						$phoneshare = 0;
						$isMinistry = 0; 
					}
					else if ($share == 'personalshare'){
						$phoneshare = 1;
						$isMinistry = 0;
					}
					else if ($share == 'ministryshare'){
						$phoneshare = 1;
						$isMinistry = 1;
					}
					$country = strip_tags($value['country']);
					$area = strip_tags($value['area']);
					$number1 = strip_tags($value['part1']);
					$number2 = strip_tags($value['part2']);
					$extension = strip_tags($value['ext']);
					$type = strip_tags($value['type']);
					if($number1 != ""){
						$number = $number1 . '-' . $number2; //format the phone number like XXX-XXXX
						$wpdb->insert( 'phone_number',
								array( 'country_code' 	=> $country,
										'country_phone_code' => countryToNumber($country), 
										'area_code'		=> $area, 
										'contact_number' =>	$number, 
										'extension'		=> $extension, 
										'phone_type'	=> $type, 
										'employee_id'	=> $user->external_id, 
										'share_phone'	=> $phoneshare, 
										'is_ministry' 	=> $isMinistry
								));
						// get the record ID
						$id = $wpdb->insert_id;
						$wpdb->insert( 'sync',
								array(  'table_name'    => 'phone_number',
										'record_id'     => $id,
										'sync_action'   => 'insert',
										'changed_date'	=>	date('Y-m-d H-i-s'),
										'user_login'	=> $user->user_login
								));
					}
					
				}
			}
			else {
				$id = $key;
				$phones = $wpdb-> get_results("SELECT * FROM phone_number WHERE phone_number_id = '" . $id . "'");
				$phone = $phones[0];
				$phoneshare = 0;
				$isMinistry = 0;
				if ($value['share'] == 'personalshare') {
					$phoneshare = 1;
				} elseif ($value['share'] == 'ministryshare') {
					$phoneshare = 1;
					$isMinistry = 1;
				}
				if (!empty($value['phonenumber1'])) {
					$country = strip_tags($value['phonecountry']);
					$countrycode = countryToNumber($country);
					$areacode = strip_tags($value['phonearea']);
					$extension = strip_tags($value['phoneextension']);
					$phonetype = strip_tags($value['phonetype']);
					$phonenumber = strip_tags($value['phonenumber1']) . '-' . strip_tags($value['phonenumber2']);
					
					//check if dirty
					
					if ($country != $phone->country_code 
							|| $countrycode != $phone->country_phone_code
							|| $areacode != $phone->area_code
							|| $phonenumber != $phone->contact_number
							|| $extension != $phone->extension
							|| $phonetype != $phone->phone_type
							|| $phoneshare != $phone->share_phone
							|| $isMinistry != $phone->is_ministry) {
						$wpdb->insert( 'sync',
								array(  'table_name'    => 'phone_number',
										'record_id'     => $phone->phone_number_id,
										'sync_action'   => 'update',
										'changed_date'	=>	date('Y-m-d H-i-s'),
										'user_login'	=> $user->user_login
								));
						$wpdb->update( 'phone_number', 
								array( 'country_code' => $country,
										'country_phone_code' => $countrycode,
										'area_code' => $areacode,
										'contact_number' => $phonenumber,
										'extension' => $extension,
										'phone_type' => $phonetype,
										'employee_id' => $user->external_id,
										'share_phone' => $phoneshare,
										'is_ministry' => $isMinistry),
								array(	'phone_number_id' => $id));
					}
				}
			}
		}
		
		//Email
		foreach($_POST['email'] as $key => $value){
		
			//if add new
			if ($key < 0){
			
				//if dirty
				if ($value['email'] != ''){
					$address = strtolower(strip_tags($value['email']));
					if (isMinistryAddress($address)) {
						$ministry = '1'; 
						$shared = '1';
					} else {
						$ministry = '0';
						$shared = $value['share'];
					}
					$wpdb->insert( 'email_address', 
						array( 'employee_id' => $user->external_id,
								'email_address' => $address,
								'is_ministry' => $ministry,
								'share_email' => $shared)
					);
					$id = $wpdb->insert_id;
					$wpdb->insert( 'sync',
						array(  'table_name'    => 'email_address',
								'record_id'     => $id,
								'sync_action'   => 'insert',
								'field_changed' => '',
								'changed_date'	=>	date('Y-m-d H-i-s'),
								'user_login'	=> $user->user_login
						));
				}
			}
			else{
				$id = $key;
				$emails = $wpdb-> get_results("SELECT * FROM email_address WHERE email_address_id = '" . $id . "'");
				$email = $emails[0];
				$address = strtolower(strip_tags($value['email']));
				if ($address == ""){
					$wpdb->insert( 'sync',
							array(  'table_name'    => 'email_address',
									'record_id'     => $value['external_id'],
									'sync_action'   => 'delete',
									'field_changed' => $id,
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login,
							));
					$wpdb->query("DELETE FROM email_address WHERE email_address_id='" . $id . "'");
				}
				else{
					if (isMinistryAddress($address)) {
						   $ministry = '1';
						   $shared = '1';
					} else {
							$ministry = '0';
							$shared = $value['share'];
					}
					$wpdb->insert( 'sync',
									array(  'table_name'    => 'email_address',
											'record_id'     => $id,
											'sync_action'   => 'update',
											'field_changed' => '',
											'changed_date'	=>	date('Y-m-d H-i-s'),
											'user_login'	=> $user->user_login
									));
					if ($address != $email->email_address 
							|| $ministry != $email->is_ministry
							|| $shared != $email->share_email) {
								
						$wpdb->update( 'email_address', 
								array( 'email_address' => $address,
										'is_ministry'	=> $ministry,
										'share_email' => $shared),
								array( 'email_address_id' => $id  ) 
							);
					
					
					}
				}
			}
		}
		
		//Ministry Social Media
		
		//checking if anything is different
		if (strip_tags($_POST['ministryWebsite']) != $user->ministry_website 
				|| strip_tags($_POST['ministryTwitter']) != $user->ministry_twitter_handle
				|| strip_tags($_POST['ministrySkype']) != $user->ministry_skype
				|| strip_tags($_POST['ministryFacebook']) != $user->ministry_facebook){
			$wpdb->update( 'employee', 
							array( 'ministry_website' => strip_tags($_POST['["ministryWebsite"]']),
									'ministry_twitter_handle' => strip_tags($_POST['ministryTwitter']),
									'ministry_skype' => strip_tags($_POST['ministrySkype']),
									'ministry_facebook' => strip_tags($_POST['ministryFacebook'])),
							array( 'external_id' => $user->external_id)
						);
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'update',
									'field_changed' => 'ministry_social_media',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));
		}
		
		
		
		//Personal Address
		
		//checking if anything is different
		if (strip_tags($_POST['personalAddress']['line1']) != $user->address_line1 
				|| strip_tags($_POST['personalAddress']['line2']) != $user->address_line2
				|| strip_tags($_POST['personalAddress']['city']) != $user->city
				|| strip_tags($_POST['personalAddress']['pr']) != $user->province
				|| strip_tags($_POST['personalAddress']['country']) != $user->country
				|| strip_tags($_POST['personalAddress']['pc']) != $user->postal_code
				|| $_POST['personalAddress']['share'] != $user->share_address){
				
			$wpdb->insert( 'sync',
				array(  'table_name'    => 'employee',
						'record_id'     => $user->external_id,
						'sync_action'   => 'update',
						'field_changed' => 'personal_address',
						'changed_date'	=>	date('Y-m-d H-i-s'),
						'user_login'	=> $user->user_login
				));
			$wpdb->update( 'employee', 
				array( 'address_line1' => strip_tags($_POST['personalAddress']['line1']),
						'address_line2' => strip_tags($_POST['personalAddress']['line2']),
						'city' => strip_tags($_POST['personalAddress']['city']),
						'province' => strip_tags($_POST['personalAddress']['pr']),
						'postal_code' => strip_tags($_POST['personalAddress']['pc']),
						'country' => strip_tags($_POST['personalAddress']['country']),
						'share_address' => $_POST['personalAddress']['share']),
				array( 'user_login' => $current_user->user_login  ) 
				);
		}
		
		//Ministry Social Media
		
		//checking if anything is different
		if (strip_tags($_POST['personalWebsite']) != $user->website 
				|| strip_tags($_POST['personalTwitter']) != $user->twitter_handle
				|| strip_tags($_POST['personalSkype']) != $user->skype
				|| strip_tags($_POST['personalFacebook']) != $user->facebook){
			$wpdb->update( 'employee', 
							array( 'website' => strip_tags($_POST['personalWebsite']),
									'twitter_handle' => strip_tags($_POST['personalTwitter']),
									'skype' => strip_tags($_POST['personalSkype']),
									'facebook' => strip_tags($_POST['personalFacebook'])),
							array( 'external_id' => $user->external_id)
						);
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'update',
									'field_changed' => 'ministry_social_media',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));
		}
		
		//Personal Note
		if(substr(strip_tags($_POST['notes'],"<b></b><br><br/><hr><hr/><p><p/>"),0,255)  != $user->notes){
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'update',
									'field_changed' => 'notes',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));
			
			
			$wpdb->update(	'employee',
							array( 'notes' => substr(strip_tags($_POST['notes'],"<b></b><br><br/><hr><hr/><p><p/>"),0,255) ),
							array( 'user_login' => $current_user->user_login  )
						);
			
									
		}
		// Re-read user, in case values changed
		$user = $wpdb->get_row("SELECT * FROM employee WHERE user_login = '" . $current_user->user_login . "'");

function isMinistryAddress($address) {
	if (endsWith($address, '@powertochange.org')
	 || endsWith($address, '@athletesinaction.com')
	 || endsWith($address, '@christianembassy.ca')
	 || endsWith($address, '@connectingstreams.com')
	 || endsWith($address, '@drime.com')
	 || endsWith($address, '@familylifecanada.com')
	 || endsWith($address, '@globalaid.net')
	 || endsWith($address, '@leaderimpactgroup.com')
	 || endsWith($address, '@truthmedia.com')
	 || endsWith($address, '@p2c.com')
	 || endsWith($address, '@c4c.ca')) {
		return true;
	}
	return false;
}

function endsWith($haystack, $needle) {
	$length = strlen($needle);
	if ($length == 0) {
		return true;
	}

	return (substr($haystack, -$length) === $needle);
} ?>
