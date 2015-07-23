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

//Every organization on missionhub has it's own client ID and authentication key.
//Naming convention is: [NAME_OF_ORGANIZATION]_[AUTH_KEY/CLIENT_ID];
//TODO; remove these....

define('POWER_TO_CHANGE_CLIENT_ID', '117');
define('POWER_TO_CHANGE_AUTH_KEY', '8ab5c9906a019e8b9e7f3943a0ca6013385bb961f645256c3144ebfd5d03f709');
define('MATTHEWS_TESTING_ORG_CLIENT_ID', '119');
define('MATTHEWS_TESTING_ORG_AUTH_KEY', '3d911aeb84d6c2766d3ea949a48e77fe3bffaa9746c3d1dd9ef8c7ce97178aa1');
define('HOBBES_TESTING_CLIENT_ID', '120');
define('HOBBES_TESTING_AUTH_KEY', '0997e11e2b63a0b3b30dfd11969217290b8ceeffa761ce7e595ea60bbfa25515');
define('ANTONS_TESTING_CLIENT_ID', '121');
define('ANTONS_TESTING_AUTH_KEY', '59435f8c2e8a3ad2d3a588557e1496abc2778bdafd3bc6843ac6f2040b3c4dac');

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
 * Parameters: (all are entered as strings)
 * $endpoint: The name of the desired endpoint.
 * $include: A comma separated list of all the related objects to nest in the response.
 * $limit: Limits the number of results returned. Use with $offset for pagination.
 * $offset: Specifies which row in the result set to start with.
 * $order: Specifies how to sort and return the results.  See MissionHub API for valid inputs for different endpoints.
 * $filters: Is an associative array. Specifies a filter for the results. Only valid for certain endpoints.
 *           See MissionHub API for details.
 * $include_archived: If true, the result set will include archived entries. The default is to not include these.
 *                    Only valid for certain endpoints. See MissionHub API for details.
 *
 ***************************************************************************************************/

function getIndexOfEndpoint($endpoint, $include = '', $organization_id = '', $limit = '', $offset = '', $order = '', $filters = '', $include_archived = '') {
	
	$curl_address = BASE_URL . $endpoint . "?secret=" . POWER_TO_CHANGE_AUTH_KEY;
	if ($filters != '') {
        $filters = filterProcessing($filters);
		$curl_address = $curl_address . $filters;
	}
	if ($include != '') {
        str_replace(',', '%2C', $include);
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
	if ($include_archived != '') {
		$curl_address = $curl_address . "&include_archived=" . $include_archived;
	}
    if ($organization_id != '') {
        $curl_address = $curl_address . "&organization_id=" . $organization_id;
    }
    
    //Diagnostic
//    echo "<br> curl address: " . $curl_address . "<br>";
	
    $curl = curl_init($curl_address);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  //Not entirely sure what this does, but without it there's a raw dump of data on the screen
	$curl_response = curl_exec($curl);

	if ($curl_response === false) {
       	$info = curl_getinfo($curl);
        //$error = curl_error($curl);
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
	$curl_address = BASE_URL . $endpoint . "/" . $id . "?secret=" . POWER_TO_CHANGE_AUTH_KEY;
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

/****************************************************************************************************
 * filterprocessing()
 *
 * This function takes the associative array that is the filters sent with a curl request and turns
 * it into an appropriate string to include in the actual curl request.
 *
 * Parameter:
 * $filter: Associative array of key/value pairs.  If multiple values are to be for one key, they must
 *          be comma separated under the same key.
 *
 ***************************************************************************************************/

function filterProcessing($filter) {
    $result = '';
    foreach($filter as $key => $value) {
        $result = $result . "&filters%5B".$key."%5D=".$value;
    }
    return $result;
}

?>