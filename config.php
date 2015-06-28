<?php
$blog_email       = "mbond@the-forgotten.org";
$blog_twitter     = "mbond";
$blog_url         = "http://the-forgotten.org/";
$blog_title       = "The-Forgotten.Org";

// meta tag for description in html header
$meta_description = "";

// Used to inject text (css, scripts, meta, etc ... ) into the header and footer of the site. 
// @TODO these should be files to be included. 
$header_inject    = "";
$footer_inject    = "";

// Template that the site uses. should be a directory located in the 
// templates directory
$template         = "simple";

// the maximum number of items to include in RSS feeds
$feed_max_items   = "10";

// Date format displayed with posts
// Format parameters are standard php date() function options
// http://php.net/manual/en/function.date.php
$date_format      = 'F jS, Y';

// set to "on" to cache individual posts, "off" to disable caching
$post_cache       = "off";

// set to "on" to cache the website homepage, "off" to disable caching
$index_cache      = "off";

// Turn on pagination on post list pages
// "on" or "off"
$pagination       = "off";

// How many posts to put in a single page, only used if $pagination is "on"
$posts_per_page   = 4;

// turn on infinate scrolling
// only used if $pagination is "on"
$infinate_scroll  = "off";

// the default language for the blog
$language         = 'en-us';

// 404 error page title
$error_title      = 'Sorry, But That&#8217;s Not Here';

// 404 error page text
// @TODO this should be an include or something
$error_text       = 'Really sorry, but what you&#8217;re looking for isn&#8217;t here. Click the button below to find something else that might interest you.';
