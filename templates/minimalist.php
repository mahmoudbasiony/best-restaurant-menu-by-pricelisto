<?php
/**
 * Minimalist template.
 *
 *
 * This template can be overridden by copying it to yourtheme/best-restaurant-menu/minimalist.php.
 *
 * @var array  $args     The shortcode parameters
 * @var array  $menu     The menu tree array
 * @var string $currency The currency symbol
 *
 * @see     
 * @package Best_Restaurant_Menu
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="brm-menu minimalist">

<?php
if (isset($menu[0])):

foreach ($menu[0] as $group): ?>

	<div class="brm-menu-section">
		<div class="brm-heading">
			<h2><?php echo esc_html( $group->name ); ?></h2>
			<?php if (!empty($group->description)): ?>
			<div class="brm-heading-description"><?php echo esc_html( $group->description ); ?></div>
			<?php endif; ?>
		</div>
		<?php foreach ($group->items as $item): ?>
		<div class="brm-item">
			<?php if (!empty($item->image_id)): ?>
			<div class="brm-item-image">
				<img src="<?php echo esc_url( wp_get_attachment_image_src($item->image_id, 'thumbnail')[0] ); ?>" alt="<?php echo esc_attr( $item->name ); ?>">
			</div>
			<?php endif; ?>
			<div class="brm-item-details">
				<div class="brm-item-name"><?php echo esc_html( $item->name ); ?></div>
				<div class="brm-item-price"><?php echo esc_html( $currency . $item->price ); ?></div>
				<div class="brm-item-description"><?php echo esc_html( $item->description ); ?></div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
<?php else:?>

<p>You have not added any groups or items to the menu.</p>

<?php endif; ?>

</div>