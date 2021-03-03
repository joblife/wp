<?php
/**
 * Commission Module
 *
 * @since      3.5.0
 * @package    Instructor_Role
 * @subpackage Instructor_Role/modules/classes
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Commission' ) ) {
	/**
	 * Class Instructor Role Commission Module
	 */
	class Instructor_Role_Commission {


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
		 * Creating wdm_instructor_commission table.
		 *
		 * @since 2.4.0
		 */
		public function wdm_instructor_table_setup() {
			global $wpdb;
			$table_name = $wpdb->prefix . 'wdm_instructor_commission';
			// if table doesn't exist then create a new table.
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				$sql = 'CREATE TABLE ' . $table_name . ' (
                id INT NOT NULL AUTO_INCREMENT,
                user_id int,
                order_id int,
                product_id int,
                actual_price float,
                commission_price float,
                transaction_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                product_type varchar(5) DEFAULT NULL,
                PRIMARY KEY  (id)
                        );';
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
			} else { // if table already exist then check that product_type coloumn exist or not.
				$fields = $wpdb->get_var( "SHOW fields FROM {$table_name} LIKE 'product_type'" );
				// if column 'product_type' isn't exist
				if ( $fields != 'product_type' ) {
					$wpdb->query( 'ALTER TABLE ' . $table_name . ' ADD product_type VARCHAR(5) DEFAULT NULL' );
				}
				$this->wdmAddProductTypeToAlteredAttribute( $table_name );
			}
		}

		/**
		 * Update product_type field if it's set to NULL.
		 *
		 * @param string $table_name table name
		 *
		 * @since 2.4.0
		 */
		function wdmAddProductTypeToAlteredAttribute( $table_name ) {
			global $wpdb;
			$undef_product_type = $wpdb->get_results( "SELECT * FROM $table_name  WHERE product_type IS NULL", ARRAY_A );
			if ( ! empty( $undef_product_type ) ) {
				foreach ( $undef_product_type as $row ) {
					$to_add_product_type = '';
					$row_product_id      = $row['product_id'];
					$row_unique_id       = $row['id'];
					if ( get_post_type( $row_product_id ) == 'product' ) {
						$to_add_product_type = 'WC';
					} elseif ( get_post_type( $row_product_id ) == 'download' ) {
						$to_add_product_type = 'EDD';
					} elseif ( get_post_type( $row_product_id ) == 'sfwd-courses' ) {
						$to_add_product_type = 'LD';
					}
					if ( ! empty( $to_add_product_type ) ) {
						$wpdb->update( $table_name, array( 'product_type' => $to_add_product_type ), array( 'id' => $row_unique_id ), array( '%s' ), array( '%d' ) );
					}
				}
			}
		}

		/**
		 * Update user meta of instructor for amount paid.
		 *
		 * @return json_encode status of operation
		 *
		 * @since 2.4.0
		 */
		public function wdm_amount_paid_instructor() {
			if ( ! is_super_admin() ) {
				die();
			}
			$instructor_id = filter_input( INPUT_POST, 'instructor_id', FILTER_SANITIZE_NUMBER_INT );
			if ( ( '' == $instructor_id ) || ( ! wdm_is_instructor( $instructor_id ) ) ) {
				echo json_encode( array( 'error' => __( 'The user is not instructor.', 'wdm_instructor_role' ) ) );
				die();
			}

			$total_paid           = filter_input( INPUT_POST, 'total_paid', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
			$amount_tobe_paid     = filter_input( INPUT_POST, 'amount_tobe_paid', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
			$enter_amount         = filter_input( INPUT_POST, 'enter_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
			$usr_instructor_total = get_user_meta( $instructor_id, 'wdm_total_amount_paid', true );

			$usr_instructor_total = $this->getUsrInstructorTotal( $usr_instructor_total );
			if ( ( '' == $amount_tobe_paid || '' == $enter_amount ) || $total_paid != $usr_instructor_total
				|| $enter_amount > $amount_tobe_paid ) {
				echo json_encode( array( 'error' => __( 'Something is not correct with the amount', 'wdm_instructor_role' ) ) );
				die();
			}

			global $wpdb;
			$sql     = "SELECT commission_price FROM {$wpdb->prefix}wdm_instructor_commission WHERE user_id = $instructor_id";
			$results = $wpdb->get_col( $sql );
			if ( empty( $results ) ) {
				echo json_encode( array( 'error' => __( 'Could not find commission', 'wdm_instructor_role' ) ) );
				die();
			} else {
				$vald_amnt_tobe_paid = 0;
				foreach ( $results as $value ) {
					$vald_amnt_tobe_paid += $value;
				}
				$vald_amnt_tobe_paid = round( ( $vald_amnt_tobe_paid - $total_paid ), 2 );
				if ( $vald_amnt_tobe_paid != $amount_tobe_paid ) {
					echo json_encode( array( 'error' => __( 'Amount to be paid is not correct', 'wdm_instructor_role' ) ) );
					die();
				}
			}

			$new_paid_amount = round( ( $total_paid + $enter_amount ), 2 );
			update_user_meta( $instructor_id, 'wdm_total_amount_paid', $new_paid_amount );

			/*
			* instructor_id is id of the instructor
			* enter_amount is amount entered by admin to pay
			* total_paid is the total amount paid by admin to insturctor before current transaction
			* amount_tobe_paid is the amount required to be paid by admin
			* new_paid_amount is the total amount paid to instructor after current transaction
			*/
			do_action( 'wdm_commission_amount_paid', $instructor_id, $enter_amount, $total_paid, $amount_tobe_paid, $new_paid_amount );
			$new_amount_tobe_paid = round( ( $amount_tobe_paid - $enter_amount ), 2 );

			$data = array(
				'amount_tobe_paid' => $new_amount_tobe_paid,
				'total_paid'       => $new_paid_amount,
			);
			echo json_encode( array( 'success' => $data ) );
			die();
		}

		/**
		 * Function returns user instructor total.
		 *
		 * @param int $usr_instructor_total usr_instructor_total
		 *
		 * @return int usr_instructor_total
		 *
		 * @since 2.4.0
		 */
		public function getUsrInstructorTotal( $usr_instructor_total ) {
			if ( '' == $usr_instructor_total ) {
				return 0;
			}

			return $usr_instructor_total;
		}

		/**
		 * On woocommerce order complete, adding commission percentage in custom table.
		 *
		 * @param int $order_id order_id
		 *
		 * @since 2.4.0
		 */
		public function wdm_add_record_to_db( $order_id ) {
			$order = new \WC_Order( $order_id );
			global $wpdb;

			$items = $order->get_items();
			foreach ( $items as $item ) {
				$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
				$total      = $item['line_total'];

				$product_post = get_post( $product_id );
				$author_id    = $product_post->post_author;
				// Fix for FCC
				// - If product not owned by instructor (like admin), then get instructor from the course owner instead of the product owner
				if ( ! wdm_is_instructor( $author_id ) ) {
					$related_course = get_post_meta( $product_id, '_related_course', true );
					if ( ! empty( $related_course ) ) {
						$course_id       = $related_course[0];
						$assigned_course = get_post( $course_id );
						$author_id       = $assigned_course->post_author;
					}
				}
				if ( wdm_is_instructor( $author_id ) ) {
					$commission_percent = get_user_meta( $author_id, 'wdm_commission_percentage', true );
					if ( '' == $commission_percent ) {
						$commission_percent = 0;
					}
					$commission_price = ( $total * $commission_percent ) / 100;
					$sql              = "SELECT id FROM {$wpdb->prefix}wdm_instructor_commission WHERE user_id = $author_id AND order_id = $order_id AND product_id = $product_id";
					$id               = $wpdb->get_var( $sql );
					$data             = array(
						'user_id'          => $author_id,
						'order_id'         => $order_id,
						'product_id'       => $product_id,
						'actual_price'     => $total,
						'commission_price' => $commission_price,
						'product_type'     => 'WC',
					);
					if ( '' == $id ) {
						$wpdb->insert( $wpdb->prefix . 'wdm_instructor_commission', $data );
					} else {
						$wpdb->update( $wpdb->prefix . 'wdm_instructor_commission', $data, array( 'id' => $id ) );
					}
				}
			}
		}

		/**
		 * Adding transaction details after LD transaction.
		 *
		 * @param int    $meta_id    meta id
		 * @param int    $object_id  object_id
		 * @param string $meta_key   meta key
		 * @param string $meta_value meta value
		 *
		 * @since 2.4.0
		 */
		public function wdm_instructor_updated_postmeta( $meta_id, $object_id, $meta_key, $meta_value ) {
			global $wpdb;
			$post_type = get_post_type( $object_id );
			$meta_id   = $meta_id;
			if ( 'sfwd-transactions' == $post_type && 'course_id' == $meta_key ) {
				$course_id   = $meta_value;
				$course_post = get_post( $course_id );
				$author_id   = $course_post->post_author;
				if ( wdm_is_instructor( $author_id ) ) {
					$commission_percent = get_user_meta( $author_id, 'wdm_commission_percentage', true );
					if ( '' == $commission_percent ) {
						$commission_percent = 0;
					}
					// @since 3.4.0 : Replaced 'payment_gross' with 'mc_gross' since the first is deprecated.
					$total = get_post_meta( $object_id, 'mc_gross', true );

					$payment_method = get_post_meta( $object_id, 'action', 1 );

					// @since - LD-Stripe 1.5.0 : Updated stripe payment method action to 'ld_stripe_init_checkout'
					if ( 'stripe' == $payment_method || 'ld_stripe_init_checkout' == $payment_method ) {
						$total    = floatval( get_post_meta( $object_id, 'stripe_price', true ) );
						$currency = get_post_meta( $object_id, 'stripe_currency', true );
						if ( 'usd' == $currency ) {
							// Since stripe stores payments in cents,
							$total = $total / 100;
						}
					}

					if ( '' == $total ) {
						$total = 0;
					}

					$commission_price = ( $total * $commission_percent ) / 100;

					$data = array(
						'user_id'          => $author_id,
						'order_id'         => $object_id,
						'product_id'       => $course_id,
						'actual_price'     => $total,
						'commission_price' => $commission_price,
					);
					$wpdb->insert( $wpdb->prefix . 'wdm_instructor_commission', $data );
					// v2.4.0
					// update_post_meta($object_id, '_ldpurchaser_id', get_current_user_id());
				}
			}
		}

		/**
		 * To allow instructor to access dashboard
		 *
		 * @param  boolean $prevent_access prevent_access
		 * @return boolean $prevent_access prevent_access
		 */
		public function wdmAllowDashboardAccess( $prevent_access ) {
			if ( wdm_is_instructor() ) {
				return false;
			}
			return $prevent_access;

		}

		/**
		 * wdmAddWoocommercePostType adding woocommerce product post type.
		 *
		 * @param  array $wdm_ar_post_types contains list of post type which instructor can access
		 */
		public function wdmAddWoocommercePostType( $wdm_ar_post_types ) {
			if ( wdmCheckWooDependency() && ! in_array( 'product', $wdm_ar_post_types ) ) {
				array_push( $wdm_ar_post_types, 'product' );
			}
			return $wdm_ar_post_types;
		}

		/**
		 * wdmAddWoocommerceMenu to add menu
		 *
		 * @param  array $allowed_tabs list of menus to be shown on dashboard
		 */
		public function wdmAddWoocommerceMenu( $allowed_tabs ) {
			if ( wdmCheckWooDependency() && ! in_array( 'edit.php?post_type=product', $allowed_tabs ) ) {
				array_push( $allowed_tabs, 'edit.php?post_type=product' );
			} elseif ( ! wdmCheckWooDependency() && in_array( 'edit.php?post_type=product', $allowed_tabs ) ) {
				unset( $allowed_tabs['edit.php?post_type=product'] );
			}
			return $allowed_tabs;
		}

		/**
		 * Conditionally provide access to the 'manage_woocommerce' capability to allow instructors to relate courses to products.
		 * Since LD-Woo added that check in version 1.6.0
		 *
		 * @since 3.2.0
		 */
		public function allowInstructorsToRelateCourses( $all_caps, $requested_caps, $args, $user ) {
			if ( ! defined( 'LEARNDASH_WOOCOMMERCE_VERSION' ) || 0 > version_compare( LEARNDASH_WOOCOMMERCE_VERSION, '1.6.0' ) ) {
				return $all_caps;
			}
			// Check if checking for woocommerce managing capability.
			if ( ! in_array( 'manage_woocommerce', $requested_caps ) ) {
				return $all_caps;
			}

			// Check if instructor.
			if ( ! wdm_is_instructor() ) {
				return $all_caps;
			}

			// Check if product edit page
			global $post, $current_screen;
			if ( empty( $post ) || 'product' !== $post->post_type || 'product' != $current_screen->id ) {
				return $all_caps;
			}

			$all_caps['manage_woocommerce'] = 1;

			return $all_caps;
		}
	}
}
