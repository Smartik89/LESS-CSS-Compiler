<?php 
require_once ZWPLCC_PATH . 'warnings/abstract-warning.php';

class ZWPLCC_NoPlugin_Warning extends ZWPLCC_Astract_Warning{

	public function notice(){
		
		$output = '';
		
		if( count( $this->data ) > 1 ){
			$message = __( 'Please install and activate the following plugins:', 'zerowp-less-css-compiler' );
		}
		else{
			$message = __( 'Please install and activate this plugin:', 'zerowp-less-css-compiler' );
		}

		$output .= '<h2>' . $message .'</h2>';


		$output .= '<ul class="zwplcc-required-plugins-list">';
			foreach ($this->data as $plugin_slug => $plugin) {
				$plugin_name = '<div class="zwplcc-plugin-info-title">'. $plugin['plugin_name'] .'</div>';

				if( !empty( $plugin['plugin_uri'] ) ){
					$button = '<a href="'. esc_url_raw( $plugin['plugin_uri'] ) .'" class="zwplcc-plugin-info-button" target="_blank">'. __( 'Get the plugin', 'zerowp-less-css-compiler' ) .'</a>';
				}
				else{
					$button = '<a href="#" onclick="return false;" class="zwplcc-plugin-info-button disabled">'. __( 'Get the plugin', 'zerowp-less-css-compiler' ) .'</a>';
				}

				$output .= '<li>'. $plugin_name . $button .'</li>';
			}
		$output .= '</ul>';

		return $output;
	}

}