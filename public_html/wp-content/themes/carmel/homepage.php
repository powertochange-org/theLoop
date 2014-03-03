<?php
/*
*Template Name: HomePage
*
*/
 get_header(); ?>
<div id="content">
	<div style='position:relative; top:-43px;'>
		<a id='slideshow_link' target="_blank"><img id='slideshow' width='976' height='400' style='border:solid 12px #d6d7d4;'></a>
		<div style='height:45px;width:976px;left:12px;top:367px;background-color:#000000;position:absolute;opacity:0.27;filter:alpha(opacity=27); '>
		<?php
			$pictures = array('/wp-content/uploads/slides/Fellowship_Web_Loop_Banner.png');
			$links = array('http://p2c.com/students/event-category/fellowship-dinner');
			foreach($pictures as $pic=>$x){
				?>
				<div id='div_pic_<?php echo $pic ?>'  onclick='showPicture(<?php echo $pic ?>);' style='display:inline-block;margin-top:16px;margin-right:12px;float:right;width:14px;height:14px;border-radius:7px;background-color:#ffffff'>
				</div>
				<?php
			}
		?>
		</div>
		<script type="text/javascript">
			
			window.setInterval(nextPic,10000);

			var select_pic = 0;
			
			function showPicture(picture){
				document.getElementById('div_pic_' + select_pic).style.backgroundColor = '#ffffff';
				select_pic = picture;
				document.getElementById('slideshow').src = pics_array[select_pic];
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
			foreach($pictures as &$pic){
				echo " '$pic',";
			}
			?> null);
			
			var links_array = new Array (<?php
			foreach($links as &$l){
				echo " '$l',";
			}
			?> null);
			
			//init slideshow
			showPicture(0);
		</script>
	</div>
    <div id="content-left">
	<div id="main-content">
		<table style='width:100%;'>
			<tr>
				<td style="border:0;width:50%;">
					<hr>
					<span class='heading'><img class="arrow" src='<?php bloginfo('template_url'); ?>/img/right-arrow.png' width=30  height=30>
						News &amp; Update</span><BR>
						<?php
							$results = $wpdb->get_results($wpdb->prepare("SELECT ministry FROM employee WHERE user_login = %s", $current_user->user_login));
							$result = $results[0];

							// If the ministry is not "Power to Change - Students", hide posts from that ministry on the home page
							if ($result->ministry == "Power to Change - Students") {
								// Show any posts
								$latest_cat_post = new WP_Query( array('posts_per_page' => 4));
							} else {					
								// Query posts associated with any category in our list. Posts that are only
								// in the P2C Students category won't get selected.
								$latest_cat_post = new WP_Query( array('posts_per_page' => 4, 'category__not_in' => array(7))); //7 is the category ID for P2C Students
							}
							
							if( $latest_cat_post->have_posts() ) : while( $latest_cat_post->have_posts() ) : $latest_cat_post->the_post();
							?>
							<BR>
							<h2 class="homepage"><a href='<?php echo get_permalink() ?>'><?php  echo strtoupper(the_title('', '', false)); ?></a></h2>
							<?php
							endwhile; endif; ?>
				</td>
				<td style='width:46px;border:0;'>
				</td>
				<td  style="border:0;width:50%;">
					<hr>
					<?php 
						$idObj = get_category_by_slug('leonards-updates'); 
						$id = $idObj->term_id;
						$latest_cat_post = new WP_Query( array('posts_per_page' => 1, 'category__in' => array($id)));
						if( $latest_cat_post->have_posts() ) : while( $latest_cat_post->have_posts() ) : $latest_cat_post->the_post();
						?>
							<a href='<?php echo get_permalink() ?>'><span class='heading'><img class="arrow" src='<?php bloginfo('template_url'); ?>/img/right-arrow.png' width=30  height=30>
							Leonard's Updates</span></a><BR>
							<BR>
							<h2 class="homepage"><?php  echo strtoupper(the_title('', '', false)); ?></h2>
							<BR>
							<span class="homepage"><?php the_excerpt(); ?></span>
							<?php
							endwhile; endif; ?>
				</td>
			</tr>
			<tr>
				<td colspan='3' style='height:46px;border:0;'>
				</td>
			</tr>
			<tr>
				
				<td  style="border:0;">
					<hr>
					<?php 
						$latest_cat_post = new WP_Query( 'p='.get_theme_mod('feature_post'));
						if( $latest_cat_post->have_posts() ) : while( $latest_cat_post->have_posts() ) : $latest_cat_post->the_post();
						?>
						<a href='<?php echo get_permalink() ?>'><span class='heading'><img class="arrow" src='<?php bloginfo('template_url'); ?>/img/right-arrow.png' width=30  height=30>
							Staff Stories</span></a><BR>
							<BR>
							<h2 class="homepage"><?php  echo strtoupper(the_title('', '', false)); ?></h2>
							<BR>
							<span class="homepage"><?php the_excerpt(); ?></span>
							<?php
							endwhile; endif; ?>
				</td>
				<td  style="border:0;"></td>
				<td  style="border:0;">
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
							endwhile; endif; ?><BR><BR>
						<a class='orange_button' href="mailto:prayersupport@powertochange.org"><center style='color:#ffffff;'>SUBMIT A PRAYER REQUEST</center></a>
				</td>
			</tr>
		</table>
	</div>
    </div>
    <div id="content-right">
		<a class='orange_button' href="/staff-directory/" style="width:280px; margin-bottom:20px; float:right;"><center style='color:#ffffff;'>Staff Directory</center></a>
		<a class='orange_button' href="/reports/" style="width:280px; margin-bottom:20px; float:right;"><center style='color:#ffffff;'>Reports</center></a>
		<div id="sidebar">
			<div class="sidebaritem">
				<h1>Search the Loop</h1><BR>
				<form method="get" id="sb_searchform" action="<?php bloginfo('home'); ?>/"><div class='search-box'>
					<input name="s" id="s" class='search-input' placeholder='Search' type='text' />
					<img onclick="document.getElementById('sb_searchform').submit();" class='search-img' src='<?php bloginfo('template_url'); ?>/img/search.png'>
				</div></form>
				<hr>
				<h1>Feature Update</h1><BR>
				<?php 
					$latest_cat_post = new WP_Query( 'p='.get_theme_mod('feature_update'));
					if( $latest_cat_post->have_posts() ) : while( $latest_cat_post->have_posts() ) : $latest_cat_post->the_post();
					echo "<a href='".get_permalink()."'><h2>".strtoupper(the_title('', '', false))."</h2></a>"; 
					endwhile; endif; ?>
				
				<hr>
				<h1>Upcoming Event</h1><BR>
				<?php 
					$latest_cat_post = new WP_Query( 'p='.get_theme_mod('upcoming_event'));
					if( $latest_cat_post->have_posts() ) : while( $latest_cat_post->have_posts() ) : $latest_cat_post->the_post();
					echo "<a href='".get_permalink()."'><h2>".strtoupper(the_title('', '', false))."</h2></a>"; 
					endwhile; endif; ?>
				
				<hr>
				<h1>Recent Comments</h1><BR>
				<?php 
				foreach(get_comments( array( 'number' => 5)) as $c){
					echo "<a href='".get_permalink($c->comment_post_ID)."/#comment-".$c->comment_ID."'><h2>$c->comment_author</h2>\n";
					echo  "<p>".get_the_title($c->comment_post_ID)."</p></a>\n";
				
				} ?>
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
<hr style='border-color:#d6d7d4'>	
<div class='middle'>
	<img class='logo' src='/wp-content/themes/carmel/img/footer-logo.png' /> <!-- change?-->
	<img class='cluster' src='/wp-content/themes/carmel/img/cluster.png'  usemap="#clustermap" />
	<map name="clustermap">
		<!-- todo change? -->
	  <area shape="rect" coords="0,0,115,50" href="/ministries/aia/" />
	  <area shape="rect" coords="115,0,250,50" href="/ministries/students/" />
	  <area shape="rect" coords="250,0,330,50" href="/ministries/gain/" />
	  <area shape="rect" coords="330,0,470,50" href="/ministries/fl/" />
	  <area shape="rect" coords="470,0,600,50" href="/ministries/tm/" />
	  <area shape="rect" coords="600,0,729,50" href="/ministries/li/" />
	  <area shape="rect" coords="0,50,125,95" href="/ministries/cs/" />
	  <area shape="rect" coords="125,50,225,95" href="/ministries/drime/" />
	  <area shape="rect" coords="225,50,310,95" href="/ministries/jfs/" />
	  <area shape="rect" coords="310,50,400,95" href="/ministries/tl/" />
	  <area shape="rect" coords="400,50,530,95" href="/ministries/icn/" />
	  <area shape="rect" coords="530,50,600,95" href="/ministries/ce/" />
	  <area shape="rect" coords="600,50,729,95" href="/ministries/btp/" />
	</map> 
</div>	
<?php get_footer(); ?>