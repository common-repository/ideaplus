<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ideaplus_Plugin
 * @subpackage Ideaplus_Plugin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @author     Your Name <email@example.com>
 * @subpackage Ideaplus_Plugin/public
 * @package    Ideaplus_Plugin
 */
if(!defined('ABSPATH')){ exit; }
class Ideaplus_Plugin_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $ideaplus_plugin The ID of this plugin.
     */
    private $ideaplus_plugin;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * @description 写下注释
     * @author      ylw <767502630@qq.com>
     * @var Ideaplus_Plugin_Loader
     */
    protected $loader;

    protected $class_name;

    /**
     * Initialize the class and set its properties.
     *
     * @param Ideaplus_Plugin_Loader $loader
     * @param string                 $ideaplus_plugin The name of the plugin.
     * @param string                 $version         The version of this plugin.
     *
     * @since    1.0.0
     */
    public function __construct($loader, $ideaplus_plugin, $version)
    {
        $this->ideaplus_plugin = $ideaplus_plugin;
        $this->version         = $version;
        $this->loader          = $loader;
        $this->loader->add_action('wp_enqueue_scripts', $this, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $this, 'enqueue_scripts');
        //$this->class_name      = $this->get_class_name();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ideaplus_Plugin_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ideaplus_Plugin_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->ideaplus_plugin, plugin_dir_url(__FILE__) . 'css/ideaplus-plugin-' . $this->class_name . '.css', [], $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ideaplus_Plugin_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ideaplus_Plugin_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->ideaplus_plugin, plugin_dir_url(__FILE__) . 'js/ideaplus-plugin-' . $this->class_name . '.js', ['jquery'], $this->version, false);
    }

    public function init()
    {
        require_once plugin_dir_path(__FILE__) . 'partials/ideaplus-plugin-' . $this->class_name . '-display.php';
    }

    protected function get_class_name()
    {
        $name = __CLASS__;
        $arr  = explode('_', $name);
        $zone = strtolower(end($arr));
        return $zone;
    }

}
