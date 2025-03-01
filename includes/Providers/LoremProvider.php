<?php
/**
 * Lorem Provider class.
 *
 * @package ModernFaker\Providers
 */

namespace ModernFaker\Providers;

/**
 * Provider for generating Lorem Ipsum text and accessing lipsum.com API.
 */
class LoremProvider extends AbstractProvider {
    /**
     * Cache for API responses.
     *
     * @var array<string, mixed>
     */
    private array $cache = [];

    /**
     * API endpoint URL.
     *
     * @var string
     */
    private string $api_url = 'https://www.lipsum.com/feed/json';

    /**
     * Local word list for fallback when API is unavailable.
     *
     * @var array<int, string>
     */
    private array $word_list = [
        'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit',
        'sed', 'do', 'eiusmod', 'tempor', 'incididunt', 'ut', 'labore', 'et', 'dolore',
        'magna', 'aliqua', 'enim', 'ad', 'minim', 'veniam', 'quis', 'nostrud', 'exercitation',
        'ullamco', 'laboris', 'nisi', 'aliquip', 'ex', 'ea', 'commodo', 'consequat', 'duis',
        'aute', 'irure', 'in', 'reprehenderit', 'voluptate', 'velit', 'esse', 'cillum',
        'eu', 'fugiat', 'nulla', 'pariatur', 'excepteur', 'sint', 'occaecat', 'cupidatat',
        'non', 'proident', 'sunt', 'culpa', 'qui', 'officia', 'deserunt', 'mollit', 'anim',
        'id', 'est', 'laborum', 'at', 'vero', 'eos', 'accusamus', 'iusto', 'odio', 'dignissimos',
        'ducimus', 'blanditiis', 'praesentium', 'voluptatum', 'deleniti', 'atque', 'corrupti',
        'quos', 'dolores', 'similique', 'sunt', 'in', 'culpa', 'officia', 'deserunt', 'mollitia',
        'animi', 'perspiciatis', 'unde', 'omnis', 'iste', 'natus', 'error', 'sit', 'voluptatem',
        'accusantium', 'doloremque', 'laudantium', 'totam', 'rem', 'aperiam', 'eaque', 'ipsa',
        'quae', 'ab', 'illo', 'inventore', 'veritatis', 'quasi', 'architecto', 'beatae', 'vitae',
        'dicta', 'explicabo', 'aspernatur', 'aut', 'odit', 'fugit', 'sed', 'consequuntur',
        'magni', 'dolores', 'ratione', 'voluptatem', 'nesciunt', 'neque', 'porro', 'quisquam',
        'dolorem', 'quia', 'numquam', 'eius', 'modi', 'tempora', 'incidunt', 'magnam',
        'aliquam', 'quaerat', 'voluptatem', 'ut', 'enim', 'ad', 'minima', 'veniam', 'quis',
        'nostrum', 'exercitationem', 'ullam', 'corporis', 'suscipit', 'laboriosam', 'nisi',
        'aliquid', 'commodi', 'consequatur', 'autem', 'vel', 'eum', 'iure', 'quam', 'nihil',
        'molestiae', 'illum', 'fugiat', 'quo', 'voluptas', 'nulla', 'pariatur'
    ];

    /**
     * First names for generating person names.
     *
     * @var array<string, array<int, string>>
     */
    private array $first_names = [
        'male' => [
            'James', 'John', 'Robert', 'Michael', 'William', 'David', 'Richard', 'Joseph',
            'Thomas', 'Charles', 'Christopher', 'Daniel', 'Matthew', 'Anthony', 'Mark',
            'Donald', 'Steven', 'Paul', 'Andrew', 'Joshua', 'Kenneth', 'Kevin', 'Brian',
            'George', 'Timothy', 'Ronald', 'Edward', 'Jason', 'Jeffrey', 'Ryan', 'Jacob',
            'Gary', 'Nicholas', 'Eric', 'Jonathan', 'Stephen', 'Larry', 'Justin', 'Scott',
            'Brandon', 'Benjamin', 'Samuel', 'Gregory', 'Alexander', 'Patrick', 'Frank',
            'Raymond', 'Jack', 'Dennis', 'Jerry'
        ],
        'female' => [
            'Mary', 'Patricia', 'Jennifer', 'Linda', 'Elizabeth', 'Barbara', 'Susan',
            'Jessica', 'Sarah', 'Karen', 'Lisa', 'Nancy', 'Betty', 'Margaret', 'Sandra',
            'Ashley', 'Kimberly', 'Emily', 'Donna', 'Michelle', 'Carol', 'Amanda', 'Dorothy',
            'Melissa', 'Deborah', 'Stephanie', 'Rebecca', 'Sharon', 'Laura', 'Cynthia',
            'Kathleen', 'Amy', 'Angela', 'Shirley', 'Anna', 'Ruth', 'Brenda', 'Pamela',
            'Nicole', 'Katherine', 'Virginia', 'Catherine', 'Christine', 'Samantha',
            'Debra', 'Janet', 'Rachel', 'Carolyn', 'Emma', 'Maria'
        ]
    ];

    /**
     * Last names for generating person names.
     *
     * @var array<int, string>
     */
    private array $last_names = [
        'Smith', 'Johnson', 'Williams', 'Jones', 'Brown', 'Davis', 'Miller', 'Wilson',
        'Moore', 'Taylor', 'Anderson', 'Thomas', 'Jackson', 'White', 'Harris', 'Martin',
        'Thompson', 'Garcia', 'Martinez', 'Robinson', 'Clark', 'Rodriguez', 'Lewis', 'Lee',
        'Walker', 'Hall', 'Allen', 'Young', 'Hernandez', 'King', 'Wright', 'Lopez', 'Hill',
        'Scott', 'Green', 'Adams', 'Baker', 'Gonzalez', 'Nelson', 'Carter', 'Mitchell',
        'Perez', 'Roberts', 'Turner', 'Phillips', 'Campbell', 'Parker', 'Evans', 'Edwards',
        'Collins'
    ];

    /**
     * Domain suffixes for generating domains.
     *
     * @var array<int, string>
     */
    private array $domain_suffixes = [
        'com', 'org', 'net', 'io', 'co', 'info', 'biz', 'dev'
    ];

    /**
     * Company suffixes for generating company names.
     *
     * @var array<int, string>
     */
    private array $company_suffixes = [
        'Inc', 'LLC', 'Group', 'Ltd', 'Co', 'Corporation', 'Partners', 'Solutions', 
        'Tech', 'Labs', 'Software', 'Systems', 'Media', 'Designs'
    ];

    /**
     * Timezones list.
     *
     * @var array<int, string>
     */
    private array $timezones = [
        'UTC', 'America/New_York', 'Europe/London', 'Europe/Paris', 'Asia/Tokyo', 
        'Australia/Sydney', 'Pacific/Auckland', 'America/Chicago', 'America/Los_Angeles',
        'Asia/Singapore', 'Asia/Dubai', 'Europe/Berlin', 'Europe/Moscow', 'America/Sao_Paulo'
    ];

    /**
     * Generate a random letter.
     *
     * @return string A single letter.
     */
    public function letter(): string {
        return chr(rand(97, 122));
    }
    
    /**
     * Generate a single random word.
     *
     * @return string A random word.
     */
    public function word(): string {
        return $this->word_list[array_rand($this->word_list)];
    }
    
    /**
     * Generate multiple random words.
     *
     * @param int  $count         Number of words to generate.
     * @param bool $sentence_case Whether to capitalize the first word.
     * @return string Random words separated by spaces.
     */
    public function words(int $count, bool $sentence_case = false): string {
        // Try to get from API first
        $words = $this->fetch_from_api('words', $count);
        
        if (empty($words)) {
            // Fallback to local generation
            $words = [];
            for ($i = 0; $i < $count; $i++) {
                $words[] = $this->word();
            }
            $words = implode(' ', $words);
        }
        
        // Apply sentence case if requested
        if ($sentence_case && !empty($words)) {
            $words = ucfirst($words);
        }
        
        return $words;
    }
    
    /**
     * Generate a random sentence.
     *
     * @param int $word_count Optional number of words.
     * @return string A random sentence.
     */
    public function sentence(int $word_count = 0): string {
        // If word count not provided, generate between 4 and 10 words
        if ($word_count <= 0) {
            $word_count = rand(4, 10);
        }
        
        // Generate words and capitalize first one
        $sentence = $this->words($word_count, true);
        
        // Add a period at the end
        return rtrim($sentence, '.') . '.';
    }
    
    /**
     * Generate multiple random sentences.
     *
     * @param int $count Number of sentences to generate.
     * @return array<int, string> Array of random sentences.
     */
    public function sentences(int $count): array {
        $sentences = [];
        
        for ($i = 0; $i < $count; $i++) {
            $sentences[] = $this->sentence();
        }
        
        return $sentences;
    }
    
    /**
     * Generate a random paragraph.
     *
     * @param int $sentence_count Optional number of sentences.
     * @return string A random paragraph.
     */
    public function paragraph(int $sentence_count = 0): string {
        // If sentence count not provided, generate between 3 and 6 sentences
        if ($sentence_count <= 0) {
            $sentence_count = rand(3, 6);
        }
        
        // Try to get from API first
        $paragraph = $this->fetch_from_api('paragraphs', 1);
        
        if (empty($paragraph)) {
            // Fallback to local generation
            $sentences = $this->sentences($sentence_count);
            $paragraph = implode(' ', $sentences);
        }
        
        return $paragraph;
    }
    
    /**
     * Generate multiple random paragraphs.
     *
     * @param int $count Number of paragraphs to generate.
     * @return array<int, string> Array of random paragraphs.
     */
    public function paragraphs(int $count): array {
        // Try to get from API first
        $content = $this->fetch_from_api('paragraphs', $count);
        
        if (!empty($content)) {
            // Split by double newlines
            return explode("\n\n", $content);
        }
        
        // Fallback to local generation
        $paragraphs = [];
        
        for ($i = 0; $i < $count; $i++) {
            $paragraphs[] = $this->paragraph();
        }
        
        return $paragraphs;
    }
    
    /**
     * Replace all ? characters with random letters.
     *
     * @param string $pattern String with ?'s to replace with letters.
     * @return string Resulting string.
     */
    public function lexify(string $pattern): string {
        return preg_replace_callback('/\?/', function() {
            return $this->letter();
        }, $pattern);
    }
    
    /**
     * Replace all * characters with random numbers.
     *
     * @param string $pattern String with *'s to replace with numbers.
     * @return string Resulting string.
     */
    public function asciify(string $pattern): string {
        return preg_replace_callback('/\*/', function() {
            return (string) rand(0, 9);
        }, $pattern);
    }
    
    /**
     * Generate a string matching a regular expression pattern.
     *
     * @param string $pattern Regular expression pattern.
     * @return string Resulting string.
     */
    public function regexify(string $pattern): string {
        // Simple character class handling: [a-z], [A-Z], [0-9]
        $result = '';
        $pattern_length = strlen($pattern);
        
        for ($i = 0; $i < $pattern_length; $i++) {
            if ($pattern[$i] === '[' && $i + 3 < $pattern_length && $pattern[$i + 2] === '-' && $pattern[$i + 4] === ']') {
                $min = ord($pattern[$i + 1]);
                $max = ord($pattern[$i + 3]);
                $result .= chr(rand($min, $max));
                $i += 4;
            } else {
                $result .= $pattern[$i];
            }
        }
        
        return $result;
    }
    
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
    public function image(int $width = 640, int $height = 480, ?string $keyword = null, bool $gray = false, string $provider = 'placeholder'): string {
        switch ($provider) {
            case 'picsum':
                $url = 'https://picsum.photos/' . $width . '/' . $height;
                if ($gray) {
                    $url .= '?grayscale';
                }
                break;
                
            case 'placeholder':
            default:
                $bg_color = $gray ? '888' : dechex(rand(0, 0xFFFFFF));
                $text_color = $gray ? 'FFF' : dechex(rand(0, 0xFFFFFF));
                
                $url = 'https://via.placeholder.com/' . $width . 'x' . $height;
                $url .= '/' . $bg_color . '/' . $text_color;
                
                if ($keyword) {
                    $url .= '?text=' . urlencode($keyword);
                }
                break;
        }
        
        return $url;
    }
    
    /**
     * Generate a random person name.
     *
     * @param string|null $gender Optional gender (male, female).
     * @return string Full name.
     */
    public function personName(?string $gender = null): string {
        return $this->firstName($gender) . ' ' . $this->lastName();
    }
    
    /**
     * Generate a random first name.
     *
     * @param string|null $gender Optional gender (male, female).
     * @return string First name.
     */
    public function firstName(?string $gender = null): string {
        // If gender not specified, pick randomly
        if ($gender === null) {
            $gender = rand(0, 1) === 0 ? 'male' : 'female';
        }
        
        // Ensure gender is valid
        if (!in_array($gender, ['male', 'female'])) {
            $gender = 'male';
        }
        
        return $this->first_names[$gender][array_rand($this->first_names[$gender])];
    }
    
    /**
     * Generate a random last name.
     *
     * @return string Last name.
     */
    public function lastName(): string {
        return $this->last_names[array_rand($this->last_names)];
    }
    
    /**
     * Generate a random email address.
     *
     * @param string|null $name Optional name to use in the email.
     * @return string Email address.
     */
    public function email(?string $name = null): string {
        if ($name === null) {
            $name = $this->personName();
        }
        
        // Convert name to email-friendly format
        $email_name = strtolower(str_replace(' ', '.', $name));
        
        // Add a domain
        return $email_name . '@' . $this->domainName();
    }
    
    /**
     * Generate a random domain name.
     *
     * @return string Domain name.
     */
    public function domainName(): string {
        // Generate 1-2 random words for domain
        $words_count = rand(1, 2);
        $words = [];
        
        for ($i = 0; $i < $words_count; $i++) {
            $words[] = $this->word();
        }
        
        $domain = strtolower(implode('', $words));
        $suffix = $this->domain_suffixes[array_rand($this->domain_suffixes)];
        
        return $domain . '.' . $suffix;
    }
    
    /**
     * Generate a random URL.
     *
     * @return string URL.
     */
    public function url(): string {
        $protocol = rand(0, 1) === 0 ? 'http' : 'https';
        $domain = $this->domainName();
        
        // Optionally add a path
        $path = '';
        if (rand(0, 1) === 1) {
            $path_segments = rand(1, 3);
            $path_parts = [];
            
            for ($i = 0; $i < $path_segments; $i++) {
                $path_parts[] = $this->word();
            }
            
            $path = '/' . implode('/', $path_parts);
        }
        
        return $protocol . '://' . $domain . $path;
    }
    
    /**
     * Generate a random company name.
     *
     * @return string Company name.
     */
    public function company(): string {
        // Company name can be:
        // 1. Last name + Suffix
        // 2. Last name + & + Last name
        // 3. Two capitalized words
        
        $type = rand(0, 2);
        
        switch ($type) {
            case 0:
                return $this->lastName() . ' ' . $this->company_suffixes[array_rand($this->company_suffixes)];
                
            case 1:
                return $this->lastName() . ' & ' . $this->lastName();
                
            case 2:
                return ucfirst($this->word()) . ' ' . ucfirst($this->word());
                
            default:
                return $this->lastName() . ' ' . $this->company_suffixes[array_rand($this->company_suffixes)];
        }
    }
    
    /**
     * Generate a random date.
     *
     * @param string $format   Date format (using PHP date format).
     * @param string $min_date Minimum date in strtotime format.
     * @param string $max_date Maximum date in strtotime format.
     * @return string Formatted date.
     */
    public function date(string $format = 'Y-m-d', string $min_date = '-30 years', string $max_date = 'now'): string {
        $min_timestamp = strtotime($min_date);
        $max_timestamp = strtotime($max_date);
        
        // Ensure valid timestamps
        if (!$min_timestamp || !$max_timestamp) {
            $min_timestamp = strtotime('-30 years');
            $max_timestamp = time();
        }
        
        // Handle case where min > max
        if ($min_timestamp > $max_timestamp) {
            $temp = $min_timestamp;
            $min_timestamp = $max_timestamp;
            $max_timestamp = $temp;
        }
        
        // Generate random timestamp and format
        $random_timestamp = rand($min_timestamp, $max_timestamp);
        return date($format, $random_timestamp);
    }
    
    /**
     * Generate a random time.
     *
     * @param string $format Time format (using PHP date format).
     * @return string Formatted time.
     */
    public function time(string $format = 'H:i:s'): string {
        $hours = rand(0, 23);
        $minutes = rand(0, 59);
        $seconds = rand(0, 59);
        
        return date($format, strtotime("$hours:$minutes:$seconds"));
    }
    
    /**
     * Generate a random timezone.
     *
     * @return string Timezone.
     */
    public function timezone(): string {
        return $this->timezones[array_rand($this->timezones)];
    }
    
    /**
     * Generate random latitude.
     *
     * @param float $min Minimum value.
     * @param float $max Maximum value.
     * @return float Latitude.
     */
    public function latitude(float $min = -90, float $max = 90): float {
        return $min + (mt_rand() / mt_getrandmax()) * ($max - $min);
    }
    
    /**
     * Generate random longitude.
     *
     * @param float $min Minimum value.
     * @param float $max Maximum value.
     * @return float Longitude.
     */
    public function longitude(float $min = -180, float $max = 180): float {
        return $min + (mt_rand() / mt_getrandmax()) * ($max - $min);
    }
    
    /**
     * Fetch content from lipsum.com API.
     *
     * @param string $type  Content type: paragraphs, words, bytes, lists.
     * @param int    $count Amount of content to generate.
     * @return string Generated content or empty string on failure.
     */
    private function fetch_from_api(string $type, int $count): string {
        // Check cache first
        $cache_key = $type . '_' . $count;
        if (isset($this->cache[$cache_key])) {
            return $this->cache[$cache_key];
        }
        
        // Build API URL
        $url = add_query_arg([
            'what' => $type,
            'amount' => $count,
            'start' => 'yes',
        ], $this->api_url);
        
        // Make request
        $response = wp_remote_get($url);
        
        // Check for errors
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return '';
        }
        
        // Parse JSON response
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (!$data || !isset($data['feed']['lipsum'])) {
            return '';
        }
        
        // Store in cache and return
        $content = $data['feed']['lipsum'];
        $this->cache[$cache_key] = $content;
        
        return $content;
    }
}