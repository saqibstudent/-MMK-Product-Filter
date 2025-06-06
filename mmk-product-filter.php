<?php
/*
Plugin Name: MMK Product Filter
Plugin URI: https://github.com/saqibstudent/mmk-product-filter
Description: Advanced WordPress plugin that adds dependent dropdown product filters for Make, Model, and Year in WooCommerce stores. Perfect for automotive, electronics, or any industry requiring hierarchical product filtering. Features AJAX-powered cascading dropdowns, responsive design, and SEO-friendly URLs.
Version: 1.0
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
        <select id="make" name="make">
            <option value=""><?php _e( 'Select Make', 'mmk' ); ?></option>
            <?php
            $makes = get_terms( array(
                'taxonomy' => $make_taxonomy,
                'hide_empty' => true,
            ));
            foreach ( $makes as $make ) {
                echo '<option value="' . esc_attr( $make->slug ) . '">' . esc_html( $make->name ) . '</option>';
            }
            ?>
        </select>

        <select id="model" name="model" disabled>
            <option value=""><?php _e( 'Select Model', 'mmk' ); ?></option>
        </select>

        <select id="year" name="year" disabled>
            <option value=""><?php _e( 'Select Year', 'mmk' ); ?></option>
        </select>

        <button id="filter-button" disabled><?php _e( 'Filter', 'mmk' ); ?></button>
    </div>

    <?php
    return ob_get_clean();
}



// Enqueue Scripts
add_action( 'wp_enqueue_scripts', 'mmk_enqueue_scripts' );

function mmk_enqueue_scripts() {
    // Only enqueue script where the shortcode is used
    if ( ! is_admin() && has_shortcode( get_post( get_the_ID() )->post_content, 'mmk_filter' ) ) {
        wp_enqueue_script( 'mmk-filter', plugins_url( 'js/mmk-filter.js', __FILE__ ), array( 'jquery' ), '1.0', true );

        wp_localize_script( 'mmk-filter', 'mmk_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'shop_url' => get_permalink( wc_get_page_id( 'shop' ) ),
            'loading_text' => __( 'Loading...', 'mmk' ),
            'select_model_text' => __( 'Select Model', 'mmk' ),
            'select_year_text' => __( 'Select Year', 'mmk' ),
        ));
        if ( ! is_admin() && has_shortcode( get_post( get_the_ID() )->post_content, 'mmk_filter' ) ) {
            wp_enqueue_style( 'mmk-filter-styles', plugins_url( 'css/mmk-filter.css', __FILE__ ) );
        }
    }
}





// AJAX Handler for Models
add_action( 'wp_ajax_mmk_get_models', 'mmk_get_models' );
add_action( 'wp_ajax_nopriv_mmk_get_models', 'mmk_get_models' );

function mmk_get_models() {
    $make_slug = sanitize_text_field( $_POST['make'] );
    $options = get_option( 'mmk_settings' );
    $make_taxonomy = 'pa_' . $options['mmk_make_attribute'];
    $model_taxonomy = 'pa_' . $options['mmk_model_attribute'];

    // Get products with the selected make
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => $make_taxonomy,
                'field'    => 'slug',
                'terms'    => $make_slug,
            ),
        ),
    );
    $products = get_posts( $args );

    // Get models from products
    $models = array();
    foreach ( $products as $product ) {
        $product_id = $product->ID;
        $product_models = wp_get_post_terms( $product_id, $model_taxonomy );
        foreach ( $product_models as $model ) {
            $models[ $model->slug ] = $model->name;
        }
    }

    // Remove duplicates
    $models = array_unique( $models );

    // Build options
    
    $options_html = '<option value="">' . __( 'Select Model', 'mmk' ) . '</option>';
    if ( empty( $models ) ) {
        $options_html = '<option value="">' . __( 'No Models Found', 'mmk' ) . '</option>';
    }
    else{
        foreach ( $models as $slug => $name ) {
            $options_html .= '<option value="' . esc_attr( $slug ) . '">' . esc_html( $name ) . '</option>';
        }
    }
    

    echo $options_html;
    wp_die();
}



// AJAX Handler for Years
add_action( 'wp_ajax_mmk_get_years', 'mmk_get_years' );
add_action( 'wp_ajax_nopriv_mmk_get_years', 'mmk_get_years' );

function mmk_get_years() {
    $make_slug = sanitize_text_field( $_POST['make'] );
    $model_slug = sanitize_text_field( $_POST['model'] );
    $options = get_option( 'mmk_settings' );
    $make_taxonomy = 'pa_' . $options['mmk_make_attribute'];
    $model_taxonomy = 'pa_' . $options['mmk_model_attribute'];
    $year_taxonomy = 'pa_' . $options['mmk_year_attribute'];

    // Get products with the selected make and model
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => array(
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
        ),
    );
    $products = get_posts( $args );

    // Get years from products
    $years = array();
    foreach ( $products as $product ) {
        $product_id = $product->ID;
        $product_years = wp_get_post_terms( $product_id, $year_taxonomy );
        foreach ( $product_years as $year ) {
            $years[ $year->slug ] = $year->name;
        }
    }

    // Remove duplicates
    $years = array_unique( $years );

    // Build options
    $options_html = '<option value="">' . __( 'Select Year', 'mmk' ) . '</option>';
    if ( empty( $years ) ) {
        $options_html = '<option value="">' . __( 'No Years Found', 'mmk' ) . '</option>';
    }
    else{
        foreach ( $years as $slug => $name ) {
            $options_html .= '<option value="' . esc_attr( $slug ) . '">' . esc_html( $name ) . '</option>';
        }
    }

    echo $options_html;
    wp_die();
}



// Filter Products on Shop Page
add_action( 'pre_get_posts', 'mmk_filter_products_query' );

function mmk_filter_products_query( $query ) {
    if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'product' ) ) {
        $options = get_option( 'mmk_settings' );
        $make_taxonomy = 'pa_' . $options['mmk_make_attribute'];
        $model_taxonomy = 'pa_' . $options['mmk_model_attribute'];
        $year_taxonomy = 'pa_' . $options['mmk_year_attribute'];

        $tax_query = array();

        if ( isset( $_GET['make'] ) && $_GET['make'] != '' ) {
            $make = sanitize_text_field( $_GET['make'] );
            $tax_query[] = array(
                'taxonomy' => $make_taxonomy,
                'field'    => 'slug',
                'terms'    => $make,
            );
        }

        if ( isset( $_GET['model'] ) && $_GET['model'] != '' ) {
            $model = sanitize_text_field( $_GET['model'] );
            $tax_query[] = array(
                'taxonomy' => $model_taxonomy,
                'field'    => 'slug',
                'terms'    => $model,
            );
        }

        if ( isset( $_GET['year'] ) && $_GET['year'] != '' ) {
            $year = sanitize_text_field( $_GET['year'] );
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
