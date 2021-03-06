<?php
/*
*Used to save a new Workflow configuration to the server. 
*Also will save the workflow and allow the user to continue later on.
*
* //TODO: create better documentation
*
*
*
* author: gerald.becker
*
*/


if(!isset($_POST['workflowname']) || $_POST['workflowname'] == '') {
    die("workflowname field missing");
}

if(!isset($_POST['destination1']) || $_POST['destination1'] == '') {
    die("destination field missing");
}

if(!isset($_POST["count"])) {
    die("count field missing");
}

if(!isset($_POST["submitmode"])) {
    die("submitmode field missing");
}

$destination2 = -1;
$destination3 = -1;
$destination4 = -1;
$processor = -1;
$behalfof = $draft = 0;

if(isset($_POST['destination2']) && $_POST['destination2'] != '') {
    $destination2 = $_POST['destination2'];
}
if(isset($_POST['destination3']) && $_POST['destination3'] != '') {
    $destination3 = $_POST['destination3'];
}
if(isset($_POST['destination4']) && $_POST['destination4'] != '') {
    $destination4 = $_POST['destination4'];
}
if(isset($_POST['processor']) && $_POST['processor'] != '') {
    $processor = $_POST['processor'];
}
if(isset($_POST['behalfof'])) {
    $behalfof = 1;
}
$numfields = $_POST['count'];

$submitmode = $_POST['submitmode'];

/*
$submitmode is used to determine whether the form is in the draft state or a brand new form.
Also it determines whether it will be published or just saved as a draft again. 
1 - Brand new draft
2 - Draft saving as a draft again
3 - Brand new publishing 
4 - Draft now being published
*/
if($submitmode == 1 || $submitmode == 2) {
    $draft = 1;
}
//The saved data allows for copying of forms later on.
$savedData = $_POST['savedData'];

if(isset($_POST['previousID']) && ($submitmode == 2 || $submitmode == 4))
    $previousID = $_POST['previousID'];

$myWorkflow = new Workflow();
$myWorkflow->createWorkflow($_POST['workflowname'], $_POST['startaccess'], $_POST['destination1'], $destination2, 
                            $destination3, $destination4, $processor, $behalfof, $draft, stripslashes($savedData), $numfields, $submitmode, $previousID);

//If the form is being published, create and store the fields in the correct table
if($submitmode == 3 || $submitmode == 4) {
    //When adding POST fields, make sure to change the javascript file function called addField. The reason for this
    //is that the javascript is configuring the numbering so this page can add it to the database. 
    for($i = 0; $i < $numfields; $i++) {
        if(isset($_POST['editable'.$i]) && $_POST['editable'.$i] == 'on') {
            $editable = 1;
            //Fix for autofill fields
            if($_POST['fieldtype'.$i] == 5 || $_POST['fieldtype'.$i] == 6)
                $editable = 0;
        } else
            $editable = 0;
        
        if($_POST['approvallevel'.$i] != 0)//if(isset($_POST['approvalonly'.$i]) && $_POST['approvalonly'.$i] == 'on')
            $approvalonly = 1;
        else
            $approvalonly = 0;
        
        if(isset($_POST['approvalshow'.$i]) && $_POST['approvalshow'.$i] == 'on')
            $approvalshow = 1;
        else
            $approvalshow = 0;
        
        if(isset($_POST['requiredfield'.$i]) && $_POST['requiredfield'.$i] == 'on')
            $requiredfield = 1;
        else
            $requiredfield = 0;
        
        if(isset($_POST['workflowtypecheck'.$i]) && $_POST['workflowtypecheck'.$i] == 8) { //Handle the ask a question input
            $myWorkflow->addField(1, $_POST['workflowlabela'.$i], $editable, $approvalonly, $approvalshow, 
                $_POST['workflowsizea'.$i], $_POST['approvallevel'.$i], $requiredfield, 0);
            $myWorkflow->addField(0, $_POST['workflowlabelb'.$i], $editable, $approvalonly, $approvalshow, 
                $_POST['workflowsizeb'.$i], $_POST['approvallevel'.$i], $requiredfield, 0);
        } else if($_POST['fieldtype'.$i] == 13 || $_POST['fieldtype'.$i] == 2) { //Radio buttons or Option list
            $numBtns = $_POST['workflowradiocount'.$i];
            for($x = 0; $x < $numBtns; $x++) {
                $newgroup = ($x == 0) ? 1 : 0;
                $myWorkflow->addField($_POST['fieldtype'.$i], $_POST['workflowradio'.$i.'-'.$x], $editable, $approvalonly, $approvalshow, 
                    $_POST['workflowsize'.$i], $_POST['approvallevel'.$i], $requiredfield, $newgroup);
            }
            
            
        } else {
            $myWorkflow->addField($_POST['fieldtype'.$i], $_POST['workflowlabel'.$i], $editable, $approvalonly, $approvalshow, 
                $_POST['workflowsize'.$i], $_POST['approvallevel'.$i], $requiredfield, 0);
        }
        
        
    }
}

$myWorkflow->storeToDatabase();

if($draft)
    header('location: ?page=view&draft');
else
    header('location: ?page=view');
?>