<?php
if (!defined('ABSPATH')) exit;

class MCQS_Developer_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'mcqs_developer_widget',
            __('MCQS Quiz Widget', 'mcqs-developer'),
            array('description' => __('Display MCQS Developer Quiz in a widget area.', 'mcqs-developer'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . '<i class="fa-solid fa-question"></i> ' . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        echo '<div class="mcqs-widget-content">';
        echo do_shortcode('[mcqs_output]');
        echo '</div>';
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Quiz', 'mcqs-developer');
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