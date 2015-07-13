
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
                    require('missionhubstats/missionhubapirequests.php');

                    ?> 
                    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<!--                    <script type="text/javascript" src="missionhubstats/script.js"></script>-->
                    <script type="text/javascript" src="missionhubstats/jsquerytest.js"></script>
                    <script type="text/javascript" src="missionhubstats/patscript.js"></script>
                    <script type="text/javascript" src="missionhubstats/missionhubstats.js"></script>
                    <div id="nav">
<!--
                    	<ul>
                            <form action="?report=surveys" id="surveys" method="post"></form>
                            <form action="?report=people" id="people" method="post"></form>
                            <form action="?report=pat" id="pat" method="post"></form>
                    		<li><button form="surveys">Surveys</button></li>
                    		<li><button form="people">People</button></li>
                            <li><button form="pat">PAT</button></li>
                    	</ul>
-->                     
                        <form action="?report=" id="home" method="post"></form>
                        <form action="?report=missionhub" id="missionhub" method="post"></form>
                        <form action="?report=pat" id="pat" method="post"></form>
                        <form action="?report=eventbrite" id="eventbrite" method="post"></form>
                        <form action="?report=theloop" id="theloop" method="post"></form>
                        <form action="?report=admin" id="admin" method="post"></form>
                        <ul>
                            <li><button form="home">Home</button></li>
                            <li><button form="missionhub">MissionHub</button></li>
                            <li><button form="pat">PAT</button></li>
                            <li><button form="eventbrite">EventBrite</button></li>
                            <li><button form="theloop">The Loop</button></li>
                            <li><button form="admin">Admin</button></li>
                        </ul>
                    </div>
                    <div id="filter"></div>
                    <div id="reportcontent">
                    <?php
                    if(isset($_GET['report'])) {
                        $site = $_GET['report'];
                    } else {
                        $site = "";
                    }
                    switch($site) {
                        case "missionhub":  
                            include "missionhubstats/missionhubexposure.php";
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
                        case "organizations":
                            include "missionhubstats/missionhuborganizationsview.php";
                            break;
                        case "reporttype":
                            include "missionhubstats/missionhubreporttypeview.php";
                            break;
                        case "people":
                            include "missionhubstats/missionhubpeople.php";
                            break;
                        default:
                            echo "Please modify the url";
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