<?php get_header(); ?>	
<?php $first = 0; global $post_ID; ?>
<?php if ( wptouch_have_posts() ) { while ( wptouch_have_posts() ) { ?>

<?php wptouch_the_post(); ?>
<?php $first++; ?>


<div class="<?php wptouch_post_classes(); ?> rounded-corners-8px">
	<h2><a href="<?php wptouch_the_permalink(); ?>"><?php wptouch_the_title(); ?></a></h2>
</div>
<div class="<?php wptouch_post_classes(); ?> rounded-corners-8px">
	<div class="<?php wptouch_content_classes(); ?> ">
		<?php $parts = explode('<table>', get_the_content());
			echo $parts[0].'<table>';
			$cells = explode('<td>', $parts[1]);
			for ($i = 1; $i < count($cells); $i += 2){
				$content = explode('</td>', $cells[$i]);
				echo '<tr><td style="border: solid 1px #000000;border-radius: 10px;padding: 10px;">'.$content[0].'</td></tr>';
			} ?>
			</table>
	</div>

</div><!-- .wptouch_posts_classes() -->

<?php } } ?>

<?php if ( wptouch_has_next_posts_link() ) { ?>
	<?php if ( !classic_is_ajax_enabled() ) { ?>	
		<div class="posts-nav post rounded-corners-8px">
			<div class="left"><?php previous_posts_link( __( "Back", "wptouch-pro" ) ) ?></div>
			<div class="right clearfix"><?php next_posts_link( __( "Next", "wptouch-pro" ) ) ?></div>
		</div>
	<?php } else { ?>
		<a class="load-more-link no-ajax" href="javascript:return false;" rel="<?php echo get_next_posts_page_link(); ?>">
			<?php _e( "Load More Entries&hellip;", "wptouch-pro" ); ?>
		</a>
	<?php } ?>
<?php } ?>
<?php get_footer(); ?>
