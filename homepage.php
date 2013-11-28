<?php
/*
*Template Name: HomePage
*
*/
 get_header(); ?>
<div id="content">
	<div id='slideshow' style='height:100px; width:100%;'>
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
						News and Update</span><BR>
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
							$idObj = get_category_by_slug('from-leonard'); 
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
						<a style='display:block;width:100%;background-color:#f7941d;padding:20px 0;border-radius:5px' href='/'><center style='color:#ffffff;'>SUBMIT A PRAYER REQUEST</center></a>
				</td>
			</tr>
		</table>
	
	
	   
	</div>
    </div>
    <div id="content-right"><?php get_sidebar(''); ?></div><div style='clear:both;'></div>
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