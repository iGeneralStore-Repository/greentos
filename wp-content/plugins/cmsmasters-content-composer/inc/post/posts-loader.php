<?php
/**
 * @package 	WordPress Plugin
 * @subpackage 	CMSMasters Content Composer
 * @version		1.5.3
 * 
 * Attachments Posts Loader
 * Created by CMSMasters
 * 
 */


$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);

require_once($parse_uri[0] . 'wp-load.php');


if (isset($_POST['offset'])) {
	$layout = $_POST['layout'];
	$layout_mode = $_POST['layoutmode'];
	$orderby = $_POST['orderby'];
	$order = $_POST['order'];
	$count = $_POST['count'];
	$categories = $_POST['categories'];
	$metadata = $_POST['metadata'];
	$offset = $_POST['offset'];
	
	
	$orderby = ($orderby == 'popular') ? 'meta_value_num' : $orderby;
	
	
	$args_all = array( 
		'post_type' => 				'post', 
		'orderby' => 				$orderby, 
		'order' => 					$order, 
		'posts_per_page' => 		-1, 
		'category_name' => 			$categories, 
		'ignore_sticky_posts' => 	true 
	);
	
	
	if ($orderby == 'meta_value_num') {
		$args_all['meta_key'] = 'cmsmasters_likes';
	}
	
	
	$cmsmasters_query_all = new WP_Query($args_all);
	
	
	if ($cmsmasters_query_all->post_count <= ($offset + $count)) {
		echo 'finish';
	}
	
	
	wp_reset_query();
	
	
	$metadata_arr = array(
		'cmsmasters_metadata' => $metadata 
	);
	
	
	$args = array( 
		'post_type' => 				'post', 
		'orderby' => 				$orderby, 
		'order' => 					$order, 
		'posts_per_page' => 		$count, 
		'category_name' => 			$categories, 
		'ignore_sticky_posts' => 	true, 
		'offset' => 				$offset 
	);
	
	
	if ($orderby == 'meta_value_num') {
		$args['meta_key'] = 'cmsmasters_likes';
	}
	
	
	$cmsmasters_query = new WP_Query($args);
	
	
	if ($cmsmasters_query->have_posts()) : 
		while ($cmsmasters_query->have_posts()) : $cmsmasters_query->the_post();
			if ($layout == 'columns') {
				if ($layout_mode == 'puzzle') {
					if (get_post_format() != '') {
						echo cmsmasters_composer_ob_load_template('framework/postType/blog/page/puzzle/' . get_post_format() . '.php', $metadata_arr);
					} else {
						echo cmsmasters_composer_ob_load_template('framework/postType/blog/page/puzzle/standard.php', $metadata_arr);
					}
				} else {
					if (get_post_format() != '') {
						echo cmsmasters_composer_ob_load_template('framework/postType/blog/page/masonry/' . get_post_format() . '.php', $metadata_arr);
					} else {
						echo cmsmasters_composer_ob_load_template('framework/postType/blog/page/masonry/standard.php', $metadata_arr);
					}
				}
			} elseif ($layout == 'timeline') {
				if (get_post_format() != '') {
					echo cmsmasters_composer_ob_load_template('framework/postType/blog/page/timeline/' . get_post_format() . '.php', $metadata_arr);
				} else {
					echo cmsmasters_composer_ob_load_template('framework/postType/blog/page/timeline/standard.php', $metadata_arr);
				}
			} else {
				if (get_post_format() != '') {
					echo cmsmasters_composer_ob_load_template('framework/postType/blog/page/default/' . get_post_format() . '.php', $metadata_arr);
				} else {
					echo cmsmasters_composer_ob_load_template('framework/postType/blog/page/default/standard.php', $metadata_arr);
				}
			}
		endwhile;
	endif;
	
	
	wp_reset_query();
}

