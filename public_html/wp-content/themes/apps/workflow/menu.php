<div id="workflow-navbar">
    <?php
        $admin = Workflow::isAdmin(Workflow::loggedInUser());
        if($admin) { 
    ?>
    <a href="?page=createworkflow">Create New Form</a>
    <?php 
        } 
        if(Workflow::isAdmin(Workflow::loggedInUser()) || Workflow::debugMode()) {
    ?>
    
    <a href="?page=view">Form Links</a>
    
    <?php 
        } 
    ?>
    
    <a href="?page=viewsubmissions&forms=my">My Forms</a>
    <a href="?page=viewsubmissions&forms=staff">My Staff's Forms</a>
    
    <?php 
        if($admin) {
    ?>
    <a href="?page=viewsubmissions&forms=all">All Staff's Forms</a>
    <a href="?page=roles">Roles</a>
    <?php 
        } 
    ?>
</div>