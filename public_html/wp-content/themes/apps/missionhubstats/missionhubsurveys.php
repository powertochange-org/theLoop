<?php 

//require("missionhubapirequests.php"); Don't actually need to "require" this as missionhubstats.php already requires it, and thus when this is included calling getIndexOfEndpoint works fine.

$surveys = getIndexOfEndpoint("surveys", "questions,keyword", "", "", "", "", "");
echo "<br /><br />";
echo count($surveys['surveys']);
echo "<br />";

for ($i = 0; $i < count($surveys['surveys']); $i++) {
    foreach($surveys['surveys'][$i] as $propertyname=>$value) {
        echo $propertyname . " has value " . $value . "<br />";
    }
}


?>