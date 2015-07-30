<?php

require('missionhuborganizations.php');
require('missionhubapirequests.php');

//There is something broken. This will probably get used in the future to implement using AJAX for changing pages in the page without reloading.

add_action('wp_enqueue_scripts', 'stats_ajax_scripts'); 
// Register Ajax handler for action "test-ajax" 
//add_action('wp_ajax_test-ajax', 'test_ajax_func');
// Register Ajax handler for action "test-ajax" for users who are not logged in (hence the _nopriv) 
//add_action('wp_ajax_nopriv_test-ajax', 'test_ajax_func');
add_action('wp_ajax_nav-click', 'stats_ajax_func');
add_action('wp_ajax_create-engagement-report', 'create_engagement_report');
add_action('wp_ajax_summer-filter', 'project_ajax_func');
add_action('wp_ajax_spring-filter', 'project_ajax_func');
//Not yet in use.
add_action('wp_ajax_create-discipleship-report', 'create_discipleship_report'); 

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


function stats_ajax_func() {
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce,'missionhubstats-include-nonce')) 
        die ('You do not have permission to use this web service');
        
    $response = "<p><i>You clicked <strong>" . $_POST['button'] . "</strong></i></p>";
        
    header("Content-Type: text/html");
    echo $response;
    
    exit;        
}

function create_engagement_report() {
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce,'missionhuborg-include-nonce')) 
        die ('You do not have permission to use this web service');
            
    header("Content-Type: text/html");
    
    $orgname = $_POST['orgname'];
    //createEngagementReport exists in missionhuborganizations.php
    $response = createEngagementReport($orgname);
    
    echo $response;
    
    exit;
}

//Not yet in use.  createDiscipleshipReport($orgname) does not yet exist.

function create_discipleship_report() {
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce,'missionhuborg-include-nonce'))
        die ('You do not have permission to use this service.');
    
    header("Content-Type: text/html");
    
    $orgname = $_POST['orgname'];
    $response = createDiscipleshipReport($orgname);
    
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