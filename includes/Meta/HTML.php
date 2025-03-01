<?php
/**
 * HTML Meta Generator class.
 *
 * @package ModernFaker\Meta
 */

namespace ModernFaker\Meta;

/**
 * Generator for HTML meta values.
 */
class HTML extends AbstractMeta {
    /**
     * Generate an HTML meta value.
     *
     * @param array<string, mixed> $config Configuration array.
     * @return string Generated HTML.
     */
    public function generate(array $config = []): string {
        // Get configuration with defaults
        $elements = $config['elements'] ?? ['p', 'h2', 'h3', 'ul', 'blockquote'];
        $min_paragraphs = $config['min_paragraphs'] ?? 2;
        $max_paragraphs = $config['max_paragraphs'] ?? 5;
        $with_links = $config['with_links'] ?? true;
        $with_images = $config['with_images'] ?? false;
        $with_wrapper = $config['with_wrapper'] ?? false;
        $wrapper_element = $config['wrapper_element'] ?? 'div';
        $wrapper_class = $config['wrapper_class'] ?? 'lorem-press-content';
        
        // Determine number of paragraphs
        $paragraphs_count = rand(intval($min_paragraphs), intval($max_paragraphs));
        
        // Generate HTML content
        $html = '';
        
        for ($i = 0; $i < $paragraphs_count; $i++) {
            // Pick a random element type for this paragraph
            $element = $elements[array_rand($elements)];
            
            switch ($element) {
                case 'h1':
                case 'h2':
                case 'h3':
                case 'h4':
                case 'h5':
                case 'h6':
                    // Generate a heading with 3-8 words
                    $heading_text = $this->provider->words(rand(3, 8), true);
                    $html .= "<{$element}>{$heading_text}</{$element}>\n";
                    break;
                    
                case 'p':
                    // Generate a paragraph with 3-5 sentences
                    $paragraph = $this->provider->paragraph(rand(3, 5));
                    
                    // Add links if enabled
                    if ($with_links && rand(1, 3) === 1) {
                        $link_text = $this->provider->words(rand(2, 5), true);
                        $link_url = 'https://example.com/' . strtolower(str_replace(' ', '-', $this->provider->words(2, true)));
                        $link_html = "<a href=\"{$link_url}\">{$link_text}</a>";
                        
                        // Insert the link at a random position in the paragraph
                        $words = explode(' ', $paragraph);
                        $insert_position = rand(1, count($words) - 1);
                        array_splice($words, $insert_position, 0, [$link_html]);
                        $paragraph = implode(' ', $words);
                    }
                    
                    $html .= "<p>{$paragraph}</p>\n";
                    break;
                    
                case 'blockquote':
                    // Generate a blockquote with 1-2 paragraphs
                    $quote_paragraphs = $this->provider->paragraphs(rand(1, 2));
                    $quote_text = implode("</p>\n<p>", $quote_paragraphs);
                    $html .= "<blockquote><p>{$quote_text}</p></blockquote>\n";
                    break;
                    
                case 'ul':
                case 'ol':
                    // Generate a list with 3-6 items
                    $list_items = '';
                    $item_count = rand(3, 6);
                    
                    for ($j = 0; $j < $item_count; $j++) {
                        $item_text = $this->provider->sentence(rand(1, 3));
                        $list_items .= "<li>{$item_text}</li>\n";
                    }
                    
                    $html .= "<{$element}>\n{$list_items}</{$element}>\n";
                    break;
            }
            
            // Add an image if enabled and randomly decided
            if ($with_images && rand(1, 5) === 1) {
                $image_width = rand(300, 800);
                $image_height = rand(200, 600);
                $image_url = $this->provider->image($image_width, $image_height);
                $alt_text = $this->provider->words(rand(3, 7), true);
                
                $html .= "<figure>\n";
                $html .= "  <img src=\"{$image_url}\" alt=\"{$alt_text}\" width=\"{$image_width}\" height=\"{$image_height}\" />\n";
                $html .= "  <figcaption>{$alt_text}</figcaption>\n";
                $html .= "</figure>\n";
            }
        }
        
        // Wrap content if required
        if ($with_wrapper) {
            $html = "<{$wrapper_element} class=\"{$wrapper_class}\">\n{$html}</{$wrapper_element}>";
        }
        
        return $html;
    }

    /**
     * Get the meta type name.
     *
     * @return string Meta type name.
     */
    public static function get_type(): string {
        return __('HTML Content', 'lorem-press');
    }

    /**
     * Get settings schema for this meta type.
     *
     * @return array<string, array<string, mixed>> Settings schema.
     */
    public static function get_settings_schema(): array {
        return [
            'elements' => [
                'type' => 'array',
                'description' => __('HTML elements to include', 'lorem-press'),
                'default' => ['p', 'h2', 'h3', 'ul', 'blockquote'],
                'items' => [
                    'type' => 'string',
                    'enum' => ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'blockquote'],
                ],
            ],
            'min_paragraphs' => [
                'type' => 'integer',
                'description' => __('Minimum number of paragraphs/elements', 'lorem-press'),
                'default' => 2,
                'min' => 1,
                'max' => 50,
            ],
            'max_paragraphs' => [
                'type' => 'integer',
                'description' => __('Maximum number of paragraphs/elements', 'lorem-press'),
                'default' => 5,
                'min' => 1,
                'max' => 50,
            ],
            'with_links' => [
                'type' => 'boolean',
                'description' => __('Include random links', 'lorem-press'),
                'default' => true,
            ],
            'with_images' => [
                'type' => 'boolean',
                'description' => __('Include random images', 'lorem-press'),
                'default' => false,
            ],
            'with_wrapper' => [
                'type' => 'boolean',
                'description' => __('Wrap content in a container element', 'lorem-press'),
                'default' => false,
            ],
            'wrapper_element' => [
                'type' => 'string',
                'description' => __('Container element tag', 'lorem-press'),
                'default' => 'div',
                'enum' => ['div', 'section', 'article', 'aside', 'main'],
            ],
            'wrapper_class' => [
                'type' => 'string',
                'description' => __('CSS class for the wrapper element', 'lorem-press'),
                'default' => 'lorem-press-content',
            ],
        ];
    }
}