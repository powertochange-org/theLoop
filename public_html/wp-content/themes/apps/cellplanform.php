<?php
/*
*Template Name: zApp CellPlan
*
*A calculator that is used to calculate the cost for different cell plans.
*
*/
?>
<?php get_header(); ?>
	<link href="<?php echo get_stylesheet_directory_uri(); ?>/staff-directory-style.css" rel="stylesheet" type="text/css" />
	<div id="content" class='staff-d'>
		<?php if (have_posts()) : while (have_posts()) : the_post();  ?>
			<h1 style="float:left;"><a style="font-size:35pt;font-family:Roboto Slab;font-weight:100;" href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
			<div class="entry">
				<br><br><div style="clear:both;"></div>
				<p>
P2C has a staff discount available to purchase Apple hardware outright from the Apple ecommerce store. Prices can be found in (<a href="https://staff.powertochange.org/uncategorized/telus-helping-you-choose-your-phone-and-plan/">https://staff.powertochange.org/uncategorized/telus-helping-you-choose-your-phone-and-plan/</a>).<br>
<br>You can sign up for the TELUS plan at <a href="http://p2c.sh/telus-signup">p2c.sh/telus-signup</a></p>
				<br>
				<h3>Select plan</h3>
				<select id="phoneplan" onchange="updateDevices();resetCost();calculatePlan();">
					<option value="3GB">3GB</option>
					<option value="5GB">5GB</option>
					<option value="voice">Voice</option>
				</select>
				
				<br><br>
				
				<h3>Brand</h3>
				<select id="phonebrand" onchange="updateDevices();resetCost();calculatePlan();">
					<option value="Apple">Apple</option>
					<option value="Samsung">Samsung</option>
					<option value="Android">Android</option>
					<option value="Other">Other</option>
				</select>
				<br><br><h3>Device</h3>
				<select id="selectedphone" onchange="resetCost();calculatePlan();">
<option value="iPhone SE 16GB">iPhone SE 16GB</option>
<option value="iPhone SE 64GB">iPhone SE 64GB</option>
<option value="iPhone 6s 32GB">iPhone 6s 32GB</option>
<option value="iPhone 6s 128GB">iPhone 6s 128GB</option>
<option value="iPhone 6s Plus 32GB">iPhone 6s Plus 32GB</option>
<option value="iPhone 6s Plus 128GB">iPhone 6s Plus 128GB</option>
<option value="iPhone 7 32GB">iPhone 7 32GB</option>
<option value="iPhone 7 128GB">iPhone 7 128GB</option>
<option value="iPhone 7 256 GB">iPhone 7 256 GB</option>
<option value="iPhone 7 Plus 32GB">iPhone 7 Plus 32GB</option>
<option value="iPhone 7 Plus 128GB">iPhone 7 Plus 128GB</option>
<option value="iPhone 7 Plus 256GB">iPhone 7 Plus 256GB</option>


<option value="Galaxy S7 32GB">Galaxy S7 32GB</option>
<option value="Galaxy S7 Edge 32GB">Galaxy S7 Edge 32GB</option>
<option value="Galaxy J3">Galaxy J3</option>


<option value="Google Pixel 32GB">Google Pixel 32GB</option>
<option value="Google Pixel 128GB">Google Pixel 128GB</option>
<option value="Google Pixel XL 32GB">Google Pixel XL 32GB</option>
<option value="LG G5">LG G5</option>
<option value="LG X Power">LG X Power</option>
<option value="Moto G Play">Moto G Play</option>
<option value="Moto G Plus">Moto G Plus</option>
<option value="Moto Z">Moto Z</option>


<option value="Blackberry Classic">Blackberry Classic</option>
<option value="Blackberry DTEK 50">Blackberry DTEK 50</option>
<option value="Alcatel A392CC">Alcatel A392CC</option>
					
					<!--<option value="Galaxy S7 32GB">Galaxy S7 32GB</option>
					<option>iPhone 6S</option>-->
				</select>
				<br><br>
				<!--<input type="checkbox" id="unlocked" onchange="resetCost();calculatePlan();">Unlocked-->
				<br><br>
				
				
				
				<h3>Total Cost of Ownership (Subsidized)</h3>
				<p id="displayCOST"></p>
				
				<br>
				<h3>Total Cost of Ownership (BYOD)</h3>
				<p id="displayBYOD"></p>
				<br><br>
				
				<button type="button" onclick="calculatePlan();">Calculate</button>
				<br><br>
			</div>
			
		
		
		<script>
			
			
var phoneCost = [];
				
				
phoneCost['iPhone SE 16GB'] = {term:0,outright:590,discount:567,brand:"Apple"};
phoneCost['iPhone SE 64GB'] = {term:30,outright:645,discount:616,brand:"Apple"};
phoneCost['iPhone 6s 32GB'] = {term:70,outright:775,discount:754,brand:"Apple"};
phoneCost['iPhone 6s 128GB'] = {term:200,outright:915,discount:881,brand:"Apple"};
phoneCost['iPhone 6s Plus 32GB'] = {term:200,outright:915,discount:881,brand:"Apple"};
phoneCost['iPhone 6s Plus 128GB'] = {term:330,outright:1055,discount:1008,brand:"Apple"};
phoneCost['iPhone 7 32GB'] = {term:200,outright:915,discount:881,brand:"Apple"};
phoneCost['iPhone 7 128GB'] = {term:330,outright:1055,discount:1008,brand:"Apple"};
phoneCost['iPhone 7 256 GB'] = {term:460,outright:1195,discount:1136,brand:"Apple"};
phoneCost['iPhone 7 Plus 32GB'] = {term:350,outright:1075,discount:1028,brand:"Apple"};
phoneCost['iPhone 7 Plus 128GB'] = {term:480,outright:1215,discount:1155,brand:"Apple"};
phoneCost['iPhone 7 Plus 256GB'] = {term:600,outright:1345,discount:1283,brand:"Apple"};


phoneCost['Galaxy S7 32GB'] = {term:170,outright:900,discount:0,brand:"Samsung"};
phoneCost['Galaxy S7 Edge 32GB'] = {term:300,outright:1000,discount:0,brand:"Samsung"};
phoneCost['Galaxy J3'] = {term:0,outright:240,discount:0,brand:"Samsung"};


phoneCost['Google Pixel 32GB'] = {term:200,outright:900,discount:0,brand:"Android"};
phoneCost['Google Pixel 128GB'] = {term:330,outright:1030,discount:0,brand:"Android"};
phoneCost['Google Pixel XL 32GB'] = {term:350,outright:1050,discount:0,brand:"Android"};
phoneCost['LG G5'] = {term:0,outright:800,discount:0,brand:"Android"};
phoneCost['LG X Power'] = {term:0,outright:240,discount:0,brand:"Android"};
phoneCost['Moto G Play'] = {term:0,outright:240,discount:0,brand:"Android"};
phoneCost['Moto G Plus'] = {term:0,outright:410,discount:0,brand:"Android"};
phoneCost['Moto Z'] = {term:220,outright:920,discount:0,brand:"Android"};


phoneCost['Blackberry Classic'] = {term:0,outright:550,discount:0,brand:"Other"};
phoneCost['Blackberry DTEK 50'] = {term:0,outright:450,discount:0,brand:"Other"};
phoneCost['Alcatel A392CC'] = {term:0,outright:80,discount:0,brand:"Other"};
			
			function calculatePlan() {
				var planCost = {threeGB:49, fiveGB:56, threeGBBYOD:35,  fiveGBBYOD:42, voice:25,voiceBYOD:25};
				var phoneplan = document.getElementById('phoneplan').value;
				
				var selectedphone = document.getElementById('selectedphone').value;
				/*var unlocked = document.getElementById('unlocked').checked;*/
				var unlocked = false;
				var unlockedCost = 0;
				if(!unlocked) {
					unlockedCost = 0;
				}
				
				var planindex = "";
				if(phoneplan == "3GB") {
					planindex = "threeGB";
				} else if(phoneplan == "5GB") {
					planindex = "fiveGB";
				} else {
					planindex = "voice";
				}
				
				var TCOdiscount = 350;
				var BYODdiscount = 600;
				
				
				var term = phoneCost[selectedphone]['term'];
				var outright = phoneCost[selectedphone]['outright'];
				
				if(planindex == "voice") {
					TCOdiscount = 100;
					BYODdiscount = 0;
					term = 50;
				}
				
				var TCOOcost = term - TCOdiscount + (planCost[planindex]*36) + unlockedCost;
				var BYODcost = outright - BYODdiscount + (planCost[(planindex + "BYOD")]*36) + unlockedCost;
				
				
				var planDetails = '<table id="breakdown"><tr><th>Device Cost</th><th>Plan Cost</th><th>Activation Credit</th></tr>';
				
				
				document.getElementById('displayCOST').innerHTML = "$"+TCOOcost + planDetails + 
					'<tr><td>' + term + '</td><td>'+planCost[planindex]+'</td><td>'+TCOdiscount+'</td></tr>' + '</table>';
				
				document.getElementById('displayBYOD').innerHTML = "$"+BYODcost + planDetails + 
					'<tr><td>' + outright + '</td><td>'+planCost[planindex]+'</td><td>'+BYODdiscount+'</td></tr>' + '</table>';
				
				
				if(phoneCost[selectedphone]['brand'] == "Apple") {
					var BYODcostAPPLE = phoneCost[selectedphone]['discount'] - BYODdiscount + (planCost[(planindex + "BYOD")]*36) + unlockedCost;
					document.getElementById('displayBYOD').innerHTML += "<br>" + "Apple Discount: $" 
						+ BYODcostAPPLE + planDetails + 
					'<tr><td>' + phoneCost[selectedphone]['discount'] + '</td><td>'+planCost[planindex]+'</td><td>'+BYODdiscount+'</td></tr>' + '</table>';;
				}
			}
			
			function updateDevices() {
				var phonebrand = document.getElementById('phonebrand').value;
				var x = document.getElementById("selectedphone");
				
				for(var c = x.length-1; c >= 0; c--) {
					x.remove(c);
				}
				for(var phone in phoneCost) {
					
					
					if(phoneCost[phone]['brand'] == phonebrand) {
						var phoneplan = document.getElementById('phoneplan').value;
						if(phoneplan == "voice" && !(phone == "Galaxy J3" || phone == "LG X Power")) {
							continue;
						}
						var option = document.createElement("option");
						option.text = phone;
						option.value = phone;
						x.add(option);
					}
				}
				
			}
			
			function resetCost() {
				document.getElementById('displayCOST').innerHTML = "$";
				document.getElementById('displayBYOD').innerHTML = "$";
			}
			
			updateDevices();
		</script>
		
		
		<?php endwhile; else: ?>
		<h2>404 - Not Found</h2>
		<p>The page you are looking for is not here.</p>					 
		<?php endif; ?>
	</div>
	<!--content end-->
	<!--Popup window-->
    </div>
    <!--main end-->
</div>
<!--wrapper end-->
<div style='clear:both;'></div>
<?php get_footer(); ?>
<style>
	select, button {
		font-size: 20px;
	}
	
	#breakdown td, th {
		border: 1px solid black;
	}
	
</style>