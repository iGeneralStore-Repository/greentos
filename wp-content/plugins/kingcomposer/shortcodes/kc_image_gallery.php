<?php

$output = $custom_links = $title = $type_class = $slider_item_start = $slider_item_end = $ul_start = $ul_end = '';
$slider_width = $navigation = $pagination = $image_masonry = $columns = '';

extract( $atts );

$wrp_class = apply_filters( 'kc-el-class', $atts );

$type_class = 'kc-grid';

if( !empty( $custom_links) && 'custom_link' === $click_action ) {

	$custom_links = preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n", $custom_links)));
	$custom_links_arr = explode("\n", $custom_links);
}

$el_classess = array(
	'kc_image_gallery',
	'kc-carousel-image',
	$type_class,
	$wrap_class
);

$images = explode( ',', $images );
$element_attribute = array();

$element_attribute[] = 'class="'. esc_attr( implode( ' ', $el_classess ) ) .'"';

if( $type == 'image_masonry' )
	$element_attribute[] = 'data-image_masonry="yes"';

$attachment_data = $attachment_data_full = array();

foreach($images as $image_id){
	$attachment_data[] = wp_get_attachment_image_src( $image_id, $image_size );
	$attachment_data_full[] = wp_get_attachment_image_src( $image_id, 'full' );
}

ob_start();

if(!empty($title))
	echo '<h3 class="kc-title image-gallery-title">'. esc_html($title) .'</h3>';

?>

<div <?php echo implode(' ', $element_attribute ); ?>>
<?php
	
	if( !isset( $attachment_data[0] ) || empty( $attachment_data[0] ) ){
		
		echo '<h3 class="kc-image-gallery-title">Images Gallery: No images found</h3>';
		
	}else{
		
		$pretty_id = rand(434,43463453);
		echo $ul_start;
		foreach($attachment_data as $i => $image){

			switch( $click_action ){

				case 'none':
					echo '<div class="item-grid grid-'. $columns .'"><img src="'. esc_attr($image[0]) .'" /></div>';
					break;

				case 'large_image':
					echo '<div class="item-grid grid-'. $columns .'"><a href="'. esc_attr( $attachment_data_full[$i][0] ) .'" target="_blank">'
						.'<img src="'. esc_attr($image[0]) .'" /></a></div>';
					break;

				case 'lightbox':
					echo '<div class="item-grid grid-'. $columns .'"><a class="kc-image-link kc-pretty-photo" data-lightbox="kc-lightbox" rel="kc-pretty-photo['.$pretty_id.']" href="'. esc_attr( esc_attr( $attachment_data_full[$i][0] ) ) .'">'
						.'<img src="'. esc_attr($image[0]) .'" /></a></div>';
					break;

				case 'custom_link':
					if(isset($custom_links_arr[$i])){
						echo '<div class="item-grid grid-'. $columns .'"><a href="'. esc_attr( strip_tags($custom_links_arr[$i]) ) .'" target="_blank">'
							.'<img src="'. esc_attr($image[0]) .'" /></a></div>';
					}else{
						echo '<div class="item-grid grid-'. $columns .'"><img src="'. esc_attr($image[0]) .'" /></div>';
					}

					break;
			}


		}
		echo $ul_end;
	
	}
?>
</div>
<?php
$output = ob_get_clean();

echo '<div class="kc-image-gallery '.implode(' ', $wrp_class).'">'.$output.'</div>';


if($type == 'image_masonry') kc_js_callback('kc_front.image_gallery.masonry');
