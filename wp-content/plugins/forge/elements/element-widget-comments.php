<?php 

function forge_element_wpwidget_comments($atts){
	$output = '';
	$output .= '<div class="forge-widget">';
	ob_start();
	the_wpwidget('WP_Widget_Recent_Comments', $atts);
	$output .= ob_get_clean();
	$output .= '</div>';
	return $output;
}


add_filter('forge_elements', 'forge_element_wpwidget_comments_metadata');
function forge_element_wpwidget_comments_metadata($data){
	$data['widget_comments'] = array(
	'title' => __('Widget: Recent Comments', 'forge'),
	'description' => __('Native WordPress comments widget', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_wpwidget_comments',
	'fields' => array(
		array(
		'name' => 'title',
		'label' => __('Title', 'forge'),
		'type' => 'text',
		'default' => __('Recent Comments')),
		
		array(
		'name' => 'number',
		'label' => __('Number', 'forge'),
		'type' => 'text',
		'default' => '5'),
	));
	
	return $data;
}