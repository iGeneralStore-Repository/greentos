<?php 

//Define customize sections
function forge_metadata_customize_panels(){
	$data = array();
	
	$data['forge_general'] = array(
	'title' => __('Forge Page Builder', 'forge'),
	'description' => __('Customize the styling and appearance of your Forge-based content, such as colors.', 'forge'),
	'priority' => 120);
	
	return $data;
}


//Define customize sections
function forge_metadata_customize_sections(){
	$data = array();
	
	//General
	$data['forge_colors'] = array(
	'title' => __('Color Palette', 'forge'),
	'description' => __('Customize the color palette of content created using Forge.', 'forge'),
	'capability' => 'edit_theme_options',
	'panel' => 'forge_general',
	'priority' => 15);
	
	return apply_filters('forge_customize_sections', $data);
}


function forge_metadata_customize_controls($std = null){
	$data = array();
	
	//Colors
	$data['primary_color'] = array(
	'label' => __('Primary Color', 'forge'),
	'description' => __('The main color used for site branding.', 'forge'),
	'section' => 'forge_colors',
	'type' => 'color',
	'sanitize' => 'sanitize_hex_color',
	'default' => '#dd8800');
	
	$data['secondary_color'] = array(
	'label' => __('Secondary Color', 'forge'),
	'description' => __('Used in backgrounds and minor elements.', 'forge'),
	'section' => 'forge_colors',
	'type' => 'color',
	'sanitize' => 'sanitize_hex_color',
	'default' => '#404549');
	
	$data['highlight_color'] = array(
	'label' => __('Highlight Color', 'forge'),
	'description' => __('Used in buttons and other important elements.', 'forge'),
	'section' => 'forge_colors',
	'type' => 'color',
	'sanitize' => 'sanitize_hex_color',
	'default' => '#66BB22');
	
	$data['headings_color'] = array(
	'label' => __('Headings & Titles', 'forge'),
	'description' => __('Used in H1-H6 headings and title sections.', 'forge'),
	'section' => 'forge_colors',
	'type' => 'color',
	'sanitize' => 'sanitize_hex_color',
	'default' => '#444444');
	
	$data['body_color'] = array(
	'label' => __('Body Text', 'forge'),
	'description' => __('Used in standard body texts.', 'forge'),
	'section' => 'forge_colors',
	'type' => 'color',
	'sanitize' => 'sanitize_hex_color',
	'default' => '#777777');
	
	return apply_filters('forge_customize_controls', $data);
}