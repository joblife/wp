<?php
$plugin_settings = get_option('eat_admin_theme_settings');
$template = $plugin_settings['general-settings']['template'];
if($template !=''){
}
if (everestAdminThemeClass::irEnableAdminTheme()) {
	add_action('admin_head', 'my_custom_scripts');
}
add_filter( 'admin_body_class', 'eat_add_admin_body_class' );
add_action( 'customize_controls_print_footer_scripts', 'eat_custom_customize_enqueue' );
/**
 * Enqueue script for custom customize control.
 */
function eat_custom_customize_enqueue() {
	$plugin_settings = get_option('eat_admin_theme_settings');
	$template = $plugin_settings['general-settings']['template'];
	$admin_menu_header = $plugin_settings['general-settings']['admin-menu-header'];
	if($template !=''){
		$template_class = 'eat-wp-toolbar-addition eat-wp-toolbar-addition-'.$template;
		$body_template_class = 'eat-body-class-wrap eat-dashboard-'.$template;
	}else{
		$template_class ='';
		$body_template_class = 'eat-body-class-wrap eat-wordpress-default-template';
	}
	wp_enqueue_script('jquery');
	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('body').addClass('<?php echo $body_template_class; ?>');
		});
	</script>
	<?php
}
/**
 * Adds one or more classes to the body tag in the dashboard.
 *
 * @param  String $classes Current body classes.
 * @return String          Altered body classes.
 */
function eat_add_admin_body_class( $classes ) {
	$plugin_settings = get_option('eat_admin_theme_settings');
	// echo "<pre>";
	// print_r($plugin_settings);
	// echo "</pre>";
	$template = $plugin_settings['general-settings']['template'];
	$admin_menu_header = $plugin_settings['general-settings']['admin-menu-header'];
	if(isset($plugin_settings['admin_bar']['layout']) && $plugin_settings['admin_bar']['layout'] == 'fixed'){
		$admin_bar_class ='eat-admin-bar-fixed';
	}else{
		$admin_bar_class ='';
	}

	if($template !=''){
		$body_template_class = "eat-body-class-wrap  eat-dashboard-$template ";
	}else{
		$body_template_class = 'eat-body-class-wrap eat-wordpress-default-template';
	}
    return "$classes $body_template_class $admin_bar_class";
    // Or:
    // return "$classes my_class_1 my_class_2 my_class_3";
}



function my_custom_scripts()
{
$plugin_settings = get_option('eat_admin_theme_settings');
$template = $plugin_settings['general-settings']['template'];
$admin_menu_header = $plugin_settings['general-settings']['admin-menu-header'];
if($template !=''){
	$template_class = 'eat-wp-toolbar-addition eat-wp-toolbar-addition-'.$template;
	$body_template_class = 'eat-body-class-wrap eat-dashboard-'.$template;
}else{
	$template_class ='';
	$body_template_class = '';
}
?>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('.wp-toolbar').addClass('<?php echo $template_class; ?>');
		<?php if($admin_menu_header['type'] == 'image'){ ?>
			$('#adminmenu').prepend("<li id='ir-admin-logo-item'><div class='ir-admin-logo ir-admin-image'><img src='<?php echo $admin_menu_header['image']['url']; ?>' alt='' /></div></li>");
			// $( "<div class='eat-admin-logo eat-admin-image'><img src='<?php // echo $admin_menu_header['image']['url']; ?>' alt='' /></div>" ).insertBefore( "#adminmenu" );
		<?php } ?>

		<?php if($admin_menu_header['type'] == 'texts'){
			$title = $admin_menu_header['text']['title'];

			$subtitle = $admin_menu_header['text']['subtitle'];
			?>
			$('#adminmenu').prepend(`
			<li id='ir-admin-logo-item'>
				<div id='ir-admin-logo-text' class='ir-admin-logo'>
					<div class='ir-admin-menu-logo-title'>
						<?php echo $title['text']; ?>
					</div>
					<div class='ir-admin-menu-logo-subtitle'>
						<?php echo $subtitle['text']; ?>
					</div>
				</div>
			</li>`);
			// $( "<div class='eat-admin-logo'><div class='eat-admin-menu-logo-title'><?php // echo $title['text']; ?></div><div class='eat-admin-menu-logo-subtitle'><?php echo $subtitle['text']; ?></div></div>" ).insertBefore( "#adminmenu" );
		<?php } ?>
		<?php if(($admin_menu_header['type'] =='texts' || $admin_menu_header['type'] =='image') && $admin_menu_header['background-color']['color'] !='' ){ ?>
			$('.eat-body-class-wrap .ir-admin-logo').css('background', "<?php echo $admin_menu_header['background-color']['color']; ?>");
		<?php } ?>

	});
</script>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		<?php
		$plugin_settings = get_option('eat_admin_theme_settings');
		$background_settings = $plugin_settings['general-settings']['background'];
		$background_overlay = $background_settings['overlay'];
		$background_type = $background_settings['type'];
		$data_attributes = array();

		if(isset($background_overlay['enable'])){
			if($background_type == 'image' || $background_type == 'video' ){
				$style_option1 = array();
				$overlay_color = $background_overlay['color'];
				?>
				$( "<div class='eat-common-overlay'></div>" ).insertBefore( '#adminmenumain' );
				$('.eat-common-overlay').css('background-color', '<?php echo $overlay_color; ?>');
				<?php
			}
		}

		if($background_type == 'image'){
			?>

			$('#wpwrap').css('background-image', "url(http://localhost/everest-admin-theme/wp-content/uploads/2017/08/bg-image.jpg)");
			<?php
			$image_settings = $background_settings['image'];
			$image_url = $image_settings['url'];
			$data_attributes[] = "'data-parallax-source':'image'";
			$data_attributes[] = "'data-parallax-image':'$image_url'";
			$data_attributes[] = "'data-parallax-image-width':'880'";
			$data_attributes[] = "'data-parallax-image-height':'400'";
			?>
			
			<?php
		}else if($background_type == 'video'){
			$data_attributes[] = "'data-parallax-source':'video'";

			$video_options = $background_settings['video'];
			$video_type = $video_options['type'];

			if($video_type == 'html5'){
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
				$data_attributes[] = "'data-parallax-video':'$videos'";
			}

			if($video_type == 'youtube'){
				$youtube_options = $video_options['youtube'];
				$youtube_url = $youtube_options['video-url'];
			    $data_attributes[] = "'data-parallax-video':'$youtube_url'";
			}

			if($video_type == 'viemo'){
				$viemo_options = $video_options['viemo'];
				$viemo_url = $viemo_options['video-url'];
				$data_attributes[] = "'data-parallax-video':'$viemo_url'";
			}

			$video_start_time = $video_options['start-time'];
			$video_end_time =$video_options['end-time'];

			$data_attributes[] = "'data-parallax-video-start-time':'$video_start_time'";
			$data_attributes[] = "'data-parallax-video-end-time':'$video_end_time'";
		}else if($background_type == 'background-color'){
			$background_color = $background_settings['background-color']['color'];
			?>
			$('#wpwrap').css('background-color', "<?php echo $background_color; ?>");
			<?php
		}


		$parallax_option = $background_settings['parallax'];
		if(isset($parallax_option['enable'])){

			$awb_parallax = $parallax_option['type'];
			$awb_parallax_speed = isset($parallax_option['speed']) ? $parallax_option['speed'] : '0.5';
			$awb_parallax_mobile = isset($parallax_option['enable-on-mobile-devices']) ? 'true' : 'false';
			if ($awb_parallax == 'scroll' || $awb_parallax == 'scale' || $awb_parallax == 'opacity' || $awb_parallax == 'scroll-opacity' || $awb_parallax == 'scale-opacity') {
				$data_attributes[] = "'data-parallax-type':'$awb_parallax'";
				$data_attributes[] = "'data-parallax-speed':'$awb_parallax_speed'";
				$data_attributes[] = "'data-parallax-mobile':'$awb_parallax_mobile'";
			}
			?>
			$('#wpwrap').addClass('eat-prallax-enabled');
			<?php
		}else{
			echo "$('#wpwrap').addClass('eat-prallax-enabled eat-parallax-for-videos-fixes');";
		}
		?>
		$('#wpwrap').attr({
						    <?php echo implode(',', $data_attributes); ?>
						});

		$('.eat-prallax-enabled').each(function () {
		    var $this = $(this);
		    var type = $this.attr('data-parallax-source');
		    var image = false;
		    var imageWidth = false;
		    var imageHeight = false;
		    var video = false;
		    var videoStartTime = false;
		    var videoEndTime = false;
		    var parallax = $this.attr('data-parallax-type');
		    var parallaxSpeed = $this.attr('data-parallax-speed');
		    var parallaxMobile = $this.attr('data-parallax-mobile') !== 'false';

		    // image type
		    if (type === 'image') {
		        image = $this.attr('data-parallax-image');
		        imageWidth = $this.attr('data-parallax-image-width');
		        imageHeight = $this.attr('data-parallax-image-height');
		    }

		    // video type
		    if (type === 'video') {
		        video = $this.attr('data-parallax-video');
		        videoStartTime = $this.attr('data-parallax-video-start-time');
		        videoEndTime = $this.attr('data-parallax-video-end-time');
		    }

		    // prevent if no parallax and no video
		    if (!parallax && !video) {
		        return;
		    }

		    var jarallaxParams = {
		        type: parallax,
		        imgSrc: image,
		        imgWidth: imageWidth,
		        imgHeight: imageHeight,
		        speed: parallaxSpeed,
		        noAndroid: !parallaxMobile,
		        noIos: !parallaxMobile
		    };

		    if (video) {
		        jarallaxParams.speed = parallax ? parallaxSpeed : 1;
		        jarallaxParams.videoSrc = video;
		        jarallaxParams.videoStartTime = videoStartTime;
		        jarallaxParams.videoEndTime = videoEndTime;
		    }

		    $this.jarallax(jarallaxParams);
		});

	});


</script>



<?php
}
