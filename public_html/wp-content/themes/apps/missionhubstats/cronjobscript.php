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
//    $admins = getIndexOfEndpoint('people', '', $organization['id'], '', '', '', array('roles' => 'admins'));
//    $users = getIndexOfEndpoint('people', '', $organization['id'], '', '', '', array('roles' => 'users'));
//    $total = getIndexOfEndpoint('people', 'interactions', $organization['id']);
//    foreach($total['people'] as $person) {
//        if ($person['interactions'] != NULL) {
//            updateInteractionDetails($organization['id'], $person);
//        }
//    }
//    $t1 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14121));
//    updateThresholdDetails($organization['id'], $t1['people'], 14121);
//    $t2 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14122));
//    updateThresholdDetails($organization['id'], $t2['people'], 14122);
//    $t3 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels '=> 14123));
//    updateThresholdDetails($organization['id'], $t3['people'], 14123);
//    $t4 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14124));
//    updateThresholdDetails($organization['id'], $t4['people'], 14124);
//    $t5 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14125));
//    updateThresholdDetails($organization['id'], $t5['people'], 14125);
//    $d1 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14126));
//    updateThresholdDetails($organization['id'], $d1['people'], 14126);
//    $d2 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14127));
//    updateThresholdDetails($organization['id'], $d2['people'], 14127);
//    $d3 = getIndexOfEndpoint('people', 'interactions,organizational_labels', $organization['id'], '', '', '', array('labels' => 14128));
//    updateThresholdDetails($organization['id'], $d3['people'], 14128);
//    $orgarray = array(
//        'id' => $organization['id'],
//        'parent_id' => (int) substr(strrchr($organization['ancestry'], '/'), 1),
//        'name' => $organization['name'],
//        't1' => sizeof($t1['people']),
//        't2' => sizeof($t2['people']),
//        't3' => sizeof($t3['people']),
//        't4' => sizeof($t4['people']),
//        't5' => sizeof($t5['people']),
//        'd1' => sizeof($d1['people']),
//        'd2' => sizeof($d2['people']),
//        'd3' => sizeof($d3['people']),
//        'exposures' => sizeof($total['people']) - (sizeof($admins['people']) + sizeof($users['people'])),
//        'admins' => sizeof($admins['people']),
//        'users' => sizeof($users['people'])
//    );
//    $result = $orgdb->replace('mh_org_tree', $orgarray, $format);
//	if ($result) {
//		echo "Successfully updated $organization[name]\r\n";
//	} else {
//		echo "Problem updating $organization[name]\r\n";
//	}
}

updateInteractionDetails(8411, showEndpoint('people', 251594, 'interactions'));

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
                echo "Successfully updated $person[first_name] $person[last_name]\r\n";
            } else {
                echo $person['id'] . "\n" . $orgid . "\n" . $label . "\n" . $person['first_name'] . "\n" . $person['last_name'] . "\n" . $person['picture'] . "\n" . $orgdb->error->get_error_message() . "\n";
            }
        }
    }
}

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
        if ($interaction != NULL) {
            $people = getIndexOfEndpoint('people', '', $orgid);
            foreach($people['people'] as $person) {
                if (in_array($person['id'], $interaction['initiator_ids'])) {
                    $initiatornames = $initiatornames . $person['first_name'] . " " . $person['last_name']. " ";
                }
            }
        }
        $interactionarray = array(
            'receiver_id' => $interaction['receiver_id'],
            'receiver_name' => $person['first_name'] . " " . $person['last_name'],
            'initiator_names' => $initiatornames,
            'org_id' => $orgid,
            'date' => $interaction['timestamp'],
            'story' => $interaction['comment']);
    }
    $result = $orgdb->replace('mh_interactions_details', $interactionarray, $format);
    if ($result) {
        echo "Successfully updated $interaction[receiver_id] interaction.\n";
    } else {
        echo $orgdb->error->get_error_message() . "\n";
    }
    
//    foreach($person['interactions'] as $interaction) {
//        if ($interaction['interaction_type_id'] == 4) {
//            //This section is ugly.  I don't recommend looking at it.
//            $initiatornames = "";
//            echo "\nReceiver is " . $person['first_name'];
//            $people = getIndexOfEndpoint('people', '', $orgid);
//            foreach($interaction['initiator_ids'] as $initiator) {
//                echo $initiator;
//                foreach($people['people'] as $person) {
//                    echo "\nDoes $initiator match $person[id]?\n";
//                    if ($person['id'] == $initiator) {
//                        $initiatornames = $initiatornames . $person['first_name'] . " " . $person['last_name'] . " ";
//                    }
//                }
//            }
//            //The ugly bit is over now...not that the rest is amazing but it's not as bad.
//            $interactionarray = array(
//                'receiver_id' => $interaction['receiver_id'],
//                'receiver_name' => $person['first_name'] . " " . $person['last_name'],
//                'initiator_names' => $initiatornames,
//                'org_id' => $interaction['organization_id'],
//                'date' => $interaction['timestamp'],
//                'story' => $interaction['comment']
//            );
//            $result = $orgdb->replace('mh_interactions_details', $interactionarray, $format);
//            if ($result) {
//                echo "Successfully updated $interaction[receiver_id] interaction.\n";
//            } else {
//                echo $orgdb->error->get_error_message() . "\n";
//            }
//        }
//    }
    
}

function updateInteractionHelper($interaction) {
    echo "\n $interaction[interaction_type_id]";
    if ($interaction['interaction_type_id'] == 4) {
        return $interaction;
    }
    return NULL;
}


?>