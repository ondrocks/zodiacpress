<?php
/**
 * Admin View: Custom Reports: Custom Report tab: Interpretations section
 * @since 1.9
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! isset( $_GET['section'] ) || 'interpretations' != $_GET['section'] ) {
	return;
}

// Get ids, types, and official titles for this report's items
$items = array();
$report = new ZP_Report( $active_tab );
$all_items = $report->get_items();
foreach ( $all_items as $item ) {
	$type = ZP_Custom_Reports::get_item_type( $item[0] );

	if ( in_array( $type, array( 'heading', 'subheading', 'text' ) ) ) {
		continue;
	}

	$items[ $item[0] ] = array(
		'type' => $type,// @test is this even used ever later? if not, remove.
		'title' => ZP_Custom_Reports::listitems( $type )[ $item[0] ]
	);
}

$subsection = isset( $_GET['subsection'] ) ? sanitize_text_field( $_GET['subsection'] ) : array_keys($items)[0];// if subsection not set, set it to the first item
?>

<div id="zp-custom-report-interp-menu">
	<h2 id="zp-tabsubtitle"><?php _e( 'Interpretations', 'zodiacpress' ); ?></h2>
	<ul class="subsubsub">
	<?php

	$number_of_sections = count( $items );
	$number = 0;

	foreach( $items as $item_id => $item ) {
		?>
		<li>
		<?php
		$number++;
		$section_url = add_query_arg( array(
			'tab' => $active_tab,// @test is this pulled over from custom-reports.php? 
			'section' => 'interpretations',
			// @test does subsection work? need?
			'subsection' => $item_id
		) );
		$section_url = remove_query_arg( 'zp-done', $section_url );

		$class = ( $subsection == $item_id ) ? 'current' : '';

		?>
		<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $section_url ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
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
	</ul>
</div>
<?php
$type = $items[ $subsection ]['type'];
$aspecting_planet = null;
$inputs = array();

switch ( $type ) {
	case 'lord':
	case 'residents':

		$main_id = str_replace( '_' . $type, '', $subsection );

		$houses = true;
		$include = null;
		if ( 'lord' == $type ) {
			$houses = false;
			$include = 10;
		}

		$planets = zp_get_planets( $houses, $include );

		foreach ( $planets as $p ) {

			$label_string = ( 'lord' == $type ) ? __( '%s is Lord of House %d', 'zodiacpress' ) : __( '%s in House %d', 'zodiacpress' );
			$inputs[ $p['id'] ] = sprintf( $label_string, $p['label'], $main_id );
		}
		break;

	case 'aspects':
		$split_item = ZP_Custom_Reports::split_aspect_item( $subsection );
		$aspecting_planet = $split_item[0];
		$main_id = $split_item[1];

		$planets = zp_get_planets();
		$aspects = zp_get_aspects();

		$main_planet_key = zp_search_array( $aspecting_planet, 'id', $planets );
		$aspect_key = zp_search_array( $main_id, 'id', $aspects );

		foreach ( $planets as $p_key => $p ) {

			if ( $p['id'] == $aspecting_planet ) {// skip same planet
				continue;
			}

			// build the label
			$label = $planets[ $main_planet_key ]['label'] . ' ' . $aspects[ $aspect_key ]['label'] . ' ' . $p['label'];

			// faster planet aspects the slower planet, not vice versa
			if ( $p_key < $main_planet_key ) {
				$label = $p['label'] . ' ' . $aspects[ $aspect_key ]['label'] . ' ' . $planets[ $main_planet_key ]['label'];
			}

			$inputs[ $p['id'] ] = $label;
		}
		break;

	case 'sign':
		$main_id = str_replace( '_' . $type, '', $subsection );
		$signs = zp_get_zodiac_signs();
		$planets = zp_get_planets();
		$planet_key = zp_search_array( $main_id, 'id', $planets );

		foreach ( $signs as $sign ) {
			$label = sprintf( __( '%1$s in %2$s', 'zodiacpress' ), $planets[ $planet_key ]['label'], $sign['label'] );
			$inputs[ $sign['id'] ] = $label;
		}

		break;

	case 'house':

		$main_id = str_replace( '_' . $type, '', $subsection );
		$planets = zp_get_planets();

		$planet_key = zp_search_array( $main_id, 'id', $planets );
		$planet_label = $planets[ $planet_key ]['label'];

		for ( $h = 1; $h < 13; $h++ ) {
			$inputs[ $h ] = sprintf( __( '%s in House %d', 'zodiacpress' ), $planet_label, $h );
		}
		break;
}

// isa_log('main id = ' . $main_id);// @test
// isa_log('inputs = ');
// isa_log($inputs);// @test now now

// Get saved interpretations values
$option_name = 'zp_' . $active_tab . '_' . $type;
if ( 'aspects' === $type ) {
	$option_name .= '_' . $aspecting_planet;
}
$option = get_option( $option_name );
$values = isset( $option[ $main_id ] ) ? $option[ $main_id ] : array();

?>
<form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
	<table class="form-table">
		<?php

		/****************************************************
		*
		* @todo @test BEGIN
		*
		****************************************************/
		
		foreach ( $inputs as $input_id => $input_label ) {
			$input_name = 'interps[' . $main_id . '][' . $input_id . ']';
			$value = isset( $values[ $input_id ] ) ? $values[ $input_id ] : '';

			?>
			<tr>
				<th scope="row"><?php echo esc_html( $input_label ); ?></th>
				<td><textarea class="large-text" cols="50" rows="5" name="<?php echo esc_attr( $input_name ); ?>"><?php echo esc_textarea( stripslashes( $value ) ); ?></textarea></td>				
			</tr>
			<?php
		}
		?>
	</table>

<!-- @todo now now 	WORKING ON: REMOVE ANY HIDDEN INPUTS THAT ARE NOT USED IN THE admin_post action, then do a commit!!!!!!!! -->
		
	<input type="hidden" name="action" value="zp-save-custom-interps" />

	<?php wp_nonce_field( 'save-custom-interps', 'zp_admin_nonce' ); ?>
		
	<input type="hidden" name="custom-report-id" value="<?php echo esc_attr( $active_tab ); ?>" />

	<input type="hidden" name="subsection" value="<?php echo esc_attr( $subsection ); ?>" />

	

	<input type="hidden" name="type" value="<?php echo esc_attr( $type ); ?>" />


	<!-- @todo may NOT need main-identifier since i will use it in the option name like 'id[sun], id[moon], etc....' -->

	<input type="hidden" name="main-identifier" value="<?php echo esc_attr( $main_id ); ?>" />

	<?php 
	if ( 'aspects' === $type ) {
		?>
		<input type="hidden" name="aspecting-planet" value="<?php echo esc_attr( $aspecting_planet ); ?>" />
		<?php
	}
	?>

	<button class="button-primary"><?php _e( 'Save Changes', 'zodiacpress' ); ?></button>
</form>