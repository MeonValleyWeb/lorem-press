<?php
/**
 * Lorem Press
 *
 * @package           LoremPress
 * @author            Andrew Wilkinson
 * @copyright         2025 Meon Valley Web
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       LoremPress
 * Plugin URI:        https://meonvalleyweb.com/plugins/lorem-press/
 * Description:       A modern content generator for WordPress with support for posts, users, terms, comments and meta fields.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      8.0
 * Author:            Your Name
 * Author URI:        https://meonvalleyweb.com/
 * Text Domain:       lorem-press
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Update URI:        https://meonvalleyweb.com/plugins/lorem-press/
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('LOREM_PRESS_VERSION', '1.0.0');
define('LOREM_PRESS_PLUGIN_FILE', __FILE__);
define('LOREM_PRESS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LOREM_PRESS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoloader
require_once LOREM_PRESS_PLUGIN_DIR . 'vendor/autoload.php';

// Initialize the plugin
function lorem_press_init(): void 
{
    // Initialize the main plugin class
    $plugin = new \LoremPress\Plugin();
    $plugin->init();
}
add_action('plugins_loaded', 'lorem_press_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    // Minimum PHP version check
    if (version_compare(PHP_VERSION, '8.0.0', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            esc_html__('Lorem Press requires PHP 8.0 or higher.', 'lorem-press'),
            'Plugin Activation Error',
            ['back_link' => true]
        );
    }

    // Activate the plugin
    \LoremPress\Plugin::activate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    \LoremPress\Plugin::deactivate();
});

// Uninstall hook - use uninstall.php for more complex operations