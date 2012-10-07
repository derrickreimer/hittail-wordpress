<?php

//===========================
// Hooks
//===========================

// Initialize plugin admin by registering settings
function ht_register_settings() {
	register_setting('ht_options_group', 'ht_options');
	add_settings_section('ht_code_settings', 'Tracking Code', 'ht_code_settings_text', 'hittail');
	add_settings_field('ht_site_id', 'Site ID', 'ht_option_site_id', 'hittail', 'ht_code_settings');
	add_settings_field('ht_is_disabled', 'Visibility', 'ht_option_is_disabled', 'hittail', 'ht_code_settings');
}

add_action('admin_init', 'ht_register_settings');

// Add plugin settings page to the WP dashboard
function ht_admin_menu() {
	add_options_page('HitTail Settings', 'HitTail', 'manage_options', 'hittail', 'ht_admin_settings');
}

add_action('admin_menu', 'ht_admin_menu');

// Output the plugin settings page
function ht_admin_settings() {
	// Ensure the user has sufficient permissions
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	} 
	?>
	
	<div class="wrap">
		<?php settings_errors(); ?>
		<div id="icon-options-hittail" class="icon32"><br></div>
		<h2>HitTail Settings</h2>
		<form name="ht-settings-form" method="post" action="options.php">
			<?php settings_fields('ht_options_group'); ?>
			<?php do_settings_sections('hittail'); ?>
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="Save Changes">
			</p>
		</form>
	</div>
	
	<?php
}

// Enqueue admin stylesheet
function ht_admin_styles() {
	wp_enqueue_style('ht_admin_css', plugins_url('/css/hittail-admin.css', HT_PLUGIN_BASENAME));
}

add_action('admin_enqueue_scripts', 'ht_admin_styles');

//===========================
// Settings
//===========================

function ht_code_settings_text() {
	echo '<p>Your site ID can be found under Account &rarr; Sites in your HitTail account.</p>';
}

function ht_option_site_id() {
	global $ht_options;
	echo "<input type='text' name='ht_options[site_id]' size='20' value='{$ht_options['site_id']}'>";
}

function ht_option_is_disabled() {
	global $ht_options;
	echo "<input type='checkbox' name='ht_options[is_disabled]' value='1' " . checked(1, $ht_options['is_disabled'], false) . " /> " .
		"Disable tracking code on all pages";
}