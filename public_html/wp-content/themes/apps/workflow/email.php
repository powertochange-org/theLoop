<?php


if(!isset($_GET['sbid']))
    die('sbid needed');

$workflow = new Workflow();
$workflow->sendEmail($_GET['sbid']);
?>