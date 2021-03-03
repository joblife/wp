<?php
/**
 * BuddyBoss Zoom Blocks.
 *
 * @package BuddyBoss\Zoom\Blocks
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BP_Zoom_Blocks' ) ) {
	/**
	 * Class BP_Zoom_Blocks
	 */
	class BP_Zoom_Blocks {
		/**
		 * Your __construct() method will contain configuration options for
		 * your extension.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			if ( ! bbp_pro_is_license_valid() || ! bp_zoom_is_zoom_enabled() || ! bp_zoom_is_zoom_setup() ) {
				return;
			}

			$this->setup_filters();
			$this->setup_actions();
		}

		/**
		 * Setup the group zoom class filters
		 *
		 * @since 1.0.0
		 */
		private function setup_filters() {
			add_filter( 'bp_block_category_post_types', array( $this, 'bp_block_category_post_types' ) );
		}

		/**
		 * Setup actions.
		 *
		 * @since 1.0.0
		 */
		public function setup_actions() {
			add_action( 'init', array( $this, 'register_blocks' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );

			// Meeting.
			add_action( 'wp_ajax_zoom_meeting_block_add', array( $this, 'zoom_meeting_block_add' ) );
			add_action( 'wp_ajax_zoom_meeting_block_update_occurrence', array( $this, 'zoom_meeting_block_update_occurrence' ) );
			add_action( 'wp_ajax_zoom_meeting_block_delete_occurrence', array( $this, 'zoom_meeting_block_delete_occurrence' ) );
			add_action( 'wp_ajax_zoom_meeting_block_delete_meeting', array( $this, 'zoom_meeting_block_delete_meeting' ) );
			add_action( 'wp_ajax_zoom_meeting_sync', array( $this, 'zoom_meeting_sync' ) );
			add_shortcode( 'zoom_meeting', array( $this, 'render_meeting_shortcode' ) );

			// Webinar.
			if ( bp_zoom_is_zoom_webinar_enabled() ) {
				add_action( 'wp_ajax_zoom_webinar_block_add', array( $this, 'zoom_webinar_block_add' ) );
				add_action( 'wp_ajax_zoom_webinar_block_update_occurrence', array( $this, 'zoom_webinar_block_update_occurrence' ) );
				add_action( 'wp_ajax_zoom_webinar_block_delete_occurrence', array( $this, 'zoom_webinar_block_delete_occurrence' ) );
				add_action( 'wp_ajax_zoom_webinar_block_delete_webinar', array( $this, 'zoom_webinar_block_delete_webinar' ) );
				add_action( 'wp_ajax_zoom_webinar_sync', array( $this, 'zoom_webinar_sync' ) );
				add_shortcode( 'zoom_webinar', array( $this, 'render_webinar_shortcode' ) );
			}
		}

		/**
		 * Register blocks
		 *
		 * @since 1.0.0
		 */
		public function register_blocks() {
			register_block_type(
				'bp-zoom-meeting/create-meeting',
				array(
					'editor_script'   => 'bp-zoom-meeting-block-js',
					'render_callback' => array( $this, 'render_meeting_block' ),
				)
			);

			if ( bp_zoom_is_zoom_webinar_enabled() ) {
				register_block_type(
					'bp-zoom-meeting/create-webinar',
					array(
						'editor_script'   => 'bp-zoom-meeting-block-js',
						'render_callback' => array( $this, 'render_webinar_block' ),
					)
				);
			}
		}

		/**
		 * Enqueue editor scripts
		 *
		 * @since 1.0.0
		 */
		public function enqueue_editor_assets() {
			$rtl_css = is_rtl() ? '-rtl' : '';
			$min     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_style( 'bp-zoom-block-css', bp_zoom_integration_url( '/assets/css/bp-zoom-block' . $rtl_css . $min . '.css' ), array( 'wp-edit-blocks' ), bb_platform_pro()->version );

			wp_enqueue_script(
				'bp-zoom-meeting-block-js',
				bp_zoom_integration_url( '/assets/js/blocks/build/bp-zoom-meeting-block.js' ),
				array(
					'wp-block-editor',
					'wp-blocks',
					'wp-date',
					'wp-element',
					'wp-i18n',
					'wp-components',
					'wp-hooks',
				),
				bb_platform_pro()->version,
				false
			);

			$timezones          = bp_zoom_get_timezone_options();
			$timezones_val      = array();
			$wp_timezone_str    = get_option( 'timezone_string' );
			$selected_time_zone = '';

			if ( empty( $wp_timezone_str ) ) {
				$wp_timezone_str_offset = get_option( 'gmt_offset' );
			} else {
				$time                   = new DateTime( 'now', new DateTimeZone( $wp_timezone_str ) );
				$wp_timezone_str_offset = $time->getOffset() / 60 / 60;
			}

			foreach ( $timezones as $key => $timezone ) {
				$timezones_val[] = array(
					'label' => $timezone,
					'value' => $key,
				);
			}

			foreach ( $timezones as $key => $timezone ) {
				if ( $key === $wp_timezone_str ) {
					$selected_time_zone = $key;
					break;
				}

				$date            = new DateTime( 'now', new DateTimeZone( $key ) );
				$offset_in_hours = $date->getOffset() / 60 / 60;

				if ( (float) $wp_timezone_str_offset === (float) $offset_in_hours ) {
					$selected_time_zone = $key;
				}
			}

			$host_user_type = 1;
			$api_host_user  = bp_zoom_api_host_user();
			if ( ! empty( $api_host_user ) && 2 === (int) $api_host_user->type ) {
				$host_user_type = 2;
			}

			wp_localize_script(
				'bp-zoom-meeting-block-js',
				'bpZoomMeetingBlock',
				array(
					'timezones'                     => $timezones_val,
					'wp_timezone'                   => $selected_time_zone,
					'wp_date_time'                  => wp_date( 'Y-m-d\TH:i:s', strtotime( 'now' ) ),
					'default_host_id'               => bp_zoom_api_email(),
					'default_host_user'             => bp_zoom_api_host_show(),
					'default_host_user_type'        => $host_user_type,
					'bp_zoom_meeting_nonce'         => wp_create_nonce( 'bp_zoom_meeting' ),
					'bp_zoom_webinar_nonce'         => wp_create_nonce( 'bp_zoom_webinar' ),
					'delete_occurrence_confirm_str' => __( 'Are you sure you want to delete this occurrence?', 'buddyboss-pro' ),
					'webinar_enabled'               => bp_zoom_is_zoom_webinar_enabled(),
					'private_webinar'               => bp_zoom_is_zoom_hide_webinar_urls_enabled(),
				)
			);
		}

		/**
		 * Get all registred post types.
		 *
		 * @return array Array of registered post types.
		 * @since 1.0.0
		 */
		public function get_registered_post_types() {
			$post_types = get_post_types( array( 'public' => true ), 'objects' );

			$registered_post_types = array();
			if ( ! empty( $post_types ) ) {
				foreach ( $post_types as $slug => $post_type ) {

					// Ignore attachment post type.
					if ( 'attachment' === $slug ) {
						continue;
					}

					$registered_post_types[ $slug ] = $post_type->label;
				}
			}

			return $registered_post_types;
		}

		/**
		 * Register meeting block to post types.
		 *
		 * @param array $post_types Post types.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function bp_block_category_post_types( $post_types = array() ) {

			$registered_post_types = $this->get_registered_post_types();
			if ( ! empty( $registered_post_types ) ) {
				$registered_post_types = array_keys( $registered_post_types );

				$post_types = array_unique(
					array_merge(
						$post_types,
						$registered_post_types
					)
				);
			}

			return $post_types;
		}

		/**
		 * Render meeting block on front end.
		 *
		 * @param array  $attributes Block attributes.
		 * @param string $content Content of block.
		 *
		 * @return string
		 * @since 1.0.0
		 */
		public function render_meeting_block( $attributes, $content ) {
			global $bp_zoom_meeting_block;
			if ( empty( $attributes['meetingId'] ) || is_admin() ) {
				return $content;
			}

			$meeting_info = get_transient( 'bp_zoom_meeting_block_' . $attributes['meetingId'] );

			if ( empty( $meeting_info ) ) {
				$meeting_info = bp_zoom_conference()->get_meeting_info( $attributes['meetingId'] );

				if ( ! empty( $meeting_info['code'] ) && 200 === $meeting_info['code'] && ! empty( $meeting_info['response'] ) ) {
					$bp_zoom_meeting_block = $meeting_info['response'];
					set_transient( 'bp_zoom_meeting_block_' . $attributes['meetingId'], wp_json_encode( $bp_zoom_meeting_block ), MINUTE_IN_SECONDS );
				}
			} else {
				$bp_zoom_meeting_block = json_decode( $meeting_info );
			}

			ob_start();
			bp_get_template_part( 'zoom/blocks/meeting-block' );
			$content = ob_get_clean();

			$bp_zoom_meeting_block = false;

			return $content;
		}

		/**
		 * Render meeting shortcode on front end.
		 *
		 * @param array $attributes Block attributes.
		 *
		 * @return string
		 * @since 1.0.0
		 */
		public function render_meeting_shortcode( $attributes ) {
			global $bp_zoom_meeting_block;

			$args = shortcode_atts(
				array(
					'id' => false,
				),
				$attributes
			);

			if ( empty( $args['id'] ) || is_admin() ) {
				return false;
			}

			$meeting_info = get_transient( 'bp_zoom_meeting_block_' . $args['id'] );

			if ( empty( $meeting_info ) ) {
				$meeting_info = bp_zoom_conference()->get_meeting_info( $args['id'] );

				if ( ! empty( $meeting_info['code'] ) && 200 === $meeting_info['code'] && ! empty( $meeting_info['response'] ) ) {
					$bp_zoom_meeting_block = $meeting_info['response'];
					set_transient( 'bp_zoom_meeting_block_' . $args['id'], wp_json_encode( $bp_zoom_meeting_block ), MINUTE_IN_SECONDS );
				}
			} else {
				$bp_zoom_meeting_block = json_decode( $meeting_info );
			}

			ob_start();
			bp_get_template_part( 'zoom/blocks/meeting-block' );
			$content = ob_get_clean();

			$bp_zoom_meeting_block = false;

			return $content;
		}

		/**
		 * Delete occurrence of meeting.
		 *
		 * @since 1.0.4
		 */
		public function zoom_meeting_block_delete_occurrence() {
			if ( ! bp_is_post_request() ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			// Nonce check!
			if ( empty( filter_input( INPUT_POST, '_wpnonce' ) ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce' ), 'bp_zoom_meeting' ) ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			$host_id = bp_zoom_api_email();

			// check user host.
			if ( empty( $host_id ) ) {
				wp_send_json_error( array( 'error' => __( 'Please choose API Host Email in the settings and try again.', 'buddyboss-pro' ) ) );
			}

			$meeting_id    = filter_input( INPUT_POST, 'bp-zoom-meeting-zoom-id', FILTER_SANITIZE_STRING );
			$occurrence_id = filter_input( INPUT_POST, 'bp-zoom-meeting-occurrence-id', FILTER_SANITIZE_STRING );

			$meeting_deleted = bp_zoom_conference()->delete_meeting( $meeting_id, $occurrence_id );

			if ( isset( $meeting_deleted['code'] ) && 204 === $meeting_deleted['code'] ) {
				delete_transient( 'bp_zoom_meeting_block_' . $meeting_id );
				delete_transient( 'bp_zoom_meeting_invitation_' . $meeting_id );
				wp_send_json_success(
					array(
						'deleted' => true,
					)
				);
			}

			if ( isset( $meeting_deleted['code'] ) && in_array( $meeting_deleted['code'], array( 400, 404 ), true ) ) {
				$response_error = array( 'error' => $meeting_deleted['response']->message );

				if ( ! empty( $meeting_deleted['response']->errors ) ) {
					$response_error['errors'] = $meeting_deleted['response']->errors;
				}
				wp_send_json_error( $response_error );
			}

			wp_send_json_success(
				array(
					'deleted' => $meeting_deleted,
				)
			);
		}

		/**
		 * Delete meeting from block.
		 *
		 * @since 1.0.4
		 */
		public function zoom_meeting_block_delete_meeting() {
			if ( ! bp_is_post_request() ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			// Nonce check!
			if ( empty( filter_input( INPUT_POST, '_wpnonce' ) ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce' ), 'bp_zoom_meeting' ) ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			$host_id = bp_zoom_api_email();

			// check user host.
			if ( empty( $host_id ) ) {
				wp_send_json_error( array( 'error' => __( 'Please choose API Host Email in the settings and try again.', 'buddyboss-pro' ) ) );
			}

			$meeting_id = filter_input( INPUT_POST, 'bp-zoom-meeting-zoom-id', FILTER_SANITIZE_STRING );

			$meeting_deleted = bp_zoom_conference()->delete_meeting( $meeting_id );

			if ( isset( $meeting_deleted['code'] ) && 204 === $meeting_deleted['code'] ) {
				delete_transient( 'bp_zoom_meeting_block_' . $meeting_id );
				delete_transient( 'bp_zoom_meeting_invitation_' . $meeting_id );
				wp_send_json_success(
					array(
						'deleted' => true,
					)
				);
			}

			if ( isset( $meeting_deleted['code'] ) && in_array( $meeting_deleted['code'], array( 400, 404 ), true ) ) {
				$response_error = array( 'error' => $meeting_deleted['response']->message );

				if ( ! empty( $meeting_deleted['response']->errors ) ) {
					$response_error['errors'] = $meeting_deleted['response']->errors;
				}
				wp_send_json_error( $response_error );
			}

			wp_send_json_success(
				array(
					'deleted' => $meeting_deleted,
				)
			);
		}

		/**
		 * Update occurrence of meeting.
		 *
		 * @since 1.0.4
		 */
		public function zoom_meeting_block_update_occurrence() {
			if ( ! bp_is_post_request() ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			// Nonce check!
			if ( empty( filter_input( INPUT_POST, '_wpnonce' ) ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce' ), 'bp_zoom_meeting' ) ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			$host_id = bp_zoom_api_email();

			// check user host.
			if ( empty( $host_id ) ) {
				wp_send_json_error( array( 'error' => __( 'Please choose API Host Email in the settings and try again.', 'buddyboss-pro' ) ) );
			}

			$meeting_id           = filter_input( INPUT_POST, 'bp-zoom-meeting-zoom-id', FILTER_SANITIZE_STRING );
			$occurrence_id        = filter_input( INPUT_POST, 'bp-zoom-meeting-occurrence-id', FILTER_SANITIZE_STRING );
			$start_time           = filter_input( INPUT_POST, 'bp-zoom-meeting-start-time', FILTER_SANITIZE_STRING );
			$timezone             = filter_input( INPUT_POST, 'bp-zoom-meeting-timezone', FILTER_SANITIZE_STRING );
			$duration             = filter_input( INPUT_POST, 'bp-zoom-meeting-duration', FILTER_VALIDATE_INT );
			$auto_recording       = filter_input( INPUT_POST, 'bp-zoom-meeting-recording', FILTER_SANITIZE_STRING );
			$alternative_host_ids = filter_input( INPUT_POST, 'bp-zoom-meeting-alt-host-ids', FILTER_SANITIZE_STRING );
			$join_before_host     = filter_input( INPUT_POST, 'bp-zoom-meeting-join-before-host', FILTER_VALIDATE_BOOLEAN );
			$host_video           = filter_input( INPUT_POST, 'bp-zoom-meeting-host-video', FILTER_VALIDATE_BOOLEAN );
			$participants_video   = filter_input( INPUT_POST, 'bp-zoom-meeting-participants-video', FILTER_VALIDATE_BOOLEAN );
			$mute_participants    = filter_input( INPUT_POST, 'bp-zoom-meeting-mute-participants', FILTER_VALIDATE_BOOLEAN );
			$waiting_room         = filter_input( INPUT_POST, 'bp-zoom-meeting-waiting-room', FILTER_VALIDATE_BOOLEAN );
			$enforce_login        = filter_input( INPUT_POST, 'bp-zoom-meeting-authentication', FILTER_VALIDATE_BOOLEAN );

			$alternative_host_ids = str_replace( ', ', ',', $alternative_host_ids );
			$alternative_host_ids = explode( ',', $alternative_host_ids );

			if ( $duration < 15 ) {
				wp_send_json_error( array( 'error' => __( 'Please select the meeting duration to a minimum of 15 minutes.', 'buddyboss-pro' ) ) );
			}

			$start_time = new DateTime( $start_time, new DateTimeZone( $timezone ) );
			$start_time = $start_time->format( 'Y-m-d\TH:i:s' );

			$data = array(
				'meeting_id'             => $meeting_id,
				'host_id'                => $host_id,
				'start_date'             => $start_time,
				'duration'               => $duration,
				'join_before_host'       => $join_before_host,
				'host_video'             => $host_video,
				'participants_video'     => $participants_video,
				'mute_participants'      => $mute_participants,
				'waiting_room'           => $waiting_room,
				'meeting_authentication' => $enforce_login,
				'auto_recording'         => $auto_recording,
				'alternative_host_ids'   => $alternative_host_ids,
			);

			$zoom_meeting = bp_zoom_conference()->update_meeting_occurrence( $occurrence_id, $data );

			if ( ! empty( $zoom_meeting['code'] ) && in_array( $zoom_meeting['code'], array( 201, 204 ), true ) ) {
				delete_transient( 'bp_zoom_meeting_block_' . $meeting_id );
				delete_transient( 'bp_zoom_meeting_invitation_' . $meeting_id );
				wp_send_json_success();
			}

			if ( ! empty( $zoom_meeting['code'] ) && in_array( $zoom_meeting['code'], array( 300, 404, 400, 429 ), true ) ) {
				$response_error = array( 'error' => $zoom_meeting['response']->message );

				if ( ! empty( $zoom_meeting['response']->errors ) ) {
					$response_error['errors'] = $zoom_meeting['response']->errors;
				}
				wp_send_json_error( $response_error );
			}

			wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
		}

		/**
		 * Zoom meeting add in API.
		 *
		 * @since 1.0.0
		 */
		public function zoom_meeting_block_add() {
			$response_error = array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) );

			if ( ! bp_is_post_request() ) {
				wp_send_json_error( $response_error );
			}

			// Nonce check!
			if ( empty( filter_input( INPUT_POST, '_wpnonce' ) ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce' ), 'bp_zoom_meeting' ) ) {
				wp_send_json_error( $response_error );
			}

			$host_id = bp_zoom_api_email();

			// check user host.
			if ( empty( $host_id ) ) {
				wp_send_json_error( array( 'error' => __( 'Please choose API Host Email in the settings and try again.', 'buddyboss-pro' ) ) );
			}

			$auto_recording       = filter_input( INPUT_POST, 'bp-zoom-meeting-recording', FILTER_SANITIZE_STRING );
			$alternative_host_ids = filter_input( INPUT_POST, 'bp-zoom-meeting-alt-host-ids', FILTER_SANITIZE_STRING );
			$title                = filter_input( INPUT_POST, 'bp-zoom-meeting-title', FILTER_SANITIZE_STRING );
			$description          = filter_input( INPUT_POST, 'bp-zoom-meeting-description', FILTER_SANITIZE_STRING );
			$meeting_id           = filter_input( INPUT_POST, 'bp-zoom-meeting-zoom-id', FILTER_SANITIZE_STRING );
			$start_date           = filter_input( INPUT_POST, 'bp-zoom-meeting-start-date', FILTER_SANITIZE_STRING );
			$duration             = filter_input( INPUT_POST, 'bp-zoom-meeting-duration', FILTER_VALIDATE_INT );
			$timezone             = filter_input( INPUT_POST, 'bp-zoom-meeting-timezone', FILTER_SANITIZE_STRING );
			$password             = filter_input( INPUT_POST, 'bp-zoom-meeting-password', FILTER_SANITIZE_STRING );
			$approval_type        = filter_input( INPUT_POST, 'bp-zoom-meeting-registration', FILTER_VALIDATE_BOOLEAN );
			$registration_type    = filter_input( INPUT_POST, 'bp-zoom-meeting-registration-type', FILTER_VALIDATE_INT );
			$join_before_host     = filter_input( INPUT_POST, 'bp-zoom-meeting-join-before-host', FILTER_VALIDATE_BOOLEAN );
			$host_video           = filter_input( INPUT_POST, 'bp-zoom-meeting-host-video', FILTER_VALIDATE_BOOLEAN );
			$participants_video   = filter_input( INPUT_POST, 'bp-zoom-meeting-participants-video', FILTER_VALIDATE_BOOLEAN );
			$mute_participants    = filter_input( INPUT_POST, 'bp-zoom-meeting-mute-participants', FILTER_VALIDATE_BOOLEAN );
			$waiting_room         = filter_input( INPUT_POST, 'bp-zoom-meeting-waiting-room', FILTER_VALIDATE_BOOLEAN );
			$enforce_login        = filter_input( INPUT_POST, 'bp-zoom-meeting-authentication', FILTER_VALIDATE_BOOLEAN );
			$type                 = filter_input( INPUT_POST, 'bp-zoom-meeting-type', FILTER_VALIDATE_INT );
			$recurrence           = filter_input( INPUT_POST, 'bp-zoom-meeting-recurrence', FILTER_VALIDATE_INT );
			$end_time_select      = filter_input( INPUT_POST, 'bp-zoom-meeting-end-time-select', FILTER_SANITIZE_STRING );

			$alternative_host_ids = str_replace( ', ', ',', $alternative_host_ids );
			$alternative_host_ids = explode( ',', $alternative_host_ids );

			if ( $duration < 15 ) {
				wp_send_json_error( array( 'error' => __( 'Please select the meeting duration to a minimum of 15 minutes.', 'buddyboss-pro' ) ) );
			}

			$start_date         = new DateTime( $start_date, new DateTimeZone( $timezone ) );
			$start_meeting_time = $start_date->format( 'H:i:s' );
			$start_date         = $start_date->format( 'Y-m-d\TH:i:s' );

			$data = array(
				'host_id'                => $host_id,
				'start_date'             => $start_date,
				'timezone'               => $timezone,
				'duration'               => $duration,
				'password'               => $password,
				'registration'           => $approval_type,
				'join_before_host'       => $join_before_host,
				'host_video'             => $host_video,
				'participants_video'     => $participants_video,
				'mute_participants'      => $mute_participants,
				'waiting_room'           => $waiting_room,
				'meeting_authentication' => $enforce_login,
				'auto_recording'         => $auto_recording,
				'alternative_host_ids'   => $alternative_host_ids,
				'title'                  => $title,
				'description'            => $description,
			);

			$recurrence_obj = array();
			if ( 8 === $type ) {
				$recurrence_obj['type'] = $recurrence;
				$repeat_interval        = filter_input( INPUT_POST, 'bp-zoom-meeting-repeat-interval', FILTER_VALIDATE_INT );

				if ( 1 === $recurrence ) {
					if ( 90 < $repeat_interval ) {
						$repeat_interval = 90;
					}
				} elseif ( 2 === $recurrence ) {
					if ( 12 < $repeat_interval ) {
						$repeat_interval = 12;
					}

					$weekly_days                   = filter_input( INPUT_POST, 'bp-zoom-meeting-weekly-days', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
					$recurrence_obj['weekly_days'] = implode( ',', $weekly_days );
				} elseif ( 3 === $recurrence ) {
					if ( 3 < $repeat_interval ) {
						$repeat_interval = 3;
					}
					$monthly_occurs_on = filter_input( INPUT_POST, 'bp-zoom-meeting-monthly-occurs-on', FILTER_SANITIZE_STRING );

					if ( 'day' === $monthly_occurs_on ) {
						$monthly_day                   = filter_input( INPUT_POST, 'bp-zoom-meeting-monthly-day', FILTER_VALIDATE_INT );
						$recurrence_obj['monthly_day'] = $monthly_day;
					} elseif ( 'week' === $monthly_occurs_on ) {
						$monthly_week_day                   = filter_input( INPUT_POST, 'bp-zoom-meeting-monthly-week-day', FILTER_VALIDATE_INT );
						$monthly_week                       = filter_input( INPUT_POST, 'bp-zoom-meeting-monthly-week', FILTER_VALIDATE_INT );
						$recurrence_obj['monthly_week_day'] = $monthly_week_day;
						$recurrence_obj['monthly_week']     = $monthly_week;
					}
				}

				if ( 'date' === $end_time_select ) {
					$end_date_time = filter_input( INPUT_POST, 'bp-zoom-meeting-end-date-time', FILTER_SANITIZE_STRING );
					$end_date_time = new DateTime( $end_date_time, new DateTimeZone( $timezone ) );
					$end_date_time = $end_date_time->format( 'Y-m-d' );
					$end_date_time = new DateTime( $end_date_time . ' ' . $start_meeting_time, new DateTimeZone( $timezone ) );
					$end_date_time->setTimezone( new DateTimeZone( 'UTC' ) );
					$recurrence_obj['end_date_time'] = $end_date_time->format( 'Y-m-d\TH:i:s\Z' );
				} else {
					$end_times = filter_input( INPUT_POST, 'bp-zoom-meeting-end-times', FILTER_VALIDATE_INT );

					if ( 50 < $end_times ) {
						$end_times = 50;
					}
					$recurrence_obj['end_times'] = $end_times;
				}

				$recurrence_obj['repeat_interval'] = $repeat_interval;

				$data['type']              = $type;
				$data['recurrence']        = $recurrence_obj;
				$data['registration_type'] = $registration_type;
			}

			if ( ! empty( $meeting_id ) ) {
				$data['meeting_id'] = $meeting_id;
				$zoom_meeting       = bp_zoom_conference()->update_meeting( $data );
			} else {
				$zoom_meeting = bp_zoom_conference()->create_meeting( $data );
			}

			if ( ! empty( $zoom_meeting['code'] ) && in_array( $zoom_meeting['code'], array( 201, 204 ), true ) ) {
				if ( ! empty( $zoom_meeting['response'] ) && null !== $zoom_meeting['response'] ) {
					delete_transient( 'bp_zoom_meeting_block_' . $zoom_meeting['response']->id );
					delete_transient( 'bp_zoom_meeting_invitation_' . $zoom_meeting['response']->id );

					if ( ! empty( $zoom_meeting['response']->occurrences ) ) {
						foreach ( $zoom_meeting['response']->occurrences as $o_key => $occurrence ) {
							$zoom_meeting['response']->occurrences[ $o_key ]->start_time = bp_zoom_convert_date_time( $occurrence->start_time, $timezone, true );
						}
					}

					if ( ! empty( $zoom_meeting['response']->recurrence->end_date_time ) ) {
						$zoom_meeting['response']->recurrence->end_date_time = bp_zoom_convert_date_time( $zoom_meeting['response']->recurrence->end_date_time, $timezone, true );
					}

					wp_send_json_success(
						array(
							'meeting' => $zoom_meeting['response'],
						)
					);
				}

				delete_transient( 'bp_zoom_meeting_block_' . $meeting_id );
				delete_transient( 'bp_zoom_meeting_invitation_' . $meeting_id );

				$meeting_info = bp_zoom_conference()->get_meeting_info( $meeting_id );

				if ( ! empty( $meeting_info['response']->occurrences ) ) {
					foreach ( $meeting_info['response']->occurrences as $o_key => $occurrence ) {
						$meeting_info['response']->occurrences[ $o_key ]->start_time = bp_zoom_convert_date_time( $occurrence->start_time, $timezone, true );
					}
				}

				if ( ! empty( $meeting_info['response']->recurrence->end_date_time ) ) {
					$meeting_info['response']->recurrence->end_date_time = bp_zoom_convert_date_time( $meeting_info['response']->recurrence->end_date_time, $timezone, true );
				}

				wp_send_json_success(
					array(
						'meeting' => $meeting_info['response'],
					)
				);
			}

			if ( ! empty( $zoom_meeting['code'] ) && in_array( $zoom_meeting['code'], array( 300, 404, 400, 429 ), true ) ) {
				$response_error = array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) );

				if ( ! empty( $zoom_meeting['response']->message ) ) {
					$response_error = array( 'error' => $zoom_meeting['response']->message );
				}

				if ( ! empty( $zoom_meeting['response']->errors ) ) {
					$response_error['errors'] = $zoom_meeting['response']->errors;
				}
				wp_send_json_error( $response_error );
			}

			wp_send_json_error( $response_error );
		}

		/**
		 * Update meeting from block or from zoom dashboard in to the site.
		 *
		 * @since 1.0.0
		 */
		public function zoom_meeting_sync() {
			if ( ! bp_is_post_request() ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			$wp_nonce = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );

			// Nonce check!
			if ( empty( $wp_nonce ) || ! wp_verify_nonce( $wp_nonce, 'bp_zoom_meeting' ) ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			$meeting_id = filter_input( INPUT_POST, 'bp-zoom-meeting-id', FILTER_SANITIZE_STRING );

			if ( empty( $meeting_id ) ) {
				wp_send_json_error( array( 'error' => __( 'Please provide Meeting ID.', 'buddyboss-pro' ) ) );
			}

			$meeting_info = bp_zoom_conference()->get_meeting_info( $meeting_id );

			if ( ! empty( $meeting_info['code'] ) && 200 === $meeting_info['code'] && ! empty( $meeting_info['response'] ) ) {
				$host_id = $meeting_info['response']->host_id;

				$user_info = bp_zoom_conference()->get_user_info( $host_id );

				$host_name  = '';
				$host_email = '';
				if ( 200 === $user_info['code'] && ! empty( $user_info['response'] ) ) {
					if ( ! empty( $user_info['response']->first_name ) ) {
						$host_name .= $user_info['response']->first_name;
					}
					if ( ! empty( $user_info['response']->last_name ) ) {
						$host_name .= ' ' . $user_info['response']->last_name;
					}

					if ( empty( $host_name ) && ! empty( $user_info['response']->email ) ) {
						$host_name                         = $user_info['response']->email;
						$host_email                        = $user_info['response']->email;
						$meeting_info['response']->host_id = $host_email;
					}
				}

				$timezone = $meeting_info['response']->timezone;

				if ( ! empty( $meeting_info['response']->occurrences ) && ! empty( $meeting_info['response']->created_at ) ) {
					$start_time = bp_zoom_convert_date_time( $meeting_info['response']->created_at, $timezone, true );
				} else {
					$start_time = bp_zoom_convert_date_time( $meeting_info['response']->start_time, $timezone, true );
				}

				if ( ! empty( $meeting_info['response']->occurrences ) ) {
					foreach ( $meeting_info['response']->occurrences as $o_key => $occurrence ) {
						$meeting_info['response']->occurrences[ $o_key ]->start_time = bp_zoom_convert_date_time( $occurrence->start_time, $timezone, true );
					}
					foreach ( $meeting_info['response']->occurrences as $occurrence ) {
						if ( 'deleted' !== $occurrence->status ) {
							$start_time = $occurrence->start_time;
							break;
						}
					}
				}

				$meeting_info['response']->start_time = $start_time;

				if ( ! empty( $meeting_info['response']->recurrence->end_date_time ) ) {
					$meeting_info['response']->recurrence->end_date_time = bp_zoom_convert_date_time( $meeting_info['response']->recurrence->end_date_time, $timezone, true );
				}

				// Delete transients for meeting.
				delete_transient( 'bp_zoom_meeting_block_' . $meeting_id );
				delete_transient( 'bp_zoom_meeting_invitation_' . $meeting_id );

				wp_send_json_success(
					array(
						'meeting'    => $meeting_info['response'],
						'host_name'  => $host_name,
						'host_email' => $host_email,
					)
				);
			}

			if ( ! empty( $meeting_info['code'] ) && in_array( $meeting_info['code'], array( 400, 404, 429 ), true ) ) {
				wp_send_json_error( array( 'error' => $meeting_info['response']->message ) );
			}

			wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
		}

		/**
		 * Render webinar block on front end.
		 *
		 * @param array  $attributes Block attributes.
		 * @param string $content Content of block.
		 *
		 * @return string
		 * @since 1.0.9
		 */
		public function render_webinar_block( $attributes, $content ) {
			global $bp_zoom_webinar_block;
			if ( empty( $attributes['webinarId'] ) || is_admin() ) {
				return $content;
			}

			$webinar_info = get_transient( 'bp_zoom_webinar_block_' . $attributes['webinarId'] );

			if ( empty( $webinar_info ) ) {
				$webinar_info = bp_zoom_conference()->get_webinar_info( $attributes['webinarId'] );

				if ( ! empty( $webinar_info['code'] ) && 200 === $webinar_info['code'] && ! empty( $webinar_info['response'] ) ) {
					$bp_zoom_webinar_block = $webinar_info['response'];
					set_transient( 'bp_zoom_webinar_block_' . $attributes['webinarId'], wp_json_encode( $bp_zoom_webinar_block ), MINUTE_IN_SECONDS );
				}
			} else {
				$bp_zoom_webinar_block = json_decode( $webinar_info );
			}

			ob_start();
			bp_get_template_part( 'zoom/blocks/webinar-block' );
			$content = ob_get_clean();

			$bp_zoom_webinar_block = false;

			return $content;
		}

		/**
		 * Render webinar shortcode on front end.
		 *
		 * @param array $attributes Block attributes.
		 *
		 * @return string
		 * @since 1.0.9
		 */
		public function render_webinar_shortcode( $attributes ) {
			global $bp_zoom_webinar_block;

			$args = shortcode_atts(
				array(
					'id' => false,
				),
				$attributes
			);

			if ( empty( $args['id'] ) || is_admin() ) {
				return false;
			}

			$webinar_info = get_transient( 'bp_zoom_webinar_block_' . $args['id'] );

			if ( empty( $webinar_info ) ) {
				$webinar_info = bp_zoom_conference()->get_webinar_info( $args['id'] );

				if ( ! empty( $webinar_info['code'] ) && 200 === $webinar_info['code'] && ! empty( $webinar_info['response'] ) ) {
					$bp_zoom_webinar_block = $webinar_info['response'];
					set_transient( 'bp_zoom_webinar_block_' . $args['id'], wp_json_encode( $bp_zoom_webinar_block ), MINUTE_IN_SECONDS );
				}
			} else {
				$bp_zoom_webinar_block = json_decode( $webinar_info );
			}

			ob_start();
			bp_get_template_part( 'zoom/blocks/webinar-block' );
			$content = ob_get_clean();

			$bp_zoom_webinar_block = false;

			return $content;
		}

		/**
		 * Delete occurrence of webinar.
		 *
		 * @since 1.0.9
		 */
		public function zoom_webinar_block_delete_occurrence() {
			if ( ! bp_is_post_request() ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			// Nonce check!
			if ( empty( filter_input( INPUT_POST, '_wpnonce' ) ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce' ), 'bp_zoom_webinar' ) ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			$host_id = bp_zoom_api_email();

			// check user host.
			if ( empty( $host_id ) ) {
				wp_send_json_error( array( 'error' => __( 'Please choose API Host Email in the settings and try again.', 'buddyboss-pro' ) ) );
			}

			$webinar_id    = filter_input( INPUT_POST, 'bp-zoom-webinar-zoom-id', FILTER_SANITIZE_STRING );
			$occurrence_id = filter_input( INPUT_POST, 'bp-zoom-webinar-occurrence-id', FILTER_SANITIZE_STRING );

			$webinar_deleted = bp_zoom_conference()->delete_webinar( $webinar_id, $occurrence_id );

			if ( isset( $webinar_deleted['code'] ) && 204 === $webinar_deleted['code'] ) {
				delete_transient( 'bp_zoom_webinar_block_' . $webinar_id );
				wp_send_json_success(
					array(
						'deleted' => true,
					)
				);
			}

			if ( isset( $webinar_deleted['code'] ) && in_array( $webinar_deleted['code'], array( 400, 404 ), true ) ) {
				$response_error = array( 'error' => $webinar_deleted['response']->message );

				if ( ! empty( $webinar_deleted['response']->errors ) ) {
					$response_error['errors'] = $webinar_deleted['response']->errors;
				}
				wp_send_json_error( $response_error );
			}

			wp_send_json_success(
				array(
					'deleted' => $webinar_deleted,
				)
			);
		}

		/**
		 * Delete webinar from block.
		 *
		 * @since 1.0.9
		 */
		public function zoom_webinar_block_delete_webinar() {
			if ( ! bp_is_post_request() ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			// Nonce check!
			if ( empty( filter_input( INPUT_POST, '_wpnonce' ) ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce' ), 'bp_zoom_webinar' ) ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			$host_id = bp_zoom_api_email();

			// check user host.
			if ( empty( $host_id ) ) {
				wp_send_json_error( array( 'error' => __( 'Please choose API Host Email in the settings and try again.', 'buddyboss-pro' ) ) );
			}

			$webinar_id = filter_input( INPUT_POST, 'bp-zoom-webinar-zoom-id', FILTER_SANITIZE_STRING );

			$webinar_deleted = bp_zoom_conference()->delete_webinar( $webinar_id );

			if ( isset( $webinar_deleted['code'] ) && 204 === $webinar_deleted['code'] ) {
				delete_transient( 'bp_zoom_webinar_block_' . $webinar_id );
				wp_send_json_success(
					array(
						'deleted' => true,
					)
				);
			}

			if ( isset( $webinar_deleted['code'] ) && in_array( $webinar_deleted['code'], array( 400, 404 ), true ) ) {
				$response_error = array( 'error' => $webinar_deleted['response']->message );

				if ( ! empty( $webinar_deleted['response']->errors ) ) {
					$response_error['errors'] = $webinar_deleted['response']->errors;
				}
				wp_send_json_error( $response_error );
			}

			wp_send_json_success(
				array(
					'deleted' => $webinar_deleted,
				)
			);
		}

		/**
		 * Update occurrence of webinar.
		 *
		 * @since 1.0.9
		 */
		public function zoom_webinar_block_update_occurrence() {
			if ( ! bp_is_post_request() ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			// Nonce check!
			if ( empty( filter_input( INPUT_POST, '_wpnonce' ) ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce' ), 'bp_zoom_webinar' ) ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			$host_id = bp_zoom_api_email();

			// check user host.
			if ( empty( $host_id ) ) {
				wp_send_json_error( array( 'error' => __( 'Please choose API Host Email in the settings and try again.', 'buddyboss-pro' ) ) );
			}

			$webinar_id           = filter_input( INPUT_POST, 'bp-zoom-webinar-zoom-id', FILTER_SANITIZE_STRING );
			$occurrence_id        = filter_input( INPUT_POST, 'bp-zoom-webinar-occurrence-id', FILTER_SANITIZE_STRING );
			$start_time           = filter_input( INPUT_POST, 'bp-zoom-webinar-start-time', FILTER_SANITIZE_STRING );
			$timezone             = filter_input( INPUT_POST, 'bp-zoom-webinar-timezone', FILTER_SANITIZE_STRING );
			$duration             = filter_input( INPUT_POST, 'bp-zoom-webinar-duration', FILTER_VALIDATE_INT );
			$auto_recording       = filter_input( INPUT_POST, 'bp-zoom-webinar-recording', FILTER_SANITIZE_STRING );
			$alternative_host_ids = filter_input( INPUT_POST, 'bp-zoom-webinar-alt-host-ids', FILTER_SANITIZE_STRING );
			$host_video           = filter_input( INPUT_POST, 'bp-zoom-webinar-host-video', FILTER_VALIDATE_BOOLEAN );
			$panelists_video      = filter_input( INPUT_POST, 'bp-zoom-webinar-panelists-video', FILTER_VALIDATE_BOOLEAN );
			$practice_session     = filter_input( INPUT_POST, 'bp-zoom-webinar-practice-session', FILTER_VALIDATE_BOOLEAN );
			$on_demand            = filter_input( INPUT_POST, 'bp-zoom-webinar-on-demand', FILTER_VALIDATE_BOOLEAN );
			$enforce_login        = filter_input( INPUT_POST, 'bp-zoom-webinar-authentication', FILTER_VALIDATE_BOOLEAN );

			$alternative_host_ids = str_replace( ', ', ',', $alternative_host_ids );
			$alternative_host_ids = explode( ',', $alternative_host_ids );

			if ( $duration < 15 ) {
				wp_send_json_error( array( 'error' => __( 'Please select the webinar duration to a minimum of 15 minutes.', 'buddyboss-pro' ) ) );
			}

			$start_time = new DateTime( $start_time, new DateTimeZone( $timezone ) );
			$start_time = $start_time->format( 'Y-m-d\TH:i:s' );

			$data = array(
				'webinar_id'             => $webinar_id,
				'host_id'                => $host_id,
				'start_date'             => $start_time,
				'duration'               => $duration,
				'host_video'             => $host_video,
				'panelists_video'        => $panelists_video,
				'practice_session'       => $practice_session,
				'on_demand'              => $on_demand,
				'meeting_authentication' => $enforce_login,
				'auto_recording'         => $auto_recording,
				'alternative_host_ids'   => $alternative_host_ids,
			);

			$zoom_webinar = bp_zoom_conference()->update_webinar_occurrence( $occurrence_id, $data );

			if ( ! empty( $zoom_webinar['code'] ) && in_array( $zoom_webinar['code'], array( 201, 204 ), true ) ) {
				delete_transient( 'bp_zoom_webinar_block_' . $webinar_id );
				wp_send_json_success();
			}

			if ( ! empty( $zoom_webinar['code'] ) && in_array( $zoom_webinar['code'], array( 300, 404, 400, 429 ), true ) ) {
				$response_error = array( 'error' => $zoom_webinar['response']->message );

				if ( ! empty( $zoom_webinar['response']->errors ) ) {
					$response_error['errors'] = $zoom_webinar['response']->errors;
				}
				wp_send_json_error( $response_error );
			}

			wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
		}

		/**
		 * Zoom webinar add in API.
		 *
		 * @since 1.0.9
		 */
		public function zoom_webinar_block_add() {
			$response_error = array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) );

			if ( ! bp_is_post_request() ) {
				wp_send_json_error( $response_error );
			}

			// Nonce check!
			if ( empty( filter_input( INPUT_POST, '_wpnonce' ) ) || ! wp_verify_nonce( filter_input( INPUT_POST, '_wpnonce' ), 'bp_zoom_webinar' ) ) {
				wp_send_json_error( $response_error );
			}

			$host_id = bp_zoom_api_email();

			// check user host.
			if ( empty( $host_id ) ) {
				wp_send_json_error( array( 'error' => __( 'Please choose API Host Email in the settings and try again.', 'buddyboss-pro' ) ) );
			}

			$auto_recording       = filter_input( INPUT_POST, 'bp-zoom-webinar-recording', FILTER_SANITIZE_STRING );
			$alternative_host_ids = filter_input( INPUT_POST, 'bp-zoom-webinar-alt-host-ids', FILTER_SANITIZE_STRING );
			$title                = filter_input( INPUT_POST, 'bp-zoom-webinar-title', FILTER_SANITIZE_STRING );
			$description          = filter_input( INPUT_POST, 'bp-zoom-webinar-description', FILTER_SANITIZE_STRING );
			$webinar_id           = filter_input( INPUT_POST, 'bp-zoom-webinar-zoom-id', FILTER_SANITIZE_STRING );
			$start_date           = filter_input( INPUT_POST, 'bp-zoom-webinar-start-date', FILTER_SANITIZE_STRING );
			$duration             = filter_input( INPUT_POST, 'bp-zoom-webinar-duration', FILTER_VALIDATE_INT );
			$timezone             = filter_input( INPUT_POST, 'bp-zoom-webinar-timezone', FILTER_SANITIZE_STRING );
			$password             = filter_input( INPUT_POST, 'bp-zoom-webinar-password', FILTER_SANITIZE_STRING );
			$approval_type        = filter_input( INPUT_POST, 'bp-zoom-webinar-registration', FILTER_VALIDATE_BOOLEAN );
			$registration_type    = filter_input( INPUT_POST, 'bp-zoom-webinar-registration-type', FILTER_VALIDATE_INT );
			$host_video           = filter_input( INPUT_POST, 'bp-zoom-webinar-host-video', FILTER_VALIDATE_BOOLEAN );
			$panelists_video      = filter_input( INPUT_POST, 'bp-zoom-webinar-panelists-video', FILTER_VALIDATE_BOOLEAN );
			$practice_session     = filter_input( INPUT_POST, 'bp-zoom-webinar-practice-session', FILTER_VALIDATE_BOOLEAN );
			$on_demand            = filter_input( INPUT_POST, 'bp-zoom-webinar-on-demand', FILTER_VALIDATE_BOOLEAN );
			$enforce_login        = filter_input( INPUT_POST, 'bp-zoom-webinar-authentication', FILTER_VALIDATE_BOOLEAN );
			$type                 = filter_input( INPUT_POST, 'bp-zoom-webinar-type', FILTER_VALIDATE_INT );
			$recurrence           = filter_input( INPUT_POST, 'bp-zoom-webinar-recurrence', FILTER_VALIDATE_INT );
			$end_time_select      = filter_input( INPUT_POST, 'bp-zoom-webinar-end-time-select', FILTER_SANITIZE_STRING );

			$alternative_host_ids = str_replace( ', ', ',', $alternative_host_ids );
			$alternative_host_ids = explode( ',', $alternative_host_ids );

			if ( $duration < 15 ) {
				wp_send_json_error( array( 'error' => __( 'Please select the webinar duration to a minimum of 15 minutes.', 'buddyboss-pro' ) ) );
			}

			$start_date         = new DateTime( $start_date, new DateTimeZone( $timezone ) );
			$start_webinar_time = $start_date->format( 'H:i:s' );
			$start_date         = $start_date->format( 'Y-m-d\TH:i:s' );

			$data = array(
				'host_id'                => $host_id,
				'start_date'             => $start_date,
				'timezone'               => $timezone,
				'duration'               => $duration,
				'password'               => $password,
				'registration'           => $approval_type,
				'host_video'             => $host_video,
				'panelists_video'        => $panelists_video,
				'practice_session'       => $practice_session,
				'on_demand'              => $on_demand,
				'meeting_authentication' => $enforce_login,
				'auto_recording'         => $auto_recording,
				'alternative_host_ids'   => $alternative_host_ids,
				'title'                  => $title,
				'description'            => $description,
			);

			$recurrence_obj = array();
			if ( 9 === $type ) {
				$recurrence_obj['type'] = $recurrence;
				$repeat_interval        = filter_input( INPUT_POST, 'bp-zoom-webinar-repeat-interval', FILTER_VALIDATE_INT );

				if ( 1 === $recurrence ) {
					if ( 90 < $repeat_interval ) {
						$repeat_interval = 90;
					}
				} elseif ( 2 === $recurrence ) {
					if ( 12 < $repeat_interval ) {
						$repeat_interval = 12;
					}

					$weekly_days                   = filter_input( INPUT_POST, 'bp-zoom-webinar-weekly-days', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
					$recurrence_obj['weekly_days'] = implode( ',', $weekly_days );
				} elseif ( 3 === $recurrence ) {
					if ( 3 < $repeat_interval ) {
						$repeat_interval = 3;
					}
					$monthly_occurs_on = filter_input( INPUT_POST, 'bp-zoom-webinar-monthly-occurs-on', FILTER_SANITIZE_STRING );

					if ( 'day' === $monthly_occurs_on ) {
						$monthly_day                   = filter_input( INPUT_POST, 'bp-zoom-webinar-monthly-day', FILTER_VALIDATE_INT );
						$recurrence_obj['monthly_day'] = $monthly_day;
					} elseif ( 'week' === $monthly_occurs_on ) {
						$monthly_week_day                   = filter_input( INPUT_POST, 'bp-zoom-webinar-monthly-week-day', FILTER_VALIDATE_INT );
						$monthly_week                       = filter_input( INPUT_POST, 'bp-zoom-webinar-monthly-week', FILTER_VALIDATE_INT );
						$recurrence_obj['monthly_week_day'] = $monthly_week_day;
						$recurrence_obj['monthly_week']     = $monthly_week;
					}
				}

				if ( 'date' === $end_time_select ) {
					$end_date_time = filter_input( INPUT_POST, 'bp-zoom-webinar-end-date-time', FILTER_SANITIZE_STRING );
					$end_date_time = new DateTime( $end_date_time, new DateTimeZone( $timezone ) );
					$end_date_time = $end_date_time->format( 'Y-m-d' );
					$end_date_time = new DateTime( $end_date_time . ' ' . $start_webinar_time, new DateTimeZone( $timezone ) );
					$end_date_time->setTimezone( new DateTimeZone( 'UTC' ) );
					$recurrence_obj['end_date_time'] = $end_date_time->format( 'Y-m-d\TH:i:s\Z' );
				} else {
					$end_times = filter_input( INPUT_POST, 'bp-zoom-webinar-end-times', FILTER_VALIDATE_INT );

					if ( 50 < $end_times ) {
						$end_times = 50;
					}
					$recurrence_obj['end_times'] = $end_times;
				}

				$recurrence_obj['repeat_interval'] = $repeat_interval;

				$data['type']              = $type;
				$data['recurrence']        = $recurrence_obj;
				$data['registration_type'] = $registration_type;
			}

			if ( ! empty( $webinar_id ) ) {
				$data['webinar_id'] = $webinar_id;
				$zoom_webinar       = bp_zoom_conference()->update_webinar( $data );
			} else {
				$zoom_webinar = bp_zoom_conference()->create_webinar( $data );
			}

			if ( ! empty( $zoom_webinar['code'] ) && in_array( $zoom_webinar['code'], array( 201, 204 ), true ) ) {
				if ( ! empty( $zoom_webinar['response'] ) && null !== $zoom_webinar['response'] ) {
					delete_transient( 'bp_zoom_webinar_block_' . $zoom_webinar['response']->id );

					if ( ! empty( $zoom_webinar['response']->occurrences ) ) {
						foreach ( $zoom_webinar['response']->occurrences as $o_key => $occurrence ) {
							$zoom_webinar['response']->occurrences[ $o_key ]->start_time = bp_zoom_convert_date_time( $occurrence->start_time, $timezone, true );
						}
					}

					if ( ! empty( $zoom_webinar['response']->recurrence->end_date_time ) ) {
						$zoom_webinar['response']->recurrence->end_date_time = bp_zoom_convert_date_time( $zoom_webinar['response']->recurrence->end_date_time, $timezone, true );
					}

					wp_send_json_success(
						array(
							'webinar' => $zoom_webinar['response'],
						)
					);
				}

				delete_transient( 'bp_zoom_webinar_block_' . $webinar_id );

				$webinar_info = bp_zoom_conference()->get_webinar_info( $webinar_id, false, true );

				if ( ! empty( $webinar_info['response']->occurrences ) ) {
					foreach ( $webinar_info['response']->occurrences as $o_key => $occurrence ) {
						$webinar_info['response']->occurrences[ $o_key ]->start_time = bp_zoom_convert_date_time( $occurrence->start_time, $timezone, true );
					}
				}

				if ( ! empty( $webinar_info['response']->recurrence->end_date_time ) ) {
					$webinar_info['response']->recurrence->end_date_time = bp_zoom_convert_date_time( $webinar_info['response']->recurrence->end_date_time, $timezone, true );
				}

				wp_send_json_success(
					array(
						'webinar' => $webinar_info['response'],
					)
				);
			}

			if ( ! empty( $zoom_webinar['code'] ) && in_array( $zoom_webinar['code'], array( 300, 404, 400, 429 ), true ) ) {
				$response_error = array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) );

				if ( ! empty( $zoom_webinar['response']->message ) ) {
					$response_error = array( 'error' => $zoom_webinar['response']->message );
				}

				if ( ! empty( $zoom_webinar['response']->errors ) ) {
					$response_error['errors'] = $zoom_webinar['response']->errors;
				}
				wp_send_json_error( $response_error );
			}

			wp_send_json_error( $response_error );
		}

		/**
		 * Update webinar from block or from zoom dashboard in to the site.
		 *
		 * @since 1.0.9
		 */
		public function zoom_webinar_sync() {
			if ( ! bp_is_post_request() ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			$wp_nonce = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );

			// Nonce check!
			if ( empty( $wp_nonce ) || ! wp_verify_nonce( $wp_nonce, 'bp_zoom_webinar' ) ) {
				wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
			}

			$webinar_id = filter_input( INPUT_POST, 'bp-zoom-webinar-id', FILTER_SANITIZE_STRING );

			if ( empty( $webinar_id ) ) {
				wp_send_json_error( array( 'error' => __( 'Please provide Webinar ID.', 'buddyboss-pro' ) ) );
			}

			$webinar_info = bp_zoom_conference()->get_webinar_info( $webinar_id, false, true );

			if ( ! empty( $webinar_info['code'] ) && 200 === $webinar_info['code'] && ! empty( $webinar_info['response'] ) ) {
				$host_id = $webinar_info['response']->host_id;

				$user_info = bp_zoom_conference()->get_user_info( $host_id );

				$host_name  = '';
				$host_email = '';
				if ( 200 === $user_info['code'] && ! empty( $user_info['response'] ) ) {
					if ( ! empty( $user_info['response']->first_name ) ) {
						$host_name .= $user_info['response']->first_name;
					}
					if ( ! empty( $user_info['response']->last_name ) ) {
						$host_name .= ' ' . $user_info['response']->last_name;
					}

					if ( empty( $host_name ) && ! empty( $user_info['response']->email ) ) {
						$host_name                         = $user_info['response']->email;
						$host_email                        = $user_info['response']->email;
						$webinar_info['response']->host_id = $host_email;
					}
				}

				$timezone = $webinar_info['response']->timezone;

				if ( ! empty( $webinar_info['response']->occurrences ) && ! empty( $webinar_info['response']->created_at ) ) {
					$start_time = bp_zoom_convert_date_time( $webinar_info['response']->created_at, $timezone, true );
				} else {
					$start_time = bp_zoom_convert_date_time( $webinar_info['response']->start_time, $timezone, true );
				}

				if ( ! empty( $webinar_info['response']->occurrences ) ) {
					foreach ( $webinar_info['response']->occurrences as $o_key => $occurrence ) {
						$webinar_info['response']->occurrences[ $o_key ]->start_time = bp_zoom_convert_date_time( $occurrence->start_time, $timezone, true );
					}
					foreach ( $webinar_info['response']->occurrences as $occurrence ) {
						if ( 'deleted' !== $occurrence->status ) {
							$start_time = $occurrence->start_time;
							break;
						}
					}
				}

				$webinar_info['response']->start_time = $start_time;

				if ( ! empty( $webinar_info['response']->recurrence->end_date_time ) ) {
					$webinar_info['response']->recurrence->end_date_time = bp_zoom_convert_date_time( $webinar_info['response']->recurrence->end_date_time, $timezone, true );
				}

				// Delete transients for webinar.
				delete_transient( 'bp_zoom_webinar_block_' . $webinar_id );

				wp_send_json_success(
					array(
						'webinar'    => $webinar_info['response'],
						'host_name'  => $host_name,
						'host_email' => $host_email,
					)
				);
			}

			if ( ! empty( $webinar_info['code'] ) && in_array( $webinar_info['code'], array( 400, 404, 429 ), true ) ) {
				wp_send_json_error( array( 'error' => $webinar_info['response']->message ) );
			}

			wp_send_json_error( array( 'error' => __( 'Something went wrong. If passcode is entered then please make sure it matches Zoom Passcode requirements and try again.', 'buddyboss-pro' ) ) );
		}
	}
}

