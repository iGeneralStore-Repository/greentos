<?php 

/* The Content Element */
function forge_element_content($atts, $content = null){
	$output = shortcode_unautop(wpautop(convert_chars(wptexturize(forge_load()->builder()->data()->get_post_content()))));
	return $output;
}


add_filter('forge_elements', 'forge_element_content_metadata');
function forge_element_content_metadata($data){
	$data['content'] = array(
	'title' => __('The Content', 'forge'),
	'description' => __('Content of current post', 'forge'),
	'group' => 'layout',
	'callback' => 'forge_element_content',
	'fields' => array(
	));
	
	return $data;
}