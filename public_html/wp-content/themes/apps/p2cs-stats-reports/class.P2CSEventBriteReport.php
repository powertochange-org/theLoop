<?php

/****************************************************************************************************
 *
 * Name: 		P2CSEventBriteReport
 *
 * Description: This class is used to generate a report on how many people are attending various
 *				events set up in EventBrite
 *
 * Author:		
 *
 ***************************************************************************************************/

require_once("class.P2CSReport.php");

class P2CSEventBriteReport extends P2CSReport {
	public function hasParameters() {
		return false;
	}
	
	public function renderHTMLReport($postData) {
		echo "This report is not yet available";
	}
}

?>