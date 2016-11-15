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

global $kc;

?>
<script type="text/html" id="tmpl-kc-container-template">
	<div id="kc-container" <# if( kc.cfg.showTips == 0 ){ #>class="hideTips"<# } #>>
		<div id="kc-controls">
			<button class="button button-large red classic-mode">
				<i class="sl-action-undo"></i> 
				<?php _e('Classic Mode', 'kingcomposer'); ?>
			</button>
			<?php do_action('kc-top-nav'); ?>
			<button class="button button-large alignright collapse mtips">
				<i class="sl-arrow-down"></i> 
				<span class="mt-mes"><?php _e('Expand / Collapse rows', 'kingcomposer'); ?></span>
			</button>
			<button class="button button-large alignright post-settings mtips">
				<i class="sl-settings"></i> 
				<span class="mt-mes"><?php _e('Content Settings', 'kingcomposer'); ?></span>
			</button>
			<button class="button button-large alignright save-page-content mtips">
				<i class="sl-paper-plane"></i> 
				<span class="mt-mes"><?php _e('Save all content to section', 'kingcomposer'); ?></span>
			</button>
			<span class="alignright inss">
				<?php _e('Press Ctrl + S to quick save', 'kingcomposer'); ?>
			</span>
		</div>
		
		<div id="kc-rows">
			<div id="kc-empty-screen">
				<h3><?php _e('You have a blank page', 'kingcomposer'); ?></h3>
				<p><?php _e('Add new element or row/column layout', 'kingcomposer'); ?></p>
			</div>
		</div>
		
		<div id="kc-footers">
			<ul>
				<li class="basic-add">
					<i class="et-expand"></i> 
					<?php _e('Elements', 'kingcomposer'); ?>
					<span class="m-a-tips"><?php _e('Browse all elements', 'kingcomposer'); ?></span>
				</li>
				<li class="one-column quickadd" data-content='[kc_row use_container="yes"][/kc_row]'>
					<span class="grp-column"></span>
					<span class="m-a-tips"><?php _e('Add an 1-column row', 'kingcomposer'); ?></span>
				</li>
				<li class="two-columns quickadd" data-content='[kc_row use_container="yes"][kc_column width="50%"][/kc_column][kc_column width="50%"][/kc_column][/kc_row]'>
					<span class="grp-column"></span>
					<span class="grp-column"></span>
					<span class="m-a-tips"><?php _e('Add a 2-column row', 'kingcomposer'); ?></span>
				</li>
				<li class="three-columns quickadd" data-content='[kc_row use_container="yes"][kc_column width="33.33%"][/kc_column][kc_column width="33.33%"][/kc_column][kc_column width="33.33%"][/kc_column][/kc_row]'>
					<span class="grp-column"></span>
					<span class="grp-column"></span>
					<span class="grp-column"></span>
					<span class="m-a-tips"><?php _e('Add a 3-column row', 'kingcomposer'); ?></span>
				</li>
				<li class="four-columns quickadd" data-content='[kc_row use_container="yes"][kc_column width="25%"][/kc_column][kc_column width="25%"][/kc_column][kc_column width="25%"][/kc_column][kc_column width="25%"][/kc_column][/kc_row]'>
					<span class="grp-column"></span>
					<span class="grp-column"></span>
					<span class="grp-column"></span>
					<span class="grp-column"></span>
					<span class="m-a-tips"><?php _e('Add a 4-column row', 'kingcomposer'); ?></span>
				</li>
				<li class="column-text quickadd" data-content="custom">
					<i class="et-document"></i>
					<span class="m-a-tips"><?php _e('Push customized content and shortcodes', 'kingcomposer'); ?></span>
				</li>
				<li class="title quickadd" data-content='paste'>
					<i class="et-clipboard"></i>
					<span class="m-a-tips"><?php _e('Paste copied element', 'kingcomposer'); ?></span>
				</li>
				<li class="kc-online-presets">
					<i class="et-genius"></i>
					<span class="m-a-tips"><?php _e('Online section presets', 'kingcomposer'); ?></span>
				</li>
				<li class="kc-add-sections">
					<i class="et-layers"></i>
					<span class="m-a-tips"><?php _e('Offline section presets', 'kingcomposer'); ?></span>
				</li>
			</ul>
		</div>
	</div>	
</script>
<script type="text/html" id="tmpl-kc-components-template">
	<div class="kc-components">
		<ul class="kc-components-categories">
			<li data-category="all" class="all active"><?php _e('All', 'kingcomposer'); ?></li>
			<?php
				
				$maps = $kc->get_maps();
				$categories = array();
				$more = array();
				$i=0;
				
				foreach ($maps as $key => $map)
				{
					$category = isset( $map['category'] ) ? $map['category'] : '';
					
					if (!in_array ($category, $categories) && $category != '')
						array_push ($categories, $category);
				}
				
				sort ($categories);
				
				foreach ($categories as $key => $category)
				{
						
					$catz = sanitize_title ($category);
					
					$li = '<li data-category="'.$catz.'" class="'.$catz.'">'.esc_html($category).'</li>';
					
					if ($i < 5)
						echo $li;
					else $more[] = $li;
					
					$i++;
					
				}
				
				if (count( $more ) > 0)
				{
					echo '<li class="more">';
					echo __('More', 'kingcomposer').' <i class="sl-options" aria-hidden="true"></i>';
					
					echo '<ul>';
					
					for ($i = 0; $i < count ($more); $i++)
						echo $more[$i];
						
					echo '</ul>';
					
					echo '</li>';
				}
				
			?>
			<li data-category="kc-wp-widgets" class="kc-wp-widgets mcl-wp-widgets">
				<i class="dashicons dashicons-wordpress" aria-hidden="true"></i> <?php _e('WordPress', 'kingcomposer'); ?>
			</li>
			<li data-category="kc-clipboard" class="kc-clipboard mcl-clipboard">
				<i class="et-layers" aria-hidden="true"></i> <?php _e('Clipboard', 'kingcomposer'); ?>
			</li>
		</ul>
		<ul class="kc-components-list-main kc-components-list">
			<?php
				
				foreach( $maps as $key => $map )
				{
					if( !isset( $map['system_only'] ) )
					{
						$category = isset( $map['category'] ) ? $map['category'] : '';
						$name = isset( $map['name'] ) ? $map['name'] : '';
						$icon = isset( $map['icon'] ) ? $map['icon'] : '';
					?>
						<li <?php
								if( isset( $map['description'] ) && !empty( $map['description'] ) ){
									echo 'title="'.esc_attr( $map['description'] ).'"';
								}
							?> data-category="<?php 
									echo sanitize_title( !empty($category) ? $category : 'uncategoried' ); 
								?>" data-name="<?php 
									echo esc_attr( $key ); 
								?>" class="kc-element-item mcpn-<?php 
									echo sanitize_title( !empty($category) ? $category : 'uncategoried' ); 
								?>">
							<div>
								<i class="cpicon <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
								<span class="cpdes">
									<strong><?php echo esc_html( $name ); ?></strong>
								</span>
								<i class="sl-star preset-open" aria-hidden="true" title="<?php _e('Show presets of this element', 'kingcomposer'); ?>"></i>
							</div>
						</li>
					<?php
					}	
				}	
			?>
		</ul>
	</div>
</script>

<script type="text/html" id="tmpl-kc-wp-widgets-template">
<div id="kc-wp-list-widgets"><?php 
	
	if( !function_exists( 'submit_button' ) ){
		function submit_button( $text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null ) {
			echo kc_get_submit_button( $text, $type, $name, $wrap, $other_attributes );
		}
	}
	
	ob_start();
		@wp_list_widgets();
		$content = str_replace( array( '<script', '</script>' ), array( '&lt;script', '&lt;/script&gt;' ), ob_get_contents() );
	ob_end_clean();
	
	echo $content;
	
?></div>
</script>

<script type="text/html" id="tmpl-kc-sections-template">
<div id="kc-sections"<# if( kc.sections.total > 1 ){ #> class="paged"<# } #> data-cols="{{kc.sections.cols}}">
	<# if( kc.sections.total > 1 ){ #> 
		<div class="kc-section-pagination">
			<ul>
				<li data-action="page" data-page='prev'><?php _e('Previous', 'kingcomposer'); ?></li>
				<#
					for( var i=1; i<=kc.sections.total; i++ ){
						#><li class="<# if( kc.sections.paged == i ){ #>active <# } #>page-{{i}}" data-action="page">{{i}}</li><#
					}
				#>
				<li data-action="page" data-page='next'><?php _e('Next', 'kingcomposer'); ?></li>
			</ul>
		</div>
	<# } #>
	<#
		
		var current_item = data.pop.data('current_item');
		
		for( var i=0; i<kc.sections.items.length; i++ ){
			var item = kc.sections.items[i];
			
			if( !item.preview )
				item.preview = kc_plugin_url+'/assets/images/get_start.jpg';
	#>
	<div class="kc-sections-item<#
		
			if( item.id == current_item ){
			#> selected<#	
			}
			
		#>" data-id="{{item.id}}" data-title="{{item.title}}">
		<div class="kc-section-sceenshot">
			<img src="{{item.preview}}" alt="" />
			<button class="kc-section-include" data-action="link">
				<i class="sl-link" data-action="link"></i> <?php _e('Include', 'kingcomposer'); ?>
			</button>
			<button class="kc-section-clone" data-action="clone">
				<i class="sl-docs" data-action="clone"></i> <?php _e('Clone', 'kingcomposer'); ?>
			</button>
			<button class="kc-section-push" data-action="push">
				<i class="fa-plus" data-action="push"></i> <?php _e('Push In', 'kingcomposer'); ?>
			</button>
			<button class="kc-section-overwrite" data-action="overwrite">
				<i class="fa-refresh" data-action="overwrite"></i> <?php _e('Overwrite', 'kingcomposer'); ?>
			</button>
		</div>
		<div class="kc-section-info">
			<span>{{item.title}}</span>
			<div class="kc-section-funcs">
				<a class="delete-section" href="#delete" data-action="delete">
					<i title="<?php _e('Delete section', 'kingcomposer'); ?>" class="sl-trash delete-section" data-action="delete"></i>
				</a>
				<a class="edit-section" href="<?php echo admin_url('/post.php?action=edit&kc_action=enable_builder&post='); ?>{{item.id}}" target="_blank">
					<i title="<?php _e('Edit section', 'kingcomposer'); ?>" class="sl-pencil edit-section"></i>
				</a>
			</div>
			<i class="taxonomies">{{item.categories.join(', ')}}</i>
		</div>
	</div>
	<#		
		}
	#>
	<# if( kc.sections.total > 1 ){ #> 
	<div class="kc-section-pagination bottom">
		<ul>
			<li data-action="page" data-page='prev'><?php _e('Previous', 'kingcomposer'); ?></li>
			<#
				for( var i=1; i<=kc.sections.total; i++ ){
					#><li class="<# if( kc.sections.paged == i ){ #>active <# } #>page-{{i}}" data-action="page">{{i}}</li><#
				}
			#>
			<li data-action="page" data-page='next'><?php _e('Next', 'kingcomposer'); ?></li>
		</ul>
	</div>
	<# } #>
	<div class="kc-section-control">
		<a href="<?php echo admin_url('/edit.php?post_type='); ?>{{kc.sections.type}}" target=_blank>
			<i class="sl-menu func go-to-list" title="<?php _e('Go to sections list', 'kingcomposer'); ?>"></i>
		</a>
		<a class="kc-add-new-section" href="<?php echo admin_url('/post-new.php?kc_action=enable_builder&post_type='); ?>{{kc.sections.type}}" target=_blank>
			<i class="sl-plus func add-new" title="<?php _e('Add new section', 'kingcomposer'); ?>"></i>
			<span><?php _e('Save to new ', 'kingcomposer'); ?>{{kc.sections.type}}</span>
		</a>
		<i class="sl-reload func reload" title="<?php _e('Reload', 'kingcomposer'); ?>"></i>
		<a href="#items-per-page" class="more-options">
			<i class="sl-options func" title="<?php _e('Display settings', 'kingcomposer'); ?>"></i>
			<div>
				<ul class="items-per-page">
					<li><?php _e('Per page', 'kingcomposer'); ?></li>
					<#
						var actv = 10;
						if( kc.sections.per_page !== undefined && kc.sections.per_page !== '' )
							actv = kc.sections.per_page;
							
						for( i in {10: '', 20: '', 30: '', 50: '', 100: ''} ){
							#><li<# if( actv == i ){ #> class="active"<#} #> data-amount="{{i}}">{{i}}</li><#
						}
					#>
				</ul>
				<ul class="grid-columns">
					<li><?php _e('Columns', 'kingcomposer'); ?></li>
					<#
						var actv = 2;
						if( kc.sections.cols !== undefined && kc.sections.cols !== '' )
							actv = kc.sections.cols;
							
						for( i in {1: '', 2: '', 3: '', 4: '', 5: ''} ){
							#><li<# if( actv == i ){ #> class="active"<#} #> data-amount="{{i}}">{{i}}</li><#
						}
					#>
				</ul>
			</div>
		</a>
		<select class="content-type">
			<option value="">- <?php _e('Content Type', 'kingcomposer'); ?> -</option>
			<?php
				
				$allows_types = $kc->get_support_content_types();
				$ignored_types = $kc->get_ignored_section_content_types();
				
				if( count( $allows_types ) > 0 ){
					foreach( $allows_types as $type ){
						
						if( !in_array( $type, $ignored_types ) ){
							$nice_name = esc_attr( str_replace(array('kc-', '-', '_'), array('KC ', ' ', ' '), $type) );
						
						?><option<#
						
							if( kc.sections.type == '<?php echo esc_attr($type); ?>' ){
								#> selected<#
							}
						
						#> value="<?php echo esc_attr($type); ?>"><?php echo $nice_name; ?></option><?php
						
						}	
					}
				}
			?>
		</select>
		<select class="category">
			<option value="">- <?php _e('All Taxonomies', 'kingcomposer'); ?> -</option>
			<#
				if( kc.sections.terms.length > 0 ){
					for( var i=0; i<kc.sections.terms.length; i++ ){
						#><option <# 
							if(kc.sections.terms[i]['id']+'|'+kc.sections.terms[i]['taxonomy'] == kc.sections.term){
								#>selected <#
							}
						#>value="{{kc.sections.terms[i]['id']}}|{{kc.sections.terms[i]['taxonomy']}}">{{kc.sections.terms[i]['name']}}</option><#
					}
				}
			#>
		</select>
		<input type="search" class="keyword" value="{{kc.sections.s}}" placeholder="<?php _e('Search by Name', 'kingcomposer'); ?>" />
		<i class="sl-magnifier"></i>
	</div>
</div>
<#
	data.callback = kc.ui.sections.render_callback;
#>
</script>

