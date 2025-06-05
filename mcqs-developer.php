<?php
/*
Plugin Name: MCQS Developer
Description: Manage Multiple Choice Questions (MCQs) with quizzes, import/export, analytics, user quiz functionality, category embedding, SEO/Yoast support, and full admin menu.
Version: 2.0.0
Author: shahidhussainsoomro
Text Domain: mcqs-developer
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'MCQS_DEV_PATH', plugin_dir_path( __FILE__ ) );
define( 'MCQS_DEV_URL', plugin_dir_url( __FILE__ ) );

// --- Activation logic ---
function mcqs_dev_plugin_activate() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // MCQs table with explanation
    $table_mcqs = $wpdb->prefix . 'mcqs';
    $sql_mcqs = "CREATE TABLE $table_mcqs (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        question TEXT NOT NULL,
        option_a VARCHAR(255) NOT NULL,
        option_b VARCHAR(255) NOT NULL,
        option_c VARCHAR(255) NOT NULL,
        option_d VARCHAR(255) NOT NULL,
        correct_option ENUM('A','B','C','D') NOT NULL,
        explanation TEXT NULL,
        difficulty ENUM('Easy','Medium','Hard') NOT NULL DEFAULT 'Easy',
        category_id BIGINT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    // Categories table
    $table_categories = $wpdb->prefix . 'mcqs_categories';
    $sql_categories = "CREATE TABLE $table_categories (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE
    ) $charset_collate;";

    // Attempts table for analytics
    $table_attempts = $wpdb->prefix . 'mcqs_attempts';
    $sql_attempts = "CREATE TABLE $table_attempts (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        mcq_id BIGINT UNSIGNED NOT NULL,
        selected_option ENUM('A','B','C','D') NOT NULL,
        is_correct TINYINT(1) NOT NULL,
        attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX(user_id), INDEX(mcq_id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql_mcqs );
    dbDelta( $sql_categories );
    dbDelta( $sql_attempts );
}
register_activation_hook( __FILE__, 'mcqs_dev_plugin_activate' );

// --- Enqueue Modern Styles, Font Awesome, and JS on frontend and admin ---
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'mcqs-frontend-css', MCQS_DEV_URL . 'assets/css/modern-quiz.css', [], '2.0.0' );
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', [], null );
    wp_enqueue_script( 'mcqs-frontend-js', MCQS_DEV_URL . 'assets/js/modern-quiz.js', ['jquery'], '2.0.0', true );
    wp_localize_script( 'mcqs-frontend-js', 'mcqsDev', [
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
    ]);
});
add_action( 'admin_enqueue_scripts', function($hook) {
    if ( strpos($hook, 'mcqs-dev') !== false ) {
        wp_enqueue_style( 'mcqs-admin-css', MCQS_DEV_URL . 'assets/css/modern-quiz.css', [], '2.0.0' );
        wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', [], null );
        wp_enqueue_script( 'mcqs-admin-js', MCQS_DEV_URL . 'assets/js/admin.js', ['jquery'], '2.0.0', true );
        wp_localize_script( 'mcqs-admin-js', 'mcqsDev', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'mcqs_admin_nonce' ),
        ]);
    }
});

// --- Includes ---
require_once MCQS_DEV_PATH . 'includes/admin-dashboard.php';
require_once MCQS_DEV_PATH . 'includes/user-dashboard.php';
require_once MCQS_DEV_PATH . 'includes/class-mcqs-developer-widget.php';
require_once MCQS_DEV_PATH . 'includes/class-mcqs-developer-stats-widget.php';

// --- Register widgets ---
add_action('widgets_init', function() {
    register_widget('MCQS_Developer_Widget');
    register_widget('MCQS_Developer_Stats_Widget');
});

// --- SEO/Yoast Friendly Shortcode: [mcqs_output category="ID|Name" limit="N"] ---
add_shortcode( 'mcqs_output', 'mcqs_dev_output_shortcode' );
function mcqs_dev_output_shortcode( $atts ) {
    $atts = shortcode_atts([
        'category' => '', // Accepts numeric ID or category name
        'limit' => 10
    ], $atts, 'mcqs_output');
    ob_start();
    do_action( 'mcqs_dev_render_frontend_quiz', $atts );
    return ob_get_clean();
}

// --- Copyright ---
add_action('wp_footer', function() {
    ?>
    <div style="text-align:center; font-size:0.95em; color:#888; margin: 24px 0 8px;">
        &copy; <?php echo date('Y'); ?> MCQS Developer Plugin. All Rights Reserved.<br>
        <i class="fa-solid fa-envelope"></i> Contact: <a href="mailto:shahidsoomro786@gmail.com">shahidsoomro786@gmail.com</a>
    </div>
    <?php
});
add_filter('admin_footer_text', function($footer_text) {
    $footer_text .= ' | &copy; ' . date('Y') . ' MCQS Developer Plugin. Contact: <a href="mailto:shahidsoomro786@gmail.com">shahidsoomro786@gmail.com</a>';
    return $footer_text;
});