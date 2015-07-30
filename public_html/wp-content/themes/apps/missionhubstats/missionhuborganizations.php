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
 * Function getOrgName()
 *
 * Parameters:
 * int ordid: The ID of the organization for which we want to get the name.
 *
 * Returns:
 * array names: An array with the desired name in it.  TODO: see about returning it not in an array.
 ***************************************************************************************************/

function getOrgName($orgid) {
    global $wpdb;
    $name = $wpdb->get_results( $wpdb->prepare(
            "SELECT `name` FROM `mh_org_tree` WHERE `id` = %d",
            $orgid
        ),
        ARRAY_N
    );
    return $name[0];
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
 * Function getOrgLabelCount($orgid, $labels)
 *
 * Parameters:
 * int orgid: The ID of the organization for which this count is to be produced
 * array(int) label: The IDs of the labels that need a count.
 *
 * Returns:
 * int filtercount: The number of people at that threshold.
 ***************************************************************************************************/

function getOrgLabelCount($orgid, $labels) {
    $result = array();
    foreach ($labels as $label) {
        $people = getIndexOfEndpoint('people', 'organizational_labels', $orgid, '', '', '', array('labels' => $label));
        array_push($result, sizeof($people['people']));
    }
    return $result;
}

/****************************************************************************************************
 * Function createEngagementReport($orgname)
 *
 * MAY OR MAY NOT EVER BE USED (and therefore may or may not ever be finished)
 * 
 * Parameters:
 * string orgname: The name of the organization for which the report is being generated
 *
 * Returns:
 * string result: The resulting html to produce a table to be displayed to the user.
  ***************************************************************************************************/ 

function createEngagementReport($orgname) {

//    $initstart = microtime(true);
    $orgid = getOrgId($orgname);
    $children = getChildren($orgid[0]);
    //Currently this is hardcoded, hopefully we can modify it to make it so it is not.
    $thresholds = array(0, 0, 0, 0, 0);
    //$labels are hardcoded to be appropriate for the report.  Should abstract out functionality of each part of the report as getting counts at given labels is not unique to engagement reports.
    $labels = array(14121, 14122, 14123, 14124, 14125);
//    $initend = microtime(true);
//    $inittotal = $initend - $initstart;
//    echo "<br>Initialization start: " . $initstart;
//    echo "<br>Initialization end: " . $initend;
//    echo "<br>Initialization: " . $inittotal;
//    
//    $parentcountstart = microtime(true);
    //Zero-indexed for loop.
    $i = 0;
    foreach($labels as $label) {
        $thresholds[$i] = getCountAtThreshold($orgid[0], $label);        
        $i++;
    }
//    $parentcountend = microtime(true);
//    $parentcounttotal = $parentcountend - $parentcountstart;
//    echo "<br>Parent count start: " . $parentcountstart;
//    echo "<br>Parent count end: " . $parentcountend;
//    echo "<br>Parent count: " . $parentcounttotal;
    
    //Table headers
    $tableheaders = generateTableHeaders($labels);
    
    $childrenrows = "";
    
    //Children organizations
//    $childstart = microtime(true);
    foreach($children as $childid) {
        $childthresholds = array(0, 0, 0, 0, 0);
        $childname = getOrgName($childid);
        $arrayindex = 0;
        
        foreach($labels as $label) {
            $count = getCountAtThreshold($childid[0], $label);
            $childthresholds[$arrayindex] = $count;
            $thresholds[$arrayindex] = $thresholds[$arrayindex] + $count;
            $arrayindex++;
        }
        
        $childrenrows = $childrenrows . "<tr>
                                            <td>" . $childname[0] ."</td>
                                            <td>" . $childthresholds[0] ."</td>
                                            <td>" . $childthresholds[1] ."</td>
                                            <td>" . $childthresholds[2] ."</td>
                                            <td>" . $childthresholds[3] ."</td>
                                            <td>" . $childthresholds[4] ."</td>
                                        </tr>";
    }
//    $childend = microtime(true);
//    $childtotal = $childend - $childstart;
//    echo "<br>Child processing start: " . $childstart;
//    echo "<br>Child processing end: " . $childend;
//    echo "<br>Child processing: " . $childtotal;
    
//    $endingstart = microtime(true);
    $parentrow =    "<tr>
                        <td><strong>" . $orgname ."</strong></td>
                        <td><strong>" . $thresholds[0] ."</strong></td>
                        <td><strong>" . $thresholds[1] ."</strong></td>
                        <td><strong>" . $thresholds[2] ."</strong></td>
                        <td><strong>" . $thresholds[3] ."</strong></td>
                        <td><strong>" . $thresholds[4] ."</strong></td>
                    </tr>";
        
    $response = $tableheaders . $parentrow . $childrenrows . "</table>";
//    $endingend = microtime(true);
//    $endingtotal = $endingend - $endingstart;
//    echo "<br>Final bit: " . $endingtotal;
    
    return $response;
}

/****************************************************************************************************
 * Function generateTableHeaders($labels)
 *
 * Generates the html for the headers of the table for a given report.
 * 
 * Parameters:
 * array labes: The list of all the label ids being used for the table.
 *
 * Returns:
 * string result: The resulting html to produce a table header for the table.
  ***************************************************************************************************/ 

function generateTableHeaders($labels) {
    //May need a way to look up label names...
     $result = "<table>    
                        <tr>
                            <th>Organization</th>";
    //One-indexed for loop.
    $i = 1; 
    foreach($labels as $label) {
        $result = $result . "<th>Threshold " . $i . "</th>";
        $i++;
    }
    $result = $result . "</tr>";
    return $result;
    
}

function generateParentRow($orgid, $labels) {
    
}

function generateChildrenRows($children, $labels) {
    
}

?>