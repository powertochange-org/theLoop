<?php
/*
*Template Name: Ministries
*
*
*/
?>
<?php get_header(); ?>
<div id="content">
	<div id="main-content" class='ministries'>
	    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<h1 class="line"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
		<hr style='margin: 20px 0;'>
		<div class="post">
		    <?php the_post_thumbnail(); ?>
		    <?php the_content(); ?>
		</div>
		<!--/box-->   
	    <?php endwhile; else: ?>
		<h2>404 - Not Found</h2>
		<p>The page you are looking for is not here.</p>					 
	    <?php endif; ?>
	    <div id="page-nav">
		<?php next_posts_link('&laquo; Previous Entries') ?>
		<?php previous_posts_link('Next Entries &raquo;') ?>
	    </div>
	</div>
</div>
<!--content end-->
<!--Popup window-->
</div>
<!--main end-->
</div>
<!--wrapper end-->
<div style='clear:both;'></div>		
<?php get_footer(); ?>