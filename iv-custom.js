jQuery(document).ready(function($) {
    // Video upload validation
    const fieldKey = ivSettings.fieldKey;
    const videoInput = $(`input[name="acf\\[${fieldKey}\\]"]`);
    if (videoInput.length) {
        videoInput.on('change', function(e) {
            const file = e.target.files[0];
            let errorContainer = $('.iv-error-message');
            if (!errorContainer.length) {
                errorContainer = $('<div class="iv-error-message"></div>').insertAfter(videoInput);
            }
            // Clear previous errors
            errorContainer.text('');
            if (file) {
                // Check file size
                if (file.size > ivSettings.maxFileSize) {
                    errorContainer.text(ivSettings.errorSize);
                    videoInput.val('');
                    return;
                }
                // Check file type
                if (!ivSettings.allowedTypes.includes(file.type)) {
                    errorContainer.text(ivSettings.errorType);
                    videoInput.val('');
                    return;
                }
            }
        });
    }
    // Update field labels
    const titleLabel = $('label[for="acf-_post_title"]');
    const contentLabel = $('label[for="acf-_post_content"]');
   
    if (titleLabel.length && ivSettings.labelTitle) {
        titleLabel.contents().filter(function() {
            return this.nodeType === 3; // Text nodes
        }).first().replaceWith(ivSettings.labelTitle);
    }
   
    if (contentLabel.length && ivSettings.labelContent) {
        contentLabel.contents().filter(function() {
            return this.nodeType === 3; // Text nodes
        }).first().replaceWith(ivSettings.labelContent);
    }
});