<?php
/**
 * Menu Template.
 *
 * @package Best_Restaurant_Menu/Templates/
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wpdb;

// Database tables.
$groups_table = $wpdb->prefix . 'brm_groups';
$items_table  = $wpdb->prefix . 'brm_items';

// SQL groups query.
$groups_sql = "SELECT * FROM $groups_table ORDER BY sort ASC";
$groups     = $wpdb->get_results( $groups_sql );

// SQL items query.
$items_sql = "SELECT $items_table.group_id, $items_table.id, $items_table.name, $items_table.description, $items_table.image_id, $items_table.price, $items_table.sort FROM $items_table LEFT JOIN $groups_table ON $groups_table.id = $items_table.group_id ORDER BY $items_table.sort ASC";
$items     = $wpdb->get_results( $items_sql );

// Initialize the menu array.
$menu_array = array(
	'categories'  => array(),
	'parent_cats' => array(),
	'items'       => array(),
);

?>

<div class="brm-menu">
	<h1 class="menu-header"><?php esc_html_e( 'Menu', 'best-restaurant-menu' ); ?></h1>

	<div class="sp sp-volume"></div>
		<?php
		if ( ! empty( $groups ) ) {

			foreach ( $groups as $group ) {
				// Push categories to menu array.
				$menu_array['categories'][ $group->id ]           = $group;
				$menu_array['parent_cats'][ $group->parent_id ][] = $group->id;
			}

			if ( ! empty( $items ) ) {
				foreach ( $items as $item ) {
					// Push items to menu array.
					$menu_array['items'][ $item->group_id ][] = $item;
				}
			}

			/*
			 * Renders the complete menu.
			 *
			 * Render the menu in the backend (groups, sub groups and items)
			 */
			echo BRM_Utilities::render_menu_backend( 0, $menu_array );
		}

		?>

</div>

<div class="add-group-form">

</div>
<div class="add-item-form">

</div>
<table style="" cellpadding="3">
	<tr>
		<td>
			<button class="button button-primary add-new-group-btn" style="culor:">
				<?php esc_html_e( 'Add new group', 'best-restaurant-menu' ); ?>
			</button>
		</td>
	</tr>
</table>

