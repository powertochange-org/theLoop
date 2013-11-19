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
?>