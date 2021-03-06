<?php
/**
* MyProfile
*
* This is used when a user looks at their own profile in the Staff Address book project.
* This is where users can view, change, update, etc their information. They can even throw
* a pic on their profile too.
*
*
**/
//var_dump($_POST);//this is super helpful for debugging/building anything with the forms
include 'countryToNumber.php';
include(dirname(__FILE__)."/../functions/functions.php");



$user;
$login_of_person_being_edited;

if (isAppAdmin('admin_picture_add', 0) && isset($_GET['person'])){

	$login_of_person_being_edited = $_GET['person'];
} else {

	$current_user = wp_get_current_user();
	$login_of_person_being_edited = $current_user->user_login;
}
$user = $wpdb->get_row("SELECT * FROM employee WHERE user_login = '" . $login_of_person_being_edited . "'");


// make a note of the current working directory relative to root.
$directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']);

// make a note of the location of the upload handler script
$uploadHandler = 'http://' . $_SERVER['HTTP_HOST'] . $directory_self . 'upload.processor.php';

// set a max file size for the html upload form
$max_file_size = 30000000; // size in bytes

?>
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/jquery.Jcrop.css" type="text/css" />
<style type="text/css">
#main-content div.form {
	margin-bottom:10px;
	padding-top:5px;
}

.plus{
	position:absolute;
	left:-7px;
	top:2px;
}

.orange{
	background-color:#f7941d;
	border:0px solid #000000;
	color:#ffffff;
	border-radius:5px;
	padding:3px 6px;
	font-size:12px;
}

.changepic {
	padding:10px;
	letter-spacing:1px;
	font-weight:bold;
	font-size:16pt;
	background-color:#adafb2;
	width:220px;
}

.addOrSwitchpic {
	padding:10px;
	letter-spacing:1px;
	font-weight:bold;
	font-size:16pt;
	background-color:#adafb2;
	width:220px;
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

#main-content form#theForm table{
	width:448px;
	border-spacing: 2px;
}

</style>
<!-- MAIN DISPLAY -->

<p/><h4 style="float:right;position:relative;top:30px;"><a href='?page=profile' class="profile-link">VIEW PROFILE</a></h4><br /><br /><br /><br />

<hr style='margin-top:0'>
<div style="clear:both"></div>

<div id="content-left">
	<div id="main-content">
		<p class='orange-box'><?php	echo "<span style='font-weight:bold;color:#ffffff;font-size:16pt'>".strtoupper ("$user->first_name $user->last_name")."<span style='font-weight:normal;color:#ffffff'> | </span></span>$user->role_title, $user->ministry"; ?></p>
		<p></p>
		<div style='float:left'>
			<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.Jcrop.min.js"></script>
			<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/staffdirectory.js" ></script> <?php
			if (is_null($user->photo)){ //if we don't have a photo

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
				echo '<img id="photo" style="display:block" src="'. $url . '" width=220 />';?>

				<input class='orange addOrSwitchpic' id="addpic" type="button" onclick='$("#file").click()' value="ADD IMAGE">

				<input class='orange changepic' style="display: block; visibility: hidden;" title='Remove this image, and revert to the image on your public staff giving site (if you have one)' type="button" id="removepic" value="REMOVE IMAGE" onclick="deleteImage()"/>

				<?php
			}
			else { //we have a photo and can share it
				echo '<img id="photo" style="display:block" src="/wp-content/uploads/staff_photos/' . $user->photo . '"  width=220 />'; ?>
				<input class='orange addOrSwitchpic' id="addpic" type="button" onclick='$("#file").click();' value="CHANGE IMAGE" >

				<input class='orange changepic' style="display: block;" title='Remove this image, and revert to the image on your public staff giving site (if you have one)' type="button" id="removepic" value="REMOVE IMAGE" onclick="deleteImage()"/>
				<?php
			}
			?>
			<div style="border-radius:5px; margin: 2px 0px; box-sizing:border-box; -moz-box-sizing:border-box; -webkit-box-sizing:border-box; display: none;" class="changepic">
				<p>Notes:</p>
				<ul style='list-style-position: inside; font-size: 12px; line-height:normal;' class='orange'>
					<li>You can click and drag on the photo to crop it</li>
					<li>To reset your cropping area, click on the image somewhere outside the current crop area</li>
					<li>When you're finished, click on "Save & View Profile"</li>
				</ul>
			</div>
		</div>



		<div style='float:left;padding-left:23px;width:457px'>
			<?php
			//determining page to send person (whether an admin is editing someone else or we edit ourselves)
			if (isAppAdmin('admin_picture_add', 0) && isset($_GET['person'])){

				echo '<form onsubmit="return preSubmit();" id="theForm" action="?page=profile&person=' . $login_of_person_being_edited
				. '" method="post" enctype="multipart/form-data">';

			} else {

				echo '<form onsubmit="return preSubmit();" id="theForm" action="?page=profile" method="post" enctype="multipart/form-data">';

			}
			?>

			<!-- These are fields for the photo upload stuff -->
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size ?>">
			<input id="file" type="file" name="file" style='display:none;' accept="image/png,image/gif,image/jpeg">
			<!-- Hidden inputs for coordinates -->
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="width" name="width" />
			<input type="hidden" id="height" name="height" />
			<!-- Remove photo -->
			<input type="hidden" id="deleteImage" name="deleteImage" />

			<p><b>NOTE: </b>To edit your personal address, as well as ministry and personal phone numbers and email addresses, please go to <b><a href="https://sso.dayforcehcm.com/p2c">Dayforce</a></b>. Changes made there will typically show up in the Staff Directory within 24 hours.</p>

			<h4 style='font-size:16pt'>MINISTRY INFORMATION</h4>
			<div class='form'>
				<table>
					<tr>
						<td><span style='font-weight:600;'>Address:</span></td>
					</tr>
					<tr>
						<td><input type="textbox" placeholder='Address Line #1' name="ministryAddress[line1]" value="<?php echo $user->ministry_address_line1 ?>" style='width:205px;'></td>
						<td style='width:100%'><input type="textbox" placeholder='Address Line #2' name="ministryAddress[line2]" value="<?php echo $user->ministry_address_line2 ?>" title='(Only needed if you have a PO Box or RR number)' ></td>
					</tr>
				</table>
				<table>
					<tr>
						<td><input type="textbox" placeholder='City' name="ministryAddress[city]" value="<?php echo $user->ministry_city ?>" style='width:152px;'></td>
						<td><input type="textbox" placeholder='Pr.'  name="ministryAddress[pr]" value="<?php echo $user->ministry_province ?>" maxlength="2" size='2'></td>
						<td><input type="textbox" placeholder='Country' name="ministryAddress[country]" value="<?php echo $user->ministry_country ?>" maxlength="2" size='2'></td>
						<td style='width:100%'><input type="textbox" style='width:100%' placeholder='PC' name="ministryAddress[pc]" value="<?php echo $user->ministry_postal_code ?>"></td>
					</tr>
				</table>
			</div>

			<div class='form'>
				<table>
					<tr>
						<td><span style='font-weight:600;'>Spouse Employee Number:</span></td>
					</tr>
					<tr>
						<td style='width:100%'>
							<input type="textbox" style='width:100%' placeholder='Spouse' name="spouseEmployeeNumber" value="<?php echo $user->spouse_employee_number ?>">
						</td>
					</tr>
				</table>
			</div>

			<div class="form">
				<table> <?php
				$phones	 = $wpdb-> get_results("SELECT * FROM phone_number WHERE employee_number = '" . $user->employee_number . "' AND is_ministry='1' ORDER BY share_phone DESC");

				if($phones){
					$last = end($phones); ?>
					<tr>
						<td colspan=2>
							<span style='font-weight:600;'>Phone Number(s): </span>
						</td>
					</tr> <?php
					foreach($phones as $phone){
						$isLast = $last === $phone;
						$id = $phone->phone_number_id;
						$contact = split("-", $phone->contact_number, 2); ?>
						<tr>
							<td>&nbsp;</td>
							<td style="text-align:left;"> <?php
							echo $phone->phone_type.':&nbsp;'.$phone->phone_number;
							if($phone->extension) { ?>
								&nbsp;Ext: <?php echo $phone->extension;
							}?>
						</td>
					</tr> <?php
				}
			} ?>
		</table>
	</div>

	<div class="form">
		<table>
			<?php
			$emails	 = $wpdb-> get_results("SELECT * FROM email_address WHERE employee_number = '" . $user->employee_number . "' AND is_ministry='1'");
			if($emails){
				$last = end($emails);?>
				<tr>
					<td colspan=2><span style="font-weight:600;">Ministry&nbsp;Email(s):</span></td>
				</tr> <?php
				foreach($emails as $email){ ?>
					<tr>
						<td>&nbsp;</td><?php
						$isLast = $last === $email;
						if ($isLast){ ?>
							<div style='position:relative;'> <?php
						}
						$id = $email->email_address_id;	?>
						<td style='width:100%'>&nbsp;<?php echo $email->email_address; ?></td>
						<?php
						if ($isLast){ ?>
						</div> <?php
					}?>
				</tr> <?php
			}
		} ?>
	</table>
</div>

<div class="form" id="editMinSocialMedia">
	<input type="text" placeholder='Website' name="ministryWebsite" value="<?php echo $user->ministry_website ?>" style="width:446px"><BR>
		<input type="text" placeholder='Twitter' name="ministryTwitter" value="<?php echo $user->ministry_twitter_handle ?>" style="width:446px"><BR>
			<input type="text" placeholder='Skype' name="ministrySkype" value="<?php echo $user->ministry_skype ?>" style="width:446px"><BR>
				<input type="text" placeholder='Facebook' name="ministryFacebook" value="<?php echo $user->ministry_facebook ?>" style="width:446px"><BR>
					<input type="text" placeholder='Instagram' name="ministryInstagram" value="<?php echo $user->ministry_instagram ?>" style="width:446px">
				</div>

				<h4 style='font-size:16pt'>PERSONAL INFORMATION</h4>
				<div class="form">
					<table>
						<tr>
							<td colspan=3><span style='font-weight:600;'>Address: </span></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td> <?php
							echo $user->address_line1;
							if($user->address_line2) {
								echo "<br>".$user->address_line2;
							}
							echo "<br>".$user->city.", ".$user->province."  ". $user->postal_code;
							echo "<br>".$user->country
							?>
						</td>
						<td>
							<select name="personalAddress[share]" style='width:90px;'>
								<option value="FULL" style="padding:10px 4px;" <?php if($user->share_address == 'FULL') { echo 'selected'; } ?>>Shared</option>
								<option value="NONE" style="padding:10px 4px;" <?php if($user->share_address == 'NONE') { echo 'selected'; } ?>>Not Shared</option>
							</select>
						</td>
					</tr>
				</table>
			</div>

			<div class="form" id="editPhone">
				<table>
					<?php
					$phones	 = $wpdb-> get_results("SELECT * FROM phone_number WHERE employee_number = '" . $user->employee_number . "' AND is_ministry='0' ORDER BY share_phone DESC");
					if($phones){
						$last = end($phones);?>
						<tr>
							<td colspan=3><span style='font-weight:600;'>Phone:</span></td>
						</tr>
						<?php
						foreach($phones as $phone){
							$isLast = $last === $phone;
							$id = $phone->phone_number_id;
							?>
							<tr>
								<td>&nbsp;</td>
								<td> <?php
								echo $phone->phone_type.':&nbsp;'.$phone->phone_number;
								if($phone->extension) { ?>
									&nbsp;Ext: <?php echo $phone->extension;
								}?>
							</td>
							<td>
								<select name="phone[<?php echo $id; ?>][share]" style="width:90px;">
									<option value="personalshare" style="padding:10px 4px;" <?php if ($phone->share_phone) { echo 'selected="selected"'; } ?>> Shared</option>
									<option value="personalnotshare" style="padding:10px 4px;" <?php if (!$phone->share_phone) {echo 'selected="selected"'; } ?>> Not Shared</option>
								</select>
							</td>
						</tr>
						<?php
					}
				} ?>
			</table>
		</div>

		<div class="form" id="editEmail">
			<table>
				<tr>
					<td colspan=3><span style='font-weight:600;'>Personal&nbsp;Email: </span></td>
				</tr>
				<?php
				$emails	 = $wpdb-> get_results("SELECT * FROM email_address WHERE employee_number = '" . $user->employee_number . "' AND is_ministry='0'");
				if($emails){
					$last = end($emails);
					foreach($emails as $email){
						$isLast = $last === $email;
						$id = $email->email_address_id; ?>
						<tr>
							<td>&nbsp;</td>
							<td><?php echo $email->email_address ?></td>
							<td>
								<select style='width:90px;' name="email[<?php echo $id; ?>][share]">
									<option value="1" style="padding:10px 4px;" <?php if($email->share_email) { echo 'selected'; } ?> >Shared</option>
									<option value="0" style="padding:10px 4px;" <?php if(!$email->share_email) { echo 'selected'; } ?> >Not Shared</option>
								</select>
							</td>
						</tr> <?php
					} //foreach
				} //if ?>
			</table>
		</div>

		<div class="form" id="editSocialMedia">
			<input type="text" placeholder='Website' name="personalWebsite" value="<?php echo $user->website ?>" style="width:446px"><BR>
				<input type="text" placeholder='Twitter' name="personalTwitter" value="<?php echo $user->twitter_handle ?>" style="width:446px"><BR>
					<input type="text" placeholder='Skype' name="personalSkype" value="<?php echo $user->skype ?>" style="width:446px"><BR>
						<input type="text" placeholder='Facebook' name="personalFacebook" value="<?php echo $user->facebook ?>" style="width:446px"><BR>
							<input type="text" placeholder='Instagram' name="personalInstagram" value="<?php echo $user->instagram ?>" style="width:446px">
						</div>

						<div class="form" id="updateNotes" style="padding-right:10px;padding-left:5px;">
							<span style='font-weight:600;'>About you (share a bit about yourself for other staff):</span><br />
							<textarea id="notes" name="notes" cols="60" rows="5"><?php echo $user->notes ?></textarea>
							<br />
							<br />
							<input class='orange' type="submit" onclick='alert("NOTE: To edit the street address, as well as ministry and personal phone numbers and email addresses, please go to Dayforce. Changes made there will typically show up in the Staff Directory within 24 hours.");' value="SAVE & VIEW PROFILE" style='padding:10px;letter-spacing:1px;font-weight:bold;font-size:16pt;cursor:pointer;' />

						</div>

					</form>
				</div>
			</div>
		</div>
		<div id="content-right" class="staff-directory download">
			<?php include('pro_sidebar.php') ?>
		</div>
		<div style='clear:both;'></div>
		<?php
		//Records that the user updated their profile so they are not warned for another year
		$update_profile_status = 0;
		$user_id = get_user_by( 'user_login', $login_of_person_being_edited );
		$user_profile_settings =  get_user_meta($user_id, 'update_profile');
		if(!empty($user_profile_settings)) {
			$update_profile_status = $user_profile_settings[0][0];
		}
		//Only update the reminder setting if the user has not selected "Never remind me again"
		if($update_profile_status != 3)
		update_user_meta($user_id, 'update_profile', array('1', date('Y-m-d', strtotime("+1 year"))));
		?>
