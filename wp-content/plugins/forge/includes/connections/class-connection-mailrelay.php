<?php

class Forge_Connection_Mailrelay extends Forge_Connection_Generic {
	
	
	//Create class
	public function __construct(){
		parent::__construct();
		$this->type = 'mailrelay';
		$this->url = 'http://<dc>.ip-zone.com/ccm/admin/api/version/2/&type=json';
		
		//Required credentials
		$this->credentials = array(
		'username' => __('Username', 'forge'),
		'key' => __('API Key', 'forge'),
		);
		
		//Settings fields
		$this->fields = array(
		'list' => array('title' => __('Select A Group', 'forge'), 'type' => 'list', 'choices' => array()),
		);
	}	
	
	
	//Make a single request
	private function request($function, $args = array()){
		if(!function_exists('curl_init') || !function_exists('curl_setopt')){
			return false;
		}

		$subdomain = $this->settings['username'];
		if($subdomain == ''){
			return false;
		}
        $this->url = str_replace('<dc>', $subdomain, $this->url);
		$url = $this->url;
		
		if(is_array($args)){
			$args['function'] = $function;
			$args['apiKey'] = $this->settings['key'];
		}
		
		if($function == 'addSubscriber'){
			$args = http_build_query($args);
		}

		$response = array('headers' => null, 'body' => null);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args); 
		
		$response = curl_exec($ch);
		if($response === false){
			return false;
		}
		
		curl_close($ch);
		
		return json_decode($response);
	}
	
	
	//Initial installation of the connection. Retrieves data for a 2-step install
	public function create() {
		if(isset($this->settings['key'])){
			$request = $this->request('getGroups');
			if(is_object($request)){
				$lists = array();
				foreach($request->data as $value){
					$lists[$value->id] = $value->name;
				}
				$this->fields['list']['choices'] = $lists;
				
				$fields = $this->generate_fields();
				
				$output = array(
				'status' => true,
				'fields' => $fields);
				return $output;
			}
		}
		return false;
	}
	
	
	//Get all Subscriber Lists from this API service
	public function get_lists() {
		$result = false;
		if(isset($this->settings['key'])){
			$request = $this->request('getGroups');
			if(is_array($request)){
				$lists = array();
				foreach($request as $key => $value){
					$lists[$value->id] = $value->name;
				}
			}
		}
		return $lists;
	}

	
	//Add a subscriber
	public function subscribe($email, $fname = '', $lname = '', $arguments = null){
		list($first_name, $last_name) = $this->split_name($arguments['name']);

		$args = array(
		'email' => $email,
		'name' => $fname.' '.$lname,
		'groups' => array($this->settings['list']),
		);

		$request = $this->request('addSubscriber', $args);
		
		if(isset($request->status) && $request->status == 1){
			return true;
		}else{
			return false;
		}
	}
}
