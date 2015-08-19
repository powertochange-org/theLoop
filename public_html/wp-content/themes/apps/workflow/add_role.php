<?php
/*
*Adds roles, role members and deletes members from the server.
*
*
* //TODO: create better documentation
*
*
*
* author: gerald.becker
*
*/
if(!isset($_POST['mode']) || $_POST['mode'] == '') {
    die("mode field missing");
}


$workflow = new Workflow();


$mode = $_POST['mode'];


if($mode == 1) {
    if(!isset($_POST['rolename']) || $_POST['rolename'] == '') {
        die("rolename field missing");
    }
    $rolename = $_POST['rolename'];
    
    if(!$workflow->storeRole($rolename)) {
        die('Failed to insert');
    }
    
} else if($mode == 2) {
    if(!isset($_POST['addmemberrole']) || $_POST['addmemberrole'] == '') {
        die("addmemberrole field missing");
    }
    $role = $_POST['addmemberrole'];
    
    if(!isset($_POST['addmembername']) || $_POST['addmembername'] == '') {
        die("addmembername field missing");
    }
    $name = $_POST['addmembername'];
    
    $workflow->storeMember($role, $name);
}

header('location: ?page=roles');


?>