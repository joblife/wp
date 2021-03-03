<?php
/**
@package Social Media Widget
Plugin Name: New Social Media Widget
Plugin URI:  http://awplife.com/
Description: The Social Media Widget is a simple sidebar widget that allows users to input their social media website profile URLs and other subscription options to show an icon on the sidebar to that social media site and more that open up in a separate browser window.
Version:     1.1.7
Author:      A WP Life
Text Domain: new-social-media-widget
Author URI:  http://awplife.com/
Domain Path: /languages
License:     GPL2

Social Media is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Social Media is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Social Media. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html.
*/


//Load text domain
add_action( 'plugins_loaded', 'NSMW_load_textdomain' );
function NSMW_load_textdomain() {
	load_plugin_textdomain( 'new-social-media-widget', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

if (!class_exists('New_Social_Media')) {
class New_Social_Media extends WP_Widget {

	/**
	 * Sets up the widgets name
	 */
	public function __construct() {
	define("NSMW_TXTDM", "new-social-media-widget");
	
		$sm_widget_ops = array( 
			'classname' => 'new_social_media_widget',
			'description' => 'Display Social Media Profiles',
		);
		parent::__construct( 'new_social_media_widget', __('Social Media Widget', NSMW_TXTDM) , $sm_widget_ops );
		
	}

	/**
	 * Widget Output
	 */
	public function widget( $args, $instance ) {
		//load css 
		wp_enqueue_style ( 'nsmw-font-awesome-css', plugin_dir_url( __FILE__ ) . 'css/font-awesome.css');
		wp_enqueue_style ( 'nsmw-bootstrap-css', plugin_dir_url( __FILE__ ) . 'css/output-bootstrap.css');
		wp_enqueue_style ( 'nsmw-hover-min-css', plugin_dir_url( __FILE__ ) . 'css/hover-min.css');

		//load save settings
		$nsmw_widget_id = ! empty( $instance['nsmw_widget_id'] ) ? $instance['nsmw_widget_id'] : '';
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$facebook = ! empty( $instance['facebook'] ) ? $instance['facebook'] : '';
		$twitter = ! empty( $instance['twitter'] ) ? $instance['twitter'] : '';
		$google_plus = ! empty( $instance['google_plus'] ) ? $instance['google_plus'] : '';
		$linkedin = ! empty( $instance['linkedin'] ) ? $instance['linkedin'] : '';
		$instagram = ! empty( $instance['instagram'] ) ? $instance['instagram'] : '';
		$pinterest = ! empty( $instance['pinterest'] ) ? $instance['pinterest'] : '';
		$flickr = ! empty( $instance['flickr'] ) ? $instance['flickr'] : '';
		$tumblr = ! empty( $instance['tumblr'] ) ? $instance['tumblr'] : '';
		$vimeo = ! empty( $instance['vimeo'] ) ? $instance['vimeo'] : '';
		$youtube = ! empty( $instance['youtube'] ) ? $instance['youtube'] : '';
		$rss = ! empty( $instance['rss'] ) ? $instance['rss'] : '';
		$email = ! empty( $instance['email'] ) ? $instance['email'] : '';
		$whatsapp = ! empty( $instance['whatsapp'] ) ? $instance['whatsapp'] : '';
		
		$columns = ! empty( $instance['columns'] ) ? $instance['columns'] : '';
		$icon_size = ! empty( $instance['icon_size'] ) ? $instance['icon_size'] : '';
		$padding = ! empty( $instance['padding'] ) ? $instance['padding'] : '';
		$background = ! empty( $instance['background'] ) ? $instance['background'] : '';
		$margin_top = ! empty( $instance['margin_top'] ) ? $instance['margin_top'] : '';
		$margin_bottom = ! empty( $instance['margin_bottom'] ) ? $instance['margin_bottom'] : '';
		$div_bg_color = ! empty( $instance['div_bg_color'] ) ? $instance['div_bg_color'] : '';
		$icon_color = ! empty( $instance['icon_color'] ) ? $instance['icon_color'] : '';
		$css = ! empty( $instance['css'] ) ? $instance['css'] : '';
		?>
		<style>
		<?php echo $css; ?>
		.row .nsmw-div-<?php echo $nsmw_widget_id; ?> {
			margin-bottom : <?php echo $margin_bottom; ?>px !important ;
			margin-top : <?php echo $margin_top; ?>px !important ;
			padding : <?php echo $padding; ?>px !important ;
			background-color:<?php echo $div_bg_color; ?> !important ; 
			clear: none !important;
		}	
		.social-media-link-<?php echo $nsmw_widget_id; ?> {
			color: <?php echo $icon_color; ?> !important ;
		}
		</style>		
		<?php
		echo $args['before_widget'];
		// widget title
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		?>
		<div class="row text-center">	
			<?php if($facebook) { ?>
				<a href="<?php echo esc_url($facebook); ?>" class="social-media-link-<?php echo $nsmw_widget_id; ?>">
					<div class="nsmw-div-<?php echo $nsmw_widget_id; ?> <?php echo $columns; ?>">
						<i class='fa fa-facebook fa-<?php echo $icon_size; ?>x' aria-hidden='true'></i>
					</div>
				</a>
			<?php } ?>
			<?php if($twitter) { ?>
			<a href="<?php echo esc_url($twitter); ?>" class="social-media-link-<?php echo $nsmw_widget_id; ?>"><div class="nsmw-div-<?php echo $nsmw_widget_id; ?> <?php echo $columns; ?>"><i class='fa fa-twitter fa-<?php echo $icon_size; ?>x' aria-hidden='true'></i></div></a>
			<?php } ?>
			<?php if($google_plus) { ?>
			<a href="<?php echo esc_url($google_plus); ?>" class="social-media-link-<?php echo $nsmw_widget_id; ?>"><div class="nsmw-div-<?php echo $nsmw_widget_id; ?> <?php echo $columns; ?>"><i class='fa fa-google-plus fa-<?php echo $icon_size; ?>x' aria-hidden='true'></i></div></a>
			<?php } ?>
			<?php if($linkedin) { ?>
			<a href="<?php echo esc_url($linkedin); ?>" class="social-media-link-<?php echo $nsmw_widget_id; ?>"><div class="nsmw-div-<?php echo $nsmw_widget_id; ?> <?php echo $columns; ?>"><i class='fa fa-linkedin fa-<?php echo $icon_size; ?>x' aria-hidden='true'></i></div></a>
			<?php } ?>
			<?php if($instagram) { ?>
			<a href="<?php echo esc_url($instagram); ?>" class="social-media-link-<?php echo $nsmw_widget_id; ?>"><div class="nsmw-div-<?php echo $nsmw_widget_id; ?> <?php echo $columns; ?>"><i class='fa fa-instagram fa-<?php echo $icon_size; ?>x' aria-hidden='true'></i></div></a>
			<?php } ?>
			<?php if($pinterest) { ?>
			<a href="<?php echo esc_url($pinterest); ?>" class="social-media-link-<?php echo $nsmw_widget_id; ?>"><div class="nsmw-div-<?php echo $nsmw_widget_id; ?> <?php echo $columns; ?>"><i class='fa fa-pinterest fa-<?php echo $icon_size; ?>x' aria-hidden='true'></i></div></a>
			<?php } ?>
			<?php if($flickr) { ?>
			<a href="<?php echo esc_url($flickr); ?>" class="social-media-link-<?php echo $nsmw_widget_id; ?>"><div class="nsmw-div-<?php echo $nsmw_widget_id; ?> <?php echo $columns; ?>"><i class='fa fa-flickr fa-<?php echo $icon_size; ?>x' aria-hidden='true'></i></div></a>
			<?php } ?>
			<?php if($tumblr) { ?>
			<a href="<?php echo esc_url($tumblr); ?>" class="social-media-link-<?php echo $nsmw_widget_id; ?>"><div class="nsmw-div-<?php echo $nsmw_widget_id; ?> <?php echo $columns; ?>"><i class='fa fa-tumblr fa-<?php echo $icon_size; ?>x' aria-hidden='true'></i></div></a>
			<?php } ?>
			<?php if($vimeo) { ?>
			<a href="<?php echo esc_url($vimeo); ?>" class="social-media-link-<?php echo $nsmw_widget_id; ?>"><div class="nsmw-div-<?php echo $nsmw_widget_id; ?> <?php echo $columns; ?>"><i class='fa fa-vimeo fa-<?php echo $icon_size; ?>x' aria-hidden='true'></i></div></a>
			<?php } ?>
			<?php if($youtube) { ?>
			<a href="<?php echo esc_url($youtube); ?>" class="social-media-link-<?php echo $nsmw_widget_id; ?>"><div class="nsmw-div-<?php echo $nsmw_widget_id; ?> <?php echo $columns; ?>"><i class='fa fa-youtube fa-<?php echo $icon_size; ?>x' aria-hidden='true'></i></div></a>
			<?php } ?>
			<?php if($rss) { ?>
			<a href="<?php echo esc_url($rss); ?>" class="social-media-link-<?php echo $nsmw_widget_id; ?>"><div class="nsmw-div-<?php echo $nsmw_widget_id; ?> <?php echo $columns; ?>"><i class='fa fa-rss fa-<?php echo $icon_size; ?>x' aria-hidden='true'></i></div></a>
			<?php } ?>
			<?php if($whatsapp) { ?>
			<a href="<?php echo esc_url($whatsapp); ?>" class="social-media-link-<?php echo $nsmw_widget_id; ?>"><div class="nsmw-div-<?php echo $nsmw_widget_id; ?> <?php echo $columns; ?>"><i class='fa fa-whatsapp fa-<?php echo $icon_size; ?>x' aria-hidden='true'></i></div></a>
			<?php } ?>
			<?php if($email) { ?>
			<a href="mailto:<?php echo $email; ?>" class="social-media-link-<?php echo $nsmw_widget_id; ?>"><div class="nsmw-div-<?php echo $nsmw_widget_id; ?> <?php echo $columns; ?>"><i class='fa fa-envelope-o fa-<?php echo $icon_size; ?>x' aria-hidden='true'></i></div></a>
			<?php } ?>
			
		</div>
		<?php
		echo $args['after_widget'];
	}
	
	/**
	 * Widget Administrator From
	 */
	public function form( $instance ) {
		//print_r($instance);
		//css
		wp_enqueue_style( 'wp-color-picker' ); 
		wp_enqueue_style ( 'nsmw-font-awesome-css', plugin_dir_url( __FILE__ ) . 'css/font-awesome.css');
		// js
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'nsmw-color-picker-js',  plugin_dir_url( __FILE__ ).'js/nsmw-color-picker.js', array( 'jquery', 'wp-color-picker' ), '', true  );
		wp_enqueue_script( 'jquery-ui-sortable' );		

		// outputs the options form on admin
		$nsmw_widget_id = ! empty( $instance['nsmw_widget_id'] ) ? $instance['nsmw_widget_id'] : '';
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$facebook = ! empty( $instance['facebook'] ) ? $instance['facebook'] : '';
		$twitter = ! empty( $instance['twitter'] ) ? $instance['twitter'] : '';
		$google_plus = ! empty( $instance['google_plus'] ) ? $instance['google_plus'] : '';
		$linkedin = ! empty( $instance['linkedin'] ) ? $instance['linkedin'] : '';
		$instagram = ! empty( $instance['instagram'] ) ? $instance['instagram'] : '';
		$pinterest = ! empty( $instance['pinterest'] ) ? $instance['pinterest'] : '';
		$flickr = ! empty( $instance['flickr'] ) ? $instance['flickr'] : '';
		$tumblr = ! empty( $instance['tumblr'] ) ? $instance['tumblr'] : '';
		$vimeo = ! empty( $instance['vimeo'] ) ? $instance['vimeo'] : '';
		$youtube = ! empty( $instance['youtube'] ) ? $instance['youtube'] : '';		
		$rss = ! empty( $instance['rss'] ) ? $instance['rss'] : '';
		$email = ! empty( $instance['email'] ) ? $instance['email'] : '';
		$whatsapp = ! empty( $instance['whatsapp'] ) ? $instance['whatsapp'] : '';
		
		//widget display setting
		$columns = ! empty( $instance['columns'] ) ? $instance['columns'] : 'col-md-3';		
		$icon_size = ! empty( $instance['icon_size'] ) ? $instance['icon_size'] : '3x';
		$padding = ! empty( $instance['padding'] ) ? $instance['padding'] : '';
		$background = ! empty( $instance['background'] ) ? $instance['background'] : '';		
		$margin_top = ! empty( $instance['margin_top'] ) ? $instance['margin_top'] : '';		
		$margin_bottom = ! empty( $instance['margin_bottom'] ) ? $instance['margin_bottom'] : '';		
		$div_bg_color = ! empty( $instance['div_bg_color'] ) ? $instance['div_bg_color'] : 'red';		
		$icon_color = ! empty( $instance['icon_color'] ) ? $instance['icon_color'] : 'white';		
		$css = ! empty( $instance['css'] ) ? $instance['css'] : '';	
		?>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'nsmw_widget_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'nsmw_widget_id' ) ); ?>" type="hidden" value="<?php echo esc_attr( $nsmw_widget_id ); ?>">
		<p>
			<label><?php _e('Widget Title :', NSMW_TXTDM); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		
		<p id="smps-<?php echo $nsmw_widget_id; ?>" onclick="return ClickToggle(this.id, 'social-media-urls-<?php echo $nsmw_widget_id; ?>');">
			<a class="button button-primary">
				<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;&nbsp;<?php _e('Social Media Profile Settings', NSMW_TXTDM); ?> 
			</a>
		</p>
		
		<?php $pos = 1;?>
		<div id="social-media-urls-<?php echo $nsmw_widget_id; ?>" style="display: none;">
			<hr>
			<p>
				<label><?php _e( 'Facebook :', NSMW_TXTDM ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'facebook' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'facebook' ) ); ?>" type="text" value="<?php echo esc_attr( $facebook ); ?>">
				<input id="pos[]" name="pos[]" type="hidden" value="<?php echo $pos++; ?>">
			</p>
			<p>
				<label><?php _e( 'Twitter :', NSMW_TXTDM ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'twitter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'twitter' ) ); ?>" type="text" value="<?php echo esc_attr( $twitter ); ?>">
				<input id="pos[]" name="pos[]" type="hidden" value="<?php echo $pos++; ?>">
			</p>
			<p>
				<label><?php _e( 'Google-Plus :', NSMW_TXTDM ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'google_plus' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'google_plus' ) ); ?>" type="text" value="<?php echo esc_attr( $google_plus ); ?>">
				<input id="pos[]" name="pos[]" type="hidden" value="<?php echo $pos++; ?>">
			</p>
			<p>
				<label><?php _e( 'Linkedin :', NSMW_TXTDM ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'linkedin' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'linkedin' ) ); ?>" type="text" value="<?php echo esc_attr( $linkedin ); ?>">
				<input id="pos[]" name="pos[]" type="hidden" value="<?php echo $pos++; ?>">
			</p>
			<p>
				<label><?php _e( 'Instagram :', NSMW_TXTDM ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'instagram' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'instagram' ) ); ?>" type="text" value="<?php echo esc_attr( $instagram ); ?>">
				<input id="pos[]" name="pos[]" type="hidden" value="<?php echo $pos++; ?>">
			</p>
			<p>
				<label><?php _e( 'Pinterest :', NSMW_TXTDM ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pinterest' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pinterest' ) ); ?>" type="text" value="<?php echo esc_attr( $pinterest ); ?>">
				<input id="pos[]" name="pos[]" type="hidden" value="<?php echo $pos++; ?>">
			</p>
			<p>
				<label><?php _e( 'Flickr :', NSMW_TXTDM ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'flickr' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'flickr' ) ); ?>" type="text" value="<?php echo esc_attr( $flickr ); ?>">
				<input id="pos[]" name="pos[]" type="hidden" value="<?php echo $pos++; ?>">
			</p>
			<p>
				<label><?php _e( 'Tumblr :', NSMW_TXTDM ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tumblr' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tumblr' ) ); ?>" type="text" value="<?php echo esc_attr( $tumblr ); ?>">
				<input id="pos[]" name="pos[]" type="hidden" value="<?php echo $pos++; ?>">
			</p>
			<p>
				<label><?php _e( 'Vimeo :', NSMW_TXTDM ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'vimeo' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'vimeo' ) ); ?>" type="text" value="<?php echo esc_attr( $vimeo ); ?>">
				<input id="pos[]" name="pos[]" type="hidden" value="<?php echo $pos++; ?>">
			</p>	
			<p>
				<label><?php _e( 'Youtube :', NSMW_TXTDM ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'youtube' ) ); ?>" type="text" value="<?php echo esc_attr( $youtube ); ?>">
				<input id="pos[]" name="pos[]" type="hidden" value="<?php echo $pos++; ?>">
			</p>	
			<p>
				<label><?php _e( 'Rss Feed:', NSMW_TXTDM ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'rss' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rss' ) ); ?>" type="text" value="<?php echo esc_attr( $rss ); ?>">
				<input id="pos[]" name="pos[]" type="hidden" value="<?php echo $pos++; ?>">
			</p>
			<p>
				<label><?php _e( 'Whatsapp:', NSMW_TXTDM ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'whatsapp' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'whatsapp' ) ); ?>" type="text" value="<?php echo esc_attr( $whatsapp ); ?>">
				<input id="pos[]" name="pos[]" type="hidden" value="<?php echo $pos++; ?>">
			</p>
			<p>
				<label><?php _e( 'Email :', NSMW_TXTDM ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>" type="text" value="<?php echo esc_attr( $email ); ?>">
				<input id="pos[]" name="pos[]" type="hidden" value="<?php echo $pos++; ?>">
			</p>
		</div>
	
		<p id="wcs-<?php echo $nsmw_widget_id; ?>" onclick="return ClickToggle(this.id, 'display-settings-<?php echo $nsmw_widget_id; ?>');">
			<a class="button button-primary">
				<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;&nbsp;<?php _e('Widget Customization Settings', NSMW_TXTDM); ?>
			</a>
		</p>
		
		<!-- widget display setting -->
		<div id="display-settings-<?php echo $nsmw_widget_id; ?>" style="display: none;">	
			<hr>
			<p>
				<label><?php _e( 'How Many Icon Into Per Row:', NSMW_TXTDM ); ?></label> 
				<select id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>">
					<option value="col-md-6 col-sm-6 col-xs-6" <?php if($columns == "col-md-6 col-sm-6 col-xs-6") echo "selected=selected"; ?>>2 Icon</option>
					<option value="col-md-4 col-sm-4 col-xs-4" <?php if($columns == "col-md-4 col-sm-4 col-xs-4") echo "selected=selected"; ?>>3 Icon</option>
					<option value="col-md-3 col-sm-3 col-xs-3" <?php if($columns == "col-md-3 col-sm-3 col-xs-3") echo "selected=selected"; ?>>4 Icon</option>
					<option value="col-md-2 col-sm-2 col-xs-2" <?php if($columns == "col-md-2 col-sm-2 col-xs-2") echo "selected=selected"; ?>>6 Icon</option>
					<option value="col-md-1 col-sm-1 col-xs-1" <?php if($columns == "col-md-1 col-sm-1 col-xs-1") echo "selected=selected"; ?>>12 Icon</option>
				</select>
			</p>
			
			<p>
				<label><?php _e( 'Icon Size:', NSMW_TXTDM ); ?></label> 
				<select id="<?php echo esc_attr( $this->get_field_id( 'icon_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon_size' ) ); ?>">
					<option value="lg" <?php if($icon_size == "lg") echo "selected=selected"; ?>>1x</option>
					<option value="2" <?php if($icon_size == 2) echo "selected=selected"; ?>>2x</option>
					<option value="3" <?php if($icon_size == 3) echo "selected=selected"; ?>>3x</option>
					<option value="4" <?php if($icon_size == 4) echo "selected=selected"; ?>>4x</option>
					<option value="5" <?php if($icon_size == 5) echo "selected=selected"; ?>>5x</option>
				</select>
			</p>
			
			<p>
				<label><?php _e( 'Padding:', NSMW_TXTDM ); ?></label> 
				<select id="<?php echo esc_attr( $this->get_field_id( 'padding' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'padding' ) ); ?>">
					<option value="0" <?php if($padding == 0) echo "selected=selected"; ?>><?php _e( 'None', NSMW_TXTDM ); ?></option>
					<option value="1" <?php if($padding == 1) echo "selected=selected"; ?>>1px</option>
					<option value="2" <?php if($padding == 2) echo "selected=selected"; ?>>2px</option>
					<option value="3" <?php if($padding == 3) echo "selected=selected"; ?>>3px</option>
					<option value="4" <?php if($padding == 4) echo "selected=selected"; ?>>4px</option>
					<option value="5" <?php if($padding == 5) echo "selected=selected"; ?>>5px</option>
					<option value="6" <?php if($padding == 6) echo "selected=selected"; ?>>6px</option>
					<option value="7" <?php if($padding == 7) echo "selected=selected"; ?>>7px</option>
					<option value="8" <?php if($padding == 8) echo "selected=selected"; ?>>8px</option>
					<option value="9" <?php if($padding == 9) echo "selected=selected"; ?>>9px</option>
					<option value="10" <?php if($padding == 10) echo "selected=selected"; ?>>10px</option>
				</select>
			</p>
			
			<p>
				<label><?php _e( 'Margin Top:', NSMW_TXTDM ); ?></label> 
				<select id="<?php echo esc_attr( $this->get_field_id( 'margin_top' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'margin_top' ) ); ?>">
					<option value="0" <?php if($margin_top == 0) echo "selected=selected"; ?>><?php _e( 'None', NSMW_TXTDM ); ?></option>
					<option value="1" <?php if($margin_top == 1) echo "selected=selected"; ?>>1px</option>
					<option value="2" <?php if($margin_top == 2) echo "selected=selected"; ?>>2px</option>
					<option value="3" <?php if($margin_top == 3) echo "selected=selected"; ?>>3px</option>
					<option value="4" <?php if($margin_top == 4) echo "selected=selected"; ?>>4px</option>
					<option value="5" <?php if($margin_top == 5) echo "selected=selected"; ?>>5px</option>
					<option value="6" <?php if($margin_top == 6) echo "selected=selected"; ?>>6px</option>
					<option value="7" <?php if($margin_top == 7) echo "selected=selected"; ?>>7px</option>
					<option value="8" <?php if($margin_top == 8) echo "selected=selected"; ?>>8px</option>
					<option value="9" <?php if($margin_top == 9) echo "selected=selected"; ?>>9px</option>
					<option value="10" <?php if($margin_top == 10) echo "selected=selected"; ?>>10px</option>
					<option value="11" <?php if($margin_top == 11) echo "selected=selected"; ?>>11px</option>
					<option value="12" <?php if($margin_top == 12) echo "selected=selected"; ?>>12px</option>
					<option value="13" <?php if($margin_top == 13) echo "selected=selected"; ?>>13px</option>
					<option value="14" <?php if($margin_top == 14) echo "selected=selected"; ?>>14px</option>
					<option value="15" <?php if($margin_top == 15) echo "selected=selected"; ?>>15px</option>
					<option value="16" <?php if($margin_top == 16) echo "selected=selected"; ?>>16px</option>
					<option value="17" <?php if($margin_top == 17) echo "selected=selected"; ?>>17px</option>
					<option value="18" <?php if($margin_top == 18) echo "selected=selected"; ?>>18px</option>
					<option value="19" <?php if($margin_top == 19) echo "selected=selected"; ?>>19px</option>
					<option value="20" <?php if($margin_top == 20) echo "selected=selected"; ?>>20px</option>
				</select>
			</p>
			
			<p>
				<label><?php _e( 'Margin Bottom:', NSMW_TXTDM ); ?></label> 
				<select id="<?php echo esc_attr( $this->get_field_id( 'margin_bottom' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'margin_bottom' ) ); ?>">
					<option value="0" <?php if($margin_bottom == 0) echo "selected=selected"; ?>><?php _e( 'None', NSMW_TXTDM ); ?></option>
					<option value="1" <?php if($margin_bottom == 1) echo "selected=selected"; ?>>1px</option>
					<option value="2" <?php if($margin_bottom == 2) echo "selected=selected"; ?>>2px</option>
					<option value="3" <?php if($margin_bottom == 3) echo "selected=selected"; ?>>3px</option>
					<option value="4" <?php if($margin_bottom == 4) echo "selected=selected"; ?>>4px</option>
					<option value="5" <?php if($margin_bottom == 5) echo "selected=selected"; ?>>5px</option>
					<option value="6" <?php if($margin_bottom == 6) echo "selected=selected"; ?>>6px</option>
					<option value="7" <?php if($margin_bottom == 7) echo "selected=selected"; ?>>7px</option>
					<option value="8" <?php if($margin_bottom == 8) echo "selected=selected"; ?>>8px</option>
					<option value="9" <?php if($margin_bottom == 9) echo "selected=selected"; ?>>9px</option>
					<option value="10" <?php if($margin_bottom == 10) echo "selected=selected"; ?>>10px</option>
					<option value="11" <?php if($margin_bottom == 11) echo "selected=selected"; ?>>11px</option>
					<option value="12" <?php if($margin_bottom == 12) echo "selected=selected"; ?>>12px</option>
					<option value="13" <?php if($margin_bottom == 13) echo "selected=selected"; ?>>13px</option>
					<option value="14" <?php if($margin_bottom == 14) echo "selected=selected"; ?>>14px</option>
					<option value="15" <?php if($margin_bottom == 15) echo "selected=selected"; ?>>15px</option>
					<option value="16" <?php if($margin_bottom == 16) echo "selected=selected"; ?>>16px</option>
					<option value="17" <?php if($margin_bottom == 17) echo "selected=selected"; ?>>17px</option>
					<option value="18" <?php if($margin_bottom == 18) echo "selected=selected"; ?>>18px</option>
					<option value="19" <?php if($margin_bottom == 19) echo "selected=selected"; ?>>19px</option>
					<option value="20" <?php if($margin_bottom == 20) echo "selected=selected"; ?>>20px</option>
				</select>
			</p>		
			
			<p>
				<label><?php _e( 'Effect Type :', NSMW_TXTDM ); ?></label> <a href="https://awplife.com/wordpress-plugins/social-media-widget-premium/"> Upgrade To Pro For This Setting  </a>
 				<p> Transform Effect : <a href="https://awplife.com/wordpress-plugins/social-media-widget-premium/"> Upgrade To Pro For This Setting  </a> </p>
 				<p> Hover Effect : <a href="https://awplife.com/wordpress-plugins/social-media-widget-premium/"> Upgrade To Pro For This Setting  </a> </p>
 				<p> 30+ Social Media Icons : <a href="http://awplife.com/wordpress-plugins/social-media-widget-premium/"> Upgrade To Pro For More Icons  </a> </p>
			</p>	
			
			<p>
				<label><?php _e( 'Div Background Color:', NSMW_TXTDM ); ?></label><br>
				<select id="<?php echo esc_attr( $this->get_field_id( 'div_bg_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'div_bg_color' ) ); ?>">
					<option value="0" <?php if($div_bg_color == 0) echo "selected=selected"; ?>><?php _e( 'None', NSMW_TXTDM ); ?></option>
					<option value="red" <?php if($div_bg_color == "red") echo "selected=selected"; ?>><?php _e( 'Red', NSMW_TXTDM ); ?></option>
					<option value="blue" <?php if($div_bg_color == "blue") echo "selected=selected"; ?>><?php _e( 'Blue', NSMW_TXTDM ); ?></option>
					<option value="black" <?php if($div_bg_color == "black") echo "selected=selected"; ?>><?php _e( 'Black', NSMW_TXTDM ); ?></option>
					<option value="white" <?php if($div_bg_color == "white") echo "selected=selected"; ?>><?php _e( 'White', NSMW_TXTDM ); ?></option>
					<option value="green" <?php if($div_bg_color == "green") echo "selected=selected"; ?>><?php _e( 'Green', NSMW_TXTDM ); ?></option>
					<option value="gold" <?php if($div_bg_color == "gold") echo "selected=selected"; ?>><?php _e( 'Gold', NSMW_TXTDM ); ?></option>
					<option value="gray" <?php if($div_bg_color == "gray") echo "selected=selected"; ?>><?php _e( 'Gray', NSMW_TXTDM ); ?></option>
				</select>	
			</p>
			<p>
				<label><?php _e( 'Icon Color:', NSMW_TXTDM ); ?></label><br>
				<select id="<?php echo esc_attr( $this->get_field_id( 'icon_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon_color' ) ); ?>">
					<option value="0" <?php if($icon_color == 0) echo "selected=selected"; ?>><?php _e( 'None', NSMW_TXTDM ); ?></option>
					<option value="red" <?php if($icon_color == "red") echo "selected=selected"; ?>><?php _e( 'Red', NSMW_TXTDM ); ?></option>
					<option value="blue" <?php if($icon_color == "blue") echo "selected=selected"; ?>><?php _e( 'Blue', NSMW_TXTDM ); ?></option>
					<option value="black" <?php if($icon_color == "black") echo "selected=selected"; ?>><?php _e( 'Black', NSMW_TXTDM ); ?></option>
					<option value="white" <?php if($icon_color == "white") echo "selected=selected"; ?>><?php _e( 'White', NSMW_TXTDM ); ?></option>
					<option value="green" <?php if($icon_color == "green") echo "selected=selected"; ?>><?php _e( 'Green', NSMW_TXTDM ); ?></option>
					<option value="gold" <?php if($icon_color == "gold") echo "selected=selected"; ?>><?php _e( 'Gold', NSMW_TXTDM ); ?></option>
					<option value="gray" <?php if($icon_color == "gray") echo "selected=selected"; ?>><?php _e( 'Gray', NSMW_TXTDM ); ?></option>
				</select>	
			</p>
			<p>
				<label><?php _e( 'Custom CSS:', NSMW_TXTDM ); ?></label><br>
				<textarea class="css" id="<?php echo esc_attr( $this->get_field_id( 'css' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'css' ) ); ?>" style=" width: 100%;"  ><?php echo esc_attr( $css ); ?></textarea>
			</p>			
		</div>
		
		<p>
			<a href="https://awplife.com/account/signup/social-media-widget-premium" target="_blank" class="button button-primary">
				<i class="fa fa-shopping-cart" aria-hidden="true"></i>&nbsp;&nbsp;<?php _e('Buy Premium Version', NSMW_TXTDM ); ?> 
			</a>
		</p>
		<script>
			// toggle div settings open close
			function ClickToggle(id, divid) {
				//alert(id);
				jQuery( "div#" + divid ).slideToggle( "slow" );
				jQuery("#"+id+" i").toggleClass("fa-plus-square fa-minus-square");
			}
			
			jQuery( document ).ready(function() {
				jQuery( "div#social-media-urls" ).sortable({
					revert: true
				});
				jQuery( "#sortable" ).sortable({
					revert: true
				});
			});		
		</script>
		<?php 
	}

	/**
	 * Widget Save Settings
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		
		if(empty( $new_instance['nsmw_widget_id'])) {
			$new_instance['nsmw_widget_id'] = mt_rand(10,100000);
		}		
		$instance['nsmw_widget_id'] = ( ! empty( $new_instance['nsmw_widget_id'] ) ) ? strip_tags( $new_instance['nsmw_widget_id'] ) : 'Follow Us';
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : 'Follow Us';
		$instance['facebook'] = ( ! empty( $new_instance['facebook'] ) ) ? strip_tags( $new_instance['facebook'] ) : '';
		$instance['twitter'] = ( ! empty( $new_instance['twitter'] ) ) ? strip_tags( $new_instance['twitter'] ) : '';
		$instance['google_plus'] = ( ! empty( $new_instance['google_plus'] ) ) ? strip_tags( $new_instance['google_plus'] ) : '';
		$instance['linkedin'] = ( ! empty( $new_instance['linkedin'] ) ) ? strip_tags( $new_instance['linkedin'] ) : '';
		$instance['instagram'] = ( ! empty( $new_instance['instagram'] ) ) ? strip_tags( $new_instance['instagram'] ) : '';
		$instance['pinterest'] = ( ! empty( $new_instance['pinterest'] ) ) ? strip_tags( $new_instance['pinterest'] ) : '';
		$instance['flickr'] = ( ! empty( $new_instance['flickr'] ) ) ? strip_tags( $new_instance['flickr'] ) : '';
		$instance['tumblr'] = ( ! empty( $new_instance['tumblr'] ) ) ? strip_tags( $new_instance['tumblr'] ) : '';
		$instance['dribble'] = ( ! empty( $new_instance['dribble'] ) ) ? strip_tags( $new_instance['dribble'] ) : '';
		$instance['vine'] = ( ! empty( $new_instance['vine'] ) ) ? strip_tags( $new_instance['vine'] ) : '';
		$instance['yahoo'] = ( ! empty( $new_instance['yahoo'] ) ) ? strip_tags( $new_instance['yahoo'] ) : '';
		$instance['qq'] = ( ! empty( $new_instance['qq'] ) ) ? strip_tags( $new_instance['qq'] ) : '';
		$instance['reddit'] = ( ! empty( $new_instance['reddit'] ) ) ? strip_tags( $new_instance['reddit'] ) : '';
		$instance['vk'] = ( ! empty( $new_instance['vk'] ) ) ? strip_tags( $new_instance['vk'] ) : '';
		$instance['wordpress'] = ( ! empty( $new_instance['wordpress'] ) ) ? strip_tags( $new_instance['wordpress'] ) : '';
		$instance['overflow'] = ( ! empty( $new_instance['overflow'] ) ) ? strip_tags( $new_instance['overflow'] ) : '';
		$instance['stumbleupon'] = ( ! empty( $new_instance['stumbleupon'] ) ) ? strip_tags( $new_instance['stumbleupon'] ) : '';
		$instance['lastfm'] = ( ! empty( $new_instance['lastfm'] ) ) ? strip_tags( $new_instance['lastfm'] ) : '';
		$instance['xing'] = ( ! empty( $new_instance['xing'] ) ) ? strip_tags( $new_instance['xing'] ) : '';
		$instance['youtube'] = ( ! empty( $new_instance['youtube'] ) ) ? strip_tags( $new_instance['youtube'] ) : '';
		$instance['digg'] = ( ! empty( $new_instance['digg'] ) ) ? strip_tags( $new_instance['digg'] ) : '';
		$instance['git'] = ( ! empty( $new_instance['git'] ) ) ? strip_tags( $new_instance['git'] ) : '';
		$instance['share_alt'] = ( ! empty( $new_instance['share_alt'] ) ) ? strip_tags( $new_instance['share_alt'] ) : '';
		$instance['snapchat'] = ( ! empty( $new_instance['snapchat'] ) ) ? strip_tags( $new_instance['snapchat'] ) : '';
		$instance['vimeo'] = ( ! empty( $new_instance['vimeo'] ) ) ? strip_tags( $new_instance['vimeo'] ) : '';
		$instance['we_chat'] = ( ! empty( $new_instance['we_chat'] ) ) ? strip_tags( $new_instance['we_chat'] ) : '';
		$instance['wikipedia_w'] = ( ! empty( $new_instance['wikipedia_w'] ) ) ? strip_tags( $new_instance['wikipedia_w'] ) : '';
		$instance['yelp'] = ( ! empty( $new_instance['yelp'] ) ) ? strip_tags( $new_instance['yelp'] ) : '';
		$instance['skype'] = ( ! empty( $new_instance['skype'] ) ) ? strip_tags( $new_instance['skype'] ) : '';
		$instance['rss'] = ( ! empty( $new_instance['rss'] ) ) ? strip_tags( $new_instance['rss'] ) : '';
		$instance['email'] = ( ! empty( $new_instance['email'] ) ) ? strip_tags( $new_instance['email'] ) : '';
		$instance['whatsapp'] = ( ! empty( $new_instance['whatsapp'] ) ) ? strip_tags( $new_instance['whatsapp'] ) : '';
		
		//widget display setting
		$instance['columns'] = ( ! empty( $new_instance['columns'] ) ) ? strip_tags( $new_instance['columns'] ) : '';
		$instance['icon_size'] = ( ! empty( $new_instance['icon_size'] ) ) ? strip_tags( $new_instance['icon_size'] ) : '';
		$instance['padding'] = ( ! empty( $new_instance['padding'] ) ) ? strip_tags( $new_instance['padding'] ) : '';
		$instance['background'] = ( ! empty( $new_instance['background'] ) ) ? strip_tags( $new_instance['background'] ) : '';
		$instance['margin_top'] = ( ! empty( $new_instance['margin_top'] ) ) ? strip_tags( $new_instance['margin_top'] ) : '';
		$instance['margin_bottom'] = ( ! empty( $new_instance['margin_bottom'] ) ) ? strip_tags( $new_instance['margin_bottom'] ) : '';
		$instance['div_bg_color'] = ( ! empty( $new_instance['div_bg_color'] ) ) ? strip_tags( $new_instance['div_bg_color'] ) : '';
		$instance['icon_color'] = ( ! empty( $new_instance['icon_color'] ) ) ? strip_tags( $new_instance['icon_color'] ) : '';
		
		$instance['css'] = ( ! empty( $new_instance['css'] ) ) ? strip_tags( $new_instance['css'] ) : '';
		update_option("social_media_icon_pos", sanitize_text_field ($_POST['pos']));
		return $instance;
	}
}
} // end of class exist
add_action( 'widgets_init', function(){
	register_widget( 'New_Social_Media' );
});
?>