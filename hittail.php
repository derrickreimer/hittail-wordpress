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

define( 'HT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once( 'lib/admin.php' );

class WP_HitTail {
	protected $options;
	
	public function __construct() {
		// Fetch options
		$this->options = get_option( 'ht_options' );
		
		// Register install function
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		
		// Register hooks
		$this->add_actions();
	}
	
	// Install the plugin and set default options.
	public function install() {
		// Set default options
		if ( ! isset( $this->options['site_id'] ) ) { $this->options['site_id'] = ""; }

		// Save options
		update_option( 'ht_options', $this->options );
	}
	
	public function add_actions() {
		// Place tracking code in the footer
		add_action( 'wp_footer', array( $this, 'tracking_code' ) );
	}
	
	// Lookup an option and return NULL unless it is set and non-empty
	public function get_option( $key ) {
		if ( isset( $this->options[ $key ] ) && $this->options[ $key ] != "" ) {
			return $this->options[ $key ];
		} else {
			return NULL;
		}
	} 
	
	// Output the HitTail tracking code with the site ID set in the options.
	public function tracking_code() {
		// Check if the ID is set and is an integer
		if ( ! $this->get_option( 'is_disabled' ) ) {
			if ( $this->get_option( 'site_id' ) ) { ?>
				
				<!-- HitTail Code -->
				<script type="text/javascript">
					(function(){ var ht = document.createElement("script");ht.async = true;
					ht.type="text/javascript";ht.src="//<?php echo $this->get_option( 'site_id' ) ?>.hittail.com/mlt.js";
					var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ht, s);})();
				</script>
				
			<?php 
			} else {
				echo '<!-- HitTail: Set your site ID to begin tracking -->';
			}
		}
	}
}

$wp_hittail = new WP_HitTail();