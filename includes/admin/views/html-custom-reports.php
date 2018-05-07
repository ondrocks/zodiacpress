<?php
/**
 * Admin View: Custom Reports main page: Manage Custom Reports
 * @since 1.9
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$ids = ZP_Custom_Reports::get_ids();
if ( $ids ) {
	?>
	<div class="zp-flex-container">
		<div><h2><?php _e( 'Manage Custom Reports', 'zodiacpress' ); ?></h2></div>
		<div><button id="zp-create-new-report" class="alignright button-primary"><?php _e( 'Create New Report', 'zodiacpress' ); ?></button></div>
	</div>

	<div class="stuffbox">
	<div class="inside">

	<table id="zp-custom-reports-table" class="striped widefat">
		<?php
		foreach ( $ids as $id ) {
			?>
			<tr>
				<td class="row-title"><label for="tablecell"><?php echo $tabs[ $id ]; ?></label></td>
				<td class="zp-custom-reports-col2">
					<a href="#" class="zp-custom-reports-delete zp-error" data-report="<?php echo $id; ?>"><?php _e( 'Delete', 'zodiacpress' ); ?></a>
				</td>
			</tr>
		<?php
		}
		?>
	</table>
	</div>
	</div>

	<?php

} else {
	?>
	<div class="zp-BlankState">
	<h2 class="zp-BlankState-message"><?php echo esc_html__( 'Custom Reports are a great way to offer specialized astrology reports on your site. They will appear here once created.', 'zodiacpress' ); ?></h2>
	<button id="zp-create-new-report" class="zp-BlankState-cta button-primary"><?php _e( 'Create your first custom report', 'zodiacpress' ); ?></button>
	</div>
	<?php
}
