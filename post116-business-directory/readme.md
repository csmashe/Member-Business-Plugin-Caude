# Post 116 Business Directory

A WordPress plugin for American Legion Post 116 to showcase family-owned businesses and services.

## Features

- Custom post type for businesses with comprehensive meta fields
- Hierarchical business categories
- Multi-owner support with individual contact information
- Advanced search with autocomplete functionality
- AJAX-powered directory with category grouping
- JSON-LD schema markup for SEO
- Ownership flags (Veteran, SAL, Auxiliary owned)
- Responsive design following Post 116 branding
- Gutenberg block for easy directory embedding

## Installation

1. Upload the plugin files to `/wp-content/plugins/post116-business-directory/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. The plugin will automatically create a "Business Directory" page at `/directory`
4. Configure settings under Business Directory → Settings

## Usage

### Adding Businesses

1. Go to Business Directory → Add New Business
2. Fill in business details:
   - Title and description
   - Business owners (multiple supported)
   - Contact information
   - Address (city is required)
   - Services offered
   - Additional links
   - Ownership flags
3. Assign to categories as needed
4. Publish the business

### Directory Display

The directory is displayed using the `p116/directory` Gutenberg block, which provides:
- Search functionality with autocomplete
- Category filtering
- Ownership flag filtering
- Paginated results grouped by category
- Responsive card-based layout

### Customization

Settings can be configured under Business Directory → Settings:
- Directory page selection
- Results per page
- Color scheme customization
- Custom CSS
- Ownership flag display toggle

## Technical Details

### Post Type: `p116_business`
- Public: Yes
- Supports: title, editor, thumbnail, excerpt, revisions
- Archive: `/directory`
- Single: `/directory/{business-slug}`

### Taxonomy: `p116_business_category`
- Hierarchical: Yes
- Archive: `/directory/category/{category-slug}`

### Meta Fields
- Owners (repeatable): name, role, email, phone, website
- Contact: phone, email, website
- Address: city (required), address1, address2, state, postal_code
- Flags: veteran_owned, sons_owned, auxiliary_owned
- Links (repeatable): label, URL
- Services offered
- Show in directory toggle
- Search helpers: owners_search, city_search

### REST API
- `GET /wp-json/p116/v1/search` - Search businesses with filtering
- `GET /wp-json/p116/v1/autocomplete` - Autocomplete suggestions

### Capabilities
- `read_business`, `edit_business`, `delete_business` (and variations)
- `manage_business_categories`

## Legal Disclaimer

The following disclaimer is displayed on all directory pages:

"American Legion Post 116 is not liable for or endorsing any listed businesses. Please independently verify their work quality, licenses, and insurance."

## Styling

The plugin follows Post 116's color scheme:
- Primary (Legion Red): `#c41e3a`
- Secondary (Navy): `#003366`
- Accent (Gold): `#ffd700`

CSS custom properties are available for easy customization:
- `--p116-primary-color`
- `--p116-secondary-color`
- `--p116-accent-color`
- `--p116-text-color`
- `--p116-light-bg`
- `--p116-border-color`
- `--p116-shadow`

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11+ for basic functionality
- Mobile responsive design

## Plugin Structure

```
post116-business-directory/
├── post116-business-directory.php  # Main plugin file
├── includes/                       # PHP classes
├── public/                        # Frontend assets
├── templates/                     # Template files
├── blocks/                        # Gutenberg blocks
├── languages/                     # Translation files
└── readme.md                      # This file
```

## Development

The plugin is built with:
- WordPress coding standards
- REST API for AJAX functionality
- Gutenberg blocks API
- Modern JavaScript (ES6+)
- CSS Grid and Flexbox
- Mobile-first responsive design

## Support

For support, please contact American Legion Post 116 or check the plugin documentation.

## Version History

### 1.0.0
- Initial release
- Custom post type and taxonomy
- Search and filtering
- Gutenberg block
- Admin interface
- JSON-LD schema
- Settings page