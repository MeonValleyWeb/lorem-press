<?php
/**
 * Main plugin class.
 *
 * @package ModernFaker
 */

namespace ModernFaker;

use ModernFaker\Admin\AdminPage;
use ModernFaker\Generators\Post;
use ModernFaker\Generators\Comment;
use ModernFaker\Generators\User;
use ModernFaker\Generators\Term;
use ModernFaker\Generators\Meta;
use ModernFaker\API\GeneratorEndpoint;

/**
 * Main plugin class that bootstraps the plugin.
 */
class Plugin {
    /**
     * The single instance of this class.
     *
     * @var Plugin|null
     */
    private static ?Plugin $instance = null;

    /**
     * The registered generators.
     *
     * @var array<string, object>
     */
    private array $generators = [];

    /**
     * Get the plugin instance.
     *
     * @return Plugin The plugin instance.
     */
    public static function get_instance(): Plugin {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Initialize the plugin.
     *
     * @return void
     */
    public function init(): void {
        // Load textdomain
        add_action('init', [$this, 'load_textdomain']);

        // Register assets
        add_action('admin_enqueue_scripts', [$this, 'register_assets']);

        // Initialize admin area
        if (is_admin()) {
            $this->init_admin();
        }

        // Register generators
        $this->register_generators();

        // Initialize REST API endpoints
        add_action('rest_api_init', [$this, 'register_rest_endpoints']);
    }

    /**
     * Load plugin textdomain.
     *
     * @return void
     */
    public function load_textdomain(): void {
        load_plugin_textdomain(
            'lorem-press',
            false,
            dirname(plugin_basename(MODERN_FAKER_PLUGIN_FILE)) . '/languages'
        );
    }

    /**
     * Register assets.
     *
     * @param string $hook_suffix The current admin page.
     * @return void
     */
    public function register_assets(string $hook_suffix): void {
        // Only load assets on plugin pages
        if (strpos($hook_suffix, 'lorem-press') === false) {
            return;
        }

        // Register and enqueue CSS
        wp_register_style(
            'lorem-press-admin',
            MODERN_FAKER_PLUGIN_URL . 'assets/css/admin.css',
            [],
            MODERN_FAKER_VERSION
        );
        wp_enqueue_style('lorem-press-admin');

        // Register and enqueue JS
        wp_register_script(
            'lorem-press-admin',
            MODERN_FAKER_PLUGIN_URL . 'assets/js/dist/admin.js',
            ['jquery', 'wp-api', 'wp-components', 'wp-element'],
            MODERN_FAKER_VERSION,
            true
        );

        wp_localize_script('lorem-press-admin', 'ModernFakerSettings', [
            'apiRoot' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ]);

        wp_enqueue_script('lorem-press-admin');
    }

    /**
     * Initialize admin functionality.
     *
     * @return void
     */
    private function init_admin(): void {
        $admin_page = new AdminPage();
        $admin_page->init();
    }

    /**
     * Register available generators.
     *
     * @return void
     */
    private function register_generators(): void {
        // Register post generator
        $this->generators['post'] = new Post();
        
        // Register comment generator
        $this->generators['comment'] = new Comment();
        
        // Register user generator
        $this->generators['user'] = new User();
        
        // Register term generator
        $this->generators['term'] = new Term();
        
        // Register meta generator
        $this->generators['meta'] = new Meta();

        // Allow other plugins to register generators
        $this->generators = apply_filters('modern_faker_generators', $this->generators);
    }

    /**
     * Get a specific generator.
     *
     * @param string $name The generator name.
     * @return object|null The generator object or null if not found.
     */
    public function get_generator(string $name): ?object {
        return $this->generators[$name] ?? null;
    }

    /**
     * Register REST API endpoints.
     *
     * @return void
     */
    public function register_rest_endpoints(): void {
        $endpoint = new GeneratorEndpoint();
        $endpoint->register_routes();
    }

    /**
     * Plugin activation.
     *
     * @return void
     */
    public static function activate(): void {
        // Create required database tables or entries if needed
        // Set default options
        if (!get_option('modern_faker_settings')) {
            update_option('modern_faker_settings', [
                'default_provider' => 'lorem',
                'batch_size' => 10,
                'image_source' => 'placeholder',
            ]);
        }

        // Add capabilities to admin
        $admin_role = get_role('administrator');
        if ($admin_role) {
            $admin_role->add_cap('manage_modern_faker');
        }

        // Clear any cached data
        wp_cache_flush();
    }

    /**
     * Plugin deactivation.
     *
     * @return void
     */
    public static function deactivate(): void {
        // Clean up temporary data
        
        // Remove capabilities
        $admin_role = get_role('administrator');
        if ($admin_role) {
            $admin_role->remove_cap('manage_modern_faker');
        }
    }
}