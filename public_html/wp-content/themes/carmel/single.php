<?php get_header(); ?>
	<div id="content">
		<div id="content-left">
			<div id="main-content">	
				<div class="post" id="post-<?php the_ID(); ?>">
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
						<?php $is_pray = in_category( 'prayer-requests' ) ?>
						<?php  if($is_pray) {} else { ?>  
							<div style='margin-right:20px;width:40px;padding-bottom:3px;background-color:#f7941d;display:inline;float:left;text-align:center;padding-top:5px;'>
								<span style='color:#ffffff;'><?php echo strtoupper(get_the_time('M')) ?></span><BR>
								<span style='color:#ffffff;font-size:20px;'><strong style='color:#ffffff;'><?php the_time('d') ?></strong></span>
							</div>
						<?php } ?>
						<h1 ><a href="<?php echo get_permalink() ?>" rel="bookmark"><?php  if($is_pray) {echo 'Prayer Reqeust'; } else { the_title();} ?></a></h1>
						<hr>
						<?php if ($is_pray){ ?>
						If you would like us to share your ministry’s praise items and prayer requests in Chapel or on The LOOP, please send them to <a href="mailto:prayersupport@powertochange.org">prayersupport@powertochange.org</a>.
						<hr>
						<div style='width:40px;height:35px;background-color:#f7941d;display:inline;float:left;text-align:center;padding-top:5px;padding-bottom: 5px;'>
							<span style='color:#ffffff;'><?php echo strtoupper(get_the_time('M')) ?></span><BR>
							<span style='color:#ffffff;font-size:20px;'><strong style='color:#ffffff;'><?php the_time('d') ?></strong></span>
						</div>
						<?php } ?>
						<?php the_post_thumbnail('single-post-thumbnail'); ?>
						<div class='indent'>
							<?php the_content('<p>Read the rest of this entry &raquo;</p>'); ?>
							<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
						</div>
						<div class="clear"></div>				
						<div >
						<hr>
						<div class='indent'>
						<p class="meta">CATEGORIES: <?php strtoupper(the_category(' | ')) ?> <BR> Posted on <?php the_time('F j, Y'); ?></p>
						</p>
						</div>
						</div>
						<hr>
						<div class='indent'>
							<?php comments_template();?>
						</div>
					<?php endwhile; else: ?>
						<h1 class="replace">Error 404 - Not Found</h1>
						<p><strong>We're sorry, but that page doesn't exist or has been moved.</strong><br />
						Please make sure you have the right URL.</p>
					<?php endif; ?>
					</div>
			</div><!-- end main content -->
		</div><!-- end content-left -->
                
		<div id="content-right">
		<?php if ($is_pray){ ?>
			<!-- a class='orange_button' style='width:280px;margin-bottom:18px;float:right' href='/'><center style='color:#ffffff;'>PRINTER-FRIENDLY VERSION</center></a -->
			<a class='orange_button' style='width:280px;margin-bottom:18px;float:right;' href="mailto:prayersupport@powertochange.org"><center style='color:#ffffff;'>SUBMIT A PRAYER REQUEST</center></a>
		<?php } ?>
		<?php get_sidebar(); ?>
		</div><div style='clear:both;'></div>
	</div>
	<!--content end-->
	<!--Popup window-->
    </div>
    <!--main end-->
</div>
<!--wrapper end-->
<div style='clear:both;'></div>	
<?php get_footer(); ?>