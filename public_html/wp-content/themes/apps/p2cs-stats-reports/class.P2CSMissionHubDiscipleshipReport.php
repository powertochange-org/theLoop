<?php

/****************************************************************************************************
 *
 * Name: 		P2CSMissionHubDiscipleshipReport
 *
 * Description: This class is used to generate a report on how many students are in various
 *				discipleship levels at each campus. It depends on some helper functions defined
 *				in missionhuborganizations.php. 
 *
 * Author:		Nathaniel Faries / Jason Brink
 *
 ***************************************************************************************************/

require_once("class.P2CSReport.php");
require_once('missionhuborganizations.php');

class P2CSMissionHubDiscipleshipReport extends P2CSReport {
	public function hasParameters() {
		return true;
	}
	
	public function renderParameters() {
		echo createOrganizationsDropDownList();
                echo createRecurseCheckbox();
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
		$labels = array(14126, 14127, 14128);
                
                $recurse = ($postData['recurse']);

		// Call a helper function to generate the report
		echo createLabelsReport($orgName, $labels, $recurse);	
	}
}

?>