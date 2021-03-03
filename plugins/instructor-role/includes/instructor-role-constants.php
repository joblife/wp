<?php
// namespace InstructorRole\Includes;.

// Review course constant.
if ( ! defined( 'WDMIR_REVIEW_COURSE' ) ) {
	$wdmir_admin_settings = get_option( '_wdmir_admin_settings', array() );
	// If Review Course setting is enabled.
	if ( isset( $wdmir_admin_settings['review_course'] ) && '1' == $wdmir_admin_settings['review_course'] ) {
		define( 'WDMIR_REVIEW_COURSE', true );
	} else {
		define( 'WDMIR_REVIEW_COURSE', false );
	}
}

// Review product constant.
if ( ! defined( 'WDMIR_REVIEW_PRODUCT' ) ) {
	$wdmir_admin_settings = get_option( '_wdmir_admin_settings', array() );
	// If Review Product setting is enabled.
	if ( isset( $wdmir_admin_settings['review_product'] ) && '1' == $wdmir_admin_settings['review_product'] ) {
		define( 'WDMIR_REVIEW_PRODUCT', true );
	} else {
		define( 'WDMIR_REVIEW_PRODUCT', false );
	}
}

// Review download constant v3.0.0.
if ( ! defined( 'WDMIR_REVIEW_DOWNLOAD' ) ) {
	$wdmir_admin_settings = get_option( '_wdmir_admin_settings', array() );
	// If Review Product setting is enabled.
	if ( isset( $wdmir_admin_settings['review_download'] ) && '1' == $wdmir_admin_settings['review_download'] ) {
		define( 'WDMIR_REVIEW_DOWNLOAD', true );
	} else {
		define( 'WDMIR_REVIEW_DOWNLOAD', false );
	}
}

// Define core modules meta key constant.
if ( ! defined( 'IR_CORE_MODULES_META_KEY' ) ) {
	define( 'IR_CORE_MODULES_META_KEY', 'ir_active_modules' );
}

global $wdm_ar_post_types;

// array of all custom post types of LD posts.
$wdm_ar_post_types = array(
	'sfwd-assignment',
	'sfwd-certificates',
	'sfwd-courses',
	'sfwd-lessons',
	'sfwd-quiz',
	'sfwd-topic',
	'sfwd-essays', // added in v2.4.0.
	'sfwd-question', // added in v2.6.0.
	'achievement-type',
	'elementor_library',
	'students_voice',   // added in v3.5.0.
	'groups',
);


// Define review update default message.
if ( ! defined( 'IR_REVIEW_UPDATE_NOTICE' ) ) {
	$message = <<<NOTICE
<div class="notice notice-{type} is-dismissible">
    <p>{message}</p>
</div>
NOTICE;
	define( 'IR_REVIEW_UPDATE_NOTICE', $message );
}
