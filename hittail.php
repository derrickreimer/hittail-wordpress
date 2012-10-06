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

$ht_options = get_option('ht_options');

//===========================
// Hooks
//===========================

// Internal: Install the plugin and set default options.
//
// Returns nothing.
function ht_install() {
	global $ht_options;
	
	// Set default options
	if (!isset($ht_options['site_id'])) { $ht_options['site_id'] = ""; }
	
	// Save options
	update_option('ht_options', $ht_options);
}

register_activation_hook(__FILE__, 'ht_install');

// Internal: Output the HitTail tracking code for the site id set
// in the options.
//
// Returns a String.
function ht_tracking_code() {
	$id = $ht_options['site_id'];
	
	// Check if the ID is set and is an integer
	if (isset($id) && strval(intval($id)) == strval($id)) {
		echo '<!-- HitTail Code -->' .
			'<script type="text/javascript">' .
				'(function(){ var ht = document.createElement("script");ht.async = true;' .
			  	'ht.type="text/javascript";ht.src="//' . $id . '.hittail.com/mlt.js";' .
			  	'var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ht, s);})();' .
			'</script>'
	} else {
		echo "<!-- HitTail: Please set your site id -->";
	}
}

add_action('wp_footer', 'ht_tracking_code');