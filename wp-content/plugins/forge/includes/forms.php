<?php 

//Readonly field
function forge_field_readonly($args, $value = ''){
	$description = isset($args['caption']) ? esc_attr($args['caption']) : '';
	$output = '<div class="forge-field-readonly">'.$description.'</div>';
	return $output;
}


//Standard text field
function forge_field_text($args, $value = ''){
	$value = esc_attr($value);
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	$placeholder = (isset($args['placeholder']) && $args['placeholder'] != '') ? ' placeholder="'.esc_attr($args['placeholder']).'"': '';
	$width = (isset($args['width']) && $args['width'] != '') ? ' style="width:'.esc_attr($args['width']).'"': '';
	
	//Add live class
	$live_class = isset($args['live_field']) ? esc_attr($args['live_field']) : '';
	
	$output = '<input type="text" class="'.$live_class.'" value="'.$value.'" name="forge_field_'.$name.'" id="forge_field_'.$name.'"'.$placeholder.$width.' autocomplete="off"/>';
	
	return $output;
}


//Numeric field
function forge_field_number($args, $value = ''){
	$value = esc_attr($value);
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	$placeholder = (isset($args['placeholder']) && $args['placeholder'] != '') ? ' placeholder="'.esc_attr($args['placeholder']).'"': '';
	$width = (isset($args['width']) && $args['width'] != '') ? ' style="width:'.esc_attr($args['width']).'"': '';
	
	$min = (isset($args['min']) && $args['min'] != '') ? esc_attr($args['min']) : 1;
	$max = (isset($args['max']) && $args['max'] != '') ? esc_attr($args['max']) : 99;
	
	//Add live class
	$live_class = isset($args['live_field']) ? esc_attr($args['live_field']) : '';
	
	$output = '<input type="number" class="'.$live_class.'" value="'.$value.'" name="forge_field_'.$name.'" id="forge_field_'.$name.'"'.$placeholder.$width.' min="'.$min.'" max="'.$max.'" autocomplete="off"/>';
	
	return $output;
}


//Slider field
function forge_field_slider($args, $value = ''){
	$value = esc_attr($value);
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	$placeholder = (isset($args['placeholder']) && $args['placeholder'] != '') ? ' placeholder="'.esc_attr($args['placeholder']).'"': '';
	$width = (isset($args['width']) && $args['width'] != '') ? ' style="width:'.esc_attr($args['width']).'"': '';
	
	$min = (isset($args['min']) && $args['min'] != '') ? esc_attr($args['min']) : 0;
	$max = (isset($args['max']) && $args['max'] != '') ? esc_attr($args['max']) : 99;
	$step = (isset($args['step']) && $args['step'] != '') ? esc_attr($args['step']) : 1;
	if($value == ''){
		$value = (isset($args['default']) && $args['default'] != '') ? esc_attr($args['default']) : $min;
	}
	
	//Add live class
	$live_class = isset($args['live_field']) ? esc_attr($args['live_field']) : '';
	
	$output = '';
	$output .= '<div class="forge-field-slider">';
	$output .= '<div class="forge-field-slider-textbox">';
	$output .= '<input type="number" class="forge-field-slider-value '.$live_class.'" value="'.$value.'" name="forge_field_'.$name.'" min="'.$min.'" max="'.$max.'" step="'.$step.'" id="forge_field_'.$name.'"'.$placeholder.' autocomplete="off"/>';
	$output .= '</div>';
	$output .= '<div class="forge-field-slider-content">';
	$output .= '<div class="forge-field-slider-bar" data-min="'.$min.'" data-max="'.$max.'" data-step="'.$step.'" data-value="'.$value.'"></div>';
	$output .= '</div>';
	$output .= '</div>';
	
	return $output;
}

//Standard text field
function forge_field_editor($args, $value = ''){
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	$placeholder = (isset($args['placeholder']) && $args['placeholder'] != '') ? ' placeholder="'.esc_attr($args['placeholder']).'"': '';
		
	remove_all_actions('media_buttons', 999999);
	remove_all_actions('media_buttons_context', 999999);
	remove_all_filters('mce_external_plugins', 999999);
	remove_all_filters('mce_buttons', 999999);
	
	$editor_id = 'forge_field_'.$name;
	
	$output = '<div class="forge-editor">';
	ob_start();
	
	wp_editor($value, $editor_id, array(
	'media_buttons' => isset($field['media_buttons']) ? $field['media_buttons'] : true,
	'textarea_rows' => isset($field['rows']) ? $field['rows'] : 12,
	'wpautop' => true)); 
	
	?>
	<script type="text/javascript">
		jQuery(function(){
			var editor_id = '<?php echo $editor_id; ?>';
			var base_editor = tinyMCEPreInit.mceInit['forgebaseeditor'];
			var base_quicktags = tinyMCEPreInit.qtInit['forgebaseeditor'];
			var new_editor  = null;
		
			if(typeof tinymce != 'undefined') {
				new_editor = tinymce.extend({}, base_editor);
				new_editor.selector = '#' + editor_id;
				new_editor.body_class = new_editor.body_class.replace('forgebaseeditor', editor_id);
				tinyMCEPreInit.mceInit[editor_id] = new_editor;
				
				new_quicktags = tinymce.extend({}, base_quicktags);
				new_quicktags.selector = '#' + editor_id;
				new_quicktags.id = editor_id;
				tinyMCEPreInit.qtInit[editor_id] = new_quicktags;
				
				tinymce.init(new_editor);
			}
			if(typeof quicktags != 'undefined') {                
				quicktags({id : editor_id});
				QTags._buttonsInit();
			}
			window.wpActiveEditor = editor_id;
		});
	</script>
	<?php
	
	$output .= ob_get_clean();
	$output .= '</div>';
	
	return $output;
}


//Standard text field
function forge_field_textarea($args, $value = ''){
	$value = esc_attr($value);
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	$placeholder = (isset($args['placeholder']) && $args['placeholder'] != '') ? ' placeholder="'.esc_attr($args['placeholder']).'"': '';
	
	$output = '<textarea class="forge-builder-expand" rows="10" name="forge_field_'.$name.'" id="forge_field_'.$name.'"'.$placeholder.'>'.$value.'</textarea>';
	
	return $output;
}


//Standard text field
function forge_field_code($args, $value = ''){
	$value = esc_attr($value);
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	$placeholder = (isset($args['placeholder']) && $args['placeholder'] != '') ? ' placeholder="'.esc_attr($args['placeholder']).'"': '';
	/*wp_enqueue_script('forge_script_codemirror');
	wp_enqueue_script('forge_script_codemirror_'.$code);
	wp_enqueue_script('forge_script_editor');
	wp_enqueue_style('forge_style_codemirror');*/
	$output = '<textarea class="forge-builder-expand forge-field-code" rows="15" name="forge_field_'.$name.'" id="forge_field_'.$name.'"'.$placeholder.'>'.$value.'</textarea>';
	
	return $output;
}


//Color Picker field
function forge_field_border($args, $value){
	$value = esc_attr($value);
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	
	$border = explode(' ', $value);
	$border_width = isset($border[0]) ? intval($border[0]) : '0';
	$border_style = isset($border[1]) ? $border[1] : 'solid';
	$border_color = isset($border[2]) ? $border[2] : '#666666';
	
	//Live edit fields
	$live_class = isset($args['live_field']) ? esc_attr($args['live_field']) : '';
	
	$output = '';
	$output .= '<div class="forge-field-border">';
	
	$output .= '<input type="hidden" class="forge-field-border-value '.$live_class.'" value="'.$value.'" name="forge_field_'.$name.'" id="forge_field_'.$name.'"/>';
	
	//Width
	$output .= '<input type="number" class="forge-field-border-width" value="'.$border_width.'" min="0" max="999" name="forge_field_'.$name.'_width" id="forge_field_'.$name.'_width"/>';
	
	//Style
	$output .= '<select class="forge-field-border-style" name="forge_field_'.$name.'_style" id="forge_field_'.$name.'_style">';
	$output .= '<option value="solid" '.selected($border_style, 'solid', false).'>'.__('Solid', 'forge').'</option>';
	$output .= '<option value="dashed" '.selected($border_style, 'dashed', false).'>'.__('Dashed', 'forge').'</option>';
	$output .= '<option value="dotted" '.selected($border_style, 'dotted', false).'>'.__('Dotted', 'forge').'</option>';
	$output .= '<option value="double" '.selected($border_style, 'double', false).'>'.__('Double', 'forge').'</option>';
	$output .= '<option value="groove" '.selected($border_style, 'groove', false).'>'.__('Groove', 'forge').'</option>';
	$output .= '<option value="ridge" '.selected($border_style, 'ridge', false).'>'.__('Ridge', 'forge').'</option>';
	$output .= '<option value="inset" '.selected($border_style, 'inset', false).'>'.__('Inset', 'forge').'</option>';
	$output .= '<option value="outset" '.selected($border_style, 'outset', false).'>'.__('Outset', 'forge').'</option>';
	$output .= '</select>';
	
	//Color
	$output .= '<input type="text" class="forge-field-border-color forge-field-color forge-field-color-hex" value="'.$border_color.'" name="forge_field_'.$name.'_color" id="forge_field_'.$name.'_color" maxlength="7"/>';
	$output .= '<div class="forge-field-color-preview" id="forge_field_'.$name.'_color_preview" style="background:'.$border_color.';"></div>';
	
	$output .= '<div class="forge-clear"></div>';
	$output .= '</div>';
	return $output;
}



//Color Picker field
function forge_field_color($args, $value){
	$value = esc_attr($value);
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	$placeholder = (isset($args['placeholder']) && $args['placeholder'] != '') ? ' placeholder="'.esc_attr($args['placeholder']).'"': '';
	
	//Live edit fields
	$live_class = isset($args['live_field']) ? esc_attr($args['live_field']) : '';
	
	$output = '';
	//Contains the value to be stored
	//Contains the actual color
	$output .= '<input type="text" class="forge-field-color forge-field-color-hex '.$live_class.'" value="'.forge_color($value).'" name="forge_field_'.$name.'_value" id="forge_field_'.$name.'_value"'.$placeholder.' maxlength="7"/>';
	$output .= '<div class="forge-field-color-preview" id="forge_field_'.$name.'_preview" style="background:'.forge_color($value).';"></div>';
	$output .= '<input type="text" class="forge-field-color-value" value="'.$value.'" name="forge_field_'.$name.'" id="forge_field_'.$name.'" style="display:none;"/>';
	$output .= '<div class="forge-clear"></div>';
	return $output;
}


//Dropdown list field
function forge_field_list($args, $value = ''){
	if(!isset($args['choices'])) return;
	$value = esc_attr($value);
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	
	//Add live class
	$live_class = isset($args['live_field']) ? esc_attr($args['live_field']) : '';
	
	$output = '<select class="forge-field-list'.$live_class.'" name="forge_field_'.$name.'" id="forge_field_'.$name.'">';
	if(sizeof($args['choices']) > 0)
		foreach($args['choices'] as $list_key => $list_value){
			$output .= '<option value="'.esc_attr($list_key).'" '.selected($value, $list_key, false).'>'.esc_attr($list_value).'</option>';
		}
	$output .= '</select>';
	
	return $output;
}


//Dropdown list field
function forge_field_buttonlist($args, $value = ''){
	if(!isset($args['choices'])) return;
	$value = esc_attr($value);
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	$columns = isset($args['columns']) ? intval($args['columns']) : '4';
	$size = isset($args['size']) ? esc_attr($args['size']) : '20px';
	
	//Add live class
	$live_class = isset($args['live_field']) ? esc_attr($args['live_field']) : '';
	
	$output = '<div class="forge-buttonlist forge-buttonlist-columns-'.$columns.'" name="'.$name.'" id="forge_field_'.$name.'">';
	if(sizeof($args['choices']) > 0){
		foreach($args['choices'] as $list_key => $list_value){
			$selected = '';
			$checked = '';
			if($list_key == $value) {
				$checked = ' selected="selected"';
				$selected = ' forge-buttonlist-selected';
			}
			$output .= '<label class="forge-buttonlist-item'.$selected.'" style="font-size:'.$size.';" for="forge_field_'.$name.'_'.$list_key.'">';
			$output .= $list_value;
			$output .= '<input type="radio" class="'.$live_class.'" name="forge_field_'.$name.'" id="forge_field_'.$name.'_'.$list_key.'" value="'.$list_key.'" '.$selected.'/>';        
			$output .= '</label>';
		}
	}
	$output .= '</div>';
	
	return $output;
}


//Standard image field
function forge_field_image($args, $value = ''){
	$value = esc_attr($value);
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	
	//Add live class
	$live_class = isset($args['live_field']) ? esc_attr($args['live_field']) : '';
	
	$empty_class = '';
	if($value == ''){
		$empty_class = ' forge-image-field-empty';
	}
	
	$output = '<div class="forge-image-field'.$empty_class.'">';
	//Image
	$output .= '<div class="forge-image-preview">';
	if($value != ''){
		$output .= '<img src="'.forge_image_url($value).'"/>';
	}
	$output .= '</div>';
	
	$output .= '<div class="forge-image-field-placeholder">'.__('No image selected', 'forge').'</div>';
	
	//Custom URL control
	$url_value = '';
	if(!is_numeric($value) && $value != ''){
		$url_value = $value;
	}
	$output .= '<div class="forge-image-field-url">';
	$output .= '<input class="forge-image-input-url" type="text" value="'.$url_value.'" placeholder="http://"/>';
	$output .= '<div class="forge-image-field-button forge-image-save">'.__('Save Custom URL', 'forge').'</div>';
	$output .= '<div class="forge-image-field-button forge-image-cancel">'.__('Cancel', 'forge').'</div>';
	$output .= '</div>';
	
	//Image selection controls
	$output .= '<div class="forge-image-field-controls">';
	$output .= '<div class="forge-image-field-button forge-image-choose">'.__('Choose', 'forge').'</div>';
	$output .= '<div class="forge-image-field-button forge-image-url">'.__('From URL', 'forge').'</div>';
	$output .= '<div class="forge-image-field-button forge-image-remove">X</div>';
	$output .= '<input class="forge-image-input-value '.$live_class.'" type="hidden" value="'.$value.'" name="forge_field_'.$name.'" id="forge_field_'.$name.'"/>';
	$output .= '</div>';
	
	$output .= '</div>';
	
	return $output;
}


//Standard gallery field
function forge_field_gallery($args, $value = ''){
	$value = esc_attr($value);
	$images = array_filter(explode(',', esc_attr($value)));
	
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	
	//Add live class
	$live_class = isset($args['live_field']) ? esc_attr($args['live_field']) : '';
	
	$empty_class = '';
	$output = '<div class="forge-gallery-field">';
	//Image
	$output .= '<div class="forge-gallery-images">';
	if($images){
		foreach($images as $current_image){
			if(trim($current_image) != ''){
				$output .= '<div class="forge-gallery-image" data-image="'.esc_attr($current_image ).'">';
				$output .= '<img src="'.forge_image_url($current_image, 'thumbnail').'"/>';
				$output .= '<span class="forge-gallery-remove">'.__('Remove', 'forge').'</span>';
				$output .= '</div>';
			}
		}
	}
	$output .= '<div class="forge-gallery-add">+</div>';
	$output .= '</div>';
	$output .= '<input class="forge-gallery-input-value '.$live_class.'" type="hidden" value="'.$value.'" name="forge_field_'.$name.'" id="forge_field_'.$name.'"/>';
	$output .= '</div>';
	
	
	return $output;
}


//Standard check field
function forge_field_checkbox($args, $value = ''){
	$value = esc_attr($value);
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	$description = isset($args['caption']) ? esc_attr($args['caption']) : '';
	$placeholder = (isset($args['placeholder']) && $args['placeholder'] != '') ? ' placeholder="'.esc_attr($args['placeholder']).'"': '';
	
	$checked = '';
	if($value == '1'){
		$checked = ' forge-field-checkbox-checked';
	}else{
		$value = 0;
	}
	
	//Add live class
	$live_class = isset($args['live_field']) ? esc_attr($args['live_field']) : '';
	
	$output = '<div class="forge-field-checkbox '.$checked.'">';
	// $output .= '<input class="'.$live_class.'" type="checkbox" value="1" name="forge_field_'.$name.'" id="forge_field_'.$name.'" '.checked($value, '1', false).'/> '.$description;
	
	$output .= '<input class="'.$live_class.' forge-field-checkbox-value" type="hidden" value="'.$value.'" name="forge_field_'.$name.'" id="forge_field_'.$name.'_0" '.checked($value, '0', false).'/> '.$description;
	$output .= '</div>';
	
	return $output;
}


//Yes/No radio selection field
function forge_field_yesno($name, $value, $args = null){
	$checked_yes = '';
	$checked_no = ' checked';
	if($value == '1'){
		$checked_yes = ' checked';
		$checked_no = '';
	}
	$output = '';
	$output .= '<label for="'.$name.'_yes">';
	$output .= '<input type="radio" name="forge_field_'.$name.'" id="forge_field_'.$name.'_yes" value="1" '.$checked_yes.'/>'; 
	$output .= __('Yes', 'forge').'</label>';
	$output .= '&nbsp;&nbsp;&nbsp;&nbsp;';
	
	$output .= '<label for="'.$name.'_no">';
	$output .= '<input type="radio" name="forge_field_'.$name.'" id="forge_field_'.$name.'_no" value="0" '.$checked_no.'/>'; 
	$output .= __('No', 'forge').'</label>';
	return $output;
}



//Icon list selection
function forge_field_iconlist($args, $value = ''){
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	$description = isset($args['description']) ? esc_attr($args['description']) : '';
	$placeholder = (isset($args['placeholder']) && $args['placeholder'] != '') ? ' placeholder="'.esc_attr($args['placeholder']).'"': '';
	
	//Add live class
	$live_class = isset($args['live_field']) ? esc_attr($args['live_field']) : '';
	
	$output = '<div id="forge_field_'.$name.'_wrap" class="forge-iconlist">';
	
	$output .= '<label class="forge-iconlist-item" for="forge_field_'.$name.'_0"> ';
	$output .= '<input type="radio" class="'.$live_class.'" name="forge_field_'.$name.'" id="forge_field_'.$name.'_0" value=""/>';        
	$output .= '</label>';  
			
	$list = forge_metadata_icons();
	foreach($list as $library_key => $library_value) {
		$output .= '<div class="forge-iconlist-heading">'.$library_value['name'].'</div>';
		foreach($library_value['icons'] as $list_key => $list_value) {
			$checked = '';
			$selected = '';
			if(strcmp($library_key.'-'.$list_key, $value) == 0) {
				$checked = ' checked="checked"';
				$selected = ' forge-iconlist-selected';
			}
			$output .= '<label class="forge-iconlist-item'.$selected.'" style="font-family:\'forge-'.$library_key.'\';" for="forge_field_'.$name.'_'.htmlentities($library_key.'-'.$list_key).'">';
			if($list_key == '0') $output .= ' '; else $output .= $list_key;
			$output .= '<input type="radio" class="'.$live_class.'" name="forge_field_'.$name.'" id="forge_field_'.$name.'_'.htmlentities($library_key.'-'.$list_key).'" value="'.htmlentities($library_key.'-'.$list_key).'" '.$checked.'/>';        
			$output .= '</label>';        
		}
	}
	$output .= '</div>';
	return $output;
}


//Uploader using Media Library
function forge_field_upload($name, $value, $args = null, $post = null) {
	if(isset($args['placeholder'])) $field_placeholder = ' placeholder="'.$args['placeholder'].'"'; else $field_placeholder = '';		
	if(stripslashes($value) != '')
		$image = stripslashes($value);
	elseif(defined('CPOTHEME_CORE_URL'))
		$image = CPOTHEME_CORE_URL.'/images/noimage.jpg';
	else
		$image = get_template_directory_uri().'/core/images/noimage.jpg';
	
	$output = '<input class="upload_field" type="upload" value="'.stripslashes($value).'" name="'.$name.'" id="forge_field_'.$name.'-field"/>';
	$output .= '<input class="upload_button" type="button" value="'.__('Upload', 'forge').'" name="'.$name.'" id="forge_field_'.$name.'-button"/>';
	$output .= '<img class="upload_preview" id="forge_field_'.$name.'-preview" src="'.$image.'"/>';
	return $output;	    
}


//Date picker field
function forge_field_date($name, $value, $args = null){
	if(isset($args['placeholder'])) $field_placeholder = ' placeholder="'.$args['placeholder'].'"'; else $field_placeholder = '';
	if(isset($args['autocomplete'])) $field_autocomplete = ' autocomplete="'.$args['placeholder'].'"'; else $field_autocomplete = ' autocomplete="off"';
	$output = '<input type="text" class="cpothemes-dateselector" value="'.stripslashes($value).'" name="'.$name.'" id="forge_field_'.$name.'"'.$field_placeholder.$field_autocomplete.'/>';
	return $output;
}


//Margins text field
function forge_field_margins($args, $value = ''){
	
	$margin_top = isset($value['top']) ? esc_attr($value['top']) : '';
	$margin_left = isset($value['left']) ? esc_attr($value['left']) : '';
	$margin_right = isset($value['right']) ? esc_attr($value['right']) : '';
	$margin_bottom = isset($value['bottom']) ? esc_attr($value['bottom']) : '';
	
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	$placeholder = (isset($args['placeholder']) && $args['placeholder'] != '') ? ' placeholder="'.esc_attr($args['placeholder']).'"': '';
	
	//Live edit fields
	$live_selector = isset($args['live']['selector']) ? ' data-live-selector="'.esc_attr($args['live']['selector']).'"' : '';
	$live_class = isset($args['live']['selector']) ? ' forge-live-field' : '';
	
	
	$output = '';
	
	$output .= '<div class="forge-margins">';
	
	$output .= '<div class="forge-margins-row forge-builder-form-field" data-live-property="css" data-live-attribute="padding-top" data-live-format="" '.$live_selector.'>';
	$output .= '<input type="text" value="'.$margin_top.'" class="forge-margins-field'.$live_class.'" name="forge_field_'.$name.'[top]" id="forge_field_'.$name.'_top"'.$placeholder.' autocomplete="off"/>';
	$output .= '</div>';
	
	$output .= '<div class="forge-margins-row">';
	
	$output .= '<div class="forge-margins-cell forge-builder-form-field" data-live-property="css" data-live-attribute="padding-left" data-live-format="" '.$live_selector.'>';
	$output .= '<input type="text" value="'.$margin_left.'" class="forge-margins-field'.$live_class.'" name="forge_field_'.$name.'[left]" id="forge_field_'.$name.'_left"'.$placeholder.' autocomplete="off"/>';
	$output .= '</div>';
	
	$output .= '<div class="forge-margins-cell forge-margins-cell-center">';
	$output .= '<input type="text" class="forge-margins-control" autocomplete="off" tabindex="-1" placeholder="'.__('All', 'forge').'"/>';
	$output .= '</div>';
	
	$output .= '<div class="forge-margins-cell forge-builder-form-field" data-live-property="css" data-live-attribute="padding-right" data-live-format="" '.$live_selector.'>';
	$output .= '<input type="text" value="'.$margin_right.'" class="forge-margins-field'.$live_class.'" name="forge_field_'.$name.'[right]" id="forge_field_'.$name.'_right"'.$placeholder.' autocomplete="off"/>';
	$output .= '</div>';
	
	$output .= '</div>';
	
	$output .= '<div class="forge-margins-row forge-builder-form-field" data-live-property="css" data-live-attribute="padding-bottom" data-live-format="" '.$live_selector.'>';
	$output .= '<input type="text" value="'.$margin_bottom.'" class="forge-margins-field'.$live_class.'" name="forge_field_'.$name.'[bottom]" id="forge_field_'.$name.'_bottom"'.$placeholder.' autocomplete="off"/>';
	$output .= '</div>';
	
	$output .= '</div>';
	
	return $output;
}


//Connection list selection
function forge_field_connection($args, $value = ''){
	$name = isset($args['name']) ? esc_attr($args['name']) : '';
	$description = isset($args['description']) ? esc_attr($args['description']) : '';
	$placeholder = (isset($args['placeholder']) && $args['placeholder'] != '') ? ' placeholder="'.esc_attr($args['placeholder']).'"': '';
	
	$list = get_option('forge_connections');
	
	$output = '<select class="forge-field-list" name="forge_field_'.$name.'" id="forge_field_'.$name.'">';
	$output .= '<option value="none" '.selected($value, 'none', false).'>'.__('(Select A Connection)', 'forge').'</option>';
	if(sizeof($list) > 0){
		foreach($list as $list_key => $list_value){
			$output .= '<option value="'.esc_attr($list_key).'" '.selected($value, $list_key, false).'>'.esc_attr($list_value['name'].' ('.$list_value['type'].')').'</option>';
		}
	}
	$output .= '</select>';
	$link = '<a target="_blank" href="'.admin_url('admin.php?page=forge_connections').'">'.__('Connections', 'forge').'</a>';
	$output .= sprintf(__('You can connect to third party services by going to the %s page.', 'forge'), $link);
	
	return $output;
}