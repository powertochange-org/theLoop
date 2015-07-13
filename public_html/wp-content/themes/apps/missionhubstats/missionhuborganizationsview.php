<?php



?>
<script type="text/javascript" >
(function($) {
    $(document).ready(function() {
        $("#submit").click(function() {
            var e = document.getElementById("orgname");
            var orgname = e.options[e.selectedIndex].value;
            console.log(orgname);
            jQuery.ajax({
                type: "POST",
                url: 'missionhuborganizations.php',
                dataType: 'json',
                data: {functionname: 'createEngagementReport', arguments: [orgname]},
                success: function (obj, textstatus) {
                    console.log("something happened!");
                    if(!('error' in obj) ) {
                        $("#test").html = obj.result;
                    }
                    else {
                        console.log(obj.error);
                    }
                },
                error: function(textStatus, errorThrown) {
                    console.log("Status: " + textStatus);
                    console.log("Error: " + errorThrown) 
                } 
            });
        }); 
    });
})(jQuery);
</script>
<div id="dropdown">
    
<select id="orgname" name="orgname" form="orgselect">
<?php

require('missionhuborganizations.php');

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
<div id="test"></div>
<div id="table">    
    <table>    
        <tr>
            <th>Organization</th>
            <th>Threshold 1</th>
            <th>Threshold 2</th>
            <th>Threshold 3</th>
            <th>Threshold 4</th>
            <th>Threshold 5</th>
        </tr>        
        <?php
            if(isset($_GET['selected'])) {
                $selected = true;
                $selectedorgname = $_GET['orgname'];
            } else {
                $selected = false;
                $selectedorgname = "";
            }
            var_dump($selectedorgname);
            $selectedorgid = getOrgId($selectedorgname);
            var_dump($selectedorgid);
            $selectedorg = showEndpoint('organizations', $selectedorgid, 'people');
            $selectedorgchildren = getChildren($selectedorgid);

            //var_dump($selectedorg);

            //FOLLOWING SHOULD PROBABLY BE REPLACED BY A REPORT OBJECT ONCE I GET A BETTER HANDLE ON HOW TO GENERICIZE THIS
            echo "<tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>"
            
        ?>
    </table>
</div>