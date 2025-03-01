<?php
/**
 * Meta Generator class.
 *
 * @package LoremPress\Generators
 */

namespace LoremPress\Generators;

use LoremPress\Meta\AbstractMeta;
use LoremPress\Meta\Number;
use LoremPress\Meta\Text;
use LoremPress\Meta\HTML;
use LoremPress\Meta\Person;
use LoremPress\Meta\Geo;
use LoremPress\Meta\Date;
use LoremPress\Meta\Company;
use LoremPress\Meta\Email;
use LoremPress\Meta\Domain;
use LoremPress\Meta\Image;
use LoremPress\Meta\Attachment;
use WP_Error;

/**
 * Generator for WordPress meta fields.
 */
class Meta extends AbstractGenerator {
    /**
     * Registered meta field type handlers.
     *
     * @var array<string, AbstractMeta>
     */
    protected array $meta_handlers = [];

    /**
     * Default settings.
     *
     * @var array<string, mixed>
     */
    protected array $default_settings = [
        'object_type' => 'post',  // post, comment, term, user
        'object_id' => 0,         // ID of the object to add meta to
        'meta_key' => '',         // Meta key (generated if empty)
        'meta_type' => 'text',    // Type of meta to generate
        'meta_config' => [],      // Configuration for meta generator
    ];

    /**
     * Constructor.
     *
     * @param AbstractProvider|null $provider Optional provider to use.
     */
    public function __construct(?AbstractProvider $provider = null) {
        parent::__construct($provider);
        $this->register_meta_handlers();
    }

    /**
     * Register available meta handlers.
     *
     * @return void
     */
    protected function register_meta_handlers(): void {
        // Register built-in meta handlers
        $this->meta_handlers = [
            'number' => new Number($this->provider),
            'text' => new Text($this->provider),
            'html' => new HTML($this->provider),
            'person' => new Person($this->provider),
            'geo' => new Geo($this->provider),
            'date' => new Date($this->provider),
            'company' => new Company($this->provider),
            'email' => new Email($this->provider),
            'domain' => new Domain($this->provider),
            'image' => new Image($this->provider),
            'attachment' => new Attachment($this->provider),
        ];

        // Allow plugins to register their own meta handlers
        $this->meta_handlers = apply_filters('lorem_press_meta_handlers', $this->meta_handlers);
    }

    /**
     * Generate a single meta field and add it to an object.
     *
     * @return string|int|WP_Error Generated meta key, meta ID, or WP_Error on failure.
     */
    public function generate(): string|int|WP_Error {
        // Get required settings
        $object_type = $this->get_setting('object_type');
        $object_id = $this->get_setting('object_id');
        
        if (empty($object_id)) {
            return new WP_Error(
                'missing_object_id',
                __('Object ID is required to generate meta.', 'lorem-press')
            );
        }

        // Get or generate meta key
        $meta_key = $this->get_setting('meta_key');
        if (empty($meta_key)) {
            $meta_key = 'press_' . uniqid();
        }

        // Get meta type and config
        $meta_type = $this->get_setting('meta_type');
        $meta_config = $this->get_setting('meta_config', []);

        // Generate meta value
        $meta_value = $this->generate_value([
            'type' => $meta_type,
            ...$meta_config,
        ]);

        // Add meta to object
        $result = false;
        switch ($object_type) {
            case 'post':
                $result = update_post_meta($object_id, $meta_key, $meta_value);
                break;
            case 'comment':
                $result = update_comment_meta($object_id, $meta_key, $meta_value);
                break;
            case 'term':
                $result = update_term_meta($object_id, $meta_key, $meta_value);
                break;
            case 'user':
                $result = update_user_meta($object_id, $meta_key, $meta_value);
                break;
        }

        if (false === $result) {
            return new WP_Error(
                'meta_creation_failed',
                __('Failed to create meta field.', 'lorem-press')
            );
        }

        return $meta_key;
    }

    /**
     * Generate a meta value based on type and configuration.
     *
     * @param array<string, mixed> $config Meta configuration.
     * @return mixed Generated meta value.
     */
    public function generate_value(array $config): mixed {
        // Get meta type
        $type = $config['type'] ?? 'text';
        
        // Get handler for this type
        $handler = $this->meta_handlers[$type] ?? null;
        
        if (null === $handler) {
            // Fallback to text if handler not found
            $handler = $this->meta_handlers['text'];
        }
        
        // Generate value
        return $handler->generate($config);
    }

    /**
     * Get available settings with descriptions and defaults.
     *
     * @return array<string, array<string, mixed>> Settings definitions.
     */
    public function get_settings_schema(): array {
        return [
            'object_type' => [
                'type' => 'string',
                'description' => __('Type of object to add meta to', 'lorem-press'),
                'default' => 'post',
                'options' => [
                    'post' => __('Post', 'lorem-press'),
                    'comment' => __('Comment', 'lorem-press'),
                    'term' => __('Term', 'lorem-press'),
                    'user' => __('User', 'lorem-press'),
                ],
            ],
            'object_id' => [
                'type' => 'integer',
                'description' => __('ID of the object to add meta to', 'lorem-press'),
                'default' => 0,
            ],
            'meta_key' => [
                'type' => 'string',
                'description' => __('Meta key (generated if empty)', 'lorem-press'),
                'default' => '',
            ],
            'meta_type' => [
                'type' => 'string',
                'description' => __('Type of meta to generate', 'lorem-press'),
                'default' => 'text',
                'options' => $this->get_meta_type_options(),
            ],
            'meta_config' => [
                'type' => 'object',
                'description' => __('Configuration for meta generator', 'lorem-press'),
                'default' => [],
            ],
        ];
    }

    /**
     * Get available meta type options.
     *
     * @return array<string, string> Meta type options.
     */
    protected function get_meta_type_options(): array {
        $options = [];
        
        foreach ($this->meta_handlers as $type => $handler) {
            $options[$type] = $handler::get_type();
        }
        
        return $options;
    }

    /**
     * Get the settings schema for a specific meta type.
     *
     * @param string $type Meta type.
     * @return array<string, array<string, mixed>> Settings schema.
     */
    public function get_meta_type_schema(string $type): array {
        $handler = $this->meta_handlers[$type] ?? null;
        
        if (null === $handler) {
            return [];
        }
        
        return $handler::get_settings_schema();
    }
}