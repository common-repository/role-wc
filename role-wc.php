<?php
/**
 * Plugin Name: Role Management for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/role-wc/
 * Description: Role management for WooCommerce.
 * Version: 1.0.4
 * Author: Artisan Workshop
 * Author URI: https://wc.artws.info/
 * Requires at least: 5.8
 * Tested up to: 5.8
 * WC requires at least: 5.5.1
 * WC tested up to: 5.5.1
 *
 * Text Domain: role-wc
 * Domain Path: /i18n/
 *
 * @package Role for WooCommerce
 * @category User Role
 * @author Artisan Workshop
 * @copyright Copyright (c) Artisan Workshop
 */
//use ArtisanWorkshop\WooCommerce\PluginFramework\v2_0_11 as Framework;
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'ROLE_WC' ) ) :

    class ROLE_WC{

        /**
         * Role for WooCommerce version.
         *
         * @var string
         */
        public $version = '1.0.4';

        /**
         * Japanized for WooCommerce Framework version.
         *
         * @var string
         */
        public $framework_version = '2.0.11';

        /**
         * The single instance of the class.
         *
         * @var object
         */
        protected static $instance = null;

        /**
         * Japanized for WooCommerce Constructor.
         *
         * @access public
         * @return mixed
         */
        public function __construct() {
            //
            add_action( 'deactivated_plugin', array( $this, 'deactivated_plugin' ) );
        }

        /**
         * Get class instance.
         *
         * @return object Instance.
         */
        public static function instance() {
            if ( null === static::$instance ) {
                static::$instance = new static();
            }
            return static::$instance;
        }

        /**
         * Init the feature plugin, only if we can detect WooCommerce.
         *
         * @since 1.0.0
         * @version 1.0.0
         */
        public function init() {
            $this->define_constants();
            register_deactivation_hook( ROLE_WC_PLUGIN_FILE, array( $this, 'on_deactivation' ) );
            add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), 20 );
        }

        /**
         * Flush rewrite rules on deactivate.
         *
         * @return void
         */
        public function on_deactivation() {
            flush_rewrite_rules();
        }

        /**
         * Setup plugin once all other plugins are loaded.
         *
         * @return void
         */
        public function on_plugins_loaded() {
            $this->load_plugin_textdomain();
            $this->includes();
        }

        /**
         * Define Constants.
         */
        protected function define_constants() {
            $this->define( 'ROLE_WC_ABSPATH', dirname( __FILE__ ) . '/' );
            $this->define( 'ROLE_WC_URL_PATH', plugins_url( '/', __FILE__ ) );
            $this->define( 'ROLE_WC_INCLUDES_PATH', ROLE_WC_ABSPATH . 'includes/' );
            $this->define( 'ROLE_WC_PLUGIN_FILE', __FILE__ );
            $this->define( 'ROLE_WC_VERSION', $this->version );
            $this->define( 'ROLE_WC_FRAMEWORK_VERSION', $this->framework_version );
        }

        /**
         * Load Localisation files.
         */
        protected function load_plugin_textdomain() {
            load_plugin_textdomain( 'role-wc', false, basename( dirname( __FILE__ ) ) . '/i18n' );
        }

        /**
         * Include JP4WC classes.
         */
        private function includes() {
            //load framework
            $version_text = 'v'.str_replace('.', '_', ROLE_WC_FRAMEWORK_VERSION);
            if ( ! class_exists( '\\ArtisanWorkshop\\WooCommerce\\PluginFramework\\'.$version_text.'\\JP4WC_Plugin' ) ) {
                require_once ROLE_WC_ABSPATH.'includes/jp4wc-framework/class-jp4wc-framework.php';
            }
            // Installation
            require_once ROLE_WC_ABSPATH.'includes/class-role-wc-install.php';
            // Remove Sub Menus
            require_once ROLE_WC_ABSPATH.'includes/class-role-wc-remove-submenus.php';
            // Admin Setting Screen
            require_once ROLE_WC_ABSPATH.'includes/admin/class-role-wc-admin-screen.php';
        }

        /**
         * Define constant if not already set.
         *
         * @param string Constant name.
         * @param string|bool Constant value.
         */
        protected function define( $name, $value ) {
            if ( ! defined( $name ) ) {
                define( $name, $value );
            }
        }

        /**
         * Ran when any plugin is deactivated.
         *
         * @since 3.6.0
         * @param string $filename The filename of the deactivated plugin.
         */
        public function deactivated_plugin( $filename ) {
        }
    }
endif;

/**
 * Load plugin functions.
 */
add_action( 'plugins_loaded', 'ROLE_WC_plugin');

function ROLE_WC_plugin() {
    if ( is_woocommerce_active() ) {
        ROLE_WC::instance()->init();
    }else{
        add_action( 'admin_notices', 'role_wc_fallback_notice' );
    }
}

function role_wc_fallback_notice() {
    ?>
    <div class="error">
        <ul>
            <li><?php echo sprintf(__( 'Role for WooCommerce is enabled but not effective. It requires %s in order to work.', 'role-wc' ), __( '"WooCommerce"', 'role-wc' ));?></li>
        </ul>
    </div>
    <?php
}

/**
 * WC Detection
 */
if ( ! function_exists( 'is_woocommerce_active' ) ) {
    function is_woocommerce_active() {
        if ( ! isset($active_plugins) ) {
            $active_plugins = (array) get_option( 'active_plugins', array() );

            if ( is_multisite() )
                $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }
        return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php',$active_plugins );
    }
}
