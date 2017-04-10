<?php /** The purpose of this file is to help with database changes */

/** Adding Department and Gender columns to employee table 
*	Date: April 10, 2017
*	Author: matthew.chell
**/

$wpdb->get_results("SHOW COLUMNS FROM `employee` = 'department'");
if(0 == $wpdb->num_rows) {
	$wpdb->get_results("ALTER TABLE `employee` ADD department varchar(100);");
	//loading dummy value.  Should be fixed after a sync
	$wpdb->get_results("UPDATE `employee` SET department = '';");
}

$wpdb->get_results("SHOW COLUMNS FROM `employee` = 'gender'");
if(0 == $wpdb->num_rows) {
	$wpdb->get_results("ALTER TABLE `employee` ADD gender varchar(1);");
	
	//loading dummy value.  Should be fixed after a sync
	$wpdb->get_results("UPDATE `employee` SET gender = case when RAND() < 0.5 then 'M' else 'F' end;");
}
