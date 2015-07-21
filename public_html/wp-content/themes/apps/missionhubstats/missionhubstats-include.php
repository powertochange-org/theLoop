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
    
    //This part builds the table to be displayed on the page. This will probably be replaced a separate, generic function
    
    $orgname = $_POST['orgname'];
    $orgid = getOrgId($orgname);
    $children = getChildren($orgid);
    
    
    //Table headers
    echo "<table>    
            <tr>
                <th>Organization</th>
                <th>Threshold 1</th>
                <th>Threshold 2</th>
                <th>Threshold 3</th>
                <th>Threshold 4</th>
                <th>Threshold 5</th>
            </tr>       ";
    
    //Parent org (TODO: stylize parent organization's row to make it stand out)
    echo "<tr>
             <td>" . $orgname ."</td>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
        </tr>";
    
    //Children organizations
    foreach($children as $childid) {
        $threshold1 = 0;
        $threshold2 = 0;
        $threshold3 = 0;
        $threshold4 = 0;
        $threshold5 = 0;
        //Gets the organization from the missionhub database using an API call that includes people.
        $child = showEndpoint('organizations', $childid[0], 'people,labels');
        $people = $child['organization']['people'];
//        if ($child['organization']['name'] == "Field Ministry")     
//            var_dump($people );
//        echo $child['organization']['name'];
        
        foreach($people as $person) {
            if ($child['organization']['name'] == "Field Ministry") {
                echo "<br>" . $person['id'];
                $curlperson = showEndpoint('people', $person['id']);
                var_dump($curlperson);
            }
            foreach($curlperson['person']['organizational_labels'] as $label) {
//                var_dump($label);
                switch ($label['label_id']){
                    case 14121:
                        $threshold1++;
                        break;
                    case 14122:
                        $threshold2++;
                        break;
                    case 14123:
                        $threshold3++;
                        break;
                    case 14124:
                        $threshold4++;
                        break;
                    case 14125:
                        $threshold5++;
                        break;
                    default:
                        break;                    
                }
            }
        }
        
        echo "<tr>
             <td>" . $child['organization']['name'] ."</td>
             <td>" . $threshold1 ."</td>
             <td>" . $threshold2 ."</td>
             <td>" . $threshold3 ."</td>
             <td>" . $threshold4 ."</td>
             <td>" . $threshold5 ."</td>
        </tr>";
    }
        
    echo "</table>";
    
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