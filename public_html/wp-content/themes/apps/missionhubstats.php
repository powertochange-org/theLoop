
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

                    <div id="nav">
                    	<ul>
                            <form action="?report=surveys" id="surveys" method="post"></form>
                            <form action="?report=people" id="people" method="post"></form>
                    		<li><button form="surveys">Surveys</button></li>
                    		<li><button form="people">People</button></li>
                    	</ul>
                    </div>
                    <div id="reportcontent">
                    <?php
                    if(isset($_GET['report'])) {
                        $site = $_GET['report'];
                    } else {
                        $site = "";
                    }
                    switch($site) {
                        case "surveys":                        
                            $surveys = getIndexOfEndpoint("surveys", "questions,keyword", "", "", "", "", "");
                            echo "<br /><br />";
                            var_export($surveys['surveys'][0]['id']);
                            echo "<br />";
                            foreach($surveys['surveys'][0] as $propertyname=>$value) {
                                echo $propertyname . " has value " . $value . "<br />";
                            }
                            break;
                        case "people":
                            $people = getIndexOfEndpoint("people", "", "10", "", "", "", "");
                            echo "<br /><br />";
                            var_export($people['people'][0]['id']);
                            echo "<br />";
                            foreach($people['people'][0] as $propertyname=>$value) {
                                echo $propertyname . " has value " . $value . "<br />";
                            }
                            break;
                        default:
                            echo "Please modify the url";
                            break;
                    }

                    

                    
                    /*function missionhubAPICall($method, $url, $data = false) { //$method is what REST method is being called. Unsure what $data is for but stackoverflow has it so let's roll with that for now
                        $curl = curl_init();

                    
                    }*/

                    

                    /***APP CODE ENDS HERE***/
                    ?>
                    </div>						
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