<?php
/**
 * The BRM_Menu_Template class.
 *
 * @package Best_Restaurant_Menu/Assets
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BRM_Menu_Template' ) ) :

	/**
	 * Menu Template.
	 *
	 * Create custom menu page template.
	 *
	 * @since 1.0.0
	 */
	class BRM_Menu_Template {

		/**
		 * The array of templates.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		protected static $templates;

		/**
		 * The constructor.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			self::$templates = array();

			if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
				add_filter( 'page_attributes_dropdown_pages_args', array( $this, 'register' ) );
			} else {
				add_filter( 'theme_page_templates', array( $this, 'add' ) );
			}

			add_filter( 'wp_insert_post_data', array( $this, 'register' ) );
			add_filter( 'template_include', array( $this, 'display' ) );

			// Templates array.
			self::$templates = array(
				'best-restaurant-menu.php' => esc_html__( 'Best Restaurant Menu', 'best-restaurant-menu' ),
			);
		}

		/**
		 * Adds the template to the page dropdown for v4.7+.
		 *
		 * @param array $posts_templates The posts templates.
		 *
		 * @since 1.0.0
		 *
		 * @return array $posts_templates
		 */
		public function add( $posts_templates ) {
			$posts_templates = array_merge( $posts_templates, self::$templates );
			return $posts_templates;
		}

		/**
		 * Add the template to the pages cache in order to trick WordPress
		 * into thinking the template file exists where it doesn't really exists
		 *
		 * @param array $atts The attributes.
		 *
		 * @since 1.0.0
		 *
		 * @return array
		 */
		public function register( $atts ) {
			// Create the key used for theme cache.
			$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

			// Retrieve the cache list
			// If it doesn't exist, or it's empty prepare an array
			$templates = wp_get_theme()->get_page_templates();

			if ( empty( $templates ) ) {
				$templates = array();
			}

			// New cache, therefore remove the old one.
			wp_cache_delete( $cache_key, 'themes' );

			// Now add our templates to the list of templates.
			$templates = array_merge( $templates, self::$templates );

			// Add the modified cache to allow WordPress to pick it up for listing available templates.
			wp_cache_add( $cache_key, $templates, 'themes', 1800 );

			return $atts;
		}

		/**
		 * Check if template assign to page, then display it.
		 *
		 * @param array $template The templates.
		 *
		 * @since 1.0.0
		 *
		 * @return mixed
		 */
		public function display( $template ) {
			global $post;

			// Return the search template if we're searching (instead of the template for the first result).
			if ( is_search() ) {
				return $template;
			}

			// Return template if post is empty.
			if ( ! $post ) {
				return $template;
			}

			// Return default template if we don't have a custom one defined.
			if ( ! isset( self::$templates[ get_post_meta( $post->ID, '_wp_page_template', true ) ] ) ) {
				return $template;
			}

			// Template arguments.
			$template_name = get_post_meta( $post->ID, '_wp_page_template', true );
			$template_path = 'best-restaurant-menu/menu/';
			$default_path  = BEST_RESTAURANT_MENU_TEMPLATE_PATH . 'menu/';

			// Locate template.
			$file = BRM_Utilities::locate_template( $template_name, $template_path, $default_path );

			// Just to be safe, we check if the file exist first.
			if ( file_exists( $file ) ) {
				return $file;
			}

			// Return template.
			return $template;
		}
	}

	return new BRM_Menu_Template();

endif;
