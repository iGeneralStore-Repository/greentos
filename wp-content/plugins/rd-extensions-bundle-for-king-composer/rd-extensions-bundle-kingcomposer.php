<?php
/*
Plugin Name: RD Extensions Bundle For King Composer
Plugin URI: https://wordpress.org/plugins/rd-extensions-bundle-for-king-composer/
Description: RD Extensions bundle For King Composer plugin is a mega powerful extensions/addons collection for King Composer page builder. New shortcodes extensions will add day by day with full Responsive feature and lots of customization Options.
Author: wpmetro
Author URI: http://wpmetro.com/
Version: 1.3.7
*/
if ( ! defined( 'ABSPATH' ) ) exit;

if(!function_exists('is_plugin_active')){
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

function kingrd_css_icon() {
    wp_enqueue_style('kingcom_admin_css', plugins_url( 'css/icon.css' , __FILE__ ) );    
}
add_action( 'admin_enqueue_scripts', 'kingrd_css_icon' );

if ( is_plugin_active( 'kingcomposer/kingcomposer.php' ) ){
    require_once ('shortcodes/hover_effects/hover-effects-shortcode.php');
    require_once ('shortcodes/animate_box/king-animate-box.php');
    require_once ('shortcodes/info_box/king_infobox.php');
    require_once ('shortcodes/modal/king_modal.php');
    require_once ('shortcodes/accordion/accordion-shortcode.php');
    require_once ('shortcodes/testimonial/king-testimonial-shortcode.php');
	require_once ('shortcodes/pricing-table/king-pricingtable-shortcode.php');
}

// Check If King Composer is activate
function rd_kinguser_required_plugin() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'kingcomposer/kingcomposer.php' ) ) {
        add_action( 'admin_notices', 'rd_king_user_required_plugin_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }

}
add_action( 'admin_init', 'rd_kinguser_required_plugin' );

function rd_king_user_required_plugin_notice(){
    ?><div class="error"><p>Error! you need to install or activate the <a href="https://wordpress.org/plugins/kingcomposer/">King Composer</a> plugin to run this plugin.</p></div><?php
}
?>