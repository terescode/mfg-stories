<?php
/**
 * Plugin Name: Manufacturing Stories Site
 * Description: WordPress customizations for manufacturingstories.com
 * Version:     1.0.0
 * Author:      Terescode, LLC
 * Author URI:  http://www.terescode.com
 * @package mfgstories
 */

require_once 'includes/constants.php';

if ( ! \Terescode\MfgStories\is_wpinc_defined() ) {
	return -1;
}

register_theme_directory( dirname( __FILE__ ) . '/themes' );

if ( is_admin() ) {
	require_once MFS_PLUGIN_DIR . '/includes/admin.php';
}
