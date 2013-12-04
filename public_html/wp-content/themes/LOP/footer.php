<div id="footer-wrapper">
    <div id="footer">
        <div class="left-col left">

			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Footer")) : ?>
            <div class="footer-col left">
				<h6>Recent Posts</h6>
				<ul>
				<?php wp_get_archives('type=postbypost&limit=3'); ?>
				</ul>
                <h3>RSS</h3>
				<ul>
                    <li><a href="<?php bloginfo('rss2_url'); ?>">RSS Feed</a></li>
            		<li><a href="http://whatisrss.com">What is RSS?</a></li>
                </ul>
            </div>
			<div class="footer-col left">
				<h6>Categories</h6>
				<ul>
				<?php wp_list_categories('depth=1'); ?> 
				</ul>
            </div>
			<div class="footer-col left">
                <h6>Resources</h6>
				<ul>
                    <?php wp_list_bookmarks('title_li=&categorize=0'); ?>
                </ul>
            </div>
			<?php endif; ?>
        </div>
        <div class="right-col left">
			<a href="<?php echo get_option('home'); ?>" title="Home"><img src="<?php if (get_option('lp_logo_footer')) : echo get_option('lp_logo_footer'); else: bloginfo('stylesheet_directory');?>/img/footer-logo.png<?php endif; ?>" alt="Home" /></a>	
			<address>
			<?php echo get_option('lp_footer_text'); ?>
			</address>
        </div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript"> Cufon.now(); </script>
<?php echo stripslashes(get_option('lp_tracking_code'))?>
<?php wp_footer(); ?>
</body>
</html>
