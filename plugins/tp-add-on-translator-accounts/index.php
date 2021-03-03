<?php
/*
Plugin Name: TranslatePress - Translator Accounts Add-on
Plugin URI: https://translatepress.com/
Description: Extends the functionality of TranslatePress by creating Translator accounts.
Version: 1.0.5
Author: Cozmoslabs, Cristian Antohe
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

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_translator_accounts() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-translator-accounts-activator.php';
    TRP_Translator_Accounts_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_translator_accounts' );


/**
 * The code that instantiates our plugin
 * This action is documented in includes/class-plugin-name-activator.php
 */
function run_translator_accounts(){
    /** Initialize update here in the main plugin file. It is a must **/
    // this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
    define( 'TRP_TA_STORE_URL', 'https://translatepress.com' );
    // the name of your product. This should match the download name in EDD exactly
    define( 'TRP_TA_ITEM_NAME', 'Translator Accounts' );
    if( !class_exists( 'TRP_EDD_SL_Plugin_Updater' ) ) {
        // load our custom updater
        include( dirname( __FILE__ ) . '/includes/class-edd-sl-plugin-updater.php' );
    }
    // retrieve our license key from the DB
    $license_key = trim( get_option( 'trp_license_key' ) );
    // setup the updater
    $edd_updater = new TRP_EDD_SL_Plugin_Updater( TRP_TA_STORE_URL, __FILE__, array(
            'version' 	=> '1.0.5', 		    // current version number
            'license' 	=> $license_key, 	    // license key (used get_option above to retrieve from DB)
            'item_name' => TRP_TA_ITEM_NAME,    // name of this plugin
            'item_id'   => '685',
            'author' 	=> 'Cozmoslabs',        // author of this plugin
            'beta' 		=> false
        )
    );


    require_once plugin_dir_path( __FILE__ ) . 'includes/class-translator-accounts.php';
    if ( class_exists( 'TRP_Translate_Press' ) ) {
        new TRP_Translator_Accounts();
    }
}
add_action( 'plugins_loaded', 'run_translator_accounts', 0 );

/**
 * Allow users with the translate_strings to translate the website, besides those with manage_options.
 *
 * Needs to be outside everything because of early call of get_lang_from_url_string when determining language.
 * Determining language happens when instantiating TRP_Translate_Press, this is why this function
 * needs to be before plugins_loaded.
 *
 * @since    1.0.5
 */
add_filter( 'trp_translating_capability', 'trp_ta_translator_account_permissions' );
function trp_ta_translator_account_permissions(){
    // Return manage_options for admins
    if (current_user_can('manage_options'))
        return 'manage_options';

    return 'translate_strings';
}