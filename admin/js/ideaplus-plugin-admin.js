var Ideaplus_Admin;

(function ( $ ) {
	'use strict';
	Ideaplus_Admin = {
		interval: 0,

		ajax_url   : '/wp-admin/admin-ajax.php?action=ideaplus_ajax_check_auth_status',
		init       : function ( ajax_url ) {
			this.loader();
			Ideaplus_Admin.start_check();
		},
		loader     : function () {
			jQuery( '.printful-connect-button' ).click( function () {
				jQuery( this ).hide();

				setTimeout( function () {
					Ideaplus_Admin.hide_loader();
				}, 60000 ); //hide the loader after a minute, assume failure
			} );

			jQuery( '#clear-cache-btn' ).click( function () {
				jQuery.ajax( {
								 type   : "GET",
								 url    : '/wp-admin/admin-ajax.php?action=ideaplus_ajax_clear_cache',
								 success: function ( response ) {
									 location.reload();
								 }
							 } );
			} );
		},
		show_loader: function () {
			jQuery( this ).siblings( '.loader' ).removeClass( 'hidden' );
		},
		hide_loader: function () {
			jQuery( this ).siblings( '.loader' ).addClass( 'hidden' );
		},
		start_check: function () {
			Ideaplus_Admin.show_loader();
			this.interval = setInterval( this.check_auth.bind( this ), 5000 ); // try every 10 sec
		}
		,
		check_auth: function () {
			var interval = this.interval;
			jQuery.ajax( {
							 type   : "GET",
							 url    : this.ajax_url,
							 success: function ( response ) {
								 console.log( 'check auth', response );
								 if ( response === 'Success' ) {
									 clearInterval( interval );
									 location.reload();
								 }
							 }
						 } );
		}
	};
	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );
