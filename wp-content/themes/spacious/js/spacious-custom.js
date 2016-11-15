// added by KH
var currentScrollTop = 0;

jQuery( document ).ready( function () {

	// added by KH
	scrollSubMenuController();

	jQuery( '#scroll-up' ).hide();

	jQuery( function () {
		jQuery( window ).scroll( function () {
			// added by KH
			scrollSubMenuController();

			if ( jQuery( this ).scrollTop() > 1000 ) {
				jQuery( '#scroll-up').fadeIn();
			} else {
				jQuery( '#scroll-up' ).fadeOut();
			}
		});
		jQuery( 'a#scroll-up' ).click( function () {
			jQuery( 'body, html' ).animate({
				scrollTop: 0
			}, 800 );
			return false;
		});
	});

	
	// added by KH 
	jQuery('.menu-item-has-children a').focus( function () {
		jQuery('.sub-menu').attr({'style' : 'z-index: 9999 !important'});
		jQuery('.current-menu-item a').attr({'style' : 'color:#444 !important'});
	});
	
	jQuery('.menu-item-has-children a').blur( function () {
		jQuery('.sub-menu').attr({'style' : 'z-index: -10 !important'});
		jQuery('.current-menu-item a').attr({'style' : 'color:#0FBE7C !important'});
	});

	jQuery('a[href*="#"]:not([href="#"])').click(function() {
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
			var target = jQuery(this.hash);
			target = target.length ? target : jQuery('[name=' + this.hash.slice(1) +']');
			if (target.length) {
				jQuery('html, body').animate({
					scrollTop: target.offset().top
				}, 500);
				return false;
			}
		}
	});
	

});

// 서브 메뉴의 위치를 제어하는 함수
function scrollSubMenuController() {
	currentScrollTop = jQuery(window).scrollTop();

	var heightHeader = jQuery('header#masthead').height();

	if (currentScrollTop < heightHeader) {
	    jQuery('#menu-promotion-container').css('top', heightHeader-(currentScrollTop));
	    if (jQuery('#menu-promotion-container').hasClass('fixed')) {
	        jQuery('#menu-promotion-container').removeClass('fixed');
	    }
	} else {
	    if (!jQuery('#menu-promotion-container').hasClass('fixed')) {
	        jQuery('#menu-promotion-container').css('top', 0);
	        jQuery('#menu-promotion-container').addClass('fixed');
	    }
	}
}
