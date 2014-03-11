<?php
/*
*Template Name: Forms
*
*
*/
?>
<?php get_header(); ?>
<div id="content">
	<div id="main-content" class='form'>
	    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<h1><?php the_title() ?></h1>
		<hr>
		<?php $parts = explode('/', get_page_uri(get_the_ID())); 
		$link = "";
		?>
		<table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr style=''>
		<?php for ($i = 0; $i < count($parts); $i ++){
			$link .= "/$parts[$i]";
			if ($i < count($parts) - 2){
				if ($i % 3 == 0 and $i > 0) {?>
					<tr></table><table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr><td class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level_grey.png' width='22' height='37' /></td>
				<? } ?>
				<td class ='crumbs'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
				<td class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level_grey.png' width='22' height='37' /></td>
			<?php } else if ($i < count($parts) - 1){
				if ($i % 3 == 0 and $i > 0) {?>
					</tr></table><table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr><td class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level_grey.png' width='22' height='37' /></td>
				<? } ?>
				<td class ='crumbs'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
				<td  class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level.png' width='22' height='37' /></td>
			<?php } else { 
				if ($i % 3 == 0 and $i > 0) {?>
					</tr></table><table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr><td class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level.png' width='22' height='37' /></td>
				<? } ?>
				<td class ='crumbs' style='background-color:#f7941d; width:auto;'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
			<?php }
		 } ?>
		</tr></table>
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