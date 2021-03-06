<?php
/*
*Template Name: HomePage
*
*/
 get_header(); ?>

<div id="staff-dashboard" style="margin-top: -37px;margin-bottom: 37px;">
<?php if(get_option( 'staffreview' , 0 )) { ?>
    <div class="container" style="border:2px solid #FF9336;margin-top:30px;padding:0;text-align: center;font-size:25px;">
        <a href="/forms-information/staff-objectives-and-development-cycle/" style="padding:30px;display:block;">P2C-Student Debrief Dashboard</a>
    </div>
    <style>
        #staff-dashboard a:hover {
            background-color: #008ADB;
            color: white !important;
        }
    </style>
<?php } ?>
</div>
<div id="content" class="slideshow-content">
	
    <div id="content-left">
		<div class='image-slider'>
		<?php 
            if(get_option( 'snowhomescreen' , 0 )) {
                include 'snow/snow.php';
            } else {
				$folder = '/wp-content/uploads/slides/';
				
				// Add as many images as have been set in the theme customizer
				$pictures = array();
				$links = array();
				
				if (get_theme_mod('image_select_1')) {
					$pictures[] = get_theme_mod('image_select_1');
					$links[] = get_theme_mod('image_url_1');
				}
				if (get_theme_mod('image_select_2')) {
					$pictures[] = get_theme_mod('image_select_2');
					$links[] = get_theme_mod('image_url_2');
				}
				if (get_theme_mod('image_select_3')) {
					$pictures[] = get_theme_mod('image_select_3');
					$links[] = get_theme_mod('image_url_3');
				}
				if (get_theme_mod('image_select_4')) {
					$pictures[] = get_theme_mod('image_select_4');
					$links[] = get_theme_mod('image_url_4');
				}
			?>
			<a id='slideshow_link'>
			<?php foreach($pictures as $pic=>$src){
				?>
				<img id='slideshow_<?php echo $pic ?>' class='slideshow-img' src='<?php echo $src ?>'>
				<?php
			}
			?>
			
			</a>
			<div class='slideshow-img-bottom-bar'>
			<?php 
				foreach($pictures as $pic=>$x){
					?>
					<div id='div_pic_<?php echo $pic ?>'  onclick='showPicture(<?php echo $pic ?>);' class='slideshow-img-tab'></div>
					<?php
				}
			?>
			</div>
			<script type="text/javascript">
				
				var mq = window.matchMedia( "(min-width: 768px)" );
				if (mq.matches === true) {
					window.setInterval(nextPic,6000);
				}

				var select_pic = 0;
				
				function showPicture(picture){
					document.getElementById('div_pic_' + select_pic).style.backgroundColor = '#ffffff';
					document.getElementById('slideshow_' + select_pic).style.display = 'none';
					select_pic = picture;
					document.getElementById('slideshow_' + select_pic).style.display = 'block';
					document.getElementById('slideshow_link').href = links_array[select_pic];
					document.getElementById('div_pic_' + select_pic).style.backgroundColor = '#fdbb30';
				}
				
				function nextPic(){
					//-2 because of the null at the end of array
					if (select_pic == pics_array.length - 2){
						showPicture(0);
					}
					else {
						showPicture(select_pic + 1);
					}
				}
	            
				var pics_array = new Array (<?php
				foreach($pictures as $pic){
					echo " '$pic',";
				}
				?> null);
				
				var links_array = new Array (<?php
				foreach($links as $l){
					echo " '$l',";
				}
				?> null);
				
				//init slideshow
				if(mq.matches === true) { 
					showPicture(0); 
				}
				
				//Fix the slide show margins
				if (document.documentMode) { // as of IE8
				    var x = document.getElementsByClassName("slideshow-img");
				    for(var i = 0; i < x.length; i++) {
				    	document.getElementById("slideshow_"+i).className += " IEfix_slideshow";
				    }
				}
				if((/Safari/.test(navigator.userAgent)) && !(/Chrome/.test(navigator.userAgent))) {
					var x = document.getElementsByClassName("slideshow-img");
				    for(var i = 0; i < x.length; i++) {
				    	document.getElementById("slideshow_"+i).className += " Safarifix_slideshow";
				    }
				}
					
				
			</script>
            
        <?php
		    }
		?>
			
		</div>
    </div>
    <div id="content-right" class="home-page">
		<div id="sidebar" class="homesidebar">
			<div class="sidebaritem">
				<!--<h1>Search the Loop</h1><BR>
				<form method="get" id="sb_searchform" action="< ?php bloginfo('home'); ?>/"><div class='search-box'>
					<input name="s" id="s" class='search-input' placeholder='Search' type='text' />
					<img onclick="document.getElementById('sb_searchform').submit();" class='search-img' src='< ?php bloginfo('template_url'); ?>/img/search.png'>
				</div></form>
				<hr>-->
				<h1>Featured Content</h1>
				<?php  
				for ($i = 1; $i <= 5; $i += 1) {
					if(get_theme_mod("title_url_$i") && get_theme_mod("feature_title_$i")) {
					?>
					<a href="<?php echo get_theme_mod("title_url_$i");?>" class="sbfeaturelinks"><?php echo get_theme_mod("feature_title_$i"); ?></a>
					<?php 
					}
				} ?>
				<hr>
				<h1>Popular Pages</h1>
				<?php
				    if (function_exists('wpp_get_mostpopular'))
				    wpp_get_mostpopular('pid="754, 8306, 8379, 8388, 8332, 8761, 7807, 8335, 8309, 5077, 4617, 8362"& 
				    	limit=3&range="daily"&stats_views=0');
				?>
				<hr>
                <div class='recent-comments'>
                    <h1 style="margin-bottom:5px">Recent Comments</h1>
                    <?php 
                    echo '<ul id="vertical-ticker">';
                    foreach(get_comments( array( 'number' => 10)) as $c){
                        echo '<li>';
                        echo "<div class='recent-comment'><a href='".get_permalink($c->comment_post_ID)."/#comment-".$c->comment_ID."'><h2>$c->comment_author</h2>\n";
                        echo  "<p>".get_the_title($c->comment_post_ID)."</p></a><span class='commentcontent'>".$c->comment_content."</span></div>\n";
                        echo '</li>';
                    } 
                    echo '</ul>';
                    ?> 
                </div>
                <?php /*<div id='staff-account-balance'>
                    <hr/>
                    <p>Just a sec...</p>
                    <input type="button" value="Quick Account Balance" onclick='$(this).css("visibility","hidden");' />
                    <script type='text/javascript'>
                        $.ajax({
                            type: "POST",
                            url: "/wp-content/themes/apps/financialreports/myBalance.php",
                            dataType: "json",
                            success: function(data, textStatus) {
                                if (textStatus=='nocontent') {
                                    $('#staff-account-balance').remove();
                                } else {
                                    $('#staff-account-balance').show();
                                    $('#staff-account-balance>p').html('Balance of '+data);
                                }
                            },
                            error: function(a,b,c) {
                                $('#staff-account-balance>p').html('Error fetching balance');
                            }
                        })
                    </script>
                </div>*/?>
                               
			</div>                        
		</div>
	</div><div style='clear:both;'></div>
	<div class="container"></div>	
		<div id="main-content">
			<div class="homepage-tiles">
				<hr>
				<span class='heading'><img class="arrow" src='<?php bloginfo('template_url'); ?>/img/right-arrow.png' width=30  height=30>
					News &amp; Updates</span><BR>
					<span class="newsUpdate">
					<?php
						$post_number = 6;
					
						$results = $wpdb->get_results($wpdb->prepare("SELECT ministry FROM employee WHERE user_login = %s", $current_user->user_login));
						$result = $results[0];
						
						$idObj = get_category_by_slug('p2cstudents'); 
						$id_students = $idObj->term_id;
						
						// Build a comma-separated list that we can pass to the query_posts function
						$category_list = get_user_meta($current_user->ID, 's2_subscribed')[0];
						
						// If the ministry is not "Power to Change - Students", hide posts from that ministry on the home page unless they are subscribed
						if ($result->ministry == "Power to Change - Students") {
							// Show any posts
							$latest_cat_post = new WP_Query( "showposts=$post_number&cat=$category_list,$id_students");
						} else {					
							// Query posts associated with any category in our list. Posts that are only
							// in the P2C Students category won't get selected.
							$latest_cat_post = new WP_Query("showposts=$post_number&cat=$category_list");
						}
						
						if( $latest_cat_post->have_posts() ) : while( $latest_cat_post->have_posts() ) : $latest_cat_post->the_post();
						?>
						<BR>
						<h2 class="homepage"><a href='<?php echo get_permalink() ?>'><?php  echo the_title('', '', false); ?></a></h2>
						<?php
						endwhile; endif; ?>
						<BR>
					</span>
					<a href='/forms-information/archives'>View Archived Posts</a>
			</div>
			<div class="homepage-tiles">
				<hr>
				<?php 
					$idObj = get_category_by_slug('presidents-updates'); 
					$id = $idObj->term_id;
					$latest_cat_post = new WP_Query( array('posts_per_page' => 1, 'category__in' => array($id)));
					if( $latest_cat_post->have_posts() ) : while( $latest_cat_post->have_posts() ) : $latest_cat_post->the_post();
					?>
					<a href='<?php echo get_permalink() ?>'><span class='heading'><img class="arrow" src='<?php bloginfo('template_url'); ?>/img/right-arrow.png' width=30  height=30>
						President's Updates</span></a><BR>
						<BR>
						<h2 class="homepage"><?php  echo strtoupper(the_title('', '', false)); ?></h2>
						<BR>
						<span class="homepage"><?php the_excerpt(); ?></span>
						<?php
						endwhile; endif; ?>
			</div>
			<div class="homepage-tiles-new-line"></div>
			<div  class="homepage-tiles">
				<hr>
                <span class='heading'><img class="arrow" src='<?php bloginfo('template_url'); ?>/img/right-arrow.png' width=30  height=30> Staff Apps</span></a><BR>
                <div id='staff-apps-quadrant'><br/>Loading...
                    <script type="text/javascript">
                        jQuery(document).ready(function(){
							copyButtons();
                        });
						
						function copyButtons(){
							if($('#staffAppsButton table').length){
								$('#staff-apps-quadrant').empty().append($('#staffAppsButton table').clone());
								return;
							}
							setTimeout(copyButtons, 100);
						}
                    </script>
                </div>
                <script type="text/javascript">
                    function trackHomePageAppsClick(Label) {
                        'use strict'; 
                        console.debug('tracking label: '+Label); 
                        if (typeof (_gaq) !== 'undefined') { 
                            _gaq.push(['_trackEvent', 'Staff Apps on Homepage', 'click', Label]); 
                        } else if (typeof (ga) !== 'undefined') { 
                            ga('send', 'event', 'Staff Apps on Homepage', 'click', Label);
                        }
                    }
                </script>
            </div>
            <div class="homepage-tiles">
    			<hr>
    			<?php 
				$idObj = get_category_by_slug('prayer-requests'); 
				$id = $idObj->term_id;
				$latest_cat_post = new WP_Query( array('posts_per_page' => 1, 'category__in' => array($id)));
				if( $latest_cat_post->have_posts() ) : while( $latest_cat_post->have_posts() ) : $latest_cat_post->the_post();
				?>
				<a href='<?php echo get_permalink() ?>'><span class='heading'><img class="arrow" src='<?php bloginfo('template_url'); ?>/img/right-arrow.png' width=30  height=30>
					Prayer Requests</span></a><BR>
				<BR>
				<h2 class="homepage"><?php  echo strtoupper(the_title('', '', false)); ?></h2>
				<BR>
				<span class="homepage"><?php the_excerpt(); ?></span>
					<?php
					endwhile; endif; ?>
				<a class='orange_button' href="mailto:prayersupport@powertochange.org"><center style='color:#ffffff;'>SUBMIT A PRAYER REQUEST</center></a>
			</div>
            <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
            <script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
            <script>var jQuery_min = $.noConflict(true);</script>
            <script type="text/javascript" src="<?php bloginfo('template_url');?>/js/jquery.totemticker.js"></script>
            <script type="text/javascript">
                jQuery_min(function(){
                    $('#vertical-ticker').totemticker({
                        row_height  :   '59px',
                        next        :   '#ticker-next',
                        previous    :   '#ticker-previous',
                        stop        :   '#stop',
                        start       :   '#start',
                        mousestop   :   true,
                        speed       :   500,
                    });
                });
            </script>
		</div>
	</div>
</div>
<!--content end-->
<!--Popup window-->
</div>
<!--main end-->
</div>
<!--wrapper end-->
<div style='clear:both;'></div>
<hr style='border-color:#d6d7d4'>	
<div class='middle'>
	<img class='logo' src='/wp-content/themes/carmel/img/footer-logo.png' /> <!-- change?-->	
	<!--<img class='cluster' src='/wp-content/themes/carmel/img/Brand_Cluster_2_Line_New_Footer.png'  usemap="#clustermap" />-->
	<div class="image-cluster">
		<a href="http://athletesinaction.com/"><img src="/wp-content/themes/carmel/img/Athletes-In-Action.png" alt=""></a>
		<a href="http://p2c.com/students"><img src="/wp-content/themes/carmel/img/P2C_Students.png" alt=""></a>
		<a href="http://globalaid.net/"><img src="/wp-content/themes/carmel/img/gain.png" alt=""></a>
		<a href="http://jesusfilmstrategy.com/"><img src="/wp-content/themes/carmel/img/Jesus_Film.png" alt=""></a>
		<a href="http://christianembassy.ca/"><img src="/wp-content/themes/carmel/img/Christian_Embassy.png" alt=""></a>
		<a href="http://www.familylifecanada.com/"><img src="/wp-content/themes/carmel/img/Family_Life.png" alt=""></a>
		<a href="http://www.connectingstreams.com/"><img src="/wp-content/themes/carmel/img/Connecting_Streams.png" alt=""></a>
		<a href="http://powertochange.com/drime/"><img src="/wp-content/themes/carmel/img/Drime.png" alt=""></a>
		<a href="http://whenetwork.ca/"><img src="/wp-content/themes/carmel/img/When_Network.png" alt=""></a>
		<a href="http://thelife.com/"><img src="/wp-content/themes/carmel/img/The_Life.png" alt=""></a>
		<a href="http://www.leaderimpact.com/"><img src="/wp-content/themes/carmel/img/Leader_Impact.png" alt=""></a>
	</div>
</div>	
<?php get_footer(); ?>

<?php 
$user_id = get_current_user_id();
/*	
	0 = Prompt User to Update
	1 = Updated (1 year until next warning)
	2 = Remind Later (7 days until next warning)
	3 = Never
*/
if(isset($_GET['rem'])) {
	if($_GET['rem'] == 'later') {
		update_user_meta($user_id, 'update_profile', array('2', date('Y-m-d', strtotime("+7 days"))));
	} else if($_GET['rem'] == 'never') {
		update_user_meta($user_id, 'update_profile', array('3', date('Y-m-d')));
	}
}

$update_profile_status = 0;
$user_profile_settings =  get_user_meta($user_id, 'update_profile'); 
if(!empty($user_profile_settings)) {
	$update_profile_status = $user_profile_settings[0][0];
}
//Last Update was a year ago or 1 Week has passed. Remind them again
if($update_profile_status == 1 || $update_profile_status == 2) { 
	if($user_profile_settings[0][1] <= date('Y-m-d'))
		$update_profile_status = 0;
}

if($update_profile_status == '0') { ?>
	<div class="update-profile">
		<div>
			<h3>Please help keep the P2C staff directory up-to-date</h3>
			<p>The staff directory is a tool that allows you to find contact information, job titles, and location for all staff in Power to Change. By updating your profile, you will help make this tool even more useful for other staff looking to connect with you.  Please take a few seconds to update your privacy settings and add any additional information.
			</p>
			<a href="/staff-directory/?page=myprofile" class="orange_button">Update Profile Now</a>
			<a href="/?rem=later" class="orange_button">Remind Me Later</a>
			<a href="/?rem=never" class="orange_button">Do Not Remind Me Again</a>
		</div>
	</div>
<?php } ?> 
