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

require('public_html/wp-config.php');

/****************************************************************************************************
 * Begin curl request 
 ***************************************************************************************************/
    
$curl_address = 'https://ca.missionhub.com/apis/v3/organizations?secret=' . MISSIONHUB_AUTH_KEY;

$curl = curl_init($curl_address);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  //Not entirely sure what this does, but without it there's a raw dump of data on the screen
$curl_response = curl_exec($curl);

if ($curl_response === false) {
$info = curl_getinfo($curl);
curl_close($curl);
    die('Error occured during curl execution. Additional info ' . var_export($info));
}

$decoded_response = json_decode($curl_response, true);

if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
  	die('Error occured: ' . $decoded->response->errormessage);
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
    //echo "\n" . $orgarray['id'];
    //replace($table, $data, $format) will insert a new row if the data does not exist.
    echo $orgdb->replace('mh_org_tree', $orgarray, $format);
}

?>