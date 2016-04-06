<!--Controlls the sub-archive page that will list every post available to be viewed.-->
<?php get_header(); ?>
	<div id="content">
		<div id="content-left" class="wiki-fix">
			<div id="main-content" class="archive-page">
			    <!--Navigation-->
                <?php include('wikimenu.php'); ?>
                    
				<h1>Knowledge Base Home Page</h1>
				<hr>
				
                <form role="search" method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" >
                    <div>
                        <!--<label class="screen-reader-text" for="s">< ?php _e('Search for:'); ?></label>-->
                        <div class="wiki-search-input">
                            <input type="text" class="fieldform" placeholder="Search the Knowledge Base for an article here..." 
                                value="<?php echo get_search_query(); ?>" name="s" id="s" />
                        </div>
                        <input type="hidden" value="incsub_wiki" name="post_type" id="post_type" />
                        <input type="hidden" value="1" name="wiki" id="wiki" />
                        <div class="wiki-search-submit">
                            <input type="submit" class="fieldform" id="searchsubmit" value="<?php esc_attr_e('Search', 'wiki'); ?>" />
                        </div>
                    </div>
                </form>
                <div style="clear:both;margin-bottom:30px;"></div>
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
                    $wikiSectionCount = 0;
                    $parentwiki = get_option( 'parentwiki' , 0 );
                    foreach ($st_categories as $st_category) {
                        if($wikiSectionCount % 3 == 0) {
                            if($wikiSectionCount != 0) 
                                echo '</div><div class="wiki-section-group-gap" style="clear: both;"></div>';
                            echo '<div class="wiki-section-group">';
                        }
                        echo '<div class="wiki-section">';
                        echo '<h3> <a href="' . get_category_link($st_category->term_id) . '" title="' . sprintf(__('View all wikis in %s', 'framework'), $st_category->name) . '" ' . '>' . $st_category->name . '</a>';
                        echo '<span class="cat-count"> (' . $st_category->count . ' Articles)</span>';
                        echo '</h3>';
                        //$st_category->description //gets the description

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
                        $subCatsFound = 0;
                        if ($st_sub_categories) {
                            $subCatsFound = 1;
                            foreach ($st_sub_categories as $st_sub_category) {
                                ?>
                                <ul class="sub-categories">
                                    <li>
                                        <h4><?php
                                            echo '<a href="' . get_category_link($st_sub_category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'framework'), $st_sub_category->name) . '" ' . '>' . $st_sub_category->name . '</a>';
                                            echo '<span class="cat-count"> (' . $st_sub_category->count . ' Articles)</span>';
                                            ?>
                                        </h4>
                                        
                                        <?php //Get child wikis for the sub category
                                            $querystr = "
                                                        SELECT $wpdb->posts.* , pageviews
                                                        FROM $wpdb->posts
                                                        LEFT OUTER JOIN wp_popularpostsdata ON wp_popularpostsdata.postid = $wpdb->posts.ID
                                                        INNER JOIN wp_term_relationships ON (wp_posts.ID = wp_term_relationships.object_id)  
                                                        WHERE 1=1 
                                                            AND wp_term_relationships.term_taxonomy_id IN (".$st_sub_category->cat_ID.")
                                                            AND $wpdb->posts.post_type = 'incsub_wiki' ";
                                            if($parentwiki != 0)
                                                $querystr .= "AND post_parent = '$parentwiki' ";
                                            $querystr .= "ORDER BY pageviews DESC
                                                        LIMIT 2";

                                                     $childcats = $wpdb->get_results($querystr, OBJECT);
                                            echo '<ul class="wiki-list">';
                                            foreach($childcats as $childwiki) {
                                                echo '<li> <a class="wiki-section-link" href="'.get_permalink($childwiki->ID).'">'.$childwiki->post_title.'<span class="cat-count"> (' . $childwiki->pageviews . ' views)</span></a></li>';
                                            }
                                            echo '</ul>';?>
                                    </li>
                                </ul>
                            <?php
                            }
                        }
                        
                        $numPosts = 5;
                        if($subCatsFound)
                            $numPosts = 3;
                        //Display the wikis under the main category
                        $querystr = "
                            SELECT $wpdb->posts.* , pageviews
                            FROM $wpdb->posts
                            LEFT OUTER JOIN wp_popularpostsdata ON wp_popularpostsdata.postid = $wpdb->posts.ID
                            INNER JOIN wp_term_relationships ON (wp_posts.ID = wp_term_relationships.object_id)  
                            WHERE 1=1 
                                AND wp_term_relationships.term_taxonomy_id IN (".$st_category->cat_ID.")
                                AND $wpdb->posts.post_type = 'incsub_wiki' ";
                        if($parentwiki != 0)
                            $querystr .= "AND post_parent = '$parentwiki' ";
                        $querystr .= "ORDER BY pageviews DESC
                            LIMIT $numPosts";
                        
                         $childcats = $wpdb->get_results($querystr, OBJECT);


                        echo '<ul class="wiki-list">';
                        foreach($childcats as $childwiki) {
                            //Make sure it is not a sub category
                            $catCheck = get_the_terms($childwiki->ID, 'incsub_wiki_category');
                            if($catCheck[0]->term_id == $st_category->cat_ID)
                                echo '<li> <a class="wiki-section-link" href="'.get_permalink($childwiki->ID).'">'.$childwiki->post_title.'<span class="cat-count"> (' . $childwiki->pageviews . ' views)</span></a>
                                        </li>';
                        }
                        echo '</ul></div>';
                        $wikiSectionCount++;
                    } //End of the loop
                    echo '</div><div style="clear: both; margin-bottom:200px;"></div>';
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