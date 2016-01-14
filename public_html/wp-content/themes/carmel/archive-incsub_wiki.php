<!--Controlls the sub-archive page that will list every post available to be viewed.-->
<?php get_header(); ?>
	<div id="content">
		<div id="content-left">
			<div id="main-content" class="archive-page">
			    <!--Navigation-->
                <a href="/kb/">Knowledge Base Home</a>
                <a href="/kb/articles/?action=edit&eaction=create" style="margin-left: 20px;">Create New Knowledge Base Article</a>
                    
                    
				<h1>Wiki Home Page</h1>
				<hr>
				
				
				
				<div id="homepage-categories" class="clearfix">
                <?php
                // Get homepage options
                // Set category counter
                $st_cat_counter = 0;

                // Base Category Query
                $st_hp_cat_args = array(
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'hierarchical' => true,
                    'hide_empty' => 0,
                    'taxonomy' => 'incsub_wiki_category',
                    'pad_counts' => 1
                );

                $st_categories = get_categories($st_hp_cat_args);
                $st_categories = wp_list_filter($st_categories, array('parent' => 0));
                // If there are catgegories
                if ($st_categories) {
                    foreach ($st_categories as $st_category) {
                        echo '<h3> <a href="' . get_category_link($st_category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'framework'), $st_category->name) . '" ' . '>' . $st_category->name . '</a>';
                        //if (of_get_option('st_hp_cat_counts') == '1') {
                            echo '<span class="cat-count">(' . $st_category->count . ')</span>';
                        //}
                        echo '</h3>';

                        // Sub category
                        $st_sub_category = get_category($st_category);
                        $st_subcat_args = array(
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'child_of' => $st_sub_category->cat_ID,
                            'pad_counts' => 1,
                            'taxonomy' => 'incsub_wiki_category',
                        );
                        $st_sub_categories = get_categories($st_subcat_args);
                        $st_sub_categories = wp_list_filter($st_sub_categories, array('parent' => $st_sub_category->cat_ID));

                        // If there are sub categories show them
                        if ($st_sub_categories /*&& (of_get_option('st_hp_subcat') == 1)*/) {
                            foreach ($st_sub_categories as $st_sub_category) {
                                ?>
                                <ul class="sub-categories">
                                    <li>
                                        <h4><?php
                                            echo '<a href="' . get_category_link($st_sub_category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'framework'), $st_sub_category->name) . '" ' . '>' . $st_sub_category->name . '</a>';
                                            if (/*of_get_option('st_hp_subcat_counts') == '1'*/true) {
                                                echo '<span class="cat-count">(' . $st_sub_category->count . ')</span>';
                                            }
                                            ?></h4>
                                    </li>
                                </ul>
                            <?php
                            }
                        }
                        
                    }
                }
				?>
					
				</div>
					
					
					
					
					
<?php /*				
					
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
							<!--<p class="meta"><?php //the_time('F j, Y'); ?> in <?php the_category(', '); ?> by <?php the_author_posts_link() ?></p>-->
							<!--<p class="meta"><?php //comments_popup_link('No comments yet', '1 comment', '% comments', '', 'Comments are disabled for this post'); ?></p>-->
						</div>
						<hr>
						<!--/box-->    
					 <?php endwhile; ?>
					<div id="page-nav">
					    <?php next_posts_link('&laquo; Previous Entries') ?>
					    <?php previous_posts_link('Next Entries &raquo;') ?>
					</div>
				<?php endif; ?>	
    */?>
			</div>
			</div>
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