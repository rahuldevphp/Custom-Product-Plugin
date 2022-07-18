 jQuery(document).ready(function () {

    jQuery('#message_new').hide();
       jQuery( "#cp_form" ).on( "submit", function(e) {
 
            var form_data = jQuery(this).serialize();
         
            jQuery.ajax({
              	type: "POST",
             //	dataType: "json",
             	url: my_ajax_object.ajax_url,
              	data: form_data,
              	success: function (data) {
                    const myObj = JSON.parse(data);               
                    var len = myObj.msg.length

                    if ( myObj.status == true) {

                        jQuery('#message_new').show();
                        jQuery('#message_new').empty();
                        var i;
                        for (i = 0; i < len; ++i) {
                            jQuery('#message_new').removeClass('error notice').addClass('updated notice');
                            jQuery('#message_new').append('<p>'+myObj.msg[i]+'</p>');
                        }
                        setTimeout(function(){
                            var url = window.location.href;
                            if (url.indexOf("?")>-1){
                                url = url.substr(0,url.indexOf("?"));
                                url+="?page=custom_product";
                                location.replace(url);
                            }
                        }, 300); 
                        
                        
                    }else{
      
                        jQuery('#message_new').show();
                        jQuery('#message_new').empty();
                        var i;
                        for (i = 0; i < len; ++i) {
                            jQuery('#message_new').addClass('error notice');
                            jQuery('#message_new').append('<p>'+myObj.msg[i]+'</p>');
                            console.log(myObj.msg[i]);
                        }

                    }
              }
            });
         
            e.preventDefault();
          });
    });