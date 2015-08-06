<?php



?>
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="missionhuborganizationsview.js"></script>
<div id="reporttype">
Report
<!--There's going to be some php most likely in here to generate all the report types...but maybe not-->
    <select id="report" name="report" >
        <option value="">--SELECT A REPORT--</option>
        <option value="engagement">Engagement Report</option>
        <option value="discipleship">Discipleship Report</option>
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
<div id="daterange">
    Select school year:
    <select id="year">
        <option value="">--SELECT A YEAR--</option>
        <?php
            $CurrYear = date("Y");
            $CurrDate = strtotime(date("Y-m-d"));
            $cutoff = strtotime($CurrYear . "-09-01");
            if ($cutoff > $CurrDate) {
                $x = 0;
            } else {
                $x = -1;
            }
            while ($CurrYear - $x >= 2007) {
                ?><option value='<?php echo $CurrYear-$x-1;?>'>
                <?php echo "September " . ($CurrYear - $x - 1). " to August " . ($CurrYear - $x);?></option>
                <?php
                $x++;
            }
        ?>
    </select>
    
</div>
<br>

<button id="submit">Generate Report!</button>


<br>
<div id="table"></div>
<br>