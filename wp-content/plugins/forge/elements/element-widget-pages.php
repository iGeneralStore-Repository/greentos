<?php 

function forge_element_wpwidget_pages($atts){
	$output = '';
	$output .= '<div class="forge-widget">';
	ob_start();
	the_wpwidget('WP_Widget_Pages', $atts);
	$output .= ob_get_clean();
	$output .= '</div>';
	return $output;
}


add_filter('forge_elements', 'forge_element_wpwidget_pages_metadata');
function forge_element_wpwidget_pages_metadata($data){
	$data['widget_pages'] = array(
	'title' => __('Widget: Pages', 'forge'),
	'description' => __('Native WordPress pages widget', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_wpwidget_pages',
	'fields' => array(
		array(
		'name' => 'title',
		'label' => __('Title', 'forge'),
		'type' => 'text',
		'default' => __('Pages')),
		
		array(
		'name' => 'sortby',
		'label' => __('Sort By', 'forge'),
		'type' => 'list',
		'choices' => array(
			'post_title' => __('Page title'),
			'menu_order' => __('Menu order'),
			'ID' => __('Page ID'),
		),
		'default' => '0'),
		
		array(
		'name' => 'exclude',
		'label' => __('Exclude', 'forge'),
		'type' => 'text',
		'default' => ''),
	));
	
	return $data;
}