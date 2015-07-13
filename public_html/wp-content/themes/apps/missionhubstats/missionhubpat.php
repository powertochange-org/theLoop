<?php

/****************************************************************************************************
 *
 * Project Application Tool interface.  This file is responsible for getting and printing data
 * from the PAT database.
 *
 ***************************************************************************************************/


?>
<style>
#buttons {
    position:relative;
    
}
#table {
    position:relative;
    float:center;
}
</style>

<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="missionhubpat.js"></script>
<div id="filterresult"></div>
<div id="buttons">
    <ul>
        <li><a href="#" id="filtersummer">Summer</a></li>
        <li><a href="#" id="filterspring">Spring</a></li>    
    </ul> 
</div>
<div id="table">
    <table>
        <tr>
            <th>Project</th>
            <th># Students</th>
            <th># Interns</th>
        </tr>
        <?php
            $mydb = new wpdb(DB_USER, DB_PASSWORD, PAT_DB_NAME, DB_HOST); 
            $sql = "SELECT event_groups.id, event_groups.title, projects.id AS 'Project ID', projects.title AS 'Project', SUM(`profiles`.as_intern) AS 'Interns', COUNT(`profiles`.id) - COALESCE(SUM(`profiles`.as_intern), 0) AS 'Students'
            FROM `event_groups` JOIN
               `projects`ON event_groups.id = projects.event_group_id JOIN
                `profiles` ON projects.id = profiles.project_id
            WHERE parent_id = 1 AND projects.start > '2015-01-01' AND profiles.status = 'accepted'
            GROUP BY event_groups.id, event_groups.title, projects.id, projects.title";
            $result = $mydb->get_results($sql);
            foreach($result as $obj) {
                if ($obj->Students == NULL) {$obj->Students = 0;}
                if ($obj->Interns == NULL) {$obj->Interns = 0;}
                echo "<tr><td>" . $obj->Project . "</td><td>" . $obj->Students . "</td><td>" . $obj->Interns . "</td></tr>";
            }
        ?>
    </table>

    <br />
    <br />
</div>


<?php



?>

