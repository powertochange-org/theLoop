<?php
/*
*Template Name: zApp Allowance Calculator
*
*Author: matthew.chell
*
*Description: A calculator to find out how much allowance a person has to raise.

req:
	tables-allowance-question/answer
		  -support constant
		  -string constant
	
	ueses wordpress users_meta


*
*/

/* ****NOTE: HARDCODED VALUES****
there is one spot that has hardcoded numbers since, for two questions
		(such as What is the scope of ministry operations for which the staff member is  responsible? 
		(which what level a person is)), for a specific person (such as for you or your spouse), you can not choose the answer
		but for anyone you can choose the answer for the question.
	since the question uses hardcoded numbers then some of the edit capabilities have been disable in the admin interface
		from the admin interface you should not be able to change anything that would need the hardcoded numbers to change
	the hardcoded values are in function setUserValues()
	
	
	
the number of hours question is different from the rest of the questions.  From the admin interface you can
	only change the label of the question.  The answer is open ended.  It does not have predefined answer
	like the other question.  Also it is used to pro rate the result calculated by the other questions.
*/

require('pdf/fpdf.php');
require('calculators/allowance-calculator-functions.php');
include('functions/functions.php');

$current_user_id = wp_get_current_user()->id;
		
parseUserValuesInput();

include('functions/js_functions.php');

?>
<?php get_header(); ?>
<div id="content">
	<h1 class="replace"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
	<hr>
    <div id="content-left">
	<div id="main-content">
		<script src="https://code.jquery.com/jquery-latest.js"></script>
		<style type="text/css">
			table {
				border-collapse: separate;
				border-spacing:10px;
			}
			
			th {
				background-color:#eeeeee;
				padding:5px 0;
			}
		
			td {
				text-align:left;
				vertical-align:middle;
			}
			.button {
				width:auto;
				border:0;
				background-color:rgba(0,0,0,0);
			}
			
			#main-content h2, #main-content h3 , #main-content form , #main-content lu, #main-content h1, #content h1{
				margin-left:0;
			}
			
			#main-content h2{
				font-size:150%;
				margin-bottom:10px;
			}
			
			#admin_view hr{
				margin:0;
			}
			
			#main-content * {
				font-size: 12pt;
			}
			
			#main-content strong {
				margin-bottom:10px;
				display:block;
			}
			@media (max-width: 767px) {
				#main-content div {
					margin: 0 10px;
				}
				
			}
		</style>
		<?php 	
		
		if (isAppAdmin('support_calculator_admin', 0)){
			include('calculators/allowance-calculator-admin.php');
		}		
		
		if (getAccess($current_user_id) == $allowance_constant['noAccess']){
			?>
			The Allowance Calculator is only available for Commissioned and Associate staff.
			<?php
		}
		else {
		
			?>
			
			<script type="text/javascript">
				
				var YOU = 0;
				var SPOUSE = 1;
				var FREE = 2;
				
				var FIELD_INDIVIDUAL = <?php  echo $allowance_constant['fieldIndividual'] ?> ;
				var FIELD_LEADER = <?php  echo $allowance_constant['fieldLeader'] ?> ;
				var CORPORATE_INDIVIDUAL = <?php  echo $allowance_constant['corporateIndividual'] ?> ;
				var CORPORATE_LEADER = <?php  echo $allowance_constant['corporateLeader'] ?> ;
				
				var ANSWERS = <?php getAnswers() ?>;
				var POINTS = <?php getPoints() ?>;
				var MAX_POINTS = <?php getMaxPoints() ?>;
				var MIN_MAX = <?php getMinMax() ?>;
				
				function getMinMax(role, level){
					if (role == FIELD_INDIVIDUAL || role == CORPORATE_INDIVIDUAL){
						return MIN_MAX[role];
					}
					else {
						return MIN_MAX[role][level];
					}
				}
			
				var BLURBS = {whichWay: '<?php echo changeNL(getStringConstant("blurb_0", "'")) ?>',
						result: '<?php echo changeNL(getStringConstant("blurb_2", "'"))?>'};
						
			
				function showSection(section){
					$('html,body').scrollTop(0);
				
					//hide all
					document.getElementById('section_whichWay').style.display  = "none";
					//document.getElementById('section_enterAll').style.display  = "none";
					document.getElementById('section_result').style.display  = "none";
					//show the one
					document.getElementById('section_' + section).style.display = "block";
					document.getElementById('blurb').innerHTML = BLURBS[section];
					 console.log("Show:" + section);
				}
				
				function showQuestions(role){
					//hide all
					$(".role0").hide();
					$(".role1").hide();
					$(".role2").hide();
					$(".role3").hide();
					
					//show stuff
					if (role > -1){
						$(".role" + role).show();
					}
					else {
						document.getElementById('name_project_code').style.display = "none";
						document.getElementById('hours').style.display = "none";
						document.getElementById('role_type_field').style.display = "none";
						document.getElementById('role_type_corp').style.display = "none";
					}
				}
				
				<?php if(getAccess($current_user_id) == $allowance_constant['fullAccess']) { ?>
				var you = {role:<?php echo getRole($current_user_id) ?>,
							name:"<?php echo getName() ?>", 
							min: '<?php echo getFieldEmployee('ministry') ?>', 
							title: '<?php echo getFieldEmployee('role_title') ?>', 
							projectCode: '<?php echo getFieldEmployee('staff_account') ?>', 
							setValues: <?php setUserValues($current_user_id) ?>};
				<?php }
				if (getSpouse() != -1 and getAccess(getSpouse()) == $allowance_constant['fullAccess']) { ?>
				var spouse = {role:<?php echo getRole(getSpouse());?>, 
								name:"<?php echo getName(getSpouse()) ?>", 
								min: '<?php echo getFieldEmployee('ministry', getSpouse()) ?>', 
								title: '<?php echo getFieldEmployee('role_title', getSpouse()) ?>', 
								projectCode: '<?php echo getFieldEmployee('staff_account', getSpouse()) ?>', 
								setValues: <?php setUserValues(getSpouse()) ?> };
				<?php } ?>
				
				var chooseWay = -1;
				
				function proceed(cw){
					chooseWay = cw
					switch(chooseWay){
					case YOU:
						showSomeFor(you);
						break;
					case SPOUSE:
						showSomeFor(spouse);
						break;
					case FREE:
						//show choose_role
						document.getElementById('user_name').innerHTML = "";
						document.getElementById('choose_role_div').style.display = "block";
						showQuestions(-1);
					break;
					}
				}
				
				function showSomeFor(who){
					document.getElementById('choose_role_div').style.display = "none";
					document.getElementById('name_project_code').style.display = "none";
					document.getElementById('hours').style.display = "block";
					document.getElementById('role_type_field').style.display = "none";
					document.getElementById('role_type_corp').style.display = "none";
					
					who.setValues();
					document.getElementById('user_name').innerHTML = getNameBlurb(who);
					showQuestions(who.role);
					$(".hidden").hide();
				}
				
				function getNameBlurb(who){
					var html = "Name: " + who.name + "<BR>";
					html += "Ministry/Department: " + who.min + "<BR>";
					html += "Project Code: " + who.projectCode + "<BR>";
					html += "Position Title: " + who.title + "<BR>";
					return html;
				}
				
				function select_role(){
					var role = parseInt(document.getElementById('choose_role').value);
					document.getElementById('name_project_code').style.display = "block";
					document.getElementById('hours').style.display = "block";
					document.getElementById('role_type_field').style.display = "none";
					document.getElementById('role_type_corp').style.display = "none";
					if (role == FIELD_LEADER){
						document.getElementById('role_type_field').style.display = "block";
					}
					else if (role == CORPORATE_LEADER){
						document.getElementById('role_type_corp').style.display = "block";
					}
					
					reset();
					showQuestions(role)
				}
				
				function getCheckTotal(checks){
					var total = 0;
					for(c in checks){
						if (checks[c] == "null"){
							continue;
						}
						else if(document.getElementById(checks[c]).checked){
							total += POINTS[document.getElementById(checks[c]).value];
						}
					}
					return total;
				}
				
				function getRadioTotal(radios){
					for(r in radios){
						if (radios[r] == "null"){
							continue;
						}
						else if(document.getElementById(radios[r]).checked){
							return POINTS[document.getElementById(radios[r]).value];
						}
					}
					return 0;
				}
				
				function getCheckAnswers(checks){
					var a = "";
					for(c in checks){
						if (checks[c] == "null"){
							continue;
						}
						else if(document.getElementById(checks[c]).checked){
							a += ANSWERS[document.getElementById(checks[c]).value] + "<BR>";
						}
					}
					return a;
				}
				
				function getRadioAnswers(radios){
					for(r in radios){
						if (radios[r] == "null"){
							continue;
						}
						else if(document.getElementById(radios[r]).checked){
							return ANSWERS[document.getElementById(radios[r]).value] + "<BR>";
						}
					}
					return '';
				}
				
				function calculate(){
					if ($('#hour_percentage').val() == '') {
						alert('Percentage of hours is a required field.');
						$('#hour_percentage').focus();
					} else {
						displayResult();
					}
				}
				
				function calculatePoints(role){
					<?php if(isAppAdmin('support_calculator_admin', 0)) { ?>
					document.getElementById('debug').innerHTML = "";
					<?php } ?>
					switch(role){
					case FIELD_INDIVIDUAL:
						return <?php getPointsEquation($allowance_constant['fieldIndividual']) ?>
						break;
					case FIELD_LEADER:
						return <?php getPointsEquation($allowance_constant['fieldLeader']) ?>
						break;
					case CORPORATE_INDIVIDUAL:
						return <?php getPointsEquation($allowance_constant['corporateIndividual']) ?>
						break;
					case CORPORATE_LEADER:
						return <?php getPointsEquation($allowance_constant['corporateLeader']) ?>
						break;
					}
					// showSelection('result');
				}
				
				function printV(who, v){
					console.log("%" + who + "%" + v);
					<?php if(isAppAdmin('support_calculator_admin', 0)) { ?>
					document.getElementById('debug').innerHTML += who + " " + v + "<BR>";
					<?php } ?>
					return v;
				}
				
				function displayResult(){
					switch(chooseWay){ 
					case YOU:
						var role = you.role;
						document.getElementById('buttonSave').style.display = "block";
						printResults(you, role);
						break;
					case SPOUSE:
						var role = spouse.role;
						document.getElementById('buttonSave').style.display = "block";
						printResults(spouse, role);
						break;
					case FREE:
						var role = parseInt(document.getElementById('choose_role').value);
						document.getElementById('buttonSave').style.display = "none";
						printResults(null, role);
						break;
					}
					var minMax = getMinMax(role, $('input[name=extra_level]:checked').val())
					var h =  get_value_float('hour_percentage');
					console.log(h);
					document.getElementById('output_minimum').innerHTML = number2currency(minMax.min * h / 100);
					document.getElementById('output_minimum_month').innerHTML = number2currency(minMax.min * h / 100 / 12);
					console.log(role);
					var p = calculatePoints(role);
					console.log(p);
					console.log(minMax.max - minMax.min);
					console.log((minMax.max - minMax.min) *  p / MAX_POINTS[role]);
					document.getElementById('output_maximum').innerHTML = number2currency((minMax.min + (minMax.max - minMax.min) *  p / MAX_POINTS[role]) * h / 100);
					document.getElementById('output_maximum_month').innerHTML = number2currency((minMax.min + (minMax.max - minMax.min) *  p / MAX_POINTS[role]) * h / 100 /12);
					showSection('result');
				}
				
				function printResults(who, role){
					html = "";
					if (who != null){
						html += getNameBlurb(who) + "<BR>";
					}
					else {
						html += "Name: " + document.getElementById('person_name').value + "<BR>";
						html += "Project Code: " + document.getElementById('projectCode').value + "<BR><BR>";
					}
					switch(role){
					case FIELD_INDIVIDUAL:
						html += <?php getSelectAnswers($allowance_constant['fieldIndividual']) ?>
						break;
					case FIELD_LEADER:
						html += <?php getSelectAnswers($allowance_constant['fieldLeader']) ?>
						break;
					case CORPORATE_INDIVIDUAL:
						html += <?php getSelectAnswers($allowance_constant['corporateIndividual']) ?>
						break;
					case CORPORATE_LEADER:
						html += <?php getSelectAnswers($allowance_constant['corporateLeader']) ?>
						break;
					}
					document.getElementById('printResult').innerHTML = html;
					
				
				}
				
				function saveUserValues(){
					switch(chooseWay){
					case YOU:
						document.getElementById('userIs').value = 'you';
						$.post( "",  $("#saveUserValues_form" ).serialize(), function( data ) {eval("you['setValues'] = " + data + ";"); alert("Saved!");});
						break;
					case SPOUSE:
						document.getElementById('userIs').value = 'spouse';
						$.post( "", $("#saveUserValues_form" ).serialize(), function( data ) { eval("spouse['setValues'] = " + data + ";"); alert("Saved!");});
						break;
					case FREE:
						break;
					}
				}
				
				function reset(){
					document.getElementById('user_name').innerHTML = "";
					document.getElementById('person_name').value = "";
					document.getElementById('projectCode').value = "";
					document.getElementById('extra-field-6').checked = false;
					document.getElementById('extra-field-7').checked = false;
					document.getElementById('extra-field-8').checked = false;
					document.getElementById('extra-corp-6').checked = false;
					document.getElementById('extra-corp-7').checked = false;
					document.getElementById('hour_percentage').value = "";
					<?php getReset() ?>
				}
				
				function download(){
					if (document.getElementById('input_effective').value == ""){
						alert("Please enter an effective date.")
					} else {
						document.getElementById('print').value = true;
						document.getElementById('saveUserValues_form').action = '';
						switch(chooseWay){
						case YOU:
							document.getElementById('userIs').value = 'you';
							document.getElementById('role').value = you.role;
							document.getElementById('person_name').value = you.name;
							document.getElementById('projectCode').value = you.projectCode;
							break;
						case SPOUSE:
							document.getElementById('userIs').value = 'spouse';
							document.getElementById('role').value = spouse.role;
							document.getElementById('person_name').value = spouse.name;
							document.getElementById('projectCode').value = spouse.projectCode;
							break;
						case FREE:
							document.getElementById('userIs').value = 'free';
							document.getElementById('role').value = document.getElementById('choose_role').value;
							break;
						}
						document.getElementById('minimum').value = document.getElementById('output_minimum').innerHTML;
						document.getElementById('maximum').value = document.getElementById('output_maximum').innerHTML;
						document.getElementById('minimum_month').value = document.getElementById('output_minimum_month').innerHTML;
						document.getElementById('maximum_month').value = document.getElementById('output_maximum_month').innerHTML;
						document.getElementById('preAllowance').value = document.getElementById('input_preAllowance').value;
						document.getElementById('newAllowance').value = document.getElementById('input_newAllowance').value;
						document.getElementById('preHours').value = document.getElementById('input_preHours').value;
						document.getElementById('newHours').value = document.getElementById('input_newHours').value;
						document.getElementById('mon').value = document.getElementById('input_mon').value;
						document.getElementById('tues').value = document.getElementById('input_tues').value;
						document.getElementById('wed').value = document.getElementById('input_wed').value;
						document.getElementById('thurs').value = document.getElementById('input_thurs').value;
						document.getElementById('fri').value = document.getElementById('input_fri').value;
						document.getElementById('effective').value = document.getElementById('input_effective').value;
						document.getElementById('saveUserValues_form').target = "_blank";
						saveUserValues_form.submit();
					}
				}
				
				function download1(){
					if (document.getElementById('input_effective').value == ""){
						alert("Please enter an effective date.")
					} else {
						document.getElementById('print').value = true;
						var sbid = document.getElementById('sbid').value;
						var sbidtxt = '';
						if(sbid != 0) {
							sbidtxt = '&sbid=' + sbid;
						}
						document.getElementById('saveUserValues_form').action = '/forms-information/workflow/?page=allowance-calculator-export' + sbidtxt;
						switch(chooseWay){
						case YOU:
							document.getElementById('userIs').value = 'you';
							document.getElementById('role').value = you.role;
							document.getElementById('person_name').value = you.name;
							document.getElementById('projectCode').value = you.projectCode;
							break;
						case SPOUSE:
							document.getElementById('userIs').value = 'spouse';
							document.getElementById('role').value = spouse.role;
							document.getElementById('person_name').value = spouse.name;
							document.getElementById('projectCode').value = spouse.projectCode;
							break;
						case FREE:
							document.getElementById('userIs').value = 'free';
							document.getElementById('role').value = document.getElementById('choose_role').value;
							break;
						}
						document.getElementById('minimum').value = document.getElementById('output_minimum').innerHTML;
						document.getElementById('maximum').value = document.getElementById('output_maximum').innerHTML;
						document.getElementById('minimum_month').value = document.getElementById('output_minimum_month').innerHTML;
						document.getElementById('maximum_month').value = document.getElementById('output_maximum_month').innerHTML;
						document.getElementById('preAllowance').value = document.getElementById('input_preAllowance').value;
						document.getElementById('newAllowance').value = document.getElementById('input_newAllowance').value;
						document.getElementById('preHours').value = document.getElementById('input_preHours').value;
						document.getElementById('newHours').value = document.getElementById('input_newHours').value;
						document.getElementById('mon').value = document.getElementById('input_mon').value;
						document.getElementById('tues').value = document.getElementById('input_tues').value;
						document.getElementById('wed').value = document.getElementById('input_wed').value;
						document.getElementById('thurs').value = document.getElementById('input_thurs').value;
						document.getElementById('fri').value = document.getElementById('input_fri').value;
						document.getElementById('effective').value = document.getElementById('input_effective').value;
						document.getElementById('saveUserValues_form').target = "_blank";
						saveUserValues_form.submit();
					}
				}
				
				function backTo(section){ //could be more
					showSection(section);
				}
				
				
				
				function window_load(){
					$("input[name='whichWay']").change(function(){
						if (document.getElementById('show_you') != null && document.getElementById('show_you').checked){
							proceed(0);
						}
						if (document.getElementById('show_spouse') != null && document.getElementById('show_spouse').checked){
							proceed(1);
						}
						if (document.getElementById('show_anyone') != null && document.getElementById('show_anyone').checked){
							proceed(2);
						}
					});
					
					<?php if(getAccess($current_user_id) == $allowance_constant['fullAccess']) { ?>
						document.getElementById('show_you').checked = true;
						proceed(0);
					<?php } else if (getSpouse() != -1 and getAccess(getSpouse()) == $allowance_constant['fullAccess']) { ?>
						document.getElementById('show_spouse').checked = true;
						proceed(1)
					<?php } else { ?>
						document.getElementById('show_anyone').checked = true;
						proceed(2)
					<?php } ?>
				}
				
				
				window.onload = window_load;
				
			</script>
			<div id='blurb'><?php echo changeNL(getStringConstant("blurb_0")) ?></div>
			<BR>
			<div id='section_whichWay'>
				<?php if(isset($_GET['sbid'])){echo '<span style="color:red;">You are currently editing a workflow submission. Clicking on the workflow submit button will edit your previous submission. Be sure to save or submit the form if you would like to keep your changes.</span><br><br>';}?>
				Please select an option:<BR>
				<?php if(getAccess($current_user_id) == $allowance_constant['fullAccess']) { ?>
				<input type='radio' name='whichWay' id='show_you' value='0'><label for='show_you'>Calculate for yourself</label>
				<?php }
				if (getSpouse() != -1 and getAccess(getSpouse()) == $allowance_constant['fullAccess']) { // hides the option if there is no spouse ?>
				<input type='radio' name='whichWay' id='show_spouse' value='1' ><label for='show_spouse'>Calculate for spouse</label>
				<?php } ?>
				<input type='radio' name='whichWay' id='show_anyone' value='2'><label for='show_anyone'>Calculate for anyone</label>
				<BR>
				<BR>
				<div id='user_name'></div>
				<div id='section_enterAll' style=' /* display:none; */'>
					<div id='choose_role_div'><select id="choose_role" onchange='select_role();'>
					<?php
						global $allowance_constant;
					for($i = 0; $i < count($allowance_constant['roleType']); $i ++){
						echo "<option value='".$i."'>".$allowance_constant['roleType'][$i]."</option>";
					}?>
					</select>
					<input type='button' value='Select' onclick='select_role();'></div>
					<!--  this are the extra special questions-->
					<form name="saveUserValues_form" id="saveUserValues_form" action="" method="post">
						<div id='questions'>
							<BR>
							<div id='name_project_code'>
								Name: <input type='text' name='person_name' id='person_name'><BR>
								Project Code: <input type='text' name='projectCode' id='projectCode' maxlength='6'><BR><BR>
								<input type="hidden" name="sbid" id="sbid" value="<?php if(isset($_GET['sbid'])){echo $_GET['sbid'];}else echo '0';?>">
							</div>
							<div id='hours'>
							    <h2><?php echo getStringConstant("first_header") ?></h2>
								<strong><?php echo getStringConstant("hour_label") ?></strong>
								<input type='text' size='3' name='hour_percentage' id='hour_percentage'>%<BR><BR>
							</div>
							<div id='role_type_field'>
								<strong>Role Type</strong><BR>
								<input type='radio' name='extra_level' id='extra-field-6' value='6'><label for='extra-field-6'>Managers and Other Ministry Leaders</label><BR>
								<input type='radio' name='extra_level' id='extra-field-7' value='7'><label for='extra-field-7'>Ministry Director</label><BR>
								<input type='radio' name='extra_level' id='extra-field-8' value='8'><label for='extra-field-8'>Domain Leader</label><BR><BR>
							</div>
							<div id='role_type_corp'>
								<strong>Role Type</strong><BR>
								<input type='radio' name='extra_level' id='extra-corp-6' value='6'><label for='extra-corp-6'>Manager / Other Dept. Leader</label><BR>
								<input type='radio' name='extra_level' id='extra-corp-7' value='7'><label for='extra-corp-7'>Department Director</label><BR><BR>
							</div>
							
							<input type='hidden' name='userIs' id='userIs'>
							<input type='hidden' name='print' id='print'>
							<input type='hidden' name='minimum' id='minimum'>
							<input type='hidden' name='maximum' id='maximum'>
							<input type='hidden' name='minimum_month' id='minimum_month'>
							<input type='hidden' name='maximum_month' id='maximum_month'>
							<input type='hidden' name='preAllowance' id='preAllowance'>
							<input type='hidden' name='newAllowance' id='newAllowance'>
							<input type='hidden' name='preHours' id='preHours'>
							<input type='hidden' name='newHours' id='newHours'>
							<input type='hidden' name='mon' id='mon'>
							<input type='hidden' name='tues' id='tues'>
							<input type='hidden' name='wed' id='wed'>
							<input type='hidden' name='thurs' id='thurs'>
							<input type='hidden' name='fri' id='fri'>
							<input type='hidden' name='date' id='date' value="">
							<input type='hidden' name='effective' id='effective'>
							<input type='hidden' name='role' id='role'>
							<?php getQuestions($allowance_constant['fieldIndividual']) ?>
						</div>
					</form>
				</div>
			</div>
			<div id='section_result' style='display:none;'>
				<div id='printResult'></div>
				<table>
					<tr>
						<td></td>
						<td>Annual</td>
						<td>Monthly</td>
					</tr>
					<tr>
						<td>Recommended Minimum:</td>
						<td id='output_minimum'></td>
						<td id='output_minimum_month'></td>
					</tr>
					<tr>
						<td>Staff Member's Personal Maximum:</td>
						<td id='output_maximum'></td>
						<td id='output_maximum_month'></td>
					</tr>
				</table>
				<hr>
				<strong>Change in Allowance or Hours</strong>
				<table><tr>
				<td>Previous Allowance: <input type='text' id='input_preAllowance'></td>
				<td>New Allowance: <input type='text' id='input_newAllowance'></td>
				</tr><tr>
				<td>Previous number of hours: <input type='text' style="width:40px" id='input_preHours'></td>
				<td>New Number of Hours: <input type='text' style="width:40px" id='input_newHours'></td>
				</tr><tr>
				<td colspan='2'><strong>** If schedule is less than 40 hours per week enter normal days/ hours worked</strong></td>
				</tr></table>
				<table><tr>
				<td>Monday: <input type='text' style="width:40px" id='input_mon'></td>
				<td>Tuesday: <input type='text' style="width:40px" id='input_tues'></td>
				<td>Wednesday: <input type='text' style="width:40px" id='input_wed'></td>
				<td>Thursday: <input type='text' style="width:40px" id='input_thurs'></td>
				<td>Friday: <input type='text' style="width:40px" id='input_fri'></td>
				</tr></table>
				Effective Date: <input type='text' id='input_effective'>
				<hr>
				<?php if(isset($_GET['sbid'])){echo '<span style="color:red;">You are currently editing a workflow submission. Clicking on the workflow submit button will edit your previous submission. Be sure to save or submit the form if you would like to keep your changes.</span><br><br>';}?>
				<table class='button'><tr>
					<td class='button'><input type='button' value='Restart' onclick='reset();showSection("whichWay");'></td>
					<td class='button'><input type='button' id='buttonSave' value='Save' onclick='saveUserValues();'></td>
					<td class='button'><input type='button' value='Download/Print' onclick='download();'></td>
					<td class='button'><input type='button' value='WorkFlowTest' onclick='download1();'></td>
					<td class='button'><input type='button' value='Back' onclick='backTo("whichWay");'></td>
				</tr></table>
				<?php if(isAppAdmin('support_calculator_admin', 0)){ ?>
					<input type='button' value='More Info' onclick='$("#t").toggle();'>
					<div id='t' style='display:none;'>
					The selection below will only show if user is an administer.<BR><BR>
					<div id='debug'></div></div>
				<?php } ?>
			</div>
		<?php } ?>
    </div>
    </div>
    <div id="content-right" class="mobile-off"><?php get_sidebar(''); ?></div><div style='clear:both;'></div>
</div>
<!--content end-->
<!--Popup window-->
</div>
<!--main end-->
</div>
<!--wrapper end-->
<div class="clear"></div>		
<?php get_footer(); ?>