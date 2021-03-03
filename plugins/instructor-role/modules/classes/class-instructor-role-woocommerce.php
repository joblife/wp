<?php

/**
 * WooCommerce Integration Module
 *
 * @since      3.5.0
 * @package    Instructor_Role
 * @subpackage Instructor_Role/modules/classes
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Woocommerce' ) ) {
	/**
	 * Class Instructor Role Woocommerce Module
	 */
	class Instructor_Role_Woocommerce {


		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 3.3.0
		 */
		protected static $instance = null;

		/**
		 * Plugin Slug
		 *
		 * @var string  $plugin_slug
		 *
		 * @since 3.3.0
		 */
		protected $plugin_slug = '';

		public function __construct() {
			$this->plugin_slug = INSTRUCTOR_ROLE_TXT_DOMAIN;
		}

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since   3.5.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * To restrict access to all other product types except 'course' to instructors.
		 *
		 * @return array
		 */
		public function restrict_product_types( $product_types ) {
			if ( wdm_is_instructor() ) {
				/**
				* added in version 1.3
				* filter name: wdmir_product_types
				* param: array of product types
				*/
				$product_types = apply_filters( 'wdmir_product_types', array( 'course' => 'Course' ) );
			}

			return $product_types;
		}
	}
}
