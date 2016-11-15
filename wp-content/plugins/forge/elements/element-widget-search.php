<?php 

function forge_element_wpwidget_search($atts){
	$output = '';
	$output .= '<div class="forge-widget">';
	ob_start();
	the_wpwidget('WP_Widget_Search', $atts);
	$output .= ob_get_clean();
	$output .= '</div>';
	return $output;
}


add_filter('forge_elements', 'forge_element_wpwidget_search_metadata');
function forge_element_wpwidget_search_metadata($data){
	$data['widget_search'] = array(
	'title' => __('Widget: Search', 'forge'),
	'description' => __('Native WordPress search widget', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_wpwidget_search',
	'fields' => array(
		array(
		'name' => 'title',
		'label' => __('Title', 'forge'),
		'type' => 'text',
		'default' => ''),
	));
	
	return $data;
}