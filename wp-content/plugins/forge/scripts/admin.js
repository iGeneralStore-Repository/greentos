(function($){
	$(document).ready(function(){
		
		//Open new connection form
		$('.forge-connections-new').on('click', function(e){
			e.preventDefault();
			$('.forge-connections-create').show();
			$('.forge-connections').hide();
		});
		
		
		//Cancel creating connection
		$('.forge-connections-create-cancel').on('click', function(e){
			e.preventDefault();
			$('.forge-connections-create').hide();
			$('.forge-connections').show();
			$('.forge-connection-type').val('none');
			$('.forge-connections-create-fields').html('');
			$('.forge-connections-submit').hide();
		});
		
		
		//Cancel creating connection
		$('.forge-connections-setup-cancel').on('click', function(e){
			e.preventDefault();
			$('.forge-connections-setup').hide();
			$('.forge-connections-create').show();
			$('.forge-connection-type').val('none');
			$('.forge-connections-create-fields').html('');
			$('.forge-connections-submit').hide();
		});
		
		
		//Change connection fields based on selected service
		$('.forge-connection-type').on('change', function(){
			var type = $(this).val();
			var fields = $('.forge-connections-fields-' + type).html();
			$('.forge-connections-create-fields').html(fields);
			if(type != 'none'){
				$('.forge-connections-submit').show();
			}else{
				$('.forge-connections-submit').hide();
			}
		});
		
		
		//Create new connection and send credentials
		$('.forge-connections-create-form').on('submit', function(e){
			e.preventDefault();
			var form_data = {
				action: 'forge_request_create_connection',
				fields: $(this).serialize(),
			};
			
			$('.forge-connections-create').addClass('forge-connections-loading');
			var result = $.post(ajaxurl, form_data, function(response){
				//Success or retry?
				var data = JSON.parse(response);
				if(data.status == true){
					$('.forge-connections-create').removeClass('forge-connections-loading');
					$('.forge-connections-setup-fields').html(data.fields);
					$('.forge-connections-create').hide();
					$('.forge-connections-setup').show();
				}
			});
		});
		
		
		//Save new connection
		$('.forge-connections-setup-form').on('submit', function(e){
			e.preventDefault();
			var form_data = {
				action: 'forge_request_save_connection',
				fields: $(this).serialize(),
			};
			
			$('.forge-connections-setup').addClass('forge-connections-loading');
			var result = $.post(ajaxurl, form_data, function(response){
				//Success or retry?
				var data = JSON.parse(response);
				if(data.status == true){
					window.location.reload();
				}
			});
		});
		
		
		//Save new connection
		$('.forge-connection-delete').on('click', function(e){
			e.preventDefault();
			var connection = $(this).closest('.forge-connection');
			var result = confirm('Are you sure?');
			if(result) {
				var form_data = {
					action: 'forge_request_delete_connection',
					connection_id: $(this).attr('data-id'),
				};
				connection.empty();
				connection.remove();
				$.post(ajaxurl, form_data);
			}
		});
		
		
		//Load preset
		$('.forge-preset-form').on('submit', function(e){
			e.preventDefault();
			var current_preset = $(this).closest('.forge-preset');
			var form_data = {
				action: 'forge_request_load_preset',
				postid: $(this).find('select[name=postid]').val(),
				fields: $(this).serialize(),
			};
			
			current_preset.addClass('forge-preset-loading');
			var result = $.post(ajaxurl, form_data, function(response){
				var data = JSON.parse(response);
				if(data.status == true){
					current_preset.removeClass('forge-preset-loading');
					current_preset.addClass('forge-preset-done');
					current_preset.find('.forge-preset-success').html(data.content);
				}
			});
		});
		
		
		//Reload preset
		$('body').delegate('.forge-preset-reload', 'click', function(e){
			e.preventDefault();
			var current_preset = $(this).closest('.forge-preset');
			
			current_preset.removeClass('forge-preset-done');
			current_preset.find('.forge-preset-success').html('');
			current_preset.find('select[name=postid]').val('new');
		});
		
		//Show edit template form
		$('.forge-template-edit').on('click', function(e){
			e.preventDefault();
			var template_id = $(this).attr('data-id');
			var form_data = {
				action: 'forge_request_edit_template_form',
				template: template_id,
			};
			
			var result = $.post(ajaxurl, form_data, function(response){
				if(response != ''){
					$('.forge-templates-edit-content').html(response);
				}
			});
			
			$('.forge-templates-edit').show();
			$('.forge-templates').hide();
		});
		
		
		//Cancel creating connection
		$('body').delegate('.forge-template-edit-cancel', 'click', function(e){
			e.preventDefault();
			$('.forge-templates-edit').hide();
			$('.forge-templates-edit-content').html('');
			$('.forge-templates').show();
		});
		
		$('body').delegate('.forge-templates-tab', 'click', function(e){
			var tab = $(this).attr('rel');
			$('.forge-templates-tab').removeClass('forge-templates-tab-active');
			$('.forge-templates-tab-group').removeClass('forge-templates-tab-active');
			$(this).addClass('forge-templates-tab-active');
			$(tab).addClass('forge-templates-tab-active');
		});
		
		$('body').delegate('.forge-templates-tab-content', 'click', function(e){
			var value = $(this).attr('rel');
			$('#template_hook').val(value);
		});
	});

})(jQuery);