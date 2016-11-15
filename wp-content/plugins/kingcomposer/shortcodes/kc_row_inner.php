<?php

$output = $row_class_container = $row_class = $row_id = $css = '';

extract( $atts );


$css_classes = apply_filters( 'kc-el-class', $atts );

$css_classes = array_merge( $css_classes, array(
	'kc_row',
	'kc_row_inner',
	$row_class
));

if( $css != '' )
	$css_classes[] = $css;
	
$attributes = array();

if ( ! empty( $row_id ) ) {
	$attributes[] = 'id="' . esc_attr( $row_id ) . '"';
}

$attributes[] = 'class="' . esc_attr( trim( implode(' ', $css_classes) ) ) . '"';


if( empty($atts['column_align']) )
    $atts['column_align'] = 'center';

if( !empty( $atts['equal_height'] ) )
{
	$attributes[] = 'data-kc-equalheight="true"';
	$attributes[] = 'data-kc-row-action="true"';
    $attributes[] = 'data-kc-equalheight-align="'. $atts['column_align'] .'"';
}

$output .= '<div ' . implode( ' ', $attributes ) . '>';

if( !empty( $row_class_container ) )
	$output .= '<div class="'.esc_attr( $row_class_container ).'">';

$output .= do_shortcode( str_replace('kc_row_inner#', 'kc_row_inner', $content ) );

if( !empty( $row_class_container ) )
	$output .= '</div>';

$output .= '</div>';

echo $output;
