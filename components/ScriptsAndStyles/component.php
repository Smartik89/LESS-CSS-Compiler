<?php 
//------------------------------------//--------------------------------------//

/**
 * Front-end scripts & styles
 *
 * @return void 
 */
add_action( 'wp_enqueue_scripts', function(){
	
	if( is_customize_preview() ){
		zwplcc()->addScript( zwplcc_config( 'id' ) . '-config-frontend', array(
			'src'     => zwplcc()->assetsURL( 'js/compiler-config-frontend.js' ),
			'deps'    => array( 'jquery' ),
		));
	}

});

//------------------------------------//--------------------------------------//

/**
 * Customizer scripts & styles
 *
 * @return void 
 */
add_action( 'customize_controls_enqueue_scripts', function(){
	
	zwplcc()->addStyle( 'noty', array(
		'src' => zwplcc()->assetsURL( 'noty/noty.css' ),
		'ver' => '3.1.0',
	));

	zwplcc()->addScript( 'es5-shim', array(
		'src' => zwplcc()->assetsURL( 'js/es5-shim.min.js' ),
		'ver' => '4.5.7',
	));
	
	zwplcc()->addScript( 'es5-sham', array(
		'src' => zwplcc()->assetsURL( 'js/es5-sham.min.js' ),
		'ver' => '4.5.7',
	));
	
	zwplcc()->addScript( 'es6-shim', array(
		'src' => zwplcc()->assetsURL( 'js/es6-shim.min.js' ),
		'ver' => '0.34.2',
	));
	
	zwplcc()->addScript( 'es6-sham', array(
		'src' => zwplcc()->assetsURL( 'js/es6-sham.min.js' ),
		'ver' => '0.34.2',
	));
	
	zwplcc()->addScript( 'less.js', array(
		'src' => zwplcc()->assetsURL( 'js/less.min.js' ),
		'ver' => '2.7.2',
	));
	
	zwplcc()->addScript( 'noty', array(
		'src' => zwplcc()->assetsURL( 'noty/noty.min.js' ),
		'ver' => '3.1.0',
	));
});

//------------------------------------//--------------------------------------//

/**
 * Back-end scripts & styles
 *
 * @return void 
 */
add_action( 'admin_enqueue_scripts', function(){

		// zwplcc()->addStyle( zwplcc_config( 'id' ) . '-styles-admin', array(
		// 	'src'     => zwplcc()->assetsURL( 'css/styles-admin.css' ),
		// 	'enqueue' => false,
		// ));

		// zwplcc()->addScript( zwplcc_config( 'id' ) . '-config-admin', array(
		// 	'src' => zwplcc()->assetsURL( 'js/config-admin.js' ),
		// 	'enqueue' => false,
		// 	'deps' => array( 'jquery' ),
		// ));

});