(function() {

	if( typeof tinymce!=='undefined' ){

		if( typeof(tinymce.majorVersion)!=='undefined' && parseInt(tinymce.majorVersion)<4 ){
			tinymce.create("tinymce.plugins.themeton_shortcode", {
					init: function(ed,e) { },
					createControl:function(d,e){
						var ed = tinymce.activeEditor;

						if(d=="themeton_shortcode"){

							d=e.createMenuButton( "themeton_shortcode",{
								title: "Themeton Shortcode",
								icons: false
								});

								var a=this;d.onRenderMenu.add(function(c,b){
									
									a.addImmediate(b, "Alert Box", '[blox_notification title="" type="alert-success" alignment="left" skin="boxed"]Content[/blox_notification]' );
									a.addImmediate(b, "Button", '[blox_button text="" link="#" target="_self" button_type="btn-default" icon="" size="btn-md" /]' );
									a.addImmediate(b, "Divider", '[blox_divider type="space" space="5" /]' );
									a.addImmediate(b, "Dropcap", '[blox_dropcap]Character[/blox_dropcap]' );
									a.addImmediate(b, "Highlight Text", '[blox_highlight type="default|primary|success|info|warning|danger"]Text[/blox_highlight]' );
									a.addImmediate(b, "Icon", '[blox_icon icon="fa-user" color="#1e73be" /]' );
									a.addImmediate(b, "Iconic List", '[blox_list title="Title" icon="fa-thumbs-o-up" color="#1e73be"]<ul><li>item 1</li><li>item 2</li></ul>[/blox_list]' );
									a.addImmediate(b, "Tooltip", '[blox_tooltip tooltip="Tooltip text"]Text[/blox_tooltip]' );



									b.addSeparator();

									c=b.addMenu({title: "Media Shortcodes"});
											a.addImmediate(c, "Audio", '[blox_audio title="" image=""]audio_url_or_embed[/blox_audio]' );
											a.addImmediate(c, "Video", '[blox_video title="" image=""]video_url_or_embed[/blox_video]' );

								});
							return d

						} 

						return null
					},
					addImmediate:function(d,e,a){d.add({title:e,onclick:function(){tinyMCE.activeEditor.execCommand( "mceInsertContent",false,a)}})}
				}
			);

			tinymce.PluginManager.add( "themeton_shortcode", tinymce.plugins.themeton_shortcode);
		}
		else{
			tinymce.PluginManager.add('themeton_shortcode', function(editor, url) {

				editor.addButton('themeton_shortcode', {
		            type: 'menubutton',
		            text: 'TT Shortcode',
		            icon: false,
		            menu: [
		                { text: "Alert Box", onclick: function(){
	                		editor.insertContent('[blox_notification title="" type="alert-success" alignment="left" skin="boxed"]Content[/blox_notification]');
	                	}},
	                	{ text: "Button", onclick: function(){
	                		editor.insertContent('[blox_button text="" link="#" target="_self" button_type="btn-default" icon="" size="btn-md" /]');
	                	}},
	                	{ text: "Divider", onclick: function(){
	                		editor.insertContent('[blox_divider type="space" space="5" /]');
	                	}},
	                	{ text: "Dropcap", onclick: function(){
	                		editor.insertContent('[blox_dropcap]Character[/blox_dropcap]');
	                	}},
	                	{ text: "Highlight Text", onclick: function(){
	                		editor.insertContent('[blox_highlight type="default|primary|success|info|warning|danger"]Text[/blox_highlight]');
	                	}},
	                	{ text: "Icon", onclick: function(){
	                		editor.insertContent('[blox_icon icon="fa-user" color="#1e73be" /]');
	                	}},
	                	{ text: "Iconic List", onclick: function(){
	                		editor.insertContent('[blox_list title="Title" icon="fa-thumbs-o-up" color="#1e73be"]<ul><li>item 1</li><li>item 2</li></ul>[/blox_list]');
	                	}},
	                	{ text: "Tooltip", onclick: function(){
	                		editor.insertContent('[blox_tooltip tooltip="Tooltip text"]Text[/blox_tooltip]');
	                	}},
	                	{ 
	                		text: "Media Shortcodes", onclick: function(){},
	                		menu: [
	                			{ text: "Audio", onclick: function(){
			                		editor.insertContent('[blox_audio title="" image=""]audio_url_or_embed[/blox_audio]');
			                	}},
			                	{ text: "Video", onclick: function(){
			                		editor.insertContent('[blox_video title="" image=""]video_url_or_embed[/blox_video]');
			                	}}
	                		]
	                	}
		            ]
		            
		        });
			});
		}

	}

})();