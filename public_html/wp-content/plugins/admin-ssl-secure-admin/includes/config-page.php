<?php
/*
 * config-page.php
 *
 * Displays Admin SSL configuration settings page
 *
 * Author: BCG
 *
 */
?>

<?php
	if(function_exists("current_user_can") && !current_user_can("level_10"))
		exit("Nice try");
	if(basename($_SERVER["SCRIPT_FILENAME"]) === basename(__FILE__))
		exit("You cannot access this page directly");

	wp_nonce_field();
?>

<?php if(!empty($_POST)){ ?>
	<div id="message" class="<?php echo($error ? "error" : "updated") ?> fade"><p><strong><?php echo(_e($message)) ?></strong></p></div>
<?php } ?>

<div class="wrap">

	<h2><?php _e('Admin SSL Configuration'); ?></h2>

	<form action="" method="post" id="admin-ssl-config">

		<h3>Enable SSL</h3>

		<p>You <strong>must</strong> have a Private SSL certificate correctly installed or enabling this option
			will render your site inaccessible.</p>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="use_ssl">
						<input id="use_ssl" name="use_ssl" type="checkbox"<?php if($use_ssl) echo(' checked="checked"') ?> />
						Secure my site with SSL
					</label>
				</th>
			</tr>
		</table>

		<br/>

		<h3>Additional URLs</h3>

		<p>Admin SSL forces wp-login.php and wp-admin/profile.php to be secured (these are the pages on which
		you can enter a password).  When HTTPS is being used, the content and includes directories are also secured.
		Here you can add other URLs to be secured by Admin SSL.</p>

		<table class="form-table">
			<tr>
				<th scope="row">URL List</th>
				<td>
					One URL per line. Your blog URL is <?php echo(get_option("siteurl")) ?>/, so to secure
					<?php echo(get_option("siteurl")) ?>/some_page.php, add 'some_page.php'
					to the box below.  To secure all your admin URLs, add 'wp-admin/', etc.
					<textarea id="additional_urls" name="additional_urls" cols="60" rows="10"
						style="width: 95%;" class="code"><?php if($additional_urls) echo($additional_urls) ?></textarea>
					<b>Warning</b>: depending on how other plugins are written, this feature may not work properly
					on a Shared SSL setup.<br/>
					Attempting to secure a blog post or page <b>will</b> cause a redirection error.  Single posts and
					pages cannot be secured - to secure your entire blog, disable Admin SSL and change your blog
					URL on the 'Settings' page.
				</td>
			</tr>
			<tr>
				<th scope="row" colspan="2">
					<label for="secure_users_only">
						<input id="secure_users_only" name="secure_users_only" type="checkbox"
							<?php if($secure_users_only) echo(' checked="checked"') ?> />
						Secure additional URLs <b>only</b> if user is signed in
					</label>
				</th>
			</tr>
		</table>

		<br/>

		<h3>Ignore Urls</h3>

		<p>If you want the Admin SSL buffer to ignore a particular page, enter its URL here. The page <b>output</b> will be
		completely unaffected by Admin SSL - i.e. all links will be left as 'http'. However, the page itself will still
		be forced to SSL if it is entered in the 'Additional URLs' box above.</p>

		<p>This feature is particularly for xmlrpc.php, but may be used if other pages or plugins require it.</p>

		<table class="form-table">
			<tr>
				<th scope="row">URL List</th>
				<td>
					One URL per line. Your blog URL is <?php echo(get_option("siteurl")) ?>/, so to ignore
					<?php echo(get_option("siteurl")) ?>/some_page.php, add 'some_page.php'
					to the box below.
					<textarea id="ignore_urls" name="ignore_urls" cols="60" rows="10"
						style="width: 95%;" class="code"><?php if($ignore_urls) echo($ignore_urls) ?></textarea>
					<b>Warning</b>: depending on how other plugins are written, this feature may not work properly
					on a Shared SSL setup.
				</td>
			</tr>
		</table>

		<br/>

		<h3>Other Settings</h3>

		<table class="form-table">
			<tr>
				<th scope="row">Config Page</th>
				<td>
					Show Admin SSL options under the following menu:<br/>
					<label for="parent_plugins">
						<input id="parent_plugins" name="config_parent" type="radio"
							value="plugins.php"<?php if($config_parent == "plugins.php") echo(' checked="checked"') ?>/>
						Plugins menu
					</label>
					<br/>
					<label for="parent_settings">
						<input id="parent_settings" name="config_parent" type="radio"
							value="options-general.php"<?php if($config_parent == "options-general.php") echo(' checked="checked"') ?>/>
						Settings menu
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row">HTTPS Detection</th>
				<td>
					<b>Warning</b>: Do <b>NOT</b> play with this feature, or you will cause endless redirection.
					<br/>
					If you need to use it, ensure that you enter the details correctly before saving the changes.

					<br/><br/>

					<input id="https_key" name="https_key" type="text" class="code"
						value="<?php echo($https_key) ?>" />
					<br/>
					<label for="https_key">The name of the HTTPS $_SERVER variable</label>

					<br/><br/>

					<input id="https_value" name="https_value" type="text" class="code"
						value="<?php echo($https_value) ?>" />
					<br/>
					<label for="https_value">The value of the HTTPS $_SERVER variable when HTTPS is ON</label>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" name="submit" value="<?php _e('Save Changes'); ?>" />
		</p>

		<?php if(function_exists("wp_nonce_field")) wp_nonce_field(); ?>

	</form>

</div>