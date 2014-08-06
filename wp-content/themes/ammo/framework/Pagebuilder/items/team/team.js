
var team_attrs = ['image', 'ratio', 'link', 'member_name', 'position', 'social', 'skin', 'animation', 'extra_class', 'visibility'];

function get_blox_element_team($content, $attrs){
    
    return '<div class="blox_item blox_team" '+($attrs!=undefined ? $attrs : '')+'> \
                <div class="blox_item_title"> \
                    <i class="fa-user"></i> \
                    <span class="blox_item_title">Team Member</span> \
                    '+ get_blox_actions() +' \
                </div> \
                <div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
			</div>';
}


function parse_shortcode_team($content){
    $content = wp.shortcode.replace( 'blox_team', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        })
		
        return get_blox_element_team( data.content, attrs );
    });
    return $content;
}

function revert_shortcode_team($content){
    $content.find('.blox_team').each(function(){
        attr = '';
        var temp_val = '';

        for (var i = 0; i < team_attrs.length; i++) {
            temp_val = jQuery(this).attr(team_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ team_attrs[i] +'="'+ temp_val +'"';
            }
        }

        jQuery(this).replaceWith('[blox_team'+attr+']'+jQuery(this).find('> .blox_item_content').html()+'[/blox_team]');
    });
    return $content;
}


function add_event_blox_element_team(){
	
    jQuery('.blox_team').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){

            var $social_values = (typeof $this.attr('social')!=='undefined') ? $this.attr('social').replace(/,/g, '\n') : '';

            var form_element = [
            {
                type: 'image',
                id: 'team_member_img',
                label: 'Image',
                value: $this.attr('image')

            },
            {
                type: 'select',
                id: 'team_member_ratio',
                label: 'Image Aspect ratio',
                value: $this.attr('ratio'),
                options: [
                    {value: '1x1',label: '1:1 - Square'},
                    {value: '4x3',label: '4:3 - Landscape'},
                    {value: '4x5',label: '4:5 - Portrait'},
                    {value: '2x3',label: '2:3 - Portrait'},
                    {value: '3x4',label: '3:4 - Portrait'}
                ]
            },
            {
                type: 'input',
                id: 'team_member_img_link',
                label: 'Image link (optional)',
                value: $this.attr('link')
            },
            {
                type: 'input',
                id: 'team_member_name',
                label: 'Name',
                value: $this.attr('member_name')
            },
            {
                type: 'input',
                id: 'team_member_position',
                label: 'Position',
                value: $this.attr('position')

            },
            {
                type: 'textarea',
                id: 'team_member_bio',
                label: 'Bio Text',
                value: $this.find('.blox_item_content').html()

            },
            {
                type: 'textarea',
                id: 'team_member_social_links',
                label: 'Social links',
                value: $social_values,
                description: "Format have to same as <strong>socialname</strong> + <strong>:</strong> (colon) + <strong>link</strong> + line break. Ex:<br><br><em>facebook: facebook.com/yourprofile<br>twitter: twitter.com/yourpage</em><br><br>You should visit <a href='http://themeton.freshdesk.com/solution/articles/152103-team-member-element' target='_blank'>this link</a> and see how many socials supports it and how you can extend your custom socials here."
            }
            ];

            show_blox_form('Edit Team', form_element, function($form){
                $this.attr('image', jQuery('#team_member_img').val() );
                $this.attr('ratio', jQuery('#team_member_ratio').val() );
                $this.attr('link', jQuery('#team_member_img_link').val() );
                $this.attr('member_name', jQuery('#team_member_name').val() );
                $this.attr('position', jQuery('#team_member_position').val() );
                $this.attr('social', (jQuery('#team_member_social_links').val()+'').replace(/\n/g, ',') );
                $this.find('.blox_item_content').html(jQuery('#team_member_bio').val());
            },
            {
                target: $this,
                extra_field: true,
                skin: true,
                visibility: true
            });

        });
			
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_team();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}
