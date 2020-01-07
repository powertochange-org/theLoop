<?php
/**
* Search.php
* 
* This is the search engine for finding staff in the Staff Directory. Builds a query, and returns results. 
*
*
*/
?>
<p/><h4 class="profile"><a class="profile-link" href= "?page=profile" >SEARCH ALL</a></h4>
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
				}
				
				?>
				<h1 style='font-size:25pt;font-family:Roboto Slab;font-weight:100;'>Search for Staff</h1>
				<form id='s_s_s' method="GET" action="">
					<div class='staff-search-box' 
						<?php if (!(isset($_POST['search']) || isset($_GET['search']))) { ?>  style="margin-top:40px;margin-bottom:40px;" <?php } else { ?> 
							style="margin-bottom:20px;" <?php } ?>
							>
						<input id='staff-search-main' class='search-input staff-search-input' type='textbox' name='search' placeholder='name, job title, ministry, city, postal code' value='<?php echo $search;?>' autocomplete="off"/>
						<img onclick="document.getElementById('s_s_s').submit();" class='search-img' src='<?php bloginfo('template_url'); ?>/img/search-bw.png'>
					</div>
					<?php if (!(isset($_POST['search']) || isset($_GET['search']))) { ?>
					<p>You can search using any relevant keywords, like name, job title, ministry, city, postal code, etc.</p> <?php } ?>
				</form>
				
				<?php
				if (isset($_POST['search']) || isset($_GET['search'])) {
					$searchTerm = $search;
					if($searchTerm == '' && isset($_GET['ministryname']))
						$searchTerm = $_GET['ministryname'];
					echo "<p class='orange-box' style='padding: 9px; margin-top:40px;'>SEARCH RESULTS FOR: \"".strtoupper($searchTerm)."\"</p> <br>";
				}
				
				
				if(true){ 
					/*$names = preg_split("/[\s,]+/", $search, -1, PREG_SPLIT_NO_EMPTY);  // split the search string by any number of commas or space characters,
					//this is where my intelligent search kicks in. here we try to identify some input
					//check if ministry
					$j=0;
					for($i=0; $i < count($names); $i++){ //for each term searched
						$ministry = ministry($names[$i]);
						if($ministry != false){
							$names[$i] = $ministry;
						}
					}*/
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
							IFNULL(first_name, ''), IFNULL(last_name, ''), IFNULL(ministry, ''), IFNULL(role_title, ''), 
							IFNULL(ministry_address_line1, ''), IFNULL(ministry_address_line2, ''), IFNULL(ministry_address_line3, ''),
							IFNULL(ministry_city, ''), IFNULL(ministry_province, ''), IFNULL(ministry_country, ''), IFNULL(ministry_postal_code, ''),
							IFNULL(website, ''), IFNULL(twitter_handle, ''), IFNULL(skype, ''), IFNULL(ministry_website, ''), 
							IFNULL(ministry_twitter_handle, ''), IFNULL(ministry_skype, ''),
							IFNULL(city, ''),
							IF(share_address='FULL', 
								CONCAT_WS(' ',
									IFNULL(address_line1,''),
									IFNULL(address_line2,''),
									IFNULL(address_line3,''),
									IFNULL(postal_code,'')),
								' '
							)
						)";
					
					$queryPart1 = " SELECT user_login, photo, first_name, last_name, role_title, ministry, share_photo, staff_account  ";
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
					$queryPart3 = " FROM employee ";
					$queryPart5 = " ORDER BY last_name, first_name  ";
					
					$results = $wpdb-> get_results($wpdb->prepare($queryPart1 . $queryPart3 . $queryPart5 , null ));
					//var_dump($results);

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
				
				echo '<br>';
				echo '<table style="border:1px solid black;">';
				if(! empty($alldata)){ //put the data into a nice table
					// Obtain a list of columns
					for($i = 0; $i < count($alldata); $i++){
						echo '<tr style="border:1px solid black;"><td><a style="line-height:20px;" href ="?page=profile&person=' . $alldata[$i]['user_login'] . '">' . strtoupper ($alldata[$i]['first_name'] . ' ' . $alldata[$i]['last_name']) . '</a></td>';
							
						echo '<td>'.$alldata[$i]['first_name'].'</td>';
						echo '<td>'.$alldata[$i]['last_name'].'</td>';
							
						echo '<td>'. $alldata[$i]['role_title'].", ". $alldata[$i]['ministry'] .'</td>';
							
						if(is_null($alldata[$i]['photo']) || $alldata[$i]['share_photo'] == 0){ //if we do not have a picture for this user
							$useAnonymous = true; // This will be the fall-back option
							$url = '';

							// If they have a staff account, attempt to use their public giving site 
							// photo; use the smaller "icon" image for search results
							if ($alldata[$i]['staff_account'] > '800000') {
								// Attempt to use their public giving site photo
				                $url = "https://secure.powertochange.org/images/Product/medium/" . $alldata[$i]['staff_account'] . ".jpg";
				                // Check to see if that url is valid
				                $code = getHttpResponseCode_using_curl($url);
				                // If it's INVALID (ie, user doesn't have a giving site image)
				                // Use the standard image
				                if($code == 200) // It's VALID! So, use it instead of the anonymous image
									$useAnonymous = false;
							}
							
							if ($useAnonymous) {
								// Use the standard image
								$url = "../../wp-content/uploads/staff_photos/anonymous.jpg";
							}
							if($useAnonymous)
								echo '<td>NO PHOTO</td>';
							else
								echo '<td>GIVING PHOTO</td>';
							
							if($useAnonymous)
								echo '<td></td>';
							else
								echo '<td><img src="' . $url . '" width="50" /></td>';
						}
						else { //if we do have a picture for this user
							echo '<td>LOOP PHOTO</td>';
							echo '<td><img src="../../wp-content/uploads/staff_photos/' . $alldata[$i]['photo'] . '" width="50" /></td>';
						}
						
						echo '</tr>';
					}
				}
				echo '</table>';

		?>
	</div>
</div>
<div id="content-right" class='download staff-directory staff-directory-sidebar' >   
	<?php the_content();
	include('pro_sidebar.php') ?>
	
</div><div style='clear:both;'></div>
<script type="text/javascript">
	/*Create shadow effect on the search box. CSS alone won't work.*/
	var textareas = document.getElementsByClassName('staff-search-input');
	for (i = 0; i < textareas.length; i++){
	    if (textareas[i].parentNode.tagName.toString().toLowerCase() == 'div') {
	        textareas[i].onfocus = function(){
	            this.parentNode.style.boxShadow = '0px 0px 5px 1px #000000';
	        }
	        textareas[i].onblur = function(){
	            this.parentNode.style.boxShadow = '0px 0px 5px 1px #888888';
	        }
	    }
	}
</script>
<script type="text/javascript">
	/* On this search page, set the focus to the search box */
	document.getElementById("staff-search-main").focus();
</script>
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
