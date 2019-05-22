=== Subscribe2 ===
Contributors: collizo4sky
Donate link: https://mailoptin.io
Tags: posts, subscription, email, subscribe, notify, notification, newsletter, post notification, email marketing
Requires at least: 4.0
Tested up to: 5.1
License: GPLv3

Sends a list of subscribers an email notification when you publish new posts.

== Description ==

= Summary =

Subscribe2 provides a comprehensive subscription management and email notification system for WordPress blogs that sends email notifications to a list of subscribers when you publish new content to your blog.

A full description of features is below.

= Email =

Email notifications can be sent on a per-post basis or periodically in a Digest email. Additionally, certain categories can be excluded from inclusion in the notification and posts can be excluded on an individual basis by setting a custom field.

= Subscriptions =

Subscription requests allows users to publicly subscribe (**Public Subscribers**) by submitting their email address in an easy to use form or to register with your blog (**Registered Users**) which enables greater flexibility over the email content for per-post notifications for the subscriber. Admins are given control over the presentation of the email notifications, can bulk manage subscriptions for users and manually send email notices to subscribers.

The format of the email can also be customised for per-post notifications, Subscribe2 can generate emails for each of the following formats:

* plaintext excerpt.
* plaintext full post (Registered Users only).
* HTML excerpt (Registered Users only).
* HTML full post (Registered Users only).

If you want to send new post notification or daily, weekly or monthly email digest of published posts to your email list in MailChimp, AWeber, Constant Contact etc, upgrade to [MailOptin](https://mailoptin.io).

== Installation ==

= AUTOMATIC INSTALLATION =

1. Log in to your WordPress blog and visit Plugins -> Add New.
2. Search for Subscribe2, click "Install Now" and then Activate the Plugin
3. Visit the "Subscribe2 -> Settings" menu.
4. Configure the options to taste, including the email template and any categories which should be excluded from notification
5. Visit the "Subscribe2 -> Subscribers" menu.
6. Manually subscribe people as you see fit.
7. Create a [WordPress Page](http://codex.wordpress.org/Pages) to display the subscription form.  When creating the page, you may click the "S2" button on the QuickBar to automatically insert the Subscribe2 token.  Or, if you prefer, you may manually insert the Subscribe2 shortcode or token: [subscribe2] or the HTML invisible `<!--subscribe2-->` ***Ensure the token is on a line by itself and that it has a blank line above and below.***
This token will automatically be replaced by dynamic subscription information and will display all forms and messages as necessary.
8. In the WordPress "Settings" area for Subscribe2 select the page name in the "Appearance" section that of the WordPress page created in step 7.

= MANUAL INSTALLATION =

1. Copy the entire /subscribe2/ directory into your /wp-content/plugins/ directory.
2. Activate the plugin.
3. Visit the "Subscribe2 -> Settings" menu.
4. Configure the options to taste, including the email template and any categories which should be excluded from notification
5. Visit the "Subscribe2 -> Subscribers" menu.
6. Manually subscribe people as you see fit.
7. Create a [WordPress Page](http://codex.wordpress.org/Pages) to display the subscription form.  When creating the page, you may click the "S2" button on the QuickBar to automatically insert the Subscribe2 token.  Or, if you prefer, you may manually insert the Subscribe2 shortcode or token: [subscribe2] or the HTML invisible `<!--subscribe2-->` ***Ensure the token is on a line by itself and that it has a blank line above and below.***
This token will automatically be replaced by dynamic subscription information and will display all forms and messages as necessary.
8. In the WordPress "Settings" area for Subscribe2 select the page name in the "Appearance" section that of the WordPress page created in step 7.

== Frequently Asked Questions ==

[Visit FAQ site](https://subscribe2.wordpress.com/support/faqs/)

== Changelog ==

= 10.28 =

* Fix warning message for when set_time_limit() is disabled in php.ini
* Remove unused variable to fix error message
* Fixes for strict in_array() checks

= 10.27 =

* Tightened checks for Coding Standards
* Fixed bug in category form display caused by tighter coding standards
* Save Editor content before sending preview email for better user experience
* Update Excerpt creation code for languages that don't use spaces - props Masatoshi Hakoshima
* Improved layout and efficiency of JavaScript files
* Enable Preview emails for Classic Editor users
* Update to use latest jQuery UI css library
* Tidied up unused globals

= 10.26.1 =

* Added Gutenberg support.

= 10.26 =

* Fixed a bug in barred domain checking
* Updated to latest PHP Code sniff standards and cleansed files
* Other minor bug fixes and enhancements

= 10.25 =

* Dropped use of the 'Precedence' email header as use is controversial and not widely supported
* Fix for date appearing in wrong language when authors use different language in WordPress admin to the main site language
* Fixed warning message on Your Subscriptions page
* Further Coding Standard improvements
* Add GDPR warning to Settings page for certain settings
* Fixed error message in older version of WordPress where get_user_locale() function is not available
* Extended 's2_form' filter to include shortcode attributes to allow more accurate filtering
* Extended Widget to pass attribute to indicate Widget use over standard form use
* Fix some notices generated by undefined variables
* Ensure Editor buttons loads when Classic Editor plugin in use with WordPress 5.0

= 10.24 =

* Fixes for the Subscribers page - sorting and Multisite improvements
* Code layout improvements
* Other minor bug fixes and enhancements

= 10.23 =

* Improve REGEX for the old Subscribe2 token - props @pik256
* Applied Grunt to packing for distribution so files are minified differently
* Load external javascripts asynchronously
* Improve Bulk Management user experience
* Move Display options for Subscriber page to screen option
* Fixed error messages when using 's2_custom_keyword' hook in digest mode - thanks to Bryan Fogarty
* Ported several features from Subscribe2 HTML including Remote IP checking and email address validation
* Other minor big fixes

= 10.22.2 =

* Fix JavaScript issue when toggling subscribers
* Fix to correct display of Registered Subscribers on multisite installs
* Fix for Content Type in plaintext emails

= 10.22.1 =

* Minor release with some bug fixes that really needed fixing - thanks to @doyousoft and @tastymouse

See complete changelog installed in the same folder as this readme.