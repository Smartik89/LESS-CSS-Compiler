;(function( $ ) {

	"use strict";

	$(document).on( 'ready load', function(){

		var self = false;

		// var _api = wp.customize;
		var _build_time = '0',
		_old_build_time = '0';
	
		window.setInterval(function(){

			/* Get last CSS modificaton time
			-------------------------------------*/
			var _build_time = sessionStorage.getItem( '_zwplcc_compiled_css_build_time' );

			/* Should we display the CSS? Is processed?
			------------------------------------------------*/
			if( _build_time !== '0' && _build_time !== _old_build_time ){
				// console.log( _build_time );
				
				/* Get compiled CSS from Storage
				-------------------------------------*/
				var _compiled_css = JSON.parse( sessionStorage.getItem( '_zwplcc_compiled_css' ) );
				
				// console.log( _compiled_css );

				/* Inject each compiled CSS
				--------------------------------*/
				$.each( _compiled_css, function( _id, _css ) {
					if( _css ){
						$( '#less-css-renderer-placeholder-'+ _id ).html( _css );
					}
				});

				/* Finally make the build time old
				---------------------------------------*/
				_old_build_time = _build_time;
			}
		}, 200);

	});

})(jQuery);