<?php
/**
 * Abstract Provider class.
 *
 * @package ModernFaker\Providers
 */

namespace ModernFaker\Providers;

/**
 * Abstract Provider class that all providers should extend.
 */
abstract class AbstractProvider {
    /**
     * Generate a random letter.
     *
     * @return string A single letter.
     */
    abstract public function letter(): string;
    
    /**
     * Generate a single random word.
     *
     * @return string A random word.
     */
    abstract public function word(): string;
    
    /**
     * Generate multiple random words.
     *
     * @param int  $count         Number of words to generate.
     * @param bool $sentence_case Whether to capitalize the first word.
     * @return string Random words separated by spaces.
     */
    abstract public function words(int $count, bool $sentence_case = false): string;
    
    /**
     * Generate a random sentence.
     *
     * @param int $word_count Optional number of words.
     * @return string A random sentence.
     */
    abstract public function sentence(int $word_count = 0): string;
    
    /**
     * Generate multiple random sentences.
     *
     * @param int $count Number of sentences to generate.
     * @return array<int, string> Array of random sentences.
     */
    abstract public function sentences(int $count): array;
    
    /**
     * Generate a random paragraph.
     *
     * @param int $sentence_count Optional number of sentences.
     * @return string A random paragraph.
     */
    abstract public function paragraph(int $sentence_count = 0): string;
    
    /**
     * Generate multiple random paragraphs.
     *
     * @param int $count Number of paragraphs to generate.
     * @return array<int, string> Array of random paragraphs.
     */
    abstract public function paragraphs(int $count): array;
    
    /**
     * Replace all ? characters with random letters.
     *
     * @param string $pattern String with ?'s to replace with letters.
     * @return string Resulting string.
     */
    abstract public function lexify(string $pattern): string;
    
    /**
     * Replace all * characters with random numbers.
     *
     * @param string $pattern String with *'s to replace with numbers.
     * @return string Resulting string.
     */
    abstract public function asciify(string $pattern): string;
    
    /**
     * Generate a string matching a regular expression pattern.
     *
     * @param string $pattern Regular expression pattern.
     * @return string Resulting string.
     */
    abstract public function regexify(string $pattern): string;
    
    /**
     * Generate a random image URL.
     *
     * @param int         $width    Image width in pixels.
     * @param int         $height   Image height in pixels.
     * @param string|null $keyword  Optional keyword for image.
     * @param bool        $gray     Whether to generate a grayscale image.
     * @param string      $provider Image provider to use (placeholder, picsum, etc.).
     * @return string Image URL.
     */
    abstract public function image(int $width = 640, int $height = 480, ?string $keyword = null, bool $gray = false, string $provider = 'placeholder'): string;
    
    /**
     * Generate a random person name.
     *
     * @param string|null $gender Optional gender (male, female).
     * @return string Full name.
     */
    abstract public function personName(?string $gender = null): string;
    
    /**
     * Generate a random first name.
     *
     * @param string|null $gender Optional gender (male, female).
     * @return string First name.
     */
    abstract public function firstName(?string $gender = null): string;
    
    /**
     * Generate a random last name.
     *
     * @return string Last name.
     */
    abstract public function lastName(): string;
    
    /**
     * Generate a random email address.
     *
     * @param string|null $name Optional name to use in the email.
     * @return string Email address.
     */
    abstract public function email(?string $name = null): string;
    
    /**
     * Generate a random domain name.
     *
     * @return string Domain name.
     */
    abstract public function domainName(): string;
    
    /**
     * Generate a random URL.
     *
     * @return string URL.
     */
    abstract public function url(): string;
    
    /**
     * Generate a random company name.
     *
     * @return string Company name.
     */
    abstract public function company(): string;
    
    /**
     * Generate a random date.
     *
     * @param string $format   Date format (using PHP date format).
     * @param string $min_date Minimum date in strtotime format.
     * @param string $max_date Maximum date in strtotime format.
     * @return string Formatted date.
     */
    abstract public function date(string $format = 'Y-m-d', string $min_date = '-30 years', string $max_date = 'now'): string;
    
    /**
     * Generate a random time.
     *
     * @param string $format Time format (using PHP date format).
     * @return string Formatted time.
     */
    abstract public function time(string $format = 'H:i:s'): string;
    
    /**
     * Generate a random timezone.
     *
     * @return string Timezone.
     */
    abstract public function timezone(): string;
    
    /**
     * Generate random latitude.
     *
     * @param float $min Minimum value.
     * @param float $max Maximum value.
     * @return float Latitude.
     */
    abstract public function latitude(float $min = -90, float $max = 90): float;
    
    /**
     * Generate random longitude.
     *
     * @param float $min Minimum value.
     * @param float $max Maximum value.
     * @return float Longitude.
     */
    abstract public function longitude(float $min = -180, float $max = 180): float;
}