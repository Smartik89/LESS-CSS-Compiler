<?php
namespace ZeroWPLCC\Component\Compiler;

use ZeroWPLCC\Component\Compiler\Execute;

class PresetsSupport{

	public function __construct(){
		add_filter( 'zwplcc:enqueue_css_file', array( $this, 'cssFile' ), 99, 3 );
		add_action( 'zwpocp_preset:create_preset', array( $this, 'duplicateCssFile' ), 99, 2 );
	}

	/*
	-------------------------------------------------------------------------------
	Replace and Enqueue CSS file. Support for
	-------------------------------------------------------------------------------
	*/
	function cssFile( $args, $compiler, $exe_obj ){
		if( !is_customize_preview() && ! is_admin() ){
			$id = false;

			if( ! empty($_GET[ 'zwpocp_preset' ]) ){
				$id = sanitize_key( $_GET[ 'zwpocp_preset' ] );
			}
			else if( ! empty( $_COOKIE[ 'zwpocp_preset' ] ) ){
				$id = sanitize_key( $_COOKIE[ 'zwpocp_preset' ] );
			}

			if( !empty( $id ) ){
				$css_file_path = $exe_obj->cssFilePath( get_stylesheet() .'-presets/'. $id .'/dynamic-css/'. $compiler['id'] );

				if( file_exists( $css_file_path ) ){
					$args['path'] = $css_file_path;
					$args['url'] = $exe_obj->cssFileUrl( get_stylesheet() .'-presets/'. $id .'/dynamic-css/'. $compiler['id'] );
				}
			}

		}

		return $args;
	}

	//------------------------------------//--------------------------------------//
	
	/**
	 * Support for "ZeroWP OneClick Presets" plugin
	 *
	 * @return void 
	 */
	public function duplicateCssFile( $id, $access_obj ){
		
		$exe = new Execute;
		$compilers = $exe->compilers();
		if( !empty($compilers) ){
			foreach ($compilers as $compiler) {
				$css_filepath = $exe->cssFilePath( $compiler['id'] );
				if( file_exists( $css_filepath ) ){
					$css = file_get_contents( $css_filepath );
					$access_obj->file_manager->putContents( 
						$exe->cssFilePath( get_stylesheet() .'-presets/'. $id .'/dynamic-css/'. $compiler['id'] ) , 
						$css 
					);
				}
			}
		}

	} 

}