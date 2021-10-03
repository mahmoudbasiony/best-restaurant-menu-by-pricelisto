<?php
/**
 * The BRM_Admin_Menu class.
 *
 * @package Best_Restaurant_Menu/Admin
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BRM_Admin_Menu' ) ) :

	/**
	 * Admin menus.
	 *
	 * Adds menu and sub-menus pages.
	 *
	 * @since 1.0.0
	 */
	class BRM_Admin_Menu {
		/**
		 * The constructor.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			// Actions.
			add_action( 'admin_menu', array( $this, 'menu' ) );
		}

		/**
		 * Adds menu and sub-menus pages.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function menu() {
			add_menu_page(
				__( 'Menu', 'best-restaurant-menu' ),
				__( 'Menu', 'best-restaurant-menu' ),
				'manage_options',
				'brm-menu',
				array( $this, 'menu_page' ),
				'dashicons-list-view'
			);

			add_submenu_page(
				'brm-menu',
				__( 'Menu', 'best-restaurant-menu' ),
				__( 'Menu', 'best-restaurant-menu' ),
				'manage_options',
				'brm-menu',
				array( $this, 'menu_page' )
			);

			add_submenu_page(
				'brm-menu',
				__( 'Settings', 'best-restaurant-menu' ),
				__( 'Settings', 'best-restaurant-menu' ),
				'manage_options',
				'brm-settings',
				array( $this, 'menu_page' )
			);
		}

		/**
		 * Renders menu page content.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function menu_page() {
			$pages = array(
				'menu',
				'settings',
			);

			$screen = get_current_screen();

			foreach ( $pages as $page ) {
				if ( $this->validate_current_screen( $screen->base, $page ) ) {
					include_once BEST_RESTAURANT_MENU_TEMPLATE_PATH . 'admin/' . $page . '.php';

					break;
				}
			}
		}

		/**
		 * Validate current screen page.
		 *
		 * @param string $screen The screen base.
		 * @param string $page   The page name.
		 *
		 * @since 1.0.0
		 *
		 * @return boolean Whether is valid page or not.
		 */
		public function validate_current_screen( $screen, $page ) {
			$length = strlen( $page );
			if ( 0 == $length ) {
				return true;
			}

			return ( substr( $screen, -$length ) === $page );
		}

	}

	return new BRM_Admin_Menu();

endif;
