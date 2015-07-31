=== Facebook Social Stream ===
Contributors: Daniele Angileri
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WLXKFHGZ9WWGN
Tags: facebook, facebook stream, facebook feed, facebook page, facebook wall, facebook posts, custom facebook feed, custom facebook stream, custom facebook wall, custom facebook posts, social media, social stream, responsive, mobile
Requires at least: 3.0.1
Tested up to: 4.2.3
Stable tag: 1.3.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Facebook Social Stream plugin allows you to simply display a Facebook feed of a public Facebook page on your website

== Description ==

The Facebook Social Stream plugin generates a **responsive**, **SEO optimized** and **cached** Facebook feed for your website.
**You do not even need a Facebook API key!** 

Just configure the Facebook page name and add the shortcode `[fb_social_stream]` to your page. That's it.

Have a look at my **[example](http://angileri.de/blog/en/free-wordpress-plugin-facebook-social-stream/)** page for a live demo.


= Info = 
This plugin was officially released at **July 17th 2015** and will be ongoing improved by new features.
If you have found some bugs or if you have feature requests, please do not hesitate to get in touch with me!

It would be nice to contact me if you have troubles before you leave a bad rating. Thanks :)


= Contact =
* [Check for more information here](http://angileri.de/blog/en/free-wordpress-plugin-facebook-social-stream/)
* [Get free support from me here](https://wordpress.org/support/plugin/facebook-social-stream)


= Philosophy =
* KISS principle (keep it simple, stupid)
* Easiest possible configuration
* Help whenever needed as fast as possible


= Features =
* Easy to add via **shortcode** `[fb_social_stream]`
* **Customizable** appearance without CSS knowledge
* **Responsive** behaviour
* **Crawlable** by search engine bots
* Fast delivery by **caching**
* HTML-links of Hashtags
* HTML-links of message URLs
* modern **HTML5 video** delivery
* display **YouTube** videos as video-box

= NEW Extensions =
* show **Facebook comments** within your stream


== Installation ==

1. Upload plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the Facebook Social Stream plugin via the Settings page
4. Use the shortcode `[fb_social_stream]` to display the feed

== Frequently Asked Questions ==
= What kind of Facebook posts can be displayed as stream? =
The Facebook page has to be publicly accessible and without any restrictions like age verifications.
It is not possible to retrieve the information of your personal profile or any private
Facebook group.

= How do I find the page-name of my Facebook page? =
The page name of your Facebook page can be identified by the last part of the URL.
If your URL looks like this `https://www.facebook.com/angileri.de` then `angileri.de` is the name of your Facebook page.

= How does the update of the stream works? =
You can configure the update interval in the plugin settings. 
Once someone browses your site with a social stream, the plugin checks if the update interval is reached. This happens via ajax in background to prevent page-load issues.
If it is time to update, the plugin retrieves new Facebook data, stores it into the database and even updates the current HTML for the user immediately!

= What is a Facebook Access Token? =
In order to read Facebook data via the Facebook Graph API you need to authenticate yourself. This is done with a Facebook Access Token.

= Do I need a Facebook Access Token? =
**No**. If you don't have an Access Token the Facebook Social Stream plugin uses a fallback service to retrieve the data.

= Can I change the appearance of the stream? =
**Yes**. You can simply change the style of your stream within the plugin-settings. A color-picker supports you to find the right color hue easily.

= Does the stream also deliver Facebook videos? =
**Yes**. Facebook videos are delivered as modern HTML5 videos.
I did not use Flash on purpose. Many mobile phones do not support it and it will die anyway on the long run. 


== Screenshots ==
1. Plugin configuration
2. Example stream of Facebook page "angileri.de"
3. Example stream of Facebook page "bbcnews"

== Changelog ==

= 1.4.0 =
* Features
	* Extensions area available for new features
	* Introduced main administration page
* Enhancements
	* Outsourced inline HTML into template-views

= 1.3.6 =
* Features
	* Display shared videos (e.g. YouTube) as video-box
* Enhancements
	* Switched to Facebook Graph API v2.4
	* Reduced API calls

= 1.3.5 =
* Bugfixes
	* QuickFix for Facebook API changes

= 1.3.4 =
* Features
	* Show last stream-update time on settings page
	* Update stream manually via settings page

= 1.3.3 =
* Features
	* Added video messages as HTML5 video player (Flash dies anyway)
	* Added text-color-customization options of default template
* Enhancements
	* Recognize WordPress timezone settings for date output
	* Harmonized plugin option names

= 1.3.2 =
* Enhancements
	* Replace \n with html line-breaks in message-text

= 1.3.1 =
* Enhancements
	* Improved regex to identify hashtags and links in message-text

= 1.3.0 =
* Features
	* Added color-customization to plugin-settings
	* Added color-picker to settings page
	* Introduced configuration of template-styling
* Enhancements
	* Save current version number in database for later update routines
	* Update stream automatically if page-name or access-token changed or max-messages increased

= 1.2.6 =
* Enhancements
	* Set Javascript variables via wp_localize_script() to identify ajax-url in script

= 1.2.5 =
* Enhancements
	* Changed default value of update interval
* Bugfixes
	* Set database table to innoDB engine to prevent "Specified key was too long" error

= 1.2.4 =
* Bugfixes
	* Fixed Javascript error

= 1.2.3 =
* Enhancements
 	* Link to gallery instead of first image of gallery
 	* Skip videos until video-player is implemented
* Bugfixes
	* Changed CSS style of links without images to prevent whitespace

= 1.2.2 =
* Enhancements
	* Validation of Facebook page-name
	* Changed CSS to add responsive behaviour to profile image

= 1.2.1 =
* Enhancements
	* Renamed classes to prevent conflicts with other plugins or themes

= 1.2.0 =
* Features
	* Added schema.org markup to default-template
	* Added German translations
	* Create HTML-links out of message text URLs
	* Create HTML-links out of hashtags in message text
* Bugfixes
	* Added alt-attribute to link-image
	* Fixed likes- and comments-count
* Enhancements
	* Cleanup cached data after changing Facebook page-name

= 1.1.0 =
* Features
	* Added function to identify Facebook post-type "link"
	* Extended default-template to handle Facebook "link" objects
	* Changed colour-theme of default-template into bright grey

= 1.0.0  =
* First version ready to go!
