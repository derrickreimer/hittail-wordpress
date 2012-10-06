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

// Public: Install the plugin and set default options.
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

