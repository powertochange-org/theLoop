<?php

//There is something broken. This will probably get used in the future to implement using AJAX for changing pages in the page without reloading.

add_action('wp_enqueue_scripts', 'stats_ajax_scripts'); 
// Register Ajax handler for action "test-ajax" 
//add_action('wp_ajax_test-ajax', 'test_ajax_func');
// Register Ajax handler for action "test-ajax" for users who are not logged in (hence the _nopriv) 
//add_action('wp_ajax_nopriv_test-ajax', 'test_ajax_func');
add_action('wp_ajax_nav-click', 'stats_ajax_func');

// Function to set up JavaScript stuff for the page
function stats_ajax_scripts() {
	// Get the JavaScript file for this page template included on the page
    wp_enqueue_script( 'missionhubstats', get_stylesheet_directory_uri() . '/missionhubstats/missionhubstats.js', array( 'jquery' ));	
	
	// Create some JavaScript variables that can be used in the .js file
    wp_localize_script( 'missionhubstats', 'WordPressAjax', array(
        'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'missionhubstats-include-nonce' ))
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

?>