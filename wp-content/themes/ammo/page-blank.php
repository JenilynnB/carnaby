<?php
/*
 * Template Name: Blank Page Template
 */
?>
<!DOCTYPE HTML>
<!--[if IE 6]>
<html class="oldie ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html class="oldie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="oldie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<head>
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    
    <!--[if lt IE 9]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="<?php echo get_template_directory_uri(); ?>/assets/plugins/html5shiv.js"></script>
    <![endif]-->

    <title> <?php wp_title("|",true, 'right'); ?> <?php if (!defined('WPSEO_VERSION')) { bloginfo('name'); } ?></title>
    
    <!-- Favicons -->
    <?php tt_icons(); ?>
    <?php
        global $smof_data;

        $body_classes = $nav_fixed = '';
        if(get_theme_mod('general-layout') == 'boxed'){ $body_classes .= 'boxed '; }
        if(isset($smof_data['use_responsive']) && $smof_data['use_responsive'] != 1){ $body_classes .= 'non-responsive '; }
        else { echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">'; }
        if(isset($smof_data['fixed_menu']) && $smof_data['fixed_menu'] == 1){ $nav_fixed = 'navbar-fixed-top'; }

    ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class($body_classes); ?>>


    <div class="layout-wrapper">

        <?php
        while ( have_posts() ) : the_post();
            $blank_page = true;
            include file_require(dirname(__FILE__).'/template-page.php');
        endwhile;
        ?>
        
        <?php tt_trackingcode(); ?>
        <?php wp_footer(); ?>

        <!--[if lt IE 9]>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/plugins/respond.min.js"></script>
        <![endif]-->


    </div><!-- end wrapper -->

    <span class="gototop">
        <i class="fa fa-angle-up"></i>
    </span>

</body>
</html>