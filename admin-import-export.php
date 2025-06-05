<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function mcqs_dev_admin_import_export() {
    global $wpdb;
    $table_mcqs = $wpdb->prefix . 'mcqs';

    // Handle import
    if (!empty($_FILES['mcqs_csv_import']['tmp_name'])) {
        $file = fopen($_FILES['mcqs_csv_import']['tmp_name'], 'r');
        $header = fgetcsv($file);
        $count = 0;
        while ($row = fgetcsv($file)) {
            $data = array_combine($header, $row);
            $wpdb->insert($table_mcqs, [
                'question' => sanitize_text_field($data['question']),
                'option_a' => sanitize_text_field($data['option_a']),
                'option_b' => sanitize_text_field($data['option_b']),
                'option_c' => sanitize_text_field($data['option_c']),
                'option_d' => sanitize_text_field($data['option_d']),
                'correct_option' => sanitize_text_field($data['correct_option']),
                'explanation' => sanitize_textarea_field($data['explanation']),
                'difficulty' => sanitize_text_field($data['difficulty']),
                'category_id' => intval($data['category_id']),
            ]);
            $count++;
        }
        fclose($file);
        echo '<div class="notice notice-success"><p>Imported ' . $count . ' MCQs!</p></div>';
    }

    // Handle export
    if (isset($_GET['mcqs_csv_export'])) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=mcqs-export-'.date('Ymd').'.csv');
        $output = fopen('php://output', 'w');
        $rows = $wpdb->get_results("SELECT * FROM $table_mcqs", ARRAY_A);
        if ($rows) {
            fputcsv($output, array_keys($rows[0]));
            foreach ($rows as $row) fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    // UI
    echo '<div class="wrap"><h2>Import/Export MCQs</h2>
    <form method="post" enctype="multipart/form-data">
        <p><strong>Import CSV:</strong> <input type="file" name="mcqs_csv_import" required> <input type="submit" class="button" value="Import"></p>
        <p>CSV columns: question, option_a, option_b, option_c, option_d, correct_option, explanation, difficulty, category_id</p>
    </form>
    <hr>
    <form method="get">
        <input type="hidden" name="page" value="mcqs-dev-import-export">
        <button class="button button-primary" name="mcqs_csv_export" value="1">Export All MCQs to CSV</button>
    </form>
    </div>';
}