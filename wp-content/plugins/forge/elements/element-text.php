<?php 

/* Text element */

function forge_element_text($atts, $content = null){
	$attributes = extract(shortcode_atts(array(
	'color' => '',
	'size' => '',
	), 
	$atts));
	
	$element_size = '';
	$element_color = '';
	
	if($color != ''){
		$element_color = ' color:'.$color.';';
	}
	
	$element_class = '';
	if(intval($size) != ''){
		$element_size = ' font-size:'.intval($size).'px;';
		if(intval($size) > 40){
			$element_class .= ' forge-element-heading-large';
		}
	}
	
	$element_style = ' style="'.$element_size.$element_color.'"';
	
	$output = '';
	$output .= '<div class="forge-element-text"'.$element_style.'>';
	$output .= do_shortcode(wptexturize(wpautop($content)));
	$output .= '</div>';		
	return $output;
}


add_filter('forge_elements', 'forge_element_text_metadata');
function forge_element_text_metadata($data){
	$data['text'] = array(
	'title' => __('Text Block', 'forge'),
	'description' => __('Standard rich text block', 'forge'),
	'featured' => 20,
	'group' => 'layout',
	'style' => 'wide',
	'callback' => 'forge_element_text',
	'fields' => array(
		array(
		'name' => 'content',
		'type' => 'editor',
		'default' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
		
		array(
		'name' => 'size',
		'label' => __('Size', 'forge').' (px)',
		'type' => 'slider',
		'min' => '10',
		'max' => '64',
		'step' => '1',
		'default' => '',
		'live' => array(
			'selector' => '.forge-element-text',
			'property' => 'css',
			'attribute' => 'font-size',
			'format' => '%VALUE%px',
		)),
		
		array(
		'name' => 'color',
		'label' => __('Color', 'forge'),
		'type' => 'color',
		'default' => '',
		'live' => array(
			'selector' => '.forge-element-text',
			'property' => 'css',
			'attribute' => 'color',
		)),
	));
	
	return $data;
}