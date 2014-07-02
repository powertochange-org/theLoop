<?php 
/*
*Template Name: Email Signature
*
*Author: matthew.chell
*
*/
get_header(); ?>
<div id="content">
	<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
	<hr>
    <div id="content-left">
	<div id="main-content">
		<script src="https://code.jquery.com/jquery-latest.js"></script>
		
		<label for='name'>Name:</label><input type='text' id='name' value=''/>
		<label for='phone'>Phone:</label><input type='text' id='phone' value=''/>
		<label for='cell'>Cell:</label><input type='text' id='cell' value=''/>
		
		<div id='preview'></div>
		<textarea id='code' readonly></textarea>
		<button type="button" onclick='refreshSignature();'>Click Me!</button> 
		
		<script type="text/javascript">
			var signature = "";
			
			function refreshSignature(){
				signature = "<div style='font-family:verdana,sans-serif;color:#444444;width:1000px;'>" +
					"<div style='font-size: 10pt;color:#231f20;font-weight: bold;margin-bottom:3px;border-top:1px solid #c0c0c0;padding-top:15px;display:inline-block'>" +
						document.getElementById('name') + "</div>" +
					"<div><span style='font-size: 11px;'><?php echo 'jobtitle' ?><span style='color:#c0c0c0;'>|</span><?php echo 'department' ?>" +
					"</span></div>" +
					"<div><span style='font-size: 11px;'>T&#x2e; ";
				var phone = document.getElmentById('phone');
				if (phone.trim() = ""){
					signature += "ptcphone";
				}
				else {
					signature += phone;
				}
				signature += "<span style='color:#c0c0c0;'>|</span>";
				var cell = document.getElmentById('cell');
				if (cell.trim() = ""){
					signature += "Toll Free: 1&#x2e;855.722&#x2e;4483";
				}
				else {
					signature += "Cell: " + cell;
				}
				signature += "</span></div>" +
					"<div ><a href='http://powertochange.org/' target='_blank'><img src='http://powertochange.com/wp-content/uploads/2014/07/P2C-Logo-Email.png' height='80'  /><img src='http://powertochange.com/wp-content/uploads/" +
					"<?php echo 'section'?>' height='80'  /></a></div></div></div>";
				document.getElementById('preview').innerHTML = signature;
				document.getElementById('code').innerHTML = signature;

			}
		</script>
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