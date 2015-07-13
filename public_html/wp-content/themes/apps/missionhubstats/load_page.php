<?php

if(!$_POST['report']) die("0");

$report = $_POST['report'];

if(file_exists('missionhub'.$report.'.php')) {
    echo file_get_contents('missionhub'.$report.'.php');
}

else echo 'Error!';
?>