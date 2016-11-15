<?php 

/* Text element */

function forge_element_image($atts, $content = null){
	$attributes = extract(shortcode_atts(array(
	'image' => '',
	'position' => '',
	'url' => '',
	'style' => '',
	'width' => '',
	'alt' => '',
	), 
	$atts));
	
	$source = forge_image_url($image);
	$element_url = '';
	$element_width = '';
	$element_alt = '';
	$element_position = ' forge-image-'.esc_attr($position);
	$element_style = ' forge-image-'.esc_attr($style);
	
	$element_tag = 'div';
	if($url != ''){
		$element_url = ' href="'.esc_url($url).'"';
		$element_tag = 'a';
	}
	
	//Width
	if($width != ''){
		$element_width = ' style="max-width:'.intval($width).'px;"';
	}
	
	//Alt
	if($alt != ''){
		$element_alt = ' alt="'.esc_attr($alt).'"';
	}
	
	$output = '';
	$output .= '<'.$element_tag.$element_url.' class="forge-image-wrap '.$element_position.'">';
	if($source){
		$output .= '<img class="forge-image '.$element_style.'" src="'.$source.'"'.$element_width.$element_alt.'/>';
	}else{
		$output .= '<div class="forge-image-placeholder"></div>';
	}
	$output .= '</'.$element_tag.'>';		
	return $output;
}


add_filter('forge_elements', 'forge_element_image_metadata');
function forge_element_image_metadata($data){
	$data['image'] = array(
	'title' => __('Image', 'forge'),
	'description' => __('Single, responsive image element.', 'forge'),
	'featured' => 40,
	'group' => 'layout',
	'callback' => 'forge_element_image',
	'fields' => array(
		array(
		'name' => 'image',
		'label' => __('Image', 'forge'),
		'type' => 'image',
		'default' => '',
		'live' => true),
		
		array(
		'name' => 'url',
		'label' => __('Destination URL', 'forge'),
		'type' => 'text',
		'placeholder' => 'http://',
		'default' => ''),
		
		array(
		'name' => 'position',
		'label' => __('Image Alignment', 'forge'),
		'type' => 'buttonlist',
		'columns' => '4',
		'choices' => array(
			'left' => forge_icon('linearicons-&#xe898', '', false),
			'center' => forge_icon('linearicons-&#xe899', '', false),
			'right' => forge_icon('linearicons-&#xe89a', '', false),
			'wide' => forge_icon('linearicons-&#xe89b', '', false),
		),
		'default' => 'none',
		'live' => array(
			'selector' => '.forge-image-wrap',
			'property' => 'class',
			'format' => 'forge-image-%VALUE%',
		)),
		
		array(
		'name' => 'style',
		'label' => __('Image Style', 'forge'),
		'type' => 'buttonlist',
		'columns' => '5',
		'choices' => array(
			'square' => __('Square', 'forge'),
			'normal' => __('Normal', 'forge'),
			'round' => __('Rounded', 'forge'),
			'oval' => __('Oval', 'forge'),
			'circle' => __('Circle', 'forge'),
			'square' => '<svg width="30" height="40"><rect x="5" y="10" rx="0" ry="0" width="20" height="20" style="fill:none;stroke:currentColor;stroke-width:1;"/></svg>',
			'normal' => '<svg width="30" height="40"><rect x="5" y="10" rx="2" ry="2" width="20" height="20" style="fill:none;stroke:currentColor;stroke-width:1;"/></svg>',
			'round' => '<svg width="30" height="40"><rect x="5" y="10" rx="7" ry="7" width="20" height="20" style="fill:none;stroke:currentColor;stroke-width:1;"/></svg>',
			'oval' => '<svg width="30" height="40"><rect x="2" y="11" rx="50" ry="50" width="26" height="18" style="fill:none;stroke:currentColor;stroke-width:1;"/></svg>',
			'circle' => '<svg width="30" height="40"><rect x="5" y="10" rx="50" ry="50" width="20" height="20" style="fill:none;stroke:currentColor;stroke-width:1;"/></svg>',
		),
		'default' => 'none',
		'live' => array(
			'selector' => '.forge-image',
			'property' => 'class',
			'format' => 'forge-image-%VALUE%',
		)),
		
		array(
		'name' => 'width',
		'label' => __('Image Width', 'forge'),
		'description' => __('Set a specific maximum width for the image. If left empty, the image will appear at its original size.', 'forge'),
		'type' => 'text',
		'placeholder' => 'auto',
		'width' => '60px',
		'default' => '',
		'live' => array(
			'selector' => '.forge-image',
			'property' => 'css',
			'format' => '%VALUE%px',
			'attribute' => 'max-width',
		)),
		
		array(
		'name' => 'alt',
		'label' => __('Alt Text', 'forge'),
		'type' => 'text',
		'placeholder' => '',
		'default' => ''),
	));

	return $data;
}
	