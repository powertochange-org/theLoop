<?php
/**
 * admin-ssl-test.php
 *
 * Unit test module for Admin SSL redirection
 *
 * Author: BCG
 *
 */

//
//	defining TEST changes the behaviour of admin ssl
//

	define("TEST",true);



//
//
//	TEST VARIABLES
//
//

	$host = "some.site.com";
	$path = "/blog";

	$shared_host = "my.amazing.host.com";
	$shared_path = "/~some_user$path";

	$additional_url1 = "wp-some-file.php";
	$additional_url2 = "wp-admin/plugins.php?page=admin-ssl-config";

	$options = array(
		"admin_ssl_use_ssl" => 					"1",
		"admin_ssl_use_shared" =>				"1",
		"admin_ssl_shared_url" =>				"https://$shared_host$shared_path/wp-admin/",
		"admin_ssl_additional_urls" => 			"$additional_url1\n$additional_url2",
		"admin_ssl_secure_users_only" =>		"0",
		"admin_ssl_config_parent" =>			"plugins.php",
		"admin_ssl_https_key" =>				"X_RANDOM_HTTPS",
		"admin_ssl_https_value" => 				"it_is_on",

		"siteurl" => 							"http://$host$path",
		"home" => 								"http://$host$path",
	);



//
//
//	PERFORM TESTS
//
//

	echo("<pre style='font-family: Consolas; font-size: 10pt;'>");

	echo("TESTING as_init()\n\n\n");

		echo("\t## SWITCH TO HTTPS\n\n");

			test_init("off",$host,"$path/wp-login.php","https://$shared_host$shared_path/wp-login.php");
			test_init("off",$host,"$path/wp-admin/profile.php","https://$shared_host$shared_path/wp-admin/profile.php");
			test_init("off",$host,"$path/$additional_url1","https://$shared_host$shared_path/$additional_url1");
			test_init("off",$host,"$path/$additional_url2","https://$shared_host$shared_path/$additional_url2");

		echo("\n\t## SWITCH TO HTTP\n\n");

			test_init("on",$shared_host,"$shared_path/wp-admin/","http://$host$path/wp-admin/");
			test_init("on",$shared_host,"$shared_path/some_post/","http://$host$path/some_post/");

		echo("\n\t## SWITCH TO CORRECT HOST\n\n");

			test_init("off",$shared_host,$shared_path,"http://$host$path");
			test_init("on",$host,"$path/wp-admin/profile.php","https://$shared_host$shared_path/wp-admin/profile.php");
			test_init("off",$host,"$path/wp-admin/profile.php","https://$shared_host$shared_path/wp-admin/profile.php");
			test_init("on",$shared_host,"$shared_path/wp-admin/","http://$host$path/wp-admin/");

		echo("\n\t## DO NOTHING\n\n");

			test_init("off",$host,"$path/some_post/");
			test_init("on",$shared_host,"$shared_path/wp-admin/profile.php");

	echo("\n\n\nTESTING as_ob_handler()\n\n\n");

		echo("\t## MAKE RELATIVE LINKS ABSOLUTE\n\n");

			test_ob_handler('href="edit-comments.php?page=akismet-admin"',
				'href="http://'.$host.$path.'/wp-admin/edit-comments.php?page=akismet-admin"');
			test_ob_handler('href="options-general.php?page=sitemap.php"',
				'href="http://'.$host.$path.'/wp-admin/options-general.php?page=sitemap.php"');

		echo("\n\t## SECURE ABSOLUTE LINKS\n\n");

			test_ob_handler("http://$host$path/wp-login.php?action=logout",
				"https://$shared_host$shared_path/wp-login.php?action=logout");

		echo("\n\t## SECURE RELATIVE LINKS\n\n");

			test_ob_handler('href="profile.php"',
				'href="https://'.$shared_host.$shared_path.'/wp-admin/profile.php"');

		echo("\n\t## SWITCH TO CORRECT HOST\n\n");

			$use_shared = true;
			test_ob_handler('href="https://'.$host.$path.'/"', 'href="https://'.$shared_host.$shared_path.'/"');

	echo("\n\n\nTESTING is_https()\n\n\n");

		test_is_https("HTTPS","on",false);
		test_is_https("X_RANDOM_HTTPS","it_is_on",true);

	echo("</pre>");



//
//
//	TESTING STUFF
//
//

	//
	//	spoof functions
	//

		function add_action(){
			return; }
		function add_filter(){
			return; }
		function current_user_can($what){
			return(true); }
		function get_option($k){
			global $options;
			return($options[$k]); }
		function is_admin(){
			return(true); }
		function is_user_logged_in(){
			return(true); }

	//
	//	all these variables need global scope
	//

		$options = null;
		$use_ssl = null;
		$use_shared = null;
		$shared_url = null;
		$secure_url = null;
		$additional_urls = null;
		$secure_users_only = null;
		$dir = null;
		$slash = null;
		$config_page = null;
		$config_parent = null;
		$https_key = null;
		$https_value = null;

		function pass_fail($pass){
			echo('<em style="color: '.($pass ? 'green">PASSED' : 'red">FAILED').'</em>'); }

	//
	//	test as_init()
	//

		function test_init($https,$http_host,$request_uri,$should_redirect_to=null,$quiet=false)
		{
			global $options,$use_ssl,$use_shared,$shared_url,$secure_url,$additional_urls,$secure_users_only;
			global $dir,$slash,$config_page,$config_parent,$https_key,$https_value;

		//
		//	only declare functions on first test
		//

			static $first_test = true;

			#if(!function_exists("update_option")):
			#	function update_option(){ return; }
			#endif;

		//
		//	set variables
		//

			$wp_version = 999;

			unset($_SERVER["HTTPS"]);
			unset($_SERVER[$https_key]);
			if($https === "on") $_SERVER[$https_key] = $https_value;
			$_SERVER["HTTP_HOST"] = $http_host;
			$_SERVER["REQUEST_URI"] = $request_uri;

		//
		//	run admin ssl
		//

			require("admin-ssl.php");

			if(!$quiet)
			{
				$redirect_to = as_init(false);
				$pass = $redirect_to === $should_redirect_to ? true : false;
				pass_fail($pass);
				echo("\t\t\thttp".($https === "on" ? "s" : "")."://".$http_host.$request_uri);
				echo($redirect_to ? "\n\t\t\twould redirect to\n\t\t\t$redirect_to" : " would not redirect");
				if(!$pass)
				{
					if($should_redirect_to) echo("\n\t\t\tand it should have redirected to\n\t\t\t$should_redirect_to");
					else echo("\n\t\t\tand it should not have redirected at all");
				}
				echo("\n\n");
			}

		//
		//	no longer the first test
		//

			$first_test = false;
		}

	//
	//	test as_ob_hander()
	//

		function test_ob_handler($buffer,$should_change_to=null)
		{
			global $host,$path;

			test_init("off",$host,$path,null,true);

			$change_to = as_ob_handler($buffer);
			$pass = $change_to === $should_change_to ? true : false;
			pass_fail($pass);
			echo("\t\t\t$buffer".($change_to ? "\n\t\t\twould be changed to\n\t\t\t$change_to" : " would not be changed"));
			if(!$pass) echo("\n\t\t\tand it should have been changed to\n\t\t\t$should_change_to");
			echo("\n\n");
		}

	//
	//	test is_https()
	//

		function test_is_https($k,$v,$should_be_https)
		{
			global $host,$path;
			global $https_key,$https_value;

			test_init("off",$host,$path,null,true);

			unset($_SERVER[$https_key]);
			$_SERVER[$k] = $v;

			$is_https = is_https();
			$pass = $is_https === $should_be_https ? true : false;
			pass_fail($pass);
			echo("\t\t\tHTTPS ($k) is set to ".($is_https ? "on" : "off"));
			if(!$pass) echo("\n\t\t\tand it should be ".($should_be_https ? "on" : "off"));
			echo("\n\n");
		}

?>