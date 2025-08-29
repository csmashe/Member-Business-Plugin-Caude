<?php
$show_flags = $attributes['showFlags'] ?? true;
$per_page = $attributes['perPage'] ?? 20;
$placeholder_text = $attributes['placeholderText'] ?? __('Search businesses, owners, or services...', 'post116-business-directory');

$categories = get_terms(array(
    'taxonomy' => 'p116_business_category',
    'hide_empty' => true,
    'orderby' => 'name',
    'order' => 'ASC'
));

$businesses_query = new WP_Query(array(
    'post_type' => 'p116_business',
    'post_status' => 'publish',
    'posts_per_page' => $per_page,
    'meta_query' => array(
        array(
            'key' => 'show_in_directory',
            'value' => '1',
            'compare' => '='
        )
    ),
    'orderby' => 'title',
    'order' => 'ASC'
));

$categories_with_businesses = array();

if ($businesses_query->have_posts()) {
    while ($businesses_query->have_posts()) {
        $businesses_query->the_post();
        $business_data = p116_get_business_data();
        
        if (!empty($business_data['categories'])) {
            foreach ($business_data['categories'] as $category) {
                if (!isset($categories_with_businesses[$category['slug']])) {
                    $categories_with_businesses[$category['slug']] = array(
                        'name' => $category['name'],
                        'slug' => $category['slug'],
                        'businesses' => array()
                    );
                }
                $categories_with_businesses[$category['slug']]['businesses'][] = $business_data;
            }
        } else {
            if (!isset($categories_with_businesses['uncategorized'])) {
                $categories_with_businesses['uncategorized'] = array(
                    'name' => __('Uncategorized', 'post116-business-directory'),
                    'slug' => 'uncategorized',
                    'businesses' => array()
                );
            }
            $categories_with_businesses['uncategorized']['businesses'][] = $business_data;
        }
    }
    wp_reset_postdata();
}

ksort($categories_with_businesses);
?>

<div class="p116-directory-container" data-per-page="<?php echo esc_attr($per_page); ?>">
    <div class="p116-disclaimer">
        <?php echo esc_html__('American Legion Post 116 is not liable for or endorsing any listed businesses. Please independently verify their work quality, licenses, and insurance.', 'post116-business-directory'); ?>
    </div>

    <div class="p116-directory-search">
        <div class="p116-search-controls">
            <div class="p116-search-input-wrapper">
                <input type="text" 
                       id="p116-search-input" 
                       class="p116-search-input" 
                       placeholder="<?php echo esc_attr($placeholder_text); ?>" 
                       autocomplete="off">
                <div class="p116-autocomplete-results" style="display: none;"></div>
            </div>
            
            <?php if (!empty($categories)): ?>
                <select id="p116-category-filter" class="p116-category-filter">
                    <option value=""><?php echo esc_html__('All Categories', 'post116-business-directory'); ?></option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo esc_attr($category->slug); ?>">
                            <?php echo esc_html($category->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            
            <?php if ($show_flags): ?>
                <div class="p116-flag-filters">
                    <label class="p116-flag-filter">
                        <input type="checkbox" name="veteran_owned" value="1">
                        <?php echo esc_html__('Veteran Owned', 'post116-business-directory'); ?>
                    </label>
                    <label class="p116-flag-filter">
                        <input type="checkbox" name="sons_owned" value="1">
                        <?php echo esc_html__('SAL Owned', 'post116-business-directory'); ?>
                    </label>
                    <label class="p116-flag-filter">
                        <input type="checkbox" name="auxiliary_owned" value="1">
                        <?php echo esc_html__('Auxiliary Owned', 'post116-business-directory'); ?>
                    </label>
                </div>
            <?php endif; ?>
            
            <button type="button" id="p116-search-btn" class="p116-search-btn button">
                <?php echo esc_html__('Search', 'post116-business-directory'); ?>
            </button>
            
            <button type="button" id="p116-clear-search" class="p116-clear-search button" style="display: none;">
                <?php echo esc_html__('Clear', 'post116-business-directory'); ?>
            </button>
        </div>
    </div>

    <div class="p116-directory-results">
        <div class="p116-loading" style="display: none;">
            <?php echo esc_html__('Loading...', 'post116-business-directory'); ?>
        </div>

        <div id="p116-results-container" class="p116-results-container">
            <?php if (!empty($categories_with_businesses)): ?>
                <?php foreach ($categories_with_businesses as $category_data): ?>
                    <div class="p116-category-section">
                        <h2 class="p116-category-title">
                            <?php echo esc_html($category_data['name']); ?>
                            <span class="p116-category-count">(<?php echo count($category_data['businesses']); ?>)</span>
                        </h2>
                        <div class="p116-businesses-grid">
                            <?php foreach ($category_data['businesses'] as $business_data): ?>
                                <?php echo P116_Templates::render_business_card($business_data); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p116-no-businesses">
                    <p><?php echo esc_html__('No businesses found.', 'post116-business-directory'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="p116-pagination-container"></div>
    </div>
</div>