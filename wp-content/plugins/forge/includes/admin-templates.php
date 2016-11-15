<?php


//Create settings page
// add_action('admin_enqueue_scripts', 'forge_templating_admin_scripts');
// function forge_templating_admin_scripts($hook){
	// if($hook == 'forge_page_forge_templates'){
		// wp_enqueue_script('forge-templates-admin', FORGE_URL.'scripts/admin.js');
		// wp_enqueue_style('forge-templates-admin', FORGE_URL.'css/admin.css');
	// }
// }


function forge_page_templating(){
    echo '<div class="wrap forge-templates">';
	echo '<h2>'.__('Templates', 'forge').'</h2>';
    
	//Create new template
	echo '<form class="forge-create-template" id="forge-create-template" method="post" style="padding:30px 0 0;">';
	echo '<input type="text" name="template_title" value="" style="padding:5px; width:400px; margin-right:10px;" placeholder="'.__('Enter the name of the template', 'forge').'" required/>';
	echo '<input type="submit" class="button button-primary" name="forge_create_template" value="'.__('Create Template', 'forge').'"/>';
	echo '</form>';
	
	//List of templates
	echo '<div class="forge-templates-list" id="forge-templates-list">';
	echo '<table class="wp-list-table widefat fixed striped">';
	
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col" id="template_title" class="manage-column column-template_title column-primary">'.__('Template Name', 'forge').'</th>';
	echo '<th scope="col" id="template_shortcode" class="manage-column column-template_shortcode">'.__('Shortcode', 'forge').'</th>';
	echo '<th scope="col" id="template_actions" class="manage-column column-template_actions">'.__('Actions', 'forge').'</th>';
	echo '</tr>';
	echo '</thead>';
	
	$templates = new WP_Query('post_type=forge_template&post_per_page=-1&orderby=post_title&order=ASC');
	echo '<tbody id="the-list" data-wp-lists="list:template">';
	foreach($templates->posts as $template){		
		echo '<tr>';
		echo '<td class="template_title column-template_title has-row-actions column-primary">';
		echo '<strong style="display:block;">'.$template->post_title.'</strong>';
		$template_hook = get_post_meta($template->ID, 'template_hook', true);
		if($template_hook != ''){
			echo ' <em>'.sprintf(__('Hooked to %s', 'forge'), $template_hook).'</em>';
		}
		echo '</td>';
		
		echo '<td class="template_shortcode column-template_shortcode" data-colname="Shortcode">';
		echo '<code>[forge_template id="'.$template->ID.'"]</code>';
		echo '</td>';
		echo '<td class="template_actions column-template_actions" data-colname="Actions">';
		echo '<a href="#" class="forge-template-edit button" data-id="'.$template->ID.'">'.__('Embed Template', 'forge').'</a>&nbsp;&nbsp;';
		echo '<a href="'.get_permalink($template->ID).'?forge_builder" class="button">'.__('Open Page Builder', 'forge').'</a>';
        $delete_link = wp_nonce_url(admin_url().'post.php?post='.$template->ID.'&action=delete', 'delete-post_'.$template->ID);
		echo '<a style="color:red; line-height:28px; margin-left:20px;" href="'.$delete_link.'" onclick="return confirm(\''.__('Are you sure you want to permanently delete this template?', 'forge').'\')"">'.__('Delete', 'forge').'</a>';
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	
	echo '</div>';
    echo '</div>'; //End list wrap
	
	//Edit template
	echo '<div class="wrap forge-templates-edit" style="display:none;">';
	echo '<h2>'.__('Edit Template', 'forge').'</h2>';
	echo '<div class="forge-templates-edit-content" id="forge-templates-edit-content"></div>';
    echo '</div>'; //End edit wrap
}


//Create template post type
add_action('init', 'forge_templating_post_types');
function forge_templating_post_types(){
	//Set up labels
	$labels = array('name' => __('Templates', 'forge'),
	'singular_name' => __('Template', 'forge'),
	'add_new' => __('Add Template', 'forge'),
	'add_new_item' => __('Add New Template', 'forge'),
	'edit_item' => __('Edit Template', 'forge'),
	'new_item' => __('New Template', 'forge'),
	'view_item' => __('View Template', 'forge'),
	'search_items' => __('Search Templates', 'forge'),
	'not_found' =>  __('No templates found.', 'forge'),
	'not_found_in_trash' => __('No templates found in the trash.', 'forge'), 
	'parent_item_colon' => '');
	
	$fields = array('labels' => $labels,
	'public' => true,
	'exclude_from_search' => true,
	'publicly_queryable' => true,
	'show_in_nav_menus' => false,
	'show_in_menu' => false,
	'show_ui' => false, 
	'rewrite' => true,
	'rewrite' => array('slug' => 'forge-template'),	
	'query_var' => true,
	'capability_type' => 'page',
	'hierarchical' => false,
	'menu_icon' => 'none',
	'menu_position' => null,
	'supports' => array('title'),
	); 
	
	register_post_type('forge_template', $fields);
}


//Redirect back to admin if trying to view plain template URL
add_action('wp', 'forge_templating_redirect');
function forge_templating_redirect(){
	global $post;
	if(is_singular('forge_template') && (!current_user_can('publish_posts') || !isset($_GET['forge_builder']))){
		wp_redirect(admin_url().'admin.php?page=forge_templates');
		exit;
	}
}


//Create settings page
add_action('admin_init', 'forge_templating_delete');
function forge_templating_delete(){
	if(current_user_can('publish_posts') && isset($_POST['forge_create_template']) && isset($_POST['template_title'])){
		if(!empty($_POST['template_title'])){
			$args = array(
			'post_type' => 'forge_template',
			'post_status' => 'publish',
			'post_title' => esc_attr($_POST['template_title']));
			$new_post = wp_insert_post($args);
			
			$metadata = array('template' => 'blank');
			update_post_meta($new_post, 'forge_builder_settings', $metadata);
			
			wp_redirect(add_query_arg('forge_builder', '', get_permalink($new_post)));
			exit;
		}		
	}
}


//Add TinyMCE button script
add_filter('mce_external_plugins', 'forge_templating_tinymce');  
function forge_templating_tinymce($plugin_array) {  
	$plugin_array['forge_templates'] = FORGE_URL.'scripts/tinymce.js';
	return $plugin_array; 
}


//Add TinyMCE button
add_filter('mce_buttons', 'forge_templating_tinymce_buttons'); 
function forge_templating_tinymce_buttons($button_list){
   array_push($button_list, 'forge_templating_button');
   return $button_list; 
} 	


add_action('admin_head-post.php', 'forge_templating_tinymce_variables');
add_action('admin_head-post-new.php', 'forge_templating_tinymce_variables');
function forge_templating_tinymce_variables(){
	$args = array('post_type' => 'forge_template', 'posts_per_page' => -1);
	$form_list = array(); ?>
	
	<script type='text/javascript'>
	var forge_template_list = [
	{ text:'<?php _e('(Select One)', 'forge'); ?>', value:'0' },
	<?php if($templates = get_posts($args)){
		foreach($templates as $template){
			$id = $template->ID;
			$title = $template->post_title;
			echo "{ text:'$title', value:'$id' },";
		}
	}
	?>];
	</script>
	<?php
}


//Generate a list of checkboxes
function forge_templating_checkbox_list($name, $value, $list){
	$output = '';
	if(sizeof($list) > 0){
		foreach($list as $list_key => $list_value){
			if(is_array($list_value)){
				$disabled = '';
				if(isset($list_value['type']) && $list_value['type'] == 'separator'){
					$output .= '<h5>'.esc_attr($list_value['name']).'</h5>';
				}
			}else{
				$list_key = esc_attr($list_key);
				$current_value = isset($value[$list_key]) ? $value[$list_key] : 0;
				$output .= '<label for="'.$name.'['.$list_key.']">';
				$output .= '<input type="checkbox" id="'.$name.'['.$list_key.']" name="'.$name.'['.$list_key.']" value="1"'.checked($current_value, 1, false).'>';				
				$output .= esc_attr($list_value);
				$output .= '</label>';
			}	
		}
	}
	return $output;
}


//Metadata for displaying on pages
add_action('wp_ajax_forge_request_edit_template_form', 'forge_templating_edit_template_form');
function forge_templating_edit_template_form(){
	if(isset($_POST['template'])){
		$template = intval($_POST['template']);
		
		if($template){
			echo '<form method="post" action="'.admin_url().'admin.php?page=forge_templates">';
			
			//Title and ID
			echo '<input type="hidden" name="template_id" value="'.$template.'">';
			
			//Hook details
			echo '<div class="forge-templates-edit-hook">';
			echo '<h3 class="forge-templates-edit-title">'.__('Place this template on a hook', 'forge').'</h3>';
			echo '<p>'.__('Enter the name of a hook or filter to embed this template onto your page, without modifying your current theme. Any WordPress hook can be used, but you can also use the table below as a quick reference sheet.', 'forge').'</p>';
			echo '<input type="text" name="template_hook" id="template_hook" value="'.get_post_meta($template, 'template_hook', true).'" style="width:300px;" placeholder="'.__('Name of action hook', 'forge').'">';
			echo '<input type="text" name="template_priority" id="template_priority" value="'.get_post_meta($template, 'template_priority', true).'" placeholder="'.__('Priority', 'forge').' (10)">';
			$template_type = get_post_meta($template, 'template_type', true);
			echo '<select name="template_type" id="template_type">';
			echo '<option value="action" '.selected('action', $template_type, false).'>'.__('Action hook', 'forge').'</option>';
			echo '<option value="prepend" '.selected('prepend', $template_type, false).'>'.__('Filter (prepend)', 'forge').'</option>';
			echo '<option value="append" '.selected('append', $template_type, false).'>'.__('Filter (append)', 'forge').'</option>';
			echo '</select>';
			forge_templating_hook_reference();
			echo '</div>';
			
			//Display options
			echo '<div class="forge-templates-edit-display">';
			echo '<h3 class="forge-templates-edit-title">'.__('Select when to display display this template', 'forge').'</h3>';
			echo '<p>'.__('Choose under which conditions this template should be displayed, in the selected hook. Remember to select at least one option or the template will never be displayed.', 'forge').'</p>';
			echo '<div class="forge-templates-edit-display-column">'; //Left col
			echo '<h4 class="forge-templates-edit-heading">'.__('Pages', 'forge').'</h4>';
			echo forge_templating_checkbox_list('template_display', get_post_meta($template, 'template_display', true), forge_templating_metadata_pages());
			echo '<h4 class="forge-templates-edit-heading">'.__('Post Types', 'forge').'</h4>';
			echo forge_templating_checkbox_list('template_display', get_post_meta($template, 'template_display', true), forge_templating_metadata_post_types());
			echo '</div>';
			echo '<div class="forge-templates-edit-display-column">'; //Right col
			echo '<h4 class="forge-templates-edit-heading">'.__('Taxonomies', 'forge').'</h4>';
			echo forge_templating_checkbox_list('template_display', get_post_meta($template, 'template_display', true), forge_templating_metadata_taxonomies());
			echo '</div>';
			echo '</div>';
			
			echo '<div class="forge-templates-edit-submit">';
			echo '<input type="submit" class="button button-primary" name="forge_save_template" value="'.__('Save Template', 'forge').'"/>';
			echo '&nbsp;&nbsp;<a href="#" class="button forge-template-edit-cancel">'.__('Cancel', 'forge').'</a>';
			echo '</div>';
			
			echo '</form>';
			die();
		}
	}
}


//Save template settings
add_action('admin_init', 'forge_templating_save_template');
function forge_templating_save_template(){
	if(current_user_can('publish_posts') && isset($_POST['forge_save_template'])){
		if(isset($_POST['template_id']) && $_POST['template_id'] != ''){
			$template_id = intval($_POST['template_id']);
			
			$data = array('template_hook', 'template_priority', 'template_type', 'template_display');
			foreach($data as $field){
				if(isset($_POST[$field])){
					if($field == 'template_display' || $_POST[$field] != ''){
						update_post_meta($template_id, $field, $_POST[$field]);
					}else{
						delete_post_meta($template_id, $field);
					}
				}
			}
		}
	}
}


function forge_templating_hook_reference(){
	$locations = forge_templating_metadata_hooks();
	$tab_content = '';
	$active_class = ' forge-templates-tab-active';
	
	echo '<div class="forge-templates-tabs">';
	echo '<div class="forge-templates-tab-menu">';
	foreach($locations as $current_key => $current_location){
		$location_key = esc_attr($current_key);
		$location_name = isset($current_location['name']) ? esc_attr($current_location['name']) : __('(Unknown)', 'forge');
		$location_description = isset($current_location['description']) ? esc_attr($current_location['description']) : '';
		$location_image = isset($current_location['image']) ? esc_url($current_location['image']) : '';
		echo '<div class="forge-templates-tab'.$active_class.'" rel="#forge-templates-tab-content-'.$location_key.'">';
		if($location_image != ''){
			echo '<img class="forge-templates-tab-image" src="'.$location_image.'">';
		}
		echo '<div class="forge-templates-tab-title">'.$location_name.'</div>';
		echo '<div class="forge-templates-tab-description">'.$location_description.'</div>';
		echo '</div>';
		
		$tab_content .= '<div class="forge-templates-tab-group'.$active_class.'" id="forge-templates-tab-content-'.$location_key.'">';
		foreach($current_location['hooks'] as $hook_key => $hook_description){
			$hook_key = esc_attr($hook_key);
			$hook_description = esc_html($hook_description);
			$tab_content .= '<div class="forge-templates-tab-content" rel="'.$hook_key.'">';
			$tab_content .= '<div class="forge-templates-tab-content-title">'.$hook_key.'</div>';
			$tab_content .= '<div class="forge-templates-tab-content-description">'.$hook_description.'</div>';
			$tab_content .= '</div>';
		}
		$tab_content .= '</div>';
		$active_class = '';
	}
	echo '</div>';
	echo '<div class="forge-templates-tab-body">';
	echo $tab_content;
	echo '</div>';
	echo '</div>';
}