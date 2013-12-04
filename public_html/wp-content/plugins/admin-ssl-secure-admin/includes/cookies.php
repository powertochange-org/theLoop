<?php
/**
 * includes/cookies.php
 *
 * Handles cookies
 *
 * Author: BCG
 *
 */

//
//	hold cookie information globally
//

	$cookie_value = false;
	$cookie_expire = false;

//
//	sets or clears siteurl cookie
//

	function as_siteurl_cookie($action)
	{
		global $cookie_value,$cookie_expire,$dir,$plugins_dir,$secure_url;

	//
	//	continue only if action is 'set' and there is a cookie value,
	//	or if action is 'clear'
	//

		$continue = false;

		if($action === "set" && $cookie_value) $continue = true;
		elseif($action === "clear")
		{
			$cookie_value = " ";
			$cookie_expire = 1;
			$continue = true;
		}

	//
	//	redirect to cookie script - only ever called from wp-login.php
	//

		if($continue)
		{
			$path = "/".content_dir()."$plugins_dir/$dir/admin-ssl-cookie.php";
			$file = str_replace("/wp-login.php","",$_SERVER["SCRIPT_FILENAME"]).$path;

			as_log("as_siteurl_cookie()\nPath to admin-ssl-cookie.php: $file");

			if(file_exists($file))
			{
			//
			//	build the URL to redirect to after setting the cookie
			//

				if(redirect_to() && redirect_to() !== "wp-admin/")
				{
					if(strpos(redirect_to(),"http") === 0) $redirect = redirect_to();
					elseif(strpos(redirect_to(),"/") === 0) $redirect = scheme($use_ssl).host().redirect_to();
					else $redirect .= $secure_url."/".redirect_to();
				}
				else $redirect = $secure_url."/wp-login.php";

			//
			//	build the URL to admin-ssl-cookie.php with the cookie data
			//

				$location = rtrim(get_option("siteurl"),"/");
				$location .= "$path?name=".AUTH_COOKIE."&value=$cookie_value";
				$location .= "&expire=$cookie_expire&path=".COOKIEPATH."&domain=".COOKIE_DOMAIN;
				$location .= "&redirect=".urlencode($redirect);

				as_log("as_siteurl_cookie()\nRedirecting to: $location");
				as_redirect($location);
			}
		}
	}
?>