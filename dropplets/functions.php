<?php

/*-----------------------------------------------------------------------------------*/
/* Include 3rd Party Functions
/*-----------------------------------------------------------------------------------*/

include('./dropplets/includes/feedwriter.php');
include('./dropplets/includes/markdown.php');
include('./dropplets/includes/actions.php');

/*-----------------------------------------------------------------------------------*/
/* User Machine
/*-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Save Cache
/*-----------------------------------------------------------------------------------*/
function save_cache($cachefile,$content,$cache='on') {

    if ($cache != 'off') {

        try {
            if (($fp = fopen($cachefile, 'w')) === false) {
                throw new Exception("Error opening file");
            }
            fwrite($fp, $content);
            fclose($fp);
        }
        catch(Exception $e) {
            return false;
        }

    }

    return true;
}

/*-----------------------------------------------------------------------------------*/
/* Serve Cache
/*-----------------------------------------------------------------------------------*/
function serve_cache($cachefile,$page_title) {

    // Define site title
    $page_title = str_replace('# ', '', $page_title);

    // Get the cached post.
    include $cachefile;

    exit;

}

/*-----------------------------------------------------------------------------------*/
/* Serve 404 Error
/*-----------------------------------------------------------------------------------*/

function serve_404() {

    //Change the cache file to 404 page.
    $cachefile = CACHE_DIR.'404.html';

    // Define the site title.
    $page_title = $error_title;

    header("HTTP/1.1 404 Not Found");

    if (file_exists($cachefile)) {
        include $cachefile;
        exit;
    }

    // Start the Output buffer
    ob_start();
    
    // Get the 404 page template.
    include NOT_FOUND_FILE;

    // Get the contents.
    $content = ob_get_contents();

    // Flush the buffer so that we dont get the page 2x times.
    ob_end_clean();

    // Start new buffer.
    ob_start();

    // Get the index template file.
    include_once INDEX_FILE;

    // Cache the post on if caching is turned on.
    save_cache($cachefile,ob_get_contents());

    exit;
}

/*-----------------------------------------------------------------------------------*/
/* Get All meta tags for the page
/*-----------------------------------------------------------------------------------*/

function get_page_meta() {

    $get_page_meta = array();

    // Get the Twitter card.
    $get_page_meta[] = '<meta name="twitter:card" content="summary">';
    $get_page_meta[] = '<meta name="twitter:site" content="' . BLOG_TWITTER . '">';
    $get_page_meta[] = '<meta name="twitter:title" content="' . BLOG_TITLE . '">';
    $get_page_meta[] = '<meta name="twitter:description" content="' . META_DESCRIPTION . '">';
    $get_page_meta[] = '<meta name="twitter:creator" content="' . BLOG_TWITTER . '">';
    $get_page_meta[] = '<meta name="twitter:domain" content="' . BLOG_URL . '">';

    // Get the Open Graph tags.
    $get_page_meta[] = '<meta property="og:type" content="website">';
    $get_page_meta[] = '<meta property="og:title" content="' . BLOG_TITLE . '">';
    $get_page_meta[] = '<meta property="og:site_name" content="' . BLOG_TITLE . '">';
    $get_page_meta[] = '<meta property="og:url" content="' .BLOG_URL . '">';
    $get_page_meta[] = '<meta property="og:description" content="' . META_DESCRIPTION . '">';

    return $get_page_meta;

}

/*-----------------------------------------------------------------------------------*/
/* Post Category Link
/*-----------------------------------------------------------------------------------*/
function get_category_link($category) {

    return BLOG_URL.'category/'.urlencode(trim(strtolower($category)));

}

/*-----------------------------------------------------------------------------------*/
/* Format Post intro
/*-----------------------------------------------------------------------------------*/

function render_intro($intro) {

    return Markdown(trim($intro));
}

function render_metadata_intro($intro) {

    return htmlentities(trim($intro));
}

/*-----------------------------------------------------------------------------------*/
/* Format Post twitter author
/*-----------------------------------------------------------------------------------*/
function render_author_twitter($twitter_handle) {

    return $twitter_handle;
}

/*-----------------------------------------------------------------------------------*/
/* Format Post category
/*-----------------------------------------------------------------------------------*/
function render_category($category) {

    return $category;
}

/*-----------------------------------------------------------------------------------*/
/* Format Post status
/*-----------------------------------------------------------------------------------*/
function render_status($status) {

    return $status;
}

/*-----------------------------------------------------------------------------------*/
/* Format Post link
/*-----------------------------------------------------------------------------------*/
function render_post_link($filename) {

    return BLOG_URL.str_replace(array(FILE_EXT, POSTS_DIR), '', $filename);
}

/*-----------------------------------------------------------------------------------*/
/* Format Post Page Title
/*-----------------------------------------------------------------------------------*/
function render_page_title($title) {

    return trim($title,"# \t\n\r\0\x0B");
}

/*-----------------------------------------------------------------------------------*/
/* Format Post Title
/*-----------------------------------------------------------------------------------*/

function format_title($title) {

    return Markdown(str_replace(array("\n",'<h1>','</h1>'), '', $title));
}

/*-----------------------------------------------------------------------------------*/
/* Format Post Author
/*-----------------------------------------------------------------------------------*/

function format_author($author) {

    return $author;
}

/*-----------------------------------------------------------------------------------*/
/* Format Post Dates
/*-----------------------------------------------------------------------------------*/

function format_date($date) {

    return date(DATE_FORMAT, strtotime($date));

}

function cleanup_post_metadata($value) {

    return trim($value,"- \t\n\r\0\x0B");

}

/*-----------------------------------------------------------------------------------*/
/* Get All Posts Function
/*-----------------------------------------------------------------------------------*/

function get_all_posts($options = array()) {
    global $dropplets;

    if($handle = opendir(POSTS_DIR)) {

        $files = array();
        $filetimes = array();

        while (false !== ($entry = readdir($handle))) {
            if(substr(strrchr($entry,'.'),1)==ltrim(FILE_EXT, '.')) {

                
                $fcontents           = file(POSTS_DIR.$entry); // Define the post file.

                $post_title          = cleanup_post_metadata($fcontents[0]); // Define the post title.
                $post_author         = cleanup_post_metadata($fcontents[1]); // Define the post author.
                $post_author_twitter = cleanup_post_metadata($fcontents[2]); // Define the post author Twitter account.
                $post_date           = cleanup_post_metadata($fcontents[3]); // Define the published date.
                $post_category       = cleanup_post_metadata($fcontents[4]); // Define the post category.
                $post_status         = cleanup_post_metadata($fcontents[5]); // Define the post status.
                $post_intro          = cleanup_post_metadata($fcontents[7]); // Define the post intro.

                // Early return if we only want posts from a certain category
                if($options["category"] && $options["category"] != trim(strtolower($post_category))) {
                    continue;
                }

                // Define the post content
                $post_content = Markdown(join('', array_slice($fcontents, 6, $fcontents.length -1)));

                // Pull everything together for the loop.
                $files[]                = array('fname' => $entry, 'post_title' => $post_title, 'post_author' => $post_author, 'post_author_twitter' => $post_author_twitter, 'post_date' => $post_date, 'post_category' => $post_category, 'post_status' => $post_status, 'post_intro' => $post_intro, 'post_content' => $post_content);
                $post_dates[]           = $post_date;
                $post_titles[]          = $post_title;
                $post_authors[]         = $post_author;
                $post_authors_twitter[] = $post_author_twitter;
                $post_categories[]      = $post_category;
                $post_statuses[]        = $post_status;
                $post_intros[]          = $post_intro;
                $post_contents[]        = $post_content;
            }
        }

        if (is_array($post_dates)) array_multisort($post_dates, SORT_DESC, $files);
        
        return $files;

    } else {
        return false;
    }
}

/*-----------------------------------------------------------------------------------*/
/* Get Posts for Selected Category
/*-----------------------------------------------------------------------------------*/

function get_posts_for_category($category) {
    $category = trim(strtolower($category));
    return get_all_posts(array("category" => $category));
}

/*-----------------------------------------------------------------------------------*/
/* Get Image for a Post
/*-----------------------------------------------------------------------------------*/
function get_post_image_url($filename)
{
    global $blog_url;
    $supportedFormats = array( "jpg", "png", "gif" );
    $slug = pathinfo($filename, PATHINFO_FILENAME);

    foreach($supportedFormats as $fmt)
    {
        $imgFile = sprintf("%s%s.%s", POSTS_DIR, $slug, $fmt);
        if (file_exists($imgFile))
            return sprintf("%s/%s.%s", "${blog_url}posts", $slug, $fmt);
    }

    return false;
}

/*-----------------------------------------------------------------------------------*/
/* Post Pagination
/*-----------------------------------------------------------------------------------*/

function get_pagination($page,$total) {

    $string = '';
    $string .= "<ul style=\"list-style:none; width:400px; margin:15px auto;\">";

    for ($i = 1; $i<=$total;$i++) {
        if ($i == $page) {
            $string .= "<li style='display: inline-block; margin:5px;' class=\"active\"><a class=\"button\" href='#'>".$i."</a></li>";
        } else {
            $string .=  "<li style='display: inline-block; margin:5px;'><a class=\"button\" href=\"?page=".$i."\">".$i."</a></li>";
        }
    }
    
    $string .= "</ul>";
    return $string;
}

/*-----------------------------------------------------------------------------------*/
/* Get Installed Templates
/*-----------------------------------------------------------------------------------*/

function get_installed_templates() {
    
    // The currently active template.
    $active_template = ACTIVE_TEMPLATE;

    // The templates directory.
    $templates_directory = './templates/';

    // Get all templates in the templates directory.
    $available_templates = glob($templates_directory . '*');
    
    foreach ($available_templates as $template):

        // Generate template names.
        $template_dir_name = substr($template, 12);

        // Template screenshots.
        $template_screenshot = '' . $templates_directory . '' . $template_dir_name . '/screenshot.jpg'; {
            ?>
            <li<?php if($active_template == $template_dir_name) { ?> class="active"<?php } ?>>
                <div class="shadow"></div>
                <form method="POST" action="./dropplets/save.php">
                    <img src="<?php echo $template_screenshot; ?>">
                    <input type="hidden" name="template" id="template" required readonly value="<?php echo $template_dir_name ?>">
                    <button class="<?php if ($active_template == $template_dir_name) :?>active<?php else : ?>activate<?php endif; ?>" type="submit" name="submit" value="submit"><?php if ($active_template == $template_dir_name) :?>t<?php else : ?>k<?php endif; ?></button>
                </form>
            </li>
        <?php
        }
    endforeach;
}

/*-----------------------------------------------------------------------------------*/
/* Get Premium Templates
/*-----------------------------------------------------------------------------------*/

function get_premium_templates($type = 'all', $target = 'blank') {
    
    $templates = simplexml_load_file('http://dropplets.com/templates-'. $type .'.xml');
    
    if($templates===FALSE) {
        // Feed not available.
    } else {
        foreach ($templates as $template):
            
            // Define some variables
            $template_file_name=$template->file;
            $template_price=$template->price;
            $template_url=$template->url;
            
            { ?>
            <li class="premium">
                <img src="http://dropplets.com/demo/templates/<?php echo $template_file_name; ?>/screenshot.jpg">
                <a class="buy" href="http://gum.co/dp-<?php echo $template_file_name; ?>" title="Purchase/Download"><?php echo $template_price; ?></a> 
                <a class="preview" href="http://dropplets.com/demo/?template=<?php echo $template_file_name; ?>" title="Prview" target="_<?php echo $target; ?>">p</a>    
            </li>
            <?php } 
        endforeach;
    }
}

function count_premium_templates($type = 'all') {

    $templates = simplexml_load_file('http://dropplets.com/templates-'. $type .'.xml');

    if($templates===FALSE) {
        // Feed not available.
    } else {
        $templates = simplexml_load_file('http://dropplets.com/templates-'. $type .'.xml');
        $templates_count = $templates->children();
        echo count($templates_count);
    }
}

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
/* Include All Plugins in Plugins Directory
/*-----------------------------------------------------------------------------------*/

foreach(glob('./plugins/' . '*.php') as $plugin){
    include_once $plugin;
}

/*-----------------------------------------------------------------------------------*/
/* Dropplets Header
/*-----------------------------------------------------------------------------------*/

function get_header() {
    
    include TEMPLATE_BASE_DIR."generic/header.php";

    print "<!-- User Header Injection -->\n";
    print HEADER_INJECT;
    
    print "<!-- Plugin Header Injection -->\n";
    action::run('dp_header');

} 

/*-----------------------------------------------------------------------------------*/
/* Dropplets Footer
/*-----------------------------------------------------------------------------------*/

function get_footer() { 
    
    include TEMPLATE_BASE_DIR."generic/footer.php";
    
    print "<!-- User Footer Injection -->\n";
    print FOOTER_INJECT;
    
    print "<!-- Plugin Footer Injection -->\n";
    action::run('dp_footer');

}
