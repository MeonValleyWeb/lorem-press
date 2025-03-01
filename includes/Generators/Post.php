<?php
/**
 * Post Generator class.
 *
 * @package ModernFaker\Generators
 */

namespace ModernFaker\Generators;

use ModernFaker\Meta\AbstractMeta;
use ModernFaker\Utils\Sanitize;
use WP_Error;

/**
 * Generator for WordPress posts and custom post types.
 */
class Post extends AbstractGenerator {
    /**
     * Default settings.
     *
     * @var array<string, mixed>
     */
    protected array $default_settings = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'comment_status' => 'open',
        'ping_status' => 'open',
        'post_date_min' => '-1 month',
        'post_date_max' => 'now',
        'with_featured_image' => true,
        'featured_image_keyword' => '',
        'title_min_words' => 4,
        'title_max_words' => 8,
        'content_min_paragraphs' => 3,
        'content_max_paragraphs' => 7,
        'with_meta' => false,
        'meta_fields' => [],
        'with_terms' => false,
        'taxonomies' => [],
        'with_comments' => false,
        'comments_min' => 0,
        'comments_max' => 5,
        'author_type' => 'existing',  // existing, create
    ];

    /**
     * Generate a single post.
     *
     * @return int|WP_Error Post ID or WP_Error on failure.
     */
    public function generate(): int|WP_Error {
        // Generate post title
        $title_length = rand(
            $this->get_setting('title_min_words'),
            $this->get_setting('title_max_words')
        );
        $title = $this->provider->words($title_length, true);

        // Generate post content
        $paragraphs = rand(
            $this->get_setting('content_min_paragraphs'),
            $this->get_setting('content_max_paragraphs')
        );
        $content = $this->provider->paragraphs($paragraphs);
        $content = implode("\n\n", $content);

        // Generate post date
        $date_min = strtotime($this->get_setting('post_date_min'));
        $date_max = strtotime($this->get_setting('post_date_max'));
        $post_date = date('Y-m-d H:i:s', rand($date_min, $date_max));

        // Prepare post data
        $post_data = [
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => $this->get_setting('post_status'),
            'post_type' => $this->get_setting('post_type'),
            'comment_status' => $this->get_setting('comment_status'),
            'ping_status' => $this->get_setting('ping_status'),
            'post_date' => $post_date,
            'post_date_gmt' => get_gmt_from_date($post_date),
        ];

        // Handle author
        if ($this->get_setting('author_type') === 'existing') {
            // Get random existing author
            $authors = get_users(['role__in' => ['administrator', 'editor', 'author']]);
            if (!empty($authors)) {
                $random_author = $authors[array_rand($authors)];
                $post_data['post_author'] = $random_author->ID;
            }
        } else {
            // Create a new user and set as author
            $user_generator = new User();
            $user_id = $user_generator->generate();
            
            if (!is_wp_error($user_id)) {
                $post_data['post_author'] = $user_id;
            }
        }

        // Apply filters to post data
        $post_data = apply_filters('modern_faker_post_data', $post_data, $this->settings);
        
        // Insert the post
        $post_id = wp_insert_post($post_data, true);
        
        if (is_wp_error($post_id)) {
            return $post_id;
        }

        // Handle featured image
        if ($this->get_setting('with_featured_image')) {
            $this->add_featured_image($post_id);
        }

        // Handle taxonomies and terms
        if ($this->get_setting('with_terms')) {
            $this->add_terms($post_id);
        }

        // Handle meta fields
        if ($this->get_setting('with_meta')) {
            $this->add_meta_fields($post_id);
        }

        // Handle comments
        if ($this->get_setting('with_comments')) {
            $this->add_comments($post_id);
        }

        // Return the post ID
        return $post_id;
    }

    /**
     * Add a featured image to a post.
     *
     * @param int $post_id Post ID.
     * @return int|bool Attachment ID or false on failure.
     */
    protected function add_featured_image(int $post_id): int|bool {
        // Get keyword for image if provided
        $keyword = $this->get_setting('featured_image_keyword', '');

        // Get a random image URL or use a placeholder service
        $image_url = $this->provider->image(
            640, 
            480, 
            $keyword ?: null, 
            true, 
            'placeholder'
        );

        if (empty($image_url)) {
            return false;
        }

        // Download and add the image to the media library
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($image_url);
        
        if (!$image_data) {
            return false;
        }

        $filename = wp_unique_filename($upload_dir['path'], 'faker-' . uniqid() . '.jpg');
        $file_path = $upload_dir['path'] . '/' . $filename;
        file_put_contents($file_path, $image_data);

        $attachment = [
            'post_mime_type' => 'image/jpeg',
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        ];

        $attachment_id = wp_insert_attachment($attachment, $file_path, $post_id);

        if (!is_wp_error($attachment_id)) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $file_path);
            wp_update_attachment_metadata($attachment_id, $attachment_data);
            set_post_thumbnail($post_id, $attachment_id);
            return $attachment_id;
        }

        return false;
    }

    /**
     * Add taxonomy terms to a post.
     *
     * @param int $post_id Post ID.
     * @return void
     */
    protected function add_terms(int $post_id): void {
        $taxonomies = $this->get_setting('taxonomies', []);
        
        // If no specific taxonomies provided, get all taxonomies for this post type
        if (empty($taxonomies)) {
            $post_type = $this->get_setting('post_type');
            $taxonomies = get_object_taxonomies($post_type);
        }

        foreach ($taxonomies as $taxonomy) {
            // Get existing terms or create new ones
            $terms = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'number' => 10,
            ]);

            if (empty($terms) || is_wp_error($terms)) {
                // Create some new terms if none exist
                $term_generator = new Term();
                $term_generator->set_settings([
                    'taxonomy' => $taxonomy,
                    'count' => rand(1, 3),
                ]);
                $term_ids = $term_generator->generate_batch(rand(1, 3));
                
                // Filter out any errors
                $term_ids = array_filter($term_ids, function($term_id) {
                    return !is_wp_error($term_id);
                });
                
                if (!empty($term_ids)) {
                    wp_set_object_terms($post_id, $term_ids, $taxonomy);
                }
            } else {
                // Use existing terms
                $selected_terms = array_rand(array_flip(wp_list_pluck($terms, 'term_id')), rand(1, min(3, count($terms))));
                if (!is_array($selected_terms)) {
                    $selected_terms = [$selected_terms];
                }
                wp_set_object_terms($post_id, $selected_terms, $taxonomy);
            }
        }
    }

    /**
     * Add meta fields to a post.
     *
     * @param int $post_id Post ID.
     * @return void
     */
    protected function add_meta_fields(int $post_id): void {
        $meta_fields = $this->get_setting('meta_fields', []);
        
        // If no specific meta fields provided, use some defaults
        if (empty($meta_fields)) {
            $meta_fields = [
                'fake_number' => ['type' => 'number', 'min' => 1, 'max' => 100],
                'fake_text' => ['type' => 'text', 'min_words' => 5, 'max_words' => 20],
                'fake_html' => ['type' => 'html', 'min_paragraphs' => 1, 'max_paragraphs' => 3],
                'fake_date' => ['type' => 'date', 'min' => '-1 year', 'max' => '+1 year'],
                'fake_boolean' => ['type' => 'boolean'],
            ];
        }

        $meta_generator = new Meta();
        
        foreach ($meta_fields as $meta_key => $meta_config) {
            $meta_value = $meta_generator->generate_value($meta_config);
            update_post_meta($post_id, $meta_key, $meta_value);
        }
    }

    /**
     * Add comments to a post.
     *
     * @param int $post_id Post ID.
     * @return array<int, int|WP_Error> Array of comment IDs or WP_Error objects.
     */
    protected function add_comments(int $post_id): array {
        $comment_count = rand(
            $this->get_setting('comments_min', 0),
            $this->get_setting('comments_max', 5)
        );

        if ($comment_count <= 0) {
            return [];
        }

        $comment_generator = new Comment();
        $comment_generator->set_settings([
            'post_id' => $post_id,
        ]);

        return $comment_generator->generate_batch($comment_count);
    }

    /**
     * Get available settings with descriptions and defaults.
     *
     * @return array<string, array<string, mixed>> Settings definitions.
     */
    public function get_settings_schema(): array {
        return [
            'post_type' => [
                'type' => 'string',
                'description' => __('Post type to generate', 'lorem-press'),
                'default' => 'post',
                'options' => $this->get_available_post_types(),
            ],
            'post_status' => [
                'type' => 'string',
                'description' => __('Post status', 'lorem-press'),
                'default' => 'publish',
                'options' => get_post_stati(['internal' => false], 'objects'),
            ],
            'comment_status' => [
                'type' => 'string',
                'description' => __('Comment status', 'lorem-press'),
                'default' => 'open',
                'options' => [
                    'open' => __('Open', 'lorem-press'),
                    'closed' => __('Closed', 'lorem-press'),
                ],
            ],
            'ping_status' => [
                'type' => 'string',
                'description' => __('Ping status', 'lorem-press'),
                'default' => 'open',
                'options' => [
                    'open' => __('Open', 'lorem-press'),
                    'closed' => __('Closed', 'lorem-press'),
                ],
            ],
            'post_date_min' => [
                'type' => 'string',
                'description' => __('Minimum post date (strtotime compatible string)', 'lorem-press'),
                'default' => '-1 month',
            ],
            'post_date_max' => [
                'type' => 'string',
                'description' => __('Maximum post date (strtotime compatible string)', 'lorem-press'),
                'default' => 'now',
            ],
            'with_featured_image' => [
                'type' => 'boolean',
                'description' => __('Generate featured image', 'lorem-press'),
                'default' => true,
            ],
            'featured_image_keyword' => [
                'type' => 'string',
                'description' => __('Keyword for featured image search', 'lorem-press'),
                'default' => '',
            ],
            'title_min_words' => [
                'type' => 'integer',
                'description' => __('Minimum words in post title', 'lorem-press'),
                'default' => 4,
                'min' => 1,
                'max' => 20,
            ],
            'title_max_words' => [
                'type' => 'integer',
                'description' => __('Maximum words in post title', 'lorem-press'),
                'default' => 8,
                'min' => 1,
                'max' => 20,
            ],
            'content_min_paragraphs' => [
                'type' => 'integer',
                'description' => __('Minimum paragraphs in post content', 'lorem-press'),
                'default' => 3,
                'min' => 1,
                'max' => 50,
            ],
            'content_max_paragraphs' => [
                'type' => 'integer',
                'description' => __('Maximum paragraphs in post content', 'lorem-press'),
                'default' => 7,
                'min' => 1,
                'max' => 50,
            ],
            'with_meta' => [
                'type' => 'boolean',
                'description' => __('Generate meta fields', 'lorem-press'),
                'default' => false,
            ],
            'meta_fields' => [
                'type' => 'object',
                'description' => __('Meta fields configuration', 'lorem-press'),
                'default' => [],
            ],
            'with_terms' => [
                'type' => 'boolean',
                'description' => __('Generate and assign taxonomy terms', 'lorem-press'),
                'default' => false,
            ],
            'taxonomies' => [
                'type' => 'array',
                'description' => __('Taxonomies to use (empty for all available)', 'lorem-press'),
                'default' => [],
                'items' => [
                    'type' => 'string',
                ],
            ],
            'with_comments' => [
                'type' => 'boolean',
                'description' => __('Generate comments', 'lorem-press'),
                'default' => false,
            ],
            'comments_min' => [
                'type' => 'integer',
                'description' => __('Minimum number of comments', 'lorem-press'),
                'default' => 0,
                'min' => 0,
                'max' => 100,
            ],
            'comments_max' => [
                'type' => 'integer',
                'description' => __('Maximum number of comments', 'lorem-press'),
                'default' => 5,
                'min' => 0,
                'max' => 100,
            ],
            'author_type' => [
                'type' => 'string',
                'description' => __('Author selection method', 'lorem-press'),
                'default' => 'existing',
                'options' => [
                    'existing' => __('Use existing authors', 'lorem-press'),
                    'create' => __('Create new authors', 'lorem-press'),
                ],
            ],
        ];
    }

    /**
     * Get all available post types.
     *
     * @return array<string, string> Post types.
     */
    private function get_available_post_types(): array {
        $post_types = get_post_types(['public' => true], 'objects');
        $options = [];

        foreach ($post_types as $post_type) {
            $options[$post_type->name] = $post_type->labels->singular_name;
        }

        return $options;
    }
}