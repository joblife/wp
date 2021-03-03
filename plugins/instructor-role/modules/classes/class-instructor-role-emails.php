<?php
/**
 * Instructor Emails Module
 *
 * @since      3.5.0
 * @package    Instructor_Role
 * @subpackage Instructor_Role/modules/classes
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Emails' ) ) {
	/**
	 * Class Instructor Role Emails Module
	 */
	class Instructor_Role_Emails {


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
		 * Add Instructor emails tab
		 *
		 * @since   3.2.1
		 */
		public function ir_add_instructor_email_tab( $tabs ) {
			if ( function_exists( 'wdm_is_instructor' ) && wdm_is_instructor() ) {
				// Check if instructor emails setting enabled.
				$ir_settings = get_option( '_wdmir_admin_settings', array() );
				if ( ! array_key_exists( 'instructor_mail', $ir_settings ) || $ir_settings['instructor_mail'] != 1 ) {
					return $tabs;
				}
				$tabs['instructor-email'] = array(
					'title'  => __( 'Instructor Emails', 'wdm_instructor_role' ),
					'access' => array( 'instructor' ),
				);
			}
			return $tabs;
		}

		/**
		 * Add instructor email tab content
		 *
		 * @since   3.2.1
		 */
		public function ir_add_instructor_email_tab_content( $current_tab ) {
			if ( ! function_exists( 'wdm_is_instructor' ) && ! wdm_is_instructor() ) {
				return;
			}
			$user_id = get_current_user_id();
			if ( 'instructor-email' === $current_tab ) {
				if ( array_key_exists( 'instructor-email-save', $_POST ) && ! empty( $_POST['instructor-email-save'] ) ) {
					$this->ir_save_instructor_email_settings( $user_id );
				}
				$subject = get_user_meta( $user_id, 'ir-course-purchase-email-sub', 1 );
				$body    = get_user_meta( $user_id, 'ir-course-purchase-email-body', 1 );
				?>
				<h2><?php esc_html_e( sprintf( '%s Purchase Email', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' ); ?></h2>
				<p>
					<strong><?php esc_html( _e( 'Description : ', 'wdm_instructor_role' ) ); ?></strong>
				<?php esc_html_e( sprintf( 'Now receive a email notification whenever your %s is purchased through a product. You can customize the contents of that email from here.', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' ); ?>
				</p>
				<form method="post">
					<p>
						<strong><?php esc_html( _e( 'Available shortcodes : ', 'wdm_instructor_role' ) ); ?></strong>
					<?php _e( '<em>[site_name]</em>, <em>[course_name]</em>, <em>[instructor_name]</em> and <em>[customer_name]</em>', 'wdm_instructor_role' ); ?>
					</p>
					<table class="ir-course-purchase-email-body">
						<tbody>
							<tr scope="row">
								<th class="ir-email-settings-label">
								<?php esc_html( _e( 'Subject', 'wdm_instructor_role' ) ); ?>
								</th>
								<td>
									<input type="text" name="ir-course-purchase-email-sub" value="<?php echo $subject; ?>">
								</td>
							</tr>
							<tr scope="row">
								<th class="ir-email-settings-label">
								<?php esc_html( _e( 'Body', 'wdm_instructor_role' ) ); ?>
								</th>
								<td>
								<?php
								wp_editor(
									$body,
									'ir-course-purchase-email-body',
									array( 'media_buttons' => false )
								);
								?>
								</td>
							</tr>
						</tbody>
					</table>
					<input type="submit" class="button-primary" name="instructor-email-save" value="<?php esc_html( _e( 'Save', 'wdm_instructor_role' ) ); ?>" />
				</form>
					<?php
			}
		}

		/**
		 * Save instructor email settings
		 *
		 * @since   3.2.1
		 */
		public function ir_save_instructor_email_settings( $user_id ) {
			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			$subject = filter_input( INPUT_POST, 'ir-course-purchase-email-sub', FILTER_SANITIZE_STRING );
			$body    = wpautop( $_POST['ir-course-purchase-email-body'] );

			update_user_meta( $user_id, 'ir-course-purchase-email-sub', $subject );
			update_user_meta( $user_id, 'ir-course-purchase-email-body', $body );
		}

		/**
		 * Send course purchase emails to instructors
		 *
		 * @since   3.2.1
		 */
		public function ir_send_course_purchase_email_to_instructor( $order_id ) {
			$order = new \WC_Order( $order_id );
			$items = $order->get_items();

			foreach ( $items as $item ) {
				$product_id     = $item['product_id'];
				$total          = $item['line_total'];
				$product_post   = get_post( $product_id );
				$author_id      = $product_post->post_author;
				$related_course = get_post_meta( $product_id, '_related_course', true );
				// If no course related then do not send emails.
				if ( empty( $related_course ) ) {
					continue;
				}
				$course_id       = $related_course[0];
				$assigned_course = get_post( $course_id );

				// Check if instructor is author of course if not product.
				if ( ! wdm_is_instructor( $author_id ) ) {
					if ( ! empty( $related_course ) ) {
						$author_id = $assigned_course->post_author;
					}
				}

				if ( wdm_is_instructor( $author_id ) ) {
					$subject = get_user_meta( $author_id, 'ir-course-purchase-email-sub', 1 );
					$body    = get_user_meta( $author_id, 'ir-course-purchase-email-body', 1 );

					if ( empty( $subject ) ) {
						// translators: Sitename.
						$subject = sprintf( __( '[ %s ] : Course Purchase Email', 'wdm_is_instructor' ), get_bloginfo( 'name' ) );
					}

					if ( empty( $body ) ) {
						$body  = '<p>' . sprintf( __( 'Hello %s ,', 'wdm_is_instructor' ), get_user_meta( $author_id, 'first_name', 1 ) . ' ' . get_user_meta( $author_id, 'last_name', 1 ), 1 ) . '</p>';
						$body .= '<p>' . sprintf( __( 'A new purchase has been made for your course <strong>%1$s</strong> by <strong>%2$s</strong>.', 'wdm_is_instructor' ), $assigned_course->post_title, $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ) . '</p>';
						$body .= '<p>' . __( 'Thank You', 'wdm_is_instructor' ) . '</p>';
					}

					$find    = array(
						'[site_name]',
						'[course_name]',
						'[customer_name]',
						'[instructor_name]',
					);
					$replace = array(
						get_bloginfo( 'name' ),
						$assigned_course->post_title,
						$order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
						get_user_meta( $author_id, 'first_name', 1 ) . ' ' . get_user_meta( $author_id, 'last_name', 1 ),
					);

					$subject     = str_replace( $find, $replace, $subject );
					$body        = str_replace( $find, $replace, $body );
					$author_data = get_userdata( $author_id );

					if ( ! wp_mail( $author_data->user_email, $subject, $body, array( 'Content-Type: text/html; charset=UTF-8' ) ) ) {
						error_log( "IR DEBUG MESSAGE :: For Order : $order_id :: Instructor Email not sent successfully" );
					}
				}
			}
		}
	}
}
