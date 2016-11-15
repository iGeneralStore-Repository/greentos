<#
if( typeof( data ) == 'undefined' )
	data = {};

var output 			= '',
	icon_wrap_class = '',
	atts 			= ( data.atts !== undefined ) ? data.atts : {},
	custom_class	= ( atts['custom_class'] !== undefined )? atts['custom_class'] : '',
	has_link        = false,
	icon = label = link = link_title = link_target = link_url = '',
	css_class       = kc.front.el_class( atts ),
	link_attr  = [];
	
try{
	var icons = JSON.parse( kc.tools.base64.decode( atts['icons'] ).toString().replace( /\%SITE\_URL\%/g, kc_site_url ) );
}catch(ex){
	var icons = atts['icons'];
}

css_class.push( 'kc-multi-icons-wrapper' );
	
if( custom_class !== '')
	css_class.push( custom_class );

#>
<div class="{{{css_class.join(' ')}}}">
	<#
		for( var i in icons ){
			item = icons[i];
			link_att   = [];
			
			icon       = item['icon'];
			label      = item['label'];
			link       = ( item['link'] !== undefined )? item['link'] : '||';
			
			if( icon == '' )
				icon = 'fa-leaf';
			
			link     = link.split('|');
			
			link_target    = '_blank';
			link_url       = '#';
			link_title     = label;
			
			if( link[0] !== undefined && link[0] !== '' ){
				link_url = link[0];
			}
			
			if( link[1] !== undefined && link[1] !== '' )
				link_title = link[1];
			
			if( link[2] !== undefined && link[2] !== '' )
				link_target = link[2];
			
			link_att.push( 'href="' + link_url + '"' );
			link_att.push( 'target="' + link_target + '"' );
			link_att.push( 'title="' + link_title + '"' );
			link_att.push( 'class="multi-icons-link multi-icons' + icon + '"' );
			
			#>
			<a {{{link_att.join(' ')}}}>
				<i class="{{{icon}}}"></i>
			</a>
	<# } #>
</div>
