jQuery(document).ready(function(){

    jQuery("#post-body-content").prepend("<div id='pricing_error' class='error' style='display:none' ></div>");

    jQuery('#post').submit(function() {

        if(jQuery("#post_type").val() =='pricing_packages'){
            return wppt_validate_pricing_packages();
        }else if(jQuery("#post_type").val() =='pricing_tables'){
            return wppt_validate_pricing_tables();
        }


    });



    jQuery("#add_features").click(function(){

        var feature = jQuery("#package_feature").val();

        if(feature == ''){

        }else{
            jQuery("#package_features_box").append("<li><input type='hidden' value='"+feature+"' name='package_features[]' />"
                +feature+"<a href='javascript:void(0);'>Delete</a></li>");
        }

    });


    jQuery("#package_features_box a").click(function(){
        jQuery(this).parent().remove();
    });


});

var wppt_validate_pricing_packages = function(){
    var err = 0;
    jQuery("#pricing_error").html("");
    jQuery("#pricing_error").hide();

    if(jQuery("#title").val() == ''){
        jQuery("#pricing_error").append("<p>Please enter Package Name.</p>");
        err++;
    }
    if(jQuery("#package_price").val() == ''){
        jQuery("#pricing_error").append("<p>Please enter Package Price.</p>");
        err++;
    }
    if(jQuery("#package_buy_link").val() == ''){
        jQuery("#pricing_error").append("<p>Please enter Package Buy Link.</p>");
        err++;
    }

    if(err>0){
        jQuery("#publish").removeClass("button-primary-disabled");
        jQuery("#ajax-loading").hide();
        jQuery("#pricing_error").show();
        return false;
    }else{
        return true;
    }
};

var wppt_validate_pricing_tables = function(){
    var err = 0;
    jQuery("#pricing_error").html("");
    jQuery("#pricing_error").hide();

    if(jQuery("#title").val() == ''){
        jQuery("#pricing_error").append("<p>Please enter Pricing Table Name.</p>");
        err++;
    }
    if(err>0){
        jQuery("#publish").removeClass("button-primary-disabled");
        jQuery("#ajax-loading").hide();
        jQuery("#pricing_error").show();
        return false;
    }else{
        return true;
    }
};
