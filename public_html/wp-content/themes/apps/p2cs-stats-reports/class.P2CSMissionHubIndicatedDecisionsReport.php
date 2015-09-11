<?php

/****************************************************************************************************
 *
 * Name: 		P2CSMissionHubIndicatedDecisionsReport
 *
 * Description: This class is used to generate a report on how many students have indicated decisions
 *				for Christ. It depends on some helper functions defined in missionhuborganizations.php. 
 *
 * Author:		Nathaniel Faries / Jason Brink
 *
 ***************************************************************************************************/

require_once("class.P2CSReport.php");
require_once('missionhuborganizations.php');

class P2CSMissionHubIndicatedDecisionsReport extends P2CSReport {
	public function hasParameters() {
		return false;
	}
	
	public function renderHTMLReport($postData) {
		// Call a helper function to generate the report
		echo createDecisionReport();
	}
}

?>