<?php
/*
Plugin Name: wpCAS with Active Directory
Version: :Jan 20, 2012
Description: Plugin to integrate WordPress with existing CAS, grabbing new user information from Active Directory.  Based on wpcas-w-ldap plugin.
Author: Brent Nesbitt
Author URI: 
*/

/* 
 Copyright (C) 2009 Ioannis C. Yessios

 This plugin owes a huge debt to 
 Casey Bisson's wpCAS, copyright (C) 2008
 and released under GPL.
 http://wordpress.org/extend/plugins/wpcasad/

 Casey Bisson's plugin owes a huge debt to Stephen Schwink's CAS Authentication plugin, copyright (C) 2008 
 and released under GPL. 
 http://wordpress.org/extend/plugins/cas-authentication/

 It also borrowed a few lines of code from Jeff Johnson's SoJ CAS/LDAP Login plugin
 http://wordpress.org/extend/plugins/soj-casldap/

 This plugin honors and extends Bisson's and Schwink's work, and is licensed under the same terms.

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA	 02111-1307	 USA 
*/



if (file_exists( dirname(__FILE__).'/wpcasad-conf.php' ) ) 
	include_once( dirname(__FILE__).'/wpcasad-conf.php' ); // attempt to fetch the optional config file

if ((isset($wpcasad_options) && (!is_array($wpcasad_options))))
#if (!is_array($wpcasad_options))
        $wpcasad_optons = array();


$wpcasad_use_options = wpcasad_getoptions();

$cas_configured = true;

// try to configure the phpCAS client
if ($wpcasad_use_options['include_path'] == '' ||
		(include_once $wpcasad_use_options['include_path']) != true)
	$cas_configured = false;

if ($wpcasad_use_options['server_hostname'] == '' ||
		$wpcasad_use_options['server_path'] == '' ||
		intval($wpcasad_use_options['server_port']) == 0)
	$cas_configured = false;

if ($cas_configured) {
	phpCAS::client($wpcasad_use_options['cas_version'], 
		$wpcasad_use_options['server_hostname'], 
		intval($wpcasad_use_options['server_port']), 
		$wpcasad_use_options['server_path']);
	
	// function added in phpCAS v. 0.6.0
	// checking for static method existance is frustrating in php4
	$phpCas = new phpCas();
	if (method_exists($phpCas, 'setNoCasServerValidation'))
		phpCAS::setNoCasServerValidation();
	unset($phpCas);
	// if you want to set a cert, replace the above few lines
 }

// plugin hooks into authentication system
add_action('wp_authenticate', array('wpCASLDAP', 'authenticate'), 10, 2);
add_action('wp_logout', array('wpCASLDAP', 'logout'));
add_action('lost_password', array('wpCASLDAP', 'disable_function'));
add_action('retrieve_password', array('wpCASLDAP', 'disable_function'));
add_action('password_reset', array('wpCASLDAP', 'disable_function'));
add_filter('show_password_fields', array('wpCASLDAP', 'show_password_fields'));

if (is_admin() ) {
	add_action( 'admin_init', 'wpcasad_register_settings' );
	add_action( 'admin_menu', 'wpcasad_options_page_add' );	
}
class wpCASLDAP {
	
	/*
	 We call phpCAS to authenticate the user at the appropriate time 
	 (the script dies there if login was unsuccessful)
	 If the user is not provisioned and wpcasad_useradd is set to 'yes', wpcasad_nowpuser() is called
	*/
	
	function authenticate() {
		global $wpcasad_use_options, $cas_configured, $blog_id;

		if ( !$cas_configured )
			die( __( 'wpCAS with Active Directory plugin not configured', 'wpcasad' ));

		if( phpCAS::isAuthenticated() ){
			// CAS was successful
			if ( $user = get_userdatabylogin( phpCAS::getUser() )){ // user already exists
				$udata = get_userdata($user->ID);
				
				if (!get_usermeta( $user->ID, 'wp_'.$blog_id.'_capabilities')) {
					if (function_exists('add_user_to_blog')) { add_user_to_blog($blog_id, $user->ID, $wpcasad_use_options['userrole']); }
				}
				
				// the CAS user has a WP account
				wp_set_auth_cookie( $user->ID );

				if( isset( $_GET['redirect_to'] )){
					//echo "<p> {$_GET['redirict_to']}</p>";
					//exit;
					
                                        wp_redirect( preg_match( ':^(http|/):', $_GET['redirect_to'] ) ? $_GET['redirect_to'] : site_url(  ));
                                        #wp_redirect( preg_match( '/^http/', $_GET['redirect_to'] ) ? $_GET['redirect_to'] : site_url(  ));
                                        die();

				}

				wp_redirect( site_url( '/wp-admin/' ));
				die();

			}else{
				// the CAS user _does_not_have_ a WP account
				if (function_exists( 'wpcasad_nowpuser' ) && $wpcasad_use_options['useradd'] == 'yes')
					wpcasad_nowpuser( phpCAS::getUser() );
				else
					die( __( 'you do not have permission here', 'wpcasad' ));
			}
		}else{
			// hey, authenticate
			phpCAS::forceAuthentication();
			die();
		}
	}
	
	
	// hook CAS logout to WP logout
	function logout() {
		global $cas_configured;

		if (!$cas_configured)
			die( __( 'wpCAS with Active Directory plugin not configured', 'wpcasad' ));

		phpCAS::logout( array( 'url' => get_settings( 'siteurl' )));
		exit();
	}

	// hide password fields on user profile page.
	function show_password_fields( $show_password_fields ) {
		if( 'user-new.php' <> basename( $_SERVER['PHP_SELF'] ))
			return false;

		$random_password = substr( md5( uniqid( microtime( ))), 0, 8 );

?>
<input id="wpcasad_pass1" type="hidden" name="pass1" value="<?php echo $random_password ?>" />
<input id="wpcasad_pass2" type="hidden" name="pass2" value="<?php echo $random_password ?>" />
<?php
		return false;
	}

	// disabled reset, lost, and retrieve password features
	function disable_function() {
		die( __( 'Sorry, this feature is disabled.', 'wpcasad' ));
	}
}

function wpcasad_nowpuser($newuserid) {
	global $wpcasad_use_options;
	
	if ($wpcasad_use_options['useldap'] == 'yes' && function_exists('ldap_connect') ) {
		$newuser = get_ldap_user($newuserid);
		//echo "<pre>";print_r($newuser);echo "</pre>";
		
		$userdata = $newuser->get_user_data();
	} else {
		$userdata = array(
				'user_login' => $newuserid,
				'user_password' => substr( md5( uniqid( microtime( ))), 0, 8 ),
				'user_email' => $newuserid.'@'.$wpcasad_use_options['email_suffix'],
				'role' => $wpcasad_use_options['userrole'],
			);
	}
	if (!function_exists('wp_insert_user'))
		include_once ( ABSPATH . WPINC . '/registration.php');
		
	$user_id = wp_insert_user( $userdata );
	
	$user = get_userdatabylogin($newuserid);

	if ( !$user_id || !$user) {
		$errors['registerfail'] = sprintf(__('<strong>ERROR</strong>: The login system couldn\'t register you in the local database. Please contact the <a href="mailto:%s">webmaster</a> !'), get_option('admin_email'));
		return;
	} else {
		wp_new_user_notification($user_id, $user_pass);
		wp_set_auth_cookie( $user->ID );

		if( isset( $_GET['redirect_to'] )){
			wp_redirect( preg_match( '/^http/', $_GET['redirect_to'] ) ? $_GET['redirect_to'] : site_url(  ));
			die();
		}

		wp_redirect( site_url( '/wp-admin/' ));
		die();
	}
}

function get_ldap_user($samaccountname) {
	global $wpcasad_use_options;
	$ds = ldap_connect($wpcasad_use_options['ldaphost'],$wpcasad_use_options['ldapport']);

	//Can't connect to LDAP.
	if(!$ds) {
		$error = 'Error in contacting the LDAP server.';
	} else {	
		//exit;
		// Make sure the protocol is set to version 3
		if(!ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)) {
			$error = 'Failed to set protocol version to 3.';
		} else {
			if (!isset($wpcasad_options['ldapbinddn'])) {
				$bind = @ldap_bind($ds,$wpcasad_use_options['ldapbinddn'],$wpcasad_use_options['ldapbindpass']);
				$search = ldap_search($ds, $wpcasad_use_options['ldapbasedn'], "samaccountname=$samaccountname");
			} else {
				//Connection made -- bind anonymously and get dn for username
				$bind = @ldap_bind($ds);
				$search = ldap_search($ds, $wpcasad_use_options['ldapbasedn'], "samaccountname=$samaccountname");
			}
			
			//Check to make sure we're bound.
			if(!$bind) {
				$error = 'Anonymous bind to LDAP failed.';
			} else {
				$info = ldap_get_entries($ds, $search);
				ldap_close($ds);
				return new wpcasaduser($info);
			}
			ldap_close($ds);
		}
	}
	return FALSE;
}

class wpcasaduser
{
	private $data = NULL;

	function __construct($member_array) {
		$this->data = $member_array;
	}

	function get_user_name() {
		if(isset($this->data[0]['cn'][0]))
			return $this->data[0]['cn'][0];
		else
			return FALSE;
	}
	
	function get_user_data() {
		global $wpcasad_use_options;
		if (isset($this->data[0]['samaccountname'][0]))
			return array(
				'user_login' => $this->data[0]['samaccountname'][0],
				'user_password' => substr( md5( uniqid( microtime( ))), 0, 8 ),
				'user_email' => $this->data[0]['mail'][0],
				'first_name' => $this->data[0]['givenname'][0],
				'last_name' => $this->data[0]['sn'][0],
				'role' => $wpcasad_use_options['userrole'],
				'nickname' => $this->data[0]['cn'][0],
				'user_nicename' => $this->data[0]['cn'][0]
			);
		else 
			return false;
	}
	
}


//----------------------------------------------------------------------------
//		ADMIN OPTION PAGE FUNCTIONS
//----------------------------------------------------------------------------

function wpcasad_register_settings() {
	global $wpcasad_options;
	
	$options = array('email_suffix', 'cas_version', 'include_path', 'server_hostname', 'server_port', 'server_path', 'useradd', 'userrole', 'ldaphost', 'ldapport', 'ldapbasedn', 'ldapbinddn', 'ldapbindpass', 'useldap');


	foreach ($options as $o) {
		if (!isset($wpcasad_options[$o])) {
			switch($o) {
				case 'cas_verion':
					$cleaner = 'wpcasad_oneortwo';
					break;
				case 'useradd':
				case 'useldap':
					$cleaner = 'wpcasad_yesorno';
					break;
				case 'email_suffix':
					$cleaner = 'wpcasad_strip_at';
					break;
				case 'userrole':
					$cleaner = 'wpcasad_fix_userrole';
					break;
				case 'ldapport':
				case 'server_port':
					$cleaner = 'intval';
					break;
				default:
					$cleaner = 'wpcasad_dummy';
			}
			register_setting( 'wpcasad', 'wpcasad_'.$o,$cleaner );
		}
	}
}

function wpcasad_strip_at($in) {
	return str_replace('@','',$in);
}
function wpcasad_yesorno ($in) {
	return (strtolower($in) == 'yes')?'yes':'no';	
}

function wpcasad_oneortwo($in) {
	return ($in == '1.0')?'1.0':'2.0';
}
function wpcasad_fix_userrole($in) {
	$roles = array('subscriber','contributor','author','editor','administrator');
	if (in_array($in,$roles))
		return $in;
	else 
		return 'subscriber';
}
function wpcasad_dummy($in) {
	return $in;
}

function wpcasad_options_page_add() {
	if (function_exists('add_management_page')) 
		add_submenu_page('options-general.php', 'wpCAS with AD', 'wpCAS with AD', 8, 'wpcasad', 'wpcasad_options_page');		
	else
		add_options_page( __( 'wpCAS with AD', 'wpcasad' ), __( 'wpCAS with AD', 'wpcasad' ), 8, basename(__FILE__), 'wpcasad_options_page');

} 

function wpcasad_getoptions() {
	global $wpcasad_options;

	$out = array (
			'email_suffix' => get_option('wpcasad_email_suffix'),
			'cas_version' => get_option('wpcasad_cas_version'),
			'include_path' => get_option('wpcasad_include_path'),
			'server_hostname' => get_option('wpcasad_server_hostname'),
			'server_port' => get_option('wpcasad_server_port'),
			'server_path' => get_option('wpcasad_server_path'),
			'useradd' => get_option('wpcasad_useradd'),
			'userrole' => get_option('wpcasad_userrole'),
			'ldaphost' => get_option('wpcasad_ldaphost'),
			'ldapport' => get_option('wpcasad_ldapport'),
			'useldap' => get_option('wpcasad_useldap'),
			'ldapbasedn' => get_option('wpcasad_ldapbasedn'),	
			'ldapbinddn' => get_option('wpcasad_ldapbinddn'),			
			'ldapbindpass' => get_option('wpcasad_ldapbindpass')			
		);
	
	if (is_array($wpcasad_options) && count($wpcasad_options) > 0)
		foreach ($wpcasad_options as $key => $val) {
			$out[$key] = $val;	
		}
	return $out;
}

function wpcasad_options_page() {
	global $wpdb, $wpcasad_options;
	
	//echo "<pre>"; print_r($wpcasad_options); echo "</pre>";
	// Get Options
	$optionarray_def = wpcasad_getoptions();
	
	?>
	<div class="wrap">
	<h2>CAS Authentication Options</h2>
	<form method="post" action="options.php">
		<?php
            settings_fields( 'wpcasad' );
        ?>
	<h3><?php _e( 'wpCAS with AD options', 'wpcasad' ) ?></h3>
	<h4><?php _e( 'Note', 'wpcasad' ) ?></h4>
	<p><?php _e( 'Now that you’ve activated this plugin, WordPress is attempting to authenticate using CAS, even if it’s not configured or misconfigured.', 'wpcasad' ) ?></p>
	<p><?php _e( 'Save yourself some trouble, open up another browser or use another machine to test logins. That way you can preserve this session to adjust the configuration or deactivate the plugin.', 'wpcasad' ) ?></p>
	<h4><?php _e( 'Also note', 'wpcasad' ) ?></h4>
	<p><?php _e( 'These settings are overridden by the <code>wpcasad-conf.php</code> file, if present.', 'wpcasad' ) ?></p>

	<?php if (!isset($wpcasad_options['include_path'])) : ?>
	<h4><?php _e( 'phpCAS include path', 'wpcasad' ) ?></h4>
    <blockquote><blockquote>
	<table width="500px" cellspacing="2" cellpadding="5" class="form-table">
		<tr>
			<td colspan="2"><?php _e( 'Full absolute path to CAS.php script', 'wpcasad' ) ?></td>
		</tr>
		<tr valign="center"> 
			<th width="300px" scope="row"><?php _e( 'CAS.php path', 'wpcasad' ) ?></th> 
			<td><input type="text" name="wpcasad_include_path" id="include_path_inp" value="<?php echo $optionarray_def['include_path']; ?>" size="35" /></td>
		</tr>
	</table>
    </blockquote></blockquote>
	<?php endif; ?>
    
    <?php if (!isset($wpcasad_options['cas_version']) ||
			!isset($wpcasad_options['server_hostname']) ||
			!isset($wpcasad_options['server_port']) ||
			!isset($wpcasad_options['server_path']) ) : ?>
	<h4><?php _e( 'phpCAS::client() parameters', 'wpcasadldap' ) ?></h4>
    <blockquote><blockquote>
	<table width="500px" cellspacing="2" cellpadding="5" class="editform">
	    <?php if (!isset($wpcasad_options['cas_version'])) : ?>
		<tr valign="center"> 
			<th width="300px" scope="row">CAS verions</th> 
			<td><select name="wpcasad_cas_version" id="cas_version_inp">
                    <option value="2.0" <?php echo ($optionarray_def['cas_version'] == '2.0')?'selected':''; ?>>CAS_VERSION_2_0</option>
                    <option value="1.0" <?php echo ($optionarray_def['cas_version'] == '1.0')?'selected':''; ?>>CAS_VERSION_1_0</option>
                </select>
			</td>
		</tr>
        <?php endif; ?>
	    <?php if (!isset($wpcasad_options['server_hostname'])) : ?>
		<tr valign="center"> 
			<th width="300px" scope="row"><?php _e( 'server hostname', 'wpcasad' ) ?></th> 
			<td><input type="text" name="wpcasad_server_hostname" id="server_hostname_inp" value="<?php echo $optionarray_def['server_hostname']; ?>" size="35" /></td>
		</tr>
        <?php endif; ?>
	    <?php if (!isset($wpcasad_options['server_port'])) : ?>
		<tr valign="center"> 
			<th width="300px" scope="row"><?php _e( 'server port', 'wpcasad' ) ?></th> 
			<td><input type="text" name="wpcasad_server_port" id="server_port_inp" value="<?php echo $optionarray_def['server_port']; ?>" size="35" /></td>
		</tr>
        <?php endif; ?>
	    <?php if (!isset($wpcasad_options['server_path'])) : ?>
		<tr valign="center"> 
			<th width="300px" scope="row"><?php _e( 'server path', 'wpcasad' ) ?></th> 
			<td><input type="text" name="wpcasad_server_path" id="server_path_inp" value="<?php echo $optionarray_def['server_path']; ?>" size="35" /></td>
		</tr>
        <?php endif; ?>
	</table>
    </blockquote></blockquote>
	<?php endif; ?>

    <?php if (!isset($wpcasad_options['useradd']) ||
			!isset($wpcasad_options['userrole']) ||
			!isset($wpcasad_options['useldap']) ||
			!isset($wpcasad_options['email_suffix']) ) : ?>
	<h4><?php _e( 'Treatment of Unregistered User', 'wpcasad' ) ?></h4>
    <blockquote><blockquote>

	<table width="500px" cellspacing="2" cellpadding="5" class="editform">
	    <?php if (!isset($wpcasad_options['useradd'])) : ?>
		<tr valign="center"> 
			<th width="300px" scope="row">Add to Database</th> 
			<td><input type="radio" name="wpcasad_useradd" id="useradd_yes" value="yes" 
            		<?php echo ($optionarray_def['useradd'] == 'yes')?'checked="checked"':''; ?> />Yes |
            	<input type="radio" name="wpcasad_useradd" id="useradd_no" value="no" 
            		<?php echo ($optionarray_def['useradd'] != 'yes')?'checked="checked"':''; ?> />No
			</td>
		</tr>
        <?php endif; ?>
	    <?php if (!isset($wpcasad_options['userrole'])) : ?>
		<tr valign="center"> 
			<th width="300px" scope="row"><?php _e( 'Default Role', 'wpcasad' ) ?></th> 
			<td><select name="wpcasad_userrole" id="cas_version_inp">
				<option value="subscriber" <?php echo ($optionarray_def['userrole'] == 'subscriber')?'selected':''; ?>>Subscriber</option>
				<option value="contributor" <?php echo ($optionarray_def['userrole'] == 'contributor')?'selected':''; ?>>Contributor</option>
				<option value="author" <?php echo ($optionarray_def['userrole'] == 'author')?'selected':''; ?>>Author</option>
				<option value="editor" <?php echo ($optionarray_def['userrole'] == 'editor')?'selected':''; ?>>Editor</option>
				<option value="administrator" <?php echo ($optionarray_def['userrole'] == 'administrator')?'selected':''; ?>>Administrator</option>
                </select>
            </td>
		</tr>
        <?php endif; ?>
	    <?php if (!isset($wpcasad_options['useldap'])) : ?>
			<?php if (function_exists('ldap_connect')) : ?>
			<tr valign="center"> 
				<th width="300px" scope="row">Use LDAP to get user info</th> 
				<td><input type="radio" name="wpcasad_useldap" id="useldap_yes" value="yes" 
						<?php echo ($optionarray_def['useldap'] == 'yes')?'checked="checked"':''; ?> />Yes |
					<input type="radio" name="wpcasad_useldap" id="useldap_no" value="no" 
						<?php echo ($optionarray_def['useldap'] != 'yes')?'checked="checked"':''; ?> />No
				</td>
			</tr>
			<?php
			else :
			?>
				<input type="hidden" name="wpcasad_useldap" id="useldap_hidden" value="no" />
			<?php
			endif;
			?>
        <?php endif; ?>
	    <?php if (!isset($wpcasad_options['email_suffix'])) : ?>
		<tr valign="center"> 
			<th width="300px" scope="row">E-mail Suffix</th> 
			<td><input type="text" name="wpcasad_email_suffix" id="server_port_inp" value="<?php echo $optionarray_def['email_suffix']; ?>" size="35" />
			</td>
		</tr>
        <?php endif; ?>
	</table>
    </blockquote></blockquote>
    <?php endif; ?>
    
    <?php if (function_exists('ldap_connect')) : ?>
    <?php if (!isset($wpcasad_options['ldapbasedn']) ||
			!isset($wpcasad_options['ldapport']) ||
			!isset($wpcasad_options['ldaphost']) ) : ?>
	<h4><?php _e( 'LDAP parameters', 'wpcasadldap' ) ?></h4>
    <blockquote><blockquote>
	<table width="500px" cellspacing="2" cellpadding="5" class="editform">
	    <?php if (!isset($wpcasad_options['ldaphost'])) : ?>
		<tr valign="center"> 
			<th width="300px" scope="row">LDAP Host</th> 
			<td><input type="text" name="wpcasad_ldaphost" id="ldap_host_inp" value="<?php echo $optionarray_def['ldaphost']; ?>" size="35" />
			</td>
		</tr>
        <?php endif; ?>
	    <?php if (!isset($wpcasad_options['ldapport'])) : ?>
		<tr valign="center"> 
			<th width="300px" scope="row">LDAP Port</th> 
			<td><input type="text" name="wpcasad_ldapport" id="ldap_port_inp" value="<?php echo $optionarray_def['ldapport']; ?>" size="35" />
			</td>
		</tr>
        <?php endif; ?>
	    <?php if (!isset($wpcasad_options['ldapbasedn'])) : ?>
		<tr valign="center"> 
			<th width="300px" scope="row">LDAP Base DN</th> 
			<td><input type="text" name="wpcasad_ldapbasedn" id="ldap_basedn_inp" value="<?php echo $optionarray_def['ldapbasedn']; ?>" size="35" />
			</td>
		</tr>
        <?php endif; ?>
	    <?php if (!isset($wpcasad_options['ldapbinddn'])) : ?>
		<tr valign="center"> 
			<th width="300px" scope="row">LDAP Bind Name</th> 
			<td><input type="text" name="wpcasad_ldapbinddn" id="ldap_binddn_inp" value="<?php echo $optionarray_def['ldapbinddn']; ?>" size="35" />
			</td>
		</tr>
        <?php endif; ?>
	    <?php if (!isset($wpcasad_options['ldapbindpass'])) : ?>
		<tr valign="center"> 
			<th width="300px" scope="row">LDAP Bind Password</th> 
			<td><input type="password" name="wpcasad_ldapbindpass" id="ldap_bindpass_inp" value="<?php echo $optionarray_def['ldapbindpass']; ?>" size="35" />
			</td>
		</tr>
        <?php endif; ?>
	</table>
    </blockquote></blockquote>
    <?php endif; ?>
    <?php endif; ?>
   
	<div class="submit">
		<input type="submit" name="wpcasad_submit" value="<?php _e('Update Options') ?> &raquo;" />
	</div>
	</form>
<?php
}
