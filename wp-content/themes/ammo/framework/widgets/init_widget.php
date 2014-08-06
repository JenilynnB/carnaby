<?php

function render_form_custom_options($widget, $return, $instance) {
	$metro_widget = isset($instance['metro_widget']) ? $instance['metro_widget'] : '0';
	$metro_color = isset($instance['widget_color']) ? $instance['widget_color'] : '';
?>
	<div>
	<p>
		<label>
			<input type="hidden" name="<?php echo $widget->get_field_name('metro_widget'); ?>" value="<?php echo $metro_widget; ?>" />
			<input type="checkbox" <?php echo $metro_widget=='1' ? 'checked="checked"' : ''; ?> onchange="javascript: jQuery(this).parent().find('input[type=hidden]').val( this.checked ? '1' : '0' );" />
			&nbsp;Active Metro Widget
		</label>
	</p>
	<p class="widget_color_container">
		<label>Widget Color</label><br>
		<input type="text" class="tt_wpcolorpicker" id="<?php echo $widget->get_field_id('widget_color'); ?>" name="<?php echo $widget->get_field_name('widget_color'); ?>" value="<?php echo $metro_color; ?>" />
	</p>
	</div>
<?php
}

function render_custom_widget_show($instance, $widget, $args) {
	if( isset($instance['metro_widget']) && $instance['metro_widget']=='1' ){
		$color = isset($instance['widget_color']) ? $instance['widget_color'] : '';
		$bgcolor = $color!='' ? 'background-color:'.$color : '';
		$text_class = $color!='' ? get_text_class($color) : '';
		
	    $args['before_widget'] = '<div class="widget_metro '.$text_class.'" style="'.$bgcolor.'">'.$args['before_widget'];
		$args['after_widget'] = $args['after_widget'].'</div>';
	}
	$widget->widget($args, $instance);
    return false;
}

function custom_update_widget_options($instance, $new_instance, $old_instance) {
    $instance['metro_widget'] = $new_instance['metro_widget'];
    $instance['widget_color'] = $new_instance['widget_color'];
    return $instance;
}

//add_filter('in_widget_form', 'render_form_custom_options', 0, 3);
//add_filter('widget_display_callback', 'render_custom_widget_show', 10, 3);
//add_filter('widget_update_callback', 'custom_update_widget_options', 10, 3);


/* Custom widgets */
require_once file_require(get_template_directory() . '/framework/widgets/recent_posts_widget.php');
require_once file_require(get_template_directory() . '/framework/widgets/social_links_widget.php');
require_once file_require(get_template_directory() . '/framework/widgets/most_commented_widget.php');
require_once file_require(get_template_directory() . '/framework/widgets/most_liked_widget.php');
require_once file_require(get_template_directory() . '/framework/widgets/authors_widget.php');

?>