jQuery(document).ready(function($) {
    // Upload validation for images and videos
    const uploadMode = ivSettings.uploadMode;
    const imageFieldKey = ivSettings.imageFieldKey;
    const videoFieldKey = ivSettings.videoFieldKey;

    // Image validation
    if ((uploadMode === 'images' || uploadMode === 'both') && imageFieldKey) {
        const imageInput = $(`input[name="acf\\[${imageFieldKey}\\]"]`);
        if (imageInput.length) {
            imageInput.on('change', function(e) {
                const file = e.target.files[0];
                let errorContainer = $(this).siblings('.iv-error-message');
                if (!errorContainer.length) {
                    errorContainer = $('<div class="iv-error-message"></div>').insertAfter(this);
                }
                // Clear previous errors
                errorContainer.text('');
                if (file) {
                    // Check file size
                    if (file.size > ivSettings.maxFileSize) {
                        errorContainer.text(ivSettings.errorSize);
                        imageInput.val('');
                        return;
                    }
                    // Check file type by MIME and extension
                    const fileType = file.type;
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    if (!ivSettings.imageTypes.includes(fileType) || !ivSettings.imageExtensions.includes(fileExt)) {
                        errorContainer.text(ivSettings.errorImageType);
                        imageInput.val('');
                        return;
                    }
                }
            });
        }
    }

    // Video validation
    if ((uploadMode === 'videos' || uploadMode === 'both') && videoFieldKey) {
        const videoInput = $(`input[name="acf\\[${videoFieldKey}\\]"]`);
        if (videoInput.length) {
            videoInput.on('change', function(e) {
                const file = e.target.files[0];
                let errorContainer = $(this).siblings('.iv-error-message');
                if (!errorContainer.length) {
                    errorContainer = $('<div class="iv-error-message"></div>').insertAfter(this);
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
                    // Check file type by MIME and extension
                    const fileType = file.type;
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    if (!ivSettings.videoTypes.includes(fileType) || !ivSettings.videoExtensions.includes(fileExt)) {
                        errorContainer.text(ivSettings.errorVideoType);
                        videoInput.val('');
                        return;
                    }
                }
            });
        }
    }

    // Update field labels
    const titleLabel = $('label[for="acf-_post_title"]');
    const contentLabel = $('label[for="acf-_post_content"]');
   
    if (titleLabel.length && ivSettings.labelTitle) {
        titleLabel.contents().filter(function() {
            return this.nodeType === 3; // Text nodes
        }).first().replaceWith(ivSettings.labelTitle + ': ');
    }
   
    if (contentLabel.length && ivSettings.labelContent) {
        contentLabel.contents().filter(function() {
            return this.nodeType === 3; // Text nodes
        }).first().replaceWith(ivSettings.labelContent + ': ');
    }
});