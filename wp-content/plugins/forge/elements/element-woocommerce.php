<?php 

/* Recent woocommerce Element */
function forge_element_woocommerce_products($atts, $content = null){
	$attributes = extract(shortcode_atts(array(
	'type' => '',
	'columns' => 4,
	'number' => 12,
	'order' => 'DESC',
	'orderby' => 'date',
	), $atts));
	
	if(class_exists('WooCommerce')){
		
		//Post number
		if(!is_numeric($number)) $number = 12; 
		elseif($number < 1) $number = 1; 
		elseif($number > 9999 ) $number = 9999;
		
		//Create the query
		if($type == ''){
			$type = 'featured_products';
		}else{
			$type = esc_attr($type);
		}
		
		add_filter('post_class', 'forge_element_woocommerce_classes');
		$output = '<div class="forge-wc-products">';
		$output .= do_shortcode('['.$type.' per_page="'.$number.'" columns="'.$columns.'" orderby="'.$orderby.'" order="'.$order.'"]');
		$output .= '<div class="forge-clear"></div>';
		$output .= '</div>';
		remove_filter('post_class', 'forge_element_woocommerce_classes');
		
		return $output;
	}
}


add_filter('forge_elements', 'forge_element_woocommerce_products_metadata');
function forge_element_woocommerce_products_metadata($data){
	$data['woocommerce'] = array(
	'title' => __('WooCommerce Products', 'forge'),
	'description' => __('List of store products', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_woocommerce_products',
	'fields' => array(
		array(
		'name' => 'type',
		'label' => __('Product Display', 'forge'),
		'type' => 'list',
		'choices' => array(
			'recent_products' => __('Recent Products', 'forge'),
			'featured_products' => __('Featured Products', 'forge'),
			'best_selling_products' => __('Best Selling Products', 'forge'),
			'sale_products' => __('Products On Sale', 'forge'),
			'top_rated_products' => __('Top Rated Products', 'forge'),
		),
		'default' => 'featured_products'),	
		
		array(
		'name' => 'number',
		'label' => __('Number of Entries', 'forge'),
		'type' => 'text',
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
			'date' => __('Date', 'forge'),
			'title' => __('Title', 'forge'),
			'menu_order' => __('Order Value', 'forge'),
		),
		'default' => 'title'),
		
		array(
		'name' => 'order',
		'label' => __('Product Ordering', 'forge'),
		'type' => 'list',
		'choices' => array(
			'asc' => __('Ascending', 'forge'),
			'desc' => __('Descending', 'forge'),
		),
		'default' => 'desc'),
	));
	
	return $data;
}