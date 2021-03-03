=== Instructor Role Extension for LearnDash ===
Current Version: 3.5.4
Author:  WisdmLabs
Author URI: http://wisdmlabs.com/
Plugin URI: http://wisdmlabs.com/instructor-role-extension-for-learndash/
Tags: LearnDash Add-on, Instructor Role LearnDash, User Role LearnDash
Requires at least: 4.1.1
Tested up to: 4.9.6
License: GNU General Public License v2 or later

Tested with LearnDash version: 3.0.x
Tested with WooCommerce version: 3.6.2
Tested with EDD version: 2.8.18

== Description ==

The Instructor Role Extension for LearnDash adds a user role 'Instructor' into your WordPress website. An 'Instructor' has capabilities to create courses content and track student progress, thus behaving as a teacher, instructor, or guide for students enrolled in your LearnDash LMS.

== Installation Guide ==
1. Upon purchasing the Instructor Role Extension plugin, an email will be sent to the registered email id, with the download link for the plugin and a purchase receipt id. Download the plugin using the download link.
2. Go to Plugin-> Add New menu in your dashboard and click on the ‘Upload’ tab. Choose the ‘instructore-role.zip’ file to be uploaded and click on ‘Install Now’.
3. After the plugin has been installed successfully, click on the Activate Plugin link or activate the Instructor Role plugin from your Plugins page.
4. A Instructor Role License sub-menu will be created under Plugins menu in your dashboard. Click on this menu and enter your purchased product's license key. Click on Activate License. If license in valid, an 'Active' status message will be displayed, else 'Inactive' will be displayed.
5. Upon entering a valid license key, and activating the license, a new user role ‘Instructor’ will be added to your LearnDash website.

== User Guide ==
How do I create an Instructor?
To create an instructor, create a new user and set the user’s role as ‘Instructor’ and save. To change an existing user’s role to an instructor, edit the user’s profile and change the user’s role to ‘Instructor’ and save the changes made.

How can an Instructor view Course Reports?
An instructor can view reports for courses created by going to LearnDash LMS -> Course Reports. The report for a course is displayed using a pie chart, and details for each student enrolled for the course are displayed in a tabular format. The course report can be downloaded as a CSV using the 'Export Course Data' button.

== Features ==
1. Instructor has the capability to create and edit his/her own courses/lessons/topics/quizzes/certificates.
2. Instructor can approve assigments for his/her own courses.
3. Instructor can view and download course reports.


== Changelog ==
= 3.5.4 =
* Bug: 'Course' string is now render from the custom label.
* Bug: Fixed instructor course limit bypass checks for shared and owned courses.
* Bug: Fixed instructor course limit bypass checks to access other courses as normal students.
* Bug: Fixed multisite crash due to authentication cookie variable check on whitelabelling LD strings.
* Bug: Fixed pluggable function redeclaration error causing issues with email add-ons.
* Bug: Updated essay and assignment handling logic to unhook LD actions and add own actions to add instructor access to shared and owned course assignments and essays.
* Bug: Fixed profile and payout settings javascript loading issue.
* Update: Added new filter hook to allow 3rd party plugins to customize instructor CSV reports data.
* Update: All strings are translation ready.
* Removed: Redundant code causing warnings and errors on sites.
= 3.5.3 =
* Critical security fix to remove phpunit composer files.
= 3.5.2 =
* Added new additional instructor settings section to hide specific settings and metaboxes on instructor dashboard.
* Updated instructor dashboard pages strings to whitelabel specific LearnDash strings.
* Fixed course grid styling issues on profile page.
* Fixed instructor profile image styling and mobile view display issues.
* Fixed filtering on assignments, essays, quizzes, questions and certificate listing pages for instructors.
= 3.5.1 =
* Fixed Yoast compatibility issue
* Fixed learndash notification access for instructors
* Fixed scheduled content access for instructor for shared and owned courses on front end.
= 3.5.0 =
* Added new instructor profile page to display instructor bio and course relevant information on the front end.
* Added compatibility for the Students Voice for LearnDash plugin to allow instructors to view and manage student voices from the instructor dashboard.
* Added new setting to restrict instructor course category and tags access from instructor dashboard.
* Added notification on the course edit page to notify instructors when a course is sent for review.
* Updated review module to draft courses when a course is pending instructor approval.
= 3.4.1 =
* Updated completed and total steps data display order.
* Updated ir_get_date_in_site_timezone function to set default timezone to UTC if not set.
* Updated payouts code to handle exceptions when fetching previous transactions.
* Updated ld-profile shortcode to fetch all instructor owned and shared. 
* Updated notifications module to check for LearnDash Notifications plugin as a dependency.
* Updated stripe payment transaction meta keys to fix commissions code.
* Fixed instructor comments reply to feature for shared courses
* Fixed shared course access for instructors.
* Fixed listing of instructor as group leaders on course and group edit pages.
* Fixed comments to display assignment and essay comments for instructors.
* Fixed quiz statistics loading issue on instructor dashboard.
* Fixed email tab visibility for instructors.
* Fixed course reports data for total and completed steps data displayed.
= 3.4.0 =
* Added new feature to pay instructor commissions using paypal payouts
* Fixed a listing issue for shared courses displaying lessons and topics with only view option.
* Fixed paypal course commission recording issue.
* Fixed shared course listing under lesson, topic and quiz edit page under association settings.
* Added support for Gamipress point-types post type by excluding them from restricted post types for instructors.
* Fixed instructor group listing on course edit page.
* Fixed filtering of users listed on course edit page to the ones enrolled in instructor courses.
* Fixed lesson redirect to course archive page issue
* Added support for ACF by excluding filtering advanced custom field on instructor dashboard.
* Added previlege to perform actions on comments of co-instructor course contents.
= 3.3.4 =
* Fixed warnings on reports page while fetching LearnDash course and lessons labels.
* Fixed essays and assignments access on instructor dashboard.
* Fixed screen options styling issue on instructor dashboard.
* Added access the groups tab on course edit page for instructors.
* Added group edit permissions to allow admin to assign instructors group leaders for existing groups.
* Added new template function to fetch plugin templates
* Added access to group administration screen for instructors.
* Updated assignment handling code for correct assignment listings on instructor dashboard.
* Updated instructor groups to include owned and shared courses for groups.
* Removed previously deprecated class report generation code files.
* Removed access to the author dropdown on the right hand side menu on course content edit pages.
= 3.3.3 =
* Fixed instructor menu items missing links issue.
= 3.3.2 =
* Fixed svg image include error.
* Fixed submissions incorrect data display error.
* Updated instructor logout to redirect to home page.
= 3.3.1 =
* Fixed issue on course reports page throwing error for empty report.
= 3.3.0 =
* Added option to set instructor dashboard menu from WP menu settings.
* Added access to comments to approve and reject comments for instructors.
* Added ability to create and manage group for instructors.
* Fixed bugs to support course sharing with shared steps enabled.
* Fixed instructor overview page statistics to include shared course details.
* Fixed bug on course edit page displaying Group Settings tab twice for instructors.
= 3.2.2 =
* Fixed bug to restrict access to instructor commissions to only administrators.
= 3.2.1 =
* Added new feature to send emails to instructors on course purchase via a woocommerce product along with a template to design the email.
* Added instructor profile and logout links to the instructor dashboard.
the email template on the instructor dashboard
* Updated course reports feature to fetch users specific to course instead of searching in all users
* Updated checks to ensure current page is course page before enqueuing necessary scripts and styles
* Updated capabilities to allow instructors to view and add H5P data
* Updated CSV code to suppress warnings when instantiating an object of the CSV parser class to avoid error messages in the output CSV
* Fixed mobile menu icon bug when displaying primary menu on instructor dashboard
* Fixed quiz import/export for instructors

= 3.2.0 =
* New feature to share courses among instructors so that multiple instructors can collaborate on the same course.
* Fixed jetpack menu page showing up in instructor dashboard.
* Fixed the menu bug of displaying the submitted essays submenu on top instead of overview page.
* Added styling to make plugin responsive and mobile ready.
* Fixed empty submissions table throwing an error when applying datatables.
* Optimized plugin by removing unnecessary images and compressing all png images.
* Added fix to updating all course content author when updating a course author by administrator.
* Added instructor menu compatibility with the User Menus plugin by Jungle Plugins.

= 3.1.4 =
* Removed incomplete paypal commisison integration files.

= 3.1.3 =
* Updated submissions table with datatables to add pagination, search and sorting features.
* Fixed setting to hide earnings donut chart if instructor commissions is disabled.
* Fixed instructor emails not being sent on quiz completion.
* Fixed redeclaration function issue due to pluggable.php file being required directly.

= 3.1.2 =
* Added fixes for FCC to IR commission migration
* Fixed instructor overview page donut chart colour selection bug
* Updated theme template CSS
* Added LearnDash-Stripe transaction commission bug fix

= 3.1.1 =
* Added fixes for displaying submissions respective to instructors
* Updated assignment and essay edit links.

= 3.1.0 =
* Added Admin Customizer and 3 new style templates.
* Added Instructor overview page.
* Added plugin activation feature showcase template.

= 3.0.9 =
* Compatibility with Gutenberg editor for admin approval button.
* Compatibility with LD 3.0-beta1

= 3.0.8 =
* Compatibility with LearnDash v2.6.3.
* Compatibility with Elementor.
* Made it compatible with Postman plugin.

= 3.0.7 =
* Improved licensing code.

= 3.0.5 =
* Improved licensing code.
* Compatibility with LearnDash v2.5.*
* Added BadgeOS support.
* Fixed menu issue on multi-language.


= 3.0.4 =
* Fixed a bug, was throwing an error for prior 2.4 LearnDash versions.
* Fixed a bug, resolved fatal error if LearnDash is deactivated.

= 3.0.3 =
* Fixed a bug, instructors were having access to import/export tab and could edit/delete quizzes and questions of other instructors/users.

= 3.0.2 =
* Added a feature to enroll instructor automatically into their course.
* Fixed a bug of "menu is not visible for instructor" for LearnDash LMS version >2.4.0.

= 3.0.1 =
* Compatible with LD v2.3.0.2

= 3.0.0 =
* Integrated with EDD v.2.6.6
* Now instructor's email ID will be used when an instructor is sending an email to a student.

= 2.4.1 =
* Fixed the issue of report and instructor page which was not accessible by Admin.

= 2.4.0 =
* Compatible with LD v2.2.1.1
* Compatible with WooCommerce v2.6.3
* Fixed issue of export data on Hebrew.
* Provided a feature to disable commission feature.
* Added Bio widget for administrator.
* Added hooks for developers.
* Fixed issue of lesson access which was not accessible by instructor.


= 2.0 =
* Commission Feature implemented for Instructor Role
* Fixed minor bugs

= 1.4 =
* Issue resolved for incorrect course reports for multilingual sites.
* Course reports tab available to the administrator.
* On some websites, Instructor was able to see content from other users. This issue is fixed now.

= 1.3 =
* Compatible with WooCommerce (2.3.11) for instructors to create courses as WooCommerce products
* Issue resolved for the appearence tab (for some themes) displayed on the instructor dashboard
* Issue with the reports display for multi lingual resolved
* Filters to add additional post types access

= 1.2 =
* Made plugin compatible with Multisite network
* Add media issue fixed

= 1.1 =
* Pagination added for course data

= 1.0 = 
* Plugin Released

= *0.9 =
* Beta Version Released.


== FAQ ==

For how long is a license valid?
Every license is valid for a year from the date of purchase. During this year you will receive free support. After the license expires you can renew the license for a discounted price.

What will happen if my license expires?
If your license expires, you will still be able to use the plugin, but you will not receive any support or updates. To continue receiving support for the plugin, you will need to purchase a new license key.

Is the license valid for more than one site?
Every purchased license is valid for one site. For multiple site, you will have to purchase additional license keys.

Help! I lost my license key!
In case you have misplaced your purchased product license key, kindly go back and retrieve your purchase receipt id from your mailbox. Use this receipt id to make a support request to retrieve your license key.

How do I contact you for support?
You can direct your support request to us, using the Support form on our website.

Do you have a refund policy?
Yes. we offer refunds requested within 30 days of purchase. But the refund will be granted only if the plugin does not work on your site and has integration issues, which we are unable to fix, even after support requests have been made.

Kindly refer to http://wisdmlabs.com/instructor-role-extension-for-learndash/ for additional details.


This is a test from Reza to check the CodeCommit pipeline.
