<?php
/**
 * Modern Faker
 *
 * @package           ModernFaker
 * @author            Your Name
 * @copyright         2025 Your Name or Company
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Modern Faker
 * Plugin URI:        https://example.com/plugin/lorem-press/
 * Description:       A modern content generator for WordPress with support for posts, users, terms, comments and meta fields.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      8.0
 * Author:            Your Name
 * Author URI:        https://example.com/
 * Text Domain:       lorem-press
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://example.com/plugin/lorem-press/
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('MODERN_FAKER_VERSION', '1.0.0');
define('MODERN_FAKER_PLUGIN_FILE', __FILE__);
define('MODERN_FAKER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MODERN_FAKER_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoloader
require_once MODERN_FAKER_PLUGIN_DIR . 'vendor/autoload.php';

// Initialize the plugin
function modern_faker_init(): void 
{
    // Initialize the main plugin class
    $plugin = new \ModernFaker\Plugin();
    $plugin->init();
}
add_action('plugins_loaded', 'modern_faker_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    // Minimum PHP version check
    if (version_compare(PHP_VERSION, '8.0.0', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            esc_html__('Modern Faker requires PHP 8.0 or higher.', 'lorem-press'),
            'Plugin Activation Error',
            ['back_link' => true]
        );
    }

    // Activate the plugin
    \ModernFaker\Plugin::activate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    \ModernFaker\Plugin::deactivate();
});

// Uninstall hook - use uninstall.php for more complex operations