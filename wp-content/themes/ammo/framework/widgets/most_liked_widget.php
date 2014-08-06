<?php

class tt_MostLikedWidget extends WP_Widget {

    function tt_MostLikedWidget() {
        $widget_ops = array('classname' => 'tt-most-liked-widget', 'description' => 'Most liked posts.');
        parent::WP_Widget(false, ': Most Liked Posts', $widget_ops);
    }

    function widget($args, $instance) {
        global $post;
        extract(array_merge(array(
                    'title' => '',
                    'number_posts' => 4,
                    'post_type' => 'post'
                        ), $instance));

        $q['posts_per_page'] = $number_posts;
        $q['paged'] = 1;
        
		$q['meta_key'] = '_post_like';
        $q['orderby'] = 'meta_value_num';
        $q['order'] = 'DESC';
        
        $q['ignore_sticky_posts'] = 1;

        query_posts($q);

        if (isset($before_widget))
            echo $before_widget;
        
        echo '<aside class="widget tt_recent_posts tt-widget tt-most-liked">';

        if ($title != '')
            echo $args['before_title'] . $title . $args['after_title'];

        echo '<ul>';
        while (have_posts()) : the_post();
			$comments = wp_count_comments($post->ID);
			$likes = blox_getmeta(get_the_ID(), 'post_like');
            echo '<li>';
            echo '<span class="widget-thumb">'.$likes.'</span>';
            echo '<a href="'.get_permalink().'">'.get_the_title().'</a>';
            echo '<ul class="list-inline">
                    <li class="tt-widget-comments-number"><a href="#" title=""><i class="fa fa-comments"></i> '.$comments->total_comments.'</a></li>
                    <li class="tt-widget-likes-number meta_like">'. get_post_like(get_the_ID()) .'</li>
                  </ul>';
            echo '</li>';
        endwhile;
        echo '</ul>';
        echo '</aside>';
        
        if (isset($after_widget))
            echo $after_widget;

        wp_reset_query();
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['number_posts'] = sanitize_text_field($new_instance['number_posts']);
        return $instance;
    }

    function form($instance) {

        //Output admin widget options form
        extract(shortcode_atts(array(
                    'title' => '',
                    'number_posts' => 5,
                        ), $instance));
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title:", "themeton"); ?></label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>"  />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number_posts'); ?>">Number of posts to show:</label>
            <input type="text" id="<?php echo $this->get_field_id('number_posts'); ?>" name="<?php echo $this->get_field_name('number_posts'); ?>" value="<?php echo $number_posts; ?>" size="3" />
        </p>
        <?php
    }
}

add_action('widgets_init', create_function('', 'return register_widget("tt_MostLikedWidget");'));