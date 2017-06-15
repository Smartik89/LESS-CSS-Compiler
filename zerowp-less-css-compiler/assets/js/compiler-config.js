;(function( $ ) {

	"use strict";

	$.fn.zwplcc_compiler = function() {

		var self = false;

		var _api      = wp.customize,
		_config       = zwplcc_config_admin,
		_ajax_url     = _config.ajax_url,
		_compilers    = _config.compilers,
		_compiled_css = {};
		
		var _base = {

			processLess: function(){
				if( ! _.isEmpty( _compilers ) && _.isObject( _compilers ) ){

					_.each( _compilers, function( compiler, key, list ){

						// Prepare variables
						var 
							_id               = compiler.id,
							_less_root        = compiler.less_root,
							_less_file        = compiler.less_file,
							_replace          = compiler.replace,
							_fields           = compiler.fields,

							_cached_less_file = false,

							_mods      = [], 
							_old_mods  = [],
							_debounce;


						// Get LESS File using AJAX and cache it for later use
						if( _cached_less_file === false ){
							$.get( _less_root + _less_file, function( data ) {
								
								_cached_less_file = data.replace( 
									new RegExp('@import \"', 'g'), 
									'@import "'+ _less_root 
								);

							});
						}
						

						// Process and save CSS when a LESS field is changed
						_api.bind( 'change', function(){
							
							clearTimeout( _debounce );

							var customize   = this; // Customize object alias.
							var _to_process = false;
							_old_mods       = _mods;

							_debounce = setTimeout(function() {
								
								var options = {
									modifyVars:   {},
									env:          'production',
									rootpath:     _less_root,
									// relativeUrls: true
								}

								var _new_mods = [];

								for (var i = _fields.length - 1; i >= 0; i--) {
									customize( _fields[i].id, function( value ) {
										var _this_val = value.get() || false;
										
										if( _this_val ){
											options.modifyVars[ _fields[i].variable ] = _this_val;
											_new_mods[ _fields[i].variable ] = _this_val;
										}

										if( _old_mods[ _fields[i].variable ] !== _this_val ){
											_to_process = true;
										}
									});
								}

								_mods = _new_mods;
								// console.log( _new_mods );

								less.render(_cached_less_file, options, function (error, output) {
									
									if( ! error ) {
										_compiled_css[ _id ] = output.css;

										$( "#customize-preview iframe" )
											.contents()
											.find( '#less-css-renderer-placeholder-'+ _id )
											.html( output.css );
	
										self.msg( 'info', 'LESS has been processed successfuly.' );
									}
									else{
										self.msg( 'error', error.message +' in '+ error.filename +' on line '+ error.line, 15 );
									}
								});

							}, 500); // setTimeout

						}); // _api.bind
					}); //_.each
				}; // if
			},

			saveCss: function(){
				_api.bind( 'save', function(){
					if( ! _.isEmpty( _compiled_css ) ){
						$.ajax({
							url: _ajax_url,
							type: 'POST',
							data: {
								'action': 'zwplcc_save_compiled_css',
								'_compiled_css': _compiled_css
							},
							timeout: 90000, //1.5 minutes
							success: function(data, textStatus, xhr) {
								data = JSON.parse( data );
								console.log( data );
								
								_.each( data, function( compiler ){
									if ( compiler.status == 'success' ){
										self.msg( 'info', 'CSS compiled and successfuly saved to: '+ compiler.css_file, 7 );
									}
									else if ( compiler.status == 'error' ){
										self.msg( 'error', 'Unable to save the compiled CSS.', 7 );
									}
								});

							},
							error: function(xhr, textStatus, errorThrown) {
								self.msg( 'error', 'Unknown error! Failed to save the CSS.', 7 );
							}
						});
					}
				});
			},

			msg: function( _type, _message, _timeout ){
				var _title = ( 'error' === _type ) ? 'Error:' : 'Info:';
				_timeout = _timeout || 4;

				new Noty({
					type: _type,
					theme: 'mint',
					layout: 'bottomRight',
					text: '<p><strong>'+ _title +'</strong><br>'+ _message +'</p>',
					timeout: _timeout*1000,
					progressBar: true,
					closeWith: ['click', 'button'],
					animation: {
						open: 'noty_effects_open',
						close: 'noty_effects_close'
					},
				}).show();
			},

			/*
			-------------------------------------------------------------------------------
			Construct plugin
			-------------------------------------------------------------------------------
			*/
			__construct: function(){
				self = this;

				self.processLess();
				self.saveCss();
				
				return this;
			}

		};

		/*
		-------------------------------------------------------------------------------
		Rock it!
		-------------------------------------------------------------------------------
		*/
		_base.__construct();

	};


$(document).on( 'ready load', function(){
	$('body').zwplcc_compiler();
});

})(jQuery);