<?php

add_action('init', 'of_options');

if (!function_exists('of_options')) {

    function of_options() {
        //Access the WordPress Categories via an Array
        $of_categories = $of_categories_pure = array();
        $of_categories_obj = get_categories('hide_empty=0');
        foreach ($of_categories_obj as $of_cat) {
            $of_categories[$of_cat->cat_ID] = $of_cat->cat_name;
        }
        $of_categories_pure = $of_categories;
        $categories_tmp = array_unshift($of_categories, "Select a category:");

        //Access the WordPress Pages via an Array
        $of_pages = array();
        $of_pages_obj = get_pages('sort_column=post_parent,menu_order');
        foreach ($of_pages_obj as $of_page) {
            $of_pages[$of_page->ID] = $of_page->post_name;
        }
        $of_pages_tmp = array_unshift($of_pages, "Select a page:");

        //Stylesheets Reader
        $alt_stylesheet_path = LAYOUT_PATH;
        $alt_stylesheets = array();

        if (is_dir($alt_stylesheet_path)) {
            if ($alt_stylesheet_dir = opendir($alt_stylesheet_path)) {
                while (($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false) {
                    if (stristr($alt_stylesheet_file, ".css") !== false) {
                        $alt_stylesheets[] = $alt_stylesheet_file;
                    }
                }
            }
        }


        //Background Images Reader
        $bg_images_path = get_stylesheet_directory() . '/images/bg/'; // change this to where you store your bg images
        $bg_images_url = get_template_directory_uri() . '/images/bg/'; // change this to where you store your bg images
        $bg_images = array();

        if (is_dir($bg_images_path)) {
            if ($bg_images_dir = opendir($bg_images_path)) {
                while (($bg_images_file = readdir($bg_images_dir)) !== false) {
                    if (stristr($bg_images_file, ".png") !== false || stristr($bg_images_file, ".jpg") !== false) {
                        $bg_images[] = $bg_images_url . $bg_images_file;
                    }
                }
            }
        }

        include_once file_require(ADMIN_PATH . 'functions/google-fonts.php');
        $google_fonts = get_google_webfonts();

        $google_webfonts["default"] = "Default (Helvetica, Arial, sans-serif)";
        foreach ($google_fonts as $font) {
            $google_webfonts[$font['family']] = $font['family'];
        }
		
        /* ----------------------------------------------------------------------------------- */
        /* TO DO: Add options/functions that use these */
        /* ----------------------------------------------------------------------------------- */

        //More Options
        $uploads_arr = wp_upload_dir();
        $all_uploads_path = $uploads_arr['path'];
        $all_uploads = get_option('of_uploads');
        $other_entries = array("Select a number:", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19");
        $body_repeat = array("no-repeat", "repeat-x", "repeat-y", "repeat");
        $body_pos = array("top left", "top center", "top right", "center left", "center center", "center right", "bottom left", "bottom center", "bottom right");

        // Image Alignment radio box
        $of_options_thumb_align = array("alignleft" => "Left", "alignright" => "Right", "aligncenter" => "Center");

        // Image Links to Options
        $of_options_image_link_to = array("image" => "The Image", "post" => "The Post");

        /* ----------------------------------------------------------------------------------- */
        /* The Options Array */
        /* ----------------------------------------------------------------------------------- */

        global $of_options,$tt_social_icons,$tt_sidebars;
        $of_options = array();
        $url = ADMIN_IMAGES;



        /* General settings
         ***********************************************************************/
        $of_options[] = array("name" => "General Settings",
            "type" => "heading"
        );
        $of_options[] = array("name" => "CSS Tips!",
            "std" => "<div style='font-size:14px;'>Please visit the <a target='_blank' href='" . admin_url() . "customize.php'>Site Customize</a> page and change your site's <strong>Logo</strong>, <strong>Color scheme</strong> and <strong>Font options</strong> . And if you don't want to use those changes any more, you should reset them with following options and turn theme back to default.</div>",
            "icon" => true,
            "type" => "info"
        );
        $of_options[] = array("name" => "Tracking Code",
            "desc" => "Add your <a href='http://analytics.google.com' target='_blank'>Google Analytics</a> or other tracking code here. This will be added into the footer of your site.",
            "id" => "site_analytics",
            "std" => "",
            "type" => "textarea"
        );
        $of_options[] = array("name" => "Remove Heart Likes",
            "desc" => "If you don't need any heart/like section on post and widget section, please turn this option ON.",
            "id" => "remove_heart",
            "std" => 0,
            "type" => "switch"
        );

		/* Page builder settings 
         ***********************************************************************/
        $of_options[] = array("name" => "Page Builder",
            "type" => "info",
            "std" => "<h3>Page Builder</h3>"
        );
        $of_options[] = array("name" => "",
            "desc" => "For Posts",
            "id" => "pb_posts",
            "std" => 1,
            "type" => "switch"
        );        
        $of_options[] = array("name" => "",
            "desc" => "For Pages",
            "id" => "pb_pages",
            "std" => 1,
            "type" => "switch"
        );        
        $of_options[] = array("name" => "",
            "desc" => "For Portfolio",
            "id" => "pb_port",
            "std" => 1,
            "type" => "switch"
        );
        $of_options[] = array("name" => "",
            "desc" => "For Directory",
            "id" => "pb_dir",
            "std" => 1,
            "type" => "switch"
        );
        /*
        $of_options[] = array("name" => "For all other post types",
            "desc" => "Controls page builder range on your custom post types.",
            "id" => "pb_other",
            "std" => 0,
            "type" => "switch"
        );
        */

        
        /* Top Bar settings 
         ***********************************************************************/
        $of_options[] = array("name" => "Top Bar",
            "type" => "heading"
        );

        $topBarOptions = array(
            'none' => 'Select Item',
            'text1' => 'Text 1',
            'text2' => 'Text 2',
            'shop' => 'Shop cart',
            'lang' => 'Language widget of WPML',
            'social' => 'Social icons',
            'login' => 'Login link',
            'menu' => 'Custom menu',
        );

        $of_options[] = array("name" => "Enable Top Bar",
            "desc" => "",
            "id" => "top_bar",
            "std" => 1,
            "folds" => 1,
            "type" => "switch"
        );

        
        $of_options[] = array("name" => "Top Bar (Left)",
            "desc" => "Please add elements here and order them with mouse drag & drop.",
            "id" => "top_bar_left_select",
            "std" => '',
            "fold" => "top_bar",
            "type" => "select",
            "multiple" => false,
            "options" => $topBarOptions
        );
        $of_options[] = array("name" => "Top Bar (Left)",
            "desc" => "",
            "id" => "top_bar_left",
            "std" => "text1",
            "type" => "text"
        );


        $of_options[] = array("name" => "Top Bar (Right)",
            "desc" => "Please add elements here and order them with mouse drag & drop.",
            "id" => "top_bar_right_select",
            "std" => '',
            "fold" => "top_bar",
            "type" => "select",
            "multiple" => false,
            "options" => $topBarOptions
        );
        $of_options[] = array("name" => "Top Bar (Right)",
            "desc" => "",
            "id" => "top_bar_right",
            "std" => "login,social,shop",
            "type" => "text"
        );


        $of_options[] = array("name" => "Text content 1",
            "desc" => "Please add text content for your top bar.",
            "id" => "top_bar_text1",
            "std" => 'Hey there! Welcome to the new site :)',
            "fold" => "top_bar",
            "type" => "textarea"
        );
        $of_options[] = array("name" => "Text content 2",
            "desc" => "Please add text content for your top bar.",
            "id" => "top_bar_text2",
            "std" => 'text',
            "fold" => "top_bar",
            "type" => "textarea"
        );
        $of_options[] = array("name" => "Social Profiles on Top Bar",
            "id" => "social_addresses",
            "fold" => "top_bar",
            "std" => "<h3>Social Profiles on Top Bar</h3>",
            "type" => "info"
        );
        foreach ($tt_social_icons as $id => $value) {
            $of_options[] = array("name" => "",
                "desc" => ucfirst($id) . " address",
                "id" => "social_" . $id,
                "fold" => "top_bar",
                "std" => "",
                "type" => "text"
            );
        }
        $of_options[] = array("name" => "social_tip",
            "std" => "<h3 style='margin: 0 0 10px'>Add Custom Social Profile</h3>
                Here I've added some popular socials. If your familiar social site doesn't have here, you can extend those as following.
                <p> Please open up the <em>framework/common-functions.php</em> file from theme directory and find <strong>tt_social_icons</strong> array which locates at the top of the file.
                This is a very popular array that saves social list for above options and widget socials too. So now we need to extend it with our new socials same as their structure (as Name and Icon code). You should find a proper icon for your new social from <a href='http://fortawesome.github.io/Font-Awesome/cheatsheet/' target='_blank'>FontAwesome library</a>.
                </p>
                <p>Also you can <strong>reorder</strong> those there.</p>",
            "icon" => true,
            "type" => "info"
        );



        
        /* Post options
         ***********************************************************************/
        $of_options[] = array("name" => "Post And Page",
            "type" => "heading"
        );
        $of_options[] = array(
            "name" => "Post Options",
            "std" => "<h3>Post Options</h3>",
            "type" => "info"
        );
        $of_options[] = array("name" => "Post Format in Single",
            "desc" => "If you turned this option OFF, post format content won't show on single post pages.",
            "id" => "show_post_format",
            "std" => 1,
            "type" => "switch"
        );
        $of_options[] = array("name" => "Related Posts",
            "desc" => "Related posts on bottom of post content in single post page.",
            "id" => "related_posts",
            "std" => 1,
            "type" => "switch"
        );
        $of_options[] = array("name" => "Author box on Posts",
            "desc" => "",
            "id" => "post_author",
            "std" => 1,
            "type" => "switch"
        );
        $of_options[] = array("name" => "Comment on Posts",
            "desc" => "WordPress has its own way to disable comments in a Post. But you should turn this option OFF if you want to remove comments for all your posts.",
            "id" => "post_comment",
            "std" => 1,
            "type" => "switch"
        );

        /* Page settings
         ***********************************************************************/
        $of_options[] = array(
            "name" => "Page Options",
            "std" => "<h3>Page Options</h3>",
            "type" => "info"
        );



        $of_options[] = array("name" => "Author box on Pages",
            "desc" => "",
            "id" => "page_author",
            "std" => 0,
            "type" => "switch"
        );

        $of_options[] = array("name" => "Comment on Pages",
            "desc" => "WordPress has its own way to disable comments in a Page. But you should turn this option OFF if you want to remove comments for all your pages.",
            "id" => "page_comment",
            "std" => 1,
            "type" => "switch"
        );


        /* Portfolio
         ***********************************************************************/

        $of_options[] = array("name" => "Portfolio Options",
            "type" => "heading"
        );
        $of_options[] = array("name" => "Portfolio Slug",
            "type" => "text",
            "id" => "portfolio_slug",
            "desc" => "Portfolio slug that should be show at url for portfolio single items.",
            "std" => "portfolio-item"
        );
        $of_options[] = array("name" => "Portfolio Columns",
            "desc" => "Item column on Taxonomy / Category page. You can select 2, 3 and 4 columns layout.",
            "id" => "portfolio_layout",
            "std" => "grid3",
            "type" => "images",
            "options" => array(
                'grid2' => $url . 'blog-grid2.png',
                'grid3' => $url . 'blog-grid3.png',
                'grid4' => $url . 'blog-grid4.png'
            )
        );

        $sidebar_layouts = array(
                'right' => $url . '2cr.png',
                'left' => $url . '2cl.png',
                'full' => $url . '1col.png'
            );
        $of_options[] = array("name" => "Portolio Sidebar Type",
            "desc" => "",
            "id" => "portfolio_sidebar_type",
            "std" => "full",
            "type" => "images",
            "options" => $sidebar_layouts
        );
        $of_options[] = array("name" => "Portfolio Sidebar",
            "desc" => "",
            "id" => "portfolio_sidebar",
            "std" => "portfolio-sidebar",
            "type" => "select",
            "options" => $tt_sidebars
        );
        $of_options[] = array("name" => "Single Layout",
            "desc" => "",
            "id" => "portfolio_single_layout",
            "std" => "full",
            "type" => "images",
            "options" => $sidebar_layouts
        );
        $of_options[] = array("name" => "Single Sidebar",
            "desc" => "",
            "id" => "portfolio_single_sidebar",
            "std" => "portfolio-sidebar",
            "type" => "select",
            "options" => $tt_sidebars
        );

        $of_options[] = array("name" => "Comment on Portfolio item page",
            "desc" => "WordPress has its own way to disable comments in a Page. But you should turn this option OFF if you want to remove comments for all your pages.",
            "id" => "port_comment",
            "std" => 0,
            "type" => "switch"
        );
        $of_options[] = array("name" => "Portfolio Next Prev Links at Top",
            "desc" => "You can turn off <a href='http://d.pr/i/SK3g' target='_blank'>Next Preview and Main page links</a> at right bottom on single portfolio page.",
            "id" => "port_next_prev_links",
            "std" => 1,
            "type" => "switch"
        );
        $of_options[] = array("name" => "Main Portfolio Page",
            "desc" => "A page which shows when click on View all button from single portfolio item.",
            "id" => "portfolio_page",
            "std" => "",
            "type" => "select",
            "options" => $of_pages
        );
        $of_options[] = array("name" => "Portfolio Related Posts at Bottom",
            "desc" => "Portfolio Related Posts at bottom of your portfolio item page.",
            "id" => "port_related",
            "std" => 1,
            "type" => "switch"
        );
        
        /* Archive and Category layout
         ***********************************************************************/

        $of_options[] = array("name" => "Archive Category Tags",
            "type" => "heading"
        );
        $of_options[] = array("name" => "",
            "std" => "<h3>Category Layout</h3>",
            "type" => "info",
        );
        $loop_layouts = array(
                'regular' => $url . 'blog-regular.png',
                'grid2' => $url . 'blog-grid2.png',
                'grid3' => $url . 'blog-grid3.png',
                'grid4' => $url . 'blog-grid4.png',
                'masonry2' => $url . 'blog-masonry2.png',
                'masonry3' => $url . 'blog-masonry3.png',
                'masonry4' => $url . 'blog-masonry4.png'
            );
        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "category_layout",
            "std" => "regular",
            "type" => "images",
            "options" => $loop_layouts
        );
        $of_options[] = array("name" => "Category Sidebar Type",
            "desc" => "",
            "id" => "category_sidebar_type",
            "std" => "right",
            "type" => "images",
            "options" => $sidebar_layouts
        );
        $of_options[] = array("name" => "Category Sidebar",
            "desc" => "",
            "id" => "category_sidebar",
            "std" => "blog-sidebar",
            "type" => "select",
            "options" => $tt_sidebars
        );
        $of_options[] = array("name" => "",
            "std" => "<h3>Archive Layout</h3>",
            "type" => "info",
        );
        $of_options[] = array("name" => "Archive Layout",
            "desc" => "",
            "id" => "archive_layout",
            "std" => "regular",
            "type" => "images",
            "options" => $loop_layouts
        );
        $of_options[] = array("name" => "Archive Sidebar Type",
            "desc" => "",
            "id" => "archive_sidebar_type",
            "std" => "right",
            "type" => "images",
            "options" => $sidebar_layouts
        );
        $of_options[] = array("name" => "Archive Sidebar",
            "desc" => "",
            "id" => "archive_sidebar",
            "std" => "blog-sidebar",
            "type" => "select",
            "options" => $tt_sidebars
        );
        $of_options[] = array("name" => "",
            "std" => "<h3>Tag Layout</h3>",
            "type" => "info",
        );
        $of_options[] = array("name" => "Tag Layout",
            "desc" => "",
            "id" => "tag_layout",
            "std" => "regular",
            "type" => "images",
            "options" => $loop_layouts
        );
        $of_options[] = array("name" => "Tag Sidebar Type",
            "desc" => "",
            "id" => "tag_sidebar_type",
            "std" => "right",
            "type" => "images",
            "options" => $sidebar_layouts
        );
        $of_options[] = array("name" => "Tag Sidebar",
            "desc" => "",
            "id" => "tag_sidebar",
            "std" => "blog-sidebar",
            "type" => "select",
            "options" => $tt_sidebars
        );
        $of_options[] = array("name" => "",
            "std" => "<h3>Search Result Layout</h3>",
            "type" => "info",
        );
        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "search_layout",
            "std" => "regular",
            "type" => "images",
            "options" => $loop_layouts
        );
        $of_options[] = array("name" => "Search Sidebar Type",
            "desc" => "",
            "id" => "search_sidebar_type",
            "std" => "right",
            "type" => "images",
            "options" => $sidebar_layouts
        );
        $of_options[] = array("name" => "Search Sidebar",
            "desc" => "",
            "id" => "search_sidebar",
            "std" => "blog-sidebar",
            "type" => "select",
            "options" => $tt_sidebars
        );
        $of_options[] = array("name" => "",
            "std" => "<h3>Author Page Layout</h3>",
            "type" => "info",
        );
        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "author_layout",
            "std" => "regular",
            "type" => "images",
            "options" => $loop_layouts
        );
        $of_options[] = array("name" => "Sidebar Type on Author Page",
            "desc" => "",
            "id" => "author_sidebar_type",
            "std" => "right",
            "type" => "images",
            "options" => $sidebar_layouts
        );
        $of_options[] = array("name" => "Author Sidebar",
            "desc" => "",
            "id" => "author_sidebar",
            "std" => "blog-sidebar",
            "type" => "select",
            "options" => $tt_sidebars
        );


        /* WooCommerce Options
         ***********************************************************************/
        $of_options[] = array("name" => "WooCommerce",
            "type" => "heading"
        );
        $of_options[] = array("name" => "WooCommerce Page Layout",
            "desc" => "",
            "id" => "woo_layout",
            "std" => "full",
            "type" => "images",
            "options" => $sidebar_layouts
        );
        $of_options[] = array("name" => "WooCommerce Sidebar",
            "desc" => "",
            "id" => "woo_sidebar",
            "std" => "woocommerce-sidebar",
            "type" => "select",
            "options" => $tt_sidebars
        );
        $of_options[] = array("name" => "Product Single Layout",
            "desc" => "",
            "id" => "product_layout",
            "std" => "right",
            "type" => "images",
            "options" => $sidebar_layouts
        );
        $of_options[] = array("name" => "Product Single Sidebar",
            "desc" => "",
            "id" => "product_sidebar",
            "std" => "woocommerce-sidebar",
            "type" => "select",
            "options" => $tt_sidebars
        );

        $of_options[] = array("name" => "Product Overlay Transition",
            "desc" => "Choose whether you would like the product overlay transition to be enabled.",
            "id" => "woo_overlay",
            "std" => 1,
            "type" => "switch"
        );



        /* Social Options
         ***********************************************************************/

        $of_options[] = array("name" => "Share and Socials",
            "type" => "heading"
        );
        $share_buttons = array("facebook" => "Facebook", "twitter" => "Twitter", "googleplus" => "Google+", "pinterest" => "Pinterest", 'email' => 'Email');
        $of_options[] = array("name" => "Share Buttons",
            "desc" => "",
            "id" => "share_buttons",
            "std" => array('facebook', 'twitter', 'googleplus', 'pinterest', 'email'),
            "type" => "multicheck",
            "options" => $share_buttons
        );
        $share_visibility = array("share_posts" => "On Posts", "share_pages" => "On Pages", "share_port" => "On Portfolio entries"); 
        $of_options[] = array("name" => "Visibility of Share Buttons",
            "desc" => "",
            "id" => "share_visibility",
            "std" => 1,
            "type" => "multicheck",
            "options" => $share_visibility
        );


        /* Sidebar Options
         ***********************************************************************/

        $of_options[] = array("name" => "Custom Sidebar",
            "type" => "heading"
        );
        $of_options[] = array("name" => "Custom sidebar",
            "desc" => "You can create unlimited siderbars for your site. You should add widgets on <strong>Appearance=><a href='widgets.php'>Widgets</a></strong> after you have added new sidebar here.",
            "id" => "custom_sidebar",
            "type" => "sidebar",
            "std" => ""
        );

        
        
        
        /* Footer Options
         ***********************************************************************/

        $of_options[] = array("name" => "Footer Options",
            "type" => "heading"
        );
        $of_options[] = array("name" => "Enable Footer",
            "desc" => "",
            "id" => "footer",
            "std" => 1,
            "type" => "switch"
        );
        $of_options[] = array("name" => "Footer Layout",
            "desc" => "Those are general footer layouts. If you need more creative footer area there, you should select Footer layout #1 and add your layout content there. It is possible to add page layout shortcode in here with text widget.",
            "id" => "footer_layout",
            "std" => "6",
            "type" => "images",
            "options" => array(
                '1' => $url . 'footer1.png',
                '2' => $url . 'footer2.png',
                '3' => $url . 'footer3.png',
                '4' => $url . 'footer4.png',
                '5' => $url . 'footer5.png',
                '6' => $url . 'footer6.png',
            )
        );
        $of_options[] = array("name" => "Footer Background Image",
            "desc" => "Select a background image or pattern.",
            "id" => "footer_bg_image",
            "std" => "",
            "type" => "media",
        );
        $of_options[] = array("name" => "",
            "desc" => "Repeat",
            "id" => "footer_bg_repeat",
            "std" => "",
            "type" => "select",
            "options" => $body_repeat
        );
        $of_options[] = array("name" => "",
            "desc" => "Position",
            "id" => "footer_bg_position",
            "std" => "",
            "type" => "select",
            "options" => $body_pos
        );
        $of_options[] = array("name" => "",
            "desc" => "Fixed or Scroll",
            "id" => "footer_bg_fixed",
            "std" => "scroll",
            "type" => "select",
            "options" => array('scroll', 'fixed')
        );



        /* Footer Bar options
         ***********************************************************************/

        $of_options[] = array("name" => "Footer Bar",
            "type" => "heading"
        );
        $of_options[] = array("name" => "Footer Bar",
            "desc" => "",
            "id" => "sub_footer",
            "std" => 1,
            "folds" => 1,
            "type" => "switch"
        );
        $footerBarOptions = array(
            'none' => 'Select item',
            'text1' => 'Text 1',
            'text2' => 'Text 2',
            'lang' => 'Language widget',
            'social' => 'Social icons',
            'menu' => 'Custom menu',
        );
        $of_options[] = array("name" => "Footer Bar (Left)",
            "desc" => "Please add elements here and order them with mouse drag & drop.",
            "id" => "sub_footer_left_select",
            "std" => 'text1',
            "fold" => "sub_footer",
            "type" => "select",
            "options" => $footerBarOptions
        );
        $of_options[] = array("name" => "Footer Bar (Left)",
            "desc" => "",
            "id" => "sub_footer_left",
            "std" => "text1",
            "type" => "text"
        );
        $of_options[] = array("name" => "Footer Bar (Right)",
            "desc" => "Please add elements here and order them with mouse drag & drop.",
            "id" => "sub_footer_right_select",
            "std" => '',
            "fold" => "sub_footer",
            "type" => "select",
            "options" => $footerBarOptions
        );
        $of_options[] = array("name" => "Footer Bar (Right)",
            "desc" => "",
            "id" => "sub_footer_right",
            "std" => "text2",
            "type" => "text"
        );
        $of_options[] = array("name" => "Footer Text 1",
            "desc" => "",
            "id" => "footer_bar_text1",
            "fold" => "sub_footer",
            "std" => "Powered by WordPress. Developed by <a href='http://themeton.com'>ThemeTon</a>.",
            "type" => "textarea"
        );
        $of_options[] = array("name" => "Footer Text 2",
            "desc" => "",
            "id" => "footer_bar_text2",
            "fold" => "sub_footer",
            "std" => "Copyright &copy; 2014.",
            "type" => "textarea"
        );
        $of_options[] = array("name" => "",
            "std" => "<h3>Social Profiles on Sub Footer Bar</h3>",
            "type" => "info",
        );
        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "social_addresses_f",
            "fold" => "sub_footer",
            "std" => "",
            "type" => ""
        );
        foreach ($tt_social_icons as $id => $value) {
            $of_options[] = array("name" => "",
                "desc" => ucfirst($id) . " address",
                "id" => "footer_social_" . $id,
                "fold" => "sub_footer",
                "std" => "",
                "type" => "text"
            );
        }




        /* CUSTOM CSS
         ***********************************************************************/
        $of_options[] = array("name" => "LESS",
            "type" => "heading"
        );

        
        /* Restore theme options */
        $less_theme_variables_opt = get_theme_mod("less_theme_variables");
        $tmp_arr = array();
        if( !empty($less_theme_variables_opt) ){
            $tmp = unserialize(base64_decode($less_theme_variables_opt));
            if( is_array($tmp) ){
                $tmp_arr = $tmp;
            }
        }
        if( count($tmp_arr)<1 && get_theme_mod('less-brand-primary')!="" ){
            $lessvars = get_less_variables();
            $restored_vars = array();
            foreach ($lessvars as $opt){
                if( $opt['variable']!='less-heading' ){
                    $vl = get_theme_mod('less-'.$opt['variable']);
                    remove_theme_mod('less-'.$opt['variable']);
                    $val = $opt['value'];
                    if( !empty($vl) && $vl!=$opt['value'] ){
                        $val = $vl;
                    }
                    $restored_vars = array_merge($restored_vars, array($opt['variable']=>$val));
                }
            }
            // Save LESS Variables
            if( !empty($restored_vars) ){
                $encoded_str = base64_encode(serialize($restored_vars));
                set_theme_mod( "less_theme_variables", $encoded_str );
            }
        }
        elseif( count($tmp_arr)<1 ){
            $less_vars = array();
            $less_options = get_less_variables();
            foreach ($less_options as $opt){
                if( $opt['variable']!='less-heading' && !empty($opt['variable']) && !empty($opt['value']) ){
                    $less_vars = array_merge($less_vars, array($opt['variable']=>$opt['value']));
                }
                $encoded_str = base64_encode(serialize($less_vars));
                set_theme_mod( "less_theme_variables", $encoded_str );
            }
        }

        /* Reformating Theme Option Less Editor */
        $less_content_data = get_less_editor_content( $less_theme_variables_opt );
        if( isset($smof_data['less_editor']) ){
            $smof_data['less_editor'] = $less_content_data;
        }
        set_theme_mod('less_editor', $less_content_data);
        $of_options[] = array("name" => "LESS Editor",
            "desc" => "LESS Code Editor",
            "id" => "less_editor",
            "std" => $less_content_data,
            "type" => "textarea"
        );




        /* CUSTOM CSS
         ***********************************************************************/

        $of_options[] = array("name" => "Custom CSS",
            "type" => "heading"
        );
        $of_options[] = array("name" => "CSS Tips!",
            "std" => "<h3 style=\"margin: 0 0 10px;\">Tips, Before customizing</h3>
                Control everything is impossible with options. Therefore we have to use custom styling for our sites if we have deep changes. 
                You can make your changes in css files but it doesn't safe and probably you lose those for next version updates of the theme. Then I suggest you to use following options. It is a safe place :)

                <p>If you don't have enough experience with CSS and selectors, you should visit following links
                <ul style='margin-left:20px'>
                    <li> - Learn about <a href='http://www.w3schools.com/cssref/css_selectors.asp' target='_blank'>CSS selectors</a></li>
                    <li> - Learn about <a href='http://net.tutsplus.com/tutorials/html-css-techniques/the-30-css-selectors-you-must-memorize/' target='_blank'>CSS selectors</a> by Jeffrey Way</li>
                    <li> - How to use <a href='http://www.youtube.com/watch?v=nOEw9iiopwI' target='_blank'>Chrome Inspector</a></li>
                    <li> - How to use <a href='http://www.youtube.com/watch?v=3KdNRZS-uSg' target='_blank'>Mozilla Firebug</a> for editing css</li>
                </ul>
                </p>",
            "icon" => true,
            "type" => "info"
        );
        $of_options[] = array("name" => "Custom CSS (General)",
            "desc" => "",
            "id" => "custom_css",
            "std" => "",
            "type" => "textarea"
        );
        $of_options[] = array("name" => "For Tablets",
            "desc" => "Screen width between 768px and 985px",
            "id" => "tablet_css",
            "std" => "",
            "type" => "textarea"
        );
        $of_options[] = array("name" => "For Wide Phones",
            "desc" => "Screen width between 480px and 767px",
            "id" => "wide_phone_css",
            "std" => "",
            "type" => "textarea"
        );
        $of_options[] = array("name" => "For Phone",
            "desc" => "Screen width up to 479px",
            "id" => "phone_css",
            "std" => "",
            "type" => "textarea"
        );



        /* Backup and Restore
         ***********************************************************************/

        $of_options[] = array("name" => "Backup Options",
            "type" => "heading"
        );
        $of_options[] = array("name" => "Backup and Restore Options",
            "id" => "of_backup",
            "std" => "",
            "type" => "backup",
            "desc" => 'You can use the two buttons below to backup your current options, and then restore it back at a later time. This is useful if you want to experiment on the options but would like to keep the old settings in case you need it back.',
        );

        $of_options[] = array("name" => "Transfer Theme Options Data",
            "id" => "of_transfer",
            "std" => "",
            "type" => "transfer",
            "desc" => 'You can tranfer the saved options data between different installs by copying the text inside the text box. To import data from another install, replace the data in the text box with the one from another install and click "Import Options".',
        );


    }

//End function: of_options()
}//End chack if function exists: of_options()
?>
