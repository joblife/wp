jQuery('#wp-admin-bar-my-account').hide();
jQuery(document).ready(function($) {
    jQuery('#wp-admin-bar-my-account').show();
    var $container = $('.eat-admin-main-wrapper');

    // http://stackoverflow.com/questions/9374894/can-codemirror-find-textareas-by-class
    function codeMirrorDisplay() {
        var $codeMirrorEditors = $('.eat-textarea-code-texts');
        $codeMirrorEditors.each(function(i, el) {
            var $active_element = $(el);
            if ($active_element.data('cm')) {
                $active_element.data('cm').doc.cm.toTextArea();
            }
            var codeMirrorEditor = CodeMirror.fromTextArea(el, {
                lineNumbers: true,
                lineWrapping: true,
                theme: 'eclipse'
            });
            $active_element.data('cm', codeMirrorEditor);
        });
    }

    $container.on('click', '.eat-tab', function(){
        var $this = $(this),
        id        = $this.attr('id');
        $('.eat-tab').removeClass('eat-active');
        $this.addClass('eat-active');
        $('.eat-tab-content').removeClass('eat-tab-content-active').hide();
        $('.'+id).addClass('eat-tab-content-active').show();
        codeMirrorDisplay();
    });

    $container.on('change', '.eat-general-template-selection', function(){
        var $this   = $(this);
        var selected_img = $(this).find('option:selected').data('img');
        $this.closest('.eat-options-wrap').find('.eat-img-selector-media img').attr('src', selected_img);
    });

    $container.on('change', '.eat-background-selector', function(){
        var $this  = $(this);
        var val    = $this.val();
        var parent = $this.closest('.eat-options-wrap-outer').find('.eat-background-select-content');
        if( val =='' ){
            parent.find('.eat-common-content-wrap').hide();
            parent.find('.eat-common-content-wrap-all').hide();
        }else if( val == 'background-color' ){
            parent.find('.eat-common-content-wrap').hide();
            parent.find('.eat-common-content-wrap-all').hide();
            parent.find('.eat-'+val).show();
        }else{
            parent.find('.eat-common-content-wrap').hide();
            parent.find('.eat-common-content-wrap-all').show();
            parent.find('.eat-'+val).show();
        }
    });

    $container.on( 'change', '.eat-select-options', function(){
        var $this  = $(this);
        var val    = $this.val();
        var parent = $this.closest('.eat-select-options-wrap').find('.eat-select-content-wrap');
        if( val =='' ){
            parent.find('.eat-common-content-wrap').hide();
        }else{
            parent.find('.eat-common-content-wrap').hide();
            parent.find('.eat-'+val).show();
            parent.find('.eat-background').show();
        }
    });

    $container.on( 'change', '.eat-video-select-option', function(){
        var $this = $(this);
        var val   = $this.val();
        $this.closest('.eat-video-options-wrap').find('.eat-common-content-wrap-inner').hide();
        $this.closest('.eat-video-options-wrap').find('.eat-'+val).show();
    });

    $container.on( 'click', '.eat-image-overlay-enable-option', function(){
        var $this = $(this);
        if ($this.is(':checked')) {
            $this.closest('.eat-checkbox-outer-wrap').find(".eat-checkbox-checked-options").fadeIn();
        }else{
            $this.closest('.eat-checkbox-outer-wrap').find(".eat-checkbox-checked-options").fadeOut();
        }
    });

    $container.find( '.eat-color-picker').wpColorPicker();

    $container.on( 'click', '.eat-image-upload-button', function (e) {
        e.preventDefault();
        var $this = $(this);
        var image = wp.media({
            title: 'Upload Image',
            multiple: false
        }).open()
                .on('select', function (e) {
                    var uploaded_image = image.state().get('selection').first();
                    var el_img_url     = uploaded_image.toJSON().url;
                    $this.closest('.eat-image-selection-wrap').find('.eat-image-upload-url').val(el_img_url);
                    $this.closest('.eat-image-selection-wrap').find('.eat-image-preview img').attr('src', el_img_url);
                });
    });

    $container.on('change', '.eat-wordpress-logo-dropdown', function(){
        var $this  = $(this);
        var val    = $this.val();
        var parent = $this.closest('.eat-wordpress-logo-wrap').find('.eat-wordpress-logo-dropdown-content-wrap');
        if( val =='hide' ){
            parent.find('.eat-common-content-wrap').hide();
            parent.find('.eat-common-content-wrap-all').hide();
        }else if( val == 'font-icon'){
            parent.find('.eat-common-content-wrap').hide();
            parent.find('.eat-common-content-wrap-all').show();
            parent.find('.eat-'+val).show();
        }else if( val == 'image' ){
            parent.find('.eat-common-content-wrap').hide();
            parent.find('.eat-common-content-wrap-all').show();
            parent.find('.eat-'+val).show();
        }else{
            parent.find('.eat-common-content-wrap').hide();
            parent.find('.eat-common-content-wrap-all').show();
        }
    });

    $container.on('click', '.eat-footer-info-custom-texts', function(){
        if ($(this).is(':checked')) {
            $(this).closest('.eat-custom-texts-options-wrap').find(".eat-custom-texts-content-wrap").fadeIn();
        }else{
            $(this).closest('.eat-custom-texts-options-wrap').find(".eat-custom-texts-content-wrap").fadeOut();
        }
    });

    $('.eat-icon-picker').iconPicker();

    $container.on( 'change', '.eat-options-select-wrap', function(){
        var $this  = $(this);
        var parent = $this.closest('.eat-select-wrap').find('.eat-options-select-content-wrap');
        var val    = $this.val();
        parent.find('.eat-common-content-wrap').removeClass('eat-active').hide();
        parent.find('.eat-'+val+'-content-wrap').addClass('eat-active').show();
    });

    $('.eat-tabs-header').theiaStickySidebar({
      // Settings
      additionalMarginTop: 30
    });

    //for dropdown selection
    $('.eat-selectbox-wrap').selectbox();

    $( "<div class='eat-background-image'></div>" ).insertBefore( '#adminmenumain' );


    ////////// menu manager JS //////////////////////
    $( '.eat-menu-header-left').click(function(){
        $(this).closest('.eat-menu-manager-item-wrap').toggleClass('eat-expand');
        $(this).closest('.eat-menu-manager-item-wrap').find('.eat-menu-manager-item-edits').toggle();
        $(this).closest('.eat-menu-manager-item-wrap').find('.eat-submenu-wrap').toggle();

    });

    $( '.eat-submenu-name').click(function(){
       $(this).closest('.eat-submenu-manager-item-wrap').toggleClass('eat-expand');
       $(this).closest('.eat-submenu-manager-item-wrap').find('.eat-submenu-manager-item-edits').toggle();
    });


    $( '.eat-submenu-wrap').sortable({
        containment: "parent",
    });

    $( '.eat-menu-wrap').sortable({
        containment: "parent",
    });

    /////////// menu manager ends //////////////

    // for template 15 and 16 //////
    if( eat_plugin_settings.dashboard_template == 'temp-16' || eat_plugin_settings.dashboard_template == 'temp-15'){
        $("#wpadminbar").detach().prependTo("#wpwrap");
        $('#adminmenumain,#wpcontent').wrapAll('<div class="eat-menu-wpcontent-wrap" />');
    }
    if( eat_plugin_settings.dashboard_template == 'temp-15' ){
        // $("#wpadminbar").detach().prependTo("#wpwrap");
        $("<div class='eat-user-profile-section'></div>").insertAfter("#wpadminbar");
        $("#wp-admin-bar-user-actions").detach().prependTo(".eat-user-profile-section");
        $('.eat-user-profile-section').jarallax({
            type: 'scale',
            speed: 1.2
        });
        // $('#adminmenumain,#wpcontent').wrapAll('<div class="eat-menu-wpcontent-wrap" />');
    }

    if( eat_plugin_settings.dashboard_template == 'temp-19' ){
        // $("#wpadminbar").detach().prependTo("#wpwrap");
        $("<div class='eat-user-profile-section'></div>").insertAfter("#wpadminbar");
        $("#wp-admin-bar-user-actions").detach().prependTo(".eat-user-profile-section");
        $('.eat-user-profile-section').jarallax({
            type: 'scale-opacity',
            speed: 1.2
        });
        // $('#adminmenumain,#wpcontent').wrapAll('<div class="eat-menu-wpcontent-wrap" />');
    }
   

    if( eat_plugin_settings.dashboard_template == 'temp-20' ){
            // $("#wpadminbar").detach().prependTo("#wpwrap");
            // $("<div class='eat-user-profile-section'></div>").insertAfter("#wpadminbar");
            $(".eat-admin-logo").detach().insertAfter("#wp-admin-bar-menu-toggle").wrap('<li>');
    }
    if( eat_plugin_settings.dashboard_template == 'temp-21' ){
            // $("#wpadminbar").detach().prependTo("#wpwrap");
            // $("<div class='eat-user-profile-section'></div>").insertAfter("#wpadminbar");
            $(".eat-admin-logo").detach().insertAfter("#wp-admin-bar-menu-toggle").wrap('<li>');
    }
    if( eat_plugin_settings.dashboard_template == 'temp-8' ){
            // $("#wpadminbar").detach().prependTo("#wpwrap");
            // $("<div class='eat-user-profile-section'></div>").insertAfter("#wpadminbar");
            $(".eat-admin-logo").detach().insertAfter("#wp-admin-bar-menu-toggle").wrap('<li>');
    }
    if( eat_plugin_settings.dashboard_template == 'temp-3' ){
        $('.eat-admin-logo').detach().insertAfter("#wp-admin-bar-menu-toggle").wrap("<li>");
    }
    if( eat_plugin_settings.dashboard_template == 'temp-5' ){
        $('.eat-admin-logo').detach().insertAfter("#wp-admin-bar-menu-toggle").wrap("<li>");
    }
    if( eat_plugin_settings.dashboard_template == 'temp-11' ){
            // $("#wpadminbar").detach().prependTo("#wpwrap");
            // $("<div class='eat-user-profile-section'></div>").insertAfter("#wpadminbar");
            $(".eat-admin-logo").detach().insertAfter("#wp-admin-bar-menu-toggle").wrap('<li>');
    }
    if( eat_plugin_settings.dashboard_template == 'temp-12' ){
            // $("#wpadminbar").detach().prependTo("#wpwrap");
            // $("<div class='eat-user-profile-section'></div>").insertAfter("#wpadminbar");
            $(".eat-admin-logo").detach().insertAfter("#wp-admin-bar-menu-toggle").wrap('<li>');
    }
    if( eat_plugin_settings.dashboard_template == 'temp-22' ){
        $("<div class='eat-user-profile-section'></div>").prependTo("#adminmenuwrap");
        $('#wp-admin-bar-my-account>a').addClass('eat-outer-menu-click');
        $('.eat-admin-logo').detach().insertAfter("#wp-admin-bar-menu-toggle").wrap("<li>");
        $("#wp-admin-bar-my-account").detach().appendTo(".eat-user-profile-section");
   
        if($('body').hasClass('auto-fold') == true){
            // alert('folded');
        }else{
            $('#wp-admin-bar-my-account>.ab-sub-wrapper').hide();
            $('#adminmenuwrap').on('click', '.menupop .eat-outer-menu-click', function(){
                $(this).closest('.eat-user-profile-section').toggleClass('eat-open-temp22');
                $(this).closest('.eat-user-profile-section').find('.ab-sub-wrapper').slideToggle(300);
                return false;
            });
        }
    }

    if( eat_plugin_settings.dashboard_template == 'temp-23' ){
        $("<div class='eat-user-profile-section'></div>").prependTo("#adminmenuwrap");
        $('.eat-admin-logo').detach().insertAfter('#adminmenuback');
        $('#wp-admin-bar-my-account>a').addClass('eat-outer-menu-click');
        // $('.eat-admin-logo').detach().insertAfter("#wp-admin-bar-menu-toggle").wrap("<li>");
        $("#wp-admin-bar-my-account").detach().appendTo(".eat-user-profile-section");
   
        // $('#wp-admin-bar-my-account>.ab-sub-wrapper').hide();
        // $('#adminmenuwrap').on('click', '.menupop .eat-outer-menu-click', function(){
        //     $(this).closest('.eat-user-profile-section').toggleClass('eat-open-temp22');
        //     $(this).closest('.eat-user-profile-section').find('.ab-sub-wrapper').slideToggle(300);
        //     return false;
        // });
        var ps = new PerfectScrollbar('#adminmenuwrap');
        if($('#adminmenuwrap').outerWidth() >= 100 ){
            // var ps = new PerfectScrollbar('#adminmenuwrap');
        }else{
            ps.destroy();
        }

        $('#adminmenumain').on('click', '#collapse-button', function(){
            var ps = new PerfectScrollbar('#adminmenuwrap');
            var outerwidth_val = $('#adminmenuwrap').outerWidth();
            if(outerwidth_val >='100'){
                // var ps = new PerfectScrollbar('#adminmenuwrap');
            }else{
                ps.destroy();
            }
        });


    }




    if( eat_plugin_settings.dashboard_template == 'temp-15' || eat_plugin_settings.dashboard_template == 'temp-16' || eat_plugin_settings.dashboard_template == 'temp-19'){
        $('#wpadminbar').jarallax({
            type: 'scale-opacity',
            speed: 1.2
        });
        // one_jarallax = new Jarallax();
        // one_jarallax.addAnimation('#wpadminbar',[{progress: "0%", top:"0%"}, {progress: "100%", top: "-10%"}]);
    }

    //if(eat_plugin_settings.dashboard_template == 'temp-1')


    $( '.eat-menu-hide-show').click(function(){
        // alert('test');
        $(this).closest('.eat-menu-manager-item-wrap').toggleClass('eat-disabled');
    });

    $( '.eat-submenu-hide-show').click(function(e){
        $(this).closest('.eat-submenu-manager-item-wrap').toggleClass('eat-disabled');
    });

    $( '.eat-ajax-menu-submenu-submit-button').click(function(){
        var new_admin_menu     = '';
        var new_admin_submenu  = '';
        var new_menu_rename    = '';
        var new_submenu_rename = '';
        var disabled_menu      = '';
        var disabled_submenu   = '';

        // for disabled menus
        $(".eat-menu-item").each(function(){
            var $this      = $(this);
            var id         = $this.data("id");
            var menuname   = $this.data("menu-name");
            new_admin_menu += menuname+"|";
            if($(this).hasClass("eat-disabled")){
                disabled_menu  += menuname+"|";
            }
        });

        // for disabled submenus
        $(".eat-submenu-item").each(function(){
            var id            = $(this).data("id");
            var parentpage    = $(this).data("parent-name");
            new_admin_submenu += parentpage+":"+id+"|";
            if($(this).hasClass("eat-disabled")){
            disabled_submenu  += parentpage+":"+id+"|";
            }
        });

        // renamed menus
        $(".eat_menu_rename").each(function(){
            var id          = $(this).data("id");
            var menu_id     = $(this).data("menu-id");
            var new_name    = $(this).val();
            var new_icon    = $(this).closest('.eat-menu-manager-item-edits').find('.eat-icon-picker').val();
            new_menu_rename += id+":"+menu_id+"|separator1|"+new_name+"|separator2|"+new_icon+"|separator3|";
        });

        // renamed submenus
        $(".eat_submenu_rename").each(function(){
            var id             = $(this).data("id");
            // var parent         = $(this).data("parent-id");
            var parentpage     = $(this).data("parent-page");
            var val            = $(this).val();
            new_submenu_rename += parentpage+"|separator4|"+id+"|separator1|"+val+"|separator3|";
        });

        //ajax call to save the new menu and submenu settings
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'eat_save_menu_submenu',
                neworder: new_admin_menu,
                menudisable: disabled_menu,
                newsuborder: new_admin_submenu,
                submenudisable: disabled_submenu,
                menurename: new_menu_rename,
                submenurename: new_submenu_rename,
                '_wpnonce' : eat_plugin_settings.ajax_nonce,
            },
            beforeSend: function() {
                $('.eat-ajax-message').show();
            },
            success: function( response ){
                $('.eat-ajax-message').show();
                setTimeout( function() {
                location.reload();
                }, 2000 );
            }
        });
    });

    //reset the original value of the menu and submenu name
    $('.eat-menu-submenu-reset-button').click(function(){
        var $original = $(this).data('original-value');
        $(this).prev('input').val($original);
    });

    // reset the icon of menu
    $('.eat-menu-icon-reset-button').click(function(){
        var $original = $(this).data('original-value');
        $(this).closest('.eat-menu-icon').find('.eat-icon-picker').val($original);
        $(this).prev('.icon-picker').removeClass().addClass('eat-button icon-picker dashicons '+$original);
    });

    //restore the default settings of menu items
    $('.eat-ajax-menu-submenu-reset-button').click(function(){
        if (confirm( 'Are you sure you want to reset menu?' )) {
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action': 'eat_reset_menu_submenu',
                    '_wpnonce' : eat_plugin_settings.ajax_nonce,
                },
                beforeSend: function() {
                    $( '.eat-ajax-message' ).show();
                },
                success: function( response ){
                    $( '.eat-ajax-message' ).show();
                    setTimeout( function() {
                        location.reload();
                    }, 2000 );
                }
            });
        }
    });
    $(function() {
        var header = $(".eat-dashboard-temp-19 #wpadminbar");
        $(window).scroll(function() {   
            var scroll = $(window).scrollTop();
       
            if (scroll >= 10) {
                header.addClass("fixedheader");
            } else {
                header.removeClass("fixedheader");
            }
        });
    });
    $(function() {
        var header = $(".eat-dashboard-temp-15 #wpadminbar");
        $(window).scroll(function() {   
            var scroll = $(window).scrollTop();
       
            if (scroll >= 10) {
                header.addClass("fixedheader");
            } else {
                header.removeClass("fixedheader");
            }
        });
    });

  
    $(function(){
         // $('.wp-has-submenu').click(function(e){
    //     e.preventDefault();
    //     $(this).find(".wp-submenu").show().slideDown("100");
       
    // });
        // $(".wp-has-submenu").hover(function(e){
        //     // return false;
        //     $(this).find(".wp-submenu").slideDown("300");
        // });
   



        // $('#adminmenu>li .wp-has-submenu').mouseover(function(e){
        //    return false;
        // });
        // $('#adminmenu>li .wp-has-submenu').mouseleave(function(e){
        //    return false;
        // });

        if( eat_plugin_settings.dashboard_template == 'temp-5' ||  eat_plugin_settings.dashboard_template == 'temp-6' || eat_plugin_settings.dashboard_template == 'temp-7' || eat_plugin_settings.dashboard_template == 'temp-8' ||  eat_plugin_settings.dashboard_template == 'temp-14' ||  eat_plugin_settings.dashboard_template == 'temp-15' || eat_plugin_settings.dashboard_template == 'temp-16' || eat_plugin_settings.dashboard_template == 'temp-17' || eat_plugin_settings.dashboard_template == 'temp-18' || eat_plugin_settings.dashboard_template == 'temp-19' || eat_plugin_settings.dashboard_template == 'temp-20' || eat_plugin_settings.dashboard_template == 'temp-21' || eat_plugin_settings.dashboard_template == 'temp-22' || eat_plugin_settings.dashboard_template == 'temp-23' || eat_plugin_settings.dashboard_template == 'temp-24' || eat_plugin_settings.dashboard_template == 'temp-25'){
            $("#adminmenu>li").unbind("mouseenter").unbind("mouseleave");
            $("#adminmenu>li").removeProp('hoverIntent_t');
            $("#adminmenu>li").removeProp('hoverIntent_s');

            $('#adminmenu>li').each(function(){
                if($(this).hasClass('wp-has-current-submenu')){
                    $(this).addClass('opensub');
                }
            });
            $('#adminmenu>li.wp-has-submenu a.menu-top').click(function(){
                var $all = $('#adminmenu>li.wp-has-submenu');
                // alert("Comeon");
                var $this = $(this);
                if($this.parent().hasClass('opensub')){
                    $this.parent().removeClass('opensub');
                    $this.next('.wp-submenu').slideUp(300);
                }else{
                    $all.not(this).removeClass('opensub'); 
                    $this.parent().addClass('opensub');
                    $this.next('.wp-submenu').slideDown(300);
                }
                return false;
            });
        }

    });

});