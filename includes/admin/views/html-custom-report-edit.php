<?php
/**
 * Admin screen for Custom Reports: Edit Report page
 *
 * @since 1.9
 * @package ZodiacPress
 */
wp_enqueue_style( 'zp-edit-report' );
wp_enqueue_script( 'zp-edit-report' );

// @test if needed for mobile

// if ( wp_is_mobile() ) {
// 	wp_enqueue_script( 'jquery-touch-punch' );
// }

$report_id = $active_tab;

// Show update success notice
$message = '';
if ( get_transient( 'zodiacpress_report_update' ) ) {
	delete_transient( 'zodiacpress_report_update' );
	$message = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Report updated.', 'zodiacpress' ) . '</p></div>';
}

wp_localize_script( 'zp-edit-report', 'reports', array(
	'moveUp'			=> __( 'Move up one', 'zodiacpress' ),
	'moveDown'			=> __( 'Move down one', 'zodiacpress' ),
	'moveToTop'			=> __( 'Move to the top', 'zodiacpress' ),
	/* translators: 1: item name, 2: item position, 3: total number of items */
	'reportFocus'		=> __( '%1$s. Rerport item %2$d of %3$d.', 'zodiacpress' ),
	'noResultsFound'	=> __( 'No results found.', 'zodiacpress' ),
) );

$report = new ZP_Report( $report_id );
$report_items = $report->get_items();
$edit_markup = $report->get_edit_markup();

zp_custom_report_items_setup();
?>
<div class="wrap">
	<?php echo $message; ?>
	<div id="zp-edit-reports-frame" class="wp-clearfix">
	
	<div id="report-settings-column" class="metabox-holder">
		<div class="clear"></div>
		<form id="zp-report-meta" class="zp-report-meta" method="post">
			<input type="hidden" name="action" value="add-report-item" />
			<?php wp_nonce_field( 'add-report_item', 'zp-edit-report-column-nonce' ); ?>
			<h2><?php _e( 'Add report items', 'zodiacpress' ); ?></h2>
			<?php do_accordion_sections( '', 'side', null ); ?>
		</form>
	</div><!-- /#report-settings-column -->

	<div id="zp-report-management-liquid">
		<div id="zp-report-management">
			<form id="update-zp-report" method="post">
				<h2><?php _e( 'Report structure', 'zodiacpress' ); ?></h2>
				<div class="report-edit">
					<?php
					wp_nonce_field( 'update-zp_report', 'update-zp-report-nonce' );
					?>
					<input type="hidden" name="report" id="report" value="<?php echo esc_attr( $report_id ); ?>" />
					<div id="zp-report-header">
						<div class="major-publishing-actions wp-clearfix">
							<label class="report-name-label" for="report-name"><?php _e( 'Report Name', 'zodiacpress' ); ?></label>
							<input name="report-name" id="report-name" type="text" class="regular-text report-item-textbox" value="<?php echo esc_attr( $tabs[ $active_tab ] ) ;?>" />
							<div class="publishing-action">
								<?php
								submit_button( __( 'Save Report', 'zodiacpress' ), 'primary large report-save', 'save_report', false, array( 'id' => 'save_report_header' ) );
								?>
							</div><!-- END .publishing-action -->
						</div><!-- END .major-publishing-actions -->
					</div><!-- END #zp-report-header -->

					<div id="post-body">
						<div id="post-body-content" class="wp-clearfix">
							<?php
							$hide_style = '';
							if ( ! $report_items || 0 == count( $report_items ) ) {
								$hide_style = 'style="display: none;"';
							}
							?>
							<div class="drag-instructions" <?php echo $hide_style; ?>>
								<p><?php _e( 'Drag each item into the order you prefer. Click the arrow on the right of the item to reveal additional configuration options.', 'zodiacpress' ); ?></p>
							</div>
							<?php
							if ( $edit_markup ) {
								echo $edit_markup;
							} else {
								?>
								<ul id="report-to-edit" class="report"></ul>
							<?php } ?>

						</div><!-- /#post-body-content -->
					</div><!-- /#post-body -->

					<div id="zp-report-footer">
						<div class="major-publishing-actions wp-clearfix">
							<div class="publishing-action">
								<?php
								submit_button( __( 'Save Report', 'zodiacpress' ), 'primary large report-save', 'save_report', false, array( 'id' => 'save_report_footer' ) );
								?>
							</div><!-- END .publishing-action -->
						</div><!-- END .major-publishing-actions -->
					</div><!-- /#zp-report-footer -->
				</div><!-- /.report-edit -->
			</form><!-- /#update-zp-report -->
		</div><!-- /#zp-report-management -->
	</div><!-- /#zp-report-management-liquid -->
	</div><!-- /#zp-edit-reports-frame -->

</div><!-- /.wrap-->
