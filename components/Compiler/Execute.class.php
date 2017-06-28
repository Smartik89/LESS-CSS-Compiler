<?php 
namespace ZeroWPLCC\Component\Compiler;

class Execute{

	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'frontendCompiledCSS' ), 999 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'compilerScript' ), 199 );
		add_action( 'wp_head', array( $this, 'compilerPlaceholder' ), 199 );
		add_action( 'wp_ajax_zwplcc_save_compiled_css', array( $this, '_saveCompiledCss' ), 299 );
	}

	protected function singleCompilerArgs(){
		return array(
			// 'id' => false, // A unique lowercase alpha-numeric ID. It's the handle for this stylesheet when it's compiled.
			// 'less_root' => false, // The root folder where all less files are in.
			// 'less_file' => false, // The main less file name from root folder.
			// 'replace' => false, // The handle of a registered stylesheet to be replaced.
			// 'fields' => array(), // An array of fields with IDs from customizer and variable names to modify from less
		);
	}

	public static function compilers(){
		$compilers = apply_filters( 'zwplcc:compilers', array() );

		if( !empty( $compilers ) ){
			$temp = array();
			foreach ( $compilers as $key => $comp ) {
				$id = false;

				// Validate ID
				if( !empty( $comp['id'] ) ){
					$id = preg_replace( "/[^a-zA-Z0-9_-]+/", "", $comp['id'] );
					$id = trim( strtolower( $id ) );
				}

				// Validate fields
				$fields = !empty( $comp['fields'] ) && is_array( $comp['fields'] ) ? $comp['fields'] : array();

				// Validate less root dir
				$less_root = !empty( $comp['less_root'] ) ? $comp['less_root'] : false;

				// Validate less file
				$less_file = !empty( $comp['less_file'] ) ? $comp['less_file'] : false;

				// Validate replacer
				$replace = !empty( $comp['replace'] ) ? $comp['replace'] : false;

				// We have the minimum requirements
				if( $id && $fields && $less_root && $less_file ){
					$temp[] = array(
						'id'        => $id,
						'fields'    => $fields,
						'less_root' => $less_root,
						'less_file' => $less_file,
						'replace' => $replace,
					);
				}
			}
			$compilers = $temp;
		}

		return $compilers;
	}

	//------------------------------------//--------------------------------------//
	
	/**
	 * Front-end scripts & styles
	 *
	 * @return void 
	 */
	public function frontendCompiledCSS(){
		if( !empty( $this->compilers() ) ){
			
			foreach ( $this->compilers() as $compiler ) {
				
				$css_file = apply_filters( 'zwplcc:enqueue_css_file', array(
					'url' => $this->cssFileUrl( $compiler['id'] ),
					'path' => $this->cssFilePath( $compiler['id'] ),
				), $compiler, $this );

				if( file_exists( $css_file['path'] ) ){

					if( !empty( $compiler['replace'] ) ){
						wp_deregister_style( $compiler['replace'] );
					}

					zwplcc()->addStyle( 
					zwplcc_config( 'id' ) . '-'. $compiler['id'] . '-compiled-css', 
						apply_filters( 'zwplcc:css_wp_enqueue', array(
							'src'     => $css_file['url'],
							'ver'     => get_option( 'zwplcc_compiled_css_version_'.  $compiler['id'], '0.1' ),
						), 
						$compiler )
					);

				}
			}

		}
	}

	public function compilerPlaceholder(){
		if( is_customize_preview() ){
			if( !empty( $this->compilers() ) ){
				
				foreach ( $this->compilers() as $compiler ) {
					echo '<style id="less-css-renderer-placeholder-'. $compiler['id'] .'"></style>';
				}

			}
		}
	}

	public function compilerScript(){
		if( !empty( $this->compilers() ) ){

			zwplcc()->addScript( zwplcc_config( 'id' ) . '-compiler-config', array(
				'src' => zwplcc()->assetsURL( 'js/compiler-config.js' ),
				'ver' => zwplcc_config( 'version' ),
				'deps' => array( 'jquery' ),
				'zwplcc_config_admin' => array(
					'ajax_url'  =>  admin_url( 'admin-ajax.php' ),
					'compilers' => $this->compilers(),
				),
			));

		}
	}

	protected function _cssFile( $id, $type = 'dir' ){
		$type       = ( $type === 'url' ) ? 'baseurl' : 'basedir';
		$upload_dir = wp_upload_dir();
		$up_path    = trailingslashit($upload_dir[ $type ]);

		return $this->cssFileName( $up_path . $id );
	}

	public function cssFileName( $name ){
		return $name .'-custom.css';
	}

	public function cssFilePath( $id ){
		return $this->_cssFile( $id, 'dir' );
	}

	public function cssFileUrl( $id ){
		return $this->_cssFile( $id, 'url' );
	}

	public function _saveCompiledCss(){
		if( !empty( $this->compilers() ) ){
		
			$output = array();

			foreach ( $this->compilers() as $compiler ) {
				if( !empty( $_POST[ '_compiled_css' ][ $compiler['id'] ] ) ){
					$css        = wp_unslash( $_POST[ '_compiled_css' ][ $compiler['id'] ] );
					$css_file   = $this->cssFilePath( $compiler['id'] );
					$status     = ( false !== file_put_contents( $css_file, $css ) ) ? 'success' : 'error';

					update_option( 'zwplcc_compiled_css_version_'.  $compiler['id'], time() );

					$output[ $compiler['id'] ] = array(
						'status' => $status,
						'css_file' => $css_file,
					);
				}
			}

			echo json_encode( $output );

		}
		die();
	}

}