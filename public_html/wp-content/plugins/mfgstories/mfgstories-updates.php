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

function get_nav_menu_item( $nav_menu_items, $menu_label ) {
	foreach ( $nav_menu_items as $item ) {
		if ( $item->title === $menu_label ) {
			return $item;
		}
	}
	return null;
}

function build_menu_item_attrs( $menu_item, $attrs ) {
	return array_merge(
		[
			'menu-item-object-id' => $menu_item->object_id,
			'menu-item-object' => $menu_item->object,
			'menu-item-parent-id' => $menu_item->menu_item_parent,
			'menu-item-position' => $menu_item->menu_order,
			'menu-item-type' => $menu_item->type,
			'menu-item-title' => $menu_item->title,
			'menu-item-url' => $menu_item->url,
			'menu-item-description' => $menu_item->description,
			'menu-item-attr-title' => $menu_item->attr_title,
			'menu-item-target' => $menu_item->target,
			'menu-item-classes' => $menu_item->classes
				? implode( ' ', $menu_item->classes )
				: '',
			'menu-item-xfn' => $menu_item->xfn,
			'menu-item-status' => 'publish',
		],
		$attrs
	);
}
function update_nav_menu_item(
	$nav_menu,
	$nav_menu_items,
	$menu_label,
	$attrs
) {
	$menu_item = get_nav_menu_item( $nav_menu_items, $menu_label );
	if ( $menu_item ) {
		wp_update_nav_menu_item(
			$nav_menu,
			$menu_item->ID,
			build_menu_item_attrs( $menu_item, $attrs )
		);
	}
}

function remove_nav_menu_item( $nav_menu_items, $menu_label ) {
	// Remove menu item
	$menu_item = get_nav_menu_item( $nav_menu_items, $menu_label );
	if ( $menu_item ) {
		wp_delete_post( $menu_item->ID );
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
	$mods['footer-four'] = [];
	$mods['instagram_footer'] = [];
	$mods['sidebar_pages'] = [];
	$mods['wp_inactive_widgets'] = $inactive;
	$sidebar = $mods['sidebar'];
	remove_value( $sidebar, 'search-2' );
	remove_value( $sidebar, 'text-10' );
	remove_value( $sidebar, 'text-14' );
	$mods['sidebar'] = $sidebar;
	update_option( 'sidebars_widgets', $mods );
}

/**
 * Set the blog description.
 */
function mfgstories_update_4() {
	update_option( 'blogdescription', 'ManufacturingStories is a place where everyone can learn about and share information on the many exciting programs available to help revitalize &amp; modernize manufacturing in America and to help bridge the skills gap between education and the workplace.' );
}

/**
 * Set the menus.
 */
function mfgstories_update_5() {
	$nav_menu = wp_get_nav_menu_object( 'MainNav' );
	if ( ! $nav_menu ) {
		return;
	}

	$items = wp_get_nav_menu_items( $nav_menu, [] );
	if ( empty( $items ) ) {
		return;
	}

	$nav_menu_id = $nav_menu->term_id;

	// Set Home url to '/'
	update_nav_menu_item(
		$nav_menu_id,
		$items,
		'Home',
		[
			'menu-item-url' => '/',
		]
	);

	// Remove partners menu item
	remove_nav_menu_item( $items, 'Partners' );

	// Remove Subscribe
	remove_nav_menu_item( $items, 'Subscribe' );

	// Remove Archives
	remove_nav_menu_item( $items, 'Archives' );

	// Re-fetch the items after deleting
	$items = wp_get_nav_menu_items( $nav_menu, [] );
	$menu_item = get_nav_menu_item( $items, 'About' );
	if ( $menu_item ) {
		foreach ( $items as $item ) {
			if ( 'About' === $item->title ) {
				wp_update_nav_menu_item(
					$nav_menu_id,
					$item->ID,
					build_menu_item_attrs(
						$item,
						[ 'menu-item-position' => 2 ]
					)
				);
				continue;
			} elseif ( 'Home' !== $item->title ) {
				wp_update_nav_menu_item(
					$nav_menu_id,
					$item->ID,
					build_menu_item_attrs(
						$item,
						[ 'menu-item-position' => ( (int) $item->menu_order ) + 1 ]
					)
				);
			}
		}
	}
}
