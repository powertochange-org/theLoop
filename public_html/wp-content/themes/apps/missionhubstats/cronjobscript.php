<?php

/****************************************************************************************************
 *
 * Cronjobscript.php
 *
 * Date create: June 25, 2015
 * Author: Nathaniel Faries
 *
 * This script will run on an hourly basis to update the database table mh_org_tree.  Curl request
 * is directly copied from missionhubapirequests.php with only a change to $curl_address.
 *
 * Cronjob line: 0 * * * * cronjobscript.php
 *
 ***************************************************************************************************/

require_once('../../../../wp-config.php');
require_once('missionhubapirequests.php');

/****************************************************************************************************
 * Organization structure
 ***************************************************************************************************/

//Creating database object
$orgdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
//Define format for rows to be inserted into database
$format = array(
    '%d', //id
    '%d', //parent_id
    '%s', //name
    '%d', //Threshold 1
    '%d', //Threshold 2
    '%d', //Threshold 3
    '%d', //Threshold 4
    '%d', //Threshold 5
    '%d', //Disciple threshold 1
    '%d', //Disciple threshold 2
    '%d', //Disciple threshold 3
    '%d', //Exposures
    '%d', //Admins
    '%d', //Users
);

$organizations = getIndexOfEndpoint('organizations');


//Iterate through all the objects returned from the curl request and put them into an associative array to pass to the database.
foreach($organizations['organizations'] as $organization) {
    $admins = getIndexOfEndpoint('people', '', $organization['id'], '', '', '', array('roles' => 'admins'));
    $users = getIndexOfEndpoint('people', '', $organization['id'], '', '', '', array('roles' => 'users'));
    $total = getIndexOfEndpoint('people', '', $organization['id']);
    $t1 = getIndexOfEndpoint('people', 'organizational_labels', $organization['id'], '', '', '', array('labels' => 14121));
    $t2 = getIndexOfEndpoint('people', 'organizational_labels', $organization['id'], '', '', '', array('labels' => 14122));
    $t3 = getIndexOfEndpoint('people', 'organizational_labels', $organization['id'], '', '', '', array('labels '=> 14123));
    $t4 = getIndexOfEndpoint('people', 'organizational_labels', $organization['id'], '', '', '', array('labels' => 14124));
    $t5 = getIndexOfEndpoint('people', 'organizational_labels', $organization['id'], '', '', '', array('labels' => 14125));
    $d1 = getIndexOfEndpoint('people', 'organizational_labels', $organization['id'], '', '', '', array('labels' => 14126));
    $d2 = getIndexOfEndpoint('people', 'organizational_labels', $organization['id'], '', '', '', array('labels' => 14127));
    $d3 = getIndexOfEndpoint('people', 'organizational_labels', $organization['id'], '', '', '', array('labels' => 14128));
    $orgarray = array(
        'id' => $organization['id'],
        'parent_id' => (int) substr(strrchr($organization['ancestry'], '/'), 1),
        'name' => $organization['name'],
        't1' => sizeof($t1['people']),
        't2' => sizeof($t2['people']),
        't3' => sizeof($t3['people']),
        't4' => sizeof($t4['people']),
        't5' => sizeof($t5['people']),
        'd1' => sizeof($d1['people']),
        'd2' => sizeof($d2['people']),
        'd3' => sizeof($d3['people']),
        'exposures' => sizeof($total['people']) - (sizeof($admins['people']) + sizeof($users['people'])),
        'admins' => sizeof($admins['people']),
        'users' => sizeof($users['people'])
    );
    $result = $orgdb->replace('mh_org_tree', $orgarray, $format);
	if ($result) {
		echo "Successfully updated $organization[name]\r\n";
	} else {
		echo "Problem updating $organization[name]\r\n";
	}
}
?>