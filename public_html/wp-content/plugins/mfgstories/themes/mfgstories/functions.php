<?php
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
	wp_enqueue_style( 'noir-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style(
		'child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[ 'noir-style' ],
		wp_get_theme()->get( 'Version' )
	);
}

function mfgstories_post_is_blog() {
	$cats = get_the_category();
	if (
		! empty( $cats ) &&
		count( $cats ) === 1 &&
		'Blog' === $cats[0]->name
	) {
		return true;
	}
	return false;
}

function mfgstories_home_pre_get_posts( &$query ) {
	if ( $query->is_home() && $query->is_main_query() ) {
		$query->set( 'posts_per_page', 9 );
		$query->set( 'no_found_rows', true );
	}
}

function mfgstories_filter_sidebars_widgets( $sidebars_widgets ) {
	if ( empty( $sidebars_widgets['sidebar'] ) ) {
		return $sidebars_widgets;
	}

	$sidebar = $sidebars_widgets['sidebar'];
	$image_widgets_seen = 0;
	foreach ( $sidebar as $index => $widget_id ) {
		if ( ! preg_match( '/^widget_sp_image-/', $widget_id ) ) {
			continue;
		}
		if ( $image_widgets_seen >= 5 ) {
			unset( $sidebar[ $index ] );
		}
		$image_widgets_seen += 1;
	}

	$sidebars_widgets['sidebar'] = $sidebar;
	return $sidebars_widgets;
}

function mfgstories_customizer_css() {
	$inline_css = '/* mfgstories inline css */';
	$accent = get_theme_mod( 'accent_color' );
	$custom_accent = get_theme_mod( 'custom_accent_color' );
	$accent = $custom_accent ? $custom_accent : $accent;

	$inline_css .= '.mfs-topbar-search .wpex-site-searchform button{';
	$inline_css .= 'background-color:' . $accent . ';}';

	echo '<style type="text/css">' . $inline_css . '</style>';
}

function mfgstories_footer_js() {
	?>
	<script>
		( function( $ ) {
			'use strict';
			$( document ).ready(function() {
				if ( $( window ).width() <= 768 ) {
					$( '.wpex-topbar-wrap' ).sticky( {
						topSpacing      : 0,
						responsiveWidth : true,
						className       : 'wpex-sticky-nav'
					} );
					$( '.mfs-topbar-mobile-search button' ).click( function () {
						$( '.mfs-topbar-search' ).show();
					} );
					$( '.mfs-topbar-mobile-search-close' ).click( function () {
						$( '.mfs-topbar-search' ).hide();
					} );
				}
			} );
		} ) ( jQuery );
	</script>
<?php
}

function mfgstories_document_title_parts( $title ) {
	if ( ! empty( $title['tagline'] ) ) {
		unset( $title['tagline'] );
	}
	return $title;
}

add_action( 'pre_get_posts', 'mfgstories_home_pre_get_posts' );
add_filter(
	'sidebars_widgets',
	'mfgstories_filter_sidebars_widgets'
);
add_action( 'wp_head', 'mfgstories_customizer_css' );
add_action( 'wp_footer', 'mfgstories_footer_js' );
add_filter( 'document_title_parts', 'mfgstories_document_title_parts' );
