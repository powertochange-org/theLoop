<?php

function produceSQLReport($sqlReportName, $employeeNumber, $reportMonth) {
	$WEB_SERVICE_URL = "http://hqiis/RunSQLReport/";

	// Create the request
	$request = curl_init($WEB_SERVICE_URL . $sqlReportName . ".aspx?APIToken=" . SQL_REPORT_API_TOKEN . "&EmployeeNumber=$employeeNumber&ReportMonth=$reportMonth");
	curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($request, CURLOPT_ENCODING,  '');
	curl_setopt($request, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	curl_setopt($request, CURLOPT_TIMEOUT, 10 * 60);
	curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($request, CURLOPT_POST, false);
	curl_setopt($request, CURLOPT_POSTFIELDS, null);
	curl_setopt($request, CURLOPT_HEADER , false);

	// Send the request
	$result = curl_exec($request);
	if (curl_errno($request)) {
		$error = "Error: " . curl_error($request);
		curl_close($request);
		echo $error;
		return $error;
		exit;
	}

	// Get the response data
	$response = curl_exec($request);

	curl_close($request);

	// Handle an error on the SQL side, embedded in the response we receive
	if(substr($response, 0, 5) == "ERROR"){
		return $response;
	}	

	echo $response;
}

?>