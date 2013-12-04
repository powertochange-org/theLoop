<?php
/**
 * includes/https.php
 *
 * Perform HTTP/HTTPS redirection and replacing
 *
 * Author: BCG
 *
 */

//
//	if SSL is enabled, force WordPress to use both the SSL and HTTP cookies
//

	function as_secure_cookie($secure_cookie,$credentials)
	{
		global $use_ssl;

		if(empty($credentials) || $use_ssl === false)
		{
			as_log("Credentials empty or use_ssl is false");
			return($secure_cookie);
		}
		elseif($use_ssl)
		{
			as_log("Verifying user and setting HTTP auth cookie");
			$user = wp_authenticate($credentials['user_login'], $credentials['user_password']);

		//
		//	set the non-secure cookies and let WP set the secure cookie
		//

			wp_set_auth_cookie($user->ID, $credentials['remember'], false);
			return(true);
		}
	}

//
//	returns additional urls as an array
//

	function as_secure_uris($separate=false)
	{
		global $additional_urls;

	//
	//	build arrays of default and additional urls to secure
	//

		$tmp1 = array();

		$tmp1[] = "wp-login.php";
		$tmp1[] = "wp-admin/profile.php";
		$tmp1[] = "wp-admin/user-edit.php";
		$tmp1[] = "wp-admin/users.php";

		if(is_https()) # these need securing whenever HTTPS is enabled
		{
			$tmp1[] = "wp-admin/css/"; # admin css files
			$tmp1[] = "wp-admin/images/"; # admin images
			$tmp1[] = "wp-admin/js/"; # admin javascript files
			$tmp1[] = "wp-admin/admin-ajax.php"; # admin ajax scripts
			$tmp1[] = "wp-admin/rtl.css"; # random admin css file
			$tmp1[] = "wp-admin/wp-admin.css"; # main admin css file

			$tmp1[] = content_dir(); # secures themes, plugins and uploads
			$tmp1[] = "wp-includes/"; # secures WP javascript files etc
		}

		$tmp2 = explode("\n",$additional_urls);

	//
	//	clean both arrays so they match properly later
	//

		if(!function_exists("as_trim")){ function as_trim(&$v){ $v = trim($v); } }

		array_walk($tmp1,"as_trim");
		array_walk($tmp2,"as_trim");

	//
	//	remove any empty values from the additional urls array
	//

		foreach($tmp2 as $k => $v) if($v == "") unset($tmp2[$k]);

	//
	//	return additional uris
	//

		if($separate) return(array("default" => $tmp1,"additional" => $tmp2));
		else return(array_merge($tmp1,$tmp2));
	}

//
//	returns ignore urls as an array
//

	function as_ignore_urls()
	{
		global $ignore_urls;

	//
	//	split ignore urls by new lines
	//

		$tmp = explode("\n",$ignore_urls);

	//
	//	make sure $tmp is actually an array
	//

		if(is_array($tmp))
		{
		//
		//	clean tmp array so they match properly later
		//

			if(!function_exists("as_trim")){ function as_trim(&$v){ $v = trim($v); } }
			array_walk($tmp, "as_trim");

		//
		//	remove any empty values from the ignore urls array
		//

			foreach($tmp as $k => $v) if($v == "") unset($tmp[$k]);
		}

	//
	//	return ignore urls
	//

		return($tmp);
	}

//
//	runs on WordPress init, performs all sorts of clever redirecting
//

	function as_init()
	{
		global $use_ssl,$secure_url;

	//
	//	check Admin SSL version and perform DB maintenance as required
	//

		$previous_version = as_option("get","version");
		if($previous_version < 2.0)
		{
		//
		//	remove old options from the database
		//

			as_option("delete", "use_shared");
			as_option("delete", "shared_url");

		//
		//	reset use SSL when switching to the new version in case shared was being used before
		//

			as_option("update", "use_ssl", false);
			$use_ssl = false;
		}

	//
	//	set the current version of the Admin SSL plugin so we know it's been migrated next time
	//

		as_option("update", "version", AS_VERSION);

		if($use_ssl)
		{
		//
		//	disable redirection if testing
		//

			$do_redirect = !defined("TEST");

		//
		//	check if any of the secure uris matches the current request uri
		//

			$match = false;
			foreach(as_secure_uris() as $uri) if(strpos(req_uri(),$uri) !== false) $match = true;

		//
		//	get the HTTP hosts for secure and non-secure URLs
		//

			$tmp = parse_url($secure_url);
			$secure_host = $tmp["host"];

			$tmp = parse_url(get_option("siteurl"));
			$siteurl_host = $tmp["host"];

			$host_should_be = is_https() ? $secure_host : $siteurl_host;
			$host_match = host() === $host_should_be  ? true : false;

		//
		//	for redirection between Shared SSL URL and site URL we need the bit of the URL
		//	AFTER either $secure_url or siteurl - as an example:
		//	to redirect from http://your_blog.com/wp-admin/profile.php
		//		to https://some_host.com/~username/wp-admin/profile.php
		//	we need to get /wp-admin/profile.php from siteurl as the path to add to $secure_url
		//

			if(host() === $secure_host) $url_info = parse_url($secure_url);
			elseif(host() === $siteurl_host) $url_info = parse_url(get_option("siteurl"));
			else # if the host is something odd, send to blog home
			{
				as_log("as_init()\nThe host ('".host()."') is neither the ".
					"secure host ('$secure_host') or the siteurl host ('$siteurl_host') - ".
					"Redirecting to blog home page");
				as_log("as_init()\nRedirecting to: ".get_option("siteurl"));
				if($do_redirect) as_redirect(get_option("siteurl"));
				else return(get_option("siteurl")); # return value for testing purposes
			}

			$url_path_len = strlen($url_info["path"]);
			$url_path = substr(req_uri(),$url_path_len);

			as_log("as_init()\nURL path: $url_path");

		//
		//	redirect as necessary - secure or de-secure page - ensure correct HTTP host is being used
		//

			if($match)
			{
				as_log("as_init()\nMatched url");

			//
			//	parse the url we need to redirect to
			//

				$url = parse_url($use_ssl ? $secure_url : get_option("siteurl"));

			//
			//	build and redirect to the correct URL
			//

				if((!is_https() && $use_ssl) || (is_https() && !$use_ssl) || host() !== $url["host"])
				{
					$location =	scheme($use_ssl).$url["host"].rtrim($url["path"],"/").$url_path;
					as_log("as_init()\nRedirecting to: $location");

					if($do_redirect) as_redirect($location);
					else return($location); # return value for testing purposes
				}

			//
			//	when switching between URLs need to remove path info before wp-admin
			//

				elseif($use_ssl && is_https() && redirect_to())
				{
					$wp_admin = strpos(redirect_to(),"wp-admin");
					if($wp_admin !== 0) $_REQUEST["redirect_to"] = substr(redirect_to(),$wp_admin);
				}
			}

		//
		//	if there is no match and the page is secured, or the hosts don't match, switch to HTTP
		//

			elseif(is_https() || !$host_match)
			{
				as_log("as_init()\nDid not match url and either it's secure or the hosts don't match");

				$location = get_option("siteurl").$url_path;
				as_log("as_init()\nRedirecting to: $location");

				if($do_redirect) as_redirect($location);
				else return($location); # return value for testing purposes
			}

		//
		//	start output buffering
		//

			if($use_ssl && !defined("TEST")) ob_start("as_ob_handler");
		}
	}

//
//	overrides check_admin_referer() in /wp-includes/pluggable.php to use $secure_url,
//	but only if wp-admin/ is in $secure_uris
//

	if(!function_exists("check_admin_referer")):
	function check_admin_referer($action=-1,$query_arg="_wpnonce"){
		global $secure_url;
		$secure_uris = as_secure_uris();
		$adminurl = strtolower(in_array("wp-admin",$secure_uris) || in_array("wp-admin/",$secure_uris) ?
			$secure_url : get_option("siteurl"))."/wp-admin";
		$referer = strtolower(wp_get_referer());
		$result = wp_verify_nonce($_REQUEST[$query_arg], $action);
		if(!$result && !(-1 == $action && strpos($referer, $adminurl) !== false)){
			wp_nonce_ays($action);
			die();
		}
		do_action("check_admin_referer",$action,$result);
		return $result;
	}endif;

//
//	output buffer handler to replace HTTP urls with HTTPS urls
//

	function as_ob_handler($buffer)
	{
		global $secure_url,$secure_users_only;

		if(!function_exists("get_option")) return($buffer);

	//
	//	log call to output buffer handler
	//

		as_log("as_ob_handler()\nBuffer: ".substr($buffer, 0, 10)."...");

	//
	//	check ignore urls
	//

		$ignore_urls = as_ignore_urls();

		$continue = true;
		foreach($ignore_urls as $uri) if(strpos(req_uri(), $uri) !== false) $continue = false;

		if($continue)
		{
		//
		//	build site urls and get secure uris
		//

			$siteurl = get_option("siteurl")."/";
			$home = get_option("home")."/";
			$secure = $secure_url."/";

			$secure_uris = as_secure_uris(true);

		//
		//	on admin side, links are not absolute but relative - change this
		//

			if(is_admin())
			{
				$pattern = "/href=['\"]((?<!http)[\w-]*\.php.*)['\"]/U";
				$replacement = "href=\"$siteurl"."wp-admin/\$1\"";
				$buffer = preg_replace($pattern,$replacement,$buffer);
			}

		//
		//	add default and additional uris
		//

			if(is_array($secure_uris["default"]))
				foreach($secure_uris["default"] as $uri)
				{
					$replace_this[] = $siteurl.$uri;
					$with_this[] = $secure.$uri;

					$replace_this[] = $home.$uri;
					$with_this[] = $secure.$uri;
				}

			if(is_array($secure_uris["additional"]) &&
				( (is_user_logged_in() && $secure_users_only) || !$secure_users_only ))
				foreach($secure_uris["additional"] as $uri)
				{
					$replace_this[] = $siteurl.$uri;
					$with_this[] = $secure.$uri;

					$replace_this[] = $home.$uri;
					$with_this[] = $secure.$uri;
				}

		//
		//	additional securing
		//

			if(is_https() && !defined("TEST") && is_preview())
			{
				$replace_this[] = $siteurl;
				$with_this[] = $secure;

				$replace_this[] = $home;
				$with_this[] = $secure;
			}

		//
		//	replace all the links and return $buffer
		//

			$replace_this[] = "</body>";
			$with_this[] = "<!-- filtered by Admin SSL --></body>";

			as_log("Buffer Pre: $buffer");

			$buffer = str_replace($replace_this,$with_this,$buffer);

			as_log("Buffer Post: $buffer");
		}

		return($buffer);
	}

//
//	ensure wp_redirect() sends people to the correct location
//

	function as_redirect_check($location,$status=false){
		return(as_ob_handler($location)); }

//
//	wp_mail hook
//

	function as_mail($a)
	{
		$a["message"] = as_ob_handler($a["message"]);
		return($a);
	}

?>