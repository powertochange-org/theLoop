<?php

require('missionhuborganizations.php');
require('missionhubapirequests.php');
require('missionhubpat.php');

// Register Ajax handler
add_action('wp_ajax_handle-submit', 'handle_submit');


// Function that will get called when an Ajax request is submitted on the server 
function handle_submit() {
    $report = $_POST['report'];
	$nonce = $_POST['nonce'];
	if (!wp_verify_nonce($nonce, 'missionhubstats-nonce'))
		die('You do not have permission to use this web serive');
  
    
    switch ($report) {
        case 'engagement':
            create_engagement_report();
            break;
        case 'discipleship':
            create_discipleship_report();
            break;
        case 'pat':
            create_pat_report();
            break;
        case 'threshold':
            create_threshold_report($_POST['label']);
            break;
        case 'decision':
            create_decision_report();
            break;
        default:
            echo 'Error in generating report.';
            break;
    }
    
    exit;
}

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


?>