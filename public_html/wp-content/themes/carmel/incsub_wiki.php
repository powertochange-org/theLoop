<?php
global $blog_id, $wp_query, $wiki, $post, $current_user;
get_header( 'wiki' );
?>
<div id="primary" class="wiki-primary-event">
<div id="main-content" class="archive-page">
    <div id="content">
        <div class="padder">
            <div id="wiki-page-wrapper">
            
                <!--Navigation-->
                <?php include('wikimenu.php'); ?>
                
                
                <h1 class="entry-title"><?php the_title(); ?></h1>
                <hr>
                <?php if ( !post_password_required() ) { ?>
                <div class="incsub_wiki incsub_wiki_single">
                    <div class="incsub_wiki_tabs incsub_wiki_tabs_top"><?php echo $wiki->tabs(); ?><div class="incsub_wiki_clear"></div></div>
                </div>
                <?php
                $revision_id = isset($_REQUEST['revision'])?absint($_REQUEST['revision']):0;
                $left        = isset($_REQUEST['left'])?absint($_REQUEST['left']):0;
                $right       = isset($_REQUEST['right'])?absint($_REQUEST['right']):0;
                $action      = isset($_REQUEST['action'])?$_REQUEST['action']:'view';

                if ($action == 'discussion') {
                   comments_template( '', true );
                } else {
                    echo $wiki->decider(wpautop($post->post_content), $action, $revision_id, $left, $right, false);
                }
                ?>
		<?php } ?>
            </div>
        </div>
    </div>
</div>
</div>

<?php //get_sidebar('wiki'); ?>

<?php get_footer('wiki'); ?>

<style type="text/css">
.single #primary {
	float: left;
	margin: 0 -26.4% 0 0;
}
.singular #content, .left-sidebar.singular #content {
	margin: 0 34% 0 7.6%;
    width: 58.4%;
}
</style>