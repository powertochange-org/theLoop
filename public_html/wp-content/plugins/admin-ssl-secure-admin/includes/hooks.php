<?php
/**
 * includes/hooks.php
 *
 * Adds Admin SSL actions/filters to WordPress
 *
 * Author: BCG
 *
 */

//
//	default actions
//

	add_action("admin_menu","as_admin_menu");
	add_action("admin_notices","as_warning");

//
//	filters and actions - only add if SSL is enabled
//

	if($use_ssl)
	{
		add_action("init", "as_init");

		add_filter("secure_signon_cookie", "as_secure_cookie", 10, 2);
		add_filter("comment_moderation_text", "as_ob_handler");
		add_filter("comment_notification_text", "as_ob_handler");
		add_filter("plugin_action_links", "as_action_links", 10, 2);
		add_filter("wp_mail","as_mail");
		add_filter("wp_redirect","as_redirect_check");
	}
?>