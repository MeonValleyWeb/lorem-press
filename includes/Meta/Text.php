<?php
/**
 * Text Meta Generator class.
 *
 * @package LoremPress\Meta
 */

namespace LoremPress\Meta;

/**
 * Generator for text meta values.
 */
class Text extends AbstractMeta {
    /**
     * Generate a text meta value.
     *
     * @param array<string, mixed> $config Configuration array.
     * @return string Generated text.
     */
    public function generate(array $config = []): string {
        // Get configuration with defaults
        $format = $config['format'] ?? 'words';
        $min = $config['min'] ?? 5;
        $max = $config['max'] ?? 10;
        $sentence_case = $config['sentence_case'] ?? true;
        
        // Determine quantity
        $quantity = rand(intval($min), intval($max));
        
        // Generate based on format
        switch ($format) {
            case 'letter':
                return $this->provider->letter();
                
            case 'word':
                return $this->provider->word();
                
            case 'words':
                return $this->provider->words($quantity, $sentence_case);
                
            case 'sentence':
                return $this->provider->sentence($quantity);
                
            case 'sentences':
                return implode(' ', $this->provider->sentences($quantity));
                
            case 'paragraph':
                return $this->provider->paragraph($quantity);
                
            case 'paragraphs':
                return implode("\n\n", $this->provider->paragraphs($quantity));
                
            case 'lexify':
                $pattern = $config['pattern'] ?? '????';
                return $this->provider->lexify($pattern);
                
            case 'asciify':
                $pattern = $config['pattern'] ?? '****';
                return $this->provider->asciify($pattern);
                
            case 'regexify':
                $pattern = $config['pattern'] ?? '[a-z0-9]{8}';
                return $this->provider->regexify($pattern);
                
            default:
                // Default to words if format not recognized
                return $this->provider->words($quantity, $sentence_case);
        }
    }

    /**
     * Get the meta type name.
     *
     * @return string Meta type name.
     */
    public static function get_type(): string {
        return __('Text', 'lorem-press');
    }

    /**
     * Get settings schema for this meta type.
     *
     * @return array<string, array<string, mixed>> Settings schema.
     */
    public static function get_settings_schema(): array {
        return [
            'format' => [
                'type' => 'string',
                'description' => __('Text format', 'lorem-press'),
                'default' => 'words',
                'options' => [
                    'letter' => __('Single Letter', 'lorem-press'),
                    'word' => __('Single Word', 'lorem-press'),
                    'words' => __('Multiple Words', 'lorem-press'),
                    'sentence' => __('Single Sentence', 'lorem-press'),
                    'sentences' => __('Multiple Sentences', 'lorem-press'),
                    'paragraph' => __('Single Paragraph', 'lorem-press'),
                    'paragraphs' => __('Multiple Paragraphs', 'lorem-press'),
                    'lexify' => __('Lexify Pattern', 'lorem-press'),
                    'asciify' => __('Asciify Pattern', 'lorem-press'),
                    'regexify' => __('Regexify Pattern', 'lorem-press'),
                ],
            ],
            'min' => [
                'type' => 'integer',
                'description' => __('Minimum number of elements', 'lorem-press'),
                'default' => 5,
                'min' => 1,
                'max' => 100,
            ],
            'max' => [
                'type' => 'integer',
                'description' => __('Maximum number of elements', 'lorem-press'),
                'default' => 10,
                'min' => 1,
                'max' => 100,
            ],
            'sentence_case' => [
                'type' => 'boolean',
                'description' => __('Use sentence case', 'lorem-press'),
                'default' => true,
            ],
            'pattern' => [
                'type' => 'string',
                'description' => __('Pattern for lexify/asciify/regexify', 'lorem-press'),
                'default' => '',
            ],
        ];
    }
}