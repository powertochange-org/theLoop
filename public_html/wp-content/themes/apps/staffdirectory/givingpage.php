<?php
/**
* Giving Page
*
*
**/

//SO_API::getProduct(93);

?>
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/jquery.Jcrop.css" type="text/css" />
<link href="<?php echo get_stylesheet_directory_uri(); ?>/staffdirectory/givingpage-sample.css" rel="stylesheet" type="text/css" />
<link href="<?php echo get_stylesheet_directory_uri(); ?>/staffdirectory/givingpage.css" rel="stylesheet" type="text/css" />
<!-- link rel="stylesheet" href="https://secure.powertochange.org/App_Themes/Skin_1/Q_style_25.css" type="text/css" / -->
<link href="https://secure.powertochange.org/App_Themes/Skin_1/aspira.css" type="text/css" rel="stylesheet">
<script>
$(document).ready(function() {
	givingpage.ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';
	givingpage.init('<?php echo get_option(Givingpage::$prefix.'soServer');?>');
	//givingpage_s.init('http://secure.adv-01d0986.powertochange.local');
});

</script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.Jcrop.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/staffdirectory/givingpage.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/staffdirectory/givingpage-sample.js"></script>
<script src="https://secure.powertochange.org/jscripts/ptc.js" ></script>
<script src="https://secure.powertochange.org/jscripts/ptc_op.js" ></script>
<script src="https://secure.powertochange.org/jscripts/list.aspx" ></script>
<hr style='clear:both'>
<div id='input'>
	<div><span>ProjectCode:</span><span class='projectcode' value='loading...'></span></div>
	<div><span>Link:</span><span class='link'></span></div>
	<div><span>Amount:</span><input class='amount' type='text' /></div>
	<div><span>Frequency:</span><label><input class='frequency ot' name='frequency' type='radio' value='ot' />One Time</label><label><input class='frequency m' name='frequency' type='radio' value='m' />Monthy</label></div>
	<div><span>Picture:</span><input type="file" class="image" accept="image/png,image/gif,image/jpeg"><img /></div>
	<div><span>Closed Country:</span><input class='closed' type='checkbox' /></div>
	<div><span>Description:</span><textarea class='description'></textarea></div>
	<div><span>Description (French):</span><textarea class='description-french'></textarea></div>
	<div><span>E Ack Letter:</span><textarea class='eAck'></textarea></div>
	<div><span>E Ack Letter (French):</span><textarea class='eAck-french'></textarea></div>
	<div><span>Preview:</span><label><input class='preview' name='preview' type='radio' value='en-US' />English</label><label><input class='preview' name='preview' type='radio' value='fr-CA' />French</label></div>
	<button>Save</button>
</div>
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
					<span>(<span class='lang-tran'>ptc.clickToChange</span>)</span>
				</label>
				<div class='frequency border'>
					<input id="onetime" name="donationFrequency" onclick="givingpage_s.adjustDateView(false);" type="radio" value="O"  />
					<label for='onetime'>  <span class='lang-tran'>SO.DonationFrequency.OneTime</span></label>
					<input id="monthly" name="donationFrequency" onclick="givingpage_s.adjustDateView(true);" type="radio" value="M" checked="checked"/>
					<label for='monthly'><span class='lang-tran'>SO.DonationFrequency.Monthly</span></label>
				</div>

				<div class='recurring border'><!-- recurring info -->
					<span><span class='lang-tran'>DS.DateLabel</span></span>
					<input id="month1" name="donationDayOfMonth" type="radio" onchange="givingpage_s.changeDateRange(this.value)" value="1" checked="checked"  />
					<label id='lmonth1' for='month1'>  <span class='lang-tran'>1ofMonth</span></label>
					<input id="month15" name="donationDayOfMonth" type="radio" onchange="givingpage_s.changeDateRange(this.value)" value="15" />
					<label for='month15'><span class='lang-tran'>15ofMonth</span></label><br />
					<span><span class='lang-tran'>orders.aspx.StartDate</span></span>
					<input id="txtStartDate" name="txtStartDate" type="text" value="" maxlength="10" size="10" />
				</div>
				<div class='options border'>
					<div><span class='lang-tran'>ptc.selectOption</span></div>
					<select id='options' onchange='ptc_op.changeSelected($(this).val());'></select>
				</div>
				<div class='recurring'>
					<input type="checkbox" id="perm" />
					<label for="perm"><span class='lang-tran'>Permission</span></label>
				</div>
				<a class="button5L"><span class='lang-tran'>DS.BtnProject</span></a>
				<div class='clear'></div>
			</div>
		</div>
		<div id='description'>
		</div>
	</div>
</div>
</div>