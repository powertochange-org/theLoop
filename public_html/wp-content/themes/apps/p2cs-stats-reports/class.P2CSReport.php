<?php

/*
 * Define the parent class for all P2C-Students Stats Reports. This defines some of the
 * basic behaviour all reports should handle, such as managing parameters, rendering themselves
 * in HTML, handling column sorting, and exporting themselves to Excel
 */
class P2CSReport {
	// Provide a central way to determine if a report name is valid, and what filename and class name it maps to
	public static $reportList = array(
			"missionhubengagement" => array(
				"fileName" => "missionhubengagement.php", 
				"className" => "P2CSMissionHubEngagementReport"),
			"missiontrips" => array(
				"fileName" => "missiontrips.php", 
				"className" => "P2CSMissionTripsReport"),
		);
	
	// Return whether the report has parameters that should be collected or not
	public function hasParameters() {
		return false;
	}
	
	// Render the HTML controls necessary to collect any parameters required for 
	public function renderParameters() {
		return "";
	}
	
	// Render the HTMl version of the report
	public function renderHTMLReport($postData) {
		return "";
	}
	
	protected function convertWPDBResultToHTMLTable($wpdbResult) {
		$result = "<table>";
		$firstRow = true;
		
		foreach($wpdbResult as $row) {
			// Check if we need to add column headings
			if ($firstRow) {
				$firstRow = false;
				$result .= "<th>";
				foreach($row as $fieldName => $fieldValue) {
					$result .= "<td>$fieldName</td>";
				}
				$result .= "</th>";
			}
			
			// Add a row of data to the output table
			$result .= "<tr>";
			foreach($row as $fieldName => $fieldValue) {
				$result .= "<td>$fieldValue</td>";
			}
			$result .= "</tr>\r\n";
		}
		
		$result .= "</table>";
		
		return $result;
	}
}

?>