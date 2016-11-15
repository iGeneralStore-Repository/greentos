<?php

//Add toolbar on pages
add_action('admin_bar_menu', 'forge_toolbar_link', 90);
function forge_toolbar_link($wp_admin_bar){
	if(current_user_can('edit_posts') && is_user_logged_in() && (is_single() || is_singular())){
		global $post;
		
		$args = array(
		'id' => 'forge-edit',
		'title' => __('Forge Page Builder', 'forge'),
		'href' => add_query_arg(array('forge_builder' => '')),
		'meta' => array('class' => 'forge-toolbar-page'));
		$wp_admin_bar->add_node($args);
	}
}


//Disable admin bar on builder
add_filter('show_admin_bar', 'forge_admin_bar', 999);
function forge_admin_bar($data){
	if(isset($_GET['forge_builder'])){
		return false;
	}
	return $data;
}


//Abstracted function for retrieving specific options inside option arrays
function forge_get_option($option_name = '', $option_array = 'forge_settings'){
	$option_list_name = $option_array;
	
	$option_list = get_option($option_list_name, false);
	
	$option_value = '';
	//If options exists and is not empty, get value
	if($option_list && isset($option_list[$option_name]) && (is_bool($option_list[$option_name]) === true || $option_list[$option_name] !== '')){
		$option_value = $option_list[$option_name];
	}
	
	//If option is empty, check whether it needs a default value
	if($option_value === '' || !isset($option_list[$option_name])){
		$options = forge_metadata_settings();
		//If option cannot be empty, use default value
		if(!isset($options[$option_name]['empty'])){
			if(isset($options[$option_name]['default'])){
				$option_value = $options[$option_name]['default'];
			}
		//If it can be empty but not set, use default value
		}elseif(!isset($option_list[$option_name])){
			if(isset($options[$option_name]['default'])){
				$option_value = $options[$option_name]['default'];
			}
		}
	}
	return $option_value;
}


//Abstracted function for updating specific options inside arrays
function forge_update_option($option_name, $option_value, $option_array = 'forge_settings'){
	$option_list_name = $option_array;
	$option_list = get_option($option_list_name, false);
	if(!$option_list)
		$option_list = array();
	$option_list[$option_name] = $option_value;
	if(update_option($option_list_name, $option_list))
		return true;
	else
		return false;
}


//Custom function to do some cleanup on nested shortcodes
//Used for columns and layout-related shortcodes
function forge_do_shortcode($content){ 
	$content = do_shortcode(shortcode_unautop($content)); 
	$content = preg_replace('#^<\/p>|^<br\s?\/?>|<p>$|<p>\s*(&nbsp;)?\s*<\/p>#', '', $content);
	return $content;
}


//Retrieves and returns the shortcode prefix with a trailing underscore
function forge_shortcode_prefix(){ 
	$prefix = forge_get_option('shortcode_prefix'); 
	if($prefix != '') $prefix = esc_attr($prefix).'_';
	return $prefix;
}


//Returns the appropriate URL, either from a string or a post ID
function forge_image_url($id, $size = 'full'){ 
	$url = '';
	if(is_numeric($id)){
		$url = wp_get_attachment_image_src($id, $size);
		$url = $url[0];
	}else{
		$url = $id;
	}
	return $url;
}


//Returns the appropriate color
function forge_color($color){ 
	//Return the correct system color: primary, secondary, highlight, headings, body
	switch($color){
		case 'primary': return forge_settings()->get('primary_color'); break;
		case 'secondary': return forge_settings()->get('secondary_color'); break;
		case 'highlight': return forge_settings()->get('highlight_color'); break;
		case 'headings': return forge_settings()->get('headings_color'); break;
		case 'body': return forge_settings()->get('body_color'); break;
	}
	return $color;
}


//Changes the brighness of a HEX color
function forge_alter_brightness($colourstr, $steps) {
	$colourstr = str_replace('#','',$colourstr);
	$rhex = substr($colourstr,0,2);
	$ghex = substr($colourstr,2,2);
	$bhex = substr($colourstr,4,2);

	$r = hexdec($rhex);
	$g = hexdec($ghex);
	$b = hexdec($bhex);

	$r = max(0,min(255,$r + $steps));
	$g = max(0,min(255,$g + $steps));  
	$b = max(0,min(255,$b + $steps));
  
	$r = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
	$g = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);  
	$b = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
	return '#'.$r.$g.$b;
}


//Sort two elements based on position attribute
function forge_sort_element_position($a, $b) {
	return $a->position - $b->position;
}


//Sort two elements based on ascending featured attribute, then title
function forge_sort_collection($a, $b) {
	$featured_a = isset($a['featured']) ? intval($a['featured']) : 0;
	$featured_b = isset($b['featured']) ? intval($b['featured']) : 0;
	
	if($featured_a && $featured_b){
		return $featured_a - $featured_b;
	}elseif($featured_a != $featured_b){
		return $featured_b - $featured_a;
	}else{
		return strcmp($a['title'], $b['title']);
	}
}
