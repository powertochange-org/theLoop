<?php
/*
*Template Name: Form_Item
*
*
*/

?>
<?php get_header(); ?>
<div id="content">
	<div id="main-content" class='form-item'>
		<?php if (have_posts()) : while (have_posts()) : the_post();  ?>
		<h1><?php the_title() ?></h1>
		<hr>
		<?php $parts = explode('/', get_page_uri(get_the_ID())); 
		$link = "";
		?>
		<table style='width:100%;margin:20px 0;border-collapse: collapse;'><tr style=''>
		<?php for ($i = 0; $i < count($parts); $i ++){
			$link .= "/$parts[$i]";
			if ($i < count($parts) - 2){
				if ($i % 3 == 0 and $i > 0) {?>
					<tr></table><table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr><td class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level_grey.png' width='22' height='37' /></td>
				<?php } ?>
				<td class ='crumbs'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
				<td class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level_grey.png' width='22' height='37' /></td>
			<?php } else if ($i < count($parts) - 1){
				if ($i % 3 == 0 and $i > 0) {?>
					</tr></table><table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr><td class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level_grey.png' width='22' height='37' /></td>
				<?php } ?>
				<td class ='crumbs'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
				<td class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level.png' width='22' height='37' /></td>
			<?php } else { 
				if ($i % 3 == 0 and $i > 0) {?>
					</tr></table><table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr><td class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level.png' width='22' height='37' /></td>
				<?php } ?>
				<td class ='crumbs' style='background-color:#f7941d; width:auto;'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
			<?php }
		} ?>
		</tr></table>
		<div id="content-left">
			<div class="post">
				<?php the_post_thumbnail();
				$content = get_the_content();
				$content = apply_filters('the_content', $content);
				$content = str_replace(']]>', ']]&gt;', $content);
				$parts = explode('<!-- links -->', $content);
				echo $parts[0]; ?>
			</div>
			<!--/box-->   
			<?php endwhile; else: ?>
			<h2>404 - Not Found</h2>
			<p>The page you are looking for is not here.</p>					 
			<?php endif; ?>
		</div>
		<div id="content-right" class='download form-item'>
			<?php $parts = explode('<!-- links -->', get_the_content());
				echo $parts[1]; ?>
		</div><div style='clear:both;'></div>
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