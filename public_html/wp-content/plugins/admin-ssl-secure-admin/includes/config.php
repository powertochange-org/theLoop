<?php
/**
 * includes/config.php
 *
 * Displays and handles confirguation
 *
 * Author: BCG
 *
 */

//
//	add Admin SSL config page to main menu
//

	function as_admin_menu()
	{
		global $config_parent,$config_page_ref;
		if(function_exists("add_submenu_page") && as_user_can("level_10"))
		{
			add_submenu_page($config_parent,"Admin SSL","Admin SSL",
				"manage_options",$config_page_ref,"as_conf");
		}
	}

//
//	display/update Admin SSL configuration
//

	function as_conf()
	{
		global $use_ssl,$secure_url;
		global $additional_urls,$ignore_urls,$secure_users_only;
		global $config_page,$config_parent;
		global $https_key,$https_value;

		if(isset($_POST["submit"]))
		{
		//
		//	make sure current user can set permissions,
		//	and that the referer was a page from this site
		//

			if(!as_user_can("manage_options")) exit("You don't have permission to change these options!");
			check_admin_referer();

		//
		//	get the posted configuration options
		//

			$use_ssl = "on" === _post("use_ssl") ? 1 : 0;
			$additional_urls = _post("additional_urls");
			$ignore_urls = _post("ignore_urls");
			$secure_users_only = "on" === _post("secure_users_only") ? 1 : 0;

			$redirect = true; # if different config parent page chosen, need to redirect later
			if($config_parent === _post("config_parent")) $redirect = false;
			else $config_parent = _post("config_parent");

			$https_key = _post("https_key");
			$https_value = _post("https_value");

		//
		//	verify the selected options
		//

			//
			//	$config_parent may only be one of two options
			//

				if($config_parent !== "plugins.php" && $config_parent !== "options-general.php")
					$message = "You submitted an invalid value ('$config_parent') for config parent.";

			//
			//	https key and value cannot be empty
			//

				if(trim($https_key) === "") $https_key = "HTTPS";
				if(trim($https_value) === "") $https_value = "on";

		//
		//	if there has been an error, reset all the options
		//

			if(isset($message))
			{
				$use_ssl = as_option("get","ssl_use_ssl");
				$additional_urls = as_option("get","additional_urls");
				$ignore_urls = as_option("get","ignore_urls");
				$secure_users_only = as_option("get","secure_users_only");
				$config_parent = as_option("get","config_parent");
				$https_key = as_option("get","https_key");
				$https_value = as_option("get","https_value");

				as_log("as_conf()\nError saving options: $message\nResetting options to previous values");
			}
			else as_log("as_conf()\nNew option values will be saved");

		//
		//	update options in database
		//

			as_option("update","use_ssl",$use_ssl);
			as_option("update","additional_urls",$additional_urls);
			as_option("update","ignore_urls",$ignore_urls);
			as_option("update","secure_users_only",$secure_users_only);
			as_option("update","config_parent",$config_parent);
			as_option("update","https_key",$https_key);
			as_option("update","https_value",$https_value);

			if(!isset($message)) $message = "Options saved.";

		//
		//	if config parent has been changed, redirect
		//

			if($redirect)
			{
				$location = $config_parent."?page=admin-ssl-config";
				as_log("as_conf():\nRedirecting to $location");
				as_redirect($location);
			}
		}

	//
	//	require configuration settings page
	//

		require_once($config_page);
	}
?>