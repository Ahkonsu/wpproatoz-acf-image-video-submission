# ACF Image & Video Frontend Submission Form
![Plugin Version](https://img.shields.io/badge/version-1.1-blue)
![WordPress Compatibility](https://img.shields.io/badge/WordPress-6.0%2B-green)
![PHP Compatibility](https://img.shields.io/badge/PHP-8.0%2B-green)
![License](https://img.shields.io/badge/license-GPLv2%2B-blue)

A WordPress plugin for creating a frontend submission form for image and video uploads using Advanced Custom Fields Pro (ACF Pro), with reCAPTCHA support, customizable field labels, and admin management tools.

## Overview
The **ACF Image & Video Frontend Submission Form** plugin enables users to submit image and video uploads via a frontend form, integrated with a configurable custom post type (default: `image-video-submission`). It offers robust features like upload restrictions, spam protection via reCAPTCHA v2/v3, and admin interfaces for managing submissions and form settings. Ideal for websites needing user-generated content with secure and customizable form handling.

## Features
- **Frontend Submission Form**: Submit submissions with `[image_video_submission]` shortcode, including title, content, and upload fields.
- **Submissions Display**: Show published submissions with `[image_video_submissions limit="5"]`.
- **Configurable CPT and Field**: Set custom post type slug and ACF field key via admin settings.
- **Customizable Labels**: Configure field labels (Title, Content, Upload) via admin settings.
- **Upload Restrictions**: Limit uploads to 30 MB and `.mov`, `.mp4`, `.m4v` formats, with a warning message.
- **reCAPTCHA Support**: Enable v2 (Checkbox) or v3 (Invisible) with adjustable score threshold (default: 0.5).
- **Form Access Control**: Public (guest submissions) or private (logged-in users only).
- **Admin Management**:
  - **Manage Submissions**: Toggle submission statuses (Publish, Private, Draft).
  - **ACF Custom Form**: Configure form settings, reCAPTCHA, CPT slug, field key, and labels.
- **Secure Validation**: Client-side and server-side checks for file uploads and spam protection.
- **Dependency Management**: Requires Advanced Custom Fields Pro (ACF Pro), prompted via TGM Plugin Activation.

## Installation
### From WordPress Admin
1. Download the plugin ZIP from [GitHub Releases](https://github.com/Ahkonsu/wpproatoz-acf-image-video-submission/releases).
2. In WordPress admin, go to **Plugins > Add New > Upload Plugin**.
3. Upload the ZIP file and activate the plugin.
4. Install and activate Advanced Custom Fields Pro (ACF Pro) when prompted.

### Manual Installation
1. Clone or download the repository to `/wp-content/plugins/wpproatoz-acf-image-video-submission`.

git clone https://github.com/Ahkonsu/wpproatoz-acf-image-video-submission.git wpproatoz-acf-image-video-submission
Activate the plugin via the WordPress admin Plugins page.
Install and activate ACF Pro when prompted.

Usage
Shortcodes

[image_video_submission]:
Add to a page to display the submission form.
Fields: Title, Content, Upload (30 MB max, .mov, .mp4, .m4v).
Supports public or private access, with reCAPTCHA v2/v3 or honeypot.
Example: [image_video_submission]

[image_video_submissions limit="5"]:
Displays published submissions.
limit: Number of submissions (default: 5).
Example: [image_video_submissions limit="3"]


Configuration

Go to Image & Video Submissions > ACF Custom Form in the WordPress admin.
Settings:
Custom Post Type Slug: Set the CPT slug (default: image-video-submission).
ACF Field Key: Set the upload field key (default: field_682e59ec3b45a).
Form Access: Public (guests) or Private (logged-in users).
reCAPTCHA:
None: Uses honeypot.
v2 (Checkbox): Requires user verification.
v3 (Invisible): Background validation with score threshold (0.0–1.0, default: 0.5).
Enter Site Key and Secret Key from Google reCAPTCHA.

Field Labels: Customize labels (e.g., "Image/Video Title", "Description", "Upload File").

Save settings.
Ensure the CPT and upload field are set up in ACF Pro.

Managing Submissions

View Submissions: Go to Image & Video Submissions > All Submissions.
Manage Statuses: Use Image & Video Submissions > Manage Submissions to set Publish, Private, or Draft.
Published submissions appear via [image_video_submissions].

Requirements

WordPress 6.0+
PHP 8.0+
Advanced Custom Fields Pro (ACF Pro) plugin
Server settings: upload_max_filesize = 30M, post_max_size = 32M in php.ini

Contributing
Contributions are welcome! Please:

Fork the repository.
Create a feature branch (git checkout -b feature/YourFeature).
Commit changes (git commit -m 'Add YourFeature').
Push to the branch (git push origin feature/YourFeature).
Open a Pull Request.

Report issues or suggest features at GitHub Issues.
Changelog
1.1 (2025-12-08)

Forked and universalized: Configurable CPT slug and ACF field key.
Updated shortcodes: [image_video_submission] and [image_video_submissions].
Enhanced admin settings for CPT and field configuration.
Updated prefixes, text domain, and styling classes.

1.0 (2025-06-11)

Initial release.

License
This plugin is licensed under the GPLv2 or later.
Support
For support, contact WPProAtoZ at https://wpproatoz.com or open an issue on GitHub.