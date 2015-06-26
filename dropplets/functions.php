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
function save_cache($cachefile,$content) {

    global $post_cache;

    if ($post_cache != 'off') {

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
/* Serve 404 Error
/*-----------------------------------------------------------------------------------*/

function serve_404() {

    // @TODO This is horrible. Settings need to be plopped into a variable
    // that can be referenced as a class
    include('./dropplets/settings.php');

    //Change the cache file to 404 page.
    $cachefile = CACHE_DIR.'404.html';

    // Define the site title.
    $page_title = $error_title;

    if (file_exists($cachefile)) {
        include $cachefile;
        exit;
    }

    // Start the Output buffer
    ob_start();
    
    // Get the 404 page template.
    include $not_found_file;

    // Get the contents.
    $content = ob_get_contents();

    // Flush the buffer so that we dont get the page 2x times.
    ob_end_clean();

    // Start new buffer.
    ob_start();

    // Get the index template file.
    include_once $index_file;

    // Cache the post on if caching is turned on.
    save_cache($cachefile,ob_get_contents());

    exit;
}

/*-----------------------------------------------------------------------------------*/
/* Get All meta tags for the page
/*-----------------------------------------------------------------------------------*/

function get_page_meta() {

    // @TODO This is horrible. Settings need to be plopped into a variable
    // that can be referenced as a class
    include('./dropplets/settings.php');

    $get_page_meta = array();

    // Get the Twitter card.
    $get_page_meta[] = '<meta name="twitter:card" content="summary">';
    $get_page_meta[] = '<meta name="twitter:site" content="' . $blog_twitter . '">';
    $get_page_meta[] = '<meta name="twitter:title" content="' . $blog_title . '">';
    $get_page_meta[] = '<meta name="twitter:description" content="' . $meta_description . '">';
    $get_page_meta[] = '<meta name="twitter:creator" content="' . $blog_twitter . '">';
    $get_page_meta[] = '<meta name="twitter:domain" content="' . $blog_url . '">';

    // Get the Open Graph tags.
    $get_page_meta[] = '<meta property="og:type" content="website">';
    $get_page_meta[] = '<meta property="og:title" content="' . $blog_title . '">';
    $get_page_meta[] = '<meta property="og:site_name" content="' . $blog_title . '">';
    $get_page_meta[] = '<meta property="og:url" content="' .$blog_url . '">';
    $get_page_meta[] = '<meta property="og:description" content="' . $meta_description . '">';

    return $get_page_meta;

}

/*-----------------------------------------------------------------------------------*/
/* Format Post Dates
/*-----------------------------------------------------------------------------------*/

function format_date($date) {

    // @TODO This is horrible. Settings need to be plopped into a variable
    // that can be referenced as a class
    include('./dropplets/settings.php');

    $published_iso_date = str_replace('-', '', $date);

    // Generate the published date.
    $published_date = date($date_format, strtotime($published_iso_date));

    return $published_date;

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

                // Define the post file.
                $fcontents = file(POSTS_DIR.$entry);

                // Define the post title.
                $post_title = Markdown($fcontents[0]);

                // Define the post author.
                $post_author = str_replace(array("\n", '-'), '', $fcontents[1]);

                // Define the post author Twitter account.
                $post_author_twitter = str_replace(array("\n", '- '), '', $fcontents[2]);

                // Define the published date.
                $post_date = str_replace('-', '', $fcontents[3]);

                // Define the post category.
                $post_category = str_replace(array("\n", '-'), '', $fcontents[4]);

                // Early return if we only want posts from a certain category
                if($options["category"] && $options["category"] != trim(strtolower($post_category))) {
                    continue;
                }

                // Define the post status.
                $post_status = str_replace(array("\n", '- '), '', $fcontents[5]);

                // Define the post intro.
                $post_intro = Markdown($fcontents[7]);

                // Define the post content
                $post_content = Markdown(join('', array_slice($fcontents, 6, $fcontents.length -1)));

                // Pull everything together for the loop.
                $files[] = array('fname' => $entry, 'post_title' => $post_title, 'post_author' => $post_author, 'post_author_twitter' => $post_author_twitter, 'post_date' => $post_date, 'post_category' => $post_category, 'post_status' => $post_status, 'post_intro' => $post_intro, 'post_content' => $post_content);
                $post_dates[] = $post_date;
                $post_titles[] = $post_title;
                $post_authors[] = $post_author;
                $post_authors_twitter[] = $post_author_twitter;
                $post_categories[] = $post_category;
                $post_statuses[] = $post_status;
                $post_intros[] = $post_intro;
                $post_contents[] = $post_content;
            }
        }
        array_multisort($post_dates, SORT_DESC, $files);
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

function get_header() { ?>
    <!-- RSS Feed Links -->
    <link rel="alternate" type="application/rss+xml" title="Subscribe using RSS" href="<?php echo BLOG_URL; ?>rss" />
    <link rel="alternate" type="application/atom+xml" title="Subscribe using Atom" href="<?php echo BLOG_URL; ?>atom" />
    
    <!-- Dropplets Styles -->
    <link rel="stylesheet" href="<?php echo BLOG_URL; ?>dropplets/style/style.css">
    <link rel="shortcut icon" href="<?php echo BLOG_URL; ?>dropplets/style/images/favicon.png">

    <!-- User Header Injection -->
    <?php echo HEADER_INJECT; ?>
    
    <!-- Plugin Header Injection -->
    <?php action::run('dp_header'); ?>
<?php 

} 

/*-----------------------------------------------------------------------------------*/
/* Dropplets Footer
/*-----------------------------------------------------------------------------------*/

function get_footer() { ?>
    <!-- jQuery & Required Scripts -->
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    
    <?php if (!IS_SINGLE && PAGINATION_ON_OFF !== "off") { ?>
    <!-- Post Pagination -->
    <script>
        var infinite = true;
        var next_page = 2;
        var loading = false;
        var no_more_posts = false;
        $(function() {
            function load_next_page() {
                $.ajax({
                    url: "index.php?page=" + next_page,
                    beforeSend: function () {
                        $('body').append('<article class="loading-frame"><div class="row"><div class="one-quarter meta"></div><div class="three-quarters"><img src="./templates/<?php echo(ACTIVE_TEMPLATE); ?>/loading.gif" alt="Loading"></div></div></article>');
                        $("body").animate({ scrollTop: $("body").scrollTop() + 250 }, 1000);
                    },
                    success: function (res) {
                        next_page++;
                        var result = $.parseHTML(res);
                        var articles = $(result).filter(function() {
                            return $(this).is('article');
                        });
                        if (articles.length < 2) {  //There's always one default article, so we should check if  < 2
                            $('.loading-frame').html('You\'ve reached the end of this list.');
                            no_more_posts = true;
                        }  else {
                            $('.loading-frame').remove();
                            $('body').append(articles);
                        }
                        loading = false;
                    },
                    error: function() {
                        $('.loading-frame').html('An error occurred while loading posts.');
                        //keep loading equal to false to avoid multiple loads. An error will require a manual refresh
                    }
                });
            }

            $(window).scroll(function() {
                var when_to_load = $(window).scrollTop() * 0.32;
                if (infinite && (loading != true && !no_more_posts) && $(window).scrollTop() + when_to_load > ($(document).height()- $(window).height() ) ) {
                    // Sometimes the scroll function may be called several times until the loading is set to true.
                    // So we need to set it as soon as possible
                    loading = true;
                    setTimeout(load_next_page,500);
                }
            });
        });
    </script>
    <?php } ?>
    
    <!-- Dropplets Tools -->
    
    <!-- User Footer Injection -->
    <?php echo FOOTER_INJECT; ?>
    
    <!-- Plugin Footer Injection -->
    <?php action::run('dp_footer'); ?>
<?php 

}
