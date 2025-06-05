<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// AJAX: Save attempt
add_action('wp_ajax_mcqs_save_attempt', 'mcqs_dev_save_attempt');
function mcqs_dev_save_attempt() {
    if ( !is_user_logged_in() ) wp_send_json_error('Not logged in');
    global $wpdb;
    $user_id = get_current_user_id();
    $mcq_id = intval($_POST['mcq_id']);
    $selected = sanitize_text_field($_POST['selected']);
    $table_mcqs = $wpdb->prefix . 'mcqs';
    $table_attempts = $wpdb->prefix . 'mcqs_attempts';
    $correct = $wpdb->get_var($wpdb->prepare("SELECT correct_option FROM $table_mcqs WHERE id=%d", $mcq_id));
    $is_correct = ($selected === $correct) ? 1 : 0;
    $wpdb->insert($table_attempts, [
        'user_id' => $user_id,
        'mcq_id' => $mcq_id,
        'selected_option' => $selected,
        'is_correct' => $is_correct,
    ]);
    wp_send_json_success(['is_correct' => $is_correct]);
}

// Modern, SEO-Friendly Frontend Quiz Output (category/limit supported, schema.org markup)
add_action('mcqs_dev_render_frontend_quiz', function($atts = []) {
    global $wpdb;
    $table_mcqs = $wpdb->prefix . 'mcqs';
    $table_cat = $wpdb->prefix . 'mcqs_categories';

    // Category filter support
    $where = '';
    $params = [];
    if (!empty($atts['category'])) {
        if (is_numeric($atts['category'])) {
            $where = 'WHERE m.category_id = %d';
            $params[] = intval($atts['category']);
        } else {
            $cat_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_cat WHERE name = %s", $atts['category']));
            if ($cat_id) {
                $where = 'WHERE m.category_id = %d';
                $params[] = $cat_id;
            }
        }
    }
    $limit = !empty($atts['limit']) ? intval($atts['limit']) : 10;

    $sql = "
        SELECT m.*, c.name as category
        FROM $table_mcqs m
        LEFT JOIN $table_cat c ON m.category_id = c.id
        $where
        ORDER BY RAND() LIMIT $limit
    ";
    $mcqs = $params ? $wpdb->get_results($wpdb->prepare($sql, ...$params)) : $wpdb->get_results($sql);

    $user_logged_in = is_user_logged_in();

    // SCHEMA MARKUP: Quiz
    echo '<section class="mcqs-quiz-modern" itemscope itemtype="https://schema.org/Quiz">';
    echo '<meta itemprop="about" content="Multiple Choice Quiz" />';
    echo '<form id="mcqs-quiz-form">';
    $q_icons = ['fa-lightbulb', 'fa-brain', 'fa-flask', 'fa-rocket', 'fa-code', 'fa-bolt', 'fa-cube', 'fa-database', 'fa-puzzle-piece', 'fa-atom'];
    foreach($mcqs as $index => $mcq) {
        $icon = $q_icons[$index%count($q_icons)];
        echo '<article class="mcq-block-modern" itemprop="hasPart" itemscope itemtype="https://schema.org/Question">';
        echo '<header class="mcq-question-modern"><span class="mcq-icon"><i class="fa-solid '.$icon.'"></i></span> <h2 itemprop="name" style="display:inline;">Q' . ($index+1) . ': ' . esc_html($mcq->question) . '</h2></header>';
        foreach(['A','B','C','D'] as $opt) {
            $option_val = $mcq->{'option_' . strtolower($opt)};
            echo '<label class="mcq-option-modern" itemprop="suggestedAnswer" itemscope itemtype="https://schema.org/Answer"><input type="radio" name="mcq_'.$mcq->id.'" value="'.$opt.'"> <span class="mcq-letter">'.$opt.'</span> <span itemprop="text">'.esc_html($option_val).'</span></label>';
        }
        if ( $user_logged_in ) {
            echo '<div class="mcq-correct-modern" style="display:none" data-answer="'.$mcq->correct_option.'"><i class="fa-solid fa-circle-check"></i> <em>'.__('Correct Answer:', 'mcqs-developer').' <span itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer"><span itemprop="text">'.$mcq->correct_option.'</span></span></em></div>';
            if($mcq->explanation) {
                echo '<div class="mcq-explanation-modern" style="display:none"><i class="fa-solid fa-info-circle"></i> <strong>Explanation:</strong> <span itemprop="comment">'.esc_html($mcq->explanation).'</span></div>';
            }
        }
        echo '</article>';
    }
    $login_url = wp_login_url( get_permalink() );
    if ( !$user_logged_in ) {
        echo '<div class="mcqs-login-popup-modern" style="display:none">';
        echo '<p><i class="fa-solid fa-circle-info"></i> '.__('You must log in to see correct answers.', 'mcqs-developer').'</p>';
        echo '<a href="'.$login_url.'" class="button">'.__('Log In', 'mcqs-developer').'</a>';
        echo '</div>';
    }
    echo '<button type="submit" class="mcqs-submit-modern"><i class="fa-solid fa-paper-plane"></i> '.__('Submit Quiz', 'mcqs-developer').'</button>';
    echo '</form>';
    echo '</section>';

    // Script for quiz logic
    add_action('wp_footer', function() use ($user_logged_in) {
        ?>
        <script>
        jQuery(function($){
            $('#mcqs-quiz-form').on('submit', function(e){
                e.preventDefault();
                var mcqs = [];
                $(this).find('.mcq-block-modern').each(function(){
                    var radio = $(this).find('input[type=radio]');
                    var mcq_id = radio.attr('name').split('_')[1];
                    var selected = $(this).find('input[type=radio]:checked').val();
                    if (selected) {
                        mcqs.push({mcq_id: mcq_id, selected: selected});
                    }
                });
                <?php if ( $user_logged_in ): ?>
                    mcqs.forEach(function(item){
                        $.post(mcqsDev.ajaxurl, {
                            action: 'mcqs_save_attempt',
                            mcq_id: item.mcq_id,
                            selected: item.selected
                        });
                    });
                    $('.mcq-correct-modern').fadeIn();
                    $('.mcq-explanation-modern').fadeIn();
                <?php else: ?>
                    $('.mcqs-login-popup-modern').fadeIn();
                <?php endif; ?>
            });
        });
        </script>
        <?php
    });
});