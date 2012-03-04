if( jQuery ) {
	jQuery( function() {
		// Content class edit
		NXC.Captcha.hideTips();
		NXC.Captcha.installSliders();
		NXC.Captcha.installColorSelectors();
		// Object edit
		NXC.Captcha.installRegenerateLink();
	} );
}

var NXC = NXC || {};
NXC.Captcha = {

	hideTips: function() {
		jQuery( 'div.nxc-captcha-tip' ).css( 'display', 'none' );
	},

	installSliders: function() {
		jQuery( 'input.nxc-captcha-slider-input' ).css( 'display', 'none' );
		jQuery.each(
			jQuery( 'div.nxc-captcha-slider' ),
			function( index, el ) {
				var el      = jQuery( this );
				var parent  = el.parent();
				var input   = jQuery( 'input.nxc-captcha-slider-input', parent );
				var options = {
					min: 0,
					max: 100,
					step: 1,
					value: input.val(),
					slide: function( event, ui ) {
						var parent = jQuery( this ).parent();
						jQuery( 'input.nxc-captcha-slider-input', parent ).val( ui.value );
						jQuery( 'span.nxc-captcha-slider-value', parent ).html( ui.value );
					}
				};
				jQuery.each( [ 'min', 'max' ], function( optionIndex, option ) {
					var optionInput = jQuery( 'input.nxc-captcha-slider-' + option, parent );
					if( optionInput.length == 1 ) {
						options[ option ] = parseInt( jQuery( optionInput[0] ).val(), 10 );
					}
				} );

				input.after(
					jQuery( '<span />', {
						'class': 'nxc-captcha-slider-value',
						'html': input.val()
					} )
				);

				el.slider( options );
			}
		);
	},

	installColorSelectors: function() {
		jQuery( 'input.nxc-captcha-color-selector-input' ).css( 'display', 'none' );
		jQuery.each(
			jQuery( 'div.nxc-captcha-color-selector' ),
			function( index, el ) {
				var el     = jQuery( this );
				var parent = el.parent();
				var input  = jQuery( 'input.nxc-captcha-color-selector-input', parent );
				el.ColorPicker( {
					color: '#' + input.val(),
					onShow: function( colpkr ) {
						jQuery( colpkr ).show();
					},
					onHide: function( colpkr ) {
						jQuery( colpkr ).hide();
					},
					onChange: function( hsb, hex, rgb ) {
						var el = jQuery( this.data('colorpicker').el );
						jQuery( 'input.nxc-captcha-color-selector-input', el.parent() ).val( hex );
						jQuery( 'div.nxc-captcha-color-selector-bg', el ).css(
							'backgroundColor', '#' + hex
						);
					}
				} );
			}
		);
	},

	installRegenerateLink: function() {
		var links = jQuery( 'a.nxc-captcha-regenerate' ).show();
		jQuery.each(
			links,
			function( index, el ) {
				var el  = jQuery( el );
				var tmp = el.attr( 'id' ).split( '-' );
				var id  = tmp[ tmp.length - 1 ];

				var image = jQuery( '#nxc-captcha-' + id );
				if( image.length > 0 ) {
					el.data( 'captcha', image );
					el.click( function( e ) {
						e.preventDefault();

						var el = jQuery( this );
						el.data( 'captcha' ).attr( 'src', false );
						el.data( 'captcha' ).attr(
							'src',
							el.attr( 'href' )+ '/' + ( 1000000 * ( Math.random() % 1 ) )
						);
					} );
				}
			}
		);
	}
}