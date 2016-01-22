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


<h1>Form Links</h1>
<a href="?page=view" style="margin-right: 10px;">Enabled</a>
<a href="?page=view&amp;disabled" style="margin-right: 10px;">Disabled</a>
<a href="?page=view&amp;draft">Drafts</a>
<br><br>
<?php

$obj = new Workflow();
echo $obj->viewAllWorkflows();
?>
