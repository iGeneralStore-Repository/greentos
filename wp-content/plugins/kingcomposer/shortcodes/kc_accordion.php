<?php
/**
 * kc_accordion shortcode
 **/

$css = $title = ''; 
extract( $atts );

$output = '';

$element_attributes = array();
$css_classes        = apply_filters('kc-el-class', $atts);
$css_classes[]      = 'kc_accordion_wrapper';

if( isset( $class ) )
	array_push( $css_classes, $class );

if( $atts['open_all'] == 'yes' )
	$element_attributes[] = 'data-allowopenall="true"';
	
$css_class = implode(' ', $css_classes);

$element_attributes[]   = 'class="' . esc_attr( trim( $css_class ) ) . '"';

?>
<div <?php echo implode( ' ', $element_attributes ) ;?>>
<?php
if( !empty($title) ):
?>
    <h3 class="kc-accordion-title"><?php echo esc_html($title);?></h3>
<?php
endif;
echo do_shortcode( str_replace( 'kc_accordion#', 'kc_accordion', $content ) );
?>
</div>
