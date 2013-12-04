<?php
// Require login for site
get_currentuserinfo();
global $user_ID;
if ($user_ID == '') { 
  header('Location: /wp-login.php'); exit(); 
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title> The Loop | An Internal Communications Blog</title>

<link rel="stylesheet" href="https://staff.powertochange.org/wp-content/themes/LOP/style.css" type="text/css" media="screen" />

<link rel="stylesheet" href="https://staff.powertochange.org/wp-content/themes/LOP/style-blue.css" type="text/css" media="screen" />

<link rel="stylesheet" href="https://staff.powertochange.org/wp-content/themes/LOP/superfish.css" type="text/css" media="screen" /> 

<link rel="alternate" type="application/rss+xml" title="The Loop RSS Feed" href="https://staff.powertochange.org/feed/" />

<link rel="pingback" href="https://staff.powertochange.org/xmlrpc.php" />

<!---->
<?php if ( is_page_template('staffdirectory.php') ) { //if we're in the staff addressbook, use these instead.
//echo get_bloginfo('template_url'); ?>	
	<link rel="stylesheet" href="https://staff.powertochange.org/wp-content/themes/LOP/staff-directory-style.css" type="text/css" media="screen" />
<?php } ?>
<!----> 


<?php wp_head(); ?>
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>-->
<script type="text/javascript" src="https://staff.powertochange.org/wp-content/themes/LOP/js/jquery.min.js"></script>
<!--PNGfix-->
<script type="text/javascript" src="https://staff.powertochange.org/wp-content/themes/LOP/js/jquery.pngFix.pack.js"></script>
<!--Superfish nav bar-->
<script type="text/javascript" src="https://staff.powertochange.org/wp-content/themes/LOP/js/hoverIntent.js"></script>
<script type="text/javascript" src="https://staff.powertochange.org/wp-content/themes/LOP/js/superfish.js"></script>
<!--Slide-->
<script type="text/javascript" src="https://staff.powertochange.org/wp-content/themes/LOP/js/easySlider1.7.js"></script>
<!--cufon-->
<script src="https://staff.powertochange.org/wp-content/themes/LOP/js/cufon-yui.js" type="text/javascript"></script>
<script src="https://staff.powertochange.org/wp-content/themes/LOP/js/Bebas_400.font.js" type="text/javascript"></script>
<!--popup window-->
<script src="https://staff.powertochange.org/wp-content/themes/LOP/js/popup.js" type="text/javascript"></script>
<!--init-->
<script src="https://staff.powertochange.org/wp-content/themes/LOP/js/init.js" type="text/javascript"></script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-17609569-4']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>
	<div id="wrapper">   
    	<div id="main">
        	<div id="header"> 
            	<div id="logo"><a href="<?php echo get_option('home'); ?>" title="Home"><img src="<?php if (get_option('lp_logo')) : echo get_option('lp_logo'); else: bloginfo('stylesheet_directory');?>/img/logo.png<?php endif; ?>" alt="Home" class="logo" /></a></div>
				<div>
				<?php if ( function_exists('wp_nav_menu') ) { wp_nav_menu( array( 'theme_location'=>'secondary', 'menu_id' => 'menu')); } ?>
				</div>
				<div id="nav">	
					<?php if ( function_exists('wp_nav_menu') ) { 
					 wp_nav_menu( array( 'theme_location'=>'msin', 'menu_class' => 'sf-menu',)); }
					else {?>
					<ul class="sf-menu">
						<?php wp_list_pages('title_li=&sort_column=menu_order&depth=3'); ?>
					</ul>
					<?php } ?>
					<div class="search">
						<?php include(TEMPLATEPATH . '/searchform.php' ); ?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			<!--header end-->
