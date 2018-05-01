<?php
/**
 * Admin View: Custom Reports
 * @since 1.9
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$ids = ZP_Custom_Reports::get_ids();

if ( $ids ) {

	// @todo do 'Create New Report' button on top right

	?>

	<h2><?php _e( 'Manage Custom Reports', 'zodiacpress' ); ?></h2>
	<div class="stuffbox">
	<div class="inside">

	<table class="widefat">
		<?php

		foreach ($ids as $id) {

			// $name = ZP_Custom_Reports::get_tabs()[ $id ];// @todo


			// @todo list all reports with delete|"edit name"|"edit layout" links.

			?>
			<tr>
				<td class="row-title"><label for="tablecell"><?php echo esc_html( ZP_Custom_Reports::get_tabs()[ $id ] ); ?></label></td>
				<td>

					<a href="<?php echo esc_url( '#@todo' ); ?>" class="button-secondary"><?php _e( 'Edit', 'zodiacpress' ); ?></a> | 
					<a href="<?php echo esc_url( '#@todo' ); ?>" class="button-secondary"><?php _e( 'Delete', 'zodiacpress' ); ?></a>
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
	<button id="zp-create-custom-report" class="zp-BlankState-cta button-primary"><?php _e( 'Create your first custom report', 'zodiacpress' ); ?></button>

	</div>

	<?php

}
