<?php

/****************************************************************************************************
 *
 * missionhuborganizations.php
 * Author: Nathaniel Faries
 * Written: July 1, 2015
 *
 * All functions related to MissionHub organizations are handled here.
 *
 ***************************************************************************************************/


/****************************************************************************************************
 * Function getChildren($parent_id)
 *
 * This function gets the ids of all the children of a certain parent.
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
 * This function gets the id of an organization given the name of the organization.
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
 * This function simply gets the list of all the organization names from the database.
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
        ARRAY_N
    );
    return $names;
}

/****************************************************************************************************
 * Function getOrgName($orgid)
 *
 * This function gets the name of an organization given its id. Opposite of getOrgId($name).
 *
 * Parameters:
 * int ordid: The ID of the organization for which we want to get the name.
 *
 * Returns:
 * string name: The desired organization name.
 ***************************************************************************************************/

function getOrgName($orgid) {
    global $wpdb;
    $name = $wpdb->get_results( $wpdb->prepare(
            "SELECT `name` FROM `mh_org_tree` WHERE `id` = %d",
            $orgid
        ),
        ARRAY_N
    );
    return $name[0][0];
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

/****************************************************************************************************
 * Function getPeopleAtThreshold($orgid, $labelid)
 *
 * Parameters:
 * int orgid: The ID of the organization for which this count is to be produced
 * int labelid: The ID of the label associated with the particular threshold.
 *
 * Returns:
 * array people: An array which contains all the people in the organization and children organizations
 * at the specified threshold.
 ***************************************************************************************************/

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

/****************************************************************************************************
 * Function getDatabaseTimestamp($orgid)
 *
 * This functon gets the timestamp for the last time that the dtabase was updated.  The value should
 * be the same for all organizations, but an optional parameter to specify the orgnaization has been 
 * included.  It defaults to the root P2C organization on missionhub.
 *
 * Parameters:
 * int orgid: The organization from which to get the "last updated" timestamp
 *
 * Returns:
 * string: The phrase to display at the bottom of the page, below the table.
 ***************************************************************************************************/

function getDatabaseTimestamp($orgid = 8411) { //hardcoded to the P2C root group
    global $wpdb;
    $queryresult = $wpdb->get_results( $wpdb->prepare(
            "SELECT `last_updated` FROM `mh_org_tree` WHERE `id` = %d",
            $orgid
        ),
        ARRAY_N
    );
    
    return "<i>Last updated on " . $queryresult[0][0] . "</i>";
}

/****************************************************************************************************
 * Function createOrganizationsDropDownList()
 *
 * This function builds an HTML drop-down list of all Organizations. It is used to generate the
 * parameters for some MissionHub reports
 *
 * Parameters:
 * none
 *
 * Returns:
 * string: HTML code of organizations drop-down list
 ***************************************************************************************************/

function createOrganizationsDropDownList() {
	$result = 'Organization:
	<select id="orgname" name="orgname">';
	
	$orgs = getListOfOrgNames();
	asort($orgs);

	foreach($orgs as $org) {
		$result .= '<option value="' . $org[0] . '">'.$org[0].'</option>';
	}

	$result .= '</select>';
	
	return $result;
}

function createRecurseCheckbox() {
    $result = "<br/><br/>Show details?: <input id='recurse' type='checkbox' checked='checked' />";
    $result .= "<input type='hidden' name='recurse' value='1'/><br/>";
    $result .= "<script type='text/javascript'>";
    $result .= "$('#recurse').on('change', function(e) {";
    $result .= "  if($(this).prop('checked')) {";
    $result .= "    $(this).next().val(1);";
    $result .= "  } else {";
    $result .= "    $(this).next().val(0);";
    $result .= "  }";
    $result .= "});";
    $result .= "</script>";
    return $result;
}

/****************************************************************************************************
 * Function convertLabelToTitle($labelid)
 *
 * This function takes a labelid and returns a string title for the label. This will need to be modified
 * if any label names change in the future.  This is used to give meaningful names to table columns.
 *
 * Parameters:
 * int labelid: The ID of the label for which we want a title
 *
 * Returns:
 * string: The name of the label
 ***************************************************************************************************/

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
 * Function getPersonName($personid)
 *
 * The purpose of this function is to provide a compact way to get someone's name given their id.
 * The process is extraordinarily complicated and this function is a work in progress.
 * 
 * Parameters:
 * int personid: The ID of the person we need a name for
 *
 * Returns:
 * string: A string with the first and last name of the person, separated by a space.
 ***************************************************************************************************/

function getPersonName($personid) {
	$people = showEndpoint('people', $personid);
    $person = $people[person];
    
    var_dump($person);
    return "$person[first_name] $person[last_name]";
    
}

/****************************************************************************************************
 * Function createLabelsReport($orgname, $labels)
 * 
 * This function is called by several report classes to create engagement and discipleship reports
 * as they both rely on labels and have the same structure.  It calls a few helper methods in this 
 * file to put together an HTML string containing the requested table.
 *
 * Parameters:
 * string orgname: The name of the organization for which the report is being generated
 * array labels: The labels to be included in the report.
 * optional bool recurse: whether to render the report in depth, or just 1 level
 *
 * Returns:
 * string response: The resulting HTML to produce a table to be displayed to the user.
  ***************************************************************************************************/ 

function createLabelsReport($orgname, $labels, $recurse=TRUE) {
	
    $orgid = getOrgId($orgname);
       
    // Build up the components of the report
    $tableheaders = generateTableHeaders($labels);
  
    $tablerows = generateTableRows($orgid, $labels, $recurse);
    
    $pagefooter = getDatabaseTimestamp($orgid);
    
    $downloadbutton = "<a href='#' class='download'>Download</a>";
        
    $response = "<table id='report'><thead>{$tableheaders}</thead><tbody>{$tablerows}</tbody></table>{$downloadbutton}<br><br>{$pagefooter}";
    
    return $response;
}

/****************************************************************************************************
 * Function createThresholdReport($orgname, $label)
 *
 * This function is called by missionhubstats-include.php when a user clicks on a column header in 
 * either a discipleship or engagement report.  It produces a table containing a detailed list of all
 * the people in the current organization and its children who are at the specific threshold.  It calls
 * the helper function getNestedPeopleAtThreshold($orgid, $label) to create all the rows, and connects
 * this to the header to create an HTML table.
 *
 * Parameters:
 * int orgname: The name of the organization for which this report is to be produced
 * int label: The ID of the label for which we are creating a table
 *
 * Returns:
 * string response: The resulting HTML to produce a table to be displayed to the user.
 ***************************************************************************************************/

function createThresholdReport($orgname, $label) {
    
    $orgid = getOrgId($orgname);
    $children = getChildren($orgid[0]);
    
    $title = "<strong>" . convertLabelToTitle($label) . "<strong><br>";
    
    $tableheaders = "<thead><tr>
                        <th>Picture</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Organization</th>
                    </tr></thead>";
    
    $tablerows = "<tbody>".getNestedPeopleAtThreshold($orgid[0], $label)."</tbody>";
    
    $pagefooter = getDatabaseTable($orgid);
    
    $downloadbutton = "<a href='#' class='download'>Download</a>";

    $response = "<table id='report'>" . $title . $tableheaders . $tablerows . "</table>{$downloadbutton}<br><br>" . $pagefooter;
    
    return $response;
    
}

/****************************************************************************************************
 * Function getNestedPeopleAtThreshold($orgid, $label);
 *
 * This function creates the table rows for a detailed report on a specific threshold.  Each row has
 * a picture, first name, last name, and organization of a user who has the specified label.  If 
 * the user has no picture, an alternate row is produced leaving that field blank.
 *
 * Parameters:
 * int orgid: The ID of the parent organization for this table
 * int label: The ID of the label for which this table is being created
 *
 * Returns:
 * string result: The HTLM to create all the table rows.
 ***************************************************************************************************/

function getNestedPeopleAtThreshold($orgid, $label) {
    $result = "";
    $people = getPeopleAtThreshold($orgid, $label);
        
    foreach ($people as $person) {
        if ($person['image_url'] == NULL) {
            $result = $result .  '<tr><td></td><td>' . $person['first_name'] . '</td><td>' . $person['last_name'] . '</td><td>' . getOrgName($person['org_id']) . '</td></tr>';   
        } else {
            $result = $result .  '<tr><td><img src="' . $person['image_url'] . '"></td><td>' . $person['first_name'] . '</td><td>' . $person['last_name'] . '</td><td>' . getOrgName($person['org_id']) . '</td></tr>';
        }
    }
    
    return $result;
}

/****************************************************************************************************
 * Function createDecisionReport()
 *
 * This function is called by missionhubstats-include.php to create a detailed table of all the users
 * who have indicated a decision to follow Christ, as determined by an interaction (NOT BY THRESHOLD
 * FIVE, ID: 14125).  This function and its helpers are still a work in progres....well...mostly the
 * helpers, this one is in almost its final form.
 *
 * Parameters:
 *
 * Returns:
 * string response: The HTML to create the table for the decision report.
 ***************************************************************************************************/

function createDecisionReport() {
    global $wpdb; 
    $people = $wpdb->get_results( 
        "SELECT * FROM `mh_interactions_details`",
        ARRAY_A
    );
        
    $tableheaders = "<thead><tr>
                        <th>Organization</th>
                        <th>Receiver</th>
                        <th>Initiator(s)</th>
                        <th>Date</th>
                        <th>Story</th>
                    <tr></thead>";
    
    $tablerows = "<tbody>";
    
    foreach($people as $person) {
        $date = strtotime($person['date']);
        $tablerows = $tablerows . "<tr><td>" . getOrgName($person[org_id]) . "</td><td>" . $person[receiver_name] . "</td><td>" . $person[initiator_names] . "</td><td>" . date("M d\, Y", $date). "</td><td>" . $person[story]. "</td></tr>";
    }
    $tablerows = $tablerows . "</tbody>";
    
    $pagefooter = getDatabaseTimestamp();
    
    $response = "<table id='report'>$tableheaders$tablerows</table><a href='#' class='download'>Download</a><br><br>$pagefooter";
    
    return $response;
    
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
     $result = "<tr>
                    <th class='clickable'>Organization</th>";
    //One-indexed for loop.
    $i = 1; 
    foreach($labels as $label) {
        $result = $result . '<th data-tsorter="numeric">' . convertLabelToTitle($label) . '</th>';
        $i++;
    }
    $result = $result . "</tr>";
    return $result;
    
}

/****************************************************************************************************
 * Function generateTableRows($$orgid, $labels)
 *
 * Recursive
 * Generates the html for the rows of the table for a given report.
 * 
 * Parameters:
 * int orgid: The id of the parent organization for the report.
 * array labels: The list of all the label ids being used for the table.
 * optional bool recurse: Whether to show all descendents rather than just
 *                        immediate children.
 * prefix: ignore, used internally.
 *
 * Returns:
 * string result: The resulting html to produce all the table rows for the table.
  ***************************************************************************************************/ 

function generateTableRows($orgid, $labels, $recurse=true, $prefix='') {
    $indent_char = "&nbsp;&nbsp;";
    $children = getChildren($orgid);
    $rowData = getRowData($prefix, $orgid, $labels);
    if (sizeOf($children)==0) {
        return $rowData;
    }
    $result=str_replace("<tr>","<tr class='parent'>",$rowData);
    foreach ($children as $child) {
        if ($recurse) {
            $result .= generateTableRows($child[0], $labels, $prefix.$indent_char);       
        } else {
            $result .=getRowData($indent_char, $child[0], $labels);
        }
    }
    return $result;
}

/*******************************************************************************
 * Function getRowData($prefix, $orgid, $labels)
 * 
 * Generates one row of table data for the specified orgid
 * 
 * Parameters
 * string prefix: string to prefix the orgname with - for indentation purposes
 * int orgid: The id of the organization to list data for
 * array labels: The list of all the label ids for this row
 * 
 * Returns:
 * string result: one <tr> of table data
 *******************************************************************************/

function getRowData($prefix, $orgid, $labels) {
    $result = "<td>".$prefix.getOrgName($orgid)."</td>";
    foreach ($labels as $label) {
        $result .= "<td>".getCountAtThreshold($orgid, $label)."</td>";
    }
    return "<tr>".$result."</tr>";
}

?>
