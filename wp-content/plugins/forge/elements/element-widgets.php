<?php 

/* Widget Area Element */
function forge_element_wpwidgets($atts, $content = null){
	$attributes = extract(shortcode_atts(array(
	'area' => 'none',
	), $atts));
	
	$widget_area = esc_attr($area);
	
	
	$output = '<div class="forge-widgets">';
	if(is_active_sidebar($widget_area)){
		ob_start();
		dynamic_sidebar($widget_area);
		$output .= ob_get_clean();
	}
	$output .= '<div class="forge-clear"></div>';
	$output .= '</div>';

	return $output;
}


add_filter('forge_elements', 'forge_element_wpwidgets_metadata');
function forge_element_wpwidgets_metadata($data){
	$data['widgets'] = array(
	'title' => __('Widget Sidebar', 'forge'),
	'description' => __('Sidebar or list of widgets', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_wpwidgets',
	'fields' => array(
		array(
		'name' => 'area',
		'label' => __('Widget Area', 'forge'),
		'type' => 'list',
		'choices' => forge_metadata_wpwidgets_areas(),
		'default' => 'none'),	
	));
	
	return $data;
}


function forge_metadata_wpwidgets_areas(){
	$data = array();
	foreach($GLOBALS['wp_registered_sidebars'] as $sidebar){
		$data[$sidebar['id']] = $sidebar['name'];
	}
	return $data;
}