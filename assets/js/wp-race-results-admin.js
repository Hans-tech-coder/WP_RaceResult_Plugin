jQuery(document).ready(function ($) {
	var mediaUploader;

	$('#upload_banner_image_button').click(function (e) {
		e.preventDefault();
		// If the uploader object has already been created, reopen the dialog
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}
		// Extend the wp.media object
		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Banner Image',
			button: {
				text: 'Choose Image'
			},
			multiple: false
		});

		// When a file is selected, grab the URL and set it as the text field's value
		mediaUploader.on('select', function () {
			var attachment = mediaUploader.state().get('selection').first().toJSON();
			$('#banner_image').val(attachment.url);
			// Show preview
			$('#banner_image_preview').attr('src', attachment.url).show();
		});

		// Open the uploader dialog
		mediaUploader.open();
	});
	// Bulk actions: Select All checkbox
	$('#wprr-select-all').on('click', function () {
		var isChecked = $(this).prop('checked');
		$('input[name="result_ids[]"]').prop('checked', isChecked);
	});
});
