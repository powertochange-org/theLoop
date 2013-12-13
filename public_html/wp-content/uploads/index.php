<?php

define('WP_USE_THEMES', false);

require('../../wp-blog-header.php' ); 

if (is_user_logged_in()) {
	if(is_file("../../$_SERVER[REQUEST_URI]")){
		
		header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
		
		$temp = explode(".", $_SERVER['REQUEST_URI']);
		$ext = strtolower(end($temp));
		header("Content-Length:".filesize ("../../$_SERVER[REQUEST_URI]"));
		if (in_array($ext, array('jpg' , 'png', 'jpeg', 'gif', 'tiff', 'bmp'))){
			//for pictures
			header("Content-Type: image/". pathinfo($_SERVER["REQUEST_URI"], PATHINFO_EXTENSION));
		}
		else{
			header("Content-Type: application/". pathinfo($_SERVER["REQUEST_URI"], PATHINFO_EXTENSION));
		}
		
		//to counter act the wp-minify plugin (ob_start(array($this, 'modify_buffer'));)
		ob_end_flush();
		readfile("../../$_SERVER[REQUEST_URI]");
		exit;
	}
	else{
		header($_SERVER["SERVER_PROTOCOL"]." 404 NOT FOUND");
	}
} else {
	header("Location: /wp-login.php?redirect_to=$_SERVER[REQUEST_URI]");
}
?>