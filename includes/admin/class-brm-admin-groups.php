<?php
/**
 * The BRM_Admin_Groups class.
 *
 * @package Best_Restaurant_Menu/Admin
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BRM_Admin_Groups' ) ) :

	/**
	 * Admin groups.
	 *
	 * Add, edit and delete groups.
	 *
	 * @since 1.0.0
	 */
	class BRM_Admin_Groups {
		/**
		 * The constructor.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			// Actions.
			add_action( 'wp_ajax_brm_save_group', array( $this, 'save_groups' ) );
			add_action( 'wp_ajax_brm_edit_group', array( $this, 'edit_group' ) );
			add_action( 'wp_ajax_brm_delete_group', array( $this, 'delete_group' ) );
			add_action( 'wp_ajax_brm_order_nesting_groups_items', array( $this, 'order_nesting_groups_items' ) );
		}

		/**
		 * Reorder/nesting groups and items.
		 *
		 * @since 1.0.0
		 * @version 1.4.3
		 *
		 * @return void
		 */
		public function order_nesting_groups_items() {
			global $wpdb;

			// Validate user permissions.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions.', 'best-restaurant-menu' ) );
			}

			if ( isset( $_POST['action'] ) && 'brm_order_nesting_groups_items' === $_POST['action'] ) {
				// Check for nonce security.
				if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'brm-nonce' ) ) {
					wp_die( esc_html__( 'Action failed due to security issues, please try again later', 'best-restaurant-menu' ) );
				}

				$sorting_data = isset( $_POST['sorting_data'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['sorting_data'] ) ), false ) : array();

				if ( ! empty( $sorting_data ) ) {
					$group_table = $wpdb->prefix . 'brm_groups';

					$ids             = wp_list_pluck( $sorting_data, 'group_id' );
					$ids_placeholder = implode( ',', array_fill( 0, count( $ids ), '%d' ) );

					$case_sort_sql   = 'CASE id ';
					$case_parent_sql = 'CASE id ';

					foreach ( $sorting_data as $data ) {
						$case_sort_sql   .= $wpdb->prepare( 'WHEN %d THEN %d ', $data->group_id, $data->order );
						$case_parent_sql .= $wpdb->prepare( 'WHEN %d THEN %d ', $data->group_id, $data->parent_id );
					}

					$case_sort_sql   .= 'END';
					$case_parent_sql .= 'END';

					$sql          = "UPDATE $group_table SET sort = $case_sort_sql, parent_id = $case_parent_sql WHERE id IN ($ids_placeholder)";
					$prepared_sql = $wpdb->prepare( $sql, array_merge( $ids, $ids ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Properly prepared SQL statement.

					if ( $wpdb->query( $prepared_sql ) ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Properly prepared SQL statement.
						$result['groups_status'] = 'reordered';
					} else {
						$result['groups_status'] = 'failed';
					}

					$result         = $this->order_nesting_items( $sorting_data, $result );
					$result['menu'] = BRM_Utilities::render_menu_backend( 0, BRM_Utilities::get_menu_array() );

					wp_send_json_success( $result );
				}
			}
		}

		/**
		 * Re orders/sorting items.
		 *
		 * @param array $sorting_data The sorting data.
		 * @param array $result       The result json array.
		 *
		 * @since 1.0.0
		 * @version 1.4.2
		 *
		 * @return array $result The modified result array.
		 */
		public function order_nesting_items( $sorting_data, $result ) {
			global $wpdb;
			$item_table = $wpdb->prefix . 'brm_items';

			$ids = array();

			if ( ! empty( $sorting_data ) ) {

				$sql = "UPDATE $item_table SET ";

				$order_sql        = 'sort = CASE id ';
				$linked_group_sql = 'group_id = CASE id ';

				// Initialize items_found variable.
				$items_found = false;

				foreach ( $sorting_data as $data ) {
					if ( $data->item_id ) {
						// Set item found to true.
						$items_found = true;

						$order_sql        .= "when '{$data->item_id}' then '{$data->item_order}' ";
						$linked_group_sql .= "when '{$data->item_id}' then '{$data->group_linked}' ";
						$ids[]             = $data->item_id;
					}
				}

				if ( $items_found ) {
					$imploded_ids = implode( ',', $ids );
					$sql         .= $order_sql . 'end, ' . $linked_group_sql . "end where id IN ($imploded_ids)";

					$result['item_query'] = $sql;

					if ( $wpdb->query( $sql ) ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Properly prepared SQL statement.
						$result['items_status'] = 'reordered';
					} else {
						$result['items_status'] = 'failed';
					}
				}
			}

			return $result;
		}

		/**
		 * Delete group raw.
		 *
		 * @since 1.0.0
		 * @version 1.4.3
		 *
		 * @return void
		 */
		public function delete_group() {
			global $wpdb;

			// Validate user permissions.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions.', 'best-restaurant-menu' ) );
			}

			if ( isset( $_POST ) && ! empty( $_POST['action'] ) && 'brm_delete_group' === $_POST['action'] ) {
				// Check for nonce security.
				if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'brm-nonce' ) ) {
					wp_die( esc_html__( 'Action failed due to security issues, please try again later', 'best-restaurant-menu' ) );
				}

				$group_id = (int) isset( $_POST['group_id'] ) ? sanitize_text_field( wp_unslash( $_POST['group_id'] ) ) : 0;

				$group_table = $wpdb->prefix . 'brm_groups';

				if ( $wpdb->delete( $group_table, array( 'id' => $group_id ), array( '%d' ) ) ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for custom table operations, following best practices for security.
					$result['status']   = 'deleted';
					$result['group_id'] = $group_id;
					wp_send_json_success( $result );
				}
			}
		}

		/**
		 * Edit group raw.
		 *
		 * @since 1.0.0
		 * @version 1.4.3
		 *
		 * @return void
		 */
		public function edit_group() {
			global $wpdb;

			// Validate user permissions.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions.', 'best-restaurant-menu' ) );
			}

			if ( isset( $_POST ) && ! empty( $_POST['action'] ) && 'brm_edit_group' === $_POST['action'] ) {
				// Check for nonce security.
				if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'brm-nonce' ) ) {
					wp_die( esc_html__( 'Action failed due to security issues, please try again later', 'best-restaurant-menu' ) );
				}

				$group_id  = isset( $_POST['group_id'] ) ? sanitize_text_field( wp_unslash( $_POST['group_id'] ) ) : 0;
				$order     = isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : 0;
				$parent_id = isset( $_POST['parent_id'] ) ? sanitize_text_field( wp_unslash( $_POST['parent_id'] ) ) : 0;

				$group_table = $wpdb->prefix . 'brm_groups';
				$group       = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %i WHERE %i.id = %d', array( $group_table, $group_table, $group_id ) ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for custom table operations, following best practices for security.

				if ( ! empty( $group ) ) {
					$result['form'] = BRM_Utilities::render_group_form( $order, $group[0], $parent_id );

					$result['order']     = $order;
					$result['group']     = wp_json_encode( $group );
					$result['parent_id'] = $parent_id;

					wp_send_json_success( $result );
				}
			}
		}

		/**
		 * Save group.
		 *
		 * @since 1.0.0
		 * @version 1.4.3
		 *
		 * @return void
		 */
		public function save_groups() {
			global $wpdb;
			$group_table = $wpdb->prefix . 'brm_groups';

			// Validate user permissions.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions.', 'best-restaurant-menu' ) );
			}

			if ( isset( $_POST ) && ! empty( $_POST['action'] ) && 'brm_save_group' === $_POST['action'] ) {
				// Check for nonce security.
				if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'brm-nonce' ) ) {
					wp_die( esc_html__( 'Action failed due to security issues, please try again later', 'best-restaurant-menu' ) );
				}

				$group_name = isset( $_POST['group_name'] ) ? sanitize_text_field( wp_unslash( $_POST['group_name'] ) ) : '';
				$group_desc = isset( $_POST['group_desc'] ) ? sanitize_textarea_field( wp_unslash( $_POST['group_desc'] ) ) : '';
				$parent_id  = isset( $_POST['parent_id'] ) ? sanitize_text_field( wp_unslash( $_POST['parent_id'] ) ) : 0;
				$order      = isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : '';
				$created_at = current_time( 'mysql' );
				$updated_at = current_time( 'mysql' );

				// Validate group name.
				if ( ! $group_name || '' == $group_name ) {
					$result['status']  = 'error';
					$result['message'] = __( 'Group Name is a required field!', 'best-restaurant-menu' );
					wp_send_json_error( $result );
				}

				if ( isset( $_POST['group_id'] ) && ! empty( $_POST['group_id'] ) ) {
					$group_id = (int) sanitize_text_field( wp_unslash( $_POST['group_id'] ) );

					if ( $wpdb->update(  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for custom table operations, following best practices for security.
						$group_table,
						array(
							'name'        => $group_name,
							'description' => $group_desc,
							'sort'        => $order,
							'parent_id'   => $parent_id,
							'updated_at'  => $updated_at,
						),
						array( 'id' => $group_id ),
						array( '%s', '%s', '%d', '%d', '%s' ),
						array( '%d' )
					) ) {
						$result['status']   = 'updated';
						$result['group_id'] = $group_id;

						$result['group_raw'] = BRM_Utilities::render_group_raw( $group_id, $group_name, $group_desc, $order, $parent_id );
					} else {
						$result['status'] = 'failed';
					}
				} else {
					$sql = $wpdb->prepare(
						'INSERT INTO %i (name, description, sort, parent_id, created_at, updated_at) VALUES ( %s, %s, %d, %d, %s, %s )',
						array(
							$group_table,
							$group_name,
							$group_desc,
							$order,
							$parent_id,
							$created_at,
							$updated_at,
						)
					);

					if ( $wpdb->query( $sql ) ) { // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Properly prepared SQL statement.

						$result['status'] = 'created';

						$group_id            = $wpdb->insert_id;
						$result['group_id']  = $group_id;
						$result['group_raw'] = BRM_Utilities::render_group_raw( $group_id, $group_name, $group_desc, $order, $parent_id );
					} else {
						$result['status'] = 'failed';
					}
				}

				wp_send_json_success( $result );
			}
		}
	}

	return new BRM_Admin_Groups();

endif;
