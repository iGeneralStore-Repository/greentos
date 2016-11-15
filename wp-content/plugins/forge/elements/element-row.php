<?php 


//Render a single row
function forge_element_row($atts, $content){
	$attributes = extract(shortcode_atts(array(
	'url' => '',
	'spacing' => 'normal',
	'full' => '0',
	'height' => '',
	'color' => 'light',
	'background' => '',
	'image' => '',
	'video' => '',
	'overlay' => '',
	'overlay_opacity' => '',
	'background_position' => '',
	'background_style' => '',
	'padding' => '',
	'border' => '',
	), 
	$atts));
	
	
	//Add metadata
	
	//Set values
	$element_spacing = ' forge-columns-'.esc_attr($spacing);
	$element_full = '';
	$element_height = '';
	$element_color = ' forge-'.esc_attr($color);
	$element_background = '';
	$element_background_position = '';
	$element_background_style = 'forge-background-'.esc_attr($background_style);
	$element_image = '';
	$element_video = '';
	$element_overlay = '';
	$element_border = '';
	$element_padding = '';
	
	//Background
	if($video != ''){
		$element_video = esc_url($video);
	}elseif($image != ''){
		$element_image = ' background-image:url('.forge_image_url($image).');';
	}elseif($background != ''){
		$element_background = ' background-color:'.$background.';';
	}
	
	//Overlay
	if($overlay != ''){
		$element_overlay = ' background-color:'.$overlay.';';
		if(is_numeric($overlay_opacity)){
			if($overlay_opacity > 1) $overlay_opacity = 1;
			if($overlay_opacity < 0) $overlay_opacity = 0;
			$element_overlay .= ' opacity:'.$overlay_opacity.';';
		}
	}
	
	//Background position
	if($background_position != ''){
		$element_background_position = ' background-position:'.$background_position.';';
	}
	
	if($border != ''){
		$element_border = ' border:'.esc_attr($border).';';
	}
	
	//Paddings
	$padding_top = isset($padding['top']) ? esc_attr($padding['top']) : '';
	$padding_left = isset($padding['left']) ? esc_attr($padding['left']) : '';
	$padding_right = isset($padding['right']) ? esc_attr($padding['right']) : '';
	$padding_bottom = isset($padding['bottom']) ? esc_attr($padding['bottom']) : '';
	if($padding_top != ''){
		if(strpos($padding_top, '%') === false){
			$padding_top = intval($padding_top).'px';
		}
		$element_padding .= ' padding-top:'.$padding_top.';';
	}
	if($padding_right != ''){
		if(strpos($padding_right, '%') === false){
			$padding_right = intval($padding_right).'px';
		}
		$element_padding .= ' padding-right:'.$padding_right.';';
	}
	if($padding_bottom != ''){
		if(strpos($padding_bottom, '%') === false){
			$padding_bottom = intval($padding_bottom).'px';
		}
		$element_padding .= ' padding-bottom:'.$padding_bottom.';';
	}
	if($padding_left != ''){
		if(strpos($padding_left, '%') === false){
			$padding_left = intval($padding_left).'px';
		}
		$element_padding .= ' padding-left:'.$padding_left.';';
	}
	
	//Full layout
	if($full == '1'){
		$element_full = ' forge-row-full';
	}
	
	//Height layout
	if($height == '1'){
		$element_height = ' forge-row-tall';
	}
	
	//Bring together all styles
	$row_styling = ' style="'.$element_border.$element_padding.'"';
	$bg_styling = $element_background.$element_image.$element_background_position;
	
	//Render row
	$output = '';
	$output .= '<div class="forge-row-wrap '.$element_full.$element_height.$element_color.'" '.$row_styling.'>';
	
	//Row background
	if($element_background_style == 'forge-background-parallax'){
		wp_enqueue_script('forge-general');
	}
	
	$output .= '<div class="forge-row-background">';
	$output .= '<div class="forge-row-background-content '.$element_background_style.'" style="'.$bg_styling.'">';
	if($element_video != ''){
		$output .= '<video class="forge-row-background-video" autoplay="autoplay" muted="muted" loop="loop" src="'.$element_video.'">';
		$output .= '<source type="video/mp4" src="'.$element_video.'">';
		$output .= '</video>';
	}
	if($element_overlay != ''){
		$output .= '<div class="forge-row-background-overlay" style="'.$element_overlay.'"></div>';
	}
	$output .= '</div>';
	$output .= '</div>';
	
	$output .= '<div class="forge-row-container">';
	
	$output .= '<div class="forge-row-content">';
	
	$output .= '<div class="forge-columns '.$element_spacing.'">';
	$output .= $content;
	$output .= '</div>';
	
	$output .= '<div class="forge-clear"></div>';	
	$output .= '</div>'; //Content
	
	$output .= '</div>'; //Container
	$output .= '</div>';	
	
	
	return $output;
}


/*function forge_element_column($atts, $content){
	$attributes = extract(shortcode_atts(array(
	'size' => '6',
	'padding' => '',
	'color' => 'light',
	'background' => '',
	'image' => '',
	'border' => '',
	), 
	$atts));
	
	//Set values
	$element_size = 'forge-col'.esc_attr($size);
	$element_color = ' forge-'.esc_attr($color);
	$element_background = '';
	$element_image = '';
	$element_border = '';
	$element_padding = '';
	
	//Background
	if($image != ''){
		$element_image = ' background-image:url('.forge_image_url($image).');';
	}elseif($background != ''){
		$element_background = ' background-color:'.$background.';';
	}
	
	if($border != ''){
		$element_border = ' border:'.esc_attr($border).';';
	}
	
	//Paddings
	$padding_top = isset($padding['top']) ? esc_attr($padding['top']) : '';
	$padding_left = isset($padding['left']) ? esc_attr($padding['left']) : '';
	$padding_right = isset($padding['right']) ? esc_attr($padding['right']) : '';
	$padding_bottom = isset($padding['bottom']) ? esc_attr($padding['bottom']) : '';
	if($padding_top != ''){
		if(strpos($padding_top, '%') === false){
			$padding_top = intval($padding_top).'px';
		}
		$element_padding .= ' padding-top:'.$padding_top.';';
	}
	if($padding_right != ''){
		if(strpos($padding_right, '%') === false){
			$padding_right = intval($padding_right).'px';
		}
		$element_padding .= ' padding-right:'.$padding_right.';';
	}
	if($padding_bottom != ''){
		if(strpos($padding_bottom, '%') === false){
			$padding_bottom = intval($padding_bottom).'px';
		}
		$element_padding .= ' padding-bottom:'.$padding_bottom.';';
	}
	if($padding_left != ''){
		if(strpos($padding_left, '%') === false){
			$padding_left = intval($padding_left).'px';
		}
		$element_padding .= ' padding-left:'.$padding_left.';';
	}
	
	//Bring together all styles
	$body_styling = ' style="'.$element_border.$element_background.$element_image.'"';
	$content_styling = ' style="'.$element_padding.'"';
	
	
	//Render column
	$output = '';
	$output .= '<div class="forge-col-body"'.$element_color.$body_styling.'>';
	$output .= '<div class="forge-col-content"'.$content_styling.'>'.$content.'</div>';
	$output .= '</div>';	
	
	return $output;
}*/


add_filter('forge_elements', 'forge_element_row_metadata');
function forge_element_row_metadata($data){
	//Rows - special type
	$data['row'] = array(
	'title' => __('Row', 'forge'),
	'description' => __('Universal layout element', 'forge'),
	'featured' => 10,
	'group' => 'layout',
	'hierarchical' => true,
	'children' => array('column', 'column'),
	'children_draggable' => false,
	'callback' => 'forge_element_row',
	'fields' => array(
		array(
		'name' => 'spacing',
		'label' => __('Column Spacing', 'forge'),
		'description' => __('Adjust the spacing between each columns in the row.', 'forge'),
		'type' => 'buttonlist',
		'columns' => '4',
		'size' => '12px',
		'choices' => array(
			'wide' => __('Wide', 'forge'),
			'normal' => __('Normal', 'forge'),
			'narrow' => __('Narrow', 'forge'),
			'fit' => __('None', 'forge'),
		),
		'default' => 'normal',
		'live' => array(
			'selector' => '.forge-columns',
			'property' => 'class',
			'format' => 'forge-columns-%VALUE%',
		)),
		
		array(
		'name' => 'full',
		'label' => __('Full Width', 'forge'),
		'description' => __('Rows have a container that limits its contents while allowing the background to fill all available width. It is used for full-width sections. Checking this option will remove the container and allow the contents to fill the entire row.', 'forge'),
		'caption' => __('Expand contents to full width', 'forge'),
		'type' => 'checkbox',
		'default' => '0',
		'live' => true),
		
		array(
		'name' => 'height',
		'label' => __('Row Height', 'forge'),
		'description' => __('Checking this option will make the row height the same as the browser window. Useful for full-height slides.', 'forge'),
		'caption' => __('Make row as tall as the window', 'forge'),
		'type' => 'checkbox',
		'default' => '0',
		'live' => true),
		
		array(
		'name' => 'padding',
		'label' => __('Paddings', 'forge'),
		'type' => 'margins',
		'default' => '',
		'live' => array(
			'selector' => '.forge-row',
			'property' => 'css',
			'attribute' => 'padding',
		)),
		
		array(
		'name' => 'background',
		'label' => __('Background Color', 'forge'),
		'type' => 'color',
		'group' => 'background',
		'default' => '',
		'live' => array(
			'selector' => '.forge-row-background-content',
			'property' => 'css',
			'attribute' => 'background',
		)),
		
		array(
		'name' => 'image',
		'label' => __('Background Image', 'forge'),
		'description' => __('Use an image as the background. The image will scale to cover the entire row.', 'forge'),
		'type' => 'image',
		'group' => 'background',
		'default' => '',
		'live' => true),
		
		array(
		'name' => 'video',
		'label' => __('Background Video', 'forge'),
		'description' => __('Use a MP4 video file as the background. The video will be muted, play and loop automatically, and scale to cover the entire row.', 'forge'),
		'type' => 'text',
		'group' => 'background',
		'default' => '',
		'live' => true),
		
		array(
		'name' => 'background_position',
		'label' => __('Background Position', 'forge'),
		'type' => 'list',
		'group' => 'background',
		'choices' => array(
			'center' => __('Centered', 'forge'),
			'top' => __('Top', 'forge'),
			'right top' => __('Top Right', 'forge'),
			'right' => __('Right', 'forge'),
			'right bottom' => __('Right Bottom', 'forge'),
			'bottom' => __('Bottom', 'forge'),
			'left bottom' => __('Left Bottom', 'forge'),
			'left' => __('Left', 'forge'),
			'left top' => __('Left Top', 'forge'),
		),
		'default' => 'center',
		'live' => array(
			'selector' => '.forge-row-background-content',
			'property' => 'css',
			'attribute' => 'background-position',
		)),
		
		array(
		'name' => 'background_style',
		'label' => __('Background Style', 'forge'),
		'type' => 'list',
		'group' => 'background',
		'choices' => array(
			'scroll' => __('Scrolling background', 'forge'),
			'fixed' => __('Fixed background', 'forge'),
			'parallax' => __('Parallax effect', 'forge'),
		),
		'default' => '',
		'live' => array(
			'selector' => '.forge-row-background-content',
			'property' => 'class',
			'format' => 'forge-background-%VALUE%',
		)),
		
		array(
		'name' => 'color',
		'label' => __('Color Scheme', 'forge'),
		'description' => __('Use a dark color scheme when you need texts to be white on dark backgrounds.', 'forge'),
		'type' => 'list',
		'group' => 'background',
		'choices' => array(
			'light' => __('Light Backgrounds', 'forge'),
			'dark' => __('Dark Backgrounds', 'forge')
		),
		'default' => '',
		'live' => array(
			'selector' => '.forge-row-content',
			'property' => 'class',
			'format' => 'forge-%VALUE%',
		)),
		
		array(
		'name' => 'overlay',
		'label' => __('Background Overlay', 'forge'),
		'description' => __('Add a transparent color overlay to the existing background.', 'forge'),
		'type' => 'color',
		'group' => 'background',
		'default' => '',
		'live' => array(
			'selector' => '.forge-row-background-overlay',
			'property' => 'css',
			'attribute' => 'background',
		)),
		
		array(
		'name' => 'overlay_opacity',
		'label' => __('Background Overlay Opacity', 'forge').' (%)',
		'description' => __('Enter a value from 0 to 1, such as 0.4.', 'forge'),
		'type' => 'slider',
		'width' => '50px',
		'group' => 'background',
		'min' => '0',
		'max' => '1',
		'step' => '0.05',
		'default' => '',
		'live' => array(
			'selector' => '.forge-row-background-overlay',
			'property' => 'css',
			'attribute' => 'opacity',
		)),
	),
	'classes' => 'forge-row', //Change forge-element to forge-row, special item
	);
	
	
	//Columns - special type
	$data['column'] = array(
	'title' => __('Column', 'forge'),
	'description' => __('A column inside a content row.', 'forge'),
	'group' => 'layout',
	'hierarchical' => true,
	'parent' => array('row'),
	'callback' => 'forge_element_column',
	'fields' => array(
		array(
		'name' => 'background',
		'label' => __('Background Color', 'forge'),
		'type' => 'color',
		'default' => '',
		'live' => array(
			'selector' => '.forge-col-body',
			'property' => 'css',
			'attribute' => 'background-color',
		)),
		
		array(
		'name' => 'image',
		'label' => __('Background Image', 'forge'),
		'type' => 'image',
		'default' => '',
		'live' => true),
		
		array(
		'name' => 'border',
		'label' => __('Border', 'forge'),
		'type' => 'border',
		'default' => '',
		'live' => array(
			'selector' => '.forge-col-body',
			'property' => 'css',
			'attribute' => 'border',
		)),
		
		array(
		'name' => 'color',
		'label' => __('Color Scheme', 'forge'),
		'type' => 'list',
		'choices' => array(
			'light' => __('Light Backgrounds', 'forge'),
			'dark' => __('Dark Backgrounds', 'forge')
		),
		'default' => '',
		'live' => array(
			'selector' => '.forge-col-body',
			'property' => 'class',
			'format' => 'forge-%VALUE%',
		)),
		
		array(
		'name' => 'padding',
		'label' => __('Paddings', 'forge'),
		'type' => 'margins',
		'default' => '',
		'live' => array(
			'selector' => '.forge-col-content',
			'property' => 'css',
			'attribute' => 'padding',
		)),
		
	),
	'classes' => 'forge-col', //Change forge-element to forge-col, special item
	);
	
	return $data;
}