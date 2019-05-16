<?php
/**
 * Register Settings
 *
 * @package     ZodiacPress
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add all settings sections and fields
 *
 * @return void
*/
function zp_register_settings() {
	if ( false == get_option( 'zodiacpress_settings' ) ) {
		add_option( 'zodiacpress_settings' );
	}

	foreach ( zp_get_registered_settings() as $tab => $sections ) {
		foreach ( $sections as $section => $settings) {

			add_settings_section(
				'zodiacpress_settings_' . $tab . '_' . $section,
				__return_null(),
				'__return_false',
				'zodiacpress_settings_' . $tab . '_' . $section
			);

			foreach ( $settings as $option ) {

				$name = isset( $option['name'] ) ? $option['name'] : '';
				add_settings_field(
					'zodiacpress_settings[' . $option['id'] . ']',
					$name,
					function_exists( 'zp_' . $option['type'] . '_callback' ) ? 'zp_' . $option['type'] . '_callback' : 'zp_missing_callback',
					'zodiacpress_settings_' . $tab . '_' . $section,
					'zodiacpress_settings_' . $tab . '_' . $section,
					array(
						'section'     => $section,
						'id'          => isset( $option['id'] )          ? $option['id']          : null,
						'desc'        => ! empty( $option['desc'] )      ? $option['desc']        : '',
						'name'        => isset( $option['name'] )        ? $option['name']        : null,
						'size'        => isset( $option['size'] )        ? $option['size']        : null,
						'options'     => isset( $option['options'] )     ? $option['options']     : '',
						'std'         => isset( $option['std'] )         ? $option['std']         : '',
						'class'		=> isset( $option['class'] )	? $option['class'] : '',
					)
				);
			}
		}

	}

	register_setting( 'zodiacpress_settings', 'zodiacpress_settings', 'zp_settings_sanitize' );
}
add_action( 'admin_init', 'zp_register_settings' );

/**
 * Retrieve the array of plugin settings
 *
 * @return array
*/
function zp_get_registered_settings() {
	/**
	 * 'Whitelisted' ZP settings, filters are provided for each settings
	 * section to allow extensions to add their own settings
	 */
	$zp_settings = array(
		/** Natal Settings */
		'natal' => apply_filters( 'zp_settings_natal',
			array(
				'main' => array(
					'planet_settings' => array(
						'id'	=> 'planet_settings',
						'name'	=> '<h3>' . __( 'Planets and Points Settings', 'zodiacpress' ) . '</h3>',
						'desc'	=> '',
						'type'	=> 'header',
					),
					'enable_planet_signs' => array(
						'id'		=> 'enable_planet_signs',
						'name'		=> __( 'Enable Planets (and Points) in Signs', 'zodiacpress' ),
						'desc'		=> __( 'Choose which to show on the "In The Signs" section of the report.', 'zodiacpress' ),
						'type'		=> 'multicheck',
						'options'	=> zp_get_planets(),
						'std'		=> zp_get_planets( false, 7 )
					),
					'enable_planet_houses' => array(
						'id'		=> 'enable_planet_houses',
						'name'		=> __( 'Enable Planets (and Points) in Houses', 'zodiacpress' ),
						'desc'		=> __( 'Choose which to show on the "In The Houses" section of the report.', 'zodiacpress' ),
						'type'		=> 'multicheck',
						'options'	=> zp_get_planets( true ),
						'std'		=> zp_get_planets( true )
					),
				),
				'aspects' => array(
					'aspects_settings' => array(
						'id'	=> 'aspects_settings',
						'name'	=> '<h3>' . __( 'Aspects Settings', 'zodiacpress' ) . '</h3>',
						'desc'	=> '',
						'type'	=> 'header',
					),
					'enable_aspects' => array(
						'id'		=> 'enable_aspects',
						'name'		=> __( 'Enable Aspects', 'zodiacpress' ),
						'desc'		=> __( 'Choose which aspects to show on the report.', 'zodiacpress' ),
						'type'		=> 'multicheck',
						'options'	=> zp_get_aspects(),
						'std'		=> zp_get_aspects()
					),
					'enable_planet_aspects' => array(
						'id'		=> 'enable_planet_aspects',
						'name'		=> __( 'Aspects To Planets (and Points)', 'zodiacpress' ),
						'desc'		=> __( 'Choose which planets/points to calculate aspects for.', 'zodiacpress' ),
						'type'		=> 'multicheck',
						'options'	=> zp_get_planets(),
						'std'		=> zp_get_planets( false, 10 )
					),
				),
				'report'	=> array(
					'report_settings' => array(
						'id'	=> 'report_settings',
						'name'	=> '<h3>' . __( 'Display Settings', 'zodiacpress' ) . '</h3>',
						'desc'	=> '',
						'type'	=> 'header',
					),
					'add_drawing_to_birthreport' => array(
								'id'	=> 'add_drawing_to_birthreport',
								'name'	=> __( 'Add Chart Wheel To Natal Report', 'zodiacpress' ),
								'type'	=> 'select',
								'desc'	=> __( 'Would you like to add the chart drawing to the birth report?', 'zodiacpress' ),
								'options'	=> array(
									'no' => __( 'do not add', 'zodiacpress' ),
									'bottom' => __( 'add to bottom of report', 'zodiacpress' ),
									'top' => __( 'add to top of report', 'zodiacpress' ),
								),
								'std'		=> 'no'
					),
					'birthreport_intro' => array(
						'id'	=> 'birthreport_intro',
						'name'	=> __( 'Birth Report Intro', 'zodiacpress' ),
						'type'	=> 'textarea',
						'desc'	=> __( 'Optional "Introduction" text for the Birth Report.', 'zodiacpress' )
					),
					'birthreport_closing' => array(
						'id'	=> 'birthreport_closing',
						'name'	=> __( 'Birth Report Closing', 'zodiacpress' ),
						'type'	=> 'textarea',
						'desc'	=> __( 'Optional "Closing" text for the Birth Report. This will appear at the end of the report.', 'zodiacpress' )
					),
					'hide_empty_titles' => array(
						'id'	=> 'hide_empty_titles',
						'name'	=> __( 'Hide Empty Titles', 'zodiacpress' ),
						'type'	=> 'checkbox',
						'desc'	=> __( 'Hide titles for pieces that have no interpretations text.', 'zodiacpress' ),
						'class' => 'zp-setting-checkbox-label'
					),
				),

				'technical'	=> array(
					'tech_settings' => array(
						'id'	=> 'tech_settings',
						'name'	=> '<h3>' . __( 'Technical Settings', 'zodiacpress' ) . '</h3>',
						'desc'	=> '',
						'type'	=> 'header',
					),
					'birthreport_allow_unknown_bt' => array(
						'id'	=> 'birthreport_allow_unknown_bt',
						'name'	=> __( 'Allow Unknown Birth Time', 'zodiacpress' ),
						'type'	=> 'checkbox',
						'desc'	=> __( 'Allow people with unknown birth times to generate a birth report. If enabled, this will allow them to generate a basic report, excluding items that require a birth time (i.e. excluding Houses, Moon, Ascendant, Midheaven, Vertex, and Part of Fortune).', 'zodiacpress' ),
						'class' => 'zp-setting-checkbox-label'
					),
				)

			)
		),
		'drawing' => apply_filters( 'zp_settings_drawing',
			array(
				'main' => array(
					'drawing_allow_unknown_bt' => array(
								'id'	=> 'drawing_allow_unknown_bt',
								'name'	=> __( 'Allow Unknown Birth Time', 'zodiacpress' ),
								'type'	=> 'checkbox',
								'std'	=> 1,
								'desc'	=> __( 'Allow people with unknown birth times to get a chart wheel drawing. Their chart will be drawn for 12:00 time. Their chart will omit Moon, Ascendant, Midheaven, Part of Fortune and Vertex.', 'zodiacpress' ),
								'class' => 'zp-setting-checkbox-label'
					),
				)
			)
		),
		'misc' => apply_filters( 'zp_settings_misc',
			array(
				'main' => array(
					'atlas_header' => array(
						'id'	=> 'atlas_header',
						'name'	=> '<h3>' . __( 'Atlas', 'zodiacpress' ) . '</h3>',
						'type'	=> 'header',
						'desc'	=> '<hr />'
					),
					'atlas'	=> array(
						'id'	=> 'atlas',
						'name'	=> __( 'Choose Atlas', 'zodiacpress' ),
						'desc'	=> sprintf( __( 'You need an atlas to get city coordinates and timezones. Do you want to use GeoNames.org or create your own atlas database? (<a href="%1$s" target="_blank" rel="noopener">Help with this decision</a>)', 'zodiacpress' ), 'https://isabelcastillo.com/docs/choose-atlas' ),
						'type'	=> 'radio',
						'options' => array(
							'geonames' => __( 'Use GeoNames', 'zodiacpress' ),
							'db' => __( 'Use my own atlas database', 'zodiacpress' ),
						),
						'std'	=> 'geonames',
						'class' => 'zp-setting-atlas'
					),
					'geonames_user'	=> array(
						'id'	=> 'geonames_user',
						'name'	=> __( 'GeoNames Username', 'zodiacpress' ),
						'desc'	=> sprintf( __( 'Your username from GeoNames.org is needed to get timezone info from their webservice. (%1$screate free account%2$s)', 'zodiacpress' ), '<a href="http://www.geonames.org/login" target="_blank" rel="noopener">', '</a>' ),
						'type'	=> 'subtext',
						'size'	=> 'medium',
						'std'	=> '',
						'class' => 'zp-setting-geonames_user'
					),
					'atlas_status'	=> array(
						'id'	=> 'atlas_status',
						'name'	=> __( 'Atlas Status', 'zodiacpress' ),
						'type'	=> 'atlas',
						'class' => 'zp-setting-atlas-status'
					),
					'uninstall_header' => array(
						'id'	=> 'uninstall_header',
						'name'	=> '<h3>' . __( 'Uninstall', 'zodiacpress' ) . '</h3>',
						'type'	=> 'header',
						'desc'	=> '<hr />'
					),
					'remove_data' => array(
						'id'	=> 'remove_data',
						'name'	=> __( 'Remove Data on Uninstall', 'zodiacpress' ),
						'type'	=> 'checkbox',
						'desc'	=> __( 'Check this box if you would like ZP to completely remove all of its data (INCLUDING INTERPRETATIONS TEXT) when the plugin is deleted.', 'zodiacpress' ),
						'class' => 'zp-setting-checkbox-label'
					),
				)
			)
		)
	);
	return apply_filters( 'zp_registered_settings', $zp_settings );
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 *
 * @param array $input The value inputted in the field
 *
 * @return string $input Sanitizied value
 */
function zp_settings_sanitize( $input = array() ) {
	global $zodiacpress_options;

	if ( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}

	parse_str( $_POST['_wp_http_referer'], $referrer );

	$settings = zp_get_registered_settings();
	$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'natal';
	$section  = isset( $referrer['section'] ) ? $referrer['section'] : 'main';

	$input 					= $input ? $input : array();
	$zodiacpress_options 	= $zodiacpress_options ? $zodiacpress_options : array();

	$input = apply_filters( 'zodiacpress_settings_' . $tab . '-' . $section . '_sanitize', $input );

	// Loop through each setting being saved and pass it through a sanitization filter
	foreach ( $input as $key => $value ) {

		// Get the setting type (checkbox, select, etc)
		$type = isset( $settings[ $tab ][ $section ][ $key ]['type'] ) ? $settings[ $tab ][ $section ][ $key ]['type'] : false;

		if ( $type ) {
			// Field type specific filter
			$input[$key] = apply_filters( 'zp_settings_sanitize_' . $type, $value, $key );
		}

		// General filter
		$input[ $key ] = apply_filters( 'zp_settings_sanitize', $input[ $key ], $key );
	}

	// Loop through the whitelist and unset any that are empty for the tab being saved
	$section_settings = ! empty( $settings[ $tab ][ $section ] ) ? $settings[ $tab ][ $section ] : array();

	if ( ! empty( $section_settings ) ) {
		foreach ( $section_settings as $key => $value ) {
			if ( empty( $input[ $key ] ) ) {
				unset( $zodiacpress_options[ $key ] );
			}
		}
	}

	// Merge our new settings with the existing
	$output = array_merge( $zodiacpress_options, $input );

	add_settings_error( 'zp-notices', '', __( 'Settings updated.', 'zodiacpress' ), 'updated' );

	return $output;
}

/**
 * Sanitize text fields
 *
 * @param string $input The field value
 * @return string $input Sanitizied value
 */
function zp_sanitize_text_field( $input, $key ) {
	// Sanitize orb fields. Must be numeric.
	if ( 0 === strpos( $key, 'orb_' ) ) {
		if ( ! is_numeric( $input ) ) {
			return 8;
		} else {
			return abs( $input );// not negative
		}
	}
	return trim( $input );
}
add_filter( 'zp_settings_sanitize_text', 'zp_sanitize_text_field', 10, 2 );

/**
 * Sanitize subtext fields
 *
 * @param string $input The field value
 * @return string $input Sanitizied value
 */
function zp_sanitize_subtext_field( $input, $key ) {
	return sanitize_text_field( $input );
}
add_filter( 'zp_settings_sanitize_subtext', 'zp_sanitize_subtext_field', 10, 2 );

/**
 * Sanitize multicheck fields
 *
 * @param array $input The field value
 * @param string $key The field id
 * @return array Sanitizied value
 */
function zp_sanitize_multicheck_field( $input, $key ) {
	foreach ( $input as $k => $v ) {
		$out[] = array( 'id' => $k, 'label' => $v );
	}

	return $out;
}
add_filter( 'zp_settings_sanitize_multicheck', 'zp_sanitize_multicheck_field', 10, 2 );

/**
 * Retrieve settings tabs
 *
 * @return array $tabs
 */
function zp_get_settings_tabs() {
	$settings = zp_get_registered_settings();
	$tabs = array(
		'natal'		=> __( 'Natal Report', 'zodiacpress' ),
		'drawing'	=> __( 'Only Chart Drawing Report', 'zodiacpress' ),
		'misc'		=> __( 'Misc', 'zodiacpress' )
	);
	if( ! empty( $settings['licenses'] ) ) {
		$tabs['licenses'] = __( 'Licenses', 'zodiacpress' );
	}

	return apply_filters( 'zp_settings_tabs', $tabs );
}

/**
 * Retrieve settings tab sections
 *
 * @return array $section
 */
function zp_get_settings_tab_sections( $tab = false ) {
	$tabs     = false;
	$sections = zp_get_registered_settings_sections();

	if ( $tab && ! empty( $sections[ $tab ] ) ) {
		$tabs = $sections[ $tab ];
	}

	return $tabs;
}

/**
 * Get the settings sections for each tab
 * Uses a static to avoid running the filters on every request to this function
 *
 * @return array Array of tabs and sections
 */
function zp_get_registered_settings_sections() {
	static $sections = false;

	if ( false !== $sections ) {
		return $sections;
	}
	$sections = array(
		'natal'		=> apply_filters( 'zp_settings_sections_natal', array(
			'main'		=> __( 'Planets and Points', 'zodiacpress' ),
			'aspects'	=> __( 'Aspects', 'zodiacpress' ),
			'orbs'		=> __( 'Orbs', 'zodiacpress' ),
			'report'	=> __( 'Display', 'zodiacpress' ),
			'technical'	=> __( 'Technical', 'zodiacpress' )
		) ),
		'drawing'		=> apply_filters( 'zp_settings_sections_drawing', array(
			'main'		=>  __( '"Only Chart Drawing" Report Settings', 'zodiacpress' )
		) ),
		'licenses'	=> apply_filters( 'zp_settings_sections_licenses', array() ),
		'misc'		=> apply_filters( 'zp_settings_sections_misc', array(
			'main'		=>  __( 'Misc Settings', 'zodiacpress' )
		) ),
	);

	$sections = apply_filters( 'zodiacpress_settings_sections', $sections );

	return $sections;
}

/**
 * Callback function that renders the header field
 *
 * @param array $args Arguments passed by the setting
 * @return void
 */
function zp_header_callback( $args ) {
	echo empty( $args['desc'] ) ? '' : $args['desc'];
}

/**
 * Callback function that renders checkbox setting
 *
 * @param array $args Arguments passed by the setting
 * @return void
 */
function zp_checkbox_callback( $args ) {
	$options = get_option( 'zodiacpress_settings' );
	$checked = isset( $options[ $args['id'] ] ) ? checked( 1, $options[ $args['id'] ], false ) : '';
	$html = '<input type="checkbox" id="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']" name="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']" value="1" ' . $checked . '/>';
	$html .= '<label for="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;
}

/**
 * Callback function that renders multicheck setting
 *
 * @param array $args Arguments passed by the setting
 * @return void
 */
function zp_multicheck_callback( $args ) {
	$options = get_option( 'zodiacpress_settings' );

	if ( ! empty( $args['options'] ) ) {

		echo '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

		/* Reformat options array as alternative to using array_column() to support PHP < 5.5 */
		$enabled_options = array();
		if ( is_array( $options ) ) {
			$plucked_keys	= array();
			$plucked_values	= array();
			foreach ( $options as $k => $v ) {
				if ( is_array( $v ) && isset( $v[0]['id'] ) ) {
					$plucked_keys[] 	= $k;
					$plucked_values[]	= wp_list_pluck( $v, 'id', 'id' );
				}
			}
			$enabled_options = array_combine( $plucked_keys, $plucked_values );
		}

		foreach( $args['options'] as $option ):

			if ( isset( $enabled_options[$args['id']][ $option['id'] ] ) ) {
				$enabled = $option['id'];
			} else {
				$enabled = NULL;
			}
			echo '<input name="zodiacpress_settings[' . esc_attr( $args['id'] ) . '][' . $option['id'] . ']" id="zodiacpress_settings[' . esc_attr( $args['id'] ) . '][' . $option['id'] . ']" type="checkbox" value="' . esc_attr( $option['label'] ) . '" ' . checked($option['id'], $enabled, false) . '/>&nbsp;';
			echo '<label for="zodiacpress_settings[' . esc_attr( $args['id'] ) . '][' . $option['id'] . ']">' . wp_kses_post( $option['label'] ) . '</label><br/>';
		endforeach;
	}
}

/**
 * Callback function that renders text settings.
 *
 * @param array $args Arguments passed by the setting
 * @return void
 */
function zp_text_callback( $args ) {
	$options = get_option( 'zodiacpress_settings' );

	if ( isset( $options[ $args['id'] ] ) ) {
		$value = $options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$name = 'name="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']"';

	$size     = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html     = '<input type="text" class="' . esc_attr( $size ) . '-text" id="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '" />';
	$html    .= '<label for="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;
}

/**
 * Callback function that renders alternate text settings.
 *
 * @param array $args Arguments passed by the setting
 * @return void
 */
function zp_subtext_callback( $args ) {
	$name = isset( $args['name'] ) ? $args['name'] : '';
	?>
	<div class="zp-flex-container stuffbox"><div><strong><?php echo $name; ?></strong></div>
	<div><?php zp_text_callback( $args ); ?></div></div>
	<?php
}

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @param array $args Arguments passed by the setting
 * @return void
 */
function zp_missing_callback($args) {
	printf(
		__( 'The callback function used for the %s setting is missing.', 'zodiacpress' ),
		'<strong>' . esc_attr( $args['id'] ) . '</strong>'
	);
}

/**
 * Callback function that renders select field
 *
 * @param array $args Arguments passed by the setting
 * @return void
 */
function zp_select_callback( $args ) {
	$options = get_option( 'zodiacpress_settings' );
	if ( isset( $options[ $args['id'] ] ) ) {
		$value = $options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$html = '<select id="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']" name="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']" />';

	foreach ( $args['options'] as $option => $name ) {
		$selected = ( $option === $value ) ? ' selected' : '';
		$html .= '<option value="' . esc_attr( $option ) . '"' . $selected . '>' . esc_html( $name ) . '</option>';
	}

	$html .= '</select>';
	$html .= '<label for="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;
}

/**
 * Callback function that renders textarea setting
 *
 * @param array $args Arguments passed by the setting
 * @return void
 */
function zp_textarea_callback( $args ) {
	$options = get_option( 'zodiacpress_settings' );
	$value = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : '';
	
	$html = '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';
	$html .= '<textarea class="large-text" cols="50" rows="5" id="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']" name="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';

	echo $html;
}

/**
 * Callback function that renders radio input setting
 *
 * @param array $args Arguments passed by the setting
 * @return void
 */
function zp_radio_callback( $args ) {
	$options = get_option( 'zodiacpress_settings' );

	if ( isset( $options[ $args['id'] ] ) ) {
		$value = $options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$html = '<label for="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	foreach ( $args['options'] as $option => $name ) {
		$checked = ( $option === $value ) ? ' checked' : '';

		$html .= '<div><input type="radio" name="zodiacpress_settings[' . esc_attr( $args['id'] ) . ']" id="zodiacpress_settings_' . esc_attr( $args['id'] ) . '_' . esc_attr( $option ) . '" value="' . esc_attr( $option ) . '"' . $checked . '>' . esc_html( $name ) . '</div>';
	}

	echo $html;
}


/**
 * Callback function that renders Atlas status box
 *
 * @param array $args Arguments passed by the setting
 * @return void
 */
function zp_atlas_callback( $args ) {
	include ZODIACPRESS_PATH . 'includes/admin/views/html-atlas-status.php';
}

/**
 * Set manage_zodiacpress_settings as the cap required to save ZP settings
 *
 * @return string capability required
 */
function zp_set_settings_cap( $cap ) {
	return 'manage_zodiacpress_settings';
}
add_filter( 'option_page_capability_zodiacpress_settings', 'zp_set_settings_cap' );

/**
 * Display help text at the top of the Orbs settings
 *
 * @return  void
 */
function zp_orbs_settings_help_text() {
	static $done_ran;
	if ( ! empty( $done_ran ) ) {
		return;
	}
	echo '<p class="clear zp-helptext">' . __( 'For each aspect, set the orb to use for each planet. If blank, the default (8) will be used.', 'zodiacpress' ) . '</p>';
	$done_ran = true;
}

/**
 * Add granular Orbs settings
 */
function zp_orbs_add_orb_settings( $settings ) {
	$planets = zp_get_planets();
	$aspects = zp_get_aspects();
	foreach ( $aspects as $asp ) {
		$asp_id		= $asp['id'];
		$header_key	= $asp_id . '_orbs';
		$settings['orbs'][ $header_key ] = array(
					'id'	=> $header_key,
					'name'	=> '<h3>' . $asp['label'] . '</h3>',
					'type'	=> 'header',
					'desc'	=> '<hr />'
		);		
		foreach ( $planets as $p ) {
			$p_id	= $p['id'];
			$key 	= 'orb_' . $asp_id . '_' . $p_id;
			$settings['orbs'][ $key ] = array(
					'id'		=> $key,
					'name'		=> '',
					'type'		=> 'text',
					'desc'		=> $p['label'],
					'size'	=> 'small',
					'std'	=> '8'
			);
		}
	}
	return $settings;
}

add_action( 'zodiacpress_settings_tab_top_natal_orbs', 'zp_orbs_settings_help_text' );
add_action( 'zp_settings_natal', 'zp_orbs_add_orb_settings' );
