<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ideaplus_Plugin
 * @subpackage Ideaplus_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @author     Your Name <email@example.com>
 * @subpackage Ideaplus_Plugin/admin
 * @package    Ideaplus_Plugin
 */
if(!defined('ABSPATH')){ exit; }
class Ideaplus_Plugin_Index
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
     * Initialize the class and set its properties.
     *
     * @param string $ideaplus_plugin The name of this plugin.
     * @param string $version         The version of this plugin.
     *
     * @since    1.0.0
     */
    public function __construct($ideaplus_plugin, $version)
    {
        $this->ideaplus_plugin = $ideaplus_plugin;
        $this->version         = $version;
    }

    /**
     * Register the stylesheets for the admin area.
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
        wp_enqueue_style($this->ideaplus_plugin, plugin_dir_url(__FILE__) . 'css/ideaplus-plugin-admin.css', [], $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
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
        wp_enqueue_script($this->ideaplus_plugin, plugin_dir_url(__FILE__) . 'js/ideaplus-plugin-admin.js', ['jquery'], $this->version, false);
    }

    public function load_admin_menu()
    {
        add_menu_page('Ideaplus Dropshipping', 'Ideaplus', 'manage_options', $this->ideaplus_plugin, [
            $this,
            'load_page_content',
            [
                'ideaplus-plugin-admin-display',
            ],
        ]);
    }

    /**
     * @description load tempalte
     * @author      ylw <767502630@qq.com>
     *
     * @param string $page
     * @param array  $params
     */
    public function load_page_content($page = '', $params = [])
    {
        if (!empty($params)) {
            extract($params);
        }
        $path = plugin_dir_path(__FILE__) . 'partials/' . $page . '.php';
        if (file_exists($page)) {
            include $path;
        }
    }

}
