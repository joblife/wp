<?php
/**
 * LearnDash V2 REST API Groups Post Controller.
 *
 * @package LearnDash
 * @subpackage REST_API
 * @since 3.3.0
 */

/**
 * This Controller class is used to GET/UPDATE/DELETE the LearnDash
 * custom post type Groups (groups).
 *
 * This class extends the LD_REST_Posts_Controller_V2 class.
 *
 * @since 3.3.0
 */
if ( ( ! class_exists( 'LD_REST_Groups_Controller_V2' ) ) && ( class_exists( 'LD_REST_Posts_Controller_V2' ) ) ) {
	/**
	 * Class REST API Courses Post Controller.
	 */
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
	class LD_REST_Groups_Controller_V2 extends LD_REST_Posts_Controller_V2 {

		/**
		 * Public constructor for class
		 */
		public function __construct( $post_type = '' ) {
			if ( empty( $post_type ) ) {
				$post_type = learndash_get_post_type_slug( 'group' );
			}
			$this->post_type = $post_type;
			$this->metaboxes = array();

			parent::__construct( $this->post_type );

			/**
			 * Set the rest_base after the parent __constructor
			 * as it will set these var with WP specific details.
			 */
			$this->rest_base = $this->get_rest_base( 'groups' );
		}

		/**
		 * Registers the routes for the objects of the controller.
		 *
		 * @since 3.3.0
		 *
		 * @see register_rest_route() in WordPress core.
		 */
		public function register_routes() {
			// Register all the default routes first.
			parent::register_routes();

			include LEARNDASH_REST_API_DIR . '/' . $this->version . '/class-ld-rest-groups-courses-controller.php';
			$this->sub_controllers['LD_REST_Groups_Courses_Controller_V2'] = new LD_REST_Groups_Courses_Controller_V2();
			$this->sub_controllers['LD_REST_Groups_Courses_Controller_V2']->register_routes();

			include LEARNDASH_REST_API_DIR . '/' . $this->version . '/class-ld-rest-groups-leaders-controller.php';
			$this->sub_controllers['LD_REST_Groups_Leaders_Controller_V2'] = new LD_REST_Groups_Leaders_Controller_V2();
			$this->sub_controllers['LD_REST_Groups_Leaders_Controller_V2']->register_routes();

			include LEARNDASH_REST_API_DIR . '/' . $this->version . '/class-ld-rest-groups-users-controller.php';
			$this->sub_controllers['LD_REST_Groups_Users_Controller_V2'] = new LD_REST_Groups_Users_Controller_V2();
			$this->sub_controllers['LD_REST_Groups_Users_Controller_V2']->register_routes();
		}

		/**
		 * Prepare the LearnDash Post Type Settings.
		 *
		 * @since 3.3.0
		 */
		protected function register_fields() {
			$this->register_fields_metabox();

			do_action( 'learndash_rest_register_fields', $this->post_type, $this );
		}

		/**
		 * Register the Settings Fields from the Post Metaboxes.
		 *
		 * @since 3.3.0
		 */
		protected function register_fields_metabox() {
			require_once LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-group-display-content.php';
			$this->metaboxes['LearnDash_Settings_Metabox_Group_Display_Content'] = LearnDash_Settings_Metabox_Group_Display_Content::add_metabox_instance();

			require_once LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-group-access-settings.php';
			$this->metaboxes['LearnDash_Settings_Metabox_Group_Access_Settings'] = LearnDash_Settings_Metabox_Group_Access_Settings::add_metabox_instance();

			//$this->metaboxes = apply_filters( 'learndash_post_settings_metaboxes_init_' . $this->post_type, $this->metaboxes );

			if ( ! empty( $this->metaboxes ) ) {
				foreach ( $this->metaboxes as $metabox ) {
					$metabox->load_settings_values();
					$metabox->load_settings_fields();
					$this->register_rest_fields( $metabox->get_settings_metabox_fields( $metabox ) );
				}
			}
		}

		/**
		 * Permissions check for getting groups.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return true|WP_Error True if the request has read access, otherwise WP_Error object.
		 */
		public function get_items_permissions_check( $request ) {
			if ( ( 'yes' === LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Groups_CPT', 'public' ) ) || ( learndash_is_admin_user() ) || ( learndash_is_group_leader_user() ) ) {
				return true;
			}
		}

		/**
		 * Checks if a given request has access to read a post.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return bool|WP_Error True if the request has read access for the item, WP_Error object otherwise.
		 */
		public function get_item_permissions_check( $request ) {
			if ( ( 'yes' === LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Groups_CPT', 'public' ) ) || ( learndash_is_admin_user() ) || ( learndash_is_group_leader_user() ) ) {
				return true;
			}
		}

		/**
		 * Filters Groups request query arguments.
		 *
		 * @since 3.3.0
		 *
		 * @param array           $query_args    Key value array of query var to query value.
		 * @param WP_REST_Request $request The request used.
		 *
		 * @return array Key value array of query var to query value.
		 */
		public function rest_query_filter( $query_args, $request ) {
			if ( learndash_is_group_leader_user() ) {
				$group_ids = learndash_get_administrators_group_ids( get_current_user_id() );
				if ( ! empty( $group_ids ) ) {
					$query_args['post__in'] = $group_ids;
				} else {
					$query_args['post__in'] = array( 0 );
				}
			}

			return $query_args;
		}

		/**
		 * Override the REST response links. This is needed when Course Shared Steps is enabled.
		 *
		 * @since 3.3.0
		 *
		 * @param object $response WP_REST_Response instance.
		 * @param object $post     WP_Post instance.
		 * @param object $request  WP_REST_Request instance.
		 */
		public function rest_prepare_response_filter( WP_REST_Response $response, WP_Post $post, WP_REST_Request $request ) {
			if ( $this->post_type === $post->post_type ) {
				$base = sprintf( '/%s/%s', $this->namespace, $this->rest_base );

				$current_links = $response->get_links();

				if ( ! isset( $current_links['users'] ) ) {
					$links['users'] = array(
						'href'       => rest_url( trailingslashit( $base ) . $post->ID ) . '/' . $this->get_rest_base( 'groups-users' ),
						'embeddable' => true,
					);
				}

				if ( ! isset( $current_links['leaders'] ) ) {
					$links['leaders'] = array(
						'href'       => rest_url( trailingslashit( $base ) . $post->ID ) . '/' . $this->get_rest_base( 'groups-leaders' ),
						'embeddable' => true,
					);
				}

				if ( ! isset( $current_links['courses'] ) ) {
					$links['courses'] = array(
						'href'       => rest_url( trailingslashit( $base ) . $post->ID ) . '/' . $this->get_rest_base( 'groups-courses' ),
						'embeddable' => true,
					);
				}

				if ( ! empty( $links ) ) {
					$response->add_links( $links );
				}
			}

			return $response;
		}

		// End of functions
	}
}
