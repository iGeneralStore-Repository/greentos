<?php 
/**
 * @package 	WordPress Plugin
 * @subpackage 	CMSMasters Content Composer
 * @version		1.5.3
 * 
 * Composer Functions
 * Created by CMSMasters
 * 
 */


function cmsmasters_composer_load_template($template_name, $args = array()) {
	$template = locate_template($template_name);
	
	
	if (is_array($args) && !empty($args)) {
		extract($args);
	}
	
	
	include($template);
	
	
	return $out;
}


function cmsmasters_composer_ob_load_template($template_name, $args = array()) {
	if (locate_template($template_name)) {
		$template = locate_template($template_name);
		
		
		if (is_array($args) && !empty($args)) {
			extract($args);
		}
		
		
		ob_start();
		
		
		include($template);
		
		
		$out = ob_get_contents();
		
		
		ob_end_clean();
		
		
		return $out;
	}
}

