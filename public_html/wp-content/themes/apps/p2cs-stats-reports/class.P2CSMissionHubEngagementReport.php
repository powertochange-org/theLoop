<?php

/****************************************************************************************************
 *
 * Name: 		P2CSMissionHubEngagementReport
 *
 * Description: This class is used to generate a report on how many students are engaged at
 *				various levels with each campus. It depends on some helper functions defined
 *				in missionhuborganizations.php. 
 *
 * Author:		Nathaniel Faries / Jason Brink
 *
 ***************************************************************************************************/

require_once("class.P2CSReport.php");
require_once('missionhuborganizations.php');

class P2CSMissionHubEngagementReport extends P2CSReport {
	public function hasParameters() {
		return true;
	}
	
	public function renderParameters() {
		echo createOrganizationsDropDownList();
	}
	
	public function renderHTMLReport($postData) {
		// Validate that an organization was chosen
		if (!isset($postData['orgname'])) {
			echo "You must select an organization";
			return;
		}
		
		// Strip slashes out of org name, as WPDB "prepare" used later will take care of handling
		// special characters.
		$orgName = stripslashes($postData['orgname']);
		
		// Create an array of the label IDs that represent the levels of engagement represented in this report
		$labels = array(14121, 14122, 14123, 14124, 14125);
                
		// Call a helper function to generate the report
		echo createLabelsReport($orgName, $labels);
	}
}

?>