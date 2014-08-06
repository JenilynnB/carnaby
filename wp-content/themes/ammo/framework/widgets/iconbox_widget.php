<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class tt_IconBoxWidget extends WP_Widget {

    function tt_IconBoxWidget() {
        $widget_ops = array('classname' => 'tticonboxwidget', 'description' => 'Custom icon with link.');
        parent::WP_Widget(false, ': Icon box', $widget_ops);
    }

    function widget($args, $instance) {
        global $post;
        extract(array(
            'title' => '',
            'icon' => 'icon',
            'link' => '',
            'target' => '_blank'
        ));
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
		
		echo "<h1>Iconbox html needed!</h1>";
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        /* Strip tags (if needed) and update the widget settings. */
        $instance['title'] = sanitize_text_field($new_instance['title']);

        $instance['icon'] = sanitize_text_field($new_instance['icon']);
        $instance['text'] = sanitize_text_field($new_instance['text']);
        $instance['link'] = sanitize_text_field($new_instance['link']);
        $instance['target'] = sanitize_text_field($new_instance['target']);

        return $instance;
    }

    function form($instance) {

        //Output admin widget options form
        extract(shortcode_atts(array(
			'title' => '',
			'icon' => 'icon',
			'link' => '',
			'target' => '_blank',
			), $instance));
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title:", "themeton"); ?></label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>"  />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('icon'); ?>"><?php _e("Icon:", "themeton"); ?></label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('icon'); ?>" name="<?php echo $this->get_field_name('icon'); ?>" value="<?php echo $icon; ?>"  />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('link'); ?>"><?php _e("Link:", "themeton"); ?></label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" value="<?php echo $link; ?>" />
        </p>
        <p>
            <input type="checkbox" id="<?php echo $this->get_field_id('target'); ?>" name="<?php echo $this->get_field_name('target'); ?>" value="1" <?php echo $target=='1' ? 'checked="checked"' : ''; ?>/>
            <label for="<?php echo $this->get_field_id('target'); ?>"><?php _e("Open in a new tab", "themeton"); ?></label>
        </p>
        <?php
    }
}

add_action('widgets_init', create_function('', 'return register_widget("tt_IconBoxWidget");'));
?>
