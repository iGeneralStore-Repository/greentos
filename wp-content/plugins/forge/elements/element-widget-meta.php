<?php 

function forge_element_wpwidget_meta($atts){
	$output = '';
	$output .= '<div class="forge-widget">';
	ob_start();
	the_wpwidget('WP_Widget_Meta', $atts);
	$output .= ob_get_clean();
	$output .= '</div>';
	return $output;
}


add_filter('forge_elements', 'forge_element_wpwidget_meta_metadata');
function forge_element_wpwidget_meta_metadata($data){
	$data['widget_meta'] = array(
	'title' => __('Widget: Meta', 'forge'),
	'description' => __('Native WordPress meta widget', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_wpwidget_meta',
	'fields' => array(
		array(
		'name' => 'title',
		'label' => __('Title', 'forge'),
		'type' => 'text',
		'default' => __('meta')),
	));
	
	return $data;
}