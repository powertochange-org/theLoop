<?php
/* Since Apps is a child theme of Carmel, this functions.php will get used in addition to the one in the carmel folder */
include_once('functions/upgrade.php');

/* Register Ajax handlers for P2C-S Stats Reporting */
include_once('p2cs-stats-reports/ajax-handler.php');

//Include the AJAX workflow file
include_once('workflow/workflowajax.php');

include_once('staffdirectory/givingpage-backend.php');

include_once('advMag/function.php');


add_filter( 'wp_mail', 'my_replace_mail' );
function my_replace_mail( $args ) {
    global $wpdb;
    $subject = $args['subject'];
    $var = strip_tags($args['headers']);
    $start = strpos($var, 'From: ');
    $end = strpos($var, 'Reply-To');
    $from = substr($var, ($start + 6), $end - ($start + 6));
    $guid = bin2hex(openssl_random_pseudo_bytes(16));
    $date_sent = date('Y-m-d H:i:s');
    $sql = "INSERT INTO email_open_tracking (trackingid, email_subject, status, date_sent, sender)
            VALUES ('$guid', '$subject', '0', '$date_sent', '$from')";
    $result = $wpdb->query($sql, ARRAY_A);
    
    //Append image to message body
    $imgLink = '<img src="https://'.$_SERVER['HTTP_HOST'].'/custom-pages/emailsurvey.php?uid='.$guid.'" alt="" width="1px" height="1px">';
    $args['message'] = str_replace('</body>', $imgLink.'</body>', $args['message']);
    return $args;
}

add_action('init', 'track_sessions', 1);
function track_sessions() { // if session isn't active, set it
    if(!session_id()) {
        session_start();
    }
}
?>