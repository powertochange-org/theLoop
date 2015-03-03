<?php
/*
 * Template Name: zApp Egencia Login
 * Description: Provide a page with some preamble text for users before they jump to the
 *				Egencia site for the first time. Give the users an option to bypass this
 *				page in the future, which would let them go straight to the Egencia site.
 * Author: Jason Brink
 */

// Define some constants
$EGENCIA_URL = 'http://p2c.egencia.ca';
$EGENCIA_USER_META_KEY = 'skip_to_egencia';

// Get information about the currently logged in user
$current_user = wp_get_current_user();

// Find out if the user has already seen this page and asked not to see it again
if (get_user_meta($current_user->id, $EGENCIA_USER_META_KEY, true) == 'true') {
	// If so, redirect
	wp_redirect($EGENCIA_URL);
	exit;
}

// Check if the user has clicked the "Proceed" button
if (isset($_POST['proceed_to_egencia']) || 
    isset($_POST['proceed_to_egencia_x'])  // An image button passes an x and y
	) {
	// Check if they clicked the button to not show the page again
	if (isset($_POST['dont_show_again'])) {
		update_user_meta($current_user->id, $EGENCIA_USER_META_KEY, 'true');
	}
	
	// Then, redirect onward
	wp_redirect($EGENCIA_URL);
	exit;
}


 
 get_header(); ?>
	<div id="content">
		<div id="main-content">	
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div id="post-<?php the_ID(); ?>" class="post">
				<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
				<div class="entry">
					<?php 
					/*** Include page content that was created by HR ***/
					the_content(); 


					/*** Code for the "don't show this again" checkbox, as well as the button goes here ***/
					var_dump($_POST);
					?>
					
					<form name="egencia_login" id="egencia_login" action="." method="POST">
						<p>
						<input type="checkbox" name="dont_show_again">
						<label for="dont_show_again">Don't show this page again</label><br />
						</p>
						
						<p>
						<input type="image" class="alignnone size-full wp-image-14785" name="proceed_to_egencia" src="https://staff.powertochange.org/wp-content/uploads/2015/03/button.png" alt="Proceed to Egencia" width="227" height="40" />
						</p>

					</form>
					
					<p>
					<a href="https://staff.powertochange.org">Return to the homepage</a>
					</p>
					
					<?php					
					/*** Code for Egencia login page ends here ***/					
					?>						
				</div>
				<div class="clear"></div>				
			</div>
			<?php endwhile; else : ?>
			<div class="post">
				<h2>404 - Not Found</h2>
				<p>The page you are looking for is not here.</p>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<!--content end-->
	<!--Popup window-->
</div>
<!--wrapper end-->
<div style='clear:both;'></div>	
<?php get_footer(); ?>