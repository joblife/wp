(function($) {
	$(document).ready(function(){
		// Globals for persisting values across the various modals and maintaining a history of the user progression.
		var stack = []; // [globals] remember to reset on modal close button click.
		var stars = 0; // [globals] remember to reset on modal close button click.
		var title = ''; // [globals] remember to reset on modal close button click.
		var body = ''; // [globals] remember to reset on modal close button click.
		var settings = review_details.settings;
		// Launch the modals on Edit Review or Leave a review link click.
		$('body').on('click', '.rrf-helper-text, .write-a-review', function(e) {
			e.preventDefault();
			e.stopPropagation();
			var step_type, security, course_id, step_no = 1;
			if ($(this).hasClass('not-allowed')) {
				return false;
			}
			course_id = $(this).attr('data-course_id');
			$(this).blur();
			if ($(this).hasClass('not-rated')) {
				step_type = 'add';
				security = review_details.add_review_nonce;
			} else if ($(this).hasClass('already-rated')) {
				step_type = 'edit';
				security = review_details.edit_review_nonce;
			}
			stack.push({'type': step_type, 'num': step_no});
			$.post(
				review_details.url,
				{
					action: 'launch_modal',
					step_type: step_type,
					security: security,
					step_no: step_no,
					course_id: course_id
				},
				function(response) {
					$(response).appendTo('body').modal(settings);
				}
			);
		});
		// Switching between modals for review submission process.(only for the next logical modal not in the backward direction).
		$('body').on('click', '.rrf-modal-content .next', function(){
			var step_type, security, step_no, referer, current_step, course_id, modal;
			var self = $(this);
			modal = $(this).parents('.rrf-modal-content');
			course_id = parseInt(modal.attr('data-course_id'));
			step_type = $(this).attr('data-steptype');
			current_step = parseInt(modal.attr('data-step'));
			if (modal.hasClass('review-details')) {
				if (modal.find('.review-title.review-headline input[type=text]').val() == '' || modal.find('.review-description.review-details textarea').val() == '') {
					return false;
				}
				title = modal.find('.review-title.review-headline input[type=text]').val();
				body = modal.find('.review-description.review-details textarea').val();
			}
			if (step_type == 'add') {
				security = review_details.add_review_nonce;
			} else if (step_type == 'edit') {
				security = review_details.edit_review_nonce;
			} else if (step_type == 'delete') {
				current_step = 0;
				security = review_details.delete_review_nonce;
			}
			step_no = current_step + 1;
			stack.push({'type': step_type, 'num': step_no});
			$.post(
				review_details.url,
				{
					action: 'launch_modal',
					step_type: step_type,
					security: security,
					step_no: step_no,
					stars: stars,
					course_id: course_id,
					title: title,
					body: body
				},
				function(response) {
					$(response).appendTo('body').modal(settings);
					if (self.hasClass('mid-submit-step')) {
						$.post(
							review_details.url,
							{
								action: 'submit_review',
								security: review_details.submit_review_nonce,
								stars: stars,
								course_id: course_id,
								title: title,
								body: body
							},
							function(status) {
								window.status2 = status
								if (!status.success) {
									modal.find('.modal-container').prepend('<span class="error-message">' + status.data + '</span>');
									return;
								}
								// $('[data-id=input-' + course_id + '-rrf]').rating('update', stars);
								// $('[data-id=input-' + course_id + '-rrf]').parents('.ratings-after-title').next().removeClass('not-rated').addClass('already-rated').attr('data-alt', review_details.alt_text).text(review_details.review_text);
								// while ($.modal.isActive()) {
								// 	$.modal.close();
								// 	// target_el.append('<span class="rrf-helper-text ' + ratings.class + '" data-course_id="' + id + '" data-alt="' + ratings.alt_text + '">' + ratings.review_text + '</span>');
								// }
							}
						);
					}
				}
			);
		});
		// Go to the previous modal window.
		$('body').on('click', '.rrf-modal-content .previous', function(){
			$.modal.close();
		});
		// Close all modals on close button click.
		$('body').on('click', '.rrf-close-all', function(e){
			e.preventDefault();
			e.stopPropagation();
			stack = [];
			stars = 0;
			title = '';
			body = '';
			while ($.modal.isActive()) {
				$.modal.close();
			}
		});
		// Delete User's review.
		$('body').on('click', '.delete-confirm', function(){
			var security = review_details.delete_review_nonce;
			var course_id = $(this).parents('.rrf-modal-content').attr('data-course_id');
			$.post(
				review_details.url,
				{
					action: 'delete_review',
					security: security,
					course_id: course_id,
				},
				function(response) {
					console.log('deleted successfully');
					window.location.reload();
				}
			);
		});
		// Close delete modal
		$('body').on('click', '.delete-close', function(){
			while ($.modal.isActive()) {
				$.modal.close();
			}
		});


		// Submission of the review for insert/update process.
		$('body').on('click', '.rrf-review-submission', function(){
			window.location.reload();
		});
		// Initialize Ratings library on modal open and keep the next button disabled until a star rating is selected.
		$('body').on($.modal.OPEN, function(event, modal){
			var course_id = parseInt(modal.$elm.attr('data-course_id'));
			if (modal.$elm.find('.rating-settings').length > 0) {
				var rating_settings;
				rating_settings = JSON.parse(modal.$elm.find('.rating-settings').val());
				modal.$elm.find('[data-id=input-' + course_id + '-rrf]').rating(rating_settings);
			}
			if (modal.$elm.find('[data-id=input-' + course_id + '-rrf]').length > 0) {
				stars = modal.$elm.find('[data-id=input-' + course_id + '-rrf]').val();
			}
			modal.$elm.find('[data-id=input-' + course_id + '-rrf]').on('rating:change', function(event, value, caption) {
				modal.$elm.find('.next').removeAttr('disabled');
				stars = value;
				if (modal.$elm.hasClass('star-submission')) {
					modal.$elm.find('.next').trigger('click');
				}
			});
			if (jQuery('.review-description > textarea').length > 0) {
				var remaining = review_details.maxlength - jQuery('.review-description > textarea').val().length;
				if (remaining <= 0) {
			    	remaining = 0;
			    }
				jQuery('.review-description > textarea').siblings('.wdm_rrf_remaining_characters').find('.wdm_cff_remaining_count').html(remaining);
			}
		});
		jQuery('body').on('keyup change', '.review-description > textarea',function(){
		    var remaining = review_details.maxlength - jQuery(this).val().length;
		    if (remaining <= 0) {
		    	remaining = 0;
		    }
			jQuery(this).siblings('.wdm_rrf_remaining_characters').find('.wdm_cff_remaining_count').html(remaining);
		});
	});
})(jQuery);

