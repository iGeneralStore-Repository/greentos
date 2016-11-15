<?php 

function forge_element_wpwidget_archives($atts){
	$output = '';
	$output .= '<div class="forge-widget">';
	ob_start();
	the_wpwidget('WP_Widget_Archives', $atts);
	$output .= ob_get_clean();
	$output .= '</div>';
	return $output;
}


add_filter('forge_elements', 'forge_element_wpwidget_archives_metadata');
function forge_element_wpwidget_archives_metadata($data){
	$data['widget_archives'] = array(
	'title' => __('Widget: Archives', 'forge'),
	'description' => __('Native WordPress archives widget', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_wpwidget_archives',
	'fields' => array(
		array(
		'name' => 'title',
		'label' => __('Title', 'forge'),
		'type' => 'text',
		'default' => __('Archives')),
		
		array(
		'name' => 'count',
		'label' => __('Post Count', 'forge'),
		'type' => 'text',
		'default' => '5'),
		
		array(
		'name' => 'dropdown',
		'label' => __('Display Style', 'forge'),
		'type' => 'list',
		'choices' => array(
			'0' => __('List', 'forge'),
			'1' => __('Dropdown', 'forge'),
		),
		'default' => '0'),
	));
	
	return $data;
}