<?php 
/*
*Template Name: Email Signature
*
*Author: matthew.chell
*
*/

$delimiter = '&#x2e;';

$current_user = wp_get_current_user();
$user = $wpdb->get_row("SELECT * FROM employee WHERE user_login = '" . $current_user->user_login . "'");
$phone = $wpdb->get_row("SELECT CONCAT(
		IFNULL(`country_phone_code`, '1'), $delimiter, 
		`area_code`, $delimiter, 
		`contact_number`,
		IFNULL(CONCAT($delimiter, `extension`),'')) as number
			FROM phone_number 
			WHERE 
				`employee_id` = $user->employee_id and 
				`is_ministry = 1 and 
				`is_ministry` = 1 and 
				`phone_type` = 'BUS' 
			ORDER BY `is_primary` DESC
			LIMIT 0,1");
$cell = $wpdb->get_row("SELECT CONCAT(
		IFNULL(`country_phone_code`, '1'), $delimiter, 
		`area_code`, $delimiter, 
		`contact_number`,
		IFNULL(CONCAT($delimiter, `extension`),'')) as number
			FROM phone_number 
			WHERE 
				`employee_id` = $user->employee_id and 
				`is_ministry = 1 and 
				`is_ministry` = 1 and 
				`phone_type` = 'CELL' 
			ORDER BY `is_primary` DESC
			LIMIT 0,1");


$division = array( 'Athletes in Action' => array('', ''),
	'Breakthrough Prayer Ministry' =>  array('', ''),
	'Christian Embassy' =>  array('', ''),
	'Connecting Streams' =>  array('', ''),
	'Corporate Services' =>  array('', ''),
	'Development' =>  array('2014/07/Advancement-Email.png', 'http://powertochange.org/'),
	'DRIME' =>  array('', ''),
	'FamilyLife' =>  array('', ''),
	'Finance' =>  array('', ''),
	'GAiN' =>  array('', ''),
	'Human Resources' =>  array('', ''),
	'Information Technology' =>  array('', ''),
	'International' =>  array('', ''),
	'LeaderImpact' =>  array('', ''),
	'Ministries Office' =>  array('', ''),
	'Office of the EDA' =>  array('', ''),
	'Office of the EDMS' =>  array('', ''),
	'Power to Change - Students' =>  array('', ''),
	'President\'s Office' =>  array('', ''),
	'Project Services' =>  array('', ''),
	'PTC Resource Centre' =>  array('', ''),
	'The Life Project' =>  array('', ''),
	'WHEN' =>  array('', ''),
	'Zones Teams' =>  array('', ''));


get_header(); ?>
<div id="content">
	<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
	<hr>
    <div id="content-left">
	<div id="main-content">
		<script src="https://code.jquery.com/jquery-latest.js"></script>
		
		<label for='name'>Name:</label><input type='text' id='name' onchange='refreshSignature();' value='<?php echo '$user->first_name $user->last_name'?>'/>
		<label for='phone'>Phone:</label><input type='text' id='phone' onchange='refreshSignature();' value='<?php echo '$phone[number]'?>'/>
		<label for='cell'>Cell:</label><input type='text' id='cell' onchange='refreshSignature();' value='<?php echo '$cell[number]'?>'/>
		<button type="button" onclick='refreshSignature();'>Click Me!</button>
		<div id='preview'></div>
		<textarea id='code' readonly></textarea>
		 
		
		<script type="text/javascript">
			function refreshSignature(){
				var signature = '<table style="font-family:verdana,sans-serif;color:#444444;width:1000px;min-width:1000px;" cellspacing="0" cellpadding="0">' +
					'<tr style="font-family:verdana,sans-serif;font-size: 10pt;color:#231f20;font-weight: bold;margin-bottom:3px;border-top:1px solid #c0c0c0;padding-top:15px;display:inline-block">' +
					'<td style="font-family:verdana,sans-serif;" >' +
						document.getElementById('name').value + '</td>' +
					'</tr>' +
					'<tr style="font-family:verdana,sans-serif;font-size: 11px;height:18px;">' +
					'<td style="font-family:verdana,sans-serif;" ><?php echo '$user->role_title' ?><span style='color:#c0c0c0;'>|</span><?php echo '$user->ministry' ?></td>' +
					'</tr>' +
					'<tr style="font-family:verdana,sans-serif;font-size: 11px;">' +
					'<td style="font-family:verdana,sans-serif;" >T.&nbsp;<a style="text-decoration:none;color:#444444;">';
				var phone = document.getElementById('phone').value;
				if (phone.trim() == ""){
					signature += "<?php echo "1$delimiter604$delimiter514$delimiter2000" ?>";
				}
				else {
					signature += phone;
				}
				signature += "</a>&nbsp;<span style='color:#c0c0c0;'>|</span>&nbsp;";
				var cell = document.getElementById('cell').value;
				if (cell.trim() == ""){
					signature += '<?php echo 'Toll&nbsp;Free:&nbsp;<a style="text-decoration:none;color:#444444;">1&#x2e;855$delimiter722&#x2e;4483' ?>';
				}
				else {
					signature += 'Cell:&nbsp;<a style="text-decoration:none;color:#444444;">' + cell;
				}
				signature += '</a></td></tr>' +
					'<tr style="font-family:verdana,sans-serif;">' +
					'<td style="font-family:verdana,sans-serif;" ><a href="http://powertochange.org/" target="_blank"><img src="http://powertochange.com/wp-content/uploads/2014/07/P2C-Logo-Email.png" height="80"  /></a><a href="<?php echo $division[$user->ministry][1]?>" target="_blank"><img src="http://powertochange.com/wp-content/uploads/' +
					'<?php echo $division[$user->ministry][0]?>" height="80"  /></a></td></tr></table>';
				document.getElementById('preview').innerHTML = signature;
				document.getElementById('code').innerHTML = signature;

			}
			
			window.onload = refreshSignature;
		</script>
	</div>
    <div id="content-right"><?php get_sidebar(''); ?></div><div style='clear:both;'></div>
</div>
<!--content end-->
<!--Popup window-->
</div>
<!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>		
<?php get_footer(); ?>
