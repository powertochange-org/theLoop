<?php get_header(); ?>
	<div id="content">
		<div id="content-left">
			<div id="header-img">
			</div>
			<div id="main-content" class="search-results">	
				<h1 class="replace">SEARCH RESULTS: <?php printf(__('\'%s\''), $s) ?></h1>
				<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>		
				<div class="post">
					<h3 class="line"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
					<?php the_excerpt(); ?>
					<p class="meta">Posted in <?php the_time('F j, Y'); ?></p>
				</div>
				<?php endwhile; else: ?>
				<p>Nothing found</p>					 
				<?php endif; ?>
			</div>
		</div>
                
		<div id="content-right" class="mobile-off">
		<?php get_sidebar(); ?>
		</div><div style='clear:both;'></div>
	</div>
	<!--content end-->
        <!--Popup window-->
    </div>
    <!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>		
<?php get_footer(); ?>