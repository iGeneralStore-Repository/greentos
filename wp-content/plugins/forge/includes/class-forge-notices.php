<?php

class Forge_Notices {
		
	//Class instance
	private static $instance;
	
	
	//Load a single Forge instance.
	public static function instance() {
		if(!isset(self::$instance) && !(self::$instance instanceof Forge_Notices)){
			self::$instance = new Forge_Notices;
			self::$instance->hooks();
		}
		return self::$instance;
	}
	
	
	//Initialize hooks
	public function hooks(){
		add_action('admin_init', array($this, 'control'));
		add_action('admin_notices', array($this, 'notice_rate'), 90);
		add_action('admin_notices', array($this, 'notice_course'), 90);
		add_action('forge_after_collection', array($this, 'notice_collection'), 100);
	}
	
	
	//Notice display and dismissal
	function control(){
		//Display a notice
		if(isset($_GET['forge-display']) && $_GET['forge-display'] != ''){
			forge_update_option(htmlentities($_GET['forge-display']), 'display', 'forge_notices');
			wp_redirect(remove_query_arg('forge-display'));
		}
		//Dismiss a notice
		if(isset($_GET['forge-dismiss']) && $_GET['forge-dismiss'] != ''){
			forge_update_option(htmlentities($_GET['forge-dismiss']), 'dismissed', 'forge_notices');
			wp_redirect(remove_query_arg('forge-dismiss'));
		}
	}


	//Display a notification for rating the plugin after 8 days
	function notice_rate(){	
		$date = get_option('forge_install', false);
		if($date){
			$diff = time() - strtotime($date); 
			if(current_user_can('manage_options') && $diff > 900000){
				$args = array(
				'title' => __('Have you found Forge useful? Help us improve it even more!', 'forge'),
				'content' => __('You have been using the Forge for some time now. What are your thoughts? We poured a lot of hours into creating it, and we\'d love it if you could give us a nice rating on the official plugin directory.', 'forge'),
				'dismiss' => __('I don\'t want to', 'forge'),
				'link_url' => 'https://wordpress.org/support/view/plugin-reviews/forge?rate=5#postform',
				'link_text' => __('Rate Forge And Help Us Out', 'forge'),
				'link_target' => '_blank');
				$this->display('notice_rate', $args);
			}
		}
	}
	
	
	//Display a notification for joining the page builder course after 1 day
	function notice_course(){	
		$date = get_option('forge_install', false);
		if($date){
			$diff = time() - strtotime($date); 
			if(current_user_can('manage_options') && $diff > 70000){
				
				$email = get_option('admin_email', '');
				$content = __('Do you want to use page builders like a professional designer? Here is a 5-day page building course full of techniques on using Forge. One email each day with clear, actionable tips.', 'forge');
				$content .= '<br>';
				$content .= '<form target="_blank" action="http://cpomail.com/subscribe" method="POST" accept-charset="utf-8" style="display:block;margin:10px 0 0;">';
				$content .= '<input type="text" name="email" id="email" value="'.$email.'" placeholder="'.__('Your email', 'forge').'"/>';
				$content .= '<input type="hidden" name="list" value="nXMTUHtceZwiUJg0keISiA"/>';
				$content .= '<input type="submit" class="button button-primary" name="submit" id="submit" value="'.__('Get The Course', 'forge').'"/>';
				$content .= '</form>';
				
				$args = array(
				'title' => __('Thank you for using Forge.', 'forge'),
				'content' => $content,
				'dismiss' => __('Not Interested', 'forge'),
				'link_target' => '_blank');
				$this->display('notice_course', $args);
			}
		}
	}
	
	
	//Display a notification for rating the plugin
	public function display($name, $args){	
		$display = true;
		
		//Don't display if user permissions are low, or dismissed
		$welcome_dismissed = trim(forge_get_option($name, 'forge_notices', false));
		if($welcome_dismissed == 'dismissed'){
			$display = false;
		}
		
		//Don't display on seoncary screens, don't be too nagging
		$screen = get_current_screen();
		if(isset($_GET['action']) && $_GET['action'] == 'edit' || $screen->action == 'add' || $screen->base == 'plugins' || $screen->base == 'widgets'){
			$display = false;
		}
		
		if($display){
			echo '<div class="forge-notice notice">';
			echo '<div class="forge-notice-image"></div>';
			echo '<div class="forge-notice-body">';
			
			$dismiss = isset($args['dismiss']) ? $args['dismiss'] : __('Dismiss', 'forge');
			echo '<a class="forge-notice-dismiss" href="'.add_query_arg('forge-dismiss', $name).'">'.$dismiss.'</a>';
			
			//Title
			if(isset($args['title'])){
				echo '<div class="forge-notice-title">'.esc_attr($args['title']).'</div>';
			}
			
			//content
			if(isset($args['content'])){
				echo '<div class="forge-notice-content">'.$args['content'].'</div>';
			}
			
			//Button
			if(isset($args['link_url']) && isset($args['link_text'])){
				$target = isset($args['link_target']) ? $args['link_target'] : '';
				echo '<div class="forge-notice-links">';
				echo '<a href="'.esc_url($args['link_url']).'" class="button button-primary" target="'.$target.'" style="text-decoration: none;">'.$args['link_text'].'</a>';
				echo '</div>';
			}
			
			echo '</div>';
			echo '</div>';
		}
	}


	
	function notice_collection($output){	
		if(!class_exists('Forge_Plus') && current_user_can('manage_options')){
			$output .= '<div class="forge-upgrade-collection">';
			$output .= '<div class="forge-upgrade-collection-content">'.__('Get <b>Forge Plus</b> to add dozens of new elements such as buttons, maps, sliders, and much more.', 'forge').'</div>';
			$output .= '<b><a class="forge-upgrade-collection-link forge-link" href="'.esc_url('http://forgeplugin.com/extension/forge-plus?utm_source=plugin&utm_medium=link&utm_campaign=collection-forge-plus').'" target="_blank" style="text-decoration: none;">'.__('Learn More', 'forge').'</a></b> ';
			$output .= '</div>';
		}
		return $output;
	}

}

//Start up the Forge builder.
function forge_notices() {
	return Forge_Notices::instance();
}
forge_notices();