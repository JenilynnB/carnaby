<?php

function get_widgets_on_menu($sidebar_id){
    $html = '';
    ob_start();
    dynamic_sidebar($sidebar_id);
    $html = ob_get_contents();
    ob_end_clean();
    
    return '';
    return "<div class='menu_widget'>$html</div>";
}


function get_current_menu_class($menies, $menu_item){
    
    global $wp_rewrite;
    $_root_relative_current = untrailingslashit( $_SERVER['REQUEST_URI'] );
    $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_root_relative_current );
    $raw_item_url = strpos( $menu_item->url, '#' ) ? substr( $menu_item->url, 0, strpos( $menu_item->url, '#' ) ) : $menu_item->url;
    $item_url = untrailingslashit( $raw_item_url );
    $_indexless_current = untrailingslashit( preg_replace( '/' . preg_quote( $wp_rewrite->index, '/' ) . '$/', '', $current_url ) );
    $front_page_url = home_url();
/*
    $term = null;
    $parent = null;
    //Determining if there is an active directory category in the query
    $query_tax = get_query_var("tax_query");
    //there is only one term inside the array passed in common-functions.php, if more are passed this should be expanded
    $query_tax = $query_tax[0];    
    $main_query = get_queried_object();
    if($query_tax){
        $term = get_term_by($query_tax["field"], $query_tax["terms"], WPBDP_CATEGORY_TAX);
        $parent = get_term($term->parent, WPBDP_CATEGORY_TAX);
    }else if($main_query){
        //$term = get_term_by($query_tax["field"], $query_tax["terms"], WPBDP_CATEGORY_TAX);
        $term = $main_query;
        $parent = get_term($main_query->parent, WPBDP_CATEGORY_TAX);
    }
  */
    $query_tax = get_query_var("tax_query");
    $main_query = get_queried_object();
    if(!empty($query_tax)){
        foreach($query_tax as $qt){
            if(isset($qt['taxonomy'])){
                if($qt['taxonomy']==WPBDP_CATEGORY_TAX){
                    $queryterms = array();
                    if(!is_array($qt['terms'])){
                        $queryterms[] = $qt['terms'];
                    }else{
                        $queryterms = $qt['terms'];
                    }
                    foreach($queryterms as $qtt){
                        if(strcasecmp($qtt,'women')==0||
                        strcasecmp($qtt,'men')==0||
                        strcasecmp($qtt,'kids-baby')==0||
                        strcasecmp($qtt,'girls')==0||
                        strcasecmp($qtt,'boys')==0||
                        strcasecmp($qtt,'baby')==0){
                            $field = $qt['field'];
                            $term = get_term_by($field, $qtt, WPBDP_CATEGORY_TAX);
                        }
                    }
                }
            }
        }
    }else if($main_query){
        $term = $main_query; 
    } 
    
    if($term->parent!=0){
            $parent_term = get_term($term->parent, WPBDP_CATEGORY_TAX);
    }else{
        $parent_term = $term;
    }
    
    $classes = implode(" ",$menu_item->classes).' menu-item menu-item-type-'.$menu_item->type.' menu-item-object-'.$menu_item->object;

    if ( $raw_item_url && in_array( $item_url, array( $current_url, $_indexless_current, $_root_relative_current ) ) ) {
        $classes .= ' current-menu-item';
    } elseif ( $item_url == $front_page_url && is_front_page() ) {
        $classes .= ' current-menu-item';
    }elseif (strcasecmp($parent_term->name, $menu_item->title)==0){
        $classes .= ' current-menu-item';
    }

    if ( untrailingslashit($item_url) == home_url() )
        $classes .= ' menu-item-home';

    $classes .= ' menu-item-'.$menu_item->ID;

    return $classes;
}

function get_custom_atts_menu($meny){
    $atts = '';

    $title = $meny->attr_title!='' ? 'title="'.$meny->attr_title.'"' : '';
    $target = $meny->target!='' ? 'target="'.$meny->target.'"' : '';

    $atts = ($title!='' ? $title.' ' : '').($target!='' ? $target : '');

    return $atts;
}

function get_icon_menu($meny){
    if (isset($meny->icon) && filter_var($meny->icon, FILTER_VALIDATE_URL)) {
        return "<img class='menu-icon nav-custom-icon' src='".$meny->icon."' alt='Menu icon'/>";
    } 
    return isset($meny->icon) && $meny->icon!='' ? '<span class="menu-icon '.$meny->icon.'"></span>' : '';
}

function get_menu_text($meny){
	return '<span class="menu-text">'.$meny->title.'</span>';
}

function get_description_menu($meny){
    return $meny->description!='' ? '<span class="menu-description">'.$meny->description.'</span>' : '';
}

function get_menu_id($meny){
    return 'menu-item-'.$meny->ID;
}

function render_submenu_items($menies, $parent){
    $html = '';
    foreach( $menies as $meny ){
        if( $meny->menu_item_parent == $parent ){
            $classes = '';
            foreach($meny->classes as $class){ $classes .= ($class!='' ? $class.' ' : ''); }
            $classes = get_current_menu_class($menies, $meny);
            
            $html .= '<li id="'. get_menu_id($meny) .'" class="'.$classes.'">
                        <a href="'.$meny->url.'" '.get_custom_atts_menu($meny).'>
                        	'.get_icon_menu($meny).'
                            '.get_menu_text($meny).'
                            '.get_description_menu($meny).'
                        </a>
                        '.render_submenu_items($menies, $meny->ID).'
                      </li>';
        }
    }
    return $html!='' ? ('<ul class="dropdown-menu">'.$html.'</ul>') : '';
}

function render_megamenu_items($menies, $parent){
    $html = '';
    foreach($menies as $meny){
        if( $meny->menu_item_parent == $parent ){
            
            $classes = $col_style = '';
            foreach($meny->classes as $class){ $classes .= ($class!='' ? $class.' ' : ''); }
            $menu_classes = get_current_menu_class($menies, $meny);

            $html .= '<div id="'. get_menu_id($meny) .'" class="menu-column '.$menu_classes.'">
                        <h3 class="'. $classes .'">'.$meny->title.'</h3>
                        '.render_megamenu_subitems($menies, $meny->ID).'
                        '.( $meny->sidebar!='sidebar_none' && $meny->sidebar!='' ? get_widgets_on_menu($meny->sidebar) : '' ) .'
                      </div>';
        }
    }
    return $html!='' ? ('<ul class="dropdown-menu"><li>'.$html.'<div class="clearfix"></div></li></ul>') : '';
}

function render_megamenu_subitems($menies, $parent){
    $html = '';
    foreach($menies as $meny){
        if( $meny->menu_item_parent == $parent ){
            $menu_text = get_icon_menu($meny) . get_menu_text($meny) . get_description_menu($meny);

            $menu_classes = get_current_menu_class($menies, $meny);
            if( trim($meny->image)!='' ){
                $menu_text = '<span class="menu-image"><img src="'. $meny->image .'" /></span>';
                $menu_classes = ' menu-image-item';
            }

            $html .= '<div id="'. get_menu_id($meny) .'" class="menu-item'. $menu_classes .'">
                        <a href="'.$meny->url.'" '.get_custom_atts_menu($meny).'>
                        	'. $menu_text .'
                        </a>
                      </div>';
        }
    }
    return $html;
}



function render_mega_nav($menu_location='primary'){
    $theme_locations = get_nav_menu_locations();

    global $smof_data;
    $layout = isset($smof_data['header_layout']) ? $smof_data['header_layout'] : '1';
    if(is_page() && tt_getmeta('customize_page') == '1') {
        $layout = tt_getmeta('header_layout');
    }
    $menu_location = isset($theme_locations[$menu_location]) ? $menu_location : 'primary';

    if( isset($theme_locations[$menu_location]) && (int)$theme_locations[$menu_location]>0 ){
        $menu_obj = get_term( $theme_locations[$menu_location], 'nav_menu' );
        $menies = wp_get_nav_menu_items($menu_obj);
        if( !is_array($menies) ){
            return;
        }
        echo "<ul class='nav navbar-nav'>";
        foreach($menies as $meny){
            if( $meny->menu_item_parent == 0 ){
            	$fullwidth = $meny->fullwidth=='1' ? 'fullwidth ' : '';
				$fullwidth .= $meny->activemega=='1' ? 'mega-menu ' : '';
				
				$color = $meny->color;
				$color = $color=='#fff' ? '#ffffff' : $color;
				$color = $color=='#000' ? '#000' : $color;
				$text_class = '';

                $astyle = '';
                if( $layout == '2' ){
                    $astyle = "background-color: $color;";
                    $text_class = $color!='' ? get_text_class($color) : '';
                }
                $text_class .= ' '.get_current_menu_class($menies, $meny);

                $menu_childs = $meny->activemega=='1' ? render_megamenu_items($menies, $meny->ID) : render_submenu_items($menies, $meny->ID);
                $text_class .= $menu_childs!='' ? ' menu-item-has-children has-children' : '';
                
                echo '<li id="'. get_menu_id($meny) .'" class="'.$fullwidth.$text_class.'">
                        <a href="'.$meny->url.'" '.get_custom_atts_menu($meny).' style="'.$astyle.'">
                        	'.get_icon_menu($meny).'
                            '.get_menu_text($meny).'
                            '.get_description_menu($meny).'
                        </a>
                        '. $menu_childs .'
                      </li>';
            }
        }

        // Nav menu items
        $args = array( 'theme_location'=>$menu_location );
        $defaults = array( 'menu' => '', 'container' => 'div', 'container_class' => '', 'container_id' => '', 'menu_class' => 'menu', 'menu_id' => '',
        'echo' => true, 'fallback_cb' => 'wp_page_menu', 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '', 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        'depth' => 0, 'walker' => '', 'theme_location' => '' );

        $args = wp_parse_args( $args, $defaults );
        $args = apply_filters( 'wp_nav_menu_args', $args );
        $args = (object) $args;

        $nav_menu_items_hook = apply_filters( 'wp_nav_menu_items', '', $args );
        echo $nav_menu_items_hook;
        echo '</ul>';
    }
    else{
        wp_nav_menu( array(
            'theme_location'    => 'primary',
            'container_class'   => 'mainmenu',
            'fallback_cb'       => 'metro_menu_callback',
            )
        );
    }
    
    // Prints menu if user set menu on Mobile location
    $mobile = 'mobile-menu';
    if (isset($theme_locations[$mobile]) && $theme_locations[$mobile] > 0) {
        wp_nav_menu(array(
            'theme_location' => $mobile,
            'container_id' => 'tt-mobile-menu',
            'container_class' => 'hidden-xs hidden-sm hidden-md hidden-lg'
                )
        );
    }
}
function metro_menu_callback() {
    echo '<ul class="nav navbar-nav"><li><a href="'.site_url().'/wp-admin/nav-menus.php">'.__("Please create a menu in <b>Appearance->Menus</b> and select it on Primary location!","themeton").'</a></li></ul>';
}














add_action('admin_enqueue_scripts', 'ttmegamenu_render_scripts');
function ttmegamenu_render_scripts($hook) {
	if( $hook!='nav-menus.php' ){
		return;
	}
    wp_enqueue_script('jquery');
    if(function_exists( 'wp_enqueue_media' )){
        wp_enqueue_media();
    }
	wp_enqueue_script('tt_admin_option_script', get_template_directory_uri().'/framework/mega-menu/mega-menu.js', false, false, true);
}


add_action( 'admin_init', 'navigation_menu_meta_box' );
function navigation_menu_meta_box() {
    add_meta_box(
	    	'custom-meta-box',
	    	__('Menu Options', 'themeton'),
	    	'tt_nav_menu_item_link_meta_box',
	    	'nav-menus',
	    	'side',
	    	'default'
		);
}
function tt_nav_menu_item_link_meta_box() {
	$value = '';
	$metro = get_option('active_metro_menu');
	if( $metro=='1' ){
		$value = 'checked="checked"';
	}
    ?>
	<label for="metro_menu">
    	<input type="checkbox" name="metro_menu" id="metro_menu" value="1" <?php echo $value; ?> />
    	&nbsp;Active Metro Menu
	</label>
	<p style="text-align: right;">
		<input type="button" class="button-primary" value="Save" id="button_metro_menu" style="float: right;" />
		<span class="spinner" style="display: none; float: right;"></span>
		&nbsp;
	</p>
	<br>
    <?php
}

add_action('wp_ajax_update_metro_menu_settings', 'update_metro_menu_settings_hook');
add_action('wp_ajax_nopriv_update_metro_menu_settings', 'update_metro_menu_settings_hook');
function update_metro_menu_settings_hook() {
    try {
        if( isset($_POST['metro_menu']) ){
        	update_option('active_metro_menu', trim($_POST['metro_menu']));
        }
        echo "1";
    } catch (Exception $e) {
        echo "-1";
    }
    exit;
}






/* Icon field */
add_filter( 'wp_setup_nav_menu_item','icon_nav_item' );
function icon_nav_item($menu_item) {
    $menu_item->icon = get_post_meta( $menu_item->ID, '_menu_item_icon', true );
    return $menu_item;
}
add_action('wp_update_nav_menu_item', 'icon_nav_update',10, 3);
function icon_nav_update($menu_id, $menu_item_db_id, $args ) {
	ob_start();
    if ( isset($_REQUEST['menu-item-icon']) && is_array($_REQUEST['menu-item-icon']) ) {
    	$items = $_REQUEST['menu-item-icon'];
        $custom_value = $items[$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_icon', $custom_value );
    }
	ob_clean();
}


/* Image field */
add_filter( 'wp_setup_nav_menu_item','image_nav_item' );
function image_nav_item($menu_item) {
    $menu_item->image = get_post_meta( $menu_item->ID, '_menu_item_image', true );
    return $menu_item;
}
add_action('wp_update_nav_menu_item', 'image_nav_update',10, 3);
function image_nav_update($menu_id, $menu_item_db_id, $args ) {
    ob_start();
    if ( isset($_REQUEST['menu-item-image']) && is_array($_REQUEST['menu-item-image']) ) {
        $items = $_REQUEST['menu-item-image'];
        $custom_value = $items[$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_image', $custom_value );
    }
    ob_clean();
}



add_filter( 'wp_setup_nav_menu_item','activemega_nav_item' );
function activemega_nav_item($menu_item) {
	$menu_item->activemega = get_post_meta( $menu_item->ID, '_menu_item_activemega', true );
    return $menu_item;
}
add_action('wp_update_nav_menu_item', 'activemega_nav_update',10, 3);
function activemega_nav_update($menu_id, $menu_item_db_id, $args ) {
	ob_start();
    if ( isset($_REQUEST['menu-item-activemega']) && is_array($_REQUEST['menu-item-activemega']) ) {
    	$items = $_REQUEST['menu-item-activemega'];
        $custom_value = $items[$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_activemega', $custom_value );
    }
	ob_clean();
}


add_filter( 'wp_setup_nav_menu_item','fullwidth_nav_item' );
function fullwidth_nav_item($menu_item) {
	$menu_item->fullwidth = get_post_meta( $menu_item->ID, '_menu_item_fullwidth', true );
    return $menu_item;
}
add_action('wp_update_nav_menu_item', 'fullwidth_nav_update',10, 3);
function fullwidth_nav_update($menu_id, $menu_item_db_id, $args ) {
	ob_start();
    if ( isset($_REQUEST['menu-item-fullwidth']) && is_array($_REQUEST['menu-item-fullwidth']) ) {
    	$items = $_REQUEST['menu-item-fullwidth'];
        $custom_value = $items[$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_fullwidth', $custom_value );
    }
	ob_clean();
}



// Menu Sidebar option
add_filter( 'wp_setup_nav_menu_item','sidebar_nav_item' );
function sidebar_nav_item($menu_item) {
    $menu_item->sidebar = get_post_meta( $menu_item->ID, '_menu_item_sidebar', true );
    return $menu_item;
}
add_action('wp_update_nav_menu_item', 'sidebar_nav_update',10, 3);
function sidebar_nav_update($menu_id, $menu_item_db_id, $args ) {
    ob_start();
    if ( isset($_REQUEST['menu-item-sidebar']) && is_array($_REQUEST['menu-item-sidebar']) ) {
        $items = $_REQUEST['menu-item-sidebar'];
        $custom_value = $items[$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_sidebar', $custom_value );
    }
    ob_clean();
}







add_filter( 'wp_edit_nav_menu_walker', 'ttmega_nav_edit_walker',10,2 );
function ttmega_nav_edit_walker($walker,$menu_id) {
    return 'Walker_Nav_Menu_Edit_TTMega';
}

class Walker_Nav_Menu_Edit_TTMega extends Walker_Nav_Menu  {

    function start_lvl( &$output, $depth = 0, $args = array() ){}
	
	function end_lvl( &$output, $depth = 0, $args = array() ){}

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
	    global $_wp_nav_menu_max_depth;
	    $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;
	
	    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	
	    ob_start();
	    $item_id = esc_attr( $item->ID );
	    $removed_args = array(
	        'action',
	        'customlink-tab',
	        'edit-menu-item',
	        'menu-item',
	        'page-tab',
	        '_wpnonce',
	    );
	
	    $original_title = '';
	    if ( 'taxonomy' == $item->type ) {
	        $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
	        if ( is_wp_error( $original_title ) )
	            $original_title = false;
	    } elseif ( 'post_type' == $item->type ) {
	        $original_object = get_post( $item->object_id );
	        $original_title = isset($original_object->post_title) ? $original_object->post_title : '';
	    }
	
	    $classes = array(
	        'menu-item menu-item-depth-' . $depth,
	        'menu-item-' . esc_attr( $item->object ),
	        'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
	    );
	
	    $title = $item->title;
	
	    if ( ! empty( $item->_invalid ) ) {
	        $classes[] = 'menu-item-invalid';
	        /* translators: %s: title of menu item which is invalid */
	        $title = sprintf( __( '%s (Invalid)', 'themeton' ), $item->title );
	    } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
	        $classes[] = 'pending';
	        /* translators: %s: title of menu item in draft status */
	        $title = sprintf( __('%s (Pending)', 'themeton'), $item->title );
	    }
	
	    $title = empty( $item->label ) ? $title : $item->label;
	
	    ?>
    <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
        <dl class="menu-item-bar">
            <dt class="menu-item-handle">
                <span class="item-title"><?php echo esc_html( $title ); ?></span>
                <span class="item-controls">
                    <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
                    <span class="item-order hide-if-js">
                        <a href="<?php
                            echo wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'move-up-menu-item',
                                        'menu-item' => $item_id,
                                    ),
                                    remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                ),
                                'move-menu_item'
                            );
                        ?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up', 'themeton'); ?>">&#8593;</abbr></a>
                        |
                        <a href="<?php
                            echo wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'move-down-menu-item',
                                        'menu-item' => $item_id,
                                    ),
                                    remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                ),
                                'move-menu_item'
                            );
                        ?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down', 'themeton'); ?>">&#8595;</abbr></a>
                    </span>
                    <a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item', 'themeton'); ?>" href="<?php
                        echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
                    ?>"><?php _e( 'Edit Menu Item', 'themeton' ); ?></a>
                </span>
            </dt>
        </dl>

        <div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
            <?php if( 'custom' == $item->type ) : ?>
                <p class="field-url description description-wide">
                    <label for="edit-menu-item-url-<?php echo $item_id; ?>">
                        <?php _e( 'URL', 'themeton' ); ?><br />
                        <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
                    </label>
                </p>
            <?php endif; ?>
            <p class="description description-thin">
                <label for="edit-menu-item-title-<?php echo $item_id; ?>">
                    <?php _e( 'Navigation Label', 'themeton' ); ?><br />
                    <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
                </label>
            </p>
            <p class="description description-thin">
                <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
                    <?php _e( 'Title Attribute', 'themeton' ); ?><br />
                    <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
                </label>
            </p>
            <p class="field-link-target description">
                <label for="edit-menu-item-target-<?php echo $item_id; ?>">
                    <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
                    <?php _e( 'Open link in a new window/tab', 'themeton' ); ?>
                </label>
            </p>
            <p class="field-css-classes description description-thin">
                <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
                    <?php _e( 'CSS Classes (optional)', 'themeton' ); ?><br />
                    <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
                </label>
            </p>
            <p class="field-xfn description description-thin">
                <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
                    <?php _e( 'Link Relationship (XFN)', 'themeton' ); ?><br />
                    <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
                </label>
            </p>
            <p class="field-description description description-wide">
                <label for="edit-menu-item-description-<?php echo $item_id; ?>">
                    <?php _e( 'Description', 'themeton' ); ?><br />
                    <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
                    <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.', 'themeton'); ?></span>
                </label>
            </p>        
            <?php
            /*
             * This is the added field
             */
            ?>      
            <p class="field-icon description description-wide">
                <label for="edit-menu-item-icon-<?php echo $item_id; ?>">
                    <?php _e( 'Menu icon (allows custom icon by url)', 'themeton' ); ?><br />
                    <input type="text" id="edit-menu-item-icon-<?php echo $item_id; ?>" class="widefat code edit-menu-item-icon" name="menu-item-icon[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->icon ); ?>" style="width: 70%; padding-top: 5px;" />
                    <a href="javascript:;" class="button browse_font_icon">Browse Icon</a>
                </label>
            </p>
            
            <p class="field-image description description-wide">
                <label for="edit-menu-item-image-<?php echo $item_id; ?>">
                    <?php _e( 'Image for mega menu links', 'themeton' ); ?><br />
                    <input type="text" id="edit-menu-item-image-<?php echo $item_id; ?>" class="widefat code edit-menu-item-image" name="menu-item-image[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->image ); ?>" style="width: 70%; padding-top: 5px;" />
                    <a href="javascript:;" class="button browse_image">Browse Image</a>
                </label>
            </p>

            <p class="field-activemega description description-wide">
                <label for="edit-menu-item-activemega-<?php echo $item_id; ?>">
                    <?php _e( 'Active Mega Menu', 'themeton' ); ?><br />
                    <input type="checkbox" id="edit-menu-item-activemega-<?php echo $item_id; ?>" class="widefat code edit-menu-item-activemega" value="1" <?php echo $item->activemega=='1' ? 'checked="checked"' : ''; ?> onchange="javascript: jQuery(this).parent().find('input[type=hidden]').val( this.checked ? '1' : '' );" />
                    <input type="hidden" name="menu-item-activemega[<?php echo $item_id; ?>]" value="<?php echo $item->activemega; ?>" />
                </label>
            </p>
            
            <p class="field-fullwidth description description-wide">
                <label for="edit-menu-item-fullwidth-<?php echo $item_id; ?>">
                    <?php _e( 'Full width mega menu', 'themeton' ); ?><br />
                    <input type="checkbox" id="edit-menu-item-fullwidth-<?php echo $item_id; ?>" class="widefat code edit-menu-item-fullwidth" value="1" <?php echo $item->fullwidth==='1' ? 'checked="checked"' : ''; ?> onchange="javascript: jQuery(this).parent().find('input[type=hidden]').val( this.checked ? '1' : '' );" />
                    <input type="hidden" name="menu-item-fullwidth[<?php echo $item_id; ?>]" value="<?php echo $item->fullwidth; ?>" />
                </label>
            </p>
            
            <script type="text/javascript">
            	if( typeof initMenuFields == 'function' ){
            		initMenuFields();
            	}
            </script>

            
            <!-- START: Menu in Widget -->
            <?php
            global $tt_sidebars;
            ?>
            <p class="field-sidebar description description-wide" style="display:none;">
                <label for="edit-menu-item-sidebar-<?php echo $item_id; ?>">
                    <?php _e( 'Label with Sidebar', 'themeton' ); ?><br />
                    <select id="edit-menu-item-sidebar-<?php echo $item_id; ?>" class="widefat code edit-menu-item-sidebar" name="menu-item-sidebar[<?php echo $item_id; ?>]">
                    <?php
                    echo '<option value="sidebar_none">None</option>';
                    foreach ($tt_sidebars as $key => $value):
                        $sidebar_select =  ($item->sidebar==$key ? 'selected="selected"' : '');
                        echo '<option value="'.$key.'" '.$sidebar_select.'>'.$value.'</option>';
                    endforeach;
                    ?>
                    </select>
                </label>
            </p>
            <!-- END: Menu in Widget -->


            
            <?php
            /*
             * end added field
             */
            ?>
            <div class="menu-item-actions description-wide submitbox">
                <?php if( 'custom' != $item->type && $original_title !== false ) : ?>
                    <p class="link-to-original">
                        <?php printf( __('Original: %s', 'themeton'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
                    </p>
                <?php endif; ?>
                <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
                echo wp_nonce_url(
                    add_query_arg(
                        array(
                            'action' => 'delete-menu-item',
                            'menu-item' => $item_id,
                        ),
                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                    ),
                    'delete-menu_item_' . $item_id
                ); ?>"><?php _e('Remove', 'themeton'); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ) );
                    ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel', 'themeton'); ?></a>
            </div>

            <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
            <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
            <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
            <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
            <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
            <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
        </div><!-- .menu-item-settings-->
        <ul class="menu-item-transport"></ul>
    <?php
    $output .= ob_get_clean();
    }
}
?>