<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        
        <title><?php echo($page_title); ?></title>
        
        <?php echo($page_meta); ?>

        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="stylesheet" href="<?php print TEMPLATE_DIR_URL; ?>style.css">
        <link rel="stylesheet" href="<?php print TEMPLATE_DIR_URL; ?>subdiv.css">
        <link href='//fonts.googleapis.com/css?family=Merriweather:400,300,700' rel='stylesheet' type='text/css'>
        <link href='//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600' rel='stylesheet' type='text/css'>
        
        <?php get_header(); ?>
    </head>

    <body>

        <!-- if header.gif exists in the template directory, use it. otherwise, use the blog name from config.php -->
        <a href="<?php print BLOG_URL; ?>">
        <?php if (file_exists(TEMPLATE_DIR."/header.gif")) {?>
            <img src="<?php echo(TEMPLATE_DIR_URL); ?>header.gif" />
        <?php } else { ?>
            <h1><?php print BLOG_TITLE; ?></h1>
        <?php } ?>
        </a>

        <?php if(IS_HOME) { ?>
        <?php } ?>
        
        <?php echo($content); ?>
        
        <?php get_footer(); ?>
    </body>
</html>
