<?php 

/*-----------------------------------------------------------------------------------*/
/* Configuration & Options
/*-----------------------------------------------------------------------------------*/

include('./config.php');

// Definitions from the included configs above.
define('BLOG_EMAIL',        $blog_email);
define('BLOG_TWITTER',      $blog_twitter);
define('BLOG_URL',          $blog_url);
define('BLOG_TITLE',        $blog_title);
define('META_DESCRIPTION',  $meta_description);
define('HEADER_INJECT',     stripslashes($header_inject));
define('FOOTER_INJECT',     stripslashes($footer_inject));
define('ACTIVE_TEMPLATE',   $template);
define("POST_CACHE",        $post_cache);
define("INDEX_CACHE",       $index_cache);
define("LANGUAGE",          $language);
define("FEED_MAX_ITEMS",    $feed_max_items);
define("DATE_FORMAT",       $date_format);
define("ERROR_TITLE",       $error_title);
define("ERROR_TEXT",        $error_text);
define('PAGINATION_ON_OFF', $pagination); 
define('POSTS_PER_PAGE', 	$posts_per_page);
define('INFINITE_SCROLL', 	$infinite_scroll);

/*-----------------------------------------------------------------------------------*/
/* If is Home 
/*-----------------------------------------------------------------------------------*/

define('IS_HOME', (parse_url(BLOG_URL, PHP_URL_PATH)==$_SERVER["REQUEST_URI"]));
define('IS_CATEGORY', (bool)strstr($_SERVER['REQUEST_URI'], '/category/'));
define('IS_SINGLE', !(IS_HOME || IS_CATEGORY));

/*-----------------------------------------------------------------------------------*/
/* Post Configuration
/*-----------------------------------------------------------------------------------*/

define("POST_DIRECTORY",'./posts/');

// Definitions from the settings above.
define('POSTS_DIR', (glob(POST_DIRECTORY . '*.md'))?'./posts/':'./dropplets/welcome/');
define('CACHE_DIR', './posts/cache/');
define('FILE_EXT', '.md');

/*-----------------------------------------------------------------------------------*/
/* Cache Configuration
/*-----------------------------------------------------------------------------------*/

if (!file_exists(CACHE_DIR) && (POST_CACHE != 'off' || INDEX_CACHE != 'off')) {
	mkdir(CACHE_DIR,0755,TRUE);
}

/*-----------------------------------------------------------------------------------*/
/* Template Files
/*-----------------------------------------------------------------------------------*/

// Get the active template directory.
define("TEMPLATE_BASE_DIR",'./templates/');
define("TEMPLATE_DIR",TEMPLATE_BASE_DIR . $template . '/');
define("TEMPLATE_DIR_URL",BLOG_URL . 'templates/' . $template . '/');

// Get the active template files.
define("NOT_FOUND_FILE",TEMPLATE_DIR . '404.php');
define("INDEX_FILE",TEMPLATE_DIR . 'index.php');
define("POST_FILE",TEMPLATE_DIR . 'post.php');
define("POSTS_FILE",TEMPLATE_DIR . 'posts.php');
