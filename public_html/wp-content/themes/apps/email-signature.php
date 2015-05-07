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
	'Advancement' =>  array('2014/07/Advancement-Email1.png', ''),
	'Business Intelligence' =>  array('2014/07/Advancement-Email1.png', ''),
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
	'Marketing & Communications' =>  array('2014/07/Advancement-Email1.png', ''),
	'Ministries Office' =>  array('2014/07/Ministry-Office-Email.png', ''),
	'Office of the EDA' =>  array('2014/07/Advancement-Email1.png', ''),
	'Office of the EDMS' =>  array('2014/07/Ministry-Services-Email.png', ''),
	'Partner Care' =>  array('2014/07/Advancement-Email1.png', ''),
	'Power to Change - Students' =>  array('2014/07/Office-President-Email.png', 'http://p2c.com/students'),
	'President\'s Office' =>  array('2014/07/Office-President-Email.png', ''),
	'Project Services' =>  array('2014/07/Project-Services-Email.png', ''),
	'The Life Project' =>  array('2014/07/TheLife-Email.png', 'http://thelife.com/'),
	'PTC Resource Centre' =>  array('2014/07/Resource-Centre.png', 'http://store.powertochange.org/'),
	'WHEN' =>  array('2014/07/WHEN-Email.png', 'http://whenetwork.ca/'),
	'Zones Teams' =>  array('2014/07/Zone-Teams-Email.png', ''),
	
	/*special people*/
	'chris.harman' => array('2015/05/Atlantic-Email.png', ''));
	
	/*very special people 
	you 
	S
	I am the good shepherd. I know my own and my own know me, just as the Father knows me and I know the Father; and I lay down my life for the sheep. 
	John 10:3
	
	*/


get_header(); ?>
<div id="content">
	<div id="main-content" class='form'>
	<?php if (have_posts()) : while (have_posts()) : the_post();  ?>
	<h1><?php the_title() ?></h1>
	<hr>
	<?php $parts = explode('/', get_page_uri(get_the_ID())); 
	$link = "";
	?>
	<table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr style=''>
	<?php for ($i = 0; $i < count($parts); $i ++){
		$link .= "/$parts[$i]";
		if ($i < count($parts) - 2){
			if ($i % 3 == 0 and $i > 0) {?>
				<tr></table><table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr><td class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level_grey.png' width='22' height='37' /></td>
			<? } ?>
			<td class ='crumbs'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
			<td class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level_grey.png' width='22' height='37' /></td>
		<?php } else if ($i < count($parts) - 1){
			if ($i % 3 == 0 and $i > 0) {?>
				</tr></table><table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr><td class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level_grey.png' width='22' height='37' /></td>
			<? } ?>
			<td class ='crumbs'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
			<td  class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level.png' width='22' height='37' /></td>
		<?php } else { 
			if ($i % 3 == 0 and $i > 0) {?>
				</tr></table><table style='width:100%;margin:30px 0;border-collapse: collapse;'><tr><td class ='crumbs' style='width:22px;'><img src='<?php bloginfo('template_url'); ?>/img/forms_level.png' width='22' height='37' /></td>
			<? } ?>
			<td class ='crumbs' style='background-color:#f7941d; width:auto;'><a href='<?php echo $link ?>'><?php echo get_page_by_path( $link )->post_title ?></a></td>
		<?php }
	 } ?>
	</tr></table>
	
    <div id="content-left">
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
		<tr><td><label for='qual'>Qualifications (MBA, PhD, etc)	:</label></td><td><input type='text' id='qual' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value=''/></td></tr>
		<tr><td><label for='phone'>Phone:</label></td><td><input type='text' id='phone' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php if($phone != null){echo $phone->number;} ?>'/></td></tr>
		<tr><td><label for='cell'>Cell:</label></td><td><input type='text' id='cell'  onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php echo "$user->role_title" ?>'/></td></tr>
		<tr><td><label for='role'>Role:</label></td><td><input type='text' id='role'  onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php if($cell != null){echo $cell->number;} ?>'/></td></tr>
		<tr><td><label for='ministry'>Ministry/Department:</label></td><td><input type='text' id='ministry' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php if($user->ministry == 'Development'){ echo 'Advancement';} else {echo $user->ministry;} ?>'/></td></tr>
		<tr><td><label for='sec_role'> Second Role:</label></td><td><input type='text' id='sec_role' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value=''/></td></tr>
		<tr><td><label for='sec_ministry'> Second Ministry/Department:</label></td><td><input type='text' id='sec_ministry' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value=''/></td></tr>
		</table>
		<div  style="border: 1px #0079C1 solid;padding:50px;" class="resetCSS" id='preview'></div>
		<textarea style='width:100%;height:200px;display:none;' id='code' readonly></textarea>

		
		<script type="text/javascript">
			function refreshSignature(){
				var signature = '<table style="border:none;font-family:verdana,sans-serif;color:#444444;" cellspacing="0" cellpadding="0">' +
					'<tr style="font-family:verdana,sans-serif;font-size: 10pt;color:#231f20;font-weight: bold;margin-bottom:3px;border-top:1px solid #c0c0c0;padding-top:15px;display:inline-block">' +
					'<td style="font-family:verdana,sans-serif;text-transform:uppercase;" >' +
					document.getElementById('name').value; 
					
				var qual = document.getElementById('qual').value.trim();
				if (qual != ""){	
					signature += '<span style="font-family:verdana,sans-serif;font-size: 11px; font-weight: normal;"><span style="color:#c0c0c0;">&nbsp;&nbsp;|&nbsp;&nbsp;</span>' + document.getElementById('qual').value + '</span></td>';
				}
				signature += '</tr>' +
					'<tr style="font-family:verdana,sans-serif;font-size: 11px;height:18px;">' +
					'<td style="font-family:verdana,sans-serif;" >' + document.getElementById('role').value + '<span style="color:#c0c0c0;">';
				var ministry = document.getElementById('ministry').value.trim();
				if (ministry != ""){
					signature += '&nbsp&nbsp;|&nbsp;&nbsp</span>' + ministry;
				}
				signature += '</td></tr>';
				var sec_role = document.getElementById('sec_role').value.trim();
				var sec_ministry = document.getElementById('sec_ministry').value.trim();
				if (sec_role != "" || sec_ministry != ""){
					signature += '<tr style="font-family:verdana,sans-serif;font-size: 11px;height:18px;"><td style="font-family:verdana,sans-serif;" >' + sec_role;
					if (sec_role != "" && sec_ministry != ""){
						signature += '<span style="color:#c0c0c0;">&nbsp;&nbsp;|&nbsp;&nbsp;</span>';
					}
					signature += sec_ministry + '</td></tr>';
				}
				signature += '<tr style="font-family:verdana,sans-serif;font-size: 11px;">' +
					'<td style="font-family:verdana,sans-serif;" >T.&nbsp;<a style="text-decoration:none;color:#444444;">';
				var phone = document.getElementById('phone').value.replace(".", "<?php echo $delimiter ?>").trim();
				if (phone == ""){
					signature += "<?php echo "604".$delimiter."514".$delimiter."2000" ?>";
				}
				else {
					signature += phone;
				}
				signature += "</a><span style='color:#c0c0c0;'>&nbsp;&nbsp;|&nbsp;&nbsp;</span>";
				var cell = document.getElementById('cell').value.replace(".", "<?php echo $delimiter ?>");
				if (cell.trim() == ""){
					signature += 'Toll&nbsp;Free&nbsp;<a style="text-decoration:none;color:#444444;"><?php echo "1".$delimiter."800".$delimiter."563".$delimiter."1106" ?>';
				}
				else {
					signature += 'C.&nbsp;<a style="text-decoration:none;color:#444444;">' + cell;
				}
				signature += '</a></td></tr>' +
					'<tr style="font-family:verdana,sans-serif;">' +
					'<td style="font-family:verdana,sans-serif;" ><a href="http://powertochange.org/" target="_blank"><img src="http://powertochange.com/wp-content/uploads/2015/03/P2C-Logo-Email1.png" height="80"  /></a><a href="<?php if ($division[$user->ministry][1] == '') { echo 'http://powertochange.org/';} else { echo $division[$user->ministry][1]; } ?>" target="_blank"><img src="http://powertochange.com/wp-content/uploads/' +
					'<?php
						if(array_key_exists($current_user->user_login, $division)){ //special people
							echo $division[$current_user->user_login][0];
						} else {
							echo $division[$user->ministry][0];
						}
					?>" height="80"  /></a></td></tr></table>';
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
