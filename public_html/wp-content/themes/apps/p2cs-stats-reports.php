<?php
/*
 * Template Name: zApp P2C-S Stats Reports
 * Description: Provide an interface to run reporting on stats from MissionHub, EventBrite, the PAT, and other systems
 * Author: Nathaniel Faries
 *
 *
 * TO ADD A NEW REPORT TO THIS STATS REPORTING SYSTEM:
 *
 * 1. Create a class that extends P2CSReport. A good way to start is to copy an existing report class file
 *	  (like class.P2CSMissionTripsReport.php), and modify it as needed.
 *
 * 2. Open class.P2CSReport.php, and add your new report to the $reportList collection. You need to come up
 *	  with a string that will be used to access your report (such as "missiontrips") which links up to the
 *	  name of the file and class you've created.
 *
 * 3. Add your new report to the menu down below. The link to your report should use the string you registered
 *	  in step 2.
 *
 */
?>
<?php get_header(); ?>
<link href="<?php echo get_stylesheet_directory_uri(); ?>/p2cs-stats-reports-style.css" rel="stylesheet" type="text/css" />
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
					/*****************************************************************************************
					 * APP CODE STARTS HERE
					 ****************************************************************************************/					
					?>

					<script type="text/javascript">
					var P2CSStatsReportsAjax = {
					<?php
					/* Create some JavaScript variables used to make Ajax calls */
					echo '"ajaxurl":"' . admin_url( 'admin-ajax.php' ) . 
						'","nonce":"' . wp_create_nonce( 'p2cs-stats-reports-nonce' ) . '"';
                    ?> 
					};
					</script>
					<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/p2cs-stats-reports/p2cs-stats-reports.js"></script>
                                        <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/p2cs-stats-reports/tsorter.min.js"></script>
                                        <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/p2cs-stats-reports/export-csv.js"></script>
                                        
					<?php					
					/*****************************************************************************************
					 * REPORT MENU STARTS HERE - if you create a new report, it must be added here
					 ****************************************************************************************/					
					?>
					<div id="report-nav">
						<ul>
							<li><span class="report-nav-heading">MissionHub Reports</span></li>
								<ul>
									<li><a href="?report=missionhubengagement">Engagement</a></li>
									<li><a href="?report=missionhubdiscipleship">Discipleship</a></li>
									<li><a href="?report=missionhubindicateddecisions">Indicated Decisions</a></li>
								</ul>
							<li><a href="?report=missiontrips">Mission Trip Report</a></li>
							<li><a href="?report=eventbrite">EventBrite Report</a></li>							
					</div>
                    
					<div id="report-content">
                    <?php
                    if(isset($_GET['report'])) {
                        $reportName = $_GET['report'];
                    } else {
                        $reportName = "";
                    }
					
					// Pull in the parent class for all reports
					require_once('p2cs-stats-reports/class.P2CSReport.php');
					
					// Determine if the report name passed in is a valid one					
					if (isset(P2CSReport::$reportList[$reportName])) {
						$fileName = P2CSReport::$reportList[$reportName]['fileName'];
						$className = P2CSReport::$reportList[$reportName]['className'];
						
						include_once('p2cs-stats-reports/' . $fileName);
						$report = new $className();
					} else {
						echo "Select a report from the menu to get started.";
					}
					
					// If the user selected a report, do typical handling with it
					if (isset($report)) {
						// First, check if the report has parameters. If so, render them.
						if ($report->hasParameters()) {
							// Build a little form to embed the parameters into
							?>
							<form id="report-parameters" name="report-parameters" action="<?php echo get_permalink() . "?report=$reportName";?>" method="POST">
							<input type="hidden" id="reportName" name="reportName" value="<?php echo $reportName; ?>" />
							
							<?php
							// Get the report to display it's own parameters
							$report->renderParameters();
							
							// Add a submit button. If the JavaScript works correctly, the report will be
							// pulled in using Ajax. If not, a POST request will be submitted and the report
							// will be generated that way.
							?>
							<br />
							<input id="report-generate" name="report-generate" type="submit" value="Generate Report" />

							<div id="ajax-loading">
							<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/ajax-loading.gif" />
							</div>
							</form>
							
							<br />
							
							<?php
						}
						
						echo '<div id="report-table" class="sortable">';
						
						// If the report doesn't have parameters, or the parameters have been
						// collected, then generate the actual report. Typically, reports with parameters
						// wouldn't get here, as the report would be called through Ajax.
						if (! $report->hasParameters() || $_SERVER['REQUEST_METHOD'] === 'POST') {
							$report->renderHTMLReport($_POST);
						}
                                                

						echo '</div>';

                    
                                        }
                    ?>
                    <script type='text/javascript'>
                        tsorter.create('report');
                        $('.download').on('click', function (event) {
                            exportToCSV.apply(this, [$('#report'), 'export.csv']);
                        });
                    </script>
 
                    </div>
                                        
                    <?php 
					/*****************************************************************************************
					 * APP CODE ENDS HERE
					 ****************************************************************************************/					
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
</div>
<!--wrapper end-->
<div style='clear:both;'></div>	
<?php get_footer(); ?>