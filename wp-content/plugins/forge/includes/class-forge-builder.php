<?php

class Forge_Builder {
	
	//The original content of the current post
	protected $post_content = false;
	
	//The data model
	protected $data = false;
	
	//Array of Javascript assets
	protected $assets_js = array();
	
	//Array of CSS assets
	protected $assets_css = array();
	
	
	//Constructor
	public function __construct($id = false, $type = 'post', $args = null){
		$this->data = new Forge_Data($id, $type);
		$this->hooks();
	}
	
	
	//Initialize hooks
	public function hooks(){
		add_action('wp_enqueue_scripts', array($this, 'scripts'));
		add_filter('the_content', array($this, 'render_content'), 99999);
		add_action('wp_footer', array($this, 'render_toolbar'));		
		add_action('wp_footer', array($this, 'render_collection'));		
		add_action('wp_footer', array($this, 'render_form'));		
		add_action('wp_footer', array($this, 'render_multiselect'));		
		add_action('wp_enqueue_scripts', array($this, 'scripts'));
		add_action('wp_enqueue_scripts', array($this, 'styles'));
		add_action('template_redirect', array($this, 'template'), 8);
		
		//Special actions for elements
		add_filter('forge_element_actions_row', array($this, 'row_actions'));
		
		//AJAX-only hooks
		if($this->data->ajax()){
			//Rows and special elements
			add_action('wp_ajax_forge_request_row_layout', array($this, 'request_row_layout'));
			add_action('wp_ajax_forge_request_create_child', array($this, 'request_create_child'));
			//Elements
			add_action('wp_ajax_forge_request_create_element', array($this, 'request_create_element'));
			add_action('wp_ajax_forge_request_edit_element', array($this, 'request_edit_element'));
			add_action('wp_ajax_forge_request_move_element', array($this, 'request_move_element'));
			add_action('wp_ajax_forge_request_copy_element', array($this, 'request_copy_element'));
			add_action('wp_ajax_forge_request_delete_element', array($this, 'request_delete_element'));
			add_action('wp_ajax_forge_request_update_history', array($this, 'request_update_history'));
			//Templates
			add_action('wp_ajax_forge_request_add_template', array($this, 'request_insert_template'));
			//Settings Form
			add_action('wp_ajax_forge_request_save_form', array($this, 'request_save_form'));
			//Page-level Settings
			add_action('wp_ajax_forge_request_settings', array($this, 'request_settings'));
			add_action('wp_ajax_forge_request_export', array($this, 'request_export'));
			add_action('wp_ajax_forge_request_import', array($this, 'request_import'));
			add_action('wp_ajax_forge_request_history', array($this, 'request_history'));
			add_action('wp_ajax_forge_request_save', array($this, 'request_save'));
			add_action('wp_ajax_forge_request_discard', array($this, 'request_discard'));
			//Live editing refresh by AJAX
			add_action('wp_ajax_forge_request_refresh', array($this, 'request_refresh'));
		}
	}
	
	
	public function scripts() {
		wp_register_script('forge-general', FORGE_URL.'scripts/general.js', array('jquery'), false, true);
		wp_register_script('forge-builder', FORGE_URL.'scripts/builder.js', array('jquery', 'jquery-ui-slider', 'jquery-ui-sortable', 'jquery-ui-resizable', 'jquery-ui-draggable'), false, true);
		wp_register_script('forge-builder-interface', FORGE_URL.'scripts/builder-interface.js', array('jquery'), false, true);
		wp_register_script('forge-colorpicker', FORGE_URL.'scripts/jquery-colorpicker.js', array('jquery'), false, true);
		wp_register_script('forge-cycle', FORGE_URL.'scripts/jquery-cycle2.js', array('jquery'), false, true);
		wp_register_script('forge-magnific', FORGE_URL.'scripts/jquery-magnific-min.js', array('jquery'), false, true);
		wp_register_script('forge-waypoints', FORGE_URL.'scripts/jquery-waypoints-min.js', array('jquery'), false, true);
		
		if($this->data->builder_active()){
			wp_enqueue_script('editor');
			wp_enqueue_script('quicktags');
			wp_enqueue_script('buttons');
			wp_enqueue_script('thickbox');
			
			wp_enqueue_media();
			
			wp_enqueue_script('forge-colorpicker');
			wp_enqueue_script('forge-waypoints');
			wp_enqueue_script('forge-cycle');
			wp_enqueue_script('forge-general');
			wp_enqueue_script('forge-builder');
			wp_enqueue_script('forge-builder-interface');
			wp_localize_script('forge-builder', 'Forge_Builder_Settings', array(
				'request_url' => admin_url('admin-ajax.php'),
				'admin_url' => admin_url(),
				'builder_auto_settings' => forge_settings()->get('auto_settings'),
				'builder_quick_delete' => forge_settings()->get('quick_delete'),
			));
			
			wp_localize_script('forge-builder', 'Forge_Builder_Strings', array(
				'publish_changes' => __('Publish Changes', 'forge'),
				'publishing' => __('Publishing...', 'forge'),
				'publish_done' => __('Saved!', 'forge'),
				'delete_confirm' => __('Are you sure you want to delete this element?', 'forge'),
				'discard_confirm' => __('Are you sure you want to discard the changes made to the layout? The content will revert to its live state.', 'forge'),
				'close_confirm' => __('Close the editor? Your changes will be preserved until you come back again.', 'forge'),
				'select_image' => __('Select Image', 'forge'),
			));
		}
	}
	
	
	function styles(){
		wp_register_style('forge-styles', FORGE_URL.'css/style.css');
		wp_register_style('forge-builder', FORGE_URL.'css/builder.css');
		//Icon fonts
		wp_register_style('forge-fontawesome', FORGE_URL.'css/icon-fontawesome.css');
		wp_register_style('forge-linearicons', FORGE_URL.'css/icon-linearicons.css');
		wp_register_style('forge-typicons', FORGE_URL.'css/icon-typicons.css');
		
		if($this->data->builder_active() || $this->data->using_builder()){
			wp_enqueue_style('forge-styles');
			if($this->data->builder_active()){
				wp_enqueue_media();
				wp_enqueue_style('forge-general');
				wp_enqueue_style('forge-builder');
				wp_enqueue_style('forge-fontawesome');
				wp_enqueue_style('forge-linearicons');
				wp_enqueue_style('forge-typicons');
			}
		}
	}
	
	
	//Add scripts and dependencies
	public function init(){
		if($this->data->builder_active()){
			//Disable caching when builder is active, especially Minify
			if(!defined('DONOTCACHEDB')){ define('DONOTCACHEDB', true);	}
			if(!defined('DONOTCACHEPAGE')){ define('DONOTCACHEPAGE', true); }
			if(!defined('DONOTMINIFY')){ define('DONOTMINIFY', true); }
			if(!defined('FORGE_DEBUG')){ define('FORGE_DEBUG', false); }
			
			//Remove sumome on builder
			if(class_exists('WP_Plugin_SumoMe')){
				global $wp_plugin_sumome;				
				remove_action('wp_head', array($wp_plugin_sumome, 'append_script_code'));
			}
		}
	}
	
	
	//Render post content if using builder
	public function render_content($content){
		//Check if usingdata from content 
		if((get_the_ID() == $this->data->get_post()) && ($this->data->builder_active() || $this->data->using_builder())){
			return $this->generate();
		}else{
			return $content;
		}
	}
	
	
	//Build layout from array
	public function generate(){
		wp_enqueue_style('forge-styles');
		
		$count = 0;
		$output = '';
		$output .= '<div class="forge-block forge-wrapper" data-element="0" data-position="0" data-type="wrapper">';
		$elements = $this->data->get_elements();		
		
		//Add custom styling
		$output .= '<style>';
		
		//Add page-level CSS
		$page_settings = $elements[0]->settings;
		if(isset($page_settings['css']) && $page_settings['css'] != ''){
			$output .= $page_settings['css'];
		}
		
		$primary_color = forge_settings()->get('primary_color');
		$secondary_color = forge_settings()->get('secondary_color');
		$highlight_color = forge_settings()->get('highlight_color');
		$headings_color = forge_settings()->get('headings_color');
		$body_color = forge_settings()->get('body_color');
		
		$output .= '.forge-primary-color { color:'.$primary_color.' !important; }';
		$output .= '.forge-primary-color-bg, .forge-colorpicker-preset-primary { background-color:'.$primary_color.' !important; }';
		
		$output .= '.forge-secondary-color { color:'.$secondary_color.' !important; }';
		$output .= '.forge-secondary-color-bg, .forge-colorpicker-preset-secondary { background-color:'.$secondary_color.' !important; }';
		
		$output .= '.forge-highlight-color { color:'.$highlight_color.' !important; }';
		$output .= '.forge-highlight-color-bg, .forge-colorpicker-preset-highlight { background-color:'.$highlight_color.' !important; }';
		
		$output .= '.forge-headings-color { color:'.$headings_color.' !important; }';
		$output .= '.forge-headings-color-bg, .forge-colorpicker-preset-headings { background-color:'.$headings_color.' !important; }';
		
		$output .= '.forge-body-color { color:'.$body_color.' !important; }';
		$output .= '.forge-body-color-bg, .forge-colorpicker-preset-body { background-color:'.$body_color.' !important; }';
		
		$output .= '</style>';
		
		$output .= '<input type="hidden" id="forge-primary-color" value="'.$primary_color.'">';
		$output .= '<input type="hidden" id="forge-secondary-color" value="'.$secondary_color.'">';
		$output .= '<input type="hidden" id="forge-highlight-color" value="'.$highlight_color.'">';
		$output .= '<input type="hidden" id="forge-headings-color" value="'.$headings_color.'">';
		$output .= '<input type="hidden" id="forge-body-color" value="'.$body_color.'">';
		
		$page_style = '';
		if(isset($page_settings['background']) && $page_settings['background'] != ''){
			$page_style .= ' background:'.$page_settings['background'].';';
		}
		
		$output .= '<div class="forge-wrapper-content" style="'.$page_style.'">';
		$output .= '<div class="forge-block-content">';
		
		if($elements){
			foreach($elements as $current_key => $current_element){
				//Generate top-level elements
				$current_parent = isset($current_element->parent) ? esc_attr($current_element->parent) : 0;
				if(!$current_parent && $current_element->type != 'column'){
					$output .= $this->generate_element($current_element);
				}	
			}
		}
		
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		
		return $output;
	}


	//Build an element from array
	public function generate_element($element){
		$output = '';
		$element_type = $element->type;
		
		//If a method for the current element exists, use it
		if(method_exists($this, 'generate_element_'.$element_type)){
			$output .= call_user_func(array($this, 'generate_element_'.$element_type), $element);
		}else{
			$output .= $this->generate_element_single(apply_filters('forge_render_element', $element));
		}
		return $output;
	}


	//Build a single element from array
	public function generate_element_single($element){
		$output = '';
		
		$element_type = $element->type;
		$metadata = $this->data->get_metadata();
		$elements = $this->data->get_elements();
		
		if(!empty($element_type) && isset($metadata[$element_type])){
			//Get element library and find the right one
			$element_metadata = $metadata[$element_type];
			
			//Generate element metadata
			$element_data = '';
			if($this->data->builder_active()){
				$element_data .= ' data-element="'.$element->id.'"';
				$element_data .= ' data-position="'.$element->position.'"';
				$element_data .= ' data-parent="'.$element->parent.'"';
				$element_data .= ' data-type="'.$element->type.'"';
				$element_data .= ' data-title="'.$element_metadata['title'].'"';
			}
			
			//Add universal element settings
			$element_settings = $element->settings;
			
			//Margins
			$margin_bottom = ' margin-bottom:0;';
			if(isset($element_settings['element_margin_bottom'])){
				$margin_bottom = ' margin-bottom:'.intval($element_settings['element_margin_bottom']).'px;';
			}
			
			//ID attribute
			$element_id = '';
			if(isset($element_settings['element_id'])){
				$element_id = ' id="'.esc_attr($element_settings['element_id']).'"';
			}
			
			//CSS Classes
			$element_classes = '';
			if(isset($element_settings['element_class'])){
				$element_classes = esc_attr($element_settings['element_class']);
			}
			
			//Element Classes
			$css_classes = 'forge-element';
			if(isset($element_metadata['classes'])){
				$css_classes = esc_attr($element_metadata['classes']);
			}
			
			$animation_classes = '';
			$animated_element = false;
			
			//Entrance animation attribute
			if(!$this->data->builder_active() && isset($element_settings['element_animation_entrance']) && $element_settings['element_animation_entrance'] != 'none'){
				$animation_classes .= ' forge-animation-'.esc_attr($element_settings['element_animation_entrance']);
				$animated_element = true;
			}
			
			//Animation speed attribute
			$animation_duration = '';
			if(!$this->data->builder_active() && isset($element_settings['element_animation_duration']) && $element_settings['element_animation_duration'] != ''){
				$animation_duration = ' transition-duration:'.esc_attr($element_settings['element_animation_duration']).'s;';
			}
			
			//Animation speed attribute
			$animation_delay = '';
			if(!$this->data->builder_active() && isset($element_settings['element_animation_delay']) && $element_settings['element_animation_delay'] != ''){
				$animation_delay = ' transition-delay:'.esc_attr($element_settings['element_animation_delay']).'s;';
			}
			
			if($animated_element){
				wp_enqueue_script('forge-waypoints');
				wp_enqueue_script('forge-general');
				$animation_classes .= ' forge-animation';
			}
			
			$element_style = ' style="'.$margin_bottom.$animation_duration.$animation_delay.'"';
			
			//Add element wrappers
			$output .= '<div class="forge-block '.$css_classes.' forge-element-'.$element_type.' '.$element_classes.$animation_classes.'"'.$element_id.$element_data.$element_style.'>';
			
			$output .= $this->generate_assets();
			
			//Add admin interface
			$output .= $this->generate_builder_overlay($element);
			
			//Get current element metadata and add builder attributes, so that elements can use them
			$element_callback = $element_metadata['callback'];
			$element_attr = isset($element->settings) ? $element->settings : array();
			if($this->data->builder_active()){
				$element_attr['forge_builder'] = true;
			}else{
				$element_attr['forge_builder'] = false;
			}
			$element_attr['forge_post_id'] = $this->data->get_post();
			$element_attr['forge_element_id'] = $element->id;
			$element_attr['forge_element_parent'] = $element->parent;
			$element_attr['forge_element_position'] = $element->position;
			
			//Format color fields
			foreach($element_metadata['fields'] as $current_field){
				if($current_field['type'] == 'color'){
					if(isset($element_attr[$current_field['name']])){
						$element_attr[$current_field['name']] = forge_color($element_attr[$current_field['name']]);
					}
				}
			}
			
			//Hierarchical elements (that can contain other elements)
			$hierarchical = isset($element_metadata['hierarchical']) ? true : false;
			
			//elements with children (that have a children interface)
			$children = isset($element_metadata['children']) ? true : false;
			
			$element_content = '';
			
			//Clear all assets before rendering
			$this->clean_assets();
			
			//Nesting shortcodes
			if($hierarchical || $children){
				$element_content_inner = '';
				
				//Add wrapper for children, if applicable
				$element_child_wrapper = '';
				if(isset($element_metadata['children_wrapper'])){
					$element_child_wrapper = esc_attr($element_metadata['children_wrapper']);
				}
				
				//Loop each child of corresponding parent
				foreach($elements as $current_key => $current_element){
					if($current_element->parent === $element->id){
						if($element_child_wrapper){
							$element_content_inner .= '<div class="'.$element_child_wrapper.'">';
						}
						$element_content_inner .= $this->generate_element($current_element);
						if($element_child_wrapper){
							$element_content_inner .= '</div>';
						}
					}
				}
				
				//Check if element forbids children from being dragged
				if(isset($element_metadata['children_draggable']) && !$element_metadata['children_draggable']){
					$element_content .= $element_content_inner;
				}else{
					$element_content .= '<div class="forge-block-content">'.$element_content_inner.'</div>';
				}
			}else{
				$element_content = isset($element->settings['content']) ? $element->settings['content'] : null;	
			}
				
			//Generate shortcode callback
			if(function_exists($element_callback)){
				$output .= call_user_func($element_callback, $element_attr, $element_content, $elements);
			}
			
			//Close element wrappers
			$output .= '<div class="forge-clear"></div>';
			$output .= '</div>'; //End forge-block
		}
		
		return $output;
	}


	//Render the builder overlay for a single element
	public function generate_builder_overlay($element){
		if($this->data->builder_active()){
			$output = '';
		
			$elements_metadata = $this->data->get_metadata();
			$elements = $this->data->get_elements();
			
			$metadata = $elements_metadata[$element->type];
			$item_title = esc_attr($metadata['title']);
			
			$item_children = isset($metadata['children']) ? $metadata['children'] : false;
			$item_parent = isset($metadata['parent']) ? $metadata['parent'] : false;
			
			if(!$item_parent){
				$output .= '<div class="forge-builder-overlay">';
				
				$element_actions = apply_filters('forge_element_actions_'.$element->type, forge_metadata_element_actions());
				
				$output .= '<div class="forge-builder-actions">';
				foreach($element_actions as $action_key => $action_value){
					$action_title = isset($action_value['title']) ? esc_attr($action_value['title']) : '';
					$action_type = isset($action_value['type']) ? $action_value['type'] : 'button';
					if($action_type == 'button'){
						$output .= '<div class="forge-builder-actions-button forge-builder-actions-'.$action_key.'">'.$action_title.'</div>';
					}elseif($action_type == 'dropdown' && isset($action_value['list'])){
						
						//Dropdown button, for extra options
						$output .= '<div class="forge-builder-actions-button forge-builder-dropdown">';
						$output .= '<div class="forge-builder-columns-title">'.$action_title.'</div>';
						$output .= '<div class="forge-builder-dropdown-list">';
						//List of dropdown
						foreach($action_value['list'] as $list_key => $list_value){
							$action_title = isset($list_value['title']) ? esc_attr($list_value['title']) : '';
							$action_class = isset($list_value['class']) ? $list_value['class'] : '';
							$action_type = isset($list_value['type']) ? $list_value['type'] : 'button';
							$action_data = '';
							if(isset($list_value['data'])){
								foreach($list_value['data'] as $data_key => $data_value){
									$action_data .= ' data-'.esc_attr($data_key).'="'.esc_attr($data_value).'"';
								}
							}
							$output .= '<div class="forge-builder-actions-'.$list_key.' '.$action_class.'"'.$action_data.'>'.$action_title.'</div>';
						}								
						$output .= '</div>';
						$output .= '</div>';
					}
				}
				$output .= '<div class="forge-builder-actions-delete"></div>';
				
				$output .= '</div>';
				
				if(false && $item_children){
					$output .= '<div class="forge-builder-children">';
					$count = 1;
					foreach($elements as $current_key => $current_element){
						if($current_element->parent == $element->id){
							$output .= '<div class="forge-builder-child" data-element="'.$current_element->id.'">'.$count.'</div>';
							$count++;
						}
					}
					$output .= '<div class="forge-builder-child-create">+</div>';
					$output .= '</div>';
				}
						
				
				//Element title
				$output .= '<div class="forge-builder-overlay-title">'.$item_title.'</div>';
				
				$output .= '</div>';
				
				//Margin controls
				// $output .= '<div class="forge-builder-actions-margin forge-builder-actions-margin-bottom"></div>';
			}
			return $output;
		}
	}


	//Add the builder interface panel
	public function render_collection(){
		if($this->data->builder_active()){
			$output = '';
		
			$metadata = $this->data->get_metadata();
			
			//Element Collection
			$output .= '<div class="forge-builder-collection forge-builder-panel">';
			$output .= '<div class="forge-builder-collection-list">';
			
			$output .= apply_filters('forge_before_collection', $output);
			
			//Render elements
			unset($metadata['template']);
			foreach($metadata as $metadata_key => $metadata_value){
				$metadata_title = isset($metadata_value['title']) ? esc_attr($metadata_value['title']) : '';
				$metadata_desc = __('Element', 'forge');
				$metadata_desc = isset($metadata_value['description']) ? esc_attr($metadata_value['description']) : '';
				$metadata_featured = isset($metadata_value['featured']) && $metadata_value['featured'] == true ? ' forge-builder-collection-item-featured' : '';
				$metadata_parent = isset($metadata_value['parent']) ? $metadata_value['parent'] : false;
				
				if($metadata_title != '' && !$metadata_parent){
					$element_data = '';
					$element_data .= ' data-name="'.strtolower($metadata_title).'"';
					$element_data .= ' data-type="'.$metadata_key.'"';
					$output .= '<div class="forge-builder-collection-item'.$metadata_featured.'"'.$element_data.'>';
					$output .= '<div class="forge-builder-collection-body">';
					$output .= '<div class="forge-builder-collection-icon forge-builder-collection-icon-'.$metadata_key.'"></div>';
					$output .= '<div class="forge-builder-collection-title">'.$metadata_title.'</div>';
					$output .= '<div class="forge-builder-collection-desc">'.$metadata_desc.'</div>';
					$output .= '</div>';
					$output .= '</div>';
				}
			}
			
			if(!$this->data->template_builder){
				$query = new WP_Query('post_type=forge_template&posts_per_page=-1&order=ASC&orderby=title');
				if($query->posts){
					foreach($query->posts as $post){
						$metadata_title = $post->post_title;
						$metadata_id = $post->ID;
						$metadata_desc = __('Template', 'forge-templates');
						
						$element_data = '';
						$element_data .= ' data-name="templates '.strtolower($metadata_title).'"';
						$element_data .= ' data-type="template"';
						$element_data .= ' data-template="'.$metadata_id.'"';
						$output .= '<div class="forge-builder-collection-item forge-builder-collection-item-template"'.$element_data.'>';
						if(has_post_thumbnail($post->ID)){
							$output .= '<div class="forge-builder-collection-image">';
							$output .= get_the_post_thumbnail($post->ID, 'medium');
							$output .= '</div>';
						}
						$output .= '<div class="forge-builder-collection-body">';
						$output .= '<div class="forge-builder-collection-title">'.$metadata_title.'</div>';
						$output .= '<div class="forge-builder-collection-desc">'.$metadata_desc.'</div>';
						$output .= '</div>';
						$output .= '</div>';
					}
				}
			}
			
			$output .= apply_filters('forge_after_collection', $output);
			
			$output .= '</div>';
			$output .= '</div>';
			
			echo $output;
		}
	}
	
	
	//Add the builder interface panel
	public function render_form(){
		if($this->data->builder_active()){
			$output = '';
		
			//Settings Form
			$output .= '<form class="forge-builder-form" id="forge-builder-form" method="post">';
			$output .= '<input type="hidden" id="forge-field-post" name="forge-field-post" value="'.$this->data->get_post().'">';
			$output .= '<input type="hidden" id="forge-field-action" name="forge-field-action" value="element">';
			$output .= '<input type="hidden" id="forge-field-redirect" name="forge-field-redirect" value="'.get_permalink($this->data->get_post()).'">';
			$output .= '<div class="forge-builder-form-container" id="forge-builder-form-container"></div>'; //Content wrapper for fields
			$output .= '<div class="forge-builder-faux-editor" style="display:none;">';
			//TODO: Initialize editor cleanly
			remove_all_filters('mce_buttons');
			ob_start();
			wp_editor(' ', 'forgebaseeditor', array('wpautop' => true));
			$output .= ob_get_clean();
			$output .= '</div>';
			$output .= '</form>';
			
			echo $output;
		}
	}
	
	
	//Add the builder interface panel
	public function render_multiselect(){
		if($this->data->builder_active()){
			$output = '';
		
			//Settings Form
			$output .= '<div class="forge-builder-multiselect" id="forge-builder-multiselect">';
			$output .= '<input type="hidden" id="forge-multiselect-elements" name="forge-multiselect-elements" value="">';
			$output .= '<div id="forge-builder-multiselect-edit" class="forge-builder-button forge-builder-multiselect-edit">'.sprintf(__('Edit %s Selected Elements', 'forge'), '<span></span>').'</div>';
			$output .= '<div id="forge-builder-multiselect-clear" class="forge-builder-button forge-builder-multiselect-clear">'.__('Clear Selection', 'forge').' <sup><small>ESC</small></sup></div>';
			// $output .= '<div id="forge-builder-multiselect-content" class="forge-builder-multiselect-content"></div>';
			$output .= '</div>';
			
			echo $output;
		}
	}
	
	
	//Add the builder interface panel
	public function render_toolbar(){
		if($this->data->builder_active()){
			$output = '';
		
			$output .= '<div class="forge-builder-modal-overlay"></div>';
			$output .= '<div class="forge-builder-toolbar">';
			
			//Element Collection
			$output .= '<div class="forge-builder-button forge-builder-collection-open"></div>';
			$output .= '<div class="forge-builder-button forge-builder-collection-close"></div>';
			$output .= '<input type="text" class="forge-builder-search" id="forge-builder-search" placeholder="'.__('Search Elements...', 'forge').'">';
			//Tools dropdown
			$output .= '<div class="forge-builder-menu forge-builder-tools">';
			$output .= '<div class="forge-builder-button forge-builder-tools-button"></div>';
			$output .= '<div class="forge-builder-menu-list">';
			$tools = forge_tools_buttons();
			foreach($tools as $tools_key => $tools_value){
				$tools_label = isset($tools_value['label']) ? esc_attr($tools_value['label']) : '';
				$tools_title = isset($tools_value['title']) ? esc_attr($tools_value['title']) : '';
				$output .= '<div class="forge-builder-menu-item forge-builder-actions-'.$tools_key.'" title="'.$tools_title.'">'.$tools_label.'</div>';
			}
			$output .= '</div>';
			$output .= '</div>';
			
			//render history dropdown
			$output .= $this->render_history();
			
			$output .= '<div class="forge-builder-loading"></div>';
			
			//Button Toolbar
			$output .= '<div class="forge-builder-buttons">';
			$toolbar = forge_toolbar_buttons();
			foreach($toolbar as $toolbar_key => $toolbar_value){
				$toolbar_label = isset($toolbar_value['label']) ? esc_attr($toolbar_value['label']) : '';
				$toolbar_title = isset($toolbar_value['title']) ? esc_attr($toolbar_value['title']) : '';
				$output .= '<div class="forge-builder-button forge-builder-actions-'.$toolbar_key.'" title="'.$toolbar_title.'">'.$toolbar_label.'</div>';
			}
			$output .= '</div>';
			$output .= '</div>';
			echo $output;
		}
	}
	
	
	//Render the history interface
	public function render_history(){
		$output = '';
		$output .= '<div id="forge-builder-history" class="forge-builder-menu forge-builder-history">';
		
		$output .= '<div class="forge-builder-button forge-builder-undo-button"></div>';
		$output .= '<div class="forge-builder-menu-list">';
		$output .= '<div class="forge-builder-menu-list-history">';
		
		$settings = $this->data->get_settings();
		$metadata = $this->data->get_metadata();
		
		//Previous states
		if(isset($settings['history'])){
			$change_history = $settings['history'];
		}else{
			$change_history = array();
		}
		
		if(!empty($change_history)){
			$change_history = array_reverse($change_history, true);
			$current_history = isset($settings['current']) ? $settings['current'] : 'now';
			foreach($change_history as $history_key => $history_value){
				if(is_array($history_value)){
					//Check if this is the current state
					$current = ' forge-builder-actions-history';
					if($current_history == $history_key){
						$current = ' forge-builder-history-current';
					}
					$output .= '<div class="forge-builder-menu-item '.$current.'" data-history="'.$history_key.'">';
					if(isset($metadata[$history_value['type']]['title'])){
						$action_title = esc_attr($metadata[$history_value['type']]['title']);
					}else{
						$action_title = __('Element', 'forge');
					}
					$output .= forge_metadata_history_actions($history_value['action'], $action_title);
					$output .= '<span>'.date('H:i:s', $history_key).'</span>';
					$output .= '</div>';
				}
			}
		}else{
			$output .= '<div class="forg-builder-menu-item">';
			$output .= __('No previous actions in history.', 'forge');
			$output .= '</div>';
		}
		
		$output .= '</div>';
		$output .= '<div class="forge-builder-loading"></div>';
		$output .= '</div>';
		$output .= '</div>';
		
		return $output;
	}


	//Return settings field for a new element through AJAX
	public function request_create_element(){
		$element_type = isset($_POST['type']) ? esc_attr($_POST['type']) : false;
		$element_parent = isset($_POST['parent']) ? esc_attr($_POST['parent']) : false;
		$element_position = isset($_POST['position']) ? esc_attr($_POST['position']) : false;
		
		//Generate element
		$new_element = $this->data->insert_element($element_type, $element_parent, $element_position);
		
		if($new_element){
			$output = array(
			'layout' => $this->generate_element($new_element), 
			'form' => $this->generate_settings($new_element), 
			'settings' => $new_element);

			//Return values
			if(defined('DOING_AJAX')){
				echo json_encode($output);
				die();
			}
		}
	}
	
	
	//Edit existing element
	public function request_edit_element(){
		$element_id = isset($_POST['element']) ? esc_attr($_POST['element']) : false;
			
		if($element_id !== false){
			
			//If using multiselect
			if(strstr($element_id, ',')){
				// $element = $this->data->get_element($element_id);
				$element_ids = explode(',', $element_id);
				
				$element_list = $this->data->get_element($element_ids);
				// $element = $this->data->get_element($element_ids[0]);
				$element_settings = $this->generate_settings($element_list, true);
				$element = (object) array('id' => '');
			}else{				
				$element = $this->data->get_element($element_id);
				$element_settings = $this->generate_settings($element);
				
			}
			
			if($element){
				$output = array(
				'form' => $element_settings, 
				'settings' => $element);

				//Return values
				if(defined('DOING_AJAX')){
					echo json_encode($output);
					die();
				}
			}else{ 
				die(); 
			}
		}
	}


	//Edit existing element
	public function request_move_element(){
		$element_id = isset($_POST['element']) ? esc_attr($_POST['element']) : false;
		$new_parent = isset($_POST['new_parent']) ? esc_attr($_POST['new_parent']) : false;
		$old_ordering = isset($_POST['old_ordering']) ? esc_attr($_POST['old_ordering']) : '';
		$new_ordering = isset($_POST['new_ordering']) ? esc_attr($_POST['new_ordering']) : '';
		
		//Todo: remove new/old ordering from the sent fields
		if($element_id){
			$element = $this->data->move_element($element_id, $new_parent, $new_ordering, $old_ordering);
		}
	}

	
	//Open page settings
	public function request_settings(){
		
		//Render options form
		$output = '';
		$output .= '<div class="forge-builder-form-title">'.__('Page Settings', 'forge').'</div>';
		
		//Assign default values to element
		//$settings_list = $this->data->get_settings();
		$settings_list = forge_metadata_page();
		foreach($settings_list as $current_field){		
			$field_type = $current_field['type'];
			$field_callback = 'forge_field_'.$field_type;
			
			if(isset($current_field['name']) && function_exists($field_callback)){
				$setting_value = isset($this->settings[$current_field['name']]) ? $this->settings[$current_field['name']] : false;
				$setting_label = isset($current_field['label']) ? esc_attr($current_field['label']) : false;
				
				$output .= '<div class="forge-builder-form-field">';
				if($setting_label){
					$output .= '<div class="forge-builder-form-field-title">'.$setting_label.'</div>';
				}
				$output .= '<div class="forge-builder-form-field-body">';
				$output .= call_user_func($field_callback, $current_field, $setting_value);
				$output .= '</div>';
				$output .= '</div>';
			}
		}
		$output .= '<input type="hidden" id="forge-field-element" name="forge-field-element" value="page">';
		
		$settings = array(
		'id' => '0',
		'parent' => '',
		'position' => '0',
		'context' => 'page',
		);
		
		$result = array(
		'form' => $output,
		'settings' => $settings
		);

		//Return values
		if(defined('DOING_AJAX')){
			echo json_encode($result);
			die();
		}
	}
	
	
	//Open page settings
	public function request_import(){
		
		//Render options form
		$output = '';
		
		$output .= '<div class="forge-builder-form-group forge-builder-form-group-open">';
		$output .= '<div class="forge-builder-form-group-content">';
		$output .= '<div class="forge-builder-form-field">';
		$output .= '<p>'.__('Paste the export code from another page in the following field. The new contents will be appended at the end of the post.', 'forge').'</p>';
		$output .= '<div class="forge-builder-form-field-body">';
		$output .= '<textarea name="forge_field_content" class="forge-field-code" rows="26"></textarea>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		
		$args = array(
		'title' => __('Import', 'forge'),
		'submit' => __('Import Content', 'forge'),
		'context' => 'import',
		);
		$output = $this->generate_form($output, $args);
		
		$settings = array(
		'id' => '0',
		'parent' => '',
		'position' => '0');
		
		$result = array(
		'form' => $output,
		'settings' => $settings);

		//Return values
		if(defined('DOING_AJAX')){
			echo json_encode($result);
			die();
		}
	}
	

	//Open page settings
	public function request_export(){
		$elements = $this->data->get_elements();
		//$new_elements = clone $elements;
		$metadata = $this->data->get_metadata();
		
		//Repurpose image IDs for portability
		foreach($elements as $element_key => $element_value){
			$element_type = $element_value->type;
			if(isset($element_value->settings) && isset($metadata[$element_type]['fields'])){
				$fields = $metadata[$element_type]['fields'];
				//Check each setting
				foreach($element_value->settings as $setting_key => $setting_value){
					foreach($fields as $field){
						if($field['name'] == $setting_key){
							if($field['type'] == 'image' && $setting_value != ''){
								//If type is an image, get real URL
								$elements[$element_key]->settings[$setting_key] = forge_image_url($setting_value);
							}elseif($field['type'] == 'gallery'){
								//If gallery, resolve each URL
								$images = explode(',', $setting_value);
								$new_images = '';
								foreach($images as $current_image){
									if(trim($current_image) != ''){
										$new_images .= str_replace(',', '%2C', forge_image_url($current_image)).',';
									}
								}
								$elements[$element_key]->settings[$setting_key] = $new_images;
							}
						}
					}				
				}				
			}
		}
		
		
		
		$page_code = base64_encode(serialize($elements));
		
		//Render options form
		$output = '';
		$output .= '<div class="forge-builder-form-group forge-builder-form-group-open">';
		$output .= '<div class="forge-builder-form-group-content">';
		$output .= '<div class="forge-builder-form-field">';
		$output .= '<p>'.__('Copy the following code to export all content from this post. You can then use the Import function in a different page or website to add these elements.', 'forge').'</p>';
		$output .= '<div class="forge-builder-form-field-body">';
		$output .= '<textarea class="forge-field-code forge-click-selectall" rows="26">'.$page_code.'</textarea>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		
		//Generate full form
		$args = array('title' => __('Export', 'forge'), 'submit' => false);
		$output = $this->generate_form($output, $args);
		
		$settings = array(
		'id' => '0',
		'parent' => '',
		'position' => '0');
		
		$result = array(
		'form' => $output,
		'settings' => $settings);

		//Return values
		if(defined('DOING_AJAX')){
			echo json_encode($result);
			die();
		}
	}
	

	//Save form changes by routing to correct function based in context
	public function request_save_form(){
		//Detect current context
		$element_context = isset($_POST['context']) && $_POST['context'] != 'form' ? 'request_save_'.esc_attr($_POST['context']) : die();
		if(method_exists('Forge_Builder', $element_context)){
			call_user_func(array(__CLASS__, $element_context));
		}
	}
	
	
	//Save form changes
	public function request_save_element(){
		$element_id = isset($_POST['element']) ? esc_attr($_POST['element']) : 0;
		$element_settings = false;
		if(isset($_POST['settings'])){
			parse_str($_POST['settings'], $element_settings);
		}
		
		//If using multiselect
		$multiselect = false;
		if(strstr($element_id, ',')){
			$element_ids = explode(',', $element_id);
			$current_element = array();
			$multiselect = true;
			
			//Remove empty options when using multiselect, to avoid overwrites
			$new_settings = array();
			foreach($element_settings as $setting_key => $setting_value){
				if(isset($element_settings[$setting_key.'_multiselect'])){
					$new_settings[$setting_key] = $setting_value;
				}
			}
			
			//Apply settings to each element
			foreach($element_ids as $current_id){
				if(trim($current_id) != ''){
					$current_element[$current_id] = $this->data->update_element($current_id, $new_settings);					
				}
			}
		}else{		
			
			//Normal, single-element saving
			$current_element = $this->data->update_element($element_id, $element_settings);
		}
		
		if($current_element){
			if($element_id === '0' || $multiselect){
				$current_element = $this->data->get_element(0);
				$element_layout = $this->generate();
			}else{
				$element_layout = $this->generate_element($current_element);
			}
			
			$output = array(
			'layout' => $element_layout, 
			// 'form' => $this->generate_settings($current_element), 
			'settings' => $current_element);

			//Return values
			if(defined('DOING_AJAX')){
				echo json_encode($output);
				die();
			}
			
		}else{ 
			die(); 
		}
	}
	
	
	//Copy element through AJAX
	public function request_copy_element(){
		$element_id = isset($_POST['element']) ? esc_attr($_POST['element']) : false;
		
		if($element_id){
			
			$new_element = $this->data->copy_element($element_id);
			
			//Copy given element
			if($new_element){
				
				$output = array(
				'layout' => $this->generate_element($new_element), 
				'settings' => $new_element, 
				'original' => $element_id);
				
				//Return values
				if(defined('DOING_AJAX')){
					echo json_encode($output);
					die();
				}
			}else{ 
				die(); 
			}
		}
	}


	//Delete element through AJAX
	public function request_delete_element(){
		$element_id = isset($_POST['element']) ? esc_attr($_POST['element']) : false;
		if($element_id){
			//Delete given element and children
			$this->data->delete_element($element_id);
			
			echo true;
			die();
		}	
	}

	
	//Save import
	public function request_save_import(){
		if(isset($_POST['settings'])){
			parse_str($_POST['settings'], $element_settings);
		}
		
		if(isset($element_settings['forge_field_content']) && $element_settings['forge_field_content'] != ''){
			$error = false;
			
			//Decode the string and validate
			$import_content = base64_decode($element_settings['forge_field_content'], true);
			if(!$import_content){
				$error = true;
			}
			
			//Ensure it is properly formed
			$import_content = @unserialize($import_content);
			if(!is_array($import_content) || $import_content === false){
				$error = true;
			}
			
			if(!$error){
				//Remove top-level element
				unset($import_content['0']);
				
				//Get highest position, add it to all new top-level elements (to append at end of page)
				$highest_position = $this->get_highest_position(0);
				if($highest_position == 0){
					$highest_position++;
				}
				
				//Add elements
				$this->data->import_elements($import_content, '0', $highest_position);
			}
			
			$current_element = $this->data->get_element('0');
			
			$output = array(
			'layout' => $this->generate(), 
			//'form' => $this->generate_settings($current_element), 
			'settings' => $this->data->get_element('0'));
			
			//Return values
			if(defined('DOING_AJAX')){
				echo json_encode($output);
				die();
			}
		}
	}
	

	//Create a child element in a nested object
	public function request_create_child(){
		$parent_id = isset($_POST['parent']) ? esc_attr($_POST['parent']) : false;
		
		if($parent_id){
			
			if(isset($elements[$parent_id])){
				$parent_element = $elements[$parent_id];
			}else{
				die();
			}
			
			$element_type = false;
			if(isset($this->metadata[$parent_element->type]['children']) && $this->metadata[$parent_element->type]['children'] != ''){
				$element_type = $this->metadata[$parent_element->type]['children'];
			}
			
			$element_position = $this->get_highest_position($parent_id);
			
			//Generate element
			$new_element = $this->create_element($element_type, $parent_id, $element_position);
			
			
			$this->data->save_progress();
			
			if($new_element){
				$output = array(
				'layout' => $this->generate_element($parent_element), 
				'settings' => $new_element);

				//Return values
				if(defined('DOING_AJAX')){
					echo json_encode($output);
					die();
				}
			}
		}
	}
	
	
	//Change number of columns
	public function request_row_layout(){
		$element_id = isset($_POST['element']) ? esc_attr($_POST['element']) : 0;
		$element_layout = isset($_POST['layout']) ? esc_attr($_POST['layout']) : 0;
		
		if($this->data->change_row_layout($element_id, $element_layout)){
			
			$current_element = $this->data->get_element($element_id);
			
			$output = array(
			'layout' => $this->generate_element($current_element), 
			'settings' => $current_element);
			
			//Return values
			if(defined('DOING_AJAX')){
				echo json_encode($output);
				die();
			}
			
		}else{ 
			die(); 
		}
	}
	
	
	function generate_element_column($element){
		$attributes = extract(shortcode_atts(array(
		'size' => '6',
		'padding' => '',
		'color' => 'light',
		'background' => '',
		'image' => '',
		'border' => '',
		'element_id' => '',
		'element_class' => '',
		), 
		$element->settings));
		
		//Builder metadata
		$element_data = '';
		if($this->data->builder_active()){
			$element_data .= ' data-element="'.$element->id.'"';
			$element_data .= ' data-parent="'.$element->parent.'"';
			$element_data .= ' data-type="column"';			
		}		
		
		
		//ID attribute
		if($element_id != ''){
			$element_id = ' id="'.esc_attr($element_id).'"';
		}
		
		//CSS Classes
		if($element_class != ''){
			$element_class = ' '.esc_attr($element_class);
		}
		
		//Set values
		$element_size = 'forge-col'.esc_attr($size);
		$element_color = ' forge-'.esc_attr($color);
		$element_background = '';
		$element_image = '';
		$element_border = '';
		$element_padding = '';
		
		//Background
		if($image != ''){
			$element_image = ' background-image:url('.forge_image_url($image).');';
		}elseif($background != ''){
			$element_background = ' background-color:'.$background.';';
		}
		
		if($border != ''){
			$element_border = ' border:'.esc_attr($border).';';
		}
		
		//Paddings
		$padding_top = isset($padding['top']) ? esc_attr($padding['top']) : '';
		$padding_left = isset($padding['left']) ? esc_attr($padding['left']) : '';
		$padding_right = isset($padding['right']) ? esc_attr($padding['right']) : '';
		$padding_bottom = isset($padding['bottom']) ? esc_attr($padding['bottom']) : '';
		if($padding_top != ''){
			if(strpos($padding_top, '%') === false){
				$padding_top = intval($padding_top).'px';
			}
			$element_padding .= ' padding-top:'.$padding_top.';';
		}
		if($padding_right != ''){
			if(strpos($padding_right, '%') === false){
				$padding_right = intval($padding_right).'px';
			}
			$element_padding .= ' padding-right:'.$padding_right.';';
		}
		if($padding_bottom != ''){
			if(strpos($padding_bottom, '%') === false){
				$padding_bottom = intval($padding_bottom).'px';
			}
			$element_padding .= ' padding-bottom:'.$padding_bottom.';';
		}
		if($padding_left != ''){
			if(strpos($padding_left, '%') === false){
				$padding_left = intval($padding_left).'px';
			}
			$element_padding .= ' padding-left:'.$padding_left.';';
		}
		
		//Bring together all styles
		$body_styling = ' style="'.$element_border.$element_background.$element_image.'"';
		$content_styling = ' style="'.$element_padding.'"';
		
		
		//Render column
		$output = '';
		$output .= '<div class="forge-block forge-undraggable forge-col '.$element_size.$element_color.$element_class.'"'.$element_data.$element_id.'>';
		$output .= '<div class="forge-col-body"'.$body_styling.'>';
		
		//Add builder interface
		if($this->data->builder_active()){
			$output .= '<div class="forge-builder-overlay forge-col-overlay">';
			$output .= '<div class="forge-builder-actions">';
			$output .= '<div class="forge-builder-actions-button forge-builder-actions-edit">'.__('Column Settings', 'forge').'</div>';
			$output .= '</div>';
			$output .= '</div>';
		}
		
		//Generate column contents
		$output .= '<div class="forge-col-content"'.$content_styling.'>';
		$output .= '<div class="forge-block-content">';
		foreach($this->data->get_elements() as $current_key => $current_element){
			if($current_element->parent == $element->id){
				$output .= $this->generate_element($current_element);
			}
		}
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		
		$output .= '</div>';	
		
		return $output;
	}
	
	
	//Save page changes and exit
	public function request_save(){
		if(defined('DOING_AJAX')){
			echo json_encode($this->data->publish_page());
		}
		die();
	}
	
	
	//Save page changes and exit
	public function request_discard(){
		if(defined('DOING_AJAX')){
			echo json_encode($this->data->discard_page());
		}
		die();
	}


	//Create a settings form for a specific element
	public function generate_settings($element, $multiselect = false){
		$output = '';
		$groups = forge_metadata_groups();
		$current_group = false;
		$old_group = false;
		$started = false;
		
		if($multiselect){
			//If using multiselect, get similar settings only
			$settings_list = array();
			
			foreach($element as $current_element){
				$element_settings[$current_element->type] = $this->data->get_element_settings($current_element->type);
			}
			
			$element_number = sizeof($element_settings);
			//Loop through each element type, only use the ones that have same name, type, and default.
			foreach($element_settings as $current_element_settings){
				//Loop through each set of settings
				foreach($current_element_settings as $current_setting){
					$has_setting = 0;
					//Loop through other elements and check if setting exists
					foreach($element_settings as $compare_element_settings){
						foreach($compare_element_settings as $compare_setting){
							$result = array_diff($current_setting ,$compare_setting);
							if(empty($result)){
								$has_setting++;
							}						
						}
					}
					if($has_setting == $element_number){
						$settings_list[$current_setting['name']] = $current_setting;
					}
				}
			}
		}else{
			//single element only
			$settings_list = $this->data->get_element_settings($element->type);	
		}
		
		//Do all settings without groups
		$metadata = $this->data->get_metadata();
		$existing_groups = array();
		
		
		if($multiselect){
			$form_title = __('Selected Elements', 'forge');
			$form_id = '';
			foreach($element as $current_element){
				$form_id .= ','.$current_element->id;
			}
		}else{
			$form_title = $metadata[$element->type]['title'];
			$form_id = $element->id;
		}
		
		//Record all different groups into array
		$setting_groups = array();
		foreach($settings_list as $current_field){
			$current_group = isset($current_field['group']) && isset($current_field['group']) ? $current_field['group'] : 'none';
			if(!in_array($current_group, $setting_groups)){
				$setting_groups[] = $current_group;
			}
		}
		
		foreach($setting_groups as $group){
			$current_group = $groups[$group];
			$open_group = true;
			$group_label = isset($current_group['label']) ? $current_group['label'] : false;
			$group_description = isset($current_group['description']) ? $current_group['description'] : false;
			$group_state = !$group_label || isset($current_group['state']) && $current_group['state'] == 'open' ? ' forge-builder-form-group-open' : '';
			if($group === 'none'){
				$group_state = ' forge-builder-form-group-open';
			}
			
			$output .= '<div class="forge-builder-form-group'.$group_state.'">';
			if($group_label){
				$output .= '<div class="forge-builder-form-group-title">';
				$output .= '<div class="forge-builder-form-group-toggle"></div>';
				$output .= '<div class="forge-builder-form-group-label">'.$group_label.'</div>';
				if($group_description){
					$output .= '<div class="forge-builder-form-group-description">'.$group_description.'</div>';
				}
				$output .= '</div>';
			}
			$output .= '<div class="forge-builder-form-group-content">';
			
			//Loop through settings in current group
			foreach($settings_list as $current_field){
				$setting_group = isset($current_field['group']) && isset($current_field['group']) ? $current_field['group'] : 'none';
				
				if($setting_group == $group){
					if($multiselect){
						$setting_value = false;
					}else{
						$setting_value = isset($element->settings[$current_field['name']]) ? $element->settings[$current_field['name']] : false;
					}
					
					$output .= $this->generate_setting_single($current_field, $setting_value, $multiselect);
				}
			}
			
			$output .= '</div>';
			$output .= '</div>';
		}
		
		//Close groups
		// $output .= '</div>';
		// $output .= '</div>';
		
		$args = array('title' => $form_title);
		if($multiselect){
			$args['class'] = 'forge-builder-multiselect-body';
			$output .= '<input type="hidden" id="forge-field-multiselect" name="forge-field-multiselect" value="1">';			
		}
		$output .= '<input type="hidden" id="forge-field-element" name="forge-field-element" value="'.$form_id.'">';
		
		
		$output = $this->generate_form($output, $args);
		
		return $output;
	}
	
	
	//Generate output for a specific setting
	public function generate_setting_single($current_field, $setting_value, $multiselect = false){
		$field_type = $current_field['type'];
		$field_callback = 'forge_field_'.$field_type;
		
		if(isset($current_field['name']) && function_exists($field_callback)){
			$setting_label = isset($current_field['label']) ? esc_attr($current_field['label']) : false;
			$setting_description = isset($current_field['description']) ? esc_attr($current_field['description']) : false;
			
			//Live edit fields
			$live_editing = isset($current_field['live']) ? ' data-live="true"' : '';
			$live_selector = isset($current_field['live']['selector']) ? ' data-live-selector="'.esc_attr($current_field['live']['selector']).'"' : ' data-live-selector=""';
			$live_property = isset($current_field['live']['property']) ? ' data-live-property="'.esc_attr($current_field['live']['property']).'"' : ' data-live-property=""';
			$live_attribute = isset($current_field['live']['attribute']) ? ' data-live-attribute="'.esc_attr($current_field['live']['attribute']).'"' : ' data-live-attribute=""';
			$live_format_value = isset($current_field['live']['format']) ? esc_attr($current_field['live']['format']) : '';
			$live_format = ' data-live-format="'.$live_format_value.'"';
			
			//Choices edit field
			$live_choices = '';
			if(isset($current_field['choices'])){
				foreach($current_field['choices'] as $current_choice => $current_choice_value){
					$choice_value = $current_choice;
					if($live_format_value != ''){
						$choice_value = str_replace('%VALUE%', $choice_value, $live_format_value);
					}
					$live_choices .= ' '.$choice_value;
				}
				$live_choices = ' data-live-choices="'.trim($live_choices).'"';
			}
			
			//General live property class
			if(isset($current_field['live'])){
				$current_field['live_field'] = ' forge-live-field';
			}
			
			$output = '';
			$output .= '<div class="forge-builder-form-field"'.$live_editing.$live_selector.$live_property.$live_format.$live_attribute.$live_choices.'>';
			
			if($multiselect){
				$output .= '<label class="forge-builder-form-field-multiselect" title="'.__('Leave unchecked to avoid making changes to this option across all elements.', 'forge').'">';
				$output .= '<input type="checkbox" name="forge_field_'.$current_field['name'].'_multiselect" class="forge-builder-multiselect-checkbox" value="1">';
				// $output .= __('Modify this option', 'forge');
				$output .= '</label>';
			}
			
			//Add description
			if($setting_description && !$multiselect){
				$output .= '<div class="forge-builder-form-field-description">';
				$output .= '<div class="forge-builder-form-field-description-icon">?</div>';
				$output .= '<div class="forge-builder-form-field-tooltip">'.$setting_description.'</div>';
				$output .= '</div>';
			}
			
			//Add label
			if($setting_label || $multiselect){
				$output .= '<div class="forge-builder-form-field-title">'.$setting_label.'</div>';
			}
			$output .= '<div class="forge-builder-form-field-body">';
			$output .= call_user_func($field_callback, $current_field, $setting_value);
			$output .= '</div>';
			$output .= '</div>';
		}
		return $output;
	}
	
	
	public function generate_settings_old_nogroups($element){
		$output = '';
		
		//Assign default values to element
		$settings_list = $this->data->get_element_settings($element->type);
		foreach($settings_list as $current_field){
			$field_type = $current_field['type'];
			$field_callback = 'forge_field_'.$field_type;
			
			if(isset($current_field['name']) && function_exists($field_callback)){
				$setting_value = isset($element->settings[$current_field['name']]) ? $element->settings[$current_field['name']] : false;
				$setting_label = isset($current_field['label']) ? esc_attr($current_field['label']) : false;
				$setting_description = isset($current_field['description']) ? esc_attr($current_field['description']) : false;
				
				//Live edit fields
				$live_selector = isset($current_field['live']['selector']) ? ' data-live-selector="'.esc_attr($current_field['live']['selector']).'"' : '';
				$live_property = isset($current_field['live']['property']) ? ' data-live-property="'.esc_attr($current_field['live']['property']).'"' : '';
				$live_attribute = isset($current_field['live']['attribute']) ? ' data-live-attribute="'.esc_attr($current_field['live']['attribute']).'"' : '';
				$live_format_value = isset($current_field['live']['format']) ? esc_attr($current_field['live']['format']) : '';
				$live_format = ' data-live-format="'.$live_format_value.'"';
				
				//Choices edit field
				$live_choices = '';
				if(isset($current_field['choices'])){
					foreach($current_field['choices'] as $current_choice => $current_choice_value){
						$choice_value = $current_choice;
						if($live_format_value != ''){
							$choice_value = str_replace('%VALUE%', $choice_value, $live_format_value);
						}
						$live_choices .= ' '.$choice_value;
					}
					$live_choices = ' data-live-choices="'.trim($live_choices).'"';
				}
				
				//General live property class
				if(isset($current_field['live']['property']) && isset($current_field['live']['selector'])){
					$current_field['live_field'] = ' forge-live-field';
				}
				
				
				$output .= '<div class="forge-builder-form-field"'.$live_selector.$live_property.$live_format.$live_attribute.$live_choices.'>';
				if($setting_description){
					$output .= '<div class="forge-builder-form-field-description">';
					$output .= '<div class="forge-builder-form-field-description-icon">?</div>';
					$output .= '<div class="forge-builder-form-field-tooltip">'.$setting_description.'</div>';
					$output .= '</div>';
				}
				if($setting_label){
					$output .= '<div class="forge-builder-form-field-title">'.$setting_label.'</div>';
				}
				$output .= '<div class="forge-builder-form-field-body">';
				$output .= call_user_func($field_callback, $current_field, $setting_value);
				$output .= '</div>';
				$output .= '</div>';
			}
		}
		$output .= '<input type="hidden" id="forge-field-element" name="forge-field-element" value="'.$element->id.'">';
		
		$metadata = $this->data->get_metadata();
		
		$args = array('title' => $metadata[$element->type]['title']);
		$output = $this->generate_form($output, $args);
		
		return $output;
	}
	
	
	//Create a generic form with given content
	public function generate_form($content, $args){
		$form_title = isset($args['title']) ? esc_attr($args['title']) : false;
		$form_submit = isset($args['submit']) ? esc_attr($args['submit']) : __('Save Changes', 'forge');
		$form_context = isset($args['context']) ? esc_attr($args['context']) : 'element';
		$form_class = isset($args['class']) ? esc_attr($args['class']) : '';
		
		$output = '';
		$output .= '<div class="forge-builder-form-body '.$form_class.'" id="forge-builder-form-body">';
		
		//Add the title, content
		$output .= '<div class="forge-builder-form-content" id="forge-builder-form-content">';
		if($form_title){
			$output .= '<div class="forge-builder-form-title">'.$form_title.'</div>';
		}
		$output .= $content;
		$output .= '</div>';
		
		//Context
		$output .= '<input type="hidden" id="forge-field-context" name="forge-field-context" value="'.$form_context.'">';
		
		//Submit buttons
		$output .= '<div class="forge-builder-form-buttons">';
		if($form_submit){
			$output .= '<input type="submit" class="forge-builder-form-button forge-builder-form-save" value="'.$form_submit.'">';
		}
		
		//Cancel buttons
		$cancel_class = '';
		if(!$form_submit){
			$cancel_class = 'forge-builder-form-button-full';
		}
		$output .= '<div class="forge-builder-form-button forge-builder-form-cancel '.$cancel_class.'">'.__('Close', 'forge').'</div>';
		$output .= '</div>';
		$output .= '</div>';
		
		return $output;
	}
	
	
	//Return settings field for a new element through AJAX
	public function request_insert_template(){
		$element_template = isset($_POST['template']) ? esc_attr($_POST['template']) : false;
		$element_parent = isset($_POST['parent']) ? esc_attr($_POST['parent']) : false;
		$element_position = isset($_POST['position']) ? esc_attr($_POST['position']) : false;
		
		if($element_template){
			$elements = $this->data()->get_elements();
			$current_element = $elements['0'];
			
			//Add elements
			$template_contents = get_post_meta($element_template, 'forge_builder_content', true);
			unset($template_contents['0']);
			$this->data()->import_elements($template_contents, $element_parent, $element_position);

			$output = array(
			'layout' => $this->generate(), 
			'settings' => $current_element);
			
			//Return values
			if(defined('DOING_AJAX')){
				echo json_encode($output);
				die();
			}
		}
	}
	
	
	//Disposes of unneeded CSS and JS assets during render
	public function clean_assets(){
		if($this->data->ajax() == true){
			global $wp_scripts;
			global $wp_styles;
			
			//Print all scripts to dispose of unneeded assets
			ob_start();
			do_action('wp_enqueue_scripts');
			do_action('wp_enqueue_styles');
			ob_end_clean();

			//Dequeue all scripts
			if(isset($wp_scripts)){
				$wp_scripts->queue = array();
			}
			
			//Dequeue all styles
			if(isset($wp_styles)){
				$wp_styles->queue = array();
			}
		}
	}
	
	
	//Retrieve a list of all CSS and JS assets needed for an element
	public function generate_assets(){
		$assets = '';
		if($this->data->ajax() == true){
			global $wp_styles;
			global $wp_scripts;
			
			ob_start();
			if(isset($wp_styles)){
				wp_print_styles($wp_styles->queue);
			}
			if(isset($wp_scripts)){
				$wp_scripts->done[] = 'jquery';
				wp_print_scripts($wp_scripts->queue);
			}
			$assets = ob_get_clean();
		}
		return $assets;
	}
	
	
	//Retrieve the highest position number for a particular child
	public function get_highest_position($parent){
		$last = 0;
		foreach($this->data->get_elements() as $current_element){
			if($current_element->parent == $parent && $current_element->position > $last){
				$last = $current_element->position;
			}
		}
		return $last;
	}
	
	
	//Return settings field for a new element through AJAX
	public function create_element($element_type, $element_parent, $element_position, $settings = null){
		
		//Retrieve field data and default values
		$metadata = $this->data->get_metadata();
		
		if(!isset($metadata[$element_type]['fields'])){
			return false;
		}
		
		//Assign default values to element
		$element_settings = array();
		$element_content = null;
		if($settings != null){
			$element_settings = $settings;
		}else{
			$settings_list = $this->data->get_element_settings($element_type);
			foreach($settings_list as $current_field){
				if(isset($current_field['name'])){
					//If field name is content, use the content attribute separate for shortcodes
					if(isset($current_field['default'])){
						$element_settings[$current_field['name']] = $current_field['default'];
					}
				}
			}
		}
		
		$element_id = $this->create_element_id();
		
		//Create element data, including random ID
		$new_element = (object)array(
		'id' => $element_id,
		'type' => $element_type,
		'parent' => $element_parent,
		'position' => $element_position,
		'content' => $element_content,
		'settings' => $element_settings);
		
		//If creating a hierarchical element, add default children as well
		if(isset($metadata[$element_type]['hierarchical'])){
			if(isset($metadata[$element_type]['children'])){
				$count = 0;
				foreach($metadata[$element_type]['children'] as $current_child){
					$this->create_element($current_child, $element_id, $count);
					$count++;
				}
			}
		}
		
		return $new_element;
	}

	
	//Create default settings for current page
	public function set_default_settings(){
		$options = array(
		'active' => false,
		'css' => '',
		'created' => date('Y-m-d H:i:s'),
		'modified' => date('Y-m-d H:i:s'),
		'history' => array(),
		);
		$this->settings = $options;
		update_post_meta($this->data->get_post(), 'forge_builder_settings', $this->settings);
	}
	
	
	//Generate a new random ID for an element
	public function create_element_id(){
		//Add a string character for good measure
		do{			
			$new_id = 'e'.date('YmdHis').rand(1000, 9999);
		}while(isset($elements[$new_id]));
		return $new_id;
	}

	
	//Save form changes
	public function request_history(){
		$history_id = isset($_POST['history']) ? esc_attr($_POST['history']) : false;
		
		if($this->data->change_history($history_id)){
			//Regenerate entire layout
			$output = array(
			'layout' => $this->generate(),
			'settings' => $this->data->get_element('0'));
			
			//Return values
			if(defined('DOING_AJAX')){
				echo json_encode($output);
				die();
			}
			
		}else{ 
			die(); 
		}
	}
	
	
	//Update history through AJAX
	public function request_update_history(){
		if(defined('DOING_AJAX')){
			echo $this->render_history();
			die();
		}
	}
	
	
	//Update history through AJAX
	public function row_actions($data){
		$data['layout'] = array(
		'title' => __('Layout', 'forge'),
		'type' => 'dropdown',
		'list' => array(
			'layout-12' => array('class' => 'forge-builder-actions-layout', 'data' => array('layout' => '12')),
			'layout-6' => array('class' => 'forge-builder-actions-layout', 'data' => array('layout' => '6,6')),
			'layout-4' => array('class' => 'forge-builder-actions-layout', 'data' => array('layout' => '4,4,4')),
			'layout-3' => array('class' => 'forge-builder-actions-layout', 'data' => array('layout' => '3,3,3,3')),
			'layout-2' => array('class' => 'forge-builder-actions-layout', 'data' => array('layout' => '2,2,2,2,2,2')),
			'layout-8-4' => array('class' => 'forge-builder-actions-layout', 'data' => array('layout' => '8,4')),
			'layout-4-8' => array('class' => 'forge-builder-actions-layout', 'data' => array('layout' => '4,8')),
			'layout-9-3' => array('class' => 'forge-builder-actions-layout', 'data' => array('layout' => '9,3')),
			'layout-3-9' => array('class' => 'forge-builder-actions-layout', 'data' => array('layout' => '3,9')),
			'layout-6-3-3' => array('class' => 'forge-builder-actions-layout', 'data' => array('layout' => '6,3,3')),
			'layout-3-3-6' => array('class' => 'forge-builder-actions-layout', 'data' => array('layout' => '3,3,6')),
			'layout-3-6-3' => array('class' => 'forge-builder-actions-layout', 'data' => array('layout' => '3,6,3')),
			// 'spacing-fit' => array('type' => 'action', 'class' => 'forge-builder-actions-spacing', 'data' => array('spacing' => 'fit')),
			// 'spacing-narrow' => array('type' => 'action', 'class' => 'forge-builder-actions-spacing', 'data' => array('spacing' => 'narrow')),
			// 'spacing-normal' => array('class' => 'forge-builder-actions-spacing', 'data' => array('spacing' => 'normal')),
			// 'spacing-wide' => array('class' => 'forge-builder-actions-spacing', 'data' => array('spacing' => 'wide')),
		));
		return $data;
	}
	
	
	//Retrieve builder object
	function data(){
		return $this->data;
	}
	
	
	//Refresh element when live editing
	public function request_refresh(){
		$element_id = isset($_POST['element']) ? esc_attr($_POST['element']) : 0;
		$element_settings = false;
		if(isset($_POST['settings'])){
			parse_str($_POST['settings'], $element_settings);
		}
		
		$elements = $this->data->get_elements();
		
		if(isset($elements[$element_id]) && $element_id !== '0'){
			$element_type = $elements[$element_id]->type;
			
			//Sanitize settings for this refresh
			$new_settings = array();
			$metadata = $this->data->get_metadata();
			if(isset($metadata[$element_type]['fields'])){
				$element_fields = $metadata[$element_type]['fields'];
				
				foreach($element_fields as $field_key => $field_value){
					$field_name = $field_value['name'];
					if(isset($element_settings['forge_field_'.$field_name])){
						$new_settings[$field_name] = $element_settings['forge_field_'.$field_name];
					}
				}
			}
			
			$current_element = $elements[$element_id];
			$current_element->settings = $new_settings;
	
			$element_layout = $this->generate_element($current_element);
			
			// print_r($element_layout); die();
			$output = array('layout' => $element_layout);

			//Return values
			echo json_encode($output);
			die();
		
		}
		return false;
	}
	
	
	//Change template if using a blank 
	public function template(){
		if(!is_singular() || is_comment_feed() || is_feed()){
			return false;
		}
		
		//Add filter so extensions can override this
		$settings = $this->data->get_settings();
		$template = isset($settings['template']) ? esc_attr($settings['template']) : 'none';
		
		if(is_singular('forge_template')){
			$template = 'template';
		}
		
		if($template != ''){
			$path = @realpath(FORGE_DIR.'templates/template-'.$template.'.php');
			if(is_file($path)){
				include $path;
				exit();
			}
		}
	}
}