<?php 
/* Template Name: Subscribe To Comments */
if (isset($wp_subscribe_reloaded)){ global $posts; $posts = $wp_subscribe_reloaded->stcr->subscribe_reloaded_manage(); } 

get_header(); ?>

<div id="content">
	<div id="content-left" style="width:100%;">
		<div id="main-content">	
		<?php
		if($posts[0] != NULL)
			echo $posts[0]->post_content;
			echo '<i>**Please be aware that currently the option "Replies to my comments" will not send emails for any replies. If you want to receive emails for replies, make sure to select "All Comments".</i>';
		?>

	<!-- .site-main -->

	<?php //get_sidebar( 'content-bottom' ); ?>

</div><!-- .content-area -->
</div></div>
<div style="clear:both;"></div> 
<?php //get_sidebar(); ?>
<?php get_footer(); ?>
