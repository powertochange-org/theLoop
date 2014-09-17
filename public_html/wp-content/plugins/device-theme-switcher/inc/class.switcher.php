<?php
	
	/**
	 * Device Theme Switcher - Load the plugin theme switching functionality
	 *
	 * The theme switching utilizes the MobileESP library to detect
	 * the browser User Agent and determine if it's a 'handheld' or 'tablet'.
	 * This plugin then taps into the WordPress template and stylesheet hooks 
	 * to deliver the alternately set themes in Appearance > Device Themes.
	 *
	 * This class is instantiated in dts_controller.php on each WordPress load 
	 * (only public, nothing is instantiated within the admin). 
	 */
	class DTS_Switcher {
		/**
		 * Here we use the contructor more like the main controller
		 * Below the three main facets of theme delivery occur;
		 * 1) Grab the saved theme preferences from the DB
		 * 2) Detect the device the current user is using
		 * 3) Deliver the theme for their device, or an alternate (i.e. 'View Full Website')
		 */
		public function __construct() {
			//Retrieve the admin's saved device theme
			$this->retrieve_saved_device_themes();
			//Detect the user's device			
			$this->device = $this->detect_users_device();
			//Determine if a theme override is in order, i.e. 'View Full Website'
			$this->detect_requested_theme_override();
		}//__construct
		
		/**
		 * Retrive the saved device/theme selections (Appearance > Device Themes)
		 * 
		 * Nothing is returned, however, $this->{device}_{theme} variables are 
		 * created for each option. E.g. $this->handheld_theme, $this->tablet_theme, 
		 * $this->low_support_theme, and $this->active_theme. These variables are used
		 * during the detection of which theme is delivered to which device 
		 */
		// ------------------------------------------------------------------------
		// RETRIEVE DEVICE THEME SELECTION SET BY ADMIN
		// ------------------------------------------------------------------------
		public function retrieve_saved_device_themes () {			
		    //The theme option is a url encoded string containing 3 values for name, template, and stylesheet
		    //See the ->active_theme array below for an idea of what is within each 
		    parse_str(get_option('dts_handheld_theme'), $this->handheld_theme);
		    parse_str(get_option('dts_tablet_theme'), $this->tablet_theme);
		    parse_str(get_option('dts_low_support_theme'), $this->low_support_theme);
		    
		    //Retrieve the current active theme
		    $this->active_theme = array(
		    	'name' => get_option('current_theme'),
		    	'template' => get_option('template'),
				'stylesheet' => get_option('stylesheet')
		   	);
		}//retrieve_saved_device_themes
		
		/**
		 * Device Detection
		 *
		 * Detect the user's device by using the MobileESP library written by Anthony Hand [http://blog.mobileesp.com/].
		 * Return the string name of their device.
		 * 
		 * @return string device The current user's device in one of four options: 
		 * active, handheld, tablet, low_support
		 */
		public function detect_users_device () {
			//Default is active (default computer theme set by the admin) until it's overridden
			$device = 'active';

			//Give the handheld theme to any low_support device
			//UNLESS one has been set in the admin already
			$low_support_device = 'handheld' ;
			$low_support_theme = get_option('dts_low_support_theme');
			if (!empty($low_support_theme) && is_array($low_support_theme)) : 
				if (isset($low_support_theme['name'])) : 
					if (!empty($low_support_theme['name'])) : 
						//Detect if the device is a low support device (poor css and javascript rendering / older devices)
						$low_support_device = 'low_support' ;
					endif;
				endif;
			endif;

			//Check for Varnish Device Detect: https://github.com/varnish/varnish-devicedetect/
			//Thanks to Tim Broder for this addition! https://github.com/broderboy | http://timbroder.com/
			$http_xua_handheld_devices = array(
			 	'mobile-iphone', 
			 	'mobile-android', 
			 	'mobile-firefoxos', 
			 	'mobile-smartphone', 
			 	'mobile-generic'
			);
			$http_xua_tablet_devices = array(
				'tablet-ipad', 
				'tablet-android'
			);

			//Determine if the HTTP X UA server variable is present
			if (isset($_SERVER['HTTP_X_UA_DEVICE'])) :
				//if it is, determine which device type is being used
				if (in_array($_SERVER['HTTP_X_UA_DEVICE'], $http_xua_handheld_devices)) : $device = 'handheld' ;
				elseif (in_array($_SERVER['HTTP_X_UA_DEVICE'], $http_xua_tablet_devices)) : $device = 'tablet' ; endif;
			else : //DEFAULT ACTION - Use MobileESP to sniff the UserAgent string
				//Include the MobileESP code library for acertaining device user agents
				include_once('mobile-esp.php');
				//Setup the MobileESP Class
				$ua = new uagent_info;
				//Detect if the device is a handheld
				if ($ua->DetectSmartphone() || $ua->DetectTierRichCss()) $device = 'handheld' ;
				//Detect if the device is a tablet
				if ($ua->DetectTierTablet() || $ua->DetectKindle() || $ua->DetectAmazonSilk()) $device = 'tablet' ;
				//Detect if the device is a low_support device (poor javascript and css support / text-only)
				if ($ua->DetectBlackBerryLow() || $ua->DetectTierOtherPhones()) $device = $low_support_device ;
			endif;
			
			//Return the user's device
			return $device ;
		}//deliver_theme_to_device
		
		/**
		 * Default Theme Override
		 *
		 * Called when this DTS_Switcher class is instantiated, the following
		 * logic determines if the user is requesting an alternate to the default theme. 
		 * By default the user is given their device theme as set in the admin. 
		 * An example of an override would be, 'View Full Website', as we're overriding
		 * the default 'handheld' theme if the user is on an iPhone.
		 *
		 * While this function does not return anything, it does, under certain conditions,
		 * populate a class variable named $theme_override containing a string of the 
		 * override theme name, e.g. 'active', 'handheld', 'tablet', 'low_support'. 
		 * 
		 * The following will also, under certain conditions, set and destroy a cookie
		 * to be stored in the user's browser. This only occurs when an alternate theme
		 * is requested, so the user can browse through the full website and the site 
		 * 'knows' the user requested the full site even though their on an iPhone..
		 */
		public function detect_requested_theme_override () {
			$this->theme_override = $requested_theme = "";
			$cookie_name = get_option('dts_cookie_name') ;
			if (empty($cookie_name)) $cookie_name = DTS_Core::build_cookie_name();
			update_option('dts_cookie_name', $cookie_name); 

			$cookie_lifespan = 0 ;

			//Is the user requesting a theme override?
			//This is how users can 'view full website' and vice versa
			if (isset($_GET['theme'])) : //i.e. site.com?theme=active
				//Clean the input data we're testing against
				$requested_theme = $_GET['theme'];				
				//Does the requested theme match the detected device theme?
				if ($requested_theme == $this->device) : 
					//The default/active theme is given back and their cookie is going to be removed
					//this condition typically exsits when someone is navigating "Back to Mobile"
					setcookie($cookie_name, "", 1, COOKIEPATH, COOKIE_DOMAIN, false);
				else : 
					//No it does not
					//this condition generally means that the user has just clicked "View full website"
					//Kill the request if it isn't valid, i.e. don't try to load ?theme=fooeybear unless it really exists
					if (isset($this->{$requested_theme . "_theme"})) :
						//Only proceed if $this->requested_theme is not empty, hence it's a valid theme
						if (!empty($this->{$requested_theme . "_theme"})) : 							
							//Retrieve the stored cookie lifespan
							//chosen in Appeance > Device Themes 
							$cookie_lifespan = get_option('dts_cookie_lifespan');
							if ($cookie_lifespan == 0) : 
								$cookie_expiration = 0 ;
							else : 
								$cookie_expiration = time() + $cookie_lifespan ;
							endif;

							//Build an array of the requested theme settings we'll need to know about when
							//the user returns to the site (or navigates to another page)
							//this way we can load the theme they previously requested
							$cookie_settings = array('theme' => $requested_theme, 'device' => $this->device);
							//Set a cookie in the users browser to identify the theme they've requested
							setcookie($cookie_name, json_encode($cookie_settings), $cookie_expiration, COOKIEPATH, COOKIE_DOMAIN, false);
							//Return the requested
							$this->theme_override = $requested_theme;
						endif;
					endif;
				endif;
			else :			
				//there is no new override being requested
				//Check if there is an already existant override stored in a cookie
				if (isset($_COOKIE[$cookie_name])) : 
					//The requested theme is an array so we stored it as a json encoded string
					//lets decode that so we can see whats in it
					//Note: once decoded we'll be working with an object not an array
					$dts_cookie = json_decode($_COOKIE[$cookie_name]);
					if (isset($dts_cookie->theme)) : 
						//Kill the request if it isn't valid
						if (isset($this->{$dts_cookie->theme . "_theme"})) : 
							//allow the override to continue
							$this->theme_override = $dts_cookie->theme;
						endif;
					endif;
				endif;
			endif;
			//
			//!! If none of the above conditions triggered 
			//!! the user is given their device-assigned theme
			//
		}//deliver_theme
				
		/**
		 * Deliver Template
		 * 
		 * Called in dts_controller.php via the deliver_template WordPress filter
		 * By default, the active theme, set in Appearance > Themes is given to the user
		 * Secondly, if the user is visitng the site with a handheld or tablet they get
		 * the themes set in Appearance > Device Themes. And lastly, if the user requested 
		 * an alternate default theme (ex: 'View full Site'), give them that theme. 
		 *
		 * All the logic prior to this sets a few variables, the actual delivery is here..
		 * @return array WordPress expected array containing the theme 'name', 'template', and 'stylesheet'
		 */
		static function deliver_template(){
			return DTS_Switcher::deliver_theme_file('template');
		}//deliver_template
		
		/**
		 * Deliver Stylesheet
		 * 
		 * Called in dts_controller.php via the deliver_stylesheet WordPress filter
		 * By default, the active theme, set in Appearance > Themes is given to the user
		 * Secondly, if the user is visitng the site with a handheld or tablet they get
		 * the themes set in Appearance > Device Themes. And lastly, if the user requested 
		 * an alternate default theme (ex: 'View full Site'), give them that theme. 
		 *
		 * NOTE** When using a parent theme, the 'stylesheet' is different. That's the 
		 * reason for two separate filters.
		 *
		 * All the logic prior to this sets a few variables, the actual delivery is here..
		 * @return array WordPress expected array containing the theme 'name', 'template', and 'stylesheet'
		 */
		static function deliver_stylesheet(){
			return DTS_Switcher::deliver_theme_file('stylesheet');
		}//deliver_stylesheet

		/**
		 * Return a theme file, template or stylesheet. 
		 *
		 * This is a single wrapper function for two hooks. The $file argument passed
		 * in determines which theme file is returned to the calling hook, 
		 * 
		 * @param  string $file the name of the theme asset being requested. Can be either 'name', 'template', or 'stylesheet'
		 * @return string the theme directory name of the theme template or stylesheet. These names are rather
		 * anbiguous and not really descriptive of whats really being returned. The 'template' is the theme directory.
		 * 'stylesheet' is also typically the theme directory, EXCEPT when using a parent theme. In which case
		 * 'stylesheet' is actually the parent theme direcotry. 
		 */
		static function deliver_theme_file ($file) {
			//see the dts::__contruct for a walkthrough on how this object is created
			global $dts; 

			//Update the active theme setting 
			//(so that other plugins can modify pre_option_template or pre_option_stylesheet)
		    $dts->active_theme = array(
		    	'name' => get_option('current_theme'),
		    	'template' => get_option('template'),
				'stylesheet' => get_option('stylesheet')
		   	);

		   	//If a theme override has been requested, return that theme's template directory
			if (!empty($dts->{$dts->theme_override . "_theme"})) : 
				return $dts->{$dts->theme_override . "_theme"}[$file]; 

			//If there is no theme override, return the users device assigned theme
			elseif (!empty($dts->{$dts->device . "_theme"})) : 
				return $dts->{$dts->device . "_theme"}[$file]; 

			//And if all else fails, simply return the active / default theme
			else : 
				return $dts->active_theme[$file] ; 
			endif;
		}//deliver_theme_file

		/**
		 * Switch Theme Link Generation
		 *
		 * Generate an html anchor link for the end user to click and 'View Full Website', or to go 'Back to Mobile Site'.
		 * This function is called by the plugins template tags and shortcodes.
		 * 
		 * @param  string  $link_type valid values are 'active' or 'device'. E.g. generate a link to
		 * view the active theme, aka 'View Full Website', or a link back to the device i.e. 'Back to Mobile Site'
		 * @param  string  $link_text The text within the generated link, i.e. <a>View Full Website</a>
		 * @param  array   $css_classes And array of additional css classes to be applied to the generated link. 
		 * Note: the classes to-full-website and back-to-mobile are added by default
		 * @param  boolean $echo 1 or 0 or true or false, by default the link is echoed to the screen. False will 
		 * return it for use as a string.
		 */
		public static function build_html_link ($link_type = 'active', $link_text = "View Full Website", $css_classes = array(), $echo = true) {
			global $dts;
			$cookie_name = get_option('dts_cookie_name') ;
			$html_output = $target_theme = "";
			
			//The link_type will either be 'active' or 'device'
			//'active' pertains to the link for devices to 'view the full website'\'active' theme
			//'device' pertains to the link for devices which viewed the full website to go 'Back to the Mobile Theme'
			switch ($link_type) : 
				case 'active' : 
					//add a css class to help identify the link type for developers to style the output how they please
					array_unshift($css_classes, 'to-full-website');
					//determine if the 'View Full Website' link should be displayed or not
					if ((($dts->device != 'active') || ($dts->device == 'active' && !empty($dts->theme_override))) && ## don't show it if the user receives the active theme by default
						$dts->theme_override != 'active' && ## don't show it if the user has requested the active theme
						!empty($dts->{$dts->device . "_theme"})) ## don't show it if the device theme does not exist, and the user is currently viewing the active theme
							$target_theme = "active"; ## display the link under all other conditions
				break;
				case 'device' : 
					//add a css class to help identify the link type for developers to style the output how they please
					array_unshift($css_classes, 'back-to-mobile');	
					
					/**
					 * show the link..
					 *
					 * when the current device is not 'active' (not really a device)
					 * when there is a cookie set and no GET theme variable
					 * when there is a cookie set and and there is a GET theme variable which does not equal the current device
					 * when there is no cookie set, but there is a GET theme variable which does not equal the current device
					 */
					if ($dts->device != 'active') :
						if (isset($_COOKIE[$cookie_name])) : 
							if (isset($_GET['theme'])) : 
								if ($_GET['theme'] != $dts->device) : 
									$target_theme = $dts->device;
								endif;
							else :
								$target_theme = $dts->device;
							endif;
						else : 
							if (isset($_GET['theme'])) : 
								if ($_GET['theme'] != $dts->device) $target_theme = $dts->device;
							endif;
						endif;
					endif;
				break;
			endswitch;

			//Only output the html link if the above logic determines if a link should be shown or not
			if (!empty($target_theme)) :
				//Start the link as protocolless and containing the current host address
				$link_href  = "//" . $_SERVER['HTTP_HOST']; #ex //local.wordpress.dev or //www.site.com
				//Next add on everything before a ?, i.e. any pretty url pages
				$link_href .= strtok($_SERVER['REQUEST_URI'], '?'); #ex //local.wordpress.dev/sample-page/
				
				//Create an array $query_array which contains the current query vars in GET
				parse_str($_SERVER['QUERY_STRING'], $query_array); #ex: array('page_id' => 2)
				//Add the Device Theme Switcher 'theme=active' query var to the other existing vars
				$query_array['theme'] = $target_theme; #ex: array('page_id' => 2, 'theme' => 'active')
				//Add our assembled query vars onto the end of the link
				$link_href .= "?" . http_build_query($query_array); #ex: //local.wordpress.dev/sample-page/?theme=active

				//Ensure 'dts-link' is the first css class in the link
				array_unshift($css_classes, 'dts-link');
				//Build the HTML link
				$html_output = "<a href='$link_href' title='$link_text' class='" . implode(' ', $css_classes) . "'>$link_text</a>\n";
			endif; 

			if ($echo) : 
				echo $html_output; ## Echo the HTML link
			else : 
				return $html_output; ## Return the HTML link
			endif; 
		}//build_html_link
	} //END DTS_Switcher Class
