<?php
/**
 * Admin View: Custom Reports: Custom tab: Technical section
 * @since 1.9
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<form method="post" action="options.php">
	<?php
	settings_fields( 'zodiacpress_settings' );

	do_settings_sections( 'zodiacpress_settings_cr' . $active_tab . '_technical' );
	submit_button(); ?>
</form>