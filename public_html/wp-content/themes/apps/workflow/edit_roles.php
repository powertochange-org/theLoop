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
if(!Workflow::isAdmin(Workflow::loggedInUser())) {
    $_SESSION['ERRMSG'] = 'Oops! You tried to access a page you shouldn\'t have.';
    header('location: ?page=viewsubmissions');
    die();
}
if(!isset($_POST['mode']) || $_POST['mode'] == '') {
    die("mode field missing");
}


$workflow = new Workflow();


$mode = $_POST['mode'];


if($mode == 1) {
    if(!isset($_POST['rolename']) || $_POST['rolename'] == '') {
        $_SESSION['ERRMSG'] = 'rolename field missing.';
        header('location: ?page=roles');
        die();
    }
    $rolename = $_POST['rolename'];
    
    if(!$workflow->storeRole($rolename)) {
        $_SESSION['ERRMSG'] = 'Failed to add role.';
        header('location: ?page=roles');
        die();
    }
    
} else if($mode == 2) {
    if(!isset($_POST['addmemberrole']) || $_POST['addmemberrole'] == '') {
        $_SESSION['ERRMSG'] = 'addmemberrole field missing.';
        header('location: ?page=roles');
        die();
    }
    $role = $_POST['addmemberrole'];
    
    if(!isset($_POST['addmembername']) || $_POST['addmembername'] == '') {
        $_SESSION['ERRMSG'] = 'addmembername field missing.';
        header('location: ?page=roles');
        die();
    }
    $name = $_POST['addmembername'];
    
    if(!$workflow->storeMember($role, $name)){
        $_SESSION['ERRMSG'] = 'Failed to add user.';
        header('location: ?page=roles');
        die();
    }
        
} else if($mode == 3) {
    if(!isset($_POST['removemember']) || $_POST['removemember'] == '') {
        $_SESSION['ERRMSG'] = 'removemember field missing.';
        header('location: ?page=roles');
        die();
    }
    $roletoremove = $_POST['removemember'];
    
    $length = strlen($roletoremove);
    //echo 'Length:'.$length.'<br>';
    $endVal = stripos($roletoremove, 'USER');
    //echo $endVal;
    //echo '<br>ROLE:'.substr($roletoremove, 4, $endVal - 4);
    //echo '<br>USER:'.substr($roletoremove, $endVal + 4, $length - $endVal + 4);
    
    $role = substr($roletoremove, 4, $endVal - 4);
    $user = substr($roletoremove, $endVal + 4, $length - $endVal + 4);
    
    if(!$workflow->removeMember($role, $user)){
        $_SESSION['ERRMSG'] = 'Failed to remove user.';
        header('location: ?page=roles');
        die();
    }
        
} else if($mode == 4) {
    foreach ($_POST as $key => $value) {
        //echo 'DEBUG:'.$key . ' has the value of ' . $value.'<br>';
        
        if(stripos($key, 'USER') != 0) {
            $length = strlen($key);
            $endVal = stripos($key, 'USER');
            
            
            $role = substr($key, 4, $endVal - 4);
            $user = substr($key, $endVal + 4, $length - $endVal + 4);
            
            $checked = ($value == 'on') ? 1 : 0;
            
            //echo 'DEBUG: ROLE:'.$role.' USER:'.$user.' CHECKED: '.$checked.'<br><br>';
            
            $workflow->updateMemberEmail($role, $user, $checked);
        }
    }
    
}

header('location: ?page=roles');


?>