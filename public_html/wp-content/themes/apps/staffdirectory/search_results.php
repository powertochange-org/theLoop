<form action="" method="post">
<input type='textbox' name='fullname' />
<input type='submit' name='report' value='Search' />
</form>
<?php
/**
* This is mostly a place to test things
*
*
**/
		/*SELECT *,
		3956 * 2 * ASIN(SQRT( POWER(SIN((orig.lat - dest.lat) *  pi()/180 / 2), 2) +COS(orig.lat * pi()/180) * COS(dest.lat * pi()/180) * POWER(SIN((orig.lon -dest.lon) * pi()/180 / 2), 2) ))
		asdistance FROM users destination, users originWHERE origin.id=userid
		and destination.longitudebetween lon1 and lon2 and destination.latitudebetween lat1 and lat2 
*/
		queryBuilder('jordan');
		$fullname = $_POST['fullname'] ;
		$names = preg_split("/[\s,]+/", $fullname);  // split the phrase by any number of commas or space characters,
		if(! empty($_POST['fullname'])){
			//check if postal code. where L is a letter and N is a number, the format is either LNLNLN or LNL NLN
			for($i=0; $i < count($names); $i++){
				if(strlen($names[$i]) == 6){
					$output = postal($names[$i]);
					if($output){
						echo 'Postal Code<p />';
					}
				}
				else if(strlen($names[$i]) == 3){
					$output = postal($names[$i] . $names[$i+1]);
					//echo $output;
					if($output){
						$i++;
						echo 'Postal Code<p />';
					}
				}
				
			}
			//check if ministry
			$j=0;
			for($i=0; $i < count($names); $i++){
				$ministry = ministry($names[$i]);
				if($ministry != false){
					$ministries[$j] = $ministry;
					$j++;
				}
			}
			//check if province/territory
			$j=0;
			for($i=0; $i < count($names); $i++){
				$province = province($names[$i]);
				if($province != false){
					$provinces[$j] = $province;
				}
			}
			//check if email
			for($i=0; $i < count($names); $i++){
				$email = isEmail($names[$i]);
				if($email){
					echo "Email";
				}
			}
		}
function postal($d) {	
	$postal = preg_match("/^([a-ceghj-npr-tv-z]){1}[0-9]{1}[a-ceghj-npr-tv-z]{1}[0-9]{1}[a-ceghj-npr-tv-z]{1}[0-9]{1}$/i", $d);
	
	if ($postal){
		return true;
	}
	else{
		return false;
	}
	
	
}
//check if $email is a proper email address
function isEmail($email) { 
	if (filter_var($email, FILTER_VALIDATE_EMAIL)){
		return true;
	}
	else{
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
		default:
			return false;
	
	}
}
//test to see if a word matches a key word associated with a certain ministry
function ministry($ministry) { //see if the given word matches a keyword associated with a ministry
	switch (strtolower($ministry)) //load the specified site, or the dashboard by default
	{
		case 'athletes':
			return 'Athletes in Action';
			break;
		case 'action':
			return 'Athletes in Action';
		case 'AIA':
			return 'Athletes in Action';
		case 'students':
			return 'Power to Change - Students';
		case 'student':
			return 'Power to Change - Students';
		case 'campus':
			return 'Power to Change - Students';
		case 'P2CS':
			return 'Power to Change - Students';
		case 'finance':
			return 'Finance';
		case 'finances':
			return 'Finance';
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
			return 'Zones Team';
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
		case 'family':
			return 'FamilyLife';
		case 'life':
			return 'FamilyLife';
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

function distance($lat1, $long1, $lat2, $long2){


}

function queryBuilder($s){
	$query = "
				SELECT 
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
					) AS info, (CASE WHEN CONCAT_WS(' ',
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
					) LIKE '%%%s%%' THEN 1 ELSE 0 END) AS relevance
						
					FROM employee ORDER BY relevance DESC
				
				
			";
	$results = $wpdb-> get_results($wpdb->prepare($query, $s));
	
	$j=0;
	foreach($results as $result){
		echo 'here I am ';
		$data = objectToArray($result);
		$alldata[$j]['user_login'] = $data['user_login'];
		$j++;
	}
	return true;
}
?>