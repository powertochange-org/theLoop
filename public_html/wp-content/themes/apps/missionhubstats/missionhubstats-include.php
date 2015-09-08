<?php

require('missionhuborganizations.php');
require('missionhubapirequests.php');
require('missionhubpat.php');

// Register Ajax handlers
add_action('wp_enqueue_scripts', 'stats_ajax_scripts'); 
// Register Ajax handler for action "test-ajax" 
//add_action('wp_ajax_test-ajax', 'test_ajax_func');
// Register Ajax handler for action "test-ajax" for users who are not logged in (hence the _nopriv) 
//add_action('wp_ajax_nopriv_test-ajax', 'test_ajax_func');
add_action('wp_ajax_nav-click', 'stats_ajax_func');
add_action('wp_ajax_create-engagement-report', 'create_engagement_report');
add_action('wp_ajax_create-discipleship-report', 'create_discipleship_report'); 
add_action('wp_ajax_handle-submit', 'handle_submit');

// Function to set up JavaScript stuff for the page
function stats_ajax_scripts() {
	// Get the JavaScript file for this page template included on the page
    wp_enqueue_script( 'missionhubstats', get_stylesheet_directory_uri() . '/missionhubstats/missionhubstats.js', array( 'jquery' ));	
    wp_enqueue_script( 'missionhuborganizationsview', get_stylesheet_directory_uri() . '/missionhubstats/missionhuborganizationsview.js', array( 'jquery'));
	wp_enqueue_script( 'missionhubpat', get_stylesheet_directory_uri() . '/missionhubstats/missionhubpat.js', array( 'jquery' ));	
    
	// Create some JavaScript variables that can be used in the .js file
    wp_localize_script( 'missionhubstats', 'WordPressAjax', array(
        'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'missionhubstats-include-nonce' ))
    );
    
    wp_localize_script( 'missionhuborganizationsview', 'WordPressAjaxOrgs', array(
        'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'missionhuborg-include-nonce' ))
    );
    
     wp_localize_script( 'missionhubpat', 'WordPressAjaxPat', array(
        'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'missionhubpat-include-nonce' ))
    );
        
    
}

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
    if (strpos($orgname, "'")) {
        substr_replace("\\'", "'", $orgname);
    }
    $labels = array(14126, 14127, 14128);
    $response = createEngagementReport($orgname, $labels);
    
    echo $orgname;
    
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