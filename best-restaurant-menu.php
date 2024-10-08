<?php
/**
 * Plugin Name: Great Restaurant Menu WP
 * Plugin URI: https://www.pricelisto.com/plugins
 * Description: The fastest and easiest way to create professional-looking menu or price list for your restaurant or business. Includes five menu templates and support for custom templates as well.
 * Version: 1.4.3
 * Author: PriceListo
 * Author URI: https://www.pricelisto.com
 * Requires at least: 6.2.0
 * Tested up to: 6.6.2
 *
 * Text Domain: best-restaurant-menu-by-pricelisto
 * Domain Path: /languages/
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Best_Restaurant_Menu
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * Globals constants.
 */
define( 'BEST_RESTAURANT_MENU_MIN_PHP_VER', '5.6.0' );
define( 'BEST_RESTAURANT_MENU_MIN_WP_VER', '6.2.0' );
define( 'BEST_RESTAURANT_MENU_VER', '1.4.3' );
define( 'BEST_RESTAURANT_MENU_ROOT_URL', plugin_dir_url( __FILE__ ) );
define( 'BEST_RESTAURANT_MENU_ROOT_PATH', plugin_dir_path( __FILE__ ) );
define( 'BEST_RESTAURANT_MENU_TEMPLATE_PATH', plugin_dir_path( __FILE__ ) . 'templates/' );

if ( ! class_exists( 'Best_Restaurant_Menu' ) ) :

	/**
	 * The main class.
	 *
	 * @since 1.0.0
	 */
	class Best_Restaurant_Menu {
		/**
		 * Menu database version.
		 *
		 * @var string
		 */
		private static $db_version = '1.0.0';

		/**
		 * Custom Menu tables.
		 *
		 * @var array
		 */
		private static $tables = array(
			'brm_options',
			'brm_groups',
			'brm_items',
		);

		/**
		 * The singelton instance of Best_Restaurant_Menu.
		 *
		 * @since 1.0.0
		 *
		 * @var Best_Restaurant_Menu
		 */
		private static $instance = null;

		/**
		 * Returns the singelton instance of Best_Restaurant_Menu.
		 *
		 * Ensures only one instance of Best_Restaurant_Menu is/can be loaded.
		 *
		 * @since 1.0.0
		 *
		 * @return Best_Restaurant_Menu
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * The constructor.
		 *
		 * Private constructor to make sure it can not be called directly from outside the class.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function __construct() {

			$this->includes();
			$this->hooks();

			/**
			 * The best_restaurant_menu_loaded hook.
			 *
			 * @since 1.0.0
			 */
			do_action( 'best_restaurant_menu_loaded' );
		}

		/**
		 * Includes the required files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function includes() {
			// Global includes.
			include_once BEST_RESTAURANT_MENU_ROOT_PATH . 'includes/class-brm-utilities.php';
			include_once BEST_RESTAURANT_MENU_ROOT_PATH . 'includes/class-brm-menu-template.php';
			if ( is_admin() ) {
				// Back-end only includes.
				include_once BEST_RESTAURANT_MENU_ROOT_PATH . 'includes/admin/class-brm-admin-notices.php';
				include_once BEST_RESTAURANT_MENU_ROOT_PATH . 'includes/admin/class-brm-admin-assets.php';
				include_once BEST_RESTAURANT_MENU_ROOT_PATH . 'includes/admin/class-brm-admin-menu.php';
				include_once BEST_RESTAURANT_MENU_ROOT_PATH . 'includes/admin/class-brm-admin-groups.php';
				include_once BEST_RESTAURANT_MENU_ROOT_PATH . 'includes/admin/class-brm-admin-items.php';
				include_once BEST_RESTAURANT_MENU_ROOT_PATH . 'includes/admin/class-brm-admin-shortcode-inserter.php';
			} else {
				// Front-end only includes.
				include_once BEST_RESTAURANT_MENU_ROOT_PATH . 'includes/class-brm-assets.php';
				include_once BEST_RESTAURANT_MENU_ROOT_PATH . 'includes/class-brm-shortcode.php';
			}
		}

		/**
		 * Plugin hooks.
		 *
		 * @since   1.0.0
		 * @version 1.3.0
		 *
		 * @return void
		 */
		public function hooks() {
			// Actions.
			add_action( 'wp_initialize_site', array( $this, 'new_site_added' ), 900, 1 );
			add_action( 'wp_delete_site', array( $this, 'site_deleted' ), 10, 1 );
		}

		/**
		 * Create database structure.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function create_structure() {
			global $wpdb;

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			$current_db_version = self::check_table_exists( 'brm_options' ) ? $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->prefix}brm_options WHERE option_name = %s", 'brm_db_version' ) ) : '0.0.0'; // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for custom table operations, following best practices for security.

			if ( version_compare( self::$db_version, $current_db_version, '>' ) ) {
				foreach ( self::$tables as $table ) {
					if ( ! self::check_table_exists( $table ) ) {
						self::create_table( $table );
					}
				}

				// Update database version option.
				$sql = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}brm_options (option_value, option_name) VALUES (%s, %s)", self::$db_version, 'brm_db_version' );

				$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for custom table operations, following best practices for security.
			}
		}

		/**
		 * Check table exists
		 *
		 * @param string $table_name The custom table name.
		 *
		 * @since 1.0.0
		 *
		 * @return bool Whether the table exist or not.
		 */
		public static function check_table_exists( $table_name ) {
			global $wpdb;

			$table_name_with_prefix = $wpdb->prefix . $table_name;

			$sql = $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name_with_prefix );

			if ( $wpdb->get_var( $sql ) != $table_name_with_prefix ) {  // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for custom table operations, following best practices for security.
				return false;
			}

			return true;
		}

		/**
		 * Create Table
		 *
		 * @param string $table_name The custom table name.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function create_table( $table_name ) {
			global $wpdb;

			$charset_collate        = $wpdb->get_charset_collate();
			$table_name_with_prefix = $wpdb->prefix . $table_name;

			$sql = self::get_table_structure( $table_name, $table_name_with_prefix, $charset_collate );
			if ( ! empty( $sql ) ) {
				dbDelta( $sql );
			}
		}

		/**
		 * Get table structure
		 *
		 * @param string $table_name The base table name.
		 * @param string $table_name_with_prefix The prefixed table name.
		 * @param string $charset_collate The charset.
		 *
		 * @since 1.0.0
		 *
		 * @return mixed
		 */
		public static function get_table_structure( $table_name, $table_name_with_prefix, $charset_collate ) {
			$sql = '';
			switch ( $table_name ) {
				case 'brm_options':
					$sql = self::create_brm_options_table( $table_name_with_prefix, $charset_collate );
					break;
				case 'brm_groups':
					$sql = self::create_brm_groups_table( $table_name_with_prefix, $charset_collate );
					break;
				case 'brm_items':
					$sql = self::create_brm_items_table( $table_name_with_prefix, $charset_collate );
					break;
				default:
					break;
			}
			return $sql;
		}

		/**
		 * Create custom options table
		 *
		 * @param string $table_name_with_prefix The prefixed table name.
		 * @param string $charset_collate        The charset.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public static function create_brm_options_table( $table_name_with_prefix, $charset_collate ) {
			$sql = "CREATE TABLE {$table_name_with_prefix} (
				option_id bigint(20) unsigned NOT NULL auto_increment,
				option_name varchar(191) NOT NULL default '',
				option_value longtext NOT NULL,
				autoload varchar(20) NOT NULL default 'yes',
				PRIMARY KEY  (option_id),
				UNIQUE KEY option_name (option_name),
				KEY autoload (autoload)
				) {$charset_collate};";

			return $sql;
		}

		/**
		 * Create custom groups table.
		 *
		 * @param string $table_name_with_prefix The prefixed table name.
		 * @param string $charset_collate        The charset.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public static function create_brm_groups_table( $table_name_with_prefix, $charset_collate ) {
			$sql = "CREATE TABLE {$table_name_with_prefix} (
				id bigint(20) unsigned NOT NULL auto_increment,
				name text NOT NULL,
				description longtext NOT NULL,
				sort int(11) NOT NULL,
				parent_id bigint(20) unsigned NOT NULL,
				created_at datetime NOT NULL default '0000-00-00 00:00:00',
				updated_at datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY (id),
				KEY parent_id (parent_id)
			) {$charset_collate};";

			return $sql;
		}

		/**
		 * Create custom items table.
		 *
		 * @param string $table_name_with_prefix The prefixed table name.
		 * @param string $charset_collate        The charset.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public static function create_brm_items_table( $table_name_with_prefix, $charset_collate ) {

			global $wpdb;
			$groups_table = $wpdb->prefix . 'brm_groups';

			$sql = "CREATE TABLE {$table_name_with_prefix} (
				id bigint(20) unsigned NOT NULL auto_increment,
				name text NOT NULL,
				description longtext NOT NULL,
				image_id bigint(20) unsigned NOT NULL default '0',
				price decimal(6,2) NULL default NULL,
				sort int(11) NOT NULL,
				group_id bigint(20) unsigned NOT NULL,
				created_at datetime NOT NULL default '0000-00-00 00:00:00',
				updated_at datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY (id),
				FOREIGN KEY (group_id) REFERENCES {$groups_table}(id) ON DELETE CASCADE

			) {$charset_collate};";

			return $sql;
		}

		/**
		 * Creates menu page for frontend.
		 *
		 * @since 1.0.0
		 * @version 1.4.2
		 *
		 * @return void
		 */
		public static function create_frontend_menu_page() {
			global $wpdb;
			$options_table = $wpdb->prefix . 'brm_options';

			// Securely fetch settings from the database.
			$sql = $wpdb->prepare( "SELECT option_value FROM {$options_table} WHERE option_name = %s", 'brm_menu_settings' ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Safe interpolation of table and column names.

			$settings_values = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for custom table operations, following best practices for security.

			$settings = $settings_values ? unserialize( $settings_values ) : false;

			// Initialize default settings if not found.
			if ( ! $settings ) {
				$settings    = array(
					'business_name'     => '',
					'business_address'  => '',
					'business_city'     => '',
					'business_state'    => '',
					'business_zip_code' => '',
					'business_country'  => 'US',
					'business_phone'    => '',
					'business_currency' => 'USD',
					'theme_template'    => 'minimalist',
				);
				$create_page = true;
			} else {
				$create_page = ! isset( $settings['menu_page_id'] ) || empty( $settings['menu_page_id'] );
			}

			// Check if the menu page exists and is not trashed.
			if ( ! $create_page ) {
				$page_id     = $settings['menu_page_id'];
				$menu_page   = get_post( $page_id );
				$create_page = ! $menu_page || 'page' !== $menu_page->post_type || 'trash' === $menu_page->post_status;
			}

			if ( $create_page ) {
				// Attempt to create the menu page.
				$menu_page_id = wp_insert_post(
					array(
						'post_title'   => 'Menu',
						'post_content' => '',
						'post_status'  => 'draft',
						'post_type'    => 'page',
						'post_parent'  => 0,
					)
				);

				if ( ! $menu_page_id || is_wp_error( $menu_page_id ) ) {
					// Handle error in page creation.
					error_log( 'Failed to create menu page: ' . $menu_page_id->get_error_message() );
					return;
				}

				// Update settings with the new page ID and save.
				$settings['menu_page_id'] = $menu_page_id;
				update_post_meta( $menu_page_id, '_wp_page_template', 'best-restaurant-menu.php' );
				$serialized_settings = serialize( $settings );
				$wpdb->replace(  // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for custom table operations, following best practices for security.
					$options_table,
					array(
						'option_name'  => 'brm_menu_settings',
						'option_value' => $serialized_settings,
					),
					array( '%s', '%s' )
				);
			}
		}

		/**
		 * Fires after new site added in multisite mode.
		 *
		 * It fires after new site added in multisite and adds custom tables to it.
		 * It requires WordPress version 5.1 or higher.
		 *
		 * @param WP_Site $new_site The new site object.
		 *
		 * @since 1.3.0
		 *
		 * @return void
		 */
		public function new_site_added( WP_Site $new_site ): void {
			// Check if plugin is active for network.
			if ( is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
				switch_to_blog( $new_site->blog_id );

				// Create database structure.
				self::create_structure();

				// Create frontend menu page.
				self::create_frontend_menu_page();

				restore_current_blog();
			}
		}

		/**
		 * Fires after site deleted from multisite mode.
		 *
		 * It fires after site deleted in multisite and delete custom tables from db.
		 * It requires WordPress version 5.1 or higher.
		 *
		 * @param WP_Site $old_site The old deleted site.
		 *
		 * @since 1.3.0
		 * @version 1.4.2
		 *
		 * @return void
		 */
		public function site_deleted( WP_Site $old_site ): void {
			switch_to_blog( $old_site->blog_id );

			global $wpdb;

			// Database prefix.
			$prefix = $wpdb->prefix;

			/*
			 * Remove plugin custom databse tables.
			 */
			foreach ( self::$tables as $table ) {
				$prefixed_table = $prefix . $table;
				$sql            = "DROP TABLE $prefixed_table";
				$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for custom table operations, following best practices for security.
			}

			restore_current_blog();
		}

		/**
		 * On plugin activation.
		 *
		 * @param bool $network_wide The network wide.
		 *
		 * @since   1.0.0
		 * @version 1.3.0
		 *
		 * @return void
		 */
		public static function activate( $network_wide ) {
			global $wpdb;

			/*
			 * Validates multisite network enabled.
			 */
			if ( is_multisite() && $network_wide ) {
				// Get ids of all sites.
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for custom table operations, following best practices for security.

				foreach ( $blogids as $blogid ) {
					switch_to_blog( $blogid );

					// Create database structure.
					self::create_structure();

					// Create frontend menu page.
					self::create_frontend_menu_page();

					restore_current_blog();
				}
			} else {
				// Create database structure.
				self::create_structure();

				// Create frontend menu page.
				self::create_frontend_menu_page();
			}
		}

		/**
		 * On plugin deactivation.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function deactivate() {
			// Nothing to Do for Now.
		}

		/**
		 * On plugin uninstall.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function uninstall() {
			include_once BEST_RESTAURANT_MENU_ROOT_PATH . 'uninstall.php';
		}
	}

endif;

/**
 * The main instance of Best_Restaurant_Menu.
 *
 * Returns the main instance of Best_Restaurant_Menu.
 *
 * @since 1.0.0
 *
 * @return Best_Restaurant_Menu
 */
function best_restaurant_menu() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Universal.Files.SeparateFunctionsFromOO.Mixed -- Function name is valid.
	return Best_Restaurant_Menu::get_instance();
}

// Global for backwards compatibility.
$GLOBALS['best_restaurant_menu'] = best_restaurant_menu();

register_activation_hook( __FILE__, array( 'Best_Restaurant_Menu', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Best_Restaurant_Menu', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'Best_Restaurant_Menu', 'uninstall' ) );
