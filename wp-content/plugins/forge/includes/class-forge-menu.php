<?php 

if(!class_exists('Forge_Menu_Walker')){
	class Forge_Menu_Walker extends Walker_Nav_Menu {
		
		function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0){
			global $wp_query;
			$indent = ($depth) ? str_repeat("\t", $depth): '';

			$class_names = $value = '';

			$classes = empty($item->classes)? array(): (array)$item->classes;

			$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
			
			$class_names .= !empty($item->description) ? ' forge-menu-has-description' : '';
			$class_names = ' class="'. esc_attr($class_names).'"';

			$output .= $indent.'<li id="forge-menu-item-'.$item->ID.'"'.$value.$class_names.'>';

			$attributes  = !empty($item->attr_title )? ' title="'.esc_attr($item->attr_title).'"': '';
			$attributes .= !empty($item->target )? ' target="'.esc_attr($item->target).'"': '';
			$attributes .= !empty($item->xfn )? ' rel="'.esc_attr($item->xfn).'"': '';
			$attributes .= !empty($item->url) && $item->url != '#' ? ' href="'.esc_attr($item->url).'"': '';

			$description  = !empty($item->description) ? '<span class="forge-menu-description">'.do_shortcode($item->description).'</span>' : '';
			
			$item_output = $args->before;
			$item_output .= '<a'. $attributes .'>';
			$item_output .= '<span class="forge-menu-link">';
			$item_output .= $args->link_before;
			$item_output .= '<span class="forge-menu-title">'.apply_filters('the_title', $item->title, $item->ID).'</span>';
			$item_output .= $description;
			$item_output .= $args->link_after;
			$item_output .= '</span>';
			$item_output .= '</a>';
			$item_output .= $args->after;

			$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);	
		}
		
		function display_element($element, &$children_elements, $max_depth, $depth=0, $args, &$output) {
			$id_field = $this->db_fields['id'];
			if(!empty($children_elements[$element->$id_field])){
				$element->classes[] = 'forge-has-children';
			}
			Walker_Nav_Menu::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
		}
	}
}
