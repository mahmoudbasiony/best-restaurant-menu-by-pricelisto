<?php
/**
 * The BRM_Shortcodes class.
 *
 * @package Best_Restaurant_Menu/Assets
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BRM_Shortcodes' ) ) :

	/**
	 * Assets.
	 *
	 * Handles shortcodes.
	 *
	 * @since 1.0.0
	 */
	class BRM_Shortcodes {
		/**
		 * The constructor.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			add_shortcode( 'brm_restaurant_menu', array( $this, 'render_shortcode' ) );
			add_action( 'brm_after_template_part', array( $this, 'render_template_backlink' ), 10, 4 );
		}

		/**
		 * Render shortcode.
		 *
		 * @param array $atts The shortcode parameters.
		 *
		 * @since 1.0.0
		 *
		 * @return mixed
		 */
		public function render_shortcode( $atts ) {
			// Global $wpdb.
			global $wpdb;

			// Get general settings.
			$options_table = $wpdb->prefix . 'brm_options';
			$sql           = "SELECT option_value FROM $options_table WHERE option_name = 'brm_menu_settings'";
			$settings      = unserialize( $wpdb->get_var( $sql ) );

			/*
			 * Set default theme template.
			 */
			if ( isset( $settings ) && isset( $settings['theme_template'] ) && ! empty( $settings['theme_template'] ) ) {
				$view = $settings['theme_template'];
			} else {
				$view = 'minimalist';
			}

			// Set defaults attributes value.
			$display = shortcode_atts(
				array(
					'groups'           => '',
					'show_group_title' => 'yes',
					'show_group_desc'  => 'yes',
					'show_items'       => 1,
					'view'             => $view,
				),
				$atts
			);

			// Frontend templates.
			$frontend_templates = apply_filters(
				'brm_frontend_templates',
				array(
					'minimalist',
					'two-column-minimalist',
					'fancy',
					'colorful',
					'bold',
				)
			);

			if ( isset( $display['view'] ) && ! empty( $display['view'] ) && in_array( $display['view'], $frontend_templates ) ) {
				$view = $display['view'];

				/*
				 * Enqueue styles and scripts.
				 */
				wp_enqueue_script( "brm_{$view}" );
				wp_enqueue_style( "brm_{$view}" );

				$menu     = $this->get_menu_array( $display );
				$currency = BRM_Utilities::get_currency_symbol();

				$display['menu']     = $menu;
				$display['currency'] = $currency;
				return BRM_Utilities::get_template_html( $view, $display );
			}
		}

		/**
		 * Render after template backlink
		 *
		 * @param string $template_name The template name.
		 * @param string $template_path The template path.
		 * @param string $located       The template full location path.
		 * @param array  $args          The template args.
		 *
		 * @since 1.0.0
		 *
		 * @return mixed
		 */
		public function render_template_backlink( $template_name, $template_path, $located, $args ) {
			echo sprintf( '<div class="pl-developer"><span>%1$s </span><a href="%2$s">%3$s</a></div>', esc_html__( 'Best Restaurant Menu Plugin by', 'best-restaurant-menu' ), 'https://www.pricelisto.com/plugins', 'PriceListo' );
		}

		/**
		 * Get menu array frontend.
		 *
		 * @param array $args The shortcode arguments
		 *
		 * @since 1.0.0
		 *
		 * @return array $menu_array The menu array
		 */
		public function get_menu_array( $args ) {
			global $wpdb;

			// Database tables.
			$groups_table = $wpdb->prefix . 'brm_groups';
			$items_table  = $wpdb->prefix . 'brm_items';

			/*
			 * Groups.
			 */
			$groups_sql = "SELECT * FROM $groups_table ORDER BY sort ASC";

			if ( isset( $args['groups'] ) && ! empty( $args['groups'] ) ) {
				$groups_sql = "SELECT * FROM $groups_table WHERE id in ({$args['groups']}) ORDER BY sort ASC";
			}

			$groups_sql = apply_filters( 'brm_groups_sql_query', $groups_sql, $args );
			$groups     = $wpdb->get_results( $groups_sql );

			/*
			 * Items
			 */
			$items = array();

			if ( isset( $args['show_items'] ) && ! empty( $args['show_items'] ) && 1 == $args['show_items'] ) {
				$items_sql = "SELECT $items_table.group_id, $items_table.id, $items_table.name, $items_table.description, $items_table.image_id, $items_table.price, $items_table.sort FROM $items_table LEFT JOIN $groups_table ON $groups_table.id = $items_table.group_id ORDER BY $items_table.sort ASC";

				if ( isset( $args['groups'] ) && ! empty( $args['groups'] ) ) {
					$items_sql = "SELECT $items_table.group_id, $items_table.id, $items_table.name, $items_table.description, $items_table.image_id, $items_table.price, $items_table.sort FROM $items_table LEFT JOIN $groups_table ON $groups_table.id = $items_table.group_id WHERE $groups_table.id in ({$args['groups']}) ORDER BY $items_table.sort ASC";
				}

				$items_sql = apply_filters( 'brm_items_sql_query', $items_sql, $args );

				$items = $wpdb->get_results( $items_sql );
			}

			// Initialize the menu array.
			$menu_array = array();

			// Validate groups.
			if ( ! empty( $groups ) ) {
				foreach ( $groups as $group ) {
					// Push categories to menu array.
					$menu_array[ $group->parent_id ][ $group->id ] = $group;

					// Remove group title if show group title attribute is set to false.
					if ( isset( $args['show_group_title'] ) && ! empty( $args['show_group_title'] ) && 'no' === strtolower( $args['show_group_title'] ) ) {
						$group->name = '';
					}

					// Remove group description if show group description attribute is set to false.
					if ( isset( $args['show_group_desc'] ) && ! empty( $args['show_group_desc'] ) && 'no' === strtolower( $args['show_group_desc'] ) ) {
						$group->description = '';
					}

					// Initialize items array.
					$items_array = array();

					if ( ! empty( $items ) ) {
						foreach ( $items as $item ) {
							if ( $item->group_id == $group->id ) {
								$items_array[] = $item;
							}
						}
					}

					if ( ! empty( $items_array ) ) {
						// Push items to menu array.
						$menu_array[ $group->parent_id ][ $group->id ]->items = $items_array;
					}
				}

				// Resorting nesting groups.
				foreach ( $groups as $group ) {
					// Validate child group.
					if ( isset( $menu_array[ $group->id ] ) ) {
						$group->childs = $menu_array[ $group->id ];

						// Remove child group from the parent index.
						unset( $menu_array[ $group->id ] );
					}
				}
			}

			return apply_filters( 'brm_menu_array', $menu_array, $args );
		}

	}

	return new BRM_Shortcodes();

endif;
