jQuery(document).ready(function ($) {
	var bannerMediaUploader;
	var logoMediaUploader;

	// Banner Image Uploader
	$('#upload_banner_image_button').click(function (e) {
		e.preventDefault();
		// If the uploader object has already been created, reopen the dialog
		if (bannerMediaUploader) {
			bannerMediaUploader.open();
			return;
		}
		// Extend the wp.media object
		bannerMediaUploader = wp.media.frames.banner_frame = wp.media({
			title: 'Choose Banner Image',
			button: {
				text: 'Choose Image'
			},
			multiple: false
		});

		// When a file is selected, grab the URL and set it as the text field's value
		bannerMediaUploader.on('select', function () {
			var attachment = bannerMediaUploader.state().get('selection').first().toJSON();
			$('#banner_image').val(attachment.url);
			// Show preview
			$('#banner_image_preview').attr('src', attachment.url).show();
		});

		// Open the uploader dialog
		bannerMediaUploader.open();
	});

	// Event Logo Uploader
	$('#upload_event_logo_button').click(function (e) {
		e.preventDefault();
		// If the uploader object has already been created, reopen the dialog
		if (logoMediaUploader) {
			logoMediaUploader.open();
			return;
		}
		// Extend the wp.media object
		logoMediaUploader = wp.media.frames.logo_frame = wp.media({
			title: 'Choose Event Logo',
			button: {
				text: 'Choose Logo'
			},
			multiple: false
		});

		// When a file is selected, grab the URL and set it as the text field's value
		logoMediaUploader.on('select', function () {
			var attachment = logoMediaUploader.state().get('selection').first().toJSON();
			$('#event_logo').val(attachment.url);
			// Show preview
			$('#event_logo_preview').attr('src', attachment.url).show();
		});

		// Open the uploader dialog
		logoMediaUploader.open();
	});

	// Bulk actions: Select All checkbox
	$('#wprr-select-all').on('click', function () {
		var isChecked = $(this).prop('checked');
		$('input[name="result_ids[]"]').prop('checked', isChecked);
	});
});
