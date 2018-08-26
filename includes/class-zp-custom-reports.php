<?php
/** @test can this file be moved to /includes/admin/....
 * @todo Possibly rename class to ZP_Custom_Reports_Settings, and move to /includes/admin/settings/
 *
 * ZodiacPress ZP_Custom_Reports class.
 *
 * Manages the Custom Reports settings.
 *
 * @package  ZodiacPress
 * @since 1.9
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class ZP_Custom_Reports {
	private static $custom_ids;
	private static $zp_settings;
	private static $tabs;
	private static $items_sign;
	private static $items_house;
	private static $items_lord;
	private static $items_residents;
	private static $items_aspects;

	/**
	 * Gets the tab names for the Custom Reports admin page.
	 * @return array
	 */
	public static function tabs() {
		if ( isset( self::$tabs ) ) {
			return self::$tabs;
		}
		$tabs = array( 'custom-reports' => __( 'Custom Reports', 'zodiacpress' ) );

		if ( isset( self::$zp_settings ) ) {
			$options = self::$zp_settings;
		} else {
			$options = get_option( 'zodiacpress_settings' );
			self::$zp_settings = $options;
		}
		if ( isset( $options['custom_reports'] ) ) {

			foreach( $options['custom_reports'] as $id => $data ) {
				$tabs[ $id ] = $data['name'];
			}
		}
		self::$tabs = $tabs;
		return $tabs;
	}

	/**
	 * Gets the sections for a custom reports tab.
	 * 
	 * This is used by all custom report tabs, except the first 'Manage' tab.
	 */
	public static function tab_sections( $tab ) {
		$sections['edit'] = __( 'Edit Report', 'zodiacpress');
		$report = new ZP_Report( $tab );
		$items = $report->get_items();

		// Add Interpretations section only if this report has an item that needs it
		$need_interps = array( 'sign', 'house', 'lord', 'residents', 'aspects' );
		foreach ( $items as $item ) {
			if ( in_array( ZP_Custom_Reports::get_item_type( $item[0] ), $need_interps ) ) {// @test now now
				$sections['interpretations'] = __( 'Interpretations', 'zodiacpress');
				break;
			}
		}

		/* Add Orbs section only if this report includes any aspects */
		foreach ( $items as $item ) {
			// @test now now
			if ( 'aspects' === ZP_Custom_Reports::get_item_type( $item[0] ) ) {
				$sections['orbs'] = __( 'Orbs', 'zodiacpress');
				break;
			}
		}

		$sections['technical'] = __( 'Technical', 'zodiacpress' );

		return $sections;
	}

	/**
	 * Gets a list of identifiers for existing custom reports.
	 *
	 * @return array List of IDs
	 */
	public static function get_ids() {
		if ( isset( self::$custom_ids ) ) {
			return self::$custom_ids;
		}
		if ( isset( self::$zp_settings ) ) {
			$options = self::$zp_settings;
		} else {
			$options = get_option( 'zodiacpress_settings' );
			self::$zp_settings = $options;
		}
		self::$custom_ids = isset( $options['custom_reports'] ) ? array_keys( $options['custom_reports'] ) : array();
		return self::$custom_ids;
	}

	/**
	 * Checks if a report ID exists among all custom and core reports
	 * 
	 * @return bool
	 */
	private static function exists( $id ) {
		$core_ids = array( 'birthreport', 'birthreport_preview', 'drawing', 'house_systems' );
		foreach ( zp_get_planets() as $p ) {
			$core_ids[] = 'planet_lookup_' . $p['id'];
		}
		return ( in_array( $id, self::get_ids() ) || in_array( $id, $core_ids ) );
	}

	/**
	 * Creates a new custom report
	 * 
	 * @return bool $update Whether new report was created
	 */
	public static function create( $name ) {
		// Make a 13-character ID from name
		$id = substr( sanitize_key( $name ), 0, 13 );

		// If this report ID already exists, create a unique report ID
		if ( self::exists( $id ) ) {
			$suffix = 1;
			do {
				$unique_id = $id . $suffix;
				$suffix ++;
			} while ( self::exists( $unique_id ) );
			$id = $unique_id;
		}

		// Save the new report id to db
		$options = self::$zp_settings;
		$options['custom_reports'][ $id ] = array( 'name' => $name, 'items' => array() );
		$update = update_option( 'zodiacpress_settings', $options );
		return $update;
	}

	/**
	 * Deletes a custom report ID 
	 * 
	 * @return bool $deleted Whether report was deleted
	 */
	public static function delete( $id ) {
		// delete the report id from db
		if ( isset( self::$zp_settings ) ) {
			$options = self::$zp_settings;
		} else {
			$options = get_option( 'zodiacpress_settings' );
			self::$zp_settings = $options;
		}
		unset( $options['custom_reports'][ $id ] );
		$deleted = update_option('zodiacpress_settings', $options);
		return $deleted;
	}

	/**
	 * Updates the name for a custom report
	 * @return mixed|bool|string True if name was updated, error message if name is already used for another report, false is report id doesn't exist or other error.
	 */
	public static function update_name( $id, $name ) {
		$options = self::$zp_settings;
		if ( ! isset( $options['custom_reports'] ) || ! isset( $options['custom_reports'][ $id ] ) ) {
			return false;
		}
		// check that new name is unique
		if ( zp_search_array( $name, 'name', $options['custom_reports'] ) ) {
			// not unique so return an error msg. This string is not currently used.
			return sprintf( __( 'The report name %s conflicts with another report name. Please try another.', 'zodiacpress' ),
			'<strong>' . esc_html( $name ) . '</strong>' );

		}
		// Update the new report name
		$options['custom_reports'][ $id ]['name'] = $name;
		$update = update_option( 'zodiacpress_settings', $options );
		self::$zp_settings = $options;
		return $update;
	}

	/**
	 * Returns a list of available Custom Reports items
	 * @param string $type Whether sign, house, lord, residents, aspects
	 */
	public static function listitems( $type ) {
		if ( isset( self::${"items_${type}"} ) ) {
			return self::${"items_${type}"};
		}
		$out = array();
		$uctype = ucwords( $type );

		switch ( $type ) {
			case 'sign':
			case 'house':
				$houses = 'house' === $type ? true : false;
				$planets = zp_get_planets( $houses );
				foreach( $planets as $p ) {
					$out[ $p['id'] . '_' . $type ] = $p['label'] . ' ' . $uctype;
				}
				break;
			case 'lord':
				$label_i18n = __( '%s of House %d', 'zodiacpress' );
				for ( $i=1; $i < 13; $i++ ) {
					$out[ $i . '_' . $type ] = sprintf( $label_i18n, $uctype, $i );
				}
				break;
			case 'residents':
				$label_i18n = __( 'House %d %s', 'zodiacpress' );
				for ( $i=1; $i < 13; $i++ ) {
					$out[ $i . '_' . $type ] = sprintf( $label_i18n, $i, $uctype );
				}
				break;
			case 'aspects':
				$planets = zp_get_planets();
				$aspects = zp_get_aspects(2);
				foreach ( $planets as $p ) {
					foreach( $aspects as $asp ) {
						$out[ $p['id'] . '_' . $asp['id'] . '_' . $type ] = $p['label'] . ' ' . $asp['label'];
					}
				}
				break;
		}
		self::${"items_${type}"} = $out;
		return $out;
	}

	/**
	 * Get the type of a custom report item.
	 *
	 * @return string|bool $type Either house, sign, residents, lord, aspects, 
	 * 			heading, subheading, or text, or FALSE for unrecognized type.
	 */
	public static function get_item_type( $item_id ) {
		$pos = strrpos( $item_id, '_' );
		if ( false !== $pos ) {
			return substr( $item_id, $pos + 1 );
		}
		return false;
	}

	/**
	 * Get the type of aspect of a custom report aspect item.
	 * @return string|bool $type The aspect type, or FALSE for unrecognized item.
	 */
	public static function get_aspect_type( $aspect_item_id ) {
		$frag = str_replace( '_aspects', '', $aspect_item_id );
		$pos = strpos( $frag, '_' );
		if ( false !== $pos ) {
			return substr( $frag, $pos + 1);
		}
		return false;
	}

	/** @test do i even need this function?
	 * Split aspect item into `array( $planet id, $aspect id )`
	 */
	public static function split_aspect_item( $item ) {
    	return explode( '_', $item );
	}

	/**
	 * Gets the draggable HTML list elements for the specified report items.
	 */
	public static function get_edit_html_items( $items, $pending = false ) {
		$out = '';

		foreach ( $items as $item ) {
			$item_id = esc_attr( $item[0] );
			$classes = array(
				'report-item',
				'report-item-edit-inactive',
			);

			if ( $pending ) {
				$classes[] = 'pending';
			}

			$type = ZP_Custom_Reports::get_item_type( $item_id );
			if ( false === $type ) {
				continue;// skip unrecognized item
			}

			$official_title = in_array( $type, array( 'heading', 'subheading', 'text' ) ) ? ucwords( $type ) : self::listitems( $type )[ $item_id ];
			$title = ( ! isset( $item[1] ) || '' == $item[1] ) ? $official_title : $item[1];
			$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';

			// Markup for each item...
			ob_start();
			?>

			<li id="report-item-<?php echo $item_id; ?>" class="<?php echo implode( ' ', $classes ); ?>">
				<div class="report-item-bar">
					<div class="report-item-handle">
						<span class="item-title"><span class="report-item-title"><?php
							if ( 'text' !== $type ) {
								echo esc_html( $title );
							} ?>
						</span></span>

						<span class="item-controls">
							<span class="item-type"><?php echo esc_html( $official_title ); ?></span>
							<a class="item-edit" id="edit-<?php echo $item_id; ?>" href="<?php echo admin_url( 'admin.php?page=zodiacpress-custom&tab=' . $tab . '#report-item-settings-' . $item_id ); ?>" aria-label="<?php esc_attr_e( 'Edit report item', 'zodiacpress' ); ?>"><span class="screen-reader-text"><?php _e( 'Edit', 'zodiacpress' ); ?></span></a>
						</span>
					</div>
				</div>

				<div class="report-item-settings wp-clearfix" id="report-item-settings-<?php echo $item_id; ?>">

					<input class="report-item-data-object-id" type="hidden" name="report-item-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item_id ); ?>" />

					<?php if ( 'text' == $type ) { ?>
						<p class="description description-wide">
							<label for="edit-report-item-title-<?php echo $item_id; ?>">
								<?php _e( 'Text' ); ?><br />
								<textarea id="edit-report-item-title-<?php echo $item_id; ?>" class="widefat " rows="3" cols="20" name="report-item-title[<?php echo $item_id; ?>]"><?php echo esc_textarea( stripslashes( $title ) ); ?></textarea>
							</label>
						</p>

					<?php } else { ?>

						<p class="description description-wide">
							<label for="edit-report-item-title-<?php echo $item_id; ?>">
								<?php if ( 'heading' != $type && 'subheading' != $type ) {
									_e( 'Label', 'zodiacpress' ); ?><br />
								<?php } ?>
								<input type="text" id="edit-report-item-title-<?php echo $item_id; ?>" class="widefat edit-report-item-title" name="report-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $title ); ?>" />
							</label>
						</p>

					<?php } ?>

					<fieldset class="field-move hide-if-no-js description description-wide">
						<span class="field-move-visual-label" aria-hidden="true"><?php _e( 'Move', 'zodiacpress' ); ?></span>
						<button type="button" class="button-link reports-move reports-move-up" data-dir="up"><?php _e( 'Up one', 'zodiacpress' ); ?></button>
						<button type="button" class="button-link reports-move reports-move-down" data-dir="down"><?php _e( 'Down one', 'zodiacpress' ); ?></button>
						<button type="button" class="button-link reports-move reports-move-top" data-dir="top"><?php _e( 'To the top', 'zodiacpress' ); ?></button>
					</fieldset>

					<div class="report-item-actions description-wide submitbox">
						<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="#"><?php _e( 'Remove', 'zodiacpress' ); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>" href="#"><?php _e( 'Cancel', 'zodiacpress' ); ?></a>
					</div>

				</div><!-- .report-item-settings-->
			</li>
			<?php
			$out .= ob_get_clean();
		}

		return $out;

	}

}
