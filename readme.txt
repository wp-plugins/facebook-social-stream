=== Facebook Social Stream ===
Contributors: Daniele Angileri
Tags: facebook, facebook stream, facebook feed, facebook page, facebook wall, facebook posts, custom facebook feed, custom facebook stream, custom facebook wall, custom facebook posts, social media, social stream, responsive, mobile
Requires at least: 3.0.1
Tested up to: 4.2.2
Stable tag: 1.2.5
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
* [Get free help from me here](http://angileri.de/#contact)
* [Forums](https://wordpress.org/support/plugin/facebook-social-stream)


= Philosophy =
* KISS principle (keep it simple, stupid)
* Easiest possible configuration
* Help whenever needed as fast as possible


= Features =
* Easy to add via shortcode `[fb_social_stream]`
* Responsive behaviour
* Crawlable by search engine bots
* Fast delivery by caching
* HTML-links of Hashtags
* HTML-links of message URLs


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



== Screenshots ==
1. Plugin configuration
2. Example stream of Facebook page "angileri.de"
3. Example stream of Facebook page "bbcnews"

== Changelog ==

= 1.2.6 =
* Set Javascript variables via wp_localize_script() to identify ajax-url in script

= 1.2.5 =
* Changed default value of update interval
* Set database table to innoDB engine to prevent "Specified key was too long" error

= 1.2.4 =
* Fixed Javascript error

= 1.2.3 =
* Changed CSS style of links without images to prevent whitespace
* Link to gallery instead of first image of gallery
* Skip videos until video-player is implemented

= 1.2.2 =
* Validation of Facebook page-name
* Changed CSS to add responsive behaviour to profile image

= 1.2.1 =
* Renamed classes to prevent conflicts with other plugins or themes

= 1.2.0 =
* Added schema.org markup to default-template
* Added alt-attribute to link-image
* Added German translations
* Fixed likes- and comments-count
* Cleanup cached data after changing Facebook page-name
* Create HTML-links out of message text URLs
* Create HTML-links out of hashtags in message text

= 1.1.0 =
* Added function to identify Facebook post-type "link"
* Extended default-template to handle Facebook "link" objects
* Changed colour-theme of default-template into bright grey

= 1.0.0  =
* First version ready to go!


`<major>.<feature>.<hotfix>`