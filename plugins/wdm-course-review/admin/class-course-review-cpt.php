<?php
/**
 * This file is used to include the class which registers Reviews CPT in WordPress.
 *
 * @package RatingsReviewsFeedback\Admin\Reviews
 */

namespace ns_wdm_ld_course_review{

	/**
	 * This will create custom post type for Course reviews to handle rating and reviews of the course.
	 */
	class Course_Review_CPT {
		/**
		 * CPT Slug
		 *
		 * @var string
		 */
		public $cpt = 'wdm_course_review';
		/**
		 * Fields shown in metabox.
		 *
		 * @var array
		 */
		public $meta_box_reviews = array();
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
			register_activation_hook( RRF_PLUGIN_FILE, array( $this, 'rrf_register_post_type' ) );
			// registering cpt of course review.
			\wdm_add_hook( 'init', 'create_post_type', $this, array( 'priority' => 11 ) );

			// adding menu on dashboard.
			\wdm_add_hook( 'admin_menu', 'real_admin_menu', $this );

			// removing add media button from editor for reviews post type.
			\wdm_add_hook( 'admin_head', 'remove_add_media_button', $this );

			// for setting default values that we are going to use on review cpt.
			\wdm_add_hook( 'admin_init', 'setting_default_values', $this );

			// adding meta box for review details.
			\wdm_add_hook( 'add_meta_boxes', 'add_meta_boxes', $this );

			// for saving meta box values.
			\wdm_add_hook( 'save_post', 'save_meta_boxes', $this, array( 'num_args' => 2 ) );

			// for setting meta fields that we are going to use on review edit page.
			\wdm_add_hook( 'admin_init', 'setting_meta_fields', $this );

			// adding new custom columns i.e assigned course, rating and excerpt.
			\wdm_add_hook( 'manage_edit-' . $this->cpt . '_columns', 'add_col_reviewed_course', $this, array( 'num_args' => 1 ) );

			// show the related data on table for custom columns.
			\wdm_add_hook( 'manage_' . $this->cpt . '_posts_custom_column', 'show_col_review_course', $this, array( 'num_args' => 2 ) );

			\wdm_add_hook(
				'manage_edit-' . $this->cpt . '_sortable_columns',
				'review_sort_columns',
				$this,
				array(
					'type' => 'filter',
					'priority' => 10,
					'num_args' => 1,
				)
			);

			\wdm_add_hook(
				'request',
				'review_sort_col_orderby',
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
				'show_specific_review',
				$this,
				array(
					'type' => 'filter',
					'priority' => 10,
					'num_args' => 1,
				)
			);

			\wdm_add_hook( 'admin_enqueue_scripts', 'enqueue_star_rating_lib', $this, array( 'num_args' => 1 ) );
			// removing discussion meta for students.
			\wdm_add_hook( 'admin_menu', 'remove_discussion_metabox', $this );
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

			// adding popup content in list table.
			\wdm_add_hook(
				'views_edit-' . $this->cpt,
				'add_popup_html',
				$this,
				array(
					'type' => 'filter',
					'priority' => 10,
					'num_args' => 1,
				)
			);

			// adding approve and reject action.
			\wdm_add_hook(
				'post_row_actions',
				'add_review_links',
				$this,
				array(
					'type' => 'filter',
					'priority' => 10,
					'num_args' => 2,
				)
			);
			// ajax call for approve and reject links.
			\wdm_add_hook(
				'wp_ajax_wdm_course_review_link_update',
				'rrf_update_review_details',
				$this,
				array(
					'type' => 'action',
					'priority' => 10,
					'num_args' => 0,
				)
			);
			// add custom post status Rejected.
			\wdm_add_hook(
				'admin_footer-post.php',
				'rrf_review_post_status',
				$this,
				array(
					'type' => 'action',
					'priority' => 10,
					'num_args' => 0,
				)
			);
			// }
		}

		/**
		 * Register post type and flush rewrite rules on plugin activation.
		 */
		public function rrf_register_post_type() {
			$this->create_post_type();
			flush_rewrite_rules();
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
		 * [add_filter_coursewise description].
		 *
		 * @param string $post_type [post_type].
		 * @param string $which     [which].
		 */
		public function add_filter_coursewise( $post_type, $which ) {
			if ( $post_type == $this->cpt && 'top' == $which ) {
				$all_courses = \rrf_get_all_courses();
				$selected = rrf_check_if_post_set( $_GET, 'wdm_reviews_course_id' );
				/* translators: %s : Course Label*/
				$default_label = sprintf( __( '--- Select %s ---', 'wdm_ld_course_review' ), rrf_get_course_label() );
				?>
				<select name="wdm_reviews_course_id">
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
		 * Removing discussion meta if they dont have publish_wdm_course_reviews capability.
		 */
		public function remove_discussion_metabox() {
			if ( ! current_user_can( 'publish_wdm_course_reviews' ) ) {
				remove_meta_box( 'commentstatusdiv', $this->cpt, 'normal' );
			}
		}
		/**
		 * Enqueuing star rating lib on review and course page.
		 *
		 * @param string $hook_suffix enqueues the star rating lib in the WP Backend.
		 */
		public function enqueue_star_rating_lib( $hook_suffix ) {
			if ( in_array( $hook_suffix, array( 'post.php', 'post-new.php', 'edit.php' ) ) ) {
				$screen = get_current_screen();
				$allowed_post_types = array( $this->cpt, 'sfwd-courses' );
				$allowed_post_types = apply_filters( 'wdm_course_review_load_star_js', $allowed_post_types );
				if ( is_object( $screen ) && in_array( $screen->post_type, $allowed_post_types ) ) {
					rrf_load_star_rating_lib();
					// Enqueue the popup library files.
					rrf_load_jquery_modal_lib();
				}
			}
		}

		/**
		 * This function will show all reviews of specific course.
		 *
		 * @param WP_Query Object $query Query Params.
		 *
		 * @return WP_Query Object $query
		 */
		public function show_specific_review( $query ) {
			if ( ! isset( $_GET['wdm_reviews_course_id'] ) || empty( $_GET['wdm_reviews_course_id'] ) ) {
				return $query;
			}
			if ( $query->is_admin && $query->query['post_type'] == $this->cpt ) {
				$query->query_vars['meta_key'] = $this->cpt . '_review_on_course';
				$query->query_vars['meta_value'] = filter_input( INPUT_GET, 'wdm_reviews_course_id', FILTER_VALIDATE_INT );
			}

			return $query;
		}
		/**
		 * Adding columns on custom post type page.
		 *
		 * @param array $columns [array of columns].
		 *
		 * @return array $columns [array of columns]
		 */
		public function review_sort_columns( $columns ) {
			$columns[ $this->cpt . '_review_rating' ] = $this->cpt . '_review_rating';
			$columns[ $this->cpt . '_review_course' ] = $this->cpt . '_review_course';

			return apply_filters( 'wdm_course_review_cpt_columns', $columns );
		}
		/**
		 * To sort the reviews table according to course and rating.
		 *
		 * @param WP_Query Object $request Args for query.
		 *
		 * @return WP_Query Object $request
		 */
		public function review_sort_col_orderby( $request ) {
			if ( ! isset( $request['post_type'] ) ) {
				return $request;
			}
			if ( $request['post_type'] !== $this->cpt ) {
				return $request;
			}

			if ( isset( $request['orderby'] ) ) {
				if ( $request['orderby'] === $this->cpt . '_review_rating' ) {
					$request = array_merge(
						$request,
						array(
							'meta_key' => $this->cpt . '_review_rating',
							'orderby' => 'meta_value_num',
						)
					);
				} elseif ( $request['orderby'] === $this->cpt . '_review_course' ) {
					$request = array_merge(
						$request,
						array(
							'meta_key' => $this->cpt . '_review_on_course',
							'orderby' => 'meta_value_num',
						)
					);
				}
			}

			return $request;
		}

		/**
		 * Showing course name with link on review wp_table.
		 *
		 * @param string $column  [column name].
		 * @param int    $post_id [post id].
		 */
		public function show_col_review_course( $column, $post_id ) {
			switch ( $column ) {
				case $this->cpt . '_review_course':
					$reviewed_post_id = get_post_meta( $post_id, $this->cpt . '_review_on_course', true );
					if ( '' !== $reviewed_post_id ) {
						$reviewed_post = get_post( $reviewed_post_id );
						$permalink = get_permalink( $reviewed_post_id );
						$not_published = ( 'publish' !== $reviewed_post->post_status ) ? __( '(Not Published)', 'wdm_ld_course_review' ) : '';
						echo sprintf( '<div><a target="_blank" href="%1$s">%2$s</a> %3$s</div>', esc_url( $permalink ), esc_html( $reviewed_post->post_title ), esc_html( $not_published ) );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						echo esc_html__( 'Not Assigned', 'wdm_ld_course_review' );
					}
					break;
				case $this->cpt . '_review_rating':
					$stars = get_post_meta( $post_id, $this->cpt . '_review_rating', true );
					?>
					<input data-id="input-<?php echo esc_attr( ( intval( $post_id ) ) ); ?>-xs" class="rating rating-loading" value="<?php echo esc_attr( floatval( $stars ) ); ?>" data-min="0" data-max="5" data-step="1" data-size="xxs" data-show-clear="false" data-show-caption="false" data-readonly="true" data-theme="krajee-fa">
					<?php
					break;
				case $this->cpt . '_review_excerpt':
					$post = get_post( $post_id );
					$text = $post->post_content;
					$content = $text;
					if ( strlen( $text ) > 50 ) {
						$content = substr( $text, 0, 50 ) . '...';
					}
					?>
					<a href='#' class='wdm_review_excerpt_popup' data-wdm-rrf-review='<?php echo esc_attr( $text ); ?>'><?php echo esc_html( $content ); ?></a>
					<?php
					break;
			}
		}
		/**
		 * Adding two columns new columns to show course and its rating.
		 *
		 * @param array $columns [contains list of columns].
		 *
		 * @return array $columns [contains list of columns]
		 *
		 * @version 1.0.0
		 */
		public function add_col_reviewed_course( $columns ) {
			$columns[ $this->cpt . '_review_course' ] = rrf_get_course_label();
			$columns[ $this->cpt . '_review_rating' ] = __( 'Rating', 'wdm_ld_course_review' );
			$columns[ $this->cpt . '_review_excerpt' ] = __( 'Excerpt', 'wdm_ld_course_review' );

			return apply_filters( 'wdm_course_review_add_remove_columns', $columns );
		}
		/**
		 * Setting default values which will get displayed on meta field.
		 */
		public function setting_meta_fields() {
			$all_courses = \rrf_get_all_course_reviews();
			$post_array = array( '' => '--- ' . __( 'Select', 'wdm_ld_course_review' ) . rrf_get_course_label() . ' ---' );
			foreach ( $all_courses as $course ) {
				$post_array[ $course->ID ] = $course->post_title;
			}
			$fields = array(
				array(
					'name' => rrf_get_course_label(),
					'desc' => '',
					'id' => $this->cpt . '_review_on_course',
					'type' => 'select',
					'options' => $post_array,
					'disabled' => true,
				),
				array(
					'name' => __( 'Rating', 'wdm_ld_course_review' ),
					'desc' => '',
					'id' => $this->cpt . '_review_rating',
					'type' => 'text',
					'default' => 0,
					'disabled' => false,
				),
			);
			$fields = apply_filters( 'wdm_course_review_meta_fields', $fields );
			$this->meta_box_reviews = $fields;
			$this->meta_box_posts = array();
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

			if ( ! current_user_can( 'edit_wdm_course_reviews', $post_id ) ) {
				return;
			} // do nothing special if user does not have permissions
			// phpcs:disable
			if ( ! isset( $_POST['_wpnonce'] ) || $post->post_type != $this->cpt ) {
				return;
			}
			// phpcs:enable
			// update meta if changed, delete it if not set or blank.
			$types = array( 'meta_box_posts', 'meta_box_reviews' ); // $this->meta_box_posts, $this->meta_box_reviews
			foreach ( $types as $type ) {
				$my_type = $this->$type; // $this->meta_box_posts, $this->meta_box_reviews
				rrf_save_meta_field_val( $post_id, $my_type );
			}
		}

		/**
		 * Adding meta to show review details eg assinged course.
		 */
		public function add_meta_boxes() {
			/* translators: %s: Course Title*/
			$title = sprintf( __( 'User Review (Title: %s)', 'wdm_ld_course_review' ), get_the_title() );
			add_meta_box( 'wdm_user_review', $title, array( &$this, 'show_user_review' ), $this->cpt, 'normal', 'high', array( 'type' => 'meta_box_reviews' ) );
			add_meta_box( 'wdm_course_review_details', __( 'Review details', 'wdm_ld_course_review' ), array( &$this, 'render_meta_boxes' ), $this->cpt, 'normal', 'high', array( 'type' => 'meta_box_reviews' ) );
		}


		/**
		 * Callback function to show user review.
		 *
		 * @param object $post [post object].
		 */
		public function show_user_review( $post ) {
			?>
			<div>
				<p><?php echo esc_html( nl2br( $post->post_content ) ); ?></p>
			</div>
			<?php
		}

		/**
		 * Callback function of review details meta field.
		 *
		 * @param object $post [post object].
		 * @param array  $args [contains field details of meta i.e  meta_box_reviews attribute].
		 */
		public function render_meta_boxes( $post, $args ) {
			$my_post_status = get_post_status( get_the_ID() );
			if ( 'rejected' == $my_post_status ) {
				$this->meta_box_reviews[] = array(
					'name' => __( 'Rejection reason', 'wdm_ld_course_review' ),
					'desc' => '',
					'id' => $this->cpt . '_review_rejected',
					'type' => 'text',
					'default' => '',
					'disabled' => false,
				);
			}
			$my_args = $args['args'];
			$fields = $this->{$my_args['type']}; // $this->meta_box_posts, $this->meta_box_reviews
			echo '<table class="form-table">';
			foreach ( $fields as $field ) {
				$meta = get_post_meta( $post->ID, $field['id'], true );
				echo '<tr>' .
				 '<th style="width:30%"><label for="' . esc_attr( $field['id'] ) . '">' . esc_html( $field['name'] ) . '</label></th>',
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
		 * Registering Review CPT.
		 */
		public function create_post_type() {
			$ld_course = rrf_get_course_label();
			$ld_courses = rrf_get_course_label( 'courses' );
			$create_posts = false;
			if ( is_multisite() ) {
				$create_posts = 'do_not_allow';
			}
			$args = array(
				'label' => $ld_course . ' ' . __( 'Review', 'wdm_ld_course_review' ),
				'description' => $ld_course . __( ' Review of LD ', 'wdm_ld_course_review' ) . $ld_courses,
				// 'public' => false,
				'exclude_from_search' => true,
				'publicly_queryable' => true,
				'show_in_nav_menus' => false,
				'show_ui' => true,
				'show_in_menu' => $this->cpt,
				'menu_position' => 25,
				'can_export' => true,
				'show_in_admin_bar' => false,
				'has_archive' => false,
				'rewrite' => true,
				'menu_icon' => 'dashicons-star-filled',
				'capability_type' => $this->cpt,
				'capabilities' => array(
					'create_posts' => $create_posts, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups ).
				),
				'map_meta_cap' => true,
				'supports' => array( 'author', 'comments' ),
				// 'supports' => array('title'),
			);
			$args['labels'] = array(
				'name' => $ld_course . __( ' review', 'wdm_ld_course_review' ),
				'singular_name' => $ld_course . __( ' Review', 'wdm_ld_course_review' ),
				/* translators: %s : Course Label */
				'menu_name' => sprintf( __( 'All %s Reviews', 'wdm_ld_course_review' ), $ld_courses ),
				/* translators: %s : Course Label */
				'add_new_item' => sprintf( __( 'Add New %s Review', 'wdm_ld_course_review' ), $ld_course ),
				/* translators: %s : Course Label */
				'edit_item' => sprintf( __( 'Edit %s Review', 'wdm_ld_course_review' ), $ld_course ),
				/* translators: %s : Course Label */
				'new_item' => sprintf( __( 'New %s Review', 'wdm_ld_course_review' ), $ld_course ),
				/* translators: %s : Course Label */
				'view_item' => sprintf( __( 'View %s Review', 'wdm_ld_course_review' ), $ld_course ),
				/* translators: %s : Course Label */
				'search_items' => sprintf( __( 'Search %s Reviews', 'wdm_ld_course_review' ), $ld_course ),
				'not_found' => __( 'No Reviews Found', 'wdm_ld_course_review' ),
				'not_found_in_trash' => __( 'No Reviews Found in Trash', 'wdm_ld_course_review' ),
			);
			register_post_type( $this->cpt, $args );

			// Register Custom Status.
			$args = array(
				'label'                     => __( 'Rejected', 'wdm_ld_course_review' ),
				/* translators: %s : Reject Review Post Status*/
				'label_count'               => _n_noop( 'Rejected (%s)', 'Rejected (%s)', 'wdm_ld_course_review' ),
				'public'                    => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'exclude_from_search'       => false,
			);
			register_post_status( 'rejected', $args );
		}

		/**
		 * Adding menu items to admin must be done in admin_menu which gets executed BEFORE admin_init.
		 */
		public function real_admin_menu() {
			/* translators: %s : Course Label */
			$title = sprintf( __( '%s Reviews', 'wdm_ld_course_review' ), rrf_get_course_label() );
			$args = array(
				'posts_per_page'    => -1,
				'post_type'         => 'wdm_course_review',
				'post_status'       => 'pending',
				'meta_key'          => 'wdm_course_review_review_rating', // for loading top rated reviews.
			);
			$reviews = get_posts( $args );
			$notification_count = count( $reviews );
			$menu_title = $notification_count ? sprintf( '%s <span class="awaiting-mod">%d</span>', $title, $notification_count ) : $title;
			add_menu_page( $title, $menu_title, 'read_private_wdm_course_reviews', $this->cpt, '', 'dashicons-star-filled', '50.92' ); // try to resolve issues with other plugins.
			$setting_title = __( 'Settings', 'wdm_ld_course_review' );
			add_submenu_page( $this->cpt, $setting_title, $setting_title, 'manage_options', 'wdm_course_review_setting', array( &$this, 'rrf_course_review_setting' ) );

			add_action( 'admin_print_scripts', array( $this, 'load_style_n_script' ) );
		}
		/**
		 * Loading css to hide add new button.
		 */
		public function load_style_n_script() {
			if ( is_admin() && isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->cpt ) {
				wp_enqueue_style( 'wdm-crr-admin-css', plugins_url( 'admin/css/admin.css', RRF_PLUGIN_FILE ), array(), WDM_LD_COURSE_VERSION );
				wp_enqueue_script( 'wdm-crr-admin-js', plugins_url( 'admin/js/admin.js', RRF_PLUGIN_FILE ), array( 'jquery' ), WDM_LD_COURSE_VERSION );
				$ajax_nonce = wp_create_nonce( 'wdm-nonce-course-review-approve' );
				wp_localize_script(
					'wdm-crr-admin-js',
					'wdm_approve_ajax',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'action' => 'wdm_course_review_link_update',
						'loader_url' => plugins_url( 'admin/images/loader.gif', RRF_PLUGIN_FILE ),
						'nonce' => $ajax_nonce,
						'wait_message' => __( 'Please wait', 'wdm_ld_course_review' ),
						'ajax_time' => __( 'Failed from timeout', 'wdm_ld_course_review' ),
						'popup_container' => '<div class="wdm-popup-div" style="display: none;">
                                    <div class="wdm-popup-div-content">
                                        <p>I am in popup</p>
                                    </div>
                                </div>',
					)
				);
				wp_enqueue_script( 'wdm-crr-admin-js' );
			}
		}

		/**
		 * Showing review settings page.
		 */
		public function rrf_course_review_setting() {
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
		 * Saving review settings.
		 */
		protected function save_settings() {
			// notice class notice-success | notice-error.
			$updated_msg = '<div class="updated notice %s is-dismissible">
                            <p>%s.</p>
                            <button type="button" class="notice-dismiss">
                            </button>
                            </div>';
			// saving general setting.
			if ( isset( $_POST['wdm_review_general_setting_nonce'] )
				&& wp_verify_nonce( sanitize_key( $_POST['wdm_review_general_setting_nonce'] ), 'wdm_review_general_setting_action' ) ) {
				$setting = rrf_check_if_post_set( $_POST, 'wdm_course_review_setting' );
				$email_setting = rrf_check_if_post_set( $_POST, 'wdm_send_email_after_review' );
				$default_subject = rrf_check_if_post_set( $_POST, 'wdm_review_default_reject_subject' );
				$default_message = rrf_check_if_post_set( $_POST, 'wdm_review_default_message' );
				update_option( 'wdm_course_review_setting', $setting );
				update_option( 'wdm_send_email_after_review', $email_setting );
				update_option( 'wdm_review_default_reject_subject', $default_subject );
				update_option( 'wdm_review_default_message', $default_message );
				echo sprintf( $updated_msg, 'notice-success', 'Setting updated' );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			// saving email template.
			if ( isset( $_POST['wdm_review_email_template_nonce'] )
				&& wp_verify_nonce( sanitize_key( $_POST['wdm_review_email_template_nonce'] ), 'wdm_review_email_template_action' ) ) {
				$email_subject = rrf_check_if_post_set( $_POST, 'wdm_review_email_subject' );
				$email_body = rrf_check_if_post_set( $_POST, 'wdm_review_email_body' );
				update_option( 'wdm_review_email_subject', $email_subject );
				update_option( 'wdm_review_email_body', $email_body );
				echo sprintf( $updated_msg, 'notice-success', 'Template updated' );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			do_action( 'wdm_on_review_setting_update' );
		}

		/**
		 * For showing tabs on review settings page.
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
				echo '<a href="admin.php?page=wdm_course_review_setting&tab=' . esc_attr( $tab['slug'] ) . '" class="nav-tab nav-tab-' . esc_attr( $active ) . '">' . esc_html( $tab['title'] ) . '</a>';
			}

			echo '</h1>';
		}
		/**
		 * Setting default values which will get displayed on meta field.
		 */
		public function setting_default_values() {
			$tabs = array(
				array(
					'slug' => '',
					'title' => __( 'General', 'wdm_ld_course_review' ),
					'template_path' => plugin_dir_path( __FILE__ ) . 'templates/review-general-setting.php',
				),
				array(
					'slug' => 'email-setting',
					'title' => __( 'Email template', 'wdm_ld_course_review' ),
					'template_path' => plugin_dir_path( __FILE__ ) . 'templates/review-email-setting.php',
				),
				array(
					'slug' => 'wdm-crr-promotion',
					'title' => __( 'Other Extensions', 'wdm_ld_course_review' ),
					'template_path' => plugin_dir_path( __FILE__ ) . 'templates/other-extensions.php',
				),
			);
			$tabs = apply_filters( 'wdm_course_review_setting_tabs', $tabs );
			$this->_registered_tabs = $tabs;
		}
		/**
		 * Adding Approve/Reject links to the reviews
		 *
		 * @param array          $actions [List of actions for Post type processing].
		 * @param WP_Post object $post_object Post Object.
		 */
		public function add_review_links( $actions, $post_object ) {
			if ( $post_object->post_type == $this->cpt ) {
				$result = $this->check_post_status( $post_object->post_status );
				$message = get_post_meta( $post_object->ID, $this->cpt . '_review_rejected', true );
				if ( ! isset( $message ) || '' == $message ) {
					$message = get_option( 'wdm_review_default_message' );
				}
				$hide_approve = '';
				$hide_reject = '';
				if ( 0 == $result ) {
					$hide_approve = 'wdm_hide_link';
					$hide_reject = 'wdm_hide_link';
				} elseif ( 1 == $result ) {
					$hide_approve = 'wdm_hide_link';
				} elseif ( 2 == $result ) {
					$hide_reject = 'wdm_hide_link';
				}
				$actions[ 'wdm_rrf_approve ' . $hide_approve ] = "<a href='#' id='wdm_rrf_approve-" . $post_object->ID . "'>" . __( 'Approve', 'wdm_ld_course_review' ) . '</a>';
				$actions[ 'wdm_rrf_reject ' . $hide_reject ] = "<a href='#' id='wdm_rrf_reject-" . $post_object->ID . "' data-rejected='" . $message . "'>" . __( 'Reject', 'wdm_ld_course_review' ) . '</a>';
				unset( $actions['inline hide-if-no-js'] );
			}
			return $actions;
		}
		/**
		 * [check_post_status description].
		 *
		 * @param  string $status [description].
		 * @return [type]         [description]
		 */
		private function check_post_status( $status ) {
			if ( 'publish' == $status ) {
				return 1;
			} elseif ( 'rejected' == $status ) {
				return 2;
			} elseif ( 'pending' == $status ) {
				return 3;
			}
			return 0;
		}
		/**
		 * Ajax call for the Approve/Reject links to the reviews
		 */
		public function rrf_update_review_details() {
			$result = array(
				'success' => false,
				'review_id' => 0,
				'status' => '',
				'message' => '',
				'validation_pass' => false,
			);

			if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_key( $_POST['security'] ), 'wdm-nonce-course-review-approve' ) ) {
				echo json_encode( $result );
				die();
			}
			$review_id = explode( '-', rrf_check_if_post_set( $_POST, 'review_id' ) );
			$result['review_id'] = $review_id[1];
			$result['status'] = rrf_check_if_post_set( $_POST, 'link_action' );
			$result['message'] = rrf_check_if_post_set( $_POST, 'message' );
			$my_post_status = get_post_status( $result['review_id'] );

			// Update the post.
			if ( 'approve' == $result['status'] && 'publish' != $my_post_status ) {
				$update_post = array(
					'ID'           => $result['review_id'],
					'post_status'   => 'publish',
				);
				wp_update_post( $update_post );
				$result['validation_pass'] = true;
			} elseif ( 'reject' == $result['status'] && 'rejected' != $my_post_status ) {
				$my_post = array(
					'ID'           => $result['review_id'],
					'post_status'   => 'rejected',
				);
				wp_update_post( $my_post );
				update_post_meta( $result['review_id'], $this->cpt . '_review_rejected', $result['message'] );

				// send mail to the review author.
				$my_post = get_post( $result['review_id'] );
				$author = get_userdata( $my_post->post_author );

				$email_subject = get_option( 'wdm_review_default_reject_subject', WDM_LD_DEFAULT_REVIEW_REJECTION_SUBJECT );

				$current_user = wp_get_current_user();
				$headers[] = "From: {$current_user->display_name} <{$current_user->user_email}>";
				$headers[] = "Reply-To: {$current_user->display_name} <{$current_user->user_email}";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				wp_mail( $author->user_email, $email_subject, nl2br( $result['message'] ), $headers );
				$result['validation_pass'] = true;
			}

			do_action( 'wdm_on_review_links_update' );
			echo json_encode( $result );
			die();
		}
		/**
		 * This function is used to add rejection popup html.
		 *
		 * @param array $views [Views variable].
		 */
		public function add_popup_html( $views ) {
			?>
			  <div id='wdm_rrf_reject_popup' style="display:none">
			  <div>
				  <label><font size="2"><b><?php esc_html_e( 'Reason for rejection', 'wdm_ld_course_review' ); ?></b></font>
			  </label>
			  </div>
			  <div>
			  <textarea id="wdm_review_reject_reason" name="wdm_review_reject_reason" rows="5" placeholder="<?php esc_attr_e( 'Provide your reason for rejection here', 'wdm_ld_course_review' ); ?>" maxlength="500"></textarea>
			  </div>
			  <button id="wdm_review_reject_submission" class="button" name="wdm_review_reject_sub_btn"><?php esc_html_e( 'Submit', 'wdm_ld_course_review' ); ?></button>
			  </div>
			<?php
			return $views;
		}
		/**
		 * Add custom post status Rejected
		 */
		public function rrf_review_post_status() {
			global $post;
			$complete = '';
			$label = '';
			if ( $post->post_type == $this->cpt ) {
				if ( 'rejected' == $post->post_status ) {
					 $complete = ' selected=\"selected\"';
					 $label = '<span id=\"post-status-display\"> Rejected</span>';
				}
				 echo '
          <script>
          jQuery(document).ready(function($){
               $("select#post_status").append("<option value=\"rejected\" ' . esc_attr( $complete ) . '>Rejected</option>");
               $(".misc-pub-section label").append("' . esc_html( $label ) . '");
          });
          </script>
          ';
			}
		}
	}

	Course_Review_CPT::get_instance();
}
