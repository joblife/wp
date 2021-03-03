<?php

/**
 * Settings Module
 *
 * @since      3.5.0
 * @package    Instructor_Role
 * @subpackage Instructor_Role/modules/classes
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Settings' ) ) {
	/**
	 * Class Instructor Role Settings Module
	 */
	class Instructor_Role_Settings {


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
		 * Adding Instructor commission menu inside learndash-lms menu.
		 *
		 * @since 2.4.0
		 */
		public function instuctor_menu() {
			if ( $this->wdmCheckInstructorCap() ) {
				add_submenu_page(
					'learndash-lms',
					__( 'Instructor', 'wdm_instructor_role' ),
					__( 'Instructor', 'wdm_instructor_role' ),
					'instructor_page',
					'instuctor',
					array( $this, 'instuctor_page_callback' )
				);
			}
		}

		/**
		 * Showing menus to insturctor and to hind according to setting for new disable commission system feature.
		 *
		 * @return boolean to show menu or not
		 *
		 * @since 2.4.0
		 */
		public function wdmCheckInstructorCap() {
			$wdmid_admin_setting = get_option( '_wdmir_admin_settings', array() );
			$wl8_show_email_tab  = false;
			$wl8_show_com_n_ex   = true;
			if ( ! is_super_admin() && array_key_exists( 'instructor_mail', $wdmid_admin_setting ) && $wdmid_admin_setting['instructor_mail'] == 1 ) {
				$wl8_show_email_tab = true;
			}
			if ( ! is_super_admin() && isset( $wdmid_admin_setting['instructor_commission'] ) && 1 == $wdmid_admin_setting['instructor_commission'] ) {
				$wl8_show_com_n_ex = false;
			}
			$wdm_instructor = get_role( 'wdm_instructor' );
			if ( null !== $wdm_instructor ) {
				if ( ! $wl8_show_email_tab && ! $wl8_show_com_n_ex ) {
					$wdm_instructor->remove_cap( 'instructor_page' );
					return false;
				} else {
					$wdm_instructor->add_cap( 'instructor_page' );
					return true;
				}
			}
		}

		/**
		 * Adding tabs inside intructor commission page.
		 *
		 * @since  2.4.0
		 */
		public function instuctor_page_callback() {
			// check whether email tab should exist for instrutor or not.
			$ir_admin_settings        = get_option( '_wdmir_admin_settings', array() );
			$ir_enable_email_settings = false;
			$ir_enable_commission     = true; // for showing commission and export tabs

			// If admin select instructor mail option then we need to display only three tabs.
			if ( array_key_exists( 'instructor_mail', $ir_admin_settings ) && 1 == $ir_admin_settings['instructor_mail'] ) {
				$ir_enable_email_settings = true;
			}

			if ( ! is_super_admin() && isset( $ir_admin_settings['instructor_commission'] ) && 1 == $ir_admin_settings['instructor_commission'] ) {
				$ir_enable_commission = false;
			}

			$current_tab = $this->wdmSetCurrentTab( $ir_admin_settings, $ir_enable_commission, $ir_enable_email_settings );
			?>
			<h2 class="nav-tab-wrapper">
				<?php $this->wl8ShowTabs( $current_tab, $ir_enable_email_settings, $ir_enable_commission ); ?>
				<?php
					/**
					 * Hook to add instructor setting tab headers.
					 *
					 * Used to add additional instructor setting tab header for adding new settings tabs.
					 *
					 * @since 3.4.0
					 *
					 * @param string $current_tab Current selected instructor settings tab tab
					 */
					do_action( 'instuctor_tab_add', $current_tab );
				?>
			</h2>
			<?php
			$this->wl8ShowCurrentTab( $current_tab );
		}

		/**
		 * Checking current tab if not set then setting it to default tab
		 *
		 * @param array $wdmid_admin_setting        List of settings tabs
		 * @param bool  $ir_enable_commission        Whether instructor commission settings are enabled.
		 * @param bool  $ir_enable_email_settings    Whether instructor email settings are enabled.
		 *
		 * @return string $current_tab              Current active tab
		 */
		public function wdmSetCurrentTab( $wdmid_admin_setting, $ir_enable_commission, $ir_enable_email_settings ) {
			$instructor_allowed_tabs = array(
				'commission_report',
				'export',
				'email',
				'instructor-email',
			);

			if ( $ir_enable_commission ) {
				$current_tab = 'commission_report';
			} else {
				$current_tab = 'email';
			}

			// If instructor and tab set.
			if ( wdm_is_instructor() && isset( $_GET['tab'] ) ) {
				// Check if tab access allowed.
				if ( in_array( $_GET['tab'], $instructor_allowed_tabs ) ) {
					$current_tab = $_GET['tab'];
				}
			}

			// If admin allow all access.
			if ( is_super_admin() ) {
				$current_tab = $_GET['tab'];
				if ( empty( $current_tab ) ) {
					$current_tab = 'instructor';
				}
			}

			return $current_tab;
		}

		/**
		 * Functions shows all tabs depending on conditions.
		 *
		 * @param string $current_tab              Currently selected instructor settings tab.
		 * @param bool   $ir_enable_email_settings Whether email settings are enabled or not.
		 * @param bool   $ir_enable_commission     Whether commission settings are enabled or not.
		 *
		 * @since  2.4.0
		 */
		public function wl8ShowTabs( $current_tab, $ir_enable_email_settings, $ir_enable_commission ) {
			$settings_tabs = array(
				'instructor'        => array(
					'title'  => __( 'Instructor', 'wdm_instructor_role' ),
					'access' => array( 'admin' ),
				),
				'commission_report' => array(
					'title'  => __( 'Commission Report', 'wdm_instructor_role' ),
					'access' => array( 'admin', 'instructor' ),
				),
				'export'            => array(
					'title'  => __( 'Export', 'wdm_instructor_role' ),
					'access' => array( 'admin', 'instructor' ),
				),
				'email'             => array(
					'title'  => __( 'Email', 'wdm_instructor_role' ),
					'access' => array( 'admin', 'instructor' ),
				),
				'settings'          => array(
					'title'  => __( 'Settings', 'wdm_instructor_role' ),
					'access' => array( 'admin' ),
				),
			);

			/**
			 * Filter the instructor settings tabs to be displayed
			*
			* @param  array $settings_tabs    List of setting tabs to be displayed.
			* @param string $current_tab      Slug of the currently selected tab.
			*
			* @since   3.4.0
			*/
			$settings_tabs = apply_filters( 'ir_filter_instructor_setting_tabs', $settings_tabs, $current_tab );

			// Add promotions tab at the end.
			$settings_tabs['wdm_ir_promotion'] = array(
				'title'  => __( 'Other Extensions', 'wdm_instructor_role' ),
				'access' => array( 'admin' ),
			);

			foreach ( $settings_tabs as $key => $tab ) {
				// Check if admin tab.
				if ( current_user_can( 'manage_options' ) && ! in_array( 'admin', $tab['access'] ) ) {
					continue;
				}

				// Check if instructor tab.
				if ( wdm_is_instructor() && ! in_array( 'instructor', $tab['access'] ) ) {
					continue;
				}

				// If commission and export tab but setting disabled then don't show.
				if ( ( 'commission_report' == $key || 'export' == $key ) && ! $ir_enable_commission ) {
					continue;
				}

				// If email tab but setting disabled then don't show.
				if ( 'email' == $key && ! $ir_enable_email_settings ) {
					continue;
				}
				?>
			<a class="nav-tab <?php echo ( $current_tab == $key ) ? 'nav-tab-active' : ''; ?>" href="?page=instuctor&tab=<?php echo esc_attr( $key ); ?>">
				<?php echo esc_html( $tab['title'] ); ?>
			</a>
				<?php
			}
		}

		/**
		 * Function shows commision and export content.
		 *
		 * @param string $current_tab       Current tab
		 * @param bool   $wl8_show_com_n_ex Whether commission settings are enabled or not.
		 *
		 * @return html to show tabs
		 *
		 * @since  2.4.0
		 */
		public function wl8ShowCommissionAndExportContent( $current_tab, $wl8_show_com_n_ex ) {
			if ( ! $wl8_show_com_n_ex ) {
				return;
			}

			$tabs = array(
				'commission_report' => __( 'Commission Report', 'wdm_instructor_role' ),
				'export'            => __( 'Export', 'wdm_instructor_role' ),
			);

			/**
			 * Filter the commission and export tabs display
			 *
			 * @param  array $tabs              The tabs to currently be displayed
			 * @param bool   $wl8_show_com_n_ex Whether commission settings are enabled or not.
			 *
			 * @return array $tabs    Updated list of tabs to be displayed
			 */
			$tabs = apply_filters( 'ir_filter_commission_and_export_tabs', $tabs, $wl8_show_com_n_ex );

			foreach ( $tabs as $key => $tab ) {
				?>
				<a class="nav-tab <?php echo ( $current_tab == $key ) ? 'nav-tab-active' : ''; ?>" href="?page=instuctor&tab=<?php echo esc_attr( $key ); ?>">
					<?php echo esc_html( $tab ); ?>
				</a>
				<?php
			}
		}

		/**
		 * Function shows mail tab.
		 *
		 * @param string  $current_tab          Current_tab.
		 * @param boolean $wl8_show_email_tab   Whether email tab to be displayed or not.
		 * @param bool    $wl8_show_com_n_ex     Whether commission settings are enabled or not.
		 *
		 * @return html to show tab
		 */
		public function wl8ShowMailTab( $current_tab, $wl8_show_email_tab, $wl8_show_com_n_ex ) {
			if ( ! $wl8_show_email_tab ) {
				return;
			} elseif ( is_super_admin() || $wl8_show_email_tab ) {
				?>
				<a class="nav-tab <?php echo ( 'email' === $current_tab ) ? 'nav-tab-active' : ''; ?>" href="?page=instuctor&tab=email">
					<?php esc_html_e( 'Email', 'wdm_instructor_role' ); ?>
				</a>
				<?php
			}
		}

		/**
		 * Function shows current tab.
		 *
		 * @param string $current_tab Current tab.
		 *
		 * @since 2.4.0
		 */
		public function wl8ShowCurrentTab( $current_tab ) {
			switch ( $current_tab ) {
				case 'instructor':
					$this->wdm_instructor_first_tab();
					break;
				case 'commission_report':
					$this->wdm_instructor_second_tab();
					break;
				case 'export':
					$this->wdm_instructor_third_tab();
					break;
				case 'email':
					if ( is_super_admin() ) {
						$this->wdmir_instructor_email_settings();
					} else {
						$this->wdmir_individual_instructor_email_setting();
					}
					break;
				case 'settings':
					$this->wdmir_instructor_settings();
					break;
				case 'wdm_ir_promotion':
					$this->wdmir_promotion();
					break;
			}

			/**
			 * Display instructor content based on currently selected tab
			 *
			 * @param string $current_tab  Current selected instructor settings tab.
			 *
			 * @since 2.4.0
			 */
			do_action( 'instuctor_tab_checking', $current_tab );
		}

		/**
		 * Showing other extentions link.
		 */
		public function wdmir_promotion() {
			if ( false === ( $extensions = get_transient( '_ir_extensions_data' ) ) ) {
				$extensions_json = wp_remote_get(
					'https://wisdmlabs.com/products-thumbs/ld_extensions.json',
					array(
						'user-agent' => 'IR Extensions Page',
					)
				);
				if ( ! is_wp_error( $extensions_json ) ) {
					$extensions = json_decode( wp_remote_retrieve_body( $extensions_json ) );

					if ( $extensions ) {
						set_transient( '_ir_extensions_data', $extensions, 72 * HOUR_IN_SECONDS );
					}
				}
			}
			include_once 'templates/other-extensions.php';
			unset( $extensions );
			$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ) ? '' : '.min';

			wp_register_style( 'wdmir-promotion', plugins_url( 'css/extension' . $min . '.css', __DIR__ ), array(), '1.0.0' );

			// Enqueue admin styles.
			wp_enqueue_style( 'wdmir-promotion' );
		}

		/**
		 * [Displaying table for allocating instructor commission percentage].
		 *
		 * @return [html] [footable table for updating commission]
		 *
		 * @since 2.4.0
		 */
		public function wdm_instructor_first_tab() {
			wp_enqueue_script( 'wdm_instructor_report_js', plugins_url( 'js/commission_report.js', __DIR__ ), array( 'jquery' ) );
			wp_enqueue_script( 'wdm_footable_pagination', plugins_url( 'js/footable.paginate.js', __DIR__ ), array( 'jquery' ) );
			wp_enqueue_script( 'wdm_commission_js', plugins_url( 'js/commission.js', __DIR__ ), array( 'jquery' ) );
			$data = array(
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'invalid_percentage' => __( 'Invalid percentage', 'wdm_instructor_role' ),
			);
			wp_localize_script( 'wdm_commission_js', 'wdm_commission_data', $data );
			// To show values in Commission column.
			$instr_commissions = get_option( 'instructor_commissions', '' );
			$instr_commissions = $instr_commissions;
			// To get user Ids of instructors.
			$args        = array(
				'fields' => array( 'ID', 'display_name', 'user_email' ),
				'role'   => 'wdm_instructor',
			);
			$instructors = get_users( $args );
			?>
			<br/>
			<div id="reports_table_div" style="padding-right: 5px">
				<div class="CL"></div>
					<?php esc_html_e( 'Search', 'wdm_instructor_role' ); ?>
					<input id="filter" type="text">
					<select name="change-page-size" id="change-page-size">
						<option value="5">
							<?php esc_html_e( '5 per page', 'wdm_instructor_role' ); ?>
						</option>
						<option value="10">
							<?php esc_html_e( '10 per page', 'wdm_instructor_role' ); ?>
						</option>
						<option value="20">
							<?php esc_html_e( '20 per page', 'wdm_instructor_role' ); ?>
						</option>
						<option value="50">
							<?php esc_html_e( '50 per page', 'wdm_instructor_role' ); ?>
						</option>
				</select>
				<br><br>
				<!--Table shows Name, Email, etc-->
				<table class="footable" data-filter="#filter"  id="wdm_report_tbl" data-page-size="5" >
					<thead>
						<tr>
							<th data-sort-initial="descending" data-class="expand">
								<?php esc_html_e( 'Name', 'wdm_instructor_role' ); ?>
							</th>
							<th>
								<?php esc_html_e( 'User email', 'wdm_instructor_role' ); ?>
							</th>
							<th>
								<?php esc_html_e( 'Commission %', 'wdm_instructor_role' ); ?>
							</th>
							<th data-hide="phone" >
								<?php esc_html_e( 'Update', 'wdm_instructor_role' ); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( ! empty( $instructors ) ) {
							foreach ( $instructors as $instructor ) {
								$commission_percent = get_user_meta( $instructor->ID, 'wdm_commission_percentage', true );
								if ( '' == $commission_percent ) {
									$commission_percent = 0;
								}
								?>
								<tr>
									<td>
									<?php echo esc_html( $instructor->display_name ); ?>
								</td>
									<td>
									<?php echo esc_html( $instructor->user_email ); ?>
								</td>
									<td>
										<input
											name="commission_input"
											size="5"
											value="<?php echo esc_attr( $commission_percent ); ?>"
											min="0"
											max="100"
											type="number"
											id="input_<?php echo esc_attr( $instructor->ID ); ?>"
										/>
									</td>
									<td>
										<a
											name="update_<?php echo esc_attr( $instructor->ID ); ?>"
											class="update_commission button button-primary"
											href="#">
											<?php esc_html_e( 'Update', 'wdm_instructor_role' ); ?>
										</a>
										<img
											class="wdm_ajax_loader"
											src="<?php echo esc_attr( plugins_url( 'media/ajax-loader.gif', __DIR__ ) ); ?>"
											style="display:none;"
										/>
									</td>
								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan="4">
									<?php esc_html_e( 'No instructor found', 'wdm_instructor_role' ); ?>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
					<tfoot class="hide-if-no-paging">
						<tr>
							<td colspan="4" style="border-radius: 0 0 6px 6px;">
								<div class="pagination pagination-centered hide-if-no-paging"></div>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<br/>
			<div id="update_commission_message"></div>
			<?php
		}

		/**
		 * [Commission report page].
		 *
		 * @return [html] [to show select tag of instructor]
		 *
		 * @since 2.4.0
		 */
		public function wdm_instructor_second_tab() {
			if ( ! is_super_admin() ) {
				$instructor_id = get_current_user_id();
			} else {
				$args          = array(
					'fields' => array( 'ID', 'display_name' ),
					'role'   => 'wdm_instructor',
				);
				$instructors   = get_users( $args );
				$instructor_id = '';
				if ( isset( $_REQUEST['wdm_instructor_id'] ) ) {
					$instructor_id = $_REQUEST['wdm_instructor_id'];
				}
				if ( empty( $instructors ) ) {
					echo __( 'No instructor found', 'wdm_instructor_role' );

					return;
				}
				?>
				<form method="post" action="?page=instuctor&tab=commission_report">
					<table>
						<tr>
							<th><?php esc_html_e( 'Select Instructor:', 'wdm_instructor_role' ); ?></th>
							<td>
								<select name="wdm_instructor_id">
									<?php foreach ( $instructors as $instructor ) : ?>
										<option
											value="<?php echo esc_attr( $instructor->ID ); ?>"
											<?php echo ( $instructor_id == $instructor->ID ) ? 'selected' : ''; ?>
										>
											<?php echo esc_html( $instructor->display_name ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</td>

							<td>
								<input
									type="submit"
									value="<?php esc_html_e( 'Submit', 'wdm_instructor_role' ); ?>"
									class="button-primary"
								/>
							</td>
						</tr>
					</table>
				</form>
				<?php
			}
			if ( '' != $instructor_id ) {
				$this->wdm_commission_report( $instructor_id );
			}
		}

		/**
		 * [Export tab for insturctor and admin].
		 *
		 * @return [html] [instructor_third_tab]
		 *
		 * @since 2.4.0
		 */
		public function wdm_instructor_third_tab() {
			if ( ! is_super_admin() ) {
				$instructor_id = get_current_user_id();
			} else {
				$args        = array(
					'fields' => array( 'ID', 'display_name' ),
					'role'   => 'wdm_instructor',
				);
				$instructors = get_users( $args );

				$instructor_id = '';
				if ( isset( $_REQUEST['wdm_instructor_id'] ) ) {
					if ( '-1' == $_REQUEST['wdm_instructor_id'] ) {
						$instructor_id = '-1';
					} else {
						$instructor_id = $_REQUEST['wdm_instructor_id'];
					}
				}
				if ( empty( $instructors ) ) {
					echo __( 'No instructor found', 'wdm_instructor_role' );

					return;
				}
			}
			wp_enqueue_script( 'wdm_instructor_report_js', plugins_url( 'js/commission_report.js', __DIR__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ) );
			// $url = plugins_url( 'js/jquery-ui.js', __DIR__ );
			// wp_enqueue_script( 'wdm-date-js', $url, array( 'jquery' ), true );
			$url = plugins_url( 'css/jquery-ui.css', __DIR__ );
			wp_enqueue_style( 'wdm-date-css', $url );
			wp_enqueue_script( 'wdm-datepicker-js', plugins_url( 'js/wdm_datepicker.js', __DIR__ ), array( 'jquery' ) );
			$start_date = $this->wdmCheckIsSet( $_POST['wdm_start_date'] );// isset($_POST['wdm_start_date']) ? $_POST['wdm_start_date'] : '';
			$end_date   = $this->wdmCheckIsSet( $_POST['wdm_end_date'] );// isset($_POST['wdm_end_date']) ? $_POST['wdm_end_date'] : '';
			?>
				<form method="post" action="?page=instuctor&tab=export">
					<table>
						<?php if ( is_super_admin() ) : ?>
							<tr>
								<th style="float:left;">
									<?php esc_html_e( 'Select Instructor:', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<select name="wdm_instructor_id">
										<option value="-1"><?php esc_html_e( 'All', 'wdm_instructor_role' ); ?></option>
										<?php foreach ( $instructors as $instructor ) : ?>
											<option
												value="<?php echo $instructor->ID; ?>" 
												<?php echo ( $instructor_id == $instructor->ID ) ? 'selected' : ''; ?>
											>
												<?php echo $instructor->display_name; ?>
											</option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
						<?php endif; ?>
						<tr>
							<th style="float:left;"><?php esc_html_e( 'Start Date:', 'wdm_instructor_role' ); ?></th>
							<td>
								<input
									type="text"
									name="wdm_start_date"
									id="wdm_start_date" value="<?php echo esc_attr( $start_date ); ?>"
									readonly
								/>
							</td>
						</tr>
						<tr>
							<th style="float:left;"><?php esc_html_e( 'End Date:', 'wdm_instructor_role' ); ?></th>
							<td>
								<input
									type="text"
									name="wdm_end_date"
									id="wdm_end_date" value="<?php echo esc_attr( $end_date ); ?>"
									readonly
								/>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input
									type="submit"
									class="button-primary" 
									value="<?php esc_html_e( 'Submit', 'wdm_instructor_role' ); ?>"
									id="wdm_submit"
								/>
							</td>
						</tr>
					</table>
				</form>
				<?php
				// }
				if ( '' != $instructor_id ) {
					$this->wdm_export_csv_report( $instructor_id, $start_date, $end_date );
				}
		}

		/**
		 * [Report filtered by instructor, start and end date].
		 *
		 * @param [int]    $instructor_id [description]
		 * @param [string] $start_date    [start_date]
		 * @param [string] $end_date      [end_date]
		 *
		 * @return [html] [report in table format]
		 *
		 * @since 2.4.0
		 */
		public function wdm_export_csv_report( $instructor_id, $start_date, $end_date ) {
			global $wpdb;
			wp_enqueue_script( 'wdm_footable_pagination', plugins_url( 'js/footable.paginate.js', __DIR__ ), array( 'jquery' ) );
			?>
			<br><br>
			<div id="reports_table_div" style="padding-right: 5px">
				<div class="CL"></div>
			<?php
			echo __( 'Search', 'wdm_instructor_role' );
			?>
				<input id="filter" type="text">
				<select name="change-page-size" id="change-page-size">
					<option value="5">
					<?php
					echo __( '5 per page', 'wdm_instructor_role' );
					?>
			</option>
					<option value="10">
					<?php
					echo __( '10 per page', 'wdm_instructor_role' );
					?>
			</option>
					<option value="20">
					<?php
					echo __( '20 per page', 'wdm_instructor_role' );
					?>
			</option>
					<option value="50">
					<?php
					echo __( '50 per page', 'wdm_instructor_role' );
					?>
			</option>
				</select>
				<?php
				if ( file_exists( INSTRUCTOR_ROLE_ABSPATH . '/libs/ParseCSV/parsecsv.lib.php' ) ) {
					$url = admin_url( 'admin.php?page=instuctor&tab=export&wdm_export_report=wdm_export_report&wdm_instructor_id=' . $instructor_id . '&start_date=' . $start_date . '&end_date=' . $end_date );
					?>
				<a href="
					<?php
					echo $url;
					?>
			" class="button-primary" style="float:right">
					<?php
					echo __( 'Export CSV', 'wdm_instructor_role' );
					?>
			</a>
					<?php
				}

				?>
					<!--Table shows Name, Email, etc-->
					<br><br>
					<table class="footable" data-filter="#filter" data-page-navigation=".pagination" id="wdm_report_tbl" data-page-size="5" >
						<thead>
							<tr>
								<th data-sort-initial="descending" data-class="expand">
									<?php
									echo __( 'Order ID', 'wdm_instructor_role' );
									?>
								</th>
								<th data-sort-initial="descending" data-class="expand">
									<?php

									echo $this->showOwnerOrPurchaser();
									?>
								</th>
								<th data-sort-initial="descending" data-class="expand">
									<?php
									echo __( 'Product / Course Name', 'wdm_instructor_role' );
									?>
								</th>
								<th>
									<?php
									echo __( 'Actual Price', 'wdm_instructor_role' );
									?>
								</th>
								<th>
				<?php
				echo __( 'Commission Price', 'wdm_instructor_role' );
				?>
								</th>

								<th>
									<?php
									echo __( 'Product Type', 'wdm_instructor_role' );
									?>
								</th>

							</tr>
							<?php
							do_action( 'wdm_commission_report_table_header', $instructor_id );
							?>
						</thead>
						<tbody>
							<?php
							$sql = "SELECT * FROM {$wpdb->prefix}wdm_instructor_commission WHERE 1=1 ";
							// echo $start_date;exit;

							$this->wdmCreateSQLQuery( $instructor_id, $start_date, $end_date, $sql );

							$results = $wpdb->get_results( $sql );
							$hasData = false;
							if ( ! empty( $results ) ) {
								foreach ( $results as $value ) {
									if ( ! $this->userIdExists( $value->user_id ) ) {
										continue;
									}
									$hasData      = true;
									$user_details = get_user_by( 'id', $value->user_id );

									?>
									<tr>
										<td>
											<?php
											if ( is_super_admin() ) {
												?>
												<a href="
												<?php
												echo $this->wdmGetPostPermalink( $value->order_id, $value->product_type );
												?>
				" target="
												<?php
												echo $this->needToOpenNewDocument();
												?>
				">
												<?php
												echo $value->order_id;
												?>
				</a>

												<?php
											} else {
												echo $value->order_id;
											}
											?>
									</td>
									<td>
									<?php
									echo $this->wdmShowUserName( $value->order_id, $user_details->display_name, $value->product_type );
									?>
								</td>
									<td><a target="_new_blank" 
									<?php
									echo $this->wdmGetPostEditLink( $value->product_id );
									?>
								>
									<?php
									echo $this->wdmGetPostTitle( $value->product_id );
									?>
					</a></td>
									<td>
									<?php
									echo $value->actual_price;
									?>
								</td>
									<td>
									<?php
									echo $value->commission_price;
									?>
								</td>
									<td>
									<?php
									echo $value->product_type;
									?>
								</td>

								</tr>
									<?php
								}
							} else {
								$hasData = true;
								?>
							<tr>
								<td>
								<?php
								echo __( 'No record found!', 'wdm_instructor_role' );
								?>
										</td>
										</tr>
												<?php
							}
							if ( ! $hasData ) {
								?>
							<tr>
								<td>
								<?php
								echo __( 'No record found!', 'wdm_instructor_role' );
								?>
										</td>
										</tr>
												<?php
							}
							do_action( 'wdm_commission_report_table', $instructor_id );
							?>
					</tbody>
					<tfoot >

						<tr>
							<td colspan="6" style="border-radius: 0 0 6px 6px;">
								<div class="pagination pagination-centered hide-if-no-paging"></div>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<br>
			<?php
			if ( file_exists( INSTRUCTOR_ROLE_ABSPATH . '/libs/ParseCSV/parsecsv.lib.php' ) ) {
				$url = admin_url( 'admin.php?page=instuctor&tab=export&wdm_export_report=wdm_export_report&wdm_instructor_id=' . $instructor_id . '&start_date=' . $start_date . '&end_date=' . $end_date );
				?>
				<a href="
				<?php
				echo $url;
				?>
				" class="button-primary" style="float:right">
				<?php
				echo __( 'Export CSV', 'wdm_instructor_role' );
				?>
				</a>
				<?php
			}
		}

		public function wdmCreateSQLQuery( $instructor_id, $start_date, $end_date, &$sql ) {
			if ( '-1' != $instructor_id ) {
				$sql .= "AND user_id = $instructor_id ";
			}
			if ( '' != $start_date ) {
				$start_date = Date( 'Y-m-d', strtotime( $start_date ) );
				$sql       .= "AND transaction_time >='$start_date 00:00:00'";
			}
			if ( '' != $end_date ) {
				$end_date = Date( 'Y-m-d', strtotime( $end_date ) );
				$sql     .= " AND transaction_time <='$end_date 23:59:59'";
			}
		}

		/**
		 * [wdmShowUserName displaying owner/purchaser name according to product].
		 *
		 * @param [int]    $order_id     [order_id]
		 * @param [string] $display_name [display_name]
		 *
		 * @return [string] [owner/purchaser name]
		 */
		public function wdmShowUserName( $order_id, $display_name, $product_type ) {
			$product_type_array = array(
				'WC' => '_customer_user',
				'LD' => 'LD', // v2.4.0
			);
			$product_type_array = apply_filters( 'wdm_product_type_array', $product_type_array );

			if ( is_super_admin() ) {
				return $display_name;
			}
			if ( ! isset( $product_type_array[ $product_type ] ) ) {
				$product_type_array['LD'] = 'LD';
			}

			if ( $product_type_array[ $product_type ] == 'LD' ) {
				$ownerID = get_post_field( 'post_author', $order_id );
			} else {
				$ownerID = get_post_meta( $order_id, $product_type_array[ $product_type ], true );
			}

			if ( empty( $ownerID ) ) {
				if ( false === get_post_status( $order_id ) ) {
					return __( 'Order has been deleted!', 'wdm_instructor_role' );
				}
			}
			$user_info = get_userdata( $ownerID );
			if ( $user_info ) {
				return $user_info->first_name . ' ' . $user_info->last_name;
			}
			return __( 'User not found!', 'wdm_instructor_role' );
		}

		/**
		 * [Export data filter wise].
		 *
		 * @return [file] [csv file]
		 *
		 * @since 2.4.0
		 */
		public function wdm_export_csv_date_filter() {
			if ( isset( $_GET['wdm_export_report'] ) && 'wdm_export_report' == $_GET['wdm_export_report'] ) {
				global $wpdb;
				$instructor_id = $_REQUEST['wdm_instructor_id'];
				$start_date    = $_GET['start_date'];
				$end_date      = $_GET['end_date'];
				$sql           = "SELECT * FROM {$wpdb->prefix}wdm_instructor_commission WHERE 1=1";
				if ( '' != $instructor_id && '-1' != $instructor_id ) {
					if ( $this->userIdExists( $instructor_id ) ) {
						$sql .= ' AND user_id=' . $instructor_id;
					}
				}
				if ( '' != $start_date ) {
					$start_date = Date( 'Y-m-d', strtotime( $start_date ) );
					$sql       .= " AND transaction_time >='$start_date 00:00:00'";
				}
				if ( '' != $end_date ) {
					$end_date = Date( 'Y-m-d', strtotime( $end_date ) );
					$sql     .= " AND transaction_time <='$end_date 23:59:59'";
				}

				$results = $wpdb->get_results( $sql );

				$course_progress_data = array();

				if ( empty( $results ) ) {
					$row = array( 'No data' => __( 'No data found', 'wdm_instructor_role' ) );
				} else {
					foreach ( $results as $value ) {
						if ( ! $this->userIdExists( $value->user_id ) ) {
							continue;
						}
						$user_data = get_user_by( 'id', $value->user_id );
						$row       = array(
							'Order id'         => $value->order_id,
							'' . $this->showOwnerOrPurchaserTR() => $this->wdmShowUserName( $value->order_id, $user_data->display_name, $value->product_type ),
							'Actual price'     => $value->actual_price,
							'Commission price' => $value->commission_price,
							'Product name'     => $this->wdmGetPostTitle( $value->product_id ),
							'Transaction time' => $value->transaction_time,
							'Product type'     => $value->product_type,
						);

						$course_progress_data[] = $row;
					}
				}

				if ( file_exists( INSTRUCTOR_ROLE_ABSPATH . '/libs/ParseCSV/parsecsv.lib.php' ) ) {
					require_once INSTRUCTOR_ROLE_ABSPATH . '/libs/ParseCSV/parsecsv.lib.php';
					$csv = @new \lmsParseCSVNS\LmsParseCSV();
					if ( empty( $course_progress_data ) ) {
						$row   = array();
						$row[] = array( '' => __( 'No data found', 'wdm_instructor_role' ) );
						$csv->output( true, 'commission_report.csv', $row, array_keys( reset( $row ) ) );
					} else {
						$csv->output( true, 'commission_report.csv', $course_progress_data, array_keys( reset( $course_progress_data ) ) );
					}
					die();
				}
			}
		}

		/**
		 * Function to check post is set or not.
		 */
		public function wdmCheckIsSet( $post ) {
			if ( isset( $post ) ) {
				return $post;
			}

			return '';
		}

		/**
		 * Function to return site url to edit post, if current user is super admin.
		 *
		 * @param [string] $value [checking for admin]
		 *
		 * @return [string] [url]
		 *
		 * @since 2.4.0
		 */
		public function wdmGetPostPermalink( $value, $type = null ) {
			if ( is_super_admin() && $type == 'EDD' ) {
				return site_url( 'wp-admin/edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $value );
			} elseif ( is_super_admin() ) {
				return site_url( 'wp-admin/post.php?post=' . $value . '&action=edit' );
			}

			return '#';
		}

		/**
		 * Function returns string '_new_blank', if user is super admin.
		 *
		 * @return [string] [open in new blank tab if user is super admin.]
		 *
		 * @since 2.4.0
		 */
		public function needToOpenNewDocument() {
			if ( is_super_admin() ) {
				return '_new_blank';
			}

			return '';
		}

		/**
		 * [showOwnerOrPurchaser showing heading if admin then username of ownwer if instructor then purchaser].
		 *
		 * @return [string] [heading]
		 */
		public function showOwnerOrPurchaser() {
			if ( is_super_admin() ) {
				return __( 'Instructor name', 'wdm_instructor_role' );
			}

			return __( 'Purchaser name', 'wdm_instructor_role' );
		}

		public function showOwnerOrPurchaserTR() {
			if ( is_super_admin() ) {
				return 'Instructor name';
			}

			return 'Purchaser name';
		}

		/**
		 * [Export functionality for admin as well as instructor].
		 *
		 * @return [nothing]
		 *
		 * @since 2.4.0
		 */
		public function wdm_export_commission_report() {
			if ( isset( $_GET['wdm_commission_report'] ) && 'wdm_commission_report' == $_GET['wdm_commission_report'] ) {
				global $wpdb;
				$instructor_id = $_REQUEST['wdm_instructor_id'];
				$user_data     = get_user_by( 'id', $instructor_id );

				$sql     = "SELECT * FROM {$wpdb->prefix}wdm_instructor_commission WHERE user_id=$instructor_id";
				$results = $wpdb->get_results( $sql );

				$course_progress_data = array();
				$amount_paid          = 0;
				if ( empty( $results ) ) {
					$row = array( 'instructor name' => $user_data->display_name );
				} else {
					foreach ( $results as $value ) {
						$row                    = array(
							'order id'         => $value->order_id,
							'instructor name'  => $user_data->display_name,
							'actual price'     => $value->actual_price,
							'commission price' => $value->commission_price,
							'product name'     => $this->wdmGetPostTitle( $value->product_id ),
							'transaction time' => $value->transaction_time,
						);
						$amount_paid            = $amount_paid + $value->commission_price;
						$course_progress_data[] = $row;
					}
					$paid_total = get_user_meta( $instructor_id, 'wdm_total_amount_paid', true );
					if ( '' == $paid_total ) {
						$paid_total = 0;
					}
					$amount_paid            = round( ( $amount_paid - $paid_total ), 2 );
					$amount_paid            = max( $amount_paid, 0 );
					$row                    = array(
						'order id'         => __( 'Paid Earnings', 'wdm_instructor_role' ),
						'instructor name'  => $paid_total,
						'actual price'     => '',
						'commission price' => '',
						'product name'     => '',
						'transaction time' => '',
					);
					$course_progress_data[] = $row;
					$row                    = array(
						'order id'         => __( 'Unpaid Earnings', 'wdm_instructor_role' ),
						'instructor name'  => $amount_paid,
						'actual price'     => '',
						'commission price' => '',
						'product name'     => '',
						'transaction time' => '',
					);
					$course_progress_data[] = $row;
				}

				if ( file_exists( INSTRUCTOR_ROLE_ABSPATH . '/libs/ParseCSV/parsecsv.lib.php' ) ) {
					require_once INSTRUCTOR_ROLE_ABSPATH . '/libs/ParseCSV/parsecsv.lib.php';
					$csv = @new \lmsParseCSVNS\LmsParseCSV();

					$csv->output( true, 'commission_report.csv', $course_progress_data, array_keys( reset( $course_progress_data ) ) );

					die();
				}
			}
		}

		/**
		 * [Commission Report page].
		 *
		 * @param [int] $instructor_id [instructor_id]
		 *
		 * @return [html] [to show all the commission report]
		 *
		 * @since 2.4.0
		 */
		public function wdm_commission_report( $instructor_id ) {
			global $wpdb;
			wp_enqueue_script( 'wdm_footable_pagination', plugins_url( 'js/footable.paginate.js', __DIR__ ), array( 'jquery' ) );
			wp_enqueue_script( 'wdm_instructor_report_js', plugins_url( 'js/commission_report.js', __DIR__ ), array( 'jquery' ) );
			$data = array(
				'ajax_url'               => admin_url( 'admin-ajax.php' ),
				'enter_amount'           => __( 'Please Enter amount', 'wdm_instructor_role' ),
				'enter_amount_less_than' => __( 'Please enter amount less than amount to be paid', 'wdm_instructor_role' ),
				'added_successfully'     => __( 'Record added successfully', 'wdm_instructor_role' ),
			);
			wp_localize_script( 'wdm_instructor_report_js', 'wdm_commission_data', $data );
			?>
			<br><br>
			<div id="reports_table_div" style="padding-right: 5px">
				<div class="CL"></div>
			<?php echo __( 'Search', 'wdm_instructor_role' ); ?>
				<input id="filter" type="text">
				<select name="change-page-size" id="change-page-size">
					<option value="5"><?php echo __( '5 per page', 'wdm_instructor_role' ); ?></option>
					<option value="10"><?php echo __( '10 per page', 'wdm_instructor_role' ); ?></option>
					<option value="20"><?php echo __( '20 per page', 'wdm_instructor_role' ); ?></option>
					<option value="50"><?php echo __( '50 per page', 'wdm_instructor_role' ); ?></option>
				</select>

				<!--Table shows Name, Email, etc-->
				<br><br>
				<table class="footable" data-filter="#filter" data-page-navigation=".pagination" id="wdm_report_tbl" data-page-size="5" >
					<thead>
						<tr>
							<th data-sort-initial="descending" data-class="expand">
								<?php echo __( 'Order ID', 'wdm_instructor_role' ); ?>
							</th>
							<th data-sort-initial="descending" data-class="expand">
								<?php echo __( 'Product / Course Name', 'wdm_instructor_role' ); ?>
							</th>
							<th>
								<?php echo __( 'Actual Price', 'wdm_instructor_role' ); ?>
							</th>
							<th>
								<?php echo __( 'Commission Price', 'wdm_instructor_role' ); ?>
							</th>
							<th>
								<?php echo __( 'Product Type', 'wdm_instructor_role' ); ?>
							</th>

						</tr>
						<?php do_action( 'wdm_commission_report_table_header', $instructor_id ); ?>
					</thead>
					<tbody>
						<?php
						$sql     = "SELECT * FROM {$wpdb->prefix}wdm_instructor_commission WHERE user_id = $instructor_id";
						$results = $wpdb->get_results( $sql );

						if ( ! empty( $results ) ) {
							$amount_paid = 0;
							foreach ( $results as $value ) {
								$amount_paid += $value->commission_price;

								?>
								<tr>
									<td>
									<?php $this->wdmcheckProductType( $value ); ?>
									</td>
									<td><a target="_new_blank" 
									<?php
									echo $this->wdmGetPostEditLink( $value->product_id );
									?>
								>
								<?php
								echo $this->wdmGetPostTitle( $value->product_id );
								?>
					</a></td>
									<td>
									<?php
									echo $value->actual_price;
									?>
								</td>
									<td>
									<?php
									echo $value->commission_price;
									?>
								</td>
									<td>
									<?php
									echo $value->product_type;
									?>
								</td>
								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td>
								<?php
								echo __( 'No record found!', 'wdm_instructor_role' );
								?>
										</td>
										</tr>
												<?php
						}
						do_action( 'wdm_commission_report_table', $instructor_id );
						?>
					</tbody>
					<tfoot >
						<?php
						if ( ! empty( $results ) ) {
							$paid_total = get_user_meta( $instructor_id, 'wdm_total_amount_paid', true );
							if ( '' == $paid_total ) {
								$paid_total = 0;
							}

							$amount_paid = round( ( $amount_paid - $paid_total ), 2 );
							$amount_paid = max( $amount_paid, 0 );
							?>
							<tr>
								<td></td>
								<th style="color:black;font-weight: bold;">
									<?php echo __( 'Paid Earnings', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<a>
										<span id="wdm_total_amount_paid"><?php echo esc_attr( $paid_total ); ?></span>
									</a>
								</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td></td>
								<th style="color:black;font-weight: bold;">
									<?php esc_html_e( 'Unpaid Earnings', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<span id="wdm_amount_paid"><?php echo $amount_paid; ?></span>
									<?php if ( 0 != $amount_paid && is_super_admin() ) : ?>
										<a href="#" class="button-primary" id="wdm_pay_amount">
											<?php esc_html_e( 'Pay', 'wdm_instructor_role' ); ?>
										</a>
									<?php endif; ?>
								</td>
								<td></td>
								<td></td>
							</tr>
						<?php } ?>
						<tr>
							<td colspan="5" style="border-radius: 0 0 6px 6px;">
								<div class="pagination pagination-centered hide-if-no-paging"></div>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<?php
			// Display commission payment popup templates for admin.
			if ( is_super_admin() ) {
				ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . '/templates/ir-commission-payment.template.php',
					array(
						'instructor_id' => $instructor_id,
					)
				);
			}

			/**
			 * After Instructor Commission Report End
			 *
			 * Run after the instructor comission report is displayed.
			 *
			 * @since 3.4.0
			 *
			 * @param int $instructor_id    User ID of the instructor.
			 */
			do_action( 'ir_action_commission_report_end', $instructor_id );
		}

		public function wdmcheckProductType( $value ) {
			if ( is_super_admin() ) {
				if ( $value->product_type == 'EDD' ) {
					?>
						<a
							href="<?php echo is_super_admin() ? esc_attr( site_url( 'wp-admin/edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $value->order_id ) ) : '#'; ?>"
							target="<?php echo is_super_admin() ? '_new_blank' : ''; ?>">
							<?php echo esc_html( $value->order_id ); ?>
						</a>
					<?php
				} else {
					?>
					<a
						href="<?php echo is_super_admin() ? esc_attr( site_url( 'wp-admin/post.php?post=' . $value->order_id . '&action=edit' ) ) : '#'; ?>"
						target="<?php echo is_super_admin() ? '_new_blank' : ''; ?>">
							<?php echo esc_html( $value->order_id ); ?>
						</a>
					<?php
				}
			} else {
				echo esc_html( $value->order_id );
			}
		}

		/**
		 * [Updating instructor commission using ajax].
		 *
		 * @return [string] [status]
		 *
		 * @since 2.4.0
		 */
		public function wdm_update_commission() {
			$percentage    = $_POST['commission'];
			$instructor_id = $_POST['instructor_id'];
			if ( wdm_is_instructor( $instructor_id ) ) {
				update_user_meta( $instructor_id, 'wdm_commission_percentage', $percentage );
				echo __( 'Updated successfully', 'wdm_instructor_role' );
			} else {
				echo __( 'Oops something went wrong', 'wdm_instructor_role' );
			}
			die();
		}

		public function userIdExists( $user_id ) {
			global $wpdb;
			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users WHERE ID = %d", $user_id ) );

			return empty( $count ) || 1 > $count ? false : true;
		}

		public function wdmGetPostTitle( $postID ) {
			$title = get_the_title( $postID );
			if ( empty( $title ) ) {
				return __( sprintf( 'Product/ %s has been deleted !', \LearnDash_Custom_Label::get_label( 'Course' ) ), 'wdm_instructor_role' );
			}
			return $title;
		}

		public function wdmGetPostEditLink( $postId = 0 ) {
			if ( get_post_status( $postId ) === false ) {
				return 'style="pointer-event:none;"';
			}
			return 'href="' . site_url( 'wp-admin/post.php?post=' . $postId . '&action=edit' ) . '"';

		}

		/**
		 *   @since: version 2.1
		 *   Display of HTML content on Instructor Email Settings page.
		 *   This function is called from file "commission.php" in function instuctor_page_callback()
		 */
		public function wdmir_instructor_email_settings() {
			// Shortcuts used in naming variables and elements
			// cra_ = course review admin
			// cri_ = course review instructor
			// pra_ = prodoct review admin
			// pri_ = product review instructor

			$email_settings = get_option( '_wdmir_email_settings' );

			?>
			<div class="wrap wdmir-email-wrap">
				<h2><?php esc_html_e( 'E-mail Settings', 'wdm_instructor_role' ); ?></h2>
					<form method="post" action="">
						<?php wp_nonce_field( 'ins_email_setting_nonce_action', 'ins_email_setting_nonce', true, true ); ?>
						<?php do_action( 'wdmir_email_settings_before' ); ?>
						<!-- Emails to admin about course review - starts -->
						<div id="wdmir_approval_email">
							<ul>
								<li>
									<a href="#wdmir_course_email"><?php \LearnDash_Custom_Label::get_label( 'course' ); ?></a>
								</li>
								<?php if ( wdmCheckWooDependency() ) : ?>
									<li>
										<a href="#wdmir_product_email">
										<?php esc_html_e( 'Product', 'wdm_instructor_role' ); ?>
										</a>
									</li>
								<?php endif; ?>
								<?php if ( wdmCheckEDDDependency() ) : ?>
									<li>
										<a href="#wdmir_download_email"><?php esc_html_e( 'Download', 'wdm_instructor_role' ); ?></a>
									</li>
								<?php endif; ?>
							</ul>
							<div id="wdmir_course_email">
								<div class="wdmir-section">
									<div class="wdmir-email-heading">
										<span class="heading">
											<?php esc_html_e( sprintf( '%s Update Notification To Admin', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' ); ?>
										</span>
										<a class="wdmir-shortcodes" href="javascript:void(0);"><?php esc_html_e( 'Shortcodes', 'wdm_instructor_role' ); ?></a>
										<div class="wdmir-shortcode-callback">
											<a href="javascript:void(0);" class="wdmir-shortcode-close"><span class="dashicons dashicons-no"></span></a>
											<br />
											<table class="wdmir-shortcode-tbl" cellpadding="4" cellspacing="2">
												<tr>
													<td><code>[ins_profile_link]</code></td>
													<td><?php esc_html_e( 'Instructor Profile Link', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[ins_first_name]</code></td>
													<td><?php esc_html_e( 'Instructor First Name', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[ins_last_name]</code></td>
													<td><?php esc_html_e( 'Instructor Last Name', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[ins_login]</code></td>
													<td><?php esc_html_e( 'Instructor Login ID', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[course_id]</code></td>
													<td><?php esc_html_e( sprintf( '%s ID', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[course_title]</code></td>
													<td><?php esc_html_e( sprintf( '%s Title', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[course_content_title]</code></td>
													<td><?php esc_html_e( sprintf( 'Title of an edited %s content', \LearnDash_Custom_Label::label_to_lower( 'course' ) ), 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[course_content_edit]</code></td>
													<td><?php esc_html_e( sprintf( 'Dashboard link of a edited %s content', \LearnDash_Custom_Label::label_to_lower( 'course' ) ), 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[course_update_datetime]</code></td>
													<td><?php esc_html_e( sprintf( 'Updated date and time of a %s', \LearnDash_Custom_Label::label_to_lower( 'course' ) ), 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[content_update_datetime]</code></td>
													<td><?php esc_html_e( 'Updated date and time of a content', 'wdm_instructor_role' ); ?></td>
												</tr>
											</table>
										</div>
									</div>
									<table class="form-table wdmir-form-table">
										<tbody>
											<tr>
												<th scope="row">
													<label for="cra_emails"><?php esc_html_e( 'Admin E-Mail ID', 'wdm_instructor_role' ); ?></label>
												</th>
												<td>
													<input class="wdmir-email-box" name="cra_emails" type="text" id="cra_emails" 
													value="<?php echo esc_attr( $email_settings['cra_emails'] ); ?>">
													<p class="description"><?php esc_html_e( sprintf( 'Comma separated E-mail IDs to send %s review notification.', \LearnDash_Custom_Label::label_to_lower( 'course' ) ), 'wdm_instructor_role' ); ?></p>
												</td>
											</tr>
											<tr>
												<th scope="row">
													<label for="cra_subject"><?php esc_html_e( 'Subject', 'wdm_instructor_role' ); ?></label>
												</th>
												<td>
													<input
														class="wdmir-full-textbox"
														name="cra_subject"
														type="text"
														id="cra_subject"
														value="<?php echo esc_attr( $email_settings['cra_subject'] ); ?>">
												</td>
											</tr>
											<tr>
												<th scope="row">
													<label for="cra_mail_content"><?php esc_html_e( sprintf( 'Review %s E-Mail Content', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' ); ?></label>
												</th>
												<td>
													<?php
														$editor_settings = array(
															'textarea_rows' => 100,
															'editor_height' => 200,
														);
														wp_editor(
															( $email_settings['cra_mail_content'] ? wp_unslash( $email_settings['cra_mail_content'] ) : '' ),
															'cra_mail_content',
															$editor_settings
														);
													?>
												</td>
											</tr>
										</tbody>
									</table>
									<?php
										// $template = wdmir_post_shortcodes( 9, $email_settings[ 'cra_mail_content' ] );
										// echo $template = wdmir_post_shortcodes( 18, $template, true );
									?>
								</div>
								<!-- Emails to admin about course review - ends -->
								<!-- Emails to instructor about course review - starts -->
								<div class="wdmir-section">
									<div class="wdmir-email-heading">
										<span class="heading"><?php esc_html_e( 'Course Update Notification To Instructor', 'wdm_instructor_role' ); ?></span>
										<a class="wdmir-shortcodes" href="javascript:void(0);"><?php esc_html_e( 'Shortcodes', 'wdm_instructor_role' ); ?></a>
										<div class="wdmir-shortcode-callback">
											<a href="javascript:void(0);" class="wdmir-shortcode-close"><span class="dashicons dashicons-no"></span></a><br />
											<table class="wdmir-shortcode-tbl" cellpadding="4" cellspacing="2">
												<tr>
													<td><code>[ins_first_name]</code></td>
													<td><?php esc_html_e( 'Instructor First Name', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[ins_last_name]</code></td>
													<td><?php esc_html_e( 'Instructor Last Name', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[ins_login]</code></td>
													<td><?php esc_html_e( 'Instructor Login ID', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[course_id]</code></td>
													<td><?php esc_html_e( sprintf( '%s ID', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' ); ?></td>
												</tr>
										<tr>
											<td><code>[course_title]</code></td>
											<td><?php esc_html_e( sprintf( '%s Title', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' ); ?></td>
										</tr>
										<tr>
											<td><code>[course_content_title]</code></td>
											<td><?php esc_html_e( sprintf( 'Title of a edited %s content', \LearnDash_Custom_Label::label_to_lower( 'course' ) ), 'wdm_instructor_role' ); ?></td>
										</tr>
										<tr>
											<td><code>[course_permalink]</code></td>
											<td><?php esc_html_e( sprintf( 'Permalink of a %s', \LearnDash_Custom_Label::label_to_lower( 'course' ) ), 'wdm_instructor_role' ); ?></td>
										</tr>
										<tr>
											<td><code>[content_permalink]</code></td>
											<td><?php esc_html_e( 'Permalink of a content', 'wdm_instructor_role' ); ?></td>
										</tr>
										<tr>
											<td><code>[course_content_edit]</code></td>
											<td><?php esc_html_e( sprintf( 'Dashboard link of a edited %s content', \LearnDash_Custom_Label::label_to_lower( 'course' ) ), 'wdm_instructor_role' ); ?></td>
										</tr>
										<tr>
											<td><code>[approved_datetime]</code></td>
											<td><?php esc_html_e( 'Approved date and time of a content', 'wdm_instructor_role' ); ?></td>
										</tr>
									</table>
								</div>
							</div>
							<table class="form-table wdmir-form-table">
								<tbody>
									<tr>
										<th scope="row">
											<label for="cri_subject"><?php esc_html_e( 'Subject', 'wdm_instructor_role' ); ?></label>
										</th>
										<td>
											<input 
												class="wdmir-full-textbox"
												name="cri_subject"
												type="text"
												id="cri_subject"
												value="<?php echo esc_attr( $email_settings['cri_subject'] ); ?>">
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="cri_mail_content"><?php esc_html_e( sprintf( 'Review %s E-Mail Content', \LearnDash_Custom_Label::get_label( 'course' ) ), 'wdm_instructor_role' ); ?></label>
										</th>
										<td>
											<?php
												$editor_settings = array(
													'textarea_rows' => 100,
													'editor_height' => 200,
												);
												wp_editor(
													( $email_settings['cri_mail_content'] ? wp_unslash( $email_settings['cri_mail_content'] ) : '' ),
													'cri_mail_content',
													$editor_settings
												);
											?>
										</td>
									</tr>
								</tbody>
							</table>
							<?php
								// $template = wdmir_post_shortcodes( 9, $email_settings[ 'cri_mail_content' ] );
								// echo $template = wdmir_post_shortcodes( 18, $template, true );
							?>
						</div>
					</div>
						<!-- Emails to instructor about course review - ends -->

						<?php if ( wdmCheckWooDependency() ) : ?>
							<!-- Emails to admin about product update - starts -->
							<div id="wdmir_product_email">
								<div class="wdmir-section">
									<div class="wdmir-email-heading">
										<span class="heading"><?php esc_html_e( 'Product Update Notification To Admin', 'wdm_instructor_role' ); ?></span>
										<a class="wdmir-shortcodes" href="javascript:void(0);">Shortcodes</a>
										<div class="wdmir-shortcode-callback">
											<a href="javascript:void(0);" class="wdmir-shortcode-close"><span class="dashicons dashicons-no"></span></a><br />
											<table class="wdmir-shortcode-tbl" cellpadding="4" cellspacing="2">
												<tr>
													<td><code>[ins_profile_link]</code></td>
													<td><?php esc_html_e( 'Instructor Profile Link', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[ins_first_name]</code></td>
													<td><?php esc_html_e( 'Instructor First Name', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[ins_last_name]</code></td>
													<td><?php esc_html_e( 'Instructor Last Name', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[ins_login]</code></td>
													<td><?php esc_html_e( 'Instructor Login ID', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[product_id]</code></td>
													<td><?php esc_html_e( 'Product ID', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[product_title]</code></td>
													<td><?php esc_html_e( 'Product Title', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[product_permalink]</code></td>
													<td><?php esc_html_e( 'Permalink of a product', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[product_update_datetime]</code></td>
													<td><?php esc_html_e( 'Updated date and time of a product', 'wdm_instructor_role' ); ?></td>
												</tr>
											</table>
										</div>
									</div>
									<table class="form-table wdmir-form-table">
										<tbody>
											<tr>
												<th scope="row">
													<label for="pra_emails"><?php esc_html_e( 'Admin E-Mail ID', 'wdm_instructor_role' ); ?></label>
												</th>
												<td>
													<input 
														class="wdmir-email-box"
														name="pra_emails"
														type="text"
														id="pra_emails"
														value="<?php echo esc_attr( $email_settings['pra_emails'] ); ?>"
													/>
													<p class="description"><?php esc_html_e( 'Comma separated E-mail IDs to send product update notification.', 'wdm_instructor_role' ); ?></p>
												</td>
											</tr>
											<tr>
												<th scope="row">
													<label for="pra_subject"><?php esc_html_e( 'Subject', 'wdm_instructor_role' ); ?></label>
												</th>
												<td>
													<input
														class="wdmir-full-textbox"
														name="pra_subject"
														type="text"
														id="pra_subject"
														value="<?php echo esc_attr( $email_settings['pra_subject'] ); ?>"
													/>
												</td>
											</tr>
											<tr>
												<th scope="row">
													<label for="pra_mail_content"><?php esc_html_e( 'Product Update E-Mail Content', 'wdm_instructor_role' ); ?></label>
												</th>
												<td>
													<?php
														$editor_settings = array(
															'textarea_rows' => 100,
															'editor_height' => 200,
														);
														wp_editor(
															( $email_settings['pra_mail_content'] ? wp_unslash( $email_settings['pra_mail_content'] ) : '' ),
															'pra_mail_content',
															$editor_settings
														);
													?>
												</td>
											</tr>
										</tbody>
									</table>
									<?php
										// echo $template = wdmir_post_shortcodes( 27, $email_settings[ 'pra_mail_content' ] );
										// echo $template = wdmir_post_shortcodes( 18, $template, true );
									?>
								</div>
								<div class="wdmir-section">
									<div class="wdmir-email-heading">
										<span class="heading">
											<?php echo __( 'Product Update Notification To Instructor', 'wdm_instructor_role' ); ?>
										</span>
										<a class="wdmir-shortcodes" href="javascript:void(0);">Shortcodes</a>
										<div class="wdmir-shortcode-callback">
											<a href="javascript:void(0);" class="wdmir-shortcode-close"><span class="dashicons dashicons-no"></span></a><br />
											<table class="wdmir-shortcode-tbl" cellpadding="4" cellspacing="2">
												<tr>
													<td><code>[ins_profile_link]</code></td>
													<td>
													<?php
													echo __( 'Instructor Profile Link', 'wdm_instructor_role' );
													?>
													</td>
												</tr>
												<tr>
													<td><code>[ins_first_name]</code></td>
													<td>
													<?php
													echo __( 'Instructor First Name', 'wdm_instructor_role' );
													?>
													</td>
												</tr>
												<tr>
													<td><code>[ins_last_name]</code></td>
													<td>
													<?php
													echo __( 'Instructor Last Name', 'wdm_instructor_role' );
													?>
													</td>
												</tr>
												<tr>
													<td><code>[ins_login]</code></td>
													<td>
													<?php
													echo __( 'Instructor Login ID', 'wdm_instructor_role' );
													?>
													</td>
												</tr>
												<tr>
													<td><code>[product_id]</code></td>
													<td>
													<?php
													echo __( 'Product ID', 'wdm_instructor_role' );
													?>
													</td>
												</tr>
												<tr>
													<td><code>[product_title]</code></td>
													<td>
													<?php
													echo __( 'Product Title', 'wdm_instructor_role' );
													?>
													</td>
												</tr>
												<tr>
													<td><code>[product_permalink]</code></td>
													<td>
													<?php
													echo __( 'Permalink of a product', 'wdm_instructor_role' );
													?>
													</td>
												</tr>
												<tr>
													<td><code>[product_update_datetime]</code></td>
													<td>
													<?php
													echo __( 'Updated date and time of a product', 'wdm_instructor_role' );
													?>
													</td>
												</tr>
											</table>
										</div>
									</div>
									<table class="form-table wdmir-form-table">
										<tbody>
											<tr>
												<th scope="row">
													<label for="pri_subject">
														<?php esc_html_e( 'Subject', 'wdm_instructor_role' ); ?>
													</label>
												</th>
												<td>
													<input
														class="wdmir-full-textbox"
														name="pri_subject"
														type="text"
														id="pri_subject"
														value="<?php echo $email_settings['pri_subject']; ?>">
												</td>
											</tr>
											<tr>
												<th scope="row">
													<label for="pri_mail_content"><?php esc_html_e( 'Product Update E-Mail Content', 'wdm_instructor_role' ); ?></label>
												</th>
												<td>
												<?php
													$editor_settings = array(
														'textarea_rows' => 100,
														'editor_height' => 200,
													);
													wp_editor(
														$this->wdmRemoveSlashs( $email_settings['pri_mail_content'] ),
														'pri_mail_content',
														$editor_settings
													);
												?>
												</td>
											</tr>
										</tbody>
									</table>
									<?php
										// echo $template = wdmir_post_shortcodes( 27, $email_settings[ 'pri_mail_content' ] );
										// echo $template = wdmir_post_shortcodes( 18, $template, true );
									?>
								</div>
							</div>

						<!-- Emails to admin about product update - ends -->
						<?php endif; ?>
						<?php if ( wdmCheckEDDDependency() ) : ?>
							<!-- Emails to admin about download update - starts -->
							<div id="wdmir_download_email">
								<div class="wdmir-section">
									<div class="wdmir-email-heading">
										<span class="heading"><?php esc_html_e( 'Download Update Notification To Admin', 'wdm_instructor_role' ); ?></span>
										<a class="wdmir-shortcodes" href="javascript:void(0);"><?php esc_html_e( 'Shortcodes', 'wdm_instructor_role' ); ?></a>
										<div class="wdmir-shortcode-callback">
											<a href="javascript:void(0);" class="wdmir-shortcode-close"><span class="dashicons dashicons-no"></span></a>
											<br />
											<table class="wdmir-shortcode-tbl" cellpadding="4" cellspacing="2">
												<tr>
													<td><code>[ins_profile_link]</code></td>
													<td><?php esc_html_e( 'Instructor Profile Link', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[ins_first_name]</code></td>
													<td><?php esc_html_e( 'Instructor First Name', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[ins_last_name]</code></td>
													<td><?php esc_html_e( 'Instructor Last Name', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[ins_login]</code></td>
													<td><?php esc_html_e( 'Instructor Login ID', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[download_id]</code></td>
													<td><?php esc_html_e( 'Download ID', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[download_title]</code></td>
													<td><?php esc_html_e( 'Download Title', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[download_permalink]</code></td>
													<td><?php esc_html_e( 'Permalink of a download', 'wdm_instructor_role' ); ?></td>
												</tr>
												<tr>
													<td><code>[download_update_datetime]</code></td>
													<td><?php esc_html_e( 'Updated date and time of a download', 'wdm_instructor_role' ); ?></td>
												</tr>
											</table>
										</div>
									</div>
									<table class="form-table wdmir-form-table">
											<tbody>
												<tr>
													<th scope="row">
														<label for="dra_emails"><?php esc_html_e( 'Admin E-Mail ID', 'wdm_instructor_role' ); ?></label>
													</th>
													<td>
														<input
															class="wdmir-email-box"
															name="dra_emails"
															type="text"
															id="dra_emails"
															value="<?php echo esc_attr( $email_settings['dra_emails'] ); ?>"
														/>
														<p class="description"><?php esc_html_e( 'Comma separated E-mail IDs to send download update notification.', 'wdm_instructor_role' ); ?>
														</p>
													</td>
												</tr>
												<tr>
													<th scope="row">
														<label for="dra_subject"><?php esc_html_e( 'Subject', 'wdm_instructor_role' ); ?></label>
													</th>
													<td>
														<input
															class="wdmir-full-textbox"
															name="dra_subject"
															type="text"
															id="dra_subject"
															value="<?php echo esc_attr( $email_settings['dra_subject'] ); ?>"
														/>
													</td>
												</tr>
												<tr>
													<th scope="row">
														<label for="dra_mail_content"><?php esc_html_e( 'Download Update E-Mail Content', 'wdm_instructor_role' ); ?></label>
													</th>
													<td>
														<?php
															$editor_settings = array(
																'textarea_rows' => 100,
																'editor_height' => 200,
															);
															wp_editor(
																( $email_settings['dra_mail_content'] ? wp_unslash( $email_settings['dra_mail_content'] ) : '' ),
																'dra_mail_content',
																$editor_settings
															);
														?>
													</td>
												</tr>
											</tbody>
										</table>
									<?php
									// echo $template = wdmir_post_shortcodes( 27, $email_settings[ 'pra_mail_content' ] );
									// echo $template = wdmir_post_shortcodes( 18, $template, true );
									?>
									</div>
									<div class="wdmir-section">
										<div class="wdmir-email-heading">
											<span class="heading"><?php esc_html_e( 'Download Update Notification To Instructor', 'wdm_instructor_role' ); ?></span>
											<a class="wdmir-shortcodes" href="javascript:void(0);">Shortcodes</a>
												<div class="wdmir-shortcode-callback">
													<a href="javascript:void(0);" class="wdmir-shortcode-close"><span class="dashicons dashicons-no"></span>
												</a>
												<br />
												<table class="wdmir-shortcode-tbl" cellpadding="4" cellspacing="2">
													<tr>
														<td><code>[ins_profile_link]</code></td>
														<td><?php esc_html_e( 'Instructor Profile Link', 'wdm_instructor_role' ); ?></td>
													</tr>
													<tr>
														<td><code>[ins_first_name]</code></td>
														<td><?php esc_html_e( 'Instructor First Name', 'wdm_instructor_role' ); ?></td>
													</tr>
													<tr>
														<td><code>[ins_last_name]</code></td>
														<td><?php esc_html_e( 'Instructor Last Name', 'wdm_instructor_role' ); ?></td>
													</tr>
													<tr>
														<td><code>[ins_login]</code></td>
														<td><?php esc_html_e( 'Instructor Login ID', 'wdm_instructor_role' ); ?></td>
													</tr>
													<tr>
														<td><code>[download_id]</code></td>
														<td><?php esc_html_e( 'Download ID', 'wdm_instructor_role' ); ?></td>
													</tr>
													<tr>
														<td><code>[download_title]</code></td>
														<td><?php esc_html_e( 'Download Title', 'wdm_instructor_role' ); ?></td>
													</tr>
													<tr>
														<td><code>[download_permalink]</code></td>
														<td><?php esc_html_e( 'Permalink of a download', 'wdm_instructor_role' ); ?></td>
													</tr>
													<tr>
														<td><code>[download_update_datetime]</code></td>
														<td><?php esc_html_e( 'Updated date and time of a download', 'wdm_instructor_role' ); ?></td>
													</tr>
												</table>
											</div>
										</div>
										<table class="form-table wdmir-form-table">
											<tbody>
											<tr>
												<th scope="row">
													<label for="dri_subject">
														<?php esc_html_e( 'Subject', 'wdm_instructor_role' ); ?>
													</label>
												</th>
												<td>
													<input
													class="wdmir-full-textbox"
													name="dri_subject"
													type="text"
													id="dri_subject"
													value="<?php echo esc_attr( $email_settings['dri_subject'] ); ?>">
												</td>
											</tr>
											<tr>
												<th scope="row">
													<label for="dri_mail_content"><?php esc_html_e( 'Download Update E-Mail Content', 'wdm_instructor_role' ); ?></label>
												</th>
												<td>
													<?php
														$editor_settings = array(
															'textarea_rows' => 100,
															'editor_height' => 200,
														);
														wp_editor(
															$this->wdmRemoveSlashs( $email_settings['dri_mail_content'] ),
															'dri_mail_content',
															$editor_settings
														);
													?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<!-- Emails to admin about download update - ends -->
						<?php endif; ?>
					</div>
					<!-- Tabs - ends -->
					<?php do_action( 'wdmir_email_settings_after' ); ?>
					<p class="submit">
						<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'wdm_instructor_role' ); ?>">
					</p>
				</form>
			</div>
			<?php
			wp_enqueue_script( 'wdmir_tabs_js', '//code.jquery.com/ui/1.12.0/jquery-ui.js', array( 'jquery' ) );
			wp_enqueue_style( 'wdmir_tabs_css', '//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css' );
			wp_enqueue_script( 'wdm_email_form', plugin_dir_url( __DIR__ ) . 'js/wdm_email_form.js', array( 'jquery' ), '0.0.1' );

		}

		public function wdmRemoveSlashs( $email_content ) {
			if ( ! empty( $email_content ) ) {
				return wp_unslash( $email_content );
			}

			return '';
		}

		/*
		*   @since version 2.1
		*   Saving HTML form content of Instructor Email Settings page.
		*
		*/
		public function wdmir_email_settings_save() {
			if ( isset( $_POST['ins_email_setting_nonce'] ) &&
									wp_verify_nonce( $_POST['ins_email_setting_nonce'], 'ins_email_setting_nonce_action' ) &&
									is_admin() ) {
				$email_settings = array();
				do_action( 'wdmir_email_settings_save_before' );

				// Course Review To Admin - starts
				$email_settings['cra_emails'] = '';
				$email_settings['cra_emails'] = $this->checkIsSets( $_POST['cra_emails'] );

				$email_settings['cra_subject'] = $this->checkIsSets( $_POST['cra_subject'] );

				$email_settings['cra_mail_content'] = $this->checkIsSets( $_POST['cra_mail_content'], 1 );

				// Course Review To Instructor - starts
				$email_settings['cri_subject'] = $this->checkIsSets( $_POST['cri_subject'] );

				$email_settings['cri_mail_content'] = '';
				if ( isset( $_POST['cri_mail_content'] ) ) {
					  $email_settings['cri_mail_content'] = $this->checkIsSets( $_POST['cri_mail_content'], 1 );
				}

				// Course Review To Instructor - ends

				// Product Review To Admin - starts
				$email_settings['pra_emails'] = '';
				if ( isset( $_POST['pra_emails'] ) ) {
					 $email_settings['pra_emails'] = $_POST['pra_emails'];
				}

				$email_settings['pra_subject'] = '';
				if ( isset( $_POST['pra_subject'] ) ) {
					$email_settings['pra_subject'] = $this->checkIsSets( $_POST['pra_subject'] );
				}

				$email_settings['pra_mail_content'] = '';
				if ( isset( $_POST['pra_mail_content'] ) ) {
					$email_settings['pra_mail_content'] = $this->checkIsSets( $_POST['pra_mail_content'], 1 );
				}
				// Product Review To Admin - ends

				// Product Review To Instructor - starts
				$email_settings['pri_subject'] = '';
				if ( isset( $_POST['pri_subject'] ) ) {
					$email_settings['pri_subject'] = $_POST['pri_subject'];
				}

				$email_settings['pri_mail_content'] = '';
				if ( isset( $_POST['pri_mail_content'] ) ) {
					$email_settings['pri_mail_content'] = $this->checkIsSets( $_POST['pri_mail_content'], 1 );
				}
				// Product Review To Instructor - ends

				// Download Review To Admin - starts v3.0.0
				$email_settings['dra_emails'] = '';
				if ( isset( $_POST['dra_emails'] ) ) {
					$email_settings['dra_emails'] = $_POST['dra_emails'];
				}

				$email_settings['dra_subject'] = '';
				if ( isset( $_POST['dra_subject'] ) ) {
					$email_settings['dra_subject'] = $_POST['dra_subject'];
				}

				$email_settings['dra_mail_content'] = '';
				if ( isset( $_POST['dra_mail_content'] ) ) {
					$email_settings['dra_mail_content'] = $this->checkIsSets( $_POST['dra_mail_content'], 1 );
				}
				// Download Review To Admin - ends

				// Download Review To Instructor - starts
				$email_settings['dri_subject'] = '';
				if ( isset( $_POST['dri_subject'] ) ) {
					$email_settings['dri_subject'] = $_POST['dri_subject'];
				}

				$email_settings['dri_mail_content'] = '';
				if ( isset( $_POST['dri_mail_content'] ) ) {
					$email_settings['dri_mail_content'] = $this->checkIsSets( $_POST['dri_mail_content'], 1 );
				}
				// Download Review To Instructor - ends

				// Saving email settings option
				update_option( '_wdmir_email_settings', $email_settings );

				do_action( 'wdmir_email_settings_save_after' );

				wp_redirect( $_POST['_wp_http_referer'] );
			}
		}

		public function checkIsSets( $value, $autop = false ) {
			if ( $autop ) {
				$value = wpautop( $value );
			}
			if ( isset( $value ) ) {
				return $value;
			}

			return '';
		}

		public function save_instructor_mail_template_data() {
			if ( ! is_user_logged_in() ) {
				return false;
			}

			$current_user_id = get_current_user_id();
			if ( isset( $_POST['instructor_email_update'] ) ) {
				$email_template_data = array();
				if ( isset( $_POST['instructor_email_sub'] ) ) {
					$email_template_data['mail_sub'] = $_POST['instructor_email_sub'];
				}
				if ( isset( $_POST['instructor_email_message'] ) ) {
					$email_template_data['mail_content'] = $_POST['instructor_email_message'];
				}

				update_user_meta( $current_user_id, 'instructor_email_template', $email_template_data );
			}
		}

		public function send_email_to_instructor( $data ) {
			if ( ! isset( $data ) ) {
				return false;
			}
			if ( wdm_is_instructor( $data['quiz']->post_author ) ) {
				$wdmid_admin_setting = get_option( '_wdmir_admin_settings', array() );
				// send mail to instructor if admin enable instruction mail option.
				if ( ! empty( $wdmid_admin_setting ) && $wdmid_admin_setting['instructor_mail'] == 1 ) {
					$current_user         = get_current_user_id();
					$current_user_details = get_userdata( $current_user );
					$quiz                 = get_post( $data['quiz'] );
					if ( empty( $quiz ) ) {
						return;
					}
					$wl8_qz_ins_details  = get_userdata( $quiz->post_author );
					$email_template_data = get_user_meta( $quiz->post_author, 'instructor_email_template', true );
					$mail_sub            = '';
					$mail_content        = '';
					if ( ! empty( $email_template_data ) ) {
						$mail_sub     = $email_template_data['mail_sub'];
						$mail_content = $email_template_data['mail_content'];
					}

					if ( empty( $mail_sub ) ) {
						$mail_sub = 'User attempt quiz';
					} else {
						$mail_sub = str_replace( '$userid', $current_user, $mail_sub );
						$mail_sub = str_replace( '$username', $current_user_details->user_login, $mail_sub );
						$mail_sub = str_replace( '$useremail', $current_user_details->user_email, $mail_sub );
						$mail_sub = str_replace( '$quizname', $quiz->post_title, $mail_sub );
						$mail_sub = str_replace( '$result', $data['percentage'], $mail_sub );
						$mail_sub = str_replace( '$points', $data['points'], $mail_sub );
					}
					// wl8 changes ends here.

					if ( empty( $mail_content ) ) {
						$mail_content  = 'User has attempt following quiz -<br/>';
						$mail_content .= 'UserName: ' . $current_user_details->user_login . '<br/>';
						$mail_content .= 'Email: ' . $current_user_details->user_email . '<br/>';
						$mail_content .= 'Quiz title: ' . $quiz->post_title . '<br/>';
						if ( $data['pass'] ) {
							$mail_sub .= 'Result: Passed ';
						} else {
							$mail_sub .= 'Result: Failed';
						}
					} else {
						$mail_content = str_replace( '$userid', $current_user, $mail_content );
						$mail_content = str_replace( '$username', $current_user_details->user_login, $mail_content );
						$mail_content = str_replace( '$useremail', $current_user_details->user_email, $mail_content );
						$mail_content = str_replace( '$quizname', $quiz->post_title, $mail_content );
						$mail_content = str_replace( '$result', $data['percentage'], $mail_content );
						$mail_content = str_replace( '$points', $data['points'], $mail_content );
					}

					add_filter( 'wp_mail_content_type', array( $this, 'wdm_ir_set_html_content_type' ), 1 );
					wp_mail( $wl8_qz_ins_details->user_email, $mail_sub, $mail_content );
				}
			}
		}

		public function wdm_ir_set_html_content_type( $content_type ) {
			 unset( $content_type );

			return 'text/html';
		}

		public function wdmir_individual_instructor_email_setting() {
			$current_user_id  = get_current_user_id();
			$prev_stored_data = get_user_meta( $current_user_id, 'instructor_email_template', true );
			?>
			<div class="wl8qcn-email-form">
			<form method="post" action="">
				<div class="wl8qcn-email-heading">
					<h2><?php echo __( 'Instructor Email', 'wdm_instructor_role' ); ?></h2>
					<span class="wl8qcn-email-desc">
						<?php _e( sprintf( '(Email to be sent to instructor when a student completes one of the %1$s from your %2$s)', \LearnDash_Custom_Label::get_label( 'quizzes' ), \LearnDash_Custom_Label::get_label( 'courses' ) ), 'wdm_instructor_role' ); ?>
					</span>
				</div>
				<div class="wl8qcn-email-sub">
				<label for="email">
				<?php
				echo __( 'Email Subject:', 'wdm_instructor_role' );
				?>
			</label>
					<input id="instructor_email_sub" rows="5" class="instructor_email_sub" name="instructor_email_sub" value="<?php echo ! empty( $prev_stored_data ) ? $prev_stored_data['mail_sub'] : ''; ?>">
				</div>

				<div class="wl8qcn-email-content">
				<label for="text">
				<?php
				echo __( 'Email Message:', 'wdm_instructor_role' );
				?>
				</label>
				<?php
				$content = '';
				if ( ! empty( $prev_stored_data ) ) {
					$content = $prev_stored_data['mail_content'];
				}
				$editor_id = 'instructor_email_message';
				wp_editor( $content, $editor_id );
				?>
				</div>
				<div id="instructor_email_template_variable">
				<h4>
				<?php
				echo __( 'ALLOWED VARIABLES', 'wdm_instructor_role' );
				?>
				</h4>
				<table>
				<?php
				$allowed_vars = $this->wl8GetAllowedVars();
				foreach ( $allowed_vars as $desc => $var ) {
					echo "<tr><td><code>$var</code></td><td>:</td><td>$desc</td></tr>";
				}
				?>
				</table>
				</div>
				<br/>
				<input id="instructor_email_update" name="instructor_email_update" class="button button-primary" type="submit" value="save"/>
			</form>
			</div>
			<?php

		}

		/**
		 * Function returns allowed variable list.
		 */
		public function wl8GetAllowedVars() {
			// allowed variables...
			$vars = array(
				'Userid'            => '$userid',
				'Username'          => '$username',
				'User\'s email'     => '$useremail',
				'Quiz name'         => '$quizname',
				'Result in percent' => '$result',
				'Reached points'    => '$points',
			);

			return $vars;
		}

		/**
		 *   @since version 2.1
		 *   Display of HTML content on Instructor Settings page.
		 *   This function is called from file "commission.php" in function instuctor_page_callback()
		 */
		public function wdmir_instructor_settings() {
			?>
			<div class="wrap">
			<h2><?php echo __( 'Instructor Settings', 'wdm_instructor_role' ); ?></h2>
			<form method="post" action="">
			<?php
			wp_nonce_field( 'instructor_setting_nonce_action', 'instructor_setting_nonce', true, true );
			do_action( 'wdmir_settings_before_table' );
			?>
				<table class="form-table wdmir-form-table">
					<tbody>
						<?php
						do_action( 'wdmir_settings_before' );
						$wdmir_admin_settings = get_option( '_wdmir_admin_settings', array() );

						// Category Check Customizer
						$ir_ld_category_check = '';
						$this->wdmSetSettingVariable( $wdmir_admin_settings, 'ir_ld_category_check', $ir_ld_category_check );

						// Admin Customizer
						$admin_customizer_check = '';
						$this->wdmSetSettingVariable( $wdmir_admin_settings, 'admin_customizer_check', $admin_customizer_check );

						// Product Review
						$review_product = '';
						$this->wdmSetSettingVariable( $wdmir_admin_settings, 'review_product', $review_product );

						// Course Review
						$review_course = '';
						$this->wdmSetSettingVariable( $wdmir_admin_settings, 'review_course', $review_course );

						// Download Review
						$review_download = '';
						$this->wdmSetSettingVariable( $wdmir_admin_settings, 'review_download', $review_download );

						// instructor mail
						$wl8_en_inst_mail = '';
						$this->wdmSetSettingVariable( $wdmir_admin_settings, 'instructor_mail', $wl8_en_inst_mail );

						// added in 2.4.0 v instructor commission
						$wl8_en_inst_commi = '';
						$this->wdmSetSettingVariable( $wdmir_admin_settings, 'instructor_commission', $wl8_en_inst_commi );

						// if (isset($wdmir_admin_settings['instructor_commission']) && $wdmir_admin_settings['instructor_commission'] == '1') {
						// $wl8_en_inst_commi = 'checked';
						// }
						// if EDD-LD integration plugin is deactivated then it will not show this setting v3.0.0
						if ( wdmCheckEDDDependency() ) {
							?>
										<tr>
										<th scope="row"><label for="wdmir_review_download">
										<?php
										echo __( 'Review Download', 'wdm_instructor_role' );
										?>
									</label></th>
										<td><input name="wdmir_review_download" type="checkbox" id="wdmir_review_download" 
										<?php
										echo $review_download;
										?>
									>
										<?php
										echo __( 'Enable admin approval for EDD product updates.', 'wdm_instructor_role' );
										?>
										</td>
									</tr>
									<?php
						}
						// if woocommerce-ld integration plugin is deactivated then it will not show this setting v3.0.0
						if ( wdmCheckWooDependency() ) {
							?>
										<tr>
									<th scope="row"><label for="wdmir_review_product">
									<?php
									echo __( 'Review Product', 'wdm_instructor_role' );
									?>
								</label></th>
									<td><input name="wdmir_review_product" type="checkbox" id="wdmir_review_product" 
									<?php
									echo $review_product;
									?>
								>
										<?php
										echo __( 'Enable admin approval for WooCommerce product updates.', 'wdm_instructor_role' );
										?>
									</td>
								</tr>
									<?php
						}
						?>
						<tr>
							<th scope="row"><label for="wdmir_review_course">
							<?php
							echo __( sprintf( 'Review %s', \LearnDash_Custom_Label::label_to_lower( 'course' ) ), 'wdm_instructor_role' );
							?>
			</label></th>
							<td><input name="wdmir_review_course" type="checkbox" id="wdmir_review_course"
							<?php
							echo $review_course;
							?>
			>
							<?php
							echo __( sprintf( 'Enable admin approval for LearnDash %s updates.', \LearnDash_Custom_Label::label_to_lower( 'course' ) ), 'wdm_instructor_role' );
							?>
							</td>
						</tr>


						<tr>
							<th scope="row"><label for="wdm_enable_instructor_mail">
							<?php
							echo __( 'Instructor Email', 'wdm_instructor_role' );
							?>
			</label></th>
							<td><input name="wdm_enable_instructor_mail" type="checkbox" id="wdm_enable_instructor_mail"
							<?php
							echo $wl8_en_inst_mail;
							?>
			>
							<?php
							echo __( sprintf( 'Enable email notification for instructor on %s completion.', \LearnDash_Custom_Label::label_to_lower( 'quiz' ) ), 'wdm_instructor_role' );
							?>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wdm_enable_instructor_commission">
							<?php
							echo __( 'Instructor Commission', 'wdm_instructor_role' );
							?>
			</label></th>
							<td><input name="wdm_enable_instructor_commission" type="checkbox" id="wdm_enable_instructor_commission"
							<?php
							echo $wl8_en_inst_commi;
							?>
			>
							<?php
							echo __( 'Disable Instructor commission feature.', 'wdm_instructor_role' );
							?>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="ir_admin_customizer_check">
							<?php
							echo __( 'Admin Customizer', 'wdm_instructor_role' );
							?>
			</label></th>
							<td><input name="ir_admin_customizer_check" type="checkbox" id="ir_admin_customizer_check"
							<?php
							echo $admin_customizer_check;
							?>
			>
							<?php
							echo __( 'Enable admin customizer for dashboard.', 'wdm_instructor_role' );
							?>
							</td>
						</tr>

						<!-- <tr>
							<th scope="row">
								<label for="wdmir_review_course_content">
									<?php // echo __('Review Course Content', 'wdm_instructor_role'); ?>
								</label>
							</th>
							<td>
							<?php
							// $editor_settings = array('textarea_rows' => 100, 'editor_height' => 200);
							// wp_editor(($wdmir_admin_settings['review_course_content'] ? $wdmir_admin_settings['review_course_content'] : ''), 'wdmir_review_course_content', $editor_settings);
							?>
							</td>
						</tr> -->

						<tr>
							<th scope="row">
								<label for="ir_ld_category_check">
									<?php esc_html_e( 'LearnDash Category', 'wdm_instructor_role' ); ?>
								</label>
							</th>
							<td>
								<input
									name="ir_ld_category_check"
									type="checkbox"
									id="ir_ld_category_check"
									<?php echo esc_html( $ir_ld_category_check ); ?>
								/>
								<?php echo esc_html_e( sprintf( 'Restrict Instructors from creating new LearnDash %s categories.', \LearnDash_Custom_Label::label_to_lower( 'course' ) ), 'wdm_instructor_role' ); ?>
							</td>
						</tr>
						<?php
						do_action( 'wdmir_settings_after' );
						?>
					</tbody>
				</table>
				<?php
					do_action( 'wdmir_settings_after_table' );
				?>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'wdm_instructor_role' ); ?>">
				</p>
			</form>
		</div>
			<?php

		} // function wdmir_instructor_settings()

		public function wdmSetSettingVariable( $wdmir_admin_settings, $key, &$value ) {
			if ( isset( $wdmir_admin_settings[ $key ] ) && '1' == $wdmir_admin_settings[ $key ] ) {
				$value = 'checked';
			}
		}

		/*
		*   @since version 2.1
		*   Saving HTML form content of Instructor Settings page.
		*
		*/
		function wdmir_settings_save() {
			if ( isset( $_POST['instructor_setting_nonce'] ) && wp_verify_nonce( $_POST['instructor_setting_nonce'], 'instructor_setting_nonce_action' ) && is_admin() ) {
				$wdmir_admin_settings = array();

				do_action( 'wdmir_settings_save_before' );

				// Product Review
				$wdmir_admin_settings['review_product'] = '';
				if ( isset( $_POST['wdmir_review_product'] ) ) {
					$wdmir_admin_settings['review_product'] = 1;
				}

				// Course Review
				$wdmir_admin_settings['review_course'] = '';
				if ( isset( $_POST['wdmir_review_course'] ) ) {
					$wdmir_admin_settings['review_course'] = 1;
				}
				// Download Review
				$wdmir_admin_settings['review_download'] = '';
				if ( isset( $_POST['wdmir_review_download'] ) ) {
					$wdmir_admin_settings['review_download'] = 1;
				}

				// Enable instructor mail
				$wdmir_admin_settings['instructor_mail'] = '';
				if ( isset( $_POST['wdm_enable_instructor_mail'] ) ) {
					$wdmir_admin_settings['instructor_mail'] = 1;
				}

				// Course Review
				$wdmir_admin_settings['review_course_content'] = '';
				if ( isset( $_POST['wdmir_review_course_content'] ) ) {
					$wdmir_admin_settings['review_course_content'] = $_POST['wdmir_review_course_content'];
				}

				// instructor commission
				$wdmir_admin_settings['instructor_commission'] = '';
				if ( isset( $_POST['wdm_enable_instructor_commission'] ) ) {
					$wdmir_admin_settings['instructor_commission'] = 1;
				}

				// Admin Customizer
				$wdmir_admin_settings['admin_customizer_check'] = '';
				if ( isset( $_POST['ir_admin_customizer_check'] ) ) {
					$wdmir_admin_settings['admin_customizer_check'] = 1;
				}

				// LD Category Access
				$wdmir_admin_settings['ir_ld_category_check'] = '';
				if ( isset( $_POST['ir_ld_category_check'] ) ) {
					$wdmir_admin_settings['ir_ld_category_check'] = 1;
				}

				// Saving instructor settings option
				update_option( '_wdmir_admin_settings', $wdmir_admin_settings );

				do_action( 'wdmir_settings_save_after' );

				wp_redirect( $_POST['_wp_http_referer'] );
			}
		}

		/**
		 * Hide the new category creation links for instructors
		 *
		 * @since 3.5.0
		 */
		public function hide_category_links() {
			if ( ! wdm_is_instructor() || ! ir_admin_settings_check( 'ir_ld_category_check' ) ) {
				return;
			}
			global $current_screen;

			$target_screens = array(
				'sfwd-courses',  // Courses
				'sfwd-lessons',  // Lessons
				'sfwd-topic',     // Topic
			);

			// Check if course or lesson edit screen.
			if ( ! empty( $current_screen ) && in_array( $current_screen->id, $target_screens ) ) {
				?>
				<style>
				/* Hide instructor category adding link */
				.components-button.editor-post-taxonomies__hierarchical-terms-add.is-link, #category-adder {
					display: none;
				}
				</style>
				<?php
			}

			$target_screens = array(
				'edit-sfwd-courses',  // Courses
				'edit-sfwd-lessons',  // Lessons
				'edit-sfwd-topic',     // Topic
			);

			// Check if course or lesson listing screen.
			if ( ! empty( $current_screen ) && in_array( $current_screen->id, $target_screens ) ) {
				?>
				<style>
				/* Hide instructor category links */
				.edit-post-header__settings {
					display: none;
				}
				</style>
				<?php
			}
		}
	}
}
