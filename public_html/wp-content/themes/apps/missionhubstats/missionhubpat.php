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
 * string year: The year selected and passed through from the main page.
 *
 * Returns:
 * string result: The resulting html to produce a table to be displayed to the user.
  ***************************************************************************************************/ 

function createPatReport($season, $year) {
    
    $response = "<tr>
                    <th>Project</th>
                    <th># Students</th>
                    <th># Interns</th>
                </tr>";
    
    if ($year == NULL) {
        $CurrDate = strtotime(date("Y-m-d"));
        $cutoff = strtotime(date("Y") . "-09-01");
        if ($cutoff > $CurrDate) {
            $startdate = (date("Y") - 1) . "-09-01";
            $enddate = date("Y") . "-08-31";
        } else {
            $startdate = date("Y") . "-09-01";
            $enddate = (date("Y") + 1) . "-08-31";
        }
    } else {
        $startdate = $year . "-09-01";
        $enddate = ($year + 1) . "-8-31";        
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
	
	if (is_wp_error($mydb->error)) {
		return "Error accessing the PAT database."; // More detailed error message for debugging: $mydb->error->get_error_message();
	}
	
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
    $patlink = '<br><br><font size="small"><i>Login to the <a href="https://pat.powertochange.org/">PAT</a> for more info</i></font>';
    
    return "<table>" . $response . "</table>" . $patlink;
    
    
}

?>