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
        <a href="<?php print $blog_url; ?>">
        <?php if (file_exists($template_dir."/header.gif")) {?>
            <img src="<?php echo($template_dir_url); ?>header.gif" />
        <?php } else { ?>
            <h1><?php print $blog_title; ?></h1>
        <?php } ?>
        </a>

        <?php if($is_home) { ?>
        <?php } ?>
        
        <?php echo($content); ?>
        
        <?php get_footer(); ?>
    </body>
</html>
