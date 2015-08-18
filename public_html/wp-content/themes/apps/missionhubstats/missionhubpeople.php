<?php


$people = getIndexOfEndpoint('people', 'interactions');
echo "<br /><br />";
echo count($people['people']);
echo "<br />";
?>
<table>
<?php 
for ($i = 0; $i < count($people['people']); $i++) {
    echo "<tr>";
    foreach($people['people'][$i] as $propertyname=>$value) {
        echo "<td>" . $propertyname . $value . "</td>";
    }
    echo "</tr>";
}
?>
</table>
<?php
for ($i = 0; $i < count($people['people']); $i++) {
  //  echo "<br /> At " . $i . " the label is ";
    var_dump($people['people'][$i]['interactions']);
    echo "<br />";
    foreach($people['people'][$i]['interactions'] as $propertyname=>$value) {
        echo "<p>" . $people['people'][$i]['first_name'] . " has " . $propertyname . " of " . $value . " as their label.";
    }
}
    
?>