<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function mcqs_dev_admin_questions() {
    global $wpdb;

    // Handle Add/Edit
    if (isset($_POST['save_mcq'])) {
        $data = [
            'question' => sanitize_text_field($_POST['question']),
            'option_a' => sanitize_text_field($_POST['option_a']),
            'option_b' => sanitize_text_field($_POST['option_b']),
            'option_c' => sanitize_text_field($_POST['option_c']),
            'option_d' => sanitize_text_field($_POST['option_d']),
            'correct_option' => sanitize_text_field($_POST['correct_option']),
            'explanation' => sanitize_textarea_field($_POST['explanation']),
            'difficulty' => sanitize_text_field($_POST['difficulty']),
            'category_id' => intval($_POST['category_id']),
        ];
        if (!empty($_POST['mcq_id'])) {
            $wpdb->update($wpdb->prefix . 'mcqs', $data, ['id' => intval($_POST['mcq_id'])]);
            echo '<div class="notice notice-success"><p>MCQ updated!</p></div>';
        } else {
            $wpdb->insert($wpdb->prefix . 'mcqs', $data);
            echo '<div class="notice notice-success"><p>MCQ added!</p></div>';
        }
    }
    // Handle Delete
    if (isset($_GET['delete']) && intval($_GET['delete']) > 0) {
        $wpdb->delete($wpdb->prefix . 'mcqs', ['id'=>intval($_GET['delete'])]);
        echo '<div class="notice notice-success"><p>MCQ deleted!</p></div>';
    }

    // Render Add/Edit Form
    $edit_mcq = null;
    if (isset($_GET['edit']) && intval($_GET['edit']) > 0) {
        $edit_mcq = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mcqs WHERE id=%d", intval($_GET['edit'])));
    }
    $table_cat = $wpdb->prefix . 'mcqs_categories';
    $categories = $wpdb->get_results("SELECT * FROM $table_cat ORDER BY name ASC");
    ?>
    <h2><?php echo $edit_mcq ? 'Edit MCQ' : 'Add MCQ'; ?></h2>
    <form method="post" action="">
        <input type="hidden" name="mcq_id" id="mcq_id" value="<?php echo esc_attr($edit_mcq->id ?? ''); ?>">
        <table class="form-table">
            <tr>
                <th><label for="question">Question</label></th>
                <td><textarea name="question" id="question" class="regular-text" rows="3" required><?php echo esc_textarea($edit_mcq->question ?? ''); ?></textarea></td>
            </tr>
            <tr><th>Option A</th><td><input name="option_a" id="option_a" type="text" class="regular-text" required value="<?php echo esc_attr($edit_mcq->option_a ?? ''); ?>" /></td></tr>
            <tr><th>Option B</th><td><input name="option_b" id="option_b" type="text" class="regular-text" required value="<?php echo esc_attr($edit_mcq->option_b ?? ''); ?>" /></td></tr>
            <tr><th>Option C</th><td><input name="option_c" id="option_c" type="text" class="regular-text" required value="<?php echo esc_attr($edit_mcq->option_c ?? ''); ?>" /></td></tr>
            <tr><th>Option D</th><td><input name="option_d" id="option_d" type="text" class="regular-text" required value="<?php echo esc_attr($edit_mcq->option_d ?? ''); ?>" /></td></tr>
            <tr>
                <th><label for="correct_option">Correct Option</label></th>
                <td>
                    <select name="correct_option" id="correct_option" required>
                        <?php foreach(['A','B','C','D'] as $o): ?>
                        <option value="<?php echo $o; ?>" <?php selected($edit_mcq->correct_option??'', $o); ?>><?php echo $o; ?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="explanation">Explanation</label></th>
                <td>
                    <textarea name="explanation" id="explanation" class="regular-text" rows="2" placeholder="Explain why this is the correct answer..."><?php echo esc_textarea($edit_mcq->explanation ?? ''); ?></textarea>
                </td>
            </tr>
            <tr>
                <th><label for="difficulty">Difficulty</label></th>
                <td>
                    <select name="difficulty" id="difficulty" required>
                        <?php foreach(['Easy','Medium','Hard'] as $diff): ?>
                        <option value="<?php echo $diff; ?>" <?php selected($edit_mcq->difficulty??'', $diff); ?>><?php echo $diff; ?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="category_id">Category</label></th>
                <td>
                    <select name="category_id" id="category_id" required>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo esc_attr($cat->id); ?>" <?php selected($edit_mcq->category_id??'', $cat->id); ?>><?php echo esc_html($cat->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        <p><input type="submit" name="save_mcq" class="button button-primary" value="<?php echo $edit_mcq ? 'Update MCQ' : 'Add MCQ'; ?>"></p>
    </form>
    <hr>
    <?php
    // MCQ Table
    $mcqs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mcqs ORDER BY id DESC LIMIT 50");
    echo '<h2>MCQ List</h2><table class="widefat mcqs-admin-table"><thead><tr>
        <th>ID</th><th>Question</th><th>Correct</th><th>Category</th><th>Embed</th><th>Actions</th></tr></thead><tbody>';
    foreach($mcqs as $mcq) {
        $cat_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM $table_cat WHERE id=%d", $mcq->category_id));
        // Embedding option: show shortcode for this category
        $embed = '[mcqs_output category="'.$mcq->category_id.'"]';
        echo '<tr>
            <td>'.$mcq->id.'</td>
            <td style="max-width:200px;">'.esc_html($mcq->question).'</td>
            <td>'.esc_html($mcq->correct_option).'</td>
            <td>'.esc_html($cat_name).'</td>
            <td><input type="text" readonly onclick="this.select();" value="'.esc_attr($embed).'" style="width:185px;font-size:12px;"></td>
            <td>
                <a href="?page=mcqs-dev-questions&edit='.$mcq->id.'" class="button">Edit</a>
                <a href="?page=mcqs-dev-questions&delete='.$mcq->id.'" class="button" onclick="return confirm(\'Delete this MCQ?\')">Delete</a>
            </td>
        </tr>';
    }
    echo '</tbody></table>';
}