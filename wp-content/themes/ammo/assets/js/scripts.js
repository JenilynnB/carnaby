function isTouchDevice(){
    return true == ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch);
}

function parallax() {
	if( jQuery('.page-title > .container').length && jQuery('#tt-slider').length<1 ){
		var scrollPosition = jQuery(window).scrollTop();
		if( jQuery('.page-title > .container').eq(0).offset().top<120 ){
			jQuery('.page-title > .container').eq(0).css('opacity',((100 - scrollPosition) *0.01));
		}
	}
}


function detectIE() {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf('MSIE ');
    var trident = ua.indexOf('Trident/');

    if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    if (trident > 0) {
        // IE 11 (or newer) => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    // other browser
    return false;
}



function getContainerWidth(){
	var $container_width = jQuery('body').hasClass('boxed') ? jQuery('body > .layout-wrapper').width() : jQuery(window).width();
	$container_width = jQuery('.layout-wrapper').hasClass('right-section') ? jQuery('.layout-wrapper.right-section').width() : $container_width;
	return $container_width;
}


jQuery(document).ready(function($) {

	var $ie_version = detectIE();
	if( $ie_version!==false ){
		$('html').addClass('oldie ie'+$ie_version);
	}

	jQuery('p').each(function(){
		if( $(this).html()=='' ){ $(this).remove(); }
	});

	/* Fixing Header when has Top Slider */
	var $top_slider = jQuery('.slider-fullscreen');
	var has_topslider = false;
	if( $top_slider.length>0 ){
		has_topslider = true;
		jQuery('#header').css({'position': 'relative'});

		jQuery('#header_spacing').height(0);
		jQuery('.admin-bar #header').css({ 'margin-top': '0px' });
	}
	else{
		
		/*	Header height calculator
		================================================== */
		if( $('.header-transparent').length>0 ){
			$('#header_spacing').height(0);
			
			if( $('#tt-slider').length<1 ){
				$('section.section').eq(0).css({
					'padding-top': +parseInt($('section.section').eq(0).css('padding-top'))+$('#header').height()+'px'
				});
			}

			/** It will works when enabled sticky menu. */
			if( $('.header-transparent').hasClass('navbar-fixed-top') ){
				$(window).on('scroll', function(){
					var scrollTop = $(window).scrollTop();
					if( scrollTop > $('#header').height()+50 ){
						if( !$('#header').hasClass('stickymenu') ){
							$('#header').addClass('stickymenu');
						}
					}
					else{
						if( $('#header').hasClass('stickymenu') ){
							$('#header').removeClass('stickymenu');
						}
					}
				});
			} // end sticky transparent menu
			
		}
		else{
			if( $('.navbar-fixed-top').length>0 ){
				jQuery('#header_spacing').height( jQuery('#header').height()-1 );
				jQuery(window).resize(function(){
					jQuery('#header_spacing').height( jQuery('#header').height());
				});
			}
			else{
				$('#header_spacing').height(0);
			}
		}
		
	}


	$(window).on('scroll', function() {
		parallax();

		var scrollTop = $(window).scrollTop();
		var topbar_h = $('#top_bar').length>0 ? $('#top_bar').outerHeight() : 0;
		if( $(window).width()>600 && $('.navbar-fixed-top').length>0 ){
			if( has_topslider ){
				var wpbarh = $('#wpadminbar').length>0 ? $('#wpadminbar').height() : 0;
				var diff_wpbar_topbar = topbar_h-wpbarh;
				if( $top_slider.height()+diff_wpbar_topbar < scrollTop-wpbarh ){
					/* in here */
					jQuery('#header_spacing').height( jQuery('#header').height() );
					jQuery('#header').addClass('stickymenu');
					jQuery('#header').css({'position': 'fixed', 'margin-top': -diff_wpbar_topbar+'px' });
				}
				else{
					/* out of scroll */
					jQuery('#header_spacing').height(0);
					jQuery('#header').removeClass('stickymenu');
					jQuery('#header').css({'position': 'static', 'margin-top': '0px' });
				}
			}
			else{
				if( topbar_h>0 ){
                    // enabled topbar
                    if( scrollTop < topbar_h ){
                        $('#header').css({ 'top': -scrollTop+'px' });
                        
                        if( !$('.header-transparent').hasClass('navbar-fixed-top') ){
                            jQuery('#header').removeClass('stickymenu');
                        }
                    }
                    else{
                        $('#header').css({ 'top': -topbar_h+'px' });
                        
                        if( !$('.header-transparent').hasClass('navbar-fixed-top') ){
                            jQuery('#header').addClass('stickymenu');
                        }
                    }
                }
                else{
                    // disabled topbar
                    var header_height_no_sticky = 0;
                    var hheight = header_height_no_sticky==0 ? $('#header').height() : header_height_no_sticky;
                    if( scrollTop < hheight ){
                        $('#header').css({ 'top': -scrollTop+'px' });
                        
                        if( !$('.header-transparent').hasClass('navbar-fixed-top') ){
                            jQuery('#header').removeClass('stickymenu');
                        }
                    }
                    else{
                        $('#header').css({ 'top': -topbar_h+'px' });
                        
                        if( !$('.header-transparent').hasClass('navbar-fixed-top') ){
                            jQuery('#header').addClass('stickymenu');
                        }
                    }
                }
			}
		}
		else{
			$('#header').css({ 'top': '0px' });
		}
	});


	/* Mobile Menu */
	if( $('#mobile-menu-wrapper nav').length<1 && $('.navmenu-cell').length>0 ){
		var $navmenu = $('.navmenu-cell').clone();
		$navmenu.attr('class', '').attr('role', '').attr('id', 'mobile-menu');
		$navmenu.find('.header-search').remove();
		$navmenu.find('ul').attr('class', '');

		$navmenu.find('li.mega-menu').each(function(){
			var $li = $(this);
			var $megamenu_item = $li.find('>ul');
			$li.find('.menu-column').each(function(){
				$megamenu_item.append('<li><a href="javascript:;"><b>'+ $(this).find('>h3').html() +'</b></a></li>');
				$(this).find('.menu-item').each(function(){
					$megamenu_item.append('<li>'+ $(this).html() +'</li>');
				});
				$(this).remove();
			});
			$li.find('>ul > li').eq(0).remove();
		});

		$('#mobile-menu-wrapper .mobile-menu-content').append($navmenu);

		$('#mobile-menu-wrapper .mobile-menu-content').find('li').each(function(){
			if( $(this).find('ul').length>0 ){
				$(this).addClass('has-children');
			}
		});
	}
	

	$('#mobile-menu-handler').live('click', function(){
		$('body').toggleClass('smenu-push-toLeft');
		$('.mobile-menu-content').toggleClass('smenu-push-toLeft');
	});
	$('body').append('<div id="smenu-overlay"></div>');
	$('#smenu-overlay').live('click', function(){
		$('body').removeClass('smenu-push-toLeft');
		$('.mobile-menu-content').removeClass('smenu-push-toLeft');
		$('#mobile-cart-wrapper').removeClass('smenu-push-toLeft');
	});
	$('.mobile-menu-content.slidemenu-push li a').live('click', function(){
		if( $(this).parent().find('>ul').length>0 ){
			$(this).parent().find('>ul').slideToggle();
			$(this).parent().toggleClass('smenu-open');
			return false;
		}
	});


	/* Mobile Shopping Cart */
	if( $('#mobile-cart-wrapper').length>0 ){
		var $ul = $('#mobile-cart-wrapper').find('.cart_list');
		var $li1 = $('<li class="total"></li>').append( $ul.parent().find('.total') );
		var $li2 = $('<li class="buttons"></li>').append( $ul.parent().find('.buttons') );
		$ul.append($li1).append($li2);
		$('#mobile-cart-wrapper').find('.mobile-cart-tmp nav').append($ul);

		$('#mobile-cart-handler').live('click', function(){
			$('body').toggleClass('smenu-push-toLeft');
			$('#mobile-cart-wrapper').toggleClass('smenu-push-toLeft');
		});
	}


	/* Search icon event */
	$('#header .header-search .search-icon').click(function(){
		$(this).parent().find('.search-form').show();
		$(this).parent().find('.search-form input[type=text]').focus();
	});
	$(document).click(function(event){
		var $target = $(event.target);
		var $p = $target.parent();
		if( $p.hasClass('search-icon') || $p.hasClass('header-search') || $p.hasClass('input-group') ){ }
		else{
			$('#header .header-search .search-form').hide();
		}
	});
	
	

	/*	Onepage Local Scroll
	================================================== */
	if( $('#onepage-menu').length>0 ){
		if($('#one_page_menu').find('li').length>0) {
			$('#header').find('ul.navbar-nav').html( jQuery('#one_page_menu').html() ).attr('id', 'one-page-menu').css('display','inline-block');
		}

		$('#onepage-menu').find('a').tooltip({
			'selector': '',
			'placement': 'left',
			'container':'body'
		});

		$('#onepage-menu,#one-page-menu:not(.custom)').find('a').click(function(){
			var $this = $(this);
			var id = '#post-'+$this.data('id');
			if( $('#post-title-'+$this.data('id')).length>0 ){
				id = '#post-title-'+$this.data('id');
			}
			var $wpbar = $('#wpadminbar').length >0 ? $('#wpadminbar').height() : 0;
			$.scrollTo( $(id), 500, { offset: -($('#header').height()-$wpbar)+10 } );
		});

		$(window).scroll(function () {

			var $wpbar = $('#wpadminbar').length >0 ? $('#wpadminbar').height() : 0;
			var header_offset = $('#header').height()-$wpbar;

			$('#onepage-menu').find('a').each(function(){
				var data_id = $(this).data('id');
				var $target = $('#post-'+data_id);
				$target = $('#post-title-'+data_id).length>0 ? $('#post-title-'+data_id) : $target;
				if( $target.offset().top-header_offset < $(window).scrollTop() ){
					// Adding class for side bullets
					$('#onepage-menu').find('a').parent().removeClass('active');
					$(this).parent().addClass('active');
					// Adding classs for main menu
					$('#one-page-menu').find('a').parent().removeClass('active');
					$('#one-page-menu').find('a[data-id="'+data_id+'"]').parent().addClass('active');
				}				
			});
			
		});
	}
	


	/*	Pretty Photo
	================================================== */
	jQuery('.gallery a').addClass('lightbox');
	jQuery("a[rel^='prettyPhoto'],a.prettyPhoto,a.lightbox,.blox-element.prettyPhoto>a,.blox-element.lightbox>a").prettyPhoto({deeplinking:false,social_tools:false});



    // Go to top arrow
    jQuery('span.gototop').click(function() {
        jQuery('body,html').animate({scrollTop: 0}, 600);
    });

    jQuery(window).scroll(function(){
        if( jQuery(window).scrollTop() > 500 ){
            jQuery('.gototop').addClass('show');
        }
        else{
            jQuery('.gototop').removeClass('show');
        }
    });

	/*	Bootstrap JS
	================================================== */
	jQuery('[data-toggle="tooltip"]').tooltip();
	jQuery('[data-toggle="popover"]').popover();


	jQuery('.affix-element').each(function(){
		var $this = $(this);
		$this.affix({
			offset: {
				top: 300,
				bottom: 10
			}
		});
	});
	
	
	/*	Check menu hasChildren
	================================================== */
	if( jQuery('.main-menu ul').length>0 ){
		jQuery('.main-menu ul').eq(0).find('li').each(function(){
			var $this = jQuery(this);
			if( $this.find('ul').length > 0 ){
				$this.addClass('has-children');
			}
		});
	}



	/* Fix Loop iFrame size
	===================================================*/
	jQuery('.grid-loop').each(function(){
		var $this = jQuery(this);
		$this.find('.entry-media iframe').each(function(){
			var $media = jQuery(this).parent();
			var $iframe = jQuery(this);
			$iframe.width($media.width()).height( parseInt($media.width()*350/600) );

			jQuery(window).resize(function(){
				$iframe.width($media.width()).height( parseInt($media.width()*350/600) );
			});
		});
	});


	/* Fix Embed Video Height
	===================================================*/
	jQuery("section.primary").fitVids();



	/*	Swiper Slider
	================================================== */
	jQuery('.swipy-slider').each(function(index){
		var $this = jQuery(this);
		$this.find('.swiper-slide,.swiper-slide img').css({ 'width':'100%', 'display':'block' });
		$this.find('.swiper-pagination').addClass('swipy-paginater'+index);
		var $swiper = $this.swiper({
							mode:'horizontal',
							loop: true,
							keyboardControl: false,
							paginationClickable: true,
							resizeReInit: true,
							calculateHeight: true,
							pagination: '.swipy-paginater'+index
						});

		$this.fadeIn('fast');
		$this.find('.swiper-control-prev').click(function(){
			$swiper.swipePrev();
		});
		$this.find('.swiper-control-next').click(function(){
			$swiper.swipeNext();
		});

		if( $this.parent().hasClass('gallery_viewport') && $this.parent().parent().parent().find('.button').length>0 ){
			$this.parent().parent().parent().find('.button').click(function(){
				$swiper.swipeTo(0);
			});
		}
	});




	/* Portfolio Slider
	===================================================*/
	jQuery('.portfolio-slider').each(function(){
		var $this = jQuery(this).find('.swiper-container');
		var xr16x6 = 0.375;
		var xr16x9 = 0.5625;
		xr16x6 = $this.width()<960 ? xr16x9 : xr16x6;
		var h = $this.width()*xr16x6;
		h = h>640 ? 640 : h;
		$this.find('.swiper-wrapper').height(h);

		var $swiper = $this.swiper({
							mode:'horizontal',
							loop: true,
							keyboardControl: true,
							paginationClickable: true,
							resizeReInit: true,
							pagination: '.swiper-pagination',
							onSlideChangeEnd: function(swiper, direction){
								if( !$this.find('.swiper-slide.video').hasClass('swiper-slide-active') ){
									$this.find('.swiper-slide.video').html( $this.find('.swiper-slide.video').html() );
								}
							}
						});
		$this.find('.swiper-control-prev').click(function(){
			$swiper.swipePrev();
		});
		$this.find('.swiper-control-next').click(function(){
			$swiper.swipeNext();
		});

		if( $this.hasClass('layout-sidebar') )
			$this.find('iframe').width( $this.width() ).height( $this.width()*xr16x6 );
		else
			$this.find('iframe').height(h).width(h*1.777);
		$this.find('.video-wrapper').show();


		jQuery(window).resize(function(){
			var xr16x6 = 0.375;
			//var xr16x9 = 0.5625;
			xr16x6 = $this.width()<960 ? xr16x9 : xr16x6;
			var h = $this.width()*xr16x6;
			h = h>640 ? 640 : h;
			$this.find('.swiper-wrapper').height(h);
			$this.find('.swiper-slide').height(h);
			
			if( $this.hasClass('layout-sidebar') )
				$this.find('iframe').width( $this.width() ).height( $this.width()*xr16x6 );
			else
				$this.find('iframe').height(h).width(h*1.777);
			
		});
	});

	
	/* Carousel Swiper Slider
	====================================*/
	jQuery('.blox-carousel.swiper-container').each(function(){
		var $this = jQuery(this);
		var column = 1;
		
		if( $this.width() > 939){ column = 6; }
		else if( $this.width() > 422 ){ column = 5; }
		else if( $this.width() > 400 ){ column = 4; }

		jQuery('.woocommerce.swiper-container ul.products li.product').css({
			'margin': 'auto',
			'padding': '15px'
		});

		if( $this.hasClass('woocommerce') ){
			$this.find('li').each(function(){
				jQuery(this).removeClass('last first')
							.addClass('swiper-slide')
							.addClass('col-md-3 col-sm-6 col-xs-12');
			});
		}
		

		var $carousel = $this.swiper({
							slidesPerView: column,
							calculateHeight: true
						});

		$this.find('.carousel-control-prev').click(function(){
			$carousel.swipePrev();
		});
		$this.find('.carousel-control-next').click(function(){
			$carousel.swipeNext();
		});

		// fix title position
		$this.find('article.entry.hover').each(function(){
            var $title = jQuery(this).find('.entry-title h2');
            $title.css({ 'margin-top': parseInt( jQuery(this).height()/2-60-$title.height()/2 ) });
        });

        jQuery(window).load(function(){
        	$carousel.reInit();
        });

		jQuery(window).resize(function(){
			if( $this.width() > 939){ $carousel.params.slidesPerView = 6; }
			else if( $this.width() > 422 ){ $carousel.params.slidesPerView = 5; }
			else if( $this.width() > 400 ){ $carousel.params.slidesPerView = 4; }
			else{ $carousel.params.slidesPerView = 1; }

			// fix title position
			$this.find('article.entry.hover').each(function(){
				var $title = jQuery(this).find('.entry-title h2');
				$title.css({ 'margin-top': parseInt( jQuery(this).height()/2-60-$title.height()/2 ) });
			});
		});
	});

	


	/* Fullwidth Carousel Swiper Slider
	====================================*/
	jQuery('.fullwidth-carousel').each(function(){
		var $this = jQuery(this);
		var column = 1;

		var $container_width = getContainerWidth();
		var lw_ofleft = jQuery('body > .layout-wrapper').offset().left;
		
		$this.width( $container_width )
			.css({ 'margin-left': -$this.offset().left+lw_ofleft });
		
		
		$this.find('.blox-element.grid-loop').css({ 'margin-bottom': '0px' });
		$this.find('.grid-loop article').css({ 'margin-bottom': '0px' });
		$this.find('.entry-media').css({ 'margin-bottom': '0px' });
		
		
		if( $this.width() > 939){ column = 4; }
		else if( $this.width() > 422 ){ column = 3; }
		else if( $this.width() > 400 ){ column = 2; }

		var $carousel = $this.swiper({
							slidesPerView: column,
							calculateHeight: true
						});

        $this.animate({ 'opacity': 1 }, 1000);

		jQuery(window).resize(function(){
			var $container_width = getContainerWidth();
			var lw_ofleft = jQuery('body > .layout-wrapper').offset().left;

			$this.width( $container_width ).css({ 'margin-left': '0' });
			$this.css({ 'margin-left': -$this.offset().left+lw_ofleft });

			if( $this.width() > 939){ $carousel.params.slidesPerView = 4; }
			else if( $this.width() > 422 ){ $carousel.params.slidesPerView = 3; }
			else if( $this.width() > 400 ){ $carousel.params.slidesPerView = 2; }
			else{ $carousel.params.slidesPerView = 1; }

			// fix title position
			$this.find('article.entry.hover').each(function(){
				var $title = jQuery(this).find('.entry-title h2');
				$title.css({ 'margin-top': parseInt( jQuery(this).height()/2-60-$title.height()/2 ) });
			});
		});
	});


	
	
	/*  Fullwidth Portfolio Masonry
    ================================================== */
    jQuery('.fullwidth-portfolio').each(function(){
        var $this = jQuery(this);

        var container_width = getContainerWidth();
		var lw_ofleft = jQuery('body > .layout-wrapper').offset().left;

        $this.find('.masonry-container').width( container_width ).css({ 'margin-left': -$this.offset().left+lw_ofleft });

        var $col = parseInt( $this.attr('data-column') );

        if( $this.width() > 939){  }
		else if( $this.width() > 422 ){ $col = 3; }
		else if( $this.width() > 400 ){ $col = 2; }
		else{ $col = 1; }

        $this.find('.post_filter_item').width( container_width/$col )
                .css({
                    'float': 'left'
                });

        $this.css({ 'margin-bottom': '0px' });
        $this.find('article.entry').css({ 'margin-bottom': '0px' });
        $this.find('.entry-media').css({ 'margin-bottom': '0px' });

        $this.animate({ 'opacity': 1 }, 1000);

        $this.find('article.entry.hover').each(function(){
            var $title = jQuery(this).find('.entry-title h2');
            $title.css({ 'margin-top': parseInt( jQuery(this).height()/2-60-$title.height()/2 ) });
        });

		jQuery(window).resize(function(){
			var container_width = getContainerWidth();
			var lw_ofleft = jQuery('body > .layout-wrapper').offset().left;

            $this.find('.masonry-container').width( container_width ).css({ 'margin-left': '0' });
            $this.find('.masonry-container').css({ 'margin-left': -$this.offset().left+lw_ofleft });

            if( $this.width() > 939){ $this.find('.post_filter_item').width( container_width/$col ); }
            else if( $this.width() > 422 ){ $this.find('.post_filter_item').width( container_width/3 ); }
            else if( $this.width() > 400 ){ $this.find('.post_filter_item').width( container_width/2 ); }
            else{ $this.find('.post_filter_item').width( container_width ); }

            $this.find('article.entry.hover').each(function(){
                var $title = jQuery(this).find('.entry-title h2');
                $title.css({ 'margin-top': parseInt( jQuery(this).height()/2-60-$title.height()/2 ) });
            });
        });
    });


	

	
	/* Woocommerce Ajax Complete Request */
	jQuery(document).ajaxComplete(function( event, request, settings ) {
		if( typeof settings.data != 'undefined' && (settings.data.indexOf('action=woocommerce_get_refreshed_fragments')>-1 || settings.data.indexOf('action=woocommerce_add_to_cart')>-1) ){
			var response = request.responseJSON;

			if( typeof response.fragments!=='undefined' && typeof response.fragments['div.widget_shopping_cart_content']!=='undefined' ){
				var cart = response.fragments['div.widget_shopping_cart_content'];
				jQuery('.woocommerce-shcart').each(function(){
					var $this = jQuery(this);
					$this.find('.shcart-content').html( cart );

					var count = 0;
					//var total = $this.find('.shcart-content').find('.total .amount').html();
					$this.find('.shcart-content').find('.quantity').each(function(){
						var $quant = jQuery(this).clone();
						$quant.find('.amount').remove();
						count += parseInt($quant.text());
					});

					$this.find('.shcart-display .total-cart').html( count );
				});
			}
			
		}
	});

	/*
	jQuery('.woocommerce-shcart').each(function(){
		var $this = jQuery(this);
		$this.find('.shcart-display').hover(
			function(){
				$this.find('.shcart-content').slideDown();
			},
			function(){
				$this.find('.shcart-content').slideUp();
			}
		);
	});
	*/
	



	/*	Animation with Waypoints
	================================================== */
	var animate_start = function($this){
		$this.find('.animate').each(function(i){
			var $item = jQuery(this);
			var animation = $item.data("animate");

			$item.waypoint(function(direction){
				$item.css({
					'-webkit-animation-delay': (i*0.1)+"s",
					'-moz-animation-delay': (i*0.1)+"s",
					'-ms-animation-delay': (i*0.1)+"s",
					'-o-animation-delay': (i*0.1)+"s",
					'animation-delay': (i*0.1)+"s"
				});
				$item.removeClass('animate').addClass('animated '+animation).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
					jQuery(this).removeClass(animation+' animated');
				});
			},
			{
				offset: '88%',
				triggerOnce: true
            });
		});
	};
	jQuery('.blox-row').each(function(){
        var $this = jQuery(this);
        animate_start( $this );
	});



	/*	Counter Element
	================================================== */
	jQuery('.blox-counter').each(function(){
		var $this = jQuery(this);
		if( $this.hasClass('counter-count') ){
			$this.find('.counter-number').counterUp({
				delay: 10,
				time: 1000
			});
		}
		else if( $this.hasClass('counter-scroll') ){
			$this.waypoint(function(){
				$this.find('.numeric').each(function(){
					var $numeric = jQuery(this);
					var $ul = $numeric.find('ul');
					var $li_count = $ul.find('li').length;
					$ul.animate({ 'top': '-'+$ul.height()/$li_count*($li_count-1)+'px' }, 1500);
				});
			},
			{offset:"100%", triggerOnce:!0 });
		}
	});
	

});


function fix_product_height(){
	jQuery('.product-image-hover').each(function(){
		var $this = jQuery(this);
		$this.height( $this.width() );
	});
}



/* Window Load/All Media Loaded */
jQuery(window).load(function() {


	/* Fix menu position */
	/*
	var menuHeight = (jQuery('#header').find('.navbar-header').height() - jQuery('#header').find('.mainmenu').height())/2;
	menuHeight = parseInt(menuHeight);
	jQuery('#header').find('.mainmenu').css({ 'margin-top': menuHeight+'px' });
	*/

	if( jQuery('.navbar-fixed-top').length>0 ){
		if( jQuery('.navbar-fixed-top').hasClass('header-transparent') ){
			jQuery('#header_spacing').height(0);
		}
		else{
			jQuery('#header_spacing').height( jQuery('#header').height()-1 );
			jQuery(window).resize(function(){
				jQuery('#header_spacing').height( jQuery('#header').height());
			});
		}
	}
	

	
	fix_product_height();
	jQuery(window).resize(function(){
		fix_product_height();
	});

	jQuery('ul.products:not(.swiper-wrapper)').each(function(){
		var $product = jQuery(this);
		$product.isotope({
            itemSelector : 'li.product',
            layoutMode: 'fitRows'
        });

        jQuery(window).resize(function(){
			$product.isotope('layout');
		});
	});
	

	/* init Skrollr Parallax
	==================================================*/
	if( !isTouchDevice() ){
		jQuery(window).stellar({
			horizontalScrolling: false,
			responsive: true
		});
	}


	/*  Fullwidth Portfolio Masonry
    ================================================== */
    jQuery('.fullwidth-portfolio').each(function(){
        var $this = jQuery(this);
        var $portfolio_masonry = $this.find('.masonry-container');
        var masonry_item = '.post_filter_item';

        $portfolio_masonry.isotope({
            itemSelector : masonry_item
        });

        $this.find('.portfolio-filter ul li a').click(function(){
            var $filter = jQuery(this);
            var filter = $filter.attr('data-filter');

            $this.find('.portfolio-filter ul li a').removeClass('active');
            $filter.addClass('active');
            
            $this.find('.portfolio-filter h3').html( $filter.html() );
            filter = filter=='all' ? '*' : '.'+filter;
            $portfolio_masonry.isotope({ filter: filter });
        });

        /** Fix title Position */
        $this.find('article.entry.hover').each(function(){
            var $title = jQuery(this).find('.entry-title h2');
            $title.css({ 'margin-top': parseInt( jQuery(this).height()/2-60-$title.height()/2 ) });
        });
    });



    /** Fix Fullwidth Carousel Title Position */
	jQuery('.fullwidth-carousel').each(function(){
		var $this = jQuery(this);
		$this.find('article.entry.hover').each(function(){
			var $title = jQuery(this).find('.entry-title h2');
			$title.css({ 'margin-top': parseInt( jQuery(this).height()/2-60-$title.height()/2 ) });
		});
	});
	


});

/******************************************************
 * Animation to scroll user down the page when
 * they click to write a review on the listing page
 ******************************************************/

$(document).ready(function(){
  $('a[href*=#][id="toplink"]').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
    && location.hostname == this.hostname) {
      var $target = $(this.hash);
      $target = $target.length && $target
      || $('[name=' + this.hash.slice(1) +']');
      if ($target.length) {
        var targetOffset = $target.offset().top -120;
        $('html,body')
        .animate({scrollTop: targetOffset}, 1000);
       return false;
      }
    }
  });
  
});

/*******************************************
 * Slick Slider animation
 * *****************************************
 */

$(document).ready(function(){
    $('.slick-slider-posts').slick({
	  dots: true,
          infinite: false,
          slidesToShow: 3,
          slidesToScroll: 3,
          responsive: [
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 3,
                slidesToScroll: 3,
                infinite: true,
                dots: true
              }
            },
            {
              breakpoint: 600,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 2
              }
            },
            {
              breakpoint: 480,
              settings: {
                slidesToShow: 1,
                slidesToScroll: 1
              }
            }
          ]
	});
});


/******************************************************************************
 * Flipping forms between registration and login
 ******************************************************************************/



$('.linkform').live('click', function(e){
   try{
        var $form_wrapper = $('#reg_form_wrapper'), 
            $currentForm = $form_wrapper.children('.active');
    }catch(e){
        return;
    }
    var $link = jQuery(this);
    var target = $link.attr('rel');
    
    
    $currentForm.fadeOut(400, function(){
        $currentForm.removeClass('active');
        $currentForm = $form_wrapper.children("."+target);
    
    /*Can this be cleaned up since the size should not change? Simply a fade in of the new form?*/
    $form_wrapper.stop()
            .animate({
                //width: $currentForm.data('width') + 'px',
                //height: $currentForm.data('height') + 'px'
            }, 500, function(){
                $currentForm.addClass('active');
                $currentForm.fadeIn(400);
            });
    });
   
});

/* Initializing the registration & login forms to flip*/

$(function(){
    
    try{
        var $form_wrapper = $('#reg_form_wrapper'), 
            $currentForm = $form_wrapper.children('.active'), 
            $linkform = $form_wrapper.find('.linkform');
        if ($form_wrapper.length==0){
            var $form_wrapper = $('#form_wrapper_edit'), 
            $currentForm = $form_wrapper.children('.active'), 
            $linkform = $form_wrapper.find('.linkform');
        }
    }catch(e){
        return;
    }
    
    $form_wrapper.children('.flip-form').each(function(i){
        var $theForm = $(this);
        if(!$theForm.hasClass('active'))
            $theForm.hide();
        $theForm.data({    
            //width : $theForm.width(),
            //height: $theForm.height() 
       });
    });

    
    /* Function to animate scroll down the page and open appropriate form when
     * the user clicks to write a review
     * 
     * Additional logic needs to be added here to have different outcomes when 
     * the user is logged in and logged out
     **/
    $( "#toplink" ).bind( "click", function(e) {
        $currentForm = $form_wrapper.children('.write-review-btn');
        if ($currentForm.length==0){
            $currentForm = $form_wrapper.children('.review-details');
        }
        
        var target = $linkform.attr('rel');
        
        $currentForm.fadeOut(400, function(){
            $currentForm.removeClass('active');
            $currentForm = $form_wrapper.children("."+target);
            $form_wrapper.stop()
                .animate({
                    width: $currentForm.data('width') + 'px',
                    height: $currentForm.data('height') + 'px'
                }, 500, function(){
                    $currentForm.addClass('active');
                    $currentForm.fadeIn(400);
                });
        });
        e.preventDefault();
    });

    
});

/*
 * Determines whether the header is fixed and adjusts the height of the
 * registration modal as necessary.
 */
$(document).on("show.bs.modal", ".modal", function() {
    try{
       var $modal = $('#reg-modal-dialog'), $header = $('#header');
    }catch(e){
        return;
    }
    //get the position from the header
    var height;
    if($header.css('position')=='fixed'){
        height = $header.height();
    }else{
        height = 0;
    }
   
    height +=10;
    $modal.css('padding-top', height);
   
});



/******************************************************************************
 * Flipping forms for writing a review
 ******************************************************************************/



$('.linkform').live('click', function(e){
   try{
        var $form_wrapper = $('#form_wrapper'), 
            $currentForm = $form_wrapper.children('.active');
    }catch(e){
        return;
    }
    var $link = jQuery(this);
    var target = $link.attr('rel');
    
    
    $currentForm.fadeOut(400, function(){
        $currentForm.removeClass('active');
        $currentForm = $form_wrapper.children("."+target);
    
    /*Can this be cleaned up since the size should not change? Simply a fade in of the new form?*/
    $form_wrapper.stop()
            .animate({
                //width: $currentForm.data('width') + 'px',
                //height: $currentForm.data('height') + 'px'
            }, 500, function(){
                $currentForm.addClass('active');
                $currentForm.fadeIn(400);
            });
    });
   
});

/* Initializing the review forms to flip*/

$(function(){
    
    try{
        var $form_wrapper = $('#form_wrapper'), 
            $currentForm = $form_wrapper.children('.active'), 
            $linkform = $form_wrapper.find('.linkform');
        if ($form_wrapper.length==0){
            var $form_wrapper = $('#form_wrapper_edit'), 
            $currentForm = $form_wrapper.children('.active'), 
            $linkform = $form_wrapper.find('.linkform');
        }
    }catch(e){
        return;
    }
    
    $form_wrapper.children('.flip-form').each(function(i){
        var $theForm = $(this);
        if(!$theForm.hasClass('active'))
            $theForm.hide();
        $theForm.data({    
            //width : $theForm.width(),
            //height: $theForm.height() 
       });
    });
    
    function setWrapperWidth(){
            $form_wrapper.css({
                    width	: $currentForm.data('width') + 'px',
                    height	: $currentForm.data('height') + 'px'
            });
    }
    
    

    
});