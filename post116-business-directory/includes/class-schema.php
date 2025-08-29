<?php

if (!defined('ABSPATH')) {
    exit;
}

class P116_Schema {
    
    public function __construct() {
        add_action('wp_head', array($this, 'add_json_ld_schema'));
    }
    
    public function add_json_ld_schema() {
        if (!is_singular('p116_business')) {
            return;
        }
        
        global $post;
        $business_data = p116_get_business_data($post->ID);
        
        $schema = $this->generate_local_business_schema($business_data);
        
        if ($schema) {
            echo '<script type="application/ld+json">';
            echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES);
            echo '</script>';
        }
    }
    
    private function generate_local_business_schema($business_data) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $business_data['title'],
            'url' => $business_data['url']
        );
        
        if (!empty($business_data['thumbnail'])) {
            $schema['image'] = $business_data['thumbnail'];
        }
        
        if (!empty($business_data['contact']['phone'])) {
            $schema['telephone'] = $business_data['contact']['phone'];
        }
        
        if (!empty($business_data['contact']['email'])) {
            $schema['email'] = $business_data['contact']['email'];
        }
        
        $address_parts = array_filter(array(
            $business_data['address']['address1'],
            $business_data['address']['city'],
            $business_data['address']['state'],
            $business_data['address']['postal_code']
        ));
        
        if (!empty($address_parts)) {
            $postal_address = array(
                '@type' => 'PostalAddress'
            );
            
            if (!empty($business_data['address']['address1'])) {
                $postal_address['streetAddress'] = $business_data['address']['address1'];
                
                if (!empty($business_data['address']['address2'])) {
                    $postal_address['streetAddress'] .= ', ' . $business_data['address']['address2'];
                }
            }
            
            if (!empty($business_data['address']['city'])) {
                $postal_address['addressLocality'] = $business_data['address']['city'];
            }
            
            if (!empty($business_data['address']['state'])) {
                $postal_address['addressRegion'] = $business_data['address']['state'];
            }
            
            if (!empty($business_data['address']['postal_code'])) {
                $postal_address['postalCode'] = $business_data['address']['postal_code'];
            }
            
            $postal_address['addressCountry'] = 'US';
            
            $schema['address'] = $postal_address;
        }
        
        if (!empty($business_data['services'])) {
            $schema['description'] = $business_data['services'];
        }
        
        $same_as = array();
        
        if (!empty($business_data['contact']['website'])) {
            $same_as[] = $business_data['contact']['website'];
        }
        
        if (!empty($business_data['links'])) {
            foreach ($business_data['links'] as $link) {
                if (!empty($link['link_url'])) {
                    $same_as[] = $link['link_url'];
                }
            }
        }
        
        if (!empty($same_as)) {
            $schema['sameAs'] = $same_as;
        }
        
        if (!empty($business_data['owners'])) {
            $persons = array();
            
            foreach ($business_data['owners'] as $owner) {
                if (!empty($owner['owner_name'])) {
                    $person = array(
                        '@type' => 'Person',
                        'name' => $owner['owner_name']
                    );
                    
                    if (!empty($owner['owner_role'])) {
                        $person['jobTitle'] = $owner['owner_role'];
                    }
                    
                    if (!empty($owner['owner_email'])) {
                        $person['email'] = $owner['owner_email'];
                    }
                    
                    if (!empty($owner['owner_phone'])) {
                        $person['telephone'] = $owner['owner_phone'];
                    }
                    
                    if (!empty($owner['owner_website'])) {
                        $person['url'] = $owner['owner_website'];
                    }
                    
                    $persons[] = $person;
                }
            }
            
            if (!empty($persons)) {
                if (count($persons) === 1) {
                    $schema['employee'] = $persons[0];
                } else {
                    $schema['employee'] = $persons;
                }
            }
        }
        
        $opening_hours = $this->get_opening_hours($business_data);
        if (!empty($opening_hours)) {
            $schema['openingHours'] = $opening_hours;
        }
        
        $categories = wp_list_pluck($business_data['categories'], 'name');
        if (!empty($categories)) {
            $schema['additionalType'] = $categories;
        }
        
        return $schema;
    }
    
    private function get_opening_hours($business_data) {
        return array();
    }
}