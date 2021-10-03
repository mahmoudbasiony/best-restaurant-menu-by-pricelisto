<?php
/**
 * The BRM_Assets class.
 *
 * @package Best_Restaurant_Menu/Assets
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BRM_Assets' ) ) :

	/**
	 * Assets.
	 *
	 * Handles front-end styles and scripts.
	 *
	 * @since 1.0.0
	 */
	class BRM_Assets {
		/**
		 * The array of templates.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		protected static $frontend_templates;

		/**
		 * The constructor.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			// Assign the frontend templates.
			self::$frontend_templates = array(
				'minimalist',
				'two-column-minimalist',
				'fancy',
				'colorful',
				'bold',
			);

			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );
		}

		/**
		 * Enqueues frontend scripts.
		 *
		 * @since   1.0.0
		 * @version 1.2.0
		 *
		 * @return void
		 */
		public function scripts() {
			// Lightbox2 scripts.
			wp_enqueue_script(
				'lightbox2',
				BEST_RESTAURANT_MENU_ROOT_URL . 'assets/dist/vendor/lightbox2/dist/js/lightbox.min.js',
				array( 'jquery' ),
				BEST_RESTAURANT_MENU_VER,
				true
			);

			/*
			 * Global front-end scripts.
			 */
			wp_enqueue_script(
				'brm_scripts',
				BEST_RESTAURANT_MENU_ROOT_URL . 'assets/dist/js/public/brm-scripts.min.js',
				array( 'lightbox2' ),
				BEST_RESTAURANT_MENU_VER,
				true
			);

			/*
			 * Registers front-end tempates scripts.
			 */
			foreach ( self::$frontend_templates as $template ) {
				wp_register_script(
					"brm_{$template}",
					BEST_RESTAURANT_MENU_ROOT_URL . "assets/dist/js/public/brm-{$template}.min.js",
					array(),
					BEST_RESTAURANT_MENU_VER,
					true
				);
			}

			/*
			 * Localization variables.
			 */
			wp_localize_script(
				'brm_scripts',
				'brm_params',
				apply_filters(
					'brm_js_params',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
					)
				)
			);
		}

		/**
		 * Enqueues frontend styles.
		 *
		 * @since   1.0.0
		 * @version 1.2.0
		 *
		 * @return void
		 */
		public function styles() {
			wp_enqueue_style( 'lightbox2', BEST_RESTAURANT_MENU_ROOT_URL . 'assets/dist/vendor/lightbox2/dist/css/lightbox.min.css', array(), false, 'all' );
			wp_enqueue_style( 'brm_styles', BEST_RESTAURANT_MENU_ROOT_URL . 'assets/dist/css/public/brm-global.min.css', array( 'lightbox2' ), false, 'all' );

			/*
			 * Registers front-end tempates styles.
			 */
			foreach ( self::$frontend_templates as $template ) {
				wp_register_style(
					"brm_{$template}",
					BEST_RESTAURANT_MENU_ROOT_URL . "assets/dist/css/public/brm-{$template}.min.css",
					array( 'brm_styles' ),
					BEST_RESTAURANT_MENU_VER,
					'all'
				);
			}
		}
	}

	return new BRM_Assets();

endif;
