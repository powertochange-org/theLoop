<?php
/****************************************************************************************************
 * missionhubapirequests.php - handles all connections to the missionhub API for missionhubstats.php
 * 
 * Author: Nathaniel Faries
 * Date created: May 25, 2015
 *
 ****************************************************************************************************/

/****************************************************************************************************
 * Constants and URL definitions.
 ***************************************************************************************************/

define('BASE_URL', 'https://www.missionhub.com/apis/v3/');
//Missionhub auth key is defined in wp-config.php as MISSIONHUB_AUTH_KEY


/****************************************************************************************************
 * GET requests
 *
 * Each function will return the specified requested object.  Depending on how often
 * specific resources are requested as the project is developed, specialized functions
 * for more and more specific resources may be added.
 *
 ***************************************************************************************************/

/****************************************************************************************************
 * getIndexOfEndpoints()
 *
 * This function returns the index of the specified endpoint.  All the arguments other than $endpoing
 * are given a default value so that this can be called with just the endpoint name if no other 
 * parameters are required. If any one of parameters are required for the request, all parameters
 * in the function before it must be specified as a null string in the function call.
 * 
 * For example, if I want to get an index of the endpoint 'people' and have no 'include' parameters,
 * but limit it to 10, I would have to call it like so
: *
 * getIndexOfEndpoint('people', '', '10'); 
 *
 * The rest can be left blank as the default value is provided.
 * See missionhub API documentation for more info about what these parameters do.
 *
 ***************************************************************************************************/

function getIndexOfEndpoint($endpoint, $include = '', $limit = '', $offset = '', $order = '', $filters = '', $include_archived = '') {
	
	$curl_address = BASE_URL . $endpoint . "?secret=" . MISSIONHUB_AUTH_KEY;
	if ($include != '') {
		$curl_address = $curl_address . "&include=" . $include;
	}
	if ($limit != '') {
		$curl_address = $curl_address . "&limit=" . $limit;
	}
	if ($offset != '') {
		$curl_address = $curl_address . "&offset=" . $offset;
	}
	if ($order != '') {
		$curl_address = $curl_address . "&order=" . $order;
	}
	//TODO: this is not actually how the filters argument works at all.  It will actually be an array of filters that each need to be individually added to the curl request
	if ($filters != '') {
		$curl_address = $curl_address . "&filters=" . $filters;
	}
	if ($include_archived != '') {
		$curl_address = $curl_address . "&include_archived=" . $include_archived;
	}

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

    return $decoded_response;
}

/****************************************************************************************************
 * showEndpoint()
 *
 * This function returns a specific instance of a given endpoint.  In order to find the endpoint an
 * ID must be supplied, which can be found using the index call above (getIndexOfEndpoint()) and
 * specifiying some identifiers to find the ID we need.  Honestly I'm not too sure how this will
 * work as it seems a little circular, but that's what is said on the missionhub API documentation.
 * We can ask Alexandra later if we have problems using it.  For now this isn't used so I'm not sure
 * what issues there will be.  The 'include' argument is optional and can be left blank when calling
 * this function. I.e.
 *
 * showEndpoint('surveys', '13951'); and showEndpoint('surveys', '13951', 'questions'); are both valid
 * calls.
 *
 * See missionhub API documentation for more information about what 'include' does.
 *
 ***************************************************************************************************/

function showEndpoint($endpoint, $id, $include = '') {
	$curl_address = BASE_URL . $endpoint . "/" . $id . "?secret=" . MISSIONHUB_AUTH_KEY;
	if ($include != '') {
		$curl_address = $curl_address . "&include=" . $include;
	}

	$curl = curl_init($curl_address);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
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

    return $decoded_response;
}

?>