var timer;
var reject_popup;
var link_id;
jQuery(document).ready( function() {
	var prev = {
		pointerEvents: 'all',
		background: document.body.style.background,
		opacity: 1,
		height: 'auto',
	};
	// Refresh rating after course update.
	jQuery('button.button.button-primary.save').on('click',function(){
		timer = setInterval(function(){
			jQuery('.rating-loading').rating('refresh');
			clearInterval(timer);
		}, 1000);
	});
	// Show full review description on excerpt link click.
	jQuery('a.wdm_review_excerpt_popup').on('click', function(){
		var excerpt = jQuery(this).attr('data-wdm-rrf-review');
		jQuery('<p>' + excerpt + '</p>').modal();
	});

	jQuery('.wdm_rrf_approve a').click(function(e) {
		e.preventDefault();
		link_id = jQuery(this).attr('id');
		updatePost(link_id, 'approve', '');
	});

	jQuery('.wdm_rrf_reject a').on(jQuery.modal.BEFORE_OPEN, function(){
	});

	jQuery('.wdm_rrf_reject a').on('click', function(evnt){
		evnt.preventDefault();
		link_id = jQuery(this).attr('id');
		reason = jQuery(this).data('rejected');
		jQuery("textarea#wdm_review_reject_reason").text(reason);
		var content = jQuery('#wdm_rrf_reject_popup').html();
		jQuery('<div>' + content + '</div>').modal();
		reject_popup = jQuery.modal.getCurrent();
	});

	jQuery('body').on('click', '#wdm_review_reject_submission', function() {
		form = jQuery(this).parent();
		var user_feedback = form.find('#wdm_review_reject_reason').val();
		reject_popup.close();
		updatePost(link_id, 'reject', user_feedback);
	});

	function updatePost(link_id, link_action, message = '') {
		var ajx = jQuery.ajax({
			url: wdm_approve_ajax.ajax_url,
			type: 'POST',
			data: {
				'action' : wdm_approve_ajax.action,
				'review_id': link_id,
				'link_action': link_action,
				'message': message,
				'security' : wdm_approve_ajax.nonce,
			},
			beforeSend: function() {
				jQuery('body').css({
					pointerEvents: 'none',
			 // backgroundImage: "url(https://upload.wikimedia.org/wikipedia/commons/b/b1/Loading_icon.gif)",
			 backgroundImage: "url("+wdm_approve_ajax.loader_url+")",
			 backgroundPosition: "center",
			 backgroundRepeat: "no-repeat",
			 opacity: '0.5',
			});
			}
		}).done(function(data){
			var result = JSON.parse(data);
			if (result.validation_pass) {
				var link = jQuery("#"+link_id);
				//remove the pending status
				var title_column = link.parents('.row-actions').siblings('strong');
				var status = title_column.children('a.row-title');
				title_column.empty().append(status);
				if (link_action == 'approve') {
					//modify the date column
					var date_column = link.parents('.title').siblings('.date');
					var date = date_column.children('abbr');
					date_column.empty().append("Published<br>");
					date_column.append(date);
					//change the class
					var row = link.parents('.type-wdm_course_review');
					var list = row.attr("class").split(' ');
					for (var i = 0; i < list.length; i++) {
						if (list[i].indexOf("status") !=-1) {
							console.log(list[i]);
							row.removeClass(list[i]);
						}
					}
					row.addClass('status-publish');
					//toggle the links
					link.parent().addClass('wdm_hide_link');
					var toggle_link = link.parent().siblings('.wdm_rrf_reject');
					toggle_link.removeClass('wdm_hide_link');
				} else if (link_action == 'reject') {
					//modify the date column
					var date_column = link.parents('.title').siblings('.date');
					var date = date_column.children('abbr');
					date_column.empty().append("Last Modified<br>");
					date_column.append(date);
					//change the class
					var row = link.parents('.type-wdm_course_review');
					var list = row.attr("class").split(' ');
					for (var i = 0; i < list.length; i++) {
						if (list[i].indexOf("status") !=-1) {
							row.removeClass(list[i]);
							break;
						}
					}
					row.addClass('status-rejected');
					//toggle the links
					link.parent().addClass('wdm_hide_link');
					var toggle_link = link.parent().siblings('.wdm_rrf_approve');
					toggle_link.removeClass('wdm_hide_link');
					//update the reject reason
					link.data('rejected', message);
				}
			}
			jQuery('body').css(prev);
		});
	}
});

