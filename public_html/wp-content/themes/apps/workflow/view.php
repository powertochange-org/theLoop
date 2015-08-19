<?php 
/*
*Displays the links to all the workflow forms that were created.
*
*
* //TODO: create better documentation
*
* author: gerald.becker
*
*/
?>

<div id="main-content">

    <div id="content-workflow">
        <h1>Form Links</h1>
        <?php
        
        $obj = new Workflow();
        echo $obj->viewAllWorkflows();
        ?>
    </div>
</div>