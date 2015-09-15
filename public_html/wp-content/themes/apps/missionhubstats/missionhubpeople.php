<?php


$people = getIndexOfEndpoint('people', 'interactions');
var_dump($people);
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
//for ($i = 0; $i < count($people['people']); $i++) {
//  //  echo "<br /> At " . $i . " the label is ";
//   var_dump($people['people'][$i]['interactions']);
//    echo "<br />";
//    if ($people['people'][$i]['interactions'][0]['interaction_type_id'] == 4) {
//        echo $people['people'][$i]['first_name'];
//    }
//}
//    
?>