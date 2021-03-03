<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Ld_Content_Cloner
 * @subpackage Ld_Content_Cloner/includes
 */

/**
 * The LD course plugin class.
 *
 * @since      1.0.0
 * @package    Ld_Content_Cloner
 * @subpackage Ld_Content_Cloner/includes
 * @author     WisdmLabs <info@wisdmlabs.com>
 */
namespace LdccCourse;

class LdccCourse
{
    /**
     *
     * @since    1.0.0
     */

    public function __construct()
    {
    }

    public static function copyQuestions($quizId, $questionArray)
    {
        $questionMapper = new \WpProQuiz_Model_QuestionMapper();

        $questions = $questionMapper->fetchById($questionArray);
        $returnQuestions = array();
        // error_log(print_r($questions, 1));
        /*foreach ($questions as $question) {
            $question->setId(0);
            $question->setQuizId($quizId);
            $new_question_slug = apply_filters( 'ldcc_question_duplicate_slug', 'Copy' );
            $question->setTitle($question->getTitle() . ' ' . $new_question_slug);
            $questionMapper->save($question);
            $returnQuestions[]  = $question->getId();

        }
        return $returnQuestions;*/
        $sort = 0;
        $categoryMapper = new \WpProQuiz_Model_CategoryMapper();
        $categoryArray = $categoryMapper->getCategoryArrayForImport();
        foreach($questions as $question) {
        
            if(get_class($question) !== 'WpProQuiz_Model_Question')
                continue;
                    
            $question->setQuizId($quizId);
            $new_question_slug = apply_filters( 'ldcc_question_duplicate_slug', 'Copy' );
            $question->setTitle($question->getTitle() . ' ' . $new_question_slug);
            $question->setId(0);
            $question->setSort($sort++);
            $question->setCategoryId(0);
            if(trim($question->getCategoryName()) != '') {
                if(isset($categoryArray[strtolower($question->getCategoryName())])) {
                    $question->setCategoryId($categoryArray[strtolower($question->getCategoryName())]);
                } else {
                    $categoryModel = new \WpProQuiz_Model_Category();
                    $categoryModel->setCategoryName($question->getCategoryName());
                    $categoryMapper->save($categoryModel);
                    
                    $question->setCategoryId($categoryModel->getCategoryId());
                    
                    $categoryArray[strtolower($question->getCategoryName())] = $categoryModel->getCategoryId();
                }
            }
            
            $questionMapper->save($question);
            $returnQuestions[]  = $question->getId();
        }
        return $returnQuestions;
    }

    public static function stripPostData($post_array)
    {
        $exclude_remove = array( 'post_content', 'post_title', 'post_status', 'post_type', 'comment_status', 'ping_status' );
        foreach ($post_array as $key => $value) {
            if (!in_array($key, $exclude_remove)) {
                unset($post_array[ $key ]);
            }
            unset($value);
        }

        // .publish all contents except course
        if ($post_array['post_type'] == 'sfwd-courses') {
            $post_array['post_status'] = "draft";
        }
        $new_module_slug = apply_filters( 'ldcc_duplicate_slug_before_insert', 'Copy', $post_array );
        $post_array['post_title'] .= ' ' . $new_module_slug;
        unset($value);
        return $post_array;
    }

    public static function updateCourseMeta($old_post_id, $new_post_id)
    {
        global $wpdb;
        $ld_data = get_post_meta($old_post_id, '_sfwd-courses', true);
        if (!empty($ld_data)) {
            $ld_data=self::getDetaultValues($ld_data);
            if (!empty($ld_data['sfwd-courses_course_price_type'])) {
                if ($ld_data['sfwd-courses_course_price_type'] == 'subscribe') {
                    $billing_cycle_time=get_post_meta($old_post_id, 'course_price_billing_t3', true);
                    update_post_meta($new_post_id, 'course_price_billing_t3', $billing_cycle_time);
                    $billing_cycle_day=get_post_meta($old_post_id, 'course_price_billing_p3', true);
                    update_post_meta($new_post_id, 'course_price_billing_p3', $billing_cycle_day);
                }
            }
        }
        update_post_meta($new_post_id, '_sfwd-courses', $ld_data);
        $term_taxonomy_ids = $wpdb->get_results("SELECT term_taxonomy_id FROM ".$wpdb->prefix."term_relationships where object_id=".$old_post_id);
        if (!empty($term_taxonomy_ids)) {
            foreach ($term_taxonomy_ids as $term_taxonomy_id) {
                $wpdb->insert(
                    $wpdb->prefix.'term_relationships',
                    array(
                    'object_id' => $new_post_id,
                    'term_taxonomy_id' => $term_taxonomy_id->term_taxonomy_id,
                    'term_order' => 0
                    ),
                    array(
                    '%d',
                    '%d',
                    '%d'
                    )
                );
            }
        }
    }

    public static function updateQuizMeta($old_post_id, $new_post_id, $other_data, $shared_steps_course)
    {
        global $wpdb;
        $unit_course_id = $other_data['course_id'];
        
        $unit_lesson_id = !empty($other_data['lesson_id']) ? $other_data['lesson_id'] : 0;
        $quiz_pro_id = $other_data['quiz_pro_id'];

        $ld_data = get_post_meta($old_post_id, '_sfwd-quiz', true);

        $ld_data['sfwd-quiz_quiz_pro'] = $quiz_pro_id;

        if ($shared_steps_course == 'yes') {
            unset($ld_data['sfwd-quiz_course']);
            unset($ld_data['sfwd-quiz_lesson']);
            update_post_meta($new_post_id, 'course_id', 0);
            update_post_meta($new_post_id, 'lesson_id', 0);
            update_post_meta($new_post_id, 'ld_course_'.$unit_course_id, $unit_course_id);
        } else {
            $ld_data['sfwd-quiz_course'] = (string) $unit_course_id;
            $ld_data['sfwd-quiz_lesson'] = (string) $unit_lesson_id;
            update_post_meta($new_post_id, 'course_id', $unit_course_id);
            update_post_meta($new_post_id, 'lesson_id', $unit_lesson_id);
        }

            $term_taxonomy_ids = $wpdb->get_results("SELECT term_taxonomy_id FROM ".$wpdb->prefix."term_relationships where object_id=".$old_post_id);

        if (!empty($term_taxonomy_ids) && empty($wpdb->get_var('select count(*) from '. $wpdb->prefix.'term_relationships where object_id = '.$new_post_id))) {
            foreach ($term_taxonomy_ids as $term_taxonomy_id) {
                $wpdb->insert(
                    $wpdb->prefix.'term_relationships',
                    array(
                    'object_id' => $new_post_id,
                    'term_taxonomy_id' => $term_taxonomy_id->term_taxonomy_id,
                    'term_order' => 0
                    ),
                    array(
                    '%d',
                    '%d',
                    '%d'
                    )
                );
            }
        }
        $old_quiz = get_post($old_post_id);
        $menu_order = $old_quiz->menu_order;
        $new_quiz_order = array(
            'ID'           => $new_post_id,
            'menu_order'   => $menu_order,
        );
        wp_update_post($new_quiz_order);
        update_post_meta($new_post_id, '_sfwd-quiz', $ld_data);
        update_post_meta($new_post_id, 'quiz_pro_id', $quiz_pro_id);
        update_post_meta($new_post_id, 'quiz_pro_id_'.$quiz_pro_id, $quiz_pro_id);
    }

    public static function updateLessonMeta($old_post_id, $new_post_id, $other_data, $shared_steps_course)
    {
        global $wpdb;
        $lesson_course_id = $other_data['course_id'];
        $ld_data = get_post_meta($old_post_id, '_sfwd-lessons', true);

        if ($ld_data !== "") {
            if ($shared_steps_course == 'yes') {
                unset($ld_data['sfwd-lessons_course']);
            } else {
                $ld_data['sfwd-lessons_course'] = $lesson_course_id;
                update_post_meta($new_post_id, 'course_id', $lesson_course_id);
            }
            update_post_meta($new_post_id, '_sfwd-lessons', $ld_data);
        }

        update_post_meta($new_post_id, 'ld_course_'.$lesson_course_id, $lesson_course_id);

        $topic_meta = get_post_meta($old_post_id, '_sfwd-topic', true);
        if ($topic_meta !== "") {
            if ($shared_steps_course == 'yes') {
                unset($topic_meta['sfwd-topic_course']);
                unset($topic_meta['sfwd-topic_lesson']);
            } else {
                $unit_lesson_id = $other_data['topic_lesson_id'];
                $topic_meta['sfwd-topic_course'] = $lesson_course_id;
                $topic_meta['sfwd-topic_lesson'] = $unit_lesson_id;
            }
            update_post_meta($new_post_id, '_sfwd-topic', $topic_meta);
        }

        $term_taxonomy_ids = $wpdb->get_results("SELECT term_taxonomy_id FROM ".$wpdb->prefix."term_relationships where object_id=".$old_post_id);
        if (!empty($term_taxonomy_ids)  && empty($wpdb->get_var('select count(*) from '. $wpdb->prefix.'term_relationships where object_id = '.$new_post_id))) {
            foreach ($term_taxonomy_ids as $term_taxonomy_id) {
                $wpdb->insert(
                    $wpdb->prefix.'term_relationships',
                    array(
                    'object_id' => $new_post_id,
                    'term_taxonomy_id' => $term_taxonomy_id->term_taxonomy_id,
                    'term_order' => 0
                    ),
                    array(
                    '%d',
                    '%d',
                    '%d'
                    )
                );
            }
        }
        $old_lesson = get_post($old_post_id);
        $menu_order = $old_lesson->menu_order;
        $new_lesson_order = array(
        'ID'           => $new_post_id,
        'menu_order'   => $menu_order,
        );
        wp_update_post($new_lesson_order);
        update_post_meta($new_post_id, 'ld_course_'.$lesson_course_id, $lesson_course_id);
    }

    public static function getDetaultValues($ld_data)
    {
        if (empty($ld_data['sfwd-courses_course_lesson_orderby'])) {
            $ld_data['sfwd-courses_course_lesson_orderby'] = '';
        }
        if (empty($ld_data['sfwd-courses_course_lesson_order'])) {
            $ld_data['sfwd-courses_course_lesson_order'] = '';
        }
        if (!empty($ld_data['sfwd-courses_course_access_list'])) {
            $ld_data['sfwd-courses_course_access_list'] = '';
        }
        return $ld_data;
    }
    
    public function addCourseRowActions($actions, $post_data)
    {
        if (get_post_type($post_data->ID) === 'sfwd-courses') {
            $actions = array_merge(
                $actions,
                array(
                            'clone_course' => '<a href="#" title="Clone this course" class="ldcc-clone-course" data-course-id="' . $post_data->ID . '" data-course="' . wp_create_nonce('dup_course_' . $post_data->ID) . '">' . __('Clone Course') . '</a>'
                        )
            );
        }
        return $actions;
    }

    public function addLessonRowActions($actions, $post_data)
    {
        if (get_post_type($post_data->ID) === 'sfwd-lessons') {
            $actions = array_merge(
                $actions,
                array(
                    'clone_lesson' => '<a href="#" title="Clone this lesson" class="ldcc-clone-lesson" data-lesson-id="' . $post_data->ID . '" >' . __('Clone Lesson') . '</a>'
                )
            );
        } elseif (get_post_type($post_data->ID) === 'sfwd-quiz') {
            $actions = array_merge(
                $actions,
                array(
                    'clone_quiz' => '<a href="#" title="Clone quiz" class="ldcc-clone-quiz" data-quiz-id="' . $post_data->ID . '" data-course-id="'.get_post_meta($post_data->ID, 'course_id', true).'">' . __('Clone Quiz') . '</a>'
                )
            );
        }
        return $actions;
    }

    public function addModalStructure()
    {
        global $current_screen;

        if (isset($current_screen) && $current_screen->base == 'edit' && in_array($current_screen->id, array( 'edit-sfwd-courses'))) {
            ?>
            <div id="ldcc-dialog" class="hidden" title="<?php _e("Course Cloning", "ld-content-cloner");
            ?>">
                <div id="ldcc_clone_status"></div>
                <div class="ldcc-success">
                    <div>
                        <?php echo sprintf(__("Click %s to edit the cloned Course", "ld-content-cloner"), "<a class='ldcc-course-link' href='#'>".__("here", "ld-content-cloner") . "</a>");
            ?>
                    </div>
                    <div>
                        <?php echo sprintf(__("Click %s to rename the cloned Course content", "ld-content-cloner"), "<a class='ldcc-course-rename-link' href='#'>".__("here", "ld-content-cloner") . "</a>");
            ?>
                    </div>
                </div>

                <div class="ldcc-notice"><?php _e("Note: Remember to change the Title and Slugs for all the cloned Posts.", "ld-content-cloner");
            ?></div>
            <?php
            $slider_loc = "popup";
            $slider_loc = $slider_loc;
            require_once('ldcc-slider.php');
        }
    }
}
