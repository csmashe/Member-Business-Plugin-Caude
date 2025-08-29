<?php
/**
 * Basic functionality tests for Post 116 Business Directory
 * 
 * These are simple tests to verify core functionality works.
 * For full testing, integrate with PHPUnit or similar framework.
 */

if (!defined('ABSPATH')) {
    exit;
}

class P116_Basic_Tests {
    
    private $errors = array();
    private $passed = 0;
    private $total = 0;
    
    public function run_tests() {
        echo '<div class="wrap">';
        echo '<h1>Post 116 Business Directory - Basic Tests</h1>';
        
        $this->test_post_type_registration();
        $this->test_taxonomy_registration();
        $this->test_meta_field_registration();
        $this->test_rest_api_endpoints();
        $this->test_capabilities();
        $this->test_template_functions();
        
        $this->display_results();
        echo '</div>';
    }
    
    private function test_post_type_registration() {
        $this->total++;
        
        if (post_type_exists('p116_business')) {
            $this->passed++;
            $this->log_success('Custom post type p116_business is registered');
        } else {
            $this->log_error('Custom post type p116_business is not registered');
        }
        
        // Test post type properties
        $post_type = get_post_type_object('p116_business');
        
        $this->total++;
        if ($post_type && $post_type->public) {
            $this->passed++;
            $this->log_success('Post type is public');
        } else {
            $this->log_error('Post type is not public');
        }
        
        $this->total++;
        if ($post_type && $post_type->has_archive) {
            $this->passed++;
            $this->log_success('Post type has archive');
        } else {
            $this->log_error('Post type does not have archive');
        }
    }
    
    private function test_taxonomy_registration() {
        $this->total++;
        
        if (taxonomy_exists('p116_business_category')) {
            $this->passed++;
            $this->log_success('Taxonomy p116_business_category is registered');
        } else {
            $this->log_error('Taxonomy p116_business_category is not registered');
        }
        
        // Test taxonomy properties
        $taxonomy = get_taxonomy('p116_business_category');
        
        $this->total++;
        if ($taxonomy && $taxonomy->hierarchical) {
            $this->passed++;
            $this->log_success('Taxonomy is hierarchical');
        } else {
            $this->log_error('Taxonomy is not hierarchical');
        }
    }
    
    private function test_meta_field_registration() {
        $meta_keys = array(
            'owners', 'business_phone', 'business_email', 'website_url',
            'city', 'veteran_owned', 'sons_owned', 'auxiliary_owned',
            'services_offered', 'show_in_directory'
        );
        
        foreach ($meta_keys as $key) {
            $this->total++;
            
            if (registered_meta_key_exists('post', $key, 'p116_business')) {
                $this->passed++;
                $this->log_success("Meta field '{$key}' is registered");
            } else {
                $this->log_error("Meta field '{$key}' is not registered");
            }
        }
    }
    
    private function test_rest_api_endpoints() {
        $this->total++;
        
        $server = rest_get_server();
        $routes = $server->get_routes();
        
        if (isset($routes['/p116/v1/search'])) {
            $this->passed++;
            $this->log_success('REST API search endpoint is registered');
        } else {
            $this->log_error('REST API search endpoint is not registered');
        }
        
        $this->total++;
        if (isset($routes['/p116/v1/autocomplete'])) {
            $this->passed++;
            $this->log_success('REST API autocomplete endpoint is registered');
        } else {
            $this->log_error('REST API autocomplete endpoint is not registered');
        }
    }
    
    private function test_capabilities() {
        $caps_to_test = array(
            'edit_businesses',
            'read_businesses',
            'delete_businesses',
            'manage_business_categories'
        );
        
        $admin = get_role('administrator');
        
        foreach ($caps_to_test as $cap) {
            $this->total++;
            
            if ($admin && $admin->has_cap($cap)) {
                $this->passed++;
                $this->log_success("Administrator has '{$cap}' capability");
            } else {
                $this->log_error("Administrator does not have '{$cap}' capability");
            }
        }
    }
    
    private function test_template_functions() {
        $this->total++;
        
        if (function_exists('p116_get_business_data')) {
            $this->passed++;
            $this->log_success('Template function p116_get_business_data exists');
        } else {
            $this->log_error('Template function p116_get_business_data does not exist');
        }
        
        $this->total++;
        if (class_exists('P116_Templates')) {
            $this->passed++;
            $this->log_success('P116_Templates class exists');
            
            $this->total++;
            if (method_exists('P116_Templates', 'render_business_card')) {
                $this->passed++;
                $this->log_success('P116_Templates::render_business_card method exists');
            } else {
                $this->log_error('P116_Templates::render_business_card method does not exist');
            }
        } else {
            $this->log_error('P116_Templates class does not exist');
            $this->total++;
        }
    }
    
    private function log_success($message) {
        echo '<div style="color: green; margin: 5px 0;">✓ ' . esc_html($message) . '</div>';
    }
    
    private function log_error($message) {
        $this->errors[] = $message;
        echo '<div style="color: red; margin: 5px 0;">✗ ' . esc_html($message) . '</div>';
    }
    
    private function display_results() {
        echo '<hr>';
        echo '<h2>Test Results</h2>';
        
        $percentage = $this->total > 0 ? round(($this->passed / $this->total) * 100) : 0;
        
        echo '<p><strong>Tests Passed:</strong> ' . $this->passed . ' / ' . $this->total . ' (' . $percentage . '%)</p>';
        
        if (empty($this->errors)) {
            echo '<div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 20px 0;">';
            echo '<strong>All tests passed!</strong> The plugin appears to be working correctly.';
            echo '</div>';
        } else {
            echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 20px 0;">';
            echo '<strong>Some tests failed.</strong> Please check the error messages above and ensure all plugin files are properly uploaded.';
            echo '</div>';
        }
        
        echo '<p><em>Note: These are basic functionality tests. For comprehensive testing, use PHPUnit or manual testing.</em></p>';
    }
}

// Add admin page for running tests
add_action('admin_menu', function() {
    add_submenu_page(
        'edit.php?post_type=p116_business',
        'Plugin Tests',
        'Tests',
        'manage_options',
        'p116-tests',
        function() {
            $tests = new P116_Basic_Tests();
            $tests->run_tests();
        }
    );
});