var themetonmgamenu = {
	build: function(menu){
		jQuery(document).ready(function($){
			var $main_ul = $(menu + ">ul");
			var $main_li = $main_ul.find('ul').parent();

			$main_li.each(function(i){
				var $this = $(this);

				$this.hover(
					function(){
						var $targetul = $(this).children("ul:eq(0)");
						var target_width = parseInt($targetul.parent().outerWidth()/2);

						if( $targetul.find('.menu-column').length > 0 ){
							$targetul.find('>li').addClass('row');
							$targetul.find('>li').css({ 'display':'block', 'width':'100%' });
							var col_count = parseInt(12/$targetul.find('.menu-column').length);
							$targetul.find('.menu-column').addClass('col-md-'+col_count+' col-sm-'+col_count);
							$targetul.width( $targetul.find('.menu-column').length*180 );
							
							// mega menu set left pos, arrow pos
							var t_left = parseInt(($targetul.find('.menu-column').length*180-target_width)/2);
							$targetul.css({ 'left': '-'+t_left+'px' });


							if( $targetul.parent().hasClass('fullwidth') ){
								var wpadin = parseInt(($(window).width() - $('#header > .container').width())/2);
								var lileft = $targetul.parent().offset().left;
								
								$targetul.css({
									'left': '-'+(lileft-wpadin)+'px',
									'width': $('#header > .container').width()+'px'
								});

							}
							else{
								var lileft = parseInt($targetul.parent().offset().left);
								if( $(window).width() < $targetul.width()/2+lileft ){
									var pos_dif = $targetul.width()/2+lileft - $(window).width();
									pos_dif = parseInt( pos_dif );
									$targetul.css({ 'left': '-'+(parseInt($targetul.width()/2) + pos_dif+target_width)+'px' });

								}
							}

							$targetul.find('.menu-column').each(function(){
								jQuery(this).height('auto');
							});
							$targetul.find('.menu-column').each(function(mc_index){
								if( mc_index==0 ){
									jQuery(this).height( jQuery(this).parent().parent().outerHeight()-30 );
								}
								else{
									jQuery(this).height( $targetul.find('.menu-column').eq(0).height() );
								}
							});

						}
						else{
							var lileft = parseInt($targetul.parent().offset().left);
							if( $(window).width() < $targetul.width()*2+lileft ){
								var pos_dif = $targetul.width()/2+lileft - $(window).width();
								pos_dif = parseInt( pos_dif );
								$targetul.css({ 'left': '-'+(parseInt($targetul.width()/2) + pos_dif+target_width)+'px' });


								$targetul.addClass('float-right-menu');
							}
						}


						// calculate Submenu Padding-Top
						if( $('.wide_menu').length>0 ){ }
						else{
							var sub_top = parseInt(jQuery('#header').css('padding-bottom')) + parseInt((jQuery('#header > .container').outerHeight()-jQuery('.mainmenu').parent().outerHeight())/2+jQuery('.mainmenu').parent().outerHeight());
							jQuery('.mainmenu ul.menu > li > ul').css({
								'padding-top': sub_top+'px'
							});

							//var stuck = jQuery('#header').hasClass('stuck');
							jQuery(window).scroll(function(){
								var sub_top = parseInt(jQuery('#header').css('padding-bottom')) + parseInt((jQuery('#header > .container').outerHeight()-jQuery('.mainmenu').parent().outerHeight())/2+jQuery('.mainmenu').parent().outerHeight());
								jQuery('.mainmenu ul.menu > li > ul').css({
									'padding-top': sub_top+'px'
								});
							});
						}


						$targetul.fadeIn('fast');
					},
					function(){
						var $targetul = $(this).children("ul:eq(0)");
						$targetul.fadeOut('fast');
					}
				);
			});
		});
	}
}

themetonmgamenu.build('.main-menu .navmenu-cell');




