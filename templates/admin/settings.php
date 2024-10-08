<?php
/**
 * Settings.
 *
 * @package Best_Restaurant_Menu/Templates/
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wpdb;

$settings_table = $wpdb->prefix . 'brm_options';

$sql = $wpdb->prepare( "SELECT %i.option_value FROM %i WHERE %i.option_name = 'brm_menu_settings'", array( $settings_table, $settings_table, $settings_table ) );

// General settings.
$settings = unserialize( $wpdb->get_var( $sql ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Properly prepared SQL statement.

$countries  = include_once 'vendor/countries.php';
$currencies = include_once 'vendor/currencies.php';
$symbols    = include_once 'vendor/currency-symbols.php';

$theme_templates = apply_filters(
	'brm_theme_templates',
	array(
		'minimalist'            => 'Minimalist',
		'two-column-minimalist' => '2 Column Minimalist',
		'fancy'                 => 'Fancy',
		'colorful'              => 'Colorful',
		'bold'                  => 'Bold',
	)
);

/**
 * Save settings.
 */
if ( isset( $_POST['save'] ) ) {

	// Validate user permissions.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions.', 'best-restaurant-menu' ) );
	}

	// Check for nonce security.
	if ( ! isset( $_POST['brm_save_settings_nonce'] ) || ( isset( $_POST['brm_save_settings_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['brm_save_settings_nonce'] ) ), 'brm_settings_nonce' ) ) ) {
		wp_die( esc_html__( 'Action failed due to security issues, please try again later', 'best-restaurant-menu' ) );
	}

	$settings['business_name']     = isset( $_POST['business_name'] ) ? sanitize_text_field( wp_unslash( $_POST['business_name'] ) ) : '';
	$settings['business_address']  = isset( $_POST['business_address'] ) ? sanitize_text_field( wp_unslash( $_POST['business_address'] ) ) : '';
	$settings['business_city']     = isset( $_POST['business_city'] ) ? sanitize_text_field( wp_unslash( $_POST['business_city'] ) ) : '';
	$settings['business_state']    = isset( $_POST['business_state'] ) ? sanitize_text_field( wp_unslash( $_POST['business_state'] ) ) : '';
	$settings['business_zip_code'] = isset( $_POST['business_zip_code'] ) ? sanitize_text_field( wp_unslash( $_POST['business_zip_code'] ) ) : '';
	$settings['business_country']  = isset( $_POST['business_country'] ) ? sanitize_text_field( wp_unslash( $_POST['business_country'] ) ) : '';
	$settings['business_phone']    = isset( $_POST['business_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['business_phone'] ) ) : '';
	$settings['business_currency'] = isset( $_POST['business_currency'] ) ? sanitize_text_field( wp_unslash( $_POST['business_currency'] ) ) : '';
	$settings['theme_template']    = isset( $_POST['theme_template'] ) ? sanitize_text_field( wp_unslash( $_POST['theme_template'] ) ) : '';

	$serialized_settings = serialize( $settings );

	// Validate updating option.
	if ( $wpdb->replace(   // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Necessary for custom table operations, following best practices for security.
		$settings_table,
		array(
			'option_name'  => 'brm_menu_settings',
			'option_value' => $serialized_settings,
		),
		array( '%s', '%s' )
	) ) {
		$notices['settings_updated'] = array(
			'class'   => 'updated',
			'message' => __( 'Settings updated!', 'best-restaurant-menu' ),
		);
	} else {
		$notices['settings_update_failed'] = array(
			'class'   => 'error',
			'message' => __( 'Updating settings failed!', 'best-restaurant-menu' ),
		);
	}

	// Output notices if any.
	if ( isset( $notices ) ) {
		BRM_Utilities::add_settings_notices( $notices );
	}
}

$nonce = wp_create_nonce( 'brm_settings_nonce' );
?>

<form method="post" id="brm_settings">
	<h2><?php esc_html_e( 'Settings', 'best-restaurant-menu' ); ?></h2>

	<table class="form-table brm-settings-general-table" id="brm-settings-general-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="business_name"><?php esc_html_e( 'Business Name', 'best-restaurant-menu' ); ?></label>
				</th>
				<td>
					<input type="text" name="business_name" id="business_name" value="<?php echo isset( $settings['business_name'] ) ? esc_html( stripslashes( $settings['business_name'] ) ) : ''; ?>" class="regular-text">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="business_address"><?php esc_html_e( 'Address', 'best-restaurant-menu' ); ?></label>
				</th>
				<td>
					<input type="text" name="business_address" id="business_address" value="<?php echo isset( $settings['business_address'] ) ? esc_html( stripslashes( $settings['business_address'] ) ) : ''; ?>" class="regular-text">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="business_city"><?php esc_html_e( 'City', 'best-restaurant-menu' ); ?></label>
				</th>
				<td>
					<input type="text" name="business_city" id="business_city" value="<?php echo isset( $settings['business_city'] ) ? esc_html( stripslashes( $settings['business_city'] ) ) : ''; ?>" class="regular-text">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="business_state"><?php esc_html_e( 'State', 'best-restaurant-menu' ); ?></label>
				</th>
				<td>
					<input type="text" name="business_state" id="business_state" value="<?php echo isset( $settings['business_state'] ) ? esc_html( stripslashes( $settings['business_state'] ) ) : ''; ?>" class="regular-text">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="business_zip_code"><?php esc_html_e( 'ZIP Code', 'best-restaurant-menu' ); ?></label>
				</th>
				<td>
					<input type="text" name="business_zip_code" id="business_zip_code" value="<?php echo isset( $settings['business_zip_code'] ) ? esc_html( stripslashes( $settings['business_zip_code'] ) ) : ''; ?>" class="regular-text">
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="business_country"><?php esc_html_e( 'Country', 'best-restaurant-menu' ); ?></label>
				</th>
				<td>
					<select name="business_country" id="business_country">
						<?php
						if ( ! empty( $countries ) ) :
							foreach ( $countries as $country_code => $country_name ) :
								?>
								<option value="<?php echo esc_attr( wp_unslash( $country_code ) ); ?>" <?php selected( $settings['business_country'], $country_code ); ?>><?php echo esc_html( wp_unslash( $country_name ) ); ?></option>
								<?php
							endforeach;
							endif;
						?>
					</select>
				</td>
			</tr>


			<tr valign="top">
				<th scope="row">
					<label for="business_phone"><?php esc_html_e( 'Phone Number', 'best-restaurant-menu' ); ?></label>
				</th>
				<td>
					<input type="text" name="business_phone" id="business_phone" value="<?php echo isset( $settings['business_phone'] ) ? esc_html( wp_unslash( $settings['business_phone'] ) ) : ''; ?>" class="regular-text">
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="business_currency"><?php esc_html_e( 'Currency', 'best-restaurant-menu' ); ?></label>
				</th>
				<td>
					<select name="business_currency" id="business_currency">
						<?php
						if ( ! empty( $currencies ) ) :
							foreach ( $currencies as $currency_code => $currency_name ) :
								?>
								<option value="<?php echo esc_attr( wp_unslash( $currency_code ) ); ?>" <?php selected( $settings['business_currency'], $currency_code ); ?>><?php echo esc_html( isset( $symbols[ $currency_code ] ) ? wp_unslash( $currency_name . ' (' . $symbols[ $currency_code ] . ') ' ) : $currency_name ); ?></option>
								<?php
							endforeach;
							endif;
						?>
					</select>
				</td>
			</tr>
		</tbody>

	</table>

	<h2><?php esc_html_e( 'Display', 'best-restaurant-menu' ); ?></h2>
	<table class="form-table brm-settings-display-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="theme-template"><?php esc_html_e( 'Theme template', 'best-restaurant-menu' ); ?></label>
				</th>
				<td>
					<select name="theme_template" id="theme-template">
						<?php
						if ( ! empty( $theme_templates ) ) :
							foreach ( $theme_templates as $template_key => $template_name ) :
								?>
								<option value="<?php echo esc_attr( wp_unslash( $template_key ) ); ?>" <?php selected( $settings['theme_template'], $template_key ); ?>><?php echo esc_html( wp_unslash( $template_name ) ); ?></option>
								<?php
							endforeach;
							endif;
						?>
					</select>
					<p class="description" id="theme-template-description"><?php esc_html_e( 'Theme used to generate front-end menu', 'best-restaurant-menu' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<p class="submit">
		<input type="hidden" name="brm_save_settings_nonce" value="<?php echo esc_attr( $nonce ); ?>">
		<input type="submit" name="save" value="<?php esc_html_e( 'Save Changes', 'best-restaurant-menu' ); ?>" class="button-primary">
	</p>
</form>
