<?php
/**
 * Topbar Layout
 *
 * This file was copied from Noir parent theme so it could be customized
 * in order to add the search form to the top bar.
 *
 * @package   Noir WordPress Theme
 * @author    Alexander Clarke
 * @copyright Copyright (c) 2015, WPExplorer.com
 * @link      http://www.wpexplorer.com
 * @since     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check display
$display = apply_filters( 'wpex_topbar_enable', wpex_get_theme_mod( 'topbar_enable', true ) );

// Show topbar if enabled
if ( $display ) : ?>

	<div class="wpex-topbar-wrap wpex-clr">

		<div class="wpex-topbar wpex-container wpex-clr">

			<?php get_template_part( 'partials/topbar/topbar-offcanvas-toggle' ); ?>

			<div class="mfs-topbar-mobile-logo"><h4>ManufacturingStories®</h4></div>

			<?php get_template_part( 'partials/topbar/topbar-social' ); ?>

			<?php get_template_part( 'partials/topbar/topbar-cart' ); ?>

			<div class="mfs-topbar-search">
				<a href="javascript:void(0)" class="mfs-topbar-mobile-search-close">
				X
				</a>
			<?php get_template_part( 'searchform' ); ?>
			</div>

			<div class="mfs-topbar-mobile-search">
				<button type="button"><span class="fa fa-search"></span></button>
			</div>
		</div><!-- .wpex-topbar -->

	</div><!-- .wpex-topbar-wrap -->

<?php endif; ?>
