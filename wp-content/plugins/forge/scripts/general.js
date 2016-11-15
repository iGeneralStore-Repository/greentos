jQuery(document).ready(function(){
	
	/* PARALLAX BACKGROUNDS */
	var window_height = jQuery(window).height();
	jQuery(window).on('resize', function(){
		window_height = jQuery(window).height();
	});
	
	//Scroll events
	jQuery(window).on('scroll', function(){
		jQuery('.forge-background-parallax').each(function(){
			forge_update_parallax(jQuery(this), window_height);
		}); 
	});
	
	//Update on first load
	jQuery('.forge-background-parallax').each(function(){
		forge_update_parallax(jQuery(this), window_height);
	});
	
	forge_animations_load();
});


function forge_update_parallax(background, window_height){
	var window_scroll = jQuery(window).scrollTop();
	var bg_scroll = background.parent().offset().top;
	var bg_height = background.parent().height();
	var bg_start = bg_scroll - window_height;
	var bg_end = bg_scroll + bg_height;
	var bg_length = bg_end - bg_start;
	var parallax_move = background.height() - bg_height;
	
	//If background is in visible area
	if(window_scroll >= bg_start && window_scroll <= bg_end){
		var parallax_ratio = (window_scroll - bg_start) / bg_length * parallax_move;
		background.css({ marginBottom: '-' + parallax_ratio + 'px' });
	}
}

//Add animation class to objects entering viewport
function forge_animations_load(){
	if(jQuery.isFunction(jQuery.fn.forgewaypoint)){
		jQuery('.forge-animation').each(function(){ 
			jQuery(this).forgewaypoint(function(direction){ 
				if(direction == 'down'){
					jQuery(this.element).addClass('forge-animation-active'); 
				}
			},{ offset:'90%' });
		});
	}
}