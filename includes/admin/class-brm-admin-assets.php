<?php
/**
 * The BRM_Admin_Assets class.
 *
 * @package Best_Restaurant_Menu/Admin
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BRM_Admin_Assets' ) ) :

	/**
	 * Admin assets.
	 *
	 * Handles back-end styles and scripts.
	 *
	 * @since 1.0.0
	 */
	class BRM_Admin_Assets {
		/**
		 * The constructor.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ), 20 );
			add_action( 'admin_enqueue_scripts', array( $this, 'styles' ), 20 );
		}

		/**
		 * Enqueues admin scripts.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function scripts() {
			// Enqueue WordPress Media.
			wp_enqueue_media();
			wp_enqueue_script( 'jquery-ui-sortable' );

			// Select2 script.
			wp_enqueue_script(
				'select2',
				BEST_RESTAURANT_MENU_ROOT_URL . 'assets/dist/vendor/select2/dist/js/select2.full.min.js',
				array( 'jquery' ),
				BEST_RESTAURANT_MENU_VER,
				true
			);

			// Nested sortable script.
			wp_enqueue_script(
				'nestedsortable',
				BEST_RESTAURANT_MENU_ROOT_URL . 'assets/dist/vendor/nestedSortable/jquery.mjs.nestedSortable.js',
				array( 'select2', 'jquery-ui-sortable' ),
				BEST_RESTAURANT_MENU_VER,
				true
			);

			// Global admin scripts.
			wp_enqueue_script(
				'brm_admin_scripts',
				BEST_RESTAURANT_MENU_ROOT_URL . 'assets/dist/js/admin/brm-admin-scripts.min.js',
				array( 'nestedsortable' ),
				BEST_RESTAURANT_MENU_VER,
				true
			);

			// Localization variables.
			wp_localize_script(
				'brm_admin_scripts',
				'brm_params',
				array(
					'ajax_url'                      => admin_url( 'admin-ajax.php' ),
					'nonce'                         => wp_create_nonce( 'brm-nonce' ),
					'render_group_form'             => BRM_Utilities::render_group_form(),
					'render_group_raw'              => BRM_Utilities::render_group_raw(),
					'render_item_form'              => BRM_Utilities::render_item_form(),
					'render_shortcode_builder_form' => BRM_Utilities::render_shortcode_builder_form(),
				)
			);
		}

		/**
		 * Enqueues admin styles.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function styles() {
			wp_enqueue_style( 'font-awesome', BEST_RESTAURANT_MENU_ROOT_URL . 'assets/dist/vendor/font-awesome/css/font-awesome.min.css', array(), BEST_RESTAURANT_MENU_VER, 'all' );
			wp_enqueue_style( 'select2-css', BEST_RESTAURANT_MENU_ROOT_URL . 'assets/dist/vendor/select2/dist/css/select2.min.css', array(), BEST_RESTAURANT_MENU_VER, 'all' );
			wp_enqueue_style( 'jquery-ui-css', BEST_RESTAURANT_MENU_ROOT_URL . 'assets/dist/vendor/jquery-ui/jquery-ui.min.css', array(), BEST_RESTAURANT_MENU_VER, 'all' );
			wp_enqueue_style( 'brm_admin_styles', BEST_RESTAURANT_MENU_ROOT_URL . 'assets/dist/css/admin/brm-admin-styles.min.css', array( 'jquery-ui-css', 'select2-css' ), BEST_RESTAURANT_MENU_VER, 'all' );
		}
	}

	return new BRM_Admin_Assets();

endif;
