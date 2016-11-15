<?php

//Baseline class for a connection to an external service
class Forge_Connection_Generic {
	
	//The connection ID. If set, it means it's a saved connection
	protected $id = false;
	
	//The connection name
	protected $name = 'New Connection';
	
	protected $url = '';
	
	//Type of conenction
	protected $type = 'custom';
	
	//The field list that this connection requires for authenticating
	protected $fields = array();
	
	//The credential fields required by the user
	protected $credentials = array();
	
	//The settings containing the list or item where the contacts will be subscribed
	protected $settings = array();

	//The status for the current connection
	protected $status = false;

	//Error messages
	protected $message = '';

	
	//Create class
	public function __construct(){}
	
	
	//Set name
	public function set_name($value){
		$this->name = $value;
	}
	
	
	//Set name
	public function get_name(){
		return $this->name;
	}

	//Get current credentials
	public function get_credentials() {
		return $this->credentials;
	}
	

	//Set credentials
	public function set_credential($key, $value){
		$this->credentials[$key] = $value;
	}

	
	//Get current credentials
	public function get_fields() {
		return $this->fields;
	}
	

	//Set credentials
	public function set_field($key, $value){
		$this->fields[$key] = $value;
	}
	
	
	//Get current settings
	public function get_settings() {
		return $this->fields;
	}
	

	//Set settings
	public function set_setting($key, $value){
		$this->settings[$key] = $value;
	}

	
	//Return true if connected
	public function get_status() {
		return !empty($this->status);
	}


	//Return true if connected
	public function load($data){
		$this->settings = isset($data['fields']) ? $data['fields'] : array();
		$this->status = isset($data['status']) ? esc_attr($data['status']) : false;
		$this->name = isset($data['name']) ? esc_attr($data['name']) : '';
	}


	//Display error
	public function error( $message ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $message;
		}
		return $this->_message( 'error', $message );
	}

	
	//Remove connection
	public function disconnect(){
		$this->set_credentials(array());
	}


	//Split full name into first name and last name
	protected function split_name($full_name){
		if(empty($full_name)){
			return array('', '');
		}
		$parts = explode(' ', $full_name);

		//single words
		if(count($parts) == 1){
			return array($parts[0], '');
		}
		
		$last_name = array_pop($parts);
		$first_name = implode(' ', $parts);

		return array($first_name, $last_name);
	}

	
	//Connect with service
	public function create(){
		return false;
	}
	
	
	//Save existing data into connections
	public function save(){
		$id = 'c'.date('YmdHis');
		$data = array(
		'name' => $this->name,
		'status' => 'ok',
		'type' => $this->type,
		'fields' => $this->settings);
		forge_update_option($id, $data, 'forge_connections');		
		
		$output = array(
		'status' => true,
		'id' => $id);
		return $output;
	}
	
	
	//Connect with service
	public function generate_fields(){
		$output = '';
		
		$output .= '<input type="hidden" name="connection_type" value="'.$this->type.'"/>';
		
		//Add current settings as hidden fields
		foreach($this->settings as $field_key => $field_value){
			$output .= '<p><input type="hidden" name="connection_data['.$field_key.']" value="'.$field_value.'"/></p>';
		}
		
		//Add remaining fields
		if(!empty($this->fields)){
			foreach($this->fields as $field_key => $field_value){
				if(is_array($field_value)){
					$field_title = $field_value['title'];
					$field_type = isset($field_value['type']) ? $field_value['type'] : 'text';
					$field_choices = isset($field_value['choices']) ? $field_value['choices'] : array();
				}else{
					$field_title = $field_value;
				}
				$field_name = $field_key;
				

				$output .= '<p>';
				$output .= $field_title;
				if($field_type == 'list' && !empty($field_choices)){
					$output .= '<select name="connection_data['.$field_name.']">';
					foreach($field_choices as $key => $value){
						$output .= '<option value="'.$key.'">'.$value.'</option>';
					}
					$output .= '</select>';
				}else{
					$output .= '<input type="text" name="'.$field_name.'">';
				}
					
				$output .= '<p>';
			}
		}else{
			$output .= '<p>'.__('The connection has been successfully tested. There are no additional fields to configure.', 'forge').'</p>';
		}
		
		return $output;
	}

	
	//Return all lists-- override this in each connection
	public function get_lists(){
		return false;
	}
	
	
	//Add contact to list
	public function subscribe($email, $fname, $lname, $args){
		
	}
}
