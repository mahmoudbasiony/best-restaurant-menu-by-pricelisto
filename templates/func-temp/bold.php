<?php
/**
 * Bold Template Functions.
 *
 * @package Best_Restaurant_Menu/Templates/temp-func/bold
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
function brm_bold_render_group_heading( $group, $is_subgroup = false ) {
	ob_start();
	?>
		<div class="brm-heading<?php echo $is_subgroup ? " subgroup" : "" ?>" style="display: <?php echo ( empty( $group->name ) && empty( $group->description ) ) ? 'none;' : 'block;'; ?>">
			<?php if ( ! empty( $group->name ) ) : ?>
				<h2>
					<span><?php echo esc_html( stripslashes( $group->name ) ); ?></span>
				</h2>
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
function brm_bold_render_items( $items, $currency, $is_subgroup = false ) {
	$counter = 0;
	$html    = "";
	$subgroup_class = $is_subgroup ? " subgroup" : "";

	$html .= '<div class="brm-items' . $subgroup_class . '">';
	foreach ( $items as $key => $item ):
		if ( $counter % 3 == 0 ) :
			$html .= '<div class="brm-menu-row">';
		endif;

		$html .= '<div class="brm-item">';
		if ( ! empty( $item->image_id ) ) :
			$html .= '<div class="brm-item-image">';
			$html .= '<img src="' . esc_url( wp_get_attachment_image_src($item->image_id, 'thumbnail')[0] ) .'" alt="' . esc_attr( $item->name ) . '">';
			$html .= '</div>';
		endif;

		$html .= '<div class="brm-item-details">';
		$html .= '<div class="brm-item-name">' . esc_html( stripslashes( $item->name ) ) . '</div>';
		$html .= '<div class="brm-item-price">' . esc_html( stripslashes( $currency . $item->price ) ) . '</div>';
		$html .= '<div class="brm-item-description">' . wp_kses_post( wptexturize( esc_html( stripslashes( $item->description) ) ) ) . '</div>';
		$html .= '</div>';
		$html .= '</div>';

		if ($counter % 3 == 2 || count($items) - 1 == $key):
			$html .= '</div>';
		endif;

		$counter++;
	endforeach;

	$html .= '</div>';
	return $html;
}

/**
 * Renders frontend menu html -- (Recursion)
 *
 * @param array  $menu     The menu array
 * @param string $currency The menu currency
 * @param bool   $is_child Whether is a child group section or not - Default: false
 *
 * @since   1.1.0
 * @version 1.1.0
 *
 * @return mixed|HTML
 */
function brm_renders_bold_frontend_menu( $menu, $currency, $is_child = false ) {
	$html = '';
	$child_class = $is_child ? " nested-child" : "";
	foreach ( $menu as $group ):
		$html .= '<div class="brm-menu-section ' . $child_class . '">';
		$html .= brm_bold_render_group_heading( $group, $is_child );
		if ( isset( $group->items ) && ! empty( $group->items ) ) :
			$html .=  brm_bold_render_items( $group->items, $currency, $is_child );
		endif;

		if ( isset( $group->childs ) && ! empty( $group->childs ) ) :
		$html .= brm_renders_bold_frontend_menu( $group->childs, $currency, true );
		endif;
		$html .= '</div>';
	endforeach;

	return $html;
}