<?php
/**
 * Abstract Generator class.
 *
 * @package ModernFaker\Generators
 */

namespace ModernFaker\Generators;

use ModernFaker\Providers\AbstractProvider;
use ModernFaker\Providers\LoremProvider;

/**
 * Abstract Generator class that all generators should extend.
 */
abstract class AbstractGenerator {
    /**
     * Data provider instance.
     *
     * @var AbstractProvider
     */
    protected AbstractProvider $provider;

    /**
     * Default settings.
     *
     * @var array<string, mixed>
     */
    protected array $default_settings = [];

    /**
     * Current settings.
     *
     * @var array<string, mixed>
     */
    protected array $settings = [];

    /**
     * Constructor.
     *
     * @param AbstractProvider|null $provider Optional provider to use.
     */
    public function __construct(?AbstractProvider $provider = null) {
        // Set default provider if not provided
        if (null === $provider) {
            $provider = new LoremProvider();
        }
        
        $this->provider = $provider;
        $this->settings = $this->default_settings;
    }

    /**
     * Set a specific setting.
     *
     * @param string $key   Setting key.
     * @param mixed  $value Setting value.
     * @return self
     */
    public function set_setting(string $key, mixed $value): self {
        $this->settings[$key] = $value;
        return $this;
    }

    /**
     * Set multiple settings.
     *
     * @param array<string, mixed> $settings Array of settings.
     * @return self
     */
    public function set_settings(array $settings): self {
        $this->settings = array_merge($this->settings, $settings);
        return $this;
    }

    /**
     * Get a specific setting.
     *
     * @param string $key     Setting key.
     * @param mixed  $default Default value if setting not found.
     * @return mixed
     */
    public function get_setting(string $key, mixed $default = null): mixed {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Reset settings to defaults.
     *
     * @return self
     */
    public function reset_settings(): self {
        $this->settings = $this->default_settings;
        return $this;
    }

    /**
     * Set the data provider.
     *
     * @param AbstractProvider $provider The provider to use.
     * @return self
     */
    public function set_provider(AbstractProvider $provider): self {
        $this->provider = $provider;
        return $this;
    }

    /**
     * Get the data provider.
     *
     * @return AbstractProvider
     */
    public function get_provider(): AbstractProvider {
        return $this->provider;
    }

    /**
     * Generate a batch of items.
     *
     * @param int   $count    Number of items to generate.
     * @param array $settings Optional settings to override defaults.
     * @return array<int, mixed> Generated items.
     */
    public function generate_batch(int $count, array $settings = []): array {
        if (!empty($settings)) {
            $this->set_settings($settings);
        }

        $results = [];
        for ($i = 0; $i < $count; $i++) {
            $results[] = $this->generate();
        }

        return $results;
    }

    /**
     * Generate a single item.
     *
     * @return mixed Generated item.
     */
    abstract public function generate(): mixed;

    /**
     * Get available settings with descriptions and defaults.
     *
     * @return array<string, array<string, mixed>> Settings definitions.
     */
    abstract public function get_settings_schema(): array;
}