<div id="sidebar" class="staff-directory-sidebar">
	<div class="sidebaritem-staff-directory">
		<h1 style='font-size:25pt;font-family:Roboto Slab;font-weight:100;'>Search for Staff</h1><BR>
		
		<div id='simple-search-staff'>
			<? /* Use GET method instead of POST so the URL to get to specific search results is
			      visible to the end-user, and can be bookmarked or linked to */ ?>
			<form id='s_s_s' method="GET" action=""><div class='search-box' style='border-color:#adafb2'>
				<input id='staff-search' class='search-input' type='textbox' name='search' placeholder='Search' />
				<img onclick="document.getElementById('s_s_s').submit();" class='search-img' src='<?php bloginfo('template_url'); ?>/img/search-bw.png'>
			</div></form>
			<BR>
			<p>You can search using any relevant keywords, like name, job title, ministry, city, postal code, etc.</p>
			<!-- BR>
			<a class='false-link' onclick='$("#simple-search-staff").hide();$("#advanced-search-staff").show();'><h2>Show Adavanced Search</h2></a -->
		</div>
		<div id='advanced-search-staff' style='display:none'>
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
		</div>
	</div>
</div>