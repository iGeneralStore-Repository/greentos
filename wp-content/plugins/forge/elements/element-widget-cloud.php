<?php 

function forge_element_wpwidget_tagcloud($atts){
	$output = '';
	$output .= '<div class="forge-widget">';
	ob_start();
	the_wpwidget('WP_Widget_Tag_Cloud', $atts);
	$output .= ob_get_clean();
	$output .= '</div>';
	return $output;
}


add_filter('forge_elements', 'forge_element_wpwidget_tagcloud_metadata');
function forge_element_wpwidget_tagcloud_metadata($data){
	$taxonomies = array();
	foreach(get_taxonomies() as $taxonomy){
		$tax = get_taxonomy($taxonomy);
		if(!$tax->show_tagcloud || empty($tax->labels->name)){
			continue;
		}
		$taxonomies[esc_attr($taxonomy)] = $tax->labels->name;
	}
	
	$data['widget_tagcloud'] = array(
	'title' => __('Widget: Tag Cloud', 'forge'),
	'description' => __('Native WordPress tag cloud widget', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_wpwidget_tagcloud',
	'fields' => array(
		array(
		'name' => 'title',
		'label' => __('Title', 'forge'),
		'type' => 'text',
		'default' => __('Tags')),
		
		array(
		'name' => 'taxonomy',
		'label' => __('Taxonomy', 'forge'),
		'type' => 'list',
		'choices' => $taxonomies,
		'default' => 'post_tag'),
	));
	
	return $data;
}