<?php

require_once('HTTP/Request.php');  // PEAR Library for making HTTP Requests

function produceSQLReport($sqlReportName, $employeeNumber, $reportMonth) {
	$WEB_SERVICE_URL = "http://hebron/RunSQLReport/";

	// Create the request
	$request = new HTTP_Request($WEB_SERVICE_URL . $sqlReportName . ".aspx?APIToken=" . SQL_REPORT_API_TOKEN . "&EmployeeNumber=$employeeNumber&ReportMonth=$reportMonth");
	$request->setMethod(HTTP_REQUEST_METHOD_GET);

	// Send the request
	$result = $request->sendRequest();
	if (PEAR::isError($result)) {
		echo "Error: " . $result->getMessage();
		exit;
	}	

	echo $request->getResponseBody();
}

?>