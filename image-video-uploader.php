<?php
/**
 * Plugin Name: ACF Image & Video Frontend Submission Form
 * Description: This plugin contains extra custom functions to allow front end submissions of limited items using a custom ACF Pro post type.
 * Author: WPProAtoZ
 * Author URI: https://wpproatoz.com
 * Version: 1.1
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: WPProAtoZ.com
 * Author URI: https://wpproatoz.com
 * Text Domain: wpproatoz-acf-image-video-submission
 * Update URI: https://github.com/Ahkonsu/wpproatoz-acf-image-video-submission/releases
 * GitHub Plugin URI: https://github.com/Ahkonsu/wpproatoz-acf-image-video-submission/releases
 * GitHub Branch: main
 * Requires Plugins: advanced-custom-fields-pro
 */
/**
 * Check for updates code
 */
require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/Ahkonsu/wpproatoz-acf-image-video-submission/',
    __FILE__,
    'wpproatoz-acf-image-video-submission'
);
// Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');
/**
 * Enqueue scripts and styles
 */
function iv_scripts() {
    wp_enqueue_style('iv-style', 'https://dl.dropboxusercontent.com/s/uqei847n1dvdyah/main.css');
    // Enqueue reCAPTCHA script and custom JS only on pages with the form
    if (is_page() && has_shortcode(get_post()->post_content, 'image_video_submission')) {
        $recaptcha_type = get_option('iv_recaptcha_type', 'none');
        $recaptcha_site_key = get_option('iv_recaptcha_site_key', '');
        if ($recaptcha_type !== 'none' && !empty($recaptcha_site_key)) {
            $script_url = $recaptcha_type === 'v3'
                ? 'https://www.google.com/recaptcha/api.js?render=' . esc_attr($recaptcha_site_key)
                : 'https://www.google.com/recaptcha/api.js';
            wp_enqueue_script('google-recaptcha', $script_url, array(), null, true);
        }
        // Get configurable CPT and field key
        $cpt_slug = get_option('iv_cpt_slug', 'image-video-submission');
        $field_key = get_option('iv_field_key', 'field_682e59ec3b45a');
        // Enqueue custom JS for client-side video validation and label updates
        wp_enqueue_script('iv-custom-js', plugin_dir_url(__FILE__) . 'vt-custom.js', array('jquery'), '1.1', true);
        wp_localize_script('iv-custom-js', 'ivSettings', array(
            'maxFileSize' => 30 * 1024 * 1024, // 30 MB in bytes
            'allowedTypes' => ['video/mp4', 'video/quicktime', 'video/x-m4v'], // MIME types for .mp4, .mov, .m4v
            'fieldKey' => $field_key,
            'errorSize' => __('File size exceeds 30 MB limit.', 'wpproatoz-acf-image-video-submission'),
            'errorType' => __('Only .mp4, .mov, and .m4v files are allowed.', 'wpproatoz-acf-image-video-submission'),
            'labelTitle' => sanitize_text_field(get_option('iv_field_label_title', 'Title')),
            'labelContent' => sanitize_text_field(get_option('iv_field_label_content', 'Content'))
        ));
    }
}
add_action('wp_enqueue_scripts', 'iv_scripts');
/**
 * Require TGM Plugin Activation
 */
require_once dirname(__FILE__) . '/class-tgm-plugin-activation.php';
add_action('tgmpa_register', 'iv_register_required_plugins');
function iv_register_required_plugins() {
    $plugins = array(
        array(
            'name' => 'Advanced Custom Fields Pro (ACF Pro)',
            'slug' => 'advanced-custom-fields-pro',
            'source' => 'https://www.advancedcustomfields.com', // Official ACF Pro download page
            'required' => true,
            'external_url' => 'https://www.advancedcustomfields.com/pro/', // Link for manual download
            'force_activation' => false,
            'force_deactivation' => false,
        ),
    );
    $config = array(
        'id' => 'iv-extra',
        'default_path' => '',
        'menu' => 'tgmpa-install-plugins',
        'parent_slug' => 'plugins.php',
        'capability' => 'manage_options',
        'has_notices' => true,
        'dismissable' => true,
        'is_automatic' => false,
    );
    tgmpa($plugins, $config);
}
/**
 * Add ACF form head
 */
add_action('wp_head', 'iv_acf_form_head');
function iv_acf_form_head() {
    $cpt_slug = get_option('iv_cpt_slug', 'image-video-submission');
    if (function_exists('acf_form_head') && is_page() && has_shortcode(get_post()->post_content, 'image_video_submission')) {
        acf_form_head();
    }
}
/**
 * Frontend submission form shortcode
 */
add_shortcode('image_video_submission', 'iv_display_submission_form');
function iv_display_submission_form() {
    if (!class_exists('ACF')) {
        return '<p>Error: Advanced Custom Fields Pro is required to use this form.</p>';
    }
    // Get configurable CPT and field key
    $cpt_slug = get_option('iv_cpt_slug', 'image-video-submission');
    $field_key = get_option('iv_field_key', 'field_682e59ec3b45a');
    // Check form access setting
    $form_access = get_option('iv_form_access', 'private');
    if ($form_access === 'private' && !is_user_logged_in()) {
        return '<p>Please log in to submit a submission.</p>';
    }
    ob_start();
    // Check if submission was successful
    if (isset($_GET['submitted']) && $_GET['submitted'] === 'true') {
        echo '<p class="iv-success-message">Thank you! Your submission has been submitted and is pending review.</p>';
    }
    // Get reCAPTCHA settings
    $recaptcha_type = get_option('iv_recaptcha_type', 'none');
    $recaptcha_site_key = get_option('iv_recaptcha_site_key', '');
    $recaptcha_enabled = $recaptcha_type !== 'none' && !empty($recaptcha_site_key) && !empty(get_option('iv_recaptcha_secret_key', ''));
    // Prepare reCAPTCHA HTML
    $recaptcha_html = '';
    if ($recaptcha_enabled) {
        if ($recaptcha_type === 'v2') {
            $recaptcha_html = '<div class="g-recaptcha" data-sitekey="' . esc_attr($recaptcha_site_key) . '"></div>';
        } elseif ($recaptcha_type === 'v3') {
            $recaptcha_html = '
<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
<script>
document.addEventListener("DOMContentLoaded", function() {
    grecaptcha.ready(function() {
        grecaptcha.execute("' . esc_attr($recaptcha_site_key) . '", {action: "submit_submission"}).then(function(token) {
            document.getElementById("g-recaptcha-response").value = token;
        });
    });
});
</script>';
        }
    } else {
        $recaptcha_html = '<input type="text" name="iv_honeypot" style="display:none;" value="">';
    }
    acf_form(array(
        'post_id' => 'new_post',
        'post_title' => true,
        'post_content' => true,
        'form' => true,
        'new_post' => array(
            'post_type' => $cpt_slug,
            'post_status' => 'pending',
            'post_author' => is_user_logged_in() ? get_current_user_id() : 1 // Current user if logged in, else default (admin)
        ),
        'fields' => array($field_key), // Configurable field key
        'submit_value' => 'Submit Submission',
        'return' => add_query_arg('submitted', 'true', get_permalink()),
        'form_attributes' => array(
            'enctype' => 'multipart/form-data'
        ),
        'html_before_fields' => $recaptcha_html
    ));
    return ob_get_clean();
}
/**
 * Customize upload field label
 */
add_filter('acf/load_field', 'iv_customize_upload_field_label');
function iv_customize_upload_field_label($field) {
    $field_key = get_option('iv_field_key', 'field_682e59ec3b45a');
    if ($field['key'] === $field_key) {
        $label = get_option('iv_field_label_upload', 'Upload');
        if (!empty($label)) {
            $field['label'] = sanitize_text_field($label);
        }
    }
    return $field;
}
/**
 * Add warning message below upload field label
 */
add_action('acf/render_field', 'iv_add_upload_warning', 10, 1);
function iv_add_upload_warning($field) {
    $field_key = get_option('iv_field_key', 'field_682e59ec3b45a');
    if ($field['key'] === $field_key) {
        echo '<p class="iv-warning-message">Video files limited to .mov, .m4v, .mp4 files only and files limited to 30MB max.</p>';
    }
}
/**
 * Restrict upload size and type
 */
add_filter('acf/upload_prefilter', 'iv_restrict_upload');
function iv_restrict_upload($errors) {
    $field_key = get_option('iv_field_key', 'field_682e59ec3b45a');
    if (!empty($_FILES['acf']['name'][$field_key])) {
        $file = $_FILES['acf']['name'][$field_key];
        $size = $_FILES['acf']['size'][$field_key];
        $allowed_types = ['video/mp4', 'video/quicktime', 'video/x-m4v'];
        $allowed_extensions = ['mp4', 'mov', 'm4v'];
        $max_size = 30 * 1024 * 1024; // 30 MB in bytes
        // Check file size
        if ($size > $max_size) {
            $errors[] = __('File size exceeds 30 MB limit.', 'wpproatoz-acf-image-video-submission');
        }
        // Check file type
        $file_type = wp_check_filetype($file)['type'];
        $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_extensions)) {
            $errors[] = __('Only .mp4, .mov, and .m4v files are allowed.', 'wpproatoz-acf-image-video-submission');
        }
    }
    return $errors;
}
/**
 * Validate reCAPTCHA or honeypot
 */
add_filter('acf/pre_save_post', 'iv_validate_submission', 10, 2);
function iv_validate_submission($post_id, $values) {
    $cpt_slug = get_option('iv_cpt_slug', 'image-video-submission');
    if ($post_id !== 'new_post' || get_post_type($post_id) !== $cpt_slug) {
        return $post_id;
    }
    // Get reCAPTCHA settings
    $recaptcha_type = get_option('iv_recaptcha_type', 'none');
    $recaptcha_site_key = get_option('iv_recaptcha_site_key', '');
    $recaptcha_secret_key = get_option('iv_recaptcha_secret_key', '');
    $recaptcha_v3_threshold = floatval(get_option('iv_recaptcha_v3_threshold', 0.5));
    $recaptcha_enabled = $recaptcha_type !== 'none' && !empty($recaptcha_site_key) && !empty($recaptcha_secret_key);
    // Check honeypot if reCAPTCHA is not enabled
    if (!$recaptcha_enabled && isset($_POST['iv_honeypot']) && !empty($_POST['iv_honeypot'])) {
        wp_die('Spam detected. Please try again.', 'Submission Error', array('back_link' => true));
    }
    // Validate reCAPTCHA if enabled
    if ($recaptcha_enabled && isset($_POST['g-recaptcha-response'])) {
        $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', array(
            'body' => array(
                'secret' => $recaptcha_secret_key,
                'response' => sanitize_text_field($_POST['g-recaptcha-response']),
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        ));
        if (is_wp_error($response)) {
            wp_die('reCAPTCHA verification failed. Please try again.', 'Submission Error', array('back_link' => true));
        }
        $result = json_decode(wp_remote_retrieve_body($response), true);
        if (!$result['success']) {
            wp_die('reCAPTCHA verification failed. Please try again.', 'Submission Error', array('back_link' => true));
        }
        // For v3, check score against threshold
        if ($recaptcha_type === 'v3' && $result['score'] <= $recaptcha_v3_threshold) {
            wp_die('reCAPTCHA score too low. Please try again.', 'Submission Error', array('back_link' => true));
        }
    } elseif ($recaptcha_enabled) {
        wp_die('Please complete the reCAPTCHA.', 'Submission Error', array('back_link' => true));
    }
    return $post_id;
}
/**
 * Display submissions shortcode
 */
add_shortcode('image_video_submissions', 'iv_display_submissions');
function iv_display_submissions($atts) {
    $cpt_slug = get_option('iv_cpt_slug', 'image-video-submission');
    $field_key = get_option('iv_field_key', 'field_682e59ec3b45a');
    $a = shortcode_atts(array(
        'limit' => 5,
    ), $atts);
    $query_args = array(
        'post_type' => $cpt_slug,
        'posts_per_page' => $a['limit'],
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    );
    $query = new WP_Query($query_args);
    if ($query->have_posts()) {
        $output = '<div class="iv-submissions"><ul class="submission-list">';
        while ($query->have_posts()) {
            $query->the_post();
            $upload = get_field($field_key); // Use configurable field key
            $title = get_the_title();
            $content = get_the_content();
            $output .= '
<li class="iv-submission">
<div class="submission-details">
<h3>' . esc_html($title) . '</h3>';
            if ($upload && is_array($upload)) {
                $output .= '
<div class="upload-container">
<video controls>
<source src="' . esc_url($upload['url']) . '" type="' . esc_attr($upload['mime_type']) . '">
Your browser does not support the video tag.
</video>
</div>';
            }
            $output .= '
<div class="submission-content">' . wp_kses_post($content) . '</div>
</div>
</li>';
        }
        $output .= '</ul></div>';
        wp_reset_postdata();
        return $output;
    } else {
        return '<p>No submissions found.</p>';
    }
}
/**
 * Add basic CSS for styling
 */
add_action('wp_head', 'iv_add_custom_styles');
function iv_add_custom_styles() {
    echo '
<style>
.iv-submissions {
    max-width: 800px;
    margin: 0 auto;
}
.submission-list {
    list-style: none;
    padding: 0;
}
.iv-submission {
    margin-bottom: 20px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
}
.upload-container {
    margin: 20px 0;
    max-width: 100%;
}
.upload-container video {
    max-width: 100%;
    height: auto;
}
.submission-content {
    margin-top: 15px;
}
.iv-success-message {
    color: #008000;
    font-weight: bold;
    margin-bottom: 20px;
}
.acf-form .acf-field-file input[type="file"] {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.g-recaptcha {
    margin-bottom: 15px;
}
.iv-error-message {
    color: #d63638;
    font-weight: bold;
    margin-top: 5px;
}
.iv-warning-message {
    color: #e67e22;
    font-size: 0.9em;
    margin-top: 5px;
    margin-bottom: 10px;
}
</style>';
}
/**
 * Add admin menus
 */
add_action('admin_menu', 'iv_add_admin_menu');
function iv_add_admin_menu() {
    $cpt_slug = get_option('iv_cpt_slug', 'image-video-submission');
    // Manage Post Status
    add_submenu_page(
        'edit.php?post_type=' . $cpt_slug,
        'Manage Submissions',
        'Manage Submissions',
        'manage_options',
        'iv-manage-submissions',
        'iv_manage_submissions_page'
    );
    // ACF Custom Form Settings
    add_submenu_page(
        'edit.php?post_type=' . $cpt_slug,
        'ACF Custom Form Settings',
        'ACF Custom Form',
        'manage_options',
        'iv-acf-form-settings',
        'iv_acf_form_settings_page'
    );
}
/**
 * Render the admin management page
 */
function iv_manage_submissions_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to access this page.');
    }
    $cpt_slug = get_option('iv_cpt_slug', 'image-video-submission');
    if (isset($_GET['action'], $_GET['post_id'], $_GET['_wpnonce']) && in_array($_GET['action'], ['publish', 'private', 'draft'])) {
        $post_id = intval($_GET['post_id']);
        $action = sanitize_text_field($_GET['action']);
        $nonce = sanitize_text_field($_GET['_wpnonce']);
        if (wp_verify_nonce($nonce, 'iv_status_' . $post_id)) {
            $new_status = $action;
            $update = wp_update_post(array(
                'ID' => $post_id,
                'post_status' => $new_status
            ));
            if ($update && !is_wp_error($update)) {
                echo '<div class="notice notice-success is-dismissible"><p>Submission status updated successfully.</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>Error updating submission status.</p></div>';
            }
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>Security check failed.</p></div>';
        }
    }
    $args = array(
        'post_type' => $cpt_slug,
        'posts_per_page' => -1,
        'post_status' => array('publish', 'pending', 'private', 'draft')
    );
    $submissions = new WP_Query($args);
    ?>
    <div class="wrap">
        <h1>Manage Image & Video Submissions</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($submissions->have_posts()) : while ($submissions->have_posts()) : $submissions->the_post(); ?>
                <tr>
                    <td><a href="<?php echo get_edit_post_link(); ?>"><?php the_title(); ?></a></td>
                    <td><?php echo esc_html(ucfirst(get_post_status())); ?></td>
                    <td><?php echo get_the_date(); ?></td>
                    <td>
                        <?php
                        $nonce = wp_create_nonce('iv_status_' . get_the_ID());
                        $publish_url = add_query_arg(array(
                            'action' => 'publish',
                            'post_id' => get_the_ID(),
                            '_wpnonce' => $nonce
                        ));
                        $private_url = add_query_arg(array(
                            'action' => 'private',
                            'post_id' => get_the_ID(),
                            '_wpnonce' => $nonce
                        ));
                        $draft_url = add_query_arg(array(
                            'action' => 'draft',
                            'post_id' => get_the_ID(),
                            '_wpnonce' => $nonce
                        ));
                        ?>
                        <?php if (get_post_status() !== 'publish') : ?>
                        <a href="<?php echo esc_url($publish_url); ?>" class="button button-primary">Make Public</a>
                        <?php endif; ?>
                        <?php if (get_post_status() !== 'private') : ?>
                        <a href="<?php echo esc_url($private_url); ?>" class="button button-secondary">Make Private</a>
                        <?php endif; ?>
                        <?php if (get_post_status() !== 'draft') : ?>
                        <a href="<?php echo esc_url($draft_url); ?>" class="button button-secondary">Make Draft</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; wp_reset_postdata(); else : ?>
                <tr>
                    <td colspan="4">No submissions found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
/**
 * Render the ACF Custom Form settings page
 */
function iv_acf_form_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to access this page.');
    }
    // Save settings
    if (isset($_POST['iv_save_settings']) && check_admin_referer('iv_save_form_settings')) {
        $form_access = isset($_POST['iv_form_access']) && $_POST['iv_form_access'] === 'public' ? 'public' : 'private';
        $recaptcha_type = in_array($_POST['iv_recaptcha_type'] ?? '', ['v2', 'v3', 'none']) ? $_POST['iv_recaptcha_type'] : 'none';
        $recaptcha_v3_threshold = floatval($_POST['iv_recaptcha_v3_threshold'] ?? 0.5);
        $recaptcha_v3_threshold = max(0.0, min(1.0, $recaptcha_v3_threshold)); // Clamp to 0.0-1.0
        $cpt_slug = sanitize_text_field($_POST['iv_cpt_slug'] ?? 'image-video-submission');
        $field_key = sanitize_text_field($_POST['iv_field_key'] ?? 'field_682e59ec3b45a');
        update_option('iv_form_access', $form_access);
        update_option('iv_recaptcha_type', $recaptcha_type);
        update_option('iv_recaptcha_v3_threshold', $recaptcha_v3_threshold);
        update_option('iv_recaptcha_site_key', sanitize_text_field($_POST['iv_recaptcha_site_key'] ?? ''));
        update_option('iv_recaptcha_secret_key', sanitize_text_field($_POST['iv_recaptcha_secret_key'] ?? ''));
        update_option('iv_field_label_title', sanitize_text_field($_POST['iv_field_label_title'] ?? 'Title'));
        update_option('iv_field_label_content', sanitize_text_field($_POST['iv_field_label_content'] ?? 'Content'));
        update_option('iv_field_label_upload', sanitize_text_field($_POST['iv_field_label_upload'] ?? 'Upload'));
        update_option('iv_cpt_slug', $cpt_slug);
        update_option('iv_field_key', $field_key);
        echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully.</p></div>';
    }
    $form_access = get_option('iv_form_access', 'private');
    $recaptcha_type = get_option('iv_recaptcha_type', 'none');
    $recaptcha_v3_threshold = get_option('iv_recaptcha_v3_threshold', 0.5);
    $recaptcha_site_key = get_option('iv_recaptcha_site_key', '');
    $recaptcha_secret_key = get_option('iv_recaptcha_secret_key', '');
    $field_label_title = get_option('iv_field_label_title', 'Title');
    $field_label_content = get_option('iv_field_label_content', 'Content');
    $field_label_upload = get_option('iv_field_label_upload', 'Upload');
    $cpt_slug = get_option('iv_cpt_slug', 'image-video-submission');
    $field_key = get_option('iv_field_key', 'field_682e59ec3b45a');
    ?>
    <div class="wrap">
        <h1>ACF Custom Form Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('iv_save_form_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="iv_cpt_slug">Custom Post Type Slug</label></th>
                    <td>
                        <input type="text" name="iv_cpt_slug" id="iv_cpt_slug" value="<?php echo esc_attr($cpt_slug); ?>" class="regular-text">
                        <p class="description">The slug for the custom post type (e.g., 'image-video-submission'). Ensure this matches your ACF setup.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="iv_field_key">ACF Field Key</label></th>
                    <td>
                        <input type="text" name="iv_field_key" id="iv_field_key" value="<?php echo esc_attr($field_key); ?>" class="regular-text">
                        <p class="description">The ACF field key for the upload field (e.g., 'field_682e59ec3b45a'). Find this in your ACF field group settings.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="iv_form_access">Form Access</label></th>
                    <td>
                        <label><input type="radio" name="iv_form_access" value="public" <?php checked($form_access, 'public'); ?>> Public (Guests can submit)</label><br>
                        <label><input type="radio" name="iv_form_access" value="private" <?php checked($form_access, 'private'); ?>> Private (Logged-in users only)</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="iv_recaptcha_type">reCAPTCHA Type</label></th>
                    <td>
                        <select name="iv_recaptcha_type" id="iv_recaptcha_type">
                            <option value="none" <?php selected($recaptcha_type, 'none'); ?>> None (Use Honeypot)</option>
                            <option value="v2" <?php selected($recaptcha_type, 'v2'); ?>> reCAPTCHA v2 (Checkbox)</option>
                            <option value="v3" <?php selected($recaptcha_type, 'v3'); ?>> reCAPTCHA v3 (Invisible)</option>
                        </select>
                        <p class="description">Select the reCAPTCHA type. <a href="https://docs.gravityforms.com/captcha/" target="_blank">Learn about reCAPTCHA.</a></p>
                    </td>
                </tr>
                <tr id="iv_recaptcha_v3_threshold_row" style="display: none;">
                    <th scope="row"><label for="iv_recaptcha_v3_threshold">reCAPTCHA v3 Score Threshold</label></th>
                    <td>
                        <input type="number" name="iv_recaptcha_v3_threshold" id="iv_recaptcha_v3_threshold" value="<?php echo esc_attr($recaptcha_v3_threshold); ?>" min="0.0" max="1.0" step="0.1" class="small-text">
                        <p class="description">Set the score threshold for reCAPTCHA v3 (0.0 to 1.0). Submissions with a score less than or equal to this value will be blocked. Default is 0.5.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="iv_recaptcha_site_key">reCAPTCHA Site Key</label></th>
                    <td>
                        <input type="text" name="iv_recaptcha_site_key" id="iv_recaptcha_site_key" value="<?php echo esc_attr($recaptcha_site_key); ?>" class="regular-text">
                        <p class="description">Enter your Google reCAPTCHA Site Key (for v2 or v3).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="iv_recaptcha_secret_key">reCAPTCHA Secret Key</label></th>
                    <td>
                        <input type="text" name="iv_recaptcha_secret_key" id="iv_recaptcha_secret_key" value="<?php echo esc_attr($recaptcha_secret_key); ?>" class="regular-text">
                        <p class="description">Enter your Google reCAPTCHA Secret Key (for v2 or v3).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="iv_field_label_title">Title Field Label</label></th>
                    <td>
                        <input type="text" name="iv_field_label_title" id="iv_field_label_title" value="<?php echo esc_attr($field_label_title); ?>" class="regular-text">
                        <p class="description">Custom label for the Title field (default: Title).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="iv_field_label_content">Content Field Label</label></th>
                    <td>
                        <input type="text" name="iv_field_label_content" id="iv_field_label_content" value="<?php echo esc_attr($field_label_content); ?>" class="regular-text">
                        <p class="description">Custom label for the Content field (default: Content).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="iv_field_label_upload">Upload Field Label</label></th>
                    <td>
                        <input type="text" name="iv_field_label_upload" id="iv_field_label_upload" value="<?php echo esc_attr($field_label_upload); ?>" class="regular-text">
                        <p class="description">Custom label for the Upload field (default: Upload).</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="iv_save_settings" class="button button-primary" value="Save Settings">
            </p>
        </form>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recaptchaType = document.getElementById('iv_recaptcha_type');
            const thresholdRow = document.getElementById('iv_recaptcha_v3_threshold_row');
            function toggleThresholdRow() {
                thresholdRow.style.display = recaptchaType.value === 'v3' ? '' : 'none';
            }
            toggleThresholdRow();
            recaptchaType.addEventListener('change', toggleThresholdRow);
        });
        </script>
    </div>
    <?php
}
/**
 * Enqueue admin styles
 */
add_action('admin_enqueue_scripts', 'iv_admin_styles');
function iv_admin_styles($hook) {
    $cpt_slug = get_option('iv_cpt_slug', 'image-video-submission');
    if (!in_array($hook, [$cpt_slug . '_page_iv-manage-submissions', $cpt_slug . '_page_iv-acf-form-settings'])) {
        return;
    }
    wp_enqueue_style('iv-admin-style', plugin_dir_url(__FILE__) . 'admin-style.css');
    echo '
<style>
.wp-list-table .column-actions { width: 300px; }
.button-primary, .button-secondary { margin-right: 5px; }
</style>';
}
?>