<?php

// Remove comments after integration. Comments are just for reference

//get site url
//Do not change this lines
$str = get_home_url();
$site_url = preg_replace('#^https?://#', '', $str);

return [
	/*
	 * Plugins short name appears on the License Menu Page
	 */
	'pluginShortName'   => 'Instructor Role',

	/*
	 * this slug is used to store the data in db. License is checked using two options viz edd_<slug>_license_key and edd_<slug>_license_status
	 */
	'pluginSlug'        => 'instructor_role',

	/*
	 * Download Id on EDD Server(1234 is dummy id please use your plugins ID)
	 */
	'itemId'            => 20277,

	/*
	 * Current Version of the plugin. This should be similar to Version tag mentioned in Plugin headers
	 */
	'pluginVersion'     => '3.5.4',

	/*
	 * Under this Name product should be created on WisdmLabs Site
	 */
	'pluginName'        => 'Instructor Role',

	/*
	 * Url where program pings to check if update is available and license validity
	 * plugins using storeUrl "https://wisdmlabs.com" or anything similar should change that to "https://wisdmlabs.com/license-check/" to avoid future issues.
	 */
	'storeUrl'          => 'https://wisdmlabs.com/license-check/',

	/**
	 * Site url which will pass in API request.
	 */
	'siteUrl'           => $site_url,

	/*
	 * Author Name
	 */
	'authorName'        => 'WisdmLabs',

	/*
	 * Text Domain used for translation
	 */
	'pluginTextDomain'  => 'wdm_instructor_role',

	/*
	 * Base Url for accessing Files
	 * Change if not accessing this file from main file
	 */
	'baseFolderUrl'     => plugins_url( '/', __FILE__ ),

	/*
	 * Base Directory path for accessing Files
	 * Change if not accessing this file from main file
	 */
	'baseFolderDir'     => untrailingslashit( plugin_dir_path( __FILE__ ) ),

	/*
	 * Plugin Main file name
	 * example : product-enquiry-pro.php
	 */
	'mainFileName'      => 'instructor.php',

	/**
	 * Set true if theme
	 */
	'isTheme'           => false,

	/**
	*  Changelog page link for theme
	*  should be false for plugin
	*  eg : https://wisdmlabs.com/elumine/documentation/
	*/
	'themeChangelogUrl' => false,

	/*
	 * Dependent plugins for your plugin
	 * pass the value in array where plugin name will be key and version number will be value
	 * Do not hard code version. Version should be the current version of dependency fetched dynamically.
	 * In given example WC_VERSION is constant defined by woocommerce for version. Check how you can get version dynamically of other dependent plugins
	 * Supported plugin names
	 * woocommerce
	 * learndash
	 * wpml
	 * unyson
	 */
	'dependencies'      => array(
		'learndash' => defined( 'LEARNDASH_VERSION' ) ? LEARNDASH_VERSION : '',
	),

	/**
	 * Sample code if your dependent plugins are not compulsory
	* Please create the following function to fetch dependencies for a theme/plugin.
	* if (!function_exists('wdm_get_active_dependencies')) {
		   function wdm_get_active_dependencies()
		   {
			   $dependencies = array();
			   include_once(ABSPATH . 'wp-admin/includes/plugin.php');
			   if (is_plugin_active('woocommerce/woocommerce.php')) {
				   $dependencies[] = 'woocommerce';
			   }
			   if (is_plugin_active('buddypress/bp-loader.php')) {
				   $dependencies[] = 'buddypress';
			   }
			   if (is_plugin_active('badgeos/badgeos.php')) {
				   $dependencies[] = 'badgeos';
			   }
			   if (is_plugin_active('bbpress/bbpress.php')) {
				   $dependencies[] = 'bbpress';
			   }
			   if (is_plugin_active('sfwd-lms/sfwd_lms.php')) {
				   $dependencies[] = 'learndash';
			   }
			   if (is_plugin_active('unyson/unyson.php')) {
				   $dependencies[] = 'unyson';
			   }
			   return $dependencies;
		   }
	   }
	*/
];
