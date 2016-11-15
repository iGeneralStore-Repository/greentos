<?php 

function forge_connections_page(){
    $services = forge_connections_list();
	echo '<div class="wrap forge-connections">';
	echo '<h1>';
	echo __('Forge Connections', 'forge');
	echo '<a href="#" class="page-title-action forge-connections-new">'.__('Add Connection', 'forge').'</a>';
	echo '</h1>';
	//Main Listing	
	echo '<div class="forge-connections-list">';
	$connections = get_option('forge_connections');
	if(!empty($connections)){
		foreach($connections as $connection_id => $connection_fields){
			$connection_name = isset($connection_fields['name']) ? esc_attr($connection_fields['name']) : '';
			$connection_status = isset($connection_fields['status']) ? esc_attr($connection_fields['status']) : '';
			$connection_type = isset($connection_fields['type']) ? esc_attr($connection_fields['type']) : '';
			
			echo '<div class="forge-connection forge-connection-type-'.$connection_type.'">';
			echo '<img class="forge-connection-image" src="'.FORGE_URL.'images/connections/'.$connection_type.'.png"/>';
			//Body
			echo '<div class="forge-connection-body">';
			echo '<h3 class="forge-connection-title">'.$connection_name.'</h3>';
			echo '<div class="forge-connection-type">'.$services[$connection_type].'</div>';
			// echo '<div class="forge-connection-status">'.$connection_status.'</div>';
			
			//Meta
			echo '<div class="forge-connection-meta">';
			echo '<div class="forge-connection-delete" data-id="'.$connection_id.'">'.__('Delete', 'forge').'</div>';
			echo '</div>';
			
			echo '</div>'; //End Body
			echo '</div>';
		}
	}
	echo '</div>';
	echo '</div>'; //End wrap
	
	//Add New Connection
	$connection_id = date('YmdHis');
	
	//Initial Setup -- Choose service, connect
	echo '<div class="forge-connections-modal forge-connections-create" style="display:none;">';
	echo '<h2 class="forge-connections-modal-title">1. '.__('Create New Connection', 'forge').'</h2>';
	echo '<form class="forge-connections-create-form" method="post">';
	echo '<p><select class="forge-connection-type" name="connection_type">';
	echo '<option value="none">'.__('(Select Service)', 'forge').'</option>';
	foreach($services as $current_service => $current_class){
		echo '<option value="'.$current_service.'">'.$current_class.'</option>';
	}
	echo '</select></p>';
	echo '<input type="hidden" name="connection_status" value="connect"/>';
	echo '<div class="forge-connections-create-fields"><input type="hidden" name="connection_required" value="" required/></div>';
	echo '<p class="forge-connections-submit" style="display:none;">';
	echo '<input type="submit" class="button button-primary" name="connection_connect" value="'.__('Connect To Service', 'forge').'"/>';
	echo '&nbsp;&nbsp;';
	echo '<a class="button forge-connections-create-cancel">'.__('Cancel', 'forge').'</a>';
    echo '</p>';
	echo '</form>';
    echo '</div>';
	
	
	//Second step -- Configure connection and finish
	echo '<div class="forge-connections-modal forge-connections-setup" style="display:none;">';
	echo '<h2 class="forge-connections-modal-title">2. '.__('Setup Connection', 'forge').'</h2>';
	echo '<form class="forge-connections-setup-form" method="post">';
	echo '<input type="text" name="connection_name" value="" placeholder="'.__('Name Your connection', 'forge').'" required/>';
	echo '<div class="forge-connections-setup-fields"></div>';
	echo '<p>';
	echo '<input type="submit" class="button button-primary" name="connection_create" value="'.__('Create Connection', 'forge').'"/>';
	echo '&nbsp;&nbsp;';
	echo '<a class="button forge-connections-setup-cancel">'.__('Go Back', 'forge').'</a>';
    echo '</p>';
    echo '</form>';
    echo '</div>';
	
	
	//List of connection services
	echo '<div class="forge-create-connection-services">';
	require_once(FORGE_DIR.'includes/connections/class-connection-generic.php');
	echo '<div class="forge-connections-fields-none"></div>';
	foreach($services as $current_service => $current_class){
		$class_name = 'Forge_Connection_'.$current_class;
		if(!class_exists($current_class)){
			require_once(FORGE_DIR.'includes/connections/class-connection-'.$current_service.'.php');
		}
		$service = new $class_name;
		echo '<div class="forge-connections-fields-'.$current_service.'">';
		foreach($service->get_credentials() as $current_field => $current_title){
			echo '<p><input type="text" class="forge-connections-field" name="connection_data['.$current_field.']" placeholder="'.$current_title.'" required/></p>';
		}
		echo '</div>';
	}
	echo '</div>';
}


add_action('wp_ajax_forge_request_create_connection', 'forge_connections_create');
function forge_connections_create(){
	if(defined('DOING_AJAX')){
		if(isset($_POST['fields'])){
			parse_str($_POST['fields'], $fields);
		}else{
			die();
		}
		
		$connection_type = isset($fields['connection_type']) ? esc_attr($fields['connection_type']) : false;
		$connection_data = isset($fields['connection_data']) ? $fields['connection_data'] : false;
		if($connection_type && $connection_data){
			
			//Prepare service
			$services = forge_connections_list();
			require_once(FORGE_DIR.'includes/connections/class-connection-generic.php');
			$class_name = 'Forge_Connection_'.$services[$connection_type];
			if(!class_exists($class_name)){
				require_once(FORGE_DIR.'includes/connections/class-connection-'.$connection_type.'.php');
			}
			$service = new $class_name;
			
			//Add credentials
			$error = false;
			foreach($service->get_credentials() as $current_field => $current_title){
				if(isset($connection_data[$current_field])){
					$service->set_setting($current_field, $connection_data[$current_field]);
				}else{
					$error = true;
				}
			}
			
			//Test connection
			if(!$error){				
				$result = $service->create();
				// Test successful. Save connection
				if($result){
					//Return values
					echo json_encode($result);
				}else{
					echo json_encode(array('status' => false));
				}
				die();
			}
		}
	}
}


add_action('wp_ajax_forge_request_save_connection', 'forge_connections_save');
function forge_connections_save(){
	if(defined('DOING_AJAX')){
		if(isset($_POST['fields'])){
			parse_str($_POST['fields'], $fields);
		}else{
			die();
		}
		
		$connection_type = isset($fields['connection_type']) ? esc_attr($fields['connection_type']) : false;
		$connection_data = isset($fields['connection_data']) ? $fields['connection_data'] : false;
		$connection_name = isset($fields['connection_name']) ? $fields['connection_name'] : 'New Connection';
		if($connection_type && $connection_data){
			
			//Prepare service
			$services = forge_connections_list();
			require_once(FORGE_DIR.'includes/connections/class-connection-generic.php');
			$class_name = 'Forge_Connection_'.$services[$connection_type];
			if(!class_exists($class_name)){
				require_once(FORGE_DIR.'includes/connections/class-connection-'.$connection_type.'.php');
			}
			$service = new $class_name;
			
			//Add credentials
			$error = false;
			foreach($service->get_credentials() as $current_field => $current_title){
				if(isset($connection_data[$current_field])){
					$service->set_setting($current_field, $connection_data[$current_field]);
				}else{
					$error = true;
				}
			}
			
			//Add fields
			$field_list = $service->get_fields();
			if(!empty($field_list)){
				foreach($field_list as $current_field => $current_title){
					if(isset($connection_data[$current_field])){
						$service->set_setting($current_field, $connection_data[$current_field]);
					}else{
						$error = true;
					}
				}
			}
			
			//Test connection
			if(!$error){				
				$service->set_name($connection_name);
				$result = $service->save();
				// Test successful. Save connection
				if($result){
					//Return values
					echo json_encode($result);
				}else{
					echo json_encode(array('status' => false));
				}
				die();
			}
		}
	}
}



add_action('wp_ajax_forge_request_delete_connection', 'forge_connections_delete');
function forge_connections_delete(){
	if(defined('DOING_AJAX')){
		if(current_user_can('manage_options') && isset($_POST['connection_id'])){
			$connection_id = esc_attr($_POST['connection_id']);
		
			$connections = get_option('forge_connections');
			if(is_array($connections)){
				unset($connections[$connection_id]);
			}
			update_option('forge_connections', $connections);
		}
	}
}


//Define customizer sections
function forge_connections_list(){
	$data = array(
	'activecampaign' => 'ActiveCampaign',
	'mailchimp' => 'Mailchimp',
	'sendy' => 'Sendy',
	'mailrelay' => 'Mailrelay',
	);
	
	return $data;
}


//A basic call to any API conenction
function forge_connections_call($url, $args = array()){
	$args['sslverify'] = false;
	$args['timeout'] = 16;
	return wp_remote_post($url, $args);
}