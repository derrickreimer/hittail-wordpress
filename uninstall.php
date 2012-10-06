<?php

// Exit if not called from WordPress
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

// Remove options
delete_option('ht_options');