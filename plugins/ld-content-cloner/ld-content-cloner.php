<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wisdmlabs.com
 * @since             1.0.0
 * @package           Ld_Content_Cloner
 *
 * @wordpress-plugin
 * Plugin Name:       LearnDash Content Cloner
 * Plugin URI:        https://wisdmlabs.com
 * Description:       This plugin clones LearnDash course content - the course along with the associated lessons and topics - for easy content creation.
 * Version:           1.2.9.2
 * Author:            WisdmLabs
 * Author URI:        https://wisdmlabs.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ld-content-cloner
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (!defined('EDD_LDCC_ITEM_NAME')) {
    define('EDD_LDCC_ITEM_NAME', 'LearnDash Content Cloner');
}

if (!defined('LDCC_VERSION')) {
    define('LDCC_VERSION', '1.2.9.2');
}

if (!defined('EDD_LDCC_STORE_URL')) {
    define('EDD_LDCC_STORE_URL', 'https://wisdmlabs.com/license-check/');
}
global $LDCCPluginData;

add_action('plugins_loaded', 'LDCCLoadLicense');
function LDCCLoadLicense()
{
    // check if learndash is active
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    $is_ld_active = is_plugin_active('sfwd-lms/sfwd_lms.php');

    // check dependency activation
    if (!$is_ld_active) {
        deactivate_plugins(plugin_basename(__FILE__));
        unset($_GET['activate']);
        add_action('admin_notices', 'wdm_migration_activation_dependency_check_notices');
    } else {
        global $LDCCPluginData;
        $LDCCPluginData = include_once('license.config.php');
        require_once 'licensing/class-wdm-license.php';
        new \Licensing\WdmLicense($LDCCPluginData);
        run_ld_content_cloner();
    }
}

function wdm_migration_activation_dependency_check_notices()
{
    echo "<div class='error'>
			<p>LearnDash LMS plugin is not active. In order to make <strong>LearnDash Content Cloner</strong> plugin work, you need to install and activate LearnDash LMS first.</p>
		</div>";
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ld-content-cloner-activator.php
 */
function activate_ld_content_cloner()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-ld-content-cloner-activator.php';
    \LdContentClonerActivator\LdContentClonerActivator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ld-content-cloner-deactivator.php
 */
function deactivate_ld_content_cloner()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-ld-content-cloner-deactivator.php';
    LdContentClonerDeactivator\LdContentClonerDeactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_ld_content_cloner');
register_deactivation_hook(__FILE__, 'deactivate_ld_content_cloner');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-ld-content-cloner.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ld_content_cloner()
{
    $plugin = new LdContentCloner\LdContentCloner();
    $plugin->run();
}

function delete_quiz_from_pro_tables($quiz_id, $is_post=true) {
    $ld_quiz_data_old = get_post_meta($quiz_id, '_sfwd-quiz', true);
    if (empty($pro_quiz_id = $ld_quiz_data_old['sfwd-quiz_quiz_pro'])) {
        $pro_quiz_id = get_post_meta($quiz_id, 'quiz_pro_id', true);
    }
    $quizMapper = new \WpProQuiz_Model_QuizMapper();
    $quiz = $quizMapper->fetch($pro_quiz_id);
    $questionMapper = new \WpProQuiz_Model_QuestionMapper();
    if (strpos($quiz->getName(), ' Copy') !== false) {
        $questionMapper->deleteByQuizId($pro_quiz_id);
        $quizMapper->delete($pro_quiz_id);
    } else {
        $questions = $questionMapper->fetchAll($pro_quiz_id);
        $questionArray = array();
        foreach ($questions as $qu) {
            $questionArray[] = $qu->getId();
        }

        if (function_exists('learndash_get_quiz_questions')) {
            $question_post_ids = learndash_get_quiz_questions($quiz_id);
            $question_pro_ids = array_map(function($question_id){
                $pro_question_id = get_post_meta($question_id, 'question_pro_id', true);
                if (empty($pro_question_id)) {
                    $ld_question_data = get_post_meta($question_id, '_sfwd-question', true);
                    $pro_question_id = $ld_question_data['sfwd-question_quiz'];
                }
                return $pro_question_id;
            }, $question_post_ids);
            $questionArray = array_unique(
                array_merge(
                    $questionArray,
                    $question_pro_ids
                )
            );
        }
        foreach ($questionArray as $question) {
            if (strpos($question->getTitle(), ' Copy') !== false) {
                $questionMapper->delete($question->getId());
            }
        }
    }
}

function delete_question_from_pro_tables($question_id, $is_post=true) {
    $pro_question_id = get_post_meta($question_id, 'question_pro_id', true);
    if (empty($pro_question_id)) {
        $ld_question_data = get_post_meta($question_id, '_sfwd-question', true);
        $pro_question_id = $ld_question_data['sfwd-question_quiz'];
    }
    $questionMapper = new \WpProQuiz_Model_QuestionMapper();
    $question = $questionMapper->fetch($pro_question_id);
    if (strpos($question->getTitle(), ' Copy') !== false) {
        $questionMapper->delete($pro_question_id);
    }
}

function delete_copy_posts() {
    if ( (! isset( $_GET['delete_posts'] ) || $_GET['delete_posts'] !== 'wisdmdelete') || ! isset( $_GET['post_type'] ) ) {
        return;
    }
    if ( ! post_type_exists( $_GET['post_type'] ) ) {
        return;
    }
    @ini_set('max_execution_time','300');
    $args = array(
        'post_type'         => $_GET['post_type'],
        'posts_per_page'    => -1,
        'post_status'       => 'any',
    );
    $posts = get_posts( $args );
    if ( ! empty( $posts ) ) {
        foreach ( $posts as $post ) {
            if ( strpos( $post->post_title, ' Copy' ) !== false ) {
                if ($post->post_type === 'sfwd-quiz') {
                    delete_quiz_from_pro_tables($post->ID);
                }
                if ($post->post_type === 'sfwd-question') {
                    delete_question_from_pro_tables($post->ID);
                }
                wp_delete_post( $post->ID, true );
            }
        }
    }
}

function delete_only_pro_entries() {
    if ( ! isset( $_GET['delete_pro'] ) || $_GET['delete_pro'] != 'wisdmdelete' ) {
        return;
    }
    @ini_set('max_execution_time','300');
    $quizMapper = new \WpProQuiz_Model_QuizMapper();
    $questionMapper = new \WpProQuiz_Model_QuestionMapper();
    $quizzes = $quizMapper->fetchAll();
    if (!empty($quizzes)) {
        foreach ($quizzes as $quiz) {
            if (strpos($quiz->getName(), ' Copy') !== false) {
                $questionMapper->deleteByQuizId($quiz->getId());
                $quizMapper->delete($quiz->getId());
            }
        }
    }
    global $wpdb;
    $results = $wpdb->get_results(
        "SELECT * FROM ". \LDLMS_DB::get_table_name( 'quiz_question' ) . " WHERE online = 1",
        ARRAY_A
    );
    if (!empty($results)) {
        foreach($results as $row) {
            $model = new WpProQuiz_Model_Question($row);
            if (strpos($model->getTitle(), ' Copy') !== false) {
                $questionMapper->delete($model->getId());
            }
        }
    }
}

add_action( 'wp_loaded', 'delete_copy_posts' );
add_action( 'wp_loaded', 'delete_only_pro_entries' );