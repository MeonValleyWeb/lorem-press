<div class="wrap lorem-press-admin">
    <h1><?php echo esc_html__('Generate Posts', 'lorem-press'); ?></h1>
    
    <div class="lorem-press-generator-container">
        <div class="lorem-press-generator-form">
            <form id="lorem-press-post-form">
                <div class="lorem-press-form-section">
                    <h2><?php echo esc_html__('General Settings', 'lorem-press'); ?></h2>
                    
                    <div class="lorem-press-form-field">
                        <label for="post-count"><?php echo esc_html__('Number of Posts to Generate', 'lorem-press'); ?></label>
                        <input type="number" id="post-count" name="count" min="1" max="100" value="10">
                        <p class="description"><?php echo esc_html__('Enter a number between 1 and 100', 'lorem-press'); ?></p>
                    </div>
                    
                    <div class="lorem-press-form-field">
                        <label for="post-type"><?php echo esc_html__('Post Type', 'lorem-press'); ?></label>
                        <select id="post-type" name="settings[post_type]">
                            <?php foreach ($settings_schema['post_type']['options'] as $value => $label) : ?>
                                <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="lorem-press-form-field">
                        <label for="post-status"><?php echo esc_html__('Post Status', 'lorem-press'); ?></label>
                        <select id="post-status" name="settings[post_status]">
                            <?php foreach ($settings_schema['post_status']['options'] as $status) : ?>
                                <option value="<?php echo esc_attr($status->name); ?>"><?php echo esc_html($status->label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="lorem-press-form-section">
                    <h2><?php echo esc_html__('Content Settings', 'lorem-press'); ?></h2>
                    
                    <div class="lorem-press-form-field lorem-press-form-field-group">
                        <label><?php echo esc_html__('Title Length', 'lorem-press'); ?></label>
                        <div class="lorem-press-form-field-inline">
                            <label for="title-min-words"><?php echo esc_html__('Min Words', 'lorem-press'); ?></label>
                            <input type="number" id="title-min-words" name="settings[title_min_words]" min="1" max="20" value="<?php echo esc_attr($settings_schema['title_min_words']['default']); ?>">
                            
                            <label for="title-max-words"><?php echo esc_html__('Max Words', 'lorem-press'); ?></label>
                            <input type="number" id="title-max-words" name="settings[title_max_words]" min="1" max="20" value="<?php echo esc_attr($settings_schema['title_max_words']['default']); ?>">
                        </div>
                    </div>
                    
                    <div class="lorem-press-form-field lorem-press-form-field-group">
                        <label><?php echo esc_html__('Content Length', 'lorem-press'); ?></label>
                        <div class="lorem-press-form-field-inline">
                            <label for="content-min-paragraphs"><?php echo esc_html__('Min Paragraphs', 'lorem-press'); ?></label>
                            <input type="number" id="content-min-paragraphs" name="settings[content_min_paragraphs]" min="1" max="50" value="<?php echo esc_attr($settings_schema['content_min_paragraphs']['default']); ?>">
                            
                            <label for="content-max-paragraphs"><?php echo esc_html__('Max Paragraphs', 'lorem-press'); ?></label>
                            <input type="number" id="content-max-paragraphs" name="settings[content_max_paragraphs]" min="1" max="50" value="<?php echo esc_attr($settings_schema['content_max_paragraphs']['default']); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="lorem-press-form-section">
                    <h2><?php echo esc_html__('Featured Image', 'lorem-press'); ?></h2>
                    
                    <div class="lorem-press-form-field">
                        <label for="with-featured-image">
                            <input type="checkbox" id="with-featured-image" name="settings[with_featured_image]" value="1" <?php checked($settings_schema['with_featured_image']['default']); ?>>
                            <?php echo esc_html__('Generate Featured Images', 'lorem-press'); ?>
                        </label>
                    </div>
                    
                    <div class="lorem-press-form-field featured-image-settings" style="<?php echo $settings_schema['with_featured_image']['default'] ? '' : 'display: none;'; ?>">
                        <label for="featured-image-keyword"><?php echo esc_html__('Image Keyword (Optional)', 'lorem-press'); ?></label>
                        <input type="text" id="featured-image-keyword" name="settings[featured_image_keyword]" placeholder="<?php echo esc_attr__('e.g., nature, business, technology', 'lorem-press'); ?>">
                        <p class="description"><?php echo esc_html__('Enter a keyword to use for the featured image search. Leave empty for random images.', 'lorem-press'); ?></p>
                    </div>
                </div>
                
                <div class="lorem-press-form-section">
                    <h2><?php echo esc_html__('Author Settings', 'lorem-press'); ?></h2>
                    
                    <div class="lorem-press-form-field">
                        <label for="author-type"><?php echo esc_html__('Author Selection', 'lorem-press'); ?></label>
                        <select id="author-type" name="settings[author_type]">
                            <?php foreach ($settings_schema['author_type']['options'] as $value => $label) : ?>
                                <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="lorem-press-form-section">
                    <h2><?php echo esc_html__('Taxonomies', 'lorem-press'); ?></h2>
                    
                    <div class="lorem-press-form-field">
                        <label for="with-terms">
                            <input type="checkbox" id="with-terms" name="settings[with_terms]" value="1">
                            <?php echo esc_html__('Generate and Assign Taxonomy Terms', 'lorem-press'); ?>
                        </label>
                    </div>
                    
                    <div class="lorem-press-form-field taxonomy-settings" style="display: none;">
                        <label><?php echo esc_html__('Taxonomies to Use', 'lorem-press'); ?></label>
                        <div class="lorem-press-checkboxes">
                            <?php 
                            $post_type = $settings_schema['post_type']['default'];
                            $taxonomies = get_object_taxonomies($post_type, 'objects');
                            
                            foreach ($taxonomies as $taxonomy) : 
                                if (!$taxonomy->public) continue;
                            ?>
                                <label for="taxonomy-<?php echo esc_attr($taxonomy->name); ?>">
                                    <input type="checkbox" id="taxonomy-<?php echo esc_attr($taxonomy->name); ?>" name="settings[taxonomies][]" value="<?php echo esc_attr($taxonomy->name); ?>" checked>
                                    <?php echo esc_html($taxonomy->labels->name); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <p class="description"><?php echo esc_html__('Select which taxonomies to generate terms for. If none selected, all available taxonomies will be used.', 'lorem-press'); ?></p>
                    </div>
                </div>
                
                <div class="lorem-press-form-section">
                    <h2><?php echo esc_html__('Meta Fields', 'lorem-press'); ?></h2>
                    
                    <div class="lorem-press-form-field">
                        <label for="with-meta">
                            <input type="checkbox" id="with-meta" name="settings[with_meta]" value="1">
                            <?php echo esc_html__('Generate Meta Fields', 'lorem-press'); ?>
                        </label>
                    </div>
                    
                    <div class="lorem-press-form-field meta-settings" style="display: none;">
                        <div id="meta-fields-container">
                            <div class="meta-field-row">
                                <input type="text" name="meta_key[]" placeholder="<?php echo esc_attr__('Meta Key', 'lorem-press'); ?>">
                                <select name="meta_type[]">
                                    <option value="text"><?php echo esc_html__('Text', 'lorem-press'); ?></option>
                                    <option value="number"><?php echo esc_html__('Number', 'lorem-press'); ?></option>
                                    <option value="html"><?php echo esc_html__('HTML', 'lorem-press'); ?></option>
                                    <option value="date"><?php echo esc_html__('Date', 'lorem-press'); ?></option>
                                    <option value="boolean"><?php echo esc_html__('Boolean', 'lorem-press'); ?></option>
                                    <option value="person"><?php echo esc_html__('Person', 'lorem-press'); ?></option>
                                    <option value="email"><?php echo esc_html__('Email', 'lorem-press'); ?></option>
                                    <option value="geo"><?php echo esc_html__('Geo Information', 'lorem-press'); ?></option>
                                </select>
                                <button type="button" class="button remove-meta-field" style="display: none;"><?php echo esc_html__('Remove', 'lorem-press'); ?></button>
                            </div>
                        </div>
                        <button type="button" class="button add-meta-field"><?php echo esc_html__('Add Meta Field', 'lorem-press'); ?></button>
                    </div>
                </div>
                
                <div class="lorem-press-form-section">
                    <h2><?php echo esc_html__('Comments', 'lorem-press'); ?></h2>
                    
                    <div class="lorem-press-form-field">
                        <label for="with-comments">
                            <input type="checkbox" id="with-comments" name="settings[with_comments]" value="1">
                            <?php echo esc_html__('Generate Comments', 'lorem-press'); ?>
                        </label>
                    </div>
                    
                    <div class="lorem-press-form-field comments-settings" style="display: none;">
                        <div class="lorem-press-form-field-inline">
                            <label for="comments-min"><?php echo esc_html__('Min Comments', 'lorem-press'); ?></label>
                            <input type="number" id="comments-min" name="settings[comments_min]" min="0" max="100" value="<?php echo esc_attr($settings_schema['comments_min']['default']); ?>">
                            
                            <label for="comments-max"><?php echo esc_html__('Max Comments', 'lorem-press'); ?></label>
                            <input type="number" id="comments-max" name="settings[comments_max]" min="0" max="100" value="<?php echo esc_attr($settings_schema['comments_max']['default']); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="lorem-press-form-submit">
                    <input type="hidden" name="generator_type" value="post">
                    <?php wp_nonce_field('modern_faker_nonce', 'nonce'); ?>
                    <button type="submit" class="button button-primary button-large"><?php echo esc_html__('Generate Posts', 'lorem-press'); ?></button>
                </div>
            </form>
        </div>
        
        <div class="lorem-press-generator-results">
            <div class="lorem-press-results-header">
                <h2><?php echo esc_html__('Results', 'lorem-press'); ?></h2>
                <div class="lorem-press-results-actions" style="display: none;">
                    <button type="button" class="button clear-results"><?php echo esc_html__('Clear Results', 'lorem-press'); ?></button>
                </div>
            </div>
            
            <div id="generation-progress" style="display: none;">
                <div class="lorem-press-progress-bar">
                    <div class="lorem-press-progress-bar-inner"></div>
                </div>
                <p class="lorem-press-progress-status"><?php echo esc_html__('Generating...', 'lorem-press'); ?></p>
            </div>
            
            <div id="generation-results">
                <p class="lorem-press-no-results"><?php echo esc_html__('No posts generated yet.', 'lorem-press'); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        // Toggle settings visibility based on checkboxes
        $('#with-featured-image').on('change', function() {
            $('.featured-image-settings').toggle($(this).is(':checked'));
        });
        
        $('#with-terms').on('change', function() {
            $('.taxonomy-settings').toggle($(this).is(':checked'));
        });
        
        $('#with-meta').on('change', function() {
            $('.meta-settings').toggle($(this).is(':checked'));
        });
        
        $('#with-comments').on('change', function() {
            $('.comments-settings').toggle($(this).is(':checked'));
        });
        
        // Update taxonomies when post type changes
        $('#post-type').on('change', function() {
            var postType = $(this).val();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'modern_faker_get_taxonomies',
                    post_type: postType,
                    nonce: '<?php echo wp_create_nonce('modern_faker_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        var taxonomies = response.data;
                        var $container = $('.lorem-press-checkboxes');
                        $container.empty();
                        
                        $.each(taxonomies, function(name, label) {
                            $container.append(
                                '<label for="taxonomy-' + name + '">' +
                                    '<input type="checkbox" id="taxonomy-' + name + '" name="settings[taxonomies][]" value="' + name + '" checked>' +
                                    label +
                                '</label>'
                            );
                        });
                    }
                }
            });
        });
        
        // Add and remove meta fields
        $('.add-meta-field').on('click', function() {
            var $newRow = $('.meta-field-row').first().clone();
            $newRow.find('input, select').val('');
            $newRow.find('.remove-meta-field').show();
            $('#meta-fields-container').append($newRow);
        });
        
        $(document).on('click', '.remove-meta-field', function() {
            $(this).closest('.meta-field-row').remove();
        });
        
        // Form submission
        $('#lorem-press-post-form').on('submit', function(e) {
            e.preventDefault();
            
            // Prepare meta fields data
            if ($('#with-meta').is(':checked')) {
                var metaFields = {};
                $('.meta-field-row').each(function() {
                    var $row = $(this);
                    var key = $row.find('input[name="meta_key[]"]').val();
                    var type = $row.find('select[name="meta_type[]"]').val();
                    
                    if (key && type) {
                        metaFields[key] = {
                            type: type
                        };
                    }
                });
                
                $('input[name="settings[meta_fields]"]').val(JSON.stringify(metaFields));
            }
            
            // Show progress
            $('#generation-progress').show();
            $('.lorem-press-progress-bar-inner').width('0%');
            $('#generation-results').empty();
            $('.lorem-press-no-results').hide();
            $('.lorem-press-results-actions').hide();
            
            // Get form data
            var formData = $(this).serialize();
            
            // AJAX request
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'modern_faker_generate',
                    ...formData
                },
                beforeSend: function() {
                    $('.lorem-press-progress-status').text('Generating...');
                },
                success: function(response) {
                    $('#generation-progress').hide();
                    $('.lorem-press-results-actions').show();
                    
                    if (response.success) {
                        var results = response.data.results;
                        var errors = response.data.errors;
                        
                        if (results.length > 0) {
                            var $resultsContainer = $('<div class="lorem-press-results-list"></div>');
                            
                            $.each(results, function(index, postId) {
                                $resultsContainer.append(
                                    '<div class="lorem-press-result-item">' +
                                        '<span class="dashicons dashicons-admin-post"></span> ' +
                                        'Created post: <a href="' + response.data.edit_links[index] + '" target="_blank">' + response.data.titles[index] + '</a> ' +
                                        '(ID: ' + postId + ')' +
                                    '</div>'
                                );
                            });
                            
                            $('#generation-results').append($resultsContainer);
                        }
                        
                        if (errors.length > 0) {
                            var $errorsContainer = $('<div class="lorem-press-errors-list"></div>');
                            
                            $.each(errors, function(index, error) {
                                $errorsContainer.append(
                                    '<div class="lorem-press-error-item">' +
                                        '<span class="dashicons dashicons-warning"></span> ' +
                                        'Error: ' + error +
                                    '</div>'
                                );
                            });
                            
                            $('#generation-results').append($errorsContainer);
                        }
                    } else {
                        $('#generation-results').append(
                            '<div class="lorem-press-error-message">' +
                                '<span class="dashicons dashicons-warning"></span> ' +
                                'Error: ' + response.data.message +
                            '</div>'
                        );
                    }
                },
                error: function() {
                    $('#generation-progress').hide();
                    $('.lorem-press-results-actions').show();
                    
                    $('#generation-results').append(
                        '<div class="lorem-press-error-message">' +
                            '<span class="dashicons dashicons-warning"></span> ' +
                            'Error: There was a problem communicating with the server.' +
                        '</div>'
                    );
                }
            });
        });
        
        // Clear results
        $('.clear-results').on('click', function() {
            $('#generation-results').empty();
            $('.lorem-press-no-results').show();
            $('.lorem-press-results-actions').hide();
        });
    });
</script>

<style>
    .lorem-press-generator-container {
        display: grid;
        grid-template-columns: 3fr 2fr;
        gap: 20px;
        margin-top: 20px;
    }
    
    .lorem-press-generator-form {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        border: 1px solid #ccd0d4;
    }
    
    .lorem-press-generator-results {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        border: 1px solid #ccd0d4;
    }
    
    .lorem-press-form-section {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .lorem-press-form-section:last-child {
        border-bottom: none;
    }
    
    .lorem-press-form-field {
        margin-bottom: 15px;
    }
    
    .lorem-press-form-field label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
    }
    
    .lorem-press-form-field-inline {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .lorem-press-form-field-inline label {
        margin-bottom: 0;
    }
    
    .lorem-press-form-field input[type="text"],
    .lorem-press-form-field input[type="number"],
    .lorem-press-form-field select {
        width: 100%;
        max-width: 400px;
    }
    
    .lorem-press-form-field-inline input[type="number"] {
        width: 80px;
    }
    
    .lorem-press-checkboxes {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }
    
    .meta-field-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
        align-items: center;
    }
    
    .meta-field-row input,
    .meta-field-row select {
        flex: 1;
    }
    
    .lorem-press-form-submit {
        margin-top: 30px;
    }
    
    .lorem-press-results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .lorem-press-progress-bar {
        height: 20px;
        background-color: #f0f0f0;
        border-radius: 10px;
        margin-bottom: 10px;
        overflow: hidden;
    }
    
    .lorem-press-progress-bar-inner {
        height: 100%;
        background-color: #2271b1;
        width: 0%;
        transition: width 0.3s ease;
    }
    
    .lorem-press-progress-status {
        text-align: center;
        font-style: italic;
    }
    
    .lorem-press-results-list {
        margin-bottom: 20px;
    }
    
    .lorem-press-result-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .lorem-press-result-item:last-child {
        border-bottom: none;
    }
    
    .lorem-press-error-message {
        color: #d63638;
        padding: 10px;
        background-color: #fcf0f1;
        border-left: 4px solid #d63638;
    }
    
    .lorem-press-errors-list {
        margin-top: 20px;
    }
    
    .lorem-press-error-item {
        color: #d63638;
        padding: 5px 0;
    }
    
    .lorem-press-no-results {
        text-align: center;
        color: #666;
        font-style: italic;
    }
    
    /* Responsive */
    @media (max-width: 782px) {
        .lorem-press-generator-container {
            grid-template-columns: 1fr;
        }
    }
</style>