<?php


//Holds all the persistent data for the builder and interfaces with the database.
class Forge_Presets {
	
	/**
	 * @var         Forge $instance The one true Forge
	 * @since       1.0.0
	 */
	private static $instance;
	
	//The original content of the current post
	private $presets = array();
	
	//The stored settings for presets
	private $settings = array();
	
	
	//Load instance
	public static function instance() {
		if(!isset(self::$instance) && !(self::$instance instanceof Forge_Presets)){
			self::$instance = new Forge_Presets;
			self::$instance->hooks();
		}
		return self::$instance;
	}
	
	
	//Constructor
	public function __construct(){}
	
	
	//Initialize hooks
	public function hooks(){
		add_action('init', array($this, 'init'), 9);
		add_action('admin_notices', array($this, 'notice'), 9);
	}
	
	
	//Setup basic data
	public function init(){
		$this->settings = get_option('forge_presets', false);
	}
	
	
	//Add a new preset to the list
	public function add_preset($name, $args = null){
		$preset_title = isset($args['title']) ? esc_attr($args['title']) : '';
		$preset_description = isset($args['description']) ? esc_attr($args['description']) : '';
		$preset_metadata = isset($args['metadata']) ? $args['metadata'] : '';
		$preset_elements = isset($args['elements']) ? esc_attr($args['elements']) : '';
		
		if($name != '' && $preset_title != '' && $preset_elements != ''){
			$theme_folder = get_template();
			$data = array(
			'title' => $preset_title,
			'description' => $preset_description,
			'metadata' => $preset_metadata,
			'elements' => $preset_elements);
			$this->presets[$theme_folder.'-'.$name] = $data;
		}
	}
	
	
	//Get the list of presets
	public function get_presets(){
		return $this->presets;
	}
	
	
	//Get single preset
	public function get_preset($id){
		if(isset($this->presets[$id])){
			return $this->presets[$id];
		}
		return false;
	}
	
	
	//Get the list of preset settings
	public function get_settings(){
		return $this->settings;
	}
	
	
	//Add a new preset to the list
	public function has_presets(){
		if(!empty($this->presets)){
			return true;
		}
		return false;
	}
	
	
	//Add a new preset to the list
	public function load_preset($preset_id, $page_id){
		if(!isset($this->presets[$preset_id])){
			return false;
		}
		
		$preset = $this->presets[$preset_id];
		$import_content = $preset['elements'];
		$error = false;
		
		//Decode the string and validate
		$import_content = base64_decode($import_content, true);
		if(!$import_content){
			$error = true;
		}
		
		//Ensure it is properly formed
		$import_content = @unserialize($import_content);
		if(!is_array($import_content) || $import_content === false){
			$error = true;
		}
		
		//If no errors, load preset
		$builder = new Forge_Builder($page_id);
		if(!$error && $builder->data()->import_elements($import_content, 0, 0, true)){
			$builder->data()->publish_page();
			
			//Assign page to preset
			$this->settings[$preset_id] = $page_id;
			update_option('forge_presets', $this->settings);
			
			return true;
		}
		
		return false;
	}
	
	
	//Create a notice when the theme has presets
	public function notice(){
		$presets_page = false;
		if(isset($_GET['page']) && $_GET['page'] == 'forge_presets'){
			$presets_page = true;
		}
		
		if(!$presets_page && $this->has_presets()){
			$theme = wp_get_theme();
			$args = array(
			'title' => sprintf(__('%s comes with preset layouts.', 'forge'), $theme),
			'content' => __('Presets are complete website layouts that come packaged with your current WordPress theme. You can load them to set up the content of your website in just a few seconds. Click on View Presets to select which ones you want to install into your website.', 'forge'),
			'link_url' => admin_url('admin.php?page=forge_presets'),
			'link_text' => __('View Presets', 'forge'));
			forge_notices()->display(get_template().'_presets', $args);
		}
	}
}


//Start up Forge.
function forge_presets() {
	return Forge_Presets::instance();
}
forge_presets();