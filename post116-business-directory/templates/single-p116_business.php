<?php
get_header();

$business_data = p116_get_business_data();
?>

<div class="p116-single-business-container">
    <div class="p116-disclaimer">
        <?php echo esc_html__('American Legion Post 116 is not liable for or endorsing any listed businesses. Please independently verify their work quality, licenses, and insurance.', 'post116-business-directory'); ?>
    </div>

    <article id="business-<?php echo $business_data['id']; ?>" class="p116-single-business">
        <header class="p116-business-header">
            <?php if (!empty($business_data['thumbnail'])): ?>
                <div class="p116-business-logo">
                    <img src="<?php echo esc_url($business_data['thumbnail']); ?>" alt="<?php echo esc_attr($business_data['title']); ?>">
                </div>
            <?php endif; ?>
            
            <div class="p116-business-title-section">
                <h1 class="p116-business-title"><?php echo esc_html($business_data['title']); ?></h1>
                
                <?php if (!empty($business_data['flags'])): ?>
                    <div class="p116-business-flags">
                        <?php foreach ($business_data['flags'] as $flag): ?>
                            <span class="p116-flag <?php echo esc_attr($flag['type']); ?>">
                                <?php echo esc_html($flag['label']); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($business_data['categories'])): ?>
                    <div class="p116-business-categories">
                        <?php foreach ($business_data['categories'] as $category): ?>
                            <a href="<?php echo get_term_link($category['id']); ?>" class="p116-category-link">
                                <?php echo esc_html($category['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <div class="p116-business-content">
            <div class="p116-business-main">
                <?php if (!empty($business_data['owners'])): ?>
                    <div class="p116-business-owners">
                        <h2><?php echo esc_html__('Business Owners', 'post116-business-directory'); ?></h2>
                        <div class="p116-owners-list">
                            <?php foreach ($business_data['owners'] as $owner): ?>
                                <div class="p116-owner">
                                    <h3 class="p116-owner-name"><?php echo esc_html($owner['owner_name']); ?></h3>
                                    <?php if (!empty($owner['owner_role'])): ?>
                                        <div class="p116-owner-role"><?php echo esc_html($owner['owner_role']); ?></div>
                                    <?php endif; ?>
                                    
                                    <div class="p116-owner-contact">
                                        <?php if (!empty($owner['owner_email'])): ?>
                                            <div class="p116-owner-email">
                                                <i class="dashicons dashicons-email"></i>
                                                <a href="mailto:<?php echo esc_attr($owner['owner_email']); ?>">
                                                    <?php echo esc_html($owner['owner_email']); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($owner['owner_phone'])): ?>
                                            <div class="p116-owner-phone">
                                                <i class="dashicons dashicons-phone"></i>
                                                <a href="tel:<?php echo esc_attr($owner['owner_phone']); ?>">
                                                    <?php echo esc_html($owner['owner_phone']); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($owner['owner_website'])): ?>
                                            <div class="p116-owner-website">
                                                <i class="dashicons dashicons-admin-site"></i>
                                                <a href="<?php echo esc_url($owner['owner_website']); ?>" target="_blank" rel="noopener">
                                                    <?php echo esc_html($owner['owner_website']); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($business_data['services'])): ?>
                    <div class="p116-business-services">
                        <h2><?php echo esc_html__('Services Offered', 'post116-business-directory'); ?></h2>
                        <div class="p116-services-content">
                            <?php echo wpautop(esc_html($business_data['services'])); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($business_data['content'])): ?>
                    <div class="p116-business-description">
                        <h2><?php echo esc_html__('About This Business', 'post116-business-directory'); ?></h2>
                        <div class="p116-description-content">
                            <?php echo wp_kses_post($business_data['content']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($business_data['links'])): ?>
                    <div class="p116-business-links">
                        <h2><?php echo esc_html__('Additional Links', 'post116-business-directory'); ?></h2>
                        <ul class="p116-links-list">
                            <?php foreach ($business_data['links'] as $link): ?>
                                <li>
                                    <a href="<?php echo esc_url($link['link_url']); ?>" target="_blank" rel="noopener">
                                        <?php echo esc_html($link['link_label']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <div class="p116-business-sidebar">
                <div class="p116-contact-info">
                    <h2><?php echo esc_html__('Contact Information', 'post116-business-directory'); ?></h2>
                    
                    <?php if (!empty($business_data['contact']['phone'])): ?>
                        <div class="p116-contact-item">
                            <i class="dashicons dashicons-phone"></i>
                            <a href="tel:<?php echo esc_attr($business_data['contact']['phone']); ?>">
                                <?php echo esc_html($business_data['contact']['phone']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($business_data['contact']['email'])): ?>
                        <div class="p116-contact-item">
                            <i class="dashicons dashicons-email"></i>
                            <a href="mailto:<?php echo esc_attr($business_data['contact']['email']); ?>">
                                <?php echo esc_html($business_data['contact']['email']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($business_data['contact']['website'])): ?>
                        <div class="p116-contact-item">
                            <i class="dashicons dashicons-admin-site"></i>
                            <a href="<?php echo esc_url($business_data['contact']['website']); ?>" target="_blank" rel="noopener">
                                <?php echo esc_html__('Visit Website', 'post116-business-directory'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php 
                $address_parts = array_filter(array(
                    $business_data['address']['address1'],
                    $business_data['address']['address2'],
                    $business_data['address']['city'],
                    $business_data['address']['state'],
                    $business_data['address']['postal_code']
                ));
                
                if (!empty($address_parts)): ?>
                    <div class="p116-address-info">
                        <h2><?php echo esc_html__('Location', 'post116-business-directory'); ?></h2>
                        <div class="p116-address">
                            <i class="dashicons dashicons-location-alt"></i>
                            <div class="p116-address-lines">
                                <?php if (!empty($business_data['address']['address1'])): ?>
                                    <div><?php echo esc_html($business_data['address']['address1']); ?></div>
                                <?php endif; ?>
                                
                                <?php if (!empty($business_data['address']['address2'])): ?>
                                    <div><?php echo esc_html($business_data['address']['address2']); ?></div>
                                <?php endif; ?>
                                
                                <div>
                                    <?php echo esc_html($business_data['address']['city']); ?>
                                    <?php if (!empty($business_data['address']['state'])): ?>
                                        , <?php echo esc_html($business_data['address']['state']); ?>
                                    <?php endif; ?>
                                    <?php if (!empty($business_data['address']['postal_code'])): ?>
                                        <?php echo esc_html($business_data['address']['postal_code']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </article>

    <div class="p116-back-to-directory">
        <a href="<?php echo home_url('/directory'); ?>" class="button">
            ← <?php echo esc_html__('Back to Directory', 'post116-business-directory'); ?>
        </a>
    </div>
</div>

<?php
get_footer();