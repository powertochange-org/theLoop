<div id="sidebar" class="staff-directory-sidebar">
	<div class="sidebaritem-staff-directory">
		<div id='simple-search-staff'>
			<? /* Use GET method instead of POST so the URL to get to specific search results is
			      visible to the end-user, and can be bookmarked or linked to */ ?>
			<h3><p style='padding: 9px;font-size:24px; color:#f7941d;'>WELCOME TO THE STAFF DIRECTORY!</p></h3>
			<br>
			<p>Your personal information is all initially marked as "Private".  Please click on "MY PROFILE" (above, right) to go to your personal profile page.  Once there, click "EDIT MY PROFILE" to upload a photo of yourself, update any incorrect information, and choose what you would like to share with other staff.  Save your changes by clicking the "SAVE & VIEW PROFILE" button that will appear at the bottom of the page.  To view your profile without saving changes when on the editing page, simply click "VIEW PROFILE."</p>
			<?php /*<!-- BR>
			<a class='false-link' onclick='$("#simple-search-staff").hide();$("#advanced-search-staff").show();'><h2>Show Adavanced Search</h2></a>*/?>
		</div>
		<?php /*<div id='advanced-search-staff' style='display:none'>
			<form id='a_s_s' method="post" action="">
				<input class='a_search' type='textbox' name='first_name' placeholder='First Name' />
				<input class='a_search' type='textbox' name='last_name' placeholder='Last Name' />
				<input class='a_search' type='textbox' name='ministry' placeholder='Ministry' />
				<input class='a_search' type='textbox' name='city' placeholder='City' />
				<input class='a_search' type='textbox' name='province' placeholder='Province' />

				<input value='Search' style='background-color:#f7941d;border:0px solid #000000;color:#ffffff;border-radius:5px;padding:3px 6px;font-size:12px;' type='submit' />
			</form>
			<BR>
      <a class='false-link' onclick='$("#advanced-search-staff").hide();$("#simple-search-staff").show();'><h2>Show Basic Search</h2></a>
		</div>*/?>
	</div>
</div>
