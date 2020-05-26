<?php
/**
 * General function.
 *
 * @package Best_Restaurant_Menu/Templates/Func
 * @author  PriceListo
 */

/**
 * Renders the group heading (group name and group desc).
 *
 * @param object $group The group object
 * @param bool   $is_subgroup Whether is a sugroup or not - Default: false
 *
 * @since   1.1.0
 * @version 1.1.0
 *
 * @return mixed|HTML
 */
function brm_render_group_heading_html( $group, $is_subgroup = false ) {
	ob_start();
	?>
		<div class="brm-heading<?php echo $is_subgroup ? " subgroup" : "" ?>" style="display: <?php echo ( empty( $group->name ) && empty( $group->description ) ) ? 'none;' : 'block;'; ?>">
			<?php if ( ! empty( $group->name ) ) : ?>
				<h2><?php echo esc_html( stripslashes( $group->name ) ); ?></h2>
			<?php endif; ?>
			<?php if (!empty($group->description)): ?>
				<div class="brm-heading-description"><?php echo wp_kses_post( wptexturize( esc_html( stripslashes( $group->description) ) ) ); ?></div>
			<?php endif; ?>
		</div>
	<?php
	return ob_get_clean();
}

/**
 * Renders the items html
 *
 * @param object $items    The items object
 * @param string $currency The menu currency
 * @param bool   $is_subgroup Whether are subgroup items or not
 *
 * @since   1.1.0
 * @version 1.1.0
 *
 * @return mixed|HTML
 */
function brm_render_items_html( $items, $currency, $is_subgroup = false ) {
	ob_start();
	?>
	<div class="brm-items<?php echo $is_subgroup ? " subgroup" : "" ?>">
	<?php
	foreach ($items as $item):
	?>
		<div class="brm-item">
			<?php if (!empty($item->image_id)): ?>
			<div class="brm-item-image">
				<img src="<?php echo esc_url( wp_get_attachment_image_src($item->image_id, 'thumbnail')[0] ); ?>" alt="<?php echo esc_attr( $item->name ); ?>">
			</div>
			<?php endif; ?>
			<div class="brm-item-details">
				<div class="brm-item-name"><?php echo esc_html( stripslashes( $item->name ) ); ?></div>
				<div class="brm-item-price"><?php echo esc_html( stripslashes ( $currency . $item->price ) ); ?></div>
				<div class="brm-item-description"><?php echo wp_kses_post( wptexturize( esc_html( stripslashes( $item->description) ) ) ); ?></div>
			</div>
		</div>
	<?php
	endforeach;
	?>
	</div>
	<?php

	return ob_get_clean();
}

/**
 * Renders frontend menu html -- (Recursion)
 *
 * @param array  $menu     The menu array
 * @param string $currency The menu currency
 * @param bool   $is_child    Whether is child group section or not - Default: false
 *
 * @since   1.1.0
 * @version 1.1.0
 *
 * @return mixed|HTML
 */
function renders_frontend_menu_html( $menu, $currency, $is_child = false ) {
	$html = '';
	foreach ( $menu as $group ):
		$html .= '<div class="brm-menu-section">';
		$html .= brm_render_group_heading_html( $group, $is_child );
		if ( isset( $group->items ) && ! empty( $group->items ) ) :
			$html .=  brm_render_items_html( $group->items, $currency, $is_child );
		endif;

		if ( isset( $group->childs ) && ! empty( $group->childs ) ) :
		$html .= renders_frontend_menu_html( $group->childs, $currency, true );
		endif;
		$html .= '</div>';
	endforeach;

	return $html;
}