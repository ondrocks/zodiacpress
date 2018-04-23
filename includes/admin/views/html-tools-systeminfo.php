<?php
/**
 * Admin View: Tools - System info
 * @since 1.8
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$active_theme = wp_get_theme();
$active_plugins = (array) get_option( 'active_plugins', array() );
if ( is_multisite() ) {
	$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
}
$wp_plugins = array();
foreach ( $active_plugins as $plugin ) {
	$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
	if ( ! empty( $plugin_data['Name'] ) ) {
		$wp_plugins[] = $plugin_data['Name'] . ' by ' . $plugin_data['AuthorName'] . ' version ' . $plugin_data['Version'];
	}
}
?>
<p><?php _e( 'The system info is a built-in debugging tool. If you contact support, please provide this info.', 'zodiacpress' ); ?></p>
<p><?php _e( '(Do not be afraid to paste this info into the support forum because this info does not reveal your website name or URL.)', 'zodiacpress' ); ?></p>
<form action="<?php echo esc_url( admin_url( 'admin.php?page=zodiacpress-tools&tab=sysinfo' ) ); ?>" method="post" dir="ltr">
<textarea readonly="readonly" onclick="this.focus(); this.select()" id="system-info-textarea" name="zp-sysinfo" title="To copy the system info, click below then press Ctrl + C (on a PC) or Cmd + C (on a Mac).">### Begin System Info ###

-- Server Info

Server Software:          <?php echo $_SERVER['SERVER_SOFTWARE']; ?>

PHP Version:              <?php echo PHP_VERSION; ?>

GD Support:               <?php echo ( ( extension_loaded( 'gd' ) && function_exists( 'gd_info' ) ) ? 'okay' : 'No!' ); ?>

PHP_SHLIB_SUFFIX:         <?php echo PHP_SHLIB_SUFFIX; ?>

exec() Function:          <?php echo ( zp_is_func_enabled( 'exec' ) ? 'okay' : 'Disabled!' ); ?>

chmod() Function:         <?php echo ( zp_is_func_enabled( 'chmod' ) ? 'okay' : 'Disabled!' ); ?>


-- ZodiacPress Info

swetest file:             <?php echo ( file_exists( ZODIACPRESS_PATH . 'sweph/swetest' ) ? 'okay' : 'Missing!' ); ?>

Ephemeris permissions:    <?php echo ( zp_is_sweph_executable() ? 'okay' : 'Not executable!' ); ?>

Atlas:                    <?php if ( ZP_Atlas_DB::is_separate_db() ) {
	echo 'separate database';
} elseif ( ZP_Atlas_DB::use_db() ) {
	echo 'WordPress database';
} else {
	echo 'GeoNames';
} ?>


-- WordPress Info

WP Version:               <?php echo get_bloginfo('version'); ?>

Multisite:                <?php echo ( is_multisite() ? 'Yes' : 'No' ); ?>

WP_DEBUG:                 <?php echo ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ); ?>

WP Memory Limit:          <?php echo WP_MEMORY_LIMIT; ?>


-- Theme

Theme Name:               <?php echo $active_theme->Name; ?>

Theme Version:            <?php echo $active_theme->Version; ?>
	<?php if ( is_child_theme() ) {
		$parent_theme = wp_get_theme( $active_theme->Template ); ?>
	
	Parent Theme Name:        <?php echo $parent_theme->Name; ?>

	Parent Theme Version:     <?php echo $parent_theme->Version;

	} ?>

Is Child Theme:           <?php echo ( empty( $parent_theme ) ? 'No' : 'Yes' ); ?>


-- Active Plugins

<?php if ( ! $wp_plugins ) { ?>
-
<?php } else {
	echo implode( ",\n", $wp_plugins );
} ?>

	
### End System Info ###
</textarea>
</form>
