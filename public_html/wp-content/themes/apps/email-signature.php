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
$sql = "SELECT CONCAT(
		IFNULL(`country_phone_code`, '1'), '$delimiter', 
		`area_code`, '$delimiter', 
		REPLACE(`contact_number`,'-', '$delimiter'),
		IFNULL(CONCAT('$delimiter', `extension`),'')) as number
			FROM phone_number 
			WHERE 
				`employee_id` = '$user->external_id' and 
				`is_ministry` = 1 and 
				`phone_type` = 'BUS' 
			ORDER BY `is_primary` DESC
			LIMIT 0,1";
//echo $sql;
$phone = $wpdb->get_row($sql);
$cell = $wpdb->get_row("SELECT CONCAT(
		IFNULL(`country_phone_code`, '1'), '$delimiter', 
		`area_code`, '$delimiter', 
		REPLACE(`contact_number`,'-', '$delimiter'),
		IFNULL(CONCAT('$delimiter', `extension`),'')) as number
			FROM phone_number 
			WHERE 
				`employee_id` = '$user->external_id' and
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
	'Information Technology' =>  array('2014/07/Advancement-Email.png', 'http://powertochange.org/'),
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
		<style type='text/css'>
		.resetCSS *{
			border:none;
			padding:0;
		}

		.resetCSS{
			border:solid black 1px;
			padding:5px;
			margin-top:5px;
			margin-bottom:5px;
		}
		</style>
		<table>
		<tr><td><label for='name'>Name:</label></td><td><input type='text' id='name' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php echo "$user->first_name $user->last_name"?>'/></td></tr>
		<tr><td><label for='phone'>Phone:</label></td><td><input type='text' id='phone' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php if($phone != null){echo $phone->number;} ?>'/></td></tr>
		<tr><td><label for='cell'>Cell:</label></td><td><input type='text' id='cell'  onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php if($cell != null){echo $cell->number;} ?>'/></td></tr>
		</table>
		<div class="resetCSS" id='preview'></div>
		<textarea style='width:100%;height:200px;' id='code' readonly></textarea>
		 
		
		<script type="text/javascript">
			function refreshSignature(){
				var signature = '<table style="border:none;font-family:verdana,sans-serif;color:#444444;width:1000px;min-width:1000px;" cellspacing="0" cellpadding="0">\n' +
					'<tr style="font-family:verdana,sans-serif;font-size: 10pt;color:#231f20;font-weight: bold;margin-bottom:3px;border-top:1px solid #c0c0c0;padding-top:15px;display:inline-block">\n' +
					'<td style="font-family:verdana,sans-serif;" >\n' +
						document.getElementById('name').value + '</td>\n' +
					'</tr>\n' +
					'<tr style="font-family:verdana,sans-serif;font-size: 11px;height:18px;">\n' +
					'<td style="font-family:verdana,sans-serif;" ><?php echo "$user->role_title" ?><span style="color:#c0c0c0;">|</span><?php echo "$user->ministry" ?></td>\n' +
					'</tr>\n' +
					'<tr style="font-family:verdana,sans-serif;font-size: 11px;">\n' +
					'<td style="font-family:verdana,sans-serif;" >T.&nbsp;<a style="text-decoration:none;color:#444444;">\n';
				var phone = document.getElementById('phone').value;
				if (phone.trim() == ""){
					signature += "<?php echo "1".$delimiter."604".$delimiter."514".$delimiter."2000" ?>\n";
				}
				else {
					signature += phone + '\n';
				}
				signature += "</a>&nbsp;<span style='color:#c0c0c0;'>|</span>&nbsp;\n";
				var cell = document.getElementById('cell').value;
				if (cell.trim() == ""){
					signature += 'Toll&nbsp;Free:&nbsp;<a style="text-decoration:none;color:#444444;"><?php echo "1".$delimiter."855".$delimiter."722".$delimiter."4483" ?>\n';
				}
				else {
					signature += 'C:&nbsp;<a style="text-decoration:none;color:#444444;">' + cell + '\n';
				}
				signature += '</a></td></tr>\n' +
					'<tr style="font-family:verdana,sans-serif;">\n' +
					'<td style="font-family:verdana,sans-serif;" ><a href="http://powertochange.org/" target="_blank"><img src="http://powertochange.com/wp-content/uploads/2014/07/P2C-Logo-Email.png" height="80"  /></a><a href="<?php echo $division[$user->ministry][1]?>" target="_blank"><img src="http://powertochange.com/wp-content/uploads/\n' +
					'<?php echo $division[$user->ministry][0]?>" height="80"  /></a></td></tr></table>\n';
				document.getElementById('preview').innerHTML = signature;
				document.getElementById('code').innerHTML = signature;

			}
			
			window.onload = refreshSignature;
		</script>
	</div></div>
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
