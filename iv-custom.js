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
        let errorContainer = uploader.find('.iv-error-message');
        if (!errorContainer.length) {
            errorContainer = $('<div class="iv-error-message"></div>').appendTo(uploader);
        }
        errorContainer.css(errorStyle);
        return errorContainer;
    }

    if ((uploadMode === 'images' || uploadMode === 'both') && imageFieldKey) {
        const imageInput = $(`input[name="acf\\[${imageFieldKey}\\]"]`);
        const imageUploader = imageInput.closest('.acf-basic-uploader');
        if (imageUploader.length) {
            imageUploader.find('[data-name="add"]').text('Choose Image');
            imageInput.on('change', function(e) {
                const errorContainer = getErrorContainer(imageUploader);
                const file = e.target.files[0] || null;
                errorContainer.text('');
                if (file) {
                    if (file.size > ivSettings.maxImageSize) {
                        errorContainer.text(`File size exceeds ${ivSettings.imageMaxMB} MB limit.`);
                        $(this).val('');
                        return;
                    }
                    const fileType = file.type;
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    if (!ivSettings.imageTypes.includes(fileType) || !ivSettings.imageExtensions.includes(fileExt)) {
                        errorContainer.text(ivSettings.errorImageType);
                        $(this).val('');
                        return;
                    }
                }
            });
        }
    }

    if ((uploadMode === 'videos' || uploadMode === 'both') && videoFieldKey) {
        const videoInput = $(`input[name="acf\\[${videoFieldKey}\\]"]`);
        const videoUploader = videoInput.closest('.acf-basic-uploader');
        if (videoUploader.length) {
            videoUploader.find('[data-name="add"]').text('Choose Video');
            videoInput.on('change', function(e) {
                const errorContainer = getErrorContainer(videoUploader);
                const file = e.target.files[0] || null;
                errorContainer.text('');
                if (file) {
                    if (file.size > ivSettings.maxVideoSize) {
                        errorContainer.text(`File size exceeds ${ivSettings.videoMaxMB} MB limit.`);
                        $(this).val('');
                        return;
                    }
                    const fileType = file.type;
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    if (!ivSettings.videoTypes.includes(fileType) || !ivSettings.videoExtensions.includes(fileExt)) {
                        errorContainer.text(ivSettings.errorVideoType);
                        $(this).val('');
                        return;
                    }
                }
            });
        }
    }

    const titleLabel = $('label[for="acf-_post_title"]');
    const contentLabel = $('label[for="acf-_post_content"]');

    if (titleLabel.length && ivSettings.labelTitle) {
        titleLabel.contents().filter(function() { return this.nodeType === 3; }).first().replaceWith(ivSettings.labelTitle + ': ');
    }

    if (contentLabel.length && ivSettings.labelContent) {
        contentLabel.contents().filter(function() { return this.nodeType === 3; }).first().replaceWith(ivSettings.labelContent + ': ');
    }
});