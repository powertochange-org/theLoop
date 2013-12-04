

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

// now echo the html page 
echo "<p />";
?>
<html lang="en"> 
    <head> 
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">      
        <link rel="stylesheet" type="text/css" href="stylesheet.css">          
        <link rel=StyleSheet href="style.css" type="text/css">
	      
    </head>     
    <body>     
	
<?php
	//
	// Process POST information if any
	//
	$null = "NULL";
	if(isset($_POST)){
		include('update.php');
	}
?>
		<!-- MAIN DISPLAY -->
		<h4 style="float:right"><a href= "?page=search" >Return to Search</a></h4>
		<div id="wrap" style="clear:both">
			<div id="nameheader">
				<h1><?php
				echo $user->first_name . " " . $user->last_name; //display name
				?></h1>
			</div>
			<div id="left">
				<p />
				<?php
				if(is_null($user->photo) || $user->share_photo == 0){ //if we don't have a photo or aren't allowed to show it
				?>
					<img src="../wp-content/uploads/staff_photos/anonymous.jpg" width=280 />
					<form id="Upload" action="../staff-directory/?page=upload_processor" enctype="multipart/form-data" method="post">	
						<input type="button" name="addPic" value="Add a Profile Picture" style="width:200px; margin-left:50px" onClick="$('#picUpload').toggle('slow');"/>
						<p />
						<div id="picUpload" style="display:none; background:lightgrey">
							<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size ?>"> 
							<input id="file" type="file" name="file" onchange="$('#submitpic').removeAttr('disabled')"> 
							<input id="submitpic" type="submit" name="submit" disabled="disabled" value="Upload Now">       
						</div>
					</form>
				<?php
				} else { //we have a photo and can share it
					echo	 '<img src="../wp-content/uploads/staff_photos/' . $user->photo . '"  width=290 />';
					echo	 '<form onsubmit="return confirm(\'Are you sure you want to remove your photo?\')" action="" method="post">';
					echo	 '	<input style="width:200px; margin-left:50px" type="submit" name="remove" value="Remove Photo" />';
					echo	 '</form>';
					
				}
				echo "<br>";
				echo '<div id="notes">';
					echo str_replace("\\", "", $user->notes); //this displays the 'About me' section located beneath the profile pic
				echo '</div>';
				?>
			</div><!--left-->
			<div class="right">
				<?php include "infocard.php"; ?>
				<div style="background:transparent; clear:both">
					<input type="button" name="updateButton" value="Update/Edit your information" style="width:300px; float:right; margin-right:150px;  margin-top:20px" onClick="$('#update').toggle('slow');"/>
				</div>	
			</div><!--right-->

			<!-- UPDATE SECTION STARTS HERE -->
			<div id="update" class="update right"  <?php if (empty($_POST)) { echo 'style="display:none"'; } ?>>
			
			<!-- PHONE NUMBERS -->
			<div id="phone">
				<hr>
				<table><tr><th>Phone Numbers:</th></tr></table>
				<?php
				$phones	 = $wpdb-> get_results("SELECT * FROM phone_number WHERE employee_id = '" . $user->external_id . "' ORDER BY is_ministry DESC, share_phone DESC");
				if($phones){
					echo '<ul>';
					foreach($phones as $phone){
						$id = $phone->phone_number_id;
						$contact = split("-", $phone->contact_number, 2);
						echo '<li>';
						echo '<div id="displayPhoneId' . $id . '" class="reset">';
							echo $phone->phone_type . ': +';
							echo countryToNumber($phone->country_code) . ' (' . $phone->area_code . ') ' . $phone->contact_number;
							if ($phone->extension != "") {
								echo ' ext:' . $phone->extension ;
							}
							if ($phone->is_ministry) {
								echo ' [Ministry - public]'; 
							} else {
								if ($phone->share_phone) {
									echo ' [Personal - shared]';
								} else {
									echo ' [Personal - private]';
								} 
							}
							echo '<input type="button" onClick="if (editable()) {$(\'#editPhone' . $id . '\').show(); $(\'#displayPhoneId' . $id . '\').hide()} " value="edit" />';
							echo '<form onsubmit="return confirm(\'Are you sure you want to DELETE this phone number?\')" action="" method="post" enctype="multipart/form-data" style="display:inline!important">';
							echo '<input type="hidden" name="deletePhone" value="' . $id . '"/> <input type="submit"  value="delete"/></form>';
						echo '</div>';
						echo '<form id="editPhone' . $id . '" action="" method="post" enctype="multipart/form-data" class="edit cancel" style="display:none">';
				?>
							<input type="hidden" name="editPhone" value="<?php echo $id; ?>">
							<input type="text" name="phonecountry" value="<?php echo $phone->country_code ?>" maxlength="3" size="3" />
							<select name="phoneShare">
								<option value="ministryshare" <?php if ($phone->is_ministry) { echo 'selected="selected"'; } ?>> Ministry - Shared</option>
								<option value="personalshare" <?php if (($phone->share_phone) && (!$phone->is_ministry)) { echo 'selected="selected"'; } ?>> Personal - Shared</option>
								<option value="personalnotshare" <?php if ((!$phone->share_phone) && (!$phone->is_ministry)) {echo 'selected="selected"'; } ?>> Personal - Not Shared</option>
							</select>
							<select name="phonetype">
								<option value="CELL" <?php if ($phone->phone_type == 'CELL') { echo 'selected="selected"'; } ?>>Cell</option>
								<option value="HOME" <?php if ($phone->phone_type == 'HOME') { echo 'selected="selected"'; } ?>>Home</option>
								<option value="BUS" <?php if ($phone->phone_type == 'BUS') { echo 'selected="selected"'; } ?>>Office</option>
								<option value="FAX" <?php if ($phone->phone_type == 'FAX') { echo 'selected="selected"'; } ?>>Fax</option>
								<option value="OTHER" <?php if ($phone->phone_type == 'OTHER') { echo 'selected="selected"'; } ?>>Other</option>
							</select>
							(<input type="text" name="phonearea" value="<?php echo $phone->area_code ?>" maxlength="3" size="3" />)
								 <input type="text" name="phonenumber1" value="<?php echo $contact[0] ?>" maxlength="3" size = "3" />
								 -<input type="text" name="phonenumber2" value="<?php echo $contact[1] ?>" maxlength="4" size = "4" />
								 ext<input type="text" name="phoneextension" value="<?php echo $phone->extension ?>" maxlength="10" size = "5" />
								 <input type="submit" value="save" style="margin-left:50px"/>
				 <?php
						echo '</form></li>';
					}
					echo '</ul>';
				}
				?> 
				<input type="button" id="add_phone_button" class="reset" onclick="if (editable()) { $('#add_phone_button').hide('slow'); $('#add_phone').show('slow')}" value="Add another phone number" /><p />
				<div id="add_phone" class="edit cancel" style="display: none;">
			
					<form id="phone" action="" method="post" enctype="multipart/form-data">
						<input type="hidden" name="new_phone_number">
						<select name="phoneShare">
							<option value="ministryshare" selected="selected"> Ministry - Shared</option>
							<option value="personalshare" > Personal - Shared</option>
							<option value="personalnotshare" > Personal - Not Shared</option>
						</select>
						<select name="phonetype">
							<option value="CELL">Cell</option>
							<option value="HOME">Home</option>
							<option value="BUS">Office</option>
							<option value="FAX">Fax</option>
							<option value="OTHER">Other</option>
						</select>
						 (<input type="text" name="phonearea" value="" maxlength="3" size="3" />)
						 <input type="text" name="phonenumber1" value="" maxlength="3" size = "3" />
						 -<input type="text" name="phonenumber2" value="" maxlength="4" size = "4" />
						 ext<input type="text" name="phoneextension" value="" maxlength="10" size = "5" />
						<?php require("countrycodes.php"); ?>
						 <input type="submit" value="save" style="margin-left:240px"/>
					</form>
				</div>
			</div>
			<hr/>
			<!-- MINSISTRY INFORMATION -->
			<div id="ministry">
				<table><tr><th>Ministry Information</th></tr></table>
				<div id="ministryAddress">
					<div id="displayMinistryAddress" class="reset">
						<?php
						if (!empty($user->ministry_address_line1)) {
							echo $user->ministry_address_line1 . "<br/>";
							if (!empty($user->ministry_address_line2))  {
								echo $user->ministry_address_line2 . "<br/>";
							}
							if (!empty($user->ministry_address_line3)) {
								echo $user->ministry_address_line3 . "<br/>";
							}
							echo $user->ministry_city . ", ";
							echo $user->ministry_province . ", ";
							echo $user->ministry_postal_code . "<br/>";
							echo $user->ministry_country . "<br/><br/>";
							echo "<input type='button' onClick=\"if (editable()) { $('#displayMinistryAddress').hide('slow'); $('#editMinistryAddress').show('slow'); }\" value='Edit Ministry Address' ><br/>";
						} else {
							echo "<input type='button' onClick=\"if (editable()) { $('#displayMinistryAddress').hide('slow'); $('#editMinistryAddress').show('slow'); }\" value='Add Ministry Address' ><br/>";
						}
						?>
					</div>
					<div id="editMinistryAddress" class="edit cancel" style="display:none">
						<form action="" method="post" enctype="multitype/form-data">
							<input type="hidden" name="ministryAddress">
							<label for="ministry_address1">Address Line #1:</label>
							<input type="textbox" name="ministry_address1" value="<?php echo $user->ministry_address_line1 ?>"><br/> 
							<label for="ministry_address2">Address Line #2:</label>
							<input type="textbox" name="ministry_address2" value="<?php echo $user->ministry_address_line2 ?>"> (Only needed if you have a PO Box or RR number)<br/>
							<?php if(is_null($user->ministry_address_line3)){ ?>
								<label for="ministry_address3">Address Line #3:</label>
								<input type="textbox" name="ministry_address3" value="<?php echo $user->ministry_address_line3 ?>"><br/> 
							<?php } ?>
							<label for="ministry_city_value">City:</label>
							<input type="textbox" name="ministry_city_value" value="<?php echo $user->ministry_city ?>"> <br/>
							<label for="ministry_province_value">Province:</label>
							<input type="textbox" name="ministry_province_value" value="<?php echo $user->ministry_province ?>" maxlength="2"> <br/>
							<label for="ministry_postal_code">Postal Code:</label>
							<input type="textbox" name="ministry_postal_code" value="<?php echo $user->ministry_postal_code ?>"><br/>
							<label for="ministry_country">Country:</label>
							<input type="textbox" name="ministry_country" value="<?php echo $user->ministry_country ?>" maxlength="2">
							<input type="submit" value="save" style="margin-left:280px">
						</form>
					</div>
				</div>
				<br/>
				<h4>Ministry Email Addresses</h4>
				<div id="ministryEmail">
					<ul>
					<?php
					$emails	 = $wpdb-> get_results("SELECT * FROM email_address WHERE employee_id = '" . $user->external_id . "' ORDER BY is_ministry DESC");
					if($emails){
						foreach($emails as $email){
							if($email->is_ministry){
								$id = $email->email_address_id;
								echo '<li><div id="displayMinEmailId' . $id . '" class="reset">';
								echo $email->email_address;
								//don't allow editing or deleting of powertochange.org address
								if(strpos(strtolower($email->email_address),'powertochange.org') === false) {
									echo '<input type="button" onClick="if (editable()) { $(\'#editMinEmail' . $id . '\').show(); $(\'#displayMinEmailId' . $id . '\').hide(); }" value="edit" />';
									echo '<form onsubmit="return confirm(\'Are you sure you want to DELETE this email address?\')" action="" method="post" enctype="multipart/form-data" style="display:inline!important">';
									echo '<input type="hidden" name="deleteMinEmail" value="' . $id . '"/>';
									echo '<input type="hidden" name="external_id" value="' . $email->external_id . '">';
									echo '<input type="submit"  value="delete"/></form>';
									echo '</div><form action="" method="post" enctype="multitype/form-data" id="editMinEmail' . $id . '" class="edit cancel" style="display:none">';
									echo '<input type="hidden" name="email_address_id" value="' . $id . '">';
									echo '<input type="text" name="minEmail" value="' . $email->email_address . '" style="width:300px"/>';
									echo '<input type="submit" value="save" style="margin-left:180px"></form></li>';
								}
							}
						}  	
					}
					?>
					</ul>
					<input type='button' id="addMinEmailButton" class="reset" value='Add a New Ministry Email Address' onClick="if (editable()) { $('#addMinEmailButton').hide('slow'); $('#addMinEmail').show('slow'); }"/><br/>
					<form action="" method="post" enctype="multitype/form-data" id="addMinEmail" class="edit cancel" style="display:none">
						<input type="text" name="new_min_email_address" style="width:300px"/>
						<input type="submit" value="save" style="margin-left:200px">
					</form>
				</div>
				<br/>
				<h4>Ministry Social Media</h4>
				<div id='displayMinSocialMedia' class='reset'>
					<ul>
					<?php
					if (!empty($user->ministry_website)) {
						echo "<li>Website: " . $user->ministry_website;
						echo '<form onsubmit="return confirm(\'Are you sure you want to REMOVE your ministry website?\')" action="" method="post" enctype="multitype/form-data" style="display:inline!important">';
							echo '<input type="hidden" name="del_min_website">';
							echo '<input type="submit" value="delete"></form></li>';
					}
					if (!empty($user->ministry_twitter_handle)) {
						echo "<li>Twitter: " . $user->ministry_twitter_handle;
						echo '<form onsubmit="return confirm(\'Are you sure you want to REMOVE your ministry Twitter handle?\')" action="" method="post" enctype="multitype/form-data" style="display:inline!important">';
							echo '<input type="hidden" name="del_min_twitter">';
							echo "<input type='submit' value='delete'></form></li>";
					}
					if (!empty($user->ministry_skype)) {
						echo "<li>Skype: " . $user->ministry_skype;
						echo '<form onsubmit="return confirm(\'Are you sure you want to REMOVE your Skype name?\')" action="" method="post" enctype="multitype/form-data" style="display:inline!important">';
							echo '<input type="hidden" name="del_min_skype">';
							echo "<input type='submit' value='delete'></form></li>";
					}
					if (!empty($user->ministry_facebook)) {
						echo "<li>Facebook: " . $user->ministry_facebook;
						echo '<form onsubmit="return confirm(\'Are you sure you want to REMOVE your ministry Facebook page?\')" action="" method="post" enctype="multitype/form-data" style="display:inline!important">';
							echo '<input type="hidden" name="del_min_facebook">';
							echo "<input type='submit' value='delete'></form></li>";
					}
					?>
					</ul>
					<input type='button' value='Edit social media' onClick="if (editable()) { $('#displayMinSocialMedia').hide('slow'); $('#editMinSocialMedia').show('slow'); }"/><br/>
				</div>
				<form id="editMinSocialMedia" action="" method="post" enctype="multitype/form-data" class="edit cancel" style="display:none">
					<input type="hidden" name="minSocialMedia">
					<ul>
						<li><label for="minwebsite">Website:</label>
						<input type="text" id="minwebsite" name="website" value="<?php echo $user->ministry_website ?>" style="width:200px"></li>
						<li><label for="mintwitter">Twitter:</label>
						<input type="text" id="mintwitter" name="twitter" value="<?php echo $user->ministry_twitter_handle ?>" style="width:200px"></li>
						<li><label for="minskype">Skype:</label>
						<input type="text" id="minskype" name="skype" value="<?php echo $user->ministry_skype ?>" style="width:200px"></li>
						<li><label for="minfacebook">Facebook:</label>
						<input type="text" id="minfacebook" name="facebook" value="<?php echo $user->ministry_facebook ?>" style="width:200px">
						<input type="submit" value="save" style="float:right"></li>
					</ul>
				</form>
			</div><br/>
			<div id="personal">
					<hr/>
					<table><tr><th>Personal Information</th></tr></table>
					<div id="personalAddress">
						<div id="displayPersonalAddress" class="reset">
							<?php
							if (!empty($user->address_line1)) {
								echo $user->address_line1 . "<br/>";
								if (!empty($user->address_line2)) {
									echo $user->address_line2 . "<br/>";
								}
								if (!empty($user->address_line3)) {
									echo $user->address_line3 . "<br/>";
								}
								echo $user->city . ", ";
								echo $user->province . ", ";
								echo $user->postal_code . "<br/>";
								echo $user->country . "<br/><br/>";
								echo "Share level? " . $user->share_address . "<br/>";
								echo "<input type='button' onClick=\"if (editable()) { $('#displayPersonalAddress').hide('slow'); $('#editPersonalAddress').show('slow'); }\" value='Edit Personal Address' ><br/>";
							} else {
								echo "<input type='button' onClick=\"if (editable()) { $('#displayPersonalAddress').hide('slow'); $('#editPersonalAddress').show('slow'); }\" value='Add Personal Address' ><br/>";
							}
							?>
						</div>
						<div id="editPersonalAddress" class="edit cancel" style="display:none">
							<form action="" method="post" enctype="multitype/form-data">
								<input type="hidden" name="personalAddress">
								<label for="address1">Address Line #1:</label>
								<input type="textbox" name="address1" value="<?php echo $user->address_line1 ?>"><br/> 
								<label for="address2">Address Line #2:</label>
								<input type="textbox" name="address2" value="<?php echo $user->address_line2 ?>"> (Only needed if you have a PO Box or RR number)<br/>
								<?php if(is_null($user->address_line3)){ ?>
									<label for="address3">Address Line #3:</label>
									<input type="textbox" name="address3" value="<?php echo $user->address_line3 ?>"><br/> 
								<?php } ?>
								<label for="city_value">City:</label>
								<input type="textbox" name="city_value" value="<?php echo $user->city ?>"> <br/>
								<label for="province_value">Province:</label>
								<input type="textbox" name="province_value" value="<?php echo $user->province ?>" maxlength="2"> <br/>
								<label for="postal_code">Postal Code:</label>
								<input type="textbox" name="postal_code" value="<?php echo $user->postal_code ?>"><br/>
								<label for="country">Country:</label>
								<input type="textbox" name="country" value="<?php echo $user->country ?>" maxlength="2"><br/>
								<fieldset style="display:inline;background:white;border:1px solid lightgrey;padding:8px">
									<legend>Address Share Level</legend>
									<input type="radio" name="addressPermissions" value="FULL" <?php if($user->share_address == 'FULL') { echo 'checked'; } ?> />Full Address<br/>
									<input type="radio" name="addressPermissions" value="CITY&PROV" <?php if($user->share_address == 'CITY&PROV') { echo 'checked'; } ?> />City and Province Only<br/>	
									<input type="radio" name="addressPermissions" value="PROVONLY" <?php if($user->share_address == 'PROVONLY') { echo 'checked'; } ?> />Province Only<br/>
									<input type="radio" name="addressPermissions" value="NONE" <?php if($user->share_address == 'NONE') { echo 'checked'; } ?> />Do not share any details about my location
								</fieldset>
								<input type="submit" value="save" style="margin-left:200px">
							</form>
						</div>
						<br/>
					</div>
					<h4>Personal Email</h4>
					<div id="personalEmail">
						<ul>
						<?php
						$emails	 = $wpdb-> get_results("SELECT * FROM email_address WHERE employee_id = '" . $user->external_id . "' AND is_ministry='0'");
						if($emails){
							foreach($emails as $email){
								$id = $email->email_address_id;
								echo '<li><div id="displayEmailId' . $id . '" class="reset">';
								echo $email->email_address;
								if ($email->share_email) {
									echo " <b>Shared</b> "; 
								} else {
									echo " <b>Private</b> ";
								}
								echo '<input type="button" onClick="if (editable()) { $(\'#editEmail' . $id . '\').show(); $(\'#displayEmailId' . $id . '\').hide(); }" value="edit" />';
								echo '<form onsubmit="return confirm(\'Are you sure you want to DELETE this email address?\')" action="" method="post" enctype="multipart/form-data" style="display:inline!important">';
								echo '<input type="hidden" name="deleteEmail" value="' . $id . '"/>';
								echo '<input type="hidden" name="external_id" value="' . $email->external_id . '">';
								echo '<input type="submit"  value="delete"/></form>';
								echo '</div><form action="" method="post" enctype="multitype/form-data" id="editEmail' . $id . '" class="edit cancel" style="display:none">';
								echo '<input type="hidden" name="email_address_id" value="' . $id . '">';
								echo '<input type="text" name="email" value="' . $email->email_address . '" style="width:300px"/>';
								echo '<input type="checkbox" name="share_email" ';
								if ($email->share_email) {
									echo 'checked="checked"';
								} 
								echo '>Share with staff?</input>';
								echo '<input type="submit" value="save" style="margin-left:40px"></form></li>';
							}  	
						}
						?>
						</ul>
						<input type='button' id="addEmailButton" value='Add a New Personal Email Address' onClick="if (editable()) { $('#addEmailButton').hide('slow'); $('#addEmail').show('slow'); }"/><br/>
						<form action="" method="post" enctype="multitype/form-data" id="addEmail" class="edit cancel" style="display:none">
							<input type="text" name="new_email_address" style="width:300px"/>
							<input type="checkbox" name="share_email">Share with staff?</input>
							<input type="submit" value="save" style="margin-left:40px"/>
						</form>
					</div>
					<br/>
					<h4>Personal Social Media</h4>
					<div id='displaySocialMedia' class="reset">
						<ul>
						<?php
						if (!empty($user->website)) {
							echo "<li>Website: " . $user->website;
							echo '<form onsubmit="return confirm(\'Are you sure you want to remove your website?\')" action="" method="post" enctype="multitype/form-data" style="display:inline!important">';
								echo '<input type="hidden" name="del_website">';
								echo '<input type="submit" value="delete"></form></li>';
						}
						if (!empty($user->twitter_handle)) {
							echo "<li>Twitter: " . $user->twitter_handle;
							echo '<form onsubmit="return confirm(\'Are you sure you want to remove your twitter handle?\')" action="" method="post" enctype="multitype/form-data" style="display:inline!important">';
								echo '<input type="hidden" name="del_twitter">';
								echo '<input type="submit" value="delete"></form></li>';
						}
						if (!empty($user->skype)) {
							echo "<li>Skype: " . $user->skype;
							echo '<form onsubmit="return confirm(\'Are you sure you want to remove your skype name?\')" action="" method="post" enctype="multitype/form-data" style="display:inline!important">';
								echo '<input type="hidden" name="del_skype">';
								echo '<input type="submit" value="delete"></form></li>';
						}
						if (!empty($user->facebook)) {
							echo "<li>Facebook: " . $user->facebook;
							echo '<form onsubmit="return confirm(\'Are you sure you want to remove your facebook page?\')" action="" method="post" enctype="multitype/form-data" style="display:inline!important">';
								echo '<input type="hidden" name="del_facebook">';
								echo '<input type="submit" value="delete"></form></li>';
						}
						?>
						</ul>
							<input type='button' value='Edit social media' onClick="if (editable()) { $('#displaySocialMedia').hide('slow'); $('#editSocialMedia').show('slow'); }"/><br/>
					</div>
					<form id="editSocialMedia" action="" method="post" enctype="multitype/form-data" class="edit cancel" style="display:none">
						<input type="hidden" name="socialMedia">
						<ul>
							<li><label for="website">Website:</label>
							<input type="text" id="website" name="website" value="<?php echo $user->website ?>" style="width:200px"></li>
							<li><label for="twitter">Twitter:</label>
							<input type="text" id="twitter" name="twitter" value="<?php echo $user->twitter_handle ?>" style="width:200px"></li>
							<li><label for="skype">Skype:</label>
							<input type="text" id="skype" name="skype" value="<?php echo $user->skype ?>" style="width:200px"></li>
							<li><label for="facebook">Facebook:</label>
							<input type="text" id="facebook" name="facebook" value="<?php echo $user->facebook ?>" style="width:200px">
							<input type="submit" value="save" style="float:right"></li>
						</ul>
					</form>
				</div>
				<br/>
				<h3>Personal Message:</h3>
				<div id="displayNotes" class='reset'>
					<textarea name="notes" cols="40" rows="5" disabled="disabled"><?php echo str_replace("\\", "", $user->notes); ?></textarea>
					<input type="button" value="edit" onClick="if (editable()) { $('#updateNotes').show(); $('#displayNotes').hide();  } "/>
				</div>
				<form id="updateNotes" action="" method="post" enctype="multitype/form-data" class="edit cancel" style="display:none; padding:10px;margin-right:150px">
					<textarea id="notes" name="notes" cols="40" rows="5"><?php echo str_replace("\\", "", $user->notes); ?></textarea>
					<input type="submit" value="save" class="cancel" />
				</form>
			</div>
		

		</div>
		<script type="text/javascript">
		editing=false;
		function cancel() {
			if (confirm("Are you SURE you want to abandon your changes without saving?")) {
				$('.cancel').hide(200);
				$('.reset').show(200);
				editing=false;
				return true;
			}
			return false
		}
		function editable() {
			if (editing) {
				if (cancel()) {
					editing=true;
					return true;
				}
				return false;
			}
			editing=true;
			return true;
		}
		</script>
	
    </body> 
</html>

