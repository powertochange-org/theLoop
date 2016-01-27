<!--Controlls the sub-archive page that will list every post available to be viewed.-->
<?php get_header(); ?>
	<div id="content">
		<div id="content-left">
			<div id="main-content" class="archive-page">
			    <!--Navigation-->
                <?php include('wikimenu.php'); ?>
                    
				<h1>Knowledge Base Home Page</h1>
				<hr>
				
                <form role="search" method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" >
                    <div>
                        <!--<label class="screen-reader-text" for="s">< ?php _e('Search for:'); ?></label>-->
                        <div style="width:80%; float:left;">
                            <input type="text" class="fieldform" placeholder="Search the Knowledge Base for an article here..." 
                                value="<?php echo get_search_query(); ?>" name="s" id="s" />
                        </div>
                        <input type="hidden" value="incsub_wiki" name="post_type" id="post_type" />
                        <input type="hidden" value="1" name="wiki" id="wiki" />
                        <div style="width:15%; float:left;">
                            <input type="submit" class="fieldform" id="searchsubmit" value="<?php esc_attr_e('Search', 'wiki'); ?>" />
                        </div>
                    </div>
                </form>
                <div style="clear:both;margin-bottom:30px;"></div>
                <div id="homepage-categories" class="clearfix">
                    <h3>Knowledge Base Categories</h3>
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