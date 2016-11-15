<?php

class Forge_Connection_ActiveCampaign extends Forge_Connection_Generic {
	
	
	//Create class
	public function __construct(){
		parent::__construct();
		$this->type = 'activecampaign';
		$this->url = '';
		
		//Required credentials
		$this->credentials = array(
		'url' => __('API URL', 'forge'),
		'key' => __('API Key', 'forge'),
		);
		
		//Settings fields
		$this->fields = array(
		'list' => array('title' => __('Select A List', 'forge'), 'type' => 'list', 'choices' => array()),
		);
	}	
	
	
	//Make a single request
	private function request($method, $args = array()){
		if(!function_exists('curl_init') || !function_exists('curl_setopt')){
			return false;
		}
		
		$this->url = $this->settings['url'];
		$apikey = $this->settings['key'];
		$query = $this->url."/admin/api.php?api_key=$apikey&$method";

		$ch = curl_init($query);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
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
			$request = $this->request('api_action=list_list&api_output=json&ids=all&full=0');
			if(is_object($request)){
				$lists = array();
				foreach($request as $key => $value){
					if(isset($value->name)){
						$lists[$value->id] = $value->name.' ('.$value->subscriber_count.')';
					}
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
			$request = $this->request('api_action=list_list&api_output=json&ids=all&full=0');
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
		
		$params = array(
		'api_action' => 'contact_sync',
		'email' => $email,
		'first_name' => $fname, 
		'last_name' => $lname,
		'p['.$this->settings['list'].']' => $this->settings['list'],
		'status['.$this->settings['list'].']' => 1,
		'instantresponders['.$this->settings['list'].']' => 1);
		
		$data = '';
		foreach($params as $key => $value){
			$data .= $key.'='.urlencode($value).'&';
		}
		$data = rtrim($data, '& ');
		
		$this->url = $this->settings['url'];
		$apikey = $this->settings['key'];
		$query = $this->url."/admin/api.php?api_key=$apikey&api_action=contact_sync&api_output=json";
		
		$ch = curl_init($query);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		curl_close($ch);

		$request = json_decode($response);
		
		if(isset($request->result_code) && $request->result_code == '1'){
			return true;
		}else{
			return false;
		}
	}
}
