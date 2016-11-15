<?php

//Render settings page
function forge_presets_page(){
	echo '<div class="wrap">';
	echo '<h2>'.__('Theme Presets', 'forge').'</h2>';
	
	echo '<div class="forge-presets">';
	
	echo '<div class="forge-preset forge-preset-info">';
	echo __('Presets are ready-made page layouts that you can quickly import into your website. Select the ones you wish to use and then click on Load Preset. You can also choose whether to create a completely new page, or to use an existing one.', 'forge');
	echo '</div>';
	
	$presets = forge_presets()->get_presets();
	$settings = forge_presets()->get_settings();
	if(!empty($presets)){
		$pages = get_pages();
		do_action('forge_before_presets');
		foreach($presets as $preset_id => $preset_fields){
			$preset_title = isset($preset_fields['title']) ? esc_attr($preset_fields['title']) : '';
			$preset_description = isset($preset_fields['description']) ? esc_attr($preset_fields['description']) : false;
			
			//Get settings for current preset
			$preset_page = isset($settings[$preset_id]) ? intval($settings[$preset_id]) : false;
			
			echo '<div class="forge-preset">';
			
			//Body
			echo '<div class="forge-preset-body">';
			echo '<h3 class="forge-preset-title">'.$preset_title.'</h3>';
			echo '<div class="forge-preset-description">'.$preset_description.'</div>';
			echo '</div>';
			
			//Success message
			echo '<div class="forge-preset-success">'.$preset_description.'</div>';
			
			//Meta
			echo '<form class="forge-preset-meta forge-preset-form" method="post">';
			echo '<div class="forge-preset-meta-field">';
			echo '<label><input type="checkbox" name="preset_home" value="1"/>'.__('Set As Homepage', 'forge').'</label>';
			echo '</div>';
			
			echo '<div class="forge-preset-meta-field">';
			echo '<select name="postid">';
			echo '<option value="new">'.__('(Create A New Page)', 'forge').'</div>';
			foreach($pages as $page){
				echo '<option value="'.$page->ID.'">'.$page->post_title.'</div>';
			}
			echo '</select>';
			echo '</div>';
			
			echo '<div class="forge-preset-meta-field">';
			echo '<input type="hidden" name="preset_id" value="'.$preset_id.'"/>';
			echo '<input type="submit" class="button forge-preset-select" value="'.__('Load Preset', 'forge').'"/>';
			echo '</div>';
			echo '</form>';
			
			echo '</div>'; //End Preset
		}
		do_action('forge_after_presets');
	}
	
	echo '</div>';
	echo '</div>';
}


add_action('wp_ajax_forge_request_load_preset', 'forge_presets_load');
function forge_presets_load(){
	if(defined('DOING_AJAX')){
		if(isset($_POST['fields'])){
			parse_str($_POST['fields'], $fields);
		}else{
			die();
		}
		
		$preset_id = isset($fields['preset_id']) ? esc_attr($fields['preset_id']) : false;
		$preset_page = isset($fields['postid']) ? $fields['postid'] : false;
		
		if($preset_id && $preset_page){
			//Retrieve preset
			$preset = forge_presets()->get_preset($preset_id);
			
			if($preset){
				$page_id = $preset_page;
				
				//Create new page if not chosen
				if($preset_page == 'new'){
					$args = array(
					'post_title' => $preset['title'],
					'post_type' => 'page',
					'post_status' => 'publish');
					$page_id = wp_insert_post($args);	
				}
				
				//Update page metadata
				if(isset($preset['metadata'])){
					foreach($preset['metadata'] as $meta_key => $meta_value){
						update_post_meta($page_id, $meta_key, $meta_value);
					}
				}
				
				//Change to homepage
				if(isset($fields['preset_home']) && $fields['preset_home'] == 1){
					update_option('page_on_front', $page_id);
					update_option('show_on_front', 'page');
				}
				
				
				//Load content
				if(forge_presets()->load_preset($preset_id, $page_id)){
					$link_reload = '<a target="_blank" class="forge-preset-reload" href="#">'.__('load preset again', 'forge').'</a>';
					$link_post = '<a target="_blank" href="'.get_permalink($page_id).'">'.__('View post', 'forge').'</a>';
					$output = sprintf(__('The preset has been loaded. %1s or %2s.', 'forge'), $link_post, $link_reload);
					echo json_encode(array(
					'status' => true,
					'content' => $output,
					));
					die();
				}
			}
		}
		echo json_encode(array('status' => false));
		die();
	}
}