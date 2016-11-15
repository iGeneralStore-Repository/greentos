<?php

//Render settings page
function forge_settings_page(){
	echo '<div class="wrap">';
	echo '<h2>Forge</h2>';
	//settings_errors();
	echo '<form method="post" action="options.php">';
    settings_fields('forge_settings');
    do_settings_sections('forge_settings');          
    submit_button();
    echo '</form>';
	echo '</div>';
}


//Create settings fields
add_action('admin_init', 'forge_settings_fields');
function forge_settings_fields(){
	$option_values = get_option('forge_settings');
	
	//Register new setting
	register_setting('forge_settings', 'forge_settings', 'forge_settings_sanitize');		
	
	//Add sections to the settings page
	$settings = forge_metadata_sections();
	foreach($settings as $setting_id => $setting_data){
		add_settings_section($setting_id, $setting_data['label'], 'forge_settings_section', 'forge_settings');
	}
	
	//Add settings & controls
	$settings = forge_metadata_settings();
	foreach($settings as $setting_id => $setting_data){
		$setting_data['id'] = $setting_id;
		if(isset($option_values[$setting_id])){
			$setting_data['value'] = $option_values[$setting_id];
		}elseif(isset($setting_data['default'])){
			$setting_data['value'] = $setting_data['default'];
		}else{
			$setting_data['value'] = '';
		}
		add_settings_field($setting_id, $setting_data['label'], 'forge_settings_field', 'forge_settings' , $setting_data['section'], $setting_data);
	}
}



function forge_settings_section($args){
	$settings = forge_metadata_sections();
	foreach($settings as $setting_id => $setting_data){
		if($args['id'] == $setting_id){
			if(isset($setting_data['description'])){
				echo '<p>'.$setting_data['description'].'</p>';
			}
			do_action('forge_settings_'.$setting_id.'_section');
		}
	}
}


function forge_settings_field($args){ 
	if(!isset($args['class'])) $args['class'] = '';
	if(!isset($args['placeholder'])) $args['placeholder'] = '';
	
	if(function_exists('forge_settings_field_'.$args['type'])){
		call_user_func('forge_settings_field_'.$args['type'], $args);
	}
}


//Sanitize options
function forge_settings_sanitize($options){
	$old_options = forge_settings()->get();
	$settings = forge_metadata_settings();
	
	//Validate checkboxes when turned off
	foreach($settings as $setting_key => $setting_value){
		if(!isset($options[$setting_key]) && $setting_value['type'] == 'checkbox'){
			// $options[$option_key] == '0';
		}
		$old_options[$setting_key] = $options[$setting_key];
	}
	return $old_options;
}


//Install settings upon theme switch
function forge_settings_defaults(){
	$option_name = 'forge_settings';
	$options_list = get_option($option_name, false);
	foreach(forge_metadata_settings() as $current_id => $current_option){
		if(!isset($options_list[$current_id])){
			if(isset($current_option['default'])){
				$field_default = $current_option['default'];
				$options_list[$current_id] = $field_default;
			}
		}
	}
	update_option($option_name, $options_list);
	
	//Register review notice
	if(!get_option('forge_install', false)){
		update_option('forge_install', date('Y-m-d'));
	}
}


function forge_settings_field_text($args){
	echo '<input name="forge_settings['.$args['id'].']" type="text" id="'.$args['id'].'" value="'.$args['value'].'" placeholder="'.$args['placeholder'].'" class="'.$args['class'].'"/>';
}


function forge_settings_field_license($args){
	$license_status = forge_get_option($args['id'].'_status', 'forge_licenses');
	$license_expires = forge_get_option($args['id'].'_expires', 'forge_licenses');
	echo '<input name="forge_settings['.$args['id'].']" type="text" id="'.$args['id'].'" style="width:350px;" value="'.$args['value'].'" class="'.$args['class'].'" placeholder="'.__('Enter your license key...', 'forge').'"/>';	
	if($args['value'] != false && $args['value'] != ''){
		if($license_status == 'invalid'){
			echo '<div class="forge-license forge-license-error">'.__('The license key you entered is invalid.', 'forge').'</div>';
		}elseif($license_status == 'valid'){
			echo '<div class="forge-license forge-license-success">';
			echo __('License key activated.', 'forge');
			if($license_expires != ''){
				echo '&nbsp;';
				printf(__('Expires on %s.', 'forge'), date('F j, Y', $license_expires));
			}
			echo '</div>';
		}
	}
}


function forge_settings_field_checkbox($args){
	
	$caption = isset($args['caption']) ? esc_attr($args['caption']) : false;
	
	echo '<label for="'.$args['id'].'">';	
	echo '<input name="forge_settings['.$args['id'].']" type="checkbox" id="'.$args['id'].'" value="1" class="'.$args['class'].'" '.checked($args['value'], '1', false).'/>';
	if($caption){
		echo '&nbsp;';
		echo $caption;
	}
	echo '</label>';	
}


