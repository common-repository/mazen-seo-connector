=== Mazen SEO Connector ===
Contributors: optimizme
Donate link:
Tags: optimizme, seo, mazen
Requires at least: 4.5
Tested up to: 4.9
Stable tag: 1.6.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin to dialog with Mazen, the first SEO software that saves your time.

== Description ==

With  [Mazen](https://mazen-app.com) SEO Connector, you can interact with your Wordpress CMS directly through Mazen, the first SEO Software that saves your time, by Optimiz.me.

Mazen gives you hints to improve your SEO, and changes done in Mazen are directly sent to your CMS: you can create/read/update/delete your Wordpress content using Mazen, sparing a lot of time!

You need to be registered in Mazen to have interaction with this plugin, otherwise it will be of no use.
Your website content data (text only) may be saved in Mazen servers in order to log and optimize your SEO, giving you personalized and calculated hints.

No personal or private information (login, password...) are stored on Mazen's servers.

== Upgrade Notice ==

== Installation ==

Requires PHP 5.4 or higher.

1. Upload the plugin files to the `/wp-content/plugins/mazen-seo-connector` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. In Mazen, activate your site to generate your unique security key which is shared between Mazen and your Wordpress website.

== Frequently Asked Questions ==

= What does it need to work? =

You need to be registered and activate your website in Mazen.

= What is the JSON Web Token error? =

Your CMS must be activated in Mazen.
If not, no dialog can be done (for security reasons).

= What kind of data is saved in Mazen servers? =

No private data is saved in Mazen. No login nor password are stored.
Mazen may save some of your posts/pages content (text only), in order to calculate and give you custom and personalized hints.

= What can I do with Mazen SEO Connector, from my backend?  =

You can't do anything directly from your backend.
The purpose of this plugin is to interact with your Wordpress in Mazen, a third-party service.

== Screenshots ==

== Changelog ==

= 1.6.7 =
* Improvements for Elementor support
* Filter to disable wp-spamshield spam control when dialog with Mazen

= 1.6.6 =
* Change "init" to "wp" in plugin hook loading
* Improvements for Fusion Builder support
* Improvements for YOAST Seo and All In One SEO Pack in meta title support

= 1.6.5 =
* Improvements for Visual Composer support

= 1.6.4 =
* Return post url with/without last "/" when Mazen requests this specific url

= 1.6.3 =
* Remove init order

= 1.6.2 =
* Change init order for better wp-spamshield and theme based builder compatibility
* Better compatibility with Avia Framework Builder

= 1.6.1 =
* Fix for HtmlDomParser with no content

= 1.6.0 =
* Compatibility for Fusion Builder, partial compatibility for Fresh Builder

= 1.5.5 =
* Compatible up to Wordpress 4.9

= 1.5.4 =
* Fix for wp-spamshield

= 1.5.1 =
* Fix for filters function not called statically

= 1.5.0 =
* Visual Composer extended support
* Fix filters when authenticate

= 1.4.1 =
* Fix for Mazen CMS authentication if wp_debug is set to true

= 1.4.0.1 =
* Remove \t \n in HTML nodes

= 1.4.0 =
* Can now search and get JSON by GET mazenseoconnectorsearch

= 1.3.4.1 =
* return HTTP code 403 for all JWT error in auth

= 1.3.4 =
* class auto-updater
* return HTTP code 403 if no JWT in database when plugin is requested

= 1.3.3 =
* Remove some unwanted post types when retrieving all posts

= 1.3.2.1 =
* little fix in UTF-8 encoding

= 1.3.2 =
* UTF-8 encoding if not already in UTF-8 before saving post content
* Change link and images informations: now an array with more details

= 1.3.1 =
* New API: is DOMDocument supported by server
* update post set to return wp_error object
* a is now an array

= 1.3.0 =
* Partial compatibility with some Page Builders: Visual Composer, Elementor, SiteOrigin Page Builder, Avia Framework Builder
* New class for DOM manipulation
* Change message returned in update slug if old url and new url are identical
* Change messages for JWT errors
* images, css, js are now in "assets" folder

= 1.2.5 =
* firebase/php-jwt upgraded to version 5.0.0

= 1.2.4 =
* Security improvements

= 1.2.3 =
* Add custom post types in get

= 1.2.2 =
* Improve performance

= 1.2.1 =
* Add filter in front-end render

= 1.2.0 =
* Support both Yoast SEO and All In One SEO Pack

= 1.1.7 =
* Uninstall plugin remove saved JWT keys

= 1.1.6 =
* changes for localization

= 1.1.5 =
* change icon size
* disable css from back-end editor

= 1.1.4 =
* Delete redirection if base is equal to redirect url

= 1.1.3 =
* Show JWT Keys in back-office

= 1.1.2 =
* Add HtmlDomParser library if no DOMDocument

= 1.1.1 =
* Compatible up to Wordpress 4.8
* Disable frontend CSS

= 1.1.0 =
* New api version - more generic
* Can have multiple JWT secrets on a same Wordpress website

= 1.0.3 =
* New api version for posts

= 1.0.2 =
* Bulk mode for h1...h6, a and img tags

= 1.0.1 =
* More informations for h1...h6

= 1.0.0 =
* Initial release
