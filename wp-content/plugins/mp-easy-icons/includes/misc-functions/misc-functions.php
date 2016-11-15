<?php
/**
 * This file contains misc functions used by MP Easy Icons
 *
 * @since 1.0.0
 *
 * @package    MP Easy Icons
 * @subpackage Functions
 *
 * @copyright  Copyright (c) 2015, Mint Plugins
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @author     Philip Johnston
 */
 
/**
 * Function which returns an array of font awesome icons
 */
function mp_easy_icons_get_font_awesome_icons(){
	
	//Get all font styles in the css document and put them in an array
	$pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
	
	$args = array(
		'timeout'     => 5,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking'    => true,
		'headers'     => array(),
		'cookies'     => array(),
		'body'        => null,
		'compress'    => false,
		'decompress'  => true,
		'sslverify'   => false,
		'stream'      => false,
		'filename'    => null
	); 

	$response = wp_remote_retrieve_body( wp_remote_get( plugins_url( '/fonts/font-awesome/css/font-awesome.css', dirname( __FILE__ ) ), $args ) );
	
	preg_match_all($pattern, $response, $matches, PREG_SET_ORDER);
		
	$icons = array();

	foreach($matches as $match){
		$icons[$match[1]] = $match[1];
	}
	
	if ( empty( $icons ) ){
		echo plugins_url( '/fonts/font-awesome/css/font-awesome.css', dirname( __FILE__ ) );
	}
	
	return $icons;
}