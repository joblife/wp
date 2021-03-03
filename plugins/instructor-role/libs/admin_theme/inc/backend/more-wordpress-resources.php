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
                        <h3><?php _e('More WordPress Resources', 'everest-admin-theme'); ?></h3>
                        <div class="eat-tab-wrapper">
                            <p><strong>Everest Admin Theme </strong> works best with every WordPress theme. It's even more remarkable when used with popular themes like VMagazine and AccessPress Parallax.</p>

                            <p>AND IF THIS PLUGIN HAS IMPRESSED YOU, THEN YOU WOULD ENJOY OUR OTHER PROJECTS TOO. DO CHECK THESE OUT :</p>

                            <p><a href="https://wpall.club/">WPAll Club</a> -  A complete WordPress resources club. WordPress tutorials, blogs, curated free and premium themes and plugins, WordPress deals, offers, hosting info and more.</p>

                            <p> <a href="https://themeforest.net/user/accesskeys/portfolio">Premium WordPress Themes</a> -   6 premium WordPress themes well suited for all sort of websites. Professional, well coded and highly configurable themes for you. </p>

                            <p>  <a href="https://codecanyon.net/user/accesskeys/portfolio?Ref=AccessKeys">Premium WordPress Plugins</a> - 45+ premium WordPress plugins of many different types. High user ratings, great quality and best sellers in CodeCanyon marketplace. </p>

                            <p>  <a href="https://accesspressthemes.com/">AccessPress Themes</a> - AccessPress Themes has 50+ beautiful and elegant, fully responsive, multipurpose themes to meet your need for free and commercial basis.</p>

                            <p>  <a href="https://8degreethemes.com/">8Degree Themes</a> - 8Degree Themes offers 15+ free WordPress themes and 16+ premium WordPress themes carefully crafted with creativity.</p>
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