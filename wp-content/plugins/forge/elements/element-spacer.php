<?php 

/* Spacer Element */
function forge_element_spacer($atts, $content = null){
	
	$attributes = extract(shortcode_atts(array(
	'height' => 'fade', 
	'id' => '',
	'class' => ''),
	$atts));			
	
	$element_height = $height != '' ? $height : '25';
	
	$element_style = ' style="height:'.$element_height.'px"';
	
	$output = '<div class="forge-spacer '.'"'.$element_style.'></div>';
	return $output;
}


add_filter('forge_elements', 'forge_element_spacer_metadata');
function forge_element_spacer_metadata($data){
	$data['spacer'] = array(
	'title' => __('Spacer', 'forge'),
	'description' => __('Invisible gap between two elements', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_spacer',
	'fields' => array(
		array(
		'name' => 'height',
		'label' => __('Height', 'forge'),
		'type' => 'text',
		'default' => '30',
		'live' => array(
			'selector' => '.forge-spacer',
			'property' => 'css',
			'attribute' => 'height',
			'format' => '%VALUE%px',
		)),
	));
	
	return $data;
}