<?php
/*
*Updates if a form is enabled or disabled. 
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


$workflow = new Workflow();


foreach ($_POST as $key => $value) {
    if(stripos($key, 'FORM') == 0) {
        $length = strlen($key);
        
        $form = substr($key, 4, $length);
        
        $checked = ($value == 'on') ? 1 : 0;
        
        $workflow->updateForms($form, $checked);
    }
    
    
}

header('location: ?page=view');


?>