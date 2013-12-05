=== Plugin Name ===
Contributors: DvanKooten
Donate link: http://dannyvankooten.com/donate/
Tags: newsletter,comments,registration,sign-up,newsletter signup,mailinglist,mailchimp,aweber,phplist,sign-up form,widget
Requires at least: 2.0
Tested up to: 3.0.1
Stable tag: 2.0.1

Adds a checkbox to your comment form to subscribe people to your mailing list. Also offers a sign-up form widget.

== Description ==

= Qoate Newsletter Sign-up =

**SUPPORT FOR THIS PLUGIN IS DISCONTINUED. DEVELOPMENT CONTINUES UNDER A NEW NAME: [Newsletter Sign-Up](http://wordpress.org/extend/plugins/newsletter-sign-up/)**

Development of this plugin continues as [Newsletter Sign-Up](http://wordpress.org/extend/plugins/newsletter-sign-up/), so please head over there to download the latest version.

This plugin lets you add a checkbox to your comment form to turn your commenters into newsletter subscribers. 

**Download [Newsletter Sign-Up](http://wordpress.org/extend/plugins/newsletter-sign-up/) now!**

== Installation ==

Do **not** download this plugin if you're not already using it succesfully. Download the new and better version instead: [Newsletter Sign-Up](http://wordpress.org/extend/plugins/newsletter-sign-up/)

Follow the instruction on the [Qoate Newsletter Sign Up](http://dannyvankooten.com/wordpress-plugins/newsletter-sign-up/) page.

== Frequently Asked Questions ==

= I am getting errors =
Yeah, that's because you should use the NEW version of this plugin which is much better. This plugin has been re-released under a different name: [Newsletter Sign-Up](http://wordpress.org/extend/plugins/newsletter-sign-up/)

= Why does the checkbox not show up? =

You're theme probably does not support the comment hook this plugin uses. You can add it manually by adding . `<?php do_action('comment_form',$post->ID); ?>`

= Where can I get the form action of my sign-up form? =

Look at the source code of your sign-up form and check for `<form action="http://www.yourmailinglist.com/signup?a=asd128"`....
The action url is what you need to configure in the admin panel.

= Where can I get the email identifier of my sign-up form? =

If you're using MailChimp or YMLP all you have to do is select your newsletter service from the dropdown. When using some other service, select other and look
at the source code of your sign-up form. Look for the input field that holds the e-mailadress. The name attribute is what you need here. Want your newsletter service to
appear in the drop down? Let me know and i'll add!

= Your checkbox stopped working with Aweber = 

I did change something in version 1.7 that caused that, sorry. All you have to do is hit the submit button with the right settings once again.

For more questions and answers go and have a look at [Qoate.com](http://dannyvankooten.com/wordpress-plugins/newsletter-signup/)

== Changelog ==
= 2.0.2 =
Added a notice to download the NEW IMPROVED [Newsletter Sign-Up](http://wordpress.org/extend/plugins/newsletter-sign-up/) plugin. You should too!

= 2.0.1 =
Fixed a thing where the cookie expiry date gave an error. 

= 2.0 =
Added a sign-up form widget, which you can customize a little for now. Will expand in the future!

= 1.8.2 =
Added an option to hide the checkbox for users that signed up trough the Qoate checkbox.

= 1.8.1 = 
PHPlist should now work.

= 1.8 = 
Advanced users with PHP knowledge can now send extra variables.

= 1.7 = 
Added support for PHPList. For the people using Aweber, you might have to hit the submit button with the right settings again. Sorry.

= 1.6 =
You can now add a checkbox to your registration form too.

= 1.5.2 =
Fixed another little Aweber bug.

= 1.5.1 =
Fixed a little bug with Aweber.

= 1.5 = 
* Added Aweber support.
* Pre select checkbox option
* Subscribe with name option.
* Conditional plugin files loading for improved blog performance.

= 1.4 =
Major performance increase

= 1.3 =
You can now select MailChimp, YMLP to help you with providing the Email identifier. 

= 1.2 = 
The checkbox now shows up automatically for most themes!

= 1.1 =
Updated so you no longer need to add the action hook manually.

= 1.0 =
Stable release.