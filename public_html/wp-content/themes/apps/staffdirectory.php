<?php
/*
*Template Name: zApp Staff_Directory
*
*This page is always loaded when you use staff address page. it contains the wordpress template.
*When we change pages, we just reload this page and 'switch' the include we want to use. It's pretty
*dandy.
*
*/
?>
<?php get_header(); ?>
	<link href="<?php echo get_stylesheet_directory_uri(); ?>/staff-directory-style.css" rel="stylesheet" type="text/css" />
	<div id="content" class='staff-d'>
		<?php if (have_posts()) : while (have_posts()) : the_post();  ?>
			<h1 style="float:left;"><a style="font-size:35pt;font-family:Roboto Slab;font-weight:100;" href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
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
						if (!isset($_GET['person'])) {
							$_GET['person'] =  $current_user->user_login;
						}
						include 'staffdirectory/profile.php';
					  break;
					case "myprofile":
					  include 'staffdirectory/myprofile.php';
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
		<?php endwhile; else: ?>
		<h2>404 - Not Found</h2>
		<p>The page you are looking for is not here.</p>					 
		<?php endif; ?>
	</div>
	<!--content end-->
	<!--Popup window-->
    </div>
    <!--main end-->
</div>
<!--wrapper end-->
<div style='clear:both;'></div>
<?php get_footer(); ?>