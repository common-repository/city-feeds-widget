=== Plugin Name ===
Contributors: efalkner@yahoo.com
Donate link: http://ericfalkner.com/cityfeeds
Tags: city, feeds, zondervan, onthecity, plaza
Requires at least: 2.8
Tested up to: 3.1
Stable tag: 1.2

The city feeds widget is a simple plugin that allows you to pull your Zondervan OnTheCity.org plaza information into 
your wordpress website.  

== Description ==

The city feeds widget is a simple plugin that allows you to pull your Zondervan OnTheCity.org plaza information into 
your wordpress website.  Your plaza information is loaded via Ajax to prevent long page loads while waiting for the 
City to output the information. I wrote this plugin to integrate our plaza posts into our main website.  I hope it 
blesses your organization.  If you have any ideas for future releases, please email me (my address is in the plugin 
comments).

== Installation ==

1. Unzip the file you downloaded
2. Upload the `city-feeds` folder to the `/wp-content/plugins/` directory
3. Log into your Wordpress admin and activate the plugin through the 'Plugins' menu
4. In the widgets screen drag the widget to the content section you want it to show up in
5. Enter your settings:
   - Title:  The title you want to show up in your website (ie. City Feeds)
   - City URL:  Your subdomain url for your city
                ex. if your city URL is 'mycity.onthecity.org' enter 'mycity'
   - Show Feeds Newer Than:  This sets how new a post must be to show up
   - Max Feeds to Show:  The max number of feeds to list
   - Max Body Characters to Show:  This will limit the number of characters that are printed for a post in your site
   - Show post type in title:  If set to "yes" (default) the post type will be added to the title
   - Feeds to Include:  Check the boxes next to the feeds you want listed
                        NOTE:  As of v1.1, the City has fixed photos
   - Loading Icon to Use:  Select the icon you want to show for dynamic content loading.  The default is 'ajax-loading.gif'.

== Frequently Asked Questions ==

= Why don't my photos show up when I have the "photos" box checked? =

There is an issue with the way the City outputs the information that the City Feeds Widget uses that errors with photos.  
I have emailed Zondervan tech support and they have told me they will let me know what the developers find out.  Until
then, sorry.

= Why am I limited to my plaza posts? =

Because users have to log in to see the news feeds and other postings inside the city, there is no way for the City Feeds
Widget to gather that information.  This is a safety measure put in place by the City to prevent sensitive information
from being shared publicly.

= I made a post in the plaza but the widget isn't seeing it =

For the City Feeds Widget to gather the information, you have to make sure you have set your post to be publicly available.
Private URL posts will not be gathered by the widget.

= My loading icon is blue and my site is not blue =

As this build was for my church's website, the default set is teal over black.  You can choose from the standard set of icons
or you can create your own at http://ajaxload.info/.  Take the generated file called 'ajax-loader.gif' and replace the one in 
your city-feeds folder.  Refresh the widgets page in the admin and your new one should now be there.

== Screenshots ==

1. The widget is placed within your standard wordpress widget boxes
2. This is what the settings screen looks like for the widget
3. This is how the feeds look in your website (based on your template settings).  The widget uses standard header and <p> tags
   so it will fit into your layouts cleanly

== Changelog ==

= 1.2.1 =
* Fixed bug related to PHP's strtotime function causing event times to lose hours

= 1.2 =
* Added ability to sort posts (currently just by creation date)

= 1.1.2 =
* Fixed bug with wrong folder for widget
* Fixed other bugs
* Added inline styling to force block formatting for titles, comments and dates 

= 1.1.1 =
* Removed description stuff in AJAX helper file so it doesn't show up as a plugin

= 1.1 =
* Restored photo albums as the City fixed the JSON for albums page
* Changed events to be sorted by ending date, not creation date
* Added post type option that adds type to the title
* Fixed events bug setting all times to GMT (now adjusts for time zone)
* Added photo thumbnails to album posts in the feed
* Added icon selector for various colors

= 1.0 =
* This is the initial public release of the City Feeds Widget
* Added Ajax to speed up page loads

= 0.5 =
* This is the first build of the City Feeds Widget


