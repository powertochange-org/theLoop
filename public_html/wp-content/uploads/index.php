<?php

define('WP_USE_THEMES', false);

require('../../wp-blog-header.php' ); 

if (is_user_logged_in()) {
	header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
	header("Content-Type: ". pathinfo($_GET['file'], PATHINFO_EXTENSION));
	
	//to counter act the wp-minify plugin (ob_start(array($this, 'modify_buffer'));)
	ob_end_flush();
	
	readfile("../../$_GET[file]");
} else {
	header("Location: /wp-login.php?redirect_to=$_GET[file]");
}
?>