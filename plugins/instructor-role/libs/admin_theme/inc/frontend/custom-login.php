<?php
// https://codex.wordpress.org/Customizing_the_Login_Form
//for wp-login.php edits
$plugin_settings = get_option('eat_admin_theme_settings');
$custom_login = $plugin_settings['custom_login'];

add_action('login_head', 'div_before_login_form');
add_action('login_enqueue_scripts', 'custom_login_scripts', 1000);
add_action('login_footer', 'my_addition_to_login_footer');

if (isset($custom_login['login_form']['logo']['title']) && $custom_login['login_form']['logo']['title'] != '') {
    add_filter('login_headertitle', 'w4_login_headertitle');
}

if (isset($custom_login['login_form']['logo']['url']) && $custom_login['login_form']['logo']['url'] != '') {
    add_filter('login_headerurl', 'the_url');
}


add_filter('login_message', 'the_login_message');
add_action('before_signup_form', 'test_function');

function test_function() {
    ?>
    <div class='whatever'></div>
    <?php
}

if (isset($custom_login['login_form']['error-message']['invalid-password']) && $custom_login['login_form']['error-message']['invalid-password'] != '') {
    add_filter('login_errors', 'the_login_error_message_for_password');
}

if (isset($custom_login['login_form']['error-message']['invalid-username']) && $custom_login['login_form']['error-message']['invalid-username'] != '') {
    add_filter('login_errors', 'the_login_error_message_for_username');
}

add_filter('login_body_class', 'login_classes');
add_action('login_head', 'my_login_logo');

// add_action( 'login_enqueue_scripts', 'my_login_logo', 999 );

function my_login_logo() {
    ?>
    <?php
    $plugin_settings = get_option('eat_admin_theme_settings');
    $custom_login = $plugin_settings['custom_login'];
    $login_form_template = $plugin_settings['custom_login']['login_form']['template'];
    $logo_title = $custom_login['login_form']['logo']['title'];
    $logo_image_url = isset($custom_login['login_form']['logo']['image-url']) ? $custom_login['login_form']['logo']['image-url'] : '';
    $background_type = $custom_login['login_form']['background']['type'];
    if ($background_type == 'image') {
        $form_background_image = $custom_login['login_form']['background']['image']['url'];
    } else if ($background_type == 'background-color') {
        $background_color = $custom_login['login_form']['background']['background-color']['color'];
    }
    ?>
    <style type="text/css">
    <?php if (isset($logo_image_url) && $logo_image_url != '') { ?>
            #login h1 a, .login h1 a {
                background-image: url(<?php echo $logo_image_url; ?>);
            }
    <?php } ?>
        .login-form-template-1 #login,
        .login-form-template-2 #login,
        .login-form-template-3 #login,
        .login-form-template-4 #login,
        .login-form-template-5 #login,
        .login-form-template-9 #login
        {
    <?php if (isset($form_background_image) && $form_background_image != '') { ?>
                background-image: url(<?php echo $form_background_image; ?>);
    <?php } ?>
    <?php if (isset($background_color) && $background_color != '') { ?>
                background-color: <?php echo $background_color; ?>;
                background-image: none;
    <?php } ?>
        }

    <?php
    if (isset($custom_login['login_form']['background']['overlay']['enable'])) {
        $overlay_color = $custom_login['login_form']['background']['overlay']['color'];
        ?>
            .login-form-template-1 #login::before,
            .login-form-template-2 #login::before,
            .login-form-template-3 #login::before,
            .login-form-template-4 #login::before,
            .login-form-template-5 #login::before,      
            .login-form-template-9 #login::before {
                background: <?php echo $overlay_color; ?>;
            }
    <?php } ?>
                    			
    <?php if (isset($custom_login['login_form']['wordpress-logo']['hide'])) { ?>
            .eat-admin-theme-custom-login-wrap h1 {
                display:none;
            }
    <?php } ?>

    <?php if (isset($custom_login['login_form']['remember-me-checkbox']['hide'])) { ?>
            .eat-admin-theme-custom-login-wrap p.forgetmenot {
                display:none;
            }
    <?php } ?>
    <?php if (isset($custom_login['login_form']['back-to-home']['hide'])) { ?>
            .eat-admin-theme-custom-login-wrap p#backtoblog {
                display:none;
            }
    <?php } ?>
                    			
    <?php if (isset($custom_login['login_form']['register-password-link']['hide'])) { ?>
            .eat-admin-theme-custom-login-wrap p#nav {
                display:none;
            }
    <?php } ?>
        .login-form-template-1 #login,
        .login-form-template-2 #login,
        .login-form-template-3 #login,
        .login-form-template-4 #login,
        .login-form-template-5 #login,
        .login-form-template-6 #login {
    <?php if (isset($custom_login['login_form']['background']['type']) && $custom_login['login_form']['background']['type'] == 'image') { ?>
                background:url("<?php echo $custom_login['login_form']['background']['image']['url']; ?>");
    <?php } else if (isset($custom_login['login_form']['background']['type']) && $custom_login['login_form']['background']['type'] == 'background-color') { ?>
                background:<?php echo $custom_login['login_form']['background']['background-color']['color']; ?>;
    <?php } ?>
        }

    <?php if (isset($custom_login['background']['overlay']['enable'])) { ?>
            .eat-admin-theme-custom-login-wrap .eat-common-overlay {
                background: red;
            }
    <?php } ?>
    <?php if (isset($custom_login['login_form']['background']['overlay']['enable']) && ($custom_login['login_form']['background']['type'] == 'image' || $custom_login['login_form']['background']['type'] == 'background-color')) { ?>
            .login-form-template-1 #login::before,
            .login-form-template-2 #login::before,
            .login-form-template-3 #login::before,
            .login-form-template-4 #login::before,
            .login-form-template-5 #login::before,
            .login-form-template-6 #login::before {
                background:<?php echo $custom_login['login_form']['background']['overlay']['color']; ?>;
            }
    <?php } ?>
                    			
    <?php
    $login_form_font_settings = $custom_login['login_form']['font_settings'];

    $google_fonts_used_array = array();

    $login_password_font_settings = $login_form_font_settings['login_password'];
//////////////////////////////////////////////////////////////////////////
    if (isset($login_password_font_settings['font-color']) && $login_password_font_settings['font-color'] != '') {
        $login_password_font_settings_dynamic_css[] = "color:{$login_password_font_settings['font-color']};";
    }

    if (isset($login_password_font_settings['font-size']) && $login_password_font_settings['font-size'] != '') {
        $login_password_font_settings_dynamic_css[] = "font-size:{$login_password_font_settings['font-size']}px;";
    }

    if (isset($login_password_font_settings['google-fonts']) && $login_password_font_settings['google-fonts'] != '') {
        if (!in_array($login_password_font_settings['google-fonts'], $google_fonts_used_array)) {
            array_push($google_fonts_used_array, preg_replace('/\s/', '+', $login_password_font_settings['google-fonts']));
        }
        $login_password_font_settings_dynamic_css[] = "font-family: {$login_password_font_settings['google-fonts']}, Dashicons;";
    }

    if (!empty($login_password_font_settings_dynamic_css)) {
        $login_password_font_settings_dynamic_css = implode(' ', $login_password_font_settings_dynamic_css);
    } else {
        $login_password_font_settings_dynamic_css = '';
    }
//////////////////////////////////////////////////////////////////////////
    ?>
        .login-form-<?php echo $login_form_template; ?> #login form label{ <?php echo $login_password_font_settings_dynamic_css; ?> }
        .login-form-<?php echo $login_form_template; ?> #login .forgetmenot label:before { <?php echo $login_password_font_settings_dynamic_css; ?> }

    <?php
    $login_button_font_settings = $login_form_font_settings['login_button'];
    $login_button_hover_font_settings = $login_form_font_settings['login_button']['hover'];
//////////////////////////////////////////////////////////////////////////
    if (isset($login_button_font_settings['font-color']) && $login_button_font_settings['font-color'] != '') {
        $login_button_font_settings_dynamic_css[] = "color:{$login_button_font_settings['font-color']};";
    }

    if (isset($login_button_font_settings['background-color']) && $login_button_font_settings['background-color'] != '') {
        $login_button_font_settings_dynamic_css[] = "background:{$login_button_font_settings['background-color']};";
    }

    if (isset($login_button_font_settings['font-size']) && $login_button_font_settings['font-size'] != '') {
        $login_button_font_settings_dynamic_css[] = "font-size:{$login_button_font_settings['font-size']}px;";
    }

    if (isset($login_button_font_settings['google-fonts']) && $login_button_font_settings['google-fonts'] != '') {
        if (!in_array($login_button_font_settings['google-fonts'], $google_fonts_used_array)) {
            array_push($google_fonts_used_array, preg_replace('/\s/', '+', $login_button_font_settings['google-fonts']));
        }
        $login_button_font_settings_dynamic_css[] = "font-family: {$login_button_font_settings['google-fonts']}, Dashicons;";
    }

    if (!empty($login_button_font_settings_dynamic_css)) {
        $login_button_font_settings_dynamic_css = implode(' ', $login_button_font_settings_dynamic_css);
    } else {
        $login_button_font_settings_dynamic_css = '';
    }

    /* hover settings */
    if (isset($login_button_hover_font_settings['font-color']) && $login_button_hover_font_settings['font-color'] != '') {
        $login_button_hover_font_settings_dynamic_css[] = "color:{$login_button_hover_font_settings['font-color']};";
    }

    if (isset($login_button_hover_font_settings['background-color']) && $login_button_hover_font_settings['background-color'] != '') {
        $login_button_hover_font_settings_dynamic_css[] = "background:{$login_button_hover_font_settings['background-color']};";
    }

    if (!empty($login_button_hover_font_settings_dynamic_css)) {
        $login_button_hover_font_settings_dynamic_css = implode(' ', $login_button_hover_font_settings_dynamic_css);
    } else {
        $login_button_hover_font_settings_dynamic_css = '';
    }
    /* hover settings end */
//////////////////////////////////////////////////////////////////////////
    ?>
        .login-form-<?php echo $login_form_template; ?> #login p.submit input[type="submit"] { <?php echo $login_button_font_settings_dynamic_css; ?> }
        .login-form-<?php echo $login_form_template; ?> #login p.submit input[type="submit"]:hover { <?php echo $login_button_hover_font_settings_dynamic_css; ?> }

    <?php
    $register_lost_password_font_settings_dynamic_css = array();
    $register_lost_password_font_settings = $login_form_font_settings['register_lost_password'];
    $register_lost_password_hover_font_settings = $login_form_font_settings['register_lost_password']['hover'];
//////////////////////////////////////////////////////////////////////////
    if (isset($register_lost_password_font_settings['font-color']) && $register_lost_password_font_settings['font-color'] != '') {
        $register_lost_password_font_settings_dynamic_css[] = "color:{$register_lost_password_font_settings['font-color']};";
    }

    if (isset($register_lost_password_font_settings['font-size']) && $register_lost_password_font_settings['font-size'] != '') {
        $register_lost_password_font_settings_dynamic_css[] = "font-size:{$register_lost_password_font_settings['font-size']}px;";
    }

    if (isset($register_lost_password_font_settings['google-fonts']) && $register_lost_password_font_settings['google-fonts'] != '') {
        if (!in_array($register_lost_password_font_settings['google-fonts'], $google_fonts_used_array)) {
            array_push($google_fonts_used_array, preg_replace('/\s/', '+', $register_lost_password_font_settings['google-fonts']));
        }
        $register_lost_password_font_settings_dynamic_css[] = "font-family: {$register_lost_password_font_settings['google-fonts']}, Dashicons;";
    }

    if (!empty($register_lost_password_font_settings_dynamic_css)) {
        $register_lost_password_font_settings_dynamic_css = implode(' ', $register_lost_password_font_settings_dynamic_css);
    } else {
        $register_lost_password_font_settings_dynamic_css = '';
    }

    /* Hover settings */
    $register_lost_password_hover_font_settings_dynamic_css = array();
    if (isset($register_lost_password_hover_font_settings['font-color']) && $register_lost_password_hover_font_settings['font-color'] != '') {
        $register_lost_password_hover_font_settings_dynamic_css[] = "color:{$register_lost_password_hover_font_settings['font-color']};";
    }

    if (!empty($register_lost_password_hover_font_settings_dynamic_css)) {
        $register_lost_password_hover_font_settings_dynamic_css = implode(' ', $register_lost_password_hover_font_settings_dynamic_css);
    } else {
        $register_lost_password_hover_font_settings_dynamic_css = '';
    }

    /* Hover settings */
//////////////////////////////////////////////////////////////////////////
    ?>
        .login-form-template-1 #login p#nav { <?php echo $register_lost_password_font_settings_dynamic_css; ?> }
        .login-form-<?php echo $login_form_template; ?> #login p#nav a, .login-form-<?php echo $login_form_template; ?> #login p#backtoblog a { <?php echo $register_lost_password_font_settings_dynamic_css; ?> }
        .login-form-<?php echo $login_form_template; ?> #login p#nav a:hover, .login-form-<?php echo $login_form_template; ?> #login p#backtoblog a:hover,
        .login-form-<?php echo $login_form_template; ?> #login p#nav a:hover, .login-form-<?php echo $login_form_template; ?> #login p#backtoblog a:hover { <?php echo $register_lost_password_hover_font_settings_dynamic_css; ?> }

    </style>
    <?php
    $google_fonts = implode('|', $google_fonts_used_array);
    if (!empty($google_fonts)) {
        ?>
        <link href="https://fonts.googleapis.com/css?family=<?php echo $google_fonts; ?>" rel="stylesheet">
        <?php
    }
    ?>
    <?php
}

function login_classes($classes) {
    $classes[] = 'eat-custom-login-class';
    return $classes;
}

function div_before_login_form() {
    $plugin_settings = get_option('eat_admin_theme_settings');
    // echo "<pre>";
    // print_r($plugin_settings['custom_login']);
    // echo "</pre>";

    $login_form_template = $plugin_settings['custom_login']['login_form']['template'];
    $custom_login_settings = $plugin_settings['custom_login'];
    $background_type = $custom_login_settings['background']['type'];
    $background_image = $custom_login_settings['background']['image']['url'];

    $wrap_inner_styles = '';
    $outer_wrap_attributes = 'data-parallax-source="' . esc_attr($background_type) . '"';
    $dynamic_classes = '';
    if ($background_type == 'image') {
        $imgWidth = '880';
        $imgHeight = '400';
        $bg_image_url = $background_image;
        $outer_wrap_attributes .= ' data-parallax-image="' . esc_url($bg_image_url) . '"';
        $outer_wrap_attributes .= ' data-parallax-image-width="' . esc_attr($imgWidth) . '"';
        $outer_wrap_attributes .= ' data-parallax-image-height="' . esc_attr($imgHeight) . '"';
        $wrap_inner_styles .= 'background-image: url(\'' . esc_url($bg_image_url) . '\');';
    }

    if ($background_type === 'background-color') {
        $background_color = $custom_login_settings['background']['background-color']['color'];
        $outer_wrap_attributes = '';
        $wrap_inner_styles .= "background: $background_color";
    }

    if ($background_type === 'video') {
        $video_options = $custom_login_settings['background']['video'];
        $video_type = $video_options['type'];
        $video_start_time = $video_options['start-time'];
        $video_end_time = $video_options['end-time'];

        if ($video_type == 'html5') {
            $videos = '';
            $html5_mp4_url = $video_options['html5']['mp4-video-url'];
            $html5_webm_url = $video_options['html5']['webm-video-url'];
            $html5_webm_url = $video_options['html5']['ogv-video-url'];
            if (isset($html5_mp4_url) && $html5_mp4_url) {
                if ($html5_mp4_url) {
                    $videos .= 'mp4:' . esc_url($html5_mp4_url);
                }
            }
            if (isset($html5_webm_url) && $html5_webm_url) {
                if ($html5_webm_url) {
                    if ($videos) {
                        $videos .= ',';
                    }
                    $videos .= 'webm:' . esc_url($html5_webm_url);
                }
            }
            if (isset($html5_ogv_url) && $html5_ogv_url) {
                if ($html5_ogv_url) {
                    if ($videos) {
                        $videos .= ',';
                    }
                    $videos .= 'ogv:' . esc_url($html5_ogv_url);
                }
            }
            $outer_wrap_attributes .= ' data-parallax-video="' . esc_attr($videos) . '"';
        }

        if ($video_type == 'youtube') {
            $youtube_options = $video_options['youtube'];
            $youtube_url = $youtube_options['video-url'];
            $outer_wrap_attributes .= ' data-parallax-video="' . esc_attr($youtube_url) . '"';
        }

        if ($video_type == 'viemo') {
            $viemo_options = $video_options['viemo'];
            $viemo_url = $viemo_options['video-url'];
            $outer_wrap_attributes .= ' data-parallax-video="' . esc_attr($viemo_url) . '"';
        }
        $outer_wrap_attributes .= ' data-parallax-video-start-time="' . esc_attr($video_start_time) . '"';
        $outer_wrap_attributes .= ' data-parallax-video-end-time="' . esc_attr($video_end_time) . '"';
    }

    if (isset($custom_login_settings['background']['parallax']['enable']) && ($background_type == 'video' || $background_type == 'image')) {
        $parallax_options = $custom_login_settings['background']['parallax'];
        $dynamic_classes .= 'eat-prallax-enabled';
        $awb_parallax = $parallax_options['type'];
        $awb_parallax_speed = isset($parallax_options['speed']) ? $parallax_options['speed'] : '0.5';
        $awb_parallax_mobile = isset($parallax_options['enable-on-mobile-devices']) ? 'true' : 'false';
        if ($awb_parallax == 'scroll' || $awb_parallax == 'scale' || $awb_parallax == 'opacity' || $awb_parallax == 'scroll-opacity' || $awb_parallax == 'scale-opacity') {
            $outer_wrap_attributes .= ' data-parallax-type="' . esc_attr($awb_parallax) . '"';
            $outer_wrap_attributes .= ' data-parallax-speed="' . esc_attr($awb_parallax_speed) . '"';
            $outer_wrap_attributes .= ' data-parallax-mobile="' . esc_attr($awb_parallax_mobile) . '"';
        }
    } else {
        $dynamic_classes .= 'eat-parallax-for-videos-fixes';
    }

    if ($login_form_template == 'default' || !isset($login_for_template)) {
        $template_classes = 'login-form-' . $login_form_template;
    } else {
        $template_classes = 'login-form-' . $login_form_template . ' login-form-template';
    }
    ?>
    <div class="eat-admin-theme-custom-login-wrap <?php echo $dynamic_classes; ?> <?php echo $template_classes; ?>" <?php echo $outer_wrap_attributes; ?> style="<?php echo $wrap_inner_styles; ?>">
        <?php
        include('parts/overlay.php');
    }

    function custom_login_scripts() {
        wp_enqueue_script('eat_jarallax_js', E_ADMIN_THEME_JS_DIR . '/jarallax.js', array('jquery'), E_ADMIN_THEME_VERSION, true);
        wp_enqueue_script('eat_jarallax_video_js', E_ADMIN_THEME_JS_DIR . '/jarallax-video.js', array('jquery'), E_ADMIN_THEME_VERSION, true);
        wp_enqueue_script('jquery');
        wp_register_style('font-awesome-icons-v4.7.0', E_ADMIN_THEME_CSS_DIR . '/font-awesome/font-awesome.min.css', false, E_ADMIN_THEME_VERSION);
        wp_enqueue_script('eat_custom_login_js', E_ADMIN_THEME_JS_DIR . '/eat-custom-login.js', array('jquery', 'eat_jarallax_js', 'eat_jarallax_video_js'), E_ADMIN_THEME_VERSION, true);

        $plugin_settings = get_option('eat_admin_theme_settings');
        $custom_login = $plugin_settings['custom_login'];
        $temp_plugin_settings = array(
            'login_template' => $custom_login['login_form']['template'],
                // 'ajax_nonce' => $ajax_nonce,
        );
        wp_localize_script('eat_custom_login_js', 'eat_custom_login_plugin_settings', $temp_plugin_settings);

        wp_register_style('custom-login-css', E_ADMIN_THEME_CSS_DIR . '/eat-custom-login.css', false, E_ADMIN_THEME_VERSION);
        wp_enqueue_style('font-awesome-icons-v4.7.0');
        wp_enqueue_style('custom-login-css');
    }

    function my_addition_to_login_footer() {
        // echo '<div style="text-align: center;"><a href="#">link</a></div>';
        ?>
        <!-- </div> -->
        <!-- Termination of div class message login wrap -->
    </div>
    <!-- Termination of div class eat-admin-theme-custom-login-wrap -->
    <?php
}

function w4_login_headertitle() {
    $plugin_settings = get_option('eat_admin_theme_settings');
    $custom_login = $plugin_settings['custom_login'];
    $logo_title = $custom_login['login_form']['logo']['title'];
    return $logo_title;
}

function the_url($url) {
    $plugin_settings = get_option('eat_admin_theme_settings');
    $custom_login = $plugin_settings['custom_login'];
    $logo_url = $custom_login['login_form']['logo']['url'];
    return $logo_url;
}

function the_login_message($message) {
    $plugin_settings = get_option('eat_admin_theme_settings');
    if (empty($message)) {
        // return "<div class='message-login-wrap'>";
    } else {
        return $message;
    }
}

function the_login_error_message_for_password($error) {
    global $errors;
    $err_codes = $errors->get_error_codes();
    $plugin_settings = get_option('eat_admin_theme_settings');
    $custom_login = $plugin_settings['custom_login'];
    $invalid_password_msg = $custom_login['login_form']['error-message']['invalid-password'];

    if (in_array('incorrect_password', $err_codes)) {
        $error = $invalid_password_msg;
    }
    return $error;
}

function the_login_error_message_for_username($error) {
    global $errors;
    $err_codes = $errors->get_error_codes();
    $plugin_settings = get_option('eat_admin_theme_settings');
    $custom_login = $plugin_settings['custom_login'];
    $invalid_username_msg = $custom_login['login_form']['error-message']['invalid-username'];

    if (in_array('invalid_username', $err_codes)) {
        $error = $invalid_username_msg;
    }
    return $error;
}
