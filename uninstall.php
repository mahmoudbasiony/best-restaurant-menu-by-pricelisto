<?php
/**
 * Plugin uninstall.
 *
 * @package Best_Restaurant_Menu
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // Exit if uninstall not called from WordPress.
}

/*
 * Only remove plugin data if the BRM_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 *
 * @todo Remove database tables and menu page.
 */
if ( defined( 'BRM_REMOVE_ALL_DATA' ) && true === BRM_REMOVE_ALL_DATA ) {
	global $wpdb;

	/*
	 * Checks if multisite is enabled.
	 */
	if ( is_multisite() ) {
		// Get ids of all sites.
		$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		foreach ( $blogids as $blogid ) {
			switch_to_blog( $blogid );

			// Deletes all plugin data.
			brm_remove_plugin_data();

			restore_current_blog();
		}
	} else {
		brm_remove_plugin_data();
	}
}

/**
 * Removes all plugin data from the database.
 *
 * @since 1.3.0
 *
 * @return void
 */
function brm_remove_plugin_data() {
	global $wpdb;

	// Database prefix.
	$prefix = $wpdb->prefix;

	// Custom tables.
	$tables = array(
		'brm_options' => $prefix . 'brm_options',
		'brm_items'   => $prefix . 'brm_items',
		'brm_groups'  => $prefix . 'brm_groups',
	);

	/*
	 * Remove created menu page.
	 */
	$sql      = "SELECT option_value FROM {$tables['brm_options']} WHERE option_name = 'brm_menu_settings'";
	$settings = unserialize( $wpdb->get_var( $sql ) );

	if ( isset( $settings ) && isset( $settings['menu_page_id'] ) && ! empty( $settings['menu_page_id'] ) ) {
		$menu_page_id = $settings['menu_page_id'];

		// Delete page.
		wp_delete_post( $menu_page_id, true );
	}

	/*
	 * Remove plugin custom databse tables.
	 */
	foreach ( $tables as $table ) {
		$sql = "DROP TABLE $table";
		$wpdb->query( $sql );
	}
}
