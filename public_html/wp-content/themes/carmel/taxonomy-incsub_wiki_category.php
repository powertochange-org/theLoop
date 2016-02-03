<!--Controlls the sub-archive page that will list every post available to be viewed.-->
<?php get_header(); ?>
    <div id="content">
        <div id="content-left" class="wiki-fix">
            <div id="main-content" class="archive-page">
            
            
                <!--Navigation-->
                <?php include('wikimenu.php'); ?>
                
                <?php
                    //Display the category title and description
                    the_archive_title( '<h1 class="page-title">', '</h1>' );
                    the_archive_description( '<div class="taxonomy-description">', '</div>' );
                ?>
                <hr>
                    
                <?php if (have_posts()) : ?>                
                    <?php /*if (is_category()) { ?>
                        <h1 class="replace">ARCHIVES</h1>
                        <?php } elseif (is_day()) { ?>
                        <h1 class="replace">ARCHIVE <?php the_time('F jS, Y'); ?></h1>
                        <?php } elseif (is_month()) { ?>
                        <h1 class="replace">ARCHIVE <?php the_time('F, Y'); ?></h1>
                        <?php } elseif (is_year()) { ?>
                        <h1 class="replace">ARCHIVE <?php the_time('Y'); ?></h1>
                    <?php } */?>
                    
                    
                    
                    <?php while (have_posts()) : the_post(); ?>     
                        <div class="post">
                            <h2 class="line"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                            <?php the_excerpt(); ?>
                            <p class="meta"><?php the_time('F j, Y'); ?></p>
                        </div>
                        <hr>
                        <!--/box-->    
                     <?php endwhile; ?>
                    <div id="page-nav">
                        <div style="float:left;">
                            <?php previous_posts_link('&laquo; Previous Results'); ?>
                        </div>
                        <div style="float:right;">
                            <?php next_posts_link('Next Results &raquo;');?>
                        </div>
                    </div>
                <?php endif; ?> 
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