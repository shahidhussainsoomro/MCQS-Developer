<?php
if (!defined('ABSPATH')) exit;

class MCQS_Developer_Stats_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'mcqs_developer_stats_widget',
            __('MCQS Quiz Stats', 'mcqs-developer'),
            array('description' => __('Show quiz stats in sidebar.', 'mcqs-developer'))
        );
    }

    public function widget($args, $instance) {
        global $wpdb;
        $table_mcqs = $wpdb->prefix . 'mcqs';
        $table_attempts = $wpdb->prefix . 'mcqs_attempts';

        // Who attempted how many questions (top 5 users)
        $user_attempts = $wpdb->get_results("
            SELECT user_id, COUNT(*) as cnt
            FROM $table_attempts
            GROUP BY user_id
            ORDER BY cnt DESC
            LIMIT 5
        ");

        // New questions in last 7 days
        $new_questions = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_mcqs WHERE created_at >= %s",
            date('Y-m-d H:i:s', strtotime('-7 days'))
        ));

        // Total questions (used as "total quizzes")
        $total_questions = $wpdb->get_var("SELECT COUNT(*) FROM $table_mcqs");

        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . '<i class="fa-solid fa-chart-simple"></i> ' . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        ?>
        <div class="mcqs-stats-widget-modern">
            <div class="mcqs-stat-row"><i class="fa-solid fa-layer-group"></i> <strong><?php _e('Total MCQs:', 'mcqs-developer'); ?></strong> <?php echo esc_html($total_questions); ?></div>
            <div class="mcqs-stat-row"><i class="fa-solid fa-star"></i> <strong><?php _e('New Questions (7 days):', 'mcqs-developer'); ?></strong> <?php echo esc_html($new_questions); ?></div>
            <div class="mcqs-stat-row"><i class="fa-solid fa-users"></i> <strong><?php _e('Top Users:', 'mcqs-developer'); ?></strong></div>
            <ul class="mcqs-user-list">
                <?php
                if ($user_attempts) {
                    foreach ($user_attempts as $ua) {
                        $user = get_userdata($ua->user_id);
                        echo '<li><i class="fa-solid fa-user"></i> ' . esc_html($user ? $user->display_name : 'User#' . $ua->user_id) . ': ' . esc_html($ua->cnt) . ' attempts</li>';
                    }
                } else {
                    echo '<li>' . __('No attempts yet.', 'mcqs-developer') . '</li>';
                }
                ?>
            </ul>
        </div>
        <?php
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Quiz Stats', 'mcqs-developer');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'mcqs-developer'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}