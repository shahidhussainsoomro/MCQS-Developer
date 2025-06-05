<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function mcqs_dev_admin_categories() {
    global $wpdb;
    // Handle Add
    if (isset($_POST['save_cat']) && !empty($_POST['name'])) {
        $wpdb->insert($wpdb->prefix . 'mcqs_categories', ['name'=>sanitize_text_field($_POST['name'])]);
        echo '<div class="notice notice-success"><p>Category added!</p></div>';
    }
    // Handle Delete
    if (isset($_GET['delete_cat']) && intval($_GET['delete_cat']) > 0) {
        $wpdb->delete($wpdb->prefix . 'mcqs_categories', ['id'=>intval($_GET['delete_cat'])]);
        echo '<div class="notice notice-success"><p>Category deleted!</p></div>';
    }
    // Add form
    echo '<h2>Add Category</h2>
    <form method="post"><input type="text" name="name" required>
    <input type="submit" name="save_cat" class="button" value="Add Category"></form><hr>';
    // List
    $cats = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mcqs_categories ORDER BY name ASC");
    echo '<h2>Categories</h2><table class="widefat"><thead><tr><th>ID</th><th>Name</th><th>Embed</th><th>Actions</th></tr></thead><tbody>';
    foreach($cats as $cat) {
        $embed = '[mcqs_output category="'.$cat->id.'"]';
        echo '<tr>
            <td>'.$cat->id.'</td>
            <td>'.esc_html($cat->name).'</td>
            <td><input type="text" readonly onclick="this.select();" value="'.esc_attr($embed).'" style="width:170px;font-size:12px;"></td>
            <td><a href="?page=mcqs-dev-categories&delete_cat='.$cat->id.'" class="button" onclick="return confirm(\'Delete this category?\')">Delete</a></td>
        </tr>';
    }
    echo '</tbody></table>';
}