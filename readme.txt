=== Plugin Name ===
Contributors: contrid, Antonie Potgieter
Donate link: http://tribulant.com
Tags: whois, domain, search, whois search, wp whois, wordpress whois, domain whois, whois plugin, wordpress whois plugin
Requires at least: 2.9
Tested up to: 3.3.1
Stable tag: 1.4.2.4

Get information on any domain name

== Description ==

The plugin allows you to insert a tag into any WordPress post or page to generate a WHOIS search form for your users to get information on domain names with different TLDs.

* Antonie Potgieter : http://tribulant.com (Author/Developer)

Please have a look at the <a href="http://docs.tribulant.com/wordpress-whois-plugin/630" title="WordPress WHOIS Change Log">WordPress WHOIS plugin release notes</a>.

== Features ==

The WordPress whois plugin has many great features including...

1. Ajax domain search form
1. Referral/affiliate redirect feature
1. Sidebar widget(s)
1. Embed into any post/page
1. Custom CSS for front-end
1. Multiple TLDs

== Installation ==

Please see the <a href="http://docs.tribulant.com/wordpress-whois-plugin/630" title="WordPress WHOIS Installation">WordPress WHOIS installation instructions</a>.

1. Extract 'wordpress-whois-search.zip' on your computer.
1. Rename 'wordpress-whois-search' folder to 'wp-whois' to prevent problems.
1. Activate the 'WHOIS' plugin in the 'Plugins' section in your dashboard.
1. Configure the WordPress WHOIS plugin under 'Tools' > 'WHOIS'
1. Put the shortcode `[whois]` into any WordPress post or page to generate the WHOIS search form.

== Frequently Asked Questions ==

= Do you have a demonstration? =

Sure thing. You can see a full demonstration at : http://tribulant.net/whois/
Please use username & password : <strong>demo</strong>

== Screenshots ==

1. Domain WHOIS search form with captcha security image
2. General settings section under 'Tools' > 'WHOIS'
3. TLD settings section under 'Tools' > 'WHOIS'
4. Domain WHOIS search output illustration
5. Custom CSS feature for front-end
6. WHOIS sidebar widget control
7. Sidebar widget with domain WHOIS search form

== Changelog ==

= 1.4.2.4 =
* FIXED: TinyMCE error breaking Visual editor.

= 1.4.2.3 =
* FIXED: XSS Vulnerability via Ajax query string.
* IMPROVE: Static stylesheet LINK tag to wp_enqueue_style.
* CHANGE: Custom Ajax call to wp_ajax_ and wp_ajax_nopriv_ hook.
* CHANGE: Locale load_textdomain to load_plugin_textdomain function.

= 1.4.2.2 =
* FIXED: The .co.za WHOIS server was not accepting requests anymore.
* IMPROVED: Small improvements throughout the plugin.

= 1.4.2 =
* FIXED: Fatal error when Really Simple Captcha is not installed and/or active.
* FIXED: Broken domain WHOIS search sidebar widget.
* FIXED: Broken Custom CSS under 'Tools' > 'WHOIS'.
* ADDED: .it TLD
* ADDED: .fr TLD
* ADDED: .ie TLD
* ADDED: Link under 'Tools' > 'WHOIS' to quickly reload modified TLDs from the config.txt file.
* ADDED: WordPress 3.2.1 compatibility!

= 1.4.1 =
* FIXED: A fatal error which kept on haunting me. I fixed it and fixed it and fixed it but the SVN kept on showing the old one so I decided to give it a new version number. Hold your thumbs!

= 1.4 =
* ADDED: TLDs - .mobi, .asia, .tel, .me, .tv, .mx
* ADDED: TinyMCE editor button for shortcode.
* CHANGED: Porting from Prototype/Scriptaculous to jQuery.
* IMPROVED: Ajax on widgets refreshes an inner DIV for compatibility with all themes.
* IMPROVED: Draggable meta boxes in 'Tools' > 'WHOIS'.
* CHANGED: Captcha now uses Really Simple Captcha API and requires the Really Simple Captcha plugin to be installed and active.

= 1.3.7 =
* ADDED: Caching of WHOIS lookups.
* IMPROVED: Improved meta boxes in admin dashboard.
* REMOVED: PHP short open tags.
* ADDED: Directory separator constant DS.
* ADDED: Israel .CO.IL and .ORG.IL TLDs added.
* COMPATIBILITY: Fully tested on Windows IIS with PHP 5.

= 1.3.3 =
* Full & short output options
* .SE domain TLD added
* .CL domain TLD added
* Other improvements...

= 1.3.2 =
* WordPress 2.8.X Compatibility