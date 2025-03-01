<div class="wrap lorem-press-admin">
    <h1><?php echo esc_html__('Modern Faker', 'lorem-press'); ?></h1>
    
    <div class="lorem-press-dashboard">
        <div class="lorem-press-welcome">
            <h2><?php echo esc_html__('Welcome to Modern Faker', 'lorem-press'); ?></h2>
            <p><?php echo esc_html__('Modern Faker is a powerful content generator for WordPress. Use it to create realistic test data for your WordPress site including posts, users, comments, terms, and meta fields.', 'lorem-press'); ?></p>
        </div>
        
        <div class="lorem-press-cards">
            <div class="lorem-press-card">
                <div class="lorem-press-card-icon dashicons dashicons-admin-post"></div>
                <h3><?php echo esc_html__('Generate Posts', 'lorem-press'); ?></h3>
                <p><?php echo esc_html__('Create posts and custom post types with featured images, taxonomies, meta fields, and comments.', 'lorem-press'); ?></p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=lorem-press-posts')); ?>" class="button button-primary"><?php echo esc_html__('Generate Posts', 'lorem-press'); ?></a>
            </div>
            
            <div class="lorem-press-card">
                <div class="lorem-press-card-icon dashicons dashicons-admin-users"></div>
                <h3><?php echo esc_html__('Generate Users', 'lorem-press'); ?></h3>
                <p><?php echo esc_html__('Create users with randomized profiles, avatars, and meta information.', 'lorem-press'); ?></p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=lorem-press-users')); ?>" class="button button-primary"><?php echo esc_html__('Generate Users', 'lorem-press'); ?></a>
            </div>
            
            <div class="lorem-press-card">
                <div class="lorem-press-card-icon dashicons dashicons-category"></div>
                <h3><?php echo esc_html__('Generate Terms', 'lorem-press'); ?></h3>
                <p><?php echo esc_html__('Create terms for categories, tags, and custom taxonomies.', 'lorem-press'); ?></p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=lorem-press-terms')); ?>" class="button button-primary"><?php echo esc_html__('Generate Terms', 'lorem-press'); ?></a>
            </div>
            
            <div class="lorem-press-card">
                <div class="lorem-press-card-icon dashicons dashicons-admin-comments"></div>
                <h3><?php echo esc_html__('Generate Comments', 'lorem-press'); ?></h3>
                <p><?php echo esc_html__('Create comments and comment threads for your posts.', 'lorem-press'); ?></p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=lorem-press-comments')); ?>" class="button button-primary"><?php echo esc_html__('Generate Comments', 'lorem-press'); ?></a>
            </div>
        </div>
        
        <div class="lorem-press-stats">
            <h2><?php echo esc_html__('Site Statistics', 'lorem-press'); ?></h2>
            
            <div class="lorem-press-stats-grid">
                <?php
                $post_types = get_post_types(['public' => true], 'objects');
                
                foreach ($post_types as $post_type) {
                    $count = wp_count_posts($post_type->name);
                    $published = $count->publish ?? 0;
                ?>
                <div class="lorem-press-stat-item">
                    <span class="lorem-press-stat-number"><?php echo esc_html($published); ?></span>
                    <span class="lorem-press-stat-label"><?php echo esc_html($post_type->labels->name); ?></span>
                </div>
                <?php } ?>
                
                <div class="lorem-press-stat-item">
                    <span class="lorem-press-stat-number"><?php echo esc_html(count_users()['total_users']); ?></span>
                    <span class="lorem-press-stat-label"><?php echo esc_html__('Users', 'lorem-press'); ?></span>
                </div>
                
                <div class="lorem-press-stat-item">
                    <span class="lorem-press-stat-number"><?php echo esc_html(wp_count_terms(['taxonomy' => 'category'])); ?></span>
                    <span class="lorem-press-stat-label"><?php echo esc_html__('Categories', 'lorem-press'); ?></span>
                </div>
                
                <div class="lorem-press-stat-item">
                    <span class="lorem-press-stat-number"><?php echo esc_html(wp_count_terms(['taxonomy' => 'post_tag'])); ?></span>
                    <span class="lorem-press-stat-label"><?php echo esc_html__('Tags', 'lorem-press'); ?></span>
                </div>
                
                <div class="lorem-press-stat-item">
                    <span class="lorem-press-stat-number"><?php echo esc_html(wp_count_comments()->total_comments); ?></span>
                    <span class="lorem-press-stat-label"><?php echo esc_html__('Comments', 'lorem-press'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .lorem-press-dashboard {
        margin-top: 20px;
    }
    
    .lorem-press-welcome {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
        border: 1px solid #ccd0d4;
    }
    
    .lorem-press-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .lorem-press-card {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        display: flex;
        flex-direction: column;
        border: 1px solid #ccd0d4;
    }
    
    .lorem-press-card-icon {
        font-size: 2.5em;
        margin-bottom: 10px;
        color: #2271b1;
    }
    
    .lorem-press-card h3 {
        margin-top: 0;
    }
    
    .lorem-press-card p {
        flex-grow: 1;
        margin-bottom: 15px;
    }
    
    .lorem-press-stats {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        border: 1px solid #ccd0d4;
    }
    
    .lorem-press-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    
    .lorem-press-stat-item {
        text-align: center;
    }
    
    .lorem-press-stat-number {
        display: block;
        font-size: 2em;
        font-weight: bold;
        color: #2271b1;
    }
    
    .lorem-press-stat-label {
        display: block;
        font-size: 1em;
        color: #50575e;
    }
</style>