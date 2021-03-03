<?php
/**
 * Common pluggable functions
 *
 * @since      3.5.0
 * @package    Instructor_Role
 * @subpackage Instructor_Role/includes
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

// namespace InstructorRole\Includes;

/**
 * Check whether the user role is instructor or not.
 *
 * @param int $user_id wp user id, if user_id is null then it considers current logged in user_id
 *
 * @return bool if instructor true, else false
 */
if ( ! function_exists( 'wdm_is_instructor' ) ) {
	/**
	 * Check if a user is an instructor
	 *
	 * @param int $user_id  ID of the User.
	 *
	 * @return bool         True if user is instructor, false otherwise.
	 */
	function wdm_is_instructor( $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		// v2.4.1 added condition to check is the user is instructor or not.
		$is_instructor = false;

		// check if get_userdata pluggable function present.
		if ( ! function_exists( 'get_userdata' ) ) {
			require_once ABSPATH . WPINC . '/pluggable.php';
		}

		$user_info = get_userdata( $user_id );

		if ( $user_info ) {
			if ( in_array( 'wdm_instructor', $user_info->roles ) ) {
				$is_instructor = true;
			}
		}

		/**
		 * Filter check for instructors
		 *
		 * @param bool $is_instructor   True if current user is instructor, false otherwise.
		 */
		return apply_filters( 'wdm_check_instructor', $is_instructor );
	}
}

/*
 * returns author id if post has author
 * @param int $post_id post id of post
 * @return int author_id author id of post
 */
if ( ! function_exists( 'wdm_get_author' ) ) {
	function wdm_get_author( $post_id = null ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}
		if ( empty( $post_id ) ) {
			return;
		}

		$postdata = get_post( $post_id );

		if ( isset( $postdata->post_author ) ) {
			return $postdata->post_author;
		}

		return;
	}
}

/*
 * to search item in multidimentional array
 */
function wdm_in_array( $needle, $haystack, $strict = false ) {
	foreach ( $haystack as $item ) {
		if ( ( $strict ? $item === $needle : $item == $needle ) || ( is_array( $item ) && wdm_in_array( $needle, $item, $strict ) ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Custom Author meta box to display on a edit post page.
 *
 * @param object $post  WP_Post object.
 */
function wdm_post_author_meta_box( $post ) {
	global $user_ID;
	?>
	<label class="screen-reader-text" for="post_author_override">
		<?php esc_html_e( 'Author', 'wdm_instructor_role' ); ?>
	</label>
	<?php
	$wdm_args = array(
		'name'             => 'post_author_override',
		'selected'         => empty( $post->ID ) ? $user_ID : $post->post_author,
		'include_selected' => true,
	);
	/**
	 * Filter author arguments
	 *
	 * @since 1.0.0
	 *
	 * @param array $wdm_args   Array of arguments.
	 */
	$args = apply_filters( 'wdm_author_args', $wdm_args );
	wdm_wp_dropdown_users( $args );
}

/**
 * To create HTML dropdown element of the users for given argument.
 *
 * @param array $args   Array of arguments.
 */
function wdm_wp_dropdown_users( $args = '' ) {
	$defaults = array(
		'show_option_all'         => '',
		'show_option_none'        => '',
		'hide_if_only_one_author' => '',
		'orderby'                 => 'display_name',
		'order'                   => 'ASC',
		'include'                 => '',
		'exclude'                 => '',
		'multi'                   => 0,
		'show'                    => 'display_name',
		'echo'                    => 1,
		'selected'                => 0,
		'name'                    => 'user',
		'class'                   => '',
		'id'                      => '',
		'include_selected'        => false,
		'option_none_value'       => -1,
	);

	$defaults['selected'] = wdmCheckAuthor( get_query_var( 'author' ) );

	$rvar              = wp_parse_args( $args, $defaults );
	$show              = $rvar['show'];
	$show_option_all   = $rvar['show_option_all'];
	$show_option_none  = $rvar['show_option_none'];
	$option_none_value = $rvar['option_none_value'];

	$query_args           = wp_array_slice_assoc( $rvar, array( 'blog_id', 'include', 'exclude', 'orderby', 'order' ) );
	$query_args['fields'] = array( 'ID', 'user_login', $show );

	$users = array_merge( get_users( array( 'role' => 'administrator' ) ), get_users( array( 'role' => 'wdm_instructor' ) ), get_users( array( 'role' => 'author' ) ) );

	if ( ! empty( $users ) && ( count( $users ) > 1 ) ) {
		$name = esc_attr( $rvar['name'] );
		if ( $rvar['multi'] && ! $rvar['id'] ) {
			$idd = '';
		} else {
			$idd = wdmCheckAndGetId( $rvar['id'], $name );
		}
		$output = "<select name='{$name}'{$idd} class='" . $rvar['class'] . "'>\n";

		if ( $show_option_all ) {
			$output .= "\t<option value='0'>$show_option_all</option>\n";
		}

		if ( $show_option_none ) {
			$_selected = selected( $option_none_value, $rvar['selected'], false );
			$output   .= "\t<option value='" . esc_attr( $option_none_value ) . "'$_selected>$show_option_none</option>\n";
		}

		$found_selected = false;
		foreach ( (array) $users as $user ) {
			$user->ID  = (int) $user->ID;
			$_selected = selected( $user->ID, $rvar['selected'], false );
			if ( $_selected ) {
				$found_selected = true;
			}
			$display = wdmGetDisplayName( $user->$show, $user->user_login );
			$output .= "\t<option value='$user->ID'$_selected>" . esc_html( $display ) . "</option>\n";
		}

		if ( $rvar['include_selected'] && ! $found_selected && ( $rvar['selected'] > 0 ) ) {
			$user      = get_userdata( $rvar['selected'] );
			$_selected = selected( $user->ID, $rvar['selected'], false );

			$display = wdmGetDisplayName( $user->$show, $user->user_login );
			$output .= "\t<option value='$user->ID'$_selected>" . esc_html( $display ) . "</option>\n";
		}

		$output .= '</select>';
	}
	wdmPrintOutput( $rvar['echo'], $output );

	return $output;
}

function wdmCheckAuthor( $query_var_author ) {
	if ( is_author() ) {
		return $query_var_author;
	}

	return 0;
}
function wdmGetDisplayName( $user_show, $user_login ) {
	if ( ! empty( $user_show ) ) {
		return $user_show;
	}

	return '(' . $user_login . ')';
}
function wdmCheckAndGetId( $rvar_id, $name ) {
	if ( $rvar_id ) {
		return " id='" . esc_attr( $rvar_id ) . "'";
	}

	return " id='$name'";
}
function wdmPrintOutput( $rvar_echo, $output ) {
	if ( $rvar_echo ) {
		echo $output;
	}
}

/**
 * @since 2.1
 * Get LearnDash content's parent course.
 */
function wdmir_get_ld_parent( $post_id ) {
	$post = get_post( $post_id );

	if ( empty( $post ) ) {
		return;
	}

	$parent_course_id = 0;

	$post_type = $post->post_type;

	switch ( $post_type ) {
		case 'sfwd-certificates':
			// Get all quizzes
			$quizzes = get_posts(
				array(
					'post_type'      => 'sfwd-quiz',
					'posts_per_page' => -1,
				)
			);

			foreach ( $quizzes as $quiz ) {
				$sfwd_quiz = get_post_meta( $quiz->ID, '_sfwd-quiz', true );

				if ( isset( $sfwd_quiz['sfwd-quiz_certificate'] ) && $sfwd_quiz['sfwd-quiz_certificate'] == $post_id ) {
					if ( isset( $sfwd_quiz['sfwd-quiz_certificate'] ) ) {
						$parent_course_id = $sfwd_quiz['sfwd-quiz_course'];
					} else {
						$parent_course_id = get_post_meta( $quiz->ID, 'course_id' );
					}

					break;
				}
			}

			break;

		case 'sfwd-lessons':
		case 'sfwd-quiz':
		case 'sfwd-topic':
			$parent_course_id = get_post_meta( $post_id, 'course_id', true );
			break;

		case 'sfwd-courses':
			$parent_course_id = $post_id;
			break;

		default:
			$parent_course_id = apply_filters( 'wdmir_parent_post_id', $post_id );
			break;
	}

	return $parent_course_id;
}

/**
 * @since 2.1
 * Description: To check if post is pending approval.
 *
 * @param $post_id int post ID of a post
 *
 * @return array/false string/boolean array of data if post has pending approval.
 */
function wdmir_am_i_pending_post( $post_id ) {
	if ( empty( $post_id ) ) {
		return false;
	}

	$parent_course_id = wdmir_get_ld_parent( $post_id );

	if ( empty( $parent_course_id ) ) {
		return false;
	}

	$approval_data = wdmir_get_approval_meta( $parent_course_id );

	if ( isset( $approval_data[ $post_id ] ) && 'pending' == $approval_data[ $post_id ]['status'] ) {
		return $approval_data[ $post_id ];
	}

	return false;
}

/**
 * @since 2.1
 * Description: To get approval meta of a course
 *
 * @param $course_id int post ID of a course
 *
 * @return array/false string/boolean array of data.
 */
function wdmir_get_approval_meta( $course_id ) {
	$approval_data = get_post_meta( $course_id, '_wdmir_approval', true );

	if ( empty( $approval_data ) ) {
		$approval_data = array();
	}

	return $approval_data;
}

/**
 * @since 2.1
 * Description: To set approval meta of a course
 *
 * @param $course_id int post ID of a course
 * @param $approval_data array approbval meta data of a course
 */
function wdmir_set_approval_meta( $course_id, $approval_data ) {
	update_post_meta( $course_id, '_wdmir_approval', $approval_data );
}

/**
 * @since 2.1
 * Description: To recheck and update course approval data.
 *
 * @param $course_id int post ID of a course
 *
 * @return $approval_data array updated new approval data.
 */
function wdmir_update_approval_data( $course_id ) {
	 $approval_data = wdmir_get_approval_meta( $course_id );

	if ( ! empty( $approval_data ) ) {
		foreach ( $approval_data as $content_id => $content_meta ) {
			$content_meta     = $content_meta;
			$parent_course_id = wdmir_get_ld_parent( $content_id );

			if ( $parent_course_id != $course_id ) {
				unset( $approval_data[ $content_id ] );
			}
		}

		wdmir_set_approval_meta( $course_id, $approval_data );
	}

	return $approval_data;
}

/**
 * @since 2.1
 * Description: To check if parent post's content has pending approval.
 *
 * @param $course_id int post ID of a course
 *
 * @return true/false boolean true if course has pending approval.
 */
function wdmir_is_parent_course_pending( $course_id ) {
	 $approval_data = wdmir_get_approval_meta( $course_id );

	if ( empty( $approval_data ) ) {
		return false;
	}

	foreach ( $approval_data as $content_meta ) {
		// If pending content found.
		if ( 'pending' == $content_meta['status'] ) {
			return true;
		}
	}
}

/**
 * @since 2.1
 * Description: To send an email using wp_mail() function
 *
 * @return bool value of wp_mail function.
 */
function wdmir_wp_mail( $touser, $subject, $message, $headers = array(), $attachments = array() ) {
	if ( ! empty( $touser ) ) {
		return wp_mail( $touser, $subject, $message, $headers, $attachments );
	}

	return false;
}

/**
 * @since 2.1
 * Description: To set mail content type to HTML
 *
 * @return string content format for mails.
 */
function wdmir_html_mail() {
	return 'text/html';
}

/**
 * @since 2.1
 * Description: To replace shortcodes in the template for the post.
 *
 * @param $post_id int post ID of a post
 * @param $template string template to replace words
 *
 * @return $template string template by replacing words
 */
function wdmir_post_shortcodes( $post_id, $template, $is_course_content = false ) {
	if ( empty( $template ) || empty( $post_id ) ) {
		return $template;
	}
	$post = get_post( $post_id );

	if ( empty( $post ) ) {
		return $template;
	}

	$post_author_id = $post->post_author;

	$author_login_name = get_the_author_meta( 'user_login', $post_author_id );

	if ( $is_course_content ) {
		$find = array(
			'[course_content_title]',
			'[course_content_edit]',
			'[content_update_datetime]',
			'[approved_datetime]',
			'[content_permalink]',
		);

		$replace = array(
			$post->post_title, // [course_content_title]
			admin_url( 'post.php?post=' . $post_id . '&action=edit' ), // [course_content_edit]
			$post->post_modified, // [content_update_datetime]
			$post->post_modified, // [approved_datetime]
			get_permalink( $post_id ), // [content_permalink]
		);

		$replace = apply_filters( 'wdmir_content_template_filter', $replace, $find );
	} else {
		$find = array(
			'[post_id]',
			'[course_id]',
			'[product_id]',
			'[download_id]', // v3.0.0
			'[post_title]',
			'[course_title]',
			'[download_title]', // v3.0.0
			'[product_title]',
			'[post_author]',
			'[course_permalink]',
			'[product_permalink]',
			'[download_permalink]', // v3.0.0
			'[course_update_datetime]',
			'[product_update_datetime]',
			'[download_update_datetime]', // v3.0.0
			'[ins_profile_link]',
		);

		$replace = array(
			$post_id, // [post_id]
			$post_id, // [course_id]
			$post_id, // [product_id]
			$post_id, // [download_id]
			$post->post_title, // [post_title]
			$post->post_title, // [course_title]
			$post->post_title, // [download_title]
			$post->post_title, // [product_title]
			$author_login_name, // [post_author]
			get_permalink( $post_id ), // [post_permalink]
			get_permalink( $post_id ), // [product_permalink]
			get_permalink( $post_id ), // [download_permalink]
			$post->post_modified, // [course_update_datetime]
			$post->post_modified, // [product_update_datetime]
			$post->post_modified, // [download_update_datetime]
		// get_edit_user_link( $post_author_id ), // [ins_profile_link]
			admin_url( 'user-edit.php?user_id=' . $post_author_id ), // [ins_profile_link]
		);

		$replace = apply_filters( 'wdmir_course_template_filter', $replace, $find );
	}

	$template = str_replace( $find, $replace, $template );

	$template = wdmir_user_shortcodes( $post_author_id, $template );

	return $template;
}

/**
 * @since 2.1
 * Description: To replace shortcodes in the template for the User.
 *
 * @param $user_id int user ID.
 * @param $template string template to replace words
 *
 * @return $template string template by replacing words
 */
function wdmir_user_shortcodes( $user_id, $template ) {
	if ( empty( $template ) || empty( $user_id ) ) {
		return $template;
	}

	$userdata = get_userdata( $user_id );

	$find = array(
		'[ins_first_name]',
		'[ins_last_name]',
		'[ins_login]',
		'[ins_profile_link]',
	);

	$replace = array(
		$userdata->first_name, // [ins_first_name]
		$userdata->last_name, // [ins_last_name]
		$userdata->user_login, // [ins_login]
		// get_edit_user_link( $user_id ), // [ins_profile_link]
		admin_url( 'user-edit.php?user_id=' . $user_id ),  // [ins_profile_link]
	);

	$replace = apply_filters( 'wdmir_user_template_filter', $replace, $find );

	$template = str_replace( $find, $replace, $template );

	return $template;
}


/**
 * For checking woocommerce dependency
 *
 * @return boolean returns true if plugin is active
 */
function wdmCheckWooDependency() {
	if ( is_multisite() ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		if ( class_exists( 'Learndash_WooCommerce' ) || class_exists( 'learndash_woocommerce' ) ) {
			// in the network.
			return true;
		}
		return false;
	} elseif ( ! class_exists( 'Learndash_WooCommerce' ) || ! class_exists( 'learndash_woocommerce' ) ) {
		return false;
	}
	return true;
}



/**
 * For checking EDD dependency
 *
 * @return boolean returns true if plugin is active
 */
function wdmCheckEDDDependency() {
	if ( is_multisite() ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		if ( class_exists( 'LearnDash_EDD' ) ) {
			// in the network
			return true;
		}
		return false;
	} elseif ( ! class_exists( 'LearnDash_EDD' ) ) {
		return false;
	}
	return true;
}

if ( ! function_exists( 'ir_admin_settings_check' ) ) {
	/**
	 * Get IR admin settings
	 *
	 * @param string $key    IR admin option key whose value is to be fetched.
	 *
	 * @return mixed         Returns admin option value if found, else false.
	 */
	function ir_admin_settings_check( $key ) {
		$ir_admin_settings = get_option( '_wdmir_admin_settings', false );

		if ( empty( $ir_admin_settings ) ) {
			return false;
		}

		if ( array_key_exists( $key, $ir_admin_settings ) ) {
			return $ir_admin_settings[ $key ];
		}

		return false;
	}
}

if ( ! function_exists( 'ir_get_instructors' ) ) {
	/**
	 * Get instructors.
	 *
	 * @param array $atts   Array of Attributes.
	 *
	 * @return array        Array of instructors.
	 */
	function ir_get_instructors( $atts = array() ) {
		// WP_User_Query arguments.
		$args = array(
			'role'    => 'wdm_instructor',
			'order'   => 'ASC',
			'orderby' => 'display_name',
			'fields'  => array( 'ID', 'user_login', 'display_name' ),
			'exclude' => '',
		);

		$args = shortcode_atts( $args, $atts );

		// Fetch Instructors.
		$user_query = new WP_User_Query( $args );

		return $user_query->results;
	}
}
if ( ! function_exists( 'ir_get_users_with_course_access' ) ) {
	/**
	 * Get users who have access to a course
	 *
	 * Note : This function excludes users who directly have access for free courses but
	 *        does include them if any progress is made or if they are explicitly enrolled.
	 *
	 * @param int   $course_id    ID of the course.
	 * @param array $sources    Sources to check for course access.
	 */
	function ir_get_users_with_course_access( $course_id, $sources ) {
		global $wpdb;
		$users = array();

		// Check if empty course id.
		if ( empty( $course_id ) ) {
			return $users;
		}

		$course = get_post( $course_id );

		// Check for empty course post.
		if ( empty( $course ) ) {
			return $users;
		}

		// Check if course post type.
		if ( 'sfwd-courses' != $course->post_type ) {
			return $users;
		}

		// 1. Get Direct course access users.
		if ( in_array( 'direct', $sources ) ) {
			$table    = $wpdb->usermeta;
			$meta_key = 'course_' . $course_id . '_access_from';
			$sql      = $wpdb->prepare( "SELECT user_id FROM $table WHERE meta_key = %s", $meta_key );

			$result = $wpdb->get_col( $sql, 0 );

			if ( ! empty( $result ) ) {
				$users = array_merge( $users, $result );
			}
		}

		// 2. Access to course from groups
		if ( in_array( 'group', $sources ) ) {
			$table    = $wpdb->postmeta;
			$meta_key = 'learndash_group_enrolled_' . '%';
			$sql      = $wpdb->remove_placeholder_escape(
				$wpdb->prepare(
					"SELECT meta_key FROM $table WHERE post_id = %d AND meta_key LIKE %s",
					$course_id,
					$meta_key
				)
			);

			$result = $wpdb->get_col( $sql, 0 );

			if ( ! empty( $result ) ) {
				$course_groups = array();

				$table = $wpdb->usermeta;

				foreach ( $result as $group ) {
					$group_id = intval( filter_var( $group, FILTER_SANITIZE_NUMBER_INT ) );
					if ( ! $group_id ) {
						continue;
					}
					$meta_key    = 'learndash_group_users_' . $group_id;
					$sql         = $wpdb->prepare( "SELECT user_id FROM $table WHERE meta_key = %s", $meta_key );
					$group_users = $wpdb->get_col( $sql, 0 );
					if ( empty( $group_users ) ) {
						continue;
					}
					$users = array_merge( $users, $group_users );
				}
			}
		}

		// 3. Course access list users
		if ( in_array( 'direct', $sources ) ) {
			$course_access_list = learndash_get_course_meta_setting( $course_id, 'course_access_list' );
			$users              = array_merge( $users, $course_access_list );
		}

		$users = array_unique( $users );

		return apply_filters( 'ir_filter_course_access_users', $users, $course_id, $sources );
	}
}

if ( ! function_exists( 'ir_refresh_shared_course_details' ) ) {
	/**
	 * Refresh shared course details for the current instructor.
	 *
	 * @param int $user_id  ID of the user.
	 *
	 * @since   3.3.0
	 */
	function ir_refresh_shared_course_details( $user_id = 0 ) {
		$refreshed_courses = array();

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$shared_courses_list = get_user_meta( $user_id, 'ir_shared_courses', 1 );

		if ( empty( $shared_courses_list ) ) {
			return false;
		}

		$shared_courses = explode( ',', $shared_courses_list );

		foreach ( $shared_courses as $course_id ) {
			$course_status = get_post_status( $course_id );
			// Remove trashed posts.
			if ( 'trash' == $course_status || empty( $course_status ) ) {
				continue;
			}

			// Get course instructors.
			$course_instructors_list = get_post_meta( $course_id, 'ir_shared_instructor_ids', 1 );
			$course_instructors      = explode( ',', $course_instructors_list );

			// Remove if not is course instructor list.
			if ( ! in_array( $user_id, $course_instructors ) ) {
				continue;
			}

			// Check if not owned course.
			if ( $user_id == get_post_field( 'post_author', $course_id ) ) {
				continue;
			}

			// Add verfied shared instructor.
			array_push( $refreshed_courses, $course_id );
		}

		// Check if refreshed and original list same.
		if ( ! empty( array_diff( $shared_courses, $refreshed_courses ) ) || ( empty( $refreshed_courses ) && ! empty( $shared_courses ) ) ) {
			$refreshed_list = implode( ',', $refreshed_courses );
			update_user_meta( $user_id, 'ir_shared_courses', $refreshed_list );
		}
	}
}

if ( ! function_exists( 'ir_get_instructor_shared_course_list' ) ) {
	/**
	 * Get shared course list for a instructor
	 *
	 * @param int $user_id  ID of the user.
	 *
	 * @since   3.3.0
	 */
	function ir_get_instructor_shared_course_list( $user_id = 0 ) {
		$shared_courses = array();

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		// Check if instructor or admin.
		if ( ! wdm_is_instructor( $user_id ) && ! current_user_can( 'manage_options' ) ) {
			return $shared_courses;
		}

		// Get shared courses.
		$shared_courses_list = get_user_meta( $user_id, 'ir_shared_courses', 1 );

		if ( ! empty( $shared_courses_list ) ) {
			$shared_courses = explode( ',', $shared_courses_list );
		}

		return $shared_courses;
	}
}

if ( ! function_exists( 'ir_get_instructor_owned_course_list' ) ) {
	/**
	 * Get owned course list for an instructor
	 *
	 * @since   3.3.0
	 */
	function ir_get_instructor_owned_course_list( $user_id = 0 ) {
		$owned_courses = array();

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$owned_courses = get_posts(
			array(
				'post_type'   => 'sfwd-courses',
				'author'      => $user_id,
				'fields'      => 'ids',
				'numberposts' => -1,
			)
		);

		return $owned_courses;
	}
}

if ( ! function_exists( 'ir_get_instructor_complete_course_list' ) ) {
	/**
	 * Get shared and owned course list for an instructor
	 *
	 * @param int $user_id  ID of the user.
	 *
	 * @since   3.3.0
	 */
	function ir_get_instructor_complete_course_list( $user_id = 0 ) {
		$instructor_courses = array();

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$owned_courses  = ir_get_instructor_owned_course_list( $user_id );
		$shared_courses = ir_get_instructor_shared_course_list( $user_id );

		$instructor_courses = array_merge( $owned_courses, $shared_courses );

		return $instructor_courses;
	}
}

/**
 * Get templates passing attributes and including the file.
 *
 * @param string $template_path Template path.
 * @param array  $args          Arguments. (default: array).
 * @param bool   $return        Whether to return the result or not. (default: false).
 */
function ir_get_template( $template_path, $args = array(), $return = false ) {
	// Check if template exists.
	if ( empty( $template_path ) ) {
		return '';
	}

	/**
	 * Allow 3rd party plugins to filter template arguments
	 *
	 * @since 3.5.0
	 *
	 * @param array  $args              Template arguments for the current template.
	 * @param string $template_path     Path of the current template.
	 */
	$args = apply_filters( 'ir_filter_template_args', $args, $template_path );

	// Check if arguments set.
	if ( ! empty( $args ) && is_array( $args ) ) {
        extract($args); // @codingStandardsIgnoreLine
	}

	/**
	 * Allow 3rd party plugins to filter template path.
	 *
	 * @since 3.4.0
	 *
	 * @param string $template_path     Path for the current template.
	 * @param array  $args              Template arguments for current template.
	 */
	$template_path = apply_filters( 'ir_filter_template_path', $template_path, $args );

	// Whether to capture contents in output buffer.
	if ( $return ) {
		ob_start();
	}

	/**
	 * Allow 3rd party plugins to perform actions before a template is rendered.
	 *
	 * @since 3.4.0
	 *
	 * @param array     $args           Template arguments for current template.
	 * @param string    $template_path  Path for the current template.
	 */
	do_action( 'ir_action_before_template', $args, $template_path );

	include $template_path;

	/**
	 * Allow 3rd party plugins to perform actions after a template is rendered.
	 *
	 * @since 3.4.0
	 *
	 * @param array     $args           Template arguments for current template.
	 * @param string    $template_path  Path for the current template.
	 */
	do_action( 'ir_action_after_template', $args, $template_path );

	// Return buffered contents.
	if ( $return ) {
		$contents = ob_get_clean();

		/**
		 * Allow 3rd party plugins to filter returned contents.
		 *
		 * @since 3.4.0
		 *
		 * @param string $contents      HTML contents for the rendered template.
		 * @param array  $args          Template arguments for the current template.
		 */
		return apply_filters( 'ir_filter_get_template_contents', $contents, $args );
	}
}

/**
 * Get date in site timezone
 *
 * @param string $timestring    Valid date time string identified by strtotime.
 * @return string               Date in site timezone.
 *
 * @since 3.4.0
 */
function ir_get_date_in_site_timezone( $timestring ) {
	// Get timestamp from the timestring.
	$timestamp = strtotime( $timestring );

	// Fetch site timezone.
	/**
	 * Filter the timezone for the returned date
	 *
	 * @since 3.4.0
	 *
	 * @param string $site_timezone     Site timezone
	 */
	$site_timezone = apply_filters( 'ir_filter_date_in_site_timezone_timezone', get_option( 'timezone_string' ) );

	// If not set, default to UTC timezone.
	if ( empty( $site_timezone ) ) {
		$site_timezone = 'UTC';
	}

	$date_format = get_option( 'date_format' );
	$time_format = get_option( 'time_format' );
	$format      = $date_format . ' - ' . $time_format;

	// If empty format, set default format.
	if ( empty( $format ) ) {
		$format = 'l jS \of F Y h:i:s A - T';
	}

	// Set return date format.
	/**
	 * Filter the datetime format for the returned date
	 *
	 * @since 3.4.0
	 *
	 * @param string $format        Valid PHP datetime format.
	 * @param string $timestamp     Unix timestamp of the date.
	 */
	$format = apply_filters( 'ir_filter_date_in_site_timezone_format', $format, $timestamp );

	$date = new DateTime();
	$date->setTimezone( new DateTimeZone( $site_timezone ) );
	$date->setTimestamp( $timestamp );
	$converted_date_string = $date->format( $format );

	/**
	 * Filter the date string to be returned.
	 *
	 * @since 3.4.0
	 *
	 * @param string $converted_date_string     Converted date string to be returned.
	 * @param object $date                      DateTime object of the returned date.
	 */
	return apply_filters( 'ir_filter_date_in_site_timezone', $converted_date_string, $date );
}

/**
 * Get instructor profile designation
 *
 * @param object $userdata  WP User data.
 * @return string
 *
 * @since 3.5.0
 */
function ir_get_profile_designation( $userdata ) {
	$designation = '';

	$role = get_role( $userdata->roles[0] );
	switch ( $role->name ) {
		case 'wdm_instructor':
			$designation = __( 'Instructor', 'wdm_instructor_role' );
			break;

		case 'administrator':
			$designation = __( 'Administrator', 'wdm_instructor_role' );
			break;

		case 'editor':
			$designation = __( 'Editor', 'wdm_instructor_role' );
			break;

		case 'subscriber':
			$designation = __( 'Subscriber', 'wdm_instructor_role' );
			break;
	}

	return apply_filters( 'ir_filter_profile_designation', $designation, $userdata );
}

/**
 * Get list of active core modules for the plugin
 *
 * @return array
 *
 * @since 3.5.0
 */
function ir_get_active_core_modules() {
	if ( ! defined( 'IR_CORE_MODULES_META_KEY' ) ) {
		return array();
	}
	return get_option( IR_CORE_MODULES_META_KEY );
}

/**
 * Disable core instructor role modules
 *
 * @param mixed $target_modules    One or more instructor core modules to be disabled in array.
 * @return bool                    True if successfully disabled, else false.
 *
 * @since 3.5.0
 */
function ir_disable_core_modules( $target_modules ) {
	if ( empty( $target_modules ) ) {
		return false;
	}

	if ( ! is_array( $target_modules ) ) {
		$target_modules = array( $target_modules );
	}

	$active_modules = ir_get_active_core_modules();
	$modules        = $active_modules;
	foreach ( $target_modules as $disable_module ) {
		$module_key = array_search( $disable_module, $modules );
		if ( false !== $module_key ) {
			unset( $active_modules[ $module_key ] );
		}
	}
	if ( count( $modules ) != count( $active_modules ) ) {
		update_option( IR_CORE_MODULES_META_KEY, array_values( $active_modules ) );
	}
	return true;
}

/**
 * Enable core instructor role modules
 *
 * @param mixed $target_modules    One or more instructor core modules to be enabled in array.
 * @return bool                    True if successfully enabled, else false.
 *
 * @since 3.5.0
 */
function ir_enable_core_modules( $target_modules ) {
	if ( empty( $target_modules ) ) {
		return false;
	}

	if ( ! is_array( $target_modules ) ) {
		$target_modules = array( $target_modules );
	}

	$active_modules = ir_get_active_core_modules();
	$modules        = $active_modules;
	foreach ( $target_modules as $enable_module ) {
		if ( ! in_array( $enable_module, $modules ) ) {
			$active_modules[] = $enable_module;
		}
	}
	if ( count( $modules ) != count( $active_modules ) ) {
		update_option( IR_CORE_MODULES_META_KEY, array_values( $active_modules ) );
	}
	return true;
}
