<?php 

function forge_extensions_page(){
    
	//Main Listing
	echo '<div class="wrap">';
	echo '<h1>'.__('Forge Extensions', 'forge').'</h1>';
	echo '<div class="forge-extensions">';
	echo forge_extensions_get();
	echo '</div>';
	echo '</div>';
	
}


function forge_extensions_get(){
	$data = get_transient('forge_extensions_feed');
	if(false === $data){
		$url = 'https://forgeplugin.com/?feed=extensions';
		$feed = wp_remote_get(esc_url_raw($url), array('sslverify' => false));

		if(!is_wp_error($feed)){
			if(isset($feed['body']) && strlen($feed['body'] ) > 0){
				$data = wp_remote_retrieve_body($feed);
				set_transient('forge_extensions_feed', $data, 3600);
			}
		}else{
			$data = '<div class="forge-error">'.__('An error ocurred while retrieving the extensions list. Please try again later.', 'forge').'</div>';
		}
	}
	return $data;
}