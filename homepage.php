<?php
/*
*Template Name: HomePage
*
*/

 get_header(); ?>
<div class="main">
	<?php /* todo include('slideshow.php'); */ ?>


	<?php 
	$about = get_post_meta(get_the_ID(), 'about-text', true);
	$involve = get_post_meta(get_the_ID(), 'involve-text', true);
	$donate = get_post_meta(get_the_ID(), 'donate-text', true);
	?>
	
	<div class='columns top'>
		<div class='column left'>
			<h2><a href='/who-we-are/'><span class='arrowbox'></span>About us</a></h2>
			<p><?php echo $about; ?></p>
		</div><div class='column centre'>
			<h2><a href='/get-involved/'><span class='arrowbox'></span>Get Involved</a></h2>
			<p><?php echo $involve; ?></p>
		</div><div class='column right'>
			<h2><a href='/donate/'><span class='arrowbox'></span>Donate</a></h2>
			<p><?php echo $donate; ?></p>
		</div>
			</div>
		<div class='bar'>
			<?php 
			$shareSaveDisabled = true;
			the_content();?>
		</div>
		<div class='columns bottom'>
		<div class='latest column'>
			<h2><a href='/blogs/org/'><span class='arrowbox'></span>Latest Post</a></h2>
				<?php
					$query =  new WP_Query('cat=11802&posts_per_page=1');
					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post(); ?>
							<h3><a href='<?php the_permalink(); ?>'><?php the_title(); ?></a></h3>
							<div class='text'><?php the_excerpt(); ?></div>
							<div class='bottombuttons'>
								<a class='readmore' href='<?php the_permalink(); ?>'>Read More</a>
								<a class='subscribe' href='subscribe'>Subscribe</a>
							</div>
							
							<?php
						}
					}
					/* Restore original Post Data */
					wp_reset_postdata();
				?>
			
		
				
			</div><div class='events column'>
				<h2><span class='arrowbox'></span><a href='/events/'>Upcoming Events</a></h2>
				
			<?php /* todo  $feed = fetch_feed('http://192.168.210.67:8787/organization/events/?listformat=rss');
			$first = true;
			foreach ($feed->get_items(0, 2) as $item){
				$loc = $item->get_item_tags ('http://powertochange.org', 'location');
				$source = $item->get_item_tags ('http://powertochange.org', 'source');
				if($source[0]['data'] == 'studioonline'){
					$linktext = 'Register';
					$class='register';
				}else{
					$linktext = 'Learn More';
					$class='learn';
				}
				
				if($first){
					echo "<div class='first' >";
					$first = false;
				}else{
					echo "<div >";
				}
				echo "<a class='link ". $class ."' href='" . $item->get_permalink() . "'>" . $linktext . "</a>";
				echo "<h3>" . $item->get_title() . "</h3>";
				echo "<h4>" . date('F j, Y') . "</h4>";
				echo "<h4>" . $loc[0]['child'][""]['city'][0]['data'] . ", " . $loc[0]['child'][""]['province'][0]['data'] . "</h4>";
				
				
				
				
				
				echo "</div>";
			}
			
			
			*/?>
			
			<div class='bottombuttons'>
				<a class='viewmore' href='/events/'>View More</a>
			</div>
			</div><div class='twitter column'>
			<?php // $tweets = get_tweets(); ?>
				<h2><span class='arrowbox'></span><a href='https://twitter.com/powertochange/'>Twitter</a> | <a href='<?php echo $tweets[0]['datelink']; ?>'><?php echo Date('M d, Y', $tweets[0]['date']); ?></a></h2>
				<a class='balllogo' href='https://twitter.com/powertochange/'><img src='/wp-content/themes/hybrid/corporate/images/balllogo.png' /></a>
				<h3><a href='https://twitter.com/powertochange/'>Power to Change</a></h3>
				<h4><a href='https://twitter.com/powertochange/'>@powertochange</a></h4>
				<p><?php echo $tweets[0]['tweet']; ?></p>
				    <script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
				
				<div class='actions'><a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $tweets[0]['id']; ?>">Reply</a>
					<a href="https://twitter.com/intent/retweet?tweet_id=<?php echo $tweets[0]['id']; ?>">Retweet</a>
					<a href="https://twitter.com/intent/favorite?tweet_id=<?php echo $tweets[0]['id']; ?>">Favorite</a>
				</div>
				
				<div class='bottombuttons'>
					<a href="https://twitter.com/powertochange" class="twitter-follow-button" data-show-count="false">Follow @powertochange</a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
				</div>
			</div><div class='facebook column'>
			<?php // $facebook = fb_feed(50767430543, array('echo'=>false)); 	?>
			<h2><span class='arrowbox'></span><a href='https://www.facebook.com/PowerToChange'>Facebook</a> | <a href='<?php echo $facebook[0]['link']; ?>'><?php echo Date('M d, Y', $facebook[0]['date']); ?></a></h2>
				<a class='balllogo' href='https://www.facebook.com/PowerToChange'><img src='/wp-content/themes/hybrid/corporate/images/balllogo.png' /></a>
				<h3><a href='https://www.facebook.com/PowerToChange'>Power to Change</a></h3>
				<p><?php echo $facebook[0]['message']; ?></p>
				<div class='bottombuttons'>
				<div id="fb-root"></div>
					<script>(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=1375050846054682";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));</script>
					<div class="fb-like" data-href="<?php echo $facebook['link']; ?>" data-send="true" data-layout="button_count" data-width="450" data-show-faces="false"></div>
				</div>
			</div>
		</div>

</div>
<?php get_footer(); ?>
