<?php 

/*-----------------------------------------------------------------------------------*/
/* Debug Mode
/*-----------------------------------------------------------------------------------*/

$display_errors = false;

// Display errors if there are any.
ini_set('display_errors', $display_errors);

/*-----------------------------------------------------------------------------------*/
/* Post Cache ('on' or 'off')
/*-----------------------------------------------------------------------------------*/

define("POST_CACHE",'off');
define("INDEX_CACHE",'off');

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
/* If is Home 
/*-----------------------------------------------------------------------------------*/

define('IS_HOME', (parse_url(BLOG_URL, PHP_URL_PATH)==$_SERVER["REQUEST_URI"]));
define('IS_CATEGORY', (bool)strstr($_SERVER['REQUEST_URI'], '/category/'));
define('IS_SINGLE', !(IS_HOME || IS_CATEGORY));

/*-----------------------------------------------------------------------------------*/
/* Definitions (These Should Be Moved to "Settings")
/*-----------------------------------------------------------------------------------*/

define("LANGUAGE",'en-us');
define("FEED_MAX_ITEMS",'10');
define("DATE_FORMAT",'F jS, Y');
define("ERROR_TITLE",'Sorry, But That&#8217;s Not Here');
define("ERROR_TEXT",'Really sorry, but what you&#8217;re looking for isn&#8217;t here. Click the button below to find something else that might interest you.');

/*-----------------------------------------------------------------------------------*/
/* Post Configuration
/*-----------------------------------------------------------------------------------*/

define('PAGINATION_ON_OFF', "off"); //Infinite scroll by default?
define('POSTS_PER_PAGE', 4);
define('INFINITE_SCROLL', "off"); //Infinite scroll works only if pagination is on.

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
