<div class='eat-tab-content eat-tab-template-management eat-tab-content-active' style=''>
	<div class="eat-tab-content-header">
		<div class='eat-tab-content-header-title'><?php _e('Template Management' , 'everest-admin-theme'); ?></div>
	</div>
	<div class='eat-tab-content-body'>
		<div class="eat-options-wrap">
			<label for='eat-general-template-selection'><?php _e('Template Selection', 'everest-admin-theme'); ?></label>
			<div class="eat-input-field-wrap">
				<select id='eat-general-template-selection' class='eat-selectbox-wrap eat-general-template-selection' name="everest_admin_theme[general-settings][template]">
					<option value='' data-img=''><?php _e('Default', 'everest-admin-theme'); ?></option>
					<?php
					$img_url = '';
					foreach ($eat_variables['templates'] as $template ) :
						if ( !empty( $template['group_name'] ) ):
							?>
							<optgroup label="<?php echo esc_attr( $template['group_name'] ); ?>"></optgroup>
							<?php
							foreach ( $template['group_data'] as $template_array ) :
								?>
								<option value="<?php echo $template_array['value']; ?>" <?php if(isset( $plugin_settings['general-settings']['template'] ) && $plugin_settings['general-settings']['template'] == $template_array['value'] ){ selected( $plugin_settings['general-settings']['template'], $template_array['value'] ); $img_url = $template_array['img']; } ?> data-img="<?php echo esc_url($template_array['img']); ?>"  ><?php echo esc_attr($template_array['name']); ?></option>
								<?php
							endforeach;
						endif;
					endforeach;
					?>
				</select>
			</div>
			<?php // if($img_url !=''){ ?>
				<div class="eat-img-selector-media eat-image-placeholder">
					<img src="<?php echo esc_url($img_url); ?>" />
				</div>
			<?php // } ?>
		</div>
	</div>
</div>