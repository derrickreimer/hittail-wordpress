<?php
/**
 * SEO Keyword Suggestions by HitTail
 * 
 * The official HitTail Wordpress plugin.
 * 
 * @package HitTail
 * @global object $WP_HitTail
 * @author Derrick Reimer <derrick@hittail.com>
 * @version 1.0.1
 */
/*
Plugin Name: SEO Keyword Suggestions by HitTail
Plugin URI: http://www.hittail.com/widget/wordpress/
Description: Drive targeted search visitors to your website by focusing on the most promising organic keywords in your existing traffic.
Version: 1.0.1
Author: Derrick Reimer
Author URI: http://www.hittail.com
License: GPLv2
*/

// Include constants file
require_once( dirname( __FILE__ ) . '/lib/constants.php' );

class WP_HitTail {
	var $namespace = "hittail";
	var $friendly_name = "HitTail";
	var $version = "1.0.1";
	var $options_name = "ht_options";
	var $options;
	
	/**
	 * Instantiate a new instance
	 * 
	 * @uses get_option()
	 */
	public function __construct() {
		// Fetch options
		$this->options = get_option( $this->options_name );
		
		// Load all library files used by this plugin
		$libs = glob( WP_HITTAIL_DIRNAME . '/lib/*.php' );
		foreach( $libs as $lib ) {
			include_once( $lib );
		}
		
		// Register hooks
		$this->_add_hooks();
	}
	
	/**
	 * Sets default options upon activation
	 *
	 * Hook into register_activation_hook action
	 *
	 * @uses update_option()
	 */
	public function activate() {
		// Set default options
		if ( ! isset( $this->options['site_id'] ) ) { $this->options['site_id'] = ""; }

		// Save options
		update_option( $this->options_name, $this->options );
	}
	
	/**
	 * Clean up after deactivation
	 *
	 * Hook into register_deactivation_hook action
	 */
	public function deactivate() {
		// Deactivation stuff here...
	}
	
	/**
	 * Add various hooks and actions here
	 *
	 * @uses add_action()
	 */
	private function _add_hooks() {
		// Activation and deactivation
		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
		
		// Options page for configuration
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );

		// Register admin settings
		add_action( 'admin_init', array( &$this, 'admin_register_settings' ) );

		// Place tracking code in the footer
		add_action( 'wp_footer', array( &$this, 'tracking_code' ) );
	}
	
	/**
	 * Lookup an option from the options array
	 *
	 * @param string $key The name of the option you wish to retrieve
	 *
	 * @return mixed Returns the option value or NULL if the option is not set or empty
	 */
	public function get_option( $key ) {
		if ( isset( $this->options[ $key ] ) && $this->options[ $key ] != "" ) {
			return $this->options[ $key ];
		} else {
			return NULL;
		}
	}
	
	/**
	 * Output the HitTail tracking code
	 *
	 * Displays nothing if tracking code is disabled or the site ID is not set.
	 */
	public function tracking_code() {
		// Check if the ID is set and is an integer
		if ( ! $this->get_option( 'is_disabled' ) ) {
			if ( $this->get_option( 'site_id' ) ) { 
				$site_id = $this->get_option( 'site_id' );
				include( WP_HITTAIL_DIRNAME . "/views/tracking-code.php" );
			} else {
				echo '<!-- HitTail: Set your site ID to begin tracking -->';
			}
		}
	}
	
	/**
	 * Define the admin menu options for this plugin
	 * 
	 * @uses add_action()
	 * @uses add_options_page()
	 */
	public function admin_menu() {
		$page_hook = add_options_page( 'HitTail Settings', $this->friendly_name, 'manage_options', $this->namespace, array( &$this, 'admin_options_page' ) );
		
		// Add admin scripts and styles
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
	}
	
	/**
	 * The admin section options page rendering method
	 * 
	 * @uses current_user_can()
	 * @uses wp_die()
	 */
	public function admin_options_page() {
		// Ensure the user has sufficient permissions
		if ( ! current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		include( WP_HITTAIL_DIRNAME . "/views/options.php" );
	}
	
	/**
	 * Register all the settings for the options page (Settings API)
	 *
	 * @uses register_setting()
	 * @uses add_settings_section()
	 * @uses add_settings_field()
	 */
	public function admin_register_settings() {
		register_setting( 'ht_options_group', $this->options_name, array( &$this, 'validate_settings' ) );
		add_settings_section( 'ht_code_settings', 'Tracking Code', array( &$this, 'admin_section_code_settings' ), $this->namespace );
		add_settings_field( 'ht_site_id', 'Site ID', array( &$this, 'admin_option_site_id' ), $this->namespace, 'ht_code_settings' );
		add_settings_field( 'ht_is_disabled', 'Visibility', array( &$this, 'admin_option_is_disabled' ), $this->namespace, 'ht_code_settings' );
	}
	
	/**
	 * Validates user supplied settings and sanitizes the input
	 *
	 * @param array $input The set of option parameters
	 *
	 * @return array Returns the set of sanitized options to save to the database
	 */
	public function validate_settings( $input ) {
		$options = $this->options;
		
		if ( isset( $input['site_id'] ) ) {
			// Remove padded whitespace
			$site_id = trim( $input['site_id'] );
			
			// Only allow an integer or blank string
			if ( is_int( $site_id ) || ctype_digit( $site_id ) || $site_id == "" ) {
				$options['site_id'] = $site_id;
			} else {
				add_settings_error( 'site_id', $this->namespace . '_site_id_error', "Please enter a valid site ID", 'error' );
			}
		}
		
		return $options;
	}
	
	/** 
	 * Output the input for the site ID option
	 */
	public function admin_option_site_id() {
		echo "<input type='text' name='ht_options[site_id]' size='20' value='{$this->get_option( 'site_id' )}'>";
	}
	
	/** 
	 * Output the input for the disabled option
	 */
	public function admin_option_is_disabled() {
		echo "<input type='checkbox' name='ht_options[is_disabled]' value='1' " . 
			checked( 1, $this->get_option( 'is_disabled' ), false ) . " /> " .
			"Disable tracking code on all pages";
	}
	
	/** 
	 * Output the description for the Tracking Code settings section
	 */
	public function admin_section_code_settings() {
		echo '<p>Your site ID can be found under <a href="http://www.hittail.com/app/sites.asp">Account &rarr; Sites</a> in your HitTail account.</p>';
	}
	
	/**
	 * Load stylesheet for the admin options page
	 * 
	 * @uses wp_enqueue_style()
	 */
	function admin_enqueue_scripts() {
		wp_enqueue_style( "{$this->namespace}_admin_css", WP_HITTAIL_URLPATH . "/css/admin.css" );
	}
	
	/**
	 * Initialization function to hook into the WordPress init action
	 * 
	 * Instantiates the class on a global variable and sets the class, actions
	 * etc. up for use.
	 */
	static function instance() {
		global $WP_HitTail;
		
		// Only instantiate the Class if it hasn't been already
		if( ! isset( $WP_HitTail ) ) $WP_HitTail = new WP_HitTail();
	}
}

if( !isset( $WP_HitTail ) ) {
	WP_HitTail::instance();
}