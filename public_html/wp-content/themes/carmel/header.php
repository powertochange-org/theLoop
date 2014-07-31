<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php

// Get the current user
$current_user = wp_get_current_user();

// Check to see if we need to display a survey
if (get_theme_mod('survey_active')) {
    // Get the last time a survey was made active
    $last_survey_date = get_theme_mod('survey_date');
   
    // Get the last time this user completed a survey 
    $user_survey_date = get_user_meta($current_user->ID, 'last_survey_date', true);
    
    //Check to see if there's a new survey since the last time we did one (and that the new survey is not for sometime in the future)
    if (strtotime($last_survey_date) > strtotime($user_survey_date) && strtotime($last_survey_date) <= time()) {
        // We have a new survey! Get the URL and display the dialog
        $survey_url = get_theme_mod('survey_url');
        echo '<div id="survey">
                  <div>
                      <h1>Hello there!</h1>
                      <p>We told you there would be a time to give feedback on the design for this site.<br/>The time has come; will you help us improve our site by participating in a survey?</p>
                      <div>
                          <a class="surveyButtons" target="_blank" href="'.$survey_url.'" onclick="decideSurvey()" title="Take the Survey">Yes</a>
                          <a class="surveyButtons" href="#" onclick="decideSurvey();" title="Don\'t Ask Me Again">No</a>
                          <a class="surveyButtons" href="#" onclick="dismissSurvey()" title="Ask Me Again Next Time">Maybe Later</a>
                      </div>
                  </div>
              </div>';
    }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
    <head profile="http://gmpg.org/xfn/11">    
		<script src="https://code.jquery.com/jquery-latest.js"></script>
        <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>" charset="<?php bloginfo('charset'); ?>" />
        <title>The Loop | An Internal Communications Blog</title>
        <?php $siteURL = get_bloginfo('url'); ?>
        <link href="<?php bloginfo('template_url'); ?>/style.css" rel="stylesheet" type="text/css" />
        <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:700,300,100|Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
        <?php wp_head(); ?> 
         <script type='text/javascript'>
			function popUpShow(){
				$("#popUp").show();
				var e = document.getElementById('popUpButton');
				e.style.background = '#f4f4f4';
				e.style.border = '1px solid #d6d7d4';
				e.style.borderBottom = '1px solid #f4f4f4';
			}
			
			function popUpHide(){
				$("#popUp").hide();
				var e = document.getElementById('popUpButton');
				e.style.background = '#f58220';
				e.style.border = '1px solid #eb8528';
			}
            
            // Helper function to hide the survey
            function dismissSurvey() {
                $("#survey").hide();
            }

            // This function updates the database to reflect that a user as
            // "decided" on a survey. 
            // Please note that this can either mean that the user took the
            // survey, or that the user decided not to participate in this
            // survey at all
            function decideSurvey() {
                // Perform an ajax call to a separate php page to update the
                // database to reflect that the user has either taken the
                // survey, or chosen to ignore it
                $.post("<?php echo get_template_directory_uri()?>/updateSurvey.php");
                // Close out of dialog
                dismissSurvey();
            }
			
			
      /* todo  jQuery(document).ready(function(){
        		
        });*/
        </script>
		<script type="text/javascript">
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-17609569-4']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		</script>		
    </head>  
    <body>
		<header>
			<div class='inner' style='position:relative;'>
				<a href='/'><img class='header-logo' src='<?php bloginfo('template_url'); ?>/img/header-logo.png' alt='Power To Change' /></a>
				<a href='/'><img style='position:relative;left:37px;top:-5px' class='header-logo' src='<?php bloginfo('template_url'); ?>/img/loop-logo.png' alt='Home' /></a>
				
				<a id='popUpButton' class='button related' onmouseout='popUpHide();' onmouseover='popUpShow();'>Staff Apps</a>
				<div id='popUp' onmouseout='popUpHide();' onmouseover='popUpShow();' style='display:none;position:absolute;background-color:#f4f4f4;padding:10px 40px;right:10px;top:69px;z-index:1;border:1px solid #d6d7d4;'>
					<center><ul class='popupMenu'>
						<?php //may turn into wordpress menu */ ?>
						<table>
							<tr>
								<td style="border:0;">
									<a href='https://absences.powertochange.org'>
									<img src='<?php bloginfo('template_url'); ?>/img/Absence-Tracker-Icon.png' alt='Absence Tracker' /></a>
								</td>
								<td style="border:0;">
									<a href='/reports/'>
									<img src='<?php bloginfo('template_url'); ?>/img/Reports-Icon.png' alt='Reports' /></a>
								</td>
								<td style="border:0;">
									<a href='/wp-admin/admin.php?page=s2'>
									<img src='<?php bloginfo('template_url'); ?>/img/My-Settings-Icon.png' alt='My Settings' /></a>
								</td>
							</tr>
							<tr>
								<td style="border:0;">
									<a href='/staff-directory/'>
									<img src='<?php bloginfo('template_url'); ?>/img/Staff-Directory-Icon.png' alt='Staff Directory' /></a>
								</td>
								<td style="border:0;">
									<a href='mailto:helpdesk@powertochange.org'>
									<img src='<?php bloginfo('template_url'); ?>/img/HelpDesk-Icon.png' alt='Help Desk' /></a>
								</td>
								<td style="border:0;">
									<a href='https://wiki.powertochange.org/help'>
									<img src='<?php bloginfo('template_url'); ?>/img/Self-Help-Wiki-Icon.png' alt='Self-Help Wiki' /></a>
								</td>
							</tr>
						</table>
					</ul></center>
				</div>
			</div>
			<div class='menu_bg'>
				<?php wp_nav_menu( array( 'theme_location'=>'main', 'menu_class' => 'menu', 'depth' => 1)); ?>
			</div>
		</header>
