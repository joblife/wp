(function($) {
	var show_ratings = function() {
		$('.entry-title, .bb-course-title, .course-entry-title, .ld-course-list-items .ld-item-name').each(function(ind, el) {
			var target_el = $(el);
			var id, ratings, rating_value;
			for (var key in rating_details) {
				if (rating_details[key].title != target_el.text()) {
					continue;
				}
				id = key;
				ratings = rating_details[key];
				if (rating_settings.showTotalReviews) {
					target_el.append(
						'<div class="ratings-after-title">' +
							'<input id="input-' + id + '-rrf" class="rating rating-loading" value="' + ratings.average_rating + '">' +
							'<span>(' + ratings.total_count + ')</span>' +
						'</div>'
					);
				} else {
					rating_value = ratings.average_rating;
					if (rating_settings.allowReviewSubmission && ratings.can_submit_rating) {
						rating_value = ratings.user_rating;
					}
					target_el.append(
						'<div class="ratings-after-title">' +
							'<input id="input-' + id + '-rrf" class="rating rating-loading" value="' + rating_value + '">' +
						'</div>'
					);
					target_el.append('<span class="rrf-helper-text ' + ratings.class + '" data-course_id="' + id + '" data-alt="' + ratings.alt_text + '">' + ratings.review_text + '</span>');
				}
				if (rating_settings.hasSeparateReviewsSections) {
					$('#input-' + id + '-rrf').on('rating:rendered', function(){
						$('.rating-stars').addClass('is-clickable');
						$('.rating-stars.is-clickable').click(function(){
							if ($("#course-reviews-section").length) {
								$('html, body').animate({
							        scrollTop: $("#course-reviews-section").offset().top
							    }, 2000);
							}
						});
					});
				}
				$('#input-' + id + '-rrf').rating(rating_settings).trigger('rating:rendered');
			}
		});
	};
	$(document).ready(function() {
		$(window).on('load', function(){
			show_ratings();
		});
		$('body').on('mouseover mouseout', '.rrf-helper-text', function(){
			var alt_text = $(this).attr('data-alt');
			var text = $(this).text();
			$(this).attr('data-alt', text);
			$(this).text(alt_text);
		});
	});
})(jQuery);