<?php
/**
 * Template Name : Best Restaurant Menu.
 *
 * This template can be overridden by copying it to yourtheme/best-restaurant-menu/menu/best-restaurant-menu.php.
 *
 * @see
 * @package Best_Restaurant_Menu
 * @author  PriceListo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get header.
get_header();

/*
 * Render display menu shortcode
 */
echo do_shortcode( '[brm_restaurant_menu]' );

// Get footer.
get_footer();
