# Post 116 Business Directory - QA Checklist

## Installation & Activation
- [ ] Plugin uploads successfully via WordPress admin
- [ ] Plugin activates without errors
- [ ] "Business Directory" page is created at `/directory`
- [ ] Business Directory menu item appears in admin
- [ ] Capabilities are assigned to all user roles
- [ ] Rewrite rules are flushed properly

## Custom Post Type & Taxonomy
- [ ] `p116_business` post type is registered
- [ ] `p116_business_category` taxonomy is registered
- [ ] Archive pages work at `/directory` and `/directory/category/{slug}`
- [ ] Single business pages work at `/directory/{business-slug}`
- [ ] Meta fields are registered and accessible via REST API

## Admin Interface
### Business Creation
- [ ] Can create new business successfully
- [ ] All meta fields are present and functional
- [ ] City field is required (shows error if empty)
- [ ] Owners repeater works (add, remove, reorder)
- [ ] Links repeater works with drag-to-sort
- [ ] Ownership flags save correctly
- [ ] Categories can be assigned (multiple selection)
- [ ] Featured image can be uploaded
- [ ] Business can be published/drafted

### Admin List View
- [ ] Custom columns display correctly (Categories, Owners, City, Phone, Flags)
- [ ] Owner names show first two with "+N more" for additional
- [ ] Ownership flags display as colored badges
- [ ] Category and ownership flag filters work
- [ ] Search functionality works

### Meta Box Interface
- [ ] Owner fields validate correctly (email, phone, URL)
- [ ] Phone numbers auto-format on blur
- [ ] URLs auto-prepend "https://" if missing
- [ ] Required fields are clearly marked
- [ ] Form validation prevents submission without city

## Directory Block (Gutenberg)
- [ ] Block appears in widgets category
- [ ] Block settings work in editor (flags toggle, per page, placeholder)
- [ ] Block renders correctly in editor with placeholder
- [ ] Block renders correctly on frontend
- [ ] Default directory page displays the block properly

## Frontend Directory
### Display
- [ ] Disclaimer appears at top of all directory pages
- [ ] Businesses grouped by category alphabetically
- [ ] Business cards display all information correctly
- [ ] Ownership flags display with correct colors
- [ ] Responsive design works on mobile/tablet
- [ ] Images display correctly with fallbacks

### Search Functionality
- [ ] Basic text search works
- [ ] Category filter works
- [ ] Ownership flag filters work
- [ ] Search results update via AJAX
- [ ] Multiple filters work together
- [ ] Clear search button works
- [ ] Pagination works correctly

### Autocomplete
- [ ] Autocomplete appears after typing 2+ characters
- [ ] Business names appear in suggestions
- [ ] Owner names appear in suggestions
- [ ] Category names appear in suggestions
- [ ] Keyboard navigation works (up/down arrows, enter, escape)
- [ ] Mouse selection works
- [ ] Autocomplete selections trigger search correctly

## Single Business Pages
### Content Display
- [ ] Business logo/image displays correctly
- [ ] Business name and title display
- [ ] Ownership flags display correctly
- [ ] Categories display as clickable links
- [ ] Owner information displays completely
- [ ] Contact information displays with icons
- [ ] Address displays properly formatted
- [ ] Services offered section appears
- [ ] Long description displays formatted content
- [ ] Additional links display correctly
- [ ] "Back to Directory" link works

### Schema Markup
- [ ] JSON-LD LocalBusiness schema is present in page source
- [ ] Schema includes all available business data
- [ ] Schema validates with structured data testing tool
- [ ] Schema improves SEO appearance

## Category Archive Pages
- [ ] Category name and description display
- [ ] Business count shows correctly
- [ ] Businesses display in card format
- [ ] Pagination works if needed
- [ ] "Back to Directory" link works

## REST API
- [ ] `/wp-json/p116/v1/search` endpoint responds correctly
- [ ] `/wp-json/p116/v1/autocomplete` endpoint responds correctly
- [ ] Search parameters work (query, category, flags, pagination)
- [ ] Results format is consistent
- [ ] Error handling works for invalid requests
- [ ] Nonce verification works

## Settings Page
- [ ] Settings page accessible under Business Directory menu
- [ ] Directory page selector works
- [ ] Per-page setting works
- [ ] Color customization works
- [ ] Ownership flags toggle works
- [ ] Custom CSS field works
- [ ] Settings save correctly
- [ ] Settings apply to frontend

## Performance & Security
- [ ] Page load times are reasonable
- [ ] AJAX requests are fast and responsive
- [ ] Database queries are optimized
- [ ] All inputs are sanitized
- [ ] All outputs are escaped
- [ ] Nonces are used for forms
- [ ] Capabilities are checked properly
- [ ] No PHP errors or warnings in debug mode

## Browser & Device Testing
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Chrome
- [ ] Mobile Safari
- [ ] Tablet view

## WordPress Compatibility
- [ ] Works with WordPress 6.0+
- [ ] Works with common themes
- [ ] No conflicts with common plugins
- [ ] Block editor integration works
- [ ] Classic editor compatibility (if needed)

## Data Validation
### Sample Data Tests
- [ ] Create business with single owner
- [ ] Create business with multiple owners
- [ ] Create business with all fields populated
- [ ] Create business with minimal required fields
- [ ] Test all ownership flag combinations
- [ ] Test multiple categories per business
- [ ] Test businesses without categories

### Search Tests
- [ ] Search by business name
- [ ] Search by owner name
- [ ] Search by city
- [ ] Search by services offered
- [ ] Test partial matches
- [ ] Test case-insensitive search
- [ ] Test special characters

## Error Handling
- [ ] Invalid business ID shows 404
- [ ] Invalid category shows 404
- [ ] AJAX errors display user-friendly messages
- [ ] Network errors are handled gracefully
- [ ] Empty search results display helpful message
- [ ] Plugin handles deactivation/reactivation cleanly

## Final Checklist
- [ ] All features from requirements are implemented
- [ ] Plugin follows WordPress coding standards
- [ ] All text is translatable
- [ ] No hard-coded URLs or paths
- [ ] Plugin can be safely deleted without leaving data
- [ ] Documentation is complete and accurate
- [ ] Legal disclaimer displays on all pages
- [ ] Plugin is ready for production use

## Notes
- Test with real business data to ensure practical functionality
- Verify all URLs and links work correctly
- Check that plugin doesn't break existing site functionality
- Ensure good user experience on both admin and frontend
- Validate accessibility features work properly