<?php

class tt_SocialLinksWidget extends WP_Widget {

    function tt_SocialLinksWidget() {
        $widget_ops = array('classname' => 'tt-social-links-widget', 'description' => 'Displays your social profile.');

        parent::WP_Widget(false, ': Social Links', $widget_ops);
    }

    function widget($args, $instance) {
        extract($args);
        global $tt_social_icons;
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if ($title)
            echo $before_title . $title . $after_title;
        echo '<aside class="widget tt_recent_posts tt-widget">';
        echo '<ul class="social-icon list-inline">';
        foreach ($tt_social_icons as $social => $hex) {
            if (isset($instance['social_' . $social]) && $instance['social_' . $social] != "") {
                $url = $instance['social_' . $social];
                if($social != 'email') {
                    if(strpos($url, 'http:') === false) $url = "http://" . $url;
                } else {
                    $url = 'mailto:' . $url . '?subject=' . get_bloginfo('name') . '&body=Your message here!';
                }
                echo '<li><a class="' . $social . '" href="' . $url . '" ><i class="fa '.$hex.'"></i></a></li>';
            }
        }
        echo '</ul>';
        echo '</aside>';

        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        global $tt_social_icons;
        /* Strip tags (if needed) and update the widget settings. */
        $instance['title'] = sanitize_text_field($new_instance['title']);
        foreach ($tt_social_icons as $social => $hex) {
            $instance['social_' . $social] = sanitize_text_field($new_instance['social_' . $social]);
        }
        return $instance;
    }

    function form($instance) {
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Widget Title:</label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo isset($instance['title']) ? $instance['title'] : ''; ?>"  />
        </p>
        <?php
        global $tt_social_icons;
        foreach ($tt_social_icons as $social => $hex) { ?>
            <p>
                <label for="<?php echo $this->get_field_id('social_' . $social); ?>"><?php echo ucwords($social); ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('social_' . $social); ?>" name="<?php echo $this->get_field_name('social_' . $social); ?>" value="<?php echo isset($instance['social_' . $social]) ? $instance['social_' . $social] : ''; ?>"  />
            </p>
            <?php
        }
    }

}

add_action('widgets_init', create_function('', 'return register_widget("tt_SocialLinksWidget");'));
?>
