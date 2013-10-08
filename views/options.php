<div class="wrap">
	<?php settings_errors(); ?>
	<div id="icon-options-hittail" class="icon32"><br></div>
	<h2>HitTail Settings</h2>
	<form name="ht-settings-form" method="post" action="options.php">
		<?php settings_fields( 'ht_options_group' ); ?>
		<?php do_settings_sections( 'hittail' ); ?>
		<?php submit_button(); ?>
	</form>
</div>