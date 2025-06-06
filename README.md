# MMK Product Filter - WordPress Plugin

A powerful WordPress plugin that adds **4-level dependent dropdown product filters** for Category, Make, Model, and Year in WooCommerce stores. Perfect for automotive, electronics, or any industry requiring advanced hierarchical product filtering.

![Version](https://img.shields.io/badge/version-2.0-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)
![WooCommerce](https://img.shields.io/badge/WooCommerce-3.0%2B-purple.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4.svg)

## 🚀 Features

- **4-Level Dependent Filtering**: Category → Make → Model → Year cascading dropdowns
- **Category Integration**: Works with WooCommerce product categories as the first filter level
- **AJAX-Powered**: Smooth, fast filtering without page reloads
- **WooCommerce Integration**: Seamlessly works with WooCommerce product attributes and categories
- **Responsive Design**: Mobile-friendly interface that works on all devices
- **Easy Configuration**: Simple admin panel to map your product attributes
- **Shortcode Support**: Easy implementation with `[mmk_filter]` shortcode
- **Customizable Styling**: Includes CSS for easy customization
- **SEO Friendly**: URL parameters for filtered results support direct linking
- **Performance Optimized**: Efficient queries using product IDs for better performance

## 📋 Requirements

- WordPress 5.0 or higher
- WooCommerce 3.0 or higher
- PHP 7.4 or higher
- Product attributes configured in WooCommerce

## 🔧 Installation

1. **Download the Plugin**
   - Download the plugin files from this repository
   - Or clone: `git clone https://github.com/saqibstudent/mmk-product-filter.git`

2. **Upload to WordPress**
   - Upload the `mmk-product-filter` folder to `/wp-content/plugins/`
   - Or install via WordPress admin: Plugins → Add New → Upload Plugin

3. **Activate the Plugin**
   - Go to Plugins in your WordPress admin
   - Find "MMK Product Filter" and click "Activate"

4. **Configure Settings**
   - Navigate to Settings → MMK Product Filter
   - Map your WooCommerce attributes to Make, Model, and Year fields

## ⚙️ Configuration

### Step 1: Create Product Categories & Attributes
1. **Categories**: Go to Products → Categories and create your main product categories
2. **Attributes**: Go to WooCommerce → Attributes and create attributes for:
   - Make (e.g., "Brand", "Manufacturer")
   - Model (e.g., "Model", "Series")
   - Year (e.g., "Year", "Model Year")

### Step 2: Configure Plugin Settings
1. Go to WordPress Admin → Settings → MMK Product Filter
2. Map each dropdown to your WooCommerce attributes:
   - **Make Attribute**: Select your brand/manufacturer attribute
   - **Model Attribute**: Select your model/series attribute
   - **Year Attribute**: Select your year attribute
3. Save settings

### Step 3: Add Products
1. Assign products to appropriate categories
2. Ensure your products have the mapped attributes assigned with appropriate values

## 📖 Usage

### Display the Filter
Add the shortcode anywhere you want the filter to appear:

```php
[mmk_filter]
```

**Common locations:**
- Shop page sidebar
- Product archive pages
- Custom landing pages
- Widget areas

### Filter Behavior
1. **Category Selection**: Choose a category to populate make dropdown
2. **Make Selection**: Choose a make to populate model dropdown
3. **Model Selection**: Choose a model to populate year dropdown
4. **Year Selection**: Enables the filter button
5. **Filter**: Redirects to shop page with filtered results

### URL Parameters
The plugin creates SEO-friendly URLs:
```
yoursite.com/shop/?mmk_category=electronics&mmk_make=samsung&mmk_model=galaxy&mmk_year=2023
```

## 🎨 Customization

### CSS Styling
The plugin includes `css/mmk-filter.css` with:
- Responsive flexbox layout
- Modern button and dropdown styling
- Mobile-first design
- Hover effects

**Customize by:**
1. Editing the CSS file directly
2. Adding custom CSS to your theme
3. Using WordPress Customizer

### PHP Hooks
The plugin provides several hooks for customization:

```php
// Modify AJAX responses
add_filter('mmk_models_query_args', 'your_custom_function');
add_filter('mmk_years_query_args', 'your_custom_function');

// Customize dropdown HTML
add_filter('mmk_dropdown_html', 'your_custom_function');
```

## 🔄 How It Works

### Backend Architecture
1. **Settings API**: Uses WordPress Settings API for configuration
2. **AJAX Handlers**: Custom AJAX endpoints for dynamic dropdowns (Categories → Makes → Models → Years)
3. **Query Modification**: Hooks into WooCommerce product queries
4. **Taxonomy Integration**: Works with WooCommerce product categories and attributes
5. **Performance Optimization**: Uses product IDs for efficient database queries

### Frontend Flow
1. Category dropdown populated from WooCommerce categories
2. AJAX call loads makes based on selected category
3. AJAX call loads models based on selected category + make
4. AJAX call loads years based on selected category + make + model
5. Filter button redirects with URL parameters
6. Shop page filters products using tax_query with category and attribute filters

## 🛠️ Development

### File Structure
```
mmk-product-filter/
├── mmk-product-filter.php    # Main plugin file
├── css/
│   └── mmk-filter.css       # Frontend styles
├── js/
│   └── mmk-filter.js        # AJAX functionality
├── README.md                # This file
└── documentation.html       # HTML documentation
```

### Key Functions
- `mmk_filter_shortcode()`: Renders the 4-level filter form (Category/Make/Model/Year)
- `mmk_get_makes()`: AJAX handler for make dropdown based on category
- `mmk_get_models()`: AJAX handler for model dropdown based on category + make
- `mmk_get_years()`: AJAX handler for year dropdown based on category + make + model
- `mmk_filter_products_query()`: Filters shop page products using category and attribute filters

## 🐛 Troubleshooting

### Common Issues

**Filter not showing:**
- Verify shortcode placement: `[mmk_filter]`
- Check if WooCommerce is active
- Ensure attributes are configured
- Verify product categories exist

**Dropdowns empty:**
- Verify attribute mapping in plugin settings
- Ensure products have attribute values assigned
- Check attribute visibility settings
- Ensure products are assigned to categories

**Category dropdown empty:**
- Check if product categories exist
- Ensure categories have products assigned
- Verify categories are not empty

**AJAX not working:**
- Check browser console for JavaScript errors
- Verify jQuery is loaded
- Test with default WordPress theme

**No products filtered:**
- Ensure shop page is being used
- Check product attribute assignments
- Verify taxonomy names match

## 📞 Support & Contact

**Developer**: Saqib Ali Khan

- **GitHub**: [https://github.com/saqibstudent/](https://github.com/saqibstudent/)
- **Website**: [https://expertwebdeveloper.com/](https://expertwebdeveloper.com/)
- **WhatsApp/Call**: +447448 418213

### Getting Help
1. Check this documentation first
2. Review the troubleshooting section
3. Contact via WhatsApp for quick support
4. Visit website for more WordPress solutions

## 📄 License

This plugin is released under the MIT License. Feel free to use and modify for your projects.

## 🚀 Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Submit a pull request

## 📝 Changelog

### Version 2.0
- **NEW**: Added Category as first filter level
- **ENHANCED**: 4-level dependent filtering (Category → Make → Model → Year)
- **IMPROVED**: Performance optimization using product IDs
- **UPDATED**: URL parameters now include category (mmk_category, mmk_make, mmk_model, mmk_year)
- **ENHANCED**: Better AJAX error handling and loading states

### Version 1.0
- Initial release
- 3-level dependent dropdown functionality (Make → Model → Year)
- AJAX-powered filtering
- Responsive design
- Admin configuration panel
- WooCommerce integration

---

**Made with ❤️ for the WordPress community**

For professional WordPress development services, visit [expertwebdeveloper.com](https://expertwebdeveloper.com/)
