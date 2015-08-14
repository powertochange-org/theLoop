<?php

require('missionhuborganizations.php');
require('missionhubapirequests.php');
require('missionhubpat.php');

// Register Ajax handlers
add_action('wp_ajax_handle-submit', 'handle_submit');
add_action('wp_ajax_summer-filter', 'project_ajax_func');
add_action('wp_ajax_spring-filter', 'project_ajax_func');
//Not yet in use.

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
    $labels = array(14126, 14127, 14128);
    $response = createEngagementReport($orgname, $labels);
    
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

function project_ajax_func() {
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce,'missionhubpat-include-nonce')) 
        die ('You do not have permission to use this web service');
        
    $response = "<p><i>No refresh needed! you asked for a filter by <strong>" . $_POST['button'] . "</strong></i></p>";
        
    header("Content-Type: text/html");
    echo $response;
    
    exit;        
}


?>