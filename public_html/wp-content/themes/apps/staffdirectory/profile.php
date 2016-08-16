<?php
/**
* Profile
* -This file is included to show another user's profile.
*
*
**/
$profile = $_GET['person']; //grab from URL the profile we want
$user = $wpdb->get_row("SELECT * FROM employee WHERE user_login = '" . $profile . "'"); //go to DB and get 
?>

	<p/>
	<?php 
	$current_user = wp_get_current_user();
	if(!isset($profile) || $current_user->user_login == $profile){
		echo '<h4 class="profile"><a class="profile-link" href= "?page=myprofile">EDIT MY PROFILE</a></h4>';
		
		if(count($_POST) > 0){
			include('update.php');
		}
	}
	else{
		echo '<h4 class="profile"><a class="profile-link" href= "?page=profile">MY PROFILE</a></h4>';
	} ?>
	
	<BR><BR><BR><BR>
	<hr style='margin-top:0'>
	<div style="clear:both"></div>
	<div id="content-left">
		<div id="main-content">
			<p class='orange-box'><?php	echo "<span style='font-weight:bold;color:#ffffff;font-size:16pt'>".strtoupper ("$user->first_name $user->last_name")."<span style='font-weight:normal;color:#ffffff'> | </span></span>$user->role_title, $user->ministry"; ?></p> <p></p>
			<div class="profile-image" style='float:left'>
			<?php if(is_null($user->photo) || $user->share_photo == 0){ //if we don't have a photo or aren't allowed to show it
                // Attempt to use their public giving site photo
                $url = "http://secure.powertochange.org/images/Product/medium/" . $user->staff_account . ".jpg";
                // Check to see if that url is valid
                // Use curl to test validity of URL; init the handle
                $handle = curl_init($url);
                // Set the option so that it returns the response to a variable
                // (which we don't actually use), as opposed to printing out the
                // response to the page
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
                // Actually try to get the response from the url
                curl_exec($handle);
                // Check the response code
                if (curl_getinfo($handle, CURLINFO_HTTP_CODE) != 200) {
                    // It's INVALID (ie, user doesn't have a giving site image)
                    // Use the standard image
                    $url = "/wp-content/uploads/staff_photos/anonymous.jpg";
                } 
				echo '<img src="'. $url . '" width=220 />';
			}
			else { //we have a photo and can share it
				echo	 '<img src="/wp-content/uploads/staff_photos/' . $user->photo . '"  width=220 />';
			} ?>
			</div>
			<div class="profile-content">
			<h4>MINISTRY INFORMATION</h4>
			<BR>
			<p style='margin:0;'>
			<?php 
			if(!empty($user->ministry_address_line1) || !empty($user->ministry_city)){
				echo "<strong>Address:</strong> ";
                // If we have the first line
                if (!empty($user->ministry_address_line1))  {
					echo $user->ministry_address_line1;
					if (!empty($user->ministry_address_line2)) { echo ", $user->ministry_address_line2";}
					if (!empty($user->ministry_address_line3)) { echo ", $user->ministry_address_line3";}
					if (!empty($user->ministry_city)) { echo ", $user->ministry_city";}
					if (!empty($user->ministry_province)) { echo ", $user->ministry_province";}
					if (!empty($user->ministry_postal_code)) { echo ", $user->ministry_postal_code";}
				}
				else { // We don't have the first line, meaning we do have the city
					echo "$user->ministry_city";
					if (!empty($user->ministry_province)) { echo ", $user->ministry_province";}
                }
				if (!empty($user->ministry_country) &&($user->ministry_country <> 'CA')) { echo ", $user->ministry_country";}
				echo "<BR>";
			}
				
			$phones = $wpdb->get_results('SELECT * FROM phone_number WHERE is_ministry=1 AND employee_number = "' . $user->employee_number . '"');
			if (!empty($phones)) {
				foreach ($phones as $phone){

					echo "<strong>".$phone->phone_type .": </strong>".$phone->phone_number;
                    // Make sure we have an extension before adding the dash
					if (isSet($phone->extension) && !empty($phone->extension)) {
						echo " EXT: $phone->extension";
					}
					echo '<BR>';
				}
			}
			
			$emails = $wpdb->get_results('SELECT * FROM email_address WHERE is_ministry = 1 AND employee_number = "' . $user->employee_number . '"  ORDER BY is_ministry DESC');
			if (!empty($emails)) {
				foreach ($emails as $email){
						echo "<strong>Email:</strong> <a href=\"mailto:".$email->email_address."\">".$email->email_address."</a><BR>";
				}
			}
			if (!empty($user->ministry_website) || !empty($user->ministry_twitter_handle) || !empty($user->ministry_skype) || !empty($user->ministry_facebook)) {
				if(!empty($user->ministry_website)){
					// a http:// in front of a website let's the browser know it's an absolute address. the user may or may not have
					// included it. So we just make sure it's there
					if(strpos($user->ministry_website, "http")===0){
						echo '<strong>Ministry Website:</strong> <a href="' . $user->ministry_website . '" target=_blank>' . $user->ministry_website . '</a><BR>';
					}
					else{
						echo '<strong>Ministry Website:</strong> <a href=http://' . $user->ministry_website . ' target=_blank>' . $user->ministry_website . '</a><BR>';
					}
				}
				if(!empty($user->ministry_twitter_handle)){
					echo '<strong>Twitter:</strong> <a href="http://www.twitter.com/' . $user->ministry_twitter_handle . '" target=_blank>'. $user->ministry_twitter_handle . '</a><BR>';
				}
				if(!empty($user->ministry_skype)){
					echo '<strong>Skype:</strong> ' . $user->ministry_skype. '<BR>';
				}
				if(!empty($user->ministry_facebook)){
					echo '<strong>Facebook:</strong> <a href="http://www.facebook.com/' . $user->ministry_facebook . 
					'" target=_blank>' . $user->ministry_facebook . '</a><BR>';
				}
				if(!empty($user->ministry_instagram)){
					echo '<strong>Instagram:</strong> <a href="http://www.instagram.com/' . $user->ministry_instagram . 
					'" target=_blank>' . $user->ministry_instagram . '</a><BR>';
				}
			}
			if(isset($user->spouse_employee_number)){ //if you're married to someone on staff we link your profiles.
				$spouse = $wpdb->get_row("SELECT * FROM employee WHERE employee_number = '" . $user->spouse_employee_number . "'");
				if(isset($spouse->employee_number)){
					echo '<br><strong>Spouse:</strong> <a href ="?page=profile&person=' . $spouse->user_login . '">' . $spouse->first_name . ' ' . $spouse->last_name . "</a>"; 
				}
			}
			?>
			</p>
			<hr>
			<h4>PERSONAL INFORMATION</h4>
			<p style='margin:0;'>
			<?php
			/* Ensure user wants to share address, and has at least the first line */
			if($user->share_address == 'FULL' && (!empty($user->address_line1))){
				echo "<strong>Address:</strong> $user->address_line1";
				if (!empty($user->address_line2)) {
					echo ", $user->address_line2";
				}
				if (!empty($user->address_line3)) {
					echo ", $user->address_line3";
				}
				if (!empty($user->city)) {
					echo ", $user->city";
				}
				if (!empty($user->province)) {
					echo ", $user->province";
				}
				if (!empty($user->postal_code)) {
					echo ", $user->postal_code";
				}
				if (!empty($user->country) && ($user->country <> 'CA')) {
					echo ", $user->country";
				}
			}
			else {
				if (!empty($user->city)) {
					echo "$user->city";
				}
				if (!empty($user->province)) {
					echo ", $user->province";
				}
				if (!empty($user->country) && ($user->country <> 'CA')) {
					echo ", $user->country";
				}
			}
			echo"<br>";

			//grab phone numbers that are shared, then display them
			$phones = $wpdb->get_results('SELECT * FROM phone_number WHERE share_phone=1 AND is_ministry=0 AND employee_number = "' . $user->employee_number . '"');
			if (!empty($phones)) {
				foreach ($phones as $phone){

					echo '<strong>' . $phone->phone_type . ': </strong> '.$phone->phone_number;
                    // Make sure we have an extension before adding the dash
					if (isSet($phone->extension) && !empty($phone->extension)) {
						echo ' EXT: '.$phone->extension;
					}
				    echo '<BR>';
				}
			}
			//grab emails that are shared, then display them
			$emails = $wpdb->get_results('SELECT * FROM email_address WHERE share_email=1 AND is_ministry = 0 AND employee_number = "' . $user->employee_number . '"  ORDER BY is_ministry DESC');
			if (!empty($emails)) {
				foreach ($emails as $email){
					echo "<strong>Email:</strong> <a href=\"mailto:".$email->email_address."\">".$email->email_address."</a><BR>";
				}
			}
			if (!empty($user->website) || !empty($user->twitter_handle) || !empty($user->skype) || !empty($user->facebook)) {
				//if user has a website, share that too
				if(!empty($user->website)){
					// a http:// in front of a website let's the browser know it's an absolute address. the user may or may not have
					// included it. So we just make sure it's there
					if(strpos($user->website, "http")===0){
						echo '<strong>Website:</strong> <a href="' . $user->website . '" target=_blank>' . $user->website . '</a><BR>';
					}
					else{
						echo '<strong>Website:</strong> <a href=http://' . $user->website . ' target=_blank>' . $user->website . '</a><BR>';
					}
				}
				if(!empty($user->twitter_handle)){
					echo '<strong>Twitter:</strong> <a href="http://www.twitter.com/' . $user->twitter_handle . '" target=_blank>'. $user->twitter_handle . '</a><BR>';
				}
				if(!empty($user->skype)){
					echo '<strong>Skype:</strong> ' . $user->skype . '<BR>';
				}
				if(!empty($user->facebook)){
					echo '<strong>Facebook:</strong> <a href="http://www.facebook.com/' . $user->facebook . 
					'" target=_blank>' . $user->facebook . '</a><BR>';
				}
				if(!empty($user->instagram)){
					echo '<strong>Instagram:</strong> <a href="http://www.instagram.com/' . $user->instagram . 
					'" target=_blank>' . $user->instagram . '</a><BR>';
				}
			}
			?>
            <?php if (!empty($user->notes)){
                echo "<p style='margin:  5px 0'><strong>About me:</strong><br />" . nl2br($user->notes) . "</p>";
            } ?>
			</div><div style='clear:both;'></div>
		</div>
	</div>
	<div id="content-right" class="staff-directory-sidebar staff-directory download">   
		<?php include('pro_sidebar.php') ?>
	</div><div style='clear:both;'></div>
