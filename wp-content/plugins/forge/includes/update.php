<?php
	
//Plugin licenses
add_action('admin_init', 'forge_license_setup');
function forge_license_setup(){
	$license = trim(forge_get_option('license_forge'));
	$updater = new Forge_Plugin_Updater(FORGE_STORE_URL, FORGE_STORE_FILE, array(
	'remote_api_url'=> FORGE_STORE_URL,
	'author'	=> FORGE_STORE_AUTHOR,
	'version' => FORGE_VERSION,
	'item_name' => FORGE_NAME,
	'license' => $license,
	'url' => home_url()));
}


//Manage license activation
function forge_license_activate($license_key, $new_license){
	return;
	$option_key = 'forge_licenses';
	$current_license = forge_get_option($license_key);
	$license_status = forge_get_option($license_key.'_status', $option_key);

	//Check license if not currently active, or if not empty and different from current one
	if($new_license != '' && ($license_status != 'valid' || $new_license != $current_license)){
		$args = array(
		'edd_action' => 'activate_license', 
		'license' => urlencode($new_license), 
		'item_name' => urlencode(FORGE_NAME));
		$response = wp_remote_get(add_query_arg($args, FORGE_STORE_URL), array('timeout' => 15, 'sslverify' => false));

		if(!is_wp_error($response)){
			$license_data = json_decode(wp_remote_retrieve_body($response));
			forge_update_option($license_key.'_status', $license_data->license, $option_key);
		}
	}elseif($new_license == ''){
		forge_update_option($license_key.'_status', '', $option_key);
	}
}


//Manage license activation
//add_action('admin_notices', 'forge_license_notice');
function forge_license_notice(){
	$current_license_dismissed = forge_get_option('license_forge_notice');
	
	//If notice hasn't been explicitly dismissed, display it
	if(current_user_can('manage_options')){
		if($current_license_dismissed != 'dismissed'){
			$current_license = forge_get_option('license_forge');
			$current_license_status = trim(forge_get_option('license_forge_status', 'forge_licenses'));

			if($current_license_status != 'valid'){
				$core_path = FORGE_URL;
				echo '<div class="forge-notice notice">';
				echo '<div class="forge-notice-image"></div>';
				echo '<div class="forge-notice-body">';
				if($current_license_status == 'invalid' && $current_license != ''){
					echo '<div class="forge-notice-error">'.__('The license you entered is invalid.', 'forge').'</div>';
				}
				echo '<div class="forge-notice-title">'.__('Enter your Forge license to allow automatic updates.', 'forge').'</div>';
				echo '<div class="forge-notice-content">'.__('Please add your Forge license key in order to get automatic updates from your dashboard.', 'forge').'</div>';
				echo '<div class="forge-notice-links">';
				echo '<a href="options-general.php?page=forge_settings" class="button button-primary" target="_blank" style="text-decoration: none;">'.__('Enter License Key', 'forge').'</a> ';
				echo '<a href="//www.cpothemes.com/dashboard/purchase-history" class="button" target="_blank">'.__('Get Your License Key', 'forge').'</a>';
				echo '<a href="'.add_query_arg('forge-dismiss', 'notice_rate').'" class="forge-notice-dismiss" style="color:#888; line-height:28px; margin-left:15px; text-decoration:none;">'.__('Dismiss This Notice', 'forge').'</a>';
				echo '</div>';
				echo '</div>';
				
				echo '</div>';
			}
		}
	}
}


//Notice display and dismissal
add_action('admin_init', 'forge_admin_notice_control');
function forge_admin_notice_control(){
	//Display a notice
	if(isset($_GET['forge-display']) && $_GET['forge-display'] != ''){
		forge_update_option(htmlentities($_GET['forge-display']), 'display');
		wp_redirect(remove_query_arg('forge-display'));
	}
	//Dismiss a notice
	if(isset($_GET['forge-dismiss']) && $_GET['forge-dismiss'] != ''){
		forge_update_option(htmlentities($_GET['forge-dismiss']), 'dismissed');
		wp_redirect(remove_query_arg('forge-dismiss'));
	}
}