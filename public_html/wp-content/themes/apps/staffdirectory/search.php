<?php
/**
* Search.php
* 
* This is the search engine for finding staff in the Staff Directory. Builds a query, and returns results. 
*
*
*/?>
<p/><h4 class="profile"><a style="color:#adafb2;font-weight:bold;" href= "?page=profile" >MY PROFILE</a></h4>
<BR><BR><BR><BR>
<hr style='margin-top:0'>
<div style="clear:both"></div>
<div id="content-left">
	<div id="main-content">
		<?php
				$search = "";
				if (isset($_POST['search']) || isset($_GET['search'])) {
					if (isset($_POST['search'])) {
						$search = $_POST['search'];
					} elseif (isset($_GET['search'])) {
						$search = $_GET['search'];
					}
					echo "<p class='orange-box'  style='padding: 9px;'>SEARCH RESULTS FOR: \"".strtoupper($search)."\"</p> <p /><p/>";
				}
				else { //display welcome message if nothing being search for
					?><p class='orange-box' style='padding: 9px;'>WELCOME TO THE STAFF DIRECTORY!</p> <p /><p/>

		This application replaces the booklet version of the PTC Staff Address Book. You can search for other staff members by name, ministry, role title, or city.<p/> 

		Your personal information is all initially marked as "Private".  Please click on "My Profile" (above, right), upload a photo of yourself, update any incorrect information, and choose what you would like to share with other staff.<p/>
<?php
				}
				if(! empty($search)){ 
					$names = preg_split("/[\s,]+/", $search);  // split the search string by any number of commas or space characters,
					//this is where my intelligent search kicks in. here we try to identify some input
					//check if ministry
					$j=0;
					for($i=0; $i < count($names); $i++){ //for each term searched
						$ministry = ministry($names[$i]);
						if($ministry != false){
							$names[$i] = $ministry;
						}
					}
					//check if province/territory
					//JasonB: commented this out, as province abbreviations seem to return too many false-positive
					// search results.
					/*$j=0;
					for($i=0; $i < count($names); $i++){ //for erach term searched
						$province = province($names[$i]);
						if($province != false){
							$names[$i] = $province;
						}
					}*/
					
					//end of identification
					$j = 0; //next spot in array
					$alldata = array();
					//I apologise for the following code. I have to build my database query. This was not simple.
					//My recommendation for changing this is to echo a concatanation of all the query parts in order to see what it does
					
					//this section of the code concatenate all of the shared information in the database into one easy to search column.
					$concatquery = "
						
							CONCAT_WS(' ',
								IFNULL(first_name, ''), IFNULL(last_name, ''), IFNULL(ministry, ''), IFNULL(role_title, ''), IFNULL(region, ''), 
								IFNULL(ministry_address_line1, ''), IFNULL(ministry_address_line2, ''), IFNULL(ministry_address_line3, ''),
								IFNULL(ministry_city, ''), IFNULL(ministry_province, ''), IFNULL(ministry_country, ''), IFNULL(ministry_postal_code, ''),
								IFNULL(website, ''), IFNULL(twitter_handle, ''), IFNULL(skype, ''), IFNULL(ministry_website, ''), 
								IFNULL(ministry_twitter_handle, ''), IFNULL(ministry_skype, ''),
								IF( share_photo =1, IFNULL(photo, ''),  ' ' ) ,
								IF( share_address ='None', ' ', 
													CONCAT(
														IFNULL(province, '') ,
														IF( share_address ='PROVONLY', ' ', 
																		CONCAT(
																			IFNULL(city, ''),
																			IF( share_address ='CITY&PROV', ' ',
																							CONCAT(
																							IFNULL(address_line1,''),
																							IFNULL(address_line2,''),
																							IFNULL(address_line3,''),
																							IFNULL(postal_code,''))
																			)
																		)
														)
													)
								)
							) 
					";
					
					
					$queryPart1 = " SELECT *,  ";
					$queryPart2 = "";
					$queryPart4 = "";
					$first=true;			  
					for($i=0; $i < count($names); $i++){
						if(!$first){
							$queryPart2 = $queryPart2 . "+ ";
							$queryPart4 = $queryPart4 . " OR ";
						}
						else{
							$queryPart4 = $queryPart4 . "WHERE ";
							$first=false;
						}
						$queryPart2 = $queryPart2 . "(CASE WHEN " . $concatquery . " LIKE '%%%s%%' THEN 1 ELSE 0 END)  ";
						$queryPart4 = $queryPart4 . $concatquery . " LIKE '%%%s%%' ";
					}
					$queryPart3 = " AS relevance FROM employee ";
					$queryPart5 = " ORDER BY relevance DESC, first_name, last_name ";
					//echo $queryPart1 . $queryPart2 . $queryPart3. $queryPart4 . $queryPart5 ;
					$results = $wpdb-> get_results($wpdb->prepare($queryPart1 . $queryPart2 . $queryPart3. $queryPart4 . $queryPart5  , Search::twice($names)));

					foreach($results as $result){ //populate this array with data from query
						$data = objectToArray($result);
						$alldata[$j]['user_login'] = $data['user_login']; //we don't publish this
						$alldata[$j]['photo'] = $data['photo'];
						$alldata[$j]['first_name'] = $data['first_name'];
						$alldata[$j]['last_name']  = $data['last_name'];
						$alldata[$j]['role_title'] = $data['role_title'];
						$alldata[$j]['ministry'] = $data['ministry'];
						$alldata[$j]['share_photo'] = $data['share_photo'];
						$alldata[$j]['staff_account'] = $data['staff_account'];
						$j++;
					}	
				}
				
				
				if(! empty($alldata)){ //put the data into a nice table
					// Obtain a list of columns
					
					for($i = 0; $i < count($alldata); $i++){
						echo "<div class='person'><div style='float:left;width:50px;min-height:10px;'>";
						if(is_null($alldata[$i]['photo']) || $alldata[$i]['share_photo'] == 0){ //if we do not have a picture for this user
							$useAnonymous = true; // This will be the fall-back option
							$url = '';

							// If they have a staff account, attempt to use their public giving site 
							// photo; use the smaller "icon" image for search results
							if ($alldata[$i]['staff_account'] > '800000') {
								$url = "http://secure.powertochange.org/images/Product/icon/" . $alldata[$i]['staff_account'] . ".jpg";
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
								if (curl_getinfo($handle, CURLINFO_HTTP_CODE) == 200) {
									// It's VALID! So, use it instead of the anonymous image
									$useAnonymous = false;
								}
							}
							
							if ($useAnonymous) {
								// Use the standard image
								$url = "../../wp-content/uploads/staff_photos/anonymous.jpg";
							}
							
							echo '<img src="' . $url . '" width="50" />';
						}
						else { //if we do have a picture for this user
							echo '<a href="?page=profile&person=' . $alldata[$i]['user_login'] . '"><img src="../../wp-content/uploads/staff_photos/' . $alldata[$i]['photo'] . '" width="50" /></a>';
						}
						echo "</div><div style='float:left;margin:5px 13px;'>";
							echo '<a style="line-height:20px;" href ="?page=profile&person=' . $alldata[$i]['user_login'] . '">' . strtoupper ($alldata[$i]['first_name'] . ' ' . $alldata[$i]['last_name']) . '</a><BR>';
							echo  $alldata[$i]['role_title'].", " ;
							echo $alldata[$i]['ministry'] ;
						echo "</div><div style='clear:both'></div>";
							
						echo "</div>";
						echo "<hr style='border-color:#d6d7d4;margin:0;height:0;'>";
					}
				}

		?>
	</div>
</div>
<div id="content-right" class='download staff-directory staff-directory-sidebar' >   
	<?php the_content();
	include('pro_sidebar.php') ?>
	<script type="text/javascript">
	/* On this search page, set the focus to the search box */
	document.getElementById("staff-search").focus();
	</script>
</div><div style='clear:both;'></div>
<?php
	function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}
 
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__FUNCTION__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}
	class Search
	{
		//
		public static function twice($array){
			$length = count($array);
			for($i=0; $i < $length; $i++){
				$array[$i + count($array) ] = $array[$i]; 
			}
			return $array;
		}
	
	}

// Test to see if a word matches a key word or abbreviation associated with a certain ministry. If so,
// return the full ministry name
function ministry($ministry) { 
	switch (strtolower($ministry))
	{
		case 'aia':
			return 'Athletes in Action';
		case 'students':
			return 'P2C-Students';
		case 'student':
			return 'P2C-Students';
		case 'campus':
			return 'P2C-Students';
		case 'icn':
			return 'Intercultural Network';
		case 'global':
			return 'GAiN';
		case 'aid':
			return 'GAiN';
		case 'tm':
			return 'TruthMedia';
		case 'it':
			return 'Information Technology';
		case 'computer':
			return 'Information Technology';
		case 'fl':
			return 'FamilyLife';
		case 'hr':
			return 'Human Resources';
		case 'lig':
			return 'LeaderImpact';
			
		default:
			return false;
	}
}

//test to see if a word matches a key word associated with a certain province
function province($province) {
	switch(strtolower($province))
	{
		case 'ontario':
			return ' ON ';
		case 'brunswick':
			return ' NB ';
		case 'manitoba':
			return ' MB ';
		case 'nova':
			return ' NS ';
		case 'scotia':
			return ' NS ';
		case 'quebec':
			return ' QC ';
		case 'saskatchewan':
			return ' SK ';
		case 'alberta':
			return ' AB ';
		default:
			return false;
	
	}
}

?>
