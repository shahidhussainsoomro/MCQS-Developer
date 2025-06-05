<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function mcqs_dev_admin_analytics() {
    global $wpdb;
    $table_attempts = $wpdb->prefix . 'mcqs_attempts';
    $table_mcqs = $wpdb->prefix . 'mcqs';

    // Overall stats
    $total_attempts = $wpdb->get_var("SELECT COUNT(*) FROM $table_attempts");
    $correct_attempts = $wpdb->get_var("SELECT COUNT(*) FROM $table_attempts WHERE is_correct=1");
    $incorrect_attempts = $wpdb->get_var("SELECT COUNT(*) FROM $table_attempts WHERE is_correct=0");
    $top_users = $wpdb->get_results("SELECT user_id, COUNT(*) as cnt FROM $table_attempts GROUP BY user_id ORDER BY cnt DESC LIMIT 5");
    $top_questions = $wpdb->get_results("SELECT mcq_id, COUNT(*) as cnt FROM $table_attempts GROUP BY mcq_id ORDER BY cnt DESC LIMIT 5");

    echo '<div class="wrap"><h2>Quiz Analytics</h2>
    <ul style="font-size:1.1em;">
        <li><strong>Total Attempts:</strong> '.intval($total_attempts).'</li>
        <li><strong>Correct Answers:</strong> '.intval($correct_attempts).'</li>
        <li><strong>Incorrect Answers:</strong> '.intval($incorrect_attempts).'</li>
    </ul>';

    echo '<h3>Top Users</h3><ul>';
    foreach($top_users as $u) {
        $user = get_userdata($u->user_id);
        echo '<li>' . esc_html($user ? $user->display_name : 'User#'.$u->user_id) . ' - ' . intval($u->cnt) . ' attempts</li>';
    }
    echo '</ul>';

    echo '<h3>Most Attempted Questions</h3><ul>';
    foreach($top_questions as $q) {
        $qtext = $wpdb->get_var($wpdb->prepare("SELECT question FROM $table_mcqs WHERE id=%d", $q->mcq_id));
        echo '<li>' . esc_html(wp_trim_words($qtext, 12)) . ' - ' . intval($q->cnt) . ' attempts</li>';
    }
    echo '</ul>';

    // Per-question stats
    echo '<h3>Attempts Per MCQ (last 20 MCQs)</h3>';
    $recent = $wpdb->get_results("SELECT * FROM $table_mcqs ORDER BY id DESC LIMIT 20");
    echo '<table class="widefat"><thead><tr><th>ID</th><th>Question</th><th>Correct</th><th>Incorrect</th></tr></thead><tbody>';
    foreach($recent as $mcq) {
        $correct = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_attempts WHERE mcq_id=%d AND is_correct=1", $mcq->id));
        $incorrect = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_attempts WHERE mcq_id=%d AND is_correct=0", $mcq->id));
        echo '<tr><td>'.$mcq->id.'</td><td>'.esc_html(wp_trim_words($mcq->question, 10)).'</td><td>'.intval($correct).'</td><td>'.intval($incorrect).'</td></tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
}