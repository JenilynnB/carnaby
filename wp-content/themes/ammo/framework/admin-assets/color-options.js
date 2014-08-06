( function( $ ) {

	$('body').append('<div id="loading-less-options" style="z-index:999999; position:fixed; top:0px;left:0px; background-color:rgba(255,255,255,0.7); color:#333; text-align:center;width:100%;height:100%; vertical-align:super;"><span style="display:block; margin-top:100px; font-size:16px;">Loading...</span></div>');

	var $color_vars = ['brand-primary','text-color','body-bg','primary-background','top-bar-background','header-background','footer-background','brand-success','brand-info','brand-warning','brand-danger', 'title-background']; 
	var $font_vars = ['base-font-body', 'base-font-menu', 'base-font-heading'];
	var $font_size_vars = ['font-size-base', 'font-weight-base', 'menu-font-size', 'font-size-h1', 'font-size-h2', 'font-size-h3', 'font-size-h4', 'font-size-h5', 'font-size-h6', 'header-height', 'header_transparent', 'top-bar-height', 'top-bar-font-size'];
	var $variables = $color_vars.concat($font_vars, $font_size_vars);

	function modify_vars_less(){
		var modify_vars = {};
		for( var i=0; i<$variables.length; i++ ){
			var modified_value = wp.customize.instance($variables[i]).get();
			if( modified_value!='' ){
				modify_vars['@'+$variables[i]] = modified_value;
			}
		}
		less.modifyVars(modify_vars);
	}

	/* Color controls events */
	var $color_change_timer = null;
	for( var i=0; i<$color_vars.length; i++ ){
		wp.customize( $color_vars[i], function(value){
			value.bind( function(newval){ 

				if( $color_change_timer!=null ){
					clearInterval($color_change_timer);
				}
				$color_change_timer = setInterval(function(){
					modify_vars_less();
					clearInterval($color_change_timer);
				}, 500);

			} );
		});
	}

	for( var i=0; i<$font_size_vars.length; i++ ){
		wp.customize( $font_size_vars[i], function(value){
			value.bind( function(newval){ modify_vars_less(); } );
		});
	}


	wp.customize( 'base-font-body', function(value){
		value.bind( function(newval){
			$('.gf_base-font-body').remove();
			if( newval!='default' ){
				newval = newval.replace(/ /g, '+');
				newval = 'http://fonts.googleapis.com/css?family='+newval;
				if( $('link[href="'+newval+'"]').length<1 ){
					jQuery('head').append('<link href="'+ newval +'" rel="stylesheet" type="text/css" class="gf_base-font-body">');
				}
			}
			modify_vars_less();
		} );
	});
	wp.customize( 'base-font-menu', function(value){
		value.bind( function(newval){
			$('.gf_base-font-menu').remove();
			if( newval!='default' ){
				newval = newval.replace(/ /g, '+');
				newval = 'http://fonts.googleapis.com/css?family='+newval;
				if( $('link[href="'+newval+'"]').length<1 ){
					jQuery('head').append('<link href="'+ newval +'" rel="stylesheet" type="text/css" class="gf_base-font-menu">');
				}
			}
			modify_vars_less();
		} );
	});
	wp.customize( 'base-font-heading', function(value){
		value.bind( function(newval){
			$('.gf_base-font-heading').remove();
			if( newval!='default' ){
				newval = newval.replace(/ /g, '+');
				newval = 'http://fonts.googleapis.com/css?family='+newval;
				if( $('link[href="'+newval+'"]').length<1 ){
					jQuery('head').append('<link href="'+ newval +'" rel="stylesheet" type="text/css" class="gf_base-font-heading">');
				}
			}
			modify_vars_less();
		} );
	});


	jQuery(window).load(function(){


		setTimeout(function(){
			var $fonts = ['base-font-body', 'base-font-menu', 'base-font-heading'];
			for( var i=0; i<$fonts.length; i++ ){
				var $val = wp.customize.instance($fonts[i]).get();
				if( $val!='default' && $val!='' ){
					$val = $val.replace(/ /ig, '+');
					$val = 'http://fonts.googleapis.com/css?family='+$val;
					if( $('link[href="'+$val+'"]').length<1 ){
						jQuery('head').append('<link href="'+ $val +'" rel="stylesheet" type="text/css" class="gf_'+$fonts[i]+'">');
					}
				}
			}
			modify_vars_less();

			$('#loading-less-options').remove();
		}, 800);

		var site_layout = wp.customize.instance('general-layout').get();
		if( !$('body').hasClass(site_layout) ){
			$('body').removeClass('full boxed').addClass(site_layout);
		}

		if( site_layout=='boxed' ){
			var site_margin_top = wp.customize.instance('general-top-space').get();
			var site_margin_bottom = wp.customize.instance('general-bottom-space').get();
		}

		$('body').css('background-image', 'url('+wp.customize.instance('general-bg-image').get()+')');
		$('body').css('background-repeat', wp.customize.instance('general-bg-repeat').get());
		$('body').css('background-position', wp.customize.instance('general-bg-position').get());
		$('body').css('background-attachment', wp.customize.instance('general-bg-attach').get());

	});


	wp.customize( 'general-layout', function(value){
		value.bind( function(newval){
			$('body').removeClass('full boxed').addClass(newval);
			if( newval=='boxed' ){
				$('.layout-wrapper').css('margin-top', wp.customize.instance('general-top-space').get());
				$('.layout-wrapper').css('margin-bottom', wp.customize.instance('general-bottom-space').get());
			}
			else{
				$('.layout-wrapper').css('margin-top', '0');
				$('.layout-wrapper').css('margin-bottom', '0');
			}
		} );
	});
	wp.customize( 'general-top-space', function(value){
		value.bind( function(newval){
			if( wp.customize.instance('general-layout').get()=='boxed' ){
				$('.layout-wrapper').css('margin-top', newval);
			}
		} );
	});
	wp.customize( 'general-bottom-space', function(value){
		value.bind( function(newval){
			if( wp.customize.instance('general-layout').get()=='boxed' ){
				$('.layout-wrapper').css('margin-bottom', newval);
			}
		} );
	});

	// Body background options
	wp.customize( 'general-bg-image', function(value){
		value.bind( function(newval){
			$('body').css('background-image', 'url('+newval+')');
		} );
	});
	wp.customize( 'general-bg-repeat', function(value){
		value.bind( function(newval){
			$('body').css('background-repeat', newval);
		} );
	});
	wp.customize( 'general-bg-position', function(value){
		value.bind( function(newval){
			$('body').css('background-position', newval);
		} );
	});
	wp.customize( 'general-bg-attach', function(value){
		value.bind( function(newval){
			$('body').css('background-attachment', newval);
		} );
	});

	// Logo
	wp.customize( 'logo', function(value){
		value.bind( function(newval){
			$('.main-menu .logo').html('<a href="#"><img src="'+newval+'" alt="Spare" class="normal" /></a>');
		} );
	});

	// Header
	wp.customize( 'fixed_menu', function(value){
		value.bind( function(newval){
			if(newval == 1) {
				$('header#header').addClass('navbar-fixed-top');
			} else {
				$('header#header').removeClass('navbar-fixed-top');
			}
		} );
	});

	// Search box
	wp.customize( 'search_box', function(value){
		value.bind( function(newval){
			if(newval == 1) {
				$('.header-search').css('display', 'table-cell');
			} else {
				$('.header-search').css('display', 'none');
			}
		} );
	});

	// Menu alignment
	wp.customize( 'menu_alignment', function(value){
		value.bind( function(newval){
			$('.navmenu-cell').css('text-align', newval);
		} );
	});

	// Page title background options
	wp.customize( 'title-bg-image', function(value){
		value.bind( function(newval){
			$('section.page-title').css('background-image', 'url('+newval+')');
		} );
	});
	wp.customize( 'title-bg-repeat', function(value){
		value.bind( function(newval){
			$('section.page-title').css('background-repeat', newval);
		} );
	});
	wp.customize( 'title-bg-position', function(value){
		value.bind( function(newval){
			$('section.page-title').css('background-position', newval);
		} );
	});
	wp.customize( 'title-bg-attach', function(value){
		value.bind( function(newval){
			$('section.page-title').css('background-attachment', newval);
		} );
	});
	wp.customize( 'title-padding', function(value){
		value.bind( function(newval){
			$('.page-title.section').css('padding-top', newval);
			$('.page-title.section').css('padding-bottom', newval);
		} );
	});

} )( jQuery );