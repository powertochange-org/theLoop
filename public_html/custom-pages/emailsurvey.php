<?php
header("Content-Type: image/png"); // it will return image 
readfile("nothing.png");

global $GET_WORD_PRESS_VARIABLE;
$GET_WORD_PRESS_VARIABLE = true;

include('../wp-config.php');

// Creates a connection
$con = mysqli_connect(constant("DB_HOST"),constant("DB_USER"),constant("DB_PASSWORD"),constant("DB_NAME"));


$uid = '0';
$status = 1; //opened
$subject = 'UNKNOWN';
$date_opened = date('Y-m-d H:i:s');
$debug = '';
$skip = false;

if(isset($_GET['uid']))
    $uid = $con->real_escape_string($_GET['uid']);

$sql = "SELECT *, COUNT(*) AS count FROM email_open_tracking WHERE trackingid = '$uid'";
$result = $con->query($sql);
if($result) {
    $obj = $result->fetch_object();
    //Reset the uid and create a debug line and insert it for tracking
    if($obj->count == 0) {
        $debug = 'User tried uid '.$uid;
        $uid = '0';
    }
    //Ignore if the user has already opened the email
    if($obj->date_opened != null && $obj->status == 1) {
        $skip = true;
    }
}

//Insert a new entry in the db or update the actual tracking record
if($uid == '0') {
    $sql = "INSERT INTO email_open_tracking (trackingid, email_subject, status, date_opened, debug)
            VALUES ('$uid', '$subject', '$status', '$date_opened', '$debug')";
} else if(!$skip) {
    $sql = "UPDATE email_open_tracking
            SET status = 1,
                date_opened = '$date_opened'
            WHERE trackingid = '$uid'";
}

if(!$skip) {
    $result = $con->query($sql);
}

$con->close();

?>
