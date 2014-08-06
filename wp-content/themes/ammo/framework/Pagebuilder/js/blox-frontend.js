
jQuery(function(){
	

    // Fixing Firefox column 3
    var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
    if( is_firefox ){
        jQuery('head').append('<style type="text/css" id="firefox_style"></style>');
        jQuery('#firefox_style').html('.col-md-4{ width: 33.2% !important; }');
    }


	/* Tab
    ============================================*/
    jQuery('.blox-element.tabs').each(function(){
        var $this = jQuery(this);

        $this.find(".tab-pane").each(function(index){
            $this.find('ul.nav').append('<li class="active"><a href="#'+ jQuery(this).attr('id') +'" data-toggle="tab">'+jQuery(this).attr('title')+'</a></li>');
        });

        $this.find('ul.nav a').click(function (e) {
            e.preventDefault();
            jQuery(this).tab('show');
        })

        if( $this.find('ul.nav a').length>0 ){
            $this.find('ul.nav > li').removeClass('active');
            $this.find('ul.nav a').eq(0).trigger('click');
        }
    });
     
     
    /* Accordion
    ============================================*/
    jQuery('.blox-element .accordion').each(function(){
        var $this = jQuery(this);
        $this.find('.panel-title a').each(function(){
            jQuery(this).attr('data-parent', '#'+$this.attr('id'));
        });
    });

    
	
	jQuery('.blox_gallery').each(function(){
		var $this = jQuery(this);
		
		if( $this.hasClass('gallery_layout2') ){
			
			var $cloned =  $this.find('.gallery_thumbs a').eq(0).clone();
			$cloned.attr('rel', '');
			$cloned.find('img').attr('src', $cloned.attr('data-preview'));
			$this.find('.gallery_preview .preview_panel').html( $cloned );
			
			$this.find('.gallery_preview').find('a').unbind('click').click(function(){
				$this.find('.gallery_thumbs a').eq(0).trigger('click');
				return false;
			});
			
			$this.find('.gallery_thumbs a').hover(
				function(){
					var $cloned_item =  jQuery(this).clone();
					$cloned_item.attr('rel', '');
					$cloned_item.find('img').attr('src', $cloned_item.attr('data-preview'))
					$this.find('.gallery_preview .preview_panel').html( $cloned_item );
					
					var selected_index = $this.find('.gallery_thumbs a').index( jQuery(this) );
					$this.find('.gallery_preview').find('a').unbind('click')
						.click(function(){
							$this.find('.gallery_thumbs a').eq(selected_index).trigger('click');
							return false;
						});
				},
				function(){
					
				}
			);
		}
        else if( $this.hasClass('gallery_imac') || $this.hasClass('gallery_laptop') || $this.hasClass('gallery_iphone') ){
            if( $this.find('.gallery_viewport > div').length<2 ){
                $this.find('.gallery_prev,.gallery_next').hide();
            }
            $this.find('.gallery_viewport').cycle({
                slides: '>div',
                prev: $this.find('.gallery_prev'),
                next: $this.find('.gallery_next'),
                swipe: true
            });
        }
        else if( $this.hasClass('gallery_layout_slider') ){
            $this.find('.gallery_preview').cycle({
                pager: $this.find('.gallery_pager'),
                swipe: true
            });
        }
		else{
			
		}
		$this.find("a[rel^='blox_gallery']").prettyPhoto({deeplinking:false,social_tools:false});
	});


    // JPlayer Audio
    jQuery('.blox-element.audio .jplayer-audio').each(function(){
        jQuery(this).jPlayer({
            ready: function () {
                jQuery(this).jPlayer("setMedia", {
                    mp3: jQuery(this).data('src')
                });
            },
            play: function(){
                jQuery('.blox-element.audio .jplayer-audio').not(this).jPlayer("pause");
            },
            wmode:"window",
            swfPath: blox_plugin_path+"../../assets/plugins/jplayer/",
            cssSelectorAncestor: "#jp_interface_"+jQuery(this).data('pid'),
            supplied: "mp3"
        });
    });

    // JPlayer Video
    jQuery('.blox-element.video .jplayer-video').each(function(){
        var $this = jQuery(this);
        $this.jPlayer({
            ready: function () {
                if( jQuery(this).data('ext') == 'flv' ){
                    jQuery(this).jPlayer("setMedia", {
                        flv: jQuery(this).data('src'),
                        poster: jQuery(this).data('poster')
                    });
                }
                else if( jQuery(this).data('ext') == 'mp4' ){
                    jQuery(this).jPlayer("setMedia", {
                        mp4: jQuery(this).data('src'),
                        poster: jQuery(this).data('poster')
                    });
                }
                else if( jQuery(this).data('ext') == 'm4v' ){
                    jQuery(this).jPlayer("setMedia", {
                        m4v: jQuery(this).data('src'),
                        poster: jQuery(this).data('poster')
                    });
                }
                else if( jQuery(this).data('ext') == 'ogv' ){
                    jQuery(this).jPlayer("setMedia", {
                        ogv: jQuery(this).data('src'),
                        poster: jQuery(this).data('poster')
                    });
                }
                else if( jQuery(this).data('ext') == 'webmv' ){
                    jQuery(this).jPlayer("setMedia", {
                        webmv: jQuery(this).data('src'),
                        poster: jQuery(this).data('poster')
                    });
                }
                else if( jQuery(this).data('ext') == 'webm' ){
                    jQuery(this).jPlayer("setMedia", {
                        webmv: jQuery(this).data('src'),
                        poster: jQuery(this).data('poster')
                    });
                }
                else if( jQuery(this).data('ext') == 'ogg' ){
                    jQuery(this).jPlayer("setMedia", {
                        ogg: jQuery(this).data('src'),
                        poster: jQuery(this).data('poster')
                    });
                }

                jQuery(this).find('.fluid-width-video-wrapper').attr('style', '');
            },
            play: function(){
                jQuery('.blox-element.video .jplayer-video').not(this).jPlayer("pause");
            },
            wmode:"window",
            swfPath: blox_plugin_path+"../../assets/plugins/jplayer/",
            solution: "html, flash",
            cssSelectorAncestor: "#jp_interface_"+jQuery(this).data('pid'),
            supplied: ( jQuery(this).data('ext')=='webm' ? 'webmv' : jQuery(this).data('ext') ),
            preload: "metadata",
            size: {
                width: $this.width(),
                height: parseInt( $this.width()*360/640 )
            }
        });
    });
    

    jQuery('.blox_element_duplicator').each(function(i){
        var $this = jQuery(this);
        $this.waypoint(function(direction){
            if( direction==='down' ){
                $this.find('.duplicator_item').each(function(dindex){
                    var $ditem = jQuery(this);
                    setTimeout(function(){
                        $ditem.css('color', $ditem.attr('data-color'));
                    }, dindex*200);
                });
            }
        },
        {
            offset: function(){
                return jQuery.waypoints('viewportHeight')+100;
            }
        });
    });

    


    jQuery('.pricing-row').each(function(){
        var $this = jQuery(this);
        var column_length = $this.find('.blox_table_row').length>0 ? $this.find('.blox_table_row').eq(0).find('.blox_table_cell').length : 0;
        var html = '';
        for(var i=0; i<column_length; i++){
            var html_col = '';
            var col_class = '';
            var elem_class = '';

            $this.find('.blox_table_row').each(function(){
                var $row = jQuery(this);
                var $cell = $row.find('.blox_table_cell').eq(i);
                var row_type = $row.attr('type')+'' != 'undefined' ? $row.attr('type')+'' : '';

                if( row_type=='header' ){
                    html_col += '<h3 class="plan-name">'+ $cell.html() +'</h3>';
                }
                else if( row_type=='price' ){
                    var price = $cell.html();
                    var parr = price.split(',');
                    var p_number = (typeof parr[0]!=='undefined' ? parr[0] : '');
                    var p_currency = (typeof parr[1]!=='undefined' ? parr[1] : '');
                    var p_duration = (typeof parr[2]!=='undefined' ? ' / '+parr[2] : '');
                    html_col += '<div class="plan-price">'+p_number+p_currency+p_duration+'</div>';
                }
                else if( row_type=='button' ){
                    var btn_txt = $cell.html();
                    var btn_link = '#';
                    var btn_txt_split = btn_txt.split(',');
                    if( btn_txt_split.length > 1 ){
                        btn_txt = btn_txt_split[0];
                        btn_link = btn_txt_split[1];
                    }
                    var button_class = $cell.attr('type')=='highlight' ? 'btn-primary' : 'btn-default';
                    var button_icon = typeof $this.data('button-icon')!=='undefined' && $this.data('button-icon')!='' ? '<i class="'+$this.data('button-icon')+'"></i>' : '';
                    html_col += '<a href="'+btn_link+'" class="btn '+button_class+' btn-sm">'+button_icon+' '+btn_txt+'</a>';
                }
                else{
                    html_col += '<div class="plan-content">'+$cell.html()+'</div>';
                }
                
                if( $cell.attr('type')=='highlight' ){
                    elem_class = 'featured-plan';
                }
                if( $cell.attr('type')=='description' ){
                    elem_class = 'description-column';
                }
            });
            
            if(column_length==2){ col_class = ' col-md-6 col-sm-6'; }
            else if(column_length==3){ col_class = ' col-md-4 col-sm-4'; }
            else if(column_length==4){ col_class = ' col-md-3 col-sm-3'; }
            else if(column_length==5){ col_class = ' col-md-2 col-sm-2'; }
            else{ col_class = ' col-md-12'; }

            elem_class += ' '+$this.data('skin');

            html_col = '<div class="'+col_class+'"> \
                            <div class="blox-element pricing '+ elem_class +'">'+html_col+'</div> \
                        </div>';
            html += html_col;
        }
        if( column_length>0 ){ $this.html(html); }
        $this.css('visibility', 'visible');
    });

    
    

    // post like event
    jQuery('.meta-like a').click(function(){
        var $this = jQuery(this);
        var $id = typeof $this.data('pid') !=='undefined' ? parseInt($this.data('pid')) : 0;
        if( !$this.hasClass('liked') && $id > 0 ){
            var ids = blox_get_cookie('liked');
            ids = ids!=null ? ids : '';
            var array_ids = ids.split(',');
            var exists = false;
            for( var i=0; i<array_ids.length; i++ ){
                if( array_ids[i]+'' == $id+'' ){
                    exists = true;
                }
            }
            if( !exists ){
                blox_set_cookie('liked', ids+','+$id);
                jQuery.post( blox_ajax_url, {'action': 'blox_post_like', 'post_id': parseInt($id) }, function(data){
                    if( data=='1' ){
                        $this.find('span').html( parseInt($this.find('span').html())+1 );
                        $this.addClass('liked');
                    }
                });
            }
        }
    });




    // init testimonials
    jQuery('.blox-testimonial').each(function(){
        var $this = jQuery(this);
		var $nav = jQuery('<div class="quote-pager"></div>');
		$this.append($nav);

        if( $this.find('.quote-wrapper >div').length<2 ){
            $this.find('.quote-pager').hide();
        }

        $this.find('.quote-wrapper').cycle({
            slides: '> div',
            timeout: 5000,
            fx: 'fade',
            pager: $nav,
            autoHeight: 'container',
            swipe: true
        });
    });
    

    /* init Fullwidth Divider */
    jQuery('.divider-fullwidth').each(function(){
        var $this = jQuery(this);
        var $wrapper = jQuery('body section.primary').eq(0);

        $this.css({
            'width' : $wrapper.width(),
            'max-width': $wrapper.width(),
            'left': -($this.offset().left-$wrapper.offset().left)+'px',
            'visibility': 'visible',
            'position': 'relative'
        });

        jQuery(window).resize(function(){
            var $resize_wrapper = jQuery('body section.primary').eq(0);
            $this.css({ 'left': '0px' });
            $this.css({
                'width' : $resize_wrapper.width(),
                'max-width': $resize_wrapper.width(),
                'left': -($this.offset().left-$wrapper.offset().left)+'px',
                'visibility': 'visible'
            });
        });
    });


    /* init Fullwidth row
    ================================================*/
    jQuery('.section-fullwidth').each(function(){
        var $this = jQuery(this);
        var $wrapper = jQuery('body section.primary').eq(0);
        
        $this.css({
            'width' : $wrapper.width(),
            'max-width': $wrapper.width(),
            'left': -($this.offset().left-$wrapper.offset().left)+'px',
            'visibility': 'visible',
            'position': 'relative'
        });
        
        jQuery(window).resize(function(){
            var $resize_wrapper = jQuery('body section.primary').eq(0);
            $this.css({ 'left': '0px' });
            $this.css({
                'width' : $resize_wrapper.width(),
                'max-width': $resize_wrapper.width(),
                'left': -($this.offset().left-$wrapper.offset().left)+'px',
                'visibility': 'visible'
            });
        });

        
        if( $this.hasClass('section-video-wrapper') ){
            $this.find('.section-video video').width( jQuery(window).width() );
            $this.find('.section-video video').height( parseInt( jQuery(window).width()/1.777 ) );
            $this.find('.section-video video').mediaelementplayer({
                pauseOtherPlayers: false
            });
        }


        // Init Columns
        $this.find('.blox-row').each(function(){
            var $row = jQuery(this);
            var col_count = $row.find('> .blox-column-wrapper').length;

            if( col_count>1 ){
                var resizing_columns = function($row){
                    var $first = $row.find('> .blox-column-wrapper').eq(0);
                    var $last = $row.find('> .blox-column-wrapper').eq(col_count-1);
                    var offset_left = jQuery('body').hasClass('boxed') ? $first.offset().left-jQuery('body > .layout-wrapper').offset().left : $first.offset().left;

                    // Resizing Fullwidth column
                    if( jQuery(window).width()>=768 ){
                        // First
                        $first.find('>.blox-column-before').css({ 'margin-left': -offset_left+'px' })
                            .width( $first.width()+parseInt($last.css('padding-left'))+parseInt($last.css('padding-right'))+offset_left );

                        // Last
                        $last.find('>.blox-column-before').css({ 'margin-left':'0px' })
                            .width( $last.width()+parseInt($last.css('padding-left'))+parseInt($last.css('padding-right'))+offset_left );

                        $row.find('> .blox-column-wrapper').height( $row.height() );
                    }
                    else{
                        // First
                        $first.find('>.blox-column-before').css({ 'margin-left': -offset_left+'px' })
                            .width( $first.width()+parseInt($last.css('padding-left'))+parseInt($last.css('padding-right'))+offset_left+offset_left );

                        // Last
                        $last.find('>.blox-column-before').css({ 'margin-left': -offset_left+'px' })
                            .width( $last.width()+parseInt($last.css('padding-left'))+parseInt($last.css('padding-right'))+offset_left+offset_left );;

                        $row.find('> .blox-column-wrapper').css({ 'height':'auto' });
                    }
                };

                resizing_columns($row);

                jQuery(window).resize(function(){
                    resizing_columns($row);
                });
            }
        });


    });

    jQuery(window).on("debouncedresize", function () {
        if( jQuery('.section-video-wrapper').length > 0 ){
            var $width;
            if(jQuery.exists('.mk-boxed-enabled')) {
                $width = jQuery('#mk-boxed-layout').width();
            } else {
                $width = jQuery('body').width();
            }

            jQuery('.mk-section-video video, .mk-section-video .mejs-overlay, .mk-section-video .mejs-container').css({width : $width, height : parseInt($width/1.777)});
            jQuery('.mk-section-video').css('width', $width);
            jQuery('.mk-section-video video, .mk-section-video object').attr({'width' : $width, 'height' : parseInt($width/1.777)});
        }
    });

	if( typeof google!=='undefined' ){
        google.maps.event.addDomListener(window, "load", initGoogleMap);
    }


    jQuery('.blox-tooltip').each(function(){
        var $this = jQuery(this);
        $this.tooltip();
    });


});


function initGoogleMap(){
    /* Google Map
    ================================================*/
    jQuery('.section-fullwidth .blox-gmap').each(function(){
        var $this = jQuery(this);
        var $wrapper = jQuery('body section.primary').eq(0);

        $this.css({
            'width' : $wrapper.width(),
            'max-width': $wrapper.width(),
            'left': -($this.offset().left-$wrapper.offset().left)+'px',
            'visibility': 'visible',
            'position': 'relative'
        });
        
        jQuery(window).resize(function(){
            var $resize_wrapper = jQuery('body section.primary').eq(0);
            $this.css({ 'left': '0px' });
            $this.css({
                'width' : $resize_wrapper.width(),
                'max-width': $resize_wrapper.width(),
                'left': -($this.offset().left-$wrapper.offset().left)+'px',
                'visibility': 'visible'
            });
        });
    });

    jQuery('.blox-gmap').each(function(){
        var $parent = jQuery(this);
        var $this = jQuery(this).find('.google-map');
        var mapid = $this.attr('id');

        var view_type = google.maps.MapTypeId.ROADMAP;
        view_type = $this.data('view-type')=='SATELLITE' ? google.maps.MapTypeId.SATELLITE : view_type;
        view_type = $this.data('view-type')=='TERRAIN' ? google.maps.MapTypeId.TERRAIN : view_type;

        var myLatlng = new google.maps.LatLng( parseFloat($this.data('lat')), parseFloat($this.data('long')) );

        var mapOptions = {
            scrollwheel: false,
            center: myLatlng,
            zoom: parseInt($this.data('zoom'))
        };

        if( $this.data('view-type')=='SATELLITE' || $this.data('view-type')=='TERRAIN' ){
            mapOptions = jQuery.extend({ mapTypeId: view_type }, mapOptions);
        }
        else{
            mapOptions = jQuery.extend({
                mapTypeControlOptions: {
                    mapTypeIds: [view_type, mapid],
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                },
                mapTypeId: mapid
            },mapOptions);
        }

        var map = new google.maps.Map(document.getElementById(mapid), mapOptions);

        /* Pin Options */
        if( $this.data('pin')!='' ){
            var marker = new google.maps.Marker({
                position: myLatlng,
                title: "",
                icon: $this.data('pin')
            });
            marker.setMap(map);
        }

        /* Custom Colored */
        var featureOpts = [];
        var styledMapOptions = {};
        if( $this.data('map-color')!='' && $this.data('view-type')=='ROADMAP' ){
            featureOpts = [{ stylers:[ { hue: $this.data('map-color') }, { saturation: -20 }] }];
            styledMapOptions = { name: 'Google Map' };
        }
        
        if( $this.data('view-type')=='ROADMAP' ){
            var customMapType = new google.maps.StyledMapType(featureOpts, styledMapOptions);
            map.mapTypes.set(mapid, customMapType);
        }
        
    });
}


/* Window Load/All Media Loaded */
jQuery(window).load(function(){

    /*  Isotope Masonry Blog
    ================================================== */
    jQuery('.blog.grid-loop').each(function(){
        var $blog = jQuery(this);//.find('>.row');

        if( $blog.find('.loop-masonry').length ){
            $blog.find('.loop-masonry').masonry({
                itemSelector : '.loop-item'
            });
        }
        else{
            $blog.find('.loop-container').isotope({
                itemSelector : '.loop-item',
                layoutMode: 'fitRows',
            });
        }

    });

    /*  Isotope Masonry Portfolio
    ================================================== */
    jQuery('.grid-loop.portfolio:not(.swiper-wrapper,.fullwidth-portfolio)').each(function(){
        var $this = jQuery(this);
        var $portfolio_masonry = $this.find('.masonry-container');
        var masonry_item = '.post_filter_item';

        if( $this.hasClass('portfolio-masonry') ){
            $portfolio_masonry.masonry({
                itemSelector : masonry_item
            });
        }
        else{
            $portfolio_masonry.isotope({
                itemSelector : masonry_item,
                layoutMode: 'fitRows'
            });
        }


        $this.find('article.entry.hover').each(function(){
            var $title = jQuery(this).find('.entry-title h2');
            $title.css({ 'margin-top': parseInt( jQuery(this).height()/2-60-$title.height()/2 ) });
        });

        jQuery(window).resize(function(){
            $this.find('article.entry.hover').each(function(){
                var $title = jQuery(this).find('.entry-title h2');
                $title.css({ 'margin-top': parseInt( jQuery(this).height()/2-60-$title.height()/2 ) });
            });
        });

        $this.find('.portfolio-filter .dropdown-menu a').click(function(){
            var $filter = jQuery(this);
            var filter = $filter.attr('data-filter');
            
            $this.find('.portfolio-filter h3').html( $filter.html() );
            filter = filter=='all' ? '*' : '.'+filter;
            $portfolio_masonry.isotope({ filter: filter });
        });

    });
    
    

});



function blox_date_diff(date1, date2) {
    date1.setHours(0);
    date1.setMinutes(0, 0, 0);
    date2.setHours(0);
    date2.setMinutes(0, 0, 0);
    var datediff = Math.abs(date1.getTime() - date2.getTime()); // difference 
    return parseInt(datediff / (24 * 60 * 60 * 1000), 10); //Convert values days and return value      
}


function blox_set_cookie(c_name,value,exdays){
    exdays = typeof exdays!=='undefined' ? exdays : 1;
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
    document.cookie=c_name + "=" + c_value;
}

function blox_get_cookie(c_name){
    var c_value = document.cookie;
    var c_start = c_value.indexOf(" " + c_name + "=");
    if (c_start == -1){
        c_start = c_value.indexOf(c_name + "=");
    }
    if (c_start == -1){
        c_value = null;
    }
    else{
        c_start = c_value.indexOf("=", c_start) + 1;
        var c_end = c_value.indexOf(";", c_start);
        if (c_end == -1){
            c_end = c_value.length;
        }
        c_value = unescape(c_value.substring(c_start,c_end));
    }
    return c_value;
}



