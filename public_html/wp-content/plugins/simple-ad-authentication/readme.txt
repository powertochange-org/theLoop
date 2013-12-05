=== Simple AD Authentication ===
Contributors: drahkar
Donate link: http://sourceforge.net/project/project_donations.php?group_id=267502
Tags: authentication, active directory, login
Requires at least: 2.7
Tested up to: 3.0.4
Stable tag: 0.9

Authenticates users through Active Directory.

== Description ==

This plugin allows WordPress to authenticate users against an Active Directory. 
Developed to be compatible with current Active Directory releases as well as allow 
for Enterprise level Forest Deployments by allowing you to enter custom BaseDNs. 

If you have bugs, suggestions, desires or requirements that you would like to see added
to this, please come to the plugin page and submit them there so I can keep track 
of them as they are found and resolved.

This plugin is redeveloped off of [Simple LDAP
Authentication](http://wordpress.org/extend/plugins/simple-ldap-authentication/).

= How to use =

You can use this plugin in a few different ways.

1. You can create WordPress accounts which match the names of your Active Directory accounts, and create these users from within the WordPress Users panel. Only the users you create in WordPress will be able to log in.

2. You can tick a checkbox so that anyone who can authenticate via Active Directory can log on. A WordPress account will be automatically created for the user if one does not already exist, with the default user role.

3. You can list the names of Active Directory groups who you want to allow to log on to WordPress. For each group in Active Directory, you can specify a corresponding WordPress user role.

You can also combine the above however you like.

== ChangeLog ==

= 0.9 =
   * Added ability to have a | delimited list of Base DNs to account for environments where the OU structure doesn't contain all users in one base DN

= 0.8 =
   * Updated code to no longer use call time pass by reference. Newer versions of PHP no longer allow the user of call time pass by reference and through an E_STRICT when it is attempted.

= 0.7 =
   * Made modifications to the examples and tool-tips for settings page to clarify configuration process.

= 0.6 =
   * Removed SSL options from code as it is not supported by Active directory.

= 0.5 =
   * Redeveloped existing code to support Active Directory instead of LDAP.
   * Removed Anonymous Binding as it is not supported by Active Directory.
   * Redesigned the authentication schema to properly support Active Directory.
   * Added assumption of based Windows Active Directory User Structure if no custom BaseDN is entered.

== Installation ==

IMPORTANT NOTE: This plugin requires the PHP LDAP module and a LDAP library such as OpenLDAP to be installed to function.

1. Login as an existing Administrator, such as admin.
2. Upload the folder named `simple-ad-authentication` to your plugins folder, usually `wp-content/plugins`.
3. Activate the plugin on the Plugins screen.

Note: This plugin has only been tested with WordPress 2.7.1 and above, and I do not think it will work on older versions of WordPress.

== Frequently Asked Questions ==

= This plugin supports Active Directory? =

Yes.
This plugin only supports Active Directory bind and can not be garuanteed to work with LDAP.

= Can I customize LDAP search filter? =

No.
All searches are based around Active Directory search functions and should be tailored that way.

= How do I use debug mode? =

This plugin has a built-in debug mode.
When `WP_DEBUG` is enabled in `wp-config.php`, it will turn on the notices that some authenticatin information are added on the log entry.
If you don't know how to define the constant, see [WordPress document](http://codex.wordpress.org/Editing_wp-config.php#Debug).
