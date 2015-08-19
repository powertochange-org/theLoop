<?php
/*
*Used to save a new Workflow configuration to the server. 
*
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

if(!isset($_POST['destination']) || $_POST['destination'] == '') {
    die("destination field missing");
}

if(!isset($_POST["count"])) {
    die("count field missing");
}


$destination2 = -1;
$destination3 = -1;
$destination4 = -1;

if(isset($_POST['destination2']) || $_POST['destination2'] == '') {
    $destination2 = $_POST['destination2'];
}
if(isset($_POST['destination3']) || $_POST['destination3'] == '') {
    $destination3 = $_POST['destination3'];
}
if(isset($_POST['destination4']) || $_POST['destination4'] == '') {
    $destination4 = $_POST['destination4'];
}

//echo 'The total count is: '.$_POST['count'].'<br>';

$numfields = $_POST['count'];
//$fields = array(); 

$myWorkflow = new Workflow();
$myWorkflow->createWorkflow($_POST['workflowname'], $_POST['startaccess'], $_POST['destination'], $destination2, $destination3, $destination4);

//When adding POST fields, make sure to change the javascript file function called addField. The reason for this
//is that the javascript is configuring the numbering so this page can add it to the database. 
for($i = 0; $i < $numfields; $i++) {
    if(isset($_POST['editable'.$i]) && $_POST['editable'.$i] == 'on')
        $editable = 1;
    else
        $editable = 0;
    
    if(isset($_POST['approvalonly'.$i]) && $_POST['approvalonly'.$i] == 'on')
        $approvalonly = 1;
    else
        $approvalonly = 0;
    
    if(isset($_POST['approvalshow'.$i]) && $_POST['approvalshow'.$i] == 'on')
        $approvalshow = 1;
    else
        $approvalshow = 0;
    
    //echo Workflow::translateFieldType($_POST['fieldtype'.$i]).' | '.$_POST['workflowlabel'.$i].' | '.$editable.' | '.$approvalonly.' | '.$approvalshow.' | '.$_POST['destination'].'<br>';
    //$fields[] = array($_POST['fieldtype'.$i], $_POST['workflowlabel'.$i], $editable, $approvalonly);
    //(Field Type, Label, Editable, Approval Only)
    $myWorkflow->addField($_POST['fieldtype'.$i], $_POST['workflowlabel'.$i], $editable, $approvalonly, $approvalshow, 
        $_POST['workflowsize'.$i], $_POST['approvallevel'.$i]);
}

/*echo '<br>TEST<br><br>';
for($i = 0; $i < count($fields); $i++) {
    echo $fields[$i][0].' | '.$fields[$i][1].' | '.$fields[$i][2].' | '.$fields[$i][3].'<br>';
}*/



//echo '<br>myWorkflowECT TEST<br>';
//echo $myWorkflow->debugDisplayWorkflow();

$myWorkflow->storeToDatabase();

header('location: ?page=view');
/*if(!isset($_POST["name"]) || strlen($_POST["name"]) == 0) {
    die("Oh no Jim he's Dead! -> name");
}

if(!isset($_POST["age"]) || strlen($_POST["age"]) == 0) {
    die("Oh no Jim he's Dead! -> age");
}

if(!isset($_POST["gender"]) || strlen($_POST["gender"]) == 0) {
    die("Oh no Jim he's Dead! -> gender");
}

echo "Name: @".strlen($_POST["name"])." : ".$_POST["name"];

$name = $_POST["name"];
$age = $_POST["age"];
$gender = $_POST["gender"];

//Connect to server and select database
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

//Check if connection succeeded
if(!$con) {
    die("Connection failed ");
}
    
$sql = "INSERT INTO person (NAME, AGE, GENDER)
        VALUES ('$name', '$age', '$gender')";
                
                
$result = mysqli_query($con, $sql);

    
mysqli_close($con);

if($result) {
    header("location: ./person.php");
    //
    
} else {
    echo "error";
    //header("location: ./index.php");
}*/

/*function translateFieldType($type) {
    if($type == 0) {
        return 'Textbox';
    } else if($type == 1) {
        return 'Label';
    } else if($type == 2) {
        return 'Option';
    } else if($type == 3) {
        return 'Newline';
    } else {
        return '---';
    }
}*/

?>