<!--Controlls the sub-archive page that will list every post available to be viewed.-->
<?php get_header(); ?>
	<div id="content">
		<div id="content-left">
			<div id="main-content" class="archive-page">
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
				    <div style="float:left;">
				    	<?php previous_posts_link('&laquo; Newer Entries'); ?>
				    </div>
				    <div style="float:right;">
				    	<?php next_posts_link('Older Entries &raquo;');?>
				    </div>
				</div>
				<?php endif; ?>	
			</div>
			</div>
        	<div id="content-right" class="mobile-off">
        	<?php if (in_category('prayer-requests')){ ?>
				<a class='orange_button submit-request' href="mailto:prayersupport@powertochange.org"><center style='color:#ffffff;'>SUBMIT A PRAYER REQUEST</center></a>
			<?php } else if(in_category('staff-stories')) {?>
				<a class='orange_button submit-request' href="mailto:staffstories@p2c.com"><center style='color:#ffffff;'>SUBMIT A STAFF STORY</center></a>
			<?php } ?>
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