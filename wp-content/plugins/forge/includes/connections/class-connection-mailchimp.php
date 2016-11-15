<?php

class Forge_Connection_Mailchimp extends Forge_Connection_Generic {
	
	
	//Create class
	public function __construct(){
		parent::__construct();
		$this->type = 'mailchimp';
		$this->url = 'https://<dc>.api.mailchimp.com/3.0/';
		
		//Required credentials
		$this->credentials = array(
		'key' => __('API Key', 'forge'),
		);
		
		//Settings fields
		$this->fields = array(
		'list' => array('title' => __('Select A List', 'forge'), 'type' => 'list', 'choices' => array()),
		);
	}	
	
	
	//Make a single request
	private function request($verb, $method, $args = array()){
		if(!function_exists('curl_init') || !function_exists('curl_setopt')){
			return false;
		}

		$datacenter  = explode('-', $this->settings['key']);
		if(!isset($datacenter[1])){
			return false;
		}
        $this->url = str_replace('<dc>', $datacenter[1], $this->url);
		$url = $this->url.$method;
		

		$response = array('headers' => null, 'body' => null);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.api+json', 'Authorization: user '.$this->settings['key']));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		switch($verb) {
			case 'post':
			curl_setopt($ch, CURLOPT_POST, true);
			$encoded = json_encode($args);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded); 
			break;

			case 'get':
			$query = http_build_query($args);
			curl_setopt($ch, CURLOPT_URL, $url.'?'.$query);
			break;
		}

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
			$args = array('count' => 100);
			$request = $this->request('get', 'lists', $args);
			if(is_object($request)){
				$lists = array();
				foreach($request->lists as $key => $value){
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
			$args = array('count' => 100);
			$request = $this->request('get', 'lists', $args);
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

		$double_optin = true;

		$args = array(
		'apikey' => $this->settings['key'],
		'email_address' => $email,
		'merge_fields' => array('FNAME' => $first_name, 'LNAME' => $last_name),
		'status' => 'subscribed',
		);

		$request = $this->request('post', 'lists/'.$this->settings['list'].'/members', $args);
		
		//print_r($request);
		
		if(isset($request->status) && $request->status == 'subscribed'){
			return true;
		}else{
			return false;
		}
	}
}
