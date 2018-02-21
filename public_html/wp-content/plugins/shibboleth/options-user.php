<?php
// functions for managing Shibboleth user options through the WordPress administration panel

add_action('profile_personal_options', 'shibboleth_profile_personal_options');
add_action('personal_options_update', 'shibboleth_personal_options_update');
add_action('show_user_profile', 'shibboleth_show_user_profile');
add_action('admin_footer-user-edit.php', 'shibboleth_admin_footer_edit_user');

/**
 * For WordPress accounts that were created by Shibboleth, limit what profile
 * attributes they can modify.
 *
 * @since 1.3
 */
function shibboleth_profile_personal_options() {
	$user = wp_get_current_user();

	if (get_user_meta( $user->ID, 'shibboleth_account') ) {

		add_filter( 'show_password_fields', create_function( '$v', 'return false;' ) );

		add_action( 'admin_footer-profile.php', 'shibboleth_admin_footer_profile' );
	}
}

/**
 * For WordPress accounts that were created by Shibboleth, disable certain fields
 * that they are allowed to modify.
 *
 * @since 1.3
 */
function shibboleth_admin_footer_profile() {
	$managed_fields = shibboleth_get_managed_user_fields();

	if ( ! empty( $managed_fields ) ) {
		$selectors = join( ',', array_map( create_function( '$a', 'return "#$a";' ), $managed_fields ) );

		echo '
		<script type="text/javascript">
			jQuery(function() {
				jQuery("' . $selectors . '").attr("disabled", true);
				jQuery("#first_name").parents(".form-table").before("<div class=\"updated fade\"><p>'
					. __( 'Some profile fields cannot be changed from WordPress.', 'shibboleth' ) . '</p></div>");
				jQuery("form#your-profile").submit(function() {
					jQuery("' . $selectors . '").attr("disabled", false);
				});
			});
		</script>';
	}
}


/**
 * For WordPress accounts that were created by Shibboleth, warn the admin of
 * Shibboleth managed attributes.
 *
 * @since 1.3
 */
function shibboleth_admin_footer_edit_user() {
	global $user_id;

	if ( get_user_meta( $user_id, 'shibboleth_account' ) ) {
		$shibboleth_fields = array();

		$shibboleth_fields = array_merge( $shibboleth_fields, shibboleth_get_managed_user_fields() );

		if ( defined( 'SHIBBOLETH_UPDATE_ROLES' ) && SHIBBOLETH_UPDATE_ROLES ) {
			$update = SHIBBOLETH_UPDATE_ROLES;
		} else {
			$update = get_site_option( 'shibboleth_update_roles' );
		}

		if ( $update ) {
			$shibboleth_fields = array_merge( $shibboleth_fields, array('role') );
		}

		if ( ! empty( $shibboleth_fields ) ) {
			$selectors = array();

			foreach( $shibboleth_fields as $field ) {
				$selectors[] = 'label[for=\'' . $field . '\']';
			}

			echo '
			<script type="text/javascript">
				jQuery(function() {
					jQuery("' . implode( ',', $selectors ) . '").before("<span style=\"color: #F00; font-weight: bold;\">*</span> ");
					jQuery("#first_name").parents(".form-table")
						.before("<div class=\"updated fade\"><p><span style=\"color: #F00; font-weight: bold;\">*</span> '
						. __( 'Starred fields are managed by Shibboleth and should not be changed from WordPress.', 'shibboleth' ) . '</p></div>");
				});
			</script>';
		}
	}
}


/**
 * Add change password link to the user profile for Shibboleth users.
 *
 * @since 1.3
 */
function shibboleth_show_user_profile() {
	$user = wp_get_current_user();
	if ( defined( 'SHIBBOLETH_PASSWORD_CHANGE_URL' ) && SHIBBOLETH_PASSWORD_CHANGE_URL ) {
		$password_change_url = SHIBBOLETH_PASSWORD_CHANGE_URL;
	} else {
		$password_change_url = get_site_option( 'shibboleth_password_change_url' );
	}
	if ( get_user_meta( $user->ID, 'shibboleth_account' ) && ! empty( $password_change_url ) ) {
?>
	<table class="form-table">
		<tr>
			<th><?php _e( 'Change Password', 'shibboleth' ) ?></th>
			<td><a href="<?php echo esc_url( $password_change_url ); ?>" rel="nofollow" target="_blank"><?php
				_e( 'Change your password', 'shibboleth' ); ?></a></td>
		</tr>
	</table>
<?php
	}
}


/**
 * Ensure profile data isn't updated by the user.  This only applies to accounts that were
 * provisioned through Shibboleth, and only for those user fields marked as 'managed'.
 *
 * @since 1.3
 */
function shibboleth_personal_options_update() {
	$user = wp_get_current_user();

	if ( get_user_meta( $user->ID, 'shibboleth_account' ) ) {
		$managed = shibboleth_get_managed_user_fields();

		if ( in_array( 'first_name', $managed ) ) {
			add_filter( 'pre_user_first_name', create_function( '$n', 'return $GLOBALS["current_user"]->first_name;' ) );
		}

		if ( in_array( 'last_name', $managed ) ) {
			add_filter( 'pre_user_last_name', create_function( '$n', 'return $GLOBALS["current_user"]->last_name;' ) );
		}

		if ( in_array( 'nickname', $managed ) ) {
			add_filter( 'pre_user_nickname', create_function( '$n', 'return $GLOBALS["current_user"]->nickname;' ) );
		}

		if ( in_array( 'display_name', $managed ) ) {
			add_filter( 'pre_user_display_name', create_function( '$n', 'return $GLOBALS["current_user"]->display_name;' ) );
		}

		if ( in_array( 'email', $managed ) ) {
			add_filter( 'pre_user_email', create_function( '$e', 'return $GLOBALS["current_user"]->user_email;' ) );
		}
	}
}

/**
 * Adds a button to user profile pages if administrator has allowed
 * users to manually combine accounts.
 *
 * @param object $user WP_User object
 * @since 1.9
 */
function shibboleth_link_accounts_button( $user ) {
	$allowed = get_site_option( 'shibboleth_manually_combine_accounts', 'disallow' );
	if ( $allowed === 'allow' || $allowed === 'bypass' ) {
		$linked = get_user_meta( $user->ID, 'shibboleth_account', true ); ?>
		<table class="form-table">
			<tr>
				<th><label for="link_shibboleth"><?php _e( 'Link Shibboleth Account', 'shibboleth' ); ?></label></th>
				<td>
					<?php if ( $linked ) { ?>
						<button type="button" disabled class="button"><?php _e( 'Link Shibboleth Account', 'shibboleth' ); ?></button>
						<p class="description"><?php _e('Your account is already linked to Shibboleth.', 'shibboleth' ); ?></p>
					<?php } else { ?>
						<a href="?shibboleth=link"><button type="button" class="button"><?php _e( 'Link Shibboleth Account', 'shibboleth' ); ?></button></a>
						<p class="description"><?php _e('Your account has not been linked to Shibboleth. To link your account, click the button above.', 'shibboleth' ); ?></p>
					<?php } ?>
				</td>
			</tr>
		</table>
	<?php }
}
add_action( 'show_user_profile', 'shibboleth_link_accounts_button' );
add_action( 'edit_user_profile', 'shibboleth_link_accounts_button' );

/**
 * Processes the linking of a user's account if administrator has allowed
 * users to manually combine accounts and redirects them to an admin notice.
 *
 * @since 1.9
 */
function shibboleth_link_accounts() {
	$screen = get_current_screen();
	if ( is_admin() && $screen->id == 'profile' ) {
		$user_id = get_current_user_id();
		if ( isset( $_GET['shibboleth'] ) && $_GET['shibboleth'] === 'link' && current_user_can( 'edit_user', $user_id ) ) {
			$allowed = get_site_option( 'shibboleth_manually_combine_accounts', 'disallow' );
			if ( ! get_user_meta( $user_id, 'shibboleth_account' ) ) {
				if ( $allowed === 'allow' || $allowed === 'bypass' ) {
					if ( shibboleth_session_active() ) {
						$shib_headers = get_site_option( 'shibboleth_headers' );
						$username = shibboleth_getenv( $shib_headers['username']['name'] );
						$email = shibboleth_getenv( $shib_headers['email']['name'] );
						$user = get_user_by( 'id', $user_id );
						if ( $user->user_login == $username && $user->user_email == $email) {
							update_user_meta( $user->ID, 'shibboleth_account', true );
							wp_safe_redirect( get_edit_user_link() . '?shibboleth=linked' );
							exit;
						} elseif ( $user->user_login == $username ) {
								$prevent_conflict = get_user_by( 'email', $email );
								if ( ! $user->ID ) {
									update_user_meta( $user->ID, 'shibboleth_account', true );
									wp_safe_redirect( get_edit_user_link() . '?shibboleth=linked' );
									exit;
								} else {
									wp_safe_redirect( get_edit_user_link() . '?shibboleth=failed' );
									exit;
								}
						} elseif ( $user->user_email == $email && $allowed === 'bypass' ) {
							update_user_meta( $user->ID, 'shibboleth_account', true );
							wp_safe_redirect( get_edit_user_link() . '?shibboleth=linked' );
							exit;
						} else {
							wp_safe_redirect( get_edit_user_link() . '?shibboleth=failed' );
							exit;
						}
					} else {
						$initiator_url = shibboleth_session_initiator_url( get_edit_user_link() . '?shibboleth=link' );
						wp_redirect( $initiator_url );
						exit;
					}
				} else {
					wp_safe_redirect( get_edit_user_link() . '?shibboleth=failed' );
					exit;
				}
			} else {
				wp_safe_redirect( get_edit_user_link() . '?shibboleth=duplicate' );
				exit;
			}
		}
	}
}
add_action( 'current_screen', 'shibboleth_link_accounts' );

/**
 * Displays admin notices based off query string.
 *
 * @since 1.9
 */
function shibboleth_link_accounts_notice() {
	if ( isset( $_GET['shibboleth'] ) ) {
		if ( $_GET['shibboleth'] === 'failed' ) {
			$class = 'notice notice-error';
			$message = __( 'Your account was unable to be linked with Shibboleth.', 'shibboleth' );
		} elseif ( $_GET['shibboleth'] === 'linked' ) {
			$class = 'notice notice-success is-dismissible';
			$message = __( 'Your account has been linked with Shibboleth.', 'shibboleth' );
		} elseif ( $_GET['shibboleth'] === 'duplicate' ) {
			$class = 'notice notice-info is-dismissible';
			$message = __( 'Your account is already linked with Shibboleth.', 'shibboleth' );
		} else {
			$class = '';
			$message = '';
		}
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
}
add_action( 'admin_notices', 'shibboleth_link_accounts_notice' );
