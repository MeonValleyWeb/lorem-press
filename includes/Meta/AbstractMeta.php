<?php
/**
 * Abstract Meta class.
 *
 * @package LoremPress\Meta
 */

namespace LoremPress\Meta;

use LoremPress\Providers\AbstractProvider;
use LoremPress\Providers\LoremProvider;

/**
 * Abstract Meta class that all meta field generators should extend.
 */
abstract class AbstractMeta {
    /**
     * Data provider instance.
     *
     * @var AbstractProvider
     */
    protected AbstractProvider $provider;

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
     * Generate a meta value.
     *
     * @param array<string, mixed> $config Configuration array.
     * @return mixed Generated value.
     */
    abstract public function generate(array $config = []): mixed;

    /**
     * Get the meta type name.
     *
     * @return string Meta type name.
     */
    abstract public static function get_type(): string;

    /**
     * Get settings schema for this meta type.
     *
     * @return array<string, array<string, mixed>> Settings schema.
     */
    abstract public static function get_settings_schema(): array;
}