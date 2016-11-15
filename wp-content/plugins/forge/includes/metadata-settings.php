<?php 

//Define customizer sections
if(!function_exists('forge_metadata_sections')){
	function forge_metadata_sections(){
		$data = array();
		
		$data['forge_builder'] = array(
		'label' => __('Page Builder', 'forge'),
		'description' => __('Customize the behavior of the page builder interface and tune it to suit your workflow.', 'forge'));
		
		$data['forge_licenses'] = array(
		'label' => __('License Keys', 'forge'),
		'description' => __('Add the license keys for your Forge extensions here. By activating your licenses, you will be able to get automatic updates.', 'forge'));
		
		return apply_filters('forge_metadata_sections', $data);
	}
}


//Settings
if(!function_exists('forge_metadata_settings')){
	function forge_metadata_settings($std = null){
		$data = array();
		
		$data['auto_settings'] = array(
		'label' => __('Settings For New Elements', 'forge'),
		'caption' => __('Automatically display the settings panel when adding a new element.', 'forge'),
		'section' => 'forge_builder',
		'default' => '0',
		'type' => 'checkbox');
		
		$data['quick_delete'] = array(
		'label' => __('Quick Delete', 'forge'),
		'caption' => __('Delete elements immediately without asking for confirmation.', 'forge'),
		'section' => 'forge_builder',
		'default' => '0',
		'type' => 'checkbox');
		
		return apply_filters('forge_settings', $data);
	}
}