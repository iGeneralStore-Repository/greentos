<?php


//Load all blocks and assign them to corresponding actions
add_action('wp_head', 'forge_templating_embed');
function forge_templating_embed(){
	$args = array(
	'post_type' => 'forge_template',
	'posts_per_page' => -1,
	'meta_query' => array(array('key' => 'template_hook', 'value' => '', 'compare' => '!=')));
	$blocks = new WP_Query($args);
	
	if($blocks->posts){
		foreach($blocks->posts as $post){
			$post_id = $post->ID;
			$template_hook = get_post_meta($post->ID, 'template_hook', true);
			$template_display = get_post_meta($post->ID, 'template_display', true);
			$template_priority = get_post_meta($post->ID, 'template_priority', true);
			if($template_priority == ''){
				$template_priority = 10;
			}
			$template_priority = absint($template_priority);
				
			//Add block to desired hook using anonymous function
			if($template_hook != ''){
				if(forge_templating_display_filter($template_display)){
					$template_type = get_post_meta($post->ID, 'template_type', true);
					if($template_type == 'action'){
						//Display template on an action hook
						add_action($template_hook, function() use ($post_id){ 
							echo forge_templating_embed_render($post_id); 
						}, $template_priority);
					}elseif($template_type == 'append'){
						//Append template to filter
						add_filter($template_hook, function($data) use ($post_id){ 
							$data .= forge_templating_embed_render($post_id); 
							return $data;
						}, $template_priority);
					}elseif($template_type == 'prepend'){
						//Prepend template to filter
						add_filter($template_hook, function($data) use ($post_id){ 
							$data = forge_templating_embed_render($post_id).$data;
							return $data;
						}, $template_priority);
					}					
				}
			}
		}
	}
}


//Determine whether to show the block on current page	
function forge_templating_display_filter($pages){
	//If show always, return
	if(!is_array($pages)) return false;
	
	foreach($pages as $current_filter => $current_data){
		//Standard filters
		switch($current_filter){
			case 'always': return true; break;
			case 'home': if(is_front_page()) return true; break;
			case 'post': if(is_single()) return true; break;
			case 'page': if(is_page()) return true; break;
			case '404': if(is_404()) return true; break;
			case 'search': if(is_search()) return true; break;
		}
		
		//Custom Post Types
		if(is_singular($current_filter)) return true;
		
		//Taxonomies
		if(is_tax($current_filter)) return true;
	}
	
	return false;
}


//Render a single block
function forge_templating_embed_render($id, $type = 'action'){
	$markup = new Forge_builder($id);
	$output = '<div class="forge-template-'.$id.'">';
	//$markup->data()->set_builder_active(false);
	$output .= $markup->generate();
	$output .= '</div>';
	return $output;		
}


//Generate shortcode for templates
function forge_templating_shortcode($atts, $content = null){
	$attributes = extract(shortcode_atts(array('id' => ''), $atts));
	
	if($id != '' && is_numeric($id)){	
		$markup = new Forge_builder($id);
		
		$output = '<div class="forge-template-shortcode forge-template-'.$id.'">';
		$output .= $markup->generate();
		$output .= '</div>';
		return $output;		
	}
}
add_shortcode('forge_template', 'forge_templating_shortcode');


class Forge_Templating_Widget extends WP_Widget{
	
	function __construct(){
		$args = array('classname' => 'forge-template', 'description' => __('Displays a specific Forge Template.', 'forge'));
		parent::__construct('forge-template', __('Forge - Template', 'forge'), $args);
	}

	function widget($args, $instance){
		extract($args);
		$widget_id = str_replace('-', '_', $widget_id);
		$template = intval($instance['template']);
		
		if($template > 0){
			$content = new Forge_builder($template);
			$content->data()->set_builder_active(false);
			$output = '<div class="forge-template-widget">';
			$output .= $content->generate();
			$output .= '</div>';
			
			//echo $before_widget;
			echo $output;
			//echo $after_widget;
		}
	}

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['template'] = (int)$new_instance['template'];
		return $instance;
	}

	function form($instance){
		$instance = wp_parse_args((array) $instance, array('template' => ''));
		$template = esc_attr($instance['template']);
		$template_list = new WP_Query(array('post_type' => 'forge_template', 'orderby' => 'title', 'posts_per_page' => -1)); ?>
		<p>
			<label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('Template', 'forge'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>">
				<option value="0" <?php if($template == 0) echo 'selected'; ?>><?php _e('(Select A Template)', 'forge'); ?></option>
				<?php foreach($template_list->posts as $current_template): ?>
				<option value="<?php echo esc_attr($current_template->ID); ?>" <?php if($template == $current_template->ID) echo 'selected'; ?>>
					<?php echo esc_attr($current_template->post_title); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</p>
	<?php 
	}
}

add_action('widgets_init', 'forge_templating_widget');
function forge_templating_widget(){
	register_widget('Forge_Templating_Widget');
}