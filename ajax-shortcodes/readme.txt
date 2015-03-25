=== Ajax shortcode ===
Contributors: Vlad Zaitsev
Tags: ajax,shortcode
Author URI: https://github.com/Zaitsev
Requires at least: 3.8
Tested up to: 4.1.1

retrieve shortcodes via ajax call

== Description ==
plugin used for retrieving shortcodes via ajax-call

*for security reasons, now plugin process only shortcodes, that beginned with 'ajax_'*
you can use html tag with data attribute to manage where retrieved data will be displayed
ex1:
<div class='sys-ajax-shortcode hidden' data-ajx_target="#shortcode_placeholder" data-ajx_shortcode="my_shortcode"></div>
where:
data-ajx_target - css-selector where to put short-code content. if you want to place result to html element with id="shortcode_placeholder" then you must set data-ajx_target="#shortcode_placeholder", if you want to add short-code to all elements with class="some_class" then data-ajx_target=".some_class"
data-ajx_shortcode - name of wordpress shortcode.

if you have [my_shortcode] that returns "<h1>I am shortcode</h1>"
and somewere in you page you have
<div id="shortcode_placeholder"></div>
then after page loading and ajax call you will have
'
<div id="shortcode_placeholder">
 <h1>I am shortcode</h1>
</div>
'
any data-* attributes except ajx_target and ajx_shortcode will be passed at shortcode parameters
so if you use yours shortcdde as

'<div id="my_gal">[ajax_gallery id="123" size="medium"]</div>'
then you can use it like this
'<div id="my_gal"  data-ajx_target="#my_gal" data-ajx_shortcode="ajax_gallery" data-id="123" data-size="medium"></div>'

== Installation ==
1. Upload the entire `ajax-shortcodes` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

== Upgrade Notice ==