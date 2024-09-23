jQuery(document).ready(function($){
    jQuery('.wdk-tabs .tab').on('click', function(e){
        e.preventDefault();
        jQuery('.wdk_tab_mobile, .wdk-tabs .tab').removeClass('active')
        jQuery('.wdk_tab_mobile.'+jQuery(this).attr('data-target')).addClass('active')
        jQuery(this).addClass('active')
    })
});