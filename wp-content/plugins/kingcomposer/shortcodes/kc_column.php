<?php

$width = $css = $output = $col_class = $col_container_class = $col_id = '';

extract( $atts );

$attributes = array();
$style = array();
$classes = apply_filters( 'kc-el-class', $atts );

$classes[] = 'kc_column';
$classes[] = @kc_column_width_class( $width );

if( !empty( $col_class ) )
	$classes[] = $col_class;

if( !empty( $col_id ) )
	$attributes[] = 'id="'. $col_id .'"';

if( count($style) > 0 )	
	$attributes[] = 'style="'.implode(';', $style).'"';

$attributes[] = 'class="' . esc_attr( trim( implode(' ', $classes) ) ) . '"';

$col_container_class = !empty( $col_container_class ) ? $col_container_class.' kc-col-container' : 'kc-col-container';

$output = '<div ' . implode( ' ', $attributes ) . '>'
		. '<div class="'.esc_attr( $col_container_class ).'">'
		. do_shortcode( str_replace('kc_column#', 'kc_column', $content ) )
		. '</div>'
		. '</div>';

echo $output;
