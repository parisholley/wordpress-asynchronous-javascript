=== Asynchronous Javascript ===
Contributors: parisholley
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paris%40holleywoodproductions%2ecom&lc=US&item_name=Paris%20Holley&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Tags: async,headjs,asynchronous,performance,javascript
Requires at least: 3.5
Tested up to: 3.5
Stable tag: trunk

== Description ==

Improve page load performance by asynchronously loading javascript using head.js


== Installation ==

1. Upload `asynchronous-javascript` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How do I include a script that I do not control but has a dependency on another script? =

In your theme, use the static AsynchronousJS::wp_enqueue_async_script() method, which is API compatible with wp_enqueue_script($handle, $src, $deps). Using this method will cause the $src to load ONLY when scripts with the handles defined in $deps have been loaded.

= Is there any potential problems with other plugins? =

As long as those plugins are using the built-in wordpress script queuing and not attempting to output scripts manually (such as invoking wp_print_scripts()), you should have no problems.

== Changelog ==

= 1.0 =
* Initial release, seems to work. :)
