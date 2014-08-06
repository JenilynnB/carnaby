jQuery(function(){
	
	jQuery("#blox_settings_form").submit(function(){ 
        jQuery.post(jQuery(this).attr("action"), jQuery(this).serialize(), function(data){
        	alert(data);
            if( data == -1 ){
                //alert("error");
            }else{
                //alert("success");
            }
        });

        return false;
    });
    
});
