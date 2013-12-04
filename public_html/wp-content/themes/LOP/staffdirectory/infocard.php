				<?php
				echo '<h3>' . $user->ministry . '</h3>';
				echo '<p />' . $user->role_title;?>
				<div id="middle">
					<?php
					//these ifs show address based on permissions user set
					echo '<h3>Ministry Information</h3>';
					if(!empty($user->address_line1)){
						echo $user->ministry_address_line1 . "<br/>";
						if (!empty($user->ministry_address_line2))  {
							echo $user->ministry_address_line2 . "<br/>";
						}
						if (!empty($user->ministry_address_line3)) {
							echo $user->ministry_address_line3 . "<br/>"; 
						}
						if (!empty($user->ministry_city)) {
							echo $user->ministry_city . ', ';
						}
						if (!empty($user->ministry_province)) {
							echo $user->ministry_province . ", ";
						}
						if (!empty($user->ministry_postal_code)) {
							echo $user->ministry_postal_code . "<br/>";
						}
						if (!empty($user->minstry_country)) {
							echo $user->ministry_country . "<br/>";
						}
						echo "<br/>";
					}
					$phones = $wpdb->get_results('SELECT * FROM phone_number, employee WHERE employee.external_id = phone_number.employee_id AND phone_number.is_ministry=1 AND phone_number.employee_id = "' . $user->external_id . '"');
					if (!empty($phones)) {
						echo '<h6>phone</h6><ul>';
						foreach ($phones as $phone){
							if($phone->phone_type == 'BUS'){
								$type = 'Office';
							}
							else if($phone->phone_type == 'HOME'){
								$type = 'Home';
							}
							else if($phone->phone_type == 'CELL'){
								$type = 'Cell';
							}
							else if($phone->phone_type == 'FAX'){
								$type = 'Fax';
							}
							else if($phone->phone_type == 'OTHER'){
								$type = 'Other';
							}
							echo '<li>'. $type . ' ('. $phone->area_code . ') ' . $phone->contact_number;
							if (isSet($phone->extension)) {
								echo ' ext:' . $phone->extension;
							}
							echo '</li>';
						}
						echo '</ul>';
					}
					//grab emails that are shared, then display them
					$emails = $wpdb->get_results('SELECT * FROM email_address, employee WHERE employee.external_id = email_address.employee_id AND (email_address.share_email=1 OR email_address.is_ministry=1) AND email_address.is_ministry = 1 AND email_address.employee_id = "' . $user->external_id . '"  ORDER BY is_ministry DESC');
					if (!empty($emails)) {
						echo '<h6>email</h6><ul>';
						foreach ($emails as $email){
								echo '<li>' . $email->email_address . '</li>';
						}
						echo '</ul>';
					}
					if (!empty($user->ministry_website) || !empty($user->ministry_twitter_handle) || !empty($user->ministry_skype) || !empty($user->ministry_facebook)) {
						echo '<h6>social media</h6><ul style="list-style:none;margin-left:0;padding-left:0">';
						if(!empty($user->ministry_website)){
							// a http:// in front of a website let's the browser know it's an absolute address. the user may or may not have
							// included it. So we just make sure it's there
							if(strpos($user->ministry_website, "http")===0){
								echo '<li>Check out my <a href="' . $user->ministry_website . '" target=_blank> ministry website</a></li>';
							}
							else{
								echo '<li>Check out my <a href=http://' . $user->ministry_website . ' target=_blank>ministry 	website</a></li>';
							}
						}
						if(!empty($user->ministry_twitter_handle)){
							echo '<li>Follow my ministry on Twitter: <a href="http://www.twitter.com/' . $user->ministry_twitter_handle . '" target=_blank>'. $user->ministry_twitter_handle . '</a></li>';
						}
						if(!empty($user->ministry_skype)){
							echo '<li>Skype: ' . $user->ministry_skype . '</li>';
						}
						if(!empty($user->ministry_facebook)){
							echo '<li>Facebook: ' . $user->ministry_facebook . '</li>';
						}
						echo '</ul>';
					}
					?>
				</div>
				<div id="far-right">
					<?php
					echo '<h3>Personal Information</h3>';
					if($user->share_address == 'FULL'){
							echo $user->address_line1 . '<br/>';
							if (!empty($user->address_line2)) {
								echo $user->address_line2 . '<br/>';
							}
							if (!empty($user->address_line3)) {
								echo $user->address_line3 . '<br/>';
							}
					}
					if($user->share_address == 'CITY&PROV' || $user->share_address == 'FULL'){
							echo $user->city ;
					}
					if($user->share_address == 'PROVONLY' || $user->share_address == 'CITY&PROV' || $user->share_address == 'FULL'){
							echo ' ' . $user->province ;
					}
					if($user->share_address == 'FULL'){
							echo ' ' . $user->postal_code ;
					}
					if($user->share_address == 'PROVONLY' || $user->share_address == 'CITY&PROV' || $user->share_address == 'FULL'){
							echo '<br/>' . $user->country . '<br/>';
							echo '<br/>';
					}
					if(isset($user->spouse_id)){ //if you're married to someone on staff we link your profiles.
						$spouse = $wpdb->get_row("SELECT * FROM employee WHERE external_id = '" . $user->spouse_id . "'");
							echo 'Spouse: <a href ="?page=profile&person=' . $spouse->user_login . '">' . $spouse->first_name . ' ' . $spouse->last_name . "</a>"; 
					}
					//grab phone numbers that are shared, then display them
					$phones = $wpdb->get_results('SELECT * FROM phone_number, employee WHERE employee.external_id = phone_number.employee_id AND phone_number.share_phone=1 AND phone_number.is_ministry=0 AND phone_number.employee_id = "' . $user->external_id . '"');
					if (!empty($phones)) {
						echo '<h6>phone</h6><ul>';
						foreach ($phones as $phone){
							if($phone->phone_type == 'BUS'){
								$type = 'Office';
							}
							else if($phone->phone_type == 'HOME'){
								$type = 'Home';
							}
							else if($phone->phone_type == 'CELL'){
								$type = 'Cell';
							}
							else if($phone->phone_type == 'FAX'){
								$type = 'Fax';
							}
							else if($phone->phone_type == 'OTHER'){
								$type = 'Other';
							}
							echo '<li>' . $type . ' ('. $phone->area_code . ') ' . $phone->contact_number;
							if (isSet($phone->extension)) {
								echo ' ext:' . $phone->extension;
							}
							echo '</li>';
						}
						echo '</ul>';
					}
					//grab emails that are shared, then display them
					$emails = $wpdb->get_results('SELECT * FROM email_address, employee WHERE employee.external_id = email_address.employee_id AND (email_address.share_email=1 OR email_address.is_ministry=1) AND email_address.is_ministry = 0 AND email_address.employee_id = "' . $user->external_id . '"  ORDER BY is_ministry DESC');
					if (!empty($emails)) {
						echo '<h6>email</h6><ul>';
						foreach ($emails as $email){
								echo '<li>'. $email->email_address . '</li>';
						}
						echo '</ul>';
					}					
					if (!empty($user->website) || !empty($user->twitter_handle) || !empty($user->skype) || !empty($user->facebook)) {
						echo '<h6>social media</h6><ul style="list-style:none;margin-left:0;padding-left:0">';
						//if user has a website, share that too
						if(!empty($user->website)){
							// a http:// in front of a website let's the browser know it's an absolute address. the user may or may not have
							// included it. So we just make sure it's there
							if(strpos($user->website, "http")===0){
								echo '<li>Check out my <a href="' . $user->website . '" target=_blank> website</a></li>';
							}
							else{
								echo '<li>Check out my <a href=http://' . $user->website . ' target=_blank>website</a></li>';
							}
						}
						if(!empty($user->twitter_handle)){
							echo '<li>Follow me on Twitter: <a href="http://www.twitter.com/' . $user->twitter_handle . '" target=_blank>'. $user->twitter_handle . '</a></li>';
						}
						if(!empty($user->skype)){
							echo '<li>Skype: ' . $user->skype . '</li>';
						}
						if(!empty($user->facebook)){
							echo '<li>Facebook: ' . $user->facebook . '<li/>';
						}
						echo '</ul>';
					}
				?>
			</div><!--far right-->
