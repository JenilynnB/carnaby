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
	
	<p class="description"><?php _e("A custom template should be selected <em>(see settings panel)</em>, enabling Ajax without doing so will likely produce unexpected results.", $this->plugin_slug ); ?></p>
	<hr />
	<div class="sf_ajax">	
		<p class="description-inline">
			
			<label for="{0}[{1}][use_ajax_toggle]">
				<input class="checkbox use_ajax_toggle" type="checkbox" id="{0}[{1}][use_ajax_toggle]" name="use_ajax_toggle"<?php $this->set_checked($values['use_ajax_toggle']); ?>> 
				<?php _e("Load results using Ajax?", $this->plugin_slug); ?><br />
				<input type="hidden"  name="use_ajax_toggle" id="{0}[{1}][use_ajax_toggle]" class="use_ajax_toggle_hidden"  value="1" disabled="disabled" />
			</label>
		</p>
		<div class="widget-content ajax-selectors">
			<p>
				<label for="{0}[{1}][auto_submit]">
					<input class="checkbox auto_submit" type="checkbox" id="{0}[{1}][auto_submit]" name="auto_submit"<?php $this->set_checked($values['auto_submit']); ?>> 
					<?php _e("Auto submit form?", $this->plugin_slug); ?><span class="hint--top hint--info" data-hint="<?php _e("Update the results whenever a user changes a value - no need for a submit button", $this->plugin_slug); ?>"><i class="dashicons dashicons-info"></i></span><br />
					<input type="hidden"  name="auto_submit" id="{0}[{1}][auto_submit]" class="auto_submit_hidden"  value="1" disabled="disabled" />
				</label>
			</p>
			<p class="item-container">
				<label for="{0}[{1}][ajax_target]"><?php _e("Content selector:", $this->plugin_slug); ?><span class="hint--top hint--info" data-hint="<?php _e("the ID or class of the container which your results are loaded in to", $this->plugin_slug); ?>"><i class="dashicons dashicons-info"></i></span><br />
				<input class="ajax_target" id="{0}[{1}][ajax_target]" name="ajax_target" type="text" value="<?php echo esc_attr($values['ajax_target']); ?>"></label>
				<br /><em><?php _e("This should be an ID, ie - <code>#content</code> - or a unique class selector, ie - <code>.content-container</code>.", $this->plugin_slug); ?></em>
				<input type="hidden"  name="ajax_target" id="{0}[{1}][ajax_target]" class="ajax_target_hidden"  value="<?php echo $values['ajax_target']; ?>" disabled="disabled" />
			</p>
			<p class="item-container">
				<label for="{0}[{1}][ajax_links_selector]"><?php _e("Pagination selector:", $this->plugin_slug); ?><span class="hint--top hint--info" data-hint="<?php _e("to enable Ajax on your pagination links you must put the CSS selector here", $this->plugin_slug); ?>"><i class="dashicons dashicons-info"></i></span><br />
				<input class="ajax_links_selector" id="{0}[{1}][ajax_links_selector]" name="ajax_links_selector" type="text" value="<?php echo esc_attr($values['ajax_links_selector']); ?>"></label>
				<br /><em><?php _e("This must be a selector targeting all pagination links on your page - ie, <code>.nav-links a</code><!--<br />Your pagination links must be contained within the content selector so they are updated with the Ajax request.-->", $this->plugin_slug); ?></em>
				<input type="hidden"  name="ajax_links_selector" id="ajax_links_selector" class="ajax_links_selector_hidden"  value="<?php echo $values['ajax_links_selector']; ?>" disabled="disabled" />
			</p>
			<div class="clear"></div>
		</div>
	</div>
	<div class="clear"></div>
	
	<!--<br /><strong><?php _e("Defaults", $this->plugin_slug ); ?></strong>
	<p class="">
		<?php _e("Sort Results By: ", $this->plugin_slug ); ?><br /><br />
		<?php _e("Sort Results By: ", $this->plugin_slug ); ?><br />
	</p>-->
	
</div>


