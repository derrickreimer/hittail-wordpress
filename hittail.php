<?php
/*
Plugin Name: HitTail Long Tail SEO Tool
Plugin URI: http://www.hittail.com/plugins/wordpress
Description: Official HitTail Wordpress plugin
Version: 0.1.0
Author: Derrick Reimer
Author URI: http://www.hittail.com
License: GPLv2
*/

//===========================
// Globals
//===========================

define( 'HT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
$ht_options = get_option( 'ht_options' );

//===========================
// Includes
//===========================

require_once( 'lib/admin.php' );

//===========================
// Hooks
//===========================

// Install the plugin and set default options.
function ht_install() {
	global $ht_options;
	
	// Set default options
	if ( ! isset( $ht_options['site_id'] ) ) { $ht_options['site_id'] = ""; }
	if ( ! isset( $ht_options['is_disabled'] ) ) { $ht_options['is_disabled'] = 0; }
	
	// Save options
	update_option( 'ht_options', $ht_options );
}

register_activation_hook( __FILE__, 'ht_install' );


// Output the HitTail tracking code with the site ID set in the options.
function ht_tracking_code() {
	global $ht_options;
	$id = $ht_options['site_id'];
	
	// Check if the ID is set and is an integer
	if ( ! $ht_options['is_disabled'] ) {
		if ( isset($id) && $id != "" ) {
			echo '<!-- HitTail Code -->' .
				'<script type="text/javascript">' .
					'(function(){ var ht = document.createElement("script");ht.async = true;' .
				  	'ht.type="text/javascript";ht.src="//' . $id . '.hittail.com/mlt.js";' .
				  	'var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ht, s);})();' .
				'</script>';
		} else {
			echo '<!-- HitTail: Set your site ID to begin tracking -->';
		}
	}
}

add_action( 'wp_footer', 'ht_tracking_code' );