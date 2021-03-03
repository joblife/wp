<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * for menu manager
 */
require_once('inc/backend/menu_manager.php' );

/**
 * Plugin's main class initilization
 */
if (!class_exists('everestAdminThemeClass')) {

    class everestAdminThemeClass {

        var $plugin_settings;

        function __construct() {
            $this->plugin_settings = get_option('eat_admin_theme_settings');
            add_action('init', array($this, 'plugin_contants'));
            add_action('init', array($this, 'plugin_variables')); // Register globals variables
            add_action('init', array($this, 'plugin_text_domain'));

            add_action('admin_head', array($this, 'my_custom_css'));
            add_action('admin_enqueue_scripts', array($this, 'register_plugin_assets'));
            add_action('admin_footer', array($this, 'eat_at_footer_custom_css'));
            add_action('init', array($this, 'everest_admin_dashboard'));

            // WDM ENABLE THESE HOOKS
            if (self::irEnableAdminTheme()) {
                add_action('in_admin_header', array( $this, 'irAddInstructorMenu'), 0);
                add_filter('wdmir_exclude_post_types', array( $this, 'irExcludeMenuItems'));
                add_filter('admin_head', array( $this, 'irRemoveAdminBar'));
                add_action('admin_enqueue_scripts', array($this, 'ir_register_theme_scripts'));
            }

            add_action('admin_menu', array($this, 'plugin_menu'));
            // add_action('admin_post_eat_settings_action', array($this, 'save_plugin_settings'));
            add_action('ir_admin_customizer_start', array($this, 'save_plugin_settings'));

            // admin dashboard widgets functions
            // add_action('init', array($this, 'eat_admin_dashboard_widgets'));

            // init hook for the footer texts
            add_action('init', array($this, 'eat_admin_footer_options'));

            // hook for the admin bar items removal
            add_action('wp_before_admin_bar_render', array($this, 'remove_admin_bar_items'), 99999);

            // add_action('init', array($this, 'eat_custom_login_options'));

            add_action('init', array($this, 'eat_admin_bar_options'));

            // action for the posts and pages meta boxes removal
            // add_action('admin_menu', array($this, 'remove_meta_boxes_for_posts_pages'));


            // get the admin bar all nodes in a method.
            add_action('wp_before_admin_bar_render', array($this, 'get_admin_bar_nodes'), 999);

            // function for the favicon set
            add_action('login_head', array($this, 'set_favicon'));
            add_action('admin_head', array($this, 'set_favicon'));

            //action to export the plugin_settings
            add_action('admin_post_eat_export_plugin_settings_action', array($this, 'eat_export_plugin_settings_action'));

            // action to import the plugin settings
            add_action('admin_post_eat_import_plugin_settings_action', array($this, 'eat_import_plugin_settings_action'));
        }

        function eat_at_footer_custom_css() {
            ?>
            <style type="text/css" >
            <?php echo $this->plugin_settings['custom_css']; ?>
            </style>
            <?php
        }

        function eat_export_plugin_settings_action() {
            if (isset($_POST['action']) && $_POST['action'] == 'eat_export_plugin_settings_action' && wp_verify_nonce($_POST['eat_export_nonce_field'], 'eat-export-nonce')) {
                $plugin_settings = get_option('eat_admin_theme_settings');

                $filename = sanitize_title('eat_plugin_settings');
                $json = json_encode($plugin_settings);

                header('Content-disposition: attachment; filename=' . $filename . '.json');
                header('Content-type: application/json');
                echo( $json);
            } else {
                die('No script kiddies please!!');
            }
        }

        /**
         * Function to perform the upload of the json data to plugin database
         * @return boolean return true or false based on the operation and redirect according to it.
         */
        function eat_import_plugin_settings_action() {
            if (isset($_POST['action']) && $_POST['action'] == 'eat_import_plugin_settings_action' && wp_verify_nonce($_POST['eat_import_nonce_field'], 'eat-import-nonce')) {
                die();
                if (!empty($_FILES) && $_FILES['import_file']['name'] != '') {

                    $filename = $_FILES['import_file']['name'];

                    $filename_array = explode('.', $filename);
                    $filename_ext = end($filename_array);
                    if ($filename_ext == 'json') {
                        $new_filename = 'import-' . rand(111111, 999999) . '.' . $filename_ext;
                        $upload_path = E_ADMIN_THEME_PLUGIN_DIR . '/tmp/' . $new_filename;
                        $source_path = $_FILES['import_file']['tmp_name'];
                        $check = @move_uploaded_file($source_path, $upload_path);
                        if ($check) {
                            $url = APPTS_PLUGIN_DIR_URL . '/tmp/' . $new_filename;
                            $params = array(
                                'sslverify' => false,
                                'timeout' => 60
                            );
                            $connection = wp_remote_get($url, $params);
                            if (!is_wp_error($connection)) {
                                $body = $connection['body'];
                                $plugin_settings = json_decode($body);
                                unlink($upload_path);

                                // var_dump($plugin_settings);
                                // die();
                                // $check = self:: appts_import_table_data( $table_row );
                                if ($check) {
                                    wp_redirect(admin_url("admin.php?page=everest-admin-menu-import-export&message=1"));
                                    exit;
                                } else {
                                    wp_redirect(admin_url('admin.php?page=everest-admin-menu-import-export&message=2'));
                                    exit;
                                }
                            } else {
                                wp_redirect(admin_url('admin.php?page=everest-admin-menu-import-export&message=3'));
                                exit;
                            }
                        } else {
                            wp_redirect(admin_url('admin.php?page=everest-admin-menu-import-export&message=4'));
                            exit;
                        }
                    } else {
                        wp_redirect(admin_url('admin.php?page=everest-admin-menu-import-export&message=5'));
                        exit;
                    }
                }
                wp_redirect(admin_url('admin.php?page=everest-admin-menu-import-export&message=3'));
                exit;
            } else {
                die('No script kiddies please!!');
            }
        }

        function my_custom_css() {
            include('inc/frontend/dynamic.css.php');
        }

        function set_favicon() {
            $plugin_settings = $this->plugin_settings;

            $favicon_url = isset($plugin_settings['general-settings']['favicon']['url']) ? $plugin_settings['general-settings']['favicon']['url'] : '';

            if ($favicon_url != '' && self::irEnableAdminTheme()) {
                echo '<link rel="shortcut icon" href="' . $favicon_url . '" />';
            }
        }

        public function remove_admin_bar_items() {
            global $wp_admin_bar;
            $plugin_settings = get_option('eat_admin_theme_settings');
            $admin_bar_items = isset($plugin_settings['admin_bar']['hide_show_opt']) ? $plugin_settings['admin_bar']['hide_show_opt'] : array();
            if (!empty($admin_bar_items)) {
                foreach ($admin_bar_items as $admin_bar_item) {
                    $wp_admin_bar->remove_node($admin_bar_item);
                }
            }
        }

        public function get_admin_bar_nodes() {
            /** @var $wp_admin_bar WP_Admin_Bar */
            global $wp_admin_bar;

            // @see: http://codex.wordpress.org/Function_Reference/get_nodes
            $all_toolbar_nodes = $wp_admin_bar->get_nodes();
            update_option('eat_admin_bar_nodes', $all_toolbar_nodes);
        }

        public function everest_admin_dashboard() {
            include(E_ADMIN_THEME_PLUGIN_DIR . 'inc/frontend/admin_dashboard.php');
        }

        function eat_admin_bar_options() {
            include(E_ADMIN_THEME_PLUGIN_DIR . 'inc/frontend/admin_bar_menu.php');
        }

        function eat_admin_dashboard_widgets() {
            include(E_ADMIN_THEME_PLUGIN_DIR . 'inc/frontend/admin_widgets.php');
        }

        function eat_admin_footer_options() {
            include(E_ADMIN_THEME_PLUGIN_DIR . 'inc/frontend/admin_footer.php');
        }

        function remove_meta_boxes_for_posts_pages() {

            $plugin_settings = get_option('eat_admin_theme_settings');
            $posts_pages_settings = isset($plugin_settings['posts_pages']) ? $plugin_settings['posts_pages'] : array();


            if (current_user_can('manage_options')) {
                if (isset($posts_pages_settings['excerpt-box'])) {
                    remove_meta_box('postexcerpt', 'post', 'normal'); // for excerpt
                }

                if (isset($posts_pages_settings['category-box'])) {
                    remove_meta_box('categorydiv', 'post', 'normal'); // for category
                }

                if (isset($posts_pages_settings['format-box'])) {
                    remove_meta_box('formatdiv', 'post', 'normal'); // for formats
                }

                if (isset($posts_pages_settings['trackback-box'])) {
                    remove_meta_box('trackbacksdiv', 'post', 'normal'); // for trackbacks
                }

                if (isset($posts_pages_settings['comment-status-box'])) {
                    remove_meta_box('commentstatusdiv', 'post', 'normal'); // for discussions
                }

                if (isset($posts_pages_settings['comments-list-box'])) {
                    remove_meta_box('commentsdiv', 'post', 'normal'); // for comments
                }

                if (isset($posts_pages_settings['custom-fields-box'])) {
                    remove_meta_box('postcustom', 'post', 'normal'); // for custom fields
                }

                if (isset($posts_pages_settings['revisions-box'])) {
                    remove_meta_box('revisionsdiv', 'post', 'normal'); // for rivisions
                }

                if (isset($posts_pages_settings['author-box'])) {
                    remove_meta_box('authordiv', 'post', 'normal'); // for author name
                }

                if (isset($posts_pages_settings['slug-box'])) {
                    remove_meta_box('slugdiv', 'post', 'normal');  // for post's slug
                }
            }
        }

        public function eat_custom_login_options() {
            include(E_ADMIN_THEME_PLUGIN_DIR . 'inc/frontend/custom-login.php');
        }

        function save_plugin_settings() {
            if (isset($_POST['eat_settings_submit']) && (wp_verify_nonce($_POST['eat_settings_nonce'], 'eat_settings_action') )) {
                include('inc/backend/save-settings.php');
            } else if (isset($_POST['eat_reset_settings']) && (wp_verify_nonce($_POST['eat_settings_nonce'], 'eat_settings_action'))) {
                global $eat_variables;
                $key = update_option('eat_admin_theme_settings', $eat_variables['default_settings']);
                if ($key == TRUE) {
                    wp_redirect(admin_url() . 'admin.php?page=ir-admin-customizer&message=3');
                } else {
                    wp_redirect(admin_url() . 'admin.php?page=ir-admin-customizer&message=2');
                }
                die();
            }
        }

        function plugin_menu() {
            add_submenu_page(
                'themes.php',
                __('Admin Customizer', 'wdm_instructor_role'),
                __('Admin Customizer', 'wdm_instructor_role'),
                'manage_options',
                'ir-admin-customizer',
                array(
                    $this,
                    'main_page'
                )
            );
            // add_submenu_page("ir-admin-customizer", "Menu Manager", 'Menu Manager', 'manage_options', 'everest-menu-manager', array('everestAdminThemeMenuManager', 'eat_menu_manager_settings_page'));
            // add_submenu_page("ir-admin-customizer", "Import/Export", 'Import/Export', 'manage_options', 'everest-admin-menu-import-export', array($this, 'import_export_page'));
            // add_submenu_page("ir-admin-customizer", "About", 'About', 'manage_options', 'everest-admin-menu-about', array($this, 'about_page'));
            // add_submenu_page("ir-admin-customizer", "More WordPress Stuffs", 'More WordPress Stuffs', 'manage_options', 'everest-admin-menu-more-wp-stuffs', array($this, 'more_wordpress_resources'));
        }

        function how_to_use_page() {
            include('inc/backend/how-to-use.php');
        }

        function about_page() {
            include('inc/backend/about.php');
        }

        function import_export_page() {
            include('inc/backend/import_export.php');
        }

        function main_page_one() {
            include('inc/backend/menu_manager.php');
        }

        function main_page() {
            include('inc/backend/main_page.php');
        }

        function more_wordpress_resources() {
            include('inc/backend/more-wordpress-resources.php');
        }

        /**
         * Function for the contant declaration of the plugins.
         * @return null
         */
        function plugin_contants() {
            //Declearation of the necessary constants for plugin
            defined('E_ADMIN_THEME_VERSION') or define('E_ADMIN_THEME_VERSION', '1.0.3');

            defined('E_ADMIN_PLUGIN_PREFIX') or define('E_ADMIN_PLUGIN_PREFIX', 'eat');

            defined('E_ADMIN_THEME_IMAGE_DIR') or define('E_ADMIN_THEME_IMAGE_DIR', plugin_dir_url(__FILE__) . 'images');

            defined('E_ADMIN_THEME_JS_DIR') or define('E_ADMIN_THEME_JS_DIR', plugin_dir_url(__FILE__) . 'js');

            defined('E_ADMIN_THEME_CSS_DIR') or define('E_ADMIN_THEME_CSS_DIR', plugin_dir_url(__FILE__) . 'css');

            defined('E_ADMIN_THEME_ASSETS_DIR') or define('E_ADMIN_THEME_ASSETS_DIR', plugin_dir_url(__FILE__) . 'assets');

            defined('E_ADMIN_THEME_LANG_DIR') or define('E_ADMIN_THEME_LANG_DIR', basename(dirname(__FILE__)) . '/languages/');

            defined('E_ADMIN_THEME_TEXT_DOMAIN') or define('E_ADMIN_THEME_TEXT_DOMAIN', 'ir-admin-customizer');

            defined('E_ADMIN_THEME_SETTINGS') or define('E_ADMIN_THEME_SETTINGS', 'everest_admin_theme_settings');

            defined('E_ADMIN_THEME_PLUGIN_DIR') or define('E_ADMIN_THEME_PLUGIN_DIR', plugin_dir_path(__FILE__));

            defined('E_ADMIN_THEME_PLUGIN_DIR_URL') or define('E_ADMIN_THEME_PLUGIN_DIR_URL', plugin_dir_url(__FILE__)); //plugin directory url
        }

        /**
         * Make plugin's variables available all around
         * @return NULL
         */
        public function plugin_variables() {
            global $eat_variables;
            include_once( E_ADMIN_THEME_PLUGIN_DIR . 'inc/plugin_variables.php' );
        }

        /**
         * Function to load the plugin text domain for plugin translation
         * @return type
         */
        function plugin_text_domain() {
            load_plugin_textdomain('ir-admin-customizer', false, E_ADMIN_THEME_LANG_DIR);
        }

        /**
         * Function to add  plugin's necessary CSS and JS files for backend
         * @return null
         */
        function register_plugin_assets() {
            //register the styles
            wp_register_style('custom-icon-picker', E_ADMIN_THEME_CSS_DIR . '/icon-picker.css', false, E_ADMIN_THEME_VERSION);
            wp_register_style('font-awesome-icons-v4.7.0', E_ADMIN_THEME_CSS_DIR . '/font-awesome/font-awesome.min.css', false, E_ADMIN_THEME_VERSION);
            wp_register_style('eat_gener_icons', E_ADMIN_THEME_CSS_DIR . '/genericons.css', false, E_ADMIN_THEME_VERSION);
            wp_register_style('jquery-ui-css', E_ADMIN_THEME_CSS_DIR . '/jquery-ui.css', false, E_ADMIN_THEME_VERSION);
            wp_register_style('jquery-selectbox-css', E_ADMIN_THEME_CSS_DIR . '/jquery.selectbox.css', false, E_ADMIN_THEME_VERSION);
            wp_register_style('eat_codemirror_css', E_ADMIN_THEME_CSS_DIR . '/eat-codemirror.css', false, E_ADMIN_THEME_VERSION);
            wp_register_style('eat_codemirror_theme_eclipse_css', E_ADMIN_THEME_CSS_DIR . '/eclipse.css', false, E_ADMIN_THEME_VERSION);
            wp_register_style('eat_perfect_scrollbar_css', E_ADMIN_THEME_CSS_DIR . '/perfect-scrollbar.css', false, E_ADMIN_THEME_VERSION);
            wp_register_style('eat_admin_css', E_ADMIN_THEME_CSS_DIR . '/eat-backend.css', false, E_ADMIN_THEME_VERSION);

            //enqueue of the styles
            wp_enqueue_style('eat_gener_icons');
            wp_enqueue_style('custom-icon-picker');
            wp_enqueue_style('font-awesome-icons-v4.7.0');
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_style('jquery-ui-css');
            wp_enqueue_style('jquery-selectbox-css');
            wp_enqueue_style('eat_codemirror_css');
            wp_enqueue_style('eat_codemirror_theme_eclipse_css');
            wp_enqueue_style('eat_perfect_scrollbar_css');
            wp_enqueue_style('eat_dashboard_resp_css');
            wp_enqueue_style('eat_admin_css');

            wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Rubik:400,500,700|Poppins', array(), E_ADMIN_THEME_VERSION);

            // registration of the js
            wp_register_script('eat_icon_picker', E_ADMIN_THEME_JS_DIR . '/icon-picker.js', array('jquery'), E_ADMIN_THEME_VERSION, true);
            wp_enqueue_script('wp-color-picker-alpha', E_ADMIN_THEME_JS_DIR . '/wp-color-picker-alpha.js', array('jquery', 'wp-color-picker'), '2.1.2');
            // wp_enqueue_script( 'resize-sensor', E_ADMIN_THEME_JS_DIR.'/ResizeSensor.js',array('jquery'), E_ADMIN_THEME_VERSION );
            // wp_enqueue_script( 'sticky-sidebar', E_ADMIN_THEME_JS_DIR.'/jquery.sticky-sidebar.js',array('jquery', 'resize-sensor'), E_ADMIN_THEME_VERSION );
            wp_enqueue_script('resize-sensor', E_ADMIN_THEME_JS_DIR . '/ResizeSensor.js', array('jquery'), E_ADMIN_THEME_VERSION);
            wp_enqueue_script('theia-sticky-sidebar', E_ADMIN_THEME_JS_DIR . '/theia-sticky-sidebar.js', array('jquery', 'resize-sensor'), E_ADMIN_THEME_VERSION);
            wp_enqueue_script('selectbox-min-js', E_ADMIN_THEME_JS_DIR . '/jquery-selectbox.js', array('jquery'), E_ADMIN_THEME_VERSION);
            wp_enqueue_script('codemirror-js', E_ADMIN_THEME_JS_DIR . '/eat-codemirror.js', array('jquery'), E_ADMIN_THEME_VERSION);
            wp_register_script('eat_codemirror-dynamic-css', E_ADMIN_THEME_JS_DIR . '/codemirror-css.js', array('jquery', 'codemirror-js', 'codemirror-js'), E_ADMIN_THEME_VERSION);
            wp_register_script('eat_perfect_scrollbar_js', E_ADMIN_THEME_JS_DIR . '/perfect-scrollbar.js', array('jquery'), E_ADMIN_THEME_VERSION);
            wp_register_script('eat_admin_js', E_ADMIN_THEME_JS_DIR . '/eat-backend.js', array('jquery', 'wp-color-picker', 'wp-color-picker-alpha', 'eat_icon_picker', 'jquery-ui-sortable', 'resize-sensor', 'theia-sticky-sidebar', 'selectbox-min-js', 'codemirror-js', 'eat_codemirror-dynamic-css', 'eat_perfect_scrollbar_js'), E_ADMIN_THEME_VERSION, true);

            // enqueue of the js
            wp_enqueue_script('eat_jarallax_js', E_ADMIN_THEME_JS_DIR . '/jarallax.js', array('jquery'), E_ADMIN_THEME_VERSION, true);
            wp_enqueue_script('eat_jarallax_video_js', E_ADMIN_THEME_JS_DIR . '/jarallax-video.js', array('jquery'), E_ADMIN_THEME_VERSION, true);
            wp_enqueue_media();
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_script('eat_icon_picker');
            wp_enqueue_script('eat_perfect_scrollbar_js');
            wp_enqueue_script('eat_admin_js');
            $ajax_nonce = wp_create_nonce('eat-ajax-nonce');

            $plugin_settings = $this->plugin_settings;
            $temp_plugin_settings = array(
                'dashboard_template' => $plugin_settings['general-settings']['template'],
                'ajax_nonce' => $ajax_nonce,
            );
            wp_localize_script('eat_admin_js', 'eat_plugin_settings', $temp_plugin_settings);
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-slider');

            // Enqueue IR Custom Styles and Scripts
            $this->irEnqueueScripts();
        }

        function ir_register_theme_scripts()
        {
            wp_register_style('eat_dashboard_css', E_ADMIN_THEME_CSS_DIR . '/eat-dashboard.css', false, E_ADMIN_THEME_VERSION);
            wp_register_style('eat_dashboard_resp_css', E_ADMIN_THEME_CSS_DIR . '/eat-dashboard-responsive.css', false, E_ADMIN_THEME_VERSION);
            wp_enqueue_style('eat_dashboard_css');
            wp_enqueue_style('eat_dashboard_resp_css');
            wp_register_style('eat_admin_rtl_css', E_ADMIN_THEME_CSS_DIR . '/eat-dashboard-rtl.css', false, E_ADMIN_THEME_VERSION);
            wp_enqueue_style('eat_admin_rtl_css');
            wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Rubik:400,500,700|PT+Sans+Narrow|Poppins|Roboto|Oxygen:300,400,600,700|Josefin+Sans:400,600,700|Ubuntu:300,400,500,700', array(), E_ADMIN_THEME_VERSION);
        }

        /**
         * Sanitizes Multi Dimensional Array
         * @param array $array
         * @param array $sanitize_rule
         * @return array
         *
         * @since 1.0.0
         */
        static function sanitize_array($array = array(), $sanitize_rule = array()) {
            if (!is_array($array) || count($array) == 0) {
                return array();
            }

            foreach ($array as $k => $v) {
                if (!is_array($v)) {
                    $default_sanitize_rule = (is_numeric($k)) ? 'text' : 'html';
                    $sanitize_type = isset($sanitize_rule[$k]) ? $sanitize_rule[$k] : $default_sanitize_rule;
                    $array[$k] = self:: sanitize_value($v, $sanitize_type);
                }

                if (is_array($v)) {
                    $array[$k] = self:: sanitize_array($v, $sanitize_rule);
                }
            }

            return $array;
        }

        /**
         * Sanitizes Value
         *
         * @param type $value
         * @param type $sanitize_type
         * @return string
         *
         * @since 1.0.0
         */
        static function sanitize_value($value = '', $sanitize_type = 'text') {
            switch ($sanitize_type) {
                case 'html':
                    $allowed_html = wp_kses_allowed_html('post');
                    return wp_kses($value, $allowed_html);
                    break;
                default:
                    return sanitize_text_field($value);
                    break;
            }
        }

        public static function print_array($array) {
            echo "<pre>";
            print_r($array);
            echo "</pre>";
        }

        function about_us_submenu_page_callback() {
            include('inc/backend/about-page.php');
        }

        function google_fonts_array() {
            /* Save Google font list in to database */
            // $font_json = file_get_contents('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyAwimCYFN08Z7h9ocdO05-EnGVjmwK1yLA');
            $font_json = file_get_contents(E_ADMIN_THEME_PLUGIN_DIR . '/inc/backend/google_fonts.json');
            $font_decoded = json_decode($font_json);
            return $font_decoded->items;
        }

        public static function get_php_version() {
            $php_ver = phpversion();
            return $php_ver;
        }

        public static function get_mysql_version() {
            $mysql_version = mysqli_get_server_info();
            return $mysql_version;
        }

        public function irEnqueueScripts()
        {
            wp_register_style('ir-settings-dashboard-css', E_ADMIN_THEME_CSS_DIR . '/ir-dashboard-settings.css', E_ADMIN_THEME_VERSION);
            wp_register_style('ir-common-dashboard-css', E_ADMIN_THEME_CSS_DIR . '/ir-dashboard.css', array('eat_dashboard_css'), E_ADMIN_THEME_VERSION);
            wp_register_style('ir-temp-3-dashboard-css', E_ADMIN_THEME_CSS_DIR . '/ir-temp-3-dashboard.css', array('eat_dashboard_css'), E_ADMIN_THEME_VERSION);
            wp_register_style('ir-temp-13-dashboard-css', E_ADMIN_THEME_CSS_DIR . '/ir-temp-13-dashboard.css', array('eat_dashboard_css'), E_ADMIN_THEME_VERSION);
            wp_register_style('ir-temp-14-dashboard-css', E_ADMIN_THEME_CSS_DIR . '/ir-temp-14-dashboard.css', array('eat_dashboard_css'), E_ADMIN_THEME_VERSION); 
            
            wp_enqueue_style('ir-settings-dashboard-css');
            wp_enqueue_style('ir-common-dashboard-css');
            $plugin_settings = $this->plugin_settings;
            if (! empty($plugin_settings['general-settings']['template'])) {
                $template = trim($plugin_settings['general-settings']['template']);
                wp_enqueue_style('ir-'.$template.'-dashboard-css');
            }

            // wp_enqueue_script('ir-dashboard-scripts', E_ADMIN_THEME_JS_DIR.'/ir-dashboard-script.js', array('jquery'));
        }

        /**
		 * Whether to enable the admin theme for the current user or not
		 *
		 * @return bool True if admin theme to be enabled for current user, false otherwise
		 * @since 3.1.0
		 */
		public static function irEnableAdminTheme()
		{
			// Get all theme settings
			$admin_theme_settings = get_option('eat_admin_theme_settings');
			if (empty($admin_theme_settings['visibility'])) {
				return false;
			}

			// Extract visibility settings
			$visibility_settings = $admin_theme_settings['visibility'];

			// Check if enabled for all users
			if (array_key_exists('enable_for_all', $visibility_settings) && $visibility_settings['enable_for_all']) {
				return true;
			}

			// Is current user admin
			if (current_user_can('manage_options')) {
				// Check if enabled for admin
				if (array_key_exists('enable_for_admin', $visibility_settings) && $visibility_settings['enable_for_admin']) {
					return true;
				}
			}

			// Is current user instructor
			if (wdm_is_instructor()) {
				// Check if enabled for instructor
				if (array_key_exists('enable_for_instructor', $visibility_settings) && $visibility_settings['enable_for_instructor']) {
					return true;
				}
			}

			// Allow 3rd party plugins to filter admin theme visibility.
			return apply_filters('ir_filter_admin_theme_visibility', false);
		}

		/**
		 * Remove default admin bar for instructors
		 *
		 * @since 3.1.0
		 */
		public function irRemoveAdminBar()
		{
			if (wdm_is_instructor()) {
				remove_action('in_admin_header', 'wp_admin_bar_render', 0);
			}
		}

		/**
		 * Add Primary frontend menu for instructors
		 *
		 * @since 3.1.0
		 */
		public function irAddInstructorMenu()
		{
			// Check if instructor
			if (wdm_is_instructor()) {
				$instructor_menu_slug = 'ir-instructor-menu';

				if (has_nav_menu($instructor_menu_slug)) {
					echo wp_nav_menu(
						array(
							'theme_location'  => $instructor_menu_slug,
							'menu_id'         => 'ir-primary-menu',
							'container_id'    => 'ir-primary-navigation',
							'container_class' => '',
							'echo'            => false
						)
					);
				} else {
					/**
					 * Allow third party plugins to update default instructor menu.
					 *
					 * @since 3.3.0
					 */
					$template = apply_filters(
						'ir_filter_default_instructor_menu_path',
						INSTRUCTOR_ROLE_ABSPATH.'/modules/templates/settings/ir-default-instructor-menu.template.php'
					);
					include($template);
				}
			}
		}

		/**
		 * Exclude menu items from the list of post types to restrict access to.
		 *
		 * @since 3.1.0
		 */
		public function irExcludeMenuItems($post_types)
		{
			if (wdm_is_instructor()) {
				$post_types[] = 'nav_menu_item';
			}
			return $post_types;
		}

    }

    $new_everest_admin_theme_obj = new everestAdminThemeClass();
}