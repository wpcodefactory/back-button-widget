<?php
/**
 * Back Button Widget - Main Class.
 *
 * @version 1.8.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\Back_Button_Widget
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_Back_Button_Widget' ) ) :

	/**
	 * Alg_Back_Button_Widget class.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 */
	final class Alg_Back_Button_Widget {

		/**
		 * Plugin version.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @var string
		 */
		public $version = ALG_BACK_BUTTON_WIDGET_VERSION;

		/**
		 * The single instance of the class.
		 *
		 * @version 1.8.0
		 * @since   1.0.0
		 *
		 * @var Alg_Back_Button_Widget
		 */
		protected static $instance = null;

		/**
		 * Main Alg_Back_Button_Widget Instance.
		 *
		 * Ensures only one instance of Alg_Back_Button_Widget is loaded or can be loaded.
		 *
		 * @version 1.8.0
		 * @since   1.0.0
		 *
		 * @static
		 *
		 * @return Alg_Back_Button_Widget
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Alg_Back_Button_Widget Constructor.
		 *
		 * @version 1.8.0
		 * @since   1.0.0
		 *
		 * @access public
		 */
		public function __construct() {
			// Load libs.
			if ( is_admin() ) {
				require_once plugin_dir_path( ALG_BACK_BUTTON_WIDGET_FILE ) . 'vendor/autoload.php';
			}

			// Pro.
			if ( 'back-button-widget-pro.php' === basename( ALG_BACK_BUTTON_WIDGET_FILE ) ) {
				require_once plugin_dir_path( __FILE__ ) . 'pro/class-alg-back-button-widget-pro.php';
			}

			// Include required files.
			$this->includes();

			// Admin.
			if ( is_admin() ) {
				$this->admin();
			}

			// Fontawesome.
			add_action( 'wp_enqueue_scripts', array( $this, 'fontawesome' ) );
		}

		/**
		 * Fontawesome.
		 *
		 * @version 1.8.0
		 * @since   1.5.3
		 *
		 * @see https://cdnjs.com/libraries/font-awesome
		 * @see https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css
		 *
		 * @todo (dev) `$this->options`?
		 */
		public function fontawesome() {
			$options = get_option( 'alg_back_button', array() );
			if (
				! empty( $options['fontawesome_enabled'] ) &&
				'yes' === $options['fontawesome_enabled']
			) {
				wp_enqueue_style(
					'alg-back-button-fontawesome',
					alg_back_button_widget()->plugin_url() . '/assets/font-awesome/all.min.css',
					array(),
					$this->version
				);
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @version 1.8.0
		 * @since   1.0.0
		 */
		public function includes() {
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-back-button-wp-widget.php';
			require_once plugin_dir_path( __FILE__ ) . 'alg-back-button-functions.php';
		}

		/**
		 * Admin.
		 *
		 * @version 1.7.0
		 * @since   1.2.1
		 */
		public function admin() {
			// Settings.
			require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-back-button-settings.php';

			// Action links.
			add_filter( 'plugin_action_links_' . plugin_basename( ALG_BACK_BUTTON_WIDGET_FILE ), array( $this, 'action_links' ) );

			// "Recommendations" page.
			add_action( 'init', array( $this, 'add_cross_selling_library' ) );

			// Version update.
			if ( get_option( 'alg_back_button_widget_version', '' ) !== $this->version ) {
				add_action( 'admin_init', array( $this, 'version_updated' ) );
			}
		}

		/**
		 * Action links.
		 *
		 * @version 1.7.0
		 * @since   1.2.1
		 *
		 * @param mixed $links Array of existing action links.
		 *
		 * @return array
		 */
		public function action_links( $links ) {
			$custom_links = array();

			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=alg-back-button-settings' ) . '">' .
				__( 'Settings', 'back-button-widget' ) .
			'</a>';

			if ( 'back-button-widget.php' === basename( ALG_BACK_BUTTON_WIDGET_FILE ) ) {
				$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/back-button-widget-wordpress-plugin/">' .
					__( 'Go Pro', 'back-button-widget' ) .
				'</a>';
			}

			return array_merge( $custom_links, $links );
		}

		/**
		 * Add cross selling library.
		 *
		 * @version 1.7.0
		 * @since   1.7.0
		 */
		public function add_cross_selling_library() {
			if ( ! class_exists( '\WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {
				return;
			}

			$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
			$cross_selling->setup( array( 'plugin_file_path' => ALG_BACK_BUTTON_WIDGET_FILE ) );
			$cross_selling->init();
		}

		/**
		 * Version updated.
		 *
		 * @version 1.2.1
		 * @since   1.2.1
		 */
		public function version_updated() {
			update_option( 'alg_back_button_widget_version', $this->version );
		}

		/**
		 * Get the plugin url.
		 *
		 * @version 1.5.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugin_dir_url( ALG_BACK_BUTTON_WIDGET_FILE ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @version 1.5.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( ALG_BACK_BUTTON_WIDGET_FILE ) );
		}
	}

endif;
