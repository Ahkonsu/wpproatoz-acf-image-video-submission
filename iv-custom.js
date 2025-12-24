jQuery(document).ready(function($) {
    const uploadMode = ivSettings.uploadMode;
    const imageFieldKey = ivSettings.imageFieldKey;
    const videoFieldKey = ivSettings.videoFieldKey;

    const errorStyle = {
        'color': '#d63638',
        'font-weight': 'bold',
        'text-align': 'center',
        'margin-top': '10px',
        'font-size': '0.9em'
    };

    function getErrorContainer(uploader) {
        let error = uploader.find('.iv-error-message');
        if (!error.length) {
            error = $('<div class="iv-error-message"></div>').appendTo(uploader);
        }
        error.css(errorStyle);
        return error;
    }

    if ((uploadMode === 'images' || uploadMode === 'both') && imageFieldKey) {
        const imageUploader = $(`input[name="acf\\[${imageFieldKey}\\]"]`).closest('.acf-basic-uploader');
        if (imageUploader.length) {
            imageUploader.find('[data-name="add"]').text('Choose Image');
        }
    }

    if ((uploadMode === 'videos' || uploadMode === 'both') && videoFieldKey) {
        const videoUploader = $(`input[name="acf\\[${videoFieldKey}\\]"]`).closest('.acf-basic-uploader');
        if (videoUploader.length) {
            videoUploader.find('[data-name="add"]').text('Choose Video');
        }
    }

    // Validation on change (optional but good)
    // ... keep the validation code from previous if wanted

    // Title & Description labels
    const titleLabel = $('label[for="acf-_post_title"]');
    const contentLabel = $('label[for="acf-_post_content"]');

    if (titleLabel.length && ivSettings.labelTitle) {
        titleLabel.contents().filter(function() { return this.nodeType === 3; }).first().replaceWith(ivSettings.labelTitle + ': ');
    }
    if (contentLabel.length && ivSettings.labelContent) {
        contentLabel.contents().filter(function() { return this.nodeType === 3; }).first().replaceWith(ivSettings.labelContent + ': ');
    }
});