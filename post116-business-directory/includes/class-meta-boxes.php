<?php

if (!defined('ABSPATH')) {
    exit;
}

class P116_Meta_Boxes {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_business_meta'));
        add_filter('manage_p116_business_posts_columns', array($this, 'add_admin_columns'));
        add_action('manage_p116_business_posts_custom_column', array($this, 'display_admin_columns'), 10, 2);
        add_action('restrict_manage_posts', array($this, 'add_admin_filters'));
        add_filter('parse_query', array($this, 'filter_admin_posts'));
    }
    
    public function add_meta_boxes() {
        add_meta_box(
            'p116_business_owners',
            __('Business Owners', 'post116-business-directory'),
            array($this, 'render_owners_meta_box'),
            'p116_business',
            'normal',
            'high'
        );
        
        add_meta_box(
            'p116_business_contact',
            __('Contact Information', 'post116-business-directory'),
            array($this, 'render_contact_meta_box'),
            'p116_business',
            'normal',
            'high'
        );
        
        add_meta_box(
            'p116_business_address',
            __('Address', 'post116-business-directory'),
            array($this, 'render_address_meta_box'),
            'p116_business',
            'normal',
            'high'
        );
        
        add_meta_box(
            'p116_business_flags',
            __('Ownership Flags', 'post116-business-directory'),
            array($this, 'render_flags_meta_box'),
            'p116_business',
            'side',
            'default'
        );
        
        add_meta_box(
            'p116_business_services',
            __('Services Offered', 'post116-business-directory'),
            array($this, 'render_services_meta_box'),
            'p116_business',
            'normal',
            'default'
        );
        
        add_meta_box(
            'p116_business_links',
            __('Additional Links', 'post116-business-directory'),
            array($this, 'render_links_meta_box'),
            'p116_business',
            'normal',
            'default'
        );
        
        add_meta_box(
            'p116_business_settings',
            __('Directory Settings', 'post116-business-directory'),
            array($this, 'render_settings_meta_box'),
            'p116_business',
            'side',
            'default'
        );
    }
    
    public function render_owners_meta_box($post) {
        wp_nonce_field('p116_business_meta', 'p116_business_meta_nonce');
        
        $owners = get_post_meta($post->ID, 'owners', true);
        $owners = $owners ? json_decode($owners, true) : array(array());
        
        echo '<div id="p116-owners-container">';
        echo '<div class="p116-owners-wrapper">';
        
        foreach ($owners as $index => $owner) {
            $this->render_owner_fields($index, $owner);
        }
        
        echo '</div>';
        echo '<button type="button" class="button p116-add-owner">' . __('Add Owner', 'post116-business-directory') . '</button>';
        echo '</div>';
        
        echo '<script type="text/template" id="p116-owner-template">';
        $this->render_owner_fields('{{INDEX}}', array());
        echo '</script>';
    }
    
    private function render_owner_fields($index, $owner) {
        $owner = wp_parse_args($owner, array(
            'owner_name' => '',
            'owner_role' => '',
            'owner_email' => '',
            'owner_phone' => '',
            'owner_website' => ''
        ));
        
        echo '<div class="p116-owner-group" data-index="' . $index . '">';
        echo '<div class="p116-owner-header">';
        echo '<h4>' . __('Owner', 'post116-business-directory') . ' <span class="owner-number">' . ($index + 1) . '</span></h4>';
        echo '<button type="button" class="button-link p116-remove-owner" aria-label="' . __('Remove Owner', 'post116-business-directory') . '">×</button>';
        echo '</div>';
        
        echo '<table class="form-table">';
        
        echo '<tr>';
        echo '<th><label for="owner_name_' . $index . '">' . __('Name', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="text" id="owner_name_' . $index . '" name="owners[' . $index . '][owner_name]" value="' . esc_attr($owner['owner_name']) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="owner_role_' . $index . '">' . __('Role/Title', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="text" id="owner_role_' . $index . '" name="owners[' . $index . '][owner_role]" value="' . esc_attr($owner['owner_role']) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="owner_email_' . $index . '">' . __('Email', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="email" id="owner_email_' . $index . '" name="owners[' . $index . '][owner_email]" value="' . esc_attr($owner['owner_email']) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="owner_phone_' . $index . '">' . __('Phone', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="tel" id="owner_phone_' . $index . '" name="owners[' . $index . '][owner_phone]" value="' . esc_attr($owner['owner_phone']) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="owner_website_' . $index . '">' . __('Website', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="url" id="owner_website_' . $index . '" name="owners[' . $index . '][owner_website]" value="' . esc_attr($owner['owner_website']) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '</table>';
        echo '</div>';
    }
    
    public function render_contact_meta_box($post) {
        $business_phone = get_post_meta($post->ID, 'business_phone', true);
        $business_email = get_post_meta($post->ID, 'business_email', true);
        $website_url = get_post_meta($post->ID, 'website_url', true);
        
        echo '<table class="form-table">';
        
        echo '<tr>';
        echo '<th><label for="business_phone">' . __('Business Phone', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="tel" id="business_phone" name="business_phone" value="' . esc_attr($business_phone) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="business_email">' . __('Business Email', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="email" id="business_email" name="business_email" value="' . esc_attr($business_email) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="website_url">' . __('Website URL', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="url" id="website_url" name="website_url" value="' . esc_attr($website_url) . '" class="regular-text" placeholder="https://" /></td>';
        echo '</tr>';
        
        echo '</table>';
    }
    
    public function render_address_meta_box($post) {
        $city = get_post_meta($post->ID, 'city', true);
        $address1 = get_post_meta($post->ID, 'address1', true);
        $address2 = get_post_meta($post->ID, 'address2', true);
        $state = get_post_meta($post->ID, 'state', true);
        $postal_code = get_post_meta($post->ID, 'postal_code', true);
        
        echo '<table class="form-table">';
        
        echo '<tr>';
        echo '<th><label for="city">' . __('City', 'post116-business-directory') . ' <span class="required">*</span></label></th>';
        echo '<td><input type="text" id="city" name="city" value="' . esc_attr($city) . '" class="regular-text" required /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="address1">' . __('Address Line 1', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="text" id="address1" name="address1" value="' . esc_attr($address1) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="address2">' . __('Address Line 2', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="text" id="address2" name="address2" value="' . esc_attr($address2) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="state">' . __('State', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="text" id="state" name="state" value="' . esc_attr($state) . '" class="regular-text" maxlength="2" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="postal_code">' . __('Postal Code', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="text" id="postal_code" name="postal_code" value="' . esc_attr($postal_code) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '</table>';
    }
    
    public function render_flags_meta_box($post) {
        $veteran_owned = get_post_meta($post->ID, 'veteran_owned', true);
        $sons_owned = get_post_meta($post->ID, 'sons_owned', true);
        $auxiliary_owned = get_post_meta($post->ID, 'auxiliary_owned', true);
        
        echo '<p>';
        echo '<label><input type="checkbox" name="veteran_owned" value="1" ' . checked($veteran_owned, 1, false) . ' /> ' . __('Veteran Owned', 'post116-business-directory') . '</label>';
        echo '</p>';
        
        echo '<p>';
        echo '<label><input type="checkbox" name="sons_owned" value="1" ' . checked($sons_owned, 1, false) . ' /> ' . __('Sons of American Legion Owned', 'post116-business-directory') . '</label>';
        echo '</p>';
        
        echo '<p>';
        echo '<label><input type="checkbox" name="auxiliary_owned" value="1" ' . checked($auxiliary_owned, 1, false) . ' /> ' . __('Auxiliary Owned', 'post116-business-directory') . '</label>';
        echo '</p>';
    }
    
    public function render_services_meta_box($post) {
        $services_offered = get_post_meta($post->ID, 'services_offered', true);
        
        echo '<p>';
        echo '<label for="services_offered">' . __('Brief description of services offered (used in listings)', 'post116-business-directory') . '</label>';
        echo '</p>';
        echo '<textarea id="services_offered" name="services_offered" rows="3" class="large-text">' . esc_textarea($services_offered) . '</textarea>';
    }
    
    public function render_links_meta_box($post) {
        $links = get_post_meta($post->ID, 'links', true);
        $links = $links ? json_decode($links, true) : array(array());
        
        echo '<div id="p116-links-container">';
        echo '<div class="p116-links-wrapper">';
        
        foreach ($links as $index => $link) {
            $this->render_link_fields($index, $link);
        }
        
        echo '</div>';
        echo '<button type="button" class="button p116-add-link">' . __('Add Link', 'post116-business-directory') . '</button>';
        echo '</div>';
        
        echo '<script type="text/template" id="p116-link-template">';
        $this->render_link_fields('{{INDEX}}', array());
        echo '</script>';
    }
    
    private function render_link_fields($index, $link) {
        $link = wp_parse_args($link, array(
            'link_label' => '',
            'link_url' => ''
        ));
        
        echo '<div class="p116-link-group" data-index="' . $index . '">';
        echo '<div class="p116-link-handle">☰</div>';
        echo '<input type="text" name="links[' . $index . '][link_label]" placeholder="' . __('Link Label', 'post116-business-directory') . '" value="' . esc_attr($link['link_label']) . '" class="regular-text" />';
        echo '<input type="url" name="links[' . $index . '][link_url]" placeholder="https://" value="' . esc_attr($link['link_url']) . '" class="regular-text" />';
        echo '<button type="button" class="button-link p116-remove-link" aria-label="' . __('Remove Link', 'post116-business-directory') . '">×</button>';
        echo '</div>';
    }
    
    public function render_settings_meta_box($post) {
        $show_in_directory = get_post_meta($post->ID, 'show_in_directory', true);
        if ($show_in_directory === '') {
            $show_in_directory = 1;
        }
        
        echo '<p>';
        echo '<label><input type="checkbox" name="show_in_directory" value="1" ' . checked($show_in_directory, 1, false) . ' /> ' . __('Show in Directory', 'post116-business-directory') . '</label>';
        echo '</p>';
    }
    
    public function save_business_meta($post_id) {
        if (!isset($_POST['p116_business_meta_nonce']) || !wp_verify_nonce($_POST['p116_business_meta_nonce'], 'p116_business_meta')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (get_post_type($post_id) !== 'p116_business') {
            return;
        }
        
        $city = sanitize_text_field($_POST['city'] ?? '');
        if (empty($city)) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>' . __('City is required for businesses.', 'post116-business-directory') . '</p></div>';
            });
            return;
        }
        
        $meta_fields = array(
            'business_phone' => 'sanitize_text_field',
            'business_email' => 'sanitize_email',
            'website_url' => 'esc_url_raw',
            'city' => 'sanitize_text_field',
            'address1' => 'sanitize_text_field',
            'address2' => 'sanitize_text_field',
            'state' => 'sanitize_text_field',
            'postal_code' => 'sanitize_text_field',
            'veteran_owned' => 'intval',
            'sons_owned' => 'intval',
            'auxiliary_owned' => 'intval',
            'services_offered' => 'sanitize_textarea_field',
            'show_in_directory' => 'intval'
        );
        
        foreach ($meta_fields as $field => $sanitize_func) {
            $value = $_POST[$field] ?? '';
            if ($sanitize_func === 'intval') {
                $value = $value ? 1 : 0;
            } else {
                $value = call_user_func($sanitize_func, $value);
            }
            update_post_meta($post_id, $field, $value);
        }
        
        if (isset($_POST['owners']) && is_array($_POST['owners'])) {
            $owners = array();
            $owners_search_parts = array();
            
            foreach ($_POST['owners'] as $owner) {
                if (!empty($owner['owner_name'])) {
                    $clean_owner = array(
                        'owner_name' => sanitize_text_field($owner['owner_name']),
                        'owner_role' => sanitize_text_field($owner['owner_role'] ?? ''),
                        'owner_email' => sanitize_email($owner['owner_email'] ?? ''),
                        'owner_phone' => sanitize_text_field($owner['owner_phone'] ?? ''),
                        'owner_website' => esc_url_raw($owner['owner_website'] ?? '')
                    );
                    $owners[] = $clean_owner;
                    $owners_search_parts[] = strtolower($clean_owner['owner_name']);
                }
            }
            
            update_post_meta($post_id, 'owners', json_encode($owners));
            update_post_meta($post_id, 'owners_search', implode(' ', $owners_search_parts));
        }
        
        if (isset($_POST['links']) && is_array($_POST['links'])) {
            $links = array();
            
            foreach ($_POST['links'] as $link) {
                if (!empty($link['link_label']) && !empty($link['link_url'])) {
                    $links[] = array(
                        'link_label' => sanitize_text_field($link['link_label']),
                        'link_url' => esc_url_raw($link['link_url'])
                    );
                }
            }
            
            update_post_meta($post_id, 'links', json_encode($links));
        }
        
        update_post_meta($post_id, 'city_search', strtolower($city));
    }
    
    public function add_admin_columns($columns) {
        $new_columns = array();
        
        foreach ($columns as $key => $title) {
            $new_columns[$key] = $title;
            
            if ($key === 'title') {
                $new_columns['business_categories'] = __('Categories', 'post116-business-directory');
                $new_columns['business_owners'] = __('Owners', 'post116-business-directory');
                $new_columns['business_city'] = __('City', 'post116-business-directory');
                $new_columns['business_phone'] = __('Phone', 'post116-business-directory');
                $new_columns['business_flags'] = __('Flags', 'post116-business-directory');
            }
        }
        
        return $new_columns;
    }
    
    public function display_admin_columns($column, $post_id) {
        switch ($column) {
            case 'business_categories':
                $terms = get_the_terms($post_id, 'p116_business_category');
                if ($terms && !is_wp_error($terms)) {
                    $term_names = wp_list_pluck($terms, 'name');
                    echo implode(', ', $term_names);
                }
                break;
                
            case 'business_owners':
                $owners = get_post_meta($post_id, 'owners', true);
                if ($owners) {
                    $owners_data = json_decode($owners, true);
                    $owner_names = array();
                    
                    foreach ($owners_data as $index => $owner) {
                        if ($index < 2) {
                            $owner_names[] = $owner['owner_name'];
                        }
                    }
                    
                    echo implode(', ', $owner_names);
                    
                    if (count($owners_data) > 2) {
                        echo ' <em>(+' . (count($owners_data) - 2) . ' more)</em>';
                    }
                }
                break;
                
            case 'business_city':
                echo esc_html(get_post_meta($post_id, 'city', true));
                break;
                
            case 'business_phone':
                echo esc_html(get_post_meta($post_id, 'business_phone', true));
                break;
                
            case 'business_flags':
                $flags = array();
                if (get_post_meta($post_id, 'veteran_owned', true)) {
                    $flags[] = '<span class="p116-flag veteran">' . __('Veteran', 'post116-business-directory') . '</span>';
                }
                if (get_post_meta($post_id, 'sons_owned', true)) {
                    $flags[] = '<span class="p116-flag sons">' . __('SAL', 'post116-business-directory') . '</span>';
                }
                if (get_post_meta($post_id, 'auxiliary_owned', true)) {
                    $flags[] = '<span class="p116-flag auxiliary">' . __('AUX', 'post116-business-directory') . '</span>';
                }
                echo implode(' ', $flags);
                break;
        }
    }
    
    public function add_admin_filters() {
        global $typenow;
        
        if ($typenow === 'p116_business') {
            $categories = get_terms(array(
                'taxonomy' => 'p116_business_category',
                'hide_empty' => false
            ));
            
            if (!empty($categories)) {
                echo '<select name="business_category_filter">';
                echo '<option value="">' . __('All Categories', 'post116-business-directory') . '</option>';
                
                $selected = $_GET['business_category_filter'] ?? '';
                
                foreach ($categories as $category) {
                    printf(
                        '<option value="%s"%s>%s</option>',
                        esc_attr($category->slug),
                        selected($selected, $category->slug, false),
                        esc_html($category->name)
                    );
                }
                
                echo '</select>';
            }
            
            $flags = array(
                'veteran_owned' => __('Veteran Owned', 'post116-business-directory'),
                'sons_owned' => __('SAL Owned', 'post116-business-directory'),
                'auxiliary_owned' => __('Auxiliary Owned', 'post116-business-directory')
            );
            
            echo '<select name="ownership_flag_filter">';
            echo '<option value="">' . __('All Ownership Types', 'post116-business-directory') . '</option>';
            
            $selected_flag = $_GET['ownership_flag_filter'] ?? '';
            
            foreach ($flags as $flag => $label) {
                printf(
                    '<option value="%s"%s>%s</option>',
                    esc_attr($flag),
                    selected($selected_flag, $flag, false),
                    esc_html($label)
                );
            }
            
            echo '</select>';
        }
    }
    
    public function filter_admin_posts($query) {
        global $pagenow;
        
        if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'p116_business') {
            if (!empty($_GET['business_category_filter'])) {
                $query->set('tax_query', array(
                    array(
                        'taxonomy' => 'p116_business_category',
                        'field' => 'slug',
                        'terms' => sanitize_text_field($_GET['business_category_filter'])
                    )
                ));
            }
            
            if (!empty($_GET['ownership_flag_filter'])) {
                $flag = sanitize_text_field($_GET['ownership_flag_filter']);
                $query->set('meta_query', array(
                    array(
                        'key' => $flag,
                        'value' => '1',
                        'compare' => '='
                    )
                ));
            }
        }
    }
}