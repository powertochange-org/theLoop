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
		`area_code`, '$delimiter', 
		REPLACE(`contact_number`,'-', '$delimiter'),
		CASE
			WHEN `extension` is null THEN ''
			WHEN `extension` = '' THEN ''
			ELSE CONCAT('$delimiter', `extension`)
		END
		) as number
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
		`area_code`, '$delimiter', 
		REPLACE(`contact_number`,'-', '$delimiter'),
		CASE
			WHEN `extension` is null THEN ''
			WHEN `extension` = '' THEN ''
			ELSE CONCAT('$delimiter', `extension`)
		END
		) as number
			FROM phone_number 
			WHERE 
				`employee_id` = '$user->external_id' and
				`is_ministry` = 1 and 
				`phone_type` = 'CELL' 
			ORDER BY `is_primary` DESC
			LIMIT 0,1");

$division = array( 'Athletes in Action' => array('2014/07/Athletes-Email.png', 'http://athletesinaction.com/'),
	'Breakthrough Prayer Ministry' =>  array('2014/07/Breakthrough-Email.png', 'http://powertochange.com/breakthroughprayer/'),
	'Christian Embassy' =>  array('2014/07/ChristianEmbassy-Email1.png', 'http://www.christianembassy.ca/'),
	'Connecting Streams' =>  array('2014/07/Connecting-Streams-Email.png', 'http://www.connectingstreams.com/'),
	'Corporate Services' =>  array('2014/07/Corporate-Services-Email.png', ''),
	'Development' =>  array('2014/07/Advancement-Email1.png', ''),
	'DRIME' =>  array('2014/07/DRIME-Email.png', 'http://powertochange.com/drime/'),
	'FamilyLife' =>  array('2014/07/FamilyLife-Email.png', 'http://powertochange.com/familylife'),
	'Finance' =>  array('2014/07/Ministry-Services-Email.png', ''),
	'GAiN' =>  array('2014/07/GAiN-Email.png', 'http://globalaid.net/'),
	'Human Resources' =>  array('2014/07/Ministry-Services-Email.png', ''),
	'Information Technology' =>  array('2014/07/Ministry-Services-Email.png', ''),
	'International' =>  array('2014/07/International-Email.png', ''),
	'LeaderImpact' =>  array('2014/07/LeaderImpact-Email.png', 'http://www.leaderimpactgroup.com/'),
	'Ministries Office' =>  array('2014/07/Ministry-Office-Email.png', ''),
	'Office of the EDA' =>  array('2014/07/Advancement-Email1.png', ''),
	'Office of the EDMS' =>  array('2014/07/Ministry-Services-Email.png', ''),
	'Power to Change - Students' =>  array('2014/07/Office-President-Email.png', 'http://p2c.com/students'),
	'President\'s Office' =>  array('2014/07/Office-President-Email.png', ''),
	'Project Services' =>  array('2014/07/Project-Services-Email.png', ''),
	'The Life Project' =>  array('2014/07/TheLife-Email.png', 'http://thelife.com/'),
	'PTC Resource Centre' =>  array('2014/07/Resource-Centre.png', 'http://store.powertochange.org/'),
	'WHEN' =>  array('2014/07/WHEN-Email.png', 'http://whenetwork.ca/'),
	'Zones Teams' =>  array('2014/07/Zone-Teams-Email.png', ''));


get_header(); ?>
<div id="content">
	<?php if (have_posts()) : while (have_posts()) : the_post();  ?>
	<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
	<hr>
    <div id="content-left">
	<div id="main-content">
		<?php the_content(); ?>
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
		
		#inputTable tr, #inputTable td {
			border: 0;
		}
		</style>
		<table style="border: 1px #F7941D solid;" id="inputTable">
		<tr><td><label for='name'>Name:</label></td><td><input type='text' id='name' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php echo "$user->first_name $user->last_name"?>'/></td></tr>
		<tr><td><label for='phone'>Phone:</label></td><td><input type='text' id='phone' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php if($phone != null){echo $phone->number;} ?>'/></td></tr>
		<tr><td><label for='cell'>Cell:</label></td><td><input type='text' id='cell'  onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php if($cell != null){echo $cell->number;} ?>'/></td></tr>
		<tr><td><label for='ministry'>Ministry/Department:</label></td><td><input type='text' id='ministry' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php if($user->ministry == 'Development'){ echo 'Advancement';} else {echo $user->ministry;} ?>'/></td></tr>
		</table>
		<div  style="border: 1px #0079C1 solid;padding:50px;" class="resetCSS" id='preview'></div>
		<textarea style='width:100%;height:200px;display:none;' id='code' readonly></textarea>
		 
		
		<script type="text/javascript">
			function refreshSignature(){
				var signature = '<table style="border:none;font-family:verdana,sans-serif;color:#444444;" cellspacing="0" cellpadding="0">\n' +
					'<tr style="font-family:verdana,sans-serif;font-size: 10pt;color:#231f20;font-weight: bold;margin-bottom:3px;border-top:1px solid #c0c0c0;padding-top:15px;display:inline-block">\n' +
					'<td style="font-family:verdana,sans-serif;text-transform:uppercase;" >\n' +
					document.getElementById('name').value + '</td>\n' +
					'</tr>\n' +
					'<tr style="font-family:verdana,sans-serif;font-size: 11px;height:18px;">\n' +
					'<td style="font-family:verdana,sans-serif;" ><?php echo "$user->role_title" ?><span style="color:#c0c0c0;">\n';
				var ministry = document.getElementById('ministry').value;
				if (ministry.trim() != ""){
					signature += '&nbsp|&nbsp</span>' + ministry + '\n';
				}
				signature += '</td>\n' +
					'</tr>\n' +
					'<tr style="font-family:verdana,sans-serif;font-size: 11px;">\n' +
					'<td style="font-family:verdana,sans-serif;" >T.&nbsp;<a style="text-decoration:none;color:#444444;">\n';
				var phone = document.getElementById('phone').value.replace(".", "<?php echo $delimiter ?>");
				if (phone.trim() == ""){
					signature += "<?php echo "604".$delimiter."514".$delimiter."2000" ?>\n";
				}
				else {
					signature += phone + '\n';
				}
				signature += "</a><span style='color:#c0c0c0;'>|</span>\n";
				var cell = document.getElementById('cell').value.replace(".", "<?php echo $delimiter ?>");
				if (cell.trim() == ""){
					signature += 'Toll&nbsp;Free&nbsp;<a style="text-decoration:none;color:#444444;"><?php echo "1".$delimiter."855".$delimiter."722".$delimiter."4483" ?>\n';
				}
				else {
					signature += 'C.&nbsp;<a style="text-decoration:none;color:#444444;">' + cell + '\n';
				}
				signature += '</a></td></tr>\n' +
					'<tr style="font-family:verdana,sans-serif;">\n' +
					'<td style="font-family:verdana,sans-serif;" ><a href="http://powertochange.org/" target="_blank"><img src="http://powertochange.com/wp-content/uploads/2014/07/P2C-Logo-Email1.png" height="80"  /></a><a href="<?php if ($division[$user->ministry][1] == '') { echo 'http://powertochange.org/';} else { echo $division[$user->ministry][1]; } ?>" target="_blank"><img src="http://powertochange.com/wp-content/uploads/\n' +
					'<?php echo $division[$user->ministry][0]?>" height="80"  /></a></td></tr></table>\n';
				document.getElementById('preview').innerHTML = signature;
				document.getElementById('code').innerHTML = signature;
			}
			window.onload = refreshSignature;
		</script>
	</div></div>
    <div id="content-right"><?php get_sidebar(''); ?></div><div style='clear:both;'></div>
    <?php endwhile; endif; ?>
</div>
<!--content end-->
<!--Popup window-->
</div>
<!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>		
<?php get_footer(); ?>
