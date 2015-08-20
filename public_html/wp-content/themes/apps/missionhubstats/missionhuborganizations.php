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
    global $wpdb;
    $count = 0;
    $children = getChildren($orgid);
    foreach($children as $child) {
        $count = $count + getCountAtThreshold($child[0], $labelid);
    }

    $people = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM `mh_threshold_details` WHERE `org_id` = %d AND `threshold_id` = %d",
            $orgid,
            $labelid
        ),
        ARRAY_N
    );
    
    return $count + sizeof($people);
}

function getPeopleAtThreshold($orgid, $labelid) {
    global $wpdb;
    $people = array();
    $children = getChildren($orgid);
    foreach($children as $child) {
        $childrenpeople = getPeopleAtThreshold($child[0], $labelid);
        foreach($childrenpeople as $person) {
            array_push($people, $person);
        }
    }
    $queryresult = $wpdb->get_results( $wpdb->prepare(
            "SELECT `first_name`,`last_name`,`image_url`,`org_id` FROM `mh_threshold_details` WHERE `org_id` = %d AND `threshold_id` = %d",
            $orgid,
            $labelid
        ),
        ARRAY_A
    );
    foreach($queryresult as $person) {
        array_push($people, $person);
    }
    return $people;
}

function convertLabelToTitle($labelid) {
    switch ($labelid) {
        case 14121:
            return 'Knows and Trusts a Christian';
        case 14122:
            return 'Became Curious';
        case 14123:
            return 'Became Open to Change';
        case 14124:
            return 'Seeking God';
        case 14125:
            return 'Made a Decision';
        case 14126:
            return 'Growing Disciple';
        case 14127:
            return 'Ministering Disciple';
        case 14128:
            return 'Multiplying Disciple';
    }
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
 * Parameters:
 * string orgname: The name of the organization for which the report is being generated
 * array labels: The labels to be included in the report.
 *
 * Returns:
 * string result: The resulting html to produce a table to be displayed to the user.
  ***************************************************************************************************/ 

function createEngagementReport($orgname, $labels) {

    $orgid = getOrgId($orgname);
    $children = getChildren($orgid[0]);
       
    //Table headers
    $tableheaders = generateTableHeaders($labels);
  
    $tablerows = generateTableRows($orgname, $orgid, $children, $labels);
        
    $response = "<table>{$tableheaders}{$tablerows}</table>";
    
    return $response;
}

function createThresholdReport($orgname, $label) {
    
    $orgid = getOrgId($orgname);
    $children = getChildren($orgid[0]);
    
    $title = "<strong>" . convertLabelToTitle($label) . "<strong><br>";
    
    $tableheaders = "<tr>
                        <th></th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Organization</th>
                    </tr>";
    
    $tablerows = getNestedPeopleAtThreshold($orgid[0], $label);
    
    
    
    $response = "<table>" . $title . $tableheaders . $tablerows . "</table>";
    
    return $response;
    
}

function getNestedPeopleAtThreshold($orgid, $label) {
    $result = "";
    $people = getPeopleAtThreshold($orgid, $label);
        
    foreach ($people as $person) {
        if ($person['image_url'] == NULL) {
            $result = $result .  '<tr><td></td><td>' . $person['first_name'] . '</td><td>' . $person['last_name'] . '</td><td>' . getOrgName($person['org_id'])[0] . '</td></tr>';   
        } else {
            $result = $result .  '<tr><td><img src="' . $person['image_url'] . '"></td><td>' . $person['first_name'] . '</td><td>' . $person['last_name'] . '</td><td>' . getOrgName($person['org_id'])[0] . '</td></tr>';
        }
    }
    
    return $result;
}

//not sure if this is going to be per org or if it should just show all orgs...
function createDecisionReport($orgname) {
    
    if ($orgname == NULL) {
        $orgname = '';
    } else {
    }    
    
    $tableheaders = "<tr>
                        <th>Organization</th>
                        <th>Receiver</th>
                        <th>Initiatior</th>
                        <th>Date</th>
                        <th>Story</th>
                    <tr>";
}


/****************************************************************************************************
 * Function generateTableHeaders($labels)
 *
 * Generates the html for the headers of the table for a given report.
 * 
 * Parameters:
 * array labels: The list of all the label ids being used for the table.
 *
 * Returns:
 * string result: The resulting html to produce a table header for the table.
  ***************************************************************************************************/ 

function generateTableHeaders($labels) {
    //May need a way to look up label names...
     $result = "<tr>
                    <th>Organization</th>";
    //One-indexed for loop.
    $i = 1; 
    foreach($labels as $label) {
        $result = $result . '<th><a href="#" id="'. $label .'" class="threshold">' . convertLabelToTitle($label) . '</th>';
        $i++;
    }
    $result = $result . "</tr>";
    return $result;
    
}

/****************************************************************************************************
 * Function generateTableRows($orgname, $orgid, $children, $labels)
 *
 * Generates the html for the rows of the table for a given report.
 * 
 * Parameters:
 * string orgname: The name of the parent organization for the report.
 * int orgid: The id of the parent organization for the report.
 * array children: An array of the ids of all the children of the parent organization.
 * array labels: The list of all the label ids being used for the table.
 *
 * Returns:
 * string result: The resulting html to produce all the table rows for the table.
  ***************************************************************************************************/ 

function generateTableRows($orgname, $orgid, $children, $labels) {
    
    //Initialization
    $childrenrows = "";
    $parentrow = "";
    $thresholds = array();
    array_pad($thresholds, sizeof($labels), 0);
    
    //Getting counts for parent organization
    $arrayindex = 0;
    foreach($labels as $label) {
        $thresholds[$arrayindex] = getCountAtThreshold($orgid[0], $label);
        $arrayindex++;
    }
    
    
    //Creating the children rows
    foreach ($children as $child) {
        $childname = getOrgName($child[0]);
        $childthresholds = array();
        array_pad($childthresholds, sizeof($labels), 0);
        $arrayindex = 0;
        $childrenrows = $childrenrows . "<tr><td>" . $childname[0] ."</td>";
        foreach ($labels as $label) {
            $count = getCountAtThreshold($child[0], $label);
            $childthresholds[$arrayindex] = $count;
            $childrenrows = $childrenrows . "<td>" . $childthresholds[$arrayindex] . "</td>";
            $arrayindex++;
        }
        $childrenrows = $childrenrows . "</tr>";        
    }
    
    //Creating the parent rows
    $parentrow = $parentrow . "<tr><td><strong>" . $orgname . "</strong></td>";
    
    $arrayindex = 0;
    while ($arrayindex < sizeof($labels)) {
        $parentrow = $parentrow . "<td><strong>" . $thresholds[$arrayindex] . "</strong></td>";
        $arrayindex++;
    }
    $parentrow = $parentrow . "</tr>";
    
    $result = $parentrow . $childrenrows;
    
    return $result;
}

?>