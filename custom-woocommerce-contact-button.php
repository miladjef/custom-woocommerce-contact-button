<?php
/**
 * Plugin Name: Custom WooCommerce Contact Button
 * Plugin URI: https://miladjafarigavzan.ir
 * Description: Changes WooCommerce "Add to Cart" button to a "Contact Us" button with customizable text and link.
 * Version: 1.0
 * Author: Milad Jafari Gavzan
 * Author URI: https://miladjafarigavzan.ir
 * License: GPL-2.0+
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Load plugin text domain for translations
function custom_woocommerce_contact_button_load_textdomain() {
    load_plugin_textdomain('custom-woocommerce-contact-button', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'custom_woocommerce_contact_button_load_textdomain');

// Add custom fields for button text and link on the product edit page
function add_contact_button_fields_metabox() {
    add_meta_box(
        'contact_button_fields',
        __('Contact Button Settings', 'custom-woocommerce-contact-button'),
        'contact_button_fields_callback',
        'product',
        'side'
    );
}
add_action('add_meta_boxes', 'add_contact_button_fields_metabox');

// Display custom fields for button text and link in the product editor
function contact_button_fields_callback($post) {
    $button_text = get_post_meta($post->ID, '_contact_button_text', true);
    $button_link = get_post_meta($post->ID, '_contact_button_link', true);
    ?>
    <p>
        <label for="contact_button_text"><?php _e('Button Text', 'custom-woocommerce-contact-button'); ?></label>
        <input type="text" id="contact_button_text" name="contact_button_text" value="<?php echo esc_attr($button_text); ?>" style="width:100%;" />
    </p>
    <p>
        <label for="contact_button_link"><?php _e('Button Link', 'custom-woocommerce-contact-button'); ?></label>
        <input type="text" id="contact_button_link" name="contact_button_link" value="<?php echo esc_attr($button_link); ?>" style="width:100%;" />
    </p>
    <?php
}

// Save custom button text and link
function save_contact_button_fields($post_id) {
    if (isset($_POST['contact_button_text'])) {
        update_post_meta($post_id, '_contact_button_text', sanitize_text_field($_POST['contact_button_text']));
    }
    if (isset($_POST['contact_button_link'])) {
        update_post_meta($post_id, '_contact_button_link', sanitize_text_field($_POST['contact_button_link']));
    }
}
add_action('save_post', 'save_contact_button_fields');

// Replace WooCommerce "Add to Cart" button with the custom button
function custom_replace_add_to_cart_button() {
    global $product;

    $button_text = get_post_meta($product->get_id(), '_contact_button_text', true);
    $button_link = get_post_meta($product->get_id(), '_contact_button_link', true);

    // Use default text if none is provided
    $button_text = $button_text ? esc_html($button_text) : __('Contact Us', 'custom-woocommerce-contact-button');

    // Generate the button HTML
    if ($button_link) {
        echo '<a href="' . esc_url($button_link) . '" class="button contact-us-button" target="_blank">' . $button_text . '</a>';
    } else {
        echo '<a href="#" class="button contact-us-button">' . $button_text . '</a>';
    }
}

// Remove the default "Add to Cart" button and add the custom button
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
add_action('woocommerce_single_product_summary', 'custom_replace_add_to_cart_button', 30);

// Add custom styles for the button
function custom_woocommerce_contact_button_styles() {
    ?>
    <style>
        .contact-us-button {
            background-color: #007cba; /* Button background color */
            color: white; /* Button text color */
            padding: 10px 20px; /* Button padding */
            text-decoration: none; /* No underline */
            border-radius: 5px; /* Rounded corners */
            font-size: 16px; /* Font size */
            display: inline-block; /* Block display */
            transition: background-color 0.3s; /* Smooth transition */
        }
        .contact-us-button:hover {
            background-color: #005177; /* Button background color on hover */
        }
    </style>
    <?php
}
add_action('wp_head', 'custom_woocommerce_contact_button_styles');