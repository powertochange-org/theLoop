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


if(!isset($_POST["count"])) {
    die("count field missing");
}

if(!isset($_POST["wfid"])) {
    die("count field missing");
}

if(!isset($_POST["sbid"])) {
    die("submission id field missing");
}

if(!isset($_POST["ns"])) {
    die("ns field missing");
}

$loggedInUser = Workflow::loggedInUser();
if($loggedInUser == '0') {
    die('You need to log in first.');
}


$numfields = $_POST['count'];
$wfid = $_POST["wfid"];
$sbid = $_POST["sbid"];
$newstatus = $_POST["ns"];
$fields = array();
$misc_content = '';
$commenttext = '';

//echo 'The total count is: '.$_POST['count'].' and the new status is :'.$newstatus.'<br>';

if(isset($_POST['misc_content']) && $_POST['misc_content'] != '') {
    $misc_content = stripslashes($_POST['misc_content']);
} 

if(isset($_POST['commenttext']) && $_POST['commenttext'] != '') {
    $commenttext = stripslashes($_POST['commenttext']);
} 


//echo '<br>';
for($i = 0; $i < $numfields; $i++) {
    if(!isset($_POST['workflowfieldid'.$i])) {
        //echo '<span style="color:red;">workflowfieldid'.$i.' NOT SET.</span><br>';
        continue;
    }
    
    
    $value = $_POST['workflowfieldid'.$i];
    //echo '<span style="color:blue;">workflowfieldid'.$i.' VALUE: '.$value.'</span><br>';
    
    //TODO: if there is a form already created just update it
    
    $fields[] = array($i, $value);
}


$obj = new Workflow();
//$fields, $newstatus, $submissionID, $formID, $user
$obj->updateWorkflowSubmissions($fields, $newstatus, $sbid, $wfid, $loggedInUser, $misc_content, $commenttext);

header('location: ?page=viewsubmissions');
?>