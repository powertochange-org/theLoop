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

function loop_sub_navigation_shortcode($atts) {

    // override default attributes with user attributes
    $subnav_atts = shortcode_atts([
        "menu_items" => 'Please add your menu items',
        "menu_links" => '/',
        "text_color" => '#fff',
        "background_color" => "#0079c1"
    ], $atts);

	// extract(shortcode_atts(array(
	// 	"menuItems" => 'Please add your menu items',
    //     "menuLinks" => '/',
    //     "textColor" => '#fff',
    //     "backgroundColor" => "#0079c1"
    // ), $atts));

    $output = '';
    $output .= '
        <style>
            .loopSubNavigation {
                width: 100%;
                display: block;
                background: ' . $subnav_atts['background_color'] . ';
                border-radius: 20px;
                padding: 10px;
            }
            .loopSubNavigation ul {
                list-style-type: none;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-around;
                padding: 0 !important;
            }
            .loopSubNavigation ul li {
                white-space: nowrap;
            }
            .loopSubNavigation ul li a {
                color: ' . $subnav_atts['text_color'] . ';
            }
            h2 {
                padding: 5px 0 20px;
            }
        </style>
    ';

    $output .= '
        <div class="loopSubNavigation">
            <ul>
        ';

    $itemsArray=explode(",",$subnav_atts['menu_items']);
    $linksArray=explode(",",$subnav_atts['menu_links']);

    foreach($itemsArray as $index=>$value) {
        // do something with $value and $linksArray[$index] which will correspond
        $output .= '<li><a href="' . $linksArray[$index] . '">' . $value . '</a></li>';
    }

    $output .= '</ul></div>';
    
    return $output;
}
add_shortcode('subnav', 'loop_sub_navigation_shortcode'); 
?>