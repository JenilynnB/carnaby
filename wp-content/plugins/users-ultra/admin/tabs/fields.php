<?php 
$fields = get_option('usersultra_profile_fields');
ksort($fields);

global $xoouserultra;


$last_ele = end($fields);
$new_position = $last_ele['position']+1;

$meta_custom_value = "";
?>
<h3>
	<?php _e('Profile Fields Customizer','xoousers'); ?>
</h3>
<p>
	<?php _e('Organize profile fields, add custom fields to profiles, control privacy of each field, and more using the following customizer. You can drag and drop the fields to change the order in which they are displayed on profiles and the registration form.','xoousers'); ?>
</p>


<p >
<div class='user-ultra-success uultra-notification' id="fields-mg-reset-conf"><?php _e('Profile fields have been restored','xoousers'); ?></div>

</p>
<a href="#uultra-add-field-btn" class="button button-secondary"  id="uultra-add-field-btn"><i
	class="uultra-icon-plus"></i>&nbsp;&nbsp;<?php _e('Click here to add new field','xoousers'); ?>
</a>


<a href="#uultra-add-field-btn" class="button button-secondary user-ultra-btn-red"  id="uultra-restore-fields-btn"><i
	class="uultra-icon-plus"></i>&nbsp;&nbsp;<?php _e('Click here to restore default fields','xoousers'); ?>
</a> 

<div class="user-ultra-sect-second user-ultra-rounded" id="uultra-add-new-custom-field-frm" >

<table class="form-table uultra-add-form">

	

	<tr valign="top">
		<th scope="row"><label for="uultra_type"><?php _e('Type','xoousers'); ?> </label>
		</th>
		<td><select name="uultra_type" id="uultra_type">
				<option value="usermeta">
					<?php _e('Profile Field','xoousers'); ?>
				</option>
				<option value="separator">
					<?php _e('Separator','xoousers'); ?>
				</option>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('You can create a seperator or a usermeta (profile field)','xoousers'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="uultra_field"><?php _e('Editor / Input Type','xoousers'); ?>
		</label></th>
		<td><select name="uultra_field" id="uultra_field">
				<?php  foreach($xoouserultra->allowed_inputs as $input=>$label) { ?>
				<option value="<?php echo $input; ?>">
					<?php echo $label; ?>
				</option>
				<?php } ?>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('When user edit profile, this field can be an input (text, textarea, image upload, etc.)','xoousers'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="uultra_meta"><?php _e('Existing Meta Key / Field','xoousers'); ?>
		</label></th>
		<td><select name="uultra_meta" id="uultra_meta">
				<option value="">
					<?php _e('Choose a Meta Key','xoousers'); ?>
				</option>
				<optgroup label="--------------">
					<option value="1">
						<?php _e('New Custom Meta Key','xoousers'); ?>
					</option>
				</optgroup>
				<optgroup label="-------------">
					<?php
					$current_user = wp_get_current_user();
					if( $all_meta_for_user = get_user_meta( $current_user->ID ) ) {

					    ksort($all_meta_for_user);

					    foreach($all_meta_for_user as $user_meta => $array) {
					        ?>
					<option value="<?php echo $user_meta; ?>">
						<?php echo $user_meta; ?>
					</option>
					<?php
					    }
					}
					?>
				</optgroup>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('Choose from a predefined/available list of meta fields (usermeta) or skip this to define a new custom meta key for this field below.','xoousers'); ?>"></i>
		</td>
	</tr>

	

	<tr valign="top" >
		<th scope="row"><label for="uultra_meta_custom"><?php _e('New Custom Meta Key','xoousers'); ?>
		</label></th>
		<td><input name="uultra_meta_custom" type="text" id="uultra_meta_custom"
			value="<?php echo $meta_custom_value; ?>" class="regular-text" /> <i
			class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('Enter a custom meta key for this profile field if do not want to use a predefined meta field above. It is recommended to only use alphanumeric characters and underscores, for example my_custom_meta is a proper meta key.','xoousers'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="uultra_name"><?php _e('Label','xoousers'); ?> </label>
		</th>
		<td><input name="uultra_name" type="text" id="uultra_name"
			value="<?php if (isset($_POST['uultra_name']) && isset($this->errors) && count($this->errors)>0) echo $_POST['uultra_name']; ?>"
			class="regular-text" /> <i
			class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('Enter the label / name of this field as you want it to appear in front-end (Profile edit/view)','xoousers'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="uultra_tooltip"><?php _e('Tooltip Text','xoousers'); ?>
		</label></th>
		<td><input name="uultra_tooltip" type="text" id="uultra_tooltip"
			value="<?php if (isset($_POST['uultra_tooltip']) && isset($this->errors) && count($this->errors)>0) echo $_POST['uultra_tooltip']; ?>"
			class="regular-text" /> <i
			class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('A tooltip text can be useful for social buttons on profile header.','xoousers'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="uultra_social"><?php _e('This field is social','xoousers'); ?>
		</label></th>
		<td><select name="uultra_social" id="uultra_social">
				<option value="0">
					<?php _e('No','xoousers'); ?>
				</option>
				<option value="1">
					<?php _e('Yes','xoousers'); ?>
				</option>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('A social field can show a button with your social profile in the head of your profile. Such as Facebook page, Twitter, etc.','xoousers'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="uultra_can_edit"><?php _e('User can edit','xoousers'); ?>
		</label></th>
		<td><select name="uultra_can_edit" id="uultra_can_edit">
				<option value="1">
					<?php _e('Yes','xoousers'); ?>
				</option>
				<option value="0">
					<?php _e('No','xoousers'); ?>
				</option>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('Users can edit this profile field or not.','xoousers'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="uultra_allow_html"><?php _e('Allow HTML Content','xoousers'); ?>
		</label></th>
		<td><select name="uultra_allow_html" id="uultra_allow_html">
				<option value="0">
					<?php _e('No','xoousers'); ?>
				</option>
				<option value="1">
					<?php _e('Yes','xoousers'); ?>
				</option>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('If yes, users will be able to write HTML code in this field.','xoousers'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="uultra_can_hide"><?php _e('User can hide','xoousers'); ?>
		</label></th>
		<td><select name="uultra_can_hide" id="uultra_can_hide">
				<option value="1">
					<?php _e('Yes','xoousers'); ?>
				</option>
				<option value="0">
					<?php _e('No','xoousers'); ?>
				</option>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('Allow users to hide this profile field from public viewing or not. Selecting No will cause the field to always be publicly visible if you have public viewing of profiles enabled. Selecting Yes will give the user a choice if the field should be publicy visible or not. Private fields are not affected by this option.','xoousers'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="uultra_private"><?php _e('This field is private','xoousers'); ?>
		</label></th>
		<td><select name="uultra_private" id="uultra_private">
				<option value="0">
					<?php _e('No','xoousers'); ?>
				</option>
				<option value="1">
					<?php _e('Yes','xoousers'); ?>
				</option>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('Make this field Private. Only admins can see private fields.','xoousers'); ?>"></i>
		</td>
	</tr>


	<tr valign="top">
		<th scope="row"><label for="uultra_private"><?php _e('This field is required','xoousers'); ?>
		</label></th>
		<td><select name="uultra_required" id="uultra_required">
				<option value="0">
					<?php _e('No','xoousers'); ?>
				</option>
				<option value="1">
					<?php _e('Yes','xoousers'); ?>
				</option>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('Selecting yes will force user to provide a value for this field at registeration and edit profile. Registration or profile edits will not be accepted if this field is left empty.','xoousers'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="uultra_show_in_register"><?php _e('Show on Registration form','xoousers'); ?>
		</label></th>
		<td><select name="uultra_show_in_register" id="uultra_show_in_register">
				<option value="0">
					<?php _e('No','xoousers'); ?>
				</option>
				<option value="1">
					<?php _e('Yes','xoousers'); ?>
				</option>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('Show this field on the registration form? If you choose no, this field will be shown on edit profile only and not on the registration form. Most users prefer fewer fields when registering, so use this option with care.','xoousers'); ?>"></i>
		</td>
        
        
	</tr>
    
   

	<tr valign="top" class="uultra-icons-holder">
		<th scope="row"><label><?php _e('Icon for this field','xoousers'); ?> </label>
		</th>
		<td><label class="uultra-icons"><input type="radio" name="uultra_icon"
				value="0" /> <?php _e('None','xoousers'); ?> </label> 
				<?php foreach($this->fontawesome as $icon) { ?>
			<label class="uultra-icons"><input type="radio" name="uultra_icon"
				value="<?php echo $icon; ?>" />
                <i class="fa fa-<?php echo $icon; ?> uultra-tooltip3" title="<?php echo $icon; ?>"></i> </label>            <?php } ?>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"></th>
		<td>
          <div class="user-ultra-success uultra-notification" id="uultra-sucess-add-field"><?php _e('Sucess ','xoousers'); ?></div>
        <input type="submit" name="uultra-add" 	value="<?php _e('Submit New Field','xoousers'); ?>"
			class="button button-primary" id="uultra-btn-add-field-submit" /> 
            <input type="button"class="button button-secondary " id="uultra-close-add-field-btn"	value="<?php _e('Cancel','xoousers'); ?>" />
		</td>
	</tr>

</table>


</div>

<div class="user-ultra-sect user-ultra-rounded">

<p style="text-align:right">

<span id="loading-animation"> <img src="<?php echo xoousers_url?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp;<a href="#uultra-add-field-btn" class="button button-primary "  id="uultradmin-confirm-rearrange"><i
	class="uultra-icon-plus"></i>&nbsp;&nbsp;<?php _e('CLICK TO CONFIRM REARRANGE ','xoousers'); ?>
</a>
</p>

</div>


<!-- show customizer -->
<ul class="user-ultra-sect user-ultra-rounded" id="uu-fields-sortable" >
		<?php



		$i = 0;
		foreach($fields as $pos => $array) 
		{
		    extract($array); $i++;

		    if(!isset($required))
		        $required = 0;

		    if(!isset($fonticon))
		        $fonticon = '';
				
				
			if ($type == 'seperator' || $type == 'separator') {
			   
				$class = "separator";
				$class_title = "";
			} else {
			  
				$class = "profile-field";
				$class_title = "profile-field";
			}
		    ?>
            
          <li class="uultra-profile-fields-row <?php echo $class_title?>" id="<?php echo $pos; ?>">
            
            
            <div class="heading_title  <?php echo $class?>">
            
            <h3>
            <?php
			if (isset($array['name']) && $array['name'])
			{
			    echo  $array['name'];
			}
			?>
            
            <?php
			if ($type == 'separator') {
			    echo __(' - Separator','xoousers');
				
			} else {
			    echo __(' - Profile Field','xoousers');
				
			}
			?>
            
            </h3>
            
            
              <div class="options-bar">
             
               
             
             <p>
            
             
				<input type="submit" name="submit" value="<?php _e('Edit','xoousers'); ?>"						class="button uultra-btn-edit-field button-primary" data-edition="<?php echo $pos; ?>" /> <input type="button" value="<?php _e('Delete','xoousers'); ?>"	data-field="<?php echo $pos; ?>" class="button button-secondary uultra-delete-profile-field-btn" />
				</p>
            
             </div>
            
            
          

            </div>
            
             
             <div class="user-ultra-success uultra-notification" id="uultra-sucess-delete-fields-<?php echo $pos; ?>"><?php _e('Sucess! This field has been deleted ','xoousers'); ?></div>
            
           
        
          <!-- edit field -->
        <div class="user-ultra-sect-second uultra-fields-edition user-ultra-rounded"  id="uu-edit-fields-bock-<?php echo $pos; ?>">
	

				<p>
					<label for="uultra_<?php echo $pos; ?>_position"><?php _e('Position','xoousers'); ?>
					</label> <input name="uultra_<?php echo $pos; ?>_position"
						type="text" id="uultra_<?php echo $pos; ?>_position"
						value="<?php echo $pos; ?>" class="small-text" /> <i
						class="uultra_icon-question-sign uultra-tooltip2"
						title="<?php _e('Please use a unique position. Position lets you place the new field in the place you want exactly in Profile view.','xoousers'); ?>"></i>
				</p>

				<p>
					<label for="uultra_<?php echo $pos; ?>_type"><?php _e('Field Type','xoousers'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_type"
						id="uultra_<?php echo $pos; ?>_type">
						<option value="usermeta" <?php selected('usermeta', $type); ?>>
							<?php _e('Profile Field','xoousers'); ?>
						</option>
						<option value="separator" <?php selected('separator', $type); ?>>
							<?php _e('Separator','xoousers'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('You can create a separator or a usermeta (profile field)','xoousers'); ?>"></i>
				</p> 
				
				<?php if ($type != 'separator') { ?>

				<p class="uultra-inputtype">
					<label for="uultra_<?php echo $pos; ?>_field"><?php _e('Field Input','xoousers'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_field"
						id="uultra_<?php echo $pos; ?>_field">
						<?php
						global $xoouserultra;
						 foreach($xoouserultra->allowed_inputs as $input=>$label) { ?>
						<option value="<?php echo $input; ?>"
						<?php selected($input, $field); ?>>
							<?php echo $label; ?>
						</option>
						<?php } ?>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('When user edit profile, this field can be an input (text, textarea, image upload, etc.)','xoousers'); ?>"></i>
				</p>

				<p>
					<label for="uultra_<?php echo $pos; ?>_meta"><?php _e('Choose Meta Field','xoousers'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_meta"
						id="uultra_<?php echo $pos; ?>_meta">
						<option value="">
							<?php _e('Choose a user field','xoousers'); ?>
						</option>
						<?php
						$current_user = wp_get_current_user();
						if( $all_meta_for_user = get_user_meta( $current_user->ID ) ) {
						    ksort($all_meta_for_user);
						    foreach($all_meta_for_user as $user_meta => $user_meta_array) {

						        ?>
						<option value="<?php echo $user_meta; ?>"
						<?php selected($user_meta, $meta); ?>>
							<?php echo $user_meta; ?>
						</option>
						<?php
						    }
						}
						?>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Choose from a predefined/available list of meta fields (usermeta) or skip this to define a new custom meta key for this field below.','xoousers'); ?>"></i>
				</p>

				<p>
					<label for="uultra_<?php echo $pos; ?>_meta_custom"><?php _e('Custom Meta Field','xoousers'); ?>
					</label> <input name="uultra_<?php echo $pos; ?>C"
						type="text" id="uultra_<?php echo $pos; ?>_meta_custom"
						value="<?php if (!isset($all_meta_for_user[$meta])) echo $meta; ?>" />
					<i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Enter a custom meta key for this profile field if do not want to use a predefined meta field above. It is recommended to only use alphanumeric characters and underscores, for example my_custom_meta is a proper meta key.','xoousers'); ?>"></i>
				</p> <?php } ?>

				<p>
					<label for="uultra_<?php echo $pos; ?>_name"><?php _e('Label / Name','xoousers'); ?>
					</label> <input name="uultra_<?php echo $pos; ?>_name" type="text"
						id="uultra_<?php echo $pos; ?>_name" value="<?php echo $name; ?>" />
					<i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Enter the label / name of this field as you want it to appear in front-end (Profile edit/view)','xoousers'); ?>"></i>
				</p>

			<?php if ($type != 'separator' ) { ?>

				<?php if ($social == 1) { ?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_tooltip"><?php _e('Tooltip Text','xoousers'); ?>
					</label> <input name="uultra_<?php echo $pos; ?>_tooltip" type="text"
						id="uultra_<?php echo $pos; ?>_tooltip"
						value="<?php echo $tooltip; ?>" /> <i
						class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('A tooltip text can be useful for social buttons on profile header.','xoousers'); ?>"></i>
				</p> <?php } ?> <?php if ($field != 'password') { ?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_social"><?php _e('This field is social','xoousers'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_social"
						id="uultra_<?php echo $pos; ?>_social">
						<option value="0" <?php selected(0, $social); ?>>
							<?php _e('No','xoousers'); ?>
						</option>
						<option value="1" <?php selected(1, $social); ?>>
							<?php _e('Yes','xoousers'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('A social field can show a button with your social profile in the head of your profile. Such as Facebook page, Twitter, etc.','xoousers'); ?>"></i>
				</p> <?php } ?> <?php 
				if(!isset($can_edit))
				    $can_edit = '1';
				?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_can_edit"><?php _e('User can edit','xoousers'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_can_edit"
						id="uultra_<?php echo $pos; ?>_can_edit">
						<option value="1" <?php selected(1, $can_edit); ?>>
							<?php _e('Yes','xoousers'); ?>
						</option>
						<option value="0" <?php selected(0, $can_edit); ?>>
							<?php _e('No','xoousers'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Users can edit this profile field or not.','xoousers'); ?>"></i>
				</p> <?php if (!isset($array['allow_html'])) { 
				    $allow_html = 0;
				} ?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_allow_html"><?php _e('Allow HTML','xoousers'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_allow_html"
						id="uultra_<?php echo $pos; ?>_allow_html">
						<option value="0" <?php selected(0, $allow_html); ?>>
							<?php _e('No','xoousers'); ?>
						</option>
						<option value="1" <?php selected(1, $allow_html); ?>>
							<?php _e('Yes','xoousers'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('If yes, users will be able to write HTML code in this field.','xoousers'); ?>"></i>
				</p> <?php if ($private != 1) { 
				     
				    if(!isset($can_hide))
				        $can_hide = '0';
				    ?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_can_hide"><?php _e('User can hide','xoousers'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_can_hide"
						id="uultra_<?php echo $pos; ?>_can_hide">
						<option value="1" <?php selected(1, $can_hide); ?>>
							<?php _e('Yes','xoousers'); ?>
						</option>
						<option value="0" <?php selected(0, $can_hide); ?>>
							<?php _e('No','xoousers'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Allow users to hide this profile field from public viewing or not. Selecting No will cause the field to always be publicly visible if you have public viewing of profiles enabled. Selecting Yes will give the user a choice if the field should be publicy visible or not. Private fields are not affected by this option.','xoousers'); ?>"></i>
				</p> <?php } ?> <?php 
				if(!isset($private))
				    $private = '0';
				?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_private"><?php _e('This field is private','xoousers'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_private"
						id="uultra_<?php echo $pos; ?>_private">
						<option value="0" <?php selected(0, $private); ?>>
							<?php _e('No','xoousers'); ?>
						</option>
						<option value="1" <?php selected(1, $private); ?>>
							<?php _e('Yes','xoousers'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Make this field Private. Only admins can see private fields.','xoousers'); ?>"></i>
				</p> <?php 
				if(!isset($required))
				    $required = '0';
				?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_required"><?php _e('This field is Required','xoousers'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_required"
						id="uultra_<?php echo $pos; ?>_required">
						<option value="0" <?php selected(0, $required); ?>>
							<?php _e('No','xoousers'); ?>
						</option>
						<option value="1" <?php selected(1, $required); ?>>
							<?php _e('Yes','xoousers'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Selecting yes will force user to provide a value for this field at registeration and edit profile. Registration or profile edits will not be accepted if this field is left empty.','xoousers'); ?>"></i>
				</p> <?php } ?> <?php

				/* Show Registration field only when below condition fullfill
				 1) Field is not private
				2) meta is not for email field
				3) field is not fileupload */
				if(!isset($private))
				    $private = 0;

				if(!isset($meta))
				    $meta = '';

				if(!isset($field))
				    $field = '';


				if($type == 'separator' ||  ($private != 1 && $meta != 'user_email' && $field != 'fileupload'))
				{
				    if(!isset($show_in_register))
				        $show_in_register= 0;
						
					 if(!isset($show_in_widget))
				        $show_in_widget= 0;
				    ?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_show_in_register"><?php _e('Show on Registration Form','xoousers'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_show_in_register"
						id="uultra_<?php echo $pos; ?>_show_in_register">
						<option value="0" <?php selected(0, $show_in_register); ?>>
							<?php _e('No','xoousers'); ?>
						</option>
						<option value="1" <?php selected(1, $show_in_register); ?>>
							<?php _e('Yes','xoousers'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Show this profile field on the registration form','xoousers'); ?>"></i>
				</p>
                
              
                
                 <?php } ?>
			<?php if ($type != 'seperator' || $type != 'separator') { ?>

		  <?php if (in_array($field, array('select','radio','checkbox')))
				 {
				    $show_choices = null;
				} else { $show_choices = 'uultra-hide';
				} ?>

				<p class="uultra-choices <?php echo $show_choices; ?>">
					<label for="uultra_<?php echo $pos; ?>_choices"
						style="display: block"><?php _e('Available Choices','xoousers'); ?> </label>
					<textarea name="uultra_<?php echo $pos; ?>_choices" type="text" id="uultra_<?php echo $pos; ?>_choices" class="large-text"><?php if (isset($array['choices'])) echo trim($choices); ?></textarea>
					<i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Enter one choice per line please. The choices will be available for front end user to choose from.','xoousers'); ?>"></i>
				</p> <?php if (!isset($array['predefined_loop'])) $predefined_loop = 0; ?>

				<p class="uultra_choices <?php echo $show_choices; ?>">
					<label for="uultra_<?php echo $pos; ?>_predefined_options" style="display: block"><?php _e('Enable Predefined Choices','xoousers'); ?>
					</label> 
                    <select name="uultra_<?php echo $pos; ?>_predefined_options"id="uultra_<?php echo $pos; ?>_predefined_options">
						<option value="0" <?php selected(0, $predefined_loop); ?>>
							<?php _e('None','xoousers'); ?>
						</option>
						<option value="countries" <?php selected('countries', $predefined_loop); ?>>
							<?php _e('List of Countries','xoousers'); ?>
						</option>
                        
                        <option value="age" <?php selected('age', $predefined_loop); ?>>
							<?php _e('Age','xoousers'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('You can enable a predefined filter for choices. e.g. List of countries It enables country selection in profiles and saves you time to do it on your own.','xoousers'); ?>"></i>
				</p>

				<p>

					<span style="display: block; font-weight: bold; margin: 0 0 10px 0"><?php _e('Field Icon:','xoousers'); ?>&nbsp;&nbsp;
						<?php if ($icon) { ?><i class="uultra-icon-<?php echo $icon; ?>"></i>
						<?php } else { _e('None','xoousers'); 
						} ?>&nbsp;&nbsp; <a href="#changeicon"
						class="button button-secondary uultra-inline-icon-uultra-edit"><?php _e('Change Icon','xoousers'); ?>
					</a> </span> <label class="uultra-icons">
                    
                    <input type="radio"	name="uultra_<?php echo $pos; ?>_icon" value=""
						<?php checked('', $fonticon); ?> /> <?php _e('None','xoousers'); ?> </label>

					<?php foreach($this->fontawesome as $fonticon) { ?>
					  
                      
                      <label class="uultra-icons"><input type="radio"	name="uultra_<?php echo $pos; ?>_icon" value="<?php echo $fonticon; ?>"
						<?php checked($fonticon, $icon); ?> />
                        <i class="fa fa-<?php echo $fonticon; ?> uultra-tooltip3"
						title="<?php echo $fonticon; ?>"></i> </label>
                        
                        
					<?php } ?>

				</p>
				<div class="clear"></div> <?php } ?>


  <div class="user-ultra-success uultra-notification" id="uultra-sucess-fields-<?php echo $pos; ?>"><?php _e('Sucess ','xoousers'); ?></div>
				<p>
                
               
                 
				<input type="button" name="submit"	value="<?php _e('Update','xoousers'); ?>"						class="button button-primary uultra-btn-submit-field"  data-edition="<?php echo $pos; ?>" /> 
                   <input type="button" value="<?php _e('Cancel','xoousers'); ?>"
						class="button button-secondary uultra-btn-close-edition-field" data-edition="<?php echo $pos; ?>" />
				</p>
			
        
        </div>

       </li>







		<?php } ?>

  </ul>
  
           <script type="text/javascript">  
		
		      var custom_fields_del_confirmation ="<?php _e('Are you totally sure that you want to delete this field?','xoousers'); ?>";
			  
			   var custom_fields_reset_confirmation ="<?php _e('Are you totally sure that you want to restore the default fields?','xoousers'); ?>";
		 
		 </script>
         
        