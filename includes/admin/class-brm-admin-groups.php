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
			// Actions
			add_action( 'wp_ajax_brm_save_group', array( $this, 'save_groups' ) );
			add_action( 'wp_ajax_brm_edit_group', array( $this, 'edit_group' ) );
			add_action( 'wp_ajax_brm_delete_group', array( $this, 'delete_group' ) );
			add_action( 'wp_ajax_brm_order_nesting_groups_items', array( $this, 'order_nesting_groups_items' ) );
		}

		/**
		 * Re order/nesting groups and items.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function order_nesting_groups_items() {
			global $wpdb;

			// Check for nonce security.
			if ( ! wp_verify_nonce( $_POST['nonce'], 'brm-nonce' ) ) {
				wp_die( esc_html__( 'Action failed due to security issues, please try again later', 'best-restaurant-menu' ) );
			}

			if ( isset( $_POST ) && ! empty( $_POST['action'] ) && 'brm_order_nesting_groups_items' === $_POST['action'] ) {
				$sorting_data = isset( $_POST['sorting_data'] ) ? json_decode( stripcslashes( $_POST['sorting_data'] ) ) : 0;

				$ids = array();

				if ( $sorting_data ) {
					$group_table = $wpdb->prefix . 'brm_groups';

					$sql = "UPDATE $group_table SET ";

					$order_sql   = 'sort = CASE id ';
					$nesting_sql = 'parent_id = CASE id ';

					foreach ( $sorting_data as $data ) {
						if ( $data->group_id ) {
							$order_sql   .= "when '{$data->group_id}' then '{$data->order}' ";
							$nesting_sql .= "when '{$data->group_id}' then '{$data->parent_id}' ";

							$ids[] = $data->group_id;
						}
					}

					$imploded_ids = implode( ',', $ids );
					$sql         .= $order_sql . 'end, ' . $nesting_sql . "end where id IN ($imploded_ids)";
				}

				$result['groups_query'] = $sql;

				if ( $wpdb->query( $sql ) ) {
					$result['groups_status'] = 'reordered';
				} else {
					$result['groups_status'] = 'failed';
				}

				$result = $this->order_nesting_items( $sorting_data, $result );

				$result['menu'] = BRM_Utilities::render_menu_backend( 0, BRM_Utilities::get_menu_array() );

				wp_send_json_success( $result );
			}
		}

		/**
		 * Re orders/sorting items.
		 *
		 * @param array $sorting_data The sorting data
		 * @param array $result       The result json array.
		 *
		 * @since 1.0.0
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

					if ( $wpdb->query( $sql ) ) {
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
		 *
		 * @return void
		 */
		public function delete_group() {
			global $wpdb;

			// Check for nonce security.
			if ( ! wp_verify_nonce( $_POST['nonce'], 'brm-nonce' ) ) {
				wp_die( esc_html__( 'Action failed due to security issues, please try again later', 'best-restaurant-menu' ) );
			}

			if ( isset( $_POST ) && ! empty( $_POST['action'] ) && 'brm_delete_group' === $_POST['action'] ) {
				$group_id = (int) isset( $_POST['group_id'] ) ? sanitize_text_field( $_POST['group_id'] ) : 0;

				$group_table = $wpdb->prefix . 'brm_groups';

				if ( $wpdb->delete(
					$group_table,
					array(
						'id' => $group_id,
					),
					array(
						'%d',
					)
				) ) {
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
		 *
		 * @return void
		 */
		public function edit_group() {
			global $wpdb;

			// Check for nonce security.
			if ( ! wp_verify_nonce( $_POST['nonce'], 'brm-nonce' ) ) {
				wp_die( esc_html__( 'Action failed due to security issues, please try again later', 'best-restaurant-menu' ) );
			}

			if ( isset( $_POST ) && ! empty( $_POST['action'] ) && 'brm_edit_group' === $_POST['action'] ) {
				$group_id  = isset( $_POST['group_id'] ) ? sanitize_text_field( $_POST['group_id'] ) : 0;
				$order     = isset( $_POST['order'] ) ? sanitize_text_field( $_POST['order'] ) : 0;
				$parent_id = isset( $_POST['parent_id'] ) ? sanitize_text_field( $_POST['parent_id'] ) : 0;

				$group_table = $wpdb->prefix . 'brm_groups';
				$sql         = "SELECT * FROM $group_table WHERE $group_table.id = '{$group_id}'";

				$group = $wpdb->get_results( $sql );

				if ( ! empty( $group ) ) {
					$result['form'] = BRM_Utilities::render_group_form( $order, $group[0], $parent_id );

					$result['order']     = $order;
					$result['group']     = json_encode( $group );
					$result['parent_id'] = $parent_id;

					wp_send_json_success( $result );
				}
			}
		}

		/**
		 * Save group.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function save_groups() {
			global $wpdb;
			$group_table = $wpdb->prefix . 'brm_groups';

			// Check for nonce security.
			if ( ! wp_verify_nonce( $_POST['nonce'], 'brm-nonce' ) ) {
				wp_die( esc_html__( 'Action failed due to security issues, please try again later', 'best-restaurant-menu' ) );
			}

			if ( isset( $_POST ) && ! empty( $_POST['action'] ) && 'brm_save_group' === $_POST['action'] ) {
				$group_name = isset( $_POST['group_name'] ) ? sanitize_text_field( $_POST['group_name'] ) : '';
				$group_desc = isset( $_POST['group_desc'] ) ? sanitize_textarea_field( $_POST['group_desc'] ) : '';
				$parent_id  = isset( $_POST['parent_id'] ) ? sanitize_text_field( $_POST['parent_id'] ) : 0;
				$order      = isset( $_POST['order'] ) ? sanitize_text_field( $_POST['order'] ) : '';
				$created_at = current_time( 'mysql' );
				$updated_at = current_time( 'mysql' );

				// Validate group name.
				if ( ! $group_name || '' == $group_name ) {
					$result['status']  = 'error';
					$result['message'] = __( 'Group Name is a required field!', 'best-restaurant-menu' );
					wp_send_json_error( $result );
				}

				if ( isset( $_POST['group_id'] ) && ! empty( $_POST['group_id'] ) ) {
					$group_id = (int) sanitize_text_field( $_POST['group_id'] );

					if ( $wpdb->update(
						$group_table,
						array(
							'name'        => $group_name,
							'description' => $group_desc,
							'sort'        => $order,
							'parent_id'   => $parent_id,
							'updated_at'  => $updated_at,
						),
						array( 'id' => $group_id ),
						array(
							'%s',
							'%s',
							'%d',
							'%d',
							'%s',
						),
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
						"INSERT INTO $group_table (name, description, sort, parent_id, created_at, updated_at) VALUES ( %s, %s, %d, %d, %s, %s )",
						array(
							$group_name,
							$group_desc,
							$order,
							$parent_id,
							$created_at,
							$updated_at,
						)
					);

					if ( $wpdb->query( $sql ) ) {
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
