<?php

require_once('class.P2CSReport.php');


// Register Ajax handler
add_action('wp_ajax_p2cs-stats-report-generate', 'p2cs_stats_report_generate');


// Function that will get called when an Ajax request is submitted from the client
// to generate a report.
function p2cs_stats_report_generate() {
    $reportName = $_POST['reportName'];
	$nonce = $_POST['nonce'];
	if (!wp_verify_nonce($nonce, 'p2cs-stats-reports-nonce'))
		die('You do not have permission to use this web serive');
  
    
    // Check if the report they are attempting to run is a valid one we recognize
	if (isset(P2CSReport::$reportList[$reportName])) {
		// The report is valid. Find out what file it is in, and what the className is
		$fileName = P2CSReport::$reportList[$reportName]['fileName'];
		$className = P2CSReport::$reportList[$reportName]['className'];
		
		// Include the necessary file
		include_once($fileName);
		
		// Create a report object of the appropriate type
		$report = new $className();

		// Render the report!
		$report->renderHTMLReport($_POST);
	} else {
		echo "Error: report '$reportName' is not defined";
	}
    
    exit;
}

/*
function create_engagement_report() {
    $orgname = $_POST['orgname'];
    //createEngagementReport exists in missionhuborganizations.php
    $labels = array(14121, 14122, 14123, 14124, 14125);  
    $response = createEngagementReport($orgname, $labels);
    
    echo $response;
    
    exit;
}

//Uses same table generation function as engagement reports but with different labels.
function create_discipleship_report() {
    $orgname = $_POST['orgname'];
//    if (strpos($orgname, "'")) {
//        substr_replace("\\'", "'", $orgname);
//    }
    $labels = array(14126, 14127, 14128);
    $response = createEngagementReport($orgname, $labels);
    
    echo $response;
    
    exit;    
}

function create_threshold_report($label) {
    $orgname = $_POST['orgname'];
    $response = createThresholdReport($orgname, $label);
    
    echo $response;
    
    exit;    
}

function create_pat_report() {
    $season = $_POST['season'];
    $year = $_POST['year'];
    $response = createPatReport($season, $year);
    
    echo $response;

    exit;    
}

function create_decision_report() {    
    header("Content-Type: text/html");
    
    $response = createDecisionReport();
    
    echo $response;
    
    exit;
}
*/

?>