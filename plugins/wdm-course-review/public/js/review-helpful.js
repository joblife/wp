function ajax_send_helpful_count(button_obj, review_id, answer) {
	var ajx = jQuery.ajax({
		url: helpful_object.url,
		type: 'POST',
		data: {
			'action' : helpful_object.action,
			'answer': answer,
			'review_id': review_id,
			'security' : helpful_object.nonce,
		},
	}).done(function(data){
		var result = JSON.parse(data);
		var display_message = button_obj.parents('.review-meta-wrap').find('.review-helpful-count');
		if (result.success == true && result.message != '') {
			button_obj.toggleClass('hide');
			button_obj.siblings('.review-helpful-icon-wrap').toggleClass('hide');
			if (result.display_msg.length == 0) {
				display_message.addClass('is-not-voted');
			} else {
				display_message.removeClass('is-not-voted');
			}
			display_message.text(result.display_msg).show();
		} else {
			window.location.href= result.redirecturl;
		}
	});
}
jQuery(document).ready(function(){
	var current_page_no, max_page_no;
	current_page_no = parseInt(jQuery('.current_page_no').val());
	max_page_no = parseInt(jQuery('.max_page_no').val());
	// Ajax call for review was helpful
	jQuery('body').on( 'click', '.wdm_helpful_yes', function(e) {
		e.preventDefault();
		var button_obj = jQuery(this);
		var review_id = button_obj.attr('data-review_id');
		ajax_send_helpful_count(button_obj, review_id, 'yes');
	});
	jQuery('body').on( 'click', '.wdm_helpful_no' ,function(e) {
		e.preventDefault();
		var button_obj = jQuery(this);
		var review_id = button_obj.attr('data-review_id');
		ajax_send_helpful_count(button_obj, review_id, 'no');
	});
	//  Comment display toggle.
	jQuery("body").on("click",".comment-toggle-alt",function(e){
		e.preventDefault();
		jQuery(this).closest(".wdm-review-replies").children(".review-comment-list:not(:first-of-type)").toggleClass('hide');
		jQuery(this).toggleClass('hide');
		jQuery(this).siblings("a").toggleClass('hide');
	});
	// Initial comment display hide.
	jQuery('.wdm-review-replies').each(function(){
		jQuery(this).children('.review-comment-list:not(:first-child)').addClass('hide');
	});
	// Reviews sorting logic.
	jQuery('.sort_results').on('change', function(e){
		var self = jQuery(this);
		var orderby = self.val();
		var filterby = jQuery(this).parents('.filter-options').find('.filter_results').val();
		jQuery('.reviews-listing-wrap').hide();
		jQuery('#course-reviews-section .loader').removeClass('hide');
		jQuery('#reviews-listing-wrap').load(
			reviews_filter_query.current_url + ' #reviews-listing-wrap', {
			'orderby' : orderby,
			'filterby' : filterby
		}, function(data){
			jQuery('.reviews-listing-wrap').show();
			jQuery('#course-reviews-section .loader').addClass('hide');
			// jQuery('.rating-loading').rating('refresh');
			jQuery('.wdm-review-replies').each(function(){
				jQuery(this).children('.review-comment-list:not(:first-child)').addClass('hide');
			});
			max_page_no = parseInt(jQuery('.max_page_no').val());
			if (current_page_no >= max_page_no) {
				jQuery('.load_more_reviews').hide();
			}
		});
	});
	// Reviews filtering logic.
	jQuery('.filter_results').on('change', function(e){
		var self = jQuery(this);
		var filterby = self.val();
		var orderby = jQuery(this).parents('.filter-options').find('.sort_results').val();
		jQuery('.reviews-listing-wrap').hide();
		jQuery('#course-reviews-section .loader').removeClass('hide');
		jQuery('#reviews-listing-wrap').load(
			reviews_filter_query.current_url + ' #reviews-listing-wrap', {
			'filterby' : filterby,
			'orderby' : orderby
		}, function(data){
			jQuery('.reviews-listing-wrap').show();
			jQuery('#course-reviews-section .loader').addClass('hide');
			// jQuery('.rating-loading').rating('refresh');
			jQuery('.wdm-review-replies').each(function(){
				jQuery(this).children('.review-comment-list:not(:first-child)').addClass('hide');
			});
			max_page_no = parseInt(jQuery('.max_page_no').val());
			if (current_page_no >= max_page_no) {
				jQuery('.load_more_reviews').hide();
			}
		});
	});
	// Reviews Pagination logic.
	if (current_page_no >= max_page_no) {
		jQuery('.load_more_reviews').hide();
	}
	jQuery('body').on('click', '.load_more_reviews', function(evnt){
		evnt.preventDefault();
		var self = jQuery(this);
		current_page_no = current_page_no + 1;
		var course_id = jQuery('.current_page_no').attr('data-course_id');
		var sortby = jQuery(this).parents('.course-reviews-section').find('.sort_results').val();
		var filterby = jQuery(this).parents('.course-reviews-section').find('.filter_results').val();
		jQuery.ajax({
			url: reviews_paginate_query.url,
			type: 'POST',
			data: {
				'action' 	: reviews_paginate_query.action,
				'course_id'	: course_id,
				'page'		: current_page_no,
				'sortby' 	: sortby,
				'filterby' 	: filterby,
				'security' 	: reviews_paginate_query.nonce,
			},
		}).done(function(response){
			var results = response;
			if (response.success) {
				jQuery('.review_listing').append(jQuery(response.data.html).find('.review-comments-wrap'));
				if (current_page_no >= max_page_no) {
					jQuery('.load_more_reviews').hide();
				}
				// jQuery('.rating-loading').rating('refresh');
				jQuery('.wdm-review-replies').each(function(){
					jQuery(this).children('.review-comment-list:not(:first-child)').addClass('hide');
				});
			}
		});
	});
});