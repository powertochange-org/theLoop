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
    
	global $postArray;
	
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

/*
 * Jason B: Commented this out 2015-02-23 as it needs more testing before deploying to production. But, need to 
 *  deploy other changes in this file to production
 *
function add_publish_confirmation(){ 
    echo '<script type="text/javascript" src="/wp-content/themes/carmel/functions.js"></script>';
} 
add_action('admin_footer', 'add_publish_confirmation');
*/
?>
