<?php
/**
 * The BRM_Utilities class.
 *
 * @package Best_Restaurant_Menu
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BRM_Utilities' ) ) :

	/**
	 * Introduces helper functions.
	 *
	 * @since 1.0.0
	 */
	class BRM_Utilities {

		/**
		 * Renders settings notices.
		 *
		 * @since 1.0.0
		 *
		 * @param array $notices List of notices.
		 *
		 * @return void
		 */
		public static function add_settings_notices( $notices ) {
			foreach ( $notices as $notice ) {
				$class   = $notice['class'];
				$message = $notice['message'];

				echo "<div id=\"message\" class=\"{$class} inline\"><p><strong>{$message}</strong></p></div>";
			}
		}

		/**
		 * Render group raw html.
		 *
		 * @param int   $group_index The index.
		 * @param array $group       The group raw.
		 * @param int   $parent_id   The parent ID.
		 *
		 * @since 1.0.0
		 *
		 * @return mixed The raw html.
		 */
		public static function render_group_form( $group_index = 0, $group = null, $parent_id = 0 ) {
			ob_start();

			?>
			<div class="group-header">
				<h2><?php esc_html_e( 'Group', 'best-restaurant-menu' ); ?></h2>
			</div>
			<form method="post" id="brm-group-form" class="brm-group-form" data-group-id="<?php echo $group ? esc_attr( stripslashes( $group->id ) ) : ''; ?>">
				<table class="form-table brm-group-raw-table" id="brm-group-raw-table">
					<tbody class="brm-group-raw" data-parent-id="<?php echo esc_attr( stripslashes( $parent_id ) ); ?>" data-order="<?php echo $group ? esc_attr( stripslashes( $group->sort ) ) : ''; ?>">

						<tr valign="top">
							<th scope="row">
								<label for="groups-name"><?php esc_html_e( 'Name', 'best-restaurant-menu' ); ?></label>
							</th>
							<td>
								<input type="text" name="name" required class="groups-name" id="groups-name" value="<?php echo ! empty( $group ) && ! empty( $group->name ) ? esc_attr( stripslashes( $group->name ) ) : ''; ?>" style="width: 80%" class="regular-text" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="groups-description"><?php esc_html_e( 'Description', 'best-restaurant-menu' ); ?></label>
							</th>
							<td>
								<textarea id="groups-description" class='groups-description' rows="4" style="width: 80%"><?php echo ! empty( $group ) && ! empty( $group->description ) ? esc_textarea( stripslashes( $group->description ) ) : ''; ?></textarea>
							</td>
						</tr>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit" name="cancel-group" value="<?php esc_html_e( 'Cancel', 'best-restaurant-menu' ); ?>" class="button-primary cancel-group" />
					<input type="submit" name="save-group" value="<?php esc_html_e( 'Save', 'best-restaurant-menu' ); ?>" class="button-primary save-group" />
				</p>

			</form>
			<?php
			return ob_get_clean();
		}

		/**
		 * Renders group raw.
		 *
		 * @param int    $id The group id.
		 * @param string $name The group name.
		 * @param string $desc The group description.
		 * @param int    $order The group order.
		 * @param int    $parent_id The group parent id.
		 *
		 * @since 1.0.0
		 *
		 * @return mixed
		 */
		public static function render_group_raw( $id = 0, $name = null, $desc = null, $order = 0, $parent_id = 0 ) {
			ob_start();
			?>
			<div class="group-raw" data-group-id="<?php echo esc_attr( stripslashes( $id ) ); ?>" data-order="<?php echo esc_attr( stripslashes( $order ) ); ?>" data-parent-id="<?php echo esc_attr( stripslashes( $parent_id ) ); ?>">
				<table>
					<tbody>
						<tr>
							<td style="width:40px;"><i class="fa fa-bars icon-move ui-sortable-handle"></i></td>
							<td style="position: relative;">
								<div class="group-name">
									<?php echo esc_html( stripslashes( $name ) ); ?>
									<span class="group-id">Group ID: <?php echo esc_attr( stripslashes( $id ) ); ?></span>
								</div>
								<div class="group-desc" id="group-<?php echo $id; ?>-desc"><?php echo nl2br( wp_kses_post( wptexturize( esc_textarea( stripslashes( $desc ) ) ) ) ); ?></div>
							</td>
							<td style="width:82px; position:absolute; top:10px; right:60px;">
								<div class="edit-icons">
									<i class="fa fa-pencil edit-group"></i>
									<i class="fa fa-remove delete-group"></i>
								</div>
							</td>

							<td style="width:20px; position:absolute; top:21px; right:10px;">
								<span title="Click to show/hide children" class="disclose ui-icon ui-icon-minusthick">
									<span></span>
								</span>
							</td>

							<td style="width:20px; position:absolute; top:21px; right:33px;">
								<span title="Click to show/hide group description" data-id="<?php echo esc_attr( $id ); ?>" class="expandEditor ui-icon ui-icon-triangle-1-n">
									<span></span>
								</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Renders group raw action buttons.
		 *
		 * @param int $group_id  The linked group ID.
		 * @param int $parent_id The parent group ID.
		 *
		 * @since   1.0.0
		 * @version 1.1.0
		 *
		 * @return mixed HTML
		 */
		public static function group_raw_actions( $group_id = 0, $parent_id = 0 ) {
			ob_start();
			?>
				<div class="group-raw-actions">
					<?php if ( 0 == $parent_id ) : ?>
						<span id="add-new-subgroup" data-group-id="<?php echo esc_attr( $group_id ); ?>">
							<input type="submit" name="add-new-subgroup" value="<?php esc_html_e( 'Add subgroup', 'best-restaurant-menu' ); ?>" class="button-primary add-new-subgroup" />
						</span>
					<?php endif; ?>
					<span id="add-new-item" data-group-id="<?php echo esc_attr( $group_id ); ?>">
						<input type="submit" name="add-new-item" value="<?php esc_html_e( 'Add item', 'best-restaurant-menu' ); ?>" class="button-primary add-new-item" />
					</span>
				</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Render item form.
		 *
		 * @param int    $item_index The item index.
		 * @param object $item       The item object.
		 * @param int    $group_id   The group id.
		 *
		 * @since 1.0.0
		 *
		 * @return mixed The item form HTML.
		 */
		public static function render_item_form( $item_index = 0, $item = null, $group_id = 0 ) {
			ob_start();
			?>
				<div class="item-header">
					<h2><?php esc_html_e( 'Item', 'best-restaurant-menu' ); ?></h2>
				</div>
				<form method="post" enctype="multipart/form-data" id="brm-item-form" class="brm-item-form" data-item-id="<?php echo $item ? esc_attr( $item->id ) : ''; ?>" data-group-id="<?php echo $item ? esc_attr( $item->group_id ) : 0; ?>" data-image-id="<?php echo $item ? esc_attr( $item->image_id ) : 0; ?>">
				<table class="form-table brm-item-raw-table" id="brm-item-raw-table">
					<tbody class="brm-item-raw" data-group-id="<?php echo esc_attr( $group_id ); ?>" data-order="<?php echo $item ? esc_attr( $item->sort ) : ''; ?>">
						<tr valign="top">
							<th scope="row">
								<label for="item-name"><?php esc_html_e( 'Name', 'best-restaurant-menu' ); ?></label>
							</th>
							<td>
								<input type="text" name="name" required class="item-name" id="item-name" value="<?php echo ! empty( $item ) && ! empty( $item->name ) ? esc_attr( stripslashes( $item->name ) ) : ''; ?>" style="width: 80%" class="regular-text" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="item-description"><?php esc_html_e( 'Description', 'best-restaurant-menu' ); ?></label>
							</th>
							<td>
								<textarea id="item-description" class='item-description' rows="4" style="width: 80%"><?php echo ! empty( $item ) && ! empty( $item->description ) ? esc_textarea( stripslashes( $item->description ) ) : ''; ?></textarea>
							</td>
						</tr>

						<?php self::image_uploader( 'item-image', ! empty( $item ) && ! empty( $item->image_id ) ? esc_attr( $item->image_id ) : 0, 150, 150 ); ?>

						<tr valign="top">
							<th scope="row">
								<label for="item-price"><?php esc_html_e( 'Price', 'best-restaurant-menu' ); ?></label>
							</th>
							<td>
								<input type="text" name="price" class="item-price" id="item-price" value="<?php echo ! empty( $item ) && ! empty( $item->price ) ? esc_attr( stripslashes( $item->price ) ) : ''; ?>" style="width: 80%" class="regular-text" />
							</td>
						</tr>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit" name="cancel-item" value="<?php esc_html_e( 'Cancel', 'best-restaurant-menu' ); ?>" class="button-primary cancel-item" />
					<input type="submit" name="save-item" value="<?php esc_html_e( 'Save', 'best-restaurant-menu' ); ?>" class="button-primary save-item" />
				</p>

			</form>
			<?php
			return ob_get_clean();
		}

		/**
		 * Render item raw html.
		 *
		 * @param int    $id              The item ID.
		 * @param string $name            The item name.
		 * @param string $desc            The item description.
		 * @param int    $image_id        The item image ID.
		 * @param float  $price           The item price.
		 * @param int    $order           The item order.
		 * @param int    $group_id        The item linked group.
		 * @param string $currency_symbol The site currency symbol.
		 *
		 * @since 1.0.0
		 *
		 * @return mixed The item raw HTML.
		 */
		public static function render_item_raw( $id = 0, $name = null, $desc = null, $image_id = 0, $price = null, $order = 0, $group_id = 0, $currency_symbol = '$' ) {
			ob_start();
			?>
			<div class="item-raw" data-item-id="<?php echo esc_attr( $id ); ?>" data-order="<?php echo esc_attr( $order ); ?>" data-group-id="<?php echo esc_attr( $group_id ); ?>">
				<table>
					<tbody>
						<tr>
							<td style="width:40px;"><i class="fa fa-bars icon-move ui-sortable-handle"></i></td>

							<td style="width:80px;">
								<div class="item-image"><?php echo wp_get_attachment_image( $image_id, array( 50, 50 ) ); ?></div>
								
							</td>
							<td style="">
								<span class="item-name"><?php echo esc_html( stripslashes( $name ) ); ?></span>
								<span class="item-desc"><?php echo nl2br( wp_kses_post( wptexturize( esc_textarea( stripslashes( $desc ) ) ) ) ); ?></span>
								<?php if ( $price && is_numeric( $price ) ) : ?>
									<span class="item-price"><?php echo esc_html( stripslashes( $currency_symbol . $price ) ); ?></span>
								<?php endif; ?>
							</td>

							<td style="width:110px; position:absolute; top: 0; right: 0">
								<div class="edit-icons">
									<i class="fa fa-pencil edit-item"></i>
									<i class="fa fa-remove delete-item"></i>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Render media uploader.
		 *
		 * @param string $name The input field name.
		 * @param int    $image_id The image ID.
		 * @param int    $width    The image width.
		 * @param int    $height   The image height.
		 *
		 * @since 1.0.0
		 *
		 * @return mixed The html uploader field.
		 */
		public static function image_uploader( $name, $image_id, $width, $height ) {

			// Set variables
			$default_image = '';

			if ( ! empty( $image_id ) ) {
				$image_attributes = wp_get_attachment_image_src( $image_id, array( $width, $height ) );
				$src              = $image_attributes[0];
				$value            = $image_id;
				$class            = 'has-image';
				$display          = 'inline-block;';
			} else {
				$src     = $default_image;
				$value   = '';
				$class   = 'no-image';
				$display = 'none;';
			}

			$text = esc_html__( 'Select Image', 'best-restaurant-menu' );

			// Print HTML field
			echo '
				<tr class="upload">
					<th>' . __( 'Item Image', 'best-restaurant-menu' ) . '</th>
					<td>
						<img data-src="' . esc_url( $default_image ) . '" src="' . esc_url( $src ) . '" width="' . $width . 'px" height="' . $height . 'px" style="display: ' . $display . '" />
						<div class="' . sanitize_html_class( $class ) . '">
							<input type="hidden" name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" />
							<button type="submit" class="upload_image_button button">' . $text . '</button>
							<button type="submit" class="remove_image_button button" style="display: ' . $display . '">&times;</button>
						</div>
					</td>
				</tr>
			';
		}

		/**
		 * Renders menu in admin dashboard.
		 *
		 * @param int         $parent_id The parent ID.
		 * @param mixed|array $group     The group.
		 *
		 * @since 1.0.0
		 *
		 * @return mixed $html The html output.
		 */
		public static function render_menu_backend( $parent_id, $group ) {
			// Get the settings currency symbol.
			$currency_symbol = self::get_currency_symbol();

			$html = '';

			if ( isset( $group['parent_cats'][ $parent_id ] ) ) {
				$html .= "<ul class='brm-groups-admin'>\n";
				foreach ( $group['parent_cats'][ $parent_id ] as $cat_id ) {
					if ( ! isset( $group['parent_cats'][ $cat_id ] ) ) {

						$html .= "<li class='brm-group-li' data-group=" . esc_attr( $group['categories'][ $cat_id ]->id ) . ">\n";
						$html .= self::render_group_raw( $group['categories'][ $cat_id ]->id, $group['categories'][ $cat_id ]->name, $group['categories'][ $cat_id ]->description, $group['categories'][ $cat_id ]->sort, $group['categories'][ $cat_id ]->parent_id );

						if ( isset( $group['items'][ $cat_id ] ) ) {
							$html .= self::get_items_per_group( $group['items'][ $cat_id ], $currency_symbol );
						}

						$html .= self::group_raw_actions( $group['categories'][ $cat_id ]->id, $parent_id );

						$html .= "</li> \n";
					}

					if ( isset( $group['parent_cats'][ $cat_id ] ) ) {

						$html .= "<li class='brm-group-li' data-group=" . esc_attr( $group['categories'][ $cat_id ]->id ) . ">\n";
						$html .= self::render_group_raw( $group['categories'][ $cat_id ]->id, $group['categories'][ $cat_id ]->name, $group['categories'][ $cat_id ]->description, $group['categories'][ $cat_id ]->sort, $group['categories'][ $cat_id ]->parent_id );

						if ( isset( $group['items'][ $cat_id ] ) ) {
							$html .= self::get_items_per_group( $group['items'][ $cat_id ], $currency_symbol );
						}

						$html .= self::group_raw_actions( $group['categories'][ $cat_id ]->id, $parent_id );

						$html .= self::render_menu_backend( $cat_id, $group );
						$html .= "</li> \n";
					}
				}
				$html .= "</ul> \n";
			}
			return $html;
		}

		/**
		 * Get items for each group.
		 *
		 * @param array  $group_index     The group index for items multidimesional array.
		 * @param string $currency_symbol The site currency symbol.
		 *
		 * @since 1.0.0
		 *
		 * @return string Shtml The html output.
		 */
		public static function get_items_per_group( $group_index, $currency_symbol ) {

			// Initialize html string.
			$html = '';

			if ( ! empty( $group_index ) ) {
				$html = "<ul class='brm-items-admin'>\n";

				foreach ( $group_index as $item ) {
					$html .= "<li class='mjs-nestedSortable-no-nesting'>\n";
					$html .= self::render_item_raw( $item->id, $item->name, $item->description, $item->image_id, $item->price, $item->sort, $item->group_id, $currency_symbol );
					$html .= "</li>\n";
				}

				$html .= "</ul>\n";
			}

			return $html;
		}

		/**
		 * Get the menu array.
		 *
		 * @since 1.0.0
		 *
		 * @return array
		 */
		public static function get_menu_array() {
			global $wpdb;

			// Database tables.
			$groups_table = $wpdb->prefix . 'brm_groups';
			$items_table  = $wpdb->prefix . 'brm_items';

			// SQL groups query.
			$groups_sql = "SELECT * FROM $groups_table ORDER BY sort ASC";
			$groups     = $wpdb->get_results( $groups_sql );

			// SQL items query
			$items_sql = "SELECT $items_table.group_id, $items_table.id, $items_table.name, $items_table.description, $items_table.image_id, $items_table.price, $items_table.sort FROM $items_table LEFT JOIN $groups_table ON $groups_table.id = $items_table.group_id ORDER BY $items_table.sort ASC";
			$items     = $wpdb->get_results( $items_sql );

			// Initialize the menu array.
			$menu_array = array(
				'categories'  => array(),
				'parent_cats' => array(),
				'items'       => array(),
			);

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
			}

			return $menu_array;
		}

		/**
		 * Get menu currency symbol.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public static function get_currency_symbol() {
			global $wpdb;

			$settings_table = $wpdb->prefix . 'brm_options';
			$sql            = "SELECT $settings_table.option_value FROM $settings_table WHERE $settings_table.option_name = 'brm_menu_settings'";
			$settings       = unserialize( $wpdb->get_var( $sql ) );

			$symbols = include BEST_RESTAURANT_MENU_TEMPLATE_PATH . 'admin/vendor/currency-symbols.php';

			$site_currency = ( isset( $settings['business_currency'] ) && ! empty( $settings['business_currency'] ) && isset( $symbols[ $settings['business_currency'] ] ) ) ? $symbols[ $settings['business_currency'] ] : '$';

			return $site_currency;
		}

		/**
		 * Render shortcode builder form.
		 *
		 * @since   1.0.0
		 * @version 1.1.0
		 *
		 * @return mixed
		 */
		public static function render_shortcode_builder_form() {
			ob_start();

			global $wpdb;
			$groups_table = $wpdb->prefix . 'brm_groups';

			$sql = "SELECT * FROM $groups_table ORDER BY sort ASC";

			$groups = $wpdb->get_results( $sql );

			?>
				<div class="brm-shortcode-header">
					<h2><?php esc_html_e( 'Insert Restaurant Menu Shortcode', 'best-restaurant-menu' ); ?></h2>
				</div>
				<form method="post" multiple="multiple" class="brm-shortcode-builder" id="brm-shortcode-builder">
					<table class="form-table shortcode-builder">
						<tbody>
							<tr valign="top">
								<th scope="raw">
									<label for="groups-included"><?php esc_html_e( 'Groups', 'best-restaurant-menu' ); ?></label>
								</th>
								<td>
									<select name="groups" multiple="multiple" class="small-text groups-included" id="groups-included" style="width: 80%">
										<?php if ( ! empty( $groups ) ) : ?>
											<?php foreach ( $groups as $group ) : ?>
												<option value="<?php echo esc_attr( $group->id ); ?>"><?php echo esc_html( $group->name ); ?></option>
											<?php endforeach; ?>
										<?php endif; ?>
									</select>
									<p class="description" id="groups-included=description"><?php esc_html_e( 'Leave it empty to display all menu groups!', 'best-restaurant-menu' ); ?></p>
								</td>
							</tr>

							<tr valign="top">
								<th scope="raw">
									<label for="show-group-title"><?php esc_html_e( 'Show Group Title', 'best-restaurant-menu' ); ?></label>
								</th>
								<td>
									<input type="checkbox" name="show_group_title" checked value="yes" class="show-group-title" id="show-group-title" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="raw">
									<label for="show-group-desc"><?php esc_html_e( 'Show Group Description', 'best-restaurant-menu' ); ?></label>
								</th>
								<td>
									<input type="checkbox" name="show_group_desc" checked value="yes" class="show-group-desc" id="show-group-desc" />
								</td>
							</tr>
	
							<tr valign="top">
								<th scope="raw">
									<label for="show-items"><?php esc_html_e( 'Show Items', 'best-restaurant-menu' ); ?></label>
								</th>
								<td>
									<input type="checkbox" name="show_items" checked value="1" class="show-items" id="show-items" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="raw">
									<label for="view-mode"><?php esc_html_e( 'View mode', 'best-restaurant-menu' ); ?></label>
								</th>
								<td>
									<select id="view-mode" name="view_mode" class="small-text">
										<option value="minimalist"><?php esc_html_e( 'Minimalist', 'best-restaurant-menu' ); ?></option>
										<option value="two-column-minimalist"><?php esc_html_e( '2 Column Minimalist', 'best-restaurant-menu' ); ?></option>
										<option value="fancy"><?php esc_html_e( 'Fancy', 'best-restaurant-menu' ); ?></option>
										<option value="colorful"><?php esc_html_e( 'Colorful', 'best-restaurant-menu' ); ?></option>
										<option value="bold"><?php esc_html_e( 'Bold', 'best-restaurant-menu' ); ?></option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>

					<p class="submit">
						<input type="submit" name="insert-shortcode" value="<?php esc_html_e( 'Insert shortcode', 'best-restaurant-menu' ); ?>" class="button-primary button-large insert-shortcode" />
						<input type="submit" name="cancel-shortcode" value="<?php esc_html_e( 'Cancel', 'best-restaurant-menu' ); ?>" class="button-primary button-large cancel-shortcode" />

					</p>
				</form>
			<?php

			return ob_get_clean();
		}

		/**
		 * Get items per group ID.
		 *
		 * Get item for each group by sql.
		 *
		 * @param int $group_id The group ID
		 *
		 * @return deprecated
		 */
		public static function get_items_per_group_sql( $group_id ) {
			global $wpdb;

			$groups_table = $wpdb->prefix . 'brm_groups';
			$items_table  = $wpdb->prefix . 'brm_items';

			$sql   = "SELECT * FROM $items_table WHERE $items_table.group_id = '{$group_id}' ORDER BY sort ASC";
			$items = $wpdb->get_results( $sql );

			$html = '';

			if ( ! empty( $items ) ) {
				$html = "<ul class=''>\n";
				foreach ( $items as $item ) {
					$html .= "<li>\n";
					$html .= self::render_item_raw( $item->id, $item->name, $item->description, $item->image_id, $item->price, $item->sort, $item->group_id );
					$html .= "</li>\n";
				}

				$html .= "</ul>\n";
			}

			return $html;
		}

		/**
		 * Get template html.
		 *
		 * @param string $template_name The template name.
		 * @param array  $args          The shortcode args.
		 * @param string $template_path The template path.
		 * @param string $default_path  The default path.
		 *
		 * @return mixed/void
		 */
		public static function get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
			ob_start();

			// Get the template
			self::get_template( $template_name, $args, $template_path, $default_path );

			return ob_get_clean();
		}

		/**
		 * Get template.
		 *
		 * @param string $template_name The template name.
		 * @param array  $args          The shortcode args.
		 * @param string $template_path The template path.
		 * @param string $default_path  The default path.
		 */
		public static function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
			$template_name = $template_name . '.php';

			// extract args array.
			if ( is_array( $args ) && ! empty( $args ) ) {
				extract( $args );
			}

			$located = self::locate_template( $template_name, $template_path, $default_path );

			// Validate file existense.
			if ( ! file_exists( $located ) ) {
				_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '5.2.2' );

				return;
			}

			/**
			 * The brm_get_template filter.
			 *
			 * Allow 3rd party plugins filter template file outside this plugin.
			 *
			 * @param string $located       The template full location path.
			 * @param string $template_name The template name.
			 * @param array  $args          The args.
			 * @param string $template_path The template path.
			 * @param string $default_path  The default path.
			 *
			 * @return $located
			 */
			$located = apply_filters( 'brm_get_template', $located, $template_name, $args, $template_path, $default_path );

			/**
			 * The brm_before_template_part action.
			 *
			 * Fires before getting template.
			 *
			 * @param string $template_name The template name.
			 * @param string $template_path The template path.
			 * @param string $located       The template full location path.
			 * @param array  $args          The args.
			 */
			do_action( 'brm_before_template_part', $template_name, $template_path, $located, $args );

			// Include template.
			include $located;

			/**
			 * The brm_after_template_part action.
			 *
			 * Fires after including template.
			 *
			 * @param string $template_name The template name.
			 * @param string $template_path The template path.
			 * @param string $located       The template full location path.
			 * @param array  $args          The args.
			 */
			do_action( 'brm_after_template_part', $template_name, $template_path, $located, $args );
		}

		/**
		 * Locates template
		 *
		 * @param string $template_name The template name.
		 * @param string $template_path The template path.
		 * @param string $default_path  The default path.
		 *
		 * @since 1.0.0
		 *
		 * @return mixed
		 */
		public static function locate_template( $template_name, $template_path = '', $default_path = '' ) {

			if ( ! $template_path ) {
				$template_path = 'best-restaurant-menu/';
			}

			if ( ! $default_path ) {
				$default_path = BEST_RESTAURANT_MENU_TEMPLATE_PATH;
			}

			$template_args = array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			);

			// Locate template from theme folder.
			$template = locate_template( $template_args );

			// If not existing template folder/file in the theme.
			if ( ! $template ) {
				// Get default template.
				$template = $default_path . $template_name;
			}

			/**
			 * The brm_locate_template filter.
			 *
			 * @param string $template      The template location.
			 * @param string $template_name The template name.
			 * @param string $template_path The template path.
			 *
			 * @return mixed The template location
			 */
			return apply_filters( 'brm_locate_template', $template, $template_name, $template_path );
		}
	}

endif;
