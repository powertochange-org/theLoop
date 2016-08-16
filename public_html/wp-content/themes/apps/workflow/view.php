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
<a href="?page=view" style="margin-right: 10px;<?php if(!isset($_GET['disabled'])&&!isset($_GET['draft'])) echo 'border-bottom:2px solid black;'; ?>">Enabled</a>
<a href="?page=view&amp;disabled" style="margin-right: 10px;<?php if(isset($_GET['disabled'])) echo 'border-bottom:2px solid black;'; ?>">Disabled</a>
<a href="?page=view&amp;draft" <?php if(isset($_GET['draft'])) echo 'style="border-bottom:2px solid black;"'; ?>>Drafts</a>
<br><br>
<?php

$obj = new Workflow();
echo $obj->viewAllWorkflows();
?>
