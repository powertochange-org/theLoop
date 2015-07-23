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
 * Function getCountAtThreshold($orgid, $labelid)
 *
 * Parameters:
 * int orgid: The ID of the organization for which this count is to be produced
 * int labelid: The ID of the label associated with the particular threshold.
 *
 * Returns:
 * int filtercount: The number of people at that threshold.
 ***************************************************************************************************/

function getCountAtThreshold($orgid, $labelid) {
    $people = getIndexOfEndpoint('people', 'organizational_labels', $orgid, '', '', '', array('labels' => $labelid));
    $filtercount = sizeof($people['people']);
    return $filtercount;
}

/****************************************************************************************************
 * Function createEngagementReport($name)
 *
 * MAY OR MAY NOT EVER BE USED (and therefore may or may not ever be finished)
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