<?php
get_header();

$term = get_queried_object();
$businesses_query = new WP_Query(array(
    'post_type' => 'p116_business',
    'post_status' => 'publish',
    'posts_per_page' => 20,
    'meta_query' => array(
        array(
            'key' => 'show_in_directory',
            'value' => '1',
            'compare' => '='
        )
    ),
    'tax_query' => array(
        array(
            'taxonomy' => 'p116_business_category',
            'field' => 'term_id',
            'terms' => $term->term_id
        )
    )
));
?>

<div class="p116-category-archive-container">
    <div class="p116-disclaimer">
        <?php echo esc_html__('American Legion Post 116 is not liable for or endorsing any listed businesses. Please independently verify their work quality, licenses, and insurance.', 'post116-business-directory'); ?>
    </div>

    <header class="p116-category-header">
        <h1><?php echo esc_html($term->name); ?></h1>
        
        <?php if (!empty($term->description)): ?>
            <div class="p116-category-description">
                <?php echo wpautop(esc_html($term->description)); ?>
            </div>
        <?php endif; ?>
        
        <div class="p116-category-count">
            <?php printf(
                _n('%d business in this category', '%d businesses in this category', $businesses_query->found_posts, 'post116-business-directory'),
                $businesses_query->found_posts
            ); ?>
        </div>
    </header>

    <?php if ($businesses_query->have_posts()): ?>
        <div class="p116-businesses-grid">
            <?php while ($businesses_query->have_posts()): ?>
                <?php $businesses_query->the_post(); ?>
                <?php 
                $business_data = p116_get_business_data();
                echo P116_Templates::render_business_card($business_data);
                ?>
            <?php endwhile; ?>
        </div>

        <?php if ($businesses_query->max_num_pages > 1): ?>
            <div class="p116-pagination">
                <?php
                echo paginate_links(array(
                    'total' => $businesses_query->max_num_pages,
                    'current' => max(1, get_query_var('paged')),
                    'prev_text' => __('← Previous', 'post116-business-directory'),
                    'next_text' => __('Next →', 'post116-business-directory'),
                ));
                ?>
            </div>
        <?php endif; ?>
        
        <?php wp_reset_postdata(); ?>
    <?php else: ?>
        <div class="p116-no-businesses">
            <p><?php echo esc_html__('No businesses found in this category.', 'post116-business-directory'); ?></p>
        </div>
    <?php endif; ?>

    <div class="p116-back-to-directory">
        <a href="<?php echo home_url('/directory'); ?>" class="button">
            ← <?php echo esc_html__('Back to Directory', 'post116-business-directory'); ?>
        </a>
    </div>
</div>

<?php
get_footer();