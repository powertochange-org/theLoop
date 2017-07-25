<?php 

require_once(get_stylesheet_directory().'/functions/functions.php');
/*
*Template Name: zAppAdvMag
*
*Author: matthew.chell
*
*/


get_header(); ?>
<div id="content">
	<div id="main-content" class='form'>
	<?php if (have_posts()) : while (have_posts()) : the_post();  ?>
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
				<tr></table><table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr><td class ='crumbs' style='width:22px;'><img class="crumbs-image" src='<?php bloginfo('template_url'); ?>/img/forms_level_grey.png' width='22' height='37' /></td>
			<?php } ?>
			<td class ='crumbs'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
			<td class ='crumbs' style='width:22px;'><img class="crumbs-image" src='<?php bloginfo('template_url'); ?>/img/forms_level_grey.png' width='22' height='37' /></td>
		<?php } else if ($i < count($parts) - 1){
			if ($i % 3 == 0 and $i > 0) {?>
				</tr></table><table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr><td class ='crumbs' style='width:22px;'><img class="crumbs-image" src='<?php bloginfo('template_url'); ?>/img/forms_level_grey.png' width='22' height='37' /></td>
			<?php } ?>
			<td class ='crumbs'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
			<td  class ='crumbs' style='width:22px;'><img class="crumbs-image" src='<?php bloginfo('template_url'); ?>/img/forms_level.png' width='22' height='37' /></td>
		<?php } else { 
			if ($i % 3 == 0 and $i > 0) {?>
				</tr></table><table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr><td class ='crumbs' style='width:22px;'><img class="crumbs-image" src='<?php bloginfo('template_url'); ?>/img/forms_level.png' width='22' height='37' /></td>
			<?php } ?>
			<td class ='crumbs' style='background-color:#f7941d; width:auto;'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
		<?php }
	 } ?>
	</tr></table>
	
    <div id="content-left">
    	<div class="mobile-indent">
		<?php  echo needOptions(array(
			API::$option_prefix.'server',
			API::$option_prefix.'username',
			API::$option_prefix.'password',
		)) ?>
		<?php the_content(); ?>
		<link rel='stylesheet' type='text/css' media='screen'  href="<?php echo get_stylesheet_directory_uri(); ?>/advMag/style.css" />
		<script src="<?php echo get_stylesheet_directory_uri(); ?>/advMag/script.js?v=0"></script>
		<script>
			var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>'
			$(document).ready(function() {advMag.init()});
		</script>
		<div class='advMag'>
		<table>
			<thead><tr>
				<th class='lang'>Recipient</th>
				<th class='lang'>Print Copy</th>
				<th class='lang'>Digital Copy</th>
				<th><button class='lang' disabled='disabled'>Save All</button></th>
			</tr></thead>
			<tbody><tr><td colspan='5'>loading...</td></tr></tbody>
		</table>
		</div>
		
	</div></div></div>
    <div style='clear:both;'></div>
    <?php endwhile; endif; ?>
</div>
<!--content end-->
<!--Popup window-->
</div>
<!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>		
<?php get_footer(); ?>
