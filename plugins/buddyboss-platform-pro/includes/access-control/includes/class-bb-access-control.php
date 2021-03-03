<?php
/**
 * BuddyBoss Access Control Class.
 *
 * @since   1.0.7
 * @package BuddyBossPro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup the bp access control class.
 *
 * @since 1.1.0
 */
class BB_Access_Control {

	/**
	 * Unique ID for the access control.
	 *
	 * @var string Access Control.
	 *
	 * @since 1.1.0
	 */
	public $id = 'access-control';

	/**
	 * Access Control Constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {

		// Include the code.
		$this->includes();
		$this->setup_actions();

	}

	/**
	 * Setup actions for access control.
	 *
	 * @since 1.1.0
	 */
	public function setup_actions() {

		add_action( 'bp_admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		add_action( 'bp_enqueue_scripts', array( $this, 'enqueue_script' ) );
		add_action( 'bp_admin_setting_messages_register_fields', array( $this, 'bb_admin_setting_messages_access_control_register_fields' ) );
		add_action( 'bp_admin_setting_friends_register_fields', array( $this, 'bb_admin_setting_friends_access_control_register_fields' ) );
		add_action( 'bp_admin_setting_media_register_fields', array( $this, 'bb_admin_setting_media_access_control_register_fields' ) );
		add_action( 'bp_admin_setting_groups_register_fields', array( $this, 'bb_admin_setting_groups_access_control_register_fields' ) );
		add_action( 'bp_admin_setting_activity_register_fields', array( $this, 'bb_admin_setting_activity_access_control_register_fields' ) );
		add_action( 'wp_ajax_get_access_control_level_options', array( $this, 'bb_get_access_control_level_options' ) );
		add_action( 'wp_ajax_plugin_get_access_control_level_options', array( $this, 'bb_get_plugin_access_control_level_options' ) );
		add_action( 'wp_ajax_gamipress_get_access_control_level_options', array( $this, 'bb_get_gamipress_access_control_level_options' ) );
	}

	/**
	 * Function will return the third party plugin options membership sub options based on the membership.
	 *
	 * @since 1.1.0
	 */
	public function bb_get_access_control_level_options() {

		$access_controls              = self::bb_get_access_control_lists();
		$selected_access_control_type = filter_input( INPUT_POST, 'value', FILTER_SANITIZE_STRING );
		$key                          = filter_input( INPUT_POST, 'key', FILTER_SANITIZE_STRING );
		$threaded                     = filter_input( INPUT_POST, 'threaded', FILTER_VALIDATE_BOOLEAN );
		$label                        = filter_input( INPUT_POST, 'label', FILTER_SANITIZE_STRING );
		$sub_label                    = filter_input( INPUT_POST, 'sub_label', FILTER_SANITIZE_STRING );
		$component_settings           = filter_input( INPUT_POST, 'component_settings', FILTER_REQUIRE_ARRAY );
		$html                         = '';
		$ajax                         = true;

		if ( '' !== trim( $selected_access_control_type ) ) {
			$options_lists = $access_controls[ $selected_access_control_type ]['class']::instance()->get_level_lists();

			ob_start();
			foreach ( $options_lists as $option ) {
				$default = $option['default'];
				$disable = ( $default ) ? ' disabled' : '';
				$checked = ( $default ) ? ' checked' : '';

				if ( 'disabled' === trim( $disable ) && 'checked' === trim( $checked ) ) {
					continue;
				}

				if ( $threaded ) {
					require bb_access_control_path() . 'templates/multiple-options.php';
				} else {
					require bb_access_control_path() . 'templates/single-options.php';
				}
			}

			$html = ob_get_contents();
			ob_end_clean();

		}

		// Show info message when no records found.
		if ( empty( $html ) ) {

			$no_record_label = '';
			if ( 'bp_member_type' === $selected_access_control_type ) {
				$no_record_label = 'Profile types';
			} elseif ( 'gender' === $selected_access_control_type ) {
				$no_record_label = 'Gender';
			}

			$html = sprintf( '<p class="description" >No %s found.</p>', $no_record_label );
		}

		wp_send_json_success(
			array(
				'message' => $html,
			)
		);

	}

	/**
	 * Function will return the gamipress options membership sub options based on the membership.
	 *
	 * @since 1.1.0
	 */
	public function bb_get_gamipress_access_control_level_options() {

		$access_controls              = self::bb_get_access_control_lists();
		$selected_access_control_type = filter_input( INPUT_POST, 'value', FILTER_SANITIZE_STRING );
		$key                          = filter_input( INPUT_POST, 'key', FILTER_SANITIZE_STRING );
		$html                         = '';
		$threaded                     = filter_input( INPUT_POST, 'threaded', FILTER_VALIDATE_BOOLEAN );
		$label                        = filter_input( INPUT_POST, 'label', FILTER_SANITIZE_STRING );
		$sub_label                    = filter_input( INPUT_POST, 'sub_label', FILTER_SANITIZE_STRING );
		$component_settings           = filter_input( INPUT_POST, 'component_settings', FILTER_REQUIRE_ARRAY );
		$ajax                         = true;

		if ( '' !== trim( $selected_access_control_type ) ) {
			$plugin_lists  = $access_controls['gamipress']['class']::instance()->bb_get_access_control_gamipress_lists();
			$options_lists = $plugin_lists[ $selected_access_control_type ]['class']::instance()->get_level_lists();

			ob_start();
			foreach ( $options_lists as $option ) {

				$default = $option['default'];
				$disable = ( $default ) ? ' disabled' : '';
				$checked = ( $default ) ? ' checked' : '';

				if ( 'disabled' === trim( $disable ) && 'checked' === trim( $checked ) ) {
					continue;
				}

				if ( $threaded ) {
					require bb_access_control_path() . 'templates/multiple-options.php';
				} else {
					require bb_access_control_path() . 'templates/single-options.php';
				}
			}

			$html = ob_get_contents();
			ob_end_clean();

		}

		// Show info message when no records found.
		if ( empty( $html ) ) {
			$html = sprintf( '<p class="description" >No GamiPress %ss found.</p>', ucfirst( $selected_access_control_type ) );
		}

		wp_send_json_success(
			array(
				'message' => $html,
			)
		);

	}

	/**
	 * Function will return the options membership sub options based on the membership.
	 *
	 * @since 1.1.0
	 */
	public function bb_get_plugin_access_control_level_options() {

		$access_controls              = self::bb_get_access_control_lists();
		$selected_access_control_type = filter_input( INPUT_POST, 'value', FILTER_SANITIZE_STRING );
		$key                          = filter_input( INPUT_POST, 'key', FILTER_SANITIZE_STRING );
		$html                         = '';
		$threaded                     = filter_input( INPUT_POST, 'threaded', FILTER_VALIDATE_BOOLEAN );
		$label                        = filter_input( INPUT_POST, 'label', FILTER_SANITIZE_STRING );
		$sub_label                    = filter_input( INPUT_POST, 'sub_label', FILTER_SANITIZE_STRING );
		$component_settings           = filter_input( INPUT_POST, 'component_settings', FILTER_REQUIRE_ARRAY );
		$ajax                         = true;

		if ( '' !== trim( $selected_access_control_type ) ) {
			$plugin_lists  = $access_controls['membership']['class']::instance()->bb_get_access_control_plugins_lists();
			$options_lists = $plugin_lists[ $selected_access_control_type ]['class']::instance()->get_level_lists();

			ob_start();
			if ( ! empty( $options_lists ) ) {
				foreach ( $options_lists as $option ) {
					$default = $option['default'];
					$disable = ( $default ) ? ' disabled' : '';
					$checked = ( $default ) ? ' checked' : '';

					if ( 'disabled' === trim( $disable ) && 'checked' === trim( $checked ) ) {
						continue;
					}

					if ( $threaded ) {
						require bb_access_control_path() . 'templates/multiple-options.php';
					} else {
						require bb_access_control_path() . 'templates/single-options.php';
					}
				}
			}

			$html = ob_get_contents();
			ob_end_clean();

		}

		// Show info message when no records found.
		if ( empty( $html ) ) {

			$no_record_label = 'Memberships';
			if ( 'learndash' === $selected_access_control_type ) {
				$no_record_label = 'Groups';
			}

			$html = sprintf( '<p class="description" >No %s found.</p>', $no_record_label );
		}

		wp_send_json_success(
			array(
				'message' => $html,
			)
		);

	}

	/**
	 * Enqueue admin related scripts and styles.
	 *
	 * @since 1.1.0
	 */
	public function enqueue_scripts_styles() {
		$min     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$rtl_css = is_rtl() ? '-rtl' : '';
		wp_enqueue_style( 'bb-access-control-admin', bb_access_control_url( '/assets/css/bb-access-control-admin' . $rtl_css . $min . '.css' ), array(), bb_platform_pro()->version );
		wp_enqueue_script( 'bb-access-control-admin', bb_access_control_url( '/assets/js/bb-access-control-admin' . $min . '.js' ), array(), bb_platform_pro()->version );
		wp_localize_script(
			'bb-access-control-admin',
			'bbAccessControlAdminVars',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);

	}

	/**
	 * Enqueue related scripts and styles.
	 *
	 * @since 1.1.0
	 */
	public function enqueue_script() {
		$rtl_css = is_rtl() ? '-rtl' : '';
		$min     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_style( 'bb-access-control', bb_access_control_url( '/assets/css/bb-access-control' . $rtl_css . $min . '.css' ), array(), bb_platform_pro()->version );
	}


	/**
	 * Function which return the list of access control.
	 *
	 * @return array list of the access control.
	 * @since 1.1.0
	 */
	public static function bb_get_access_control_lists() {
		$access_controls = array(
			'wp_role'        => array(
				'label'      => __( 'WordPress Role', 'buddyboss-pro' ),
				'is_enabled' => true,
				'class'      => BB_Access_Control_WordPress_Role::class,
			),
			'bp_member_type' => array(
				'label'      => __( 'Profile Type', 'buddyboss-pro' ),
				'is_enabled' => ( true === bp_member_type_enable_disable() ) ? true : false,
				'class'      => BB_Access_Control_Member_Type::class,
			),
			'gamipress'      => array(
				'label'      => __( 'GamiPress', 'buddyboss-pro' ),
				'is_enabled' => ( class_exists( 'GamiPress' ) ) ? true : false,
				'class'      => BB_Access_Control_Gamipress::class,
			),
			'membership'     => array(
				'label'      => __( 'Membership', 'buddyboss-pro' ),
				'is_enabled' => ( BB_Access_Control_Access_Control::instance()->bb_is_access_control_available() ) ? true : false,
				'class'      => BB_Access_Control_Access_Control::class,
			),
			'gender'         => array(
				'label'      => __( 'Gender', 'buddyboss-pro' ),
				'is_enabled' => ( function_exists( 'bp_get_xprofile_gender_type_field_id' ) && bp_get_xprofile_gender_type_field_id() ) ? true : false,
				'class'      => BB_Access_Control_Gender::class,
			),
		);

		return apply_filters( 'bb_get_access_control_lists', $access_controls );

	}

	/**
	 * Link to Access Control tutorial
	 *
	 * @since BuddyBoss 1.5.7
	 */
	public function bb_admin_access_control_setting_tutorial() {
		?>

		<p>
			<a class="button" href="
			<?php
			echo bp_get_admin_url( // phpcs:ignore
				add_query_arg(
					array(
						'page'    => 'bp-help',
						'article' => 121813,
					),
					'admin.php'
				)
			);
			?>
			"><?php esc_html_e( 'View Tutorial', 'buddyboss-pro' ); ?></a>
		</p>

		<?php
	}

	/**
	 * Register the settings field.
	 *
	 * @param string $setting settings field.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_setting_activity_access_control_register_fields( $setting ) {

		// Main General Settings Section.
		$setting->add_section(
			'activity_access_control_block',
			sprintf(
				'%1s <span class="require-licence">%2s</span>',
				__( 'Activity Access', 'buddyboss-pro' ),
				( ! bbp_pro_is_license_valid() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
			)
		);

		if ( bbp_pro_is_license_valid() ) {
			$args = array();
			$setting->add_field(
				bb_access_control_create_activity_key(),
				__( 'Activity Posts', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_activity_access_control_setting_callback_create_activity',
				),
				'string',
				$args
			);

			$setting->add_field( 'bb-access-control-setting-tutorial', '', array( $this, 'bb_admin_access_control_setting_tutorial' ) );

			// simply add notice.
			$setting->add_field(
				'activity_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					__( 'Note: These settings do not apply to administrators or group activity feeds.', 'buddyboss-pro' )
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				$args
			);
		} else {
			// simply add notice.
			$setting->add_field(
				'connection_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper no-access-controls" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					sprintf(
					/* translators: %1$s - Platform Pro string, %2$s - License URL */
						esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
						'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
						sprintf(
							'<a href="%s">%s</a>',
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page' => 'buddyboss-updater',
											'tab'  => 'buddyboss_theme',
										),
										'admin.php'
									)
								)
							),
							esc_html__( 'Add License key', 'buddyboss-pro' )
						)
					)
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				array()
			);
		}

	}

	/**
	 * Callback function for the create activity settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_activity_access_control_setting_callback_create_activity() {
		$label    = __( 'Select which members should have access to create activity posts, based on:', 'buddyboss-pro' );
		$settings = bb_access_control_create_activity_settings();
		self::bb_admin_print_access_control_setting( bb_access_control_create_activity_key(), bb_access_control_create_activity_key(), '', $label, $settings );
	}

	/**
	 * Register the settings field.
	 *
	 * @param string $setting settings field.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_setting_friends_access_control_register_fields( $setting ) {

		// Main General Settings Section.
		$setting->add_section(
			'connection_access_control_block',
			sprintf(
				'%1s <span class="require-licence">%2s</span>',
				__( 'Connection Access', 'buddyboss-pro' ),
				( ! bbp_pro_is_license_valid() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
			)
		);

		if ( bbp_pro_is_license_valid() ) {

			$args = array();
			$setting->add_field(
				bb_access_control_friends_key(),
				__( 'Connection Request', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_friends_access_control_setting_callback_connection',
				),
				'string',
				$args
			);

			$setting->add_field( 'bb-access-control-setting-tutorial', '', array( $this, 'bb_admin_access_control_setting_tutorial' ) );

			// simply add notice.
			$setting->add_field(
				'connection_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					__( 'Note: These settings do not apply to administrators.', 'buddyboss-pro' )
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				$args
			);

		} else {
			// simply add notice.
			$setting->add_field(
				'connection_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper no-access-controls" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					sprintf(
					/* translators: %1$s - Platform Pro string, %2$s - License URL */
						esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
						'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
						sprintf(
							'<a href="%s">%s</a>',
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page' => 'buddyboss-updater',
											'tab'  => 'buddyboss_theme',
										),
										'admin.php'
									)
								)
							),
							esc_html__( 'Add License key', 'buddyboss-pro' )
						)
					)
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				array()
			);
		}
	}

	/**
	 * Empty Callback function for the display notices only.
	 *
	 * @since 1.1.0
	 */
	public function bb_access_control_empty_callback() {
		if ( ! bbp_pro_is_license_valid() ) {
			?>
			<style type="text/css">
				#messages_access_control_block + p.submit {
					display: none;
				}
			</style>
			<?php
		}
	}

	/**
	 * Register the settings field.
	 *
	 * @param string $setting settings field.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_setting_messages_access_control_register_fields( $setting ) {

		// Main General Settings Section.
		$setting->add_section(
			'messages_access_control_block',
			sprintf(
				'%1s <span class="require-licence">%2s</span>',
				__( 'Messages Access', 'buddyboss-pro' ),
				( ! bbp_pro_is_license_valid() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
			)
		);

		if ( bbp_pro_is_license_valid() ) {
			$args = array();
			$setting->add_field(
				bb_access_control_send_message_key(),
				__( 'Send Messages', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_messages_access_control_setting_callback_send_message',
				),
				'string',
				$args
			);

			$setting->add_field( 'bb-access-control-setting-tutorial', '', array( $this, 'bb_admin_access_control_setting_tutorial' ) );

			// simply add notice.
			$setting->add_field(
				'messages_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					__( 'Note: These settings do not apply to administrators or group messages.', 'buddyboss-pro' )
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				$args
			);
		} else {
			// simply add notice.
			$setting->add_field(
				'messages_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper no-access-control" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					sprintf(
					/* translators: %1$s - Platform Pro string, %2$s - License URL */
						esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
						'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
						sprintf(
							'<a href="%s">%s</a>',
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page' => 'buddyboss-updater',
											'tab'  => 'buddyboss_theme',
										),
										'admin.php'
									)
								)
							),
							esc_html__( 'Add License key', 'buddyboss-pro' )
						)
					)
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				array()
			);
		}
	}

	/**
	 * Empty callback of blocks.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_access_control_setting_callback_empty_block() {
		?>
		<style type="text/css">
			#messages_access_control_block + p.submit {
				display: none;
			}
		</style>
		<div class="section-bb_access_control_invalid_licence_settings_section">
			<p>
				<?php
				printf(
				/* translators: %1$s - Platform Pro string, %2$s - License URL */
					esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
					'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
					sprintf(
						'<a href="%s">%s</a>',
						esc_url(
							bp_get_admin_url(
								add_query_arg(
									array(
										'page' => 'buddyboss-updater',
										'tab'  => 'buddyboss_theme',
									),
									'admin.php'
								)
							)
						),
						esc_html__( 'Add License key', 'buddyboss-pro' )
					)
				)
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Function to show the default messsage when site doesn't have valid licence.
	 *
	 * @since 1.1.0
	 */
	public function bb_access_control_invalid_licence_callback_message() {

		printf(
		/* translators: %1$s - Platform Pro string, %2$s - License URL */
			esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
			'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
			sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					bp_get_admin_url(
						add_query_arg(
							array(
								'page' => 'buddyboss-updater',
								'tab'  => 'buddyboss_theme',
							),
							'admin.php'
						)
					)
				),
				esc_html__( 'Add License key', 'buddyboss-pro' )
			)
		);

	}

	/**
	 * Callback function for the message settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_messages_access_control_setting_callback_send_message() {
		$label     = __( 'Select which members should have access to send messages to other members, based on:', 'buddyboss-pro' );
		$sub_label = __( 'Members with the {{option_value}} {{select_value}} can send messages to members with - Any Member / With Specific {{select_value}}(s)', 'buddyboss-pro' );
		$settings  = bb_access_control_send_messages_settings();
		self::bb_admin_print_access_control_setting( bb_access_control_send_message_key(), bb_access_control_send_message_key(), '', $label, $settings, true, $sub_label );
	}

	/**
	 * Callback function for the friendship settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_friends_access_control_setting_callback_connection() {
		$label     = __( 'Select which members should have access to send connection requests to other members, based on:', 'buddyboss-pro' );
		$sub_label = __( 'Members with the {{option_value}} {{select_value}} can send connection request to members with - Any Member / With Specific {{select_value}}(s)', 'buddyboss-pro' );
		$settings  = bb_access_control_friends_settings();
		self::bb_admin_print_access_control_setting( bb_access_control_friends_key(), bb_access_control_friends_key(), '', $label, $settings, true, $sub_label );
	}

	/**
	 * Register the settings field.
	 *
	 * @param string $setting settings field.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_setting_media_access_control_register_fields( $setting ) {

		// Main General Settings Section.
		$setting->add_section(
			'media_access_control_block',
			sprintf(
				'%1s <span class="require-licence">%2s</span>',
				__( 'Media Access', 'buddyboss-pro' ),
				( ! bbp_pro_is_license_valid() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
			)
		);

		if ( bbp_pro_is_license_valid() ) {

			$args = array();

			$setting->add_field(
				bb_access_control_upload_media_key(),
				__( 'Upload Photos', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_media_access_control_setting_callback_upload_photos',
				),
				'string',
				$args
			);

			$setting->add_field(
				bb_access_control_upload_document_key(),
				__( 'Upload Documents', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_media_access_control_setting_callback_upload_documents',
				),
				'string',
				$args
			);

			$setting->add_field( 'bb-access-control-setting-tutorial', '', array( $this, 'bb_admin_access_control_setting_tutorial' ) );

			// simply add notice.
			$setting->add_field(
				'media_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					__( 'Note: These settings do not apply to administrators or group media.', 'buddyboss-pro' )
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				$args
			);
		} else {
			// simply add notice.
			$setting->add_field(
				'media_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper no-access-controls" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					sprintf(
					/* translators: %1$s - Platform Pro string, %2$s - License URL */
						esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
						'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
						sprintf(
							'<a href="%s">%s</a>',
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page' => 'buddyboss-updater',
											'tab'  => 'buddyboss_theme',
										),
										'admin.php'
									)
								)
							),
							esc_html__( 'Add License key', 'buddyboss-pro' )
						)
					)
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				array()
			);
		}

	}

	/**
	 * Callback function for the upload media upload settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_media_access_control_setting_callback_upload_photos() {
		$label              = __( 'Select which members should have access to upload photos, based on:', 'buddyboss-pro' );
		$settings           = bb_access_control_upload_photos_settings();
		$component_settings = array(
			'component' => 'media',
			'notices'   => array(
				'disable_photos_creation' => array(
					'is_disabled' => ( ! bp_is_profile_media_support_enabled() && ! bp_is_messages_media_support_enabled() && ! bp_is_forums_media_support_enabled() ) ? true : false,
					'message'     => __( 'Enable upload photos settings above in either profiles or messages or forums, to control which members can upload photos in components above.', 'buddyboss-pro' ),
					'type'        => 'info',
				),
			),
		);
		self::bb_admin_print_access_control_setting( bb_access_control_upload_media_key(), bb_access_control_upload_media_key(), '', $label, $settings, false, '', $component_settings );
	}

	/**
	 * Callback function for the upload document upload settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_media_access_control_setting_callback_upload_documents() {
		$label              = __( 'Select which members should have access to upload documents, based on:', 'buddyboss-pro' );
		$settings           = bb_access_control_upload_document_settings();
		$component_settings = array(
			'component' => 'document',
			'notices'   => array(
				'disable_document_creation' => array(
					'is_disabled' => ( ! bp_is_profile_document_support_enabled() && ! bp_is_messages_document_support_enabled() && ! bp_is_forums_document_support_enabled() ) ? true : false,
					'message'     => __( 'Enable upload documents settings above in either profiles or messages or forums, to control which members can upload documents in components above.', 'buddyboss-pro' ),
					'type'        => 'info',
				),
			),
		);
		self::bb_admin_print_access_control_setting( bb_access_control_upload_document_key(), bb_access_control_upload_document_key(), '', $label, $settings, false, '', $component_settings );
	}

	/**
	 * Callback function for the group create settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_group_access_control_setting_callback_create_groups() {
		$label    = __( 'Select which members should have access to create groups, based on:', 'buddyboss-pro' );
		$settings = bb_access_control_create_group_settings();

		$component_settings = array(
			'component' => 'groups',
			'notices'   => array(
				'disable_group_creation' => array(
					'is_disabled' => ( bp_restrict_group_creation() ) ? true : false,
					'message'     => __( 'Enable social group creation by all members above to control which members can create groups.', 'buddyboss-pro' ),
					'type'        => 'info',
				),
			),
		);
		self::bb_admin_print_access_control_setting( bb_access_control_create_group_key(), bb_access_control_create_group_key(), '', $label, $settings, false, '', $component_settings );
	}

	/**
	 * Register the settings field.
	 *
	 * @param string $setting settings field.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_setting_groups_access_control_register_fields( $setting ) {

		// Main General Settings Section.
		$setting->add_section(
			'group_access_control_block',
			sprintf(
				'%1s <span class="require-licence">%2s</span>',
				__( 'Group Access', 'buddyboss-pro' ),
				( ! bbp_pro_is_license_valid() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
			)
		);

		if ( bbp_pro_is_license_valid() ) {

			$args = array();
			$setting->add_field(
				bb_access_control_create_group_key(),
				__( 'Create Groups', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_group_access_control_setting_callback_create_groups',
				),
				'string',
				$args
			);
			$setting->add_field(
				bb_access_control_join_group_key(),
				__( 'Join Groups', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_group_access_control_setting_callback_join_groups',
				),
				'string',
				$args
			);

			$setting->add_field( 'bb-access-control-setting-tutorial', '', array( $this, 'bb_admin_access_control_setting_tutorial' ) );

			// simply add notice.
			$setting->add_field(
				'group_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					__( 'Note: These settings do not apply to administrators.', 'buddyboss-pro' )
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				$args
			);
		} else {
			// simply add notice.
			$setting->add_field(
				'group_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper no-access-controls" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					sprintf(
					/* translators: %1$s - Platform Pro string, %2$s - License URL */
						esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
						'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
						sprintf(
							'<a href="%s">%s</a>',
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page' => 'buddyboss-updater',
											'tab'  => 'buddyboss_theme',
										),
										'admin.php'
									)
								)
							),
							esc_html__( 'Add License key', 'buddyboss-pro' )
						)
					)
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				array()
			);
		}
	}

	/**
	 * Callback function for the group join group settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_group_access_control_setting_callback_join_groups() {
		$label    = __( 'Select which members should have access to join public groups or request access to private groups, based on:', 'buddyboss-pro' );
		$settings = bb_access_control_join_group_settings();
		self::bb_admin_print_access_control_setting( bb_access_control_join_group_key(), bb_access_control_join_group_key(), '', $label, $settings, false );
	}

	/**
	 * Function which will print the display settings.
	 *
	 * @param string $db_option_key           key to be stored in DB.
	 * @param string $option_name             option name.
	 * @param string $class_name              class name.
	 * @param string $label                   label to be displayed.
	 * @param array  $access_control_settings existing stored settings in DB.
	 * @param false  $multiple                show the threader or not.
	 * @param string $sub_label               sub label to be displayed.
	 * @param array  $component_settings      component settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_print_access_control_setting( $db_option_key = '', $option_name = '', $class_name = '', $label = '', $access_control_settings = array(), $multiple = false, $sub_label = '', $component_settings = array() ) {

		$option_access_controls = '';
		if ( isset( $access_control_settings ) && isset( $access_control_settings['access-control-type'] ) && '' !== $access_control_settings['access-control-type'] ) {
			$option_access_controls = $access_control_settings['access-control-type'];
		}

		$access_controls = self::bb_get_access_control_lists();
		$threaded        = $multiple;
		$multiple        = ( $multiple ) ? 'display-threaded' : 'single';
		$ajax            = false;

		require bb_access_control_path() . 'templates/core-options.php';

		$variable     = 'membership';
		$plugin_lists = $access_controls[ $variable ]['class']::instance()->bb_get_access_control_plugins_lists();
		require bb_access_control_path() . 'templates/plugin-options.php';

		$variable     = 'gamipress';
		$plugin_lists = $access_controls[ $variable ]['class']::instance()->bb_get_access_control_gamipress_lists();
		require bb_access_control_path() . 'templates/gamipress-options.php';

		require bb_access_control_path() . 'templates/checkboxes-selected.php';

	}

	/**
	 * Includes files.
	 *
	 * @param array $includes list of the files.
	 *
	 * @since 1.1.0
	 */
	public function includes( $includes = array() ) {

		$bb_platform_pro = bb_platform_pro();
		$slashed_path    = trailingslashit( $bb_platform_pro->access_control_dir );

		$includes = array(
			'cache',
			'filters',
			'rest-filters',
			'template',
			'functions',
		);

		// Loop through files to be included.
		foreach ( (array) $includes as $file ) {

			if ( empty( $this->bb_access_control_check_has_licence() ) ) {
				if ( in_array( $file, array( 'filters', 'rest-filters' ), true ) ) {
					continue;
				}
			}

			$paths = array(

				// Passed with no extension.
				'bb-' . $this->id . '-' . $file . '.php',
				'bb-' . $this->id . '/' . $file . '.php',

				// Passed with extension.
				$file,
				'bb-' . $this->id . '-' . $file,
				'bb-' . $this->id . '/' . $file,
			);

			foreach ( $paths as $path ) {
				if ( @is_file( $slashed_path . $path ) ) {
					require $slashed_path . $path;
					break;
				}
			}
		}
	}

	/**
	 * Function to return the default value if no licence.
	 *
	 * @param bool $has_access Whether has access.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed Return the default.
	 */
	protected function bb_access_control_check_has_licence( $has_access = true ) {

		if ( ! bbp_pro_is_license_valid() ) {
			return false;
		}

		return $has_access;

	}
}
