<?php
/*
Template Name: Health_Plan_Report
The purpose of this page is to give a report on who has selected which health plan. administrative features are also offered.
Purposes, in order of importance
1)display report
2)administrators can grant access to the report to users as needed
3)as well as set a time window of when staff can select a health plan.

@Author Jordan Shelvock, May 31, 2012
*/
?>
<?php get_header(); ?>
	<div id="content">
		<div id="main-content">	
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div id="post-<?php the_ID(); ?>" class="post">
					<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
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
			
			<?php
					$isAdministrator = get_user_meta($current_user->ID, "wp_capabilities", true);
					//anything above the if statement on the next line will be visible to any schmuck that stumbles upon this URL
					if('true'==get_user_meta($current_user->ID, "healthplan_admin", true) //if user is flagged as as having access to healthplan
					|| '1'==$isAdministrator[administrator] ){ //or if user is wp admin
						
						if (! isset($_POST['dates'])) { //if user hasn't yet tried to update dates show them the form
							//grab the open and close dates from the database
							$open = $wpdb->get_var('SELECT option_value FROM wp_options WHERE option_name = "opendate"');
							$close = $wpdb->get_var('SELECT option_value FROM wp_options WHERE option_name = "closedate"');
							//use the date class to break up the open (O) and close (C) dates into month (M), day (D), year (Y)
							$defaultOM = date("n",strtotime($open)); //Numeric representation of a month, without leading zeros
							$defaultOD = date("j",strtotime($open)); //Day of the month without leading zeros
							$defaultOY = date("Y",strtotime($open)); //A full numeric representation of a year, 4 digits
							$defaultCM = date("n",strtotime($close));
							$defaultCD = date("j",strtotime($close));
							$defaultCY = date("Y",strtotime($close));
			?>	
						
							<form action="" method="post">
								<table>
									<tr>
										<th colspan=3>
											The signup form will be available from the following dates
										</th>
									</tr>'
									<tr>
										<td>
											<?php //this section dynamically picks the current month for open date. the php is used to add selected='selected' to the proper month, so it appears automatically ?>
											Open Date: <?php $default = date("n",strtotime($open)); ?>
											<select name="monthOpen" >
												<option value="1" <?php if($defaultOM==1){ echo "selected='selected'"; } ?>>January</option>
												<option value="2" <?php if($defaultOM==2){ echo "selected='selected'"; } ?>>February</option>
												<option value="3" <?php if($defaultOM==3){ echo "selected='selected'"; } ?>>March</option>
												<option value="4" <?php if($defaultOM==4){ echo "selected='selected'"; } ?>>April</option>
												<option value="5" <?php if($defaultOM==5){ echo "selected='selected'"; } ?>>May</option>
												<option value="6" <?php if($defaultOM==6){ echo "selected='selected'"; } ?>>June</option>
												<option value="7" <?php if($defaultOM==7){ echo "selected='selected'"; } ?>>July</option>
												<option value="8" <?php if($defaultOM==8){ echo "selected='selected'"; } ?>>August</option>
												<option value="9" <?php if($defaultOM==9){ echo "selected='selected'"; } ?>>September</option>
												<option value="10" <?php if($defaultOM==10){ echo "selected='selected'"; } ?>>October</option>
												<option value="11" <?php if($defaultOM==11){ echo "selected='selected'"; } ?>>November</option>
												<option value="12" <?php if($defaultOM==12){ echo "selected='selected'"; } ?>>December</option>
											</select>
											<select name="dayOpen">
												<?php
													//generates 31 days for the day selection box
													for($i = 1; $i < 32; $i++){
														if($defaultOD==$i){ 
															echo '<option value="'.$i.'"' .  'selected="selected"' . '>'.$i.'</option>';
														}
														else{
															echo '<option value="'.$i.'" >'.$i.'</option>';
														}
													}
												?>	
											</select>
											<select name="yearOpen">
												<?php
													//this grabs the current year then allows you to enter a date (either this year or next year)	 
													for($i = date("Y"); $i < date("Y")+2; $i++){
														if($defaultOY==$i){
															echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
														}
														else{
															echo '<option value="'.$i.'">'.$i.'</option>';
														}
													}
												?>
											</select>
										</td>
										<td>
											<?php //this section dynamically picks the current month for close date. the php is used to add selected='selected' to the proper month, so it appears automatically ?>
											Close Date:
											<select name="monthClose">
												<option value="1" <?php if($defaultCM==1){ echo "selected='selected'"; } ?>>January</option>
												<option value="2" <?php if($defaultCM==2){ echo "selected='selected'"; } ?>>February</option>
												<option value="3" <?php if($defaultCM==3){ echo "selected='selected'"; } ?>>March</option>
												<option value="4" <?php if($defaultCM==4){ echo "selected='selected'"; } ?>>April</option>
												<option value="5" <?php if($defaultCM==5){ echo "selected='selected'"; } ?>>May</option>
												<option value="6" <?php if($defaultCM==6){ echo "selected='selected'"; } ?>>June</option>
												<option value="7" <?php if($defaultCM==7){ echo "selected='selected'"; } ?>>July</option>
												<option value="8" <?php if($defaultCM==8){ echo "selected='selected'"; } ?>>August</option>
												<option value="9" <?php if($defaultCM==9){ echo "selected='selected'"; } ?>>September</option>
												<option value="10" <?php if($defaultCM==10){ echo "selected='selected'"; } ?>>October</option>
												<option value="11" <?php if($defaultCM==11){ echo "selected='selected'"; } ?>>November</option>
												<option value="12" <?php if($defaultCM==12){ echo "selected='selected'"; } ?>>December</option>
											</select>
											<select name="dayClose">
												<?php
													//generates 31 days for the day selection box
													for($i = 1; $i < 32; $i++){
														if($defaultCD==$i){ 
															echo '<option value="'.$i.'"' .  'selected="selected"' . '>'.$i.'</option>';
														}
														else{
															echo '<option value="'.$i.'" >'.$i.'</option>';
														}
													}
												?>	
											</select>
											<select name="yearClose">
												<?php
													//this grabs the current date then allows you to enter a date from last year until next year
													for($i = date("Y"); $i < date("Y")+2; $i++){
														if($defaultOY==$i){
															echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
														}
														else{
															echo '<option value="'.$i.'">'.$i.'</option>';
														}
													}
												?>
											</select>
										</td>
										<td><input type="submit" name="dates" value="Update Open/Close Dates" /></td>
									</tr>
								</table>
							</form>
							
			<?php
						} 
						else { //validate input if user updated dates
							$openDate = $_POST['yearOpen'] . '-' . $_POST['monthOpen'] . '-' . $_POST['dayOpen'];
							$closeDate = $_POST['yearClose'] . '-' . $_POST['monthClose'] . '-' . $_POST['dayClose'];
							//check edge dates for valid input 
							$validinput = true; //assume valid until proven otherwise
							$errormessage = ""; //we will concatenate any error messages onto this variable. this lets us support multiple error messages
							//now, to check if the dates were valid (for both open and close dates).
							//30 days has September, April, June and November. All the rest have 31. Except February.
							//september 31
							if($_POST['monthOpen'] == 9 && $_POST['dayOpen'] == 31){
								$validinput = false;
								$errormessage = $errormessage . " The chosen open month does not have 31 days.";
							}
							if($_POST['monthClose'] == 9 && $_POST['dayClose'] == 31){
								$validinput = false;
								$errormessage = $errormessage . " The chosen close month does not have 31 days.";
							}
							//april 31
							if($_POST['monthOpen'] == 4 && $_POST['dayOpen'] == 31){
								$validinput = false;
								$errormessage = $errormessage . " The chosen open month does not have 31 days.";
							}
							if($_POST['monthClose'] == 4 && $_POST['dayClose'] == 31){
								$validinput = false;
								$errormessage = $errormessage . " The chosen close month does not have 31 days.";
							}
							//june 31
							if($_POST['monthOpen'] == 6 && $_POST['dayOpen'] == 31){
								$validinput = false;
								$errormessage = $errormessage . " The chosen open month does not have 31 days.";
							}
							if($_POST['monthClose'] == 6 && $_POST['dayClose'] == 31){
								$validinput = false;
								$errormessage = $errormessage . " The chosen close month does not have 31 days.";
							}
							//november 31
							if($_POST['monthOpen'] == 11 && $_POST['dayOpen'] == 31){
								$validinput = false;
								$errormessage = $errormessage . " The chosen open month does not have 31 days.";
							}
							if($_POST['monthClose'] == 11 && $_POST['dayClose'] == 31){
								$validinput = false;
								$errormessage = $errormessage . " The chosen close month does not have 31 days.";
							}
							//feb 29--leap years are weird. so we need to determine if a given year is a leap year before we can check if it's a proper date
							if( ! date('L', strtotime($_POST['yearOpen']."-1-1"))){ //if not leap year
								if($_POST['monthOpen'] == 2 && $_POST['dayOpen'] == 29){
									$validinput = false;
									$errormessage = $errormessage . " This is not a leap year.";
								}
								if($_POST['monthClose'] == 2 && $_POST['dayClose'] == 29){
									$validinput = false;
									$errormessage = $errormessage . " This is not a leap year.";
								}
							}
							if( ! date('L', strtotime($_POST['yearClose']."-1-1"))){ //if not leap year
								if($_POST['monthOpen'] == 2 && $_POST['dayOpen'] == 29){
									$validinput = false;
									$errormessage = $errormessage . " This is not a leap year.";
								}
								if($_POST['monthClose'] == 2 && $_POST['dayClose'] == 29){
									$validinput = false;
									$errormessage = $errormessage . " This is not a leap year.";
								}
							}
							//feb 30, 31
							if($_POST['monthOpen'] == 2 && $_POST['dayOpen'] == 30){
								$validinput = false;
								$errormessage = $errormessage . " The chosen open month does not have 30 days.";
							}
							if($_POST['monthClose'] == 2 && $_POST['dayClose'] == 30){
								$validinput = false;
								$errormessage = $errormessage . " The chosen close month does not have 30 days.";
							}
							if($_POST['monthOpen'] == 2  && $_POST['dayOpen'] == 31){
								$validinput = false;
								$errormessage = $errormessage . " The chosen open month does not have 31 days.";
							}
							if($_POST['monthClose'] == 2 && $_POST['dayClose'] == 31){
								$validinput = false;
								$errormessage = $errormessage . " The chosen close month does not have 31 days.";
							}
							//check if open date is later then close date. It's important that this is right
							//because we check on the healthplan.php whether we are currently inbetween the open and close dates.
							if(strtotime($_POST['yearOpen']."-".$_POST['monthOpen']."-".$_POST['dayOpen'])
								>
								strtotime($_POST['yearClose']."-".$_POST['monthClose']."-".$_POST['dayClose'])){
								$validinput = false;
								$errormessage = $errormessage . " The close date should be after the open date.";
							}
							
							//now, if our input is still considered valid, send it to the db!!
							if($validinput){
								//clear old open dates
								$wpdb-> get_results("DELETE FROM wp_options WHERE option_name='opendate'"); 
								//insert new open dates
								$wpdb -> insert('wp_options', 
											array(
												'option_name' => 'opendate',
												'option_value' => $_POST['yearOpen']."-".$_POST['monthOpen']."-".$_POST['dayOpen']
											),
											array( 
												'%s', 
												'%s' 
											)
										);
								//clear old close dates
								$wpdb-> get_results("DELETE FROM wp_options WHERE option_name='closedate'");
								//insert new close dates
								$wpdb -> insert('wp_options', 
											array(
												'option_name' => 'closedate',
												'option_value' => $_POST['yearClose']."-".$_POST['monthClose']."-".$_POST['dayClose']
											),
											array( 
												'%s', 
												'%s' 
											)
										);
								echo "Successfully updated Open/Close times";
							}
							//if input is not valid
							else{
								echo "Did not successfully update Open/Close times.";
								echo "<p>" . $errormessage; //this contains all those error messages we checked for. It is pretty specific.
							}
							echo "<form action='' method='Post'><input type='submit' name='report' value='Return'></form>";
						}
						//REPORT
						//This section populates the table with everyone's information and chosen health plan
						//------------------------------------------------------------------------------------
						//Query for the main report table--the one that shows everyones info and plan. Uses left outer joins so if they
						//haven't picked a plan, they still show up.
						$results = $wpdb-> get_results('SELECT employee.user_login, first_name, last_name, plan, birth_date, dateentered, employee_number, staff_account
														FROM employee, wp_users, healthplan
														WHERE wp_users.user_login = employee.user_login
														AND wp_users.id = healthplan.userid
														GROUP BY last_name ');
														
						//build table with info from above query
						echo '<table>';
							echo '<tr>'; //header row
								echo '<th>User Login</th>';
								echo '<th>First Name</th>';
								echo '<th>Last Name</th>';
								echo '<th>Plan</th>';
								echo '<th>Birthday</th>';
								echo '<th>Date Entered</th>';
								echo '<th>Employee Number</th>';
								echo '<th>Staff Account</th>';
							echo '</tr>';
							foreach ( $results as $result ) 
							{
								echo '<tr>'; //data rows - contain all the data for each entry from db
									echo '<td>' . $result->user_login . '</td>';
									echo '<td>' . $result->first_name . '</td>';
									echo '<td>' . $result->last_name . '</td>';
									echo '<td>' . $result->plan . '</td>';
									echo '<td>' . $result->birth_date . '</td>';
									echo '<td>' . $result->dateentered . '</td>';
									echo '<td>' . $result->employee_number . '</td>';
									echo '<td>' . $result->staff_account . '</td>';
								echo '</tr>';
							}
						echo '</table>';
						//Granting Priviliges
						//this section allows all with access to this page to grant or revoke access to this page
						//only administrators priviliges can't be removed--they won't show up in the list unless added. 
						//adding and removing administrators affects nothing.
						if (isset($_POST['report'])) { //if the form has a $_POST['report'] variable, they were just sent here from the health plan page. this is the typical case. we will show  them who can access the report 
							echo '<form action="" method="post">';
								echo '<p />All administrators have access to this report. Additionally, the following users have access to this report. <p />';
								
								$results = $wpdb-> get_results('SELECT user_login
															FROM wp_users, wp_usermeta
															WHERE wp_users.id = wp_usermeta.user_id
															AND wp_usermeta.meta_key = "healthplan_admin"
															AND wp_usermeta.meta_value = "true"');
								echo '<table>';
									echo '<tr>';
										echo '<th>Username</th>';
									echo '</tr>';
									foreach ( $results as $result ) 
									{
										echo '<tr>';
											echo '<td>' . $result->user_login . '</td>';
										echo '</tr>';
									}
								echo '</table>';
								echo 'To add or remove a user\'s access to this report, use the forms below. <p /> ';
								echo '<input type="text" name="userAdd" />';
								echo '<input type="submit" name="submitAdd" value="Add User" /> <p />';
								echo '<input type="text" name="userRevoke" />';
								echo '<input type="submit" name="submitRevoke" value="Remove User" />';
							echo '</form>';	
						
						} //end if. anything above here is shown if the user hasn't used the form. 
						if (isset($_POST['submitAdd'])) { //else if the user added a user
							$toAdd = $_POST['userAdd'];
							$id = $wpdb-> get_var($wpdb->prepare("	SELECT id FROM wp_users WHERE user_login = %s ", $toAdd));
							//$wpdb-> get_results("REPLACE INTO wp_usermeta (user_id, meta_key, meta_value) VALUES ($id, 'healthplan_admin', 'true'	)");						
							$wpdb-> get_results("DELETE FROM wp_usermeta WHERE user_id=$id AND meta_key='healthplan_admin'"); 
							$wpdb-> get_results("INSERT INTO wp_usermeta VALUES ('', $id, 'healthplan_admin', 'true')");
							echo "Successfully added the user";
							echo '<form action="" method="post"><input type="submit" name="report" value="Click here to see updated access list" /></form>';
						} //else if
						else if (isset($_POST['submitRevoke'])) { //else if the user removed a user
							$toRevoke = $_POST['userRevoke'];
							$id = $wpdb-> get_var($wpdb->prepare("	SELECT id FROM wp_users WHERE user_login = %s ", $toRevoke));
							$wpdb-> get_results("DELETE FROM wp_usermeta WHERE user_id=$id AND meta_key='healthplan_admin'"); 
							$wpdb-> get_results("INSERT INTO wp_usermeta VALUES ('', $id, 'healthplan_admin', 'false')");
							echo "Successfully removed the user";
							echo '<form action="" method="post"><input type="submit" name="report" value="Click here to see updated access list" /></form>';
						} //else if
						

					} //end if. anything passed here will be visible to any schmuck that stumbles upon this URL					?>
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