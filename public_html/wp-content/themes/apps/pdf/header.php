<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>


    <head profile="http://gmpg.org/xfn/11">    
    
        <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>" charset="<?php bloginfo('charset'); ?>" />
        <title>
        	
            <?php 
            if(!$protectedPage){
            	wp_title('');
            }
            ?>
        </title>
        
        
        <?php $siteURL = get_bloginfo('url'); ?>
        <link href="<?php bloginfo('template_url'); ?>/corporate/style.css" rel="stylesheet" type="text/css" />
        <link href='http://fonts.googleapis.com/css?family=Roboto+Slab:700,300|Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
       
        
        
        <?php wp_head(); ?> 
         <script type='text/javascript'>
        jQuery(document).ready(function(){
        		
        });
        </script>
		 


    </head>  
    <body>
		<header>
			<div class='inner'>
				<a href='/'><img class='header-logo' src='/wp-content/themes/hybrid/corporate/images/header-logo.png' alt='Home' /></a>
				<ul class='menu'>
					<li id='about-us'>About us</li>
					<li id='get-involved'>Get Involved</li>
					<li id='our-ministries'>Our Ministries</li>
				</ul>
				
				<a class='button bookstore'  href='http://store.powertochange.org'>Bookstore</a>
				<a class='button donate' href='/donate/'>Donate</a>
				<a class='button blog' href='/blogs/org/'>Blog</a>
				<div style='margin:0 auto;width:200px;'>Ministiries dd</div>
			</div>
		</header>