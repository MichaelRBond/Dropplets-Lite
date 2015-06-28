<?php 

// @TODO remove all the variables and define everything directly as constants

/*-----------------------------------------------------------------------------------*/
/* Debug Mode
/*-----------------------------------------------------------------------------------*/

$display_errors = false;

// Display errors if there are any.
ini_set('display_errors', $display_errors);

/*-----------------------------------------------------------------------------------*/
/* Post Cache ('on' or 'off')
/*-----------------------------------------------------------------------------------*/

$post_cache = 'off';
$index_cache = 'off';

define("POST_CACHE",$post_cache);
define("INDEX_CACHE",$index_cache);

/*-----------------------------------------------------------------------------------*/
/* Configuration & Options
/*-----------------------------------------------------------------------------------*/

include('./config.php');

// Definitions from the included configs above.
define('BLOG_EMAIL', $blog_email);
define('BLOG_TWITTER', $blog_twitter);
define('BLOG_URL', $blog_url);
define('BLOG_TITLE', $blog_title);
define('META_DESCRIPTION', $meta_description);
define('INTRO_TITLE', $intro_title);
define('INTRO_TEXT', $intro_text);
define('HEADER_INJECT', stripslashes($header_inject));
define('FOOTER_INJECT', stripslashes($footer_inject));
define('ACTIVE_TEMPLATE', $template);

/*-----------------------------------------------------------------------------------*/
/* If is Home (Could use "is_single", "is_category" as well.)
/*-----------------------------------------------------------------------------------*/

$homepage = parse_url(BLOG_URL, PHP_URL_PATH);

// Get the current page.    
$currentpage  = $_SERVER["REQUEST_URI"];

// If is home.
$is_home = ($homepage==$currentpage);
define('IS_HOME', $is_home);
define('IS_CATEGORY', (bool)strstr($_SERVER['REQUEST_URI'], '/category/'));
define('IS_SINGLE', !(IS_HOME || IS_CATEGORY));

/*-----------------------------------------------------------------------------------*/
/* Definitions (These Should Be Moved to "Settings")
/*-----------------------------------------------------------------------------------*/

$language = 'en-us';
$feed_max_items = '10';
$date_format = 'F jS, Y';
$error_title = 'Sorry, But That&#8217;s Not Here';
$error_text = 'Really sorry, but what you&#8217;re looking for isn&#8217;t here. Click the button below to find something else that might interest you.';

define("LANGUAGE",$language);
define("FEED_MAX_ITEMS",$feed_max_items);
define("DATE_FORMAT",$date_format);
define("ERROR_TITLE",$error_title);
define("ERROR_TEXT",$error_text);

/*-----------------------------------------------------------------------------------*/
/* Post Configuration
/*-----------------------------------------------------------------------------------*/

$pagination_on_off = "off"; //Infinite scroll by default?
define('PAGINATION_ON_OFF', $pagination_on_off);

$posts_per_page = 4;
define('POSTS_PER_PAGE', $posts_per_page);

$infinite_scroll = "off"; //Infinite scroll works only if pagination is on.
define('INFINITE_SCROLL', $infinite_scroll);

$post_directory = './posts/';
$cache_directory = './posts/cache/';

if (glob($post_directory . '*.md') != false)
{
    $posts_dir = './posts/';
} else {
    $posts_dir = './dropplets/welcome/';
}

// Definitions from the settings above.
define('POSTS_DIR', $posts_dir);
define('CACHE_DIR', $cache_directory);
define('FILE_EXT', '.md');

/*-----------------------------------------------------------------------------------*/
/* Cache Configuration
/*-----------------------------------------------------------------------------------*/

if (!file_exists(CACHE_DIR) && ($post_cache != 'off' || $index_cache != 'off')) {
	mkdir(CACHE_DIR,0755,TRUE);
}

/*-----------------------------------------------------------------------------------*/
/* Template Files
/*-----------------------------------------------------------------------------------*/

// Get the active template directory.
$template_base_dir = './templates/';
$template_dir      = $template_base_dir . $template . '/';
$template_dir_url  = $blog_url . 'templates/' . $template . '/';

// Get the active template files.
$index_file        = $template_dir . 'index.php';
$post_file         = $template_dir . 'post.php';
$posts_file        = $template_dir . 'posts.php';
$not_found_file    = $template_dir . '404.php';

define("TEMPLATE_BASE_DIR",$template_base_dir);
define("TEMPLATE_DIR",$template_dir);
define("TEMPLATE_DIR_URL",$template_dir_url);
define("NOT_FOUND_FILE",$not_found_file);
define("INDEX_FILE",$index_file);
define("POST_FILE",$post_file);
define("POSTS_FILE",$posts_file);
