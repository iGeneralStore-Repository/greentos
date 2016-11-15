<#
	if( data === undefined )data = {};
	var width = '', output = '', attributes = [],
		atts = ( data.atts !== undefined ) ? data.atts : {},
		classes = kc.front.el_class( atts );

classes.push('kc_column');

if( atts['col_class'] !== undefined )
	classes.push( atts['col_class'] );

if( atts['css'] !== undefined )
	classes.push( atts['css'].split('|')[0] );
	
if( atts['col_id'] !== undefined && atts['col_id'] !== '' )
	attributes.push( 'id="'+ atts['col_id'] +'"' );
	
attributes.push( 'class="'+classes.join(' ')+'"' );

var col_container_class = ( atts['col_container_class'] !== undefined ) ? ' '+atts['col_container_class'] : '';

if( data.content === undefined )
	data.content = '';
	
data.content += '<div class="kc-element drag-helper" data-model="-1" droppable="true" draggable="true"><a href="javascript:void(0)" class="kc-add-elements-inner"><i class="sl-plus kc-add-elements-inner"></i></a></div>';

#><div {{{attributes.join(' ')}}}>
	<div class="kc-col-container{{col_container_class}}">{{{data.content}}}</div>
	<#
		if( atts[ 'css' ] !== undefined && atts[ 'responsive' ] !== undefined &&  atts[ 'responsive' ] !== '' ){
			#><style type="text/css">{{{kc.front.ui.style.responsive(atts['responsive'],atts['css'].split('|')[0])}}}</style><#
		}
	#>
</div>
