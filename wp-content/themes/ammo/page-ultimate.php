<?php
/*
 * Template Name: Ultimate Template
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
        global $smof_data, $post;

        $body_classes = $nav_fixed = '';
        $up_page_layout = tt_getmeta('up_layout', $post->ID);
        if( $up_page_layout == 'boxed'){
            $body_classes .= 'boxed ';
        }

        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        
        // check fixed menu
        if( tt_getmeta('up_fixed_menu', $post->ID) == 1){ $nav_fixed = 'navbar-fixed-top'; }

        // check header transparent
        $header_transparent = tt_getmeta('up_header_transparent', $post->ID)=='1' ? 'header-transparent' : '';
        $body_classes .= 'slidemenu-push';

        // Page Background style when Boxed layout
        $page_custom_style = '';
        $upbg = tt_getmeta('up_background_img', $post->ID);
        if( $up_page_layout == 'boxed' && !empty($upbg) ){
            $up_split = explode('$', $upbg); //img, repeat, position, attach
            $page_custom_style .= 'background-image:url('.$up_split[0].') !important;';
            if( $up_split[1]=='cover' ){
                $page_custom_style .= 'background-size:cover !important; background-repeat:no-repeat !important;'; }
            else{ $page_custom_style .= 'background-repeat:'.$up_split[1].' !important;'; }
            $page_custom_style .= 'background-position:'.$up_split[2].' !important;';
            $page_custom_style .= 'background-attachment:'.$up_split[3].' !important;';
            $page_custom_style = "body{ $page_custom_style }";
        }
        if( $up_page_layout == 'boxed' ){
            $upmtop = tt_getmeta('up_margin_top', $post->ID);
            $upmbottom = tt_getmeta('up_margin_bottom', $post->ID);
            $page_custom_style .= '.layout-wrapper{ margin-top: '.$upmtop.'px !important; margin-bottom: '.$upmbottom.'px !important; }';
        }
        echo "<style type='text/css'> $page_custom_style </style>";

    ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class($body_classes); ?>>


    <div class="layout-wrapper">

        <?php
        /* Page Top Slider */
        getPageSlider(true);
        ?>

        <?php if( tt_getmeta('up_header', $post->ID)!='0' ): ?>
        <div id="header_spacing" class="hidden-xs hidden-sm" style="height: 80px;"></div>
        <!-- Start Header
        ================================================== -->
        <header id="header" class="header active-section navbar-inverse <?php echo $nav_fixed.' '.$header_transparent; ?>" role="banner">

            <?php if( tt_getmeta('up_topbar', $post->ID) == '1' ) : ?>
            <div id="top_bar" class="top-bar">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="top-bar-left">
                                <?php tt_bar_content($smof_data['top_bar_left']); ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="top-bar-right text-right">
                                <?php tt_bar_content($smof_data['top_bar_right']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; //top bar ?>
            
            <div class="container">
                <div class="row">
                    <div class="header-style">
                        <div class="hidden-lg hidden-md visible-sm visible-xs">
                            <!-- Your Logo -->
                            <?php tt_up_site_logo(); ?>
                        </div>
                        <!-- Start Navigation -->
                        <nav class="main-menu hidden-xs hidden-sm visible-md visible-lg" role="navigation">
                            <?php if( tt_getmeta('up_enable_logo', $post->ID)=='1' ): ?>
                                <?php tt_up_site_logo(); ?>
                            <?php endif; ?>

                            <?php
                                $menu_alignment = tt_getmeta('up_menu_align', $post->ID)!='' ? tt_getmeta('up_menu_align', $post->ID) : 'left';
                            ?>
                            <div class="navmenu-cell" style="text-align:<?php echo $menu_alignment; ?>;">
                                <?php
                                    render_mega_nav();
                                ?>
                            </div>
    
                            <?php 
                            // Search Box
                            if( tt_getmeta('up_enable_search', $post->ID) == '1'):
                                echo '<div class="header-search"><a class="search-icon"><i class="fa fa-search"></i></a>';
                                get_search_form();
                                echo '</div>';
                            endif; ?>
                        </nav>
    
                        <!-- MOBILE MENU START -->
                        <div id="mobile-menu-wrapper" class="visible-xs visible-sm" data-skin="<?php echo isset($smof_data['mobile_menu_dark']) ? $smof_data['mobile_menu_dark'] : '0'; ?>">
                            <?php get_mobile_cart_holder(); ?>
                            <a class="mobile-menu-icon" href="javascript:;" id="mobile-menu-handler"><i class="fa fa-align-justify"></i></a>
                            <div class="mobile-menu-content slidemenu-push">
                                <?php wp_nav_menu(array('theme_location' => 'mobile-menu', 
                                                        'fallback_cb' => '',
                                                        'menu_class'=>'list-inline',
                                                        'container_id'=>'mobile-menu',
                                                        'container'=>'nav' )); ?>
                            </div>
                        </div>
                        <!-- MOBILE MENU END -->
    
    
                        <!-- WOOCOMMERCE MOBILE CART START -->
                        <?php
                        if( class_exists( 'woocommerce' ) ):
                            ob_start();
                            woocommerce_mini_cart();
                            $mini_cart = ob_get_clean();
                        ?>
                        <div id="mobile-cart-wrapper" class="slidemenu-push">
                            <div class="mobile-cart-content">
                                <?php echo $mini_cart; ?>
                            </div>
                            <div class="mobile-cart-tmp">
                                <nav id="mobile-cart" class="woocommerce"></nav>
                            </div>
                        </div>
                        <?php endif; ?>
                        <!-- WOOCOMMERCE MOBILE CART END -->
                    </div>

                </div>
            </div>
        </header>
        <!-- ==================================================
        End Header -->
        <?php endif; ?>





        <?php
        while ( have_posts() ) : the_post();
            include file_require(get_template_directory().'/template-page.php');
        endwhile;
        ?>






<?php
global $smof_data; 
$up_footer = tt_getmeta('up_footer', $post->ID);
$up_footer_bar = tt_getmeta('up_footer_bar', $post->ID);

if ( $up_footer == '1') {
    $layout = isset($smof_data['footer_layout']) ? $smof_data['footer_layout'] : 3;
    switch ($layout) {
        case 1:
            $col = 1;
            $percent = array('col-xs-12 col-sm-12 col-md-12 col-lg-12');
            break;
        case 2:
            $col = 2;
            $percent = array(
                'col-xs-12 col-sm-6 col-md-6 col-lg-6',
                'col-xs-12 col-sm-6 col-md-6 col-lg-6');
            break;
        case 3:
            $col = 3;
            $percent = array(
                'col-xs-12 col-sm-12 col-md-6 col-lg-6',
                'col-xs-12 col-sm-6 col-md-3 col-lg-3',
                'col-xs-12 col-sm-6 col-md-3 col-lg-3');
            break;
        case 4:
            $col = 3;
            $percent = array(
                'col-xs-12 col-sm-6 col-md-3 col-lg-3',
                'col-xs-12 col-sm-6 col-md-3 col-lg-3',
                'col-xs-12 col-sm-12 col-md-6 col-lg-6 pull-right');
            break;
        case 5:
            $col = 3;
            $percent = array(
                'col-md-4 col-sm-4',
                'col-md-4 col-sm-4',
                'col-md-4 col-sm-4');
            break;
        case 6:
            $col = 4;
            $percent = array(
                'col-md-3 col-sm-6',
                'col-md-3 col-sm-6',
                'col-md-3 col-sm-6',
                'col-md-3 col-sm-6');
            break;
        default:
            $col = 4;
            $percent = array(
                'col-md-3 col-sm-6',
                'col-md-3 col-sm-6',
                'col-md-3 col-sm-6',
                'col-md-3 col-sm-6');
            break;
    }

?>
    <!-- Start Footer
    ================================================== -->
    <footer id="footer" class="section">
    
        <div class="container">
            <div class="row">
            
                <?php 
                for ($i = 1; $i <= $col; $i++) {
                    echo "<div class='footer_widget_container ".$percent[$i - 1]."'>";
                    dynamic_sidebar('sidebar_metro_footer' . $i);
                    echo '</div>';
                } ?>

            </div>
        </div>
    
    </footer>
    <!-- ================================================== 
    End Footer -->
<?php } ?>

<?php if( $up_footer_bar=='1' ){ ?>
    <!-- Start Sub-Footer
    ================================================== -->
    <div class="sub-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="top-bar-left">
                       <?php
                       if( isset($smof_data['sub_footer_left']) ){
                        tt_bar_content($smof_data['sub_footer_left'], true);
                       }
                       ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="top-bar-right text-right">
                       <?php
                       if( isset($smof_data['sub_footer_right']) ){
                        tt_bar_content($smof_data['sub_footer_right'], true);
                       }
                       ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ================================================== 
    End Sub-Footer -->

<?php } ?>
    <?php tt_trackingcode(); ?>

    </div><!-- end wrapper -->

    <span class="gototop">
        <i class="fa fa-angle-up"></i>
    </span>

    <!--[if lt IE 9]>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/plugins/respond.min.js"></script>
    <![endif]-->

    <?php wp_footer(); ?>

</body>
</html>