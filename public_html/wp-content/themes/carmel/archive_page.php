<?php
/*
*Template Name: Archives Page
*
*
*/
?>
<?php get_header(); ?>
<div id="content">
	<div id="content-left">
		<div id="main-content">
			<?php wp_get_archives() ?>
			<br>
		</div>
	</div>
	<div id="content-right">
	<?php get_sidebar(); ?>
	</div><div style='clear:both;'></div>
</div>
<!--wrapper end-->
<div style='clear:both;'></div>
<?php get_footer(); ?>
