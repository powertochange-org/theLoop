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


//add in possible alert
if (get_theme_mod('alert_text') != ''){
  $alert_text = get_theme_mod('alert_text');
  echo '<div id="alert">
                      <h1><strong>'.$alert_text .'</strong></h1>
          
        </div>';
}


?>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
    <head profile="http://gmpg.org/xfn/11">    
		<script src="https://code.jquery.com/jquery-latest.js"></script>
        <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>" charset="<?php bloginfo('charset'); ?>" />
        <title>The Loop | An Internal Communications Blog</title>
        <?php $siteURL = get_bloginfo('url'); ?>
        <link href="<?php bloginfo('template_url'); ?>/style.css?ver=1.0.1" rel="stylesheet" type="text/css" />
        <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:700,300,100|Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
        <link rel="shortcut icon" href="/wp-content/themes/carmel/favicon.png?v=2" />
        <?php wp_head(); ?> 
         <script type='text/javascript'>
           
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
        </script>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-17609569-4', 'auto');
      ga('send', 'pageview');
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>  
    <body>
    <?php
    if(get_option( 'loopstaffphotocontest' , 0 )) {
      include 'extrafeatures/staffdirectorycontest.php';
    }
    ?>
    <header>
      <div class='inner'>
  			<div class="header-logo">
          <a href="/"><img class='header-logo' src='<?php bloginfo('template_url'); ?>/img/header-logo.png?v=2' alt='Power To Change' /></a>
    			<a href="/"><img class='header-logo-img' src='<?php bloginfo('template_url'); ?>/img/loop-logo.png' alt='Home' /></a>
        </div>
        <div class="header-right">
          <div id='staff-app-container'>
          <a id="staffAppsButton" class="button related">Staff Apps</a>
		  <script src="https://staffappsbutton.powertochange.org/script.js"></script>
		  </div>
          <div class="search-position">
            <form method="get" id="sb_searchform" action="<?php bloginfo('home'); ?>/">
              <div class='search-box'>
                <?php $_SESSION['wiki'] = 0; //Resetting wiki flag so that search results don't filter to wiki only ?>
                <input name="s" id="s" class='search-input' placeholder='Search' type='text' 
                <?php if(isset($_GET['s']) && !isset($_GET['wiki'])) {echo 'value="'.$s.'"';} ?>
                data-swplive="true"/>
                <img onclick="document.getElementById('sb_searchform').submit();" class='search-img' src='<?php bloginfo('template_url'); ?>/img/search.png'>
              </div>
            </form>
          </div>
        </div>  
			</div>
      <script type="text/javascript">
        $(document).ready(function(){
          $("#mobile-menu").click(function(){
            $(".menu_bg") .toggleClass("menu_bg_show");
          });
        
        });
      </script>
      <button id="mobile-menu"><img src="/wp-content/themes/carmel/img/menu_icon.png" alt="" /></button>
			<div class='menu_bg'>
				<?php wp_nav_menu( array( 'theme_location'=>'main', 'menu_class' => 'menu', 'depth' => 1)); ?>
			</div>
      <div id='main-nav-full-screen'>
        <?php wp_nav_menu( array( 'theme_location'=>'main', 'container' => '', 'menu_class' => 'menu', 'depth' => 3)); ?>
      </div>
      <script>
        var timeout = null;
        var selectedElement = null;
        $('#main-nav-full-screen ul.menu > li').mouseenter(function() {
          if(selectedElement != null) {
            selectedElement.removeClass("display-sub-menu");
            clearTimeout(timeout);
          }
          selectedElement = $(this).find('ul');
          timeout = setTimeout(function () {
              selectedElement.addClass("display-sub-menu");
              console.log('display');
          }, 500);
        }).mouseleave(function() {
          if(selectedElement != null) {
            selectedElement.removeClass("display-sub-menu");
          }
          selectedElement = null;
          clearTimeout(timeout);
          timeout = null;
        });
      </script>
		</header>
