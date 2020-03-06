=== Best Restaurant Menu by PriceListo ===
Contributors: pricelisto
Tags: restaurant menu, food menu, dinner menu, restaurant, price list, cafe, food, fast food, pizza
Requires at least: 4.4.0
Tested up to: 5.3.2
Stable tag: 1.0.1
License: GNU General Public License v3.0

The fastest and easiest way to create professional-looking menu or price list for your restaurant or business. Includes five menu templates and support for custom templates as well. You can insert the menu into a WordPress page using a shortcode or selecting the page template from the page editor settings.

== Description ==

= What this plugin can do for you =

- Allows to create a menu quickly for your restaurant or business.
- It comes with 5 templates out of the box and supports custom templating.
- Default templates are all responsive and look great on desktop, tablet, and mobile.
- Menu supports groups/categories and items.
- Items can include item name, description, image, and price.
- Menu editor allows you to easily drag and drop groups and items to re-organize easily and quickly.

== Installation ==

= From your WordPress Dashboard =

1. Visit “Plugins” → Add New
2. Search for Best Restaurant Menu by PriceListo
3. Activate Best Restaurant Menu from your Plugins page.

= From WordPress.org =

1. Download Best Restaurant Menu by PriceListo
2. Upload the “best-restaurant-menu” directory to your “/wp-content/plugins/” directory, using ftp, sftp, scp etc.
3. Activate Best Restaurant Menu from your Plugins page.

= Once Activated =

1. In WordPress admin, click on Menu in left navigation.
2. Then click on Settings link under the Menu.
3. Fill out the settings page with your business name, location, currency, and menu template.
4. Click "Save Changes" button to save Settings.
5. Then click on Menu link under the main Menu category in the left navigation.
6. Start adding groups and items to your menu.
7. When you're done adding your groups and items, it's time to insert the menu on a WordPress page.

= Adding menu to the WordPress page =

1. Upon plugin activation, a new page is created called Menu. The menu is automatically inserted on this page. If you'd like to display the menu on a different page, proceed to step 2.
2. Create a page or go into the editing mode in an already-created page.
3. Under Page Attributes, select "Best Restaurant Menu" as template of the page.
4. If you want more control over it, you can instead insert the shortcode `[brm_restaurant_menu]`.

= Short code attibutes =

1. `groups` - With this attribute you can specify the group IDs you would like to display in the frontend by this shortcode (comma-separated). ex: `[brm_restaurant_menu groups="1,4,6"]`. That will display only three groups with IDs 1, 4 and 6.
2. `show_items` - Whether to display the group items or not. `0` : Not to show items. Displays only the groups without related items. `1` : Show items. ex: `[brm_restaurant_menu show_items="1"]`
3. `view` - With this attribute, you can select the style view of the menu. Available style attributes: `minimalist`, `two-column-minimalist`, `fancy`, `colorful`, and `bold`. ex: `[brm_restaurant_menu view="colorful"]`

== Frequently Asked Questions ==

= Can I use this menu for businesses other than restaurants? =

Yes. The Best Restaurant Menu plugin can be used to create menus and pricing lists for any business type.

= Are the menus responsive and work on mobile devices? =

You. The menu templates were designed and coded to look great on desktop and mobile.

= How can I create a custom template for my menu? =

Creating a custom template is easy. If you're used to working and creating WordPress themes, then you're in luck. Best Restuarant Menu templates work the same way. To get started, simply create a new file in `/wp-content/plugins/best-restaurant-menu/templates/` and use the format from one of the default templates as a starting point. The template file should be named the same as the template name. If you wish to use a separate css file, you can name it the same as the template name.

== Screenshots ==

1. Floating badge that shows up on every part of your website.
2. Embedded badge showing up on the cart page. (WooCommerce)
3. Embedded badge showing up on the checkout page. (WooCommerce)

== Changelog ==

= 1.0.0 - 2020-01-14 =

*Initial release*

= 1.0.1 - 2020-03-06 =

* Fixed a bug regarding database table naming.
