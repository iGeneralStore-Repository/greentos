<?php 

function forge_element_wpwidget_categories($atts){
	$output = '';
	$output .= '<div class="forge-widget">';
	ob_start();
	the_wpwidget('WP_Widget_Categories', $atts);
	$output .= ob_get_clean();
	$output .= '</div>';
	return $output;
}


add_filter('forge_elements', 'forge_element_wpwidget_categories_metadata');
function forge_element_wpwidget_categories_metadata($data){
	$data['widget_categories'] = array(
	'title' => __('Widget: Categories', 'forge'),
	'description' => __('Native WordPress categories widget', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_wpwidget_categories',
	'fields' => array(
		array(
		'name' => 'title',
		'label' => __('Title', 'forge'),
		'type' => 'text',
		'default' => __('Categories')),
		
		array(
		'name' => 'count',
		'label' => __('Show Post Count', 'forge'),
		'type' => 'list',
		'choices' => array(
			'0' => __('No', 'forge'),
			'1' => __('Yes', 'forge'),
		),
		'default' => '0'),
		
		array(
		'name' => 'hierarchical',
		'label' => __('Hierarchical', 'forge'),
		'type' => 'list',
		'choices' => array(
			'0' => __('No', 'forge'),
			'1' => __('Yes', 'forge'),
		),
		'default' => '0'),
		
		array(
		'name' => 'dropdown',
		'label' => __('Display As Dropdown', 'forge'),
		'type' => 'list',
		'choices' => array(
			'0' => __('No', 'forge'),
			'1' => __('Yes', 'forge'),
		),
		'default' => '0'),
	));
	
	return $data;
}