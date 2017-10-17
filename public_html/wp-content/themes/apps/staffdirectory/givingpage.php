<?php
require_once(get_stylesheet_directory().'/functions/functions.php');

/**
* Giving Page
* 
* Author: matthew.chell
*
* This part of staff directory allows staff to edit there givingpage
*
* This page has several dependency:
*	-StudioOnline
*		>stylesheets
*		>javascripts
*		>Storefront webservice
*		>Our webservice
*	-Our StudioEnterprise webservice
**/

?>
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/jquery.Jcrop.css" type="text/css" />
<link href="<?php echo get_stylesheet_directory_uri(); ?>/staffdirectory/givingpage-sample.css" rel="stylesheet" type="text/css" />
<link href="<?php echo get_stylesheet_directory_uri(); ?>/staffdirectory/givingpage.css" rel="stylesheet" type="text/css" />
<link href="<?php echo get_option(Givingpage::$prefix.'soServer');?>/App_Themes/Skin_1/aspira.css" type="text/css" rel="stylesheet">
<script>
$(document).ready(function() {
	givingpage.ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';
	givingpage.init('<?php echo get_option(Givingpage::$prefix.'soServer');?>');
});

</script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.Jcrop.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/staffdirectory/givingpage.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/staffdirectory/givingpage-sample.js"></script>
<script src="<?php echo get_option(Givingpage::$prefix.'soServer');?>/jscripts/ptc.js" ></script>
<script src="<?php echo get_option(Givingpage::$prefix.'soServer');?>/jscripts/ptc_op.js" ></script>
<script src="<?php echo get_option(Givingpage::$prefix.'soServer');?>/jscripts/list.aspx?r=<?php echo rand() ?>" ></script>
<hr style='clear:both'>
<?php  echo needOptions(array(
	Givingpage::$prefix.'soServer',
	Givingpage::$prefix.'seWebService',
	Givingpage::$prefix.'soServer_User',
	Givingpage::$prefix.'soServer_Pass'
)) ?>
<h2>Staff Donation Page</h2>
<div class='input'>
	<div><span>ProjectCode:</span><span class='projectcode'>loading...</span></div>
	<div><span>Link:</span><span class='link'></span></div>
	<div><span>Amount:</span><input class='amount' type='text' /></div>
	<div><span>Frequency:</span><label><input class='frequency ot' name='frequency' type='radio' value='ot' />One Time</label><label><input class='frequency m' name='frequency' type='radio' value='m' />Monthy</label></div>
	<div><span>Picture:</span><input type="file" class="image" accept="image/png,image/gif,image/jpeg"><img /></div>
	<div><span>Closed Country:</span><input class='closed' type='checkbox' /></div><br />
	<div><span>Description (English):</span><textarea class='description'></textarea></div>
	<div><span>Description (French):</span><textarea class='description-french'></textarea></div>
</div>
<br />
<div><span>Preview:</span><label><input class='preview' name='preview' type='radio' value='en-US' />English</label><label><input class='preview' name='preview' type='radio' value='fr-CA' />French</label></div>
<div id="sample">
	<div id='don-box'>
		<div class='max-width'>
			<div id='logo'></div>
			<h1 id='title'></h1>
			<div class='box'><!-- donation box -->
				<img id="project_pic" class="round" />
				<div id='tab1'>
					<label class='amount'>
						<label for='txtDonationAmount'>$</label>
						<input id="txtDonationAmount" onfocus='if("none" != $(this).siblings("span").css("display"))$(this).val("");$(this).siblings("span").hide();' name="txtDonationAmount" type="text" value="0.00" onblur="givingpage_s.balanceAmount(this);" maxlength="10" size="10" />
						<span>(<span class='lang-tran' data-tran-word='ptc.clickToChange'></span>)</span>
					</label>
					<div class='frequency border'>
						<input id="onetime" name="donationFrequency" onclick="givingpage_s.adjustDateView(false);" type="radio" value="O"  />
						<label for='onetime' class='lang-tran' data-tran-word='SO.DonationFrequency.OneTime'></label>
						<input id="monthly" name="donationFrequency" onclick="givingpage_s.adjustDateView(true);" type="radio" value="M" checked="checked"/>
						<label for='monthly' class='lang-tran' data-tran-word='SO.DonationFrequency.Monthly'></label>
					</div>

					<div class='recurring border'><!-- recurring info -->
						<span class='lang-tran' data-tran-word='DS.DateLabel'></span>
						<input id="month1" name="donationDayOfMonth" type="radio" onchange="givingpage_s.changeDateRange(this.value)" value="1" checked="checked"  />
						<label id='lmonth1' for='month1' class='lang-tran' data-tran-word='1ofMonth'>1ofMonth</label>
						<input id="month15" name="donationDayOfMonth" type="radio" onchange="givingpage_s.changeDateRange(this.value)" value="15" />
						<label for='month15' class='lang-tran' data-tran-word='15ofMonth'></label><br />
						<span class='lang-tran' data-tran-word='orders.aspx.StartDate'></span>
						<input id="txtStartDate" name="txtStartDate" type="text" value="" maxlength="10" size="10" />
					</div>
					<div class='options border'>
						<div class='lang-tran' data-tran-word='ptc.selectOption'></div>
						<select id='options' onchange='ptc_op.changeSelected($(this).val());'></select>
					</div>
					<div class='recurring'>
						<input type="checkbox" id="perm" />
						<label for="perm" class='lang-tran' data-tran-word='Permission'></label>
					</div>
					<a class="button5L lang-tran" data-tran-word='DS.BtnProject'></a>
					<div class='clear'></div>
				</div>
			</div>
			<div id='description'></div>
		</div>
	</div>
</div>
<br />
<div class='input'>
	<button class='save'>Save</button>
</div>
<br />
<h2>Staff Email Acknowledgement Letter to Donor</h2>
<div class='input'>
	<div><span>E Ack Letter (English):<br /><br />(Do not include the salutation of the letter.)</span><textarea class='eAck'></textarea></div>
	<div><span>E Ack Letter (French):<br /><br />(Do not include the salutation of the letter.)</span><textarea class='eAck-french'></textarea></div>
</div>
<br />
<div><span>Preview:</span><label><input class='preview' name='preview' type='radio' value='en-US' />English</label><label><input class='preview' name='preview' type='radio' value='fr-CA' />French</label></div>
<div id="letter" style="font-family: Calibri, Arial, sans-serif;color:#000000; margin:0">
	<div style="width:630px">
		<img class="staffPic" style='height: 210px;' />
		<img style="width:420px; height: 210px; margin-left: -5px;" />
		<div style="padding-bottom:3px;	border-bottom:1px solid #adafb2;margin-bottom:3px;">
			<p class='lang-tran merge' data-tran-word='ptc.salutation'></p>
			<span id='staffLetter' class='merge'></span>
			<div style='clear:both;'></div>
		</div>
	</div>
</div>
<br />
<div class='input'>
	<button class='save'>Save</button>
</div>