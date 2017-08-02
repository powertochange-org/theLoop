<?php
/*
* Checks which submissions need to have a reminder email sent out. 
* Requires the $dateCheck variable to be set with a date when called.
* 
* author: gerald.becker
*
*/

$sql = "SELECT  SUBMISSIONID, 
                SEND_REMINDER
        FROM workflowformstatus
        WHERE SEND_REMINDER <= '".$dateCheck->format('Y-m-d H:i:s')."' 
            AND (STATUS = '4' OR STATUS = '3')
            AND SEND_REMINDER IS NOT NULL 
            AND HR_VOID = '0'";

$result = $wpdb->get_results($sql, ARRAY_A);

foreach($result as $row) {
    Workflow::sendEmail($row['SUBMISSIONID']);
}

?>
