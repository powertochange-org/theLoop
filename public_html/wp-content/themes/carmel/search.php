<?php get_header(); ?>
	<div id="content">
		<div id="content-left" <?php if(isset($_GET['wiki'])) echo 'class="wiki-fix"';?>>
			<div id="header-img">
			</div>
			<div id="main-content" class="search-results">	
				<?php if(isset($_GET['wiki'])) { include('wikimenu.php'); }?>
				<h1 class="replace">SEARCH RESULTS: <?php printf(__('\'%s\''), $s) ?></h1>
				<?php if(!isset($_GET['wiki'])) { ?>
				<h5 style="float:left;margin-right:20px;">Filter By: </h5>
				<?php $s = str_replace(' ', '+', $s); ?>
				<a href="/?s=<?php echo $s;?>&amp;post_type=incsub_wiki"><img src="/wp-content/images/Self-Help-Wiki-Icon.png"></a>
				<a href="/?s=<?php echo $s;?>&amp;post_type=page"><img src="/wp-content/images/Pages-Icon.png"></a>
				<a href="/?s=<?php echo $s;?>&amp;post_type=post"><img src="/wp-content/images/Posts-Icon.png"></a>
				<?php } ?>
				<hr>
				<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>		
				<?php $postType = get_post_type();
				if(!isset($_GET['post_type']) || $_GET['post_type'] == $postType) { //Fix to prevent posts from populating with pages ?>
				<div class="post">
					<div style="width:90%;float:left;">
						<h3 class="line"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
						<?php the_excerpt(); ?>
						<p class="meta">Posted on <?php the_time('F j, Y'); ?></p>
					</div>
					<div style="width:10%;float:left;">
						<p><?php 
							
							if($postType == 'incsub_wiki') {
								echo '<img src="/wp-content/images/Self-Help-Wiki-Icon.png">';
							} else if($postType == 'page') {
								echo '<img src="/wp-content/images/Pages-Icon.png">';
							} else if($postType == 'post') {
								echo '<img src="/wp-content/images/Posts-Icon.png">';
							} else {
								echo $postType;
							}?>
						</p>
					</div>
				</div>
				<hr style="clear:both;">
				<?php } endwhile; ?>
				<div id="page-nav">
				    <div style="float:left;">
				    	<?php previous_posts_link('&laquo; Previous Results'); ?>
				    </div>
				    <div style="float:right;">
				    	<?php next_posts_link('Next Results &raquo;');?>
				    </div>
				</div>
				<?php else: ?>
				<p>Nothing found</p>					 
				<?php endif; ?>
			</div>
		</div>
                
		<div id="content-right" class="mobile-off">
		<?php if(!isset($_GET['wiki'])) get_sidebar(); ?>
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