
<?php
/*
 * Template Name: zApp MissionHub Stats Reporting
 * Description: Provide an interface to run reporting on stats from MissionHub and the Loop.
 * Author: Nathaniel Faries
 */
?>
<?php get_header(); ?>
<link href="<?php echo get_stylesheet_directory_uri(); ?>/missionhubstatsstyle.css" rel="stylesheet" type="text/css" />
	<div id="content">
		<div id="main-content">	
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div id="post-<?php the_ID(); ?>" class="post">
				<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
				<div class="entry">
					<?php 
					/*** Include page content for instructions, help text, etc ***/
					the_content(); 
					?>
					
                   
					<?php
                     /***APP CODE STARTS HERE***/
                    //require('missionhubstats/missionhubapirequests.php');

                    ?> 
                    <script type="text/javascript" src="missionhubstats/patscript.js"></script>
                    <script type="text/javascript" src="missionhubstats/missionhubstats.js"></script>
                    <script type="text/javascript" src="missionhubstats/missionhuborganizations.js"></script>
                    <div id="report-nav">
                        <ul>
                            <li><a href="?report=missionhub">MissionHub</a></li>
                            <li><a href="?report=pat">PAT</a></li>
                            <li><a href="?report=eventbrite">EventBrite</a></li>
                            <li><a href="?report=theloop">The Loop</a></li>
                            <li><a href="?report=admin">Admin</a></li>
                        </ul>
                    </div>
                    <div id="filter"></div>
                    <div id="reportcontent">
                    <?php
                    if(isset($_GET['report'])) {
                        $report = $_GET['report'];
                    } else {
                        $report = "";
                    }
                    switch($report) {
                        case "missionhub":  
                            include "missionhubstats/missionhuborganizationsview.php";
                            break;
                        case "pat":
                            include "missionhubstats/missionhubpat.php";
                            break;
                        case "eventbrite":
                            include "missionhubstats/missionhubeventbrite.php";
                            break;
                        case "theloop":
                            include "missionhubstats/missionhubtheloop.php";
                            break;
                        case "admin":
                            include "missionhubstats/missionhubadmin.php";
                            break;
                        case "reporttype":
                            include "missionhubstats/missionhubreporttypeview.php";
                            break;
                        case "people":
                            include "missionhubstats/missionhubpeople.php";
                            break;
                        default:
                            echo "Select a report button on the left to get started";
                            break;
                    }

                    ?>
                    </div>
                    
                    
                    <?php/***APP CODE ENDS HERE***/?>
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
</div>
<!--wrapper end-->
<div style='clear:both;'></div>	
<?php get_footer(); ?>