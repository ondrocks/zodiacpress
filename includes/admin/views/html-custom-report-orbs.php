<?php
/**
 * Admin View: Custom Reports: Custom Report tab: Orbs section
 * @since 1.9
 */
if ( ! defined( 'ABSPATH' ) ) exit;
zp_orbs_settings_help_text();
$report = new ZP_Report( $active_tab );
$orbs = $report->get_orbs();

// Get aspect types for this report.
$aspects = array();
$items = $report->get_items();
foreach ( $items as $item ) {
	$item_id = $item[0];
	if ( 'aspects' === ZP_Custom_Reports::get_item_type( $item_id ) ) {
		if ( $type = ZP_Custom_Reports::get_aspect_type( $item_id ) ) {
			$aspects[ $type ] = '';// avoid duplicates
		}
	}
}
$aspects = array_keys( $aspects );
if ( $aspects ) {
	?>

	<form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
		<table class="form-table">
		<?php
		$planets = zp_get_planets();
		$official_aspects = zp_get_aspects();

		foreach ( $aspects as $asp ) {
			$aspect_key	= zp_search_array( $asp, 'id', $official_aspects );
			
			?>
			<tr>
				<th scope="row"><h3><?php echo $official_aspects[ $aspect_key ]['label']; ?></h3></th>
				<td><hr></td>
			</tr>

			<?php
			foreach ( $planets as $p ) {
				$key = $asp . '_' . $p['id'];
				$field = "zp_custom_orbs[$key]";
				$value = isset( $orbs[ $key ] ) ? $orbs[ $key ] : 8;
				?>
				<tr>
					<th scope="row"></th>
					<td>
						<input type="text" class="small-text" id="<?php echo esc_attr( $field ); ?>" name="<?php echo esc_attr( $field ); ?>" value="<?php echo esc_attr( stripslashes( $value ) ); ?>" />
						<label for="<?php echo esc_attr( $field ); ?>"><?php echo esc_html( $p['label'] ); ?></label>
					</td>
				</tr>
				<?php
			}
		}
		?>
		</table>
		
		<input type="hidden" name="action" value="zp-save-custom-orbs" />
		<?php wp_nonce_field( 'save-custom-orbs', 'zp_admin_nonce' ); ?>
		<input type="hidden" name="custom-report-id" value="<?php echo esc_attr( $active_tab ); ?>" />
		<button class="button-primary"><?php _e( 'Save Changes', 'zodiacpress' ); ?></button>
	</form>
<?php
}
