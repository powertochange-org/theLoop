<html>
	<head>
		<title>IT Survey</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<link rel="stylesheet" type="text/css" href="https://cas.powertochange.org/themes/cas.css" />
		<link rel="stylesheet" type="text/css" href="https://cas.powertochange.org/themes/PTC/theme.css" media="screen"/>
		<link rel="icon" type="image/png" href="https://cas.powertochange.org/themes/PTC/favicon.png" />
	</head>
		<?php if(count ($_GET) > 1) {
			//gets the nesscary constants from the wp config file
			global $GET_WORD_PRESS_VARIABLE;
			$GET_WORD_PRESS_VARIABLE = true;
			
			include('../wp-config.php');
			
			// Creates a connection because wp will not be active
			$con=mysqli_connect(constant("DB_HOST"),constant("DB_USER"),constant("DB_PASSWORD"),constant("DB_NAME"));
			
			$sql_part1 = 'INSERT INTO `it_survey`(`id`, `time`';
			$sql_part2 = ') VALUES (NULL, NULL';
			$array_key = array_keys($_GET);
			for ($i = 0; $i < count($_GET); $i ++){
				$key = $array_key[$i];
				$sql_part1 .= ', `'.$key.'`';
				$sql_part2 .= ', "'.mysql_real_escape_string($_GET[$key]).'"';
			}
			$sql= $sql_part1.$sql_part2.')';
			mysqli_query($con, $sql);
			mysqli_close($con);
		?>
		<body>
			<div style='top: 300px; position: relative;'>
				Thank you for completing the IT survey.<BR><BR>
				We appreciate your feedback!
			</div>

			<table id="list-of-sites-container" style='top: 220px'>
				<tr>
				  <td id="list-of-sites">
					<ul><li><a href=https://absences.powertochange.org>Absence Tracker</a></li><li><a href=http://moodle.powertochange.org/staff/login>Global Learning Center</a></li><li><a href=http://moodle.powertochange.org/sandbox/login>Moodle Sandbox</a></li><li><a href=https://wiki.powertochange.org/help>Self-Help Wiki</a></li><li><a href=https://staff.powertochange.org>the LOOP</a></li><li><a href=http://staffblogs.powertochange.org/wp-login.php>Staff Blog Site</a></li></ul>
				  </td>
				</tr>
				<tr>
					 <td id="forgot-my-password">
						<a href="http://cas.powertochange.org/password-change" target="_blank">change or reset password</a>
					 </td>
				</tr>
			</table>
		</body>
		<?php }	else { ?>
		<body style='margin-left: auto; margin-right: auto; width: 350px'>
			<div style='top: 240px; position: relative; text-align:left'>
				<h2>IT Survey</h2>
				<form name="survey" id="survey" action="https://staff.powertochange.org/custom-pages/it-survey.php" method="GET" target="_self" onsubmit="">
					1. How well did we meet your expectations?<BR>
					<input type="radio" name="how_well" value="1">1 - Poor
					<input type="radio" name="how_well" value="2">2
					<input type="radio" name="how_well" value="3">3
					<input type="radio" name="how_well" value="4">4
					<input type="radio" name="how_well" value="5">5 - Excellent
					<BR><BR>
					2. How can we improve in providing IT service?<BR>
					<textarea name="comment" id="comment" form="survey" rows="6" cols="35" maxlength="1000"></textarea><BR>
					(1000 character max)<BR><BR>
					<input type="hidden" name="ticket_id" value="<?php if (array_key_exists ( 'ticket_id' , $_GET )) {echo $_GET['ticket_id'];} else {echo 0;}; ?>">
					<input type="submit" value="Submit">
				</form>
			</div>
		</body>
		<?php } ?>
	
</html>