<?php



?>
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="missionhuborganizationsview.js"></script>
<div id="dropdown">
    
<select id="orgname" name="orgname" form="orgselect">
<?php

include_once('missionhuborganizations.php');

$orgs = getListOfOrgNames();
asort($orgs);

foreach($orgs as $org) {
    //TODO string processing to make each orgname without spaces
    echo '<option value="' . $org[0] . '">'.$org[0].'</option>';
}

?>
</select>
    <button id="submit">Submit</button>

</div>


<br>
<div id="table"></div>
<br>