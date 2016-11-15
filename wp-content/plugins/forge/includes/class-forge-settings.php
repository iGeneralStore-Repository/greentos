<?php


//Holds all the persistent data for the builder and interfaces with the database.
class Forge_Settings {
	
	//Instance of Forge Settings
	private static $instance;
	
	//The settings array
	private $options = false;
	
	//The settings schema array
	private $metadata = false;
	
	
	public static function instance(){
		if(!isset(self::$instance ) && !(self::$instance instanceof Forge_Settings)){
			self::$instance = new Forge_Settings;
			self::$instance->hooks();
		}
		return self::$instance;
	}
	

	//Initialize hooks
	public function hooks(){
		if(defined('DOING_AJAX')){
			add_action('init', array($this, 'init'));			
		}else{
			add_action('admin_init', array($this, 'init'));			
			add_action('wp', array($this, 'init'), 20);	
		}
	}
	
	
	//Initialize settings
	public function init($id = false){
		$this->options = apply_filters('forge_get_settings', get_option('forge_settings'));
		$this->metadata = forge_metadata_customize_controls();
	}
	
	
	//Setup basic data
	public function get($option_name = false){
		if($option_name !== false){
			$option_value = false;
			
			//If options exists and is not empty, get value
			if(isset($this->options[$option_name]) && (is_bool($this->options[$option_name]) === true || $this->options[$option_name] !== '')){
				$option_value = $this->options[$option_name];
			}	
			
			//If option is empty, check whether it needs a default value
			if($option_value === '' || !isset($this->options[$option_name])){
				//If option cannot be empty, use default value
				if(!isset($this->metadata[$option_name]['empty']) || !isset($this->options[$option_name])){
					if(isset($this->metadata[$option_name]['default'])){
						$option_value = $this->metadata[$option_name]['default'];
					}
				}
			}
			
			return $option_value;
		}else{
			return $this->options;
		}
		return false;
	}
}


//Start up Forge Settings
function forge_settings(){
	return Forge_Settings::instance();
}
forge_settings();