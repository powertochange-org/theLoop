<?php
/*
Template Name: Site_Home
*/
?>

<?php get_header(); ?>
<?php $cat_tab_1 = get_option('lp_tab_cat_1');  ?>      
<?php $cat_tab_2 = get_option('lp_tab_cat_2');  ?>      
<?php $cat_tab_3 = get_option('lp_tab_cat_3');  ?>      
<?php $post_number = get_option('lp_post_num');  ?>   

		<div id="content">			 
			<div id="content-left">
				<!-- slide-container -->
				<div class="slide-container">
					<div id="slider">
						<ul>
							<li class="img_right"><a href="<?php echo get_option('lp_slide_link_1'); ?>"><img src="<?php echo get_option('lp_slide_img_1'); ?>" alt="slide 1"/></a></li> 
							<li class="img_right"><a href="<?php echo get_option('lp_slide_link_2'); ?>"><img src="<?php echo get_option('lp_slide_img_2'); ?>" alt="slide 2"/></a></li> 
							<li class="img_right"><a href="<?php echo get_option('lp_slide_link_3'); ?>"><img src="<?php echo get_option('lp_slide_img_3'); ?>" alt="slide 3"/></a></li>
							<li class="img_right"><a href="<?php echo get_option('lp_slide_link_4'); ?>"><img src="<?php echo get_option('lp_slide_img_4'); ?>" alt="slide 4"/></a></li>   
						</ul>
					</div>
				</div>
				<?php if (  get_option('lp_latest_posts') != 'true' ) { ?>
					<ul class="tabs">
						<li><a href="#tab1"><?php echo $cat_tab_1 ?></a></li>
						<li><a href="#tab2"><?php echo $cat_tab_2 ?></a></li>
						<li><a href="#tab3"><?php echo $cat_tab_3 ?></a></li>
					</ul> 					
					<!-- tab-container -->
					<div class="tab_container">
						<!-- tab-content begin -->
						<div id="tab1" class="tab_content">
							<?php query_posts("category_name=$cat_tab_1&showposts=4"); ?>
							<?php while (have_posts()) : the_post(); ?>
							<ul class="tab-post">
								<li>
									<div class="date left"><?php the_time('M j'); ?></div>
									<h4 class="post-title event-post left"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h4>
									<div class="clear"></div>
								</li>
							</ul>
							<?php endwhile;?>
						</div>
						<div id="tab2" class="tab_content">
							<?php query_posts("category_name=$cat_tab_2&showposts=4"); ?>
							<?php while (have_posts()) : the_post(); ?>
							<ul class="tab-post">
								<li>
									<div class="date left"><?php the_time('M j'); ?></div>
									<h4 class="post-title event-post left"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h4>
									<div class="clear"></div>
								</li>
							</ul>
							<?php endwhile;?>
						</div>
						<div id="tab3" class="tab_content">
							<?php query_posts("category_name=$cat_tab_3&showposts=4"); ?>
							<?php while (have_posts()) : the_post(); ?>
							<ul class="tab-post">
								<li>
									<div class="date left"><?php the_time('M j'); ?></div>
									<h4 class="post-title event-post left"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h4>
									<div class="clear"></div>
								</li>
							</ul>
							<?php endwhile;?>
						</div>
						<!-- tab-content end --> 
					</div>
				<?php } else { ?>
					<div id="main-content">
					
						<?php
						// Jason B 2013-11-29: add filtering so that posts only intended for P2C Students don't show up
						// 		on the home page for people not in the P2C Students ministry
						
						// First, find out what ministry they are part of
						$results = $wpdb->get_results($wpdb->prepare("SELECT ministry FROM employee WHERE user_login = %s", $current_user->user_login));
						$result = $results[0];
						
						// If the ministry is not "Power to Change - Students", hide posts from that ministry on the home page
						if ($result->ministry == "Power to Change - Students") {
							// Show any posts
							query_posts("showposts=$post_number");
						} else {					
							// Get a list of all categories EXCEPT the one for P2C Students
							$categories = get_categories(array('orderby' => 'id', 'exclude' => '7')); //7 is the category ID for P2C Students
							// Build a comma-separated list that we can pass to the query_posts function
							$category_list = "";
							foreach ($categories as $category) {	
								if ($category_list != "") {
									$category_list .= ",";
								}
								$category_list .= $category->term_id;
							}
							// Query posts associated with any category in our list. Posts that are only
							// in the P2C Students category won't get selected.
							query_posts("showposts=$post_number&cat=$category_list");
						}
						?>
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
						<div class="post">
							<h2 class="line"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
							<?php the_post_thumbnail(); ?>
							<?php the_excerpt(); ?>
							<p class="meta"><?php the_time('F j, Y'); ?> in <?php the_category(', '); ?> by <?php the_author_posts_link() ?></p>
							<p class="meta"><?php comments_popup_link('No comments yet', '1 comment', '% comments', '', 'Comments are disabled for this post'); ?></p>	        
						</div>
						<!--/box-->   
						<?php endwhile; else: ?>
						<h2>404 - Not Found</h2>
						<p>The page you are looking for is not here.</p>					 
						<?php endif; ?>
					</div>
				<?php } ?>
            </div>
			<div id="content-right"><?php get_sidebar('home'); ?></div>
        </div>
        <!--content end-->
        <!--Popup window-->
		<?php include(TEMPLATEPATH.'/popup.php') ?>
    </div>
    <!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>		
<?php get_footer(); ?>
