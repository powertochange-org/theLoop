<?php
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
		// INSERT PHONE
		if(isset($_POST['new_phone_number'])){ 
			$share = $_POST['phoneShare'];
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
			$country = strip_tags($_POST['phonecountry']);
			$area = strip_tags($_POST['phonearea']);
			$number1 = strip_tags($_POST['phonenumber1']);
			$number2 = strip_tags($_POST['phonenumber2']);
			$extension = strip_tags($_POST['phoneextension']);
			$type = strip_tags($_POST['phonetype']);
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
		// UPDATE PHONE
		if(isset($_POST['editPhone'])) {
			$id = $_POST['editPhone'];
			$phones = $wpdb-> get_results("SELECT * FROM phone_number WHERE phone_number_id = '" . $id . "'");
			foreach ($phones as $phone) {
				$phoneshare = 0;
				$isMinistry = 0;
				if ($_POST['phoneShare'] == 'personalshare') {
					$phoneshare = 1;
				} elseif ($_POST['phoneShare'] == 'ministryshare') {
					$phoneshare = 1;
					$isMinistry = 1;
				}
				if (!empty($_POST['phonenumber1'])) {
					$country = strip_tags($_POST['phonecountry']);
					$countrycode = countryToNumber($country);
					$areacode = strip_tags($_POST['phonearea']);
					$extension = strip_tags($_POST['phoneextension']);
					$phonetype = strip_tags($_POST['phonetype']);
					$phonenumber = strip_tags($_POST['phonenumber1']) . '-' . strip_tags($_POST['phonenumber2']);
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
		// DELETE PHONE #
		if(isset($_POST['deletePhone'])) {
			$phones = $wpdb-> get_results("SELECT * FROM phone_number WHERE phone_number_id = '" . $_POST['deletePhone'] ."' LIMIT 1");
			foreach ($phones as $phone) {
				$wpdb->insert( 'sync',
						array(  'table_name'    => 'phone_number',
								'record_id'     => $phone->external_id, 
								'sync_action'   => 'delete',
								'field_changed'	=> $_POST['deletePhone'],
								'changed_date'	=>	date('Y-m-d H-i-s'),
								'user_login'	=> $user->user_login
						));
			}
			$wpdb->query("DELETE FROM phone_number WHERE phone_number_id='" . $_POST['deletePhone'] . "'");
		}
		// UPDATE MINISTRY ADDRESS
		if (isset($_POST['ministryAddress'])) {
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'update',
									'field_changed' => 'ministry_address',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));
			$wpdb->update( 'employee', 
							array( 'ministry_address_line1' => strip_tags($_POST['ministry_address1']),
									'ministry_address_line2' => strip_tags($_POST['ministry_address2']),
									'ministry_city' => strip_tags($_POST['ministry_city_value']),
									'ministry_province' => strip_tags($_POST['ministry_province_value']),
									'ministry_postal_code' => strip_tags($_POST['ministry_postal_code']),
									'ministry_country' => strip_tags($_POST['ministry_country'])),
							array( 'user_login' => $current_user->user_login  ) 
						);
			if (isset($_POST['ministry_address3'])) {
				$wpdb->update( 'employee',
							array( 'ministry_address_line3' => strip_tags($_POST['ministry_address3'])),
							array( 'user_login' => $current_user->user_login )
						);
			}
		}
		// INSERT MINISTRY EMAIL
		if (isset($_POST['new_min_email_address'])) {
			$address = strtolower(strip_tags($_POST['new_min_email_address']));
			if (isMinistryAddress($address)) {
				$ministry = '1'; 
			} else {
				$ministry = '0';
			}
			$wpdb->insert( 'email_address', 
							array( 'employee_id' => $user->external_id,
									'email_address' => $address,
									'is_ministry' => $ministry,
									'share_email' => $ministry)
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
		// UPDATE MINISTRY EMAIL
		if (isset($_POST['minEmail'])) {
                        $address = strtolower(strip_tags($_POST['min_email']));
			if (isMinistryAddress($address)) {
                               $ministry = '1';
                        } else {
                                $ministry = '0';
                        }
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'email_address',
									'record_id'     => $_POST['email_address_id'],
									'sync_action'   => 'update',
									'field_changed' => '',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));
			$wpdb->update( 'email_address', 
							array( 'email_address' => $address,
									'is_ministry'	=> $ministry),
							array( 'email_address_id' => $_POST['email_address_id']  ) 
						);
		}
		// DELETE MINISTRY EMAIL
		if (isset($_POST['deleteMinEmail'])) {
			$id = $_POST['deleteMinEmail'];
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'email_address',
									'record_id'     => $_POST['external_id'],
									'sync_action'   => 'delete',
									'field_changed' => $id,
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login,
							));
			$wpdb->query("DELETE FROM email_address WHERE email_address_id='" . $id . "'");
		}
		// UPDATE MINISTRY SOCIAL MEDIA
		if (isset($_POST['minSocialMedia'])) {
			$wpdb->update( 'employee', 
							array( 'ministry_website' => strip_tags($_POST['website']),
									'ministry_twitter_handle' => strip_tags($_POST['twitter']),
									'ministry_skype' => strip_tags($_POST['skype']),
									'ministry_facebook' => strip_tags($_POST['facebook'])),
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
		// DELETE MINISTRY SOCIAL MEDIA
		if (isset($_POST['del_min_website'])) {
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'delete',
									'field_changed' => 'ministry_website',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));			
			$wpdb->update( 'employee',
							array( 'ministry_website' => '' ),
							array( 'external_id' => $user->external_id)
						);
		}
		if (isset($_POST['del_min_twitter'])) {
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'delete',
									'field_changed' => 'ministry_twitter_handle',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));			
			$wpdb->update( 'employee',
							array( 'ministry_twitter_handle' => '' ),
							array( 'external_id' => $user->external_id)
						);
		}
		if (isset($_POST['del_min_skype'])) {
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'delete',
									'field_changed' => 'ministry_skype',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));			
			$wpdb->update( 'employee',
							array( 'ministry_skype' => '' ),
							array( 'external_id' => $user->external_id)
						);
		}
		if (isset($_POST['del_min_facebook'])) {
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'delete',
									'field_changed' => 'ministry_facebook',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));			
			$wpdb->update( 'employee',
							array( 'ministry_facebook' => '' ),
							array( 'external_id' => $user->external_id)
						);
		}
		// UPDATE PERSONAL ADDRESS
		if (isset($_POST['personalAddress'])) {
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'update',
									'field_changed' => 'personal_address',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));
			$wpdb->update( 'employee', 
							array( 'address_line1' => strip_tags($_POST['address1']),
									'address_line2' => strip_tags($_POST['address2']),
									'city' => strip_tags($_POST['city_value']),
									'province' => strip_tags($_POST['province_value']),
									'postal_code' => strip_tags($_POST['postal_code']),
									'country' => strip_tags($_POST['country']),
									'share_address' => $_POST['addressPermissions']),
							array( 'user_login' => $current_user->user_login  ) 
						);
			if (isset($_POST['address3'])) {
				$wpdb->update( 'employee',
							array( 'address_line3' => strip_tags($_POST['address3'])),
							array( 'user_login' => $current_user->user_login )
						);
			}
		}
		// INSERT PERSONAL EMAIL
		if (isset($_POST['new_email_address'])) {
                        $address = strtolower(strip_tags($_POST['new_email_address']));
                        if (isMinistryAddress($address)) {
                                $ministry = '1';
				$shared = '1';
                        } else {
                                $ministry = '0';
				$shared = isset($_POST['share_email']);
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
		// UPDATE PERSONAL EMAIL
		if (isset($_POST['email'])) {
                        $address = strtolower(strip_tags($_POST['email']));
                        if (isMinistryAddress($address)) {
                                $ministry = '1';
                                $shared = '1';
                        } else {
                                $ministry = '0';
                                $shared = isset($_POST['share_email']);
                        }

			$wpdb->insert( 'sync',
							array(  'table_name'    => 'email_address',
									'record_id'     => $_POST['email_address_id'],
									'sync_action'   => 'update',
									'field_changed' => '',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));
			$wpdb->update( 'email_address', 
							array( 'email_address' => $address,
								'is_ministry' => $ministry,
								'share_email' => $shared),
							array( 'email_address_id' => $_POST['email_address_id']  ) 
						);
		}
		// DELETE PERSONAL EMAIL
		if (isset($_POST['deleteEmail'])) {
			$id = $_POST['deleteEmail'];
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'email_address',
									'record_id'     => $_POST['external_id'],
									'sync_action'   => 'delete',
									'field_changed' => $id,
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));
			$wpdb->query("DELETE FROM email_address WHERE email_address_id='" . $id . "'");
		}
		// UPDATE PERSONAL SOCIAL MEDIA
		if (isset($_POST['socialMedia'])) {
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'update',
									'field_changed' => 'social_media',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));
			$wpdb->update( 'employee', 
							array( 'website' => strip_tags($_POST['website']),
									'twitter_handle' => strip_tags($_POST['twitter']),
									'skype' => strip_tags($_POST['skype']),
									'facebook' => strip_tags($_POST['facebook'])),
							array( 'external_id' => $user->external_id)
						);
		}
		// DELETE PERSONAL SOCIAL MEDIA
		if (isset($_POST['del_website'])) {
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'delete',
									'field_changed' => 'personal_website',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));			
			$wpdb->update( 'employee',
							array( 'website' => '' ),
							array( 'external_id' => $user->external_id)
						);
		}
		if (isset($_POST['del_twitter'])) {
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'delete',
									'field_changed' => 'personal_twitter_handle',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));			
			$wpdb->update( 'employee',
							array( 'twitter_handle' => '' ),
							array( 'external_id' => $user->external_id)
						);
		}
		if (isset($_POST['del_skype'])) {
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'delete',
									'field_changed' => 'personal_skype',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));			
			$wpdb->update( 'employee',
							array( 'skype' => '' ),
							array( 'external_id' => $user->external_id)
						);
		}
		if (isset($_POST['del_facebook'])) {
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'delete',
									'field_changed' => 'personal_facebook',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));			
			$wpdb->update( 'employee',
							array( 'facebook' => '' ),
							array( 'external_id' => $user->external_id)
						);
		}
		if(isset($_POST['notes'])){
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

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

?>
