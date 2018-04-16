<?php
/**
 * Functions that are needed only in admin
 *
 * @package     ZodiacPress
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Receive Heartbeat data and respond.
 *
 * Recieves our zpatlas_status request flag, and sends the atlas install status back to the front end.
 *
 * @param array $response Heartbeat response data to pass back to front end.
 * @param array $data Data received from the front end (unslashed).
 */
function zp_atlas_receive_heartbeat( $response, $data ) {
	if ( empty( $data['zpatlas_status'] ) ) {
		return $response;    
	}

	// Show "Atlas is Ready" notice once, the first time the atlas is ready
	if ( get_transient( 'zp_atlas_ready_once' ) ) {
		$status = zp_string( 'complete' );
		delete_transient( 'zp_atlas_ready_once' );

	} else {

		// since the atlas_ready transient is not found, get pending msg
		$status = get_option( 'zp_atlas_db_pending' );
	}

	// only send something if there is something to add
	if ( $status ) {
		$response['zpatlas_status_message'] = $status;  
	}
	 
	return $response;
}
add_filter( 'heartbeat_received', 'zp_atlas_receive_heartbeat', 10, 2 );

/**
 * ZP Admin notices
 */
function zp_admin_notices() {
	global $zodiacpress_options;

	// On activation, adds admin notice to inform that Atlas must be set up.
	if ( get_transient( 'zodiacpress_activating' ) ) {
		delete_transient( 'zodiacpress_activating' );
		// Only show notice if atlas db is not in use and a geonames username is not set
		if ( ! ZP_Atlas_DB::use_db() && empty( $zodiacpress_options['geonames_user'] ) ) {
			include plugin_dir_path( __FILE__ ) . 'views/html-notice-install.php';
		}
	}

	// Success notices for ZP Tools.
	if ( isset( $_GET['zp-done'] ) ) {
		switch( $_GET['zp-done'] ) {
			case 'natal_in_signs':
				$success = __( 'Interpretations for natal planets in signs were erased.', 'zodiacpress' );
				break;
			case 'natal_in_houses':
				$success = __( 'Interpretations for natal planets in houses were erased.', 'zodiacpress' );
				break;
			case 'natal_aspects':
				$success = __( 'Interpretations for natal aspects were erased.', 'zodiacpress' );
				break;
			case 'settings-imported':
				$success = __( 'Your ZodiacPress settings have been imported.', 'zodiacpress' );
				break;
			case 'interps-imported':
				$success = __( 'Your ZodiacPress interpretations have been imported.', 'zodiacpress' );
				break;				
		}

		if ( isset( $success ) ) {
			printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', $success );
		}
	}
	
	// Notify when plugin cannot work

	if ( zp_is_admin_page() ) {

		if ( ! zp_is_func_enabled( 'exec' ) ) {
			echo '<div class="notice notice-error is-dismissible"><p>' .
			__( 'The PHP exec() function is disabled on your server. ZodiacPress requires the exec() function in order to create astrology reports. Please ask your web host to enable the PHP exec() function.', 'zodiacpress' ) .
			'</p></div>';
		}

		if ( zp_is_server_windows() ) {
			if ( ! defined( 'ZP_WINDOWS_SERVER_PATH' ) ) {

				echo '<div class="notice notice-error is-dismissible"><p>' .
				sprintf( __( 'Your website server uses Windows hosting. For ZodiacPress to work on your server, you need the %1$sZP Windows Server%2$s plugin. See <a href="%3$s" target="_blank" rel="nofollow">this</a> for details.', 'zodiacpress' ), '<strong>', '</strong>', 'https://cosmicplugins.com/docs/your-site-windows-hosting/' ) .
				'</p></div>';
			}
		}
	}
}
add_action( 'admin_notices', 'zp_admin_notices' );

/**
 * Add admin notice when file permissions on ephemeris will not permit the plugin to work.
 */
function zp_admin_notices_chmod_failed() {
	if ( zp_is_admin_page() ) {
		$msg = sprintf( __( 'Your server did not allow ZodiacPress to set the necessary file permissions for the Ephemeris. ZodiacPress requires this in order to create astrology reports. <a href="%s" target="_blank" rel="nofollow">See this</a> to fix it.', 'zodiacpress' ), 'https://cosmicplugins.com/docs/file-permissions-swetest/' );

		printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', $msg );
	}
}

/**
 * Add admin notice when swetest file is missing.
 */
function zp_admin_notices_missing_file() {
	if ( zp_is_admin_page() ) {
		$msg = sprintf( __( 'You are missing a file from ZodiacPress. This file is required in order to create astrology reports. <a href="%s" target="_blank" rel="nofollow">See this</a> for more information.', 'zodiacpress' ), 'https://cosmicplugins.com/docs/missing-file/' );
		printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', $msg );
	}
}

/**
 * Erase Interpretations for Natal Planets in Signs when using ZP Cleanup Tools.
 */
function zp_erase_natal_in_signs() {
	if ( ! wp_verify_nonce( $_GET['_nonce'], 'zp_erase_natal_in_signs' ) ) {
		return false;
	}

	delete_option( 'zp_natal_planets_in_signs' );

	$url  = esc_url_raw( add_query_arg( array(
		'page'	=> 'zodiacpress-tools',
		'tab'	=> 'cleanup',
		'zp-done'	=> 'natal_in_signs'
		), admin_url( 'admin.php' )
	) );
	wp_redirect( wp_sanitize_redirect( $url ) );
	exit;
}
add_action( 'admin_post_erase_natal_in_signs', 'zp_erase_natal_in_signs' );

/**
 * Erase Interpretations for Natal Planets in Houses when using ZP Cleanup Tools.
 */
function zp_erase_natal_in_houses() {
	if ( ! wp_verify_nonce( $_GET['_nonce'], 'zp_erase_natal_in_houses' ) ) {
		return false;
	}

	delete_option( 'zp_natal_planets_in_houses' );

	$url  = esc_url_raw( add_query_arg( array(
		'page'	=> 'zodiacpress-tools',
		'tab'	=> 'cleanup',
		'zp-done'	=> 'natal_in_houses'
		), admin_url( 'admin.php' )
	) );
	wp_redirect( wp_sanitize_redirect( $url ) );
	exit;
}
add_action( 'admin_post_erase_natal_in_houses', 'zp_erase_natal_in_houses' );

/**
 * Erase Interpretations for Natal Aspects when using ZP Cleanup Tools.
 */
function zp_erase_natal_aspects() {
	if ( ! wp_verify_nonce( $_GET['_nonce'], 'zp_erase_natal_aspects' ) ) {
		return false;
	}

	foreach ( zp_get_planets() as $planet ) {
		$p = ( 'sun' == $planet['id'] ) ? 'main' : $planet['id'];
		delete_option( 'zp_natal_aspects_' . $p );
	}

	$url  = esc_url_raw( add_query_arg( array(
		'page'	=> 'zodiacpress-tools',
		'tab'	=> 'cleanup',
		'zp-done'	=> 'natal_aspects'
		), admin_url( 'admin.php' )
	) );
	wp_redirect( wp_sanitize_redirect( $url ) );
	exit;
}
add_action( 'admin_post_erase_natal_aspects', 'zp_erase_natal_aspects' );

/**
 * Custom admin menu icon
 */
function zp_custom_admin_menu_icon() {
   echo '<style>@font-face {
  font-family: "zodiacpress";
  src:    url("' . ZODIACPRESS_URL . 'assets/fonts/zodiacpress.eot?fr7qsr");
  src:    url("' . ZODIACPRESS_URL . 'assets/fonts/zodiacpress.eot?fr7qsr#iefix") format("embedded-opentype"),
  url("' . ZODIACPRESS_URL . 'assets/fonts/zodiacpress.ttf?fr7qsr") format("truetype"),
  url("' . ZODIACPRESS_URL . 'assets/fonts/zodiacpress.woff?fr7qsr") format("woff"),
  url("' . ZODIACPRESS_URL . 'assets/fonts/zodiacpress.svg?fr7qsr#zodiacpress") format("svg");
  font-weight: normal;
  font-style: normal;
  }#adminmenu .toplevel_page_zodiacpress .dashicons-universal-access-alt.dashicons-before::before {font-family: "zodiacpress" !important}#adminmenu .toplevel_page_zodiacpress div.dashicons-universal-access-alt::before{content:"\e90c"}</style>';
}
add_action('admin_head', 'zp_custom_admin_menu_icon');
/**
 * Displays a link to see ZP extensions.
 */
function zp_extend_link() {
	echo '<a href="https://cosmicplugins.com/downloads/category/zodiacpress-extensions/" class="button-secondary zp-extend-link alignright" target="_blank" rel="nofollow">';
	_e( 'See ZodiacPress Extensions', 'zodiacpress' );
	echo '</a>';
}

/**
 * Displays a link to rate ZodacPress
 */
function zp_feedback_link() {
	echo '<a href="https://wordpress.org/support/plugin/zodiacpress/reviews/" class="button-secondary zp-feedback-link alignright" target="_blank" rel="nofollow">';
	_e( 'Feedback', 'zodiacpress' );
	echo '</a>';
}

/**
 * Get size of the zp_atlas database table including the size of its index.
 *
 * @return int $size in bytes
 */
function zp_atlas_get_size() {
	static $size;
	if ( isset( $size ) ) {
		return $size;
	}
	global $zpdb;
	$size = 0;
    $results = $zpdb->get_results( 'SHOW TABLE STATUS', ARRAY_A );
	if ( $results ) {
		foreach ( $results as $table ) {
			if ( "{$zpdb->prefix}zp_atlas" != $table['Name'] ) {
				continue;
			}
		    $size += $table['Data_length'] + $table['Index_length'];
		}
	}
	return $size;
}
