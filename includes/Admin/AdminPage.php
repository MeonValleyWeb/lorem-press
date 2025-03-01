<?php
/**
 * Admin Page class.
 *
 * @package LoremPress\Admin
 */

namespace LoremPress\Admin;

use LoremPress\Plugin;

/**
 * Class to handle admin UI and functionality.
 */
class AdminPage {
    /**
     * The plugin's page hook suffix.
     *
     * @var string
     */
    private string $page_hook;

    /**
     * Initialize the admin functionality.
     *
     * @return void
     */
    public function init(): void {
        // Add admin menu
        add_action('admin_menu', [$this, 'add_admin_menu']);
        
        // Register AJAX handlers
        add_action('wp_ajax_lorem_press_generate', [$this, 'handle_generate_ajax']);
    }

    /**
     * Add admin menu items.
     *
     * @return void
     */
    public function add_admin_menu(): void {
        // Add top-level menu
        $this->page_hook = add_menu_page(
            __('LoremPress', 'lorem-press'),
            __('LoremPress', 'lorem-press'),
            'manage_lorem_press',
            'lorem-press',
            [$this, 'render_main_page'],
            'dashicons-database-add',
            30
        );

        // Add submenus
        add_submenu_page(
            'lorem-press',
            __('Dashboard', 'lorem-press'),
            __('Dashboard', 'lorem-press'),
            'manage_lorem_press',
            'lorem-press',
            [$this, 'render_main_page']
        );

        add_submenu_page(
            'lorem-press',
            __('Generate Posts', 'lorem-press'),
            __('Posts', 'lorem-press'),
            'manage_lorem_press',
            'lorem-press-posts',
            [$this, 'render_posts_page']
        );

        add_submenu_page(
            'lorem-press',
            __('Generate Users', 'lorem-press'),
            __('Users', 'lorem-press'),
            'manage_lorem_press',
            'lorem-press-users',
            [$this, 'render_users_page']
        );

        add_submenu_page(
            'lorem-press',
            __('Generate Terms', 'lorem-press'),
            __('Terms', 'lorem-press'), 
            'manage_lorem_press',
            'lorem-press-terms',
            [$this, 'render_terms_page']
        );

        add_submenu_page(
            'lorem-press',
            __('Generate Comments', 'lorem-press'),
            __('Comments', 'lorem-press'),
            'manage_lorem_press',
            'lorem-press-comments',
            [$this, 'render_comments_page']
        );

        add_submenu_page(
            'lorem-press',
            __('Settings', 'lorem-press'),
            __('Settings', 'lorem-press'),
            'manage_lorem_press',
            'lorem-press-settings',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Render the main dashboard page.
     *
     * @return void
     */
    public function render_main_page(): void {
        include LOREM_PRESS_PLUGIN_DIR . 'templates/admin/dashboard.php';
    }

    /**
     * Render the posts generator page.
     *
     * @return void
     */
    public function render_posts_page(): void {
        $post_generator = Plugin::get_instance()->get_generator('post');
        $settings_schema = $post_generator->get_settings_schema();
        
        include LOREM_PRESS_PLUGIN_DIR . 'templates/admin/generators/post.php';
    }

    /**
     * Render the users generator page.
     *
     * @return void
     */
    public function render_users_page(): void {
        $user_generator = Plugin::get_instance()->get_generator('user');
        $settings_schema = $user_generator->get_settings_schema();
        
        include LOREM_PRESS_PLUGIN_DIR . 'templates/admin/generators/user.php';
    }

    /**
     * Render the terms generator page.
     *
     * @return void
     */
    public function render_terms_page(): void {
        $term_generator = Plugin::get_instance()->get_generator('term');
        $settings_schema = $term_generator->get_settings_schema();
        
        include LOREM_PRESS_PLUGIN_DIR . 'templates/admin/generators/term.php';
    }

    /**
     * Render the comments generator page.
     *
     * @return void
     */
    public function render_comments_page(): void {
        $comment_generator = Plugin::get_instance()->get_generator('comment');
        $settings_schema = $comment_generator->get_settings_schema();
        
        include LOREM_PRESS_PLUGIN_DIR . 'templates/admin/generators/comment.php';
    }

    /**
     * Render the settings page.
     *
     * @return void
     */
    public function render_settings_page(): void {
        $settings = get_option('lorem_press_settings', []);
        
        include LOREM_PRESS_PLUGIN_DIR . 'templates/admin/settings.php';
    }

    /**
     * Handle AJAX request to generate content.
     *
     * @return void
     */
    public function handle_generate_ajax(): void {
        // Check nonce for security
        check_ajax_referer('lorem_press_nonce', 'nonce');
        
        // Check permissions
        if (!current_user_can('manage_lorem_press')) {
            wp_send_json_error([
                'message' => __('You do not have permission to perform this action.', 'lorem-press'),
            ]);
            exit;
        }
        
        // Get request data
        $generator_type = sanitize_text_field($_POST['generator_type'] ?? '');
        $count = intval($_POST['count'] ?? 1);
        $settings = isset($_POST['settings']) ? json_decode(stripslashes($_POST['settings']), true) : [];
        
        // Validate count
        if ($count < 1) {
            $count = 1;
        } elseif ($count > 100) {
            $count = 100;
        }
        
        // Get the appropriate generator
        $generator = Plugin::get_instance()->get_generator($generator_type);
        
        if (!$generator) {
            wp_send_json_error([
                'message' => __('Invalid generator type.', 'lorem-press'),
            ]);
            exit;
        }
        
        // Apply settings to generator
        $generator->set_settings($settings);
        
        // Generate items
        $results = [];
        $errors = [];
        
        try {
            // Generate in batches to avoid timeouts for large requests
            $batch_size = 10;
            $processed = 0;
            
            while ($processed < $count) {
                $batch_count = min($batch_size, $count - $processed);
                $batch_results = $generator->generate_batch($batch_count);
                
                foreach ($batch_results as $result) {
                    if (is_wp_error($result)) {
                        $errors[] = $result->get_error_message();
                    } else {
                        $results[] = $result;
                    }
                }
                
                $processed += $batch_count;
            }
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage(),
            ]);
            exit;
        }
        
        // Send response
        wp_send_json_success([
            'count' => count($results),
            'results' => $results,
            'errors' => $errors,
        ]);
    }
}