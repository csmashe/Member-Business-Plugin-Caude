<?php
/**
 * Plugin Name: Post 116 Business Directory
 * Plugin URI: https://alpost116nc.org
 * Description: Directory plugin for American Legion Post 116 family-owned businesses and services.
 * Version: 1.0.0
 * Author: American Legion Post 116
 * License: GPL v2 or later
 * Text Domain: post116-business-directory
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('P116_PLUGIN_URL', plugin_dir_url(__FILE__));
define('P116_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('P116_PLUGIN_VERSION', '1.0.0');

class Post116_Business_Directory {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        $this->load_textdomain();
        $this->includes();
        $this->register_post_type();
        $this->register_taxonomy();
        $this->register_meta_fields();
        
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        add_action('init', array($this, 'register_blocks'));
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('post116-business-directory', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function includes() {
        require_once P116_PLUGIN_PATH . 'includes/class-meta-boxes.php';
        require_once P116_PLUGIN_PATH . 'includes/class-rest-api.php';
        require_once P116_PLUGIN_PATH . 'includes/class-templates.php';
        require_once P116_PLUGIN_PATH . 'includes/class-settings.php';
        require_once P116_PLUGIN_PATH . 'includes/class-schema.php';
        
        new P116_Meta_Boxes();
        new P116_REST_API();
        new P116_Templates();
        new P116_Settings();
        new P116_Schema();
        
        // Load tests in development
        if (defined('WP_DEBUG') && WP_DEBUG) {
            require_once P116_PLUGIN_PATH . 'tests/test-basic-functionality.php';
        }
    }
    
    public function register_post_type() {
        $labels = array(
            'name' => __('Businesses', 'post116-business-directory'),
            'singular_name' => __('Business', 'post116-business-directory'),
            'menu_name' => __('Business Directory', 'post116-business-directory'),
            'add_new' => __('Add New Business', 'post116-business-directory'),
            'add_new_item' => __('Add New Business', 'post116-business-directory'),
            'edit_item' => __('Edit Business', 'post116-business-directory'),
            'new_item' => __('New Business', 'post116-business-directory'),
            'view_item' => __('View Business', 'post116-business-directory'),
            'search_items' => __('Search Businesses', 'post116-business-directory'),
            'not_found' => __('No businesses found', 'post116-business-directory'),
            'not_found_in_trash' => __('No businesses found in trash', 'post116-business-directory')
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'directory', 'with_front' => false),
            'capability_type' => array('business', 'businesses'),
            'map_meta_cap' => true,
            'has_archive' => 'directory',
            'hierarchical' => false,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-building',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'),
            'taxonomies' => array('p116_business_category')
        );
        
        register_post_type('p116_business', $args);
    }
    
    public function register_taxonomy() {
        $labels = array(
            'name' => __('Business Categories', 'post116-business-directory'),
            'singular_name' => __('Business Category', 'post116-business-directory'),
            'search_items' => __('Search Categories', 'post116-business-directory'),
            'all_items' => __('All Categories', 'post116-business-directory'),
            'parent_item' => __('Parent Category', 'post116-business-directory'),
            'parent_item_colon' => __('Parent Category:', 'post116-business-directory'),
            'edit_item' => __('Edit Category', 'post116-business-directory'),
            'update_item' => __('Update Category', 'post116-business-directory'),
            'add_new_item' => __('Add New Category', 'post116-business-directory'),
            'new_item_name' => __('New Category Name', 'post116-business-directory'),
            'menu_name' => __('Categories', 'post116-business-directory'),
        );
        
        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'directory/category', 'with_front' => false),
            'capabilities' => array(
                'manage_terms' => 'manage_business_categories',
                'edit_terms' => 'manage_business_categories',
                'delete_terms' => 'manage_business_categories',
                'assign_terms' => 'edit_businesses'
            )
        );
        
        register_taxonomy('p116_business_category', array('p116_business'), $args);
    }
    
    public function register_meta_fields() {
        $meta_fields = array(
            'owners', 'business_phone', 'business_email', 'website_url',
            'city', 'address1', 'address2', 'state', 'postal_code',
            'veteran_owned', 'sons_owned', 'auxiliary_owned',
            'links', 'services_offered', 'show_in_directory',
            'owners_search', 'city_search'
        );
        
        foreach ($meta_fields as $field) {
            register_post_meta('p116_business', $field, array(
                'show_in_rest' => true,
                'single' => true,
                'type' => in_array($field, array('veteran_owned', 'sons_owned', 'auxiliary_owned', 'show_in_directory')) ? 'boolean' : 'string',
                'sanitize_callback' => array($this, 'sanitize_meta_field'),
                'auth_callback' => function() {
                    return current_user_can('edit_businesses');
                }
            ));
        }
    }
    
    public function sanitize_meta_field($value, $meta_key) {
        switch ($meta_key) {
            case 'business_email':
                return sanitize_email($value);
            case 'website_url':
                return esc_url_raw($value);
            case 'business_phone':
                return sanitize_text_field($value);
            case 'owners':
            case 'links':
                return is_array($value) ? array_map('sanitize_text_field', $value) : sanitize_text_field($value);
            case 'veteran_owned':
            case 'sons_owned':
            case 'auxiliary_owned':
            case 'show_in_directory':
                return (bool) $value;
            default:
                return sanitize_text_field($value);
        }
    }
    
    public function register_rest_routes() {
        // This will be handled by the REST API class
    }
    
    public function register_blocks() {
        register_block_type(P116_PLUGIN_PATH . 'blocks/directory');
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('p116-directory', P116_PLUGIN_URL . 'public/css/directory.css', array(), P116_PLUGIN_VERSION);
        wp_enqueue_script('p116-directory', P116_PLUGIN_URL . 'public/js/directory.js', array('jquery'), P116_PLUGIN_VERSION, true);
        
        wp_localize_script('p116-directory', 'p116_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => rest_url('p116/v1/'),
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }
    
    public function admin_enqueue_scripts($hook) {
        global $post_type;
        
        if ($post_type === 'p116_business') {
            wp_enqueue_script('p116-admin', P116_PLUGIN_URL . 'public/js/admin.js', array('jquery', 'jquery-ui-sortable'), P116_PLUGIN_VERSION, true);
            wp_enqueue_style('p116-admin', P116_PLUGIN_URL . 'public/css/admin.css', array(), P116_PLUGIN_VERSION);
        }
    }
    
    public function activate() {
        $this->create_capabilities();
        $this->create_directory_page();
        $this->add_meta_indexes();
        flush_rewrite_rules();
    }
    
    private function create_capabilities() {
        $roles = array('administrator', 'editor', 'author', 'contributor');
        
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                $role->add_cap('read_business');
                $role->add_cap('read_businesses');
                $role->add_cap('edit_business');
                $role->add_cap('edit_businesses');
                $role->add_cap('edit_others_businesses');
                $role->add_cap('edit_published_businesses');
                $role->add_cap('publish_businesses');
                $role->add_cap('delete_business');
                $role->add_cap('delete_businesses');
                $role->add_cap('delete_others_businesses');
                $role->add_cap('delete_published_businesses');
                $role->add_cap('manage_business_categories');
            }
        }
    }
    
    private function create_directory_page() {
        $page_exists = get_page_by_path('directory');
        if (!$page_exists) {
            $page_data = array(
                'post_title' => __('Business Directory', 'post116-business-directory'),
                'post_content' => '<!-- wp:p116/directory --><!-- /wp:p116/directory -->',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_slug' => 'directory'
            );
            wp_insert_post($page_data);
        }
    }
    
    private function add_meta_indexes() {
        global $wpdb;
        
        $wpdb->query("CREATE INDEX IF NOT EXISTS idx_owners_search ON {$wpdb->postmeta} (meta_key, meta_value(191)) WHERE meta_key = 'owners_search'");
        $wpdb->query("CREATE INDEX IF NOT EXISTS idx_city_search ON {$wpdb->postmeta} (meta_key, meta_value(191)) WHERE meta_key = 'city_search'");
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
}

new Post116_Business_Directory();