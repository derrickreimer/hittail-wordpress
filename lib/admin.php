<?php

class WP_HitTail_Admin {
	protected $options;
	
	public function __construct() {
		// Fetch options
		$this->options = get_option( 'ht_options' );
		
		// Add actions
		$this->add_actions();
	}
	
	// Lookup an option and return NULL unless it is set and non-empty
	public function get_option( $key ) {
		if ( isset( $this->options[ $key ] ) && $this->options[ $key ] != "" ) {
			return $this->options[ $key ];
		} else {
			return NULL;
		}
	}
	
	public function add_actions() {
		// Register settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		
		// Setup settings page
		add_action( 'admin_menu', array( $this, 'setup_settings_page' ) );
		
		// Enqueue styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' )  );
	}
	
	public function register_settings() {
		register_setting( 'ht_options_group', 'ht_options' );
		add_settings_section( 'ht_code_settings', 'Tracking Code', array( $this, 'display_section_code_settings' ), 'hittail' );
		add_settings_field( 'ht_site_id', 'Site ID', array( $this, 'display_option_site_id' ), 'hittail', 'ht_code_settings' );
		add_settings_field( 'ht_is_disabled', 'Visibility', array( $this, 'display_option_is_disabled' ), 'hittail', 'ht_code_settings' );
	}
	
	public function enqueue_styles() {
		wp_enqueue_style( 'ht_admin_css', plugins_url( '/css/hittail-admin.css', HT_PLUGIN_BASENAME ) );
	}
	
	public function setup_settings_page() {
		add_options_page( 'HitTail Settings', 'HitTail', 'manage_options', 'hittail', array( $this, 'display_settings_page' ) );
	}
	
	public function display_settings_page() {
		// Ensure the user has sufficient permissions
		if ( ! current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		} 
		?>

		<div class="wrap">
			<?php settings_errors(); ?>
			<div id="icon-options-hittail" class="icon32"><br></div>
			<h2>HitTail Settings</h2>
			<form name="ht-settings-form" method="post" action="options.php">
				<?php settings_fields( 'ht_options_group' ); ?>
				<?php do_settings_sections( 'hittail' ); ?>
				<p class="submit">
					<input type="submit" name="submit" class="button-primary" value="Save Changes">
				</p>
			</form>
		</div>

		<?php
	}
	
	public function display_option_site_id() {
		echo "<input type='text' name='ht_options[site_id]' size='20' value='{$this->get_option( 'site_id' )}'>";
	}
	
	public function display_option_is_disabled() {
		echo "<input type='checkbox' name='ht_options[is_disabled]' value='1' " . 
			checked( 1, $this->get_option( 'is_disabled' ), false ) . " /> " .
			"Disable tracking code on all pages";
	}
	
	public function display_section_code_settings() {
		echo '<p>Your site ID can be found under Account &rarr; Sites in your HitTail account.</p>';
	}
}

$wp_hittail_admin = new WP_HitTail_Admin();
