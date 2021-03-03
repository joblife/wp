<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<div class="eat-about-main-wrapper">
    <div class="eat-header">
        <div>
            <div id="eat-fb-root"></div>
            <script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4"; fjs.parentNode.insertBefore(js, fjs); }(document, 'script', 'facebook-jssdk'));</script>
            <script>!function(d, s, id){var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location)?'http':'https'; if (!d.getElementById(id)){js = d.createElement(s); js.id = id; js.src = p + '://platform.twitter.com/widgets.js'; fjs.parentNode.insertBefore(js, fjs); }}(document, 'script', 'twitter-wjs');</script>
        </div>
        <div class="eat-header-section">
            <div class="eat-header-left">
                <?php /* ?>
                <div class="eat-title"><?php _e('Everest Admin Theme', 'everest-admin-theme'); ?></div>
                <div class="eat-version-wrap">
                    <span>Version <?php echo E_ADMIN_THEME_VERSION; ?></span>
                </div>
                <?php */ ?>
                <img src='<?php echo E_ADMIN_THEME_IMAGE_DIR; ?>/header-logo.png' alt='Header logo' />
            </div>

            <div class="eat-header-social-link">
                <p class="eat-follow-us"><?php _e('Follow us for new updates', 'everest-admin-theme'); ?></p>
                <div class="fb-like" data-href="https://www.facebook.com/accesspressthemes" data-layout="button" data-action="like" data-show-faces="true" data-share="false"></div>
                <a href="https://twitter.com/accesspressthemes" class="twitter-follow-button" data-show-count="false">Follow @accesspressthemes</a>
            </div>
        </div>
    </div>

    <div class="eat-how-to-use-container">
        <div class="eat-column-one-wrap">
            <div class="eat-panel-body">
                <div class="eat-row">
                    <div class="eat-col-three-third">
                        <h3><?php _e('About Us', 'everest-admin-theme'); ?></h3>
                        <div class="eat-tab-wrapper">
                            <p><strong><?php _e('Everest Admin Theme - #1 WordPress Backend customizer', 'everest-admin-theme') ?></strong> <?php _e('- is a Premium WordPress Plugin by AccessPress Themes.', 'everest-admin-theme'); ?> </p>

                            <p><?php _e('AccessPress Themes is a venture of Access Keys - who has developed hundreds of Custom WordPress themes and plugins for its clients over the years. ', 'everest-admin-theme'); ?></p>

                            <p><strong><?php _e('Everest Admin Theme', 'everest-admin-theme') ?></strong><?php _e(" - makes your WordPress website's admin dashboard look non-WordPess. Though WordPress itself is great, many of you might want to white-label the WP Admin dashboard and give it a complete different look, rearrange the menus and stuff and make it more customized for you and your clients.", 'everest-admin-theme'); ?></p>
                            <div class="eat-halfseperator"></div>
                            <p><strong><?php _e('Please visit our product page for more details here:', 'everest-admin-theme'); ?></strong>
                                <br />
                                <a href="https://accesspressthemes.com/wordpress-plugins/everest-admin-theme" target="_blank">https://accesspressthemes.com/wordpress-plugins/everest-admin-theme</a>
                            </p>
                            <div class="eat-halfseperator"></div>
                            <p><strong><?php _e('Please visit our demo page here:', 'everest-admin-theme'); ?></strong>
                                <br />
                                <a href="http://demo.accesspressthemes.com/wordpress-plugins/everest-admin-theme" target="_blank">http://demo.accesspressthemes.com/wordpress-plugins/everest-admin-theme/</a>
                            </p>

                            <p>&nbsp;</p>
                            <h3 class="eat-sub-title">More from AccessPress themes </h3>
                            <div class="eat-row">
                                <div class="eat-col-one-third">
                                    <div class="eat-product">
                                        <div class="eat-logo-product">
                                            <a href="http://accesspressthemes.com/plugins/" target="_blank">
                                                <img src="<?php echo E_ADMIN_THEME_IMAGE_DIR; ?>/plugin.png" alt="<?php esc_attr_e('AccessPress Social Icons', 'everest-admin-theme'); ?>" />
                                            </a>
                                        </div>
                                        <div class="eat-productext">
                                            <p><strong>WordPress Plugins</strong>
                                                <br />
                                                <a href="http://accesspressthemes.com/plugins/" target="_blank">http://accesspressthemes.com/plugins/</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="eat-col-one-third">
                                    <div class="eat-product">
                                        <div class="eat-logo-product">
                                            <a href="http://accesspressthemes.com/themes/" target="_blank"><img src="<?php echo E_ADMIN_THEME_IMAGE_DIR; ?>/theme.png" /></a>
                                        </div>
                                        <div class="eat-productext">
                                            <p><strong>WordPress Themes</strong>
                                                <br />
                                                <a href="http://accesspressthemes.com/themes/" target="_blank">http://accesspressthemes.com/themes/</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>



                                <div class="eat-col-one-third">
                                    <div class="eat-product">
                                        <div class="eat-logo-product">
                                            <a href="http://accesspressthemes.com/contact/" target="_blank"><img src="<?php echo E_ADMIN_THEME_IMAGE_DIR; ?>/customize.png" /></a>
                                        </div>
                                        <div class="eat-productext">
                                            <p><strong>WordPress Customization</strong>
                                                <br />
                                                <a href="http://accesspressthemes.com/contact/" target="_blank">http://accesspressthemes.com/wordpress-plugins/contact/</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <hr />
                            <h3><?php _e('Get in touch', 'everest-admin-theme'); ?></h3>
                            <p><?php _e('If you have any question/feedback, please get in touch:', 'everest-admin-theme'); ?></p>
                            <p>
                                <strong>General enquiries:</strong> <a href="mailto:info@accesspressthemes.com">info@accesspressthemes.com</a><br />
                                <strong>Support:</strong> <a href="mailto:support@accesspressthemes.com">support@accesspressthemes.com</a><br />
                                <strong>Sales:</strong> <a href="mailto:sales@accesspressthemes.com">sales@accesspressthemes.com</a>
                            </p>
                            <div class="eat-seperator"></div>
                            <div class="eat-dottedline"></div>
                            <div class="eat-seperator"></div>
                        </div>
                    </div>
                    <div class="eat-col-three-third">
                        <h3><?php _e('Get social', 'everest-admin-theme'); ?></h3>
                        <p><?php _e('Get connected with us on social media. Facebook is the best place to find updates on our themes/plugins: ', 'everest-admin-theme'); ?></p>

                        <p><strong>Like us on facebook:</strong>
                            <br />
                            <iframe style="border: none; overflow: hidden; width: 700px; height: 250px;" src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FAccessPress-Themes%2F1396595907277967&amp;width=842&amp;height=258&amp;colorscheme=light&amp;show_faces=true&amp;header=false&amp;stream=false&amp;show_border=true&amp;appId=1411139805828592" width="240" height="150" frameborder="0" scrolling="no"></iframe>
                        </p>

                        <ul class="eat-about eat-unstyled eat-inlinelist">
                            <li><a href="https://plus.google.com/u/0/+Accesspressthemesprofile/about" target="_blank"><img src="<?php echo E_ADMIN_THEME_IMAGE_DIR; ?>/googleplus.png" alt="google+"></a>
                            </li>
                            <li><a href="http://www.pinterest.com/accesspresswp/" target="_blank"><img src="<?php echo E_ADMIN_THEME_IMAGE_DIR; ?>/pinterest.png" alt="pinterest"></a>
                            </li>
                            <li><a href="https://www.flickr.com/photos/accesspressthemes/" target="_blank"><img src="<?php echo E_ADMIN_THEME_IMAGE_DIR; ?>/flicker.png" alt="flicker"></a>
                            </li>
                            <li><a href="https://twitter.com/apthemes" target="_blank"><img src="<?php echo E_ADMIN_THEME_IMAGE_DIR; ?>/twitter.png" alt="twitter"></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>

<style type="text/css">
	/*==========ABOUT-US================*/
.eat-about-main-wrapper {
    border: 1px solid #272B33;
    border-top: 0;
    background: #fff;
    margin-right: 20px;
    margin-top: 25px;
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
}
.eat-header {
    background-color: #272B33;
    margin-right: 7px;
    padding: 20px;
    width: 100%;
    margin-top: 3px;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
.eat-header-left {
    font-size: 30px;
    color: #fff;
    display: inline-block;
    padding-top: 10px;
}
.eat-header-left .eat-title {
    text-transform: uppercase;
}
.eat-version-wrap span {
    font-size: 12px;
}
.eat-header-social-link {
    float: right;
}
.eat-header-social-link iframe {
    display: inline-block;
    vertical-align: top;
}
.eat-follow-us {
    color: #fff;
    margin-top: 0;
}
.eat-how-to-use-container {
    padding: 10px 20px;
}
.eat-col-three-third:first-child {
    margin-bottom: 30px;
}
.eat-col-three-third h3 {
    font-size: 24px;
    font-weight: 500;
    margin: 15px 0;
}
.eat-tab-wrapper strong {
    font-weight: 700;
}
.eat-col-one-third {
    width: 33.3333%;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    float: left;
    padding-left: 10px;
    padding-right: 10px;
    margin-bottom: 20px;
}
.eat-about.eat-inlinelist li {
    display: inline-block;
    -moz-transition: all 0.3s ease-in-out 0s;
    -webkit-transition: all 0.3s ease-in-out 0s;
    transition: all 0.3s ease-in-out 0s;
}
.eat-about.eat-inlinelist li:hover {
    -moz-transform: translate3d(0px, -4px, 0px);
    -webkit-transform: translate3d(0px, -4px, 0px);
    transform: translate3d(0px, -4px, 0px);
}

.wp-picker-container, .wp-picker-container:active {
    display: inline-block;
    outline: 0;
}
.eat-counter-main-wrap .wp-picker-input-wrap label {
    float: none;
    width: auto;
    margin-right: 0;
}

</style>