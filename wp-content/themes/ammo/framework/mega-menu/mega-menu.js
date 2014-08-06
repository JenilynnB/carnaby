var wpActiveEditor = null;

jQuery(function(){
	// init color picker
	initMenuFields();
	
	jQuery('#button_metro_menu').click(function(){
		value = '0';
		if( jQuery('#metro_menu').is(':checked') ){
			value = '1';
		}
		$spinner = jQuery(this).parent().find('.spinner');
		$spinner.show();
		jQuery.post( ajaxurl,
			{ action: 'update_metro_menu_settings', metro_menu: value },
			function(data){
	            if( data != "-1" ){
	            	//console.log('success. saved metro option');
	            }
	            else{
	            	//console.log(data);
	            }
	            $spinner.hide();
			});
		
	});
});


function initMenuFields(){
	
	jQuery('.browse_font_icon').unbind('click')
		.click(function(){
			themeton_get_font( jQuery(this).parent().find('input') );
		});

	jQuery('.browse_image').unbind('click')
		.click(function(){
			var $this = jQuery(this);
			var send_attachment_bkp = wp.media.editor.send.attachment;
			wp.media.editor.send.attachment = function(props, attachment){
				wp.media.editor.send.attachment = send_attachment_bkp;
				$this.parent().find('input').val(attachment.url);
			}
			wp.media.editor.open();
			return false;
		});


	jQuery('#menu-to-edit').find('>li').each(function(){
		var $li = jQuery(this);
		var $mega = $li.find('.edit-menu-item-activemega');
		var mega_txt = ' <b style="color:#000; font-size:11px; font-weight:normal;">(Mega Menu)</b>';
		var col_txt = ' <b style="color:#000; font-size:11px; font-weight:normal;">(Column)</b>';

		var curindex = jQuery('#menu-to-edit').find('>li').index($li);
		var lilength = jQuery('#menu-to-edit').find('>li').length;


		if( $mega.is(':checked') ){
			$li.find('.item-type').html( ($li.find('.item-type').html()+'').replace(mega_txt,'') + mega_txt );
		}

		if( $li.hasClass('menu-item-depth-0') ){
			if( curindex+1 < lilength ){
				for (var i = curindex+1; i < lilength; i++) {
					var $curobj = jQuery('#menu-to-edit').find('>li').eq(i);
					if( $curobj.hasClass('menu-item-depth-1') ){
						if( $mega.is(':checked') ){
							$curobj.find('.item-type').html( ($curobj.find('.item-type').html()+'').replace(col_txt,'') + col_txt );
						}
						else{
							$curobj.find('.item-type').html( ($curobj.find('.item-type').html()+'').replace(col_txt,''));
						}
					}

					if( $curobj.hasClass('menu-item-depth-0') ){
						break;
					}
				}
			}
		}


	});
	

	jQuery('#menu-to-edit').find('>li').find('.edit-menu-item-activemega').unbind('change').change(function(){
		var $li = jQuery(this).parent().parent().parent().parent();
		var mega_txt = ' <b style="color:#000; font-size:11px; font-weight:normal;">(Mega Menu)</b>';
		var col_txt = ' <b style="color:#000; font-size:11px; font-weight:normal;">(Column)</b>';

		var curindex = jQuery('#menu-to-edit').find('>li').index($li);
		var lilength = jQuery('#menu-to-edit').find('>li').length;

		if( this.checked ){
			$li.find('.item-type').html( ($li.find('.item-type').html()+'').replace(mega_txt,'') + mega_txt );
		}
		else{
			$li.find('.item-type').html( ($li.find('.item-type').html()+'').replace(mega_txt,''));
		}

		if( $li.hasClass('menu-item-depth-0') ){
			if( curindex+1 < lilength ){
				for (var i = curindex+1; i < lilength; i++) {
					var $curobj = jQuery('#menu-to-edit').find('>li').eq(i);
					if( $curobj.hasClass('menu-item-depth-1') ){
						if( this.checked ){
							$curobj.find('.item-type').html( ($curobj.find('.item-type').html()+'').replace(col_txt,'') + col_txt );
						}
						else{
							$curobj.find('.item-type').html( ($curobj.find('.item-type').html()+'').replace(col_txt,''));
						}
					}

					if( $curobj.hasClass('menu-item-depth-0') ){
						break;
					}
				}
			}
		}

	});
	
	jQuery('#menu-to-edit').on( "sortstop", function( event, ui ) {
		setTimeout(function(){

			jQuery('#menu-to-edit').find('>li').each(function(){
				var $li = jQuery(this);
				var $mega = $li.find('.edit-menu-item-activemega');
				var mega_txt = ' <b style="color:#000; font-size:11px; font-weight:normal;">(Mega Menu)</b>';
				var col_txt = ' <b style="color:#000; font-size:11px; font-weight:normal;">(Column)</b>';

				var curindex = jQuery('#menu-to-edit').find('>li').index($li);
				var lilength = jQuery('#menu-to-edit').find('>li').length;

				if( $mega.is(':checked') ){
					$li.find('.item-type').html( ($li.find('.item-type').html()+'').replace(mega_txt,'') + mega_txt );
				}
				else{
					if( $li.hasClass('menu-item-depth-0') ){
						$li.find('.item-type').html( ($li.find('.item-type').html()+'').replace(mega_txt,'') );
						$li.find('.item-type').html( ($li.find('.item-type').html()+'').replace(col_txt,'') );
					}
				}

				if( $li.hasClass('menu-item-depth-0') ){
					if( curindex+1 < lilength ){
						for (var i = curindex+1; i < lilength; i++) {
							var $curobj = jQuery('#menu-to-edit').find('>li').eq(i);
							if( $curobj.hasClass('menu-item-depth-1') ){
								if( $mega.is(':checked') ){
									$curobj.find('.item-type').html( ($curobj.find('.item-type').html()+'').replace(col_txt,'') + col_txt );
								}
								else{
									$curobj.find('.item-type').html( ($curobj.find('.item-type').html()+'').replace(col_txt,''));
								}
							}

							if( $curobj.hasClass('menu-item-depth-0') ){
								break;
							}
							else if( !$curobj.hasClass('menu-item-depth-1') ){
								$curobj.find('.item-type').html( ($curobj.find('.item-type').html()+'').replace(mega_txt,''));
								$curobj.find('.item-type').html( ($curobj.find('.item-type').html()+'').replace(col_txt,''));
							}
						}
					}
				}


			});

		}, 500);

	} );

}
