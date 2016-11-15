<?php 

function forge_element_wpwidget_rss($atts){
	$output = '';
	$output .= '<div class="forge-widget">';
	ob_start();
	the_wpwidget('WP_Widget_RSS', $atts);
	$output .= ob_get_clean();
	$output .= '</div>';
	return $output;
}


add_filter('forge_elements', 'forge_element_wpwidget_rss_metadata');
function forge_element_wpwidget_rss_metadata($data){
	$data['widget_rss'] = array(
	'title' => __('Widget: RSS', 'forge'),
	'description' => __('Native WordPress RSS widget', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_wpwidget_rss',
	'fields' => array(
		array(
		'name' => 'title',
		'label' => __('Title', 'forge'),
		'type' => 'text',
		'default' => ''),
		
		array(
		'name' => 'url',
		'label' => __('Feed URL', 'forge'),
		'type' => 'text',
		'default' => ''),
		
		array(
		'name' => 'items',
		'label' => __('Number of Items', 'forge'),
		'type' => 'text',
		'default' => '5'),
		
		array(
		'name' => 'show_summary',
		'label' => __('Show Summary', 'forge'),
		'type' => 'list',
		'choices' => array(
			'0' => __('No', 'forge'),
			'1' => __('Yes', 'forge'),
		),
		'default' => '0'),
		
		array(
		'name' => 'show_author',
		'label' => __('Show Author', 'forge'),
		'type' => 'list',
		'choices' => array(
			'0' => __('No', 'forge'),
			'1' => __('Yes', 'forge'),
		),
		'default' => '0'),
		
		array(
		'name' => 'show_date',
		'label' => __('Show Date', 'forge'),
		'type' => 'list',
		'choices' => array(
			'0' => __('No', 'forge'),
			'1' => __('Yes', 'forge'),
		),
		'default' => '0'),
	));
	
	return $data;
}