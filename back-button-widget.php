<?php
/**
 * Plugin Name: Back Button Widget
 * Plugin URI: https://wpfactory.com/item/back-button-widget-wordpress-plugin/
 * Description: Back button widget for WordPress.
 * Version: 1.8.0
 * Author: WPFactory
 * Author URI: https://wpfactory.com
 * Text Domain: back-button-widget
 * Domain Path: /langs
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WPFactory\Back_Button_Widget
 */

defined( 'ABSPATH' ) || exit;

if ( 'back-button-widget.php' === basename( __FILE__ ) ) {
	if ( ! function_exists( 'alg_back_button_widget_is_pro_activated' ) ) {
		/**
		 * Check if Pro plugin version is activated.
		 *
		 * @version 1.8.0
		 * @since   1.5.0
		 */
		function alg_back_button_widget_is_pro_activated() {
			$plugin = 'back-button-widget-pro/back-button-widget-pro.php';
			return (
				in_array(
					$plugin,
					(array) get_option( 'active_plugins', array() ),
					true
				) ||
				(
					is_multisite() &&
					array_key_exists(
						$plugin,
						(array) get_site_option( 'active_sitewide_plugins', array() )
					)
				)
			);
		}
	}

	if ( alg_back_button_widget_is_pro_activated() ) {
		return;
	}
}

/**
 * Plugin version.
 */
defined( 'ALG_BACK_BUTTON_WIDGET_VERSION' ) || define( 'ALG_BACK_BUTTON_WIDGET_VERSION', '1.8.0' );

/**
 * Plugin file.
 */
defined( 'ALG_BACK_BUTTON_WIDGET_FILE' ) || define( 'ALG_BACK_BUTTON_WIDGET_FILE', __FILE__ );

/**
 * Include the main plugin class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-alg-back-button-widget.php';

if ( ! function_exists( 'alg_back_button_widget' ) ) {
	/**
	 * Returns the main instance of Alg_Back_Button_Widget to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_back_button_widget() {
		return Alg_Back_Button_Widget::instance();
	}
}

/**
 * Initialize the plugin.
 */
add_action( 'plugins_loaded', 'alg_back_button_widget' );
