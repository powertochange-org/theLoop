<?php
/*
*Template Name: Form_Item
*
*
*/

function changeNL($string){
	$out = "";
	for ($i = 0; $i < strlen($string); $i ++){
		if (ord ($string{$i}) == 10){
			$out.= "<BR>";
			if ($i + 1 < strlen($string) and ord ($string{$i + 1}) == 13){
				$i ++;
			}
		}
		else if (ord ($string{$i}) == 13){
			$out.= "<BR>";
			if ($i + 1 < strlen($string) and ord ($string{$i + 1}) == 10){
				$i ++;
			}
		}
		else {
			$out .= $string{$i};
		}
	}
	return $out;
}

?>
<?php get_header(); ?>
<div id="content">
	<div id="main-content" class='form'>
		<h1>Forms &amp; Information</h1>
		<hr>
	    <?php if (have_posts()) : while (have_posts()) : the_post(); 
		$parts = explode('/', get_page_uri(get_the_ID())); 
		$link = "";
		?>
		<table style='width:100%;margin:30px 0;'><tr style=''>
		<?php for ($i = 0; $i < count($parts); $i ++){
			$link .= "/$parts[$i]";
			if ($i < count($parts) - 2){ ?>
				<td class ='crumbs'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
				<td style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level_grey.png' width='22' height='37' /></td>
			<?php } else if ($i < count($parts) - 1){ ?>
				<td class ='crumbs'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
				<td style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level.png' width='22' height='37' /></td>
			<?php } else { ?>
				<td class ='crumbs' style='background-color:#f7941d; width:auto;'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
			<?php }
		 } ?>
		</tr></table>
		<div id="content-left">
			<div class="post">
				 <h2 style="font-size:20px;font-weight:bold;margin-bottom:20px;"><a style='color:#f7941d;' href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
				 <hr>
				 <BR>
				<?php the_post_thumbnail(); ?>
				<?php $parts = explode('<!-- links -->', changeNL(get_the_content()));
				echo $parts[0]; ?>
			</div>
			<!--/box-->   
			<?php endwhile; else: ?>
			<h2>404 - Not Found</h2>
			<p>The page you are looking for is not here.</p>					 
			<?php endif; ?>
		</div>
		<div id="content-right" class='download' style='width: 240px;'>
			<span style='font-weight: bold;color: #005e90;display:block;margin-bottom:25px;'>DOWNLOADS</span>
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