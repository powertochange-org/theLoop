<?php
/*
Plugin Name: Admin SSL
Plugin URI: http://www.kerrins.co.uk/blog/admin-ssl/
Description: Secures any WordPress URL using Private SSL. Once the plugin is activated go to the Admin SSL config page to enable SSL and read the <a href="http://www.kerrins.co.uk/blog/admin-ssl/setup/">installation instructions</a>.
Author: BCG
Version: 2.0-b2
Date: 2011-04-23 12:10
Author URI: http://www.kerrins.co.uk/

For changelog please see release-notes.txt

*/

//
//	if you are experiencing problems, set this to 'true' and messages will be logged
//	in the Admin SSL directory (usually /wp-content/plugins/admin-ssl/debug.log
//

	define("DEBUG",false);

//
//	if this is 'true' then the database will be updated with the default options - use if
//	you cannot access admin-ssl-reset.php after setting wrong Shared SSL settings
//	you MUST change back to false after resetting, or you will not be able to enable SSL
//

	define("RESET",false);

//
//	the Admin SSL version branch
//

	define("AS_VERSION",2.0);

/*																														*
 *																														*
 * 																													*
 * 																													*
 * 			DO NOT EDIT BELOW THIS LINE - USE THE CONFIG PAGE TO CHANGE SETTINGS				*
 * 																													*
 * 																													*
 * 																													*
 * 																													*
 */


//
//	requires $wp_version check - this plugin WILL BREAK earlier versions of WordPress and WPMU
//

	if(isset($wp_version) && $wp_version >= 3.0)
	{
	//
	//
	//	DEBUG MODE AND FUNCTIONS
	//
	//

		if(!defined("TEST") || $first_test):

		//
		//	log debug messages to plugin operating directory
		//

			function as_log($msg)
			{
				global $path,$slash;

				$msg = preg_replace('/\t/',"",$msg);

				if($path && $slash && DEBUG)
					error_log($msg."\n\n",3,$path.$slash."debug.log");
				elseif(@TEST && DEBUG)
					echo("\n$msg");
			}

		//
		//	display message on admin pages if debug or reset are enabled
		//

			function as_warning()
			{
				global $path,$slash;

				if(DEBUG)
					echo('
						<div id="admin-ssl-debug-warning" class="error fade">
							<p><strong>Admin SSL</strong> is currently debugging to: '.$path.$slash.'debug.log</p>
						</div>
					');

				if(RESET)
					echo('
						<div id="admin-ssl-reset-warning" class="error fade">
							<p><strong>Admin SSL</strong> is currently in reset mode - you will not
							be able to secure your site with SSL until you disable reset mode.</p>
						</div>
					');
			}

		//
		//	shorthands for lazy people
		//

			function content_dir($with_slash=true) { # returns the name of the content directory
				$a = explode("/", WP_CONTENT_DIR);
				return(max($a).($with_slash ? "/" : "")); }
			function host(){ # returns HTTP_HOST with any port numbers removed
				return(preg_replace('/:.+$/',"",$_SERVER["HTTP_HOST"])); }
			function is_https(){ # return true or false, HTTPS enabled
				global $https_key,$https_value;
				return(isset($_SERVER[$https_key]) && $https_value === $_SERVER[$https_key] ? true : false); }
			function _post($key){ # safely return escaped value from $_POST array
				return(isset($_POST[$key]) ? esc_attr($_POST[$key]) : null); }
			function redirect_to(){ # return WordPress' redirect_to
				return(isset($_REQUEST["redirect_to"]) ? esc_attr($_REQUEST["redirect_to"]) : ""); }
			function req_uri(){ # return server request uri
				return($_SERVER["REQUEST_URI"]); }
			function scheme($use_https){ # return scheme based on test value
				return(($use_https ? "https" : "http")."://"); }
			function as_user_can($what){ # checks if function exists before calling it
				return(function_exists("current_user_can") ? current_user_can($what) : false); }

		//
		//	safe redirect function - don't want to use wp_redirect()
		//

			function as_redirect($location)
			{
				if((!defined("TEST") || $first_test) && !defined("ADMIN_SSL_DO_NOT_REDIRECT"))
				{
					session_write_close();
					header("location: $location");
					exit;
				}
				elseif(TEST) return($location);
			}

		//
		//	get the Apache version
		//

			function apache_version($test=null, $precision=0)
			{
				$re = "/Apache\/([\d.]+)/";
				preg_match($re, $_SERVER["SERVER_SOFTWARE"], $matches);
				$version = $matches[1];

				if(is_null($test)) return(doubleval($version));
				else return(round($version, $precision) == $test ? true : false);
			}

		endif;

	//
	//
	//	OPERATING DIRECTORY DETECTION
	//
	//

		//
		//	get operating directory and log environment variables
		//

			$slash = strpos(__FILE__,"/") === false ? "\\" : "/";
			$path = str_replace($slash."admin-ssl.php","",__FILE__);
			$dir = substr($path,strrpos($path,$slash)+1);

			as_log("### ADMIN SSL BEGINS ###");
			as_log("HTTP Host: ".host()."
				Request URI: ".req_uri()."
				Redirect to: ".redirect_to()."
				Found admin-ssl.php in
				 - path: $path
				 - directory: $dir");

		//
		//	if operating directory is mu-plugins, get the name of admin-ssl directory
		//

			$plugins_dir = "plugins";
			$config_page = "config-page.php";

		//
		//	log variables just defined
		//

			as_log("Plugins directory: $plugins_dir
				Config page: $config_page");

	//
	//
	//	GET (OR SET DEFAULT) OPTIONS
	//
	//

		require_once("includes/options.php");

	//
	//
	//	ADD LINK TO SETTINGS PAGE ON PLUGIN LIST
	//
	//

		function as_action_links($links, $file)
		{
			global $config_parent,$config_page_ref;

			if($file == plugin_basename(__FILE__))
			{
				$links[] = '<a href="'.$config_parent.'?page='.$config_page_ref.'">'._("Settings")."</a>";
			}

			return($links);
		}

	//
	//
	//	THIS IS WHERE THE REAL STUFF BEGINS...
	//
	//

		if(!defined("TEST") || $first_test):

		//
		//
		//	WORDPRESS HOOKS - CHECKING HTTP/HTTPS
		//
		//

			require_once("includes/https.php");

		//
		//
		//	WORDPRESS HOOKS - CONFIGURATION
		//
		//

			require_once("includes/config.php");

		//
		//
		//	ADD WORDPRESS HOOKS
		//
		//

			require_once("includes/hooks.php");

		endif;
	}

?>