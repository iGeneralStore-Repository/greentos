<?php

class Forge_Connection_Sendy extends Forge_Connection_Generic {
	
	
	//Create class
	public function __construct(){
		parent::__construct();
		$this->type = 'sendy';
		$this->url = '';
		
		//Required credentials
		$this->credentials = array(
		'url' => __('URL', 'forge'),
		'list' => __('List Key', 'forge'),
		);
		
		//Settings fields
		$this->fields = array();
	}	
	
	
	//Make a single request
	private function request($args){
		//Set URL
		$this->url = trailingslashit($this->settings['url']).'subscribe';
		
		//Connection args
		$opts = array('http' => array(
		'method'  => 'POST', 
		'header'  => 'Content-type: application/x-www-form-urlencoded', 
		'content' => http_build_query($args)));
		
		//Make connection
		$context = stream_context_create($opts);
		$response = file_get_contents($this->url, false, $context);

		return $response;
	}
	
	
	//Initial installation of the connection. Retrieves data for a 2-step install
	public function create(){
		if(isset($this->settings['url']) && isset($this->settings['list'])){
			$args = array(
			'email' => date('YmdHis').'test@test.com',
			'list' => $this->settings['list'],
			'boolean' => 'true');
			
			$request = $this->request($args);
			
			if($request == '1'){
				$fields = $this->generate_fields();
				$output = array(
				'status' => true,
				'fields' => $fields);
				return $output;
			}
		}
		return false;
	}
	
	
	//Add a subscriber
	public function subscribe($email, $fname = '', $lname = '', $arguments = null){
		$name = $fname;
		if($lname != ''){
			$name .= ' '.$lname;
		}
		
		$args = array(
		'email' => $email,
		'list' => $this->settings['list'],
		'boolean' => 'true');
		if($name != ''){
			$args['name'] = $name;
		}
		
		$request = $this->request($args);
		
		if($request == '1'){
			return true;
		}
		
		return false;
	}
}
