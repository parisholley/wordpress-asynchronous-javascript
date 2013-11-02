=== Asynchronous Javascript ===
Contributors: parisholley
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paris%40holleywoodproductions%2ecom&lc=US&item_name=Paris%20Holley&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Tags: async,js,headjs,asynchronous,javascript,performance
Requires at least: 3.5
Tested up to: 3.5
Stable tag: trunk

Improve page load performance by asynchronously loading javascript using head.js

== Description ==

This plugin is meant to be a drop-in to your wordpress installation with no additional configuration. The goals/features of this plugin are:

* Load javascript files in an asynchronous manner to improve time to DomReady/OnLoad
* Use existing wordpress APIs for backwards compatability and prevent coupling to this plugin
* Leverage dependency model wordpress provides for assets to improve loading performance

Please submit bugs or contributions to the github location and not here on wordpress' system:

https://github.com/parisholley/wordpress-asynchronous-javascript/

SEO: async js, asynchronous js, async javascript


== Installation ==

1. Upload `asynchronous-javascript` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Is there any potential problems with other plugins? =

As long as those plugins are using the built-in wordpress script queuing and not attempting to output scripts manually (such as invoking wp_print_scripts()), you should have no problems. If you find plugins that don't work properly (or without modifications), let me know and I will maintain an incompatibility list.

== Incompatibility ==

This plugin will not work out the box with the following plugins (unless they are modified to support asynchronous loading).

* Jetpack by WordPress.com (social plugin)
* WP Most Popular

== Changelog ==

= 1.3.5 =
* Ability to mark headjs as always in header (thanks DeanStr)

= 1.3.4 =
* Fixed notices
* Added filter to alter src location

= 1.3.3 =
* Fixed bug introduced in last version

= 1.3.2 =
* Ability to specify your own head.js file (thanks to DeanStr)

= 1.3.1 =
* Removed PHP warning

= 1.3.0 =
* Ability to exclude files by filename or queue id from being loaded asynchronously

= 1.2.1 =
* Fixed static reference for PHP < 5.2.3

= 1.2.0 =
* Wordpress already orders dependencies, updated as head.js doesn't support multiple dependency resolution

= 1.1.2 =
* Starting incompatibility list

= 1.1.1 =
* Updated readme and versioning

= 1.1 =
* Removed `wp_enqueue_async_script()`, should be able to use normal wordpress method `wp_enqueue_script()`
* Updated normal enqueue processing to use dependencies, will need to improve upon this in future.

= 1.0 =
* Initial release, seems to work. :)

== Upgrade Notice ==

= 1.1 =
* Dependencies are now honored and it is recommended that you use this version instead of 1.0
* `wp_enqueue_async_script()` is no longer available for use in the theme
