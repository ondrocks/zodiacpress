<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Shows the Custom Reports page.
 *
 * @return      void
 */
function zp_custom_reports_page() {

	$tabs = ZP_Custom_Reports::get_tabs();

	$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_text_field( $_GET['tab'] ) : 'custom-reports';

	$sections = ( 'custom-reports' === $active_tab ) ? 0 : ZP_Custom_Reports::get_tabs_sections();// no sections for main custom reports tab

	isa_log("sections = ");// @test
	isa_log($sections);// @test

	$section = isset( $_GET['section'] ) && ! empty( $sections ) && array_key_exists( $_GET['section'], $sections ) ? sanitize_text_field( $_GET['section'] ) : 'main';

	isa_log("current $section = $section");// @test

	?>
	<div class="wrap <?php echo 'wrap-' . $active_tab; ?>">
	<?php zp_admin_links(); ?>
	<nav class="nav-tab-wrapper clear">

	<?php

	settings_errors( 'zp-custom-reports-notices' );// @test need? perhaps not since will use custom js. Maybe only for Technical and Orbs sections.

	foreach( $tabs as $tab_id => $tab_name ) {

		$tab_url = add_query_arg( array(
			'settings-updated' => false,// @test need? perhaps not since will use custom js.
			'tab'              => $tab_id,
		) );

		// Remove the section from the tabs so we always end up at the main section
		$tab_url = remove_query_arg( 'section', $tab_url );

		$active = $active_tab == $tab_id ? ' nav-tab-active' : '';
		?>

		<a href="<?php echo esc_url( $tab_url ); ?>" class="nav-tab<?php echo esc_attr( $active ); ?>">
			<?php echo esc_html( $tab_name ); ?>
		</a>

		<?php
	}
	?>
	</nav>
	<h1 class="screen-reader-text"><?php echo $tabs[ $active_tab ]; ?></h1>

	<?php
	$location = $active_tab;
	isa_log('$location = ' . $location);// @test

	$number_of_sections = count( $sections );
	$number = 0;
	if ( $number_of_sections > 1 ) {
		$location .= 'custom-reports-' . $section;
		isa_log('$location 2 = ' . $location);// @test
		?>
		<div><ul class="subsubsub">
			<?php foreach( $sections as $section_id => $section_name ) {
				?>
				<li>
				<?php
				$number++;
				$tab_url = add_query_arg( array(
					'settings-updated' => false,
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
	<div id="tab_container" class="<?php echo $location; ?>">
		<?php include ZODIACPRESS_PATH . 'includes/admin/views/html-' . $location . '.php'; ?>
	</div><!-- #tab_container-->
	</div><!-- .wrap -->

	<?php
}
