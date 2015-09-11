<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/missionhubstats/missionhuborganizationsview.js"></script>
<div id="reporttype">
Report: 
<!--There's going to be some php most likely in here to generate all the report types...but maybe not-->
    <select id="report" name="report" >
        <option value="">--SELECT A REPORT--</option>
        <option value="engagement">Engagement Report</option>
        <option value="discipleship">Discipleship Report</option>
        <option value="decision">Indicated Decision Report</option>
        <option value="pat">PAT Report</option>
    </select>
</div>

<div id="organizations">
Organization
    <select id="orgname" name="orgname" form="orgselect">
        <?php

        include_once('missionhuborganizations.php');

        $orgs = getListOfOrgNames();
        asort($orgs);

        foreach($orgs as $org) {echo '<option value="' . $org[0] . '">'.$org[0].'</option>';}
        ?>
    </select>
</div>

<br>

<button id="submit" value="Generate Report!">Generate Report!</button>


<br>
<div id="report-table"></div>
<br>