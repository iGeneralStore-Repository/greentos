<?php 

/* Login element */
function forge_element_contactform7($atts, $content = null){
	$attributes = extract(shortcode_atts(array(
	'form' => false,
	'forge_builder' => false,
	), 
	$atts));
	
	$output = '';
	if($form && function_exists('wpcf7_contact_form_tag_func')){
		$output .= wpcf7_contact_form_tag_func(array('id' => $form), null, 'contact-form-7');
	}
	return $output;
}


add_filter('forge_elements', 'forge_element_contactform7_metadata');
function forge_element_contactform7_metadata($data){
	if(defined('WPCF7_VERSION')){
		$data['cf7'] = array(
			'title' => __('Contact Form 7', 'forge'),
			'description' => __('Simple but flexible contact form', 'forge'),
			'group' => 'layout',
			'callback' => 'forge_element_contactform7',
			'fields' => array(
				array(
				'name' => 'form',
				'label' => __('Form', 'forge'),
				'type' => 'list',
				'choices' => forge_element_contactform7_forms(),
				'default' => '0'),
		));
	}
	
	return $data;
}


function forge_element_contactform7_forms(){
	$args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
	$form_list = array();
	if($forms = get_posts($args)){
		foreach($forms as $form){
			$form_list[$form->ID] = $form->post_title;
		}
	}
	return $form_list;
}