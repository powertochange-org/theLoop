<?php include 'countryToNumber.php';
		//$test = $wpdb->get_row('SELECT * from approval_address_change where user_login="' . $user->user_login . '"');
		//if(!isset($test->user_login)){ //we use updates to update the sync table. so if they don't have a sync record just make a blank one
		//	$wpdb->insert( 'approval_address_change', array( 'user_login' => $user->user_login)); 
		//}

        // This keeps track of all the changes that need to be made, so we can
        // send out an email with the changes
        $changes = array();

        if(is_uploaded_file($_FILES['file']['tmp_name'])) { // If we have a new photo
            include ('upload.processor.php');
        }
		
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

            // Add to the changes
            $changes['Ministry Address'] = array(
                'old' => 
		            getField($user->ministry_address_line1) . " <br/>" .
		            getField($user->ministry_address_line2) . " <br/>" .
		            getField($user->ministry_city) . " <br/>" .
		            getField($user->ministry_province) . " <br/>" .
		            getField($user->ministry_country) . " <br/>" .
		            getField($user->ministry_postal_code),
                'new' => 
				    getField(strip_tags($_POST['ministryAddress']['line1']))  . "<br/>" .
				    getField(strip_tags($_POST['ministryAddress']['line2']))  . "<br/>" .
				    getField(strip_tags($_POST['ministryAddress']['city']))  . "<br/>" .
				    getField(strip_tags($_POST['ministryAddress']['pr']))  . "<br/>" .
				    getField(strip_tags($_POST['ministryAddress']['country']))  . "<br/>" .
				    getField(strip_tags($_POST['ministryAddress']['pc']))
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
                        // NOTE: I originally had begun to create code that would include changes to
                        // phone numbers in the email of changes. For now, however, we're going to
                        // ignore those changes, and ONLY email notifications for ministry or
                        // personal address changes. I'm only leaving this code here for future
                        // reference, if we decide we want to add it in at some point

                        //// Add to the changes
                        //$changes['Phone'] = array(
                        //    'old' => "None (New Phone)",
                        //    'new' => "
                        //    <strong>" . ($type == 'BUS' ? 'Office' : ucfirst(strtolower($type))) . ": </strong>+" . countryToNumber($country) . " ($area) $number" . (empty($extension) ? "" : "-" . $extension) . 
                        //    "<br />" . ($phoneshare ? "Shared" : "Not Shared") . 
                        //    "<br />" . ($isMinistry ? "Ministry" : "Non-Ministry")
                        //    );
					    //}
					
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
				if (!empty($value['part1'])) {
					$country = strip_tags($value['country']);
					$countrycode = countryToNumber($country);
					$areacode = strip_tags($value['area']);
					$extension = strip_tags($value['ext']);
					$phonetype = strip_tags($value['type']);
					$phonenumber = strip_tags($value['part1']) . '-' . strip_tags($value['part2']);
					
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
							array( 'ministry_website' => strip_tags($_POST['ministryWebsite']),
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
            
            // Add to the changes
            $changes['Personal Address'] = array(
                'old' => 
		            getField($user->address_line1) . " <br/>" .
		            getField($user->address_line2) . " <br/>" .
		            getField($user->city) . " <br/>" .
		            getField($user->province) . " <br/>" .
		            getField($user->country) . " <br/>" .
		            getField($user->postal_code) . " <br/>" .
                    ($user->share_address == 'FULL' ? 'Shared' : 'Not shared'),
                'new' => 
				    getField(strip_tags($_POST['personalAddress']['line1']))  . "<br/>" .
				    getField(strip_tags($_POST['personalAddress']['line2']))  . "<br/>" .
				    getField(strip_tags($_POST['personalAddress']['city']))  . "<br/>" .
				    getField(strip_tags($_POST['personalAddress']['pr']))  . "<br/>" .
				    getField(strip_tags($_POST['personalAddress']['country']))  . "<br/>" .
				    getField(strip_tags($_POST['personalAddress']['pc']))  . "<br/>" .
                    ($_POST['personalAddress']['share'] == 'FULL' ? "Shared" : "Not shared")
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

        // Send email notification for changes
        sendEmail($changes, "$user->first_name $user->last_name");
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
}

function sendEmail($changes, $userName) {
    // If we have changes; don't send any email if there's no changes!
    if (!empty($changes)) {
        if (defined('DEV_ENV') && DEV_ENV) { // We're in dev environment
            $to = "brent.nesbitt@p2c.com";
            $subject = "Staff Directory: User " . $userName . " changed info [Dev Environment]";
        }
        else {
            // TODO: At some point, change to a mailing list (ie, hradmins@p2c.com)
            $to = "Rachel.janz@p2c.com, Leoni.anderson@p2c.com, Cherie.rodway@p2c.com, Marian.ocampo@p2c.com, cam.ludwig@powertochange.org";
            $subject = "Staff Directory: User " . $userName . " changed info";
        }
        // Set up the headers
        $headers = "From: staff-directory@powertochange.org\r\n";
        $headers .= "Reply-To: helpdesk@powertochange.org\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        // Add the message prelude
        $message = "
        <html>
            <head>
                <!-- Set up the style for the email -->
                <style>
                    body {
                        text-decoration:none;
                        font-family: 'Open Sans', sans-serif;
                        color:#4a4d4e;
                    }
                    h1 {
                        color:#ffffff;
                        background-color:#f7941d;
                        padding:5px;
                        line-height:normal;
                        margin:0;
                        font-size:125%;
                    }
                    table {
                        border-collapse: collapse;
                        border: 1px solid #f7941d;
                    }
                    th, td {
                        border: 1px solid #f7941d;
                        padding: 10px;
                    }
                </style>
            </head>
            <body>
                <h1>Changes:</h1>
                <table>
                    <tr>
                        <th>Field changed:</th>
                        <th>Old info:</th>
                        <th>Updated info:</th>
                    </tr>";
        // iterate through each of the changes and add them to the table
        foreach ($changes as $key => $value) {
            $message .= "
            <tr>
                <td>" . $key . "</td>
                <td>" . $value['old'] . "</td>
                <td>" . $value['new'] . "</td>
            </tr>";
        }
        // Add closing tags, and a note so that users understand it might take
        // a bit before they see the changes in HRIS and StudioEnterprise
        $message .= "</table>
                <p><i><b>Note: </b>This address change from the Staff Directory will be
                automatically synced to HRIS and StudioEnterprise within the next
                hour</i></p>
            </body>
        </html>";

        // Do the email sending
        mail($to, $subject, $message, $headers);
    }
} 

// Quick function to return either the field value, or "[Field Empty]"
function getField($field) {
    return empty($field) ? "[Field Empty]" : $field;
} ?>
