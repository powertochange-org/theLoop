<?php get_header(); ?>
	<div id="content">
		<div id="content-left">
			<div id="main-content">	
				<div class="post" id="post-<?php the_ID(); ?>">
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<h1 ><a href="<?php echo get_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
					<?php the_post_thumbnail('single-post-thumbnail'); ?>
					<?php the_content('<p>Read the rest of this entry &raquo;</p>'); ?>
					<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<div class="clear"></div>				
				<div >
				<div class="divider"></div>
				<p class="meta">Categories <?php the_category(', ') ?> | Tags: <?php the_tags(' ', ', ', ' '); ?> | Posted on <?php the_time('F j, Y'); ?></p>
				
				</p>
				</div>
				<?php comments_template(); ?>
				<?php endwhile; else: ?>
				<h1 class="replace">Error 404 - Not Found</h1>
				<p><strong>We're sorry, but that page doesn't exist or has been moved.</strong><br />
				Please make sure you have the right URL.</p>
				<?php endif; ?>
				</div>
			</div><!-- end main content -->
		</div><!-- end content-left -->
                
		<div id="content-right">
		<?php get_sidebar(); ?>
		</div>
	</div>
	<!--content end-->
	<!--Popup window-->
	<?php include(TEMPLATEPATH.'/popup.php') ?>
    </div>
    <!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>		
<?php get_footer(); ?>