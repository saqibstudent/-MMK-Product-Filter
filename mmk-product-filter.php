<?php
/*
Plugin Name: MMK Product Filter
Plugin URI: https://github.com/saqibstudent/mmk-product-filter
Description: Advanced WordPress plugin that adds 4-level dependent dropdown product filters for Category, Make, Model, and Year in WooCommerce stores. Features Category integration, AJAX-powered cascading dropdowns, responsive design, and SEO-friendly URLs. Perfect for automotive, electronics, or any industry requiring hierarchical product filtering.
Version: 2.0
Author: Saqib Ali Khan
Author URI: https://expertwebdeveloper.com/
License: MIT
License URI: https://opensource.org/licenses/MIT
Text Domain: mmk
Domain Path: /languages
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
WC requires at least: 3.0
WC tested up to: 8.4
Network: false
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Add Admin Menu
add_action( 'admin_menu', 'mmk_add_admin_menu' );

function mmk_add_admin_menu() {
    add_options_page(
        'MMK Product Filter Settings',
        'MMK Product Filter',
        'manage_options',
        'mmk-product-filter',
        'mmk_options_page'
    );
}

// Register Settings
add_action( 'admin_init', 'mmk_settings_init' );

function mmk_settings_init() {
    register_setting( 'mmk_options_group', 'mmk_settings' );

    add_settings_section(
        'mmk_section',
        __( 'Configure Make, Model, and Year Attributes', 'mmk' ),
        null,
        'mmk-product-filter'
    );

    add_settings_field(
        'mmk_make_attribute',
        __( 'Make Attribute', 'mmk' ),
        'mmk_make_attribute_render',
        'mmk-product-filter',
        'mmk_section'
    );

    add_settings_field(
        'mmk_model_attribute',
        __( 'Model Attribute', 'mmk' ),
        'mmk_model_attribute_render',
        'mmk-product-filter',
        'mmk_section'
    );

    add_settings_field(
        'mmk_year_attribute',
        __( 'Year Attribute', 'mmk' ),
        'mmk_year_attribute_render',
        'mmk-product-filter',
        'mmk_section'
    );
}

// Render Functions
function mmk_make_attribute_render() {
    $options = get_option( 'mmk_settings' );
    $attributes = wc_get_attribute_taxonomies();
    ?>

    <select name="mmk_settings[mmk_make_attribute]">
        <option value=""><?php _e( 'Select Attribute', 'mmk' ); ?></option>
        <?php foreach ( $attributes as $attribute ) : ?>
            <option value="<?php echo esc_attr( $attribute->attribute_name ); ?>" <?php selected( $options['mmk_make_attribute'], $attribute->attribute_name ); ?>>
                <?php echo esc_html( $attribute->attribute_label ); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php
}

function mmk_model_attribute_render() {
    $options = get_option( 'mmk_settings' );
    $attributes = wc_get_attribute_taxonomies();
    ?>

    <select name="mmk_settings[mmk_model_attribute]">
        <option value=""><?php _e( 'Select Attribute', 'mmk' ); ?></option>
        <?php foreach ( $attributes as $attribute ) : ?>
            <option value="<?php echo esc_attr( $attribute->attribute_name ); ?>" <?php selected( $options['mmk_model_attribute'], $attribute->attribute_name ); ?>>
                <?php echo esc_html( $attribute->attribute_label ); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php
}

function mmk_year_attribute_render() {
    $options = get_option( 'mmk_settings' );
    $attributes = wc_get_attribute_taxonomies();
    ?>

    <select name="mmk_settings[mmk_year_attribute]">
        <option value=""><?php _e( 'Select Attribute', 'mmk' ); ?></option>
        <?php foreach ( $attributes as $attribute ) : ?>
            <option value="<?php echo esc_attr( $attribute->attribute_name ); ?>" <?php selected( $options['mmk_year_attribute'], $attribute->attribute_name ); ?>>
                <?php echo esc_html( $attribute->attribute_label ); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php
}

// Settings Page HTML
function mmk_options_page() {
    ?>

    <form action="options.php" method="post">
        <h1><?php _e( 'MMK Product Filter Settings', 'mmk' ); ?></h1>
        <?php
        settings_fields( 'mmk_options_group' );
        do_settings_sections( 'mmk-product-filter' );
        submit_button();
        ?>
    </form>

    <?php
}


// Create Shortcode
add_shortcode( 'mmk_filter', 'mmk_filter_shortcode' );

function mmk_filter_shortcode() {
    $options = get_option( 'mmk_settings' );

    // Get attribute taxonomies
    $make_taxonomy = 'pa_' . $options['mmk_make_attribute'];
    $model_taxonomy = 'pa_' . $options['mmk_model_attribute'];
    $year_taxonomy = 'pa_' . $options['mmk_year_attribute'];

    ob_start();
    ?>

    <div id="mmk-filter">
        <!-- Category Dropdown -->
        <select id="category" name="category">
            <option value=""><?php _e( 'Select Category', 'mmk' ); ?></option>
            <?php
            $categories = get_terms( array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
            ));
            foreach ( $categories as $category ) {
                echo '<option value="' . esc_attr( $category->slug ) . '">' . esc_html( $category->name ) . '</option>';
            }
            ?>
        </select>

        <!-- Make Dropdown -->
        <select id="make" name="make" disabled>
            <option value=""><?php _e( 'Select Make', 'mmk' ); ?></option>
        </select>

        <!-- Model Dropdown -->
        <select id="model" name="model" disabled>
            <option value=""><?php _e( 'Select Model', 'mmk' ); ?></option>
        </select>

        <!-- Year Dropdown -->
        <select id="year" name="year" disabled>
            <option value=""><?php _e( 'Select Year', 'mmk' ); ?></option>
        </select>

        <!-- Filter Button -->
        <button id="filter-button" disabled><?php _e( 'Filter', 'mmk' ); ?></button>
    </div>

    <?php
    return ob_get_clean();
}


// Enqueue Scripts and Styles
add_action( 'wp_enqueue_scripts', 'mmk_enqueue_assets' );

function mmk_enqueue_assets() {
   
        // Enqueue JavaScript File
        wp_enqueue_script( 'mmk-filter', plugins_url( 'js/mmk-filter.js', __FILE__ ), array( 'jquery' ), '6.0', true );

        // Localize Script Variables
        wp_localize_script( 'mmk-filter', 'mmk_ajax', array(
            'ajax_url'          => admin_url( 'admin-ajax.php' ),
            'shop_url'          => wc_get_page_permalink( 'shop' ),
            'loading_text'      => __( 'Loading...', 'mmk' ),
            'select_make_text'  => __( 'Select Make', 'mmk' ),
            'select_model_text' => __( 'Select Model', 'mmk' ),
            'select_year_text'  => __( 'Select Year', 'mmk' ),
        ));

        // Enqueue CSS File
        wp_enqueue_style( 'mmk-filter-styles', plugins_url( 'css/mmk-filter.css', __FILE__ ) );
   
}

// AJAX Handler for Makes
add_action( 'wp_ajax_mmk_get_makes', 'mmk_get_makes' );
add_action( 'wp_ajax_nopriv_mmk_get_makes', 'mmk_get_makes' );

function mmk_get_makes() {
    $category_slug = sanitize_text_field( $_POST['category'] );
    $options = get_option( 'mmk_settings' );
    $make_taxonomy = 'pa_' . $options['mmk_make_attribute'];

    // Build tax query
    $tax_query = array();

    if ( ! empty( $category_slug ) ) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $category_slug,
        );
    }

    // Get products with the selected category
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'tax_query'      => $tax_query,
        'fields'         => 'ids', // Only get product IDs for efficiency
    );
    $product_ids = get_posts( $args );

    // Get makes from products
    $makes = array();
    if ( ! empty( $product_ids ) ) {
        foreach ( $product_ids as $product_id ) {
            $product_makes = wp_get_post_terms( $product_id, $make_taxonomy );
            foreach ( $product_makes as $make ) {
                $makes[ $make->slug ] = $make->name;
            }
        }
    }

    // Remove duplicates
    $makes = array_unique( $makes );

    // Build options
    if ( ! empty( $makes ) ) {
        $options_html = '<option value="">' . __( 'Select Make', 'mmk' ) . '</option>';
        foreach ( $makes as $slug => $name ) {
            $options_html .= '<option value="' . esc_attr( $slug ) . '">' . esc_html( $name ) . '</option>';
        }
    } else {
        $options_html = '<option value="">' . __( 'No Makes Found', 'mmk' ) . '</option>';
    }

    echo $options_html;
    wp_die();
}

// AJAX Handler for Models
add_action( 'wp_ajax_mmk_get_models', 'mmk_get_models' );
add_action( 'wp_ajax_nopriv_mmk_get_models', 'mmk_get_models' );

function mmk_get_models() {
    $category_slug = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
    $make_slug = sanitize_text_field( $_POST['make'] );
    $options = get_option( 'mmk_settings' );
    $make_taxonomy = 'pa_' . $options['mmk_make_attribute'];
    $model_taxonomy = 'pa_' . $options['mmk_model_attribute'];

    // Build tax query
    $tax_query = array(
        array(
            'taxonomy' => $make_taxonomy,
            'field'    => 'slug',
            'terms'    => $make_slug,
        ),
    );

    if ( ! empty( $category_slug ) ) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $category_slug,
        );
    }

    // Get products with the selected make and category
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'tax_query'      => $tax_query,
        'fields'         => 'ids',
    );
    $product_ids = get_posts( $args );

    // Get models from products
    $models = array();
    if ( ! empty( $product_ids ) ) {
        foreach ( $product_ids as $product_id ) {
            $product_models = wp_get_post_terms( $product_id, $model_taxonomy );
            foreach ( $product_models as $model ) {
                $models[ $model->slug ] = $model->name;
            }
        }
    }

    // Remove duplicates
    $models = array_unique( $models );

    // Build options
    if ( ! empty( $models ) ) {
        $options_html = '<option value="">' . __( 'Select Model', 'mmk' ) . '</option>';
        foreach ( $models as $slug => $name ) {
            $options_html .= '<option value="' . esc_attr( $slug ) . '">' . esc_html( $name ) . '</option>';
        }
    } else {
        $options_html = '<option value="">' . __( 'No Models Found', 'mmk' ) . '</option>';
    }

    echo $options_html;
    wp_die();
}


// AJAX Handler for Years
add_action( 'wp_ajax_mmk_get_years', 'mmk_get_years' );
add_action( 'wp_ajax_nopriv_mmk_get_years', 'mmk_get_years' );

function mmk_get_years() {
    $category_slug = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
    $make_slug = sanitize_text_field( $_POST['make'] );
    $model_slug = sanitize_text_field( $_POST['model'] );
    $options = get_option( 'mmk_settings' );
    $make_taxonomy = 'pa_' . $options['mmk_make_attribute'];
    $model_taxonomy = 'pa_' . $options['mmk_model_attribute'];
    $year_taxonomy = 'pa_' . $options['mmk_year_attribute'];

    // Build tax query
    $tax_query = array(
        'relation' => 'AND',
        array(
            'taxonomy' => $make_taxonomy,
            'field'    => 'slug',
            'terms'    => $make_slug,
        ),
        array(
            'taxonomy' => $model_taxonomy,
            'field'    => 'slug',
            'terms'    => $model_slug,
        ),
    );

    if ( ! empty( $category_slug ) ) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $category_slug,
        );
    }

    // Get products with the selected make, model, and category
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'tax_query'      => $tax_query,
        'fields'         => 'ids',
    );
    $product_ids = get_posts( $args );

    // Get years from products
    $years = array();
    if ( ! empty( $product_ids ) ) {
        foreach ( $product_ids as $product_id ) {
            $product_years = wp_get_post_terms( $product_id, $year_taxonomy );
            foreach ( $product_years as $year ) {
                $years[ $year->slug ] = $year->name;
            }
        }
    }

    // Remove duplicates
    $years = array_unique( $years );

    // Build options
    if ( ! empty( $years ) ) {
        $options_html = '<option value="">' . __( 'Select Year', 'mmk' ) . '</option>';
        foreach ( $years as $slug => $name ) {
            $options_html .= '<option value="' . esc_attr( $slug ) . '">' . esc_html( $name ) . '</option>';
        }
    } else {
        $options_html = '<option value="">' . __( 'No Years Found', 'mmk' ) . '</option>';
    }

    echo $options_html;
    wp_die();
}

// Filter Products on Shop Page
add_action( 'pre_get_posts', 'mmk_filter_products_query' );

function mmk_filter_products_query( $query ) {
    if ( ! is_admin() && $query->is_main_query() && ( is_shop() || is_post_type_archive( 'product' ) ) ) {
        $options = get_option( 'mmk_settings' );
        $make_taxonomy = 'pa_' . $options['mmk_make_attribute'];
        $model_taxonomy = 'pa_' . $options['mmk_model_attribute'];
        $year_taxonomy = 'pa_' . $options['mmk_year_attribute'];

        $tax_query = array();

        if ( isset( $_GET['mmk_category'] ) && $_GET['mmk_category'] != '' ) {
            $category = sanitize_text_field( $_GET['mmk_category'] );
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $category,
            );
        }

        if ( isset( $_GET['mmk_make'] ) && $_GET['mmk_make'] != '' ) {
            $make = sanitize_text_field( $_GET['mmk_make'] );
            $tax_query[] = array(
                'taxonomy' => $make_taxonomy,
                'field'    => 'slug',
                'terms'    => $make,
            );
        }

        if ( isset( $_GET['mmk_model'] ) && $_GET['mmk_model'] != '' ) {
            $model = sanitize_text_field( $_GET['mmk_model'] );
            $tax_query[] = array(
                'taxonomy' => $model_taxonomy,
                'field'    => 'slug',
                'terms'    => $model,
            );
        }

        if ( isset( $_GET['mmk_year'] ) && $_GET['mmk_year'] != '' ) {
            $year = sanitize_text_field( $_GET['mmk_year'] );
            $tax_query[] = array(
                'taxonomy' => $year_taxonomy,
                'field'    => 'slug',
                'terms'    => $year,
            );
        }

        if ( ! empty( $tax_query ) ) {
            $tax_query['relation'] = 'AND';
            $query->set( 'tax_query', $tax_query );
        }
    }
}
