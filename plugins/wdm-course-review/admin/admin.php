<?php
/**
 * This file is used to require all the files needed for admin-side logic.
 *
 * @file admin.php
 * @package RatingsReviewsFeedback\Admin
 */

/**
 * Include all adminside files as well as functional logic.
 */
require_once 'class-course-review-cpt.php'; // Review CPT.
require_once 'class-course-metabox.php';    // For adding meta box on course.
require_once 'class-course-feedback-cpt.php'; // Feedback CPT.
require_once 'class-instructor-handler.php'; // IR complatibility.
