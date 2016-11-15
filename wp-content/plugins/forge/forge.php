<?php
/*
Plugin Name: Forge - Front-End Page Builder
Description: A versatile, easy to use front end page builder.
Author: CPOThemes
Version: 1.3.8
Author URI: http://www.cpothemes.com
Plugin URI: http://forgeplugin.com
Text Domain: forge
*/


// Exit if accessed directly
if(!defined('ABSPATH')) exit;

if(!class_exists('Forge')){

    class Forge {

        //Instance of Forge
        private static $instance;
		
		//Builder instance for the current post or taxonomy term
		public $builder = false;
		
		//General settings for forge
		private $settings = false;
		
		
		public static function instance(){
			if(!isset(self::$instance) && !(self::$instance instanceof Forge)){
				self::$instance = new Forge;
				self::$instance->constants();
				self::$instance->includes();
				self::$instance->hooks();
				self::$instance->builder = new Forge_Builder();
				
				add_action('plugins_loaded', array( self::$instance, 'load_textdomain'));
			}
			return self::$instance;
		}


        //Setup plugin constants
        private function constants() {
            define('FORGE_NAME', 'Forge');
			define('FORGE_VERSION', '1.3.8');
			define('FORGE_DIR', plugin_dir_path(__FILE__));
			define('FORGE_URL', plugin_dir_url(__FILE__));
        }


        //Include all the files
        private function includes() {
			require_once(FORGE_DIR.'includes/class-forge-settings.php');
			require_once(FORGE_DIR.'includes/class-forge-data.php');
			require_once(FORGE_DIR.'includes/class-forge-builder.php');
            require_once(FORGE_DIR.'includes/class-forge-license.php');
            require_once(FORGE_DIR.'includes/class-forge-menu.php');
            require_once(FORGE_DIR.'includes/class-forge-presets.php');
			require_once(FORGE_DIR.'includes/class-forge-notices.php');
            require_once(FORGE_DIR.'includes/general.php');
            require_once(FORGE_DIR.'includes/templates.php');
			require_once(FORGE_DIR.'includes/admin-dashboard.php');
			require_once(FORGE_DIR.'includes/admin-connections.php');
			require_once(FORGE_DIR.'includes/admin-extensions.php');
			require_once(FORGE_DIR.'includes/admin-templates.php');
			require_once(FORGE_DIR.'includes/admin-presets.php');
			require_once(FORGE_DIR.'includes/admin-settings.php');
			require_once(FORGE_DIR.'includes/metaboxes.php');
			require_once(FORGE_DIR.'includes/metadata.php');
			require_once(FORGE_DIR.'includes/metadata-settings.php');
			require_once(FORGE_DIR.'includes/metadata-icons.php');
			require_once(FORGE_DIR.'includes/metadata-customizer.php');
			require_once(FORGE_DIR.'includes/metadata-templates.php');
			require_once(FORGE_DIR.'includes/customizer.php');
			require_once(FORGE_DIR.'includes/forms.php');
			require_once(FORGE_DIR.'includes/icons.php');
			require_once(FORGE_DIR.'elements/element-post-content.php');
			require_once(FORGE_DIR.'elements/element-heading.php');
			require_once(FORGE_DIR.'elements/element-image.php');
			require_once(FORGE_DIR.'elements/element-row.php');
			require_once(FORGE_DIR.'elements/element-spacer.php');
			require_once(FORGE_DIR.'elements/element-text.php');
			require_once(FORGE_DIR.'elements/element-cf7.php');
			require_once(FORGE_DIR.'elements/element-edd.php');
			require_once(FORGE_DIR.'elements/element-woocommerce.php');
			require_once(FORGE_DIR.'elements/element-widgets.php');
			require_once(FORGE_DIR.'elements/element-widget-archives.php');
			require_once(FORGE_DIR.'elements/element-widget-calendar.php');
			require_once(FORGE_DIR.'elements/element-widget-categories.php');
			require_once(FORGE_DIR.'elements/element-widget-comments.php');
			require_once(FORGE_DIR.'elements/element-widget-meta.php');
			require_once(FORGE_DIR.'elements/element-widget-pages.php');
			require_once(FORGE_DIR.'elements/element-widget-rss.php');
			require_once(FORGE_DIR.'elements/element-widget-search.php');
			require_once(FORGE_DIR.'elements/element-widget-cloud.php');
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         *
         */
        private function hooks() {
            add_action('admin_print_styles', array( $this, 'admin_styles'));
            add_action('admin_enqueue_scripts', array( $this, 'admin_scripts'));
			add_action('in_plugin_update_message-forge/forge.php', array($this, 'upgrade_notice'));
        }


		//Add styles for the admin area
        function admin_styles(){
			wp_enqueue_style('forge-admin', FORGE_URL.'css/admin.css');
			wp_register_style('forge-fontawesome', FORGE_URL.'css/icon-fontawesome.css');
			wp_register_style('forge-linearicons', FORGE_URL.'css/icon-linearicons.css');
			wp_register_style('forge-typicons', FORGE_URL.'css/icon-typicons.css');
		}


        //Add styles for the admin area
        function admin_scripts(){
			wp_enqueue_script('forge-admin-general', FORGE_URL.'scripts/admin.js');
		}


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = FORGE_DIR.'/languages/';
            $lang_dir = apply_filters('forge_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'forge');
            $mofile = sprintf('%1$s-%2$s.mo', 'forge', $locale);
            $mofile_local = $locale.'.mo';

            // Setup paths to current locale file
            $mofile_local   = $lang_dir.$mofile_local;
            $mofile_global  = WP_LANG_DIR.'/forge/'.$mofile;
			
			if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/forge/ folder
                load_textdomain('forge', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/forge/languages/ folder
                load_textdomain('forge', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain('forge', false, $lang_dir );
            }
        }


		/**
         * Add action link in plugin page.
         *
         * @access      public
         * @since       1.0.0
         */
		//add_filter('plugin_action_links', 'action_links', 10, 2);
		function action_links($links, $file){
			if($file == 'forge/forge.php'){
				$new_links = '<a href="'.admin_url('options-general.php?page=forge_settings').'">'.__('Settings', 'forge').'</a>';
				array_unshift($links, $new_links);
			}
			return $links;
		}


		/**
         * Add upgrade notice for plugin page.
         *
         * @access      public
         * @since       1.0.0
         */
		function upgrade_notice($current, $new = ''){
			if(isset($new->upgrade_notice) && strlen(trim($new->upgrade_notice)) > 0){
				echo '<p style="background-color:#d54e21; padding:10px; color:#f9f9f9; margin-top:10px">';
				echo esc_html($new->upgrade_notice);
				echo '</p>';
			}
		}
		
		
		//Retrieve builder object
		function builder(){
			return $this->builder;
		}
    }
} // End if class_exists check


/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       1.0.0
 * @return      void
 */
register_activation_hook(__FILE__, 'forge_plugin_activation');
function forge_plugin_activation(){
	forge_settings_defaults();
	//flush_rewrite_rules();
}


/**
 * The deactivation hook.
 *
 * @since       1.0.0
 * @return      void
 */
register_deactivation_hook(__FILE__, 'forge_plugin_deactivation');
function forge_plugin_deactivation(){
	//flush_rewrite_rules();
}



//Start up Forge.
function forge_load() {
	return Forge::instance();
}
forge_load();