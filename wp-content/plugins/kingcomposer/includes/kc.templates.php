<?php
/**
*
*	King Composer
*	(c) KingComposer.com
*
*/
if(!defined('KC_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
	
$kc = KingComposer::globe();
	
?>
<script type="text/html" id="tmpl-kc-clipboard-template">
	<div id="kc-clipboard">
		<ul class="ms-funcs">
			<li>
				<strong><?php _e('Tips', 'kingcomposer'); ?>:</strong> 
				<?php _e('Drag and drop to arrange items, click to select an item. Read more', 'kingcomposer'); ?> 
				<a href="<?php echo esc_url('http://docs.kingcomposer.com/documentation/copy-cut-double-paste-for-element-column-row/?source=client_installed'); ?>" target="_blank"><?php _e('Document', 'kingcomposer'); ?></a>
			</li>
			<li class="delete button delete right">
				<?php _e('Delete selected', 'kingcomposer'); ?> <i class="sl-close"></i>
			</li>
			<li class="select button right">
				<?php _e('Select all', 'kingcomposer'); ?> <i class="sl-check"></i>
			</li>
			<li class="unselect button right">
				<?php _e('Unselect all', 'kingcomposer'); ?> <i class="sl-close"></i>
			</li>
		</ul>
		<#
		try{
			var clipboards = kc.backbone.stack.get( 'KC_ClipBoard' ), 
				outer = '<div style="text-align:center;margin:20px auto;"><?php _e('The ClipBoard is empty, Please copy elements to clipboard', 'kingcomposer'); ?>.</div>';
			
			if( clipboards.length > 0 ){
				
				var stack, map, li = '';
					
				for( var n in clipboards ){
					if( clipboards[n] != null && clipboards[n] != undefined ){
						
						stack = clipboards[n];
						map = kc.maps[stack.title];
						
						li += '<li data-sid="'+n+'" title="<?php _e('Click to select, drag to move', 'kingcomposer'); ?>">';
						if( map != undefined ){
							if( map['icon'] != undefined )
								li += '<span class="ms-icon cpicon '+map['icon']+'"></span>';
						}
						li += '<span class="ms-title">'+stack.title.replace(/\kc_/g,'').replace(/\_/g,' ').replace(/\-/g,' ')+'</span>';
						li += '<span class="ms-des">'+kc.tools.unesc(stack.des)+'</span>';
						li += '<i title="<?php _e('Paste now', 'kingcomposer'); ?>" class="ms-quick-paste fa-paste"></i></li>';
						
					}
				}
				
			}else{
				li = '<h2 class="align-center"><?php _e('No items found, please copy elements first.', 'kingcomposer'); ?></h2>';
			}
		}catch(e){console.log(e);}	
		#>
		<ul class="ms-list">{{{li}}}</ul>
	</div>
	<# 
		data.callback = kc.ui.clipboard;
	#>
</script>
<script type="text/html" id="tmpl-kc-post-settings-template">
	<div id="kc-page-settings">
		<h1 class="mgs-t02">
			<?php _e('Page Settings', 'kingcomposer'); ?>
		</h1>
		<button class="button pop-btn save-post-settings"><?php _e('Save', 'kingcomposer'); ?></button>
		<div class="m-settings-row">
			<div class="msr-left">
				<label><?php _e('Body Class', 'kingcomposer'); ?></label>
				<span><?php _e('The class will be added to body tag on the front-end', 'kingcomposer'); ?> </span>
			</div>
			<div class="msr-right">
				<div class="msr-content">
					<input class="kc-post-classes-inp" type="text" placeholder="Body classes" value="{{data.classes}}" />
				</div>
			</div>
		</div>
		<div class="m-settings-row">
			<div class="msr-left">
				<label><?php _e('Max width container', 'kingcomposer'); ?></label>
				<span><?php _e('The max width of container for this page (default is 1170px). You also can change it in KC general settings', 'kingcomposer'); ?> </span>
			</div>
			<div class="msr-right">
				<div class="msr-content">
					<input class="kc-post-max-width-inp" type="text" placeholder="Max width container" value="{{data.max_width}}" />
				</div>
			</div>
		</div>
		<div class="m-settings-row">
			<div class="msr-left msr-single">
				<label><?php _e('Css Code', 'kingcomposer'); ?></label>
				<button class="button button-larger css-beautifier float-right">
					<i class="sl-energy"></i> <?php _e('Beautifier', 'kingcomposer'); ?>
				</button>
				<textarea class="rt03 kc-post-css-inp">{{data.css}}</textarea>
				<i><?php _e('Notice: CSS must contain selectors', 'kingcomposer'); ?></i>
			</div>
		</div>
		
		<div class="m-settings-row">
			<div class="msr-left">
				<label><?php _e('Scroll Assistant', 'kingcomposer'); ?></label>
				<span>
					<?php _e('Keep the viewport in a reasonable place while a popup is opened', 'kingcomposer'); ?>.
					<?php if(!defined('KC_SLUG')||md5(KC_SLUG)!='0f882acc192505fa98c9a8e1167539a1')exit; ?>
				</span>
			</div>
			<div class="msr-right">
				<div class="msr-content">
					<div class="kc-el-ui meu-boolen" data-cfg="scrollAssistive" data-type="radio" onclick="kc.ui.elms(event,this)">
						<ul>
							<li<# if(kc.cfg.scrollAssistive==1){ #> class="active"<# } #>>
								<input type="radio" name="m-c-layout" value="1" />
							</li>
							<li<# if(kc.cfg.scrollAssistive!=1){ #> class="active"<# } #>>
								<input type="radio" name="m-c-layout" value="0" />
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		
		<div class="m-settings-row">
			<div class="msr-left">
				<label><?php _e('Scroll Prevention', 'kingcomposer'); ?></label>
				<span>
					<?php _e('Keep the web page unmoved while scrolling a popup', 'kingcomposer'); ?>.
				</span>
			</div>
			<div class="msr-right">
				<div class="msr-content">
					<div class="kc-el-ui meu-boolen" data-cfg="preventScrollPopup" data-type="radio" onclick="kc.ui.elms(event,this)">
						<ul>
							<li<# if(kc.cfg.preventScrollPopup==1){ #> class="active"<# } #>>
								<input type="radio" name="m-c-layout" value="1" />
							</li>
							<li<# if(kc.cfg.preventScrollPopup!=1){ #> class="active"<# } #>>
								<input type="radio" name="m-c-layout" value="0" />
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		
		<div class="m-settings-row">
			<div class="msr-left">
				<label><?php _e('Tooltips display', 'kingcomposer'); ?></label>
				<span>
					<?php _e('A brief description will appear when you hover the function icon', 'kingcomposer'); ?>.
				</span>
			</div>
			<div class="msr-right">
				<div class="msr-content">
					<div class="kc-el-ui meu-boolen showTipsCfg" data-cfg="showTips" data-type="radio">
						<ul>
							<li<# if(kc.cfg.showTips==1){ #> class="active"<# } #>>
								<input type="radio" name="m-c-layout" value="1" />
							</li>
							<li<# if(kc.cfg.showTips!=1){ #> class="active"<# } #>>
								<input type="radio" name="m-c-layout" value="0" />
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="m-settings-row">
			<div class="msr-left">
				<label><?php _e('Thumbnail', 'kingcomposer'); ?></label>
				<span>
					<?php _e('The thumbnail for this content, it will display in sections list', 'kingcomposer'); ?>.
				</span>
				<br />
				<img class="thumnail-preview" src="{{(data.thumbnail === undefined || data.thumbnail === '')?kc.cfg.defaultImg:data.thumbnail}}" alt="" />
				<br />
				<input type="text" placeholder="<?php _e('Image url', 'kingcomposer'); ?>" class="kc-post-thumbnail" name="m-c-thumbnail" value="{{data.thumbnail}}" />
			</div>
			<div class="msr-right">
				<div class="msr-content">
					<button class="button button-larger button-primary open-library"><?php _e('Open Library', 'kingcomposer'); ?></button>
				</div>
			</div>
		</div>
		<button class="button pop-btn save-post-settings"><?php _e('Save', 'kingcomposer'); ?></button>
	</div>
	<#
		data.callback = function( wrp, $ ){
			
			wrp.find('.save-post-settings').on( 'click', wrp, function(e){
				
				$('#kc-page-body-classes').val( e.data.find('input.kc-post-classes-inp').val() );
				$('#kc-page-max-width').val( e.data.find('input.kc-post-max-width-inp').val() );
				$('#kc-page-css-code').val( e.data.find('textarea.kc-post-css-inp').val() );
				$('#kc-page-thumbnail').val( e.data.find('input.kc-post-thumbnail').val() );
				
				kc.get.popup( this, 'close' ).trigger('click');
				
			});
			
			wrp.find('.css-beautifier').on( 'click', function(){
				var txta = $(this).parent().find('textarea');
				txta.val( kc.tools.decode_css( txta.val() ) );
			});
			
			wrp.find('.showTipsCfg').on( 'click', function(event){
				kc.ui.elms( event, this );
				if( kc.cfg.showTips == 1 )
					$('#kc-container').removeClass('hideTips');
				else $('#kc-container').addClass('hideTips');
			});
			
			wrp.find('.open-library').on('click', { callback : function( atts ){
		
				var wrp = $(this.el).closest('.m-settings-row'), url = atts.url;

				if( atts.size != undefined && atts.size != null && atts.sizes[atts.size] != undefined ){
					var url = atts.sizes[atts.size].url;
				}else if( typeof atts.sizes.medium == 'object' ){
					var url = atts.sizes.medium.url;
				}

				if( url != undefined && url != '' ){
					wrp.find('input[name="m-c-thumbnail"]').val(url);
					wrp.find('img.thumnail-preview').attr({src: url});
				}
				
			}, atts : {frame:'post'} }, kc.tools.media.open );
			
			wrp.find('input[name="m-c-thumbnail"]').on('change', function(){
				$(this).closest('.m-settings-row').find('img.thumnail-preview').attr({src: this.value});
			})
			
		}
	#>
</script>

<script type="text/html" id="tmpl-kc-presets-template"><#

var items = kc.backbone.stack.get( 'kc_presets', data.name ),
	cates = [], i;

if( typeof items == 'object' ){
	for( i in items ){
		if( items[i][0] !== '' && cates.indexOf( items[i][0] ) === -1 )
			cates.push( items[i][0] );
	}
}

#><li class="kc-presets-list<# if(data.class !== undefined){#> {{data.class}}<#} #>">
	<h2 class="kc-pretit">
		{{data.name.replace('kc_','').replace(/\-/g,' ').replace(/\_/g,' ')}}
		<?php _e('Presets', 'kingcomposer'); ?>
	</h2>
	<a href="#add_preset" class="add kbtn"><i class="fa-plus"></i> <?php _e('Create new preset', 'kingcomposer'); ?></a>
	<a href="#close" class="preset-close" title="<?php _e('Close Presets', 'kingcomposer'); ?>"></a>
	<div class="kc-preset-create" style="display: none" data-mesg="<?php _e('Create new element preset based on the current settings', 'kingcomposer'); ?>">
		<input type="text" class="kc-preset-name-input" placeholder="<?php _e('Preset name', 'kingcomposer'); ?>" />
		<input type="text" class="kc-preset-cats-input" placeholder="<?php _e('Preset category', 'kingcomposer'); ?>" />
		<button class="kc-preset-create-button"><i class="fa-check"></i>
		<button class="kc-preset-create-close"><i class="fa-times"></i></button>
		<# if( cates.length > 0 ){ #>
			<ul class="kc-pre-cats">
				<#
					for( i in cates ){
						#><li>{{cates[i]}}</li><#
					}
				#>
			</ul>
		<# } #>
		<h2 class="success-mesg"><?php _e('The preset has been created successfully', 'kingcomposer'); ?> </h2>
	</div>
	<# if( cates.length > 0 ){ #>
	<ul class="kc-preset-categories">
		<li><a href="#all" class="active">All</a></li>
		<#
			for( i in cates ){
				#><li>/</li><li><a href="#">{{cates[i]}}</a></li><#
			}
		#>
	</ul>
	<# } #>
	<div class="kc-preset-wrp">
		<div class="kc-preset-outer" data-name="{{data.name}}">
			<# if( typeof items == 'object' && !_.isEmpty( items ) ){ #>
				<ul>
					<# for( i in items ){ #>
					<li data-name="{{data.name}}" class="kc-preset-item kc-preset-cat-{{kc.tools.esc_slug(items[i][0])}}" title="{{i}}">
						<p>{{i}}</p>
						<small>{{items[i][1]}}</small>
						<i class="sl-close" data-pid="{{i}}" data-pname="{{data.name}}" title="<?php _e('Delete preset', 'kingcomposer'); ?>"></i>
					</li>
					<# } #>
				</ul>
			<# }else{ #>
				<h2 class="kc-prempty">
					<?php _e('You have not created any preset for this element', 'kingcomposer'); ?> 
					<br />
					<a href="http://docs.kingcomposer.com/presets" target=_blank>
						<?php _e('Check the preset document', 'kingcomposer'); ?> 
					</a>
				</h2>
			<# } #>
		</div>
	</div>
</li>
<#
	data.callback = kc.ui.callbacks.presets;
#>
</script>

<script type="text/html" id="tmpl-kc-row-template">
<#
 
var fEr3 = '', Hrdw = '', sEtd4 = '';

if( data.row_id !== undefined && data.row_id != '__empty__' )
	sEtd4 = '#'+data.row_id;

if( data.disabled !== undefined && data.disabled == 'on' ){
	fEr3 = ' collapse',
	Hrdw = ' disabled';
}

if( data.__section_link !== undefined ){
	fEr3 += ' kc-section-link';
}

#>
	<div class="kc-row m-r-sortdable{{fEr3}}">
		<ul class="kc-row-control row-container-control">
		
		<# if( data.__section_link === undefined ){ #>
		
			<li class="right close mtips">
				<i class="sl-close"></i>
				<span class="mt-mes"><?php _e('Delete this row', 'kingcomposer'); ?></span>
			</li>
			<li class="double mtips">
				<i class="sl-docs"></i>
				<span class="mt-mes"><?php _e('Double this row', 'kingcomposer'); ?></span>
			</li>
			<li class="right settings mtips">
				<i class="sl-note"></i>
				<span class="mt-mes"><?php _e('Row settings', 'kingcomposer'); ?></span>
			</li>
			<li class="right move mtips">
				<i class="sl-cursor-move"></i>
				<span class="order-row">
					<input type="number" placeholder="order" /> <button><i class="fa-exchange"></i></button>
				</span>
				<span class="mt-mes"><?php _e('Drag and drop to arrange this row', 'kingcomposer'); ?></span>
			</li>
			
		<# }else{ #>
		
			<li class="right close mtips">
				<i class="sl-close"></i>
				<span class="mt-mes"><?php _e('Delete this section', 'kingcomposer'); ?></span>
			</li>
			<li class="right move mtips">
				<i class="sl-cursor-move"></i>
				<span class="order-row">
					<input type="number" placeholder="order" /> <button><i class="fa-exchange"></i></button>
				</span>
				<span class="mt-mes"><?php _e('Drag and drop to arrange this section', 'kingcomposer'); ?></span>
			</li>
			
		<# } #>
		
		</ul>
		<div class="kc-row-admin-view">{{sEtd4}}</div>
		<ul class="kc-row-control row-container-control pos-left">
			
			<# if( data.__section_link === undefined ){ #>
			
			<li class="right collapse mtips">
				<i class="sl-arrow-down"></i>
				<span class="mt-mes"><?php _e('Expand / Collapse this row', 'kingcomposer'); ?></span>
			</li>
			<li class="columns mtips">
				<i class="sl-list"></i>
				<span class="mt-mes"><?php _e('Set number of columns for this row', 'kingcomposer'); ?></span>
			</li>
			<li class="addToSections mtips">
				<i class="sl-share-alt"></i>
				<span class="mt-mes"><?php _e('Save this row to section', 'kingcomposer'); ?></span>
			</li>
			<li class="copy mtips">
				<i class="sl-doc"></i>
				<span class="mt-mes"><?php _e('Copy this row', 'kingcomposer'); ?></span>
			</li>
			<li class="rowStatus{{Hrdw}} mtips">
				<i></i>
				<span class="mt-mes"><?php _e('Publish / Unpublish this row', 'kingcomposer'); ?></span>
			</li>
			<# }else if( data.__section_title !== undefined ){ #>
				<li class="bpdl">
					<strong class="red"><i class="sl-link"></i> {{data.__section_title}}</strong>
					<?php if(!defined('KC_SLUG')||md5(KC_SLUG)!='0f882acc192505fa98c9a8e1167539a1')exit; ?>
				</li>
				<li class="right collapse mtips">
					<i class="sl-arrow-down"></i>
					<span class="mt-mes"><?php _e('Expand / Collapse this section', 'kingcomposer'); ?></span>
				</li>
				<li class="rowStatus{{Hrdw}} mtips">
					<i></i>
					<span class="mt-mes"><?php _e('Publish / Unpublish this section', 'kingcomposer'); ?></span>
				</li>
			<# } #>
		</ul>	
		<div class="kc-row-wrap"><# 
			if( data.__section_link !== undefined ){
			#>
			<div class="kc-row-section-body">
				<div class="kc-row-section-preview">
					<img src="<?php echo admin_url("/admin-ajax.php?action=kc_get_thumbn&size=full&type=post_featured&id="); ?>{{data.__section_link}}" />
					<a href="<?php echo admin_url('/post.php?action=edit&post='); ?>{{data.__section_link}}" class="kcrbtn edit" target=_blank>
						<i class="sl-note"></i> <?php _e('Go to edit this section', 'kingcomposer'); ?>
					</a>
					<button class="kcrbtn select select-another-section" data-current="{{data.__section_link}}">
						<i class="sl-list"></i> <?php _e('Select another section', 'kingcomposer'); ?>
					</button>
				</div>
			</div>
			<# } #></div>
	</div>
</script>
<script type="text/html" id="tmpl-kc-row-inner-template">
	<div class="kc-row-inner">
		<ul class="kc-row-control kc-row-inner-control">
			<li class="right delete mtips">
				<i class="sl-close"></i>
				<span class="mt-mes"><?php _e('Delete this row', 'kingcomposer'); ?></span>
			</li>
			<li class="right settings mtips">
				<i class="sl-note"></i>
				<span class="mt-mes"><?php _e('Open row settings', 'kingcomposer'); ?></span>
			</li>
			<li class="right double mtips">
				<i class="sl-docs"></i>
				<span class="mt-mes"><?php _e('Double this row', 'kingcomposer'); ?></span>
			</li>
			<li class="right move mtips">
				<i class="sl-cursor-move"></i>
				<span class="mt-mes"><?php _e('Drag and drop to arrange this row', 'kingcomposer'); ?></span>
			</li>
		</ul>
		<ul class="kc-row-control pos-left kc-row-inner-control">
			<li class="right collapse mtips">
				<i class="sl-arrow-down"></i>
				<span class="mt-mes"><?php _e('Expand / Collapse this row', 'kingcomposer'); ?></span>
			</li>
			<li class="right columns mtips">
				<i class="sl-list"></i>
				<span class="mt-mes"><?php _e('Set number of columns for this row', 'kingcomposer'); ?></span>
			</li>
			<li class="right copyRowInner mtips">
				<i class="sl-doc"></i>
				<span class="mt-mes"><?php _e('Copy this row', 'kingcomposer'); ?></span>
			</li>
		</ul>	
		<div class="kc-row-wrap"></div>
	</div>	
</script>
<script type="text/html" id="tmpl-kc-column-template">
	<div class="kc-column" style="width: {{data.width}}">
		<ul class="kc-column-control column-container-control">
			<li class="kc-column-settings more">
				<i class="sl-options edit"></i>
				<div class="mme-more-actions">
					<ul>
						<li class="double" title="<?php _e('Double column', 'kingcomposer'); ?>">
							<i class="sl-docs"></i>
						</li>
						<li class="insert" title="<?php _e('Insert new column', 'kingcomposer'); ?>">
							<i class="sl-doc"></i> 
						</li>
						<li class="add" title="<?php _e('Add element', 'kingcomposer'); ?>">
							<i class="sl-plus"></i>
						</li>
						<li class="delete" title="<?php _e('Delete column', 'kingcomposer'); ?>">
							<i class="fa fa-trash-o"></i>
						</li>
					</ul>
				</div>
				<span class="narrow edit"></span>
			</li>
		</ul>
		<div class="kc-column-wrap">
			<div class="kc-element drag-helper">
				<a href="javascript:void(0)" class="kc-add-elements-inner">
					<i class="sl-plus"></i> <?php _e('Add Element', 'kingcomposer'); ?>
				</a>
			</div>
		</div>
		<ul class="kc-column-control pos-bottom">
			<li class="add mtips">
				<i class="sl-plus"></i>
				<span class="mt-mes"><?php _e('Add elements to bottom of this column', 'kingcomposer'); ?></span>
			</li>
		</ul>
		<div class="column-resize cr-left"></div>
		<div class="column-resize cr-right"></div>
		<div class="kc-cols-info">{{Math.round(parseFloat(data.width))}}%</div>
	</div>
</script>
<script type="text/html" id="tmpl-kc-column-inner-template">
	<div class="kc-column-inner" style="width: {{data.width}}">
		<ul class="kc-column-control column-inner-control">
			<li class="kc-column-settings more">
				<i class="sl-options edit"></i>
				<div class="mme-more-actions">
					<ul>
						<li class="double" title="<?php _e('Double column', 'kingcomposer'); ?>">
							<i class="sl-docs"></i>
						</li>
						<li class="insert" title="<?php _e('Insert new column', 'kingcomposer'); ?>">
							<i class="sl-doc"></i> 
						</li>
						<li class="add" title="<?php _e('Add element', 'kingcomposer'); ?>">
							<i class="sl-plus"></i>
						</li>
						<li class="delete" title="<?php _e('Delete column', 'kingcomposer'); ?>">
							<i class="fa fa-trash-o"></i>
						</li>
					</ul>
				</div>
				<span class="narrow edit"></span>
			</li>
		</ul>
		<div class="kc-column-wrap">
			<div class="kc-element drag-helper">
				<a href="javascript:void(0)" class="kc-add-elements-inner">
					<i class="sl-plus"></i> <?php _e('Add Elements', 'kingcomposer'); ?>
				</a>
			</div>
		</div>
		<ul class="kc-column-control pos-bottom">
			<li class="add mtips">
				<i class="sl-plus"></i>
				<span class="mt-mes"><?php _e('Add elements to bottom of this column', 'kingcomposer'); ?></span>
			</li>
		</ul>
		<div class="column-resize cr-left"></div>
		<div class="column-resize cr-right"></div>
		<div class="kc-cols-info">{{Math.round(parseFloat(data.width))}}%</div>
	</div>
</script>
<script type="text/html" id="tmpl-kc-views-sections-template">
	<#
		try{
			var sct = kc.maps[data.name].views.sections;
			if( kc.maps[data.name].views.display == 'vertical' )
				var vertical = ' kc-views-vertical';
		}catch(e){
			var sct = 'kc_tab', vertical = 'kc-views-horizontal';
		}	
	#>
	<div class="kc-views-sections kc-views-{{data.name}}{{vertical}}">
		<ul class="kc-views-sections-control kc-controls">
			<li class="right move mtips">
				<i class="sl-cursor-move"></i> {{kc.maps[data.name].name}}
				<span class="mt-mes"><?php _e('Drag and drop to arrange this section', 'kingcomposer'); ?></span>
			</li>
			<li class="more" title="<?php _e('More Actions', 'kingcomposer'); ?>">
				<i class="fa fa-caret-right"></i>
				<div class="mme-more-actions">
					<ul>
						<li class="right edit" title="<?php _e('Edit', 'kingcomposer'); ?>">
							<i class="sl-note"></i>
						</li>
						<li class="double" title="<?php _e('Double', 'kingcomposer'); ?>">
							<i class="sl-docs"></i>
						</li>
						<li class="copy" title="<?php _e('Copy', 'kingcomposer'); ?>">
							<i class="sl-doc"></i>
						</li>
						<li class="cut" title="<?php _e('Cut', 'kingcomposer'); ?>">
							<i class="fa fa-cut"></i> 
						</li>
						<li class="delete" title="<?php _e('Delete', 'kingcomposer'); ?>">
							<i class="fa fa-trash-o"></i>
						</li>
					</ul>
				</div>
			</li>
		</ul>
		<div class="kc-views-sections-wrap">
			<div class="kc-views-sections-label">
				<div class="add-section">
					<i class="sl-plus"></i> <span> <?php _e('Add', 'kingcomposer'); ?> {{kc.maps[sct].name}}</span>
				</div>
			</div>	
		</div>
	</div>
</script>
<script type="text/html" id="tmpl-kc-views-section-template">
	<#
		var icon = '';
		if( data.args.icon != undefined )
			icon = '<i class="'+data.args.icon+'"></i> ';
	#>
	<div class="kc-views-section<# if(data.first==true){ #> kc-section-active<# } #>">
		<h3 class="kc-vertical-label sl-arrow-down">{{{icon}}}{{data.args.title}}</h3>
		<ul class="kc-controls-2 kc-vs-control">
			<li class="right add mtips">
				<i class="sl-plus"></i>
				<span class="mt-mes"><?php _e('Add Elements', 'kingcomposer'); ?></span>
			</li>
			<li class="right double mtips">
				<i class="sl-docs"></i>
				<span class="mt-mes"><?php _e('Double this section', 'kingcomposer'); ?></span>
			</li>
			<li class="right settings mtips">
				<i class="sl-note"></i>
				<span class="mt-mes"><?php _e('Open settings', 'kingcomposer'); ?></span>
			</li>
			<li class="right delete mtips" title="<?php _e('Remove', 'kingcomposer'); ?>">
				<i class="sl-close"></i>
				<span class="mt-mes"><?php _e('Remove this section', 'kingcomposer'); ?></span>
			</li>
		</ul>
		<div class="kc-views-section-wrap kc-column-wrap">
			<div class="kc-element drag-helper">
				<a href="javascript:void(0)" class="kc-add-elements-inner">
					<i class="sl-plus"></i> <?php _e('Add Element', 'kingcomposer'); ?>
				</a>
			</div>
		</div>
	</div>
</script>
<script type="text/html" id="tmpl-kc-element-template">
	 <div class="kc-element {{data.params.name}}<# if(data.map.preview_editable == true){ #> viewEditable<# } #>">
		<div class="kc-element-icon"><span class="cpicon {{data.map.icon}}"></span></div>
		<span class="kc-element-label">{{data.map.name}}</span>
		<div class="kc-element-control" title="<?php _e('Drag to move this element', 'kingcomposer'); ?>">
			<ul class="kc-controls">
				<!--li class="move" title="<?php _e('Move', 'kingcomposer'); ?>">
					<i class="sl-cursor-move"></i>
				</li-->
				<li class="edit mtips" title="">
					<i class="sl-note"></i>
					<span class="mt-mes"><?php _e('Edit this element', 'kingcomposer'); ?></span>
				</li>
				<li class="double mtips" title="">
					<i class="sl-docs"></i>
					<span class="mt-mes"><?php _e('Double this element', 'kingcomposer'); ?></span>
				</li>
				<li class="more" title="">
					<i class="fa fa-caret-right"></i>
					<div class="mme-more-actions">
						<ul>
							<li class="copy" title="<?php _e('Copy this element', 'kingcomposer'); ?>">
								<i class="sl-doc"></i>
							</li>
							<li class="cut" title="<?php _e('Cut this element', 'kingcomposer'); ?>">
								<i class="fa fa-cut"></i>
							</li>
							<li class="delete" title="<?php _e('Delete this element', 'kingcomposer'); ?>">
								<i class="fa fa-trash-o"></i>
							</li>
						</ul>
					</div>
				</li>
			</ul>
		</div>
		<br />
	</div>
</script>
<script type="text/html" id="tmpl-kc-undefined-template">
	 <div class="kc-undefined kc-element {{data.params.name}}">
		<div class="admin-view content">{{data.params.args.content}}</div>
		<div class="kc-element-control">
			<ul class="kc-controls">
				<li class="move" title="<?php _e('Move', 'kingcomposer'); ?>">
					<i class="sl-cursor-move"></i>
				</li>
				<li class="double" title="<?php _e('Double', 'kingcomposer'); ?>">
					<i class="sl-docs"></i>
				</li>
				<li class="edit" title="<?php _e('Edit', 'kingcomposer'); ?>">
					<i class="sl-note"></i>
				</li>
				<li class="delete" title="<?php _e('Delete', 'kingcomposer'); ?>">
					<i class="sl-close"></i>
				</li>
			</ul>
		</div>		
	</div>
</script>
<script type="text/html" id="tmpl-kc-popup-template">
	<div class="kc-params-popup wp-pointer-top {{data.class}}<# if(data.bottom!=0){ #> posbottom<# } #>" style="<# if(data.bottom!=0){ #>bottom:{{data.bottom}}px;top:auto;<# }else{ #>top:{{data.top}}px;<# } #>left:{{data.left}}px;<#
			if( data.width != undefined ){ #>width:{{data.width}}px<# } 
		#>">
		<div class="m-p-wrap wp-pointer-content">
			<h3 class="m-p-header">
				<i data-prevent-drag="true" class="m-p-toggle dashicons dashicons-arrow-down-alt2" title="<?php _e('Collapse popup', 'kingcomposer'); ?>"></i>
				<span data-st="label">{{data.title}}</span>
				<# if( data.help != '' ){ #>
				<a href="{{data.help}}" target="_blank" title="<?php _e('Help', 'kingcomposer'); ?>" class="sl-help sl-func">
					&nbsp;
				</a>
				<# } #>
				<i data-prevent-drag="true" title="<?php _e('Cancel & close popup', 'kingcomposer'); ?>" class="sl-close sl-func"></i>
				<i data-prevent-drag="true" title="<?php _e('Save changes (ctrl+s)', 'kingcomposer'); ?>" class="sl-check sl-func"></i></h3>
			<div class="m-p-body">
				{{{data.content}}}
			</div>
			<# if( data.footer === true ){ #>
			<div class="m-p-footer">
				<ul class="m-p-controls">
					<li>
						<button class="button save button-large">
							<i class="sl-check"></i> {{data.save_text}}
						</button>
					</li>
					<li>
						<button class="button cancel button-large">
							<i class="sl-close"></i> {{data.cancel_text}}
						</button>
					</li>
					<li class="pop-tips">{{{data.footer_ext}}}</li>
				</ul>
			</div>
			<# } #>
			<# if( data.success_mesage !== undefined ){ #>
			<div class="m-p-overlay">{{{data.success_mesage}}}</div>
			<# } #>
		</div>
		<div class="wp-pointer-arrow"<#
				if( data.pos != undefined ){
					var css = '';
					if( data.pos == 'center' ){
						css += 'left:50%;margin-left:-13px;';
					}else if( data.pos == 'right' ){
						css += 'left:auto;right:50px;';
					}
					if( css != '' ){
						#> style="{{css}}"<#
					}
				}
			#>>
			<div class="wp-pointer-arrow-inner"></div>
		</div>
	</div>
</script>
<script type="text/html" id="tmpl-kc-field-template">
	<#
		
		/*
		*	Some of param name is not allowed to use, because it would conflict with the system
		*	So if we will not render this field & display the warning instead
		*/
		
		var iglist = ['css', 'css_data', 'content', '_name', '_id', '_full', '_content', '_base'];
		if( data.name == 'textarea_html' ){
			if( data.base != 'content' )
				data.content = '<p class="kc-notice"><i class="fa-warning"></i> <?php _e('The name of this field must be set to "content"', 'kingcomposer'); ?></p>';
		}else if( iglist.indexOf( data.base ) > -1 ){
			data.content = '<p class="kc-notice"><i class="fa-warning"></i> <?php _e('The name of this field is not allowed to use as', 'kingcomposer'); ?> "'+data.base+'"</p>';
		}
	
		var el_class = ['kc-param-row'];
		
		el_class.push( 'field-'+data.name.replace(/[^0-9a-zA-Z\-\_]/g,'') );
		el_class.push( 'field-base-'+data.base.replace(/\]\[/g,'-').replace( /[^0-9a-zA-Z\-\_]/g, '' ) );
		
		if( data.relation != undefined )
			el_class.push( 'relation-hidden' );
			
	#>
	<div class="{{el_class.join(' ')}}">
		<# if( data.label != undefined && data.label != '' ){ #>
		<div class="m-p-r-label">
			<label>{{{data.label}}}:</label>
		</div>
		<div class="m-p-r-content">
		<# }else{ #>
		<div class="m-p-r-content full-width">
		<# } #>	
			{{{data.content}}}
			<# if( data.des != undefined && data.des != '' ){ #>
				<div class="m-p-r-des">{{{data.des}}}</div>
			<# } #>
		</div>
	</div>
</script>

<script type="text/html" id="tmpl-kc-row-columns-template">
	<div class="kc-row-columns">
		&nbsp; <input type="checkbox" data-name="columnDoubleContent" id="m-r-c-double-content" {{kc.cfg.columnDoubleContent}} /> 
		<?php _e('Double content', 'kingcomposer'); ?> 
		<a href="javascript:alert('<?php _e('Copy content in the last column to the newly-created column. This option is available when you choose to set the column amount greater than the current column amount', 'kingcomposer'); ?>.\n\n<?php _e('For example: Currently there is 1 column and you are going to set 2 columns', 'kingcomposer'); ?>')"> <i class="sl-question"></i> </a> &nbsp; &nbsp; 
		<input type="checkbox" data-name="columnKeepContent" id="m-r-c-keep-content" {{kc.cfg.columnKeepContent}} /> 
		<?php _e('Keep content', 'kingcomposer'); ?> <a href="javascript:alert('<?php _e('Keep content of the removed column and transfer it to the last existing column', 'kingcomposer'); ?>.\n\n<?php _e('This option is available when you choose to set the column amount smaller than the current column amount', 'kingcomposer'); ?>.\n\n<?php _e('For example: Currently there are 2 columns and you are going to set 1 column', 'kingcomposer'); ?>.')"> <i class="sl-question"></i> </a>
		<br /><br />
		<p class="kc-col-btns">
			<button class="button button-large<# if( data.current == 1 ){ #> active<# } #>" data-column="1">
				1 <?php _e('Column', 'kingcomposer'); ?> &nbsp;
			</button>
			<button class="button button-large<# if( data.current == 2 ){ #> active<# } #>" data-column="2">
				2 <?php _e('Columns', 'kingcomposer'); ?> &nbsp;
			</button>
			<button class="button button-large<# if( data.current == 3 ){ #> active<# } #>" data-column="3">
				3 <?php _e('Columns', 'kingcomposer'); ?> &nbsp;
			</button>
		</p>
		<p class="kc-col-btns">
			<button class="button button-large<# if( data.current == 4 ){ #> active<# } #>" data-column="4">
				4 <?php _e('Columns', 'kingcomposer'); ?> &nbsp;
			</button>
			<button class="button button-large<# if( data.current == 5 ){ #> active<# } #>" data-column="5">
				5 <?php _e('Columns', 'kingcomposer'); ?> &nbsp;
			</button>
			<button class="button button-large<# if( data.current == 6 ){ #> active<# } #>" data-column="6">
				6 <?php _e('Columns', 'kingcomposer'); ?> &nbsp;
			</button>
		</p>
		<p class="kc-col-custom">
			<input type="text" placeholder="Example: 15% + 35% + 6/12" />
			<button data-column="custom" class="button button-large">Apply</button>
		</p>
	</div>
</script>

<script type="text/html" id="tmpl-kc-box-design-template">
<#
	if( typeof data == 'object' && data.length > 0 ){
		
		data.forEach( function( item ){
			
	        if( typeof item.attributes != 'object' )
	        	item.attributes = {};
	        if( item.tag == 'a' && item.attributes.href == undefined )
	        	item.attributes.href = '';
	        
	        var classes = '';	
	        if( item.tag == 'icon' || item.tag == 'text' || item.tag == 'image' ){
	        	classes += ' kc-box-elm';
			}else if( item.tag == 'clumn' ){
				var ncl = 'one-one';
				if( item.attributes.class !== undefined ){
					['one-one','one-second','one-third','two-third'].forEach(function(cl){
						if( item.attributes.class.indexOf( cl ) > -1 )
							ncl = cl;
					});
				}
				classes += ' kc-column-'+ncl;
			}
			
			
	        if( item.attributes.cols != undefined )
	        	classes += ' kc-column-'+item.attributes.cols;
	        	
#>
			<div class="kc-box kc-box-{{item.tag}}{{classes}}" data-tag="{{item.tag}}" data-attributes='{{JSON.stringify(item.attributes)}}'>
		        <ul class="mb-header">
			        <li class="mb-toggle" data-action="toggle"><i class="mb-toggle fa-caret-down"></i></li>
			        <li class="mb-tag">{{item.tag}}</li>
			        <# if( item.attributes.id != undefined && item.attributes.id != '' ){ #>
			        	<li class="mb-id">Id: <span>{{item.attributes.id}}</span></li>
			        <# } if( item.attributes.class != undefined && item.attributes.class != '' ){ #>
			        	<li class="mb-class">
			        		Class: <span title="{{item.attributes.class}}">{{item.attributes.class.substr(0,30)}}..</span>
			        	</li>
			        <# } if( item.attributes.href != '' && item.attributes.href != undefined ){ #>
			        	<li class="mb-href">
			        		Href: <span title="{{item.attributes.href}}">{{item.attributes.href.substr(0,30)}}..</span>
			        	</li>
			        <# } #>
			        <li class="mb-funcs">
			        	<ul>
					        <li title="<?php _e('Remove', 'kingcomposer'); ?>" class="mb-remove mb-func" data-action="remove">
					        	<i class="sl-close"></i>
					        </li>
					        <# if( item.tag == 'text' ){ #>
					        <li  title="<?php _e('Edit with Editor', 'kingcomposer'); ?>"class="mb-edit mb-func" data-action="editor">
					        	<i class="sl-pencil"></i>
					        </li>
					        <# }else{ #>
					        <li  title="<?php _e('Settings', 'kingcomposer'); ?>"class="mb-edit mb-func" data-action="settings">
					        	<i class="sl-settings"></i>
					        </li>
					        <# } #>
					        <li title="<?php _e('Double', 'kingcomposer'); ?>" class="mb-double mb-func" data-action="double">
					        	<i class="sl-docs"></i>
					        </li>
					        <# if( item.tag != 'div' ){ #>
					        <li title="<?php _e('Add Node', 'kingcomposer'); ?>" class="mb-add mb-func" data-action="add" data-pos="inner"><i class="sl-plus"></i></li>
					        <# }else{ #>
					        <li title="<?php _e('Columns', 'kingcomposer'); ?>" class="mb-columns mb-func" data-action="columns"><i class="sl-list"></i></li>    
							<# } #>
						</ul>
				    </li>
		        </ul>
		        <div class="kc-box-body"><# 
			        
			        var empcol = false;
			        
		        	if( item.tag == 'div' ){
			        	if( item.children == undefined )
				        		empcol = true;
			        	else if( item.children.length == 0 )
				        		empcol = true;
				        else if( item.children[0].tag != 'column' )
				        	empcol = true;
			        }
			        
			        if( empcol == true ){
				        
				       #>{{{kc.template( 'box-design', [{ tag: 'column', attributes: { cols:'one-one' }, children: item.children }]
				       	)}}}<# 
				        
			        }else{
			        
			        	
				        if( empcol == true ){
					        #><div data-cols="one-one" class="kc-box-column one-one"><#
				        }	


				        if( item.tag == 'text' ){
					        if( item.content == undefined )
					        	item.content = 'Sample Text';
					        #>
								<div class="kc-box-inner-text" contenteditable="true">{{{item.content}}}</div>
						    <#
					    }else if( item.tag == 'image' ){
						    if( item.attributes.src == undefined )
						    	item.attributes.src = kc_plugin_url+'/assets/images/get_start.jpg';
					        #>
								<img data-action="select-image" src="{{item.attributes.src}}" />
						    <#
					    }else if( item.tag == 'icon' ){
						    if( item.attributes.class == undefined )
						    	item.attributes.class = 'fa-leaf';
					        #>
							<span data-action="icon-picker"><i class="{{item.attributes.class}}"></i></span>
						    <#
					    }else{
				        
					       					        	
					        #>{{{kc.template( 'box-design', item.children )}}}<#
				        
				        }
				        
				        #><div class="kc-box mb-helper">
					        <a href="#" data-action="add" data-pos="inner">
						        <i class="sl-plus"></i> 
						        <?php _e('Add Node', 'kingcomposer'); ?>
						    </a>
					    </div>
				    
				    <# }/*EndIf*/ #>
				    
		        </div>
		    </div>
		    
		<#
		
		});
	}	
#>
</script>

<script type="text/html" id="tmpl-kc-param-group-template">

	<div class="kc-group-row">
		<div class="kc-group-controls">
			<ul>
				<li class="collapse" data-action="collapse" title="<?php _e('expand / collapse', 'kingcomposer' ); ?>">
					<i class="sl-arrow-down" data-action="collapse"></i>
				</li>
				<li class="counter"> #1 </li>
				<li class="delete" data-action="delete" title="<?php _e('Delete this group', 'kingcomposer' ); ?>">
					<i data-action="delete" class="sl-close"></i>
				</li>

				<li class="double" data-action="double" title="<?php _e('Double this group', 'kingcomposer' ); ?>">
					<i class="sl-docs" data-action="double"></i>
				</li>			
			</ul>
		</div>
		<div class="kc-group-body"></div>
	</div>

</script>

<script type="text/html" id="tmpl-kc-wp-widgets-element-template">
<ul class="kc-wp-widgets-ul kc-components-list kc-wp-widgets-pop">
	<li data-category="wp_widgets" data-name="kc_wp_sidebar" class="kc-element-item" title="<?php _e('Display WordPress sidebar', 'kingcomposer'); ?>">
		<div>
			<i class="cpicon kc-icon-sidebar"></i>
			<span class="cpdes">
				<strong><?php _e('WordPress SideBar', 'kingcomposer'); ?></strong>
			</span>
		</div>
	</li>
	<#
	kc.widgets.find('>div.widget').each(function(){
		var tit = jQuery(this).find('.widget-title').text(),
			des = jQuery(this).find('.widget-description').html(),
			base = '{"'+jQuery(this).find('input[name="id_base"]').val()+'":{}}';
			
#>	
		<li class="kc-element-item" data-data="{{kc.tools.base64.encode(base)}}" data-category="wp_widgets" data-name="kc_wp_widget" title="{{des}}">
			<div>
				<span class="cpicon kc-icon-wordpress"></span>
				<span class="cpdes">
					<strong>{{tit}}</strong>
					<i>{{des}}</i>
				</span>
			</div>
		</li>
<#	
	});
#>
</ul>
<#
	data.callback = function( wrp, e ){
		wrp.find( 'li' ).on( 'click', e.data.items );
	}
#>
</script>

