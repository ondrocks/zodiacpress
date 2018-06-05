<?php
/**
 * ZodiacPress Custom Reports API
 *
 * @package ZodiacPress
 * @since 1.9
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registers custom report items meta boxes for Edit Report pages.
 *
 * @since 1.9
 */
function zp_custom_report_items_setup() {
	$zodiac_boxes = array(
		'sign'	=> __( 'Planet/Point Sign', 'zodiacpress' ),
		'house'	=> __( 'Planet/Point House', 'zodiacpress' ),
		'lord'	=> __( 'House Lord', 'zodiacpress' ),
		'residents' => __( 'House Residents', 'zodiacpress' )
	);
	foreach( $zodiac_boxes as $box => $label ) {
		add_meta_box( "zp-add-zodiac-$box", $label, 'zp_zodiac_meta_box', '', 'side', 'default', array( 'id' => $box ) );
	}
	add_meta_box( 'zp-add-aspects', __( 'Aspects', 'zodiacpress' ), 'zp_aspects_meta_box', '', 'side', 'default' );
	$text_boxes = array(
		'heading' => __( 'Heading', 'zodiacpress' ),
		'subheading' => __( 'Subheading', 'zodiacpress' ),
		'text' => __( 'Text', 'zodiacpress' )
	);
	foreach ( $text_boxes as $box => $label ) {
		add_meta_box( "zp-add-custom-$box", $label, 'zp_custom_text_meta_box', '', 'side', 'default', array( 'id' => $box ) );
	}

}

/**
 * Shows the Custom Reports page.
 *
 * @return      void
 */
function zp_custom_reports_page() {
	$tabs = ZP_Custom_Reports::tabs();
	$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_text_field( $_GET['tab'] ) : 'custom-reports';

	$sections = ( 'custom-reports' === $active_tab ) ? 0 : ZP_Custom_Reports::tab_sections( $active_tab );// no sections for main custom reports tab

	$section = isset( $_GET['section'] ) && ! empty( $sections ) && array_key_exists( $_GET['section'], $sections ) ? sanitize_text_field( $_GET['section'] ) : 'edit';
	?>
	<div class="wrap wrap-<?php echo $active_tab; ?>">
	<?php zp_admin_links(); ?>
	<nav class="nav-tab-wrapper clear">

	<?php
	settings_errors( 'zp-notices' );// @test with orbs

	foreach( $tabs as $tab_id => $tab_name ) {
		$tab_url = add_query_arg( array( 'tab' => $tab_id ) );
		// Remove the section from the tabs so we always end up at the main 'edit' section, and remove the zp-done admin notice flag
		$tab_url = remove_query_arg( array( 'section', 'zp-done' ), $tab_url );

		$active = $active_tab == $tab_id ? ' nav-tab-active' : '';
		?>

		<a href="<?php echo esc_url( $tab_url ); ?>" class="nav-tab<?php echo esc_attr( $active ); ?>">
			<?php echo esc_html( $tab_name ); ?>
		</a>

		<?php
	}
	?>
	</nav>
	<h1 class="screen-reader-text"><?php echo esc_html( $tabs[ $active_tab ] ); ?></h1>

	<?php
	$view = $active_tab;
	$number_of_sections = count( $sections );
	$number = 0;
	if ( $number_of_sections > 1 ) {
		$view = 'custom-report-' . $section;
		?>
		<div><ul class="subsubsub">
			<?php foreach( $sections as $section_id => $section_name ) {
				?>
				<li>
				<?php
				$number++;
				$tab_url = add_query_arg( array(
					'tab' => $active_tab,
					'section' => $section_id
				) );
				$class = '';
				if ( $section == $section_id ) {
					$class = 'current';
				}
				?>
				<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $tab_url ); ?>"><?php echo esc_html( $section_name ); ?></a>
				<?php
				if ( $number != $number_of_sections ) {
					?>
					 | 
					 <?php
				}
				?>
				</li>
				<?php
			}
			?>
		</ul></div>
	<?php }
	?>
	<div id="zp-custom-reports-tab-container" class="<?php echo $view; ?>">
		<?php include ZODIACPRESS_PATH . 'includes/admin/views/html-' . $view . '.php'; ?>
	</div>
	</div><!-- .wrap -->

	<?php
}

/**
 * Processes the 'Create New Custom Report' form.
 */
add_action( 'admin_post_zp_create_new_report', function () {
	check_admin_referer( 'zp_create_new_report', 'zp_admin_nonce' );
	if ( ! current_user_can( 'manage_zodiacpress_settings' ) ) {
		return;
	}

	// validate name
	$name = isset( $_POST['zp-report-name-field'] ) ? sanitize_text_field( $_POST['zp-report-name-field'] ) : '';

	// name must be at least 2 characters
	if ( strlen( utf8_decode( $name ) ) < 2 ) {
		$response = 'cr-fail-length';
	} else {
		$response = ZP_Custom_Reports::create( $name ) ? 'cr-success' : 'cr-fail';
	}
	wp_safe_redirect( admin_url( "admin.php?page=zodiacpress-custom&zp-done=$response" ) ); exit;
} );

/**
 * Processes the 'Delete Report' form.
 */
add_action( 'admin_post_zp_delete_report', function () {
	check_admin_referer( 'zp_delete_report', 'zp_admin_nonce' );
	if ( ! current_user_can( 'manage_zodiacpress_settings' ) ) {
		return;
	}
	if ( isset( $_POST['zp-report-id'] ) ) {
		$name = sanitize_text_field( $_POST['zp-report-id'] );
	} else {
		return;
	}
	$response = ZP_Custom_Reports::delete( $name ) ? 'cr-d' : 'cr-d-fail';
	wp_safe_redirect( admin_url( "admin.php?page=zodiacpress-custom&zp-done=$response" ) ); exit;
} );


/**
 * Ajax handler that updates a report when 'Save Report' is clicked.
 */
add_action( 'wp_ajax_zp_update_report', function () {
	check_ajax_referer( 'update-zp_report', 'update-report-nonce' );
	if ( ! current_user_can( 'manage_zodiacpress_settings' ) ) {
		return;
	}
	$data = array();

	// decode data
	$decoded = json_decode( stripslashes( $_POST['report-data'] ) );
	foreach ( $decoded as $item ) {
		// For index names that are arrays (e.g. `report-item-title[moon_sign]`),
		// derive the array path keys via regex and build new array.
		preg_match( '#([^\[]*)(\[(.+)\])?#', $item->name, $matches );
		if ( isset( $matches[3] ) ) {
			$data[ $matches[1] ][] = $item->value;
		} else {
			$data[ $item->name ] = $item->value;
		}
	}

	$report_id = sanitize_text_field( $data['report'] );
	$report_title = sanitize_text_field( $data['report-name'] );
	$old_title = ZP_Custom_Reports::tabs()[ $report_id ];

	if ( ! $report_title ) {

		wp_send_json_error();

	} elseif ( $old_title !== $report_title ) {

		$update_name = ZP_Custom_Reports::update_name( $report_id, $report_title );
		if ( true === $update_name ) {
			set_transient( 'zodiacpress_report_update', true, 5 );
		} else {
			wp_send_json_error();
		}

	}

	// update items if changed

	// build items array
	$items = array();
	if ( isset( $data['report-item-id'][0] ) ) {
		foreach ( $data['report-item-id'] as $key => $id ) {
			// Get item type because textarea is sanitized differently
			if ( 'text' === ZP_Custom_Reports::get_item_type( $id ) ) {
				$text = wp_kses_post( $data['report-item-title'][ $key ] );
			} else {
				$text = sanitize_text_field( $data['report-item-title'][ $key ] );
			}

			$items[] = array( sanitize_text_field( $id ), $text );
		}
	}
	$report = new ZP_Report( $report_id );
	$old_items = $report->get_items();
	if ( $old_items !== $items ) {
		if ( true === $report->update_items( $items ) ) {
			set_transient( 'zodiacpress_report_update', true, 5 );
		} else {
			wp_send_json_error();
		}
	}
	
	wp_send_json_success();

} );

/**
 * Displays the HTML list content for custom report items.
 * @param array    $items The report items
 * @return string The HTML list of checkbox items for the report items.
 */
function zp_reports_list_items_html( $items ) {
	foreach( $items as $id => $label ) {
		?>
		<li>
			<label class="report-item-title">
				<input type="checkbox" class="report-item-checkbox" name="report-item[<?php echo esc_attr( $id ); ?>][report-item-id]" value="<?php echo esc_attr( $id ); ?>" />
				<?php echo esc_html( $label ); ?>
			</label>
			<input type="hidden" class="report-item-title" name="report-item[<?php echo esc_attr( $id ); ?>][report-item-title]" value="<?php echo esc_attr( $label ); ?>" />
		</li>
		<?php
	}
}

/**
 * Displays Sign/House/Lord/Residents items meta boxes for Edit Custom Reports pages
 * @param string $object Not used.
 * @param array
 */
function zp_zodiac_meta_box( $object, $box ) {
	$box_name = esc_attr( 'zodiac_' . $box['args']['id'] ); // sign/house/lord/residents
	$listitems = ZP_Custom_Reports::listitems( $box['args']['id'] );
	?>
	<div id="zodiac-<?php echo $box_name; ?>" class="zodiacdiv">
		<div id="tabs-panel-<?php echo $box_name; ?>" class="tabs-panel tabs-panel-active">
		<ul id="<?php echo $box_name; ?>checklist" class="categorychecklist form-no-clear">
			<?php zp_reports_list_items_html( $listitems ); ?>
		</ul>
		</div> <!-- /.tabs-panel -->
		
		<p class="button-controls wp-clearfix">
			<span class="add-to-report">
				<input type="submit" class="button submit-add-to-report right" value="<?php _e( 'Add to Report', 'zodiacpress' ); ?>" id="<?php echo 'submit-zodiac-' . $box_name; ?>" />
				<span class="spinner"></span>
			</span>
		</p>

	</div>
	<!-- .zodiacdiv -->

	<?php

}

/**
 * Displays Aspects meta box for the Edit Custom Reports pages
 * @param string $object Not used.
 * @param array
 */
function zp_aspects_meta_box( $object, $box ) {
	$listitems = ZP_Custom_Reports::listitems( 'aspects' );
	?>
	<div id="aspects" class="aspectsdiv">
		<ul id="aspects-tabs" class="aspects-tabs add-report-item-tabs">
			<li class="tabs">
				<a class="aspects-tab-link" data-type="aspects-all" href="#aspects-all">
					<?php _e( 'View All', 'zodiacpress' ); ?>
				</a>
			</li>
			<li>
				<a class="aspects-tab-link" data-type="tabs-panel-aspects-search" href="#tabs-panel-aspects-search">
					<?php _e( 'Search', 'zodiacpress' ); ?>
				</a>
			</li>
		</ul><!-- .aspects-tabs -->

		<div id="aspects-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
			<ul id="aspectschecklist" class="categorychecklist form-no-clear">
				<?php zp_reports_list_items_html( $listitems ); ?>
			</ul>
		</div> <!-- /.tabs-panel -->

		<div class="tabs-panel tabs-panel-inactive" id="tabs-panel-aspects-search">
			<p class="quick-search-wrap">
				<label for="quick-search-zp-aspects" class="screen-reader-text"><?php _e( 'Search', 'zodiacpress' ); ?></label>
				<input type="search" class="quick-search" value="" name="quick-search-zp-aspects" id="quick-search-zp-aspects" />
				<span class="spinner"></span>
				<?php submit_button( __( 'Search', 'zodiacpress' ), 'small quick-search-submit hide-if-js', 'submit', false, array( 'id' => 'submit-quick-search-zp-aspects' ) ); ?>
			</p>
			<ul id="aspects-search-checklist" class="categorychecklist form-no-clear"></ul>
		</div><!-- /.tabs-panel -->

		<p class="button-controls wp-clearfix">
			<span class="add-to-report">
				<input type="submit" class="button submit-add-to-report right" value="<?php esc_attr_e( 'Add to Report', 'zodiacpress' ); ?>" id="<?php echo esc_attr( 'submit-aspects' ); ?>" />
				<span class="spinner"></span>
			</span>
		</p>
	</div><!-- /.aspectsdiv -->
	<?php

}

/**
 * Displays Heading/Subheading/Text meta boxes for the Edit Custom Reports pages
 * @param string $object Not used.
 * @param array
 */
function zp_custom_text_meta_box( $object, $box ) {
	$box_name = esc_attr( $box['args']['id'] );
	?>
	<div class="zpcustomdiv" id="zpcustom<?php echo $box_name; ?>">
		<input type="hidden" name="report-item[<?php echo $box_name; ?>][report-item-id]" value="<?php echo $box_name; ?>" />
		<p id="<?php echo $box_name; ?>-item-wrap" class="zpcustom-item-wrap wp-clearfix">
			<label class="screen-reader-text" for="custom-<?php echo $box_name; ?>-item"><?php _e( 'Text', 'zodiacpress' ); ?></label>
			<?php if ( 'text' == $box_name ) { ?>

				 <textarea id="custom-<?php echo $box_name; ?>-item" rows="3" cols="20" name="report-item[<?php echo $box_name; ?>][report-item-title]"></textarea>

			<?php } else { ?>

				<input id="custom-<?php echo $box_name; ?>-item" name="report-item[<?php echo $box_name; ?>][report-item-title]" type="text" class="regular-text report-item-textbox" />

			<?php } ?>
			
		</p>

		<p class="button-controls wp-clearfix">
			<span class="add-to-report">
				<input type="submit" class="button submit-add-to-report right" value="<?php _e( 'Add to Report', 'zodiacpress' ); ?>" id="submit-zpcustom<?php echo $box_name; ?>" />
				<span class="spinner"></span>
			</span>
		</p>

	</div><!-- /.zpcustomdiv -->

	<?php
}

/**
 * Ajax handler that adds report item in the Edit Reports view.
 */
function zp_ajax_add_report_item() {
	check_ajax_referer( 'add-report_item', 'zp-edit-report-column-nonce' );
	if ( ! current_user_can( 'manage_zodiacpress_settings' ) ) {
		wp_die( -1 );
	}
	$items = array();
	foreach ( (array) $_POST['report-item'] as $data ) {
		$items[] = array( $data['report-item-id'], $data['report-item-title'] );
	}
	echo ZP_Custom_Reports::get_edit_html_items( $items, true );
	wp_die();
}
add_action( 'wp_ajax_add-report-item', 'zp_ajax_add_report_item', 1 );

/**
 * Ajax handler for Custom Reports aspects-items quick searching.
 */
function zp_ajax_aspects_quick_search() {
	check_ajax_referer( 'add-report_item', 'zp-edit-report-column-nonce' );
	if ( ! current_user_can( 'manage_zodiacpress_settings' ) ) {
		wp_die( -1 );
	}
	if ( empty( $_REQUEST['q'] ) ) {
		wp_die();
	} else {
		$query = $_REQUEST['q'];		
	}
	$listitems = ZP_Custom_Reports::listitems( 'aspects' );
	$matches = array_filter( $listitems, function ( $haystack ) use ( $query ) {
	    return( false !== stripos( $haystack, $query ) );
	} );
	zp_reports_list_items_html( $matches );
	wp_die();
}
add_action( 'wp_ajax_zp-aspects-quick-search', 'zp_ajax_aspects_quick_search', 1 );

/**
 * Adds a Technical Settings section for each Custom Report.
 */
function zp_custom_reports_tech_settings( $settings ) {
	$ids = ZP_Custom_Reports::get_ids();
	if ( ! $ids ) {
		return $settings;
	}
	foreach ( $ids as $id ) {
		$settings["cr$id"]['technical'] = array(
					'tech_settings' => array(
						'id'	=> 'tech_settings',
						'name'	=> '<h3>' . __( 'Technical Settings', 'zodiacpress' ) . '</h3>',
						'desc'	=> '',
						'type'	=> 'header',
					),
					"{$id}_allow_unknown_bt" => array(
						'id'	=> "{$id}_allow_unknown_bt",
						'name'	=> __( 'Allow Unknown Birth Time', 'zodiacpress' ),
						'type'	=> 'checkbox',
						'desc'	=> __( 'Allow people with unknown birth times to get this custom report. If enabled, this will allow them to generate a report, excluding items that require a birth time (i.e. excluding Houses, House Lords, Moon, Ascendant, Midheaven, Vertex, and Part of Fortune).', 'zodiacpress' ),
						'class' => 'zp-setting-checkbox-label'
					),
		);
	}
	return $settings;
}
add_filter( 'zp_registered_settings', 'zp_custom_reports_tech_settings' );
