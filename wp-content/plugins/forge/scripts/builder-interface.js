(function($){
	
	$(document).ready(function(){
		
		//Focus on search box upon load
		$('#forge-builder-search').focus();
		
		
		//Filter element collection by title 
		$('#forge-builder-search').on('keyup', function(){
			builder_search = $('#forge-builder-search').val().toLowerCase();
			if(builder_search != ''){
				//$('.forge-builder-collection-item').hide();
				$('.forge-builder-collection-item').addClass('forge-builder-collection-hidden');
				$('.forge-builder-collection-item').each(function(){
					if($(this).attr('data-name').toLowerCase().indexOf(builder_search) > -1){
						//$(this).show();
						$(this).removeClass('forge-builder-collection-hidden');
					}
				});
			}else{
				//$('.forge-builder-collection-item').show();
				$('.forge-builder-collection-item').removeClass('forge-builder-collection-hidden');
			}
		});
		
		
		//Selected buttonlist field
		$('body').delegate('.forge-buttonlist-item', 'click', function(){
			var parent = $(this).parent();
			parent.children('.forge-buttonlist-item').each(function(){
				$(this).removeClass('forge-buttonlist-selected');
			});
			$(this).addClass('forge-buttonlist-selected');
		});
		
		
		//Selected iconlist field
		$('body').delegate('.forge-iconlist-item', 'click', function(){
			//Change other borders
			$('.forge-iconlist-item').removeClass('forge-iconlist-selected');
			$(this).addClass('forge-iconlist-selected');        
		});
		
		
		//Margin control field
		$('body').delegate('.forge-margins-control', 'keyup', function(){
			var value = $(this).val();
			$(this).closest('.forge-margins').find('.forge-margins-field').val(value);
			$(this).closest('.forge-margins').find('.forge-margins-field').each(function(){
				$(this).trigger('change');
			});
		});
		
		
		//Select all on click
		$('body').delegate('.forge-click-selectall', 'click', function(){
			$(this).focus();
			$(this).select();
		});
		
		
		//Add row in collection field
		jQuery('body').on('click', '.forge-collection-add', function(e) {
			e.preventDefault();
			var current_element = jQuery(this);
			var row = current_element.parent().prev('.forge-collection-row');
			var new_row = forge_collection_add(row);
			new_row.insertAfter(row);
		});
		
		//Remove row in collection field
		jQuery('body').on('click', '.forge-collection-remove', function(e) {
			e.preventDefault();

			var row = jQuery(this).parent('.forge-collection-row');
			var count = row.parent().find('.forge-collection-row').length;
			
			//Always leave at least one row
			if(count > 1){
				jQuery('input, select', row).val('');
				row.remove();
			}

			//Reorder rows
			jQuery('.forge-collection-row').each(function(rowIndex){
				jQuery(this).find('input, select').each(function(){
					var name = jQuery( this ).attr('name');
					name = name.replace(/\[(\d+)\]/, '[' + rowIndex + ']');
					jQuery(this).attr('name', name ).attr('id', name);
				});
			});
		});
	
	});

})(jQuery);


function forge_collection_add(row){
	// Retrieve the highest current field index
	var key = highest = 1;
	row.parent().find('.forge-collection-row').each(function(){
		var current = jQuery(this).data('index');
		if(parseInt(current) > highest){
			highest = current;
		}
	});
	key = highest += 1;
	
	new_row = row.clone();
	new_row.find('.forge-collection-cell input').val('');
	
	//Update index and names of new row
	new_row.data('index', key);
	new_row.find('input, select, textarea').each(function() {
		var new_name = jQuery(this).attr('name');
		new_name = new_name.replace(/\[(\d+)\]/, '[' + parseInt( key ) + ']');
		jQuery(this).attr('name', new_name).attr('id', new_name);
	});
	
	return new_row;
}