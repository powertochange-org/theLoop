<?php
/**
 * includes/options.php
 *
 * Gets or sets default options
 *
 * Author: BCG
 *
 */
//
//	Admin SSL config page link
//

	$config_page_ref = "admin-ssl-config";

//
//	get, update or insert default Admin SSL database options
//

	if(!defined("TEST") || $first_test):
	function as_option($action,$name,$value=false)
	{
	//
	//	add the prefix for Admin SSL options
	//

		$name = "admin_ssl_$name";

		if($action === "get")
		{
		//
		//	get the existing option from the database, or set to false if resetting options
		//

			$option = RESET ? false : get_option($name);

			if($option === false && $value !== false)
			{
				update_option($name,$value);
				return($value);
			}
			else return($option);
		}

	//
	//	when updating ensure that the user has enough privileges to do so
	//

		elseif($action === "update" && as_user_can("manage_options"))
		{
			return(update_option($name,$value));
		}
		elseif($action === "delete" && as_user_can("manage_options"))
		{
			return(delete_option($name));
		}
	}endif;

//
//	get (or set default) options from the database
//

	$use_ssl = as_option("get","use_ssl","0") === "1" ? true : false;
	$additional_urls = as_option("get","additional_urls","wp-comments-post.php\nwp-admin/plugins.php?page=akismet-key-config");
	$ignore_urls = as_option("get","ignore_urls","xmlrpc.php");
	$secure_users_only = as_option("get","secure_users_only","0") === "1" ? true : false;
	if(!isset($config_parent)) $config_parent = as_option("get","config_parent","plugins.php");

	if(apache_version(1.3, 1))
	{
		$default_https_key = "SERVER_PORT";
		$default_https_value = "443";
	}
	elseif(apache_version(2))
	{
		$default_https_key = "HTTPS";
		$default_https_value = "on";
	}

	$https_key = as_option("get","https_key",$default_https_key);
	$https_value = as_option("get","https_value",$default_https_value);

//
//	build secure site url
//

	$secure_url = preg_replace("|^https?://|",scheme($use_ssl),get_option("siteurl"));
	$secure_url = rtrim(trim($secure_url),"/"); # remove any trailing slashes

//
//	log plugin options
//

	as_log("HTTPS: ".(is_https() ? "Yes" : "No")."
		URL: http".(is_https() ? "s" : "")."://".host().req_uri()."

		Use SSL: ".($use_ssl ? "Yes" : "No")."
		Site URL: ".get_option("siteurl")."
		Secure URL: $secure_url
		Additional urls:\n$additional_urls
		Ignore urls:\n$ignore_urls
		Secure users only: ".($secure_users_only ? "Yes" : "No")."
		Config parent: $config_parent");

	as_log("\n-- end initialisation, begin functions --\n");
?>