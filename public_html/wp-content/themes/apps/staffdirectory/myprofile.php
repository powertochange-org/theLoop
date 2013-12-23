<?php
/**
* MyProfile
*
* This is used when a user looks at their own profile in the Staff Address book project. 
* This is where users can view, change, update, etc their information. They can even throw
* a pic on their, too.
*
*
**/
	//var_dump($_POST);//this is super helpful for debugging/building anything with the forms
	include 'countryToNumber.php';
	$current_user = wp_get_current_user();
	$user = $wpdb->get_row("SELECT * FROM employee WHERE user_login = '" . $current_user->user_login . "'");
	
	
// make a note of the current working directory relative to root. 
$directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']); 
                                                     
// make a note of the location of the upload handler script 
$uploadHandler = 'http://' . $_SERVER['HTTP_HOST'] . $directory_self . 'upload.processor.php'; 

// set a max file size for the html upload form 
$max_file_size = 30000000; // size in bytes 

	//
	// Process POST information if any
	//
	$null = "NULL";
	if(isset($_POST)){
		include('update.php');
	}
?>
	<style type="text/css">
			#main-content div.form {
				margin-bottom:10px;
				padding-top:5px;
			}
			input, select {	
				margin-bottom:5px;
			}
			
			.plus{
				position:absolute;
				left:-7px;
				top:-7px;' 
			}
			
			.orange{
				background-color:#f7941d;
				border:0px solid #000000;
				color:#ffffff;
				border-radius:5px;
				padding:3px 6px;
				font-size:12px;
			}
			
			#main-content form input[type=text], #main-content form input[type=textbox], #main-content form select{
				background-color:#f4f4f4;
				border:solid #adafb2 1px;
				margin:2px;
				font-size:12px;
				height:27px;
			}
			
			#main-content form input[type=text], #main-content form input[type=textbox]{
				padding-left:5px;
			}
			
			#main-content form {
				font-size:12px;
			}
		</style>
		<!-- MAIN DISPLAY -->
		
		<p/><h4 style="float:right;color:#adafb2;"><a href= "?page=profile">MY PROFILE</a></h4><BR>
	<hr>
	<div style="clear:both"></div>
	<div id="content-left">
		<div id="main-content">
			<p class='orange-box'><?php	echo "<span style='font-weight:bold;color:#ffffff;'>".strtoupper ("$user->first_name $user->last_name")."</span> | $user->role_title, $user->ministry"; ?></p> <p></p>
			<div style='float:left'>
				<?php if(is_null($user->photo)){ //if we don't have a photo or aren't allowed to show it
				echo '<img style="display:block" src="../../wp-content/uploads/staff_photos/anonymous.jpg" width=220 />';
				}
				else { //we have a photo and can share it
					echo '<img style="display:block" src="../wp-content/uploads/staff_photos/' . $user->photo . '"  width=220 />';
				} ?>
				
				<form style=" width:220px" id="Upload" action="../staff-directory/?page=upload_processor" enctype="multipart/form-data" method="post">	
					<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size ?>"> 
					<input style=" width:220px" id="file" type="file" name="file" onchange="$('#submitpic').removeAttr('disabled')"><BR>
					<input class='orange' id="submitpic" type="submit" name="submit" disabled="disabled" value="CHANGE IMAGE">       
				</form>
				
			</div>
			
			<div style='float:left;padding-left:23px;width:457px'>
			
			<form action="" method="post" enctype="multitype/form-data">
			<h4>MINISTRY INFORMATION</h4>	
			<div class='form'>
				<span style='font-weight:600;'>Address:</span>
				<input type="hidden" name="ministryAddress">
				<input type="textbox" placeholder='Address Line #1' name="ministry_address1" value="<?php echo $user->ministry_address_line1 ?>" style='width:205px;'>
				<input type="textbox" placeholder='Address Line #2' name="ministry_address2" value="<?php echo $user->ministry_address_line2 ?>" title='(Only needed if you have a PO Box or RR number)' style='width:172px;'>
				<?php if(is_null($user->ministry_address_line3)){ ?>
					<input type="hidden" name="ministry_address3" value="<?php echo $user->ministry_address_line3 ?>">
				<?php } ?>
				<input type="textbox" placeholder='City' name="ministry_city_value" value="<?php echo $user->ministry_city ?>" style='width:152px;'>
				<input type="textbox" placeholder='Pr.'  name="ministry_province_value" value="<?php echo $user->ministry_province ?>" maxlength="2" size='2'>
				<input type="textbox" placeholder='Country' name="ministry_country" value="<?php echo $user->ministry_country ?>" maxlength="2" size='2'>
				<input type="textbox" placeholder='PC' name="ministry_postal_code" value="<?php echo $user->ministry_postal_code ?>" style='width:172px;'>
			</div>
			
			
			<?php
			$phones	 = $wpdb-> get_results("SELECT * FROM phone_number WHERE employee_id = '" . $user->external_id . "' AND is_ministry='1' ORDER BY share_phone DESC");
			if($phones){
				$last = end($phones);
				foreach($phones as $phone){
					$isLast = $last === $phone;
					$id = $phone->phone_number_id;
					$contact = split("-", $phone->contact_number, 2);
					if ($isLast){
						echo "<div style='position:relative;'>";
					}
					echo '<div class="form" id="editPhone' . $id . '" action="" method="post" enctype="multipart/form-data">';
			?>
					<span style='font-weight:600;'>Phone: </span>
					<input type="hidden" name="editPhone_min" value="<?php echo $id; ?>">
					<input type="text" placeholder='Country' name="phonecountry_min" value="<?php echo $phone->country_code ?>" maxlength="3" style="width:52px" />
					<input type='hidden' name='phoneShare_min' value='ministryshare' >
					<input type='hidden' name='phonetype_min' value='BUS' >
					(   <input type="text" placeholder='Area' name="phonearea_min" value="<?php echo $phone->area_code ?>" maxlength="3" style="width:52px" /> )
					 <input type="text" name="phonenumber1_min" value="<?php echo $contact[0] ?>" maxlength="3" style="width:52px" />
					  -   <input type="text" name="phonenumber2_min" value="<?php echo $contact[1] ?>" maxlength="4"style="width:60px" />
					 <input type="text" placeholder='Ext' name="phoneextension_min" value="<?php echo $phone->extension ?>" maxlength="10" style="width:110px" />
			 <?php
					echo '</div>';
					if ($isLast){
						echo "<img class='false-link plus' src='".get_stylesheet_directory_uri()."/res/plus.png' width='14' height='14' onclick='$(\"#min_phone\").slideToggle()'>";
						echo "</div>";
					}
				}
			}
			?> 
			
			<div class="form" id="min_phone" <?php echo ($phones ? "style='display:none'" : "") ?> >
				<input type="hidden" name="new_phone_number_min">
				<input type='hidden' name='phoneShare_min' value='ministryshare' >
				<input type='hidden' name='phonetype_min' value='BUS' >
				<?php $width = 245; require("countrycodes.php");  ?>
				 (<input type="text" placeholder='Area' name="phonearea_min" value="" maxlength="3" style="width:30px" />)
				 <input type="text" name="phonenumber1_min" value="" maxlength="3" style="width:30px" />
				 -<input type="text" name="phonenumber2_min" value="" maxlength="4" style="width:40px" />
				 <input type="text"  placeholder='Ext' name="phoneextension_min" value="" maxlength="10" style="width:40px" />
			</div>
			
			
			<?php
			$emails	 = $wpdb-> get_results("SELECT * FROM email_address WHERE employee_id = '" . $user->external_id . "' AND is_ministry='1'");
			if($emails){
				$last = end($emails);
				foreach($emails as $email){
					$isLast = $last === $email;
					if ($isLast){
						echo "<div style='position:relative;'>";
					}
					$id = $email->email_address_id;
					//don't allow editing or deleting of powertochange.org address
					if(strpos(strtolower($email->email_address),'powertochange.org') === false) {
						echo '<div class="form" id="editMinEmail' . $id . '">';
						echo '<span style="font-weight:600;">Ministry Email: </span>';
						echo '<input type="hidden" name="email_address_id" value="' . $id . '">';
						echo '<input type="text" name="minEmail" value="' . $email->email_address . '" style="width:446px"/>';
					}
					else{
						?><div><span style='font-weight:600;'>Ministry Email: </span><input type="text" value='<?php echo $email->email_address; ?>' disabled style="width:359px"/></div><?php
					}
					if ($isLast){
						echo "<img class='false-link plus' src='".get_stylesheet_directory_uri()."/res/plus.png' width='14' height='14' onclick='$(\"#addMinEmail\").slideToggle()'>";
						echo "</div>";
					}
				}  	
			}
			?>
			<div class="form" id="addMinEmail" <?php echo ($emails ? "style='display:none'" : "") ?> >
				<input type="text"  placeholder='Ministry Email' name="new_min_email_address" style="width:446px"/>
			</div>
			<div class="form" id="editMinSocialMedia">
				<input type="hidden" name="minSocialMedia">
					<input type="text" placeholder='Website' id="minwebsite" name="website" value="<?php echo $user->ministry_website ?>" style="width:446px"><BR>
					<input type="text" placeholder='Twitter' id="mintwitter" name="twitter" value="<?php echo $user->ministry_twitter_handle ?>" style="width:446px"><BR>
					<input type="text" placeholder='Skype' id="minskype" name="skype" value="<?php echo $user->ministry_skype ?>" style="width:446px"><BR>
					<input type="text" placeholder='Facebook' id="minfacebook" name="facebook" value="<?php echo $user->ministry_facebook ?>" style="width:446px">
			</div>
			<h4>PERSONAL INFORMATION</h4>
			<div class="form">
				<span style='font-weight:600;'>Address: </span>
				<input type="hidden" name="personalAddress">
				<input type="textbox" placeholder='Address Line #1' name="address1" value="<?php echo $user->address_line1 ?>" style="width:205px">
				<input type="textbox" placeholder='Address Line #2' name="address2" value="<?php echo $user->address_line2 ?>" title='(Only needed if you have a PO Box or RR number)' style="width:172px" >
				<?php if(is_null($user->address_line3)){ ?>
					<input type="hidden" name="address3" value="<?php echo $user->address_line3 ?>"> 
				<?php } ?>
				<input type="textbox"placeholder='City' name="city_value" value="<?php echo $user->city ?>" style="width:130px"> 
				<input type="textbox" placeholder='Pr.' name="province_value" value="<?php echo $user->province ?>" maxlength="2" style="width:30px">
				<input type="textbox" placeholder='Country' name="country" value="<?php echo $user->country ?>" maxlength="2" style="width:30px">
				<input type="textbox" placeholder='PC' name="postal_code" value="<?php echo $user->postal_code ?>" style="width:110px">
				<select name="addressPermissions">
					<option value="FULL" <?php if($user->share_address == 'FULL') { echo 'selected'; } ?>>Shared</option>
					<option value="NONE" <?php if($user->share_address == 'NONE') { echo 'selected'; } ?>>Not Shared</option>
				</select>
			</div>
			
			
			<?php
			$phones	 = $wpdb-> get_results("SELECT * FROM phone_number WHERE employee_id = '" . $user->external_id . "' AND is_ministry='0' ORDER BY share_phone DESC");
			if($phones){
				$last = end($phones);
				foreach($phones as $phone){
					$isLast = $last === $phone;
					if ($isLast){
						echo "<div style='position:relative;'>";
					}
					$id = $phone->phone_number_id;
					$contact = split("-", $phone->contact_number, 2);
					echo '<div class="form" id="editPhone' . $id . '">';
					?>
					<span style='font-weight:600;'>Phone:</span>
					<input type="hidden" name="editPhone" value="<?php echo $id; ?>">
					<input type="text" name="phonecountry" value="<?php echo $phone->country_code ?>" maxlength="3" style="width:30px" />
					<select name="phoneShare" >
						<option value="personalshare" <?php if ($phone->share_phone) { echo 'selected="selected"'; } ?>> Shared</option>
						<option value="personalnotshare" <?php if (!$phone->share_phone) {echo 'selected="selected"'; } ?>> Not Shared</option>
					</select>
					<select name="phonetype">
						<option value="CELL" <?php if ($phone->phone_type == 'CELL') { echo 'selected="selected"'; } ?>>Cell</option>
						<option value="HOME" <?php if ($phone->phone_type == 'HOME') { echo 'selected="selected"'; } ?>>Home</option>
						<option value="FAX" <?php if ($phone->phone_type == 'FAX') { echo 'selected="selected"'; } ?>>Fax</option>
						<option value="OTHER" <?php if ($phone->phone_type == 'OTHER') { echo 'selected="selected"'; } ?>>Other</option>
					</select>
					(<input type="text" name="phonearea" value="<?php echo $phone->area_code ?>" maxlength="3" style="width:27px" />)
					 <input type="text" name="phonenumber1" value="<?php echo $contact[0] ?>" maxlength="3" style="width:27px" />
					 -<input type="text" name="phonenumber2" value="<?php echo $contact[1] ?>" maxlength="4" style="width:35px" />
					 <input type="text" placeholder="Ext." name="phoneextension" value="<?php echo $phone->extension ?>" maxlength="10" style="width:50px" />
					 <?php
					echo '</div>';
					if ($isLast){
						echo "<img class='false-link plus' src='".get_stylesheet_directory_uri()."/res/plus.png' width='14' height='14' onclick='$(\"#phone\").slideToggle()'>";
						echo "</div>";
					}
				}
			} ?> 
				
			<div class="form" id="phone" <?php echo ($phones ? "style='display:none'" : "") ?> >
				<input type="hidden" name="new_phone_number">
				<select name="phoneShare" style="width:67px">
					<option value="personalshare" > Shared</option>
					<option value="personalnotshare" > Not Shared</option>
				</select>
				<select name="phonetype">
					<option value="CELL">Cell</option>
					<option value="HOME">Home</option>
					<option value="FAX">Fax</option>
					<option value="OTHER">Other</option>
				</select>
				<?php $width=100; require("countrycodes.php"); ?>
				 (<input type="text" name="phonearea" value="" maxlength="3" style="width:27px" />)
				 <input type="text" name="phonenumber1" value="" maxlength="3" style="width:27px" />
				  - <input type="text" name="phonenumber2" value="" maxlength="4" style="width:35px" />
				 <input type="text" placeholder='Ext.' name="phoneextension" value="" maxlength="10" style="width:50px" />
			</div>
					
					
			<?php
			$emails	 = $wpdb-> get_results("SELECT * FROM email_address WHERE employee_id = '" . $user->external_id . "' AND is_ministry='0'");
			if($emails){
				$last = end($emails);
				foreach($emails as $email){
					$isLast = $last === $email;
					if ($isLast){
						echo "<div style='position:relative;'>";
					}
					$id = $email->email_address_id;
					echo '<div class="form" id="editEmail' . $id . '">';
					echo "<span style='font-weight:600;'>Personal Email: </span>";
					echo '<input type="hidden" name="email_address_id" value="' . $id . '">';
					echo '<input type="text" name="email" value="' . $email->email_address . '" style="width:260px"/>';
					?>
					<select name="share_email">
						<option value="true" <?php if($email->share_email) { echo 'selected'; } ?> >Shared</option>
						<option value="false" <?php if(!$email->share_email) { echo 'selected'; } ?> >Not Shared</option>
					</select>
					<?php
					if ($isLast){
						echo "<img class='false-link plus' src='".get_stylesheet_directory_uri()."/res/plus.png' width='14' height='14' onclick='$(\"#addEmail\").slideToggle()'>";
						echo "</div>";
					}
					echo '</div>';
				}  	
			}
			?>
			<div class="form" id="addEmail" <?php echo ($emails ? "style='display:none'" : "") ?> >
				<input type="text" placeholder='Personal Email' name="new_email_address" style="width:350px"/>
				<select name="share_email">
					<option value="true" <?php if($email->share_email) { echo 'selected'; } ?> >Shared</option>
					<option value="false" <?php if(!$email->share_email) { echo 'selected'; } ?> >Not Shared</option>
				</select>
			</div>
			<div class="form" id="editSocialMedia">
				<input type="hidden" name="socialMedia">
					<input type="text" placeholder='Website' id="website" name="website" value="<?php echo $user->website ?>" style="width:446px"><BR>
					<input type="text" placeholder='Twitter' id="twitter" name="twitter" value="<?php echo $user->twitter_handle ?>" style="width:446px"><BR>
					<input type="text" placeholder='Skype' id="skype" name="skype" value="<?php echo $user->skype ?>" style="width:446px"><BR>
					<input type="text" placeholder='Facebook' id="facebook" name="facebook" value="<?php echo $user->facebook ?>" style="width:446px">
			</div>
			<div class="form" id="updateNotes" style="padding-right:10px;padding-left:5px;">
				Personal Message:
				<textarea id="notes" name="notes" cols="40" rows="5"><?php echo str_replace("\\", "", $user->notes); ?></textarea>
				<input class='orange' type="submit" value="SAVE & VIEW PROFILE" />
			</div>
			</form>
			</div>	
		</div>
	</div>
	<div id="content-right">   
		<?php include('pro_sidebar.php') ?>
</div><div style='clear:both;'></div>