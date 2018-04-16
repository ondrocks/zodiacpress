<?php
/**
 * Admin View: Notice - Install
 * @since 1.8
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="updated zp-atlas-message">
	<p><strong><?php echo __( 'Welcome to ZodiacPress', 'zodiacpress' ); ?></strong> &#8211; 
		<span id="zpatlas-status">
			<?php _e( 'You&lsquo;re almost ready to generate reports.', 'zodiacpress' ); ?></span></p>
	<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=zodiacpress-settings&tab=misc' ) ); ?>" class="button-primary"><?php _e( 'Go to Atlas Setup', 'zodiacpress' ); ?></a> <button id="zp-skip-setup" class="button-secondary"><?php _e( 'Skip setup', 'zodiacpress' ); ?></button></p>
</div>