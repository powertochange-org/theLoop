<?php
/*
 * Template Name: zApp Test Ajax
 * Description: Simple test of ajax calls
 * Author: Jason Brink
 */
?>
<?php get_header(); ?>
	<div id="content">
		<div id="main-content">	
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div id="post-<?php the_ID(); ?>" class="post">
				<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
				<div class="entry">
					<?php 
					/*** Include page content for instructions, help text, etc ***/
					the_content(); 
					?>
					
					<?php
					/*** Page Template custom code begins here ***/
					?>
					<div id="content-left">
						Here is the left content that will get loaded by Ajax
					</div>
					<div id="content-right" class="download form-item">
						<?php /* Button click events are wired up in test-ajax.js */ ?>
						<a href="#" id="button1">Button 1</a>
						<a href="#" id="button2">Button 2</a>
					</div>
					<?php
					
					/*** Page Template custom code ends here ***/
					
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