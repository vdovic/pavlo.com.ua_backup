

function dbte_installFieldType(folder){

    jQuery('#button_'+folder).html('Processing');
    ajaxCall('dbte_installFieldType', folder, function(i){
        jQuery('#button_'+folder).html(i.button);
        jQuery('#button_'+folder).removeClass('button');
        jQuery('#button_'+folder).removeClass('button-primary');
        jQuery('#button_'+folder).addClass(i.styleClass);
    });

}

function dbte_installProcessor(folder){

    jQuery('#button_'+folder).html('Processing');
    ajaxCall('dbte_installProcessor', folder, function(i){
        jQuery('#button_'+folder).html(i.button);
        jQuery('#button_'+folder).removeClass('button');
        jQuery('#button_'+folder).removeClass('button-primary');
        jQuery('#button_'+folder).addClass(i.styleClass);
    });

}

function dbte_installViewProcessor(folder){

    jQuery('#button_'+folder).html('Processing');
    ajaxCall('dbte_installViewProcessor', folder, function(i){
        jQuery('#button_'+folder).html(i.button);
        jQuery('#button_'+folder).removeClass('button');
        jQuery('#button_'+folder).removeClass('button-primary');
        jQuery('#button_'+folder).addClass(i.styleClass);
    });

}