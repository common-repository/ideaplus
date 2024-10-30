<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.ideaplus.com/
 * @since             1.0.0
 * @package           Ideaplus_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Ideaplus
 * Description:       Provide customized jewelry dropshipping, including jewelry custom, storage management, package, transportation, and other services.
 * Version:           1.0.5
 * Author:            Ideaplus
 * Author URI:        http://www.ideaplus.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ideaplus-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('IDEAPLUS_PLUGIN_VERSION', '1.0.0');
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ideaplus-plugin-activator.php
 */
function activate_ideaplus_plugin()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-ideaplus-plugin-activator.php';
    Ideaplus_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ideaplus-plugin-deactivator.php
 */
function deactivate_ideaplus_plugin()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-ideaplus-plugin-deactivator.php';
    Ideaplus_Plugin_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_ideaplus_plugin');
register_deactivation_hook(__FILE__, 'deactivate_ideaplus_plugin');
/**
 * The code that make plugin config
 */
require plugin_dir_path(__FILE__) . 'env.php';
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-ideaplus-plugin.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
$plugin = new Ideaplus_Plugin();
$plugin->run();
