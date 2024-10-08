<?php
/**
 * The BRM_Admin_Notices class.
 *
 * @package Best_Restaurant_Menu/Admin
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BRM_Admin_Notices' ) ) :

	/**
	 * Admin notices.
	 *
	 * Handles admin notices.
	 *
	 * @since 1.0.0
	 */
	class BRM_Admin_Notices {
		/**
		 * Notices array.
		 *
		 * @var array
		 */
		public $notices = array();

		/**
		 * The constructor.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'wp_loaded', array( $this, 'hide_notices' ) );
		}

		/**
		 * Adds slug keyed notices (to avoid duplication).
		 *
		 * @since 1.0.0
		 *
		 * @param string $slug        Notice slug.
		 * @param string $class       CSS class.
		 * @param string $message     Notice body.
		 * @param bool   $dismissible Optional. Allow/disallow dismissing the notice. Default false.
		 *
		 * @return void
		 */
		public function add_admin_notice( $slug, $class, $message, $dismissible = false ) {
			$this->notices[ $slug ] = array(
				'class'       => $class,
				'message'     => $message,
				'dismissible' => $dismissible,
			);
		}

		/**
		 * Displays the notices.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function admin_notices() {
			// Exit if user has no privilges.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Basic checks.
			$this->check_environment();

			// Display the notices collected so far.
			foreach ( (array) $this->notices as $notice_key => $notice ) {
				echo '<div class="' . esc_attr( $notice['class'] ) . '" style="position:relative;">';

				if ( $notice['dismissible'] ) {
					echo '<a href="' . esc_url( wp_nonce_url( add_query_arg( 'brm-hide-notice', $notice_key ), 'brm_hide_notices_nonce', '_brm_notice_nonce' ) ) . '" class="woocommerce-message-close notice-dismiss" style="position:absolute;right:1px;padding:9px;text-decoration:none;"></a>';
				}

				echo '<p>' . wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) ) . '</p>';

				echo '</div>';
			}
		}

		/**
		 * Handles all the basic checks.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function check_environment() {
			$show_phpver_notice = get_option( 'brm_show_phpver_notice' );
			$show_wpver_notice  = get_option( 'brm_show_wpver_notice' );

			if ( empty( $show_phpver_notice ) ) {
				if ( version_compare( phpversion(), BEST_RESTAURANT_MENU_MIN_PHP_VER, '<' ) ) {
					/* translators: 1) int version 2) int version */
					$message = __( 'Best Restaurant Menu Extension - The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'best-restaurant-menu' );
					$this->add_admin_notice( 'phpver', 'error', sprintf( $message, BEST_RESTAURANT_MENU_MIN_PHP_VER, phpversion() ), true );
				}
			}

			if ( empty( $show_wpver_notice ) ) {
				global $wp_version;

				if ( version_compare( $wp_version, BEST_RESTAURANT_MENU_MIN_WP_VER, '<' ) ) {
					/* translators: 1) int version 2) int version */
					$message = __( 'Best Restaurant Menu Extension - The minimum WordPress version required for this plugin is %1$s. You are running %2$s.', 'best-restaurant-menu' );
					$this->add_admin_notice( 'wpver', 'notice notice-warning', sprintf( $message, BEST_RESTAURANT_MENU_MIN_WP_VER, $wp_version ), true );
				}
			}
		}

		/**
		 * Hides any admin notices.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function hide_notices() {
			if ( isset( $_GET['brm-hide-notice'] ) && isset( $_GET['_brm_notice_nonce'] ) ) {
				// Check for nonce security.
				if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_brm_notice_nonce'] ) ), 'brm_hide_notices_nonce' ) ) {
					wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'best-restaurant-menu' ) );
				}

				if ( ! current_user_can( 'manage_options' ) ) {
					wp_die( esc_html__( 'Cheatin&#8217; huh?', 'best-restaurant-menu' ) );
				}

				$notice = wc_clean( sanitize_text_field( wp_unslash( $_GET['brm-hide-notice'] ) ) );

				switch ( $notice ) {
					case 'phpver':
						update_option( 'brm_show_phpver_notice', 'no' );
						break;
					case 'wpver':
						update_option( 'brm_show_wpver_notice', 'no' );
						break;
				}
			}
		}
	}

	new BRM_Admin_Notices();

endif;
