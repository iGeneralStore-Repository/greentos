<?php 

//Elements
function forge_metadata_elements($key = null){
	$data = array();
	
	$data = apply_filters('forge_elements', $data);
	
	//Wrapper - special type
	$data['wrapper'] = array(
	'title' => __('Page Settings', 'forge'),
	'description' => __('The main page wrapper.', 'forge'),
	'hierarchical' => true,
	'parent' => array('page'),
	'callback' => 'forge_element_wrapper',
	'fields' => array(
		array(
		'name' => 'css',
		'label' => __('CSS Styling', 'forge'),
		'type' => 'code',
		'default' => ''),
		
		array(
		'name' => 'background',
		'label' => __('Background Color', 'forge'),
		'type' => 'color',
		'default' => ''),
	));
	
	return isset($key) ? $data[$key] : $data;
}


//Element actions
function forge_metadata_element_actions($key = null){
	$data = array();
	$data = apply_filters('forge_element_actions', $data);
	
	$data['edit'] = array(
	'title' => __('Edit', 'forge'),
	'type' => 'button');
	
	$data['copy'] = array(
	'title' => __('Copy', 'forge'),
	'type' => 'button');
	
	// $data['column'] = array(
	// 'title' => __('Column', 'forge'),
	// 'type' => 'button');
	
	return isset($key) ? $data[$key] : $data;
}


function forge_metadata_element_settings($key = null){
	$data = array();
	
	$data[] = array(
	'name' => 'element_animation_entrance',
	'label' => __('Entrance Animation', 'forge'),
	'description' => __('Adds an entrance animation when the user scrolls to this element.', 'forge'),
	'type' => 'list',
	'group' => 'animation',
	'choices' => forge_metadata_element_animations(),
	'default' => 'none');
	
	$data[] = array(
	'name' => 'element_animation_duration',
	'label' => __('Animation Duration', 'forge').' (s)',
	'description' => __('The time it takes for the entrance animation to complete.', 'forge'),
	'type' => 'slider',
	'group' => 'animation',
	'min' => '0',
	'max' => '6',
	'step' => '0.1',
	'default' => '1');
	
	$data[] = array(
	'name' => 'element_animation_delay',
	'label' => __('Animation Delay', 'forge').' (s)',
	'description' => __('The time it takes for the entrance animation to start.', 'forge'),
	'type' => 'slider',
	'group' => 'animation',
	'min' => '0',
	'max' => '20',
	'step' => '0.2',
	'default' => '0');
	
	$data[] = array(
	'name' => 'element_margin_bottom',
	'label' => __('Bottom Margin', 'forge'),
	'description' => __('Sets a bottom margin for this element, in pixels.', 'forge'),
	'type' => 'text',
	'group' => 'misc',
	'placeholder' => '0px',
	'width' => '50px',
	'default' => '30',
	'live' => array(
		'selector' => '.forge-element',
		'property' => 'css',
		'attribute' => 'margin-bottom',
	));
	
	$data[] = array(
	'name' => 'element_id',
	'label' => __('Element ID', 'forge'),
	'description' => __('Sets a custom ID attribute for this element, so that you can reference it from elsewhere.', 'forge'),
	'type' => 'text',
	'group' => 'misc',
	'default' => '');
		
	$data[] = array(
	'name' => 'element_class',
	'label' => __('Element CSS Classes', 'forge'),
	'description' => __('Adds custom CSS classes for styling purposes. You can add any number of classes separated by spaces.', 'forge'),
	'type' => 'text',
	'group' => 'misc',
	'default' => '');
	
	/*$data[] = array(
	'name' => 'element_animation',
	'label' => __('Entrance Animation', 'forge'),
	'type' => 'list',
	'choices' => array(
		'none' => __('(None)', 'forge'),
		'slideup' => __('Slide Upwards', 'forge'),
		'slidedown' => __('Slide Downwards', 'forge'),
		'slideleft' => __('Slide To The Left', 'forge'),
		'slideright' => __('Slide To The Right', 'forge'),
		'zoomin' => __('Zoom In', 'forge'),
		'zoomout' => __('Zoom In', 'forge'),
	),
	'default' => '');*/
		
	$data = apply_filters('forge_common_settings', $data);
	
	return isset($key) ? $data[$key] : $data;
}


function forge_metadata_post_types(){
	//Posts List
	$post_types = get_post_types(array('public' => true), 'objects'); 
	$type_list = array();
	foreach($post_types as $post_type){
		$type_list[$post_type->name] = $post_type->label.' ('.$post_type->name.')';
	}
	return $type_list;
}


function forge_metadata_taxonomy_terms($args = null){
	//Posts List
	if(!is_array($args)){
		$args = array('orderby' => 'term_group', 'order' => 'asc');
	}
	$terms = get_terms($args); 
	$term_list = array();
	$term_list['0'] = __('(None)', 'forge');
	foreach($terms as $term){
		$term_list[$term->taxonomy.':'.$term->term_id] = $term->taxonomy.': '.$term->name;
	}
	return $term_list;
}


function forge_metadata_order_by($args = null){
	$data = array(
	'title' => __('Title', 'forge'),
	'date' => __('Date', 'forge'),
	'menu_order' => __('Menu Order', 'forge'),
	'comment_count' => __('Comments', 'forge'),
	);
	return $data;
}


function forge_metadata_order($args = null){
	$data = array(
	'asc' => __('Ascending', 'forge'),
	'desc' => __('Descending', 'forge'),
	);
	return $data;
}


function forge_metadata_history_actions($action = null, $type = null){
	$data = array(
	'create' => __('Created %s', 'forge'),
	'save' => __('Modified %s', 'forge'),
	'copy' => __('Copied %s', 'forge'),
	'move' => __('Moved %s', 'forge'),
	'delete' => __('Deleted %s', 'forge'),
	'layout' => __('Changed row layout', 'forge'),
	'import' => __('Imported content', 'forge'),
	);
	
	$return = $data;
	if($action != null && $type != null && isset($data[$action])){
		$return = sprintf($data[$action], $type);
	}
	
	return $return;
}


//Buttons on the toolbar
function forge_tools_buttons($key = null){
	$data = array();
	
	$data['settings'] = array('label' => __('Page Settings', 'forge'));
	$data['templates'] = array('label' => __('Templates', 'forge'));
	$data = apply_filters('forge_tools_buttons', $data);
	
	//Help and tools at the bottom
	$data['import'] = array('label' => __('Import', 'forge'));
	$data['export'] = array('label' => __('Export', 'forge'));
	$data['help'] = array('label' => __('Help & Feedback', 'forge'));
	
	return isset($key) ? $data[$key] : $data;
}


//Buttons on the toolbar
function forge_toolbar_buttons($key = null){
	$data = array();
	
	$data['discard'] = array(
	'label' => __('Discard', 'forge'),
	'title' => __('Discard current changes and revert to initial state.', 'forge'),
	'classes' => '');
	
	$data['save'] = array(
	'label' => __('Publish Changes', 'forge'),
	'title' => __('Publish changes to live website.', 'forge'),
	'classes' => '');
	
	$data['close'] = array(
	'title' => __('Exit without publishing. Your changes will be preserved until you return.', 'forge'),
	'classes' => '');
	
	$data = apply_filters('forge_toolbar_buttons', $data);
	
	return isset($key) ? $data[$key] : $data;
}


//Buttons on the toolbar
function forge_metadata_groups($key = null){
	$data = array();
	
	$data['default'] = array(
	'label' => __('General', 'forge'),
	'state' => 'open');
	
	$data['styling'] = array(
	'label' => __('Styling', 'forge'),
	'state' => 'open');
	
	$data['background'] = array(
	'label' => __('Background', 'forge'),
	'state' => 'open');
	
	$data['layout'] = array(
	'label' => __('Layout', 'forge'),
	'state' => 'open');
	
	$data['animation'] = array(
	'label' => __('Animations', 'forge'),
	'description' => __('Add entrance effects when the user scrolls down to this element.', 'forge'),
	'state' => 'closed');
	
	$data['misc'] = array(
	'label' => __('Miscellaneous', 'forge'),
	'state' => 'closed');
	
	$data['icon'] = array(
	'label' => __('Icon', 'forge'),
	'state' => 'open');
	
	$data['query'] = array(
	'label' => __('Query Data', 'forge'),
	'description' => __('Settings for selecting post data.', 'forge'),
	'state' => 'closed');
	
	$data = apply_filters('forge_groups', $data);
	
	return isset($key) ? $data[$key] : $data;
}


//Buttons on the toolbar
function forge_row_layouts($key = null){
	$data = array();
	$data['12'] = '12';
	$data['6'] = '6,6';
	$data['4'] = '4,4,4';
	$data['3'] = '3,3,3,3';
	$data['2'] = '2,2,2,2,2,2';
	
	$data = apply_filters('forge_row_layouts', $data);
	
	return isset($key) ? $data[$key] : $data;
}


//Available animations
function forge_metadata_element_animations(){
	$data = array(
	'none' => __('(None)', 'forge'),
	'slideup' => __('Slide Upwards', 'forge'),
	'slidedown' => __('Slide Downwards', 'forge'),
	'slideleft' => __('Slide To The Left', 'forge'),
	'slideright' => __('Slide To The Right', 'forge'),
	'zoomin' => __('Zoom In', 'forge'),
	'zoomout' => __('Zoom Out', 'forge'),
	);
	return $data;
}


//Buttons on the toolbar
function forge_metadata_post_thumbnails($key = null){
	$data = array('none' => __('(None)', 'forge'));
	foreach(get_intermediate_image_sizes() as $size){
		$data[$size] = $size;
	}
	return $data;
}
