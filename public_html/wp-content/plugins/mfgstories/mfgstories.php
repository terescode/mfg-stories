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

	function mfgstories_admin_notices() {
		$message = null;
		if ( ! empty( $_REQUEST['mfgstories_status_msg'] ) ) {
			$message = $_REQUEST['mfgstories_status_msg'];
		}

		if ( ! $message ) {
			$message = get_transient( 'mfgstories_status_msg' );
		}

		if ( $message ) {
			$type = $message['type'];
			$msg = $message['message'];
			$clazz = ( 'error' === $type ? 'notice-error' : 'notice-success' );
			?>
			<div class="notice <?php echo esc_attr( $clazz ); ?> is-dismissible">
					<p><?php echo esc_html( $msg ); ?></p>
			</div>
			<?php
		}
	}

	add_action( 'admin_notices', 'mfgstories_admin_notices' );

	function mfgstories_plugin_add_settings_link( $links ) {
		$settings_link
			= '<a href="options-general.php?page=mfs_options">'
				. __( 'Settings', 'mfgstories' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	add_filter(
		'plugin_action_links_' . MFS_PLUGIN_BASE,
		'mfgstories_plugin_add_settings_link'
	);

	function mfgstories_admin_apply_updates() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$action_nonce = $_POST['action_nonce'];
		if ( empty( $action_nonce )
			|| ! wp_verify_nonce( $action_nonce, 'mfs_apply_upd' ) ) {
			return;
		}

		$all = ! empty( $_POST['update_all'] );
		$prefix = 'mfgstories_update_';
		$last_update_num = ( $all ? -1 : get_option( 'mfgstories_updates', -1 ) );
		require_once( MFS_PLUGIN_DIR . 'mfgstories-updates.php' );
		$done = false;
		$updates = 0;
		$message = '';
		try {
			$next_update_num = $last_update_num + 1;
			while ( ! $done ) {
				if ( ! function_exists( $prefix . $next_update_num ) ) {
					$done = true;
					continue;
				}
				call_user_func( $prefix . $next_update_num );
				$next_update_num += 1;
				$updates += 1;
			}
			update_option( 'mfgstories_updates', $next_update_num - 1 );
			$message = [
				'type' => 'notice',
				'message' => sprintf(
					__( '%d updates applied', 'mfgstories' ),
					$updates
				),
			];
		} catch ( \Exception $exc ) {
			$message = [
				'type' => 'error',
				'message' => __( 'Apply updates failed', 'mfgstories' ),
			];
		}

		set_transient( 'mfgstories_status_msg', $message, 10 );
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		return;
	}

	add_action( 'admin_post_apply_updates', 'mfgstories_admin_apply_updates' );

	function mfgstories_admin_menu_render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$update_level = get_option( 'mfgstories_updates', 0 );
		?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="/wp-admin/admin-post.php" method="post">
		<input type="hidden" name="action" value="apply_updates">
		<input
			type="hidden"
			name="action_nonce"
			value="<?php echo wp_create_nonce( 'mfs_apply_upd' ); ?>"
		/>
		<input type="hidden" name="update" value="all">
		<h2 class="title"><?php _e( 'Site Updates', 'mfgstories' ); ?></h2>
		<p>
			<?php _e( 'Current update level:', 'mfgstories' ); ?>
			<?php echo ( $update_level + 1 ); ?>
		</p>
		<?php
			submit_button(
				__( 'Apply pending updates', 'mfgstories' ),
				'primary',
				'update',
				false
			);
		?>
		&nbsp;
		<?php
			submit_button(
				__( 'Apply all updates', 'mfgstories' ),
				'secondary',
				'update_all',
				false
			);
		?>
	</form>
</div>
<?php
	}

	function mfgstories_admin_menu() {
		add_options_page(
			__( 'MFS Options', 'mfgstories' ),
			__( 'MFS Options', 'textdomain' ),
			'manage_options',
			'mfs_options',
			'mfgstories_admin_menu_render'
		);
	}

	add_action( 'admin_menu', 'mfgstories_admin_menu' );
}
