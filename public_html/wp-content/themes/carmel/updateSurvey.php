<?php
// Get the blog header, which gives us access to wordpress functions
require('../../../wp-blog-header.php');

// Check to make sure the user is logged in
if (is_user_logged_in()) {
    // Get the current user
    $current_user = wp_get_current_user();
    // All this does is update the user_meta with the last time they dealt with a survey
    update_user_meta($current_user->ID, 'last_survey_date', $date = date('m/d/Y', time())); 
}

?>
