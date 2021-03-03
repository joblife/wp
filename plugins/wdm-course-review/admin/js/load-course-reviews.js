jQuery(document).ready(function(){
if (document.getElementsByClassName('wdm_course_student_review').length > 0) {
	var prev = {
		 pointerEvents: 'all',
		 background: document.getElementsByClassName('wdm_course_student_review')[0].style.background,
		 opacity: 1,
		 height: 'auto',
	 };
	 var ajx = jQuery.ajax({
			 url: wdm_cr_ajax.ajax_url,
			 type: 'POST',
			 data: {
			 'action' : wdm_cr_ajax.action,
             'security':wdm_cr_ajax.nonce,
             'course_id':wdm_cr_ajax.course_id
		 },
		 beforeSend: function() {
			 jQuery('.wdm_course_student_review').css({
			 pointerEvents: 'none',
			 backgroundImage: "url("+wdm_cr_ajax.loader_url+")",
			 backgroundRepeat: "no-repeat",
			 backgroundPosition: "center",
			 opacity: '0.75',
			 height: '100px',
			 });
			 }
		 }).done(function(data){
			 jQuery('.wdm_course_student_review').css(prev);
	 });

}



});