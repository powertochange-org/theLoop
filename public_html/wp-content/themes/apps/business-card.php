<?php 
/*
*Template Name: zApp BusinessCard
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
				`phone_type` = 'FAX' 
			ORDER BY `is_primary` DESC
			LIMIT 0,1");



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
		
		<link rel="stylesheet" type="text/css" href="/wp-content/themes/apps/business-card/MyFontsWebfontsKit.css">
		<style type="text/css">
		.front {
			background-image: url('/wp-content/uploads/2014/09/front.png');
		}
		.back {
			background-image: url('/wp-content/uploads/2014/09/back.png');
			
		}
		.bc {
			height:371px;
			width:650px;
			border: 1px black solid;
			position:relative;
		}
		.text {
			position:absolute;
			left:52px;
		}
		.name{
			top:40px;
			font: 24px Aspira-Bold;
		}
		.title{
			top:71px;
			font: 24px Aspira-Light;
		}
		.department{
			top: 101px;
			font: 24px Aspira-Light;
		}
		.contact{
			top: 155px;
			font: 24px AspiraXXNar-Regular;
		}
		.email{
			top: 182px;
			font: 24px AspiraXXNar-Regular;
		}
		.tfc{
			font: 24px AspiraXXNar-Demi;
			color:#0088FF
		}
		</style>
		<table style="border: 1px #F7941D solid;" id="inputTable">
		<tr><td><label for='name'>Name:</label></td><td><input type='text' id='name' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php echo "$user->first_name $user->last_name"?>'/></td></tr>
		<tr><td><label for='phone'>Phone:</label></td><td><input type='text' id='phone' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php if($phone != null){echo $phone->number;} ?>'/></td></tr>
		<tr><td><label for='cell'>Cell:</label></td><td><input type='text' id='cell'  onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php if($cell != null){echo $cell->number;} ?>'/></td></tr>
		<tr><td><label for='cell'>Fax:</label></td><td><input type='text' id='fax'  onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php if($fax != null){echo $fax->number;} ?>'/></td></tr>
		<tr><td><label for='ministry'>Ministry/Department:</label></td><td><input type='text' id='ministry' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php if($user->ministry == 'Development'){ echo 'Advancement';} else {echo $user->ministry;} ?>'/></td></tr>
		<tr><td><label for='email'>Email Address:</label></td><td><input type='text' id='email' onpaste='refreshSignature();' onkeyup='refreshSignature();' onchange='refreshSignature();' value='<?php echo  $current_user->user_email; ?>'/></td></tr>
		</table>
		
		
		<div class="resetCSS front bc" id='preview'></div>
		<div class="back bc"></div>
		<textarea style='width:100%;height:200px;display:none;' id='code' readonly></textarea>
		 
		
		<script type="text/javascript">
			function refreshSignature(){
			
			
				var signature = '<span class="name text">' + document.getElementById('name').value + '</span>' +
					'<span class="title text"><?php echo "$user->role_title" ?></span>' +
					'<span class="department text">' + document.getElementById('ministry').value + '</span>' +
					'<span class="contact text">';
				var phone = document.getElementById('phone').value;
				if (phone.trim() != ""){
					signature += '<span class="tfc">T&nbsp;</span>' + phone + '&emsp;';
				}
				var cell = document.getElementById('cell').value;
				if (cell.trim() != ""){
					signature += '<span class="tfc">C&nbsp;</span>' + cell + '&emsp;';
				}
				var fax = document.getElementById('fax').value;
				if (fax.trim() != ""){
					signature += '<span class="tfc">F&nbsp;</span>' + fax + '&emsp;';
				}
				signature += '</span>' +
					'<span class="email text">' + document.getElementById('email').value + '</span>';
				
				document.getElementById('preview').innerHTML = signature;
				document.getElementById('code').innerHTML = signature;
			}
			window.onload = refreshSignature;
		</script>
	</div></div>
    <div style='clear:both;'></div>
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
