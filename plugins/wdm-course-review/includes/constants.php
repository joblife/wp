<?php
/**
 * This file contains all the constants defined by the plugin.
 *
 * @package RatingsReviewsFeedback
 */

if ( ! defined( 'WDM_LD_COURSE_REVIEW_DEBUG' ) ) {
	define( 'WDM_LD_COURSE_REVIEW_DEBUG', false );
}
if ( ! defined( 'WDM_LD_COURSE_VERSION' ) ) {
	define( 'WDM_LD_COURSE_VERSION', '2.0.1' );
}
if ( ! defined( 'RRF_REVIEW_HEADLINE_MAX_LENGTH' ) ) {
	define( 'RRF_REVIEW_HEADLINE_MAX_LENGTH', 128 );
}
if ( ! defined( 'RRF_REVIEW_DETAILS_MAX_LENGTH' ) ) {
	define( 'RRF_REVIEW_DETAILS_MAX_LENGTH', 400 );
}
if ( ! defined( 'WDM_LD_DEFAULT_FEEDBACK_SUBJECT' ) ) {
	define( 'WDM_LD_DEFAULT_FEEDBACK_SUBJECT', "Feedback on course '[course_title]'" );
}
if ( ! defined( 'WDM_LD_DEFAULT_FEEDBACK_BODY' ) ) {
	define(
		'WDM_LD_DEFAULT_FEEDBACK_BODY',
		'<div style="background-color: #efefef; width: 100%; -webkit-text-size-adjust: none !important; margin: 0; padding: 70px 70px 70px 70px;">
	<table id="template_container" style="padding-bottom: 20px; background-color: #dfdfdf; height: 352px; box-shadow: rgba(0, 0, 0, 0.024) 0px 0px 0px 3px !important; border-radius: 6px !important;" border="0" width="660" cellspacing="0" cellpadding="0">
	<tbody>
	<tr>
	<td style="background-color: #465c94; border-top-left-radius: 6px !important; border-top-right-radius: 6px !important; border-bottom: 0; font-family: Arial; font-weight: bold; line-height: 100%; vertical-align: middle;">
	<h1 style="color: white; margin: 0; padding: 28px 24px; text-shadow: 0 1px 0 0; display: block; font-family: Arial; font-size: 30px; font-weight: bold; text-align: left; line-height: 150%;">Feedback on course [course_title]</h1>
	</td>
	</tr>
	<tr>
	<td style="padding: 20px; background-color: #dfdfdf; border-radius: 6px !important;" align="center" valign="top">
	<div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
	<div>Hi <b>[author_display_name]</b>,</div>
	<div>You have got a<strong> Feedback</strong> on course <strong>[course_title]</strong>.</div>
	<div>Student details :
	First name: <strong>[user_first_name]</strong>
	Last name: <strong>[user_last_name]</strong>
	Email ID: <strong>[user_email_id]</strong></div>
	</div></td>
	</tr>
	</tbody>
	</table>
	</div>
		'
	);
}
if ( ! defined( 'WDM_LD_DEFAULT_REVIEW_SUBJECT' ) ) {
	define( 'WDM_LD_DEFAULT_REVIEW_SUBJECT', "Review on course '[course_title]'" );
}
if ( ! defined( 'WDM_LD_DEFAULT_REVIEW_BODY' ) ) {
	define(
		'WDM_LD_DEFAULT_REVIEW_BODY',
		'<div style="background-color: #efefef; width: 100%; -webkit-text-size-adjust: none !important; margin: 0; padding: 70px 70px 70px 70px;">
	<table id="template_container" style="padding-bottom: 20px; background-color: #dfdfdf; height: 352px; box-shadow: rgba(0, 0, 0, 0.024) 0px 0px 0px 3px !important; border-radius: 6px !important;" border="0" width="660" cellspacing="0" cellpadding="0">
	<tbody>
	<tr>
	<td style="background-color: #465c94; border-top-left-radius: 6px !important; border-top-right-radius: 6px !important; border-bottom: 0; font-family: Arial; font-weight: bold; line-height: 100%; vertical-align: middle;">
	<h1 style="color: white; margin: 0; padding: 28px 24px; text-shadow: 0 1px 0 0; display: block; font-family: Arial; font-size: 30px; font-weight: bold; text-align: left; line-height: 150%;">Review on course [course_title]</h1>
	</td>
	</tr>
	<tr>
	<td style="padding: 20px; background-color: #dfdfdf; border-radius: 6px !important;" align="center" valign="top">
	<div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
	<div>Hi <b>[author_display_name]</b>,</div>
	<div>You have got a<strong> Review</strong> on course <strong>[course_title]</strong>.</div>
	<div>Student details :
	First name: <strong>[user_first_name]</strong>
	Last name: <strong>[user_last_name]</strong>
	Email ID: <strong>[user_email_id]</strong></div>
	</div></td>
	</tr>
	</tbody>
	</table>
	</div>
		'
	);
}
if ( ! defined( 'WDM_LD_DEFAULT_REVIEW_REJECTION_SUBJECT' ) ) {
	define( 'WDM_LD_DEFAULT_REVIEW_REJECTION_SUBJECT', 'Your course review has been rejected' );
}
define( 'WDM_LD_COURSE_ACTIVATION_MSG', "<div class='notice notice-info is-dismissible'><p><b>LearnDash LMS</b> " . __( 'plugin is not active. In order to make', 'wdm_ld_course_review' ) . ' <b>' . __( "'LearnDash Ratings, Reviews, and Feedback'", 'wdm_ld_course_review' ) . '</b> ' . __( 'plugin work, you need to install and activate', 'wdm_ld_course_review' ) . ' <b>LearnDash LMS</b> ' . __( 'first', 'wdm_ld_course_review' ) . '.</p></div>' );
