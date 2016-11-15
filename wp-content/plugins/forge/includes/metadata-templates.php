<?php 


//Metadata for displaying on pages
function forge_templating_metadata_pages(){
	$metadata = array(
	'always' => __('Show Always', 'forge'),
	'home' => __('Home Page', 'forge'),
	'404' => __('404 Pages', 'forge'),
	'search' => __('Search Pages', 'forge'));
	
	return $metadata;
}


//Metadata for displaying on pages
function forge_templating_metadata_post_types(){
	$metadata = array();
	
	//Add public post types
	$post_types = get_post_types(array('public' => true), 'objects');
	foreach($post_types as $current_type => $current_data){
		if(!isset($metadata[$current_type])){
			$metadata[$current_type] = $current_data->labels->name;
		}
	}
	
	return $metadata;
}


//Metadata for displaying on pages
function forge_templating_metadata_taxonomies(){
	$metadata = array();
	
	//Add public taxonomies
	$taxonomies = get_taxonomies(array('public' => true), 'objects');
	foreach($taxonomies as $taxonomy => $current_data){
		if(!isset($metadata[$taxonomy])){
			$metadata[$taxonomy] = $current_data->labels->name;
		}
	}
	
	return $metadata;
}


//Position within the site
function forge_templating_metadata_hooks($key = null){
	$metadata = array(
	'wordpress' => forge_templating_metadata_hooks_wordpress(),
	'furnace' => forge_templating_metadata_hooks_furnace(),
	'cpothemes' => forge_templating_metadata_hooks_cpothemes(),
	'woocommerce' => forge_templating_metadata_hooks_woocommerce(),
	//'edd' => forge_templating_metadata_hooks_edd(),
	'genesis' => forge_templating_metadata_hooks_genesis(),
	//'bbpress' => forge_templating_metadata_hooks_bbpress(),
	//'buddypress' => forge_templating_metadata_hooks_buddypress(),
	);
	
	$metadata = apply_filters('forge_templating_metadata_hooks', $metadata);
	return $key != null && isset($metadata[$key]) ? $metadata[$key] : $metadata;
}


//Position within the site
function forge_templating_metadata_hooks_wordpress($key = null){
	$metadata = array(
	'name' => 'WordPress',
	'description' => __('Standard WordPress action hooks.', 'forge'),
	'image' => FORGE_URL.'/images/hooks/wordpress.png',
	'hooks' => array(
		'wp_head' => __('In the head of the document', 'forge'),
		'wp_footer' => __('In the footer, right before closing the body tag', 'forge'),
		)
	);
	
	return $metadata;
}


//List of CPOThemes hooks
function forge_templating_metadata_hooks_cpothemes($key = null){
	$metadata = array(
	'name' => 'CPOThemes',
	'description' => __('Themes from CPOThemes', 'forge'),
	'image' => FORGE_URL.'/images/hooks/cpothemes.png',
	'hooks' => array(
		'cpotheme_before_wrapper' => __('Before the main website wrapper', 'forge'),
		'cpotheme_top' => __('In the topmost  bar', 'forge'),
		'cpotheme_header' => __('In the header area with the menu and logo', 'forge'),
		'cpotheme_before_main' => __('Before the main content area, above post content and sidebar', 'forge'),
		'cpotheme_before_title' => __('Before the title of the page', 'forge'),
		'cpotheme_title' => __('In the title of the page', 'forge'),
		'cpotheme_after_title' => __('After the title of the page', 'forge'),
		'cpotheme_before_content' => __('Before post content, at the same level as the sidebar', 'forge'),
		'cpotheme_after_content' => __('After post content, at the same level as the sidebar', 'forge'),
		'cpotheme_after_main' => __('After main content, under the post content and sidebar', 'forge'),
		'cpotheme_subfooter' => __('In the subfooter area', 'forge'),
		'cpotheme_before_footer' => __('Before the footer area', 'forge'),
		'cpotheme_footer' => __('In the footer', 'forge'),
		'cpotheme_after_footer' => __('After the footer area', 'forge'),
		'cpotheme_after_wrapper' => __('After the main website wrapper', 'forge'),
		'cpotheme_author_links' => __('On the links located in the author bios', 'forge'),
		'cpotheme_before_404' => __('Before the content of 404 pages', 'forge'),
		'cpotheme_404' => __('On 404 pages', 'forge'),
		'cpotheme_after_404' => __('After the content of 404 pages', 'forge'),
		)
	);
	
	return $metadata;
}


//List of CPOThemes hooks
function forge_templating_metadata_hooks_furnace($key = null){
	$metadata = array(
	'name' => 'Forge Themes',
	'description' => __('Furnace framework official themes', 'forge'),
	'image' => FORGE_URL.'/images/hooks/furnace.png',
	'hooks' => array(
		'furnace_before_wrapper' => __('Before the main website wrapper', 'forge'),
		'furnace_top' => __('In the topmost  bar', 'forge'),
		'furnace_header' => __('In the header area with the menu and logo', 'forge'),
		'furnace_before_main' => __('Before the main content area, above post content and sidebar', 'forge'),
		'furnace_before_title' => __('Before the title of the page', 'forge'),
		'furnace_title' => __('In the title of the page', 'forge'),
		'furnace_after_title' => __('After the title of the page', 'forge'),
		'furnace_before_content' => __('Before post content, at the same level as the sidebar', 'forge'),
		'furnace_after_content' => __('After post content, at the same level as the sidebar', 'forge'),
		'furnace_after_main' => __('After main content, under the post content and sidebar', 'forge'),
		'furnace_subfooter' => __('In the subfooter area', 'forge'),
		'furnace_before_footer' => __('Before the footer area', 'forge'),
		'furnace_footer' => __('In the footer', 'forge'),
		'furnace_after_footer' => __('After the footer area', 'forge'),
		'furnace_after_wrapper' => __('After the main website wrapper', 'forge'),
		'furnace_author_links' => __('On the links located in the author bios', 'forge'),
		'furnace_before_404' => __('Before the content of 404 pages', 'forge'),
		'furnace_404' => __('On 404 pages', 'forge'),
		'furnace_after_404' => __('After the content of 404 pages', 'forge'),
		)
	);
	
	return $metadata;
}


//Position within the site
function forge_templating_metadata_hooks_genesis($key = null){
	$metadata = array(
	'name' => 'Genesis',
	'description' => __('Genesis framework themes', 'forge'),
	'image' => FORGE_URL.'/images/hooks/genesis.png',
	'hooks' => array(
		'genesis_before' => __('Before the main website wrapper', 'forge'),
		'genesis_before_header' => __('Before the header area', 'forge'),
		'genesis_header' => __('In the header area before the site logo', 'forge'),
		'genesis_site_title' => __('In the site title', 'forge'),
		'genesis_site_description' => __('In the site description after the title', 'forge'),
		'genesis_header_right' => __('In the header to the right of the site title', 'forge'),
		'genesis_after_header' => __('After the header area', 'forge'),
		'genesis_before_content_sidebar_wrap' => __('Before the wrapper of main content area', 'forge'),
		'genesis_before_content' => __('Before the main content area', 'forge'),
		'genesis_before_loop' => __('Before the main loop', 'forge'),
		'genesis_loop' => __('In the loop, right before it starts', 'forge'),
		'genesis_before_entry' => __('Before an individual entry in the loop', 'forge'),
		'genesis_entry_header' => __('In the header of an individual entry', 'forge'),
		'genesis_entry_content' => __('In the content of an individual entry', 'forge'),
		'genesis_entry_footer' => __('In the footer of an individual entry', 'forge'),
		'genesis_after_entry' => __('After an individual entry in the loop', 'forge'),
		'genesis_after_endwhile' => __('After the loop finishes', 'forge'),
		'genesis_after_loop' => __('After the main loop', 'forge'),
		'genesis_after_content' => __('After the main content area', 'forge'),
		'genesis_before_sidebar_widget_area' => __('Before the sidebar widget area', 'forge'),
		'genesis_after_sidebar_widget_area' => __('After the sidebar widget area', 'forge'),
		'genesis_after_content_sidebar_wrap' => __('After the wrapper of main content area', 'forge'),
		'genesis_before_footer' => __('Before the footer', 'forge'),
		'genesis_footer' => __('In the footer', 'forge'),
		'genesis_after_footer' => __('After the footer', 'forge'),
		'genesis_after' => __('After the main website wrapper', 'forge'),
		)
	);
	
	return $metadata;
}


function forge_templating_metadata_hooks_bbpress($key = null){
	$metadata = array(
	'name' => 'bbPress',
	'description' => __('Forums and topics.', 'forge'),
	'image' => FORGE_URL.'/images/hooks/bbpress.png',
	'hooks' => array(
		'woocommerce_before_main' => __('Before main shop content', 'forge'),
		)
	);
	
	return $metadata;
}


function forge_templating_metadata_hooks_buddypress($key = null){
	$metadata = array(
	'name' => 'BuddyPress',
	'description' => __('Social pages and user profiles.', 'forge'),
	'image' => FORGE_URL.'/images/hooks/buddypress.png',
	'hooks' => array(
		'woocommerce_before_main' => __('Before main shop content', 'forge'),
		)
	);
	
	return $metadata;
}


function forge_templating_metadata_hooks_woocommerce($key = null){
	$metadata = array(
	'name' => 'WooCommerce',
	'description' => __('Shop and store pages', 'forge'),
	'image' => FORGE_URL.'/images/hooks/woocommerce.png',
	'hooks' => array(
		'woocommerce_before_main' => __('Before main shop content', 'forge'),
		'woocommerce_before_cart' => __('Before the cart contents', 'forge'),
		'woocommerce_after_cart' => __('After the cart contents', 'forge'),
		'woocommerce_before_checkout_form' => __('Before the checkout form', 'forge'),
		'woocommerce_after_checkout_form' => __('After the checkout form', 'forge'),
		'woocommerce_thankyou' => __('In the thank you page shown after making a purchase', 'forge'),
		)
	);
	
	return $metadata;
}


//Position within the site
function forge_templating_metadata_hooks_edd($key = null){
	$metadata = array(
	'name' => 'Easy Digital Downloads',
	'description' => __('Shop pages powered by EDD.', 'forge'),
	'image' => FORGE_URL.'/images/hooks/edd.png',
	'hooks' => array(
		'woocommerce_before_main' => __('Before main shop content', 'forge'),
		'woocommerce_before_cart' => __('Before the cart contents', 'forge'),
		'edd_after_download_content' => __('After the contents of a single download item.', 'forge'),
		'woocommerce_before_checkout_form' => __('Before the checkout form', 'forge'),
		'woocommerce_after_checkout_form' => __('After the checkout form', 'forge'),
		'woocommerce_thankyou' => __('In the thank you page shown after making a purchase', 'forge'),
		)
	);
	
	return $metadata;
}