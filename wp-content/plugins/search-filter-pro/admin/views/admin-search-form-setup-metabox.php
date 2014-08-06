<?php
/**
 * Search & Filter Pro
 * 
 * @package   Search_Filter
 * @author    Ross Morsali
 * @link      http://www.designsandcode.com/
 * @copyright 2014 Designs & Code
 */
 
?>

<div id="available-fields" class="widgets-search-filter-draggables ui-search-filter-sortable setup" data-allow-expand="0">
	<?php
		global $post;
	?>
	<p class="description"><?php _e("Setup defaults for the Search Form.", $this->plugin_slug ); ?></p>
	<hr />
	<br /><strong><?php _e("Post Types", $this->plugin_slug ); ?></strong>
	<p class="description-inline"><?php _e("Search in the following post types:", $this->plugin_slug ); ?></p>
	
	<p class="sf_post_types">
	<?php
		$args = array(
		   'public'   => true
		);
		
		
		$post_types = get_post_types( $args, 'objects' ); 
		
		$is_default = false;
		if(isset($values['post_types']))
		{
			if(!is_array($values['post_types']))
			{
				$is_default = true;
				$values['post_types'] = array();
			}
		}
		else
		{
			$is_default = true;
			$values['post_types'] = array();
		}
		
		
		foreach ( $post_types as $post_type )
		{
			if($post_type->name!="attachment")
			{
				if($is_default)
				{
					if(($post_type->name=="post")||($post_type->name=="page"))
					{
						$values['post_types'][$post_type->name] = "1";
					}
					else
					{
						$values['post_types'][$post_type->name] = "";
					}
				}
				else if(!isset($values['post_types'][$post_type->name]))
				{
					$values['post_types'][$post_type->name] = "";
				}
				
				?>
				
				<input class="checkbox" type="checkbox" id="{0}[{1}][post_types][<?php echo $post_type->name; ?>]" value="<?php echo $post_type->name; ?>" name="settings_post_types[<?php echo $post_type->name; ?>]"<?php $this->set_checked($values['post_types'][$post_type->name]); ?>>
				<label for="{0}[{1}][post_types][<?php echo $post_type->name; ?>]"><?php _e($post_type->labels->name, $this->plugin_slug); ?></label>
				
				<?php
			}
		}
	?>
	</p><br />
	<hr />
	<br /><strong><?php _e("Template", $this->plugin_slug ); ?></strong>
	<p class="description-inline">
		<label for="{0}[{1}][use_template_manual_toggle]">
			<input class="checkbox use_template_manual_toggle" type="checkbox" id="{0}[{1}][use_template_manual_toggle]" name="use_template_manual_toggle"<?php $this->set_checked($values['use_template_manual_toggle']); ?>> 
			<?php _e("Use a custom template for results?", $this->plugin_slug); ?><span class="hint--top hint--info" data-hint="<?php _e("if your meta key is not listed or not yet created enter here", $this->plugin_slug); ?>"><i class="dashicons dashicons-info"></i></span><br />
		</label>
	</p>
	<p class="description">
		<label class="custom_template">
			
			<?php _e("Enter the filename of the custom template:", $this->plugin_slug); ?><br />
			<input class="template_name_manual" id="{0}[{1}][template_name_manual]" name="template_name_manual" type="text" value="<?php echo esc_attr($values['template_name_manual']); ?>" />
			<br /><em><?php _e("The template will be loaded from your theme directory.", $this->plugin_slug); ?></em>
			<input type="hidden"  name="template_name_manual" id="{0}[{1}][template_name_manual]" class="template_name_manual_hidden"  value="<?php echo $values['template_name_manual']; ?>" disabled="disabled" />
		</label>
		<label class="custom_template set_slug">
			<br /><br />
			<strong><?php _e("Set a slug?", $this->plugin_slug); ?></strong>
			<br /><?php echo trailingslashit(home_url()); ?>
			
			<input class="page_slug" id="{0}[{1}][page_slug]" name="page_slug" type="text" value="<?php echo esc_attr($values['page_slug']); ?>" placeholder="?sfid=<?php echo $this->toBase($post->ID); ?>"  />
			
			<input type="hidden"  name="page_slug" id="{0}[{1}][page_slug]" class="page_slug_hidden"  value="<?php echo $values['page_slug']; ?>" disabled="disabled" />
		</label>
		
	</p>
	
	<hr />
	<br /><strong><?php _e("Auto Count <em>*beta*</em>", $this->plugin_slug ); ?></strong>
	<p class="description-inline">
		<label for="{0}[{1}][enable_auto_count]">
			<input class="checkbox enable_auto_count" type="checkbox" id="{0}[{1}][enable_auto_count]" name="enable_auto_count"<?php $this->set_checked($values['enable_auto_count']); ?>> 
			<?php _e("Enable Auto Count", $this->plugin_slug); ?><span class="hint--top hint--info" data-hint="<?php _e("this is a beta feature and still being tested - your feedback is most welcome especially if you have a large numbers of posts or using a lot of fields.", $this->plugin_slug); ?>"><i class="dashicons dashicons-info"></i></span><br />
			<br /><em><?php _e("Currently only works for taxonomies, other fields are ignored.", $this->plugin_slug); ?></em><br />
			<br /><em><?php _e("Automatically update the search form based on user selection, hiding or disabling any unavaliable choices (eg, hiding all Tags that are not present in the current selected Category).", $this->plugin_slug); ?></em>
		</label>
	</p>
	<br />
</div>


