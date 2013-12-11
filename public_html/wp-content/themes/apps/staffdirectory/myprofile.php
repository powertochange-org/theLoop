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
			form:hover {
				background-color:#f5f5f5;
			}
			form {
				margin-bottom:10px;
			}
			h4{
				font-weight:bold;
				margin-bottom:10px;
			}
				
			input {	
				margin-bottom:2px;
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
			}
			
			#main-content form input[type=text], #main-content form input[type=textbox], #main-content form select{
				background-color:#f4f4f4;
				border:solid #adafb2 1px;
			}
		</style>
		<!-- MAIN DISPLAY -->
		
		<p/><h4 style="float:right;color:#adafb2;"><a href= "?page=profile" >MY PROFILE</a></h4><BR>
	<hr>
	<div style="clear:both"></div>
	<div id="content-left">
		<div id="main-content">
			<p class='orange-box'><?php	echo strtoupper ("$user->first_name $user->last_name")." | $user->role_title, $user->ministry"; ?></p> <p></p>
			<div style='float:left'>
				<?php if(is_null($user->photo)){ //if we don't have a photo or aren't allowed to show it
				echo '<img style="display:block" src="../../wp-content/uploads/staff_photos/anonymous.jpg" width=220 />';
				}
				else { //we have a photo and can share it
					echo '<img style="display:block" src="../wp-content/uploads/staff_photos/' . $user->photo . '"  width=220 />';
				} ?>
				
				<input type='button' value='CHANGE IMAGE'  onClick="$('#picUpload').toggle('slow');">
				<!-- are you sure todo -->
				
				<form id="Upload" action="../staff-directory/?page=upload_processor" enctype="multipart/form-data" method="post">	
						<p />
						<div id="picUpload" style="display:none; background:lightgrey; width:220px">
							<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size ?>"> 
							<input id="file" type="file" name="file" onchange="$('#submitpic').removeAttr('disabled')"><BR>
							<input id="submitpic" type="submit" name="submit" disabled="disabled" value="Upload Now">       
						</div>
					</form>
				
			</div>
			
			<div style='float:left;padding-left:23px;width:457px'>
			
			<h4>MINISTRY INFORMATION</h4>	
			<form action="" method="post" enctype="multitype/form-data">
				Address:
				<input type="hidden" name="ministryAddress">
				<input type="textbox" placeholder='Address Line #1' name="ministry_address1" value="<?php echo $user->ministry_address_line1 ?>">
				<input type="textbox" placeholder='Address Line #2' name="ministry_address2" value="<?php echo $user->ministry_address_line2 ?>" title='(Only needed if you have a PO Box or RR number)' >
				<?php if(is_null($user->ministry_address_line3)){ ?>
					<input type="textbox" placeholder='Address Line #3' name="ministry_address3" value="<?php echo $user->ministry_address_line3 ?>">
				<?php } ?>
				<input type="textbox" placeholder='City' name="ministry_city_value" value="<?php echo $user->ministry_city ?>">
				<input type="textbox" placeholder='Pr.'  name="ministry_province_value" value="<?php echo $user->ministry_province ?>" maxlength="2" size='2'>
				<input type="textbox" placeholder='Country' name="ministry_country" value="<?php echo $user->ministry_country ?>" maxlength="2" size='2'>
				<input type="textbox" placeholder='PC' name="ministry_postal_code" value="<?php echo $user->ministry_postal_code ?>">
				<input class='orange' type="submit" value="SAVE">
			</form>
			
			
			<?php
			$phones	 = $wpdb-> get_results("SELECT * FROM phone_number WHERE employee_id = '" . $user->external_id . "' ORDER BY is_ministry DESC, share_phone DESC");
			if($phones){
				$last = end($phones);
				foreach($phones as $phone){
					$isLast = $last === $phone;
					$id = $phone->phone_number_id;
					$contact = split("-", $phone->contact_number, 2);
					if ($isLast){
						echo "<div style='position:relative;'>";
					}
					echo '<form id="editPhone' . $id . '" action="" method="post" enctype="multipart/form-data">';
			?>
					Phone:
					<input type="hidden" name="editPhone" value="<?php echo $id; ?>">
					<input type="text" placeholder='Country' name="phonecountry" value="<?php echo $phone->country_code ?>" maxlength="3" size="3" />
					<input type='hidden' name='phoneShare' value='ministryshare' >
					<input type='hidden' name='phonetype' value='BUS' >
					(<input type="text" placeholder='Area' name="phonearea" value="<?php echo $phone->area_code ?>" maxlength="3" size="3" />)
					 <input type="text" name="phonenumber1" value="<?php echo $contact[0] ?>" maxlength="3" size = "3" />
					 -<input type="text" name="phonenumber2" value="<?php echo $contact[1] ?>" maxlength="4" size = "4" />
					 <input type="text" placeholder='Ext' name="phoneextension" value="<?php echo $phone->extension ?>" maxlength="10" size = "5" />
					 <input class='orange' type="submit" value="SAVE" />
			 <?php
					echo '</form>';
					if ($isLast){
						echo "<img class='false-link plus' src='".get_stylesheet_directory_uri()."/res/plus.png' width='14' height='14' onclick='$(\"#min_phone\").slideToggle()'>";
						echo "</div>";
					}
				}
			}
			?> 
			
			<form id="min_phone" action="" method="post" enctype="multipart/form-data" <?php echo ($phones ? "style='display:none'" : "") ?> >
				<input type="hidden" name="new_phone_number">
				<input type='hidden' name='phoneShare' value='ministryshare' >
				<input type='hidden' name='phonetype' value='BUS' >
				 (<input type="text" placeholder='Area' name="phonearea" value="" maxlength="3" size="3" />)
				 <input type="text" name="phonenumber1" value="" maxlength="3" size = "3" />
				 -<input type="text" name="phonenumber2" value="" maxlength="4" size = "4" />
				 <input type="text"  placeholder='Ext' name="phoneextension" value="" maxlength="10" size = "5" />
				<?php require("countrycodes.php"); ?>
				 <input class='orange' type="submit" value="ADD PHONE" />
			</form>
			
			
			<?php
			$emails	 = $wpdb-> get_results("SELECT * FROM email_address WHERE employee_id = '" . $user->external_id . "' ORDER BY is_ministry DESC");
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
						echo '<form action="" method="post" enctype="multitype/form-data" id="editMinEmail' . $id . '">';
						echo 'Ministry Email: ';
						echo '<input type="hidden" name="email_address_id" value="' . $id . '">';
						echo '<input type="text" name="minEmail" value="' . $email->email_address . '" style="width:250px"/>';
						echo '<input class="orange" type="submit" value="SAVE"></form>';
					}
					else{
						echo 'Ministry Email: ';
						?><input type="text" value='<?php echo $email->email_address; ?>' disabled style="width:250px"/><?php
					}
					if ($isLast){
						echo "<img class='false-link plus' src='".get_stylesheet_directory_uri()."/res/plus.png' width='14' height='14' onclick='$(\"#addMinEmail\").slideToggle()'>";
						echo "</div>";
					}
				}  	
			}
			?>
			<form action="" method="post" enctype="multitype/form-data" id="addMinEmail" <?php echo ($emails ? "style='display:none'" : "") ?> >
				<input type="text"  placeholder='Ministry Email' name="new_min_email_address" style="width:300px"/>
				<input class='orange' type="submit" value="ADD EMAIL" >
			</form>
				
			<form id="editMinSocialMedia" action="" method="post" enctype="multitype/form-data">
				<input type="hidden" name="minSocialMedia">
					<input type="text" placeholder='Website' id="minwebsite" name="website" value="<?php echo $user->ministry_website ?>" style="width:200px"><BR>
					<input type="text" placeholder='Twitter' id="mintwitter" name="twitter" value="<?php echo $user->ministry_twitter_handle ?>" style="width:200px"><BR>
					<input type="text" placeholder='Skype' id="minskype" name="skype" value="<?php echo $user->ministry_skype ?>" style="width:200px"><BR>
					<input type="text" placeholder='Facebook' id="minfacebook" name="facebook" value="<?php echo $user->ministry_facebook ?>" style="width:200px"><BR>
					<input class='orange' type="submit" value="SAVE">
			</form>
			
			
			<BR>
			<h4>PERSONAL INFORMATION</h4>
			
			
			<form action="" method="post" enctype="multitype/form-data">
				Address: 
				<input type="hidden" name="personalAddress">
				<input type="textbox" placeholder='Address Line #1' name="address1" value="<?php echo $user->address_line1 ?>">
				<input type="textbox" placeholder='Address Line #2' name="address2" value="<?php echo $user->address_line2 ?>" title='(Only needed if you have a PO Box or RR number)' >
				<?php if(is_null($user->address_line3)){ ?>
					<input type="textbox" placeholder='Address Line #3' name="address3" value="<?php echo $user->address_line3 ?>"> 
				<?php } ?>
				<input type="textbox"placeholder='City' name="city_value" value="<?php echo $user->city ?>"> 
				<input type="textbox" placeholder='Pr.' name="province_value" value="<?php echo $user->province ?>" maxlength="2" size='2'>
				<input type="textbox" placeholder='Country' name="country" value="<?php echo $user->country ?>" maxlength="2" size='2'>
				<input type="textbox" placeholder='PC' name="postal_code" value="<?php echo $user->postal_code ?>">
				<input type='hidden' name='addressPermissions' value='NONE'>
				<input type='checkbox' name='addressPermissions' value='FULL'  <?php if($user->share_address == 'FULL') { echo 'checked'; } ?> >
				<label for='addressPermissions'>Share with staff?</label>
				<input class='orange' type="submit" value="SAVE">
			</form>
			
			
			<?php
			$phones	 = $wpdb-> get_results("SELECT * FROM phone_number WHERE employee_id = '" . $user->external_id . "' ORDER BY is_ministry DESC, share_phone DESC");
			if($phones){
				$last = end($phones);
				foreach($phones as $phone){
					$isLast = $last === $phone;
					if ($isLast){
						echo "<div style='position:relative;'>";
					}
					$id = $phone->phone_number_id;
					$contact = split("-", $phone->contact_number, 2);
					echo '<form id="editPhone' . $id . '" action="" method="post" enctype="multipart/form-data">';
					?>
					Phone:
					<input type="hidden" name="editPhone" value="<?php echo $id; ?>">
					<input type="text" name="phonecountry" value="<?php echo $phone->country_code ?>" maxlength="3" size="3" />
					<select name="phoneShare">
						<option value="personalshare" <?php if (($phone->share_phone) && (!$phone->is_ministry)) { echo 'selected="selected"'; } ?>> Shared</option>
						<option value="personalnotshare" <?php if ((!$phone->share_phone) && (!$phone->is_ministry)) {echo 'selected="selected"'; } ?>> Not Shared</option>
					</select>
					<select name="phonetype">
						<option value="CELL" <?php if ($phone->phone_type == 'CELL') { echo 'selected="selected"'; } ?>>Cell</option>
						<option value="HOME" <?php if ($phone->phone_type == 'HOME') { echo 'selected="selected"'; } ?>>Home</option>
						<option value="FAX" <?php if ($phone->phone_type == 'FAX') { echo 'selected="selected"'; } ?>>Fax</option>
						<option value="OTHER" <?php if ($phone->phone_type == 'OTHER') { echo 'selected="selected"'; } ?>>Other</option>
					</select>
					<BR>
					(<input type="text" name="phonearea" value="<?php echo $phone->area_code ?>" maxlength="3" size="3" />)
					 <input type="text" name="phonenumber1" value="<?php echo $contact[0] ?>" maxlength="3" size = "3" />
					 -<input type="text" name="phonenumber2" value="<?php echo $contact[1] ?>" maxlength="4" size = "4" />
					 <input type="text" placeholder="Ext." name="phoneextension" value="<?php echo $phone->extension ?>" maxlength="10" size = "5" />
					 <input class='orange' type="submit" value="SAVE"/>
					 <?php
					echo '</form>';
					if ($isLast){
						echo "<img class='false-link plus' src='".get_stylesheet_directory_uri()."/res/plus.png' width='14' height='14' onclick='$(\"#phone\").slideToggle()'>";
						echo "</div>";
					}
				}
			} ?> 
				
			<form id="phone" action="" method="post" enctype="multipart/form-data" <?php echo ($phones ? "style='display:none'" : "") ?> >
				<input type="hidden" name="new_phone_number">
				<select name="phoneShare">
					<option value="personalshare" > Shared</option>
					<option value="personalnotshare" > Not Shared</option>
				</select>
				<select name="phonetype">
					<option value="CELL">Cell</option>
					<option value="HOME">Home</option>
					<option value="FAX">Fax</option>
					<option value="OTHER">Other</option>
				</select><BR>
				 (<input type="text" name="phonearea" value="" maxlength="3" size="3" />)
				 <input type="text" name="phonenumber1" value="" maxlength="3" size = "3" />
				 -<input type="text" name="phonenumber2" value="" maxlength="4" size = "4" />
				 <input type="text" placeholder='Ext.' name="phoneextension" value="" maxlength="10" size = "5" />
				<?php require("countrycodes.php"); ?>
				 <input class='orange' type="submit" value="ADD PHONE" />
			</form>
					
					
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
					echo '<form action="" method="post" enctype="multitype/form-data" id="editEmail' . $id . '">';
					echo "Personal Email: ";
					echo '<input type="hidden" name="email_address_id" value="' . $id . '">';
					echo '<input type="text" name="email" value="' . $email->email_address . '" style="width:300px"/>';
					echo '<input type="checkbox" name="share_email" ';
					if ($email->share_email) {
						echo 'checked="checked"';
					} 
					echo '>Share with staff?</input>';
					echo '<input class="orange" type="submit" value="SAVE" ></form>';
					if ($isLast){
						echo "<img class='false-link plus' src='".get_stylesheet_directory_uri()."/res/plus.png' width='14' height='14' onclick='$(\"#addEmail\").slideToggle()'>";
						echo "</div>";
					}
				}  	
			}
			?>
			<form action="" method="post" enctype="multitype/form-data" id="addEmail" <?php echo ($emails ? "style='display:none'" : "") ?> >
				<input type="text" placeholder='Personal Email' name="new_email_address" style="width:240px"/>
				<input type="checkbox" name="share_email">Share with staff?</input>
				<input class='orange' type="submit" value="ADD EMAIL" />
			</form>
			<br/>
			<form id="editSocialMedia" action="" method="post" enctype="multitype/form-data">
				<input type="hidden" name="socialMedia">
					<input type="text" placeholder='Website' id="website" name="website" value="<?php echo $user->website ?>" style="width:200px"><BR>
					<input type="text" placeholder='Twitter' id="twitter" name="twitter" value="<?php echo $user->twitter_handle ?>" style="width:200px"><BR>
					<input type="text" placeholder='Skype' id="skype" name="skype" value="<?php echo $user->skype ?>" style="width:200px"><BR>
					<input type="text" placeholder='Facebook' id="facebook" name="facebook" value="<?php echo $user->facebook ?>" style="width:200px"><BR>
					<input class='orange' type="submit" value="SAVE">
			</form>
			<br/>
			<form id="updateNotes" action="" method="post" enctype="multitype/form-data" style="padding-right:10px;padding-left:5px;">
				Personal Message:
				<textarea id="notes" name="notes" cols="40" rows="5"><?php echo str_replace("\\", "", $user->notes); ?></textarea>
				<input class='orange' type="submit" value="SAVE" />
			</form>
			</div>	
		</div>
	</div>
	<div id="content-right">   
	<div id="sidebar">
		<div class="sidebaritem">
			<h1>Search for Staff</h1><BR>
			
			<div id='simple-search-staff'>
				<form id='s_s_s' method="post" action=""><div class='search-box' style='border-color:#adafb2'>
					<input class='search-input' type='textbox' name='fullname' placeholder='Search' />
					<img onclick="document.getElementById('s_s_s').submit();" class='search-img' src='<?php bloginfo('template_url'); ?>/img/search-bw.png'>
				</div></form>
				<BR>
				<p>You can search using any relevant keywords, like name, jop title, ministry, city, postal code, etc.</p>
				<!-- BR>
				<!-- a class='false-link'><h2>SHOW ADAVANCED SEARCH</h2></a -->
			</div>
			<div id='advanced-search-staff'>
			</div>
		</div>
	</div>
</div><div style='clear:both;'></div>

