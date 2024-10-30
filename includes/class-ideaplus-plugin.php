<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.ideaplus.com/
 * @since      1.0.0
 *
 * @package    Ideaplus_Plugin
 * @subpackage Ideaplus_Plugin/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @author     brazz <767502630@qq.com>
 * @package    Ideaplus_Plugin
 * @subpackage Ideaplus_Plugin/includes
 * @since      1.0.0
 */
if(!defined('ABSPATH')){ exit; }
class Ideaplus_Plugin
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Ideaplus_Plugin_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $ideaplus_plugin The string used to uniquely identify this plugin.
     */
    protected $ideaplus_plugin;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('IDEAPLUS_PLUGIN_VERSION')) {
            $this->version = IDEAPLUS_PLUGIN_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->ideaplus_plugin = 'ideaplus';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Ideaplus_Plugin_Loader. Orchestrates the hooks of the plugin.
     * - Ideaplus_Plugin_i18n. Defines internationalization functionality.
     * - Ideaplus_Plugin_Admin. Defines all hooks for the admin area.
     * - Ideaplus_Plugin_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ideaplus-plugin-loader.php';
        /**
         * TODO::翻译
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ideaplus-plugin-func.php';
        /**
         *
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ideaplus-plugin-server.php';
        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ideaplus-plugin-i18n.php';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ideaplus-plugin-admin.php';
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ideaplus-plugin-public.php';
        $this->loader = new Ideaplus_Plugin_Loader();

        $this->loader->add_action("plugins_loaded",$this,"addShipping", 20);

        $this->loader->add_action("plugins_loaded",$this,"updateClientScheme", 20);


    }

    /**
     *
     */
    public function addShipping()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ideaplus-plugin-shipping.php';
        Ideaplus_Plugin_Shipping::init();
    }

    /**
     * update client scheme
     */
    public function updateClientScheme()
    {
        Ideaplus_Plugin_Func::get_client_headers();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Ideaplus_Plugin_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Ideaplus_Plugin_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Ideaplus_Plugin_Admin($this->get_ideaplus_plugin(), $this->get_version());
        // $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        // $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'load_admin_menu');
        $this->loader->add_action('rest_api_init', $this, 'define_rest_route_hooks', 20);
        $this->loader->add_action('wp_ajax_ideaplus_ajax_check_auth_status', $plugin_admin, 'check_auth_status');
        $this->loader->add_action('wp_ajax_ideaplus_ajax_clear_cache', $plugin_admin, 'clear_cache');
        $this->loader->add_action( 'add_meta_boxes', $this, 'add_meta_box' );
        
        if (Ideaplus_Plugin_Func::getCurrentPage() == Ideaplus_Plugin_Func::UPDATE_OPTION_PAGE) {
            $this->loader->add_action("update_option", $plugin_admin,"updateShopDomain",20);
        }
        
        //add_action( 'wp_ajax_ajax_force_check_connect_status', array( 'Printful_Integration', 'ajax_force_check_connect_status' ) );
    }

    public function add_meta_box()
    {
        add_meta_box( 'woocommerce-advanced-shipment-tracking', __( 'Shipment Tracking', 'woo-advanced-shipment-tracking' ), array( $this, 'meta_box' ), 'shop_order', 'side', 'high' );
    }

    public function meta_box()
    {
        global $post;
        $tracking_items = $this->get_order_track_info($post->ID);
        if ($tracking_items) {
            $tracking_link = $tracking_items['url'] . $tracking_items['number'];
            echo '<div class="tracking-info">';
            echo '<p>Ideaplus Tracking info</p>';
            echo '<p>'. esc_html($tracking_items['company_code'] . ' ' . $tracking_items['company'] . '-<a target="_blank" href="'. $tracking_link .'">' . $tracking_items['number']) .'</a></p>';
            echo '<a class="button button-primary btn_ast2 button-save-form" target="_blank" href="' . esc_url($tracking_link) . '">Tracking detail</a>';
            echo '</div>';
        }
    }

    /**
     * @description get order track info
     */
    private function get_order_track_info($order_id)
    {
        if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
            $tracking_items = get_post_meta( $order_id, '_ideaplus_track_number', true );
        } else {
            $tracking_items = wc_get_order_item_meta($order_id, '_ideaplus_track_number', true );			
        }
        
        if ( is_array( $tracking_items ) ) {
            return $tracking_items;
        } else {
            return array();
        }
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ideaplus-plugin-product.php';
        new Ideaplus_Plugin_Product($this->loader, $this->get_ideaplus_plugin(), $this->get_version());
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ideaplus-plugin-cart.php';
        new Ideaplus_Plugin_Cart($this->loader, $this->get_ideaplus_plugin(), $this->get_version());
    }

    public function define_rest_route_hooks()
    {
        /**
         * TODO::翻译
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ideaplus-plugin-rest-api-route.php';
        $rest_api_route = new Ideaplus_Plugin_Rest_Api_Route($this->get_ideaplus_plugin(), $this->get_version());
        $rest_api_route->init();
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_ideaplus_plugin()
    {
        return $this->ideaplus_plugin;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Ideaplus_Plugin_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

}
