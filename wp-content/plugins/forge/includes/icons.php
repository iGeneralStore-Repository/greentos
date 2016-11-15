<?php 

//Print HTML for an icon
function forge_icon($value, $wrapper = '', $echo = true){
	if($value === '0' || $value === 0 || $value === '') return;
	
	$icon_data = explode('-', $value);
	$font_library = $icon_data[0];
	$font_value = $icon_data[1];
	
	$output = '';
	if($wrapper != '')
		$output .= '<div class="'.$wrapper.'">';
	$output .= forge_get_icon($font_library, html_entity_decode($font_value));
	if($wrapper != '')
		$output .= '</div>';
	
	if($echo == false)
		return $output;
	else
		echo $output;
}


//Retrieve the correct library
function forge_get_icon($library, $value){
	$result = '';
	switch($library){
		case 'fontawesome': 
			$result = forge_icon_library_fontawesome($value); 
		break;
		case 'linearicons': 
			$result = forge_icon_library_linearicons($value); 
		break;
		case 'typicons': 
			$result = forge_icon_library_typicons($value); 
		break;
		default: 
			$result = forge_icon_library_fontawesome($value); 
		break;
	}
	return $result;
}


//Icon library for fontawesome
function forge_icon_library_fontawesome($value){
	wp_enqueue_style('forge-fontawesome');
	return '<span style="font-family:\'forge-fontawesome\'">'.$value.'</span>';
}


//Icon library for linearicons
function forge_icon_library_linearicons($value){
	wp_enqueue_style('forge-linearicons');
	return '<span style="font-family:\'forge-linearicons\'">'.$value.'</span>';
}


//Icon library for typicons
function forge_icon_library_typicons($value){
	wp_enqueue_style('forge-typicons');
	return '<span style="font-family:\'forge-typicons\'">'.$value.'</span>';
}