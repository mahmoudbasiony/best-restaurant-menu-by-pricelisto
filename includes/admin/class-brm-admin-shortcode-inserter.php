<?php
/**
 * The BRM_Admin_Shortcode_Inserter class.
 *
 * @package Best_Restaurant_Menu/Admin
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BRM_Admin_Shortcode_Inserter' ) ) :

	/**
	 * Shortcode inserter.
	 *
	 * Adds shortcode inserter to tinymce editor.
	 *
	 * @since 1.0.0
	 */
	class BRM_Admin_Shortcode_Inserter {
		/**
		 * The constructor.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			// Actions.
			add_action( 'admin_init', array( $this, 'add_inserter_button' ) );
			add_action( 'wp_ajax_brm_shortcode_builder_form', array( $this, 'shortcode_builder_form' ) );
		}

		/**
		 * Shortcode builder form
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function shortcode_builder_form() {
			// Check for nonce security.
			if ( ! wp_verify_nonce( $_POST['nonce'], 'brm-nonce' ) ) {
				wp_die( esc_html__( 'Cheatin&#8217; huh?', 'best-restaurant-menu' ) );
			}

			if ( isset( $_POST ) && isset( $_POST['action'] ) && 'brm_shortcode_builder_form' === $_POST['action'] ) {
				// Initialize result array.
				$result                   = array();
				$result['status']         = '200';
				$result['shortcode_form'] = BRM_Utilities::render_shortcode_builder_form();

				wp_send_json_success( $result );
			}
		}

		/**
		 * Register shortcode inserter button.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function add_inserter_button() {
			// Validate editing posts/pages capablities.
			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
				return;
			}

			// Check if WYSIWYG editor is enabled.
			if ( 'true' == get_user_option( 'rich_editing' ) ) {
				add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ), 10, 1 );
				add_filter( 'mce_buttons', array( $this, 'register_mce_button' ), 10, 1 );
			}
		}

		/**
		 * Adds shortcode inserter button to wysiwyg editor.
		 *
		 * @param array $buttons The current editor buttons.
		 *
		 * @since 1.0.0
		 *
		 * @return array $buttons The updated buttons
		 */
		public function register_mce_button( $buttons ) {
			array_push( $buttons, 'brm_add_menu' );
			return $buttons;
		}

		/**
		 * Adds mce external plugin scripts.
		 *
		 * @param array $plugin_array The plugin array.
		 *
		 * @since 1.0.0
		 *
		 * @return array
		 */
		public function mce_external_plugins( $plugin_array ) {
			$plugin_array['brm_restaurant_menu'] = BEST_RESTAURANT_MENU_ROOT_URL . 'assets/dist/js/admin/brm-admin-mce-script.min.js';
			return $plugin_array;
		}

	}

	return new BRM_Admin_Shortcode_Inserter();

endif;
