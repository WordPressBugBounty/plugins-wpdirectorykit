jQuery(document).ready(function($){
    //wdk_ajax_loading_listings();
});


const wdk_popup_listings = ($url = '') => {
    var $ = jQuery,
    theme_jc = null;

    $('.wdk_listing_popup').off().on('click', function(e){
        e.preventDefault();

        /*
        if($(e.target).closest('.slick-arrow').length){
            return false
        }
        if($(e.target).closest('.wdk-favorites-actions').length){
            return false
        }
        if($(e.target).closest('.wdk-compare-listing-button-actions').length){
            return false
        }
        */

        var url = $(this).attr('data-listing_url');

        if(theme_jc)
            theme_jc.close();

        theme_jc = $.confirm({
            backgroundDismiss: true, // this will just close the modal
            columnClass: 'col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1 listing_popup', 
            title: false,
            boxWidth: '1200px',
            useBootstrap: false,
            buttons: {
                cancel: {
                  text: 'Close',
                  action: function() {}
                },
                close_btn: {
                  text: '<svg viewBox="0 0 24 24" class="sc-ikHGee ebuEaN sc-hAQmFe iTHtnb" xmlns="http://www.w3.org/2000/svg" style="font-size: 24px;color: rgb(255, 255, 255);height: 24px;width: 24px;"><path d="M11.8 11.8L4 4l7.8 7.8L4 19.6l7.8-7.8zm0 0l7.8 7.8-7.8-7.8L19.6 4l-7.8 7.8z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path></svg>',
                  btnClass: 'btn-close',
                  action: function() {}
                }, 
            },
            content: function () {
                var self = this;
                return $.ajax({
                    url: url,
                }).done(function (response) {

                    // Find all <script> elements in the data and remove them if the src contains "jquery"
                    var tempContainer = jQuery("<div>").html(response);
                    tempContainer.find("script").each(function() {
                        var src = jQuery(this).attr("src");
                        if (src && src.toLowerCase().includes("jquery")) {
                            jQuery(this).remove(); // Remove the script with "jquery" in the src attribute
                        }
                        if (src && src.toLowerCase().includes("leaflet")) {
                            jQuery(this).remove(); // Remove the script with "jquery" in the src attribute
                        }
                    });
                
                    self.setContent(tempContainer);

                }).fail(function(){
                    self.setContent('Something went wrong.');
                });
            }
        });
        return false;
    })
}

