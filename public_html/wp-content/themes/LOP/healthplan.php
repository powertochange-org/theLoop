<?php
/*
Template Name: Health_Plan

@author Jordan Shelvock, May 31, 2012
*/

// Create array of health care spending account plans. The keys are the names, and the
// values are the monthly contribution amounts
$plans = array(
	'Granite' => 0,
	'Bronze' => 25,
	'Bronze Plus' => 50,
	'Silver' => 100,
	'Silver Plus' => 150,
	'Gold' => 200,
	'Gold Plus' => 300,
	'Platinum' => 350,
	'Platinum Plus' => 400,
	'Diamond' => 500);
	
?>
<?php get_header(); ?>
	<div id="content">
		<div id="main-content">	
			
			<?php if (empty($_POST)) { //if the form hasn't set any variables in $_POST, they haven't seen it yet. give them the form ?>
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div id="post-<?php the_ID(); ?>" class="post">
					<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
					<?php 
						// Set the time zone to PST, so that cutoff is at midnight PST
						date_default_timezone_set("America/Vancouver");
						
						//grab the open and close dates for the health plan. later we'll see if the user should be able to edit their plan
						$opendate  = $wpdb->get_var('SELECT option_value FROM wp_options WHERE option_name = "opendate"');
						$closedate = $wpdb->get_var('SELECT option_value FROM wp_options WHERE option_name = "closedate"');
						//post the open and close dates so the user is aware of when they can edit their health plan
						echo "You will be able to choose your health plan between <strong>" . $opendate . "</strong> and <strong>" . $closedate . "</strong>.";
					?>
					<div class="entry">
						<?php the_content(); ?>
					</div>
					<div class="clear"></div>
				</div>
			<?php endwhile;else:?>
				<div class="post">
                    <h2>404 - Not Found</h2>
                    <p>The page you are looking for is not here.</p>
                </div>
            <?php endif; ?>
			
			
			<form action="" method="post">
				<table class="small-table" style="float: left">
					<tr><th>Plan</th><th>Annual Amount</th><th>Monthly Amount</th></tr>
					<?php
					// Display table of plan options
					foreach ($plans as $planName => $planMonthly) {
						echo "<tr><td>$planName</td><td>\$".($planMonthly*12)."</td><td>\$$planMonthly</td></tr>";
					}
					?>
				</table>
				<table class="small-table" style="float: left; width: 500px; margin-left: 15px;">
					<tr>
					  <th>
						<?php
							$opendate = strtotime($opendate); //convert to unix time
							$closedate = strtotime("$closedate 23:59:59"); // convert to just before midnight unix time
							$currentdate = date("U"); //grab current date in unix time
							
							//determine if current date is inbetween the open date and the close date, then let the user know whether they can change plans or not
							if($currentdate >= $opendate && $currentdate <= $closedate){
								echo 'Please choose a health care spending account plan:';
							}
							else{
								echo 'You cannot currently change your health care spending account plan.';
							}
						?>
					  </th>
					</tr>
					<?php
					// Check if the current user has already submitted health plan information
					$message = "";
					$healthplanrow = $wpdb->get_row(
						$wpdb->prepare("SELECT plan, dateentered FROM healthplan WHERE userid = %s", $current_user->ID) );
					
					if ($healthplanrow != null) {
						$message = "<p>(You selected the \"\$".($plans[$healthplanrow->plan]*12)." annual - $healthplanrow->plan\" plan on $healthplanrow->dateentered.)</p>";
					}
					?>
					<?php 
					//check if we need another row in the table
					if (($currentdate > $opendate && $currentdate < $closedate) ||
					     $message != "") { 
					?>
					<tr>
						<td>
							<?php 
							//check again if current date is inbetween the open date and the close date
							if ($currentdate > $opendate && $currentdate < $closedate) { 
							?>
							<p>
							<select name="healthplan">
								<?php
								// Display health plan options
								foreach ($plans as $planName => $planMonthly) {
									echo "<option value=\"$planName\">\$".($planMonthly*12)." annual - $planName</option>";
								}
								?>
							</select>
							<input type="submit" name="submit" value="Submit" />
							</p>
							<?php
							}
							if ($message != "") {
								print $message;
							}
							?>
						</td>
					</tr>
					<?php
					}
					?>
				</table>
				<center>
					
				</center>
				
			</form> 	
			<?php
				}
				 else{ 
					// Form has variables from $_POST, ie they've seen the form already and entered what plan they want
					$healthplan = $_POST['healthplan'];
					
					// Save choice to the database
					$result = $wpdb->query( $wpdb->prepare( "REPLACE INTO healthplan VALUES (%d,%s,CURDATE() )", $current_user->ID, $healthplan ) );
					
					if ($result === false) {
						echo "There was an error saving your selection. Please contact helpdesk@powertochange.org.";
					} else {
						// Send the user an email reminding them of what they chose
						$headers = "From: hr@powertochange.org";
						$subject = "Health Care Spending Account Enrollment";
						$emailMessage = 
"$current_user->display_name,

Your enrollment in the Health Care Spending Account has been 
recorded. You chose the following plan:

  Plan name: $healthplan
  Annual amount: \$".($plans[$healthplan]*12)."
  Monthly amount: \$".$plans[$healthplan]."
  
For more information about the HCSA for 2013, refer to this post on
the loop: https://staff.powertochange.org/human-resources/important-update-2013-hcsa-enrollment-deadline-december-27th-2012/
or email hr@powertochange.org.

Your HR team
";

						mail($current_user->user_email, $subject, $emailMessage, $headers);
						
						// Print a success message
						echo "Successfully Updated your Health Plan";
						echo '<form action="" ><input type="submit" name="submit" value="Return" /></form>';
					}
				}
			?>  
		    <?php  
				//This sections gives administrators (or users with special permission) access to the healthplanreport.php page.
				$isAdministrator = get_user_meta($current_user->ID, "wp_capabilities", true);
				if('true'==get_user_meta($current_user->ID, "healthplan_admin", true) //if user is flagged as as having access to healthplan
					|| '1'==$isAdministrator[administrator] ){ //or if user is wp admin
			?>
					<table>
						<tr>
							<th>
								You are an administrator! You can view the Health Plan Report by clicking this button!
							</th>
							<th>
								<form action='../health-care-spending-account-admin/' method='post'>
									<input type='submit' name='report' value='See Report' >
								</form>
							</th>
						</tr>
					</table>
				<?php  } ?>
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