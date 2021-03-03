jQuery(document).ready(function($) {

	$('#login').addClass('message-login-wrap');
	$('.eat-prallax-enabled').each(function () {
	    var $this = $(this);
	    var type = $this.attr('data-parallax-source');
	    var image = false;
	    var imageWidth = false;
	    var imageHeight = false;
	    var video = false;
	    var videoStartTime = false;
	    var videoEndTime = false;
	    var parallax = $this.attr('data-parallax-type');
	    var parallaxSpeed = $this.attr('data-parallax-speed');
	    var parallaxMobile = $this.attr('data-parallax-mobile') !== 'false';

	    // image type
	    if (type === 'image') {
	        image = $this.attr('data-parallax-image');
	        imageWidth = $this.attr('data-parallax-image-width');
	        imageHeight = $this.attr('data-parallax-image-height');
	    }

	    // video type
	    if (type === 'video') {
	        video = $this.attr('data-parallax-video');
	        videoStartTime = $this.attr('data-parallax-video-start-time');
	        videoEndTime = $this.attr('data-parallax-video-end-time');
	    }

	    // prevent if no parallax and no video
	    if (!parallax && !video) {
	        return;
	    }

	    var jarallaxParams = {
	        type: parallax,
	        imgSrc: image,
	        imgWidth: imageWidth,
	        imgHeight: imageHeight,
	        speed: parallaxSpeed,
	        noAndroid: !parallaxMobile,
	        noIos: !parallaxMobile
	    };

	    if (video) {
	        jarallaxParams.speed = parallax ? parallaxSpeed : 1;
	        jarallaxParams.videoSrc = video;
	        jarallaxParams.videoStartTime = videoStartTime;
	        jarallaxParams.videoEndTime = videoEndTime;
	    }

	    $this.jarallax(jarallaxParams);
	});

	
	if(eat_custom_login_plugin_settings.login_template == 'template-6'){
		$('#loginform,#registerform,#lostpasswordform').find('label').each(function( indexInArray , value){
			$(this).find('br').remove();
			if ( $(this).parent().is( "p" ) ) {
				$(this).unwrap();
			}

			if($(this).attr('for') == 'user_login'){
				/*Adding eat custom class*/
				$(value).contents().eq(0).wrap('<span class="eat-login-label"/>');
				$(this).addClass('eat-login-field');

				var login_label = $(this).text();
				$('.eat-login-label').remove();
				$(this).find('#user_login').attr('placeholder',login_label);

				var login_icon = '<i class="fa fa-user"></i>';
				$(this).prepend(login_icon);
			}

			if($(this).attr('for') == 'user_pass'){
				$(value).contents().eq(0).wrap('<span class="eat-login-label"/>');
				$(this).addClass('eat-password-field');

				var password_label = $(this).text();
				$('.eat-login-label').remove();
				$(this).find('#user_pass').attr('placeholder',password_label);
				var password_icon = '<i class="fa fa-lock"></i>';
				$(this).prepend(password_icon);
			}

			/*Remove Remember me*/
			// if($(this).attr('for') == 'rememberme'){
			// $(this).remove();
			// }
		});
	}
	if(eat_custom_login_plugin_settings.login_template == 'template-1'){
		$("#rememberme").detach().insertBefore(".forgetmenot>label");
	}

	if(eat_custom_login_plugin_settings.login_template == 'template-2'){
		$('#loginform,#registerform,#lostpasswordform').find('label').each(function( indexInArray , value){
			$(this).find('br').remove();
			if ( $(this).parent().is( "p" ) ) {
				$(this).unwrap();
			}

			if($(this).attr('for') == 'user_login'){
				/*Adding eat custom class*/
				$(value).contents().eq(0).wrap('<span class="eat-login-label"/>');
				$(this).addClass('eat-login-field');

				var login_label = $(this).text();
				$('.eat-login-label').remove();
				$(this).find('#user_login').attr('placeholder',$.trim(login_label));
			}

			if($(this).attr('for') == 'user_pass'){
				$(value).contents().eq(0).wrap('<span class="eat-login-label"/>');
				$(this).addClass('eat-password-field');

				var password_label = $(this).text();
				$('.eat-login-label').remove();
				$(this).find('#user_pass').attr('placeholder',$.trim(password_label));
			}

			if($(this).attr('for') == 'user_email'){
				$(value).contents().eq(0).wrap('<span class="eat-login-label"/>');
				$(this).addClass('eat-user-email');

				var email_label = $(this).text();
				$('.eat-login-label').remove();
				$(this).find('#user_email').attr('placeholder',$.trim(email_label));
			}

			// if($(this).attr('for') == 'user_email'){
			// 	$(value).contents().eq(0).wrap('<span class="eat-login-label"/>');
			// 	$(this).addClass('eat-password-field');

			// 	var password_label = $(this).text();
			// 	$('.eat-login-label').remove();
			// 	$(this).find('#user_email').attr('placeholder',"Email");
			// }

			/*Remove Remember me*/
			// if($(this).attr('for') == 'rememberme'){
			// $(this).remove();
			// }
		});
		// $("#nav>a:last-child").detach().insertBefore(".submit").wrap('<div class="lst-pswd">');
		// $(".lst-pswd").prev('label').wrap('<div class="remem-ext-cls"/>');
		$(".eat-password-field").next('label').wrap('<div class="remem-field">');
		$("#rememberme").prependTo(".remem-field");
		$('#nav').html($('#nav').html().split("|").join(""));
	}

	if(eat_custom_login_plugin_settings.login_template == 'template-3'){
		// $("#nav>a:last-child").detach().insertBefore(".submit").wrap('<div class="lst-pswd">');
		$("#rememberme").detach().insertBefore(".forgetmenot>label");
		$('#nav').html($('#nav').html().split("|").join(""));
	}

	if(eat_custom_login_plugin_settings.login_template == 'template-4'){
		 $("<div class='eat-login-header-text'>Please login to your account</div>").insertBefore("#loginform");
		 $('#loginform,#registerform,#lostpasswordform').find('label').each(function( indexInArray , value){
			$(this).find('br').remove();
			if ( $(this).parent().is( "p" ) ) {
				$(this).unwrap();
			}

			if($(this).attr('for') == 'user_login'){
				/*Adding eat custom class*/
				$(value).contents().eq(0).wrap('<span class="eat-login-label"/>');
				$(this).addClass('eat-login-field');

				var login_label = $(this).text();
				$('.eat-login-label').remove();
				$(this).find('#user_login').attr('placeholder',$.trim(login_label));

				var login_icon = '<i class="fa fa-user"></i>';
				$(this).prepend(login_icon);
			}

			if($(this).attr('for') == 'user_pass'){
				$(value).contents().eq(0).wrap('<span class="eat-login-label"/>');
				$(this).addClass('eat-password-field');

				var password_label = $(this).text();
				$('.eat-login-label').remove();
				$(this).find('#user_pass').attr('placeholder',$.trim(password_label));
				var password_icon = '<i class="fa fa-lock"></i>';
				$(this).prepend(password_icon);
			}

			if($(this).attr('for') == 'user_email'){
				/*Adding eat custom class*/
				$(value).contents().eq(0).wrap('<span class="eat-login-label"/>');
				$(this).addClass('eat-login-field');

				var login_label = $(this).text();
				$('.eat-login-label').remove();
				$(this).find('#user_email').attr('placeholder',$.trim(login_label));

				var login_icon = '<i class="fa fa-user"></i>';
				$(this).prepend(email_icon);
			}

			/*Remove Remember me*/
			// if($(this).attr('for') == 'rememberme'){
			// $(this).remove();
			// }
		});
		 $(".eat-password-field").next('label').wrap('<div class="remem-field">');
		 $("#rememberme").prependTo(".remem-field");
		$('#nav').html($('#nav').html().split("|").join(""));
		 // $("#nav>a:last-child").detach().insertBefore(".submit").wrap('<div class="lst-pswd">');
	}
	if(eat_custom_login_plugin_settings.login_template == 'template-5'){
		$("#rememberme").prependTo(".forgetmenot");
		// $("#nav>a:last-child").detach().insertBefore(".submit").wrap('<div class="lst-pswd">');
 		$('#nav').html($('#nav').html().split("|").join(""));
	}
});