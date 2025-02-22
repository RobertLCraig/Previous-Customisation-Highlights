jQuery(document).ready(function($){
    var mediaUploader;

    // Event listener for adding images
    $('#add-previous-customisations-images').click(function(e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Images',
            button: {
                text: 'Add Images'
            },
            multiple: true
        });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').toJSON();
            var image_ids = [];
            var gallery_wrapper = $('#previous-customisations-gallery-wrapper');
            gallery_wrapper.empty();
            $.each(attachment, function(index, image) {
                image_ids.push(image.id);
                gallery_wrapper.append('<div class="previous-customisation-image-wrapper"><img src="' + image.sizes.thumbnail.url + '" /><button type="button" class="remove-customisation-image" data-image-id="' + image.id + '">Delete</button></div>');
            });
            $('#previous_customisations_images').val(JSON.stringify(image_ids));
        });
        mediaUploader.open();
    });

    // Event listener for removing images
    $(document).on('click', '.remove-customisation-image', function() {
        var imageId = $(this).data('image-id');
        var galleryWrapper = $('#previous-customisations-gallery-wrapper');
        var imageIds = JSON.parse($('#previous_customisations_images').val());

        imageIds = imageIds.filter(function(id) {
            return id != imageId;
        });

        $(this).closest('.previous-customisation-image-wrapper').remove();
        $('#previous_customisations_images').val(JSON.stringify(imageIds));
    });
});
