<?php
/**
 * The BRM_Admin_Items class.
 *
 * @package Best_Restaurant_Menu/Admin
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BRM_Admin_Items' ) ) :

	/**
	 * Admin items.
	 *
	 * Add, edit and delete item.
	 *
	 * @since 1.0.0
	 */
	class BRM_Admin_Items {
		/**
		 * The constructor.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			// Actions.
			add_action( 'wp_ajax_brm_save_item', array( $this, 'save_item' ) );
			add_action( 'wp_ajax_brm_edit_item', array( $this, 'edit_item' ) );
			add_action( 'wp_ajax_brm_delete_item', array( $this, 'delete_item' ) );
		}

		/**
		 * Delete item
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function delete_item() {
			global $wpdb;

			$item_table = $wpdb->prefix . 'brm_items';

			// Check for nonce security.
			if ( ! wp_verify_nonce( $_POST['nonce'], 'brm-nonce' ) ) {
				wp_die( esc_html__( 'Action failed due to security issues, please try again later', 'best-restaurant-menu' ) );
			}

			if ( isset( $_POST ) && isset( $_POST['action'] ) && 'brm_delete_item' === $_POST['action'] ) {
				$item_id = isset( $_POST['item_id'] ) ? (int) sanitize_text_field( $_POST['item_id'] ) : 0;

				if ( $wpdb->delete(
					$item_table,
					array(
						'id' => $item_id,
					),
					array( '%d' )
				) ) {
					$result['status']  = 'deleted';
					$result['item_id'] = $item_id;

					wp_send_json_success( $result );
				}
			}
		}

		/**
		 * Edit item.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function edit_item() {
			global $wpdb;
			$item_table = $wpdb->prefix . 'brm_items';

			// Check for nonce security.
			if ( ! wp_verify_nonce( $_POST['nonce'], 'brm-nonce' ) ) {
				wp_die( esc_html__( 'Action failed due to security issues, please try again later', 'best-restaurant-menu' ) );
			}

			if ( isset( $_POST ) && ! empty( $_POST['action'] ) && 'brm_edit_item' === $_POST['action'] ) {
				$item_id  = isset( $_POST['item_id'] ) ? (int) sanitize_text_field( $_POST['item_id'] ) : 0;
				$order    = isset( $_POST['order'] ) ? (int) sanitize_text_field( $_POST['order'] ) : 0;
				$group_id = isset( $_POST['group_id'] ) ? (int) sanitize_text_field( $_POST['group_id'] ) : 0;

				$sql = "SELECT * FROM $item_table WHERE $item_table.id = '{$item_id}'";

				$item = $wpdb->get_results( $sql );

				if ( ! empty( $item ) ) {
					$result['form']     = BRM_Utilities::render_item_form( $order, $item[0], $group_id );
					$result['order']    = $order;
					$result['item']     = json_encode( $item );
					$result['group_id'] = $group_id;

					wp_send_json_success( $result );
				}
			}
		}

		/**
		 * Save Item.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function save_item() {
			global $wpdb;
			$item_table = $wpdb->prefix . 'brm_items';

			// Check for nonce security.
			if ( ! wp_verify_nonce( $_POST['nonce'], 'brm-nonce' ) ) {
				wp_die( esc_html__( 'Action failed due to security issues, please try again later', 'best-restaurant-menu' ) );
			}

			if ( isset( $_POST ) && ! empty( $_POST['action'] ) && 'brm_save_item' === $_POST['action'] ) {
				$item_name  = isset( $_POST['item_name'] ) ? sanitize_text_field( $_POST['item_name'] ) : 0;
				$item_desc  = isset( $_POST['item_desc'] ) ? sanitize_textarea_field( $_POST['item_desc'] ) : 0;
				$image_id   = isset( $_POST['image_id'] ) ? (int) sanitize_text_field( $_POST['image_id'] ) : 0;
				$price      = isset( $_POST['price'] ) ? sanitize_text_field( $_POST['price'] ) : 0;
				$order      = isset( $_POST['order'] ) ? sanitize_text_field( $_POST['order'] ) : 0;
				$group_id   = isset( $_POST['group_id'] ) ? sanitize_text_field( $_POST['group_id'] ) : 0;
				$created_at = current_time( 'mysql' );
				$updated_at = current_time( 'mysql' );

				// Validate item name.
				if ( ! $item_name || '' == $item_name ) {
					$result['status']  = 'error';
					$result['class']   = 'item-name';
					$result['message'] = __( 'Item Name is a required field!', 'best-restaurant-menu' );
					wp_send_json_error( $result );
				}

				// Validate price number.
				if ( ! empty( $price ) && 0 !== $price && ! is_numeric( $price ) ) {
					$result['status']  = 'error';
					$result['class']   = 'item-price';
					$result['message'] = __( 'Price is a numeric field!', 'best-restaurant-menu' );
					wp_send_json_error( $result );
				}

				if ( isset( $_POST['item_id'] ) && ! empty( $_POST['item_id'] ) ) {
					$item_id = (int) $_POST['item_id'];

					if ( $wpdb->update(
						$item_table,
						array(
							'name'        => $item_name,
							'description' => $item_desc,
							'image_id'    => $image_id,
							'price'       => round( $price, 2 ),
							'sort'        => $order,
							'group_id'    => $group_id,
							'updated_at'  => $updated_at,
						),
						array( 'id' => $item_id ),
						array(
							'%s',
							'%s',
							'%d',
							'%f',
							'%d',
							'%d',
							'%s',
						),
						array( '%d' )
					) ) {
						$result['status']  = 'updated';
						$result['item_id'] = $item_id;

						$result['item_raw'] = BRM_Utilities::render_item_raw( $item_id, $item_name, $item_desc, $image_id, $price, $order, $group_id );
					} else {
						$result['status'] = 'failed';
					}
				} else {

					$sql = $wpdb->prepare(
						"INSERT INTO $item_table (name, description, image_id, price, sort, group_id, created_at, updated_at) VALUES ( %s, %s, %d, %f, %d, %d, %s, %s )",
						array(
							$item_name,
							$item_desc,
							$image_id,
							$price,
							$order,
							$group_id,
							$created_at,
							$updated_at,
						)
					);

					if ( $wpdb->query( $sql ) ) {
						$result['status'] = 'created';

						$item_id            = $wpdb->insert_id;
						$result['item_id']  = $item_id;
						$result['item_raw'] = BRM_Utilities::render_item_raw( $item_id, $item_name, $item_desc, $image_id, $price, $order, $group_id );
					} else {
						$result['status'] = 'failed';
					}
				}

				wp_send_json_success( $result );
			}
		}
	}

	return new BRM_Admin_Items();

endif;
