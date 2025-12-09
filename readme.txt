=== ACF Image & Video Frontend Submission Form ===
Contributors: wpproatoz, John, Grok
Tags: acf, custom post type, frontend form, image video submission, recaptcha
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 8.0
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires Plugins: advanced-custom-fields-pro
== Description ==
The ACF Image & Video Frontend Submission Form plugin allows users to submit image and video uploads via a frontend form, integrated with Advanced Custom Fields Pro (ACF Pro). It supports a configurable custom post type (default: image-video-submission) with fields for title, content, and upload. Features include customizable field labels, file restrictions (30 MB max, .mov, .mp4, .m4v), reCAPTCHA v2/v3 for spam protection, and admin tools to manage post statuses and form settings.
Key Features:

Frontend form with shortcode [image_video_submission] for submitting submissions.
Display submissions with [image_video_submissions limit="5"].
Configurable CPT slug and ACF field key.
Customizable field labels via admin settings.
Upload restrictions: 30 MB max, .mov, .mp4, .m4v formats.
reCAPTCHA v2 (Checkbox) or v3 (Invisible) with configurable score threshold.
Public or private form access (logged-in users only).
Admin menus for managing post statuses (Publish, Private, Draft) and form settings.
Warning message for upload restrictions.
Client-side and server-side validation for secure submissions.

== Installation ==

Upload the wpproatoz-acf-image-video-submission folder to the /wp-content/plugins/ directory.
Activate the plugin through the 'Plugins' menu in WordPress.
Install and activate the required Advanced Custom Fields Pro (ACF Pro) plugin when prompted.
Configure the plugin settings under Image & Video Submissions > ACF Custom Form in the WordPress admin.
Add the [image_video_submission] shortcode to a page for the submission form.
Add the [image_video_submissions] shortcode to display approved submissions.

== Frequently Asked Questions ==
= What are the requirements for this plugin? =
The plugin requires WordPress 6.0+, PHP 8.0+, and the Advanced Custom Fields Pro (ACF Pro) plugin.
= How do I set up reCAPTCHA? =

Register your site at Google reCAPTCHA.
Obtain v2 Checkbox or v3 Invisible keys.
Enter the Site Key and Secret Key in Image & Video Submissions > ACF Custom Form.
For v3, set a score threshold (0.0 to 1.0, default 0.5).

= How do I customize field labels? =
Go to Image & Video Submissions > ACF Custom Form and update the Title, Content, and Upload field labels. Save to apply changes to the frontend form.
= What file types and sizes are allowed for uploads? =
Only .mov, .mp4, and .m4v files are allowed, with a maximum size of 30 MB. A warning message appears below the upload field.
= Can guests submit submissions? =
Yes, if Form Access is set to Public in Image & Video Submissions > ACF Custom Form. Otherwise, only logged-in users can submit.
= How do I configure the CPT and field key? =
In Image & Video Submissions > ACF Custom Form, set the Custom Post Type Slug and ACF Field Key to match your ACF setup.
== Screenshots ==

Frontend submission form with custom labels and upload warning.
Admin settings page for configuring form access, reCAPTCHA, CPT, field key, and labels.
Manage Submissions page for toggling statuses.

== Changelog ==

= 1.3.1 = (2025-12-08)
* Added Documentation tab in admin settings for quick access to plugin docs.

= Upgrade Notice =
= 1.3.1 =
New Documentation tab added to settings page.

= 1.3 =
* Added separate ACF field keys for images and videos.
* New "Upload Mode" setting to choose Images Only, Videos Only, or Both.
* Independent validation and display for each media type.

= Upgrade Notice =
= 1.3 =
Configure separate image/video field keys and upload mode in settings.


= 1.2 =
* Added dedicated admin menu and settings link in plugins list.
* Added support for image uploads (.jpg, .jpeg, .png, .webp).
* Updated messages and validation for mixed media.
* Enhanced display shortcode to render images with <img> tag.

= Upgrade Notice =
= 1.2 =
Added separate settings page and image upload support. Update shortcodes if needed.

= 1.1 =

Universal fork: Added configurable CPT slug and ACF field key.
Updated shortcodes to [image_video_submission] and [image_video_submissions].
Enhanced admin settings and styling.

= 1.0 =

Initial release.

== Upgrade Notice ==
= 1.1 =
Forked for universality with configurable CPT and field key. Update shortcodes accordingly.