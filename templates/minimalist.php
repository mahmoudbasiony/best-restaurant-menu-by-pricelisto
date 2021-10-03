<?php
/**
 * Minimalist template.
 *
 * This template can be overridden by copying it to yourtheme/best-restaurant-menu/minimalist.php.
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
require_once BEST_RESTAURANT_MENU_TEMPLATE_PATH . 'func-temp/minimalist.php';

?>

<div class="brm-menu minimalist">

<?php
$key = key( $menu );
if ( ! is_null( $key ) && isset( $menu[ $key ] ) ) :
	/*
	 * Render the menu HTML.
	 */
	echo brm_minimalist_renders_frontend_menu( $menu[ $key ], $currency );
else :
	?>
	<p>You have not added any groups or items to the menu.</p>
<?php endif; ?>

</div>
