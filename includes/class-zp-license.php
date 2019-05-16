<?php
/**
 * @todo deprecated. This will be removed in next update 
 * License handler for ZodiacPress
 *
 * This class simplifies the process of adding license information
 * to ZP extensions.
 *
 * @version 2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'ZP_License' ) ) :

/**
 * ZP_License Class
 */
class ZP_License {
	private $file;
	private $license;
	private $item_name;
	private $item_id;
	private $item_shortname;
	private $version;
	private $author;
	private $api_url = 'https://cosmicplugins.com';

	/**
	 * Class constructor
	 *
	 * @param string  $_file
	 * @param string  $_item_name
	 * @param string  $_version
	 * @param string  $_author
	 * @param string  $_api_url
	 * @param int     $_item_id
	 */
	function __construct( $_file, $_item_name, $_version, $_author, $_api_url = null, $_item_id = null ) {
		$zp_options = get_option( 'zodiacpress_settings' );

		$this->file 		= $_file;
		$this->item_name 	= $_item_name;

		if ( is_numeric( $_item_id ) ) {
			$this->item_id 	= absint( $_item_id );
		}
		$this->item_shortname = preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->item_name ) ) );
		$this->version        = $_version;
		$this->license        = isset( $zp_options[ $this->item_shortname . '_license_key' ] ) ? trim( $zp_options[ $this->item_shortname . '_license_key' ] ) : '';
		$this->author         = $_author;
		$this->api_url        = is_null( $_api_url ) ? $this->api_url : $_api_url;
		$this->includes();
		add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );
	}

	/**
	 * Include the updater class
	 *
	 * @access  private
	 * @return  void
	 */
	private function includes() {
		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) )  {
			include_once 'EDD_SL_Plugin_Updater.php';
		}
	}

	/**
	 * Auto updater
	 *
	 * @access  private
	 * @return  void
	 */
	public function auto_updater() {
		$args = array(
			'version'   => $this->version,
			'license'   => $this->license,
			'author'    => $this->author
		);

		if( ! empty( $this->item_id ) ) {
			$args['item_id']   = $this->item_id;
		} else {
			$args['item_name'] = $this->item_name;
		}

		// Setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater(
			$this->api_url,
			$this->file,
			$args
		);
	}
}

endif; // end class_exists check
