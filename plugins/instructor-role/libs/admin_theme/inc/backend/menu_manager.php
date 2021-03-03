<?php
/**
 * Class to perform the menu edits.
 */
if(! class_exists( 'everestAdminThemeMenuManager' )){
	class everestAdminThemeMenuManager {
		function __construct() {
			$plugin_settings = get_option('eat_admin_theme_settings');
			if(isset($plugin_settings['admin_menu']['enable_menu_manager'])){
				add_filter( 'custom_menu_order', array($this, 'eat_custom_menu_order'), 999999999); // https://wordpress.stackexchange.com/questions/239755/reorder-custom-submenu-item
			}
			add_action( 'wp_ajax_eat_save_menu_submenu', array($this, 'eat_save_menu_submenu'));
			add_action( 'wp_ajax_eat_reset_menu_submenu', array($this, 'eat_reset_menu_submenu'));
		}

		function eat_custom_menu_order(){
			self:: eat_admin_menu_rearrange();
			self:: eat_admin_submenu_rearrange();
		}

		function eat_admin_menu_rearrange() {
			global $menu;
			ksort($menu);
			$renamemenu    = self:: eat_rename_menu();
			$menu          = $renamemenu;
			$neworder      = self:: eat_admin_menu_submenu_neworder();
			$returned_menu = self:: eat_adminmenu_newmenu( $neworder, $menu );
			$menu          = $returned_menu;
			$GLOBALS['eat_main_menu'] = $menu;

			$menu = self:: eat_adminmenu_disable($menu);
			return $menu;
		}

		function eat_admin_submenu_rearrange(){
			global $eat_main_menu;
			global $submenu;
			$renamesubmenu           = self:: eat_rename_submenu();
			$submenu                 = $renamesubmenu;
			$newsuborder             = self:: eat_admin_menu_submenu_neworder();
			$ret                     = self:: eat_adminmenu_newsubmenu($newsuborder,$submenu,$eat_main_menu);
			$submenu                 = $ret;
			$GLOBALS['eat_sub_menu'] = $submenu;
			$submenu                 = self:: eat_adminsubmenu_disable($submenu);
			foreach ($submenu as $submenu_key => $submenu_value) {
				if(is_array($submenu_value)){
					foreach ($submenu_value as $key => $value) {
						if(!isset($value['2'])){
							unset($submenu[$submenu_key][$key]);
						}
					}
				}
			}
			return $submenu;
		}

		function eat_rename_menu(){
			global $menu;
			$menurename = get_option("eat_renamed_menu");
			if($menurename != ""){
				$renamed_menu_items = explode("|separator3|", $menurename);

				foreach($renamed_menu_items as $renamed_item){
					if($renamed_item != ""){
						$arr = explode( "|separator1|", $renamed_item );
						if(isset($arr[0])){ $ids                      = $arr[0]; }
						if(isset($arr[1])){ $modified_menuname_n_icon = $arr[1]; }

						if($ids != ""){
							$exploded_ids = explode(":", $ids);
							if(isset($exploded_ids[0])) { $original_id = $exploded_ids[0]; }
							if(isset($exploded_ids[1])) { $new_id      = $exploded_ids[1]; }
						}

						if(isset($modified_menuname_n_icon) && $modified_menuname_n_icon !=''){
							$exploded_array = explode("|separator2|", $modified_menuname_n_icon);
							if(isset($exploded_array[0])){ $modified_menu_name = $exploded_array[0]; }
							if(isset($exploded_array[1])){ $modified_icon      = $exploded_array[1]; }
						}

						if(isset($menu[$original_id][0]) && isset($menu[$original_id][5]) && $menu[$original_id][5] == $new_id){
							$menu[$original_id]['original_name'] = $menu[$original_id][0];
							$menu[$original_id][0] = $modified_menu_name;
							$menu[$original_id]['original_icon'] = $menu[$original_id][6];
							$menu[$original_id][6] = $modified_icon;
						}
					}
				}
			}
			return $menu;
		}

		function eat_rename_submenu(){
			global $submenu;
			$submenurename = get_option("eat_renamed_submenu");
			if($submenurename != ""){
				$exp = explode("|separator3|", $submenurename);
				foreach($exp as $str){
					$idstr = $page = $parentid = $id = $val = $original = "";

					$arr = explode("|separator1|", $str);

					if(isset($arr[0])){ $idstr = $arr[0]; }
					if(isset($arr[1])){ $val   = $arr[1]; }
					$idexp = explode("|separator4|", $idstr);
					if(isset($idexp[0])){ $parent_page = $idexp[0]; }
					if(isset($idexp[1])){ $id   = $idexp[1]; }

					if(isset($submenu[$parent_page][$id][0])){
						$submenu[$parent_page][$id]['originalsubname'] = $submenu[$parent_page][$id][0];
						$submenu[$parent_page][$id][0]                 = $val;
					}
				}
			}
			return $submenu;
		}

		function eat_admin_menu_submenu_neworder() {
			$new    = array();
			$subnew = array();
			$ret    = array();

			$neworder    = get_option("eat_custom_menu_order");
			$newsuborder = get_option("eat_custom_submenu_order");

			$exp    = explode("|",$neworder);
			$subexp = explode("|",$newsuborder);

		    foreach ($exp as $id) {
		    	if($id != "") {
		    		$new[] = $id;
		    	}
		    }

		    foreach ($subexp as $id) {
		    	if($id != "") {
		    		$subid = explode(":",$id);
		    		$subnew[$subid[0]][] = $subid[1];
		    	}
		    }

			$ret['menu']    = $new;
			$ret['submenu'] = $subnew;

		    return $ret;
		}

		function eat_adminmenu_newmenu($neworder,$menu){
			$relation = array();

			foreach($menu as $id=>$valarr){
				if(isset($valarr[5])){
					$relation[$valarr[5]] = $id;
				}else{
					$relation[$valarr[2]] = $id;
				}
			}

			$ret = array();
			$allids = $menu;

			$k = 100000;
			foreach($neworder['menu'] as $newmenuid) {
				// echo "reached here";
				if(isset($relation[$newmenuid])){
					$k++;
					$ret[$k] = $menu[$relation[$newmenuid]];
					$ret[$k]['original'] = $relation[$newmenuid];
					unset($allids[$relation[$newmenuid]]);
				}
			}

			foreach($allids as $itemid => $item) {
				$k++;
				$ret[$k] = $item;
				$ret[$k]['original'] = $itemid;
			}

			return $ret;
		}

		function eat_adminmenu_disable($menu){
		    $menudisable = get_option("eat_disabled_menu");
		    $exp = explode("|", $menudisable);
		    foreach($menu as $menuKey => $menuItem){
		    	if(isset($menuItem[5]) && in_array( $menuItem[5], $exp )){
					unset($menu[$menuKey]);
		    	}else if( in_array( $menuItem[2], $exp )){
		    		unset($menu[$menuKey]);
		    	}
		    }
			return $menu;
		}

		function eat_adminmenu_newsubmenu($newsuborder,$submenu,$menu){
			$allids = $menu;
			$allsubids = $submenu;

			$ret = array();
			foreach($newsuborder['submenu'] as $submenuid => $arr) {
				$k = 0;
				foreach($arr as $linkid) {
					$submenu[$submenuid][$linkid]['original'] = $linkid;
					$ret[$submenuid][$k] = $submenu[$submenuid][$linkid];
					unset($allsubids[$submenuid][$linkid]);
					$k++;
				}
			}

			foreach($allsubids as $itemid => $item) {
				$k = 0;
				foreach($item as $a => $b) {
					$allsubids[$itemid][$a]['original'] = $a;
					$ret[$itemid][$k] = $allsubids[$itemid][$a];
					$k++;
				}
			}
			return $ret;
		}

		// Remove item from a submenu
		function eat_removeSubmenu( $parentMenuItem, $item ) {
			global $submenu, $menu;
			foreach ( $submenu as $menuKey => $menuItem ) {
				if ( $menuKey == $parentMenuItem ) {
					foreach ( $menuItem as $submenuKey => $submenuItems ) {
						if ( $submenuItems[2] == $item ) {
							unset( $submenu[$menuKey][$submenuKey] );
							break;
						}
					}
				}
			}
		}

		// Remove item from the main menu
		function eat_removeMenu( $item ) {
			global $menu;
			foreach ( $menu as $menuKey => $menuItem ) {
				if ( $menuItem[2] == $item ) {
					unset( $menu[$menuKey] );
					break;
				}
			}
		}

		function eat_adminsubmenu_disable($submenu){
			global $menu;
			$enabledmenu = array();
			foreach ($menu as $key => $value) {
				$enabledmenu[] = $value[2];
			}

			$menumap = array();
		    $submenudisable = get_option("eat_disabled_submenu");

		    $exp = explode("|", $submenudisable);

		    foreach ($submenu as $subMenuKey => $subMenuItem) {

		    	if(!in_array($subMenuKey,$enabledmenu)){
		    		unset($submenu[$subMenuKey]);
		    	} else {

			    $parentid = "";
		    	foreach($subMenuItem as $k => $v){
		    		$subid = "";
		    		if(isset($v['original'])){
		    			$subid = $v['original'];
		    		}
		    		if(in_array($subMenuKey.":".$subid,$exp)){
		    			unset($submenu[$subMenuKey][$k]);
		    		}
		    	}
		      }
		    }
			return $submenu;
		}

		public static function eat_menu_manager_settings_page(){
			include('common/header.php');
			global $menu;
			global $submenu;
			global $eat_main_menu;
			global $eat_sub_menu;

			if(empty($eat_main_menu)){ $eat_main_menu = $menu; }
			if(empty($eat_sub_menu)){ $eat_sub_menu   = $submenu; }
			?>
			<div class='eat-menu-manager-wrap'>
				<div class='eat-menu-wrap'><?php
					foreach($eat_main_menu as $pid=>$menu_attrs){
						//As of WP 3.5, the "Links" menu is hidden by default.
						if($menu_attrs['1'] == 'manage_links'){ continue; }

						$tabid = $pid;
						if(isset($menu_attrs['original'])){ $tabid = $menu_attrs['original'];}

						$sid = $tabid;
						if(isset($menu_attrs[5])){ $sid = $menu_attrs[5]; }

						$menupage = $tabid;
						if(isset($menu_attrs[2])){ $menupage = $menu_attrs[2]; }

						$original_name = $menu_attrs[0];
						if(isset($menu_attrs['original_name'])){
							$original_name = $menu_attrs['original_name'];
						}

						if(isset($menu_attrs[6])){
							$original_icon = $menu_attrs[6];
							if(isset($menu_attrs['original_icon'])){
								$original_icon = $menu_attrs['original_icon'];
							}
						}

						if(isset($menu_attrs['6'])){
							$disabled_menu_class = '';
							$disabled_menus = get_option('eat_disabled_menu');
							$disabled_menus = explode('|', $disabled_menus);
							foreach($disabled_menus as $disabled_menu){
								if($disabled_menu == $menu_attrs[5]){
									$disabled_menu_class = 'eat-disabled';
								}
							}

							?>
							<div class="eat-menu-item eat-menu-manager-item-wrap <?php echo $disabled_menu_class; ?> clearfix" data-id="<?php echo $tabid; ?>" data-menu-name="<?php echo $menu_attrs[5]; ?>">
								<div class='eat-menu-header-options clearfix eat-header-options'>
									<div class='eat-menu-header-left'>
										<span class="eat-icon-name"><i class='dashicons-before <?php echo esc_attr($menu_attrs['6']); ?>'></i></span>
										<span class='eat-menu-name'><?php echo $menu_attrs['0']; ?></span>
									</div>
									<div class='eat-menu-edits-actions'>
										<span class='eat-menu-hide-show eat-menu-submenu-hide-show'>Hide/Show</span>
										<span class="eat-menu-submenu-move">Move/sort</span>
									</div>
								</div>
								<div class='eat-menu-edits eat-menu-manager-item-edits' style='display:none;'>
									<div class='eat-menu-rename'>
										<label><?php _e('Rename:', 'everest-admin-theme'); ?></label>
										<input type='text' name='menu_rename' data-id='<?php echo $tabid; ?>' class='eat_menu_rename' data-menu-id='<?php echo $menu_attrs['5']; ?>' value="<?php echo stripslashes_deep( htmlspecialchars($menu_attrs['0'])); ?>" />
										<button name="Reset" class="eat-menu-submenu-reset-button" data-original-value="<?php echo stripslashes_deep(htmlspecialchars($original_name)); ?>"><?php _e('Reset', 'everest-admin-theme'); ?></button>
									</div>
									<div class='eat-menu-icon'>
										<label><?php _e('Icon:', 'everest-admin-theme'); ?></label>
										<input class="eat-icon-picker" type="hidden" id="wpfm-icon-picker-icon_<?php echo $tabid; ?>" name='menu_icon_picker' value='<?php echo $menu_attrs['6']; ?>' />
										<div data-target="#wpfm-icon-picker-icon_<?php echo $tabid; ?>" class="eat-button icon-picker dashicons <?php if (isset($menu_attrs['6']) && $menu_attrs['6'] !='') { echo $menu_attrs['6']; } ?> "><?php _e( 'Select Icon', 'everest-admin-theme' ); ?></div>
										<button name="Reset" class="eat-menu-icon-reset-button" data-original-value="<?php echo stripslashes_deep(htmlspecialchars($original_icon)); ?>"><?php _e('Reset', 'everest-admin-theme'); ?></button>
									</div>
								</div>
								<?php
								if(isset($eat_sub_menu[$menu_attrs[2]])){ ?>
									<div class='eat-submenu-wrap' style='display:none;'>
										<?php
										foreach($eat_sub_menu[$menu_attrs[2]] as $cid => $submenu_attrs){
											$originalsubname = $submenu_attrs[0];
											if(isset($submenu_attrs['originalsubname'])){
												$originalsubname = $submenu_attrs['originalsubname'];
											}

											$disabled_submenu = get_option('eat_disabled_submenu');
											$disabled_submenu = explode('|', $disabled_submenu);
											$disabled_class   = '';
											foreach($disabled_submenu as $disabled_submenu){
												$disabled_item = explode(':', $disabled_submenu);
												if($menu_attrs[2] == $disabled_item[0]){
													if($disabled_item['1'] == $submenu_attrs['original']){
														$disabled_class = "eat-disabled";
													}
												}
											}
											?>
											<div class='eat-submenu-item eat-submenu-manager-item-wrap <?php echo $disabled_class; ?>' data-id="<?php echo $submenu_attrs['original']; ?>" data-parent-id="<?php echo $tabid; ?>" data-parent-name='<?php echo $menu_attrs[2];?>'>
												<div class='eat-submenu-header-options eat-header-options clearfix'>
													<span class='eat-submenu-name'><?php echo $submenu_attrs['0']; ?></span>
													<div class='eat-submenu-edits-actions'>
														<span class='eat-submenu-hide-show eat-menu-submenu-hide-show'>Hide/Show</span>
														<span class="eat-menu-submenu-move">Move/Sort</span>
													</div>
												</div>
												<div class='eat-submenu-edits eat-submenu-manager-item-edits' style="display:none;">
													<div class='eat-submenu-rename'>
														<label><?php _e('Rename:', 'everest-admin-theme'); ?></label>
														<input type='text' name='menu_rename' class='eat_submenu_rename' data-id="<?php echo $submenu_attrs['original']; ?>" data-parent-id='<?php echo $tabid; ?>' data-parent-page='<?php echo $menu_attrs[2]; ?>' value="<?php echo stripslashes_deep(htmlspecialchars($submenu_attrs['0'])); ?>" />
														<button name="Reset" class="eat-menu-submenu-reset-button" data-original-value="<?php echo stripslashes_deep(htmlspecialchars($originalsubname)); ?>"><?php _e('Reset', 'everest-admin-theme'); ?></button>
													</div>
												</div>
											</div>
										<?php
										}
										?>
									</div>
									<?php
								} ?>
							</div>
						<?php
						}
					} ?>
				</div>
				<div class="eat-submit-button">
					<button name='Update' class='eat-ajax-menu-submenu-submit-button'><?php _e('Update', 'everest-admin-theme' ); ?></button>
					<button name='Reset' class="eat-ajax-menu-submenu-reset-button"><?php _e('Reset', 'everest-admin-theme'); ?></button>
					<div class='eat-ajax-message' style="display:none;"><img src='<?php echo E_ADMIN_THEME_IMAGE_DIR.'/ajax_loader.gif' ?>' alt='Saving...' /></div>
				</div>
			</div>
			<?php
			include('common/footer.php');
		}

		public static function eat_save_menu_submenu(){
			if(!empty( $_POST['action'] ) && $_POST['action'] == 'eat_save_menu_submenu' && wp_verify_nonce( $_POST['_wpnonce'], 'eat-ajax-nonce' )){
				$neworder         = isset( $_POST['neworder']) 			? $_POST['neworder'] : array();
				$disabled_menu    = isset( $_POST['menudisable']) 		? $_POST['menudisable'] : array();
				$newsuborder      = isset( $_POST['newsuborder']) 		? $_POST['newsuborder'] : array();
				$disabled_submenu = isset( $_POST['submenudisable']) 	? $_POST['submenudisable'] : array();
				$menu_rename      = isset( $_POST['menurename'] ) 		? stripslashes_deep($_POST['menurename']) : array();
				$submenu_rename   = isset( $_POST['submenurename'] ) 	? stripslashes_deep($_POST['submenurename']) : array();
				update_option( 'eat_custom_menu_order', $neworder);
				update_option( 'eat_custom_submenu_order', $newsuborder);
				update_option( 'eat_renamed_menu', $menu_rename);
				update_option( 'eat_renamed_submenu', $submenu_rename);
				update_option( 'eat_disabled_menu', $disabled_menu);
				update_option( 'eat_disabled_submenu', $disabled_submenu);
			}
		}

		function eat_reset_menu_submenu(){
			if(!empty( $_POST['action'] ) && $_POST['action'] == 'eat_reset_menu_submenu' && wp_verify_nonce( $_POST['_wpnonce'], 'eat-ajax-nonce' )){
				update_option( 'eat_custom_menu_order', '');
				update_option( 'eat_custom_submenu_order', '');
				update_option( 'eat_renamed_menu', '');
				update_option( 'eat_renamed_submenu', '');
				update_option( 'eat_disabled_menu', '');
				update_option( 'eat_disabled_submenu', '');
			}
		}
	}

	$new_everest_admin_menu_manager = new everestAdminThemeMenuManager();
}