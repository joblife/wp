<?php
/**
 * LearnDash Settings Metabox for Group Access Settings.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Metabox' ) ) && ( ! class_exists( 'LearnDash_Settings_Metabox_Group_Access_Settings' ) ) ) {
	/**
	 * Class to create the settings section.
	 */
	class LearnDash_Settings_Metabox_Group_Access_Settings extends LearnDash_Settings_Metabox {

		/**
		 * Public constructor for class
		 */
		public function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'groups';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'learndash-group-access-settings';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Group.
				esc_html_x( '%s Access Settings', 'placeholder: Group', 'learndash' ),
				learndash_get_custom_label( 'group' )
			);

			$this->settings_section_description = sprintf(
				// translators: placeholder: group.
				esc_html_x( 'Controls how users will gain access to the %s', 'placeholder: group', 'learndash' ),
				esc_html( learndash_get_custom_label_lower( 'group' ) )
			);

			add_filter( 'learndash_metabox_save_fields_' . $this->settings_metabox_key, array( $this, 'filter_saved_fields' ), 30, 3 );
			add_filter( 'learndash_admin_settings_data', array( $this, 'learndash_admin_settings_data' ), 30, 1 );

			// Map internal settings field ID to legacy field ID.
			$this->settings_fields_map = array(
				// Legacy fields
				'group_price_type'                 => 'group_price_type',
				'group_price_type_paynow_price'    => 'group_price',
				'group_price_type_subscribe_price' => 'group_price',
				'group_price_type_closed_custom_button_label' => 'custom_button_label',
				'group_price_type_closed_custom_button_url' => 'custom_button_url',
				'group_price_type_closed_price'    => 'group_price',
				'group_price_billing_p3'           => 'group_price_billing_p3',
				'group_price_billing_t3'           => 'group_price_billing_t3',
			);

			parent::__construct();
		}

		/**
		 * Add script data to array.
		 *
		 * @since 3.0
		 * @param array $script_data Script data array to be sent out to browser.
		 * @return array $script_data
		 */
		public function learndash_admin_settings_data( $script_data = array() ) {

			$script_data['valid_recurring_paypal_day_range']   = esc_html__( 'Valid range is 1 to 90 when the Billing Cycle is set to days.', 'learndash' );
			$script_data['valid_recurring_paypal_week_range']  = esc_html__( 'Valid range is 1 to 52 when the Billing Cycle is set to weeks.', 'learndash' );
			$script_data['valid_recurring_paypal_month_range'] = esc_html__( 'Valid range is 1 to 24 when the Billing Cycle is set to months.', 'learndash' );
			$script_data['valid_recurring_paypal_year_range']  = esc_html__( 'Valid range is 1 to 5 when the Billing Cycle is set to years.', 'learndash' );

			return $script_data;
		}

		/**
		 * Initialize the metabox settings values.
		 */
		public function load_settings_values() {
			parent::load_settings_values();
			if ( true === $this->settings_values_loaded ) {

				if ( ! isset( $this->setting_option_values['group_price_type_paynow_price'] ) ) {
					$this->setting_option_values['group_price_type_paynow_price'] = '';
				}

				if ( ! isset( $this->setting_option_values['group_price_type_subscribe_price'] ) ) {
					$this->setting_option_values['group_price_type_subscribe_price'] = '';
				}

				if ( ! isset( $this->setting_option_values['group_price_type_closed_price'] ) ) {
					$this->setting_option_values['group_price_type_closed_price'] = '';
				}

				if ( ! isset( $this->setting_option_values['group_price_type_closed_custom_button_url'] ) ) {
					$this->setting_option_values['group_price_type_closed_custom_button_url'] = '';
				}

				if ( ! isset( $this->setting_option_values['group_price_type'] ) ) {
					$this->setting_option_values['group_price_type'] = LEARNDASH_DEFAULT_GROUP_PRICE_TYPE;
				}

				if ( ! isset( $this->setting_option_values['group_price_type_closed_custom_button_label'] ) ) {
					$this->setting_option_values['group_price_type_closed_custom_button_label'] = '';
				}
			}

			// Ensure all settings fields are present.
			foreach ( $this->settings_fields_map as $_internal => $_external ) {
				if ( ! isset( $this->setting_option_values[ $_internal ] ) ) {
					$this->setting_option_values[ $_internal ] = '';
				}
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 */
		public function load_settings_fields() {
			global $sfwd_lms;

			$this->settings_sub_option_fields = array();

			$this->setting_option_fields = array(
				'group_price_type_paynow_price' => array(
					'name'    => 'group_price_type_paynow_price',
					'label'   => sprintf(
						// translators: placeholder: Group.
						esc_html_x( '%s Price', 'placeholder: Group', 'learndash' ),
						learndash_get_custom_label( 'group' )
					),
					'type'    => 'text',
					'class'   => '-medium',
					'value'   => $this->setting_option_values['group_price_type_paynow_price'],
					'default' => '',
					'rest'    => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'price_type_paynow_price',
								// translators: placeholder: Group.
								'description' => sprintf( esc_html_x( 'Pay Now %s Price', 'placeholder: Group', 'learndash' ), LearnDash_Custom_Label::get_label( 'group' ) ),
								'type'        => 'string',
								'default'     => '',
							),
						),
					),
				),
			);
			parent::load_settings_fields();
			$this->settings_sub_option_fields['group_price_type_paynow_fields'] = $this->setting_option_fields;

			$this->setting_option_fields = array(
				'group_price_type_subscribe_price'         => array(
					'name'    => 'group_price_type_subscribe_price',
					'label'   => sprintf(
						// translators: placeholder: Group.
						esc_html_x( '%s Price', 'placeholder: Group', 'learndash' ),
						learndash_get_custom_label( 'group' )
					),
					'type'    => 'text',
					'class'   => '-medium',
					'value'   => $this->setting_option_values['group_price_type_subscribe_price'],
					'default' => '',
					'rest'    => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'price_type_subscribe_price',
								// translators: placeholder: Group.
								'description' => sprintf( esc_html_x( 'Subscribe %s Price', 'placeholder: Group', 'learndash' ), learndash_get_custom_label( 'group' ) ),
								'type'        => 'string',
								'default'     => '',
							),
						),
					),
				),
				'group_price_type_subscribe_billing_cycle' => array(
					'name'  => 'group_price_type_subscribe_billing_cycle',
					'label' => esc_html__( 'Billing Cycle', 'learndash' ),
					'type'  => 'custom',
					'html'  => $sfwd_lms->learndash_course_price_billing_cycle_html(),
				),
			);
			parent::load_settings_fields();
			$this->settings_sub_option_fields['group_price_type_subscribe_fields'] = $this->setting_option_fields;

			$this->setting_option_fields = array(
				'group_price_type_closed_price' => array(
					'name'    => 'group_price_type_closed_price',
					'label'   => sprintf(
						// translators: placeholder: Group.
						esc_html_x( '%s Price', 'placeholder: Group', 'learndash' ),
						learndash_get_custom_label( 'group' )
					),
					'type'    => 'text',
					'class'   => '-medium',
					'value'   => $this->setting_option_values['group_price_type_closed_price'],
					'default' => '',
					'rest'    => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'price_type_closed_price',
								// translators: placeholder: Group.
								'description' => sprintf( esc_html_x( 'Closed %s Price', 'placeholder: Group', 'learndash' ), learndash_get_custom_label( 'group' ) ),
								'type'        => 'string',
								'default'     => '',
							),
						),
					),
				),
				'group_price_type_closed_custom_button_url' => array(
					'name'      => 'group_price_type_closed_custom_button_url',
					'label'     => esc_html__( 'Button URL', 'learndash' ),
					'type'      => 'url',
					'class'     => 'full-text',
					'value'     => $this->setting_option_values['group_price_type_closed_custom_button_url'],
					'help_text' => sprintf(
						// translators: placeholder: "Take this Group" button label
						esc_html_x( 'Redirect the "%s" button to a specific URL.', 'placeholder: "Join Group" button label', 'learndash' ),
						learndash_get_custom_label( 'button_take_this_group' )
					),
					'default'   => '',
					'rest'      => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'price_type_closed_custom_button_url',
								// translators: placeholder: Group.
								'description' => sprintf( esc_html_x( 'Closed %s Button URL', 'placeholder: Group', 'learndash' ), learndash_get_custom_label( 'group' ) ),
								'type'        => 'string',
								'default'     => '',
							),
						),
					),
				),
			);

			parent::load_settings_fields();
			$this->settings_sub_option_fields['group_price_type_closed_fields'] = $this->setting_option_fields;

			$this->setting_option_fields = array(
				'group_price_type' => array(
					'name'    => 'group_price_type',
					'label'   => esc_html__( 'Access Mode', 'learndash' ),
					'type'    => 'radio',
					'value'   => $this->setting_option_values['group_price_type'],
					'default' => LEARNDASH_DEFAULT_GROUP_PRICE_TYPE,
					'options' => array(
						'free'      => array(
							'label'       => esc_html__( 'Free', 'learndash' ),
							'description' => sprintf(
								// translators: placeholder: group.
								esc_html_x( 'The %s is protected. Registration and enrollment are required in order to access the content.', 'placeholder: group', 'learndash' ),
								esc_html( learndash_get_custom_label_lower( 'group' ) )
							),
						),
						'paynow'    => array(
							'label'               => esc_html__( 'Buy now', 'learndash' ),
							'description'         => sprintf(
								// translators: placeholder: course, group.
								esc_html_x( 'The %1$s is protected via the LearnDash built-in PayPal and/or Stripe. Users need to purchase the %2$s (one-time fee) in order to gain access.', 'placeholder: group, group', 'learndash' ),
								learndash_get_custom_label_lower( 'group' ),
								learndash_get_custom_label_lower( 'group' )
							),
							'inline_fields'       => array(
								'group_price_type_paynow' => $this->settings_sub_option_fields['group_price_type_paynow_fields'],
							),
							'inner_section_state' => ( 'paynow' === $this->setting_option_values['group_price_type'] ) ? 'open' : 'closed',
						),
						'subscribe' => array(
							'label'               => esc_html__( 'Recurring', 'learndash' ),
							'description'         => sprintf(
								// translators: placeholder: group, group.
								esc_html_x( 'The %1$s is protected via the built-in LearnDash PayPal/Stripe functionality. Users need to purchase the %2$s to gain access and will be charged on a recurring basis.', 'placeholder: group, group', 'learndash' ),
								learndash_get_custom_label_lower( 'group' ),
								learndash_get_custom_label_lower( 'group' )
							),
							'inline_fields'       => array(
								'group_price_type_subscribe' => $this->settings_sub_option_fields['group_price_type_subscribe_fields'],
							),
							'inner_section_state' => ( 'subscribe' === $this->setting_option_values['group_price_type'] ) ? 'open' : 'closed',
						),
						'closed'    => array(
							'label'               => esc_html__( 'Closed', 'learndash' ),
							'description'         => sprintf(
								// translators: placeholder: group.
								esc_html_x( 'The %s can only be accessed through admin enrollment (manual), group enrollment, or integration (shopping cart or membership) enrollment. No enrollment button will be displayed, unless a URL is set (optional).', 'placeholder: group', 'learndash' ),
								learndash_get_custom_label_lower( 'group' )
							),
							'inline_fields'       => array(
								'group_price_type_closed' => $this->settings_sub_option_fields['group_price_type_closed_fields'],
							),
							'inner_section_state' => ( 'closed' === $this->setting_option_values['group_price_type'] ) ? 'open' : 'closed',
						),
					),
					'rest'    => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'price_type',
								// translators: placeholder: Group.
								'description' => sprintf( esc_html_x( '%s Price Type', 'placeholder: Group', 'learndash' ), LearnDash_Custom_Label::get_label( 'group' ) ),
								'type'        => 'string',
								'default'     => 'open',
								'enum'        => array(
									'closed',
									'free',
									'buynow',
									'subscribe',
								),
							),
						),
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_metabox_key );

			parent::load_settings_fields();
		}

		protected function get_save_settings_fields_map_form_post_values( $post_values = array() ) {
			$settings_fields_map = $this->settings_fields_map;
			if ( ( isset( $post_values['group_price_type'] ) ) && ( ! empty( $post_values['group_price_type'] ) ) ) {
				if ( 'paynow' === $post_values['group_price_type'] ) {
					unset( $settings_fields_map['group_price_type_subscribe_price'] );
					unset( $settings_fields_map['group_price_type_subscribe_billing_cycle'] );
					unset( $settings_fields_map['group_price_type_closed_price'] );
					unset( $settings_fields_map['group_price_type_closed_custom_button_label'] );
					unset( $settings_fields_map['group_price_type_closed_custom_button_url'] );
				} elseif ( 'subscribe' === $post_values['group_price_type'] ) {
					unset( $settings_fields_map['group_price_type_paynow_price'] );
					unset( $settings_fields_map['group_price_type_closed_price'] );
					unset( $settings_fields_map['group_price_type_closed_custom_button_label'] );
					unset( $settings_fields_map['group_price_type_closed_custom_button_url'] );
				} elseif ( 'closed' === $post_values['group_price_type'] ) {
					unset( $settings_fields_map['group_price_type_subscribe_price'] );
					unset( $settings_fields_map['group_price_type_subscribe_billing_cycle'] );
					unset( $settings_fields_map['group_price_type_paynow_price'] );
				} else {
					unset( $settings_fields_map['group_price_type_paynow_price'] );
					unset( $settings_fields_map['group_price_type_subscribe_price'] );
					unset( $settings_fields_map['group_price_type_subscribe_billing_cycle'] );
					unset( $settings_fields_map['group_price_type_closed_price'] );
					unset( $settings_fields_map['group_price_type_closed_custom_button_label'] );
					unset( $settings_fields_map['group_price_type_closed_custom_button_url'] );
				}
			}
			return $settings_fields_map;
		}

		/**
		 * Filter settings values for metabox before save to database.
		 *
		 * @param array  $settings_values Array of settings values.
		 * @param string $settings_metabox_key Metabox key.
		 * @param string $settings_screen_id Screen ID.
		 * @return array $settings_values.
		 */
		public function filter_saved_fields( $settings_values = array(), $settings_metabox_key = '', $settings_screen_id = '' ) {
			if ( ( $settings_screen_id === $this->settings_screen_id ) && ( $settings_metabox_key === $this->settings_metabox_key ) ) {

				if ( ! isset( $settings_values['group_price_type'] ) ) {
					$settings_values['group_price_type'] = '';
				}

				if ( 'paynow' === $settings_values['group_price_type'] ) {
					$settings_values['custom_button_url']      = '';
					$settings_values['group_price_billing_p3'] = '';
					$settings_values['group_price_billing_t3'] = '';
				} elseif ( 'subscribe' === $settings_values['group_price_type'] ) {
					$settings_values['custom_button_url'] = '';
				} elseif ( 'closed' === $settings_values['group_price_type'] ) {
					$settings_values['group_price_billing_p3'] = '';
					$settings_values['group_price_billing_t3'] = '';
				} else {
					$settings_values['group_price']            = '';
					$settings_values['custom_button_url']      = '';
					$settings_values['group_price_billing_p3'] = '';
					$settings_values['group_price_billing_t3'] = '';
				}

				/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
				$settings_values = apply_filters( 'learndash_settings_save_values', $settings_values, $this->settings_metabox_key );
			}

			return $settings_values;
		}

		// End of functions.
	}

	add_filter(
		'learndash_post_settings_metaboxes_init_' . learndash_get_post_type_slug( 'group' ),
		function( $metaboxes = array() ) {
			if ( ( ! isset( $metaboxes['LearnDash_Settings_Metabox_Group_Access_Settings'] ) ) && ( class_exists( 'LearnDash_Settings_Metabox_Group_Access_Settings' ) ) ) {
				$metaboxes['LearnDash_Settings_Metabox_Group_Access_Settings'] = LearnDash_Settings_Metabox_Group_Access_Settings::add_metabox_instance();
			}

			return $metaboxes;
		},
		50,
		1
	);
}