<?php
function getEmployeesWhoReportToUser($user_id, $admin) {
	global $wpdb;
	
	if($admin) {
		$reportsToMeResults = $wpdb->get_results(
			$wpdb->prepare( 
			/* Get Everyone instead of the people that really report to the user*/
			"SELECT e.employee_number, e.user_login, CONCAT(e.first_name,' ',e.last_name) AS 'full_name'
			FROM employee e
			WHERE e.staff_account IS NOT NULL   
			ORDER BY CASE WHEN user_login = %s THEN 1 ELSE 2 END, full_name", $user_id));
	} else {
		/* For the Staff List report, we need the current user's employee number, and also a list
		 * of the staff who report to the current user */
		$reportsToMeResults = $wpdb->get_results(
			$wpdb->prepare( 
			/* First select is for the current user; next is for everyone who reports to the current user */
			"SELECT e.employee_number, e.user_login, '                           ' AS 'supervisor_login', CONCAT(e.first_name,' ',e.last_name) AS 'full_name'
			FROM employee e
			WHERE e.user_login = %s
			  AND e.staff_account IS NOT NULL
			  
			UNION
			
			SELECT e.employee_number, e.user_login, s1.user_login AS 'supervisor_login', CONCAT(e.first_name,' ',e.last_name) AS 'full_name'
			FROM employee e
			LEFT JOIN employee s1 ON s1.employee_number = e.supervisor
			LEFT JOIN employee s2 ON s2.employee_number = s1.supervisor
			LEFT JOIN employee s3 ON s3.employee_number = s2.supervisor
			LEFT JOIN employee s4 ON s4.employee_number = s3.supervisor
			LEFT JOIN employee s5 ON s5.employee_number = s4.supervisor
			LEFT JOIN employee s6 ON s6.employee_number = s5.supervisor
			LEFT JOIN employee s7 ON s7.employee_number = s6.supervisor
			WHERE %s IN (s1.user_login, s2.user_login, s3.user_login, s4.user_login, s5.user_login, s6.user_login, s7.user_login)
			  AND e.staff_account IS NOT NULL		

			ORDER BY CASE WHEN user_login = %s THEN 1
						  WHEN supervisor_login = %s THEN 2
						  ELSE 3 END,
				full_name", /* Order by current user first, then direct reports, then everyone else */
			$user_id, $user_id, $user_id, $user_id));	
	  
	  // Add on the extra entries from the employee manager table
	  for($z = 0; $z < 7; $z++) { //Up to 7 levels
		if($z == 0) {
		  $extrareportsToMeResults = $wpdb->get_results($wpdb->prepare( 
			"SELECT 
			employee_manager.employee_number, 
			employee.user_login,
			s1.user_login AS 'supervisor_login',
			CONCAT(employee.first_name,' ', employee.last_name  ) AS full_name
			FROM employee_manager
			LEFT OUTER JOIN employee ON employee.employee_number = employee_manager.employee_number
			LEFT OUTER JOIN employee s1 ON s1.employee_number = employee_manager.manager_employee_number
			WHERE s1.user_login = %s AND employee.staff_account IS NOT NULL", $user_id));
		} else {
		  $sql = "SELECT 
			employee_manager.employee_number, 
			employee.user_login,
			s1.user_login AS 'supervisor_login',
			CONCAT(employee.first_name,' ', employee.last_name  ) AS full_name
			FROM employee_manager
			LEFT OUTER JOIN employee ON employee.employee_number = employee_manager.employee_number
			LEFT OUTER JOIN employee s1 ON s1.employee_number = employee_manager.manager_employee_number
			WHERE s1.employee_number IN( %s ) AND employee.staff_account IS NOT NULL";
			
			$params = '';
			foreach($extrareportsToMeResults as $extra) {
			  if(!empty($params))
				$params .= ', ';
			  $params .= $extra->employee_number;
			}
		  $extrareportsToMeResults = $wpdb->get_results($wpdb->prepare($sql, $params));
		}
		//Check for duplicates
		foreach($extrareportsToMeResults as $extra) {
		  $duplicate = 0;
		  foreach($reportsToMeResults as $duplicateCheck) {
			if($duplicateCheck->employee_number == $extra->employee_number) {
			  $duplicate = 1;
			  break;
			}
		  }
		  //Add the person if they are not a duplicate
		  if(!$duplicate)
			array_push($reportsToMeResults, $extra);
		}
	  }
	}

	return $reportsToMeResults;
}


function generateYearMonthDropDown($fieldName, $selectedValue) {
	echo "<SELECT NAME='$fieldName' ID='$fieldName'><OPTION VALUE=''>--Year Month--</OPTION>";

	$CurrYear = date("Y");
	// Go back 10 years
	for ($yearIdx = $CurrYear; $yearIdx >= $CurrYear-10; $yearIdx--) {
		
		// Go through all the months (in reverse order so most recent is at top)
		for ($monthIdx = 12; $monthIdx >= 1; $monthIdx--) {
			$dateObj = DateTime::createFromFormat('!Y-m', $yearIdx.'-'.$monthIdx);
			$yearMonthValue = $dateObj->format('Y-m'); // This adds the leading 0 for 2-digit months
			$yearMonthLabel = $dateObj->format('Y F');
			echo '<OPTION VALUE="'.$yearMonthValue.'" ';
			
			// Determine if the current item is selected
			if ($selectedValue == $yearMonthValue) {
				echo 'SELECTED';
			}
			echo '>'.$yearMonthLabel.'</OPTION>';
		}
	}

	echo "</SELECT>";

}
?>