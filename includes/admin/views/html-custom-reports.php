<?php
/**
 * Admin View: Custom Reports
 * @since 1.9
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$ids = ZP_Custom_Reports::get_ids();

isa_log('in html-custom-reports. Any existing custom report ids = ');// @test
isa_log($ids);// @test

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

	 <div id="zp-create-custom-wrap" class="stuffbox">
		<form id="zp-create-custom-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
		<label><?php _e( 'Report Name', 'zodiacpress' ); ?></label>
		<!-- @todo only add id if i will use it in js-->
		<input name="zp-report-name-field" class="medium-text" type="text" required minlength="2" />
		<!-- @todo also validate minlength in php server side -->
		<input type="hidden" name="action" value="zp_create_new_report" />
		<?php wp_nonce_field( 'zp_create_new_report', 'zp_admin_nonce' ); ?>
		<input type="submit" name="submit" class="button-primary" value="<?php _e( 'Create', 'zodiacpress' ); ?>" />
		<a class="zp-error" id="zp-cancel-create" href="#">Cancel</a>
		</form>
	</div>

	<button id="zp-create-new-report" class="zp-BlankState-cta button-primary"><?php _e( 'Create your first custom report', 'zodiacpress' ); ?></button>
	</div>

	<?php

}
