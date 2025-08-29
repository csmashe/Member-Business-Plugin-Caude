jQuery(document).ready(function($) {
    'use strict';
    
    let searchTimeout;
    let currentRequest;
    let currentPage = 1;
    
    const $container = $('.p116-directory-container');
    const $searchInput = $('#p116-search-input');
    const $categoryFilter = $('#p116-category-filter');
    const $flagFilters = $('.p116-flag-filter input');
    const $searchBtn = $('#p116-search-btn');
    const $clearBtn = $('#p116-clear-search');
    const $resultsContainer = $('#p116-results-container');
    const $paginationContainer = $('.p116-pagination-container');
    const $loading = $('.p116-loading');
    const $autocompleteResults = $('.p116-autocomplete-results');
    
    const perPage = parseInt($container.data('per-page')) || 20;
    
    // Initialize
    init();
    
    function init() {
        bindEvents();
        setupAutocomplete();
    }
    
    function bindEvents() {
        $searchBtn.on('click', performSearch);
        $clearBtn.on('click', clearSearch);
        
        $searchInput.on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                performSearch();
            }
        });
        
        $categoryFilter.on('change', performSearch);
        $flagFilters.on('change', performSearch);
        
        // Pagination
        $(document).on('click', '.p116-pagination a', function(e) {
            e.preventDefault();
            const page = getPageFromUrl($(this).attr('href'));
            if (page) {
                currentPage = page;
                performSearch();
            }
        });
    }
    
    function setupAutocomplete() {
        let selectedIndex = -1;
        
        $searchInput.on('input', function() {
            const query = $(this).val().trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                hideAutocomplete();
                return;
            }
            
            searchTimeout = setTimeout(function() {
                fetchAutocompleteResults(query);
            }, 300);
        });
        
        $searchInput.on('keydown', function(e) {
            const $items = $autocompleteResults.find('.p116-autocomplete-item');
            const itemCount = $items.length;
            
            if (itemCount === 0) return;
            
            switch(e.which) {
                case 38: // Up arrow
                    e.preventDefault();
                    selectedIndex = selectedIndex <= 0 ? itemCount - 1 : selectedIndex - 1;
                    updateSelection($items, selectedIndex);
                    break;
                    
                case 40: // Down arrow
                    e.preventDefault();
                    selectedIndex = selectedIndex >= itemCount - 1 ? 0 : selectedIndex + 1;
                    updateSelection($items, selectedIndex);
                    break;
                    
                case 13: // Enter
                    e.preventDefault();
                    if (selectedIndex >= 0 && $items.eq(selectedIndex).length) {
                        selectAutocompleteItem($items.eq(selectedIndex));
                    } else {
                        performSearch();
                    }
                    break;
                    
                case 27: // Escape
                    hideAutocomplete();
                    break;
            }
        });
        
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.p116-search-input-wrapper').length) {
                hideAutocomplete();
            }
        });
        
        $(document).on('click', '.p116-autocomplete-item', function() {
            selectAutocompleteItem($(this));
        });
        
        $(document).on('mouseenter', '.p116-autocomplete-item', function() {
            selectedIndex = $(this).index();
            updateSelection($autocompleteResults.find('.p116-autocomplete-item'), selectedIndex);
        });
    }
    
    function fetchAutocompleteResults(query) {
        if (currentRequest) {
            currentRequest.abort();
        }
        
        currentRequest = $.ajax({
            url: p116_ajax.rest_url + 'autocomplete',
            method: 'GET',
            data: {
                query: query,
                limit: 8
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', p116_ajax.nonce);
            }
        }).done(function(response) {
            if (response.suggestions && response.suggestions.length > 0) {
                displayAutocompleteResults(response.suggestions);
            } else {
                hideAutocomplete();
            }
        }).fail(function(xhr) {
            if (xhr.statusText !== 'abort') {
                hideAutocomplete();
            }
        }).always(function() {
            currentRequest = null;
        });
    }
    
    function displayAutocompleteResults(suggestions) {
        let html = '';
        
        suggestions.forEach(function(suggestion) {
            let typeClass = 'p116-autocomplete-type-' + suggestion.type;
            let typeLabel = '';
            
            switch(suggestion.type) {
                case 'business':
                    typeLabel = 'Business';
                    break;
                case 'owner':
                    typeLabel = 'Owner';
                    break;
                case 'category':
                    typeLabel = 'Category';
                    break;
            }
            
            html += '<div class="p116-autocomplete-item ' + typeClass + '" data-value="' + 
                    escapeHtml(suggestion.value) + '" data-type="' + suggestion.type + '">';
            html += '<span class="p116-autocomplete-label">' + escapeHtml(suggestion.label) + '</span>';
            html += '<span class="p116-autocomplete-type">' + typeLabel + '</span>';
            html += '</div>';
        });
        
        $autocompleteResults.html(html).show();
    }
    
    function updateSelection($items, index) {
        $items.removeClass('selected');
        if (index >= 0 && index < $items.length) {
            $items.eq(index).addClass('selected');
        }
    }
    
    function selectAutocompleteItem($item) {
        const value = $item.data('value');
        const type = $item.data('type');
        
        $searchInput.val(value);
        hideAutocomplete();
        
        if (type === 'category') {
            const categorySlug = $item.data('slug');
            if (categorySlug) {
                $categoryFilter.val(categorySlug);
            }
        }
        
        performSearch();
    }
    
    function hideAutocomplete() {
        $autocompleteResults.hide().empty();
    }
    
    function performSearch() {
        if (currentRequest) {
            currentRequest.abort();
        }
        
        const searchData = {
            query: $searchInput.val().trim(),
            category: $categoryFilter.val(),
            per_page: perPage,
            page: currentPage
        };
        
        // Add flag filters
        $flagFilters.each(function() {
            if ($(this).is(':checked')) {
                searchData[$(this).attr('name')] = true;
            }
        });
        
        showLoading();
        hideAutocomplete();
        
        currentRequest = $.ajax({
            url: p116_ajax.rest_url + 'search',
            method: 'GET',
            data: searchData,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', p116_ajax.nonce);
            }
        }).done(function(response) {
            displayResults(response);
            updatePagination(response);
            updateClearButton();
        }).fail(function(xhr) {
            if (xhr.statusText !== 'abort') {
                showError('Failed to load results. Please try again.');
            }
        }).always(function() {
            hideLoading();
            currentRequest = null;
        });
    }
    
    function displayResults(response) {
        if (response.categories && response.categories.length > 0) {
            let html = '';
            
            response.categories.forEach(function(category) {
                html += '<div class="p116-category-section">';
                html += '<h2 class="p116-category-title">';
                html += escapeHtml(category.name);
                html += '<span class="p116-category-count">(' + category.businesses.length + ')</span>';
                html += '</h2>';
                html += '<div class="p116-businesses-grid">';
                
                category.businesses.forEach(function(business) {
                    html += renderBusinessCard(business);
                });
                
                html += '</div>';
                html += '</div>';
            });
            
            $resultsContainer.html(html);
        } else {
            $resultsContainer.html('<div class="p116-no-businesses"><p>No businesses found.</p></div>');
        }
    }
    
    function renderBusinessCard(business) {
        let html = '<article class="p116-business-card">';
        
        // Header
        html += '<div class="p116-business-header">';
        
        if (business.thumbnail) {
            html += '<div class="p116-business-thumbnail">';
            html += '<img src="' + escapeHtml(business.thumbnail) + '" alt="' + escapeHtml(business.title) + '">';
            html += '</div>';
        }
        
        html += '<div class="p116-business-title-wrapper">';
        html += '<h3 class="p116-business-title">';
        html += '<a href="' + escapeHtml(business.url) + '">' + escapeHtml(business.title) + '</a>';
        html += '</h3>';
        
        // Flags
        if (business.flags && business.flags.length > 0) {
            html += '<div class="p116-business-flags">';
            business.flags.forEach(function(flag) {
                html += '<span class="p116-flag ' + escapeHtml(flag.type) + '">' + escapeHtml(flag.label) + '</span>';
            });
            html += '</div>';
        }
        
        html += '</div>';
        html += '</div>';
        
        // Categories
        if (business.categories && business.categories.length > 0) {
            const categoryNames = business.categories.map(function(cat) {
                return cat.name;
            });
            html += '<div class="p116-business-categories">' + escapeHtml(categoryNames.join(', ')) + '</div>';
        }
        
        // Owners
        if (business.owners && business.owners.length > 0) {
            const ownerNames = business.owners.map(function(owner) {
                return owner.owner_name;
            }).filter(function(name) {
                return name && name.trim();
            });
            
            if (ownerNames.length > 0) {
                html += '<div class="p116-business-owners">Owners: ' + escapeHtml(ownerNames.join(', ')) + '</div>';
            }
        }
        
        // Contact
        html += '<div class="p116-business-contact">';
        
        if (business.contact.phone) {
            html += '<div class="p116-business-phone">';
            html += '<i class="dashicons dashicons-phone"></i> ';
            html += escapeHtml(business.contact.phone);
            html += '</div>';
        }
        
        if (business.address.city) {
            html += '<div class="p116-business-city">';
            html += '<i class="dashicons dashicons-location-alt"></i> ';
            html += escapeHtml(business.address.city);
            html += '</div>';
        }
        
        html += '</div>';
        
        // Services
        if (business.services) {
            html += '<div class="p116-business-services">' + escapeHtml(business.services) + '</div>';
        }
        
        html += '</article>';
        
        return html;
    }
    
    function updatePagination(response) {
        if (response.total_pages <= 1) {
            $paginationContainer.empty();
            return;
        }
        
        let html = '<div class="p116-pagination">';
        
        if (response.current_page > 1) {
            html += '<a href="#" data-page="' + (response.current_page - 1) + '">← Previous</a>';
        }
        
        const startPage = Math.max(1, response.current_page - 2);
        const endPage = Math.min(response.total_pages, response.current_page + 2);
        
        if (startPage > 1) {
            html += '<a href="#" data-page="1">1</a>';
            if (startPage > 2) {
                html += '<span>...</span>';
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === response.current_page) {
                html += '<span class="current">' + i + '</span>';
            } else {
                html += '<a href="#" data-page="' + i + '">' + i + '</a>';
            }
        }
        
        if (endPage < response.total_pages) {
            if (endPage < response.total_pages - 1) {
                html += '<span>...</span>';
            }
            html += '<a href="#" data-page="' + response.total_pages + '">' + response.total_pages + '</a>';
        }
        
        if (response.current_page < response.total_pages) {
            html += '<a href="#" data-page="' + (response.current_page + 1) + '">Next →</a>';
        }
        
        html += '</div>';
        
        $paginationContainer.html(html);
    }
    
    function updateClearButton() {
        const hasQuery = $searchInput.val().trim().length > 0;
        const hasCategory = $categoryFilter.val().length > 0;
        const hasFlags = $flagFilters.filter(':checked').length > 0;
        
        if (hasQuery || hasCategory || hasFlags) {
            $clearBtn.show();
        } else {
            $clearBtn.hide();
        }
    }
    
    function clearSearch() {
        $searchInput.val('');
        $categoryFilter.val('');
        $flagFilters.prop('checked', false);
        currentPage = 1;
        hideAutocomplete();
        performSearch();
    }
    
    function showLoading() {
        $loading.show();
        $resultsContainer.hide();
    }
    
    function hideLoading() {
        $loading.hide();
        $resultsContainer.show();
    }
    
    function showError(message) {
        $resultsContainer.html('<div class="p116-no-businesses"><p>' + escapeHtml(message) + '</p></div>');
    }
    
    function getPageFromUrl(url) {
        const match = url.match(/[?&]page=(\d+)/);
        return match ? parseInt(match[1]) : null;
    }
    
    function escapeHtml(text) {
        if (typeof text !== 'string') {
            return '';
        }
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});