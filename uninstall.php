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

global $wpdb;

/*
 * Checks if multisite is enabled.
 */
if ( is_multisite() ) {
	// Get ids of all sites.
	$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Properly prepared SQL statement.

	foreach ( $blogids as $blogid ) {
		switch_to_blog( $blogid );

		// Deletes all plugin data.
		brm_remove_plugin_data();

		restore_current_blog();
	}
} else {
	brm_remove_plugin_data();
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
	$sql      = $wpdb->prepare( "SELECT option_value FROM %i WHERE option_name = 'brm_menu_settings'", $tables['brm_options'] );
	$settings = unserialize( $wpdb->get_var( $sql ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Properly prepared SQL statement.

	if ( isset( $settings ) && isset( $settings['menu_page_id'] ) && ! empty( $settings['menu_page_id'] ) ) {
		$menu_page_id = $settings['menu_page_id'];

		// Delete page.
		wp_delete_post( $menu_page_id, true );
	}

	/*
	 * Remove plugin custom databse tables.
	 */
	foreach ( $tables as $table ) {
		$sql = $wpdb->prepare( 'DROP TABLE %i', $table );
		$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for custom table operations, following best practices for security.
	}
}
