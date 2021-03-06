<?php

namespace Terescode\MfgStories;

define( 'MFS_PLUGIN_ID', 'mfgstories' );
define( 'MFS_PLUGIN_DIR', plugin_dir_path(
	dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . MFS_PLUGIN_ID
) );
define( 'MFS_PLUGIN_URL', plugin_dir_url(
	dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . MFS_PLUGIN_ID
) );
define( 'MFS_PLUGIN_BASE', plugin_basename(
	dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . MFS_PLUGIN_ID . '.php'
) );

if ( ! function_exists( __NAMESPACE__ . '\is_wpinc_defined' ) ) {
	function is_wpinc_defined() {
		return defined( 'WPINC' );
	}
}
