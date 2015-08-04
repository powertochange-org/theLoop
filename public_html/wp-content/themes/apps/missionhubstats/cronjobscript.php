<?php

/****************************************************************************************************
 *
 * Cronjobscript.php
 *
 * Date create: June 25, 2015
 * Author: Nathaniel Faries
 *
 * This script will run on a nightly basis to update the database table mh_org_tree.  Curl request
 * is directly copied from missionhubapirequests.php with only a change to $curl_address.
 *
 * Cronjob line: 0 0 * * * cronjobscript.php
 *
 ***************************************************************************************************/

require_once('../../../../wp-config.php');
require_once('missionhubapirequests.php');

/****************************************************************************************************
 * Begin curl request 
 ***************************************************************************************************/

$curl_address = BASE_URL . 'organizations?secret=' . MISSIONHUB_AUTH_KEY;

$curl = curl_init($curl_address);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  //Return the data rather than printing it on the screen
$curl_response = curl_exec($curl);

if ($curl_response === false) {
    $info = curl_getinfo($curl);
	$error = curl_error($curl);
    curl_close($curl);
	die("Error occurred during curl execution: " . $error . "\r\nAdditional info:\r\n" . var_export($info, true));
}

$decoded_response = json_decode($curl_response, true);

if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
  	die('Error occurred: ' . $decoded->response->errormessage);
}

/****************************************************************************************************
 * End curl request
 ***************************************************************************************************/

//Creating database object
$orgdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
//Define format for rows to be inserted into database
$format = array(
    '%d',
    '%d',
    '%s'
);

//Iterate through all the objects returned from the curl request and put them into an associative array to pass to the database.
foreach($decoded_response['organizations'] as $organization) {
    $orgarray = array(
        'id' => $organization['id'],
        'parent_id' => (int) substr(strrchr($organization['ancestry'], '/'), 1),
        'name' => $organization['name']
    );
    $result = $orgdb->replace('mh_org_tree', $orgarray, $format);
	if ($result) {
		echo "Successfully updated $organization[name]\r\n";
	} else {
		echo "Problem updating $organization[name]\r\n";
	}
}

?>