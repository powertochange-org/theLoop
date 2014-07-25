<?php
// Get the blog header, which gives us access to wordpres functions
require('../../../wp-blog-header.php');
// All this does is update the user_meta with the last time they dealt with a survey
update_user_meta($_POST['user_id'], 'last_survey_date', $date = date('m/d/Y', time())); 
?>
