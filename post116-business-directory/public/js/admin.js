jQuery(document).ready(function($) {
    'use strict';
    
    // Initialize
    initOwnerRepeater();
    initLinkRepeater();
    
    function initOwnerRepeater() {
        let ownerIndex = $('.p116-owner-group').length;
        
        // Add owner button
        $('.p116-add-owner').on('click', function() {
            const template = $('#p116-owner-template').html();
            const newOwner = template.replace(/\{\{INDEX\}\}/g, ownerIndex);
            
            $('.p116-owners-wrapper').append(newOwner);
            updateOwnerNumbers();
            ownerIndex++;
        });
        
        // Remove owner button
        $(document).on('click', '.p116-remove-owner', function() {
            $(this).closest('.p116-owner-group').remove();
            updateOwnerNumbers();
        });
        
        // Update owner numbers
        function updateOwnerNumbers() {
            $('.p116-owner-group').each(function(index) {
                $(this).find('.owner-number').text(index + 1);
                
                // Update field names and IDs
                $(this).find('input').each(function() {
                    const name = $(this).attr('name');
                    const id = $(this).attr('id');
                    
                    if (name) {
                        const newName = name.replace(/owners\[\d+\]/, 'owners[' + index + ']');
                        $(this).attr('name', newName);
                    }
                    
                    if (id) {
                        const newId = id.replace(/_\d+/, '_' + index);
                        $(this).attr('id', newId);
                        
                        // Update corresponding label
                        const $label = $(this).closest('tr').find('label[for="' + id + '"]');
                        if ($label.length) {
                            $label.attr('for', newId);
                        }
                    }
                });
                
                $(this).attr('data-index', index);
            });
        }
    }
    
    function initLinkRepeater() {
        let linkIndex = $('.p116-link-group').length;
        
        // Make links sortable
        $('.p116-links-wrapper').sortable({
            handle: '.p116-link-handle',
            axis: 'y',
            update: function() {
                updateLinkIndices();
            }
        });
        
        // Add link button
        $('.p116-add-link').on('click', function() {
            const template = $('#p116-link-template').html();
            const newLink = template.replace(/\{\{INDEX\}\}/g, linkIndex);
            
            $('.p116-links-wrapper').append(newLink);
            updateLinkIndices();
            linkIndex++;
        });
        
        // Remove link button
        $(document).on('click', '.p116-remove-link', function() {
            $(this).closest('.p116-link-group').remove();
            updateLinkIndices();
        });
        
        // Update link indices after sorting or adding/removing
        function updateLinkIndices() {
            $('.p116-link-group').each(function(index) {
                $(this).find('input').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/links\[\d+\]/, 'links[' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
                
                $(this).attr('data-index', index);
            });
        }
    }
    
    // Form validation
    $('form#post').on('submit', function() {
        const city = $('#city').val().trim();
        
        if (!city) {
            alert('City is required for businesses.');
            $('#city').focus();
            return false;
        }
        
        return true;
    });
    
    // Format phone numbers on blur
    $(document).on('blur', 'input[type="tel"]', function() {
        const value = $(this).val().replace(/\D/g, '');
        
        if (value.length === 10) {
            const formatted = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            $(this).val(formatted);
        }
    });
    
    // URL validation
    $(document).on('blur', 'input[type="url"]', function() {
        const value = $(this).val().trim();
        
        if (value && !value.match(/^https?:\/\//)) {
            $(this).val('https://' + value);
        }
    });
});