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
    $thresholds = array(0, 0, 0, 0, 0);
    $parentpeople = getIndexOfEndpoint('people', 'organizational_labels', $orgid[0]);
    
    
    foreach($parentpeople['people'] as $person) {
        foreach($person['organizational_labels'] as $label) {
            switch ($label['label_id']){
                case 14121:
                    $thresholds[0]++;
                    break;
                case 14122:
                    $thresholds[1]++;
                    break;
                case 14123:
                    $thresholds[2]++;
                    break;
                case 14124:
                    $thresholds[3]++;
                    break;
                case 14125:
                    $thresholds[4]++;
                    break;
                default:
                    break;                    
            }
        }
    }
    
    
    //Table headers
    $tableheaders = "<table>    
                        <tr>
                            <th>Organization</th>
                            <th>Threshold 1</th>
                            <th>Threshold 2</th>
                            <th>Threshold 3</th>
                            <th>Threshold 4</th>
                            <th>Threshold 5</th>
                        </tr>";
    
    $childrenrows = "";
    
    //Children organizations
    foreach($children as $childid) {
        $childthresholds = array(0, 0, 0, 0, 0);
        $labels = array(14121, 14122, 14123, 14124, 14125);
        $child = showEndpoint('organizations', $childid[0]);
        $childname = $child['organization']['name'];
//        $people = getIndexOfEndpoint('people', 'organizational_labels', $childid[0]);
        
        foreach($labels as $label) {
            $count = getCountAtThreshold($childid[0], $label);
            $childthresholds[$label - 14121] = $count;
            $thresholds[$label - 14121] += $count;
        }
        
//        foreach($people['people'] as $person) {
//            foreach($person['organizational_labels'] as $label) {
//                switch ($label['label_id']){
//                    case 14121:
//                        $childthresholds[0]++;
//                        $thresholds[0]++;
//                        break;
//                    case 14122:
//                        $childthresholds[1]++;
//                        $thresholds[1]++;
//                        break;
//                    case 14123:
//                        $childthresholds[2]++;
//                        $thresholds[2]++;
//                        break;
//                    case 14124:
//                        $childthresholds[3]++;
//                        $thresholds[3]++;
//                        break;
//                    case 14125:
//                        $childthresholds[4]++;
//                        $thresholds[4]++;
//                        break;
//                    default:
//                        break;                    
//                }
//            }
//        }
        
        $childrenrows = $childrenrows . "<tr>
                                            <td>" . $childname ."</td>
                                            <td>" . $childthresholds[0] ."</td>
                                            <td>" . $childthresholds[1] ."</td>
                                            <td>" . $childthresholds[2] ."</td>
                                            <td>" . $childthresholds[3] ."</td>
                                            <td>" . $childthresholds[4] ."</td>
        </tr>";
    }
    
    $parentrow =    "<tr>
                        <td><strong>" . $orgname ."</strong></td>
                        <td><strong>" . $thresholds[0] ."</strong></td>
                        <td><strong>" . $thresholds[1] ."</strong></td>
                        <td><strong>" . $thresholds[2] ."</strong></td>
                        <td><strong>" . $thresholds[3] ."</strong></td>
                        <td><strong>" . $thresholds[4] ."</strong></td>
                    </tr>";
        
    $response = $tableheaders . $parentrow . $childrenrows . "</table>";
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