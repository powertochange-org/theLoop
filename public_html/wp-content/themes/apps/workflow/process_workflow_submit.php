<?php
/*
*Processes data and prepares it for updating a form submission.
*
*
* //TODO: create better documentation
*
*
*
* author: gerald.becker
*
*/


if(!isset($_POST["wfid"])) {
    die("wfid field missing");
}

if(!isset($_POST["sbid"])) {
    die("submission id field missing");
}

if(!isset($_POST["ns"])) {
    die("ns field missing");
}

$loggedInUser = Workflow::loggedInUser();
if($loggedInUser == '0') {
    $_SESSION['ERRMSG'] = 'You need to log in first.';
    header('location: ?page=viewsubmissions');
    die();
}

if(isset($_POST["onbehalf"]) && $_POST["onbehalf"] != '' && $_POST["onbehalf"] != 'Myself') {
    $behalfof = $loggedInUser;
    $loggedInUser = $_POST["onbehalf"];
    if(Workflow::getUserName($loggedInUser) == '') {
        $_SESSION['ERRMSG'] = 'User does not exist';
        header('location: ?page=viewsubmissions');
        die();
    }
}

//TODO: Add more security to make sure that the submission is from the correct person.

$numfields = $_POST['count'];
$wfid = $_POST["wfid"];
$sbid = $_POST["sbid"];
$newstatus = $_POST["ns"];
$statuslevel = 0;
if(isset($_POST["statuslevel"]))
    $statuslevel = $_POST["statuslevel"];
$fields = array();
$miscfields = array();
$misc_content = '';
$commenttext = '';
$hrnotes = '';
$sup = 0;

//echo 'The total count is: '.$_POST['count'].' and the new status is :'.$newstatus.'<br>';

//This happens if the user did not click the approve or deny button and someone clicked the submit button by pressing enter
if($newstatus == 0) { 
    $_SESSION['ERRMSG'] = 'Error submitting form.';
    header('location: ?page=viewsubmissions');
    die();
}

if(isset($_SESSION['ERRMSG']) && $_SESSION['ERRMSG'] == 'deny' && $newstatus == 8) {
    $_SESSION['ERRMSG'] = 'You have denied submission #'.$sbid;
} else if(isset($_SESSION['ERRMSG']) && $_SESSION['ERRMSG'] == 'approve') {
    $_SESSION['ERRMSG'] = 'You have approved submission #'.$sbid;
} else {
    $_SESSION['ERRMSG'] = '';
}

if(isset($_POST['misc_content']) && $_POST['misc_content'] != '') {
    $misc_content = stripslashes($_POST['misc_content']);
    $misc_content = str_replace("<script", htmlentities("<script"), $misc_content);
    $misc_content = str_replace("</script", htmlentities("</script"), $misc_content);
} 

if(isset($_POST['commenttext']) && $_POST['commenttext'] != '') {
    $commenttext = stripslashes($_POST['commenttext']);
    $commenttext = str_replace("<script", htmlentities("<script"), $commenttext);
    $commenttext = str_replace("</script", htmlentities("</script"), $commenttext);
} 

if(isset($_POST['hrnotes']) && $_POST['hrnotes'] != '') {
    $hrnotes = stripslashes($_POST['hrnotes']);
    $hrnotes = str_replace("<script", htmlentities("<script"), $hrnotes);
    $hrnotes = str_replace("</script", htmlentities("</script"), $hrnotes);
} 

if(isset($_POST['directsupervisor']) && $_POST['directsupervisor'] != '') {
    $sup = $_POST['directsupervisor'];
}

/* Old method for the supervisor override. Used for behalf of submissions.*/
if(isset($_POST['nextsupervisor']) && $_POST['nextsupervisor'] != '') {
    $sup = $_POST['nextsupervisor'];
} 

if(isset($_POST['uniquetoken']) && $_POST['uniquetoken'] != '') {
    $uniqueToken = $_POST['uniquetoken'];
} 



for($i = 0; $i < $numfields; $i++) {
    if(!isset($_POST['workflowfieldid'.$i])) {
        continue;
    }
    
    $value = $_POST['workflowfieldid'.$i];
    
    $fields[] = array($i, $value);
}

if(isset($_POST['reminderdate'])) {
    $miscfields['SEND_REMINDER'] = $_POST['reminderdate'];
}

$obj = new Workflow();

$sbid = $obj->updateWorkflowSubmissions($fields, $newstatus, $sbid, $wfid, $loggedInUser, $misc_content, $commenttext, $behalfof, $sup, $uniqueToken, $miscfields, $hrnotes, $statuslevel);

if($sbid != 0)
    $obj->sendEmail($sbid);


if(isset($_POST['approverredirect']))
    header('location: ?page=viewsubmissions&forms=staff');
else
    header('location: ?page=viewsubmissions');
?>