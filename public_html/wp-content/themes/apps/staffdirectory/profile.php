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
		echo '<h4 style="float:right;position:relative;top:30px;"><a style="color:#adafb2;font-weight:bold;" href= "?page=myprofile">EDIT MY PROFILE</a></h4>';
		
		if(count($_POST) > 0){
			include('update.php');
		}
	}
	else{
		echo '<h4 style="float:right;position:relative;top:30px;"><a  style="color:#adafb2;font-weight:bold;" href= "?page=profile">MY PROFILE</a></h4>';
	} ?>
	
	<BR><BR><BR><BR>
	<hr style='margin-top:0'>
	<div style="clear:both"></div>
	<div id="content-left">
		<div id="main-content">
			<p class='orange-box'><?php	echo "<span style='font-weight:bold;color:#ffffff;font-size:18pt'>".strtoupper ("$user->first_name $user->last_name")."<span style='font-weight:normal;color:#ffffff'> | </span></span>$user->role_title, $user->ministry"; ?></p> <p></p>
			<div style='float:left'>
			<?php if(is_null($user->photo) || $user->share_photo == 0){ //if we don't have a photo or aren't allowed to show it
				echo '<img src="/wp-content/uploads/staff_photos/anonymous.jpg" width=220 />';
			}
			else { //we have a photo and can share it
				echo	 '<img src="/wp-content/uploads/staff_photos/' . $user->photo . '"  width=220 />';
			} ?>
			</div>
			<div style='float:left;padding-left:23px;width:407px'>
			<BR>
			<h4>MINISTRY INFORMATION</h4>
			<BR><p style='margin:0;'>
			<?php 
			if(!empty($user->address_line1)){
				echo "<strong>Address:</strong> $user->ministry_address_line1";
				if (!empty($user->ministry_address_line2))  {
					echo ", $user->ministry_address_line2";
				}
				if (!empty($user->ministry_address_line3)) {
					echo ", $user->ministry_address_line3"; 
				}
				if (!empty($user->ministry_city)) {
					echo ", $user->ministry_city";
				}
				if (!empty($user->ministry_province)) {
					echo ", $user->ministry_province";
				}
				if (!empty($user->ministry_postal_code)) {
					echo ", $user->ministry_postal_code";
				}
				if (!empty($user->minstry_country)) {
					echo ", $user->ministry_country";
				}
				echo "<BR>";
			}
				
			$phones = $wpdb->get_results('SELECT * FROM phone_number, employee WHERE employee.external_id = phone_number.employee_id AND phone_number.is_ministry=1 AND phone_number.employee_id = "' . $user->external_id . '"');
			if (!empty($phones)) {
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
					echo "<strong>" . $type . ':</strong> ('. $phone->area_code . ') ' . $phone->contact_number;
					if (isSet($phone->extension)) {
						echo "-$phone->extension";
					}
					echo '<BR>';
				}
			}
			
			$emails = $wpdb->get_results('SELECT * FROM email_address, employee WHERE employee.external_id = email_address.employee_id AND (email_address.share_email=1 OR email_address.is_ministry=1) AND email_address.is_ministry = 1 AND email_address.employee_id = "' . $user->external_id . '"  ORDER BY is_ministry DESC');
			if (!empty($emails)) {
				foreach ($emails as $email){
						echo "<strong>Email:</strong> $email->email_address<BR>";
				}
			}
			if (!empty($user->ministry_website) || !empty($user->ministry_twitter_handle) || !empty($user->ministry_skype) || !empty($user->ministry_facebook)) {
				if(!empty($user->ministry_website)){
					// a http:// in front of a website let's the browser know it's an absolute address. the user may or may not have
					// included it. So we just make sure it's there
					if(strpos($user->ministry_website, "http")===0){
						echo '<strong>Ministry Website</strong><a href="' . $user->ministry_website . '" target=_blank>$user->ministry_website</a><BR>';
					}
					else{
						echo '<strong>Ministry Website</strong><a href=http://' . $user->ministry_website . ' target=_blank>ministry$user->ministry_website</a><BR>';
					}
				}
				if(!empty($user->ministry_twitter_handle)){
					echo '<strong>Twitter:</strong> <a href="http://www.twitter.com/' . $user->ministry_twitter_handle . '" target=_blank>'. $user->ministry_twitter_handle . '</a><BR>';
				}
				if(!empty($user->ministry_skype)){
					echo '<strong>Skype:</strong> ' . $user->ministry_skype. '<BR>';
				}
				if(!empty($user->ministry_facebook)){
					echo '<strong>Facebook:</strong> ' . $user->ministry_facebook. '<BR>';
				}
			}
			?>
			</p>
			<hr>
			<h4>PERSONAL INFORMATION</h4>
			<BR><p style='margin:0;'>
			<?php
			if($user->share_address == 'FULL'){
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
				if (!empty($user->country	)) {
					echo ", $user->country";
				}
			}
			echo "<BR>";
			if(isset($user->spouse_id)){ //if you're married to someone on staff we link your profiles.
				$spouse = $wpdb->get_row("SELECT * FROM employee WHERE external_id = '" . $user->spouse_id . "'");
				echo '<strong>Spouse:</strong> <a href ="?page=profile&person=' . $spouse->user_login . '">' . $spouse->first_name . ' ' . $spouse->last_name . "</a>"; 
			}
			//grab phone numbers that are shared, then display them
			$phones = $wpdb->get_results('SELECT * FROM phone_number, employee WHERE employee.external_id = phone_number.employee_id AND phone_number.share_phone=1 AND phone_number.is_ministry=0 AND phone_number.employee_id = "' . $user->external_id . '"');
			if (!empty($phones)) {
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
					echo '<strong>' . $type . '</strong> ('. $phone->area_code . ') ' . $phone->contact_number;
					if (isSet($phone->extension)) {
						echo '-' . $phone->extension;
					}
				}
				echo '<BR>';
			}
			//grab emails that are shared, then display them
			$emails = $wpdb->get_results('SELECT * FROM email_address, employee WHERE employee.external_id = email_address.employee_id AND (email_address.share_email=1 OR email_address.is_ministry=1) AND email_address.is_ministry = 0 AND email_address.employee_id = "' . $user->external_id . '"  ORDER BY is_ministry DESC');
			if (!empty($emails)) {
				foreach ($emails as $email){
						echo '<strong>Email:</strong> '. $email->email_address . '<BR>';
				}
			}					
			if (!empty($user->website) || !empty($user->twitter_handle) || !empty($user->skype) || !empty($user->facebook)) {
				//if user has a website, share that too
				if(!empty($user->website)){
					// a http:// in front of a website let's the browser know it's an absolute address. the user may or may not have
					// included it. So we just make sure it's there
					if(strpos($user->website, "http")===0){
						echo '<strong>Website:</strong> <a href="' . $user->website . '" target=_blank>$user->website </a><BR>';
					}
					else{
						echo '<strong>Website:</strong> <a href=http://' . $user->website . ' target=_blank>$user->website </a><BR>';
					}
				}
				if(!empty($user->twitter_handle)){
					echo '<strong>Twitter:</strong> <a href="http://www.twitter.com/' . $user->twitter_handle . '" target=_blank>'. $user->twitter_handle . '</a><BR>';
				}
				if(!empty($user->skype)){
					echo '<strong>Skype:</strong> ' . $user->skype . '<BR>';
				}
				if(!empty($user->facebook)){
					echo '<strong>Facebook:</strong> ' . $user->facebook . '<BR>';
				}
			}
			?>
			<p style='margin:  5px 0'>
			A personal message:
			<?php echo $user->notes ?>
			</p>
			</div><div style='clear:both;'></div>
		</div>
	</div>
	<div id="content-right">   
		<?php include('pro_sidebar.php') ?>
	</div><div style='clear:both;'></div>
