=== SECDASH ===
Contributors: baseplus, phelmig
Plugin URI: https://secdash.de
Tags: secdash, monitoring, security, updates
Requires at least: 5.0.0
Tested up to: 5.5.1
Stable tag: 1.5.1
Requires PHP: 7.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SECDASH allows website owners to monitor the security of their WordPress installation and Plugins in almost real time.

== Description == 

SECDASH is a cloud-based website monitoring platform that empowers organizations with multiple websites to always know if they up-to-date and secure.

We provide organizations the SECDASH plug-in that they install on their website/s. The plug-in continuously sends us data of the website’s software assets (Content-Management-System (CMS), the installed modules). The data includes information such as names, version numbers, etc. At the other end we crawl the web and aggregate information that affects these website components such as available updates and security vulnerabilities.

SECDASH then matches the information and figures out if all parts of the website are okay. If not, SECDASH notifies the right person that is responsible for the affected website and component. In addition, we provide a dashboard that shows and visualizes all the information about the websites and its modules.

Try it out here: [secdash.de](https://www.secdash.de/)

== Installation ==

1. Upload the `secdash` folder to `/wp-content/plugins/`
1. Activate the plugin through the 'Plugins' menu in WordPress
3. Initialize SECDASH through the item 'SECDASH' under the 'Options' menu

== Changelog ==

= 1.5.1 =
* added local and database parameter for mailer token 

= 1.5.0 =
* mailer action added for form tests
* code cleanup

= 1.4.1 =
* fixed typo in ext-json check

= 1.4 =
* Reactivate plugin

= 1.3.1 =
* Another fix for even older PHP Versions

= 1.3 =
* Fix for old PHP Versions

= 1.2 =
*   First Plugin Update Support

= 1.1 =
*    Minor improvements

= 1.0 =
*   Major UI improvements
*   Localization (currently available in German und Swedish)
*   Improved initialization process
*   Optional handshake process when cookies are not available
*   First final release

= 0.9.5 =
*   The manual activation key is now shown in a text area to make it more accessible
*   Instructions / Error messages and UI improvements

= 0.9.4 =
*   Increased backwards compatibility. SECDASH now works even with WP3.0 (we don't recommend using this though ^^)

= 0.9.3 =
*   (re-)include wp-includes/version.php to make sure we get the right WP Version

= 0.9.2 =
*   Force HTTPS

= 0.9.1 =
*    Bugfix for older PHP Versions

= 0.9 =
*   Initial Public Release (Beta)
