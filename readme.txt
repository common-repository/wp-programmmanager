=== Plugin Name ===
Contributors: Mantus667
Donate link: http://www.angelofagony.de.vu/
Tags: tabcontrol, jquery, datatables, programm, overview, download, count
Requires at least: 2.9.0
Tested up to: 3.0
Stable tag: 1.2

Display programms you want to share in a TabView with additional information.

== Description ==

With the wp-programmmanager plugin you are able to visualise programms, you want to share, in a TabControl.
This TabControl is build by jQuery, which comes with wordpress. The plugin also keeps track how often a programm
was downloaded. You can add additinal information about a programm, like a description, an installation/usage guide, a faq
and information about the download. To help you, style your information the plugin uses the TinyMCE-Editor which also comes with wordpress.

It allows you to change the style of the tabcontrol, the bordercolors and backgroundcolor, on a settings page. there you can also delete the database tables, the plugin creates.

It provides Shortcode to display the outcome. To show all programms just use the following shortcode [wp-pamanager]. To show programs for a specific category use [wp-pmanager cat_id="id"].

== Installation ==

1. Upload the Folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `[wp-pmanager]` where you want to display your programms
4. Ceate a folder named `downloads` on the root of your webspace
5. Put the `pmanager-count.php` in the root folder of your wordpress installation

== Frequently Asked Questions ==


== Screenshots ==

1. Output from Shortcode
2. Settingspage in the admin controlpanel
3. Editor to add the data of a programm
4. Output of the overviewpage

== Changelog ==

= 1.2 =
* Changed the Programm to be object-oriented
* Changed install script
* Now you can add Categorys to the Programms
* Added Option to show or don't show Categorys
* Added new Shortcode function. By placing [wp-pmanager cat_id="id"] you can show Programms which belongs to a specific category
* Changed style for settings page. Now it uses the Wordpress Metaboxes.

= 1.1.1 =
* Fixed Bug in Download Script
* Fixed Bug in Overview-Page

= 1.1 =
* Fixed several bugs
* Using idTabs jQuery Plugin for TabControl

= 1.0 =
* Initial realease


== Upgrade Notice ==

