<?php
/*
*Template Name: Staff_Directory
*
*This page is always loaded when you use staff address page. it contains the wordpress template.
*When we change pages, we just reload this page and 'switch' the include we want to use. It's pretty
*dandy.
*
*/
?>
<?php get_header(); ?>
	<div id="content">
			<div id="main-content">	
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div id="post-<?php the_ID(); ?>" class="post">
					<h1 class="replace" style="float:left"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
					<div class="entry">
						<?php 
							if(isset($_GET['page'])){ //check if the page has been specified
								$site = $_GET['page']; //grab from the URL which page we're looking for
							} else { 
								$site = "";
							}
							switch ($site) //load the specified site, or the dashboard by default
							{
							case "profile":
								if (isset($_GET['person'])) {
									$profile = $_GET['person'];
								}
								$current_user = wp_get_current_user();
								if(!isset($profile) || $current_user->user_login == $profile){
									include 'staffdirectory/myprofile.php';
								}
								else{
									include 'staffdirectory/profile.php';
								}
							  break;
							case "search":
							  include 'staffdirectory/search.php';
							  break;
							//case "search_results":
							  //include 'staffdirectory/search_results.php';
							  //break;
							case "upload_processor":
							  include 'staffdirectory/upload.processor.php';
							  break;
							//case "upload_success":
							  //include 'staffdirectory/upload.success.php';
							  //break;
							 case "approval":
							  include 'staffdirectory/approval.php';
							  break;
							default:
							  include "staffdirectory/search.php";
							}
						?>
					</div>
					<div class="clear"></div>				
				</div>
				<?php endwhile; else : ?>
                <div class="post">
                    <h2>404 - Not Found</h2>
                    <p>The page you are looking for is not here.</p>
                </div>
                <?php endif; ?>
			</div>
			
	</div>
	<!--content end-->
	<!--Popup window-->
	<?php include(TEMPLATEPATH.'/popup.php') ?>
    </div>
    <!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>		
<?php get_footer(); ?>