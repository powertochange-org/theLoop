<?php

//adding menu
add_theme_support('nav-menus');
register_nav_menus(array(
	 'main'=>__('Main Menu')
	)
);

//adding widget for the subscriber count;

register_sidebar(array(
	'name' => 'Footer',
	'id' => 'footer',
	'before_widget' => '<div>',
	'after_widget' => '</div>',
	'before_title' => '<h1>',
	'after_title' => '</h1>',
));

// custom 'read more' link
function excerpt_ellipse($text) {
    return str_replace(' [&hellip;]', '... <BR><BR><a href="'.get_permalink().'">Read more</a>', $text);
}
add_filter('the_excerpt', 'excerpt_ellipse');

/*function new_excerpt_more( $more ) {
	return '[.....]';
}
add_filter('excerpt_more', 'new_excerpt_more');*/



$postArray = array();
/*$count_posts = wp_count_posts();
$published_posts = $count_posts->publish;*/
$posts_array = get_posts( array('numberposts'   => 50));
foreach($posts_array as $post){
	$postArray[$post->ID] = $post->post_title;
}

function themename_customize_register($wp_customize){
    
    // var_dump($wp_customize);
	global $postArray;
	
	// *************************************************************************************************

    $wp_customize->add_section('sidebar_settings', array(
        'title'         =>  'Feature Sidebar Settings',
        'description'   =>  'The Sidebar section allows you to select feature pages,
                             posts, and other documents you believe would be valuable
                             to P2C staff and show them directly on the homepage.',
        'priority'      =>  '201'
    ));
    $wp_customize->add_setting('feature_title_1', array(
        'default'       =>  null,
        'capability'    =>  'edit_theme_options',
        'type'          =>  'theme_mod',
        'transport'     =>  'postMessage'
    ));
    $wp_customize->add_control('select_feature_title_1', array(
        'label'      => 'Title 1',
        'section'    => 'sidebar_settings',
        'settings'   => 'feature_title_1'
    ));
    $wp_customize->add_setting('title_url_1', array(
        'default'        => null,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'transport'     =>  'postMessage'
    ));
    $wp_customize->add_control('input_title_url_1', array(
        'label'      => 'Title URL 1',
        'section'    => 'sidebar_settings',
        'settings'   => 'title_url_1',
    ));
    
    $wp_customize->add_setting('feature_title_2', array(
        'default'       =>  null,
        'capability'    =>  'edit_theme_options',
        'type'          =>  'theme_mod',
        'transport'     =>  'postMessage'
    ));
    $wp_customize->add_control('select_feature_title_2', array(
        'label'      => 'Title 2',
        'section'    => 'sidebar_settings',
        'settings'   => 'feature_title_2'
    ));
    $wp_customize->add_setting('title_url_2', array(
        'default'        => null,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'transport'     =>  'postMessage'
    ));
    $wp_customize->add_control('input_title_url_2', array(
        'label'      => 'Title URL 2',
        'section'    => 'sidebar_settings',
        'settings'   => 'title_url_2',
    ));

    $wp_customize->add_setting('feature_title_3', array(
        'default'       =>  null,
        'capability'    =>  'edit_theme_options',
        'type'          =>  'theme_mod',
        'transport'     =>  'postMessage'
    ));
    $wp_customize->add_control('select_feature_title_3', array(
        'label'      => 'Title 3',
        'section'    => 'sidebar_settings',
        'settings'   => 'feature_title_3'
    ));
    $wp_customize->add_setting('title_url_3', array(
        'default'        => null,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'transport'     =>  'postMessage'
    ));
    $wp_customize->add_control('input_title_url_3', array(
        'label'      => 'Title URL 3',
        'section'    => 'sidebar_settings',
        'settings'   => 'title_url_3',
    ));

    $wp_customize->add_setting('feature_title_4', array(
        'default'       =>  null,
        'capability'    =>  'edit_theme_options',
        'type'          =>  'theme_mod',
        'transport'     =>  'postMessage'
    ));
    $wp_customize->add_control('select_feature_title_4', array(
        'label'      => 'Title 4',
        'section'    => 'sidebar_settings',
        'settings'   => 'feature_title_4'
    ));
    $wp_customize->add_setting('title_url_4', array(
        'default'        => null,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'transport'     =>  'postMessage'
    ));
    $wp_customize->add_control('input_title_url_4', array(
        'label'      => 'Title URL 4',
        'section'    => 'sidebar_settings',
        'settings'   => 'title_url_4',
    ));

    $wp_customize->add_setting('feature_title_5', array(
        'default'       =>  null,
        'capability'    =>  'edit_theme_options',
        'type'          =>  'theme_mod',
        'transport'     =>  'postMessage'
    ));
    $wp_customize->add_control('select_feature_title_5', array(
        'label'      => 'Title 5',
        'section'    => 'sidebar_settings',
        'settings'   => 'feature_title_5'
    ));
    $wp_customize->add_setting('title_url_5', array(
        'default'        => null,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'transport'     =>  'postMessage'
    ));
    $wp_customize->add_control('input_title_url_5', array(
        'label'      => 'Title URL 5',
        'section'    => 'sidebar_settings',
        'settings'   => 'title_url_5',
    ));


    // *************************************************************************************************

    $wp_customize->add_setting('feature_update', array(
        'default'        => null,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
 
    ));
    $wp_customize->add_control( 'select_feature_update', array(
        'settings' => 'feature_update',
        'label'   => 'Feature Update:',
        'section' => 'static_front_page',
        'type'    => 'select',
        'choices'    => $postArray
    ));
	
	   $wp_customize->add_setting('upcoming_event', array(
        'default'        => null,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
 
    ));
    $wp_customize->add_control( 'select_upcoming_event', array(
        'settings' => 'upcoming_event',
        'label'   => 'Upcoming Event:',
        'section' => 'static_front_page',
        'type'    => 'select',
        'choices'    => $postArray
    ));

    // *** THIS IS THE CUSTOM IMAGE SLIDER SECTION

    // Add section for Image Slider Settings
    $wp_customize->add_section('image_slider_settings', array(
        // Visible title of section
        'title'       => 'Image Slider Settings', 
        // Visible Description of what the section is supposed to do
        'description' => 'Here you can set up the Image Slider for specific Images,
                          without code! Phew! *Breath a sigh of relief* To operate, 
                          copy and paste the URL of the new page/post into the "Image URL"
                          section, and then Select the Image you want associated
                          with that URL :)',
        // Set Priority to lowest (this puts it at the bottom)
        'priority'    => 200
    ));

    // URL Setting - this is for the Image URL
    // so that when the Image is clicked, it routes to the correct page.
    $wp_customize->add_setting('image_url_1', array(
        'default'        => null,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
 
    ));

    $wp_customize->add_control('input_image_url_1', array(
        'label'      => 'Image URL 1',
        'section'    => 'image_slider_settings',
        'settings'   => 'image_url_1',
    ));

    // Select an Image
    $wp_customize->add_setting('image_select_1', array(
        'default'        => '',
        // 'capability'     => 'edit_theme_options',
        // 'type'           => 'option',
        'transport'      => 'postMessage'
    ));

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'image_select_1',
            array(
                'label'     => 'Select Image 1',
                'section'   => 'image_slider_settings',
                'settings'  => 'image_select_1'
            )
        )  
    );

    $wp_customize->add_setting('image_url_2', array(
        'default'        => null,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
 
    ));

    $wp_customize->add_control('input_image_url_2', array(
        'label'      => 'Image URL 2',
        'section'    => 'image_slider_settings',
        'settings'   => 'image_url_2',
    ));

    $wp_customize->add_setting('image_select_2', array(
        'default'        => '',
        // 'capability'     => 'edit_theme_options',
        // 'type'           => 'option',
        'transport'      => 'postMessage'
    ));

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'image_select_2',
            array(
                'label'     => 'Select Image 2',
                'section'   => 'image_slider_settings',
                'settings'  => 'image_select_2'
            )
        )  
    );
    
    $wp_customize->add_setting('image_url_3', array(
        'default'        => null,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
 
    ));

    $wp_customize->add_control('input_image_url_3', array(
        'label'      => 'Image URL 3',
        'section'    => 'image_slider_settings',
        'settings'   => 'image_url_3',
    ));

    $wp_customize->add_setting('image_select_3', array(
        'default'        => '',
        // 'capability'     => 'edit_theme_options',
        // 'type'           => 'option',
        'transport'      => 'postMessage'
    ));

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'image_select_3',
            array(
                'label'     => 'Select Image 3',
                'section'   => 'image_slider_settings',
                'settings'  => 'image_select_3'
            )
        )  
    );

    $wp_customize->add_setting('image_url_4', array(
        'default'        => null,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
 
    ));

    $wp_customize->add_control('input_image_url_4', array(
        'label'      => 'Image URL 4',
        'section'    => 'image_slider_settings',
        'settings'   => 'image_url_4',
    ));

    $wp_customize->add_setting('image_select_4', array(
        'default'        => '',
        // 'capability'     => 'edit_theme_options',
        // 'type'           => 'option',
        'transport'      => 'postMessage'
    ));

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'image_select_4',
            array(
                'label'     => 'Select Image 4',
                'section'   => 'image_slider_settings',
                'settings'  => 'image_select_4'
            )
        )  
    );

    // *** THIS IS THE END OF THE CUSTOM IMAGE SLIDER SECTION //

    // Add section for survey settings
    $wp_customize->add_section('survey_settings', array(
        'title'       => 'Survey Settings',
        'description' => 'Here you can set up a survey that will prompt staff on
                          the homepage of The Loop. You can use various formats for the survey
                          start date, and you can choose to set it to begin in the future.',
    ));

    // URL Setting 
    $wp_customize->add_setting('survey_url', array(
        'default'        => null,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
 
    ));
 
    $wp_customize->add_control('input_survey_url', array(
        'label'      => 'Survey URL',
        'section'    => 'survey_settings',
        'settings'   => 'survey_url',
    ));

    // Date setting
    $wp_customize->add_setting('survey_date', array(
        'default'           => date('m/d/Y'),
        'capability'        => 'edit_theme_options',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'sanitize_date',
    ));
 
    $wp_customize->add_control('input_survey_date', array(
        'label'      => 'Survey start date',
        'section'    => 'survey_settings',
        'settings'   => 'survey_date',
    ));
    
    // Active flag
    $wp_customize->add_setting('survey_active', array(
        'capability' => 'edit_theme_options',
        'type'       => 'theme_mod',
    ));
 
    $wp_customize->add_control('survey_active_checkbox', array(
        'settings' => 'survey_active',
        'label'    => 'Survey Active',
        'section'  => 'survey_settings',
        'type'     => 'checkbox',
    ));


    //this is where Ben is adding a weather (or other) alert for Ann
    $wp_customize->add_setting('alert_text', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
 
    ));
 
    $wp_customize->add_control('input_alert_text', array(
        'label'      => 'Alert Text',
        'section'    => 'static_front_page',
        'settings'   => 'alert_text',
    ));

    //end of weather alert section

}
 
add_action('customize_register', 'themename_customize_register');


//change "Howdy" to "Welcome"
add_action( 'admin_bar_menu', 'wp_admin_bar_my_custom_account_menu', 11 );


function wp_admin_bar_my_custom_account_menu( $wp_admin_bar ) {
	$user_id = get_current_user_id();
	$current_user = wp_get_current_user();
	$profile_url = get_edit_profile_url( $user_id );

	if ( 0 != $user_id ) {
		/* Add the "My Account" menu */
		$avatar = get_avatar( $user_id, 28 );
		$howdy = sprintf( __('Hello, %1$s'), $current_user->display_name );
		$class = empty( $avatar ) ? '' : 'with-avatar';

		$wp_admin_bar->add_menu( array(
			'id' => 'my-account',
			'parent' => 'top-secondary',
			'title' => $howdy . $avatar,
			'href' => $profile_url,
			'meta' => array(
			'class' => $class,
		),
		) );

	}
}

function sanitize_date($string) {
    // Attempt to get a date from it
    $date = strtotime($string);
    if ($date) {
        // When storing dates related to the survey in the database, they are
        // all stored as strings containing month, day, and year
        return date('m/d/Y', $date);
    }
    // Just return original string
    return $string;
}

/*Used for the loop search if the user clicks on one of the filter buttons.*/
function wpshock_search_filter( $query ) {
    if ( $query->is_search ) {
        $searchfilter = "";
        if(isset($_GET['searchfilter']))
            $searchfilter = $_GET['searchfilter'];
        $query->set( 'post_type', array($searchfilter) );
    }
    return $query;
}
if(isset($_GET['searchfilter']))
    add_filter('pre_get_posts','wpshock_search_filter');

/*Searches for posts that are newer than 3 years and searches for all kb articles and loop pages.*/
function wpsearch_date_filter( $where ) {
    global $wp_query;
    
    $where .= " AND (post_date >= '".date('Y-m-d', strtotime('-3 years'))."' 
        AND wp_posts.post_type = 'post' 
        OR wp_posts.post_type IN ('page', 'incsub_wiki')) ";
    
    return $where;
}

//Applies the date filter only when using the search bar
if(isset($_GET['s']) && !isset($_GET['showarchived']))
    add_filter( 'posts_where' , 'wpsearch_date_filter' );
 

/*
 * Jason B: Commented this out 2015-02-23 as it needs more testing before deploying to production. But, need to 
 *  deploy other changes in this file to production
 *
 * Gerald B: Adding code to check if there is a publish button first. Then display the confirmation alert. 
 */
function add_publish_confirmation(){ 
    //echo '<script type="text/javascript" src="/wp-content/themes/carmel/functions.js"></script>';
    $confirmation_message = get_option( 'publishconfirmationmessage' , 'Are you sure you want to publish this post?' ); 
    
    //Get all categories including ones that have no posts yet. 
    $cats = get_categories(array('hide_empty' => false));
    
    echo '<script type="text/javascript">';
    echo 'var publish = document.getElementById("publish");'; 
    echo 'if(publish !== null){';
    echo 'publish.onclick = function(){ ';
    echo 'var cats = document.getElementsByName("post_category[]");';
    echo 'var wpcats = [];';
    $i = 0;
    foreach($cats as $cat) {
        echo 'wpcats['.$i++.']'.' = ['.$cat->cat_ID.', "'.$cat->name.'"];';
    }
    //Matches all the checked categories with their names and creates a confirmation message displaying those categories.
    echo '
        var categoryMessage = "";
        var i;
        for (i = 0; i < cats.length; i++) {
            if (cats[i].type == "checkbox" && cats[i].checked) {
                var selectedCat = cats[i].id.split("in-category-")[1];
                var i;
                for (x = 0; x < wpcats.length; x++) {
                    if(selectedCat == wpcats[x][0]) {
                        categoryMessage += ("\n" + wpcats[x][1]);
                    }
                }
            }
        }';
    echo 'var fullMessage = "'.$confirmation_message.'" + categoryMessage;';
    echo 'return confirm(fullMessage); };'; 
    echo '}'; 
    echo '</script>'; 
} 
add_action('admin_footer', 'add_publish_confirmation');

// Remove social links from subscribe2 email messages
function s2_social_links_off() {
    return array();
}
add_filter('s2_social_links', 's2_social_links_off');

function getHttpResponseCode_using_curl($url, $followredirects = true){
    // returns int responsecode, or false (if url does not exist or connection timeout occurs)
    // NOTE: could potentially take up to 0-30 seconds , blocking further code execution (more or less depending on connection, target site, and local timeout settings))
    // if $followredirects == false: return the FIRST known httpcode (ignore redirects)
    // if $followredirects == true : return the LAST  known httpcode (when redirected)
    if(! $url || ! is_string($url)){
        return false;
    }
    $ch = curl_init($url);
    if($ch === false){
        return false;
    }
    curl_setopt($ch, CURLOPT_HEADER         ,true);    // we want headers
    curl_setopt($ch, CURLOPT_NOBODY         ,true);    // dont need body
    curl_setopt($ch, CURLOPT_RETURNTRANSFER ,true);    // catch output (do NOT print!)
    if($followredirects){
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,true);
        curl_setopt($ch, CURLOPT_MAXREDIRS      ,10);  // fairly random number, but could prevent unwanted endless redirects with followlocation=true
    }else{
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,false);
    }
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);   // fairly random number (seconds)... but could prevent waiting forever to get a result
    curl_setopt($ch, CURLOPT_TIMEOUT        ,6);   // fairly random number (seconds)... but could prevent waiting forever to get a result
    //curl_setopt($ch, CURLOPT_USERAGENT      ,"Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1");   // pretend we're a regular browser
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    if(curl_errno($ch)){   // should be 0
        curl_close($ch);
        return false;
    }
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // note: php.net documentation shows this returns a string, but really it returns an int
    curl_close($ch);
    return $code;
}
?>
