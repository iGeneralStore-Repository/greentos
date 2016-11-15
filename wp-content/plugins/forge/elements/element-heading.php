<?php 

/* Text element */

function forge_element_heading($atts, $content = null){
	$attributes = extract(shortcode_atts(array(
	'size' => '',
	'tag' => '',
	'color' => '',
	'position' => '',
	'weight' => '',
	), 
	$atts));
	
	$element_size = '';
	$element_position = ' forge-element-heading-'.esc_attr($position);
	$element_tag = 'h2';
	$element_color = '';
	$element_weight = '';
	
	if($tag != ''){
		$element_tag = esc_attr($tag);
	}
	
	if($color != ''){
		$element_color = ' color:'.$color.';';
	}
	
	$element_class = '';
	if(intval($size) != ''){
		$element_size = ' font-size:'.intval($size).'px;';
		if(intval($size) > 60){
			$element_class .= ' forge-element-heading-huge';
		}elseif(intval($size) > 40){
			$element_class .= ' forge-element-heading-large';
		}elseif(intval($size) > 30){
			$element_class .= ' forge-element-heading-medium';
		}
	}
	
	if($weight != '' && $weight != 'inherit'){
		$element_weight = ' font-weight:'.intval($weight).';';
	}
	
	$container_style = ' style="'.$element_size.'"';
	$element_style = ' style="'.$element_color.$element_weight.'"';
	
	$output = '';
	$output .= '<div class="forge-element-heading-container '.$element_class.$element_position.'"'.$container_style.'>';
	$output .= '<'.$element_tag.' class="forge-element-heading-item"'.$element_style.'>';
	$output .= $content;
	$output .= '</'.$element_tag.'>';		
	$output .= '</div>';		
	return $output;
}


add_filter('forge_elements', 'forge_element_heading_metadata');
function forge_element_heading_metadata($data){
	$data['heading'] = array(
	'title' => __('Heading', 'forge'),
	'description' => __('Opening title of different sizes', 'forge'),
	'featured' => 30,
	'group' => 'layout',
	'callback' => 'forge_element_heading',
	'fields' => array(
		array(
		'name' => 'content',
		'label' => __('Content', 'forge'),
		'type' => 'text',
		'default' => 'This is a heading',
		'live' => array(
			'selector' => '.forge-element-heading-item',
			'property' => 'html',
		)),
		
		array(
		'name' => 'size',
		'label' => __('Size', 'forge').' (px)',
		'type' => 'slider',
		'min' => '10',
		'max' => '128',
		'step' => '1',
		'default' => '24',
		'live' => array(
			'selector' => '.forge-element-heading-container',
			'property' => 'css',
			'attribute' => 'font-size',
			'format' => '%VALUE%px',
		)),
		
		array(
		'name' => 'position',
		'label' => __('Position', 'forge'),
		'type' => 'buttonlist',
		'columns' => '3',
		'choices' => array(
			'left' => forge_icon('linearicons-&#xe898', '', false),
			'center' => forge_icon('linearicons-&#xe899', '', false),
			'right' => forge_icon('linearicons-&#xe89a', '', false),
		),
		'default' => 'left',
		'live' => array(
			'selector' => '.forge-element-heading-container',
			'property' => 'class',
			'format' => 'forge-element-heading-%VALUE%',
		)),
		
		array(
		'name' => 'color',
		'label' => __('Color', 'forge'),
		'type' => 'color',
		'default' => '',
		'live' => array(
			'selector' => '.forge-element-heading-item',
			'property' => 'css',
			'attribute' => 'color',
		)),
		
		array(
		'name' => 'weight',
		'label' => __('Font Weight', 'forge'),
		'type' => 'buttonlist',
		'columns' => '4',
		'size' => '12px',
		'choices' => array(
			'inherit' => __('(None)', 'forge'),
			'300' => '<span style="font-weight:300;">'.__('Light', 'forge').'</span>',
			'400' => '<span style="font-weight:400;">'.__('Regular', 'forge').'</span>',
			'700' => '<span style="font-weight:700;">'.__('Bold', 'forge').'</span>',
		),
		'default' => 'inherit',
		'live' => array(
			'selector' => '.forge-element-heading-item',
			'property' => 'css',
			'attribute' => 'font-weight',
		)),
		
		array(
		'name' => 'tag',
		'label' => __('Heading Tag', 'forge'),
		'type' => 'buttonlist',
		'columns' => '7',
		'size' => '12px',
		'choices' => array(
			'h1' => 'H1',
			'h2' => 'H2',
			'h3' => 'H3',
			'h4' => 'H4',
			'h5' => 'H5',
			'h6' => 'H6',
			'span' => '<small>Span</small>',
		),
		'default' => 'h2'),
	));
	
	return $data;
}