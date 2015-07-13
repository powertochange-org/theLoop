<?php

/****************************************************************************************************
 *
 * missionhuborganizations.php
 * Author: Nathaniel Faries
 * Written: July 1, 2015
 *
 * All functions related to organizations are handled here.
 *
 ***************************************************************************************************/


/****************************************************************************************************
 * Set up database object and establish connection
 ***************************************************************************************************/
 
//Database object
global $wpdb;

//$result = $wpdb->get_results("SELECT * FROM `mh_org_tree` WHERE 1");
//var_dump($result);
//Format of rows in database
$format = array(
    '%d',   //id, int
    '%d',   //parent_id, int
    '%s',   //name, string
);

$aResult = array();

//error_log("Hey look!");

var_dump($_POST['functionname']);

switch($_POST['functionname']) {
    case 'createEngagementReport':
        $aResult['error'] = "Not Found";
        break;
    default:
        $aResult['error'] = "Not found";
        break;
}

echo json_encode($aResult);

/****************************************************************************************************
 * Function getChildren($parent_id)
 *
 * Parameters:
 * int parent_id: The id of the parent from which to get the children from
 *
 * Returns:
 * array(int) children: An array containing all the ids of the children of the specified parent.
 ***************************************************************************************************/


function getChildren($parent_id) {
    global $wpdb;
    echo $parent_id;
    $children = $wpdb->get_results( $wpdb->prepare(
            "SELECT `id` FROM `mh_org_tree` WHERE `parent_id` = %d",
            $parent_id
        ),
        ARRAY_N       
    );
    return $children;
}

/****************************************************************************************************
 * Function getOrgId($name)
 *
 * Parameters:
 * string name: The name of the organization being examined
 *
 * Returns:
 * int id: The id of the organization being examined
 ***************************************************************************************************/

function getOrgId($name) {
    global $wpdb;
    echo $name;
    $id = $wpdb->get_results( $wpdb->prepare(
            "SELECT `id` FROM `mh_org_tree` WHERE `name` = %s",
            $name    
        ),
        ARRAY_N
    );
    return $id[0];
}

/****************************************************************************************************
 * Function getListOfOrgNames()
 *
 * Parameters:
 *
 * Returns:
 * array names: An array of all the organization names
 ***************************************************************************************************/

function getListOfOrgNames() {
    global $wpdb;
    $names = $wpdb->get_results(
        "SELECT `name` FROM `mh_org_tree` WHERE 1",
        ARRAY_N //not sure if this is the correct type.
    );
    return $names;
}

/****************************************************************************************************
 * Function getCurlObject()
 *
 * Parameters:
 *
 * Returns:
 * object curl_object: The JSON object from missionhubapirequests
 ***************************************************************************************************/

function getCurlObject() {
    $curl_object = getIndexOfEndpoint('organizations');
    return $curl_object;
}

/****************************************************************************************************
 * Function createEngagementReport($name)
 * 
 * Parameters:
 * string name: The name of the organization for which the report is being generated
 *
 * Returns:
 * I don't even know yet....
  ***************************************************************************************************/ 

function createEngagementReport($name) {
    return $name;
}

?>