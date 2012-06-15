=== Plugin Name ===
Contributors: aramzs
Donate link: http://aramzs.me/
Tags: twitter, shortcode, archival
Requires at least: 2.8
Tested up to: 3.31

This plugin allows you to place a shortcode in your post that will archive a Twitter search term, including hashtags. 

== Description ==

This plugin allows you to place a shortcode in your post that will archive a Twitter search term, including hashtags. 

Now upgraded to take advantage of WordPress's native oEmbed of tweets!

Shortcode [searchtwitter] options:

*   [searchtwitter for="#term"] - The 'for' attribute allows you to designate the search term you wish to search for and archive. You can use any term you'd normally but into the search.twitter.com search box. If you don't put anything in there, it will just search for me, which is cool and all, but probobly not what you want. It doesn't have to be a hashtag.
*   [searchtwitter for="#term" within="2012-04-24"] - The within attribute designates the limiting date for the search. If must be in the YYYY-MM-DD format, as shown above. You can limit searches by full date, as above, or by YYYY-MM or YYYY. However, there is no escaping Twitter's limit that only allows the plugin to retrieve the last 7 days of Tweets maximum. Default is no limitations. 
*   [searchtwitter for="#term" order="normal"] - By default the plugin displays and retrieves the Twitter list in reverse date format, with the earliest Tweet on top, and the latest Tweet on the bottom. You can put it in chronological order by designating the order attribute as "normal".
*   [searchtwitter for="#term" title="Twitter Archival listing of"] - By default the shortcode generates a h3 header for your list of Tweets that reads "Twitter Archive for" and is followed by your search term hyperlinked to Twitter's search page. You can change what precedes the title using this attribute. Or you can enter "none" to eliminate the entire h3 title section. 
*   [searchtwitter for="#term" blackbird="yes"] - HIGHLY EXPERIMENTAL. This will only work if you have the Twitter Blackbird Pie plugin installed. If you do, the shortcode queries the plugin and will attempt to skin all colected tweets using the Blackbird Pie method. Current testing indicates that the Blackbird plugin skins each tweet with an individual query to Twitter. As a result, for any collections of tweets over a certain unknown number Twitter will stop allowing you to query it and the remaining Tweets will fail to show up. 

    This plugin comes with default CSS styling. If you don't like it, you don't need to alter the plugin (and chance having your styling erased by an upgrade). Instead just create a stylesheet in your stylesheet directory called 'user-ta-style.css' and it will completely replace my stylesheet. 

    The maximum amount of tweets that can be retrieved is 1500.
	
Finally, this plugin would not have been possible without the kind sharing of code by [Daniel Thorogood](http://twitter.com/SLODeveloper), who wrote almost all of the code that interacts with Twitter, and styling information by [Kim Bui](http://twitter.com/kimbui). It wouldn't have been possible to get that code without [#wjchat] (http://wjchat.webjournalist.org/). Thanks to all of them! 

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the twiter-search-shortcode foldr to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Start using the [searchtwitter] shortcode anywhere you'd normally be able to use shortcodes in the site. Be aware, the archival function will only work in posts, as it uses the post meta. 

== Frequently Asked Questions ==

= I posted one Twitter chat, but it was the wrong one, how do I change it? =

This plugin does not yet support refreshing archives through the shortcode controls. In order to delete and recreate a Twitter search archive on your post, you have to edit your post, go into Custom Fields, and delete the content of the Value field for "twitter_search_archive". Then you'll need to resave the post with your new shortcode entry. 

= I used your shortcode in an area that wasn't a post and a week later all my tweets were gone! WTF? =

The archival function of the plugin is only functional with posts at this time. The reason is that it ties into WordPress's post-meta functionality to store your Twitter archive. 

= I can't put more than one archive in a post! =

No you cannot. At this time the plugin does not support more than one archive per post.

= When I first save the post and go to look at it, it loads really slowly. What's up with that? =

The first time you save a post using the Twitter Archival shortcode and post or view it, your server must contact Twitter and retrieve all the relevant Tweets. This takes time and it will sometimes be slow. After the first time, it will only be pulling from the archive stored on your server, and it will be much faster. 

= The timestamps on the tweets are all wrong! How can I get them to show my time? =

You have not set the date-time, or have the wrong date-time set. You can now set the date-time for your archives from the options page for the plugin.  

= When people change their avatars, it changes in my archive. =

Currently the plugin does not archive avatars. 

= This is a big list of things I want to do! When will you get on fixing them, so that I can do this stuff? =

I'm working on it! I'll push out updates as I get them together. In the meantime, you are free to help out. Come join the [GitHub for this project](https://github.com/AramZS/twitter-search-shortcode) and code with me!

= Doesn't this break Twitter's Terms of Service? =

I don't think so, I hope not, but I can't tell you for certain. You use the plugin at your own risk. I'm not liable for how you use or deploy this plugin.

== Screenshots ==

1. `tascreenshot.png`

== Changelog ==

= 0.85 =
* Built in integration with WordPress's oEmbed function for tweets. 

= 0.8 =
* Created Dashboard options page for plugin with instructions for use. 
* Now allow users to set a custom datetime for all archives to reference. 

= 0.6 =
* Fixed broken div.
* Time stamps now link to original Tweets.
* Added experimental Twitter Blackbird Pie support. 
* Further error checking added. 

= 0.5 =
* Finally got it working. 

== Upgrade Notice ==

= 0.85 =
* Now upgraded to take advantage of WordPress's native oEmbed of tweets!

= 0.8 =
* Created Dashboard options page for plugin with instructions for use. 
* Now allow users to set a custom datetime for all archives to reference. 
* Blackbird Pie Support

= 0.5 =
This version caches the chats, so you won't loose them in a week. 