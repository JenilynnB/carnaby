<?php

add_action('admin_enqueue_scripts', 'admin_option_render_scripts');

function admin_option_render_scripts($hook) {
    global $post_type;
    if ($post_type == '' || $post_type == NULL) {
        return;
    }
    wp_enqueue_style('tt_admin_option_style', get_template_directory_uri() . '/framework/post-type/renderer.css');
    wp_enqueue_script('tt_admin_option_script', get_template_directory_uri() . '/framework/post-type/renderer.js', false, false, true);
}

function get_post_meta_array(){
    $tt_post_meta = get_post_type_options_page();
    $tt_post_meta = array_merge($tt_post_meta, get_post_type_options_portfolio());
    $tt_post_meta = array_merge($tt_post_meta, get_post_type_options_post());
    return $tt_post_meta;
}

// Add Meta Boxed
add_action('add_meta_boxes', 'themeton_admin_option_add_custom_box', 1);
function themeton_admin_option_add_custom_box() {
    $tt_post_meta = get_post_meta_array();
    foreach ($tt_post_meta as $key => $value) {
        $position = 'advanced';
        $priority = 'core';
        if ($value['post_type'] == 'post') {
            $position = 'advanced';
            $priority = 'high';
        }
        add_meta_box(
            'pmeta_' . $key, $value['label'], 'themeton_ao_render_section', $value['post_type'], $position, 'core', $value['items']
        );
    }
}

function themeton_ao_render_section($post, $metabox) {
    global $post;
    foreach ($metabox['args'] as $value) {
        
        if ($value['type'] == 'start_group') {
            $style = '';
            if(isset($value['visible']) && $value['visible'] == false){
                $style = 'style="display:none;"';
            }
            echo '<div id="'.$value['name'].'" class="'.$value['name'].'" '.$style.'>';
        }
        elseif ($value['type'] == 'end_group') {
            echo '</div><!-- #'.$value['name'].' -->';
        }
        else {
            echo "<div id='option_wrapper_" . $value['name'] . "' class='page_option_fieldset'>";
            if( isset($value['label']) && $value['label'] != '' && !in_array($value['type'], array('checkbox1', 'colorpicker1', 'number1')) ){
                echo "<div><label for='" . $value['name'] . "'>" . $value['label'] . "</label></div>";
            }
            echo "<div>";
                if( isset($value['fullwidth']) ){
                    echo "<div class='page_option_field_full'>".themeton_ao_get_field($value)."</div>";
                }
                else{
                    echo "<div class='page_option_field'>".themeton_ao_get_field($value)."</div>";
                    if(isset($value['desc'])) {
                        echo '<div class="field_description">'.$value['desc'].'</div>';
                    }
                }
            echo "</div>";
            echo "<div style='clear:both;'></div></div>";
        }
    }
}

function get_bg_values($value){
    $exp = explode('$', $value);
    if( count($exp) > 3 ){
        return array(
                'url'=>$exp[0],
                'repeat'=>$exp[1],
                'position'=>$exp[2],
                'attach'=>$exp[3]
            );
    }
    return array(
                'url'=>'',
                'repeat'=>'',
                'position'=>'',
                'attach'=>''
            );
}
function themeton_ao_get_field($params) {
    global $post;
    $name = $params['name'];
    $type = $params['type'];
    $meta_val = tt_getmeta($params['name'], $post->ID);

    $default = isset($params['default']) ? $params['default'] : '';
    $value = $meta_val != '' ? $meta_val : $default;
    if (isset($params['option']))
        $option = $params['option'];

    $gatts = 'id="' . $name . '" name="' . $name . '"';



    switch ($type) {
        case 'title':
            return '<span style="font-weight: bold;font-size:14px;background-color: #4cd864;display: inline-block;padding:7px;border-radius: 3px;color: #fff;">' . $params['title'] . '</span>';
            break;
        case 'colorpicker':
            return '<input type="text" ' . $gatts . ' value="' . $value . '" class="tt_wpcolorpicker" data-default-color="' . $default . '" />';
            break;
        case 'text':
            return '<input type="text" ' . $gatts . ' value="' . $value . '" />';
            break;
        case 'number':
            return '<input type="number" ' . $gatts . ' step="1" min="0" value="' . (int)$value . '" class="small-text" />';
            break;
        case 'textarea':
            return '<textarea ' . $gatts . '>' . $value . '</textarea>';
            break;
        case 'select':
            $html = '<select ' . $gatts . ' default-value="' . $value . '" class="tt_wpselectbox">';
            foreach ($option as $key => $val) {
                $html .= '<option value="' . $key . '">' . $val . '</option>';
            }
            $html .= '</select>';
            return $html;
            break;
        case 'radio':
            $html = '';
            foreach ($option as $key => $val) {
                $html .= '<input type="radio" group="tt_group_' . $name . '" ' . $gatts . ' class="tt_wpradiobox" ' . ($value == $key ? 'checked' : '') . '>';
                $html .= $val . '<br>';
            }
            return $html;
            break;
        case 'icon':
            $html = '';
            foreach ($option as $key => $val) {
                $html .= '<input type="radio" group="tt_group_' . $name . '" ' . $gatts . ' class="tt_wpradiobox" ' . ($val == $key ? 'checked' : '') . '>';
                $html .= $val;
                if ($key == 'custom')
                    $html .= '<input type="text" value""/>';
                $html .= '<br>';
            }
            return $html;
            break;
        case 'thumbs':
            $html = '';
            $ndx = 0;
            foreach ($option as $key => $val) {
                $ndx++;
                $html .= '<input type="radio" group="tt_group_' . $name . '" name="' . $name . '" id="' . $name . $ndx . '" value="' . $key . '" class="tt_wpradiobox" ' . ($key == $value ? 'checked' : '') . '>';
                $html .= '<label for="' . $name . $ndx . '"><img src="' . $val . '" class="'.($key == $value ? 'active' : '').'" /></label>';
            }
            return "<div class='page_option_field_thumbs'>$html</div>";
            break;
        case 'image':
            $html = '';
            $html .= '<div class="pmeta_item_browse">
                            <input type="text" ' . $gatts . ' value="' . $value . '" />
                            <a href="javascript:;" class="button pmeta_button_browse">Browse...</a>
                            <div class="browse_preview">' . ($value != '' ? '<img src="' . $value . '" /><a href="javascript:;">Remove</a>' : '') . '</div>
                    </div>';
            return $html;
            break;
        case 'metro':
            $html = '<div id="metro_style_container">
                            <a href="javascript:;" class="small1"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/small-icon-title.png" /></a>
                            <a href="javascript:;" class="small2"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/small-title.png" /></a>
                            <a href="javascript:;" class="small3"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/small-image.png" /></a>
                            <a href="javascript:;" class="small4"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/small-icon.png" /></a>
                            <a href="javascript:;" class="hor1"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/big-icon-title.png" /></a>
                            <a href="javascript:;" class="hor2"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/big-icon.png" /></a>
                            <a href="javascript:;" class="hor3"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/big-image.png" /></a>
                            <a href="javascript:;" class="hor4"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/big-title.png" /></a>
                            <a href="javascript:;" class="large1"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/large-icon-title.png" /></a>
                            <a href="javascript:;" class="large2"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/large-icon.png" /></a>
                            <a href="javascript:;" class="large3"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/large-image-stitle.png" /></a>
                            <a href="javascript:;" class="large4"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/large-image-title.png" /></a>
                            <a href="javascript:;" class="large5"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/large-image.png" /></a>
                            <a href="javascript:;" class="large6"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/large-title.png" /></a>
                            <a href="javascript:;" class="ver1"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/large-ver-title.png" /></a>
                            <a href="javascript:;" class="ver2"><img src="' . get_template_directory_uri() . '/framework/post-type/images/post-format/large-ver-icon.png" /></a>
                    </div>';

            $html .= '<input type="hidden" ' . $gatts . ' value="' . $value . '" />';
            return $html;
            break;
        case 'font_icon':
            $html = '';
            $html .= '<div class="pmeta_item_font">
                                <input type="text" ' . $gatts . ' value="' . $value . '" />
                                <a href="javascript:;" class="button pmeta_button_font">Font Icon...</a>
                        </div>';
            return $html;
            break;
        case 'onepage':
            $html = '';

            $html .= '<select id="onpage_allpages">';
            $html .= '<option value="0">' . __('Choose page...', 'themeton') . '</option>';
            $pages = get_pages();
            foreach ($pages as $page) {
                if( get_post_meta( $page->ID, '_wp_page_template', true )!='page-one-page.php' ){
                    $html .= '<option value="' . $page->ID . '" data-link="' . get_permalink($page->ID) . '">' . $page->post_title . '</option>';
                }
            }
            $html .= '</select>';
            $html .= '&nbsp;&nbsp;<a href="javascript:;" class="button" id="button_onepage_add">Add Page</a>';
            $html .= '&nbsp;&nbsp;<a href="javascript:;" class="button" id="button_op_custom_link">Custom Link</a>';

            $html .= '<div id="onepage_container"></div>';

            $html .= '<input type="hidden" ' . $gatts . ' value="' . $value . '" />';
            return $html;
            break;
        case 'checkbox':
            return '<span class="blox_switcher '.($value == '1' ? 'on' : '').'">
                        <span class="handle"></span>
                        <input type="hidden" ' . $gatts . ' value="' . ($value == '1' ? '1' : '0') . '" />
                    </span>';
            break;
        case 'customlink':
            return '<input type="text" ' . $gatts . ' value="' . $value . '" /><input type="checkbox" value="1"/> Open in a new tab?';
            break;
        case 'video':
            $html = '<div class="pmeta_video">
                        <input type="text" ' . $gatts . ' value="' . $value . '" />
                        <a href="javascript:;" class="button pmeta_button_browse">Browse...</a>
                    </div>';
            return $html;
            break;
        case 'gallery':
            $imgs = '';
            $arr = explode(',', trim($value));
            foreach ($arr as $uri) {
                if( $uri!='' ){
                    $imgs .= '<span style="background-image: url('.wp_get_attachment_url($uri).');"></span>';
                }
            }
            $html = '<div class="pmeta_gallery">
                        <a href="javascript:;" class="button pmeta_button_browse">Insert/Update Gallery...</a>
                        <a href="javascript:;" class="pmeta_remove" title="Remove Gallery">(<i class="fa-times"></i>)</a>
                        <input type="hidden" ' . $gatts . ' value="'.trim($value).'" class="gallery_images" />
                        <div class="gallery_images_preview">'.$imgs.'</div>
                    </div>';
            return $html;
            break;
        case 'background':
            $values = get_bg_values($value);
            $html = '';
            $html .= '<div class="pmeta_item_background">
                            <input type="text" id="'.$name.'_image" name="'.$name.'_image" value="' . $values['url'] . '" class="bg_image_url" />
                            <input type="hidden" ' . $gatts . ' value="'.$value.'" class="bg_hidden_value" />
                            <a href="javascript:;" class="button pmeta_button_browse">Browse...</a>
                            <div class="browse_preview" style="'.($values['url']=='' ? 'display:none;' : '').'">
                                <div class="preview_sample" style="background-image:url('.$values['url'].');"></div>
                                <a href="javascript:;">Remove</a>
                            </div>
                            <div class="background-controlls" style="'.($values['url']=='' ? 'display:none;' : '').'">
                                <select id="'.$name.'_repeat" name="'.$name.'_repeat" default-value="'.$values['repeat'].'" class="tt_wpselectbox bg_image_repeat">
                                    <option value="repeat">Repeat</option>
                                    <option value="repeat-x">Repeat-X</option>
                                    <option value="repeat-y">Repeat-Y</option>
                                    <option value="no-repeat">No Repeat</option>
                                    <option value="cover">Cover</option>
                                </select>
                                <select id="'.$name.'_position" name="'.$name.'_position" default-value="'.$values['position'].'" class="tt_wpselectbox bg_image_position">
                                    <option value="top left">Top &amp; Left</option>
                                    <option value="top center">Top &amp; Center</option>
                                    <option value="top right">Top &amp; Right</option>
                                    <option value="center left">Center &amp; Left</option>
                                    <option value="center center">Center &amp; Center</option>
                                    <option value="center right">Center &amp; Right</option>
                                    <option value="bottom left">Bottom &amp; Left</option>
                                    <option value="bottom center">Bottom &amp; Center</option>
                                    <option value="bottom right">Bottom &amp; Right</option>
                                </select>
                                <select id="'.$name.'_attach" name="'.$name.'_attach" default-value="'.$values['attach'].'" class="tt_wpselectbox bg_image_attach">
                                    <option value="scroll">Scroll</option>
                                    <option value="fixed">Fixed</option>
                                    <option value="parallax">Parallax</option>
                                </select>
                            </div>
                      </div>';
            return $html;
            break;
        default:
            return "Option doesn't prepared!";
            break;
    }
}

// Save fields data
add_action('save_post', 'tt_admin_option_save_postdata');

function tt_admin_option_save_postdata($post_id) {

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;

    if (isset($_GET['post_type']) && 'page' == $_GET['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
    }
    else {
        if (!current_user_can('edit_post', $post_id))
            return $post_id;
    }

    $field_name = array();
    
    $tt_post_meta = get_post_meta_array();
    foreach ($tt_post_meta as $key => $value) {
        foreach ($value['items'] as $item) {
            $field_name[] = $item['name'];
        }
    }


    foreach ($field_name as $field) {
        if (isset($_POST[$field])) {
            $data_field = '_' . $field;
            $data_value = $_POST[$field];
            // $data_value = strlen($data_value)>5 ? esc_textarea($data_value) : $data_value;

            if (count(get_post_meta($post_id, $data_field)) == 0) {
                add_post_meta($post_id, $data_field, trim($data_value), true);
            } elseif ($data_value != get_post_meta($post_id, $data_field, true)) {
                update_post_meta($post_id, $data_field, trim($data_value));
            } elseif ($data_value == "") {
                delete_post_meta($post_id, $data_field, trim(get_post_meta($post_id, $data_field, true)));
            }
        }
    }

    if( isset($_POST['page_template']) && $_POST['page_template']=='page-ultimate.php' ){
        generate_css_ultimate_page( $post_id, $_POST );
    }
}

?>