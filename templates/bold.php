<?php
/**
 * Bold template.
 *
 * This template can be overridden by copying it to yourtheme/best-restaurant-menu/bold.php.
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
require_once BEST_RESTAURANT_MENU_TEMPLATE_PATH . 'func-temp/bold.php';

?>

<div class="brm-menu bold">

<?php
$key = key( $menu );
if ( ! is_null( $key ) && isset( $menu[ $key ] ) ) :
	/*
	 * Render the menu HTML.
	 */
	echo brm_renders_bold_frontend_menu( $menu[ $key ], $currency );
else :
	?>
	<p>You have not added any groups or items to the menu.</p>
<?php endif; ?>

</div>
