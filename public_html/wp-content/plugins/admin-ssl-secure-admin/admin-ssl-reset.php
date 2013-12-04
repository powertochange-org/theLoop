<?php
/*
 * admin-ssl-reset.php
 *
 * Resets all Admin SSL options in the WordPress database
 * To be used if you set Shared SSL options incorrectly and your site is inaccessible
 *
 * Author: BCG
 *
 */

//
//	include WordPress configuration file
//

	define("ADMIN_SSL_DO_NOT_REDIRECT",true);

	$wp_config1 = "../../wp-config.php";
	$wp_config2 = "../../../wp-config.php";
	$wp_config3 = "../../../../wp-config.php";

	if(file_exists($wp_config1)) require_once($wp_config1);
	elseif(file_exists($wp_config2)) require_once($wp_config2);
	elseif(file_exists($wp_config3)) require_once($wp_config3);
	else exit("Unable to find WordPress configuration file.");

//
//	die if user is not logged in with full admin privileges
//

	if(function_exists("current_user_can") && !current_user_can("level_10"))
		exit(__("Nice try"));

//
//	reset options
//

	update_option("admin_ssl_use_ssl",0);
	update_option("admin_ssl_use_shared",0);
	update_option("admin_ssl_shared_url","");
	update_option("admin_ssl_additional_urls","");
	update_option("admin_ssl_secure_users_only",0);
	update_option("admin_ssl_config_parent","");
	update_option("admin_ssl_https_key","HTTPS");
	update_option("admin_ssl_https_value","on");

//
//	reset WPMU options
//

	if(function_exists("update_site_option"))
	{
		update_site_option("admin_ssl_use_ssl",0);
		update_site_option("admin_ssl_use_shared",0);
		update_site_option("admin_ssl_shared_url","");
		update_site_option("admin_ssl_additional_urls","");
		update_site_option("admin_ssl_secure_users_only",0);
		update_site_option("admin_ssl_config_parent","");
		update_site_option("admin_ssl_https_key","HTTPS");
		update_site_option("admin_ssl_https_value","on");
	}

//
//	redirect to wp-admin
//

	wp_redirect(get_option("siteurl")."/wp-admin");

?>