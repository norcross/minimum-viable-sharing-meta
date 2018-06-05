=== Minimum Viable Sharing Meta ===
Contributors: norcross
Website Link: https://github.com/norcross/minimum-viable-sharing-meta
Donate link: http://andrewnorcross.com/donate
Tags: meta tags, sharing tags
Requires at least: 4.9
Tested up to: 4.9.6
Stable tag: 0.0.5
Requires PHP: 5.6
License: MIT
License URI: http://norcross.mit-license.org/

Just the minimum required meta tags to work.

== Description ==

Just the minimum required meta tags to work.

The source / inspiration for this plugin came from [this blog post](http://www.phpied.com/minimum-viable-sharing-meta-tags/) written by [Stoyan Stefanov](http://www.phpied.com/bio/)

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `minimum-viable-sharing-meta` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to the "Sharing Meta Tags" menu item underneath the "Appearance" main tab and set your defaults.
4. Set any individual posts or pages.

== Frequently Asked Questions ==


= What's this all about? =

Meta tags are a pain. And there are a lot of them. So this plugins boils it down to just the minimum tags required to "work".

= How do I use this? =

See the installation instructions.

= Is this really all of them? =

Yes, really.

I would suggest reading [this blog post](http://www.phpied.com/minimum-viable-sharing-meta-tags/) written by [Stoyan Stefanov](http://www.phpied.com/bio/) if you're curious.

= Can I customize it? =

Yeah. There are filters and whatnot that I'll eventually get around to documenting.

== Screenshots ==

1. Example settings page
2. Example post meta


== Changelog ==

= 0.0.5 - 2018/06/05 =
* added canonical URL field. props @dryan for the suggestion.
* included image meta tags from Yoast SEO in conversion function.

= 0.0.4 - 2018/01/08 =
* fixed incorrect admin file loading. props @raajtram.
* added metadata conversion for the Genesis theme framework.
* added `minshare_meta_localized_js_args` filter for localized args.
* added post meta removal on plugin deleting.


= 0.0.3 - 2018/01/02 =
* added metadata conversion for Yoast SEO and All In One SEO Pack.


= 0.0.2 - 2018/01/01 =
* added character count warnings for titles and descriptions
* introducted the `minshare_meta_use_default_tags` filter to use default values when tags for singular content has not been entered. The default is `false`.
* added the `MINSHARE_META_POSTKEY` and `MINSHARE_META_OPTIONKEY` constants to use in the entire plugin.


= 0.0.1 - 2017/12/28 =
* First release!


== Upgrade Notice ==
