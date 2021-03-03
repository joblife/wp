(function($) {
	function submit_feedback_form(form){
		var user_feedback = form.elements.namedItem("wdm_course_feedback_text").value.trim();
		var textareaObj = jQuery(form.elements.namedItem("wdm_course_feedback_text"));
		textareaObj.next('.wdm_crr_error_msg').remove();
		if (user_feedback.length === 0) {
			form.elements.namedItem("wdm_course_feedback_text").style.borderColor = "red";
			form.elements.namedItem("wdm_course_feedback_text").focus();
			return false;
		} else if(textareaObj.val().trim().length > feedback_ajax_data.maxlength){
			form.elements.namedItem("wdm_course_feedback_text").style.borderColor = "red";
		 	textareaObj.after('<span class="wdm_crr_error_msg">'+feedback_ajax_data.error_msg+'</span>');
			return false;
		}
		var submitBtn = jQuery(form.elements.namedItem("wdm_feedback_sub_btn"));
		var width = 0;
		var height = 0;
		var formObj = jQuery(form);
		var btnText = submitBtn.html();
		var ajx = $.ajax({
			url: feedback_ajax_data.url,
			type: 'POST',
			timeout:60000, //3000=3 60000=60 seconds timeout
			data: {
				action: feedback_ajax_data.action,
				security: feedback_ajax_data.nonce,
				course_id: feedback_ajax_data.course_id,
				user_feedback: user_feedback
			},
			beforeSend: function() {
				submitBtn.prop('disabled', true);
				submitBtn.prop('cursor', 'wait');
				submitBtn.html(feedback_ajax_data.wait_message);
				width = formObj.context.offsetWidth;
				height = formObj.context.offsetHeight;
			}
		}).done(function(data){
			 	submitBtn.html(btnText);
				submitBtn.prop('disabled', false);
				var result = JSON.parse(data);
				if (result.hasOwnProperty("success")) {
					switch(result.status){
						case 'empty_feedback':
						form.elements.namedItem("wdm_course_feedback_text").style.borderColor = "red";
						form.elements.namedItem("wdm_course_feedback_text").focus();
							break;
						default :
					 	// var msgStructure = '<div style="width:'+width+'px;height:'+height+'px;padding-top: 10%;padding-bottom: 10%;text-align: center;">'+result.message+'</div>';
					 	var msgStructure = '<div style="text-align: center;">'+result.message+'</div>';
					 	feedback_ajax_data.rrf_modal_settings.closeClass += ' feedback-close-success';
					 	$(msgStructure).modal(feedback_ajax_data.rrf_modal_settings);
					 	// formObj.removeChild('div');
					 	// formObj.context.innerHTML = msgStructure;
					 	
				 		break;
					}
				}
		}).fail(function(jqXHR, textStatus){
		    if(textStatus === 'timeout')
		    {
		    	var msgStructure = '<div style="text-align: center;"><span>'+feedback_ajax_data.ajax_time+'</span></div>';
		    	feedback_ajax_data.rrf_modal_settings.closeClass += ' feedback-close-fail';
			 	$(msgStructure).modal(feedback_ajax_data.rrf_modal_settings);
		        //do something. Try again perhaps?
		    }
		});
	}
	$(document).ready(function(){
		var current_modal;
		$('body').on('submit', '.wdm_feedback_form', function(evnt){
			evnt.preventDefault();
			submit_feedback_form(this);
		});
		$('body').on('click', 'button.wdm_feedback_form_pop', function(){
			var content = $('#wdm_feedback_form_pop_content').html();
			$(content).modal(feedback_ajax_data.rrf_modal_settings);
			current_modal = $.modal.getCurrent();

		});
		$('body').on('click', '.feedback_review_link', function(){
			if ($.modal.isActive()) {
				current_modal.close();
			}
		});
	  	jQuery('body').on('keyup change', '.wdm_course_feedback_textarea',function(){
		    var remaining = feedback_ajax_data.maxlength - jQuery(this).val().length;
		    if (remaining <= 0) {
		    	remaining = 0;
		    }
			jQuery(this).siblings('.wdm_rrf_remaining_characters').find('.wdm_cff_remaining_count').html(remaining);
		});
		$('body').on('click', '.feedback-close-success', function(){
			$('button.wdm_feedback_form_pop').hide();
		});
	});
})(jQuery);