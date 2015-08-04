<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php wptouch_bloginfo('html_type'); ?>; charset=<?php wptouch_bloginfo('charset'); ?>" />
	<title><?php wptouch_title(); ?></title>
	<?php wptouch_head(); ?>
	<link type="text/css" rel="stylesheet" media="screen" href="<?php classic_the_static_css_url( 'ipad' ); ?>?version=<?php classic_the_static_css_version( 'ipad' ); ?>"></link>
</head>		
<?php flush(); ?>
	<body class="<?php wptouch_body_classes(); ?>">
	<noscript>
		<div id="noscript">
			<h2><?php _e( "Notice", "wptouch-pro" ); ?></h2>
			<p><em><?php _e( "JavaScript is currently off.", "wptouch-pro" ); ?></em></p>
			<p><?php _e( "Turn it on in Settings -> Safari to view this mobile website correctly.", "wptouch" ); ?></p>
		</div>
	</noscript>

		<?php include_once('menubar.php'); ?>
		<?php include_once( 'popovers.php' ); ?>
		
		<!-- The Landscape Sidebar ( menu is dynamically attatched here by js ) -->
		<?php if ( wptouch_has_menu() ) { ?>
			<div id="main-menu">
				<?php if ( classic_has_ipad_logo() ) { ?>
					<div id="logo-area">
						<a href="<?php  bloginfo( 'url' ); ?>"><?php classic_ipad_logo_image(); ?></a>
					</div>
				<?php } ?>
			</div>
		<?php } ?>

		<?php do_action( 'wptouch_body_top' ); ?>

		<!-- Rock N' Scroll! -->
			<div id="iscroll-wrapper" class="iscroller">
			<div id="staff-apps-menu" class="closed">
				<div>
                    <a href='https://absences.powertochange.org'>
                        <img src='/wp-content/images/Absence-Tracker-Icon.png' alt='Absence Tracker' />
                    </a>
                </div>
				<div>
                    <a href='/reports/'>
                        <img src='/wp-content/images/Reports-Icon.png' alt='Reports' />
                    </a>
                </div>
				<div>
                    <a href='/wp-admin/admin.php?page=s2'>
                        <img src='/wp-content/images/My-Settings-Icon.png' alt='My Settings' />
                    </a>
                </div>
				<div>
                    <a href='/staff-directory/'>
                        <img src='/wp-content/images/Staff-Directory-Icon.png' alt='Staff Directory' />
                    </a>
                </div>
				<div>
                    <a href='mailto:helpdesk@powertochange.org'>
                        <img src='/wp-content/images/HelpDesk-Icon.png' alt='Help Desk' />
                    </a>
                </div>
				<div>
                    <a href='https://wiki.powertochange.org/help'>
                        <img src='/wp-content/images/Self-Help-Wiki-Icon.png' alt='Self-Help Wiki' />
                    </a>
                </div>
			</div>
			<div id="iscroll-content">
				<div id="content">
						<?php do_action( 'wptouch_advertising_top' ); ?>
						<?php if ( wptouch_has_welcome_message() && !isset( $_COOKIE['wptouch_welcome'] ) && !isset( $_COOKIE['web-app-mode'] ) ) { ?>
							<div id="welcome-message">
								<?php wptouch_the_welcome_message(); ?>
								<br /><br />
								<a href="<?php wptouch_the_welcome_message_dismiss_url(); ?>" id="close-msg" class="wptouch-button"><?php _e( "Close Message", "wptouch-pro" ); ?></a>	
							</div>
						<?php } ?>