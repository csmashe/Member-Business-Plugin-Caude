<?php

if (!defined('ABSPATH')) {
    exit;
}

class P116_Templates {
    
    public function __construct() {
        add_filter('template_include', array($this, 'template_loader'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_template_styles'));
    }
    
    public function template_loader($template) {
        if (is_singular('p116_business')) {
            $plugin_template = P116_PLUGIN_PATH . 'templates/single-p116_business.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        if (is_tax('p116_business_category')) {
            $plugin_template = P116_PLUGIN_PATH . 'templates/taxonomy-p116_business_category.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        return $template;
    }
    
    public function enqueue_template_styles() {
        if (is_singular('p116_business') || is_tax('p116_business_category') || $this->is_directory_page()) {
            wp_enqueue_style('p116-templates', P116_PLUGIN_URL . 'public/css/templates.css', array(), P116_PLUGIN_VERSION);
        }
    }
    
    private function is_directory_page() {
        global $post;
        return $post && has_block('p116/directory', $post);
    }
    
    public static function render_business_card($business_data, $show_excerpt = true) {
        $flags_html = '';
        if (!empty($business_data['flags'])) {
            foreach ($business_data['flags'] as $flag) {
                $flags_html .= '<span class="p116-flag ' . esc_attr($flag['type']) . '">' . esc_html($flag['label']) . '</span>';
            }
        }
        
        $categories_html = '';
        if (!empty($business_data['categories'])) {
            $category_names = wp_list_pluck($business_data['categories'], 'name');
            $categories_html = '<div class="p116-business-categories">' . esc_html(implode(', ', $category_names)) . '</div>';
        }
        
        $owners_html = '';
        if (!empty($business_data['owners'])) {
            $owner_names = array();
            foreach ($business_data['owners'] as $owner) {
                if (!empty($owner['owner_name'])) {
                    $owner_names[] = esc_html($owner['owner_name']);
                }
            }
            if (!empty($owner_names)) {
                $owners_html = '<div class="p116-business-owners">' . __('Owners:', 'post116-business-directory') . ' ' . implode(', ', $owner_names) . '</div>';
            }
        }
        
        $contact_html = '';
        if (!empty($business_data['contact']['phone'])) {
            $contact_html .= '<div class="p116-business-phone"><i class="dashicons dashicons-phone"></i> ' . esc_html($business_data['contact']['phone']) . '</div>';
        }
        
        $city = $business_data['address']['city'] ?? '';
        if (!empty($city)) {
            $contact_html .= '<div class="p116-business-city"><i class="dashicons dashicons-location-alt"></i> ' . esc_html($city) . '</div>';
        }
        
        $services = $business_data['services'] ?? '';
        $services_html = '';
        if (!empty($services) && $show_excerpt) {
            $services_html = '<div class="p116-business-services">' . esc_html($services) . '</div>';
        }
        
        ob_start();
        ?>
        <article class="p116-business-card">
            <div class="p116-business-header">
                <?php if (!empty($business_data['thumbnail'])): ?>
                    <div class="p116-business-thumbnail">
                        <img src="<?php echo esc_url($business_data['thumbnail']); ?>" alt="<?php echo esc_attr($business_data['title']); ?>">
                    </div>
                <?php endif; ?>
                <div class="p116-business-title-wrapper">
                    <h3 class="p116-business-title">
                        <a href="<?php echo esc_url($business_data['url']); ?>">
                            <?php echo esc_html($business_data['title']); ?>
                        </a>
                    </h3>
                    <?php if (!empty($flags_html)): ?>
                        <div class="p116-business-flags"><?php echo $flags_html; ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php echo $categories_html; ?>
            <?php echo $owners_html; ?>
            
            <div class="p116-business-contact">
                <?php echo $contact_html; ?>
            </div>
            
            <?php echo $services_html; ?>
        </article>
        <?php
        return ob_get_clean();
    }
}

function p116_get_business_data($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $owners = get_post_meta($post_id, 'owners', true);
    $owners_data = $owners ? json_decode($owners, true) : array();
    
    $links = get_post_meta($post_id, 'links', true);
    $links_data = $links ? json_decode($links, true) : array();
    
    $terms = get_the_terms($post_id, 'p116_business_category');
    $categories = array();
    if ($terms && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $categories[] = array(
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug
            );
        }
    }
    
    $flags = array();
    if (get_post_meta($post_id, 'veteran_owned', true)) {
        $flags[] = array('type' => 'veteran', 'label' => __('Veteran Owned', 'post116-business-directory'));
    }
    if (get_post_meta($post_id, 'sons_owned', true)) {
        $flags[] = array('type' => 'sons', 'label' => __('SAL Owned', 'post116-business-directory'));
    }
    if (get_post_meta($post_id, 'auxiliary_owned', true)) {
        $flags[] = array('type' => 'auxiliary', 'label' => __('Auxiliary Owned', 'post116-business-directory'));
    }
    
    return array(
        'id' => $post_id,
        'title' => get_the_title($post_id),
        'slug' => get_post_field('post_name', $post_id),
        'url' => get_permalink($post_id),
        'excerpt' => get_the_excerpt($post_id),
        'thumbnail' => get_the_post_thumbnail_url($post_id, 'medium'),
        'content' => get_post_field('post_content', $post_id),
        'owners' => $owners_data,
        'contact' => array(
            'phone' => get_post_meta($post_id, 'business_phone', true),
            'email' => get_post_meta($post_id, 'business_email', true),
            'website' => get_post_meta($post_id, 'website_url', true)
        ),
        'address' => array(
            'city' => get_post_meta($post_id, 'city', true),
            'address1' => get_post_meta($post_id, 'address1', true),
            'address2' => get_post_meta($post_id, 'address2', true),
            'state' => get_post_meta($post_id, 'state', true),
            'postal_code' => get_post_meta($post_id, 'postal_code', true)
        ),
        'services' => get_post_meta($post_id, 'services_offered', true),
        'links' => $links_data,
        'flags' => $flags,
        'categories' => $categories
    );
}