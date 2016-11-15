(function($){
	
	Forge_Builder = {
	
		content: null,
		
		preview: null,
	
		//Main class for the builder
		data_content_class: false,
		
		status_dragging: false,
		
		margin_dragging: false,
		
		//Indicates the current action performed by the builder
		status: 'ready',
		
		//Instance of the current element
		live_current: '',
		
		//Timeout for preventing live editing
		live_request: false,
		
		//Timeout for preventing live editing
		live_timeout: 0,
		
		//Timeout for preventing live editing
		live_timer: 0,
		
		//Instance of the WP Media popup
		media_image_popup: null,
		
		//Instance of the WP Media popup for galleries
		media_gallery_popup: null,
		
		//Instance of the WP Media popup for galleries
		selected_elements: [],
		
		
		//Starts up the editor
		init: function(){
			//Forge_Builder.init_jquery_fix();
			Forge_Builder.init_error_handling();
			Forge_Builder.init_class_names();
			Forge_Builder.init_wrappers();
			Forge_Builder.init_fields();
			Forge_Builder.init_media_upload();
			Forge_Builder.init_sortable_elements();
			Forge_Builder.init_events();
			Forge_Builder.highlight_empty_blocks();
		},
		
		
		//Prevent errors thrown in jQuery's ready function
		init_jquery_fix: function(){
			jQuery.fn.oldReady = jQuery.fn.ready;
			jQuery.fn.ready = function( fn ) {
				return jQuery.fn.oldReady( function() {
					try {
						if ( 'function' == typeof fn ) {
							fn();
						}
					}
					catch ( e ){
						Forge_Builder.logError(e);
					}
				});
			};
		},
		
		
		//Catch every Javascript error to avoid breaking the builder from third party plugins
		init_error_handling: function(){
			return;
			window.onerror = function(error, source, line, column, message){
				return true;
			};
		},
		
		
		//Lock this post to prevent editing
		// initPostLock: function(){
			// if(typeof wp.heartbeat != 'undefined') {
			
				// wp.heartbeat.interval(30);
				
				// wp.heartbeat.enqueue('forge_builder_post_lock', {
					// post_id: $('#forge-field-post').val()
				// });
			// }
		// },
		
		
		//Init environment variables
		init_class_names: function(){
			$('html').addClass('forge-builder-ui');
			
			//TODO: Fix!
			//Forge_Builder.data_content_class = '.forge-builder-content-' + Forge_BuilderConfig.postId;
			Forge_Builder.data_content_class = '.forge-wrapper';
		},
		
		
		//Initializes fields after loading settings form
		init_fields: function(){
			//Colorpicker
			$('.forge-field-color').each(function(){
				var picker = $(this);
				var preview = $(this).parent().children('.forge-field-color-preview');
				var value = $(this).parent().children('.forge-field-color');
				picker.ForgeColorPicker({
					color: picker.val(),
					onChange: function (hsb, hex, rgb){
						picker.val('#' + hex);
						value.val('#' + hex);
						preview.css('background', '#' + hex);
						value.trigger('change');
					}
				});
				picker.on('change keyup', function(){
					var color_value = picker.val();
					// if(color_value.indexOf('#') == -1 && color_value.length < 7 && color_value != ''){
						// color_value = '#' + color_value;
					// }
					picker.ForgeColorPickerSetColor(color_value);
					picker.val(color_value);
					value.val(color_value);
					preview.css('background', color_value);
				});
			});
			
			
			//System colors on colorpicker
			$('.forge-colorpicker-preset').click(function(){
				var form_container = $(this).closest('.forge-builder-form-field-body');
				var real_value = $(this).attr('data-value');
				form_container.find('.forge-field-color-hex').val($('#forge-' + real_value + '-color').val());
				form_container.find('.forge-field-color-hex').trigger('change');
				form_container.find('.forge-field-color-value').val(real_value);
			});
			
			
			//Checkbox fields
			$('.forge-field-checkbox').click(function(){
				var value = $(this).find('.forge-field-checkbox-value');
				if(value.val() == '1'){
					value.val('0');
					$(this).removeClass('forge-field-checkbox-checked');
				}else{
					value.val('1');
					$(this).addClass('forge-field-checkbox-checked');
				}
			});
			
			//Border fields
			$('.forge-field-border').each(function(){
				var border_value = $(this).find('.forge-field-border-value');
				var border_width = $(this).find('.forge-field-border-width');
				var border_style = $(this).find('.forge-field-border-style');
				var border_color = $(this).find('.forge-field-border-color');
				
				border_width.on('change keyup', function(){
					border_value.val(border_width.val() + 'px ' + border_style.val() + ' ' + border_color.val());
					border_value.trigger('change');
				});
				border_style.on('change', function(){
					border_value.val(border_width.val() + 'px ' + border_style.val() + ' ' + border_color.val());
					border_value.trigger('change');
				});
				border_color.on('change keyup', function(){
					border_value.val(border_width.val() + 'px ' + border_style.val() + ' ' + border_color.val());
					border_value.trigger('change');
				});
			});
			
			
			//Slider fields
			$('.forge-field-slider').each(function(){
				var slider_field = $(this).find('.forge-field-slider-bar');
				var slider_value = $(this).find('.forge-field-slider-value');
				var min_value = slider_field.attr('data-min');
				if(min_value < 0) min_value = 0;
				var max_value = slider_field.attr('data-max');
				var step_value = slider_field.attr('data-step');
				var current_value = slider_value.val();
				
				slider_field.slider({
					orientation: 'horizontal',
					range: 'min',
					min: parseFloat(min_value),
					max: parseFloat(max_value),
					value: parseFloat(current_value),
					step: parseFloat(step_value),
					slide: function(event, ui){ 
						slider_value.val(ui.value);
						slider_value.trigger('change');
					},
				});
				slider_value.on('change', function(){
					slider_field.slider('value', slider_value.val());
				});
			});
		},
		
		
		//Initialize visual editor instance
		init_editor: function(id){
			var visual_editor = tinyMCEPreInit.mceInit['forgebaseeditor'];
			visual_editor['elements'] = id;
			tinyMCEPreInit.mceInit[id] = visual_editor;
		},
		
		
		//Initializes WP Media uploader
		init_media_upload: function(){
			wp.media.model.settings.post.id = $('#forge-field-post').val();
		},
		
		
		//Set parent containers to overflow:visible to avoid breaks
		init_wrappers: function(){
			$(Forge_Builder.data_content_class).parents().css('overflow', 'visible');
			$('body').wrapInner('<div class="forge-body-wrapper"></div>');
			$('.forge-body-wrapper').after($('.forge-builder-collection'));
			$('.forge-body-wrapper').after($('.forge-builder-form'));
			$('.forge-body-wrapper').after($('.forge-builder-toolbar'));
			$('.forge-body-wrapper').after($('.forge-builder-modal-overlay'));
		},
		
		
		//Initialize sortable elements
		init_sortable_elements: function(){
			var sortable_options = {
				appendTo: 'body',
				cursor: 'move',
				cursorAt: { left:60, top:25 },
				distance: 1,
				helper: Forge_Builder.element_drag_helper,
				//sort: Forge_Builder.element_drag_sort,
				placeholder: 'forge-block-placeholder',
				tolerance: 'intersect',
			};
			
			//Drag elements from the builder collection
			$('.forge-builder-collection-list').sortable($.extend({}, sortable_options, {
				connectWith: '.forge-block-content',
				items: '.forge-builder-collection-item',
				distance: 10,
				start: Forge_Builder.collection_drag_start,
				stop: Forge_Builder.collection_drag_stop
			}));
			
			//Change element position in the layout
			$('.forge-wrapper .forge-block-content').sortable($.extend({}, sortable_options, {
				//sort: Forge_Builder.element_drag_sort,
				connectWith: '.forge-block-content',
				handle: '.forge-builder-overlay',
				helper: Forge_Builder.element_active_drag_helper,
				items: '.forge-block:not(.forge-undraggable)',
				distance: 15,
				start : Forge_Builder.element_drag_start,
				stop: Forge_Builder.element_drag_stop,
			}));
		},
		
		
		//Add event listeners to all actions
		init_events: function(){
			
			//General
			$('a').on('click', Forge_Builder.link_clicked);
			$('body').keydown(Forge_Builder.action_keypress);
			$('body').keyup(Forge_Builder.action_keyup);
			
			//Heartbeat API
			// $(document).on('heartbeat-tick', Forge_Builder.lockdown);			
			
			//Collection actions
			$('body').delegate('.forge-builder-collection-open', 'click', Forge_Builder.action_collection_open);
			$('body').delegate('.forge-builder-collection-close', 'click', Forge_Builder.action_collection_close);
			$('body').delegate('.forge-builder-modal-overlay', 'click', Forge_Builder.action_collection_close);
			$('body').delegate('.forge-builder-search', 'click', Forge_Builder.action_collection_open);
			
			//General page actions
			$('body').delegate('.forge-builder-actions-upgrade', 'click', Forge_Builder.action_page_upgrade);
			$('body').delegate('.forge-builder-actions-help', 'click', Forge_Builder.action_page_help);
			$('body').delegate('.forge-builder-actions-settings', 'click', Forge_Builder.action_page_settings);
			$('body').delegate('.forge-builder-actions-import', 'click', Forge_Builder.action_page_import);
			$('body').delegate('.forge-builder-actions-export', 'click', Forge_Builder.action_page_export);
			$('body').delegate('.forge-builder-actions-history', 'click', Forge_Builder.action_page_history);
			$('body').delegate('.forge-builder-actions-save', 'click', Forge_Builder.action_page_save);
			$('body').delegate('.forge-builder-actions-discard', 'click', Forge_Builder.action_page_discard);
			$('body').delegate('.forge-builder-actions-close', 'click', Forge_Builder.action_page_close);
			
			//Column actions
			$('body').delegate('.forge-builder-actions-layout', 'click', Forge_Builder.action_row_layout);
			$('body').delegate('.forge-builder-actions-spacing', 'click', Forge_Builder.action_row_spacing);
			$('body').delegate('.forge-builder-actions-column', 'click', Forge_Builder.action_column_edit);
			
			//Element actions
			$('body').delegate('.forge-builder-actions-edit', 'click', Forge_Builder.action_element_edit);
			$('body').delegate('.forge-builder-actions-copy', 'click', Forge_Builder.action_element_copy);
			$('body').delegate('.forge-builder-actions-delete', 'click', Forge_Builder.action_element_delete);
			$('body').delegate('.forge-builder-actions-action', 'click', Forge_Builder.action_element_action);
			
			//Multiselection
			$('body').delegate('.forge-builder-overlay', 'click', Forge_Builder.action_element_click);
			$('body').delegate('.forge-builder-multiselect-edit', 'click', Forge_Builder.action_selection_edit);
			$('body').delegate('.forge-builder-multiselect-clear', 'click', Forge_Builder.action_selection_clear);
			$('body').delegate('.forge-builder-multiselect-checkbox', 'click', Forge_Builder.action_selection_toggle_field);
			$(window).on('focus', Forge_Builder.action_keyup);
			
			//Settings form actions
			$('body').delegate('.forge-builder-form', 'submit', Forge_Builder.action_settings_save);
			$('body').delegate('.forge-builder-form-cancel', 'click', Forge_Builder.action_settings_cancel);
			$('body').delegate('.forge-builder-form-group-title', 'click', Forge_Builder.action_group_toggle);
			
			//Image Fields
			$('body').delegate('.forge-image-field .forge-image-choose', 'click', Forge_Builder.media_image_choose);
			$('body').delegate('.forge-image-field .forge-image-url', 'click', Forge_Builder.media_image_url);
			$('body').delegate('.forge-image-field .forge-image-remove', 'click', Forge_Builder.media_image_remove);
			$('body').delegate('.forge-image-field .forge-image-save', 'click', Forge_Builder.media_image_save);
			$('body').delegate('.forge-image-field .forge-image-cancel', 'click', Forge_Builder.media_image_cancel);
			
			//Gallery Fields
			$('body').delegate('.forge-gallery-field .forge-gallery-add', 'click', Forge_Builder.media_gallery_choose);
			$('body').delegate('.forge-gallery-field .forge-gallery-remove', 'click', Forge_Builder.media_gallery_remove);
			
			//Editing & Interface
			$('body').delegate('.forge-builder-multiselect-body .forge-builder-form-field-body input', 'change keyup paste', Forge_Builder.field_update);
			$('body').delegate('.forge-builder-multiselect-body .forge-builder-form-field-body select', 'change keyup paste', Forge_Builder.field_update);
			$('body').delegate('.forge-builder-multiselect-body .forge-builder-form-field-body textarea', 'change keyup paste', Forge_Builder.field_update);
			$('body').delegate('.forge-live-field', 'change keyup paste', Forge_Builder.field_live_update);
			$('body').delegate('.forge-builder-dropdown', 'hover', Forge_Builder.field_dropdown_hover);
			$('body').delegate('.forge-builder-dropdown', 'mouseleave', Forge_Builder.field_dropdown_blur);
			
			//Templates
			// $('body').delegate('.forge-builder-actions-layouts', 'click', Forge_Builder.element_template);
			$('body').delegate('.forge-builder-actions-templates', 'click', Forge_Builder.action_page_templates);
		},
		
		
		//Disables clicking on normal links
		link_clicked: function(e){
			if(!$(this).hasClass('forge-link')){
				e.preventDefault();
			}
		},
		
		
		//Helper for dragging items
		element_drag_helper: function (e, item){
			var copy = item.clone();
			item.clone().insertAfter(item);
			copy.addClass('forge-builder-element-helper');
			
			return copy;
		},
		
		
		//Event when sortables change
		element_drag_change: function(ev, ui){
			//$(".forge-block-placeholder").css({"height": "0px"});
			$('.forge-block-placeholder').removeClass('forge-block-placeholder-active');
			setTimeout(function(){
				//$(".forge-block-placeholder").css({"height": "50px"});
				$('.forge-block-placeholder').addClass('forge-block-placeholder-active');
			},10);
		},
		
		
		//Start dragging an element
		element_drag_start: function(e, ui){
			//Check if current element is a slider. If so, destroy first
			var type = ui.item.attr('data-type');
			if(type == 'slider' && $.fn.cycle){
				var element = ui.item.attr('data-element');
				$('.forge-element-slider[data-element=' + element + '] > * > .forge-cycle-slideshow').cycle('destroy');
			}
			
			Forge_Builder.status_dragging();
			Forge_Builder.highlight_empty_blocks();
			$('.forge-wrapper .forge-block-content').sortable('refreshPositions');
		},
		
		
		//Changes the name of the drag helper depending on location
		element_drag_sort: function(e, ui){
			if(typeof ui.placeholder === 'undefined') {
				return;
			}
			
			var parent = ui.placeholder.parent();			
			if(parent.hasClass('forge-block-content')){
				title  = ui.item.attr('data-name');
			}
			
			ui.placeholder.html(title);
		},
		
		
		//Clear the interface
		interface_clear: function(){
			$('.forge-builder-form-container').html('');
			$('.forge-builder-form').removeClass('forge-builder-form-active');
		},
		
		
		//Prepare the form ready for editing
		interface_editing: function(status){	
			$('.forge-builder-form-container').html('');
			$('.forge-builder-form').removeClass('forge-builder-form-ready');
			if(status == true){
				$('.forge-builder-form').addClass('forge-builder-form-active');
			}else{
				$('.forge-builder-form').removeClass('forge-builder-form-active');
			}
		},
		
		
		//The form is ready
		interface_ready: function(status){	
			if(status == true){
				$('.forge-builder-form').addClass('forge-builder-form-ready');
			}else{
				$('.forge-builder-form').removeClass('forge-builder-form-ready');
			}
		},
		
		
		//Highlights empty blocks and resizes them to 
		highlight_empty_blocks: function(){
			Forge_Builder.resize_columns();
			$('.forge-block-content').removeClass('forge-block-highlight');
			$('.forge-block-content').each(function(){
				if($(this).text() == ''){
					$(this).addClass('forge-block-highlight');
				}
			});
			$(window).resize();
		},
		
		
		//Increase height of empty columns to match the tallest in the same row
		resize_columns: function(){
			$('.forge-block-content').css('min-height', 'auto');
			$('.forge-columns').each(function(){
				var max_height = 80;
				$(this).children('.forge-col').each(function(){
					var height = $(this).find('.forge-block-content').first().height();
					max_height = height > max_height ? height : max_height;
				});
				
				$(this).children('.forge-col').each(function(){
					var child = $(this).find('.forge-block-content').first();
					if(child.text() == ''){
						//Needs to be min-height to allow dropping into empty lists
						child.css('min-height', max_height);
					}
				});
			});
		},
		
		
		//Change the element into a small box when dragging
		element_active_drag_helper: function(e, item){   
			return $('<div class="forge-block-drag-helper">' + item.data('title') + '</div>');
		},
		
		
		//Start dragging an element
		collection_drag_start: function(e, ui){
			Forge_Builder.status_dragging();
			//Refresh sortable elements
			$('.forge-builder-collection-list').sortable('refreshPositions');
		},
		
		
		//Stop event for when a collection item is dropped
		collection_drag_stop: function(e, ui){
			var item = ui.item;
			var parent = item.parent();
			var position = 0;
			var element_parent = 0;
			
			Forge_Builder.status_ready();
			
			//Forge_Builder.block_drag_stop(e, ui);
			
			//Drop collection item back into list, or into the layout
			if(parent.hasClass('forge-builder-collection-list')) {
				item.remove();
				return;
			}else if(parent.hasClass('forge-block-content')) {
				// Add the new module.
				element_position = parent.children().index(item);
				element_parent = item.closest('.forge-block').attr('data-element');
				element_type = item.attr('data-type');
				
				if(element_type != 'template'){
					Forge_Builder.element_create(element_type, element_parent, element_position)
				}else{
					element_template = item.attr('data-template');
					Forge_Builder.element_template(element_template, element_parent, element_position)
				}
				// Remove the drag helper.
				ui.item.remove();
			}
		},
		
		
		//Stop event when draggin an existing element
		element_drag_stop: function(e, ui){
			var item = ui.item;
			var parent = item.parent();
			var position = 0;
			var parentId = 0;
			
			Forge_Builder.status_ready();
			
			//Check if current element is a slider. If so, destroy first
			var type = ui.item.attr('data-type');
			if(type == 'slider' && $.fn.cycle){
				var element = ui.item.attr('data-element');
				$('.forge-element-slider[data-element=' + element + '] > * > .forge-cycle-slideshow').cycle();
			}
			
			
			//Drop module in another column
			Forge_Builder.action_selection_clear();
			Forge_Builder.element_move(item);
		},
		
		
		//Save changes to the page
		page_settings: function(){
			Forge_Builder.status_waiting();
			Forge_Builder.request_send({
				action: 'forge_request_settings',
				postid: $('#forge-field-post').val(),
			}, Forge_Builder.page_settings_complete);
		},
		
		
		//Save changes to the page
		page_settings_complete: function(response){
			var data = JSON.parse(response);
			Forge_Builder.status_editing(0);
			Forge_Builder.element_settings_loaded(data);
		},
		
		
		//Save changes to the page
		page_save: function(element_type, element_parent, element_position){
			Forge_Builder.status_waiting();
			$('.forge-builder-actions-save').html(Forge_Builder_Strings.publishing);
			Forge_Builder.request_send({
				action: 'forge_request_save',
				postid: $('#forge-field-post').val(),
			}, Forge_Builder.page_save_complete);
		},
		
		
		//Save changes to the page
		page_save_complete: function(response){
			//Forge_Builder.status_ready();
			$('.forge-builder-actions-save').html(Forge_Builder_Strings.publish_done);
			window.location.replace($('#forge-field-redirect').val());
		},
		
		
		//Save changes to the page
		page_discard: function(){
			Forge_Builder.status_waiting();
			Forge_Builder.request_send({
				action: 'forge_request_discard',
				postid: $('#forge-field-post').val(),
			}, Forge_Builder.page_discard_complete);
		},
		
		
		//Save changes to the page
		page_discard_complete: function(response){
			window.location.replace($('#forge-field-redirect').val());
		},
		
		
		//Close page as-is
		page_close: function(){
			Forge_Builder.status_waiting();
			window.location.replace($('#forge-field-redirect').val());
		},
		
		
		//Create a new element in the layout 
		element_create: function(element_type, element_parent, element_position){
			$('.forge-builder-search').val('');
			$('.forge-builder-search').trigger('keyup');
			
			console.log('Creating element at position ' + element_position);
			
			//Forge_Builder.display_loading();
			Forge_Builder.element_settings_display();
			Forge_Builder.status_waiting();
			Forge_Builder.request_send({
				action: 'forge_request_create_element',
				postid: $('#forge-field-post').val(),
				type: element_type,
				parent: element_parent,
				position: element_position,
			}, Forge_Builder.element_create_complete);
		},
		
		
		//Finish up creating new element and display edit box 
		element_create_complete: function(response){
			Forge_Builder.live_current = '';
			
			//alert(response);
			var data = JSON.parse(response);
			
			element = data.settings.id;
			parent = data.settings.parent;
			position = data.settings.position;
			
			//Display the settings panel of the element
			if(Forge_Builder_Settings.builder_auto_settings == '1'){
				Forge_Builder.element_settings_loaded(data);
				Forge_Builder.status_editing(element);
			}else{
				Forge_Builder.status_ready();
				Forge_Builder.interface_editing(false);
				Forge_Builder.interface_ready(false);
			}
			
			Forge_Builder.element_render(data.layout, element, parent, position);
			Forge_Builder.element_sort_position(parent);
			
			//Refresh sortables
			Forge_Builder.init_sortable_elements();
			Forge_Builder.highlight_empty_blocks();
			//TODO: Add an attribute for new modules created since page load
			//$('.forge-builder-module-settings').data('new-module', '1');
			Forge_Builder.update_history();
			
			//Trigger an update event so dynamic elements can be refreshed
			$(document).trigger('forge-create-' + data.settings.type);
		},
		
		
		//Insert a template element into the layout
		element_template: function(element_template, element_parent, element_position){
			$('.forge-builder-search').val('');
			$('.forge-builder-search').trigger('keyup');
			
			console.log('Inserting template at position ' + element_position);
			
			Forge_Builder.status_waiting();
			Forge_Builder.request_send({
				action: 'forge_request_add_template',
				postid: $('#forge-field-post').val(),
				template: element_template,
				parent: element_parent,
				position: element_position,
			}, Forge_Builder.element_template_complete);
		},
		
		
		//Finish up creating new element and display edit box 
		element_template_complete: function(response){
			var data = JSON.parse(response);
			
			element = data.settings.id;
			parent = data.settings.parent;
			position = data.settings.position;
			Forge_Builder.element_render(data.layout, element, parent, position);
			Forge_Builder.element_sort_position(parent);
			Forge_Builder.status_ready();
			
			//Refresh sortables
			Forge_Builder.init_sortable_elements();
			Forge_Builder.highlight_empty_blocks();
			Forge_Builder.update_history();
		},
		
		
		//Edit existing element in the layout
		element_edit: function(element_id){
			//If element is a list, do multiselect
			if(element_id.constructor === Array){
				element_id = element_id.join();
				Forge_Builder.live_current = '';
			}else{
				//Save element markup for live editing
				Forge_Builder.live_current = $('.forge-element[data-element=' + element_id + ']').html();
			}
			
			Forge_Builder.element_settings_display();
			Forge_Builder.status_waiting();
			Forge_Builder.request_send({
				action: 'forge_request_edit_element',
				postid: $('#forge-field-post').val(),
				element: element_id,
			}, Forge_Builder.element_edit_complete);
			//TODO: Lock interface to ensure no weird things
			Forge_Builder.action_selection_clear();
			Forge_Builder.interface_editing(true);	
		},
		
		
		//Finish up creating new element and display edit box 
		element_edit_complete: function(response){
			var data = JSON.parse(response);
			element = data.settings.id;
			Forge_Builder.status_editing(element);
			Forge_Builder.element_settings_loaded(data);
		},
		
		
		//Save changes of existing element in the layout
		settings_save: function(){
			Forge_Builder.request_send({
				action: 'forge_request_save_form',
				context: $('#forge-field-context').val(),
				element: $('#forge-field-element').val(),
				postid: $('#forge-field-post').val(),
				settings: $('#forge-builder-form').serialize(),
			}, Forge_Builder.settings_save_complete);
			
			//TODO: Lock the form while saving to ensure no weird things
		},
		
		
		//Finish up saving existing element and close edit box
		settings_save_complete: function(response){
			//alert(response);
			Forge_Builder.editor_cleanup();
			Forge_Builder.interface_editing(false);
			var data = JSON.parse(response);
			
			element = data.settings.id;
			parent = data.settings.parent;
			position = data.settings.position;
			Forge_Builder.element_render(data.layout, element, parent, position);
			
			//If element is a slider, refresh
			if(data.settings.type == 'slider' && $.fn.cycle){
				Forge_Builder.highlight_empty_blocks();
				$('.forge-slider[data-element=' + element + '] > * > .forge-cycle-slideshow').cycle();
			}
			if(data.settings.type == 'slide' && $.fn.cycle){
				Forge_Builder.highlight_empty_blocks();
				$('.forge-slider[data-element=' + parent + '] > * > .forge-cycle-slideshow').cycle();
			}
			//TODO: Add an attribute for new modules created since page load
			Forge_Builder.status_ready();
			Forge_Builder.update_history();
			
			//Trigger an update event so dynamic elements can be refreshed
			$(document).trigger('forge-update');
			$(document).trigger('forge-update-' + data.settings.type);
		},
		
		
		//Move element to new location
		element_move: function(element){
			var new_parent = element.parent().closest('.forge-block').data('element');
			var old_parent = element.attr('data-parent');
			var element_id = element.attr('data-element');
			var element_type = element.attr('data-type');
			var position = element.index();
			var old_parent_ids = '';
			var new_parent_ids = '';
			
			//Get all element IDs from the old parent, if changed
			if(new_parent != old_parent){				
				element.parent('.forge-block-content').children('.forge-block').each(function(){
					old_parent_ids += ',' + $(this).data('element');
				});
				Forge_Builder.element_sort_position(old_parent);
			}
			
			//Get all element IDs from the new parent
			element.parent('.forge-block-content').children('.forge-block').each(function(){
				new_parent_ids += ',' + $(this).data('element');
				Forge_Builder.element_sort_position(new_parent);
			});
			
			Forge_Builder.request_send({
				action: 'forge_request_move_element',
				postid: $('#forge-field-post').val(),
				element: element_id,
				new_parent: new_parent,
				old_parent: new_parent,
				old_ordering: old_parent_ids,
				new_ordering: new_parent_ids
			}, function(response){
				Forge_Builder.update_history();
			});
			
			Forge_Builder.highlight_empty_blocks();
			
			//Trigger an update event so dynamic elements can be refreshed
			$(document).trigger('forge-update');
			$(document).trigger('forge-update-' + element_type);
		},
		
		
		//Request element data for editing
		element_copy: function(element){
			Forge_Builder.request_send({
				action: 'forge_request_copy_element',
				postid: $('#forge-field-post').val(),
				element: element.attr('data-element'),
			}, Forge_Builder.element_copy_complete);
		},
		
		
		//Finish up copying new element and display edit box 
		element_copy_complete: function(response){
			//alert(response);
			var data = JSON.parse(response);
			after = data.original;
			parent = data.settings.parent;
			Forge_Builder.element_render_after(data.layout, after);
			Forge_Builder.element_sort_position(parent);
			//TODO: Add an attribute for new modules created since page load
			Forge_Builder.update_history();
		},
		
		
		//Delete an element from the layout
		element_delete: function(element){
			var row = element.closest('.forge-row');
			
			Forge_Builder.request_send({
				action: 'forge_request_delete_element',
				postid: $('#forge-field-post').val(),
				element: element.attr('data-element'),
			}, function(response){
				Forge_Builder.update_history();
			});
			
			element.empty();
			element.remove();
			Forge_Builder.highlight_empty_blocks();
			Forge_Builder.resize_columns();
			Forge_Builder.element_sort_position(element.attr('data-parent'));
		},
		
		
		//Change the layout of a row
		row_layout: function(element_id, element_layout){
			Forge_Builder.status_waiting();
			Forge_Builder.request_send({
				action: 'forge_request_row_layout',
				postid: $('#forge-field-post').val(),
				element: element_id,
				layout: element_layout,
			}, Forge_Builder.row_layout_complete);
		},
		
		//Change the layout of a row
		row_spacing: function(element_id, element_spacing){
			Forge_Builder.status_waiting();
			Forge_Builder.request_send({
				action: 'forge_request_row_spacing',
				postid: $('#forge-field-post').val(),
				element: element_id,
				layout: element_spacing,
			}, Forge_Builder.row_layout_complete);
		},
		
		
		//Finish up creating new element and display edit box 
		row_layout_complete: function(response){
			//alert(response);
			var data = JSON.parse(response);
			
			element = data.settings.id;
			parent = data.settings.parent;
			position = data.settings.position;
			Forge_Builder.element_render(data.layout, element, parent, position);
			
			//Refresh sortables
			Forge_Builder.init_sortable_elements();
			Forge_Builder.highlight_empty_blocks();
			Forge_Builder.status_ready();
			Forge_Builder.update_history();
		},
		
		
		//Render given element into the layout
		element_render: function(html, element, parent, position){
			//Update existing element
			if($(".forge-block[data-element=" + element + "]").length){
				$(".forge-block[data-element=" + element + "]").replaceWith(Forge_Builder.scripts_remove_duplicates(html));
			}else{
				console.log('Rendering element at position ' + position);
				//var container = $(".forge-block[data-element=" + parent + "] > div > .forge-block-content");
				var container = $(".forge-block[data-element=" + parent + "]").find(".forge-block-content").first();
				//Insert new element
				if(position == 0){
					container.prepend(Forge_Builder.scripts_remove_duplicates(html));
				}else{
					container.children(".forge-block:nth-child(" + position + ")").after(Forge_Builder.scripts_remove_duplicates(html));
				}
			}
			Forge_Builder.highlight_empty_blocks();
			Forge_Builder.init_sortable_elements();
		},
		
		
		//Render element right after a given element
		element_render_after: function(html, after){
			//Insert new element
			$(".forge-block[data-element=" + after + "]").after(Forge_Builder.scripts_remove_duplicates(html));
			Forge_Builder.highlight_empty_blocks();
			Forge_Builder.init_sortable_elements();
		},
		
		
		//Sort element positions within a given parent
		element_sort_position: function(parent){
			var container = $(".forge-block[data-element=" + parent + "]").find('.forge-block-content').first();
			var count = 0;
			container.children(".forge-block").each(function(){
				$(this).attr('data-position', count);
				count++;
			})
		},
		
		
		//Click on open button in collection
		action_collection_open: function(e){
			Forge_Builder.status_browsing();
			$('.forge-builder-search').focus();
		},
		
		
		//Click on close button in collection
		action_collection_close: function(e){
			if(Forge_Builder.status == 'browsing'){
				Forge_Builder.status_ready();
				$('.forge-builder-search').val('');
				$('.forge-builder-search').trigger('keyup');
			}
		},
		
		
		//Detect keypresses for the collection
		action_keypress: function(e){
			//Actions when builder is ready
			if(Forge_Builder.status == 'ready'){
				//Add class while holding Ctrl
				if(e.ctrlKey){
					$('body').addClass('forge-builder-status-multiselection');
				}
				
				//Ctrl + Z to undo
				if(e.keyCode == 90 && e.ctrlKey){
					Forge_Builder.action_page_undo();
				
				//Ctrl + Y to redo
				}else if(e.keyCode == 89 && e.ctrlKey){
					Forge_Builder.action_page_redo();
				
				//Open collection if typing a-z, only if status is ready
				}else if(e.keyCode >= 65 && e.keyCode <= 90){
					Forge_Builder.action_collection_open();
				}
				
				//Clear selection on Escape
				if(e.keyCode == 27){
					Forge_Builder.action_selection_clear();
				}
			
			//Actions when the collection is open
			}else if(Forge_Builder.status == 'browsing'){
				//Close collection on Escape
				if(e.keyCode == 27){
					Forge_Builder.action_collection_close();
				}
			
			//Actions when editing a panel
			}else if(Forge_Builder.status == 'editing'){
				//Close collection on Escape, only if collection is open
				if(e.keyCode == 27){
					Forge_Builder.action_settings_cancel();
				}
			}
		},
		
		
		//Detect key release
		action_keyup: function(e){
			if(Forge_Builder.status == 'ready'){
				if(!e.ctrlKey){
					$('body').removeClass('forge-builder-status-multiselection');
				}
			}
		},
		
		
		//Click on Update button in the form
		action_page_upgrade: function(e){
			e.preventDefault();
			window.open('http://forgeplugin.com/extension/bundle?utm_source=upsell&utm_medium=plugin&utm_campaign=Forge%20Upgrade%20Button', '_blank');
			e.stopPropagation();
		},
		
		
		//Click on Update button in the form
		action_page_help: function(e){
			e.preventDefault();
			window.open('http://forgeplugin.com/documentation?utm_source=link&utm_medium=plugin&utm_campaign=Forge%20Help%20Button', '_blank');
			e.stopPropagation();
		},
		
		
		//Click on Settings button in toolbar
		action_page_settings: function(e){
			var element = $('.forge-wrapper');
			Forge_Builder.element_edit('0');
			e.stopPropagation();
		},
		
		
		//Click on Import button in toolbar
		action_page_import: function(e){
			Forge_Builder.element_settings_display();
			Forge_Builder.status_editing();
			Forge_Builder.request_send({
				action: 'forge_request_import',
				postid: $('#forge-field-post').val(),
			}, Forge_Builder.element_edit_complete);
			Forge_Builder.interface_editing(true);
			e.stopPropagation();
		},
		
		
		//Click on Export button in toolbar
		action_page_export: function(e){
			Forge_Builder.element_settings_display();
			Forge_Builder.status_editing();
			Forge_Builder.request_send({
				action: 'forge_request_export',
				postid: $('#forge-field-post').val(),
			}, Forge_Builder.element_edit_complete);
			Forge_Builder.interface_editing(true);
			e.stopPropagation();
		},
		
		
		//Click on Undo button in toolbar, or press Ctrl + Z
		action_page_undo: function(e){
			if(!$('#forge-builder-history').hasClass('forge-builder-history-updating') && Forge_Builder.status == 'ready'){
				var history_id = false;
				var current_history = $('#forge-builder-history .forge-builder-history-current');
				if(current_history.length){
					if(!current_history.is(':last-child')){
						var target_history = current_history.next();
						history_id = target_history.attr('data-history');
					}
				}else{
					var target_history = $('#forge-builder-history .forge-builder-actions-history').first();
					history_id = target_history.attr('data-history');
				}
				
				if(history_id != false){
					//Remove action-triggering class and set current history
					current_history.addClass('forge-builder-actions-history');
					current_history.removeClass('forge-builder-history-current');
					target_history.removeClass('forge-builder-actions-history');
					target_history.addClass('forge-builder-history-current');
					Forge_Builder.history_change(history_id);
				}
			}
		},
		
		
		//Press Ctrl + Y
		action_page_redo: function(e){
			if(!$('#forge-builder-history').hasClass('forge-builder-history-updating') && Forge_Builder.status == 'ready'){
				var history_id = false;
				var current_history = $('#forge-builder-history .forge-builder-history-current');
				if(current_history.length){
					if(!current_history.is(':first-child')){
						var target_history = current_history.prev();
						history_id = target_history.attr('data-history');				
					}
				}
					
				if(history_id != false){
					//Remove action-triggering class and set current history
					current_history.addClass('forge-builder-actions-history');
					current_history.removeClass('forge-builder-history-current');
					target_history.removeClass('forge-builder-actions-history');
					target_history.addClass('forge-builder-history-current');
					Forge_Builder.history_change(history_id);
				}
			}
		},
		
		
		//Click on History button in toolbar
		action_page_history: function(e){
			var history_id = $(this).attr('data-history');
			
			$('.forge-builder-history-current').addClass('forge-builder-actions-history');
			$('.forge-builder-history-current').removeClass('forge-builder-history-current');
			
			$(this).removeClass('forge-builder-actions-history');
			$(this).addClass('forge-builder-history-current');
			
			Forge_Builder.history_change(history_id);
			e.stopPropagation();
		},
		
		
		//Click on History button in toolbar
		history_change: function(history_id){
			Forge_Builder.status_waiting();
			Forge_Builder.request_send({
				action: 'forge_request_history',
				history: history_id,
				postid: $('#forge-field-post').val(),
			}, function(response){
				var data = JSON.parse(response);
				element = data.settings.id;
				parent = data.settings.parent;
				position = data.settings.position;
				Forge_Builder.element_render(data.layout, element, parent, position);
				Forge_Builder.status_ready();
			});
		},
		
		
		//Click on Update button in the form
		action_page_save: function(e){
			e.preventDefault();
			Forge_Builder.page_save();
			e.stopPropagation();
		},
		
		
		//Click on Discard button in the form
		action_page_discard: function(e){
			e.preventDefault();
			var result = confirm(Forge_Builder_Strings.discard_confirm);
			if(result) {
				Forge_Builder.page_discard();
			}
			e.stopPropagation();
		},
		
		
		//Click on Exit button in the form
		action_page_close: function(e){
			e.preventDefault();
			var result = confirm(Forge_Builder_Strings.close_confirm);
			if(result) {
				Forge_Builder.page_close();
			}
			e.stopPropagation();
		},
		
		
		//Click on Edit button in an element
		action_element_edit: function(e){
			var element_id = $(this).closest('.forge-block').attr('data-element');
			
			Forge_Builder.element_edit(element_id);
			//Set interface ready for editing
			//Forge_Builder.interface_clear();
			e.stopPropagation();
		},
		
		
		//Click on Save button in the form
		action_settings_save: function(e){
			e.preventDefault();
			Forge_Builder.settings_save();
			//Set interface ready for editing
			//Forge_Builder.interface_clear();
			e.stopPropagation();
		},
		
		
		//Click on Copy button in an element
		action_element_copy: function(e){
			var element = $(this).closest('.forge-block');
			Forge_Builder.action_selection_clear();
			Forge_Builder.element_copy(element);
			e.stopPropagation();
		},
		
		
		//Click on delete button for an element
		action_element_delete: function(e){
			var element = $(this).closest('.forge-block');
			//Display the settings panel of the element
			var result = true;
			if(Forge_Builder_Settings.builder_quick_delete != '1'){
				var result = confirm(Forge_Builder_Strings.delete_confirm);
			}
			
			if(result) {
				Forge_Builder.action_selection_clear();
				Forge_Builder.element_delete(element);
				//Forge_Builder._removeAllOverlays();
			}
			
			e.stopPropagation();
		},
		
		
		//Click on a Quick Action button
		action_element_action: function(e){
			var element_id = $(this).closest('.forge-block').data('element');
			var element_action = $(this).data('action');
			Forge_Builder.status_waiting();
			Forge_Builder.request_send({
				action: 'forge_request_element_action',
				postid: $('#forge-field-post').val(),
				element: element_id,
				action: element_action,
			}, Forge_Builder.row_layout_complete);
			e.stopPropagation();
		},
		
		
		//Click on an overlay for special actions
		action_element_click: function(e){
			//Actions when builder is ready
			if(Forge_Builder.status == 'ready'){
				//Ctrl + Click to add to selection
				if(e.ctrlKey){
					var element = $(this).closest('.forge-block');
					Forge_Builder.action_selection_toggle(element);
				}
			}
			e.stopPropagation();
		},
		
		
		//Add element to selection
		action_selection_toggle: function(element){
			var element_id = element.attr('data-element');
			
			if($.inArray(element_id, Forge_Builder.selected_elements) == -1){
				//If not in the selection add it.
				Forge_Builder.selected_elements.push(element_id);
				element.addClass('forge-builder-overlay-selected');
			}else{
				//Otherwise, remove from selection
				var index = Forge_Builder.selected_elements.indexOf(element_id);
				Forge_Builder.selected_elements.splice(index, 1);
				element.removeClass('forge-builder-overlay-selected');
			}
			
			//If array is not empty, add multiselect interface
			if(Forge_Builder.selected_elements.length > 0){
				$('body').addClass('forge-builder-multiselect-active');
				$('.forge-builder-multiselect-edit span').html(Forge_Builder.selected_elements.length);
			}else{
				$('body').removeClass('forge-builder-multiselect-active');
			}
			
		},
		
		
		//Add element to selection
		action_selection_clear: function(){
			Forge_Builder.selected_elements = [];
			$('body').removeClass('forge-builder-multiselect-active');
			$('.forge-block').removeClass('forge-builder-overlay-selected');
		},
		
		
		//Click on Edit button in an element
		action_selection_edit: function(e){
			if(Forge_Builder.selected_elements.length > 0){
				Forge_Builder.element_edit(Forge_Builder.selected_elements);
				e.stopPropagation();
			}
		},
		
		//Add element to selection
		action_selection_toggle_field: function(e){
			if($(this).is(':checked')){
				$(this).closest('.forge-builder-form-field').addClass('forge-builder-form-field-multiselected');
			}else{
				$(this).closest('.forge-builder-form-field').removeClass('forge-builder-form-field-multiselected');
			}
			e.stopPropagation();
		},
		
		
		//Click on cancel button
		action_settings_cancel: function(){
			
			//Restore element
			var element_id = $('#forge-field-element').val();
			if(Forge_Builder.live_current != '' && element_id != ''){
				$('.forge-element[data-element=' + element_id + ']').html(Forge_Builder.live_current);
			}
			
			Forge_Builder.editor_cleanup();
			Forge_Builder.interface_editing(false);
			Forge_Builder.interface_ready(false);
			Forge_Builder.status_ready();
		},
		
		
		//Click on title of a settings group
		action_group_toggle: function(){
			$(this).closest('.forge-builder-form-group').toggleClass('forge-builder-form-group-open');
		},
		
		
		//Click on Columns button in an element -- edits parent column
		action_column_edit: function(e){
			var element = $(this).closest('.forge-col');
			
			Forge_Builder.element_edit(element);
			e.stopPropagation();
		},
		
		
		//Click on Layout button in a row -- change number of columns
		action_row_layout: function(e){
			var element = $(this).closest('.forge-row').data('element');
			var columns = $(this).data('layout');
			Forge_Builder.row_layout(element, columns);
			e.stopPropagation();
		},
		
		
		//Click on Layout button in a row -- change number of columns
		action_row_spacing: function(e){
			var element = $(this).closest('.forge-row');
			var spacing = $(this).data('spacing');
			Forge_Builder.row_layout(element, spacing);
			e.stopPropagation();
		},
		
		
		//Refresh history dropdown, after doing something
		update_history: function(e){
			$('#forge-builder-history').addClass('forge-builder-history-updating');
			Forge_Builder.request_send({
				action: 'forge_request_update_history',
				postid: $('#forge-field-post').val(),
			}, function(response){
				$('#forge-builder-history').replaceWith(response);
			});
		},
		
		
		//Displays the edit form once they are loaded
		element_settings_loaded: function(data){
			//TODO: Adding previews and handling live updates
			var content = typeof data == 'string' ? data : data.form;
			var settings = typeof data == 'string' ? data : data.settings;
			
			$('#forge-builder-form-container').html(content);
			
			//Expand form if needed
			//TODO: Add a formal expanded field in settings data
			if($('#forge-builder-form').find('.forge-editor').length){
				$('#forge-builder-form').addClass('forge-builder-form-large');
			}
			
			$('.forge-builder-form-container input, .forge-builder-form-container textarea').first().focus();
			Forge_Builder.interface_ready(true);
			Forge_Builder.init_fields();
		},
		
		
		//Select an image using the WordPress media popup
		media_image_choose: function(){
			if(Forge_Builder.media_image_popup === null){
				Forge_Builder.media_image_popup = wp.media({
					title: Forge_Builder_Strings.select_image,
					button: { text: Forge_Builder_Strings.select_image },
					library : { type : 'image' },
					multiple: false
				});
			}
			
			Forge_Builder.media_image_popup.once('open', $.proxy(Forge_Builder.media_single_popup_opened, this));
			Forge_Builder.media_image_popup.once('select', $.proxy(Forge_Builder.media_single_popup_selected, this));
			Forge_Builder.media_image_popup.open();
		},
		
		
		//Callback for when the single image selector is shown.
		media_single_popup_opened: function(){
			var selection = Forge_Builder.media_image_popup.state().get('selection');
			//Get field elements
			var image_field_wrapper = $(this).closest('.forge-image-field');
			var image_field = image_field_wrapper.find('.forge-image-input-value');
			var image_id = image_field.val();
			var attachment = null;
			
			
			selection.reset();
			/*
			if($(this).hasClass('forge-image-replace')) {
				//image_field_wrapper.addClass('forge-image-empty');
				//image_field.val('');
			}else if(image_id != '') {           
				attachment = wp.media.attachment(image_id);
				attachment.fetch();
				selection.add(attachment ? [attachment] : []);
			}else{
				selection.reset();
			}*/
		},
		
		
		//Selected an image from the WP Media Popup
		media_single_popup_selected: function(){
			var image = Forge_Builder.media_image_popup.state().get('selection').first().toJSON();
			//Get field elements
			var image_field_wrapper = $(this).closest('.forge-image-field');
			var image_field = image_field_wrapper.find('.forge-image-input-value');
			var image_preview = image_field_wrapper.find('.forge-image-preview');
			//var srcSelect = wrap.find('select');
			
			//Assign image ID to field
			image_field.val(image.id);
			image_field.trigger('change');
			image_preview.html('<img src="' + image.url + '"/>');
			image_field_wrapper.find('.forge-image-input-url').val('');
			image_field_wrapper.removeClass('forge-image-field-empty');
		},
		
		
		//Clear an image field
		media_image_remove: function(){
			//Get field elements
			var image_field_wrapper = $(this).closest('.forge-image-field');
			var image_field = image_field_wrapper.find('.forge-image-input-value');
			var image_preview = image_field_wrapper.find('.forge-image-preview');
			
			//Assign image ID to field
			image_field.val('');
			image_preview.html('');
			image_field_wrapper.addClass('forge-image-field-empty');
			image_field.trigger('change');
		},
		
		
		//Select a custom URL for the image field
		media_image_url: function(){
			var image_field_wrapper = $(this).closest('.forge-image-field');
			image_field_wrapper.addClass('forge-image-field-custom-url');
			//image_field_wrapper.find('.forge-image-input-url').val('');
		},
		
		
		//Save custom URL for the image field
		media_image_save: function(){
			//Get field elements
			var image_field_wrapper = $(this).closest('.forge-image-field');
			image_field_wrapper.addClass('forge-image-field-custom-url');
			
			var image_value = image_field_wrapper.find('.forge-image-input-url').val();
			var image_field = image_field_wrapper.find('.forge-image-input-value');
			var image_preview = image_field_wrapper.find('.forge-image-preview');
			
			//Assign image ID to field
			image_field.val(image_value);
			image_field.trigger('change');
			image_preview.html('<img src="' + image_value + '"/>');
			image_field_wrapper.removeClass('forge-image-field-empty');
			image_field_wrapper.removeClass('forge-image-field-custom-url');
		},
		
		
		//Cancel custom URL for the image field
		media_image_cancel: function(){
			$(this).closest('.forge-image-field').removeClass('forge-image-field-custom-url');
		},
		
		
		//Select an image using the WordPress media popup
		media_gallery_choose: function(){
			if(Forge_Builder.media_gallery_popup === null){
				Forge_Builder.media_gallery_popup = wp.media({
					title: Forge_Builder_Strings.select_image,
					button: { text: Forge_Builder_Strings.select_image },
					library : { type : 'image' },
					multiple: true
				});
			}
			
			Forge_Builder.media_gallery_popup.once('select', $.proxy(Forge_Builder.media_gallery_popup_selected, this));
			Forge_Builder.media_gallery_popup.open();
		},
		
		
		//Selected an image from the WP Media Popup
		media_gallery_popup_selected: function(){
			//Get field elements
			var images = Forge_Builder.media_gallery_popup.state().get('selection');
			var image_field_wrapper = $(this).closest('.forge-gallery-field');
			var image_field = image_field_wrapper.find('input');
			var image_field_value = image_field.val();
			var gallery_images = image_field_wrapper.find('.forge-gallery-images');
			
			var image_ids = '';
			images.map(function(current_image){
				current_image = current_image.toJSON();
				image_ids = current_image.id + "," + image_ids;
				var image_url = current_image.sizes.thumbnail ? current_image.sizes.thumbnail.url : current_image.url;
				gallery_images.prepend('<div class="forge-gallery-image" data-image="' + current_image.id + '"><img src="' + image_url + '"/><span class="forge-gallery-remove"></span></div>');
			});
			image_field.val(image_field_value + image_ids);
			image_field.trigger('change');
		},
		
		
		//Clear a single image from the gallery
		media_gallery_remove: function(){
			var gallery_list = $(this).closest('.forge-gallery-field');
			var gallery_image = $(this).closest('.forge-gallery-image');
			var gallery_image_id = gallery_image.attr('data-image');
			var gallery_field = gallery_list.find('input');
			
			//Assign image ID to field
			gallery_image.remove();
			
			//Reconstruct IDs
			var image_ids = '';
			gallery_list.find('.forge-gallery-image').each(function(){
				image_ids = image_ids + jQuery(this).attr('data-image') + ',';
			});
			gallery_field.val(image_ids);
			gallery_field.trigger('change');
		},
		
		
		//Returns URL for a single image, based on ID and size
		get_image_url: function(photo){
			if(typeof photo.sizes === 'undefined') {
				return photo.url;
			}
			else if(typeof photo.sizes.thumbnail !== 'undefined') {
				return photo.sizes.thumbnail.url;
			}
			else {
				return photo.sizes.full.url;
			}
		},
		
		
		//Destroy the tinymce editor to prevent errors
		editor_cleanup: function(data, callback){
			$('#forge-builder-form').removeClass('forge-builder-form-large');
			$('.forge-colorpicker').hide();
			$('.forge-builder-form-container').find('textarea').each(function(){
				var id = $(this).attr('id');
				tinymce.remove('#' + id);
			});
		},

		
		//Send AJAX request with a response
		request_send: function(data, callback){
			return $.post(Forge_Builder.request_url(), data, function(response){
				if(typeof callback !== 'undefined') {
					callback.call(this, response);
				}
			});
		},

		
		//Callback for when an AJAX request is complete. Runs a queued AJAX request if a silent update was in progress
		request_complete: function(){
			var data, callback;
			
			//Set the silent update flag to false so other ajax requests can run.
			Forge_Builder._silentUpdate = false;
			
			//Do an ajax request that was stopped by a silent ajax request.
			if(Forge_Builder._silentUpdateCallbackData !== null) {
				//Forge_Builder.display_loading();
				data = Forge_Builder._silentUpdateCallbackData[0];
				callback = Forge_Builder._silentUpdateCallbackData[1];
				Forge_Builder._silentUpdateCallbackData = null;
				Forge_Builder.request_send(data, callback);
			}else{
				// We're done, hide the loader incase it's showing.
				//Forge_Builder.hide_loading();
			}
		},

		
		//Returns a URL for an AJAX request.
		request_url: function(params){
			return Forge_Builder_Settings.request_url;
		},

		
		//Display editing form
		element_settings_display: function(draggable){
			$('.forge-builder-form').addClass('forge-builder-form-active');
		},
		
		
		//Set status to ready
		status_clear: function(){
			$('.forge-block').removeClass('forge-block-editing');
			$('body').removeClass('forge-builder-status-ready');
			$('body').removeClass('forge-builder-status-browsing');
			$('body').removeClass('forge-builder-status-waiting');
			$('body').removeClass('forge-builder-status-dragging');
			$('body').removeClass('forge-builder-status-editing');
		},
		
		
		//Set status to ready
		status_ready: function(){
			Forge_Builder.status_clear();
			$('body').addClass('forge-builder-status-ready');
			Forge_Builder.status = 'ready';
		},
		
		
		//Set status to waiting
		status_waiting: function(){
			Forge_Builder.status_clear();
			$('body').addClass('forge-builder-status-waiting');
			Forge_Builder.status = 'waiting';
		},
		
		
		//Set status to browsing collection
		status_browsing: function(){
			Forge_Builder.status_clear();
			$('body').addClass('forge-builder-status-browsing');
			Forge_Builder.status = 'browsing';
		},
		
		
		//Set status to dragging
		status_dragging: function(){
			Forge_Builder.status_clear();
			$('body').addClass('forge-builder-status-dragging');
			Forge_Builder.status = 'dragging';
		},
		
		
		//Set status to editing
		status_editing: function(element_id){
			Forge_Builder.status_clear();
			$('body').addClass('forge-builder-status-editing');
			if(element_id != ''){
				$('.forge-block[data-element=' + element_id + ']').addClass('forge-block-editing');
			}
			Forge_Builder.status = 'editing';
		},
		
		
		//Remove duplicate JS scripts
		scripts_remove_duplicates: function(assets){
			var cleaned = $('<div id="forge-duplicate-scripts">' + assets + '</div>')
			var src = '';
			var script = null;
			
			cleaned.find('script').each(function(){
				//Get source of current script and look for the same file being loaded
				src = $(this).attr('src');
				script = $('script[src="' + src + '"]');
				
				//If not empty, remove it
				if(script.length > 0){
					$(this).remove();
				}
			});
			
			return cleaned.html();
		},
		
		
		//Hover on a overlay dropdown
		field_dropdown_hover: function(object){
			$(this).closest('.forge-block').addClass('forge-block-dropdown-open');
		},
		
		
		//Hover on a overlay dropdown
		field_dropdown_blur: function(object){
			$(this).closest('.forge-block').removeClass('forge-block-dropdown-open');
		},
		
		
		//Activate checkbox when using multiselect and modifying fields
		field_update: function(object){
			if(Forge_Builder.status == 'editing'){
				var form_field = $(this).closest('.forge-builder-form-field');
				form_field.addClass('forge-builder-form-field-multiselected');
				form_field.find('.forge-builder-multiselect-checkbox').prop('checked', true);
			}
		},	
		
		
		//Apply live changes
		field_live_update: function(object){
			if(Forge_Builder.status == 'editing' && Forge_Builder.live_current != ''){
				var element_id = $('#forge-field-element').val();
				var element_object = $('.forge-block[data-element=' + element_id + ']');
				var element_parent = element_object.attr('data-parent');
				var element_position = element_object.attr('data-position');
				var element_type = element_object.attr('data-type');
				
				var form_field = $(this).closest('.forge-builder-form-field');
				
				//Get data about the field
				var live = form_field.attr('data-live');
				var selector = form_field.attr('data-live-selector');
				var property = form_field.attr('data-live-property');
				var format = form_field.attr('data-live-format');
				var value = $(this).val();
				if(format != ''){
					value = format.replace('%VALUE%', value);
				}
				if(selector != '' && property != ''){
					
					//Create selector
					//TODO: Stop at current object for hierarchical elements
					var full_selector = '.forge-block[data-element=' + element_id + '] ' + selector;
					//console.log(full_selector + ' ' + property + ' ' + value);
					
					//Simple text change
					if(property == 'html'){
						$(full_selector).html(value);
					}
					
					//CSS Class switching - Add selected class and remove others
					if(property == 'class'){
						var choices = form_field.attr('data-live-choices');
						$(full_selector).removeClass(choices);
						$(full_selector).addClass(value);
					}
					
					//CSS attribute changing
					if(property == 'css'){
						var attribute = form_field.attr('data-live-attribute');
						if(attribute != ''){
							$(full_selector).css(attribute, value);
						}
					}
				}else if(live != ''){
					//Full refresh by AJAX. Not optimal, but better than static content
					var timeout = 0;
					if(Forge_Builder.live_request == true){
						timeout = 400;
					}
					Forge_Builder.live_request = true;
					
					//Send query
					clearTimeout(Forge_Builder.live_timeout);
					Forge_Builder.live_timeout = setTimeout(function(){
						Forge_Builder.field_live_timeout();
						Forge_Builder.request_send({
							action: 'forge_request_refresh',
							element: element_id,
							postid: $('#forge-field-post').val(),
							settings: $('#forge-builder-form').serialize(),
						}, function(response){
							var data = JSON.parse(response);
							Forge_Builder.element_render(data.layout, element_id, element_parent, element_position);
							Forge_Builder.status_editing(element_id);
							$(document).trigger('forge-refresh-' + element_type);
						});	
					}, timeout);
					
					Forge_Builder.field_live_timeout();
				}
			}
		},
		
		
		//Add a slower timeout on immediate subsequent requests
		field_live_timeout: function(){
			clearTimeout(Forge_Builder.live_timer);
			Forge_Builder.live_timer = setTimeout(function(){
				Forge_Builder.live_request = false;
			}, 400);
		},
		
		
		//Insert a template element into the layout
		element_template: function(element_template, element_parent, element_position){
			$('.forge-builder-search').val('');
			$('.forge-builder-search').trigger('keyup');
			
			console.log('Inserting template at position ' + element_position);
			
			Forge_Builder.status_waiting();
			Forge_Builder.request_send({
				action: 'forge_request_insert_template',
				postid: $('#forge-field-post').val(),
				template: element_template,
				parent: element_parent,
				position: element_position,
			}, Forge_Builder.element_template_complete);
		},
		
		
		//Finish up creating new element and display edit box 
		element_template_complete: function(response){
			var data = JSON.parse(response);
			
			element = data.settings.id;
			parent = data.settings.parent;
			position = data.settings.position;
			Forge_Builder.element_render(data.layout, element, parent, position);
			Forge_Builder.element_sort_position(parent);
			Forge_Builder.status_ready();
			
			//Refresh sortables
			Forge_Builder.init_sortable_elements();
			Forge_Builder.highlight_empty_blocks();
			Forge_Builder.update_history();
		},
		
		
		//Click on Update button in the form
		action_page_templates: function(e){
			e.preventDefault();
			window.open(Forge_Builder_Settings.admin_url + 'admin.php?page=forge_templates', '_blank');
			e.stopPropagation();
		},
	};

	//Start up the builder
	$(function(){ Forge_Builder.init(); });

})(jQuery);