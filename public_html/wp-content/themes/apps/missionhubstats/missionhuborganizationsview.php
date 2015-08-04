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
    Select starting date:
    <select id="startmonth">
        <option value="">--SELECT A MONTH--</option>
        <option value="01">January</option>
        <option value="02">February</option>
        <option value="03">March</option>
        <option value="04">April</option>
        <option value="05">May</option>
        <option value="06">June</option>
        <option value="07">July</option>
        <option value="08">August</option>
        <option value="09">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
    </select>
    <select id="startyear">
        <option value="">--SELECT A YEAR--</option>
        <?php $CurrYear = date("Y");
					     $x = 0;
						 while ($CurrYear-$x >= 1989){
						 ?>
						 <option value='<?php echo $CurrYear-$x;?>' 
								     <?php if($RPTYEAR == $CurrYear-$x){echo("selected");}?>>
									 <?php echo $CurrYear-$x;?></option>
						 <?php
						 $x++;
                         } ?>
    </select>
    <br>
    Select ending date:
    <select id="endmonth">
        <option value="">--SELECT A MONTH--</option>
        <option value="01">January</option>
        <option value="02">February</option>
        <option value="03">March</option>
        <option value="04">April</option>
        <option value="05">May</option>
        <option value="06">June</option>
        <option value="07">July</option>
        <option value="08">August</option>
        <option value="09">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
    </select>
    <select id="endyear">
        <option value="">--SELECT A YEAR--</option>
        <?php $CurrYear = date("Y");
					     $x = 0;
						 while ($CurrYear-$x >= 1989){
						 ?>
						 <option value='<?php echo $CurrYear-$x;?>' 
								     <?php if($RPTYEAR == $CurrYear-$x){echo("selected");}?>>
									 <?php echo $CurrYear-$x;?></option>
						 <?php
						 $x++;
                         } ?>
    </select>
</div>
<br>

<button id="submit">Generate Report!</button>


<br>
<div id="table"></div>
<br>