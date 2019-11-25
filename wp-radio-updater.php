<?php

/**
 * Plugin Name: WP Radio Updater
 * Plugin URI:  https://princeboss.com
 * Description: Update Your Radio Station.
 * Version:     1.0.0
 * Author:      Prince
 * Author URI:  http://princeboss.com
 * Text Domain: wp-radio-updater
 * Domain Path: /languages/
 */

defined( 'ABSPATH' ) || exit();


/**
 * Main initiation class
 *
 * @since 1.0.0
 */
final class WP_Radio_Updater {

	public $version = '0.0.1';

	private $min_php = '5.6.0';

	private $min_wp_radio = '2.0.5';

	private $name = 'WP Radio Updater';

	protected static $instance = null;

	public function __construct() {
		register_activation_hook( __FILE__, [ $this, 'install' ] );
		add_action( 'plugins_loaded', [ $this, 'let_the_journey_begin' ] );
	}

	function install() {
		include_once dirname( __FILE__ ) . '/includes/class-install.php';
		call_user_func( [ 'WP_Radio_Updater_Install', 'activate' ] );
	}

	function let_the_journey_begin() {
		if ( $this->check_environment() ) {
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
			do_action( 'wp_radio_updater_loaded' );
		}
	}

	function check_environment() {

		$return = true;

		if ( version_compare( PHP_VERSION, $this->min_php, '<=' ) ) {
			$return = false;

			$notice = sprintf(
			/* translators: %s: Min PHP version */
				esc_html__( 'Unsupported PHP version Min required PHP Version: "%s"', 'wp-radio-updater' ),
				$this->min_php
			);
		}

		// Check if WP Radio installed and activated
		if ( ! did_action( 'wp_radio_loaded' ) ) {
			$return = false;

			$notice = sprintf(
			/* translators: 1: Plugin name 2: WP Radio */
				esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'wp-radio-updater' ),
				'<strong>' . $this->name . '</strong>',
				'<strong>' . esc_html__( 'WP Radio', 'wp-radio-updater' ) . '</strong>'
			);

		}

		//check min WP Radio version
		if ( version_compare( WP_RADIO_VERSION, $this->min_wp_radio, '<' ) ) {
			$return = false;

			$notice = sprintf(
			/* translators: 1: Plugin name 2: WP Radio 3: Required WP Radio version */
				esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'wp-radio-updater' ),
				'<strong>' . $this->name . '</strong>',
				'<strong>' . esc_html__( 'WP Radio', 'wp-radio-updater' ) . '</strong>',
				$this->min_wp_radio
			);
		}

		if ( ! $return ) {

			add_action( 'admin_notices', function () use ( $notice ) { ?>
                <div class="notice is-dismissible notice-error">
                    <p><?php echo $notice; ?></p>
                </div>
			<?php } );

			if ( ! function_exists( 'deactivate_plugins' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			deactivate_plugins( plugin_basename( __FILE__ ) );

			return $return;
		} else {
			return $return;
		}

	}

	function define_constants() {
		define( 'WR_UPDATER_VERSION', $this->version );
		define( 'WR_UPDATER_FILE', __FILE__ );
		define( 'WR_UPDATER_PATH', dirname( WR_UPDATER_FILE ) );
		define( 'WR_UPDATER_INCLUDES', WR_UPDATER_PATH . '/includes' );
		define( 'WR_UPDATER_URL', plugins_url( '', WR_UPDATER_FILE ) );
		define( 'WR_UPDATER_ASSETS', WR_UPDATER_URL . '/assets' );
		define( 'WR_UPDATER_TEMPLATES', WR_UPDATER_PATH . '/templates' );
	}

	function includes() {
		//Freemius
		//include_once WR_UPDATER_INCLUDES . '/freemius.php';

		//core includes
		include_once WR_UPDATER_INCLUDES . '/wp-async-request.php';
		include_once WR_UPDATER_INCLUDES . '/wp-background-process.php';
		include_once WR_UPDATER_INCLUDES . '/functions.php';
		include_once WR_UPDATER_INCLUDES . '/class-checker.php';


	}

	function init_hooks() {

		// Localize our plugin
		add_action( 'init', [ $this, 'localization_setup' ] );

		//action_links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'plugin_action_links' ] );
	}

	function localization_setup() {
		load_plugin_textdomain( 'wp-radio-updater', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	function plugin_action_links( $links ) {

		return $links;
	}

	static function instance() {

		if ( is_null( self::$instance ) ) {

			self::$instance = new self();
		}

		return self::$instance;
	}

}

function wr_updater() {
	return WP_Radio_Updater::instance();
}

wr_updater();
