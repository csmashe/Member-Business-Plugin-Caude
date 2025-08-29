<?php

if (!defined('ABSPATH')) {
    exit;
}

class P116_REST_API {
    
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
    public function register_routes() {
        register_rest_route('p116/v1', '/search', array(
            'methods' => 'GET',
            'callback' => array($this, 'search_businesses'),
            'permission_callback' => '__return_true',
            'args' => array(
                'query' => array(
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                'category' => array(
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                'veteran_owned' => array(
                    'required' => false,
                    'type' => 'boolean'
                ),
                'sons_owned' => array(
                    'required' => false,
                    'type' => 'boolean'
                ),
                'auxiliary_owned' => array(
                    'required' => false,
                    'type' => 'boolean'
                ),
                'per_page' => array(
                    'required' => false,
                    'type' => 'integer',
                    'default' => 20,
                    'minimum' => 1,
                    'maximum' => 100
                ),
                'page' => array(
                    'required' => false,
                    'type' => 'integer',
                    'default' => 1,
                    'minimum' => 1
                )
            )
        ));
        
        register_rest_route('p116/v1', '/autocomplete', array(
            'methods' => 'GET',
            'callback' => array($this, 'autocomplete_suggestions'),
            'permission_callback' => '__return_true',
            'args' => array(
                'query' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($value) {
                        return strlen($value) >= 2;
                    }
                ),
                'limit' => array(
                    'required' => false,
                    'type' => 'integer',
                    'default' => 10,
                    'minimum' => 1,
                    'maximum' => 20
                )
            )
        ));
    }
    
    public function search_businesses($request) {
        $query = $request->get_param('query');
        $category = $request->get_param('category');
        $veteran_owned = $request->get_param('veteran_owned');
        $sons_owned = $request->get_param('sons_owned');
        $auxiliary_owned = $request->get_param('auxiliary_owned');
        $per_page = $request->get_param('per_page');
        $page = $request->get_param('page');
        
        $args = array(
            'post_type' => 'p116_business',
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'paged' => $page,
            'meta_query' => array(
                array(
                    'key' => 'show_in_directory',
                    'value' => '1',
                    'compare' => '='
                )
            )
        );
        
        if (!empty($query)) {
            $args['meta_query'][] = array(
                'relation' => 'OR',
                array(
                    'key' => 'owners_search',
                    'value' => strtolower($query),
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => 'services_offered',
                    'value' => $query,
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => 'city_search',
                    'value' => strtolower($query),
                    'compare' => 'LIKE'
                )
            );
            
            $args['s'] = $query;
        }
        
        if (!empty($category)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'p116_business_category',
                    'field' => 'slug',
                    'terms' => $category
                )
            );
        }
        
        if ($veteran_owned || $sons_owned || $auxiliary_owned) {
            $flag_queries = array();
            
            if ($veteran_owned) {
                $flag_queries[] = array(
                    'key' => 'veteran_owned',
                    'value' => '1',
                    'compare' => '='
                );
            }
            
            if ($sons_owned) {
                $flag_queries[] = array(
                    'key' => 'sons_owned',
                    'value' => '1',
                    'compare' => '='
                );
            }
            
            if ($auxiliary_owned) {
                $flag_queries[] = array(
                    'key' => 'auxiliary_owned',
                    'value' => '1',
                    'compare' => '='
                );
            }
            
            if (count($flag_queries) > 1) {
                $args['meta_query'][] = array(
                    'relation' => 'OR',
                    $flag_queries
                );
            } else {
                $args['meta_query'][] = $flag_queries[0];
            }
        }
        
        if (count($args['meta_query']) > 1) {
            $args['meta_query']['relation'] = 'AND';
        }
        
        $query_obj = new WP_Query($args);
        $businesses = array();
        $categories_data = array();
        
        if ($query_obj->have_posts()) {
            while ($query_obj->have_posts()) {
                $query_obj->the_post();
                $post_id = get_the_ID();
                
                $business_data = $this->format_business_data($post_id);
                $businesses[] = $business_data;
                
                $terms = get_the_terms($post_id, 'p116_business_category');
                if ($terms && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        if (!isset($categories_data[$term->slug])) {
                            $categories_data[$term->slug] = array(
                                'id' => $term->term_id,
                                'name' => $term->name,
                                'slug' => $term->slug,
                                'businesses' => array()
                            );
                        }
                        $categories_data[$term->slug]['businesses'][] = $business_data;
                    }
                }
            }
            wp_reset_postdata();
        }
        
        ksort($categories_data);
        
        return new WP_REST_Response(array(
            'businesses' => $businesses,
            'categories' => array_values($categories_data),
            'total' => $query_obj->found_posts,
            'total_pages' => $query_obj->max_num_pages,
            'current_page' => $page
        ), 200);
    }
    
    public function autocomplete_suggestions($request) {
        $query = $request->get_param('query');
        $limit = $request->get_param('limit');
        
        $suggestions = array();
        
        $business_args = array(
            'post_type' => 'p116_business',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            's' => $query,
            'meta_query' => array(
                array(
                    'key' => 'show_in_directory',
                    'value' => '1',
                    'compare' => '='
                )
            )
        );
        
        $business_query = new WP_Query($business_args);
        
        if ($business_query->have_posts()) {
            while ($business_query->have_posts()) {
                $business_query->the_post();
                $suggestions[] = array(
                    'type' => 'business',
                    'label' => get_the_title(),
                    'value' => get_the_title(),
                    'url' => get_permalink(),
                    'city' => get_post_meta(get_the_ID(), 'city', true)
                );
            }
        }
        wp_reset_postdata();
        
        global $wpdb;
        $owner_results = $wpdb->get_results($wpdb->prepare("
            SELECT DISTINCT meta_value as owners_data, post_id
            FROM {$wpdb->postmeta} pm1
            INNER JOIN {$wpdb->postmeta} pm2 ON pm1.post_id = pm2.post_id
            INNER JOIN {$wpdb->posts} p ON pm1.post_id = p.ID
            WHERE pm1.meta_key = 'owners'
            AND pm2.meta_key = 'show_in_directory'
            AND pm2.meta_value = '1'
            AND p.post_status = 'publish'
            AND pm1.meta_value LIKE %s
            LIMIT %d
        ", '%' . strtolower($query) . '%', $limit - count($suggestions)));
        
        foreach ($owner_results as $result) {
            $owners_data = json_decode($result->owners_data, true);
            if ($owners_data) {
                foreach ($owners_data as $owner) {
                    if (stripos($owner['owner_name'], $query) !== false) {
                        $business_title = get_the_title($result->post_id);
                        $suggestions[] = array(
                            'type' => 'owner',
                            'label' => $owner['owner_name'] . ' (' . $business_title . ')',
                            'value' => $owner['owner_name'],
                            'business' => $business_title,
                            'url' => get_permalink($result->post_id)
                        );
                        break;
                    }
                }
            }
            
            if (count($suggestions) >= $limit) {
                break;
            }
        }
        
        $category_terms = get_terms(array(
            'taxonomy' => 'p116_business_category',
            'name__like' => $query,
            'number' => $limit - count($suggestions),
            'hide_empty' => true
        ));
        
        if (!is_wp_error($category_terms)) {
            foreach ($category_terms as $term) {
                $suggestions[] = array(
                    'type' => 'category',
                    'label' => $term->name,
                    'value' => $term->name,
                    'slug' => $term->slug,
                    'count' => $term->count
                );
            }
        }
        
        return new WP_REST_Response(array(
            'suggestions' => array_slice($suggestions, 0, $limit)
        ), 200);
    }
    
    private function format_business_data($post_id) {
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
}