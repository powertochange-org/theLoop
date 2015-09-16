<?php

/****************************************************************************************************
 *
 * Name: 		P2CSReport
 *
 * Description: Define the parent class for all P2C-Students Stats Reports. This defines some of the
 * 				basic behaviour all reports should handle, such as managing parameters, rendering themselves
 * 				in HTML, and eventually handling column sorting and exporting themselves to Excel
 *
 * Author:		Jason Brink
 *
 * If you want to add a new report to the stats reporting system, you will need to add it to the 
 * $reportList collection below, so that it is recognized as a valid report. However, you shouldn't
 * need to make any other changes to this file.
 *
 ***************************************************************************************************/ 
 
class P2CSReport {
	/***********************************************************************************************
	* This static (class) variable allows code that works with reports to know if a particular
	* report is valid, and where to find it.
	*
	* If you create a new report, you must add it in this list.
	***********************************************************************************************/
	public static $reportList = array(
			"missionhubengagement" => array(
				"fileName" => "class.P2CSMissionHubEngagementReport.php", 
				"className" => "P2CSMissionHubEngagementReport"),
			"missionhubdiscipleship" => array(
				"fileName" => "class.P2CSMissionHubDiscipleshipReport.php", 
				"className" => "P2CSMissionHubDiscipleshipReport"),
			"missionhubindicateddecisions" => array(
				"fileName" => "class.P2CSMissionHubIndicatedDecisionsReport.php", 
				"className" => "P2CSMissionHubIndicatedDecisionsReport"),
			"missiontrips" => array(
				"fileName" => "class.P2CSMissionTripsReport.php", 
				"className" => "P2CSMissionTripsReport"),
			"eventbrite" => array(
				"fileName" => "class.P2CSEventBriteReport.php", 
				"className" => "P2CSEventBriteReport"),
		);

		
	/* 
	 * Return whether the report has parameters that should be collected or not
	 */
	public function hasParameters() {
		return false;
	}

	
	/*
	 * Render the HTML controls necessary to collect any parameters required for the selected
	 * report. This function should not render a <form> tag, as the items it renders will
	 * already be embedded inside a <form> tag. When the user clicks the "Generate Report"
	 * button, all the data in the form will be collected and sent by an Ajax POST request
	 * to the server, which will call the renderHTMLReport function below.
	 */
	public function renderParameters() {
		return "";
	}
	
	
	/*
	 * Render the HTML version of the report. If this report has parameters, they will be
	 * collected first before this function is called. The $postData will contain the POST
	 * information assembled from any parameters the report might have set up in
	 * renderParameters()
	 */
	public function renderHTMLReport($postData) {
		return "";
	}

	
	/*
	 * This helper function can be used to quickly and easily convert a result set from
	 * a MySQL database query (through a WPDB object) into an HTML table
	 */
	protected function convertWPDBResultToHTMLTable($wpdbResult) {
		$result = "<table id='report'>";
		$firstRow = true;
		
		foreach($wpdbResult as $row) {
			// Check if we need to add column headings
			if ($firstRow) {
				$firstRow = false;
				$result .= "<thead><tr>";
				foreach($row as $fieldName => $fieldValue) {
                                    if (strpos($fieldName, '#') !== false) {
					$result .= "<th data-tsorter='numeric'>$fieldName</th>";
                                    } else {
                                        $result .= "<th data-sorter='text'>$fieldName</th>";
                                    }
				}
				$result .= "</tr></thead><tbody>";
			}
			
			// Add a row of data to the output table
			$result .= "<tr>";
			foreach($row as $fieldName => $fieldValue) {
				$result .= "<td>$fieldValue</td>";
			}
			$result .= "</tr>\r\n";
		}
		
		$result .= "</tbody></table>";
		
		return $result;
	}
}

?>