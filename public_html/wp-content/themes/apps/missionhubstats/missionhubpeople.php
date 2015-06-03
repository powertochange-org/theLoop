<?php

$people = getIndexOfEndpoint("people", "", "", "", "", "", "");
echo "<br /><br />";
echo count($people['people']);
echo "<br />";
?> <table>
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

?>