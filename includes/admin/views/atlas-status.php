<?php
/**
 * Admin View: Atlas Status setting
 * @since 1.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$table_exists = ZP_Atlas_DB::table_exists();
$installing = get_option( 'zp_atlas_db_installing' );
$pending_msg = get_option( 'zp_atlas_db_pending' );

$status = __( 'error', 'zodiacpress' );
$class = 'atlas-error';
$checkmark = '';

if ( $pending_msg ) {
	$status = $pending_msg;
} else {
	$status = ( $installing ? zp_string( 'installing' ) : zp_string( 'none' ) );
}

if ( ! $table_exists ) {
	$status = zp_string( 'none' );
} else {
	
	if ( 'db' !== zp_atlas_option() ) {
		$status = __( 'not in use', 'zodiacpress' );
	} else {

		if ( ! $installing && ! $pending_msg ) {

		    // check if table is complete by counting rows & checking keys

		    if (
		    	ZP_Atlas_DB::row_count() > 3000000 &&
		    	ZP_Atlas_DB::key_exists( 'PRIMARY' ) &&
		    	ZP_Atlas_DB::key_exists( 'ix_name_country' )
		    ) {
		    	$status = zp_string( 'active' );
		    	$class = 'success';
		    	$checkmark = ' &#x2713; &nbsp; ';
			}

		}

	}
} // match this also in the js for status field

// Show installer only if the db has not been installed and a custom one is not being used, and it's not currently installing.

if ( ! ZP_Atlas_DB::is_installed() && ! ZP_Atlas_DB::is_separate_db() && ! $installing ) {
	?>
	<div id="zp-atlas-installer">
		<p><?php echo __( 'To create your atlas inside your WordPress database, run the Atlas Installer.', 'zodiacpress' ); ?>	
			<strong><?php _e( 'Skip this to use a separate database.', 'zodiacpress' ); ?></strong></p>
		<p><button id="zp-atlas-install" class="button-primary"><?php _e( 'Run the Atlas Installer', 'zodiacpress' ); ?></button></p>
	</div>
<?php } ?>

<div id="zp-atlas-status" class="stuffbox">
	<div class="inside">
		<h2><?php _e( 'Atlas Status', 'zodiacpress' ); ?></h2>
		<table class="widefat">

			<tr>
				<td><label><?php _e( 'Status', 'zodiacpress' ); ?></label></td>
				<td>
					<span class="zp-<?php echo $class; ?>"> <?php echo $checkmark; ?>
						<?php echo $status; ?>
					</span>
				</td>
			</tr>

			<tr>
				<td><label><?php _e( 'City records count', 'zodiacpress' ); ?></label></td>
				<td>
					<?php 
					if ( $table_exists && ! $installing ) {
						echo number_format( ZP_Atlas_DB::row_count() );
					}
					?>
				</td>
			</tr>

			<tr>
				<td><label><?php _e( 'Database table size', 'zodiacpress' ); ?></label></td>
				<td>
					<?php
					if ( $table_exists && ! $installing ) {
						echo ( $size = zp_atlas_get_size() ) ? ( number_format( $size / 1048576, 1 ) . ' MB' ) : $size;
					}
					?>
				</td>
			</tr>

			<tr>
				<td><label><?php _e( 'Database table primary key', 'zodiacpress' ); ?></label></td>
				<td>
					<?php 
					if ( $table_exists && ! $installing ) {

						echo ZP_Atlas_DB::key_exists( 'PRIMARY' ) ? __( 'okay', 'zodiacpress' ) : __( 'missing', 'zodiacpress' );

					}
					?>
				</td>
			</tr>

			<tr>
				<td><label><?php _e( 'Database table index', 'zodiacpress' ); ?></label></td>
				<td>
					<?php 
					if ( $table_exists && ! $installing ) {

						echo ZP_Atlas_DB::key_exists( 'ix_name_country' ) ? __( 'okay', 'zodiacpress' ) : __( 'missing', 'zodiacpress' );

					}
					?>
				</td>
			</tr>

		</table>

	</div>
</div>