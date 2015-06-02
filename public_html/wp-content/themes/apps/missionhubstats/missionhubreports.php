<?php

/****************************************************************************************************
 * missionhubapireports.php - generic report object which will have specialized reports as subclasses
 * and generally will be responsible for populating data tables to pass to whatever we decide to use
 * for rendering
 * 
 * Author: Nathaniel Faries
 * Date created: May 27, 2015
 *
 ****************************************************************************************************/

class missionhubReport {

    require('missionhubapirequests.php');

/****************************************************************************************************
 * Properties will be private and exposed through specific methods just for security's sake
 ****************************************************************************************************/

	private $data; //2D array


	

	void __construct() {

	}

}

?>