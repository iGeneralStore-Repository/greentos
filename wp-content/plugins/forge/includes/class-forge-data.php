<?php


//Holds all the persistent data for the builder and interfaces with the database.
class Forge_Data {
	
	//The ID of the current post or term
	private $post = false;
	
	//Whether the current object is a post or taxonomy term
	private $type = 'post';
	
	//The original content of the current post
	private $post_content = false;
	
	//Settings for the current builder page
	private $settings;
	
	//The current element structure
	private $elements;
	
	//An original copy of the element structure
	private $elements_original;
	
	//The schema of elements
	private $metadata;
	
	//Common settings for every builder element
	private $element_settings;
	
	//Determines if the builder interface is being used
	private $builder_active = false;
	
	//Determines if builder layout is being used for the current post, instead of content
	private $using_builder = false;
	
	//Determines if doing an AJAX request
	private $ajax = false;
	
	//Determines if working with a template
	public $template_builder = false;
	
	
	//Constructor
	public function __construct($id = false, $type = 'post', $args = null){
		$this->post = $id;
		$this->type = $type;
		$this->hooks($id);
		if(defined('DOING_AJAX') && DOING_AJAX){
			$this->ajax = true;
		}elseif($id){
			$this->init();
		}
	}
	
	
	//Initialize hooks
	public function hooks($id = false){
		if(!$id){
			add_action('wp', array($this, 'init'), 8);
			add_action('init', array($this, 'init_ajax'), 8);
		}
	}
	
	
	//Setup basic data
	public function init_ajax(){
		if(defined('DOING_AJAX') && DOING_AJAX){
			
			//Setup post id
			if(!$this->post){
				if(isset($_POST['postid']) && is_numeric($_POST['postid'])){
					$this->post = intval($_POST['postid']);
				}
			}
			
			//Check if doing AJAX
			if(current_user_can('edit_posts') && $this->post && is_user_logged_in()){	
				//Under AJAX, initialize all variables
				$this->using_builder = true;
				$this->builder_active = true;
				$this->metadata = forge_metadata_elements();
				$this->element_settings = forge_metadata_element_settings();
				$this->elements = get_post_meta($this->post, 'forge_builder_progress', true);
				
				$this->elements_original = array();
				
				//Create a deep copy of the elements
				if($this->elements){
					foreach($this->elements as $element_key => $element_value){
						$this->elements_original[$element_key] = clone $element_value;
					}
				}
				
				//Get builder settings for current page
				$this->settings = get_post_meta($this->post, 'forge_builder_settings', true);
				
				//Check for wrapper element; if it does not exist, add it
				if(!isset($this->elements['0'])){
					$this->elements['0'] = (object) array('id' => '0', 'type' => 'wrapper', 'parent' => 'page', 'position' => '0', 'settings' => array());
				}			
				//Do not use die() to avoid interfering with other AJAX calls!
				
				//Disable caching when builder is active, especially Minify
				if(!defined('DONOTCACHEDB')){ define('DONOTCACHEDB', true);	}
				if(!defined('DONOTCACHEPAGE')){ define('DONOTCACHEPAGE', true); }
				if(!defined('DONOTMINIFY')){ define('DONOTMINIFY', true); }
				add_filter( 'do_rocket_generate_caching_files', '__return_false');
			}		
		}		
	}
	
	
	//Setup basic data
	public function init(){
		
		//Get current post as specified by WordPress
		$current_post = false;
		$current_type = false;
		if(is_singular() || is_single()){
			global $post;
			if(isset($post->ID)){
				$current_post = $post->ID;
				$current_type = 'post';
			}
		}elseif(is_tax()){
			//TODO: Add support for taxonomy terms
		}
		
		//If not set, use current post. Otherwise, leave as is
		if(!$this->post){
			$this->post = $current_post;
			$this->type = $current_type;
		}
		
		if($this->post){
			//Get builder settings for current page
			$this->settings = get_post_meta($this->post, 'forge_builder_settings', true);
			
			//If there are no existing settings, create new ones when using the builder
			if(!$this->settings && isset($_GET['forge_builder'])){
				$this->set_default_settings();
			}
			
			//Check if builder is being used in this page
			$this->using_builder = isset($this->settings['active']) && $this->settings['active'] == true ? true : false;
			
			//Check if builder interface is active
			if(current_user_can('edit_posts') && is_user_logged_in() && isset($_GET['forge_builder']) && $this->post == $current_post){
				$this->builder_active = true;						
			}	
			
			if($this->builder_active || $this->using_builder){
			
				if(is_singular('forge_template') && $this->using_builder()){
					$this->template_builder = true;
				}
		
				//Retrieve element schema
				//$this->post_content = $content;
				$this->metadata = forge_metadata_elements();
				$this->element_settings = forge_metadata_element_settings();
				uasort($this->metadata, 'forge_sort_collection');
				
				//Retrieve original elements
				$this->elements = get_post_meta($this->post, 'forge_builder_content', true);	
				
				//Check for wrapper element; if it does not exist, add it
				if(!isset($this->elements['0'])){
					$this->elements['0'] = (object) array('id' => '0', 'type' => 'wrapper', 'parent' => 'page', 'position' => '0', 'settings' => array());
				}
				
				//Initialize builder interface if composing
				if($this->builder_active){
					
					//Check if there is a working copy; use draft until changes are published
					$working_draft = get_post_meta($this->post, 'forge_builder_progress', true);
					if(!empty($working_draft)){
						$this->elements = $working_draft;
					}else{
						update_post_meta($this->post, 'forge_builder_progress', $this->elements);
					}
					
					//Check if there is an element array
					if($this->elements){		
						uasort($this->elements, 'forge_sort_element_position');
					}else{
						$this->elements = array();
					}
					
					//Disable caching when builder is active, especially Minify
					if(!defined('DONOTCACHEDB')){ define('DONOTCACHEDB', true);	}
					if(!defined('DONOTCACHEPAGE')){ define('DONOTCACHEPAGE', true); }
					if(!defined('DONOTMINIFY')){ define('DONOTMINIFY', true); }
					add_filter( 'do_rocket_generate_caching_files', '__return_false');
				}
			}
		}
	}
	
	
	//Save page changes and exit
	public function publish_page(){
		if(current_user_can('edit_post', $this->post)){
		
			//TODO:Release heartbeat lockdown
			
			$this->clean_history();
			
			//Update content data with draft, then delete draft
			update_post_meta($this->post, 'forge_builder_content', $this->elements);
			delete_post_meta($this->post, 'forge_builder_progress');
			
			//Update settings
			$this->settings['active'] = true;
			$this->settings['published'] = current_time('timestamp');
			update_post_meta($this->post, 'forge_builder_settings', $this->settings);
			
			return true;
		}
		return false;
	}
	
	
	//Discard page changes and exit
	public function discard_page(){
		if(current_user_can('edit_post', $this->post)){
			
			$this->clean_history();
			
			//Update content data with draft, then delete draft
			delete_post_meta($this->post, 'forge_builder_progress');
			update_post_meta($this->post, 'forge_builder_settings', $this->settings);
			
			return true;
		}
		return false;
	}
	
	
	//Retrieve the highest position number for a particular child
	public function get_highest_position($parent){
		$last = 0;
		foreach($this->elements as $current_element){
			if($current_element->parent == $parent && $current_element->position > $last){
				$last = $current_element->position;
			}
		}
		return $last;
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
		update_post_meta($this->post, 'forge_builder_settings', $this->settings);
	}
	
	
	//Generate a new random ID for an element
	public function create_element_id(){
		//Add a string character for good measure
		do{			
			$new_id = 'e'.date('YmdHis').rand(1000, 9999);
		}while(isset($this->elements[$new_id]));
		return $new_id;
	}


	//Save current element structure into post metadata
	public function save_progress($action = 'none', $type = ''){
		uasort($this->elements, 'forge_sort_element_position');
		$now = date('Y-m-d H:i:s');
		
		if($action !== false){
		
			//Sanity check 
			if(!isset($this->settings['history']) || !is_array($this->settings['history'])){
				$this->settings['history'] = array();
			}
			
			//Check if we're on a previous history. If so, erase future records
			$current_history = isset($this->settings['current']) ? $this->settings['current'] : 'now';
			$change_history = $this->settings['history'];
			if($current_history != 'now'){
				foreach($change_history as $history_key => $history_value){
					if($history_key >= $current_history){
						unset($this->settings['history'][$history_key]);
						delete_post_meta($this->post, 'forge_builder_progress_'.$history_key);
					}
				}
			}
			
			//Add to history
			$new_history_id = current_time('timestamp');
			$new_history = array(
			'action' => $action,
			'type' => $type);
			$this->settings['history'][$new_history_id] = $new_history;
			
			//If history exceeds allowed buffer size, remove oldest entry
			if(sizeof($this->settings['history']) > 10){
				reset($this->settings['history']);
				$first_history = key($this->settings['history']);
				unset($this->settings['history'][key($this->settings['history'])]);
				delete_post_meta($this->post, 'forge_builder_progress_'.$first_history);
			}
			
			$this->settings['current'] = 'now';
			update_post_meta($this->post, 'forge_builder_progress_'.$new_history_id, $this->elements_original);
		}
		
		//Change modified date
		$this->settings['modified'] = $now;
		
		//Save element changes, settings, and history
		update_post_meta($this->post, 'forge_builder_settings', $this->settings);
		update_post_meta($this->post, 'forge_builder_progress', $this->elements);
	}
	
	
	//Save form changes
	public function request_history(){
		$history_id = isset($_POST['history']) ? esc_attr($_POST['history']) : false;
		
		if($history_id){
			if(isset($this->settings['history'][$history_id])){
				
				//Retrieve data
				$this->settings['current'] = $history_id;
				$this->elements = get_post_meta($this->post, 'forge_builder_progress_'.$history_id, true);
				
				//Save progress without generating a history action
				$this->save_progress(false);
				
				//Regenerate entire layout
				$current_element = $this->elements['0'];
				$element_layout = $this->forge_generate();
				
				$output = array(
				'layout' => $element_layout,
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
	}

		
	//Determines whether the builder is in use.
	public function get_post(){
		return $this->post;
	}
	
	
	//Determines whether the builder is in use.
	public function using_builder(){
		return $this->using_builder;
	}
	
	
	//Determines whether the builder is in use.
	public function get_elements(){
		return $this->elements;
	}
	
	
	//Retrieve single element, or an array of element IDs
	public function get_element($element_id){
		if(is_array($element_id)){
			$elements = array();
			foreach($element_id as $current_id){				
				if(isset($this->elements[$current_id])){
					$elements[$current_id] = $this->elements[$current_id];
				}
			}
			return $elements;
		}else{
			if(isset($this->elements[$element_id])){
				return $this->elements[$element_id];
			}
		}
		return false;
	}


	//Insert a new element
	public function insert_element($element_type, $element_parent, $element_position){
		$new_element = $this->create_element($element_type, $element_parent, $element_position);
		
		//Displace all other elements within same parent
		foreach($this->elements as $current_key => $current_element){
			if($current_element->parent == $element_parent && $current_element->position >= $element_position && $current_element->id != $new_element->id){
				$this->elements[$current_key]->position++;
			}
		}
		
		$this->save_progress('create', $element_type);
			
		return $new_element;
	}
	
	
	//Create element, including children
	public function create_element($element_type, $element_parent, $element_position, $settings = null){
		if(!isset($this->metadata[$element_type]['fields'])){
			return false;
		}
		
		//Assign default values to element
		$element_settings = array();
		$element_content = null;
		if($settings != null){
			$element_settings = $settings;
		}else{
			$settings_list = $this->get_element_settings($element_type);
			foreach($settings_list as $current_field){
				if(isset($current_field['name'])){
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
		if(isset($this->metadata[$element_type]['hierarchical'])){
			if(isset($this->metadata[$element_type]['children'])){
				$count = 0;
				foreach($this->metadata[$element_type]['children'] as $current_child){
					$this->create_element($current_child, $element_id, $count);
					$count++;
				}
			}
		}
		
		//Save new element to draft	
		$this->elements[$element_id] = $new_element;
		
		return $new_element;
	}
	
	
	//Save element changes
	public function update_element($element_id, $element_settings){
		if(isset($this->elements[$element_id])){
		
			$current_element = $this->elements[$element_id];
			$current_type = $current_element->type;
			$new_settings = $current_element->settings;
			
			//TODO:Update and sanitize settings
			$settings_list = $this->get_element_settings($current_type);
			if(!$settings_list){
				die();
			}
			
			foreach($settings_list as $setting_value){
				$setting_name = $setting_value['name'];
				if(isset($element_settings['forge_field_'.$setting_name])){
					$new_settings[$setting_name] = $element_settings['forge_field_'.$setting_name];
				}
			}
			$current_element->settings = $new_settings;
			
			
			//Save new element to draft	
			$this->elements[$element_id] = $current_element;
			
			$this->save_progress('save', $current_type);
			
			return $current_element;
		}
		return false;
	}
	
	
	//Move existing element
	public function move_element($element_id, $new_parent, $new_ordering, $old_ordering){
		if(isset($this->elements[$element_id])){
			$current_element = $this->elements[$element_id];
			$current_element->parent = $new_parent;
			$this->elements[$element_id] = $current_element;
			
			//Reorder all elements within same parent according to new element
			if($old_ordering){
				$id_list = explode(',', $old_ordering);
				$count = 0;
				foreach($id_list as $link_id){
					if(trim($link_id) != ''){
						$this->elements[$link_id]->position = $count;
						$count++;
					}
				}
			}
			
			//Reorder all elements within same parent according to new element
			if($new_ordering){
				$id_list = explode(',', $new_ordering);
				$count = 0;
				foreach($id_list as $link_id){
					if(trim($link_id) != ''){
						$this->elements[$link_id]->position = $count;
						$count++;
					}
				}
			}
			
			$this->save_progress('move', $current_element->type);
			
			return $current_element;
		}
		return false;
	}
	
	
	//Copy element through AJAX
	public function copy_element($element_id){
		//Copy given element
		if(isset($this->elements[$element_id])){
			
			//Create a copy of the current element
			$current_element = $this->elements[$element_id];
			
			$new_element = (object) array(
			'id' => $this->create_element_id(),
			'parent' => $current_element->parent,
			'position' => $current_element->position + 1,
			'type' => $current_element->type,
			'settings' => $current_element->settings);
			
			$this->elements[$new_element->id] = $new_element;
			
			//Create a copy of every children of the main element
			$copied_elements = $this->copy_children($element_id, $new_element->id);
			foreach($copied_elements as $copy_key => $copy_value){
				$this->elements[$copy_key] = $copy_value;
			}
			
			//Push all elements within same parent after the new element
			foreach($this->elements as $element_value){
				if($element_value->id != $new_element->id && $element_value->parent == $current_element->parent && $element_value->position >= $new_element->position){
					$element_value->position++;
				}
			}
			
			$this->save_progress('copy', $current_element->type);
			
			return $new_element;
		}else{ 
			return false;
		}
	}
	
	
	//Traverses the builder metadata and recovers all descendants of an element, changing the IDs to original ones
	public function copy_children($parent, $new_parent){
		$result = array();
		foreach($this->elements as $data_key => $data_value){
			if($data_value->parent == $parent){
				$new_id = $this->create_element_id();
				
				//Save element
				$new_element = (object) array(
				'id' => $new_id,
				'parent' => $new_parent,
				'position' => $data_value->position,
				'type' => $data_value->type,
				'settings' => $data_value->settings);
				$result[$new_id] = $new_element;
				
				//Look further down
				$children = $this->copy_children($data_key, $new_id);
				foreach($children as $child_key => $child_value){
					$result[$child_key] = $child_value;
				}
			}
		}
		return $result;
	}
	
	
	//Delete element through AJAX
	public function delete_element($element_id){
		$element_parent = $this->elements[$element_id]->parent;
		$element_position = $this->elements[$element_id]->position;
		$element_type = $this->elements[$element_id]->type;
		
		//Delete given element and children
		$this->delete_children($element_id);
		
		//Reoder all other elements within same parent
		foreach($this->elements as $current_key => $current_element){
			if($current_element->parent == $element_parent && $current_element->position > $element_position){
				$this->elements[$current_key]->position--;
				if($this->elements[$current_key]->position < 0){
					$this->elements[$current_key]->position = 0;
				}
			}
		}
		
		$this->save_progress('delete', $element_type);
		
		return true;
	}
	
	
	//Traverses the builder metadata and deletes all descendants of an element
	public function delete_children($id, $new_parent = null){
		foreach($this->elements as $data_key => $data_value){
			if($data_value->parent == $id){
				$this->delete_children($data_value->id);
			}
		}
		unset($this->elements[$id]);
	}
	
	
	//Change number of columns
	public function change_row_layout($element_id, $element_layout){
		if(isset($this->elements[$element_id])){
			
			//Find number of columns
			$layout_list = explode(',', $element_layout);
			
			//Change existing columns
			$count = 0;
			$last_column = '';
			foreach($this->elements as $current_key => $current_value){
				if($current_value->type == 'column' && $current_value->parent == $element_id){
					if($count < sizeof($layout_list)){
						//We keep this column
						$last_column = $current_key;
						$column_settings = $current_value->settings;
						$column_settings['size'] = $layout_list[$count];
						$current_value->settings = $column_settings;	
					}else{
						//Remove this column and relocate children
						unset($this->elements[$current_key]);
						foreach($this->elements as $child_key => $child_value){
							if($child_value->parent == $current_key){
								$this->elements[$child_key]->parent = $last_column;
							}
						}
					}
					$count++;
				}
			}
			
			//If count is still less than layout size, add columns
			while($count < sizeof($layout_list)){
				$new_settings = array('size' => $layout_list[$count]);
				$new_element = $this->create_element('column', $element_id, $count, $new_settings);
				$count++;
			}
			
			//Save to draft	
			$this->save_progress('layout', 'row');
			
			
			return true;
		}else{ 
			return false;
		}
	}
	
	
	//Import a number of elements into the layout
	public function import_elements($import_content, $parent = 0, $position = 0, $replace = false){
		if($replace){
			return $this->import_elements_replace($import_content);
		}else{
			return $this->import_elements_append($import_content, $parent, $position);
		}
	}
	
	
	//Import of elements and replace the contents
	public function import_elements_replace($import_content){
		
		if(is_array($import_content)){
			//If replace, remove current content
			$this->elements = array();
			
			foreach($import_content as $import_key => $import_element){
				$this->elements[$import_key] = clone $import_element;
			}
			return true;
		}
		return false;
	}
	
	
	//Import a number of elements into the layout
	public function import_elements_append($import_content, $parent = 0, $position = 0){
		
		if(is_array($import_content)){
			
			//Remove top-level element when not replacing
			unset($import_content['0']);				
		
			//Create a dictionary of all IDs, and their new values
			$id_list = array();
			$top_size = 0;
			foreach($import_content as $import_key => $import_element){
				if(isset($import_element->parent) && $import_element->parent == '0'){
					$top_size++;
				}
				if(isset($import_element->parent) && $import_element->parent != 'page'){
					$id_list[$import_key] = $this->create_element_id();
				}
			}
			
			//Displace all other elements within same parent to make room
			foreach($this->elements as $current_key => $current_element){
				if($current_element->parent == $parent && $current_element->position >= $position){
					$this->elements[$current_key]->position += $top_size;
				}
			}
			
			//Import each element
			foreach($import_content as $import_key => $import_element){
				if(isset($import_element->id) && isset($import_element->type) && isset($import_element->parent) && isset($import_element->position)){
					//Change ID and parent
					$current_type = $import_element->type;
					if($current_type != 'wrapper'){
						$current_parent = $import_element->parent;
						$new_id = 'temp';
						foreach($id_list as $id_key => $id_value){
							if($import_key == $id_key){
								$new_id = $id_value;
							}
							if($current_parent == $id_key){
								$current_parent = $id_value;
							}
						}
						
						//Add position to top level elements
						$current_position = $import_element->position;
						if($current_parent == '0'){
							$current_parent = $parent;
							$current_position += $position;
						}
						
						//TODO:Validate settings
						if(isset($import_element->settings)){
							$new_settings = $import_element->settings;
						}else{
							$new_settings = array();
						}
						
						//Save new element to draft	
						$this->elements[$new_id] = (object) array(
						'id' => $new_id,
						'parent' => $current_parent,
						'position' => $current_position,
						'type' => $current_type,
						'settings' => $new_settings);
					}
				}
			}
			
			$this->save_progress('import');
			
			return true;
		}
		return false;
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
			
			
			$this->save_progress();
			
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
	
	
	//Determines whether the builder is in use.
	public function get_settings(){
		return $this->settings;
	}
	
		
	//Retrieve settings for a particular element type
	public function get_element_settings($element_type){
		$settings = false;
		if(isset($this->metadata[$element_type]['fields'])){
			if(isset($this->metadata[$element_type]['parent']) || $element_type == 'wrapper'){
				$settings = $this->metadata[$element_type]['fields'];
			}else{
				$settings = array_merge($this->metadata[$element_type]['fields'], $this->element_settings);
			}
		}
		return $settings;
	}
	
	
	//Determines whether the builder is in use.
	public function get_metadata(){
		return $this->metadata;
	}
	
	
	//Determines whether the builder is in use.
	public function get_elements_original(){
		return $this->elements_original;
	}
	
	
	//Determines whether the builder is in use.
	public function builder_active(){
		return $this->builder_active;
	}
	
	//Determines whether the builder is in use.
	public function ajax(){
		return $this->ajax;
	}
	
	
	//Save form changes
	public function change_history($history_id){
		if(isset($this->settings['history'][$history_id])){
			
			//Retrieve data and change elements
			$this->settings['current'] = $history_id;
			$this->elements = get_post_meta($this->post, 'forge_builder_progress_'.$history_id, true);
			
			//Save progress without generating a history action
			$this->save_progress(false);
			
			return true;
			
		}else{ 
			return false;
		}
	}
	
	
	//Clean history records
	public function clean_history(){
		//Remove history
		$original_history = $this->settings['history'];
		foreach($original_history as $history_key => $history_value){
			unset($this->settings['history'][$history_key]);
			delete_post_meta($this->post, 'forge_builder_progress_'.$history_key);
		}
	}
	
	
	//retrieve post content
	public function get_post_content(){
		if($this->post_content === false){
			$current_post = get_post($this->post);
			if($current_post){
				$this->post_content = $current_post->post_content;
			}
		}
		return $this->post_content;
	}
	
	
	//Set using builder
	public function set_builder_active($value){
		$this->builder_active = $value;
	}
	
}


//Start up Forge.
// function forge_data() {
	// return Forge_Data::instance();
// }
// forge_data();