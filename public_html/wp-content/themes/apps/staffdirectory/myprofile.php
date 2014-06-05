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
				top:2px;'
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

		<p/><h4 style="float:right;position:relative;top:30px;"><a class='false-link' onclick='updateCoords(jcrop_api.tellSelect()); getElementById("theForm").submit()' style="color:#adafb2;font-weight:bold;">SAVE & VIEW PROFILE</a></h4><BR><BR><BR><BR>
	<hr style='margin-top:0'>
	<div style="clear:both"></div>
	<div id="content-left">
		<div id="main-content">
			<p class='orange-box'><?php	echo "<span style='font-weight:bold;color:#ffffff;font-size:16pt'>".strtoupper ("$user->first_name $user->last_name")."<span style='font-weight:normal;color:#ffffff'> | </span></span>$user->role_title, $user->ministry"; ?></p> <p></p>
			<div style='float:left'>
            <script src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.Jcrop.min.js"></script>
			<script type="text/javascript">
                var jcrop_api;

                $(document).ready(function() {
                    // Set up the change function for the file element 
                    jQuery("#file").change(function () {
                        // If this browser supports the FileReader API
                        if (window.FileReader) {
                            // Toggle the buttons
                            $(".changepic").toggle();
                            // Set up the jcrop:
                            jQuery(function($) {
                                $('#photo').Jcrop({
                                    bgColor: 'white',
                                    boxWidth: $("#photo").width() // Limit the width to the same as the current image being displayed
                                },function(){
                                    jcrop_api = this;
                                    jcrop_api.disable();

                                    // Create a new filereader
                                    var fRead = new FileReader();

                                    // Get the first file
                                    fRead.readAsDataURL($("#file")[0].files[0]);
                                    
                                    // Once we're done loading...
                                    fRead.onload = function () {
                                        // Set the source of the preview image to this new image
                                        jcrop_api.setImage(fRead.result, function() {
                                            jcrop_api.enable();
                                            jcrop_api.setOptions({
                                                trueSize: [
                                                    $('.jcrop-holder img')[0].naturalWidth,
                                                    $('.jcrop-holder img')[0].naturalHeight
                                                ]
                                            });
                                        });
                                    }
                                });
                            });
                        } else { // browser doesn't support filereader
                            // Immediately upload; don't support any cropping
                            document.getElementById("theForm").submit();
                        }
				    });
                });

                // This function updates the coordinates of some form values, 
                // based off of a passed-in object that has the values of the
                // crop values
                function updateCoords(c) {
                    $('#x').val(c.x);
                    $('#y').val(c.y);
                    $('#width').val(c.w);
                    $('#height').val(c.h);
                }


			</script>
				<?php
				if(is_null($user->photo)){ //if we don't have a photo
					echo '<img id="photo" style="display:block" src="/wp-content/uploads/staff_photos/anonymous.jpg" width=220 />';?>
					<input class='orange changepic' id="addpic" type="button" onclick='$("#file").click();' value="ADD IMAGE">
				<?php }
				else { //we have a photo and can share it
					echo '<img id="photo" style="display:block" src="/wp-content/uploads/staff_photos/' . $user->photo . '"  width=220 />'; ?>
					<input class='orange changepic' id="addpic" type="button" onclick='$("#file").click();' value="CHANGE IMAGE" >
				<?php } ?>
                <div style="border-radius:5px; margin: 2px 0px; box-sizing:border-box; -moz-box-sizing:border-box; -webkit-box-sizing:border-box; display: none;" class="changepic">
                    <p>Notes:</p>
                    <ul style='list-style-position: inside; font-size: 12px; line-height:normal;' class='orange'>
                        <li>
                            You can click and drag on the photo to crop it 
                        </li>
                        <li>
                            To reset your cropping area, click on the image somewhere outside the current crop area
                        </li>
                        <li>
                            When you're finished, click on "Save & View Profile"
                        </li>
                    </ul>
                </div>
			</div>

			<div style='float:left;padding-left:23px;width:457px'>

			<form onsubmit="updateCoords(jcrop_api.tellSelect());" id='theForm' action="?page=profile" method="post" enctype='multipart/form-data'>
			
            <!-- These are fields for the photo upload stuff -->
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size ?>">
			<input id="file" type="file" name="file" style='display:none;' accept="image/png,image/gif,image/jpeg">
            <!-- Hidden inputs for coordinates -->
	        <input type="hidden" id="x" name="x" />
	        <input type="hidden" id="y" name="y" />
	        <input type="hidden" id="width" name="width" />
	        <input type="hidden" id="height" name="height" />

			<h4 style='font-size:16pt'>MINISTRY INFORMATION</h4>
			<div class='form'>
				<table><tr>
					<td><span style='font-weight:600;'>Address:</span></td>
					<td><input type="textbox" placeholder='Address Line #1' name="ministryAddress[line1]" value="<?php echo $user->ministry_address_line1 ?>" style='width:205px;'></td>
					<td style='width:100%'><input type="textbox" placeholder='Address Line #2' name="ministryAddress[line2]" value="<?php echo $user->ministry_address_line2 ?>" title='(Only needed if you have a PO Box or RR number)' style='width:100%'></td>
				</tr></table><table><tr>
					<td><input type="textbox" placeholder='City' name="ministryAddress[city]" value="<?php echo $user->ministry_city ?>" style='width:152px;'></td>
					<td><input type="textbox" placeholder='Pr.'  name="ministryAddress[pr]" value="<?php echo $user->ministry_province ?>" maxlength="2" size='2'></td>
					<td><input type="textbox" placeholder='Country' name="ministryAddress[country]" value="<?php echo $user->ministry_country ?>" maxlength="2" size='2'></td>
					<td style='width:100%'><input type="textbox" style='width:100%' placeholder='PC' name="ministryAddress[pc]" value="<?php echo $user->ministry_postal_code ?>"></td>
				</tr></table>
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
				?><table><tr>
						<td><span style='font-weight:600;'>Phone: </span></td>
						<td><input type="text" placeholder='Country' name="phone[<?php echo $id; ?>][country]" value="<?php echo $phone->country_code ?>" maxlength="3" style="width:52px" /></td>
						<td><input type='hidden' name='phone[<?php echo $id; ?>][share]' value='ministryshare' ></td>
						<td><input type='hidden' name='phone[<?php echo $id; ?>][type]' value='BUS' ></td>
						<td>(</td>
						<td><input type="text" placeholder='Area' name="phone[<?php echo $id; ?>][area]" value="<?php echo $phone->area_code ?>" maxlength="3" style="width:52px" /></td>
						<td>)</td>
						<td><input type="text" name="phone[<?php echo $id; ?>][part1]" value="<?php echo $contact[0] ?>" maxlength="3" style="width:52px" /></td>
						<td>-</td>
						<td><input type="text" name="phone[<?php echo $id; ?>][part2]" value="<?php echo $contact[1] ?>" maxlength="4"style="width:60px" /></td>
						<td style='width:100%'> <input type="text" style='width:100%' placeholder='Ext' name="phone[<?php echo $id; ?>][ext]" value="<?php echo $phone->extension ?>" maxlength="10"/></td>
					 </tr></table>
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
				<input type='hidden' name='phone[-1][share]' value='ministryshare' >
				<input type='hidden' name='phone[-1][type]' value='BUS' >
				<table><tr>
					<td><?php $width = 245; $name = 'phone[-1][country]'; require("countrycodes.php");  ?></td>
					 <td>(</td>
					 <td><input type="text" placeholder='Area' name="phone[-1][area]" value="" maxlength="3" style="width:30px" /></td>
					 <td>)</td>
					 <td><input type="text" name="phone[-1][part1]" value="" maxlength="3" style="width:30px" /></td>
					 <td>-</td>
					 <td><input type="text" name="phone[-1][part2]" value="" maxlength="4" style="width:40px" /></td>
					 <td style='width:100%'><input type="text" style='width:100%' placeholder='Ext' name="phone[-1][ext]" value="" maxlength="10"/></td>
				 </tr></table>
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
					$id = $email->email_address_id;	?>
					<div class="form">
					<table><tr>
						<td><span style="font-weight:600;">Ministry&nbsp;Email: </span></td>
						<?php	//don't allow editing of powertochange.org address
						if(strpos(strtolower($email->email_address),'powertochange.org') === false) { ?>
							<td style='width:100%'><input type="text" style='width:100%' name="email[<?php echo $id; ?>][email]" value="<?php echo $email->email_address; ?>" /></td>
						<?php }	else{ ?>
							<td style='width:100%'><input type="text" style='width:100%' value='<?php echo $email->email_address; ?>' disabled /></td>
						<?php }
					echo "</tr></table>";
					echo "</div>";
					if ($isLast){
						echo "<img class='false-link plus' src='".get_stylesheet_directory_uri()."/res/plus.png' width='14' height='14' onclick='$(\"#addMinEmail\").slideToggle()'>";
						echo "</div>";
					}
				}
			}
			?>
			<div class="form" id="addMinEmail" <?php echo ($emails ? "style='display:none'" : "") ?> >
				<input type="text"  placeholder='Ministry Email' name="email[-1][email]" style="width:446px"/>
			</div>
			<div class="form" id="editMinSocialMedia">
				<input type="text" placeholder='Website' name="ministryWebsite" value="<?php echo $user->ministry_website ?>" style="width:446px"><BR>
				<input type="text" placeholder='Twitter' name="ministryTwitter" value="<?php echo $user->ministry_twitter_handle ?>" style="width:446px"><BR>
				<input type="text" placeholder='Skype' name="ministrySkype" value="<?php echo $user->ministry_skype ?>" style="width:446px"><BR>
				<input type="text" placeholder='Facebook' name="ministryFacebook" value="<?php echo $user->ministry_facebook ?>" style="width:446px">
			</div>
			<h4 style='font-size:16pt'>PERSONAL INFORMATION</h4>
			<div class="form">
				<table><tr>
					<td><span style='font-weight:600;'>Address: </span></td>
					<td><input type="textbox" placeholder='Address Line #1' name="personalAddress[line1]" value="<?php echo $user->address_line1 ?>" style="width:205px"></td>
					<td style='width:100%'><input type="textbox" style='width:100%' placeholder='Address Line #2' name="personalAddress[line2]" value="<?php echo $user->address_line2 ?>" title='(Only needed if you have a PO Box or RR number)' ></td>
				</tr></table><table><tr>
					<td><input type="textbox"placeholder='City' name="personalAddress[city]" value="<?php echo $user->city ?>" style="width:130px"></td>
					<td><input type="textbox" placeholder='Pr.' name="personalAddress[pr]" value="<?php echo $user->province ?>" maxlength="2" style="width:30px"></td>
					<td><input type="textbox" placeholder='Country' name="personalAddress[country]" value="<?php echo $user->country ?>" maxlength="2" style="width:30px"></td>
					<td><input type="textbox" placeholder='PC' name="personalAddress[pc]" value="<?php echo $user->postal_code ?>" style="width:110px"></td>
					<td style='width:100%'><select name="personalAddress[share]" style='width:100%'>
						<option value="FULL" <?php if($user->share_address == 'FULL') { echo 'selected'; } ?>>Shared</option>
						<option value="NONE" <?php if($user->share_address == 'NONE') { echo 'selected'; } ?>>Not Shared</option>
					</select></td>
				</tr></table>
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
					<table><tr>
						<td><span style='font-weight:600;'>Phone:</span></td>
						<td><input type="text" name="phone[<?php echo $id; ?>][country]" value="<?php echo $phone->country_code ?>" maxlength="3" style="width:30px" /></td>
						<td><select name="phone[<?php echo $id; ?>][share]" >
							<option value="personalshare" <?php if ($phone->share_phone) { echo 'selected="selected"'; } ?>> Shared</option>
							<option value="personalnotshare" <?php if (!$phone->share_phone) {echo 'selected="selected"'; } ?>> Not Shared</option>
						</select></td>
						<td><select name="phone[<?php echo $id; ?>][type]">
							<option value="CELL" <?php if ($phone->phone_type == 'CELL') { echo 'selected="selected"'; } ?>>Cell</option>
							<option value="HOME" <?php if ($phone->phone_type == 'HOME') { echo 'selected="selected"'; } ?>>Home</option>
							<option value="FAX" <?php if ($phone->phone_type == 'FAX') { echo 'selected="selected"'; } ?>>Fax</option>
							<option value="ALT" <?php if ($phone->phone_type == 'ALT') { echo 'selected="selected"'; } ?>>Other</option>
						</select></td>
						<td>(</td>
						<td><input type="text" name="phone[<?php echo $id; ?>][area]" value="<?php echo $phone->area_code ?>" maxlength="3" style="width:27px" /></td>
						<td>)</td>
						<td><input type="text" name="phone[<?php echo $id; ?>][part1]" value="<?php echo $contact[0] ?>" maxlength="3" style="width:27px" /></td>
						<td>-</td>
						<td><input type="text" name="phone[<?php echo $id; ?>][part2]" value="<?php echo $contact[1] ?>" maxlength="4" style="width:35px" /></td>
						<td style='width:100%'><input type="text" style='width:100%' placeholder="Ext." name="phone[<?php echo $id; ?>][ext]" value="<?php echo $phone->extension ?>" maxlength="10" /></td>
					</tr></table>
					 <?php
					echo '</div>';
					if ($isLast){
						echo "<img class='false-link plus' src='".get_stylesheet_directory_uri()."/res/plus.png' width='14' height='14' onclick='$(\"#phone\").slideToggle()'>";
						echo "</div>";
					}
				}
			} ?>

			<div class="form" id="phone" <?php echo ($phones ? "style='display:none'" : "") ?> >
				<table><tr>
					<td><select name="phone[-2][share]" style="width:67px">
						<option value="personalshare" > Shared</option>
						<option value="personalnotshare" > Not Shared</option>
					</select></td>
					<td><select name="phone[-2][type]">
						<option value="CELL">Cell</option>
						<option value="HOME">Home</option>
						<option value="FAX">Fax</option>
						<option value="OTHER">Other</option>
					</select></td>
					<td><?php $width=100; $name = 'phone[-2][country]'; require("countrycodes.php"); ?></td>
					 <td>(</td>
					 <td><input type="text" name="phone[-2][area]" value="" maxlength="3" style="width:27px" /></td>
					 <td>)</td>
					 <td><input type="text" name="phone[-2][part1]" value="" maxlength="3" style="width:27px" /></td>
					 <td>-</td>
					 <td><input type="text" name="phone[-2][part2]" value="" maxlength="4" style="width:35px" /></td>
					 <td style='width:100%'><input type="text" style='width:100%' placeholder='Ext.' name="phone[-2][ext]" value="" maxlength="10" /></td>
				</tr></table>
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
					$id = $email->email_address_id; ?>
					<div class="form">
					<table><tr>
						<td><span style='font-weight:600;'>Personal&nbsp;Email: </span></td>
						<td><input type="text" name="email[<?php echo $id; ?>][email]" value="<?php echo $email->email_address ?>" style="width:260px"/></td>
						<td style='width:100%'><select style='width:100%' name="email[<?php echo $id; ?>][share]">
							<option value="1" <?php if($email->share_email) { echo 'selected'; } ?> >Shared</option>
							<option value="0" <?php if(!$email->share_email) { echo 'selected'; } ?> >Not Shared</option>
						</select></td>
					</tr></table>
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
				<table><tr>
					<td><input type="text" placeholder='Personal Email' name="email[-2][email]" style="width:350px"/></td>
					<td><select name="email[-2][share]" style='width:100%'>
						<option value="1" <?php if($email->share_email) { echo 'selected'; } ?> >Shared</option>
						<option value="0" <?php if(!$email->share_email) { echo 'selected'; } ?> >Not Shared</option>
					</select></td>
				</tr></table>
			</div>
			<div class="form" id="editSocialMedia">
				<input type="text" placeholder='Website' name="personalWebsite" value="<?php echo $user->website ?>" style="width:446px"><BR>
				<input type="text" placeholder='Twitter' name="personalTwitter" value="<?php echo $user->twitter_handle ?>" style="width:446px"><BR>
				<input type="text" placeholder='Skype' name="personalSkype" value="<?php echo $user->skype ?>" style="width:446px"><BR>
				<input type="text" placeholder='Facebook' name="personalFacebook" value="<?php echo $user->facebook ?>" style="width:446px">
			</div>
			<div class="form" id="updateNotes" style="padding-right:10px;padding-left:5px;">
				Personal Message:
				<textarea id="notes" name="notes" cols="40" rows="5"><?php echo str_replace("\\", "", $user->notes); ?></textarea>
				<input class='orange' type="submit" value="SAVE & VIEW PROFILE" style='padding:10px;letter-spacing:1px;font-weight:bold;font-size:16pt;' />
			</div>
			</form>
			</div>
		</div>
	</div>
	<div id="content-right">
		<?php include('pro_sidebar.php') ?>
</div><div style='clear:both;'></div>
