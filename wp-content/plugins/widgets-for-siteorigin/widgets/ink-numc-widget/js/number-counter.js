( function($) {

	$(document).ready( function() {

		$( '.iw-so-number-timer' ).waypoint( {
			offset: '75%',
			handler: function() {
				$(this.element).countTo();
				this.destroy()
			}
		} );

	});

})( jQuery );
