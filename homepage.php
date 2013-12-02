<?php
/*
*Template Name: HomePage
*
*/
 get_header(); ?>
<div id="content">
	<div style='position:relative; top:-46px;'>
		<img id='slideshow' width='976' height='400' style='border:solid 12px #d6d7d4;'>
		<div style='height:45px;width:976px;left:12px;top:367px;background-color:#000000;position:absolute;opacity:0.27;filter:alpha(opacity=27); '>
		<?php
			$pictures = array('/wp-content/uploads/house.png', '/wp-content/uploads/untitled.png', '/wp-content/uploads/two.png');
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
			
			//init slideshow
			showPicture(0);
		</script>
	</div>
    <div id="content-left">
	<div id="main-content">
		<table style='width:100%;'>
			<tr>
				<td>
					<hr>
					<span class='heading'><img src='<?php bloginfo('template_url'); ?>/img/right-arrow.png' width=30  height=30>
						Featured</span><BR>
						<?php 
							$latest_cat_post = new WP_Query( 'p='.get_theme_mod('feature_post'));
							if( $latest_cat_post->have_posts() ) : while( $latest_cat_post->have_posts() ) : $latest_cat_post->the_post();
							?>
							<BR>
							<h2 class="homepage"><?php  echo strtoupper(the_title('', '', false)); ?></h2>
							<BR>
							<span class="homepage"><?php the_excerpt(); ?></span>
							<?php
							endwhile; endif; ?>
				</td>
				<td style='width:46px;'>
				</td>
				<td>
					<hr>
					<span class='heading'><img src='<?php bloginfo('template_url'); ?>/img/right-arrow.png' width=30  height=30>
						News &amp; Update</span><BR>
						<?php 
							$latest_cat_post = new WP_Query( array('posts_per_page' => 1));
							if( $latest_cat_post->have_posts() ) : while( $latest_cat_post->have_posts() ) : $latest_cat_post->the_post();
							?>
							<BR>
							<h2 class="homepage"><?php  echo strtoupper(the_title('', '', false)); ?></h2>
							<BR>
							<span class="homepage"><?php the_excerpt(); ?></span>
							<?php
							endwhile; endif; ?>
				</td>
			</tr>
			<tr>
				<td colspan='3' style='height:46px;'>
				</td>
			</tr>
			<tr>
				<td>
					<hr>
					<span class='heading'><img src='<?php bloginfo('template_url'); ?>/img/right-arrow.png' width=30  height=30>
						From Leonard</span><BR>
						<?php 
							$idObj = get_category_by_slug('leonards-updates'); 
							$id = $idObj->term_id;
							$latest_cat_post = new WP_Query( array('posts_per_page' => 1, 'category__in' => array($id)));
							if( $latest_cat_post->have_posts() ) : while( $latest_cat_post->have_posts() ) : $latest_cat_post->the_post();
							?>
							<BR>
							<h2 class="homepage"><?php  echo strtoupper(the_title('', '', false)); ?></h2>
							<BR>
							<span class="homepage"><?php the_excerpt(); ?></span>
							<?php
							endwhile; endif; ?>
				</td>
				<td></td>
				<td>
					<hr>
					<span class='heading'><img src='<?php bloginfo('template_url'); ?>/img/right-arrow.png' width=30  height=30>
						Prayer Requests</span><BR>
						<?php 
							$idObj = get_category_by_slug('prayer-requests'); 
							$id = $idObj->term_id;
							$latest_cat_post = new WP_Query( array('posts_per_page' => 1, 'category__in' => array($id)));
							if( $latest_cat_post->have_posts() ) : while( $latest_cat_post->have_posts() ) : $latest_cat_post->the_post();
							?>
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
		<div id="sidebar">
			<div class="sidebaritem">
				<a href='http://localhost/development/dummy.html'>Staff Directory</a><BR>
				<hr>
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
					echo strtoupper(the_title('', '', false));
					endwhile; endif; ?>
				
				<BR>
				<hr>
				<h1>Upcoming Event</h1><BR>
				<?php 
					$latest_cat_post = new WP_Query( 'p='.get_theme_mod('upcoming_event'));
					if( $latest_cat_post->have_posts() ) : while( $latest_cat_post->have_posts() ) : $latest_cat_post->the_post();
					echo strtoupper(the_title('', '', false)); 
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