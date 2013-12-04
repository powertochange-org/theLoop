<?php
/**
* Profile
* -This file is included to show another user's profile. A user will not view their own profile with this page, they
*  will be redirected to myprofile.
*
*
**/
$profile = $_GET['person']; //grab from URL the profile we want
	$user = $wpdb->get_row("SELECT * FROM employee WHERE user_login = '" . $profile . "'"); //go to DB and get 
?>
	<p/><h4 style="float:right"><a href= "?page=search" >Return to Search</a></h4>
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
				echo '<img src="../wp-content/uploads/staff_photos/anonymous.jpg" width=290 />';
			}
			else { //we have a photo and can share it
				echo	 '<img src="../wp-content/uploads/staff_photos/' . $user->photo . '"  width=290 />';
			}
			echo str_replace("\\", "", $user->notes); //this displays the 'About me' section located beneath the profile pic
			?>
			</div><!--left-->
			<div class="right">
			<?php include "infocard.php"; ?>
			</div>
