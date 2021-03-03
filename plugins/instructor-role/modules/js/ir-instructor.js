jQuery( document ).ready(
	function(){
		/* show hamburger button and hide and show sidebar */
		jQuery( '.wdm-mob-menu' ).on(
			'click',
			function () {
				if (jQuery( '.wdm-mob-menu' ).hasClass( 'wdm-hidden' )) {
					jQuery( this ).removeClass( 'wdm-hidden' );
					jQuery( '#adminmenumain' ).css( 'display', 'block' );
					jQuery( '#adminmenuback' ).css( 'display', 'block' );
					jQuery( '#adminmenuwrap' ).css( 'display', 'block' );
				} else {
					jQuery( this ).addClass( 'wdm-hidden' );
					jQuery( '#adminmenumain' ).css( 'display', 'none' );
					jQuery( '#adminmenuback' ).css( 'display', 'none' );
					jQuery( '#adminmenuwrap' ).css( 'display', 'none' );
				}
			}
		);

		// Remove sticky menu
		jQuery("body").removeClass("sticky-menu");
	}
);
