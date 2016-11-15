<?php

//Generate settings
add_action('customize_register', 'forge_customize_settings');
function forge_customize_settings($customize){
	
	//Add panels to the customizer
	$settings = forge_metadata_customize_panels();
	foreach($settings as $setting_id => $setting_data){
		$customize->add_panel($setting_id, $setting_data);
		
	}
	
	//Add sections to the customizer
	$settings = forge_metadata_customize_sections();
	foreach($settings as $setting_id => $setting_data){
		$customize->add_section($setting_id, $setting_data);
	}
	
	//Add settings & controls
	$settings = forge_metadata_customize_controls();
	foreach($settings as $setting_id => $setting_data){
		$default = isset($setting_data['default']) ? $setting_data['default'] : '';
		
		$setting_args = array(
		'type' => 'option',
		'default' => $default,
		'capability' => 'edit_theme_options',
		'transport' => 'refresh');
		if(isset($setting_data['sanitize']) && $setting_data['sanitize'] != ''){
			$setting_args['sanitize_callback'] = $setting_data['sanitize'];
		}
		
			
		//If language is not the default one
		$args = $setting_data;
		$option_array = 'forge_settings';
		$control_id = $setting_id;
		
		//Add setting to the customizer
		$customize->add_setting($option_array.'['.$setting_id.']', $setting_args); 
		
		//Define control metadata
		$args['settings'] = $option_array.'['.$setting_id.']';
		$args['priority'] = isset($args['priority']) ? $args['priority'] : 10;
		if(!isset($args['type'])) $args['type'] = 'text';
		
		switch($args['type']){
			case 'text': 
			case 'textarea': 
			case 'checkbox': 
			case 'select': 
			$customize->add_control('forge_'.$control_id, $args); break;
			case 'color': 
			$customize->add_control(new WP_Customize_Color_Control($customize, 'forge_'.$control_id, $args)); break;
			case 'image': 
			$customize->add_control(new WP_Customize_Image_Control($customize, 'forge_'.$control_id, $args)); break;
			// case 'notice': 
			// $customize->add_control(new Furnace_Customize_Control_Notice($customize, 'forge_'.$control_id, $args)); break;
		}
	}
}


add_action('customize_controls_print_styles', 'forge_customize_styles');
function forge_customize_styles(){
	echo '<style>';
	echo '#accordion-panel-forge_general > h3.accordion-section-title { color:#f84 !important; }';
	echo '</style>';
}