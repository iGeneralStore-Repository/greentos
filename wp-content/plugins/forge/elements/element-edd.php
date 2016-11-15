<?php 

/* Recent edd Element */
function forge_element_edd_products($atts, $content = null){
	
	$attributes = extract(shortcode_atts(array(
	'columns' => 4,
	'number' => 12,
	'price' => 'no',
	'excerpt' => 'no',
	'full_content' => 'no',
	'thumbnails' => 'yes',
	'buy_button' => 'no',
	'pagination' => 'no',
	'order' => 'DESC',
	'orderby' => 'date',
	), $atts));
	
	//Post number
	if(!is_numeric($number)) $number = 12; 
	elseif($number < 1) $number = 1; 
	elseif($number > 9999 ) $number = 9999;
	
	if($price == '1'){
		$price = 'yes';
	}
	
	if($excerpt == '1'){
		$excerpt = 'yes';
	}
	
	if($full_content == '1'){
		$full_content = 'yes';
	}
	
	if($buy_button == '1'){
		$buy_button = 'yes';
	}
	
	if($pagination == '1'){
		$pagination = 'true';
	}
	
	$output = '<div class="forge-edd">';
	$output .= do_shortcode('[downloads number="'.$number.'" columns="'.$columns.'" price="'.$price.'" excerpt="'.$excerpt.'" full_content="'.$full_content.'" buy_button="'.$buy_button.'" orderby="'.$orderby.'" order="'.$order.'"]');
	$output .= '<div class="forge-clear"></div>';
	$output .= '</div>';
	
	return $output;
}


add_filter('forge_elements', 'forge_element_edd_products_metadata');
function forge_element_edd_products_metadata($data){
	$data['edd'] = array(
	'title' => __('Easy Digital Downloads', 'forge'),
	'description' => __('List of store products', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_edd_products',
	'fields' => array(
		array(
		'name' => 'number',
		'label' => __('Number of Entries', 'forge'),
		'type' => 'text',
		'width' => '60px',
		'default' => '8'),
		
		array(
		'name' => 'columns',
		'label' => __('Number of Columns', 'forge'),
		'type' => 'list',
		'choices' => array(
			'1' => sprintf(__('%s Columns', 'forge'), '1'),
			'2' => sprintf(__('%s Columns', 'forge'), '2'),
			'3' => sprintf(__('%s Columns', 'forge'), '3'),
			'4' => sprintf(__('%s Columns', 'forge'), '4'),
			'5' => sprintf(__('%s Columns', 'forge'), '5'),
			'6' => sprintf(__('%s Columns', 'forge'), '6'),
		),
		'default' => '4'),	
		
		array(
		'name' => 'orderby',
		'label' => __('Order Products By', 'forge'),
		'type' => 'list',
		'choices' => array(
			'post_date' => __('Date', 'forge'),
			'title' => __('Title', 'forge'),
			'price' => __('Price', 'forge'),
		),
		'default' => 'title'),
		
		array(
		'name' => 'order',
		'label' => __('Product Ordering', 'forge'),
		'type' => 'list',
		'choices' => array(
			'ASC' => __('Ascending', 'forge'),
			'DESC' => __('Descending', 'forge'),
		),
		'default' => 'DESC'),
			
		array(
		'name' => 'price',
		'caption' => __('Display Download Price', 'forge'),
		'type' => 'checkbox',
		'default' => '1'),
		
		array(
		'name' => 'excerpt',
		'caption' => __('Display Excerpt', 'forge'),
		'type' => 'checkbox',
		'default' => '0'),
		
		array(
		'name' => 'full_content',
		'caption' => __('Display Full Content', 'forge'),
		'type' => 'checkbox',
		'default' => '0'),
		
		array(
		'name' => 'buy_button',
		'caption' => __('Display Purchase Button', 'forge'),
		'type' => 'checkbox',
		'default' => '1'),
		
		array(
		'name' => 'pagination',
		'caption' => __('Display Pagination', 'forge'),
		'type' => 'checkbox',
		'default' => '0'),
	));
	
	return $data;
}