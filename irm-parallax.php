<?php
/**
 * Plugin Name: IRM Parallax
 * Description: A custom Elementor widget for a parallax effect with repeater fields.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Ensure Elementor is installed and active
function irm_parallax_check_elementor() {
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', function() {
            echo '<div class="error"><p>IRM Parallax requires Elementor to be installed and activated.</p></div>';
        });
        return false;
    }
    return true;
}

// Enqueue scripts and styles
function irm_parallax_enqueue_scripts() {
    wp_enqueue_style('irm-normalize', plugin_dir_url(__FILE__) . 'assets/normalize.css', [], '1.0.0');
    wp_enqueue_style('irm-style', plugin_dir_url(__FILE__) . 'assets/style.css', [], '1.0.0');
    wp_enqueue_script('irm-main', plugin_dir_url(__FILE__) . 'assets/main.js', ['jquery'], '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'irm_parallax_enqueue_scripts');



// Register widget
function irm_register_parallax_widget($widgets_manager) {
    if (!irm_parallax_check_elementor()) {
        return;
    }

    require_once plugin_dir_path(__FILE__) . 'widget-parallax.php';
    $widgets_manager->register(new \IRM_Parallax_Widget());
}
add_action('elementor/widgets/register', 'irm_register_parallax_widget');

// Register plugin category (optional)
function irm_add_elementor_widget_categories($elements_manager) {
    $elements_manager->add_category(
        'irm-category',
        [
            'title' => esc_html__('IRM Widgets', 'irm-parallax'),
            'icon' => 'fa fa-plug',
        ]
    );
}
add_action('elementor/elements/categories_registered', 'irm_add_elementor_widget_categories');
