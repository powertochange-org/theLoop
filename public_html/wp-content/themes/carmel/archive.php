<?php get_header(); ?>
	<div id="content">
		<div id="content-left">
			<div id="main-content">
				<?php if (have_posts()) : ?>				
					<?php if (is_category()) { ?>
						<h1 class="replace">ARCHIVES</h1>
						<?php } elseif (is_day()) { ?>
						<h1 class="replace">ARCHIVE <?php the_time('F jS, Y'); ?></h1>
						<?php } elseif (is_month()) { ?>
						<h1 class="replace">ARCHIVE <?php the_time('F, Y'); ?></h1>
						<?php } elseif (is_year()) { ?>
						<h1 class="replace">ARCHIVE <?php the_time('Y'); ?></h1>
					<?php } ?>
					<hr>
					<?php while (have_posts()) : the_post(); ?>		
						<div class="post">
							<h2 class="line"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
							<?php the_excerpt(); ?>
							<p class="meta"><?php the_time('F j, Y'); ?> in <?php the_category(', '); ?> by <?php the_author_posts_link() ?></p>
							<p class="meta"><?php comments_popup_link('No comments yet', '1 comment', '% comments', '', 'Comments are disabled for this post'); ?></p>
						</div>
						<hr>
						<!--/box-->    
					 <?php endwhile; ?>
					<div id="page-nav">
					    <?php next_posts_link('&laquo; Previous Entries') ?>
					    <?php previous_posts_link('Next Entries &raquo;') ?>
					</div>
				<?php endif; ?>	
			</div>
			</div>
        	<div id="content-right" class="mobile-off">
			<?php get_sidebar(''); ?>
		</div></div><div style='clear:both;'></div>
        </div>
        <!--content end-->
        <!--Popup window-->
    </div>
    <!--main end-->
</div>
<!--wrapper end-->
<div style='clear:both;'></div>		
<?php get_footer(); ?>