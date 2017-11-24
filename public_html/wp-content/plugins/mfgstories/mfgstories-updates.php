<?php

function import_config( $path, $option_name ) {
	$data = file_get_contents( MFS_PLUGIN_DIR . $path );
	$mods = json_decode( $data, true );
	if ( false !== $mods ) {
		update_option( $option_name, $mods );
	}
}

function remove_value( &$array, $value ) {
	$key = array_search( $value, $array );
	if ( false !== $key ) {
		unset( $array[ $key ] );
	}
}
/**
 * Activate the custom theme.
 */
function mfgstories_update_0() {
	switch_theme( 'mfgstories' );
}

/**
 * Import theme mods.
 */
function mfgstories_update_1() {
	import_config( 'config/theme_mods.json', 'theme_mods_mfgstories' );
}

/**
 * Import widget configurations.
 */
function mfgstories_update_2() {
	import_config(
		'config/widget_wpex_info_widget.json',
		'widget_wpex_info_widget'
	);
	import_config(
		'config/widget_nav_menu.json',
		'widget_nav_menu'
	);
	import_config(
		'config/widget_wpex_social_profiles.json',
		'widget_wpex_social_profiles'
	);
}

/**
 * Map the widgets to the right sidebars.
 */
function mfgstories_update_3() {
	$mods = get_option( 'sidebars_widgets' );
	$inactive = $mods['wp_inactive_widgets'];
	remove_value( $inactive, 'wpex_info_widget-2' );
	$mods['footer-one'] = [ 'wpex_info_widget-2' ];
	remove_value( $inactive, 'nav_menu-2' );
	$mods['footer-two'] = [ 'nav_menu-2' ];
	remove_value( $inactive, 'wpex_social_profiles-4' );
	$mods['footer-three'] = [ 'wpex_social_profiles-4' ];
	$mods['instagram_footer'] = [];
	$mods['sidebar_pages'] = [];
	$mods['footer-four'] = [];
	$mods['wp_inactive_widgets'] = $inactive;
	update_option( 'sidebars_widgets', $mods );
}

/**
 * Set the blog description.
 */
function mfgstories_update_4() {
	update_option( 'blogdescription', 'ManufacturingStories is a place where everyone can learn about and share information on the many exciting programs available to help revitalize &amp; modernize manufacturing in America and to help bridge the skills gap between education and the workplace.' );
}

// Manual updates:
// Set Home to /
// Remove partners menu
