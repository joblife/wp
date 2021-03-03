<?php
/*
Plugin Name: TranslatePress - Navigation Based on Language Add-on
Plugin URI: https://translatepress.com/
Description: Extends the functionality of TranslatePress by allowing different navigation menu items based on language
Version: 1.0.3
Author: Cozmoslabs, Madalin Ungureanu
Author URI: https://translatepress.com/
License: GPL2

== Copyright ==
Copyright 2017 Cozmoslabs (www.cozmoslabs.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/



function trp_nbl_run(){
	
	/** Initialize update here in the main plugin file. It is a must **/
	// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
	define( 'TRP_NBL_STORE_URL', 'https://translatepress.com' );
	// the name of your product. This should match the download name in EDD exactly
	define( 'TRP_NBL_ITEM_NAME', 'Navigation Based on Language' );

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'trp_license_key' ) );
	// setup the updater
	$edd_updater = new TRP_EDD_SL_Plugin_Updater( TRP_NBL_STORE_URL, __FILE__, array(
						'version' 	=> '1.0.3', 		// current version number
						'license' 	=> $license_key, 	// license key (used get_option above to retrieve from DB)
						'item_name' => TRP_NBL_ITEM_NAME, 	// name of this plugin
						'item_id'   => '840',
						'author' 	=> 'Cozmoslabs',  // author of this plugin
						'beta' 		=> false
						)
			);
	/** End the update initialization here **/
	
	
    require_once plugin_dir_path( __FILE__ ) . 'class-navigation-based-on-language.php';
    if ( class_exists( 'TRP_Translate_Press' ) ) {
        new TRP_Navigation_Based_on_Language();
    }
	
}
add_action( 'plugins_loaded', 'trp_nbl_run', 0 );