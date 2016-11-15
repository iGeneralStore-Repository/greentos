<?php

//Add default metaboxes to posts
add_action('add_meta_boxes', 'forge_metaboxes');
function forge_metaboxes(){
	$args = array('public' => true);
	
	//Add common metaboxes
	$post_types = get_post_types($args, 'names');
	$post_type_list = array();
	foreach($post_types as $current_type){
		add_meta_box('forge_layout_'.$current_type, __('Forge Options', 'forge'), 'forge_metabox_layout', $current_type, 'side', 'high');
	}
}

//Display and save post metaboxes
function forge_metabox_layout($post){ 
	$screen = get_current_screen();
	$output = '';
	$output .= '<div class="forge-metabox">';
	
	if($screen->action != 'add'){
		wp_enqueue_style('forge_admin');
		wp_nonce_field('forge_savemeta', 'forge_nonce');
		$current_options = get_post_meta($post->ID, 'forge_builder_settings', true);
		
		
		//Activate page builder
		$field_active = (isset($current_options['active']) && $current_options['active'] == true) ? ' checked' : '';
		$output .= '<div class="forge-metabox-field">';
		$output .= '<label for="forge_active">';
		$output .= '<input type="checkbox" name="forge_active" id="forge_active" value="1" '.$field_active.'>';	
		$output .= '<span class="forge-metabox-title">'.__('Activate Page Builder', 'forge').'</span>';
		$output .= '</label>';
		$output .= '</div>';
		
		$output .= '<div class="forge-metabox-description">';
		if($field_active != ''){
			$output .= __('Forge is active and the page builder layout will be displayed instead of the post content.', 'forge');
		}else{
			$output .= __('Forge is inactive and the post content will be displayed as normal.', 'forge');
		}
		$output .= '</div>';
		
		//Post template
		$field_template = isset($current_options['template']) ? esc_attr($current_options['template']) : 'none';
		$output .= '<p>';
		$output .= '<select name="forge_template" id="forge_template" class="widefat">';	
		$output .= '<option value="none">'.__('Normal Page', 'forge').'</option>';	
		$output .= '<option value="blank" '.selected($field_template, 'blank', false).'>'.__('Blank Page (Landing)', 'forge').'</option>';
		$output .= '</select>';	
		$output .= '</p>';
		
		$output .= '<a href="'.add_query_arg(array('forge_builder' => ''), get_permalink($post)).'" target="_blank" class="forge-metabox-lin button">';
		$output .= __('Open Forge Page Builder', 'forge');
		$output .= '</a>';
	}else{
		$output .= '<div class="forge-metabox-description">'.__('Please save the post before using the Forge page builder.', 'forge').'</div>';
	}
	
	$output .= '</div>';
	echo $output;
}


add_action('save_post', 'forge_metaboxes_save');
function forge_metaboxes_save($post){
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
		return;
	}
	
	if(!isset($_POST['forge_nonce']) || !wp_verify_nonce($_POST['forge_nonce'], 'forge_savemeta')){
		return;
	}
	
	$current_options = get_post_meta($post, 'forge_builder_settings', true);
	$update_options = true;
	
	//If options are not set and builder is active, install them
	if(!is_array($current_options)){
		$current_options = array(
		'active' => true,
		'css' => '',
		'created' => date('Y-m-d H:i:s'),
		'modified' => date('Y-m-d H:i:s'));
		$update_options = false;
	}
	
	//Set builder status
	if(isset($_POST['forge_active']) && $_POST['forge_active'] == '1'){
		$current_options['active'] = true;
		$update_options = true;
	}else{
		$current_options['active'] = false;
	}
	
	//Set builder template
	if(isset($_POST['forge_template'])){
		$current_options['template'] = esc_attr($_POST['forge_template']);
		$update_options = true;
	}
	
	if($update_options){
		update_post_meta($post, 'forge_builder_settings', $current_options);
	}
}
