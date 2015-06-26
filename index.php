<?php

session_start();

/*-----------------------------------------------------------------------------------*/
/* If the config file doesn't exist, Tell the user to copy over the config file
/*-----------------------------------------------------------------------------------*/
if (!file_exists('./config.php')) {

include "config.html";

exit;
}

/*-----------------------------------------------------------------------------------*/
/* Get Settings & Functions
/*-----------------------------------------------------------------------------------*/

include('./dropplets/settings.php');
include('./dropplets/functions.php');

/*-----------------------------------------------------------------------------------*/
/* Reading File Names
/*-----------------------------------------------------------------------------------*/

$category = NULL;
if (empty($_GET['filename'])) {
    $filename = NULL;
} 
else if($_GET['filename'] == 'rss' || $_GET['filename'] == 'atom') {
    $filename = $_GET['filename'];
}  
else {
    
    //Filename can be /some/blog/post-filename.md We should get the last part only
    $filename = basename($_GET['filename']);

    if (preg_match("/^category\//", $_GET['filename'])) {
        $category = $filename;
        $filename = NULL;
    }
    else {
      
        // Individual Post
        $filename = sprintf("%s%s%s",POSTS_DIR, $filename, FILE_EXT);
    }

}

/*-----------------------------------------------------------------------------------*/
/* The Home Page (All Posts)
/*-----------------------------------------------------------------------------------*/

if (is_null($filename)) {

    $page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1) ? $_GET['page'] : 1;
    $offset = ($page == 1) ? 0 : ($page - 1) * $posts_per_page;

    //Index page cache file name, will be used if index_cache = "on"

    $cachefile = CACHE_DIR . ($category ? $category : "index") .$page. '.html';

    //If index cache file exists, serve it directly wihout getting all posts
    if (file_exists($cachefile) && $index_cache != 'off') serve_cache($cachefile,"");


    if($category) {
        $all_posts = get_posts_for_category($category);
    } else {
        $all_posts = get_all_posts();
    }

    $pagination = ($pagination_on_off != "off") ? get_pagination($page,round(count($all_posts)/ $posts_per_page)) : "";
    define('PAGINATION', $pagination);
    $posts = ($pagination_on_off != "off") ? array_slice($all_posts,$offset,($posts_per_page > 0) ? $posts_per_page : null) : $all_posts;

    if($posts) {
        ob_start();
        $content = '';
        foreach($posts as $post) {
            
            $post_title          = format_title($post['post_title']);                   // Get the post title.
            $post_author         = format_author($post['post_author']);                 // Get the post author.
            $post_author_twitter = render_author_twitter($post['post_author_twitter']); // Get the post author twitter id.
            $published_date      = format_date($post['post_date']);                     // Generate the published date.
            $post_category       = render_category($post['post_category']);             // Get the post category.
            $post_category_link  = get_category_link($post_category);                   // Get the post category link.
            $post_status         = render_status($post['post_status']);                 // Get the post status.
            $post_intro          = render_intro($post['post_intro']);                   // Get the post intro.
            $post_content        = $post['post_content'];                               // Get the post content

            // Get the post link.
            if ($category) {
                $post_link = trim(strtolower($post_category)).'/'.str_replace(FILE_EXT, '', $post['fname']);
            } else {
                $post_link = $blog_url.str_replace(FILE_EXT, '', $post['fname']);
            }

            // Get the post image url.
            $post_image = get_post_image_url( $post['fname'] );

            if ($post_status == 'draft') continue;

            // Get the milti-post template file.
            include $posts_file;
        }
        echo $content;
        $content = ob_get_contents();

        // Get the site title
        $page_title = $blog_title;

        $get_page_meta = get_page_meta();

        // Get the page description and author meta.
        $get_page_meta[] = '<meta name="description" content="' . $meta_description . '">';
        $get_page_meta[] = '<meta name="author" content="' . $blog_title . '">';

        // Get all page meta.
        $page_meta = implode("\n", $get_page_meta);

        ob_end_clean();
    } else {
       serve_404();
    }
        ob_start();

        // Get the index template file.
        include_once $index_file;

        //Now that we have the whole index page generated, put it in cache folder
        save_cache($cachefile,ob_get_contents(),$index_cache);
    }

/*-----------------------------------------------------------------------------------*/
/* RSS Feed
/*-----------------------------------------------------------------------------------*/

else if ($filename == 'rss' || $filename == 'atom') {
    ($filename=='rss') ? $feed = new FeedWriter(RSS2) : $feed = new FeedWriter(ATOM);

    $feed->setTitle($blog_title);
    $feed->setLink($blog_url);

    if($filename=='rss') {
        $feed->setDescription($meta_description);
        $feed->setChannelElement('language', $language);
        $feed->setChannelElement('pubDate', date(DATE_RSS, time()));
    } else {
        $feed->setChannelElement('author', $blog_title.' - ' . $blog_email);
        $feed->setChannelElement('updated', date(DATE_RSS, time()));
    }

    $posts = get_all_posts();

    if($posts) {
        $c=0;
        foreach($posts as $post) {
            if($c<$feed_max_items) {
                $item = $feed->createNewItem();

                // Remove HTML from the RSS feed.
                $item->setTitle(substr($post['post_title'], 4, -6));
                $item->setLink(rtrim($blog_url, '/').'/'.str_replace(FILE_EXT, '', $post['fname']));
                $item->setDate($post['post_date']);

                // Remove Meta from the RSS feed.
				$remove_metadata_from = file(rtrim(POSTS_DIR, '/').'/'.$post['fname']);

                if($filename=='rss') {
                    $item->addElement('author', $blog_email . ' (' . str_replace('-', '', $remove_metadata_from[1]) .')');
                    $item->addElement('guid', rtrim($blog_url, '/').'/'.str_replace(FILE_EXT, '', $post['fname']));
                }

				// Remove the metadata from the RSS feed.
				unset($remove_metadata_from[0], $remove_metadata_from[1], $remove_metadata_from[2], $remove_metadata_from[3], $remove_metadata_from[4], $remove_metadata_from[5]);
				$remove_metadata_from = array_values($remove_metadata_from);

                $item->setDescription(Markdown(implode($remove_metadata_from)));

                $feed->addItem($item);
                $c++;
            }
        }
    }
    $feed->genarateFeed();
}

/*-----------------------------------------------------------------------------------*/
/* Single Post Pages
/*-----------------------------------------------------------------------------------*/

else {

    // Define the cached file.
    $cachefile = sprintf("%s%s.html",CACHE_DIR,basename($filename,FILE_EXT));

    // If there's no file for the selected permalink, grab the 404 page template.
    if (!file_exists($filename)) serve_404();

    // If there is a cached file for the selected permalink, display the cached post. 
    if (file_exists($cachefile)) serve_cache($cachefile,$fcontents[0]);

    // Display the individual post
    ob_start();
    
    $fcontents           = file($filename); // Define the post file.

    $post_title          = format_title($fcontents[0]);                              // Get the post title.
    $post_intro          = render_metadata_intro($fcontents[7]);                     // Get the post intro.
    $post_author         = format_author($fcontents[1]);                             // Get the post author.
    $post_author_twitter = render_author_twitter($fcontents[2]);                     // Get the post author Twitter ID.
    $published_date      = format_date($fcontents[3]);                               // Generate the published date.
    $post_category       = render_category($fcontents[4]);                           // Get the post category.
    $post_status         = render_status($fcontents[5]);                             // Get the post status.
    $post_category_link  = get_category_link($post_category);                        // Get the post category link.
    $post_link           = render_post_link($filename);                              // Get the post link.
    $post_image          = get_post_image_url($filename);                            // Get the post image url.
    $post_content        = Markdown(trim(implode("", array_slice( $fcontents, 7)))); // Get the post content
    $page_title          = render_page_title($fcontents[0]);                         // Get the site title.

    $get_page_meta = get_page_meta();

    // Generate the page description and author meta.
    $get_page_meta[] = '<meta name="description" content="' . $post_intro . '">';
    $get_page_meta[] = '<meta name="author" content="' . $post_author . '">';

    // Generate all page meta.
    $page_meta = implode("\n\t", $get_page_meta);

    // Get the post template file.
    include $post_file;

    $content = ob_get_contents();
    ob_end_clean();
    ob_start();

    // Get the index template file.
    include_once $index_file;

    // Cache the post on if caching is turned on.
    save_cache($cachefile,ob_get_contents(),$post_cache);
    
}
?>