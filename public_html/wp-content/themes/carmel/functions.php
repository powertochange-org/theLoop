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
    return str_replace(' [&hellip;]', ' <BR><BR><a href="'.get_permalink().'">Read more</a>', $text);
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
     $wp_customize->add_setting('feature_post', array(
        'default'        => null,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
 
    ));
    $wp_customize->add_control( 'select_feature_post', array(
        'settings' => 'feature_post',
        'label'   => 'Feature Post:',
        'section' => 'static_front_page',
        'type'    => 'select',
        'choices'    => $postArray
    ));
	
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
?>