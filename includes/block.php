<?php
/**
 * Server-side rendering of the `zodiacpress/birthreport` block.
 *
 * @package	ZodiacPress
 */

/**
 * Renders the `zodiacpress/birthreport` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the birth report form
 */
function zp_render_block_birthreport( $attributes ) {

	$attributes['report'] = 'birthreport'; // @test do this here so as not to make this an editable att.

	
	// This is back compatibility so that a title doesn't get inserted by the shortcode function.
	// Can be removed if/when the shortcode function is removed.
	$attributes['form_title'] = '';


	/****************************************************
	* @todo
	* @todo WITHIN THE ZP-WC ADDON: Must auto detect if this a WooCommerce product and then set sell value accordingly.
	****************************************************/

	$attributes['sell'] = false;


	/****************************************************
	* @todo
	* do i need to check for atts values here (for sidereal and house_system) to update the atts to pass to the shortcode?
	or is that done on the sidebar with js only, in the "inspector?"

	****************************************************/
	
	return zp_birthreport_shortcode($attributes);
}

register_block_type( 'zodiacpress/birthreport', array(
	'attributes'	=> array(

		// defaults
		'sidereal'		=> false,
		'house_system'	=> false // @todo in future, this can be set to Placidus rather than false.
	),
	'render_callback'	=> 'zp_render_block_birthreport',
) );
