String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g, '');};


/*
 * GET UNIQUE ID
 */
function guid_temp(){
	return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
}
function guid(){
	return (guid_temp()+guid_temp());
}





/*
	Font Icon Dialog Public
	Usage: themeton_get_font( jQuery(this).parent().find('input') );
*/
function themeton_get_font($obj){
	jQuery.fancybox.open([jQuery('<div id="themeton_modal" />')],{
		width: 600,
		height: 500,
		autoSize: false,
		afterLoad: function(){
			$awesome = '';
			for(i=0; i<font_awesome.length; i++){
				$awesome += '<a href="javascript:;" class="fa '+font_awesome[i]+'"></a>';
			}
			html = '<div id="themeton_modal_font_selector"><input type="text" id="tt_icon_searcher" placeholder="Search icon..." /></div>';
			html += '<div id="themeton_modal_font"><div>'+$awesome+'</div></div>';
			this.content = html;
		},
		afterShow: function(){
			
			jQuery('#themeton_modal_font a').unbind('click')
				.click(function(){
					var cls = jQuery(this).attr('class');
					cls = cls.replace('fa ', '');
					$obj.val( cls );
					$obj.change();
					jQuery.fancybox.close();
				});


			jQuery('#tt_icon_searcher').bind("keyup change", function(e){
				var $input = jQuery(this);
				if( this.value.length > 0 ){
					jQuery('#themeton_modal_font a').each(function(){
						var attr_class = jQuery(this).attr('class');
						if( attr_class.indexOf($input.val()) < 0 ){
							jQuery(this).hide();
						}
						else{
							jQuery(this).show();
						}
					});
				}
				else{
					jQuery('#themeton_modal_font a').show();
				}
			});
		}
	});
}




function template_hide_admin_notice(){
	jQuery('#theme-admin-notice').slideUp();
	jQuery.post( ajaxurl, {'action':'template_hide_admin_notice'}, function(data){
		
	});
}