<?php
/**
 * Plugin Name: Instructor Role
 * Plugin URI: https://wisdmlabs.com/
 * Description: This extension adds a user role 'Instructor' into your WordPress website and provides capabilities to create courses content and track student progress in your LearnDash LMS.
 * Version: 3.5.4
 * Author: WisdmLabs
 * Author URI: https://wisdmlabs.com/
 * Text Domain: wdm_instructor_role
 * Domain Path: /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set Plugin Version
 *
 * @since 3.5.0
 */
if ( ! defined( 'INSTRUCTOR_ROLE_PLUGIN_VERSION' ) ) {
	define( 'INSTRUCTOR_ROLE_PLUGIN_VERSION', '3.5.4' );
}

/**
 * Plugin dir path Constant
 *
 * @since 3.1.0
 */
if ( ! defined( 'INSTRUCTOR_ROLE_ABSPATH' ) ) {
	define( 'INSTRUCTOR_ROLE_ABSPATH', plugin_dir_path( __FILE__ ) );
}

/**
 * Plugin BaseName Constant
 *
 * @since 3.1.1
 */
if ( ! defined( 'INSTRUCTOR_ROLE_BASE' ) ) {
	define( 'INSTRUCTOR_ROLE_BASE', plugin_basename( __FILE__ ) );
}

/**
 * Set the plugin slug as default text domain.
 *
 * @since 3.5.0
 */
if ( ! defined( 'INSTRUCTOR_ROLE_TXT_DOMAIN' ) ) {
	define( 'INSTRUCTOR_ROLE_TXT_DOMAIN', 'wdm_instructor_role' );
}

require INSTRUCTOR_ROLE_ABSPATH . 'includes/class-instructor-role.php';

/**
 * Begins execution of the plugin.
 */
function run_instructor_role() {
	$plugin = new \InstructorRole\Includes\Instructor_Role();
	$plugin->run();
}
run_instructor_role();

