<?php

/****************************************************************************************************
 *
 * Name: 		P2CSMissionTripsReport
 *
 * Description: This class is used to generate a report on how many students and interns are going
 *				on each mission trip in a particular school year. It pulls its data from the
 *				PAT (Project Application Tool) database. 
 *
 * Author:		Nathaniel Faries / Jason Brink
 *
 ***************************************************************************************************/

require_once("class.P2CSReport.php");

class P2CSMissionTripsReport extends P2CSReport {
	public function hasParameters() {
		return true;
	}
	
	public function renderParameters() {
		?>
		Select school year:
		<select name="schoolYear">
			<option value="">--SELECT A YEAR--</option>
			<?php
				$CurrYear = date("Y");
				$CurrDate = strtotime(date("Y-m-d"));
				$cutoff = strtotime($CurrYear . "-09-01");
				if ($cutoff > $CurrDate) {
					$x = 0;
				} else {
					$x = -1;
				}
				while ($CurrYear - $x >= 2007) {
					?><option value='<?php echo $CurrYear-$x-1;?>'>
					<?php echo "September " . ($CurrYear - $x - 1). " to August " . ($CurrYear - $x);?></option>
					<?php
					$x++;
				}
			?>
		</select>    
		<?php
	}
	
	public function renderHTMLReport($postData) {
		// Get the parameters set up for the SQL query
	    if (! isset($postData['schoolYear'])) {
			$CurrDate = strtotime(date("Y-m-d"));
			$cutoff = strtotime(date("Y") . "-09-01");
			if ($cutoff > $CurrDate) {
				$startdate = (date("Y") - 1) . "-09-01";
				$enddate = date("Y") . "-08-31";
			} else {
				$startdate = date("Y") . "-09-01";
				$enddate = (date("Y") + 1) . "-08-31";
			}
		} else {
			$year = $postData['schoolYear'];
			
			$startdate = $year . "-09-01";
			$enddate = ($year + 1) . "-8-31";        
		}
		
		// Connect to the PAT database
		$mydb = new wpdb(DB_USER, DB_PASSWORD, PAT_DB_NAME, DB_HOST);
		
		// Prepare and execute the SQL query to gather the data
		$sql = $mydb->prepare(
			"SELECT projects.title AS 'Project', 
					COALESCE(COUNT(`profiles`.id), 0) - COALESCE(SUM(`profiles`.as_intern), 0) AS '# Students',
					COALESCE(SUM(`profiles`.as_intern), 0) AS '# Interns'
			FROM `event_groups` JOIN
				`projects`ON event_groups.id = projects.event_group_id JOIN
				`profiles` ON projects.id = profiles.project_id
			WHERE parent_id = 1 AND projects.start > %s AND projects.start < %s AND profiles.status = 'accepted'
			GROUP BY event_groups.id, event_groups.title, projects.id, projects.title",
			$startdate,
			$enddate
			);
		
		$result = $mydb->get_results($sql);
		
		// Check if there was an error
		if (is_wp_error($mydb->error)) {
			echo "Error accessing the PAT database."; // More detailed error message for debugging: $mydb->error->get_error_message();
                        echo $mydb->error->get_error_message();
			return;
		}
		
		// Convert the database results to an HTML table, and echo that out
		echo P2CSReport::convertWPDBResultToHTMLTable($result);

		?>
		<a href='#' class='download'>Download</a>
		<br><br><font size="small"><i>Login to the <a href="https://pat.powertochange.org/">PAT</a> for more info</i></font>
	   
		<?php
	}
}

?>