<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//$spawn_preview_window = false;

//print_r($_GET);
//if(isset($_GET['do_preview']))
//{
//	$spawn_preview_window = true;
//}

require_once(dirname(__FILE__) . "/../classes/class-easy-pie-utility.php");

$active = EZP_MM_U::get_option_value("enabled");
?>
<style>
    p.submit { padding-bottom: 12px!important; }
	.postbox .inside {margin-bottom: 6px}
	.form-table th{padding: 8px 8px 8px 25px}
	.form-table th[scope=row]{width: 150px}
	.form-table td{padding: 3px 0 3px 0}
	
	/*Section Titles*/
	h3.ezp-mm-subtitle {border-bottom: 1px solid #dfdfdf;  padding:0 0 6px 8px; margin:-3px 0 0 -10px; font-size:17px; font-weight: bold }
	form#easy-pie-mm-advanced-form h2  { font-weight: bold; font-size:15px; margin-top:15px; }
	div.bx-wrapper {max-width:400px; width:400px}
	div.bx-controls {max-width:400px; width:400px}
	div#major-publishing-actions p.submit {text-align: right !important; padding:0 5px}
	div#major-publishing-actions {margin: 10px -12px -24px -12px; padding:0}
	
	#ezp-mm-container {width: 100%; margin: 0 auto; }
	#ezp-mm-content {width: 100%; }
	#ezp-mm-sidebar {width: 100%;}
	#ezp-mm-sidebar-pad {padding:0}
	
	/*Window: Above 1200px*/
	@media only screen and (min-width:1200px) {
		#ezp-mm-content {width:68%; float:left; }
		#ezp-mm-sidebar {width:32%; float:right; min-width:280px; }
		#ezp-mm-sidebar-pad {margin:0 5px 0 10px;}
	}
	/*Window: Above 1400px*/
	@media only screen and (min-width:1400px) {
		#ezp-mm-content {width:70%; float:left; }
		#ezp-mm-sidebar {width:30%; float:right; min-width:280px; }
	}
	/*Window: Above 1600px*/
	@media only screen and (min-width:1600px) {
		#ezp-mm-content {width:75%; float:left; }
		#ezp-mm-sidebar {width:25%; float:right; min-width:280px; }
	}
	/*Window: Above 1800px*/
	@media only screen and (min-width:1800px) {
		#ezp-mm-content {width:80%; float:left; }
		#ezp-mm-sidebar {width:20%; float:right; min-width:280px; }
	}
</style>

<div class="wrap">
<?php screen_icon(Easy_Pie_MM_Constants::PLUGIN_SLUG); ?>
<h2><?php Easy_Pie_MM_Utility::_e('EZP Maintenance Mode'); ?></h2>
<?php
	if (isset($_GET['settings-updated'])) {
		echo "<div class='updated'><p>" . Easy_Pie_MM_Utility::__('If you have a caching plugin, be sure to clear the cache!') . "</p></div>";
	}
	$option_array = get_option(Easy_Pie_MM_Constants::OPTION_NAME);
?>
<form method="post" action="options.php" id="easy-pie-mm-advanced-form"> 
	<input type="hidden" id="ezp-action" name="ezp-action" value=""/>
	<div id="ezp-mm-container">
		<div id="ezp-mm-content">
			<!-- ===================================
			BASIC  -->
			<div class="postbox"  style="margin-top:10px;">
			<div class="inside">
				<h3 class="ezp-mm-subtitle"><?php Easy_Pie_MM_Utility::_e("Basic") ?></h3>
				<?php
					settings_fields(Easy_Pie_MM_Constants::MAIN_PAGE_KEY);
					do_settings_sections(Easy_Pie_MM_Constants::MAIN_PAGE_KEY);
				?>				
			</div>
			</div>

			<!-- ===================================
			ADVANCED  -->
			<div class="postbox"  style="margin-top:10px;">
			<div class="inside">
				<h3 style="cursor:pointer" onclick="easyPie.MM.toggleAdvancedBox()" class="ezp-mm-subtitle"><?php Easy_Pie_MM_Utility::_e("ADVANCED") ?></h3>
				<table id="easy-pie-mm-advanced" style="display:none" class="form-table">
					<tr valign="top">
						<th scope="row"><?php Easy_Pie_MM_Utility::_e("Custom CSS") ?></th>
						<td>
							<textarea cols="67" rows="9" id="easy-pie-mm-field-junk" name="easy-pie-mm-options[css]"><?php echo $option_array["css"]; ?></textarea>
							<p>
								<small><strong><?php Easy_Pie_MM_Utility::_e("Page styling varies greatly. ")?></strong>
								<?php Easy_Pie_MM_Utility::_e("Update custom CSS when switching mini-themes."); ?></small>
							</p>
						</td>
					</tr>      
				</table>
			</div>
			</div>
		</div> 

		<div id="ezp-mm-sidebar"  >
			<div id="ezp-mm-sidebar-pad">
				<!-- ===================================
				GO PRO  -->
				<div class="postbox"  style="margin-top:10px;">
					<div class="inside" style="line-height:25px">
					<h3 class="ezp-mm-subtitle"><?php Easy_Pie_MM_Utility::_e("Publish") ?></h3>
					<div style="text-align:right; margin:10px 0">
						<button class="button" onclick="EZP.MM.saveAndPreview(); return false; ">Preview Changes</button>
					</div>

					<?php if ($active) :?>
					<b><?php Easy_Pie_MM_Utility::_e("Status") ?>:</b> <span style="color:red">Active</span> <br/>
<!--						<b><?php Easy_Pie_MM_Utility::_e("Active On") ?>:</b> Mar 10, 2016 @ 17:01-->
					<?php else :?>
						<b><?php Easy_Pie_MM_Utility::_e("Status") ?>:</b> <span style="color:green">Inactive</span> <br/>
<!--						<b><?php Easy_Pie_MM_Utility::_e("Inactive On") ?>:</b> Mar 10, 2016 @ 17:01-->
					<?php endif;?>
					<br/><br/>

					<div id="major-publishing-actions" class="ezp-mm-pub-save">
						<?php submit_button();  ?>
					</div>

				</div>
				</div>					
				<!-- ===================================
				GO PRO  -->
				<div class="postbox"  style="margin-top:10px;">
				<div class="inside">
					<h3 class="ezp-mm-subtitle"><?php Easy_Pie_MM_Utility::_e("Go Professional") ?></h3>
					<div style="text-align:center; font-style: italic; margin:10px 0 -5px 0">
						<?php Easy_Pie_MM_Utility::_e("With Coming Soon Elite!") ?>
					</div>

					<ul style="font-size:15px">
						<li>Custom Backgrounds</li>
						<li>Collect Emails</li>
						<li>MailChimp Autosync</li>
						<li>Grant Site Access</li>
						<li>Social Media Links </li>
						<li>Video Backgrounds</li>
						<li>Countdown Timer</li>
						<li>Fully Responsive</li>
						<li><a href="https://snapcreek.com/ezp-coming-soon/" target="_blank">And much more!</a></li>
					</ul>
				</div>
				</div>

				<!-- ===================================
				RATE  -->
				<div class="postbox"  style="margin-top:10px;">
				<div class="inside">
					<h3 class="ezp-mm-subtitle"><?php Easy_Pie_MM_Utility::_e("Rate Maintenance Mode") ?></h3>
					<div style="padding: 8px">
						<?php Easy_Pie_MM_Utility::_e("If you have benefited from this plugin please consider leaving us a 5 star review!") ?><br/>
						<a target="_blank" href="https://wordpress.org/support/plugin/easy-pie-maintenance-mode/reviews/">Rate Us Here!</a>
						<br/><br/>
						<?php Easy_Pie_MM_Utility::_e("Cheers~") ?>
					</div>
				</div>
				</div>					
			</div>
		</div>

		<br style="clear:both"/>
		<?php //submit_button();  ?>

		<a href="https://snapcreek.com/ezp-coming-soon/docs/faq-maintenance-mode/" target="_blank"><?php $this->_e('Plugin FAQ'); ?></a> |
		<a href="mailto:support@snapcreek.com" target="_blank"><?php $this->_e('Contact'); ?></a>
	</div>
	
	<script>
	EZP = { };
	EZP.MM = { };
	
	jQuery(document).ready(function ($) {

		EZP.MM.getCookie = function(cname) {
			
			var name = cname + "=";
			var ca = document.cookie.split(';');
			for(var i = 0; i <ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') {
					c = c.substring(1);
				}
				if (c.indexOf(name) == 0) {
					return c.substring(name.length,c.length);
				}
			}
			
			return "";
		};
		
		EZP.MM.setCookie = function (cname, cvalue, exdays) {
			
			var d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			var expires = "expires="+ d.toUTCString();
			document.cookie = cname + "=" + cvalue + "; " + expires;
		};
		
		
		EZP.MM.saveAndPreview = function() {
					
			EZP.MM.setCookie("ezp_mm_preview", "true", 1);
						
			$("#submit").trigger("click");
		};
				
		var previewCookie = EZP.MM.getCookie("ezp_mm_preview") === "true";
				
		if (previewCookie) 
		{
			EZP.MM.setCookie("ezp_mm_preview", "false", 3650);
														
			window.open('<?php echo get_site_url() . "?ezp_mm_preview=true" ?>').focus();
		}	
	});
  
	
</script>
</form>
</div>


	