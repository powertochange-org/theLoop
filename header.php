<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
    <head profile="http://gmpg.org/xfn/11">    
		<script src="http://code.jquery.com/jquery-latest.js"></script>
        <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>" charset="<?php bloginfo('charset'); ?>" />
        <title>The Loop | An Internal Communications Blog</title>
        <?php $siteURL = get_bloginfo('url'); ?>
        <link href="<?php bloginfo('template_url'); ?>/style.css" rel="stylesheet" type="text/css" />
        <link href='http://fonts.googleapis.com/css?family=Roboto+Slab:700,300|Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
        <?php wp_head(); ?> 
         <script type='text/javascript'>
      /* todo  jQuery(document).ready(function(){
        		
        });*/
        </script>
    </head>  
    <body>
		<header>
			<div class='inner'>
				<a href='localhost/development/dummy.html'><img class='header-logo' src='<?php bloginfo('template_url'); ?>/img/header-logo.png' alt='Power To Change' /></a>
				<a href='/'><img class='header-logo' src='<?php bloginfo('template_url'); ?>/img/loop-logo.png' alt='Home' /></a>
				
				<a class='button selfhelp'  href='http://http://localhost/development/dummy.html'>Self-Help Wiki</a>
				<a class='button helpdesk' href='http://http://localhost/development/dummy.html'>HelpDesk</a>
				<a class='button absence' href='http://http://localhost/development/dummy.html'>Absence Tracker</a>
			</div>
			<div class='menu_bg'>
				<?php wp_nav_menu( array( 'theme_location'=>'main', 'menu_class' => 'menu', 'depth' => 1)); ?>
			</div>
		</header>