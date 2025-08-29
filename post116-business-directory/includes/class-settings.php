<?php

if (!defined('ABSPATH')) {
    exit;
}

class P116_Settings {
    
    private $options;
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'init_settings'));
    }
    
    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=p116_business',
            __('Directory Settings', 'post116-business-directory'),
            __('Settings', 'post116-business-directory'),
            'manage_options',
            'p116-directory-settings',
            array($this, 'render_settings_page')
        );
    }
    
    public function init_settings() {
        register_setting(
            'p116_directory_settings',
            'p116_directory_options',
            array($this, 'sanitize_options')
        );
        
        add_settings_section(
            'p116_directory_general',
            __('General Settings', 'post116-business-directory'),
            array($this, 'render_general_section'),
            'p116-directory-settings'
        );
        
        add_settings_field(
            'directory_page',
            __('Directory Page', 'post116-business-directory'),
            array($this, 'render_directory_page_field'),
            'p116-directory-settings',
            'p116_directory_general'
        );
        
        add_settings_field(
            'show_ownership_flags',
            __('Show Ownership Flags', 'post116-business-directory'),
            array($this, 'render_show_flags_field'),
            'p116-directory-settings',
            'p116_directory_general'
        );
        
        add_settings_field(
            'businesses_per_page',
            __('Businesses Per Page', 'post116-business-directory'),
            array($this, 'render_per_page_field'),
            'p116-directory-settings',
            'p116_directory_general'
        );
        
        add_settings_section(
            'p116_directory_display',
            __('Display Settings', 'post116-business-directory'),
            array($this, 'render_display_section'),
            'p116-directory-settings'
        );
        
        add_settings_field(
            'primary_color',
            __('Primary Color', 'post116-business-directory'),
            array($this, 'render_primary_color_field'),
            'p116-directory-settings',
            'p116_directory_display'
        );
        
        add_settings_field(
            'secondary_color',
            __('Secondary Color', 'post116-business-directory'),
            array($this, 'render_secondary_color_field'),
            'p116-directory-settings',
            'p116_directory_display'
        );
        
        add_settings_field(
            'accent_color',
            __('Accent Color', 'post116-business-directory'),
            array($this, 'render_accent_color_field'),
            'p116-directory-settings',
            'p116_directory_display'
        );
        
        add_settings_section(
            'p116_directory_advanced',
            __('Advanced Settings', 'post116-business-directory'),
            array($this, 'render_advanced_section'),
            'p116-directory-settings'
        );
        
        add_settings_field(
            'enable_map_view',
            __('Enable Map View', 'post116-business-directory'),
            array($this, 'render_map_field'),
            'p116-directory-settings',
            'p116_directory_advanced'
        );
        
        add_settings_field(
            'custom_css',
            __('Custom CSS', 'post116-business-directory'),
            array($this, 'render_custom_css_field'),
            'p116-directory-settings',
            'p116_directory_advanced'
        );
    }
    
    public function get_options() {
        if ($this->options === null) {
            $defaults = array(
                'directory_page' => '',
                'show_ownership_flags' => true,
                'businesses_per_page' => 20,
                'primary_color' => '#c41e3a',
                'secondary_color' => '#003366',
                'accent_color' => '#ffd700',
                'enable_map_view' => false,
                'custom_css' => ''
            );
            
            $saved_options = get_option('p116_directory_options', array());
            $this->options = wp_parse_args($saved_options, $defaults);
        }
        
        return $this->options;
    }
    
    public function sanitize_options($input) {
        $sanitized = array();
        $options = $this->get_options();
        
        $sanitized['directory_page'] = absint($input['directory_page'] ?? 0);
        $sanitized['show_ownership_flags'] = !empty($input['show_ownership_flags']);
        $sanitized['businesses_per_page'] = max(5, min(100, absint($input['businesses_per_page'] ?? 20)));
        $sanitized['primary_color'] = sanitize_hex_color($input['primary_color'] ?? $options['primary_color']);
        $sanitized['secondary_color'] = sanitize_hex_color($input['secondary_color'] ?? $options['secondary_color']);
        $sanitized['accent_color'] = sanitize_hex_color($input['accent_color'] ?? $options['accent_color']);
        $sanitized['enable_map_view'] = !empty($input['enable_map_view']);
        $sanitized['custom_css'] = wp_strip_all_tags($input['custom_css'] ?? '');
        
        return $sanitized;
    }
    
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Directory Settings', 'post116-business-directory'); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('p116_directory_settings');
                do_settings_sections('p116-directory-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    public function render_general_section() {
        echo '<p>' . esc_html__('Configure general settings for the business directory.', 'post116-business-directory') . '</p>';
    }
    
    public function render_display_section() {
        echo '<p>' . esc_html__('Customize the appearance of the business directory to match your site.', 'post116-business-directory') . '</p>';
    }
    
    public function render_advanced_section() {
        echo '<p>' . esc_html__('Advanced settings for power users. Use with caution.', 'post116-business-directory') . '</p>';
    }
    
    public function render_directory_page_field() {
        $options = $this->get_options();
        $pages = get_pages();
        
        echo '<select name="p116_directory_options[directory_page]" id="directory_page">';
        echo '<option value="">' . esc_html__('Select a page', 'post116-business-directory') . '</option>';
        
        foreach ($pages as $page) {
            $selected = selected($options['directory_page'], $page->ID, false);
            echo '<option value="' . esc_attr($page->ID) . '"' . $selected . '>';
            echo esc_html($page->post_title);
            echo '</option>';
        }
        
        echo '</select>';
        echo '<p class="description">' . esc_html__('Select the page that contains the directory block.', 'post116-business-directory') . '</p>';
    }
    
    public function render_show_flags_field() {
        $options = $this->get_options();
        
        echo '<label>';
        echo '<input type="checkbox" name="p116_directory_options[show_ownership_flags]" value="1" ' . checked($options['show_ownership_flags'], true, false) . '>';
        echo ' ' . esc_html__('Show veteran, SAL, and auxiliary ownership filters', 'post116-business-directory');
        echo '</label>';
    }
    
    public function render_per_page_field() {
        $options = $this->get_options();
        
        echo '<input type="number" name="p116_directory_options[businesses_per_page]" value="' . esc_attr($options['businesses_per_page']) . '" min="5" max="100" class="small-text">';
        echo '<p class="description">' . esc_html__('Number of businesses to show per page (5-100).', 'post116-business-directory') . '</p>';
    }
    
    public function render_primary_color_field() {
        $options = $this->get_options();
        
        echo '<input type="color" name="p116_directory_options[primary_color]" value="' . esc_attr($options['primary_color']) . '" class="color-field">';
        echo '<p class="description">' . esc_html__('Main accent color for buttons and highlights (default: Legion red).', 'post116-business-directory') . '</p>';
    }
    
    public function render_secondary_color_field() {
        $options = $this->get_options();
        
        echo '<input type="color" name="p116_directory_options[secondary_color]" value="' . esc_attr($options['secondary_color']) . '" class="color-field">';
        echo '<p class="description">' . esc_html__('Secondary color for text and borders (default: Navy blue).', 'post116-business-directory') . '</p>';
    }
    
    public function render_accent_color_field() {
        $options = $this->get_options();
        
        echo '<input type="color" name="p116_directory_options[accent_color]" value="' . esc_attr($options['accent_color']) . '" class="color-field">';
        echo '<p class="description">' . esc_html__('Accent color for auxiliary flags and special elements (default: Gold).', 'post116-business-directory') . '</p>';
    }
    
    public function render_map_field() {
        $options = $this->get_options();
        
        echo '<label>';
        echo '<input type="checkbox" name="p116_directory_options[enable_map_view]" value="1" ' . checked($options['enable_map_view'], true, false) . '>';
        echo ' ' . esc_html__('Enable map view for businesses (Phase 2 feature)', 'post116-business-directory');
        echo '</label>';
        echo '<p class="description">' . esc_html__('This feature is planned for a future release.', 'post116-business-directory') . '</p>';
    }
    
    public function render_custom_css_field() {
        $options = $this->get_options();
        
        echo '<textarea name="p116_directory_options[custom_css]" rows="10" cols="50" class="large-text code">';
        echo esc_textarea($options['custom_css']);
        echo '</textarea>';
        echo '<p class="description">' . esc_html__('Add custom CSS to override default styles. Use CSS custom properties for colors.', 'post116-business-directory') . '</p>';
        
        echo '<div class="custom-css-help" style="margin-top: 15px; padding: 15px; background: #f1f1f1; border-radius: 4px;">';
        echo '<strong>' . esc_html__('Available CSS Custom Properties:', 'post116-business-directory') . '</strong><br>';
        echo '<code>--p116-primary-color</code>, <code>--p116-secondary-color</code>, <code>--p116-accent-color</code><br>';
        echo '<code>--p116-text-color</code>, <code>--p116-light-bg</code>, <code>--p116-border-color</code>, <code>--p116-shadow</code>';
        echo '</div>';
    }
}