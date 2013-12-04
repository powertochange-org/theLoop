<?php
/**
* Search.php
* 
* This is the search engine for finding staff in the Staff Directory. Builds a query, and returns results. 
*
*
*/?>

<p/><h4 style="float:right"><a href= "?page=profile" >My Profile</a></h4>
<div style="clear:both">
</div>
<br/>
Welcome to the Staff Directory. <p /><p/>

This application replaces the booklet version of the PTC Staff Address Book.  Staff will now be able to search for other staff members in their city or region as well as look up details about other staff.  Feel free to use any parameters to begin your search.<p/> 

Your personal information is all initially marked as "Private".  Please click on "My Profile" (above, right), upload a photo of yourself, update any incorrect information, and choose what you would like to share with other staff.<p/>

We hope you enjoy using this new tool!<p/>
<form action="" method="post">
<input type='textbox' name='fullname' />
<input type='submit' name='report' value='Search' />
</form>
<?php
		$fullname = "";
		if (isset($_POST['fullname'])) {
			$fullname = $_POST['fullname'] ; //the entire thing that was searched for
		} 
		$names = preg_split("/[\s,]+/", $fullname);  // split the phrase by any number of commas or space characters,
		if(! empty($_POST['fullname'])){ //if user searched something, it'll live in post
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
			$j=0;
			for($i=0; $i < count($names); $i++){ //for erach term searched
				$province = province($names[$i]);
				if($province != false){
					$names[$i] = $province;
				}
			}
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
			$queryPart5 = " ORDER BY relevance DESC ";
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
				$j++;
			}	
		}
		if(! empty($alldata)){ //put the data into a nice table
			// Obtain a list of columns
			
			echo '<table>';
			echo '<tr>';
				echo '<th>Photo</th>';
				echo '<th>Name</th>';
				echo '<th>Role</th>';
				echo '<th>Ministry</th>';
			echo '</tr>';
			
			for($i = 0; $i < count($alldata); $i++){
				echo '<tr>';
					if(is_null($alldata[$i]['photo']) || $alldata[$i]['share_photo'] == 0){ //if we have a picture for this user
						//echo $alldata[$i]['share_photo'];
						echo '<td><center><img src="../wp-content/uploads/staff_photos/anonymous.jpg" width=50 /></center></td>';
					}
					else { //if we don't have a picture for this user
						echo '<td><a href ="?page=profile&person=' . $alldata[$i]['user_login'] . '"><center><img src="../wp-content/uploads/staff_photos/' . $alldata[$i]['photo'] . '" width=50 /></center></a></td>';
					}
					echo '<td><a href ="?page=profile&person=' . $alldata[$i]['user_login'] . '">' . $alldata[$i]['first_name'] . ' ' . $alldata[$i]['last_name'] . '</a></td>';
					echo '<td>' . $alldata[$i]['role_title'] . '</td>';
					echo '<td>' . $alldata[$i]['ministry'] . '</td>';
				echo '</tr>';
			}
			echo '</table>';
		}

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
	//test to see if a word matches a key word associated with a certain ministry
function ministry($ministry) { //see if the given word matches a keyword associated with a ministry
	switch (strtolower($ministry)) //load the specified site, or the dashboard by default
	{
		case 'athletes':
			return 'Athletes in Action';
		case 'aia':
			return 'Athletes in Action';
		case 'action':
			return 'Athletes in Action';
		case 'students':
			return 'Power to Change - Students';
		case 'student':
			return 'Power to Change - Students';
		case 'campus':
			return 'Power to Change - Students';
		case 'finance':
			return 'Finance';
		case 'finances':
			return 'Finance';
		case 'icn':
			return 'Intercultural Network';
		case 'intercultural':
			return 'Intercultural Network';
		case 'network':
			return 'Intercultural Network';
		case 'development':
			return 'Development';
		case 'international':
			return 'International';
		case 'resource':
			return 'PTC Resource Centre';
		case 'centre':
			return 'PTC Resource Centre';
		case 'center':
			return 'PTC Resource Centre';
		case 'zones':
			return 'Zones Teams';
		case 'zone':
			return 'Zones Teams';
		case 'teams':
			return 'Zones Teams';
		case 'team':
			return 'Zones Teams';
		case 'gain':
			return 'GAiN';
		case 'global':
			return 'GAiN';
		case 'aid':
			return 'GAiN';
		case 'ministries':
			return 'Ministries Office';
		case 'ministry':
			return 'Ministries Office';
		case 'office':
			return 'Ministries Office';
		case 'tm':
			return 'TruthMedia';
		case 'truth':
			return 'TruthMedia';
		case 'media':
			return 'TruthMedia';
		case 'it':
			return 'Information Technology';
		case 'information':
			return 'Information Technology';
		case 'technology':
			return 'Information Technology';
		case 'computer':
			return 'Information Technology';
		case 'fl':
			return 'FamilyLife';
		case 'family':
			return 'FamilyLife';
		case 'life':
			return 'FamilyLife';
		case 'hr':
			return 'Human Resources';
		case 'human':
			return 'Human Resources';
		case 'resource':
			return 'Human Resources';
		case 'resources':
			return 'Human Resources';
		case 'breakthrough':
			return 'Breakthrough Prayer Ministry';
		case 'prayer':
			return 'Breakthrough Prayer Ministry';
		case 'oasis':
			return 'Oasis';
		case 'connecting':
			return 'Connecting Streams';
		case 'streams':
			return 'Connecting Streams';
		case 'lig':
			return 'LIG';
		case 'leader':
			return 'LeaderImpact Group';
		case 'impact':
			return 'LeaderImpact Group';
		case 'drime':
			return 'DRIME';
		case 'christian':
			return 'Christian Embassy';
		case 'embassy':
			return 'Christian Embassy';
		case 'president':
			return "President's Office";
		case "president's":
			return "President's Office";
		case 'corporate':
			return 'Corporate Services';
		case 'services':
			return 'Corporate Services';
		case 'advancement':
			return 'Advancement';
		case 'church':
			return 'Church Relations';
		case 'relations':
			return 'Church Relations';
		case 'hq':
			return 'HQ Operations';
		case 'operation':
			return 'HQ Operations';
		case 'operations':
			return 'HQ Operations';
		case 'marketing':
			return 'Marketing & Communications';
		case 'communication':
			return 'Marketing & Communications';
		case 'communications':
			return 'Marketing & COmmunications';
			
		default:
			return false;
	}
}

//test to see if a word matches a key word associated with a certain province
function province($province) {
	switch(strtolower($province))
	{
		case 'ontario':
			return 'ON';
		case 'on':
			return 'ON';
		case 'brunswick':
			return 'NB';
		case 'nb':
			return 'NB';
		case 'manitoba':
			return 'MB';
		case 'mb':
			return 'MB';
		case 'nova':
			return 'NS';
		case 'scotia':
			return 'NS';
		case 'ns':
			return 'NS';
		case 'quebec':
			return 'QC';
		case 'qc':
			return 'QC';
		case 'saskatchewan':
			return 'SK';
		case 'sk':
			return 'SK';
		case 'alberta':
			return 'AB';
		default:
			return false;
	
	}
}

?>