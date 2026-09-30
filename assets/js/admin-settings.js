/**
 * Theme Settings screen: color pickers, presets, live preview and contrast
 * checks (adapted from the PEN theme), image pickers, footer column options
 * and the reset confirmation.
 */
( function ( $ ) {
	'use strict';

	var i18n = window.c1791Settings || {};

	/* ------------------------------------------------------------------
	 * Colors
	 * ------------------------------------------------------------------ */

	function hexToRgb( hex ) {
		hex = String( hex || '' ).replace( '#', '' );
		if ( 3 === hex.length ) {
			hex = hex.replace( /(.)/g, '$1$1' );
		}
		if ( ! /^[0-9a-f]{6}$/i.test( hex ) ) {
			return null;
		}
		var n = parseInt( hex, 16 );
		return [ ( n >> 16 ) & 255, ( n >> 8 ) & 255, n & 255 ];
	}

	function luminance( rgb ) {
		var c = rgb.map( function ( v ) {
			v /= 255;
			return v <= 0.03928 ? v / 12.92 : Math.pow( ( v + 0.055 ) / 1.055, 2.4 );
		} );
		return 0.2126 * c[ 0 ] + 0.7152 * c[ 1 ] + 0.0722 * c[ 2 ];
	}

	function contrast( a, b ) {
		var x = hexToRgb( a );
		var y = hexToRgb( b );
		if ( ! x || ! y ) {
			return null;
		}
		var l1 = luminance( x );
		var l2 = luminance( y );
		return ( Math.max( l1, l2 ) + 0.05 ) / ( Math.min( l1, l2 ) + 0.05 );
	}

	function initColors( $wrap ) {
		var $inputs = $wrap.find( '.c1791-color-field' );
		var $preview = $wrap.find( '[data-c1791-preview]' );
		var $contrast = $wrap.find( '.c1791-contrast' );
		var pairs = $contrast.data( 'pairs' ) || [];
		var pending = null;

		// Effective color for a key: the typed value, else the default.
		function resolved( key ) {
			if ( '#' === key.charAt( 0 ) ) {
				return key;
			}
			var $input = $inputs.filter( '[data-key="' + key + '"]' );
			return $.trim( $input.val() ) || $input.data( 'default' );
		}

		function update() {
			pending = null;
			$inputs.each( function () {
				var $input = $( this );
				$preview[ 0 ].style.setProperty( $input.data( 'var' ), resolved( $input.data( 'key' ) ) );
			} );

			$contrast.empty();
			pairs.forEach( function ( pair ) {
				var fg = resolved( pair[ 1 ] );
				var bg = resolved( pair[ 2 ] );
				var ratio = contrast( fg, bg );
				if ( null === ratio ) {
					return;
				}
				var low = ratio < 4.5;
				var $li = $( '<li/>' ).toggleClass( 'is-low', low );
				$( '<span class="c1791-contrast__sample" aria-hidden="true">Aa</span>' ).css( { color: fg, background: bg } ).appendTo( $li );
				$( '<span class="c1791-contrast__label"/>' ).text( pair[ 0 ] ).appendTo( $li );
				$( '<span class="c1791-contrast__ratio"/>' ).text( ratio.toFixed( 1 ) + ':1' ).appendTo( $li );
				$( '<span class="c1791-contrast__status"/>' ).text( low ? i18n.low : i18n.ok ).appendTo( $li );
				$contrast.append( $li );
			} );
		}

		function queue() {
			if ( ! pending ) {
				pending = window.setTimeout( update, 30 );
			}
		}

		$inputs.wpColorPicker( {
			change: queue,
			clear: queue,
		} );
		$inputs.on( 'input change', queue );

		$wrap.on( 'click', '.c1791-preset', function () {
			var colors = $( this ).data( 'colors' ) || {};
			$inputs.each( function () {
				var key = $( this ).data( 'key' );
				if ( colors[ key ] ) {
					$( this ).wpColorPicker( 'color', colors[ key ] );
				}
			} );
			queue();
		} );

		$wrap.on( 'click', '.c1791-clear-colors', function () {
			$inputs.each( function () {
				var $input = $( this );
				if ( $input.val() ) {
					$input.closest( '.wp-picker-container' ).find( '.wp-picker-clear' ).trigger( 'click' );
				}
			} );
			queue();
		} );

		update();
	}

	/* ------------------------------------------------------------------
	 * Image fields (media library)
	 * ------------------------------------------------------------------ */

	function initImage( $wrap ) {
		var $input = $wrap.find( 'input[type="url"]' );
		var $preview = $wrap.find( '.c1791-image__preview' );
		var $clear = $wrap.find( '.c1791-image__clear' );
		var frame = null;

		function set( url ) {
			$input.val( url ).trigger( 'change' );
			$preview.prop( 'hidden', ! url ).css( 'background-image', url ? 'url("' + url + '")' : '' );
			$clear.prop( 'hidden', ! url );
		}

		$wrap.on( 'click', '.c1791-image__pick', function () {
			if ( ! window.wp || ! wp.media ) {
				return;
			}
			if ( ! frame ) {
				frame = wp.media( {
					title: i18n.chooseImage,
					button: { text: i18n.useImage },
					library: { type: 'image' },
					multiple: false,
				} );
				frame.on( 'select', function () {
					var attachment = frame.state().get( 'selection' ).first().toJSON();
					set( attachment.url );
				} );
			}
			frame.open();
		} );

		$clear.on( 'click', function () {
			set( '' );
		} );

		$input.on( 'change', function () {
			var art = /flag/.test( $input.attr( 'name' ) ) ? 'flag' : ( /target/.test( $input.attr( 'name' ) ) ? 'target' : '' );
			if ( art && i18n.art ) {
				$( '[data-c1791-art="' + art + '"]' ).css( 'background-image', 'url("' + ( $input.val() || i18n.art[ art ] ) + '")' );
			}
		} );
	}

	/* ------------------------------------------------------------------
	 * Footer columns
	 * ------------------------------------------------------------------ */

	function initFooter() {
		var $count = $( '#c1791-footer-columns' );
		var $brand = $( '#c1791-footer-brand' );
		var $layout = $( '#c1791-footer-layout' );
		var $diagram = $( '[data-c1791-footer-diagram]' );
		if ( ! $count.length ) {
			return;
		}

		function columnTypes() {
			$( '[data-c1791-column]' ).each( function () {
				var $col = $( this );
				var type = $col.find( 'select[id$="-type"]' ).val();
				$col.find( '[data-show-for]' ).each( function () {
					var types = String( $( this ).data( 'show-for' ) ).split( ' ' );
					$( this ).prop( 'hidden', -1 === types.indexOf( type ) );
				} );
				$col.find( '[data-hide-for]' ).each( function () {
					$( this ).prop( 'hidden', String( $( this ).data( 'hide-for' ) ) === type );
				} );
			} );
		}

		function layout() {
			var count = Math.max( 0, Math.min( 4, parseInt( $count.val(), 10 ) || 0 ) );
			var brand = $brand.is( ':checked' );
			$( '[data-c1791-column]' ).each( function () {
				var n = parseInt( $( this ).data( 'c1791-column' ), 10 );
				$( this ).toggleClass( 'is-off', n > count );
			} );
			$diagram.find( '[data-col]' ).each( function () {
				$( this ).prop( 'hidden', parseInt( $( this ).data( 'col' ), 10 ) > count );
			} );
			$diagram.find( '.is-brand' ).prop( 'hidden', ! brand );
			$diagram.toggleClass( 'is-wide', 'brand-wide' === $layout.val() && brand );
		}

		$( document ).on( 'change', '[data-c1791-column] select', columnTypes );
		$count.add( $brand ).add( $layout ).on( 'input change', layout );
		columnTypes();
		layout();
	}

	$( function () {
		$( '.c1791-colors' ).each( function () {
			initColors( $( this ) );
		} );
		$( '[data-c1791-image]' ).each( function () {
			initImage( $( this ) );
		} );
		initFooter();

		$( '[data-c1791-confirm]' ).on( 'submit', function ( e ) {
			// eslint-disable-next-line no-alert
			if ( ! window.confirm( i18n.confirmReset ) ) {
				e.preventDefault();
			}
		} );
	} );
} )( jQuery );
