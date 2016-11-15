<?php
/**
 * License handler for Easy Digital Downloads
 *
 * This class should simplify the process of adding license information
 * to new EDD extensions.
 *
 * @version 1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Forge_License' ) ) {

	/**
	 * Forge_License Class
	 */
	class Forge_License {
		private $file;
		private $license;
		private $item_name;
		private $item_id;
		private $item_shortname;
		private $version;
		private $author;
		private $api_url = 'http://forgeplugin.com';

		
		/**
		 * Class constructor
		 *
		 * @param string  $_file
		 * @param string  $_item_name
		 * @param string  $version
		 * @param string  $author
		 * @param string  $_optname
		 * @param string  $api_url
		 */
		function __construct( $file, $item, $version, $author, $api_url = null){

			$this->file = $file;

			if(is_numeric($item)){
				$this->item_id = absint($item);
			}else{
				$this->item_name = $item;
			}

			$this->item_shortname = 'forge_'.preg_replace('/[^a-zA-Z0-9_\s]/', '', str_replace(' ', '_', strtolower($this->item_name)));
			$this->version = $version;
			$this->license = forge_get_option($this->item_shortname.'_license');
			$this->author = $author;
			$this->api_url = is_null($api_url) ? $this->api_url : $api_url;

			// Setup hooks
			$this->includes();
			$this->hooks();
		}

		/**
		 * Include the updater class
		 *
		 * @access  private
		 * @return  void
		 */
		private function includes() {
			if(!class_exists('EDD_SL_Plugin_Updater')){
				require_once('EDD_SL_Plugin_Updater.php');
			}
		}

		/**
		 * Setup hooks
		 *
		 * @access  private
		 * @return  void
		 */
		private function hooks(){

			// Register settings
			add_filter('forge_settings', array($this, 'settings'), 1);

			// Activate license key on settings save
			add_action('admin_init', array( $this, 'activate_license'));

			// Deactivate license key
			add_action('admin_init', array( $this, 'deactivate_license'));

			// Check that license is valid once per week
			//add_action('edd_weekly_scheduled_events', array( $this, 'weekly_license_check' ) );

			// For testing license notices, uncomment this line to force checks on every page load
			//add_action('admin_init', array( $this, 'weekly_license_check' ) );

			// Updater
			add_action('admin_init', array( $this, 'auto_updater'), 0);

			// Display notices to admins
			add_action('admin_notices', array( $this, 'notices'));

			add_action('in_plugin_update_message-'.plugin_basename($this->file), array( $this, 'plugin_row_license_missing'), 10, 2);

		}

		/**
		 * Auto updater
		 *
		 * @access  private
		 * @return  void
		 */
		public function auto_updater() {

			$args = array(
				'version'   => $this->version,
				'license'   => $this->license,
				'author'    => $this->author
			);

			if( ! empty( $this->item_id ) ) {
				$args['item_id']   = $this->item_id;
			} else {
				$args['item_name'] = $this->item_name;
			}

			// Setup the updater
			$edd_updater = new EDD_SL_Plugin_Updater($this->api_url, $this->file, $args);
		}


		/**
		 * Add license field to settings
		 *
		 * @access  public
		 * @param array   $settings
		 * @return  array
		 */
		public function settings($data){
			$data[$this->item_shortname.'_license'] = array(
			'label' => $this->item_name,
			'section' => 'forge_licenses',
			'empty' => true,
			'default' => '',
			'type' => 'license');
			return $data;
		}


		/**
		 * Activate the license key
		 *
		 * @access  public
		 * @return  void
		 */
		public function activate_license() {
			
			$license_key = $this->item_shortname.'_license';
			if(isset($_POST['forge_settings'][$license_key]) && current_user_can('manage_options')){
				$new_license = trim(esc_attr($_POST['forge_settings'][$license_key]));
				$current_license = forge_get_option($license_key);
				$license_status = forge_get_option($license_key.'_status', 'forge_licenses');
				
				//Check license if not currently active, or if not empty and different from current one
				if(!empty($new_license) && ($license_status !== 'valid' || $new_license != $current_license)){
					$args = array(
					'edd_action' => 'activate_license', 
					'license' => urlencode($new_license), 
					'item_name' => urlencode($this->item_name),
					'url' => home_url());
					
					$response = wp_remote_post($this->api_url, array('timeout' => 15, 'sslverify' => false, 'body' => $args));
					if(is_wp_error($response)){
						return false;
					}
					
					$license_data = json_decode(wp_remote_retrieve_body($response));
					forge_update_option($license_key.'_status', $license_data->license, 'forge_licenses');
					forge_update_option($license_key.'_expires', strtotime($license_data->expires), 'forge_licenses');
					
					//Tell WordPress to look for updates
					//set_site_transient('update_plugins', null);
					
				}elseif($new_license == ''){
					
				}
			}
		}


		/**
		 * Deactivate the license key
		 *
		 * @access  public
		 * @return  void
		 */
		public function deactivate_license() {

			if ( ! isset( $_POST['forge_settings'] ) )
				return;

			if ( ! isset( $_POST['forge_settings'][ $this->item_shortname . '_license_key'] ) )
				return;

			if( ! wp_verify_nonce( $_REQUEST[ $this->item_shortname . '_license_key-nonce'], $this->item_shortname . '_license_key-nonce' ) ) {

				wp_die( __( 'Nonce verification failed', 'forge' ), __( 'Error', 'forge' ), array( 'response' => 403 ) );

			}

			if( ! current_user_can( 'manage_shop_settings' ) ) {
				return;
			}

			// Run on deactivate button press
			if ( isset( $_POST[ $this->item_shortname . '_license_key_deactivate'] ) ) {

				// Data to send to the API
				$api_params = array(
					'edd_action' => 'deactivate_license',
					'license'    => $this->license,
					'item_name'  => urlencode( $this->item_name ),
					'url'        => home_url()
				);

				// Call the API
				$response = wp_remote_post(
					$this->api_url,
					array(
						'timeout'   => 15,
						'sslverify' => false,
						'body'      => $api_params
					)
				);

				// Make sure there are no errors
				if ( is_wp_error( $response ) ) {
					return;
				}

				// Decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				delete_option( $this->item_shortname . '_license_active' );

			}
		}


		/**
		 * Check if license key is valid once per week
		 *
		 * @access  public
		 * @since   2.5
		 * @return  void
		 */
		public function weekly_license_check() {

			if( ! empty( $_POST['forge_settings'] ) ) {
				return; // Don't fire when saving settings
			}

			if( empty( $this->license ) ) {
				return;
			}

			// data to send in our API request
			$api_params = array(
				'edd_action'=> 'check_license',
				'license' 	=> $this->license,
				'item_name' => urlencode( $this->item_name ),
				'url'       => home_url()
			);

			// Call the API
			$response = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params
				)
			);

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			update_option( $this->item_shortname . '_license_active', $license_data );

		}


		/**
		 * Admin notices for errors
		 *
		 * @access  public
		 * @return  void
		 */
		public function notices() {

			static $showed_invalid_message;

			if( empty( $this->license ) ) {
				return;
			}

			$messages = array();

			$license = get_option( $this->item_shortname . '_license_active' );

			if( is_object( $license ) && 'valid' !== $license->license && empty( $showed_invalid_message ) ) {

				if( empty( $_GET['tab'] ) || 'licenses' !== $_GET['tab'] ) {

					$messages[] = sprintf(
						__( 'You have invalid or expired license keys for Forge. Please go to the <a href="%s" title="Go to Licenses page">Licenses page</a> to correct this issue.', 'forge' ),
						admin_url( 'admin.php?page=forge_settings&tab=licenses' )
					);

					$showed_invalid_message = true;

				}

			}

			if( ! empty( $messages ) ) {

				foreach( $messages as $message ) {

					echo '<div class="error">';
						echo '<p>' . $message . '</p>';
					echo '</div>';

				}

			}

		}

		/**
		 * Displays message inline on plugin row that the license key is missing
		 *
		 * @access  public
		 * @since   2.5
		 * @return  void
		 */
		public function plugin_row_license_missing( $plugin_data, $version_info ) {

			$license = forge_get_option($this->item_shortname.'_license_status', 'forge_licenses');

			if(('valid' !== $license)) {
				echo '&nbsp;<strong><a href="' . esc_url( admin_url( 'admin.php?page=forge_settings' ) ) . '">' . __( 'Enter your license key to enable automatic updates.', 'forge' ) . '</a></strong>';
			}

		}
	}

} // end class_exists check
