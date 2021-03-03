<?php
/**
 * This file is used to include the class which registers Reviews CPT in WordPress.
 *
 * @package RatingsReviewsFeedback\Admin\Reviews
 */

namespace ns_wdm_ld_course_review{

	/**
	 * This will create custom post type called Course feedbacks to handle feedbacks of course.
	 */
	class Course_Feedback_CPT {
		/**
		 * CPT Slug
		 *
		 * @var string
		 */
		public $cpt = 'wdm_course_feedback';
		/**
		 * Fields shown in metabox.
		 *
		 * @var array
		 */
		public $meta_box_feedback = array();
		/**
		 * Dunno.
		 *
		 * @var array
		 */
		public $meta_box_posts = array();
		/**
		 * Registered Settings Tabs
		 *
		 * @var array
		 */
		public $_registered_tabs = array();
		/**
		 * Instance of this class.
		 *
		 * @since    1.0.0
		 *
		 * @var object
		 */
		protected static $instance = null;
		/**
		 * Constructor for the class.
		 * Used to initialize all the hooks in the class.
		 */
		public function __construct() {
			// registering cpt of course feedback.
			\wdm_add_hook( 'init', 'create_post_type', $this, array( 'priority' => 11 ) );
			// adding menu on dashboard.
			\wdm_add_hook( 'admin_menu', 'real_admin_menu', $this );
			// removing add media button from editor.
			\wdm_add_hook( 'admin_head', 'remove_add_media_button', $this );

			// adding meta box for feedback details.
			\wdm_add_hook( 'add_meta_boxes', 'add_meta_boxes', $this );

			// for saving meta box values.
			\wdm_add_hook( 'save_post', 'save_meta_boxes', $this, array( 'num_args' => 3 ) );

			// for setting default values that we are going to use on feedback cpt.
			\wdm_add_hook( 'admin_init', 'setting_default_values', $this );
			// adding new custom columns i.e assigned course and rating.
			\wdm_add_hook( 'manage_edit-' . $this->cpt . '_columns', 'add_column_feedback_course', $this, array( 'num_args' => 1 ) );

			// show the related data on table for custom columns.
			\wdm_add_hook( 'manage_' . $this->cpt . '_posts_custom_column', 'show_column_feedback_course', $this, array( 'num_args' => 2 ) );

			// making sortable column i.e course.
			\wdm_add_hook(
				'manage_edit-' . $this->cpt . '_sortable_columns',
				'feedback_sortable_columns',
				$this,
				array(
					'type' => 'filter',
					'priority' => 10,
					'num_args' => 1,
				)
			);
			// adding meta to sort.
			\wdm_add_hook(
				'request',
				'feedback_sortable_columns_order_by',
				$this,
				array(
					'type' => 'filter',
					'priority' => 10,
					'num_args' => 1,
				)
			);

			// changing the query to show only of specific course.
			\wdm_add_hook(
				'pre_get_posts',
				'show_specific_course_feedback',
				$this,
				array(
					'type' => 'filter',
					'priority' => 10,
					'num_args' => 1,
				)
			);
			// adding new field for filter course wise.
			\wdm_add_hook( 'restrict_manage_posts', 'add_filter_coursewise', $this, array( 'num_args' => 2 ) );
			// removing column count for instructor.
			\wdm_add_hook(
				'views_edit-' . $this->cpt,
				'remove_column_count',
				$this,
				array(
					'type' => 'filter',
					'priority' => 10,
					'num_args' => 1,
				)
			);
		}
		/**
		 * Returns an instance of this class.
		 *
		 * @since     1.0.0
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
		/**
		 * This will remove the posts count for instructor.
		 *
		 * @param string $views [contains count].
		 *
		 * @return string $views [after removing count]
		 */
		public function remove_column_count( $views ) {
			if ( function_exists( 'wdm_is_instructor' ) && wdm_is_instructor() && ! empty( $views ) ) {
				foreach ( $views as $key => $value ) {
					if ( 'mine' == $key ) {
						unset( $views[ $key ] );
						continue;
					}
					$start_pos = strpos( $value, '<span' );
					$end_pos = strpos( $value, '</a>' );

					$views[ $key ] = substr_replace( $value, '', $start_pos, ( $end_pos - $start_pos ) );
				}
			}

			return $views;
		}
		/**
		 * [Adding new select field to show all courses].
		 *
		 * @param string $post_type [post_type].
		 * @param string $which     [place eg: top].
		 */
		public function add_filter_coursewise( $post_type, $which ) {
			if ( $post_type == $this->cpt && 'top' == $which ) {
				$all_courses = \rrf_get_all_courses();
				$selected = rrf_check_if_post_set( $_GET, 'wdm_feedback_course_id' );
				/* translators: %s : Course Label*/
				$default_label = sprintf( __( '--- Select %s ---', 'wdm_ld_course_review' ), rrf_get_course_label() );
				?>
				<select name="wdm_feedback_course_id">
					<option value="0"><?php echo esc_html( $default_label ); ?></option>;
					<?php
					foreach ( $all_courses as $course ) {
						echo sprintf( '<option value="%1$d" %2$s>%3$s</option>', esc_attr( $course->ID ), selected( $selected, $course->ID ), esc_html( $course->post_title ) );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</select>
				<?php
			}
		}
		/**
		 * Adding columns on custom post type page i.e course.
		 *
		 * @param array $columns [array of columns].
		 *
		 * @return array $columns [array of columns]
		 */
		public function feedback_sortable_columns( $columns ) {
			$columns[ $this->cpt . '_feedback_course' ] = $this->cpt . '_feedback_course';

			return apply_filters( 'wdm_course_feedback_cpt_columns', $columns );
		}
		/**
		 * To sort the reviews table according to course and rating.
		 *
		 * @param WP_Query Object $request [current request].
		 *
		 * @return WP_Query Object $request
		 */
		public function feedback_sortable_columns_order_by( $request ) {
			if ( ! isset( $request['post_type'] ) ) {
				return $request;
			}
			if ( $request['post_type'] !== $this->cpt . '_feedback' ) {
				return $request;
			}

			if ( isset( $request['orderby'] ) ) {
				if ( $request['orderby'] === $this->cpt . '_feedback_course' ) {
					$request = array_merge(
						$request,
						array(
							'meta_key' => $this->cpt . '_feedback_on_course',
							'orderby' => 'meta_value_num',
						)
					);
				}
			}

			return $request;
		}
		/**
		 * Showing course name with link on feedback wp_table.
		 *
		 * @param string $column  [column name].
		 * @param int    $post_id [post id].
		 */
		public function show_column_feedback_course( $column, $post_id ) {
			if ( $this->cpt . '_feedback_course' == $column ) {
				$reviewed_post_id = get_post_meta( $post_id, $this->cpt . '_feedback_on_course', true );
				if ( ! empty( $reviewed_post_id ) ) {
					$reviewed_post = get_post( $reviewed_post_id );
					$permalink = get_permalink( $reviewed_post_id );
					echo sprintf( '<a target="_blank" href="%1$s">%2$s</a>', esc_url( $permalink ), esc_html( $reviewed_post->post_title ) );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo esc_html__( 'Not Assigned', 'wdm_ld_course_review' );
				}
			}
		}
		/**
		 * Adding column to show associated course.
		 *
		 * @param array $columns [contains list of columns].
		 *
		 * @return array $columns [contains list of columns]
		 *
		 * @version 1.0.0
		 */
		public function add_column_feedback_course( $columns ) {
			$columns[ $this->cpt . '_feedback_course' ] = rrf_get_course_label();

			return apply_filters( 'wdm_course_feedback_add_remove_columns', $columns );
		}

		/**
		 * Setting default values which will get displayed on meta field.
		 */
		public function setting_default_values() {
			$all_courses = \rrf_get_all_courses();
			$post_array = array( '' => '--- ' . __( 'Select', 'wdm_ld_course_review' ) . rrf_get_course_label() . ' ---' );
			foreach ( $all_courses as $course ) {
				$post_array[ $course->ID ] = $course->post_title;
			}
			$fields = array(
				array(
					'name' => rrf_get_course_label(),
					'desc' => '',
					'id' => $this->cpt . '_feedback_on_course',
					'type' => 'select',
					'options' => $post_array,
					'disabled' => true,
				),
			);
			$fields = apply_filters( 'wdm_course_feedback_meta_fields', $fields );
			$this->meta_box_feedback = $fields;
			$this->meta_box_posts = array();
			$tabs = array(
				array(
					'slug' => '',
					'title' => __( 'General', 'wdm_ld_course_review' ),
					'template_path' => plugin_dir_path( __FILE__ ) . 'templates/feedback-general-setting.php',
				),
				array(
					'slug' => 'email-setting',
					'title' => __( 'Email template', 'wdm_ld_course_review' ),
					'template_path' => plugin_dir_path( __FILE__ ) . 'templates/feedback-email-setting.php',
				),
				array(
					'slug' => 'wdm-crr-promotion',
					'title' => __( 'Other Extensions', 'wdm_ld_course_review' ),
					'template_path' => plugin_dir_path( __FILE__ ) . 'templates/other-extensions.php',
				),
			);
			$tabs = apply_filters( 'wdm_course_feedback_setting_tabs', $tabs );
			$this->_registered_tabs = $tabs;
		}

		/**
		 * Saving meta box values.
		 *
		 * @param int    $post_id [post id].
		 * @param object $post    [post object].
		 */
		public function save_meta_boxes( $post_id, $post ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			} // do nothing special if autosaving
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				return;
			} // do nothing special if ajax

			if ( ! current_user_can( 'edit_wdm_course_feedbacks', $post_id ) ) {
				return;
			} // do nothing special if user does not have permissions
            // phpcs:disable
			if ( ! isset( $_POST['_wpnonce'] ) || $post->post_type != $this->cpt ) {
				return;
			}
            // phpcs:enable
			// update meta if changed, delete it if not set or blank.
			$types = array( 'meta_box_posts', 'meta_box_feedback' ); // $this->meta_box_posts, $this->meta_box_feedback
			foreach ( $types as $type ) {
				$my_type = $this->$type; // $this->meta_box_posts, $this->meta_box_feedback
				rrf_save_meta_field_val( $post_id, $my_type );
			}
		}

		/**
		 * Adding user feedback and feedback details metabox on feedback page.
		 */
		public function add_meta_boxes() {
			/* translators: %s: Course Title*/
			$title = sprintf( __( 'User Feedback (%s)', 'wdm_ld_course_review' ), get_the_title() );
			add_meta_box( 'wdm_user_feedback', $title, array( &$this, 'show_user_feedback' ), $this->cpt, 'normal', 'high', array( 'type' => 'meta_box_feedback' ) );

			add_meta_box( 'wdm_course_feedback_details', __( 'Feedback details', 'wdm_ld_course_review' ), array( &$this, 'render_meta_boxes' ), $this->cpt, 'normal', 'high', array( 'type' => 'meta_box_feedback' ) );
		}
		/**
		 * Callback function to show user feedback.
		 *
		 * @param object $post [post object].
		 */
		public function show_user_feedback( $post ) {
			echo '<div>
                <p>' . esc_html( nl2br( $post->post_content ) ) . '</p>
            </div>';
		}

		/**
		 * Callback function of Feedback details.
		 *
		 * @param object $post [post object].
		 * @param array  $args [contains field details of meta i.e  meta_box_feedback attribute].
		 */
		public function render_meta_boxes( $post, $args ) {
			$my_args = $args['args'];
			$fields = $this->{$my_args['type']}; // $this->meta_box_posts, $this->meta_box_feedback
			echo '<table class="form-table">';
			foreach ( $fields as $field ) {
				$meta = get_post_meta( $post->ID, $field['id'], true );
				echo '<tr>' .
				 '<th style="width:30%"><label for="' . esc_attr( $field['id'] ) . '">' . esc_html( $field['name'] ) . '</label></th>' ,
				 '<td>';
				switch ( $field['type'] ) {
					case 'text':
						if ( $field['id'] == $this->cpt . '_review_rating' ) {
							?>
							<input name="<?php echo esc_attr( $field['id'] ); ?>" data-id="input-<?php echo esc_attr( $field['id'] ); ?>-xs" class="rating rating-loading" value="<?php echo floatval( $meta ); ?>" data-min="0" data-max="5" data-step="1" data-size="xxs" data-show-clear="false" data-show-caption="false" data-readonly="true" data-theme="krajee-fa">
							<?php
						} else {
							?>
							<input type="text" name="<?php echo esc_attr( $field['id'] ); ?>=" id="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $meta ? $meta : $field['default'] ); ?>" size="30" style="width:97%" <?php echo ( ! $field['disabled'] ? '' : 'disabled="disabled"' ); ?>/>
							<?php
						}
						break;
					case 'textarea':
						?>
						<textarea name="<?php echo esc_attr( $field['id'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" cols="60" rows="4" style="width:97%" <?php echo ( ! $field['disabled'] ? '' : 'disabled="disabled"' ); ?>> <?php echo esc_html( $meta ? $meta : $field['default'] ); ?></textarea>
						<?php
						break;
					case 'select':
						if ( $field['disabled'] ) {
							$temp_post = get_post( $meta );
							if ( ! $temp_post ) {
								break;
							}
							echo '<a href="' . esc_attr( get_edit_post_link( $temp_post->ID ) ) . '" target="_blank">' . esc_html( $temp_post->post_title ) . '</a>';
							break;
						}
						echo '<select name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '">';
						foreach ( $field['options'] as $value => $label ) {
							echo '<option value="' . esc_attr( $value ) . '" ' . ( $meta == $value ? ' selected="selected"' : '' ) . '>' . esc_html( $label ) . '</option>';
						}
						echo '</select>';
						break;
					case 'checkbox':
						echo '<input value="1" type="checkbox" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '"' . ( $meta ? ' checked="checked"' : '' ) . ' ' . ( ! $field['disabled'] ? '' : 'disabled="disabled"' ) . '/>';
						break;
				}
				echo '<div style="padding-top:5px;"><small>' . esc_html( $field['desc'] ) . '</small></div>';
				echo '</td></tr>';
			}
			echo '</table>';
		}

		/**
		 * Removing add media button.
		 */
		public function remove_add_media_button() {
			global $post;
			if ( ! empty( $post ) && $post->post_type == $this->cpt ) {
				remove_action( 'media_buttons', 'media_buttons' );
			}
		}

		/**
		 * Registering feedback CPT.
		 */
		public function create_post_type() {
			$ld_course = rrf_get_course_label();
			$ld_courses = rrf_get_course_label( 'courses' );
			$create_posts = false;
			if ( is_multisite() ) {
				$create_posts = 'do_not_allow';
			}
			$args = array(
				'label' => $ld_course . __( ' Feedback', 'wdm_ld_course_review' ),
				'description' => $ld_course . __( ' Feedback of LD Courses', 'wdm_ld_course_review' ),
				// 'public' => false,
				'exclude_from_search' => true,
				'publicly_queryable' => false,
				'show_in_nav_menus' => false,
				'show_ui' => true,
				'show_in_menu' => $this->cpt,
				'menu_position' => 25,
				'can_export' => true,
				'show_in_admin_bar' => false,
				'has_archive' => false,
				'rewrite' => true,
				'menu_icon' => 'dashicons dashicons-format-chat',
				'capability_type' => $this->cpt,
				'capabilities' => array(
					'create_posts' => $create_posts, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups ).
				),
				'map_meta_cap' => true,
				'supports' => array( 'author' ),
				// 'supports' => array('title'),
			);

			$args['labels'] = array(
				'name' => $ld_course . __( ' feedback', 'wdm_ld_course_review' ),
				'singular_name' => $ld_course . __( ' Feedback', 'wdm_ld_course_review' ),
				/* translators: %s : Course Label */
				'menu_name' => sprintf( __( 'All %s Feedback', 'wdm_ld_course_review' ), $ld_courses ),
				/* translators: %s : Course Label */
				'add_new_item' => sprintf( __( 'Add New %s Feedback', 'wdm_ld_course_review' ), $ld_course ),
				/* translators: %s : Course Label */
				'edit_item' => sprintf( __( 'Edit %s Feedback', 'wdm_ld_course_review' ), $ld_course ),
				/* translators: %s : Course Label */
				'new_item' => sprintf( __( 'New %s Feedback', 'wdm_ld_course_review' ), $ld_course ),
				/* translators: %s : Course Label */
				'view_item' => sprintf( __( 'View %s Feedback', 'wdm_ld_course_review' ), $ld_course ),
				/* translators: %s : Course Label */
				'search_items' => sprintf( __( 'Search %s Feedback', 'wdm_ld_course_review' ), $ld_course ),
				'not_found' => __( 'No Feedback Found', 'wdm_ld_course_review' ),
				'not_found_in_trash' => __( 'No Feedback Found in Trash', 'wdm_ld_course_review' ),
			);
			register_post_type( $this->cpt, $args );
			flush_rewrite_rules();
		}

		/**
		 * Adding menu items to admin must be done in admin_menu which gets executed BEFORE admin_init.
		 */
		public function real_admin_menu() {
			/* translators: %s : Course Label */
			$title = sprintf( __( '%s Feedback', 'wdm_ld_course_review' ), rrf_get_course_label() );
			add_menu_page( $title, $title, 'read_private_wdm_course_feedbacks', $this->cpt, array( $this, 'load_style_n_script' ), 'dashicons-format-chat', '50.94' ); // try to resolve issues with other plugins.
			$setting_title = __( 'Settings', 'wdm_ld_course_review' );
			add_submenu_page( $this->cpt, $setting_title, $setting_title, 'manage_options', 'wdm_course_feedback_setting', array( &$this, 'rrf_course_feedback_setting' ) );
			// loading style to hide add button.
			add_action( 'admin_print_scripts', array( $this, 'load_style_n_script' ) );
		}
		/**
		 * Loading css to hide add new button.
		 */
		public function load_style_n_script() {
			if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->cpt ) {
				wp_enqueue_style( 'wdm-crr-admin-css', plugins_url( 'admin/css/admin.css', __DIR__ ), array(), WDM_LD_COURSE_VERSION );
			}
		}

		/**
		 * Showing feedback setting page.
		 */
		public function rrf_course_feedback_setting() {
			echo '<div class="wrap">';
			$this->save_settings();
			$selected_tab = rrf_check_if_post_set( $_GET, 'tab' );
			// showing tabs.
			$this->tabs( $selected_tab );
			$template_path = '';
			// searching template_path.
			foreach ( $this->_registered_tabs as $tab ) {
				if ( $selected_tab == $tab['slug'] ) {
					$template_path = $tab['template_path'];
					break;
				}
			}

			// including the template.
			if ( ! empty( $template_path ) ) {
				include_once $template_path;
			}
			echo '</div>';
		}

		/**
		 * This function will show all reviews of specific course.
		 *
		 * @param WP_Query Object $query [query object].
		 *
		 * @return WP_Query Object $query
		 */
		public function show_specific_course_feedback( $query ) {
			if ( ! isset( $_GET['wdm_feedback_course_id'] ) || empty( $_GET['wdm_feedback_course_id'] ) ) {
				return $query;
			}
			if ( $query->is_admin ) {
				if ( $query->query['post_type'] == $this->cpt ) {
					$query->query_vars['meta_key'] = $this->cpt . '_feedback_on_course';
					$query->query_vars['meta_value'] = filter_input( INPUT_GET, 'wdm_feedback_course_id', FILTER_VALIDATE_INT );
				}
			}

			return $query;
		}

		/**
		 * For showing tabs on feedback setting page.
		 *
		 * @param string $selected_tab [current selected tab].
		 */
		protected function tabs( $selected_tab = '' ) {
			echo '<h1 class="nav-tab-wrapper">';

			foreach ( $this->_registered_tabs as $tab ) {
				$active = '';
				if ( $tab['slug'] == $selected_tab ) {
					$active = 'active';
				}
				echo '<a href="admin.php?page=wdm_course_feedback_setting&tab=' . esc_attr( $tab['slug'] ) . '" class="nav-tab nav-tab-' . esc_attr( $active ) . '">' . esc_html( $tab['title'] ) . '</a>';
			}

			echo '</h1>';
		}

		/**
		 * Saving feedback settings.
		 */
		protected function save_settings() {
			// notice class notice-success | notice-error.
			$updated_msg = '<div class="updated notice %s is-dismissible">
                            <p>%s.</p>
                            <button type="button" class="notice-dismiss">
                            </button>
                            </div>';
			// saving general setting.
			if ( isset( $_POST['wdm_feedback_general_setting_nonce'] )
				&& wp_verify_nonce( sanitize_key( $_POST['wdm_feedback_general_setting_nonce'] ), 'wdm_feedback_general_setting_action' ) ) {
				$setting = rrf_check_if_post_set( $_POST, 'wdm_course_feedback_setting' );
				$email_setting = rrf_check_if_post_set( $_POST, 'wdm_send_email_after_feedback' );
				$btn_txt = rrf_check_if_post_set( $_POST, 'wdm_course_feedback_btn_txt' );
				update_option( 'wdm_course_feedback_setting', $setting );
				update_option( 'wdm_send_email_after_feedback', $email_setting );
				update_option( 'wdm_course_feedback_btn_txt', $btn_txt );
				echo sprintf( $updated_msg, 'notice-success', 'Setting updated' );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			// saving email template.
			if ( isset( $_POST['wdm_feedback_email_template_nonce'] )
				&& wp_verify_nonce( sanitize_key( $_POST['wdm_feedback_email_template_nonce'] ), 'wdm_feedback_email_template_action' ) ) {
				$email_subject = rrf_check_if_post_set( $_POST, 'wdm_feedback_email_subject' );
				$email_body = rrf_check_if_post_set( $_POST, 'wdm_feedback_email_body' );
				update_option( 'wdm_feedback_email_subject', $email_subject );
				update_option( 'wdm_feedback_email_body', $email_body );
				echo sprintf( $updated_msg, 'notice-success', 'Template updated' );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			do_action( 'wdm_on_feedback_setting_update' );
		}
	}

	Course_Feedback_CPT::get_instance();
}
