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
		return true;
	}
        
        public function renderParameters() {
		echo createSchoolYearDropDownList();
	}
	
	public function renderHTMLReport($postData) {
		// Get the parameters set up for the SQL query
	    if (! isset($postData['schoolYear'])) {
                $CurrDate = strtotime(date("Y-m-d"));
                $cutoff = strtotime(date("Y") . "-09-01");
                if ($cutoff > $CurrDate) {
                        $startdate = (date("Y") - 1) . "-09-01";
                        $enddate = date("Y") . "-08-31";
                } else {
                        $startdate = date("Y") . "-09-01";
                        $enddate = (date("Y") + 1) . "-08-31";
                }
            } else {
                $year = $postData['schoolYear'];

                $startdate = $year . "-09-01";
                $enddate = ($year + 1) . "-8-31";        
            }		
            // Call a helper function to generate the report
            echo createDecisionReport($startdate, $enddate);
    }
}

?>