<?php

/****************************************************************************************************
 *
 * Cronjobscript.php
 *
 * Date create: June 25, 2015
 * Author: Nathaniel Faries
 *
 * This script will run on an hourly basis to update the database table mh_org_tree 
 * and mh_threshold_details.  
 *
 * Cronjob line: 0 * * * * cronjobscript.php
 *
 ***************************************************************************************************/

/* Include configuration information and helper functions for interacting with the MissionHub API */
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
    '%s' //Last updated
);

// Setting up the timezone
date_default_timezone_set('America/Vancouver');

// Get the list of organizations we need to work through
fwrite(STDOUT, "Getting all organizations...\n");

$organizations = getIndexOfEndpoint('organizations');

fwrite(STDOUT, "  Got " . count($organizations['organizations']) . " organizations.\n\n");

// Set up some tracking variables
$index = 0;
$startTime = time();
$timeRemainingMessage = "";

// Iterate through all the objects returned from the curl request and put them into an associative array to pass to the database.
foreach($organizations['organizations'] as $organization) {
	// Attempt to guess how much time is left till the script completes (if this is not the first org we are processing)
	if ($index >= 1) {
		$currentTime = time();
		$elapsedSeconds = $currentTime - $startTime;
		$timePerOrg = $elapsedSeconds / $index;
		$timeRemaining = $timePerOrg * ( count($organizations['organizations']) - $index );
		$timeRemainingMessage = ", est. " . floor($timeRemaining / 60) . " min " . ($timeRemaining % 60) . " sec remaining";
	}

	$index = $index + 1;
	
	// Write a message showing what we are working on. If this is 
	fwrite(STDOUT, "Processing organization $organization[name]... ($index of " . count($organizations['organizations']) . "$timeRemainingMessage)\n");
	
    $admins = getIndexOfEndpoint('people', '', $organization['id'], '', '', '', array('roles' => 'admins'));
    $users = getIndexOfEndpoint('people', '', $organization['id'], '', '', '', array('roles' => 'users'));
    $total = getIndexOfEndpoint('people', 'interactions', $organization['id']);
    if ($total['people'] != NULL) {
        foreach($total['people'] as $person) {
            if ($person['interactions'] != NULL) {
                updateInteractionDetails($organization['id'], $person);
            }
        }
    }
    $t1 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14121));
    updateThresholdDetails($organization['id'], $t1['people'], 14121);
    $t2 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14122));
    updateThresholdDetails($organization['id'], $t2['people'], 14122);
    $t3 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels '=> 14123));
    updateThresholdDetails($organization['id'], $t3['people'], 14123);
    $t4 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14124));
    updateThresholdDetails($organization['id'], $t4['people'], 14124);
    $t5 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14125));
    updateThresholdDetails($organization['id'], $t5['people'], 14125);
    $d1 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14126));
    updateThresholdDetails($organization['id'], $d1['people'], 14126);
    $d2 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14127));
    updateThresholdDetails($organization['id'], $d2['people'], 14127);
    $d3 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14128));
    updateThresholdDetails($organization['id'], $d3['people'], 14128);
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
        'users' => sizeof($users['people']),
        'last_updated' => date(DATE_RFC2822)
    );
    $result = $orgdb->replace('mh_org_tree', $orgarray, $format);

	if ($result) {
		fwrite(STDOUT, "  Successfully updated $organization[name]\n");
	} else {
		fwrite(STDOUT, "  Problem updating $organization[name]\n");
	}	
}

 
/****************************************************************************************************
 * Threshold Details
 ***************************************************************************************************/

function updateThresholdDetails($orgid, $people, $label) {
    global $orgdb;
    $format = array(
        '%d', //id
        '%d', //org_id
        '%d', //threshold_id
        '%s', //first_name
        '%s', //last_name
        '%s' //image_url
    );
    if ($people != NULL) {
        foreach($people as $person) {
            $personarray = array(
                'id' => $person['id'],
                'org_id' => $orgid,
                'threshold_id' => $label,
                'first_name' => $person['first_name'],
                'last_name' => $person['last_name'],
                'image_url' => $person['picture']
            );
            
            $result = $orgdb->replace('mh_threshold_details', $personarray, $format);
            if ($result) {
                fwrite(STDOUT, "  Successfully updated $person[first_name] $person[last_name]\n");
            } else {
                fwrite(STDOUT, "  Error updating person " . $person['id'] . "\n    " . $orgid . "\n    " . $label . "\n    " . $person['first_name'] . "\n    " . $person['last_name'] . "\n    " . $person['picture'] . "\n    " . $orgdb->error->get_error_message() . "\n");
            }
        }
    }
}

/****************************************************************************************************
 * Interaction Details
 ***************************************************************************************************/

function updateInteractionDetails($orgid, $person) {
    global $orgdb;
    $format = array(
        '%d', //receiver_id
        '%s', //receiver_name
        '%s', //initiatior_names
        '%d', //org_id
        '%s', //date
        '%s' //story
    );
    
    foreach($person['interactions'] as $i) {
        $interaction = updateInteractionHelper($i);
        $initiatornames = "";
        if ($interaction != NULL && $person['id'] == $interaction['receiver_id']) {
            $people = getIndexOfEndpoint('people', '', $orgid);
            foreach($people['people'] as $p) {
                if (in_array($p['id'], $interaction['initiator_ids'])) {
                    $initiatornames = $initiatornames . $p['first_name'] . " " . $p['last_name']. ", ";
                }
            }
            $initiatornames = rtrim($initiatornames, ', ');            
            $interactionarray = array(
                'receiver_id' => $interaction['receiver_id'],
                'receiver_name' => $person['first_name'] . " " . $person['last_name'],
                'initiator_names' => $initiatornames,
                'org_id' => $orgid,
                'date' => $interaction['timestamp'],
                'story' => $interaction['comment']
            );
            $result = $orgdb->replace('mh_interactions_details', $interactionarray, $format);
            if ($result) {
                fwrite(STDOUT, "  Successfully updated $interaction[receiver_id] interaction.\n");
            } else {
                fwrite(STDOUT, "  Error updating interation: " . $orgdb->error->get_error_message() . "\n");
            }
        }
    }    
}

/****************************************************************************************************
 * Originally this was going to be used as an argument for an array_map, but that didn't work.
 * It still helps readability though...
 ***************************************************************************************************/

function updateInteractionHelper($interaction) {
    if ($interaction['interaction_type_id'] == 4) {
        return $interaction;
    }
    return NULL;
}


?>