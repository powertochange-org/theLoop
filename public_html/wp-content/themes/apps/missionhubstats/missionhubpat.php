<?php

/****************************************************************************************************
 *
 * Project Application Tool interface.  This file is responsible for getting and printing data
 * from the PAT database.
 *
 ***************************************************************************************************/

/****************************************************************************************************
 * Function createPatReport($season, $startdate, $enddate)
 * 
 * Parameters:
 * string season: optional parameter to specify season. If not specified, a report covering all seasons
 * will be generated.
 * string startdate: optional parameter to specify start date of report.  If not specified, a report 
 * covering the current year (Aug-July) will be generated.
 * string enddate: optional parameter to specify the end date of the report. If not specified, a report
 * covering the current year (Aug-July) will be generated.
 * 
 * NOTE: including just one of startdate or enddate, but not both, is an error case.
 *
 * Returns:
 * string result: The resulting html to produce a table to be displayed to the user.
  ***************************************************************************************************/ 

function createPatReport($season, $startdate, $enddate) {
    
    $response = "<tr>
                    <th>Project</th>
                    <th># Students</th>
                    <th># Interns</th>
                </tr>";
    
    if ($startdate == NULL && $enddate != NULL) 
        die ("Please enter an end date.");
    if ($startdate != NULL && $enddate == NULL)
        die ("Please enter a start date.");
    if ($startdate == NULL && $enddate == NULL) {
        $startdate = (date("Y") - 1) . "-08-01";
        $enddate = (date("Y") . "07-31");
    }
    
    $mydb = new wpdb(DB_USER, DB_PASSWORD, PAT_DB_NAME, DB_HOST); 
    $sql = $mydb->prepare("SELECT event_groups.id, event_groups.title, projects.id AS 'Project ID', projects.title AS 'Project', SUM(`profiles`.as_intern) AS 'Interns', COUNT(`profiles`.id) - COALESCE(SUM(`profiles`.as_intern), 0) AS 'Students'
    FROM `event_groups` JOIN
        `projects`ON event_groups.id = projects.event_group_id JOIN
        `profiles` ON projects.id = profiles.project_id
    WHERE parent_id = 1 AND projects.start > %s AND projects.start < %s AND profiles.status = 'accepted'
    GROUP BY event_groups.id, event_groups.title, projects.id, projects.title",
    $startdate,
    $enddate
    );
    $result = $mydb->get_results($sql);
    foreach($result as $obj) {
        if ($obj->Students == NULL) {$obj->Students = 0;}
        if ($obj->Interns == NULL) {$obj->Interns = 0;}
        $response = $response . "<tr><td>" . $obj->Project . "</td><td>" . $obj->Students . "</td><td>" . $obj->Interns . "</td></tr>";
    }
    
    return "<table>" . $response . "</table>";
    
    
}

function createPatInterface() {
    
    $result = "
        <div id=\"startdate\">
        
        </div>
        <div id=\"enddate\">
        
        </div>";
    
    
    
}


?>