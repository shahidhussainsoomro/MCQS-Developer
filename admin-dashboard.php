<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('admin_menu', 'mcqs_dev_admin_menu');
function mcqs_dev_admin_menu() {
    add_menu_page(
        'MCQS Developer', 'MCQS Developer', 'manage_options', 'mcqs-dev', 'mcqs_dev_admin_home_page',
        'dashicons-welcome-learn-more', 6
    );
    add_submenu_page('mcqs-dev', 'Questions', 'Questions', 'manage_options', 'mcqs-dev-questions', 'mcqs_dev_admin_questions_page');
    add_submenu_page('mcqs-dev', 'Categories', 'Categories', 'manage_options', 'mcqs-dev-categories', 'mcqs_dev_admin_categories_page');
    add_submenu_page('mcqs-dev', 'Analytics', 'Analytics', 'manage_options', 'mcqs-dev-analytics', 'mcqs_dev_admin_analytics_page');
    add_submenu_page('mcqs-dev', 'Import/Export', 'Import/Export', 'manage_options', 'mcqs-dev-import-export', 'mcqs_dev_admin_import_export_page');
}

// --- Home Page with Feature Buttons ---
function mcqs_dev_admin_home_page() {
    ?>
    <div class="wrap">
        <h1>MCQS Developer</h1>
        <p>Welcome to MCQS Developer! Use the buttons below or the side menu to manage everything:</p>
        <div style="display: flex; flex-wrap: wrap; gap: 24px; margin: 28px 0;">
            <a href="<?php echo admin_url('admin.php?page=mcqs-dev-questions'); ?>" class="mcqs-admin-btn">
                <i class="fa fa-list-alt"></i> Manage Questions
            </a>
            <a href="<?php echo admin_url('admin.php?page=mcqs-dev-categories'); ?>" class="mcqs-admin-btn">
                <i class="fa fa-folder-open"></i> Manage Categories
            </a>
            <a href="<?php echo admin_url('admin.php?page=mcqs-dev-analytics'); ?>" class="mcqs-admin-btn">
                <i class="fa fa-chart-bar"></i> Analytics
            </a>
            <a href="<?php echo admin_url('admin.php?page=mcqs-dev-import-export'); ?>" class="mcqs-admin-btn">
                <i class="fa fa-upload"></i> Import/Export
            </a>
            <a href="https://wordpress.org/support/plugin/mcqs-developer/" class="mcqs-admin-btn" target="_blank" rel="noopener">
                <i class="fa fa-question-circle"></i> Help & Support
            </a>
        </div>
        <hr>
        <h2 style="margin-top:32px;">Quick Embedding</h2>
        <p>Embed quizzes anywhere using the shortcode:<br>
            <code>[mcqs_output category="CATEGORY_ID" limit="10"]</code>
        </p>
        <ul style="margin-top:10px;">
            <li><strong>category</strong>: Category ID or name (see <a href="<?php echo admin_url('admin.php?page=mcqs-dev-categories'); ?>">Categories</a>)</li>
            <li><strong>limit</strong>: Number of questions to show (default 10)</li>
        </ul>
        <p style="margin-top:16px;">Example: <code>[mcqs_output category="Math" limit="5"]</code></p>
    </div>
    <style>
    .mcqs-admin-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #009688;
        color: #fff !important;
        padding: 18px 28px;
        border-radius: 8px;
        font-size: 1.14em;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        transition: background 0.2s, box-shadow 0.2s;
        border: none;
    }
    .mcqs-admin-btn:hover, .mcqs-admin-btn:focus {
        background: #00796b;
        color: #fff !important;
        box-shadow: 0 4px 16px rgba(44,62,80,0.13);
        text-decoration: none;
    }
    .mcqs-admin-btn i {
        font-size: 1.25em;
    }
    </style>
    <?php
}

// --- Questions Page ---
function mcqs_dev_admin_questions_page() {
    require_once __DIR__.'/admin-questions.php';
    mcqs_dev_admin_questions();
}

// --- Categories Page ---
function mcqs_dev_admin_categories_page() {
    require_once __DIR__.'/admin-categories.php';
    mcqs_dev_admin_categories();
}

// --- Analytics Page ---
function mcqs_dev_admin_analytics_page() {
    require_once __DIR__.'/admin-analytics.php';
    mcqs_dev_admin_analytics();
}

// --- Import/Export Page ---
function mcqs_dev_admin_import_export_page() {
    require_once __DIR__.'/admin-import-export.php';
    mcqs_dev_admin_import_export();
}