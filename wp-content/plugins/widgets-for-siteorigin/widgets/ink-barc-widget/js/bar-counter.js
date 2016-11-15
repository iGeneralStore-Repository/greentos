( function($) {

	$(document).ready( function() {

		$( '.iw-so-bar-counter.iw-so-bars-animated' ).find( '.iw-so-bar-meter' ).waypoint( {
			offset: '85%',
			handler: function() {
				$(this.element).each(function(){
					each_bar_width = $(this).attr( 'aria-valuenow' );
					$(this).width(each_bar_width + '%' );
				});
				this.destroy()
			}
		} );

	});

})( jQuery );
