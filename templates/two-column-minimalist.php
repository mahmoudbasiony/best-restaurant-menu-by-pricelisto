<?php
/**
 * 2 Column Minimalist template.
 *
 * This template can be overridden by copying it to yourtheme/best-restaurant-menu/two-column-minimalist.php.
 *
 * @var array  $args     The shortcode parameters
 * @var array  $menu     The menu tree array
 * @var string $currency The currency symbol
 *
 * @version 1.1.0
 *
 * @see
 * @package Best_Restaurant_Menu
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * Include template functions.
 */
require_once BEST_RESTAURANT_MENU_TEMPLATE_PATH . 'func-temp/two-column-minimalist.php';

?>

<div class="brm-menu minimalist-2-column">

<?php
$key = key( $menu );

if ( ! is_null( $key ) && isset( $menu[ $key ] ) ) :

	$menus = array_chunk( $menu[ $key ], ceil( count( $menu[ $key ] ) / 2 ) );

	?>

	<?php foreach ( $menus as $groups ) : ?>

	<div class="brm-menu-column">

		<?php echo brm_column_minimalist_renders_frontend_menu( $groups, $currency ); ?>

	</div>

<?php endforeach; ?>
<?php else : ?>

<p>You have not added any groups or items to the menu.</p>

<?php endif; ?>

</div>
