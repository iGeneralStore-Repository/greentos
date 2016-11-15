<?php 

function forge_element_wpwidget_links($atts){
	$output = '';
	$output .= '<div class="forge-widget">';
	ob_start();
	the_wpwidget('WP_Widget_Links', $atts);
	$output .= ob_get_clean();
	$output .= '</div>';
	return $output;
}


add_filter('forge_elements', 'forge_element_wpwidget_links_metadata');
function forge_element_wpwidget_links_metadata($data){
	$data['widget_links'] = array(
	'title' => __('Widget: links', 'forge'),
	'description' => __('Native WordPress links widget', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_wpwidget_links',
	'fields' => array(
		array(
		'name' => 'title',
		'label' => __('Title', 'forge'),
		'type' => 'text',
		'default' => __('links')),
		
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