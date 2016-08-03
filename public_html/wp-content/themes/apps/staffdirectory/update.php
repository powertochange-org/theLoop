<?php include 'countryToNumber.php';
		//$test = $wpdb->get_row('SELECT * from approval_address_change where user_login="' . $user->user_login . '"');
		//if(!isset($test->user_login)){ //we use updates to update the sync table. so if they don't have a sync record just make a blank one
		//	$wpdb->insert( 'approval_address_change', array( 'user_login' => $user->user_login)); 
		//}

        // This keeps track of all the changes that were made that require
        // notifications, so we can send out an email with the changes
        $changes = array();
        
        // These is a variable to track all needed changes to the employee
        // table, so we only have to update it once
        $employeeChanges = array();

        // Wordpress by default adds slashes to escape strings. How we are using
        // them, however, prefers that they are not escaped
        $_POST = stripslashes_deep($_POST);

        if(is_uploaded_file($_FILES['file']['tmp_name'])) { // If we have a new photo
            include ('upload.processor.php');
        }
		
		//all these ifs check if the user changed something. then it updates the database (including to a sync table so we can send it back to HRIS
		// Remove Photo
		if($_POST['deleteImage'] == 1){ //user clicked remove photo button
            // Update the row, setting share_photo to 0 and photo to NULL. 
            // The standard $wpdb->update call doesn't work with nulls, so this
            // is the only way to insert a null into the table; it should be
            // safe though, because there are no user-input parameters.
            $wpdb->query("UPDATE employee set share_photo = 0, photo = NULL where user_login = '$current_user->user_login'");
		}
		
		//Ministry Address
		
		//checking if anything is different
		if (strip_tags($_POST['ministryAddress']['line1']) != $user->ministry_address_line1 
				|| strip_tags($_POST['ministryAddress']['line2']) != $user->ministry_address_line2
				|| strip_tags($_POST['ministryAddress']['city']) != $user->ministry_city
				|| strip_tags($_POST['ministryAddress']['pr']) != $user->ministry_province
				|| strip_tags($_POST['ministryAddress']['country']) != $user->ministry_country
				|| strip_tags($_POST['ministryAddress']['pc']) != $user->ministry_postal_code){
				
			// Store all of the changes needed
			$employeeChanges['ministry_address_line1'] = strip_tags($_POST['ministryAddress']['line1']);
			$employeeChanges['ministry_address_line2'] = strip_tags($_POST['ministryAddress']['line2']);
			$employeeChanges['ministry_city'] = strip_tags($_POST['ministryAddress']['city']);
			$employeeChanges['ministry_province'] = strip_tags($_POST['ministryAddress']['pr']);
			$employeeChanges['ministry_country'] = strip_tags($_POST['ministryAddress']['country']);
			$employeeChanges['ministry_postal_code'] = strip_tags($_POST['ministryAddress']['pc']);

			// Add to the changes for the email
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
		if($_POST['phone'] != NULL) {
            foreach($_POST['phone'] as $key => $value){
                if ($key >= 0) {
                    $id = $key;
                    $phones = $wpdb-> get_results("SELECT * FROM phone_number WHERE phone_number_id = '" . $id . "'");
                    $phone = $phones[0];
                    $phoneshare = 0;
                    $isMinistry = 0;
                    if ($value['share'] == 'personalshare') {
                        $phoneshare = 1;
                    } elseif ($value['share'] == 'ministryshare' || $phone->is_ministry == 1) {
                        continue; //Skip to the next personal address
                    }
                    
                    if ($phoneshare != $phone->share_phone) {
                        $wpdb->insert( 'sync',
                                array(  'table_name'    => 'phone_number',
                                        'record_id'     => $phone->phone_number_id,
                                        'sync_action'   => 'update',
                                        'changed_date'  =>  date('Y-m-d H-i-s'),
                                        'user_login'    => $user->user_login
                                ));
                        $wpdb->update( 'phone_number', 
                                array( 'share_phone' => $phoneshare),
                                array(  'phone_number_id' => $id));
                    }
                     
                }
            } 
        }
		
		//Spouse Employee Number
		if (strip_tags($_POST['spouseEmployeeNumber']) != $user->spouse_employee_number ){

			if (!strip_tags($_POST['spouseEmployeeNumber'])) {
				$spouse_employee_number = NULL;
			}
			else {
				$spouse_employee_number = strip_tags($_POST['spouseEmployeeNumber']);
			}

			$wpdb->update( 'employee', 
				array( 'spouse_employee_number' => $spouse_employee_number),
				array(	'employee_number' => $user->employee_number));
		}
		
		//Email
        if($_POST['email'] != NULL) {
    		foreach($_POST['email'] as $key => $value){
    		
    			//if add new
    			if ($key >= 0) {
    				$id = $key;
    				$emails = $wpdb-> get_results("SELECT * FROM email_address WHERE email_address_id = '" . $id . "'");
    				$email = $emails[0];
    				
    				if ($email->is_ministry == 1) {
    					continue;
    				} else {
    					//$ministry = '0';
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
    				if ($shared != $email->share_email) {
                        // Let the user know if the type of email was automatically changed
                        //echo getEmailNoticeChange($email->is_ministry, $ministry, $address);
    							
    					$wpdb->update( 'email_address', 
    							array( 'share_email' => $shared),
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
				|| strip_tags($_POST['ministryFacebook']) != $user->ministry_facebook
                || strip_tags($_POST['ministryInstagram']) != $user->ministry_instagram){
			//$wpdb->insert( 'sync',
			//				array(  'table_name'    => 'employee',
			//						'record_id'     => $user->external_id,
			//						'sync_action'   => 'update',
			//						'field_changed' => 'ministry_social_media',
			//						'changed_date'	=>	date('Y-m-d H-i-s'),
			//						'user_login'	=> $user->user_login
			//				));
			$employeeChanges['ministry_website'] = strip_tags($_POST['ministryWebsite']);
			$employeeChanges['ministry_twitter_handle'] = strip_tags($_POST['ministryTwitter']);
			$employeeChanges['ministry_skype'] = strip_tags($_POST['ministrySkype']);
            $employeeChanges['ministry_facebook'] = strip_tags($_POST['ministryFacebook']);
			$employeeChanges['ministry_instagram'] = strip_tags($_POST['ministryInstagram']);
		}
		
		
		
		//Personal Address
		
		//checking if anything is different
		if ($_POST['personalAddress']['share'] != $user->share_address) {
			$wpdb->insert( 'sync',
				array(  'table_name'    => 'employee',
						'record_id'     => $user->external_id,
						'sync_action'   => 'update',
						'field_changed' => 'personal_address',
						'changed_date'	=>	date('Y-m-d H-i-s'),
						'user_login'	=> $user->user_login
				));
			
			$employeeChanges['share_address'] = $_POST['personalAddress']['share'];
            
            // Add to the changes
            $changes['Personal Address'] = array(
                'old' => 
                    ($user->share_address == 'FULL' ? 'Shared' : 'Not shared'),
                'new' => 
                    ($_POST['personalAddress']['share'] == 'FULL' ? "Shared" : "Not shared")
                );
		}
		
		//Personal Social Media
		
		//checking if anything is different
		if (strip_tags($_POST['personalWebsite']) != $user->website 
				|| strip_tags($_POST['personalTwitter']) != $user->twitter_handle
				|| strip_tags($_POST['personalSkype']) != $user->skype
				|| strip_tags($_POST['personalFacebook']) != $user->facebook
                || strip_tags($_POST['personalInstagram']) != $user->instagram){
			$employeeChanges['website'] = strip_tags($_POST['personalWebsite']);
			$employeeChanges['twitter_handle'] = strip_tags($_POST['personalTwitter']);
			$employeeChanges['skype'] = strip_tags($_POST['personalSkype']);
            $employeeChanges['facebook'] = strip_tags($_POST['personalFacebook']);
			$employeeChanges['instagram'] = strip_tags($_POST['personalInstagram']);
			//$wpdb->insert( 'sync',
			//				array(  'table_name'    => 'employee',
			//						'record_id'     => $user->external_id,
			//						'sync_action'   => 'update',
			//						'field_changed' => 'ministry_social_media',
			//						'changed_date'	=>	date('Y-m-d H-i-s'),
			//						'user_login'	=> $user->user_login
			//				));
		}
		
		//Personal Note
		if(substr(strip_tags($_POST['notes'],"<b></b><br><br/><hr><hr/><p><p/>"),0,8000) != $user->notes){
			$wpdb->insert( 'sync',
							array(  'table_name'    => 'employee',
									'record_id'     => $user->external_id,
									'sync_action'   => 'update',
									'field_changed' => 'notes',
									'changed_date'	=>	date('Y-m-d H-i-s'),
									'user_login'	=> $user->user_login
							));
			
			
			
			$employeeChanges['notes'] = substr(strip_tags($_POST['notes'],"<b></b><br><br/><hr><hr/><p><p/>"),0,8000);
		}

        // If we have changes to the employee table
        if (!empty($employeeChanges)) {
            // Update the employee table with all of the changes we have collected
		    $wpdb->update(	'employee',
		    				$employeeChanges,
                            array( 'user_login' => $current_user->user_login  )
		    			);
        }

        // Send email notification for changes
        //sendEmail($changes, "$user->first_name $user->last_name");
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
            $to = "jason.brink@p2c.com";
            $subject = "Staff Directory: User " . $userName . " changed info [Dev Environment]";
        }
        else {
            $to = "staff_address_change@p2c.com";
            $subject = "Staff Directory: User " . $userName . " changed info";
        }
        // Set up the headers
        $headers = "From: Staff Directory <helpdesk@p2c.com>\r\n";
        $headers .= "Reply-To: helpdesk@p2c.com\r\n";
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
}

// A function that will return an email notice message to the page in the event
// that the system automatically added a personal email address as a ministry,
// or vice versa. Doesn't return anything otherwise
function getEmailNoticeAdd($key, $ministry, $address) {
    // It's a ministry email, but it was added as a personal
    if ($ministry == 1 && $key == -2) {
        return "<br/><br/><br/><br/><p class='orange-box' style='margin-bottom:
        -50px; background-color: rgb(229, 226, 5);'>NOTE: The address
        '$address' was recognized as a ministry address, and has been added as
        one</p>"; 
    } else if ($ministry == 0 && $key == -1) { // Not ministry, but added as one 
        return "<br/><br/><br/><br/><p class='orange-box'
        style='margin-bottom: -50px; background-color: rgb(229, 226, 5);'>NOTE:
        The address '$address' was not recognized as a ministry address, so it
        was added as a personal address</p>";
    }
    return; // Don't return anything if no changes were made
} 

// A function to return a notice to the page if the email address type
// was automatically changed
function getEmailNoticeChange($orig_ministry, $new_ministry, $address) {
    // Used to be ministry; isn't anymore
    if ($orig_ministry > $new_ministry) {
        return "<br/><br/><br/><br/><p class='orange-box'
        style='margin-bottom: -50px; background-color: rgb(229, 226, 5);'>NOTE:
        The address '$address' was not recognized as a ministry address, so it
        was changed to a personal address</p>";
    } else if ($new_ministry > $orig_ministry) { // Used to be personal, now ministry
        return "<br/><br/><br/><br/><p class='orange-box'
        style='margin-bottom: -50px; background-color: rgb(229, 226, 5);'>NOTE:
        The address '$address' was recognized as a ministry address, so it
        was automatically changed to one</p>";
    }
    return; // Don't return anything if no changes were made
} ?>
