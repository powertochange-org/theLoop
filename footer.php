<?php wp_footer(); ?>
<footer>
	<div class='top'>
		<div class='contact'>
			<img class='logo' src='/wp-content/themes/carmel/img/loop-logo.png' />
		</div><div class='links'>
				<?php dynamic_sidebar("footer") ?>
			<div>
				<h1>Links</h1>
				<ul>
					<li><a href='localhost/development/dummy.html'>My Settings</a></li>
					<li><a href='localhost/development/dummy.html'>myGCX</a></li>
					<li><a href='localhost/development/dummy.html'>Absence Tracker</a></li>
					<li><a href='localhost/development/dummy.html'>Help Desk</a></li>
					<li><a href='localhost/development/dummy.html'>Self-Help Wiki</a></li>
				</ul>
			</div><div>
				<h1>Archives</h1>
				<ul>
					<?php wp_get_archives('type=yearly'); ?>
				</ul>
			</div>
		</div>
	</div>
	<div class='wide'>
		<div class='middle'>
			<img class='logo' src='/wp-content/themes/carmel/img/footer-logo.png' />
			<img class='cluster' src='/wp-content/themes/carmel/img/cluster.png'  usemap="#clustermap" />
			<map name="clustermap">
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
	</div>
	<div class='bottom'>
		<div class='copy'>&copy; 2013 Power to Change Ministries. All rights reserved.</span><a class='privacy' href=''>Privacy Policy</a></div>
	</div>
</footer>
</body>
</html>








