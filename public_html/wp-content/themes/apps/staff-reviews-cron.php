<?php
if (!defined('ABSPATH')) {
require_once(dirname( __FILE__ ) . '/../../../wp-load.php');
}
global $wpdb;


//Use this script execution to send out emails that require supervisor reminders
$date = new DateTime(date("Y-m-d"));
$newdate = new DateTime(date("Y-m-d"));
$newdate->add(new DateInterval('P7D'));

$sql = "SELECT staffreview.*, employee.first_name, employee.last_name, sup.first_name AS supfirst_name, sup.last_name AS suplast_name, wp_users.user_email 
        FROM staffreview 
        LEFT JOIN employee on staffreview.empid = employee.employee_number 
        LEFT JOIN employee sup on staffreview.supid = sup.employee_number 
        LEFT JOIN wp_users ON sup.user_login = wp_users.user_login 
        WHERE empsubmitdate IS NOT NULL 
            AND supsubmitdate IS NULL 
            AND (supreminder <= '".$date->format('Y-m-d')."' 
                    OR supreminder IS NULL)
            AND skipreminder = '0'";
$result = $wpdb->get_results($sql, ARRAY_A);

$template = '{SUPERVISOR_NAME}, <br><br>
    <p>{STAFF_NAME} has completed their Prepwork for the Staff {REVIEW_TYPE} {REVIEW_YEAR} discussion!</p>

    <p>You can now review their answers and complete your {REVIEW_TYPE} prepwork. After authorizing the script, you should see the staff member\'s answers.  In rare cases, the data may not load on the first try. If this happens, simply select "get staff {REVIEW_TYPE}" again and it should load.  </p>

    <p><a href="{SUP_LINK}" target="_blank">Complete your Prepwork.</a></p>

    <p>You can check the progress of all your staff on the <a href="https://staff.powertochange.org/forms-information/staff-objectives-and-development-cycle/" target="_blank">Staff Review Dashboard</a>. </p>';

foreach($result as $row) {
    $subId = $row['id'];
    
    $mail = array('to' => '');
    $mail['headers'][] =  'From: Staff Review <staffreview-no-reply@p2c.com>';
    $mail['headers'][] = 'Content-Type: text/html; charset=UTF-8';
    
    $mail['to'] = $row['user_email'];
    
    $mail['subject'] = 'Staff '.($row['reviewtype'] == 2 ? 'Debrief' : 'Review').' Prepwork Completed by '.$row['first_name'].' '.$row['last_name'];
    
    $tmpBody = str_replace('{SUPERVISOR_NAME}', $row['supfirst_name'].' '.$row['suplast_name'], $template);
    $tmpBody = str_replace('{STAFF_NAME}', $row['first_name'].' '.$row['last_name'], $tmpBody);
    $tmpBody = str_replace('{REVIEW_YEAR}', ($row['year'] != '' ? ($row['year']-1).'/'.$row['year'] : ''), $tmpBody);
    $tmpBody = str_replace('{REVIEW_TYPE}', ($row['reviewtype'] == 2 ? 'Debrief' : 'Review'), $tmpBody);
    $body = str_replace('{SUP_LINK}', $row['supdraftlink'], $tmpBody);
    
    $mail['message'] = $body;
    wp_mail($mail['to'], $mail['subject'], $mail['message'], $mail['headers']);
    
    //Update the submission reminder date
    $sql = "UPDATE staffreview 
            SET supreminder = '".$newdate->format('Y-m-d')."'
            WHERE id = '$subId'";
    $wpdb->query($sql, ARRAY_A);
}


//Send email to staff member that their review has been submitted
$sql = "SELECT staffreview.*, employee.first_name, employee.last_name, wp_users.user_email 
        FROM staffreview 
        LEFT JOIN employee on staffreview.empid = employee.employee_number
        LEFT JOIN wp_users ON employee.user_login = wp_users.user_login 
        WHERE empsubmitdate IS NOT NULL AND staffemailsent = '0'";
$result = $wpdb->get_results($sql, ARRAY_A);

$template = '<p style="font-size:30px;">{STAFF_NAME}, you have now completed your prep work for the Staff {REVIEW_TYPE} {REVIEW_YEAR} discussion!</p>

    <p style="font-size:30px;">It will be forwarded to your supervisor. Your supervisor will review your submission and then complete their own prep work for the {REVIEW_TYPE} conversation. Once completed your supervisor will be in touch with you to set a time for the {REVIEW_TYPE} discussion.</p>';

foreach($result as $row) {
    $subId = $row['id'];
    
    $mail = array('to' => '');
    $mail['headers'][] =  'From: Staff Review <staffreview-no-reply@p2c.com>';
    $mail['headers'][] = 'Content-Type: text/html; charset=UTF-8';
    
    $mail['to'] = $row['user_email'];
    
    $mail['subject'] = 'Staff '.($row['reviewtype'] == 2 ? 'Debrief' : 'Review').' Prepwork Completed';
    
    $tmpBody = str_replace('{STAFF_NAME}', $row['first_name'].' '.$row['last_name'], $template);
    $tmpBody = str_replace('{REVIEW_YEAR}', ($row['year'] != '' ? ($row['year']-1).'/'.$row['year'] : ''), $tmpBody);
    $body = str_replace('{REVIEW_TYPE}', ($row['reviewtype'] == 2 ? 'Debrief' : 'Review'), $tmpBody);
    
    $mail['message'] = $body;
    wp_mail($mail['to'], $mail['subject'], $mail['message'], $mail['headers']);
    
    //Update the submission reminder date
    $sql = "UPDATE staffreview 
            SET staffemailsent = '1'
            WHERE id = '$subId'";
    $wpdb->query($sql, ARRAY_A);
}

?>