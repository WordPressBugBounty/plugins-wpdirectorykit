(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

    $(function () {
        $('.wdk-click-load-animation').on('click', function () {
            $(this).find('.fa-ajax-indicator').css('display', 'inline-block');
            
            if ($(this).closest('form').length) {
                if (!$(this).closest('form')[0].checkValidity()) {
                    $(this).find('.fa-ajax-indicator').css('display', 'none');
                }
            }
        });

        $('.wdk-form-animation').on('submit', function () {
            var form, indicator;
            form = $(this);
            indicator = form.find('.fa-ajax-indicator');
            indicator.css('display', 'inline-block');

            if(form[0].checkValidity())
                indicator.css('display', 'none');
        });

        
        $('.wdk-submit-loading').on('click', function () {
            var form;
            form = $(this).closest('form');

            if( $(this).hasClass('wdk_btn_load_indicator')) {
                console.log('disabled')
                return false;
            }

            $(this).addClass('wdk_btn_load_indicator disabled');
        });
        
        $('.wdk-click-loading').on('click', function (e) {

            if( $(this).hasClass('wdk_btn_load_indicator')) {
                return false;
            }
            $(this).addClass('wdk_btn_load_indicator disabled');
        });

        const wdk_start_search = (form) => {
            var url,scrollTo, data = {};
            url = form.attr('action').replace(/#results/, '');
            if (url.indexOf('?') == -1) {
                url += '?';
            } else {
                url += '&';
            }
            var str_parameters = "";
            $.each($("form.wdk-search-form:visible").serializeArray(), function (i, k) {
                if (k.value != '' && k.name.indexOf('skip') == -1) {
                    if (str_parameters != "") {
                        str_parameters += "&";
                    }
                    str_parameters += k.name + "=" + encodeURIComponent(k.value); 
                }
            });

            $.each($("form.wdk-search-form.map").serializeArray(), function (i, k) {
                if (k.value != '' && k.name.indexOf('skip') == -1) {
                    if (str_parameters != "") {
                        str_parameters += "&";
                    }
                    str_parameters += k.name + "=" + encodeURIComponent(k.value); 
                }
            });

            $.each($(".wdk-search-popup .toggle-btn:visible"), function (i, k) {
                let el = $($(this).attr('data-wdk-target'));
                if(el && el.length) {
                    $.each(el.find('.wdk-search-form').serializeArray(), function (i, k) {
                        if (k.value != '' && k.name.indexOf('skip') == -1) {
                            if (str_parameters != "") {
                                str_parameters += "&";
                            }
                            str_parameters += k.name + "=" + encodeURIComponent(k.value); 
                        }
                    });
                }
            });

            /* view_type */
            if($('.wmvc-view-type .nav-link.active').length) {
                str_parameters += "&wmvc_view_type="+$('.wmvc-view-type .nav-link.active').attr('data-id'); 
            } 

            /* order by */
            if($('.wdk-order').val() != '' && $('.wdk-order').val()) {
                str_parameters += "&order_by="+$('.wdk-order').val(); 
            } 

            if ($('.wdk-search-form[data-scrollto]').length)
                scrollTo = '#'+$('.wdk-search-form[data-scrollto]').attr('data-scrollto');
                


            var setCookie = (cname, cvalue, exdays) => {
                var d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                var expires = "expires="+d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }
            setCookie('wdk_last_search', str_parameters);

            return url+str_parameters+scrollTo;
        }

        /* set current page for search results if exists results widget and enabled in setttings*/
        $('form.wdk-search-form').each(function() {
            var $form = $(this);
            if ($('.wdk-listings-results').length && $('.wdk-listings-results#results').length && $form.attr('data-current-link')) {
                $form.attr('action', $form.attr('data-current-link'));
            }
        });

        $("form.wdk-search-form").on('submit', function (e) {
            e.preventDefault();
            var url = wdk_start_search($(this));
            $(this).closest('form').addClass('loading')
            if (decodeURI(window.location.href) == decodeURI(url)) {
                window.location.reload();
            } else {
                window.location.href = url;
            }
            return false;
        });

        $("form.wdk-search-form.auto_search").find('input, select').on('input', function (e) {
            $("form.wdk-search-form").first().trigger('submit')
        });

        $("form.wdk-search-form .wdk-search-start:not(.wdk-search-reset)").on('click', function (e) {
            e.preventDefault();
            var url = wdk_start_search($(this).closest('form'));
            $(this).closest('form').addClass('loading')

            if($(this).closest('.ajax_results_enabled').length) {
                if (typeof wdk_ajax_loading_listings == 'function') {
                    wdk_ajax_loading_listings(url);
                }
            } else if (decodeURI(window.location.href) == decodeURI(url)) {
                window.location.reload(url);
            } else {
                window.location.href = url;
            }
            return false;
        })

        $('.wdk-listings-results.ajax_results_enabled .wdk-pagination .page-numbers').on('click', function (e) {
            e.preventDefault();
            if (typeof wdk_ajax_loading_listings == 'function') {
                wdk_ajax_loading_listings($(this).attr('href'));
            }
        })

        /* elementor popup */
        jQuery( document ).on( 'elementor/popup/show', () => {
            wdk_reset_form();
        } );

        const wdk_reset_form = () => {
            $("form.wdk-search-form .wdk-search-reset").off().on('click', function (e) {
                e.preventDefault();
                let this_form = jQuery(this).closest('form');
                jQuery('input:not([type="checkbox"]):not([name="element_id"]):not([type="radio"]):not([type="hidden"]),textarea,select').val('');
                jQuery('select').val(jQuery(this).find('option:first').val())
                jQuery('input[type="checkbox"]').prop('checked', false); 
                jQuery('input[type="radio"]').prop('checked', false); 
                jQuery('.wdk-field.LOCATION .wdk_dropdown_tree button:first-child').html(jQuery('.wdk-field.LOCATION .list_items li:first').text()); 
                jQuery('.wdk-field.CATEGORY .wdk_dropdown_tree button:first-child').html(jQuery('.wdk-field.CATEGORY .list_items li:first').text()); 
                jQuery('input[name="rectangle_ne"],input[name="rectangle_sw"] ').val('');

                // Reset Select2 fields with .select_ajax class
                jQuery('.select_ajax').each(function() {
                    if (typeof jQuery(this).select2 === 'function') {
                        jQuery(this).val(null).trigger('change'); // Clear selection and notify Select2
                    }
                }); 

                $('.wdk-slider-range-field').each(function () {

                    var instance = $(this).data('fieldSliderRange');
                
                    if(instance) {
                        instance.reset();
                    }
                
                });

                // Remove all query params starting with 'search_' or 'field_' and hash '#results' from the URL on reset
                if (window.history && window.history.replaceState) {
                    let url = window.location.href;
                    let parser = document.createElement('a');
                    parser.href = url;

                    // Get query string and hash
                    let query = parser.search.replace(/^\?/, '');
                    let hash = parser.hash;

                    // Split query into key-value pairs
                    let params = query ? query.split('&') : [];
                    let filteredParams = [];

                    // Remove params whose key starts with 'search_' or 'field_'
                    params.forEach(function(param) {
                        let key = param.split('=')[0];
                        if (!(key.startsWith('search_') || key.startsWith('field_'))) {
                            filteredParams.push(param);
                        }
                    });

                    // Rebuild the URL without the removed params and hash
                    let newQuery = filteredParams.length ? '?' + filteredParams.join('&') : '';
                    let newUrl = parser.protocol + '//' + parser.host + parser.pathname + newQuery;

                    // Remove hash if it is '#results'
                    if (hash && hash !== '#results') {
                        newUrl += hash;
                    }

                    window.history.replaceState({}, document.title, newUrl);
                }

                if(this_form.hasClass('auto_search')) {
                    $("form.wdk-search-form").first().trigger('submit')
                }
                return false;
            })
        };
        wdk_reset_form();

        /* date time fields init */
        if ($('.wdk-fielddate').length && typeof $.datepicker != 'undefined') {

            $('.wdk-fielddate').each(function () {
                let dateFormat = wdk_script_parameters.format_date_js;
				var self = $(this);

                if (self.attr('date-format'))
                    dateFormat = self.attr('date-format');

                self.datepicker({ dateFormat: dateFormat,  onSelect: function() {
						self.parent().find('.db-date').val(wdk_date_sql_normalize(self.datepicker("getDate"), self)).trigger('input');
					}
				}).on( "change", function() {
                    if(self.val() == '') {
						self.parent().find('.db-date').val('');
					} else {
                        self.parent().find('.db-date').val(wdk_date_sql_normalize(self.datepicker("getDate"), self));
					}
				});
                
                if(self.parent().find('.db-date').val() == '' && false) {
                    self.parent().find('.db-date').val(wdk_date_notime_sql_normalize());
                }
            })
        } ;

        
        if ($('.wdk-fielddatetime').length && typeof $.datepicker != 'undefined') {

            $('.wdk-fielddatetime').each(function () {
                let dateFormat = wdk_script_parameters.format_datetime_js;

				var self = $(this);
                if (self.attr('date-format'))
                    dateFormat = self.attr('date-format');

                self.datepicker({ dateFormat: dateFormat,  onSelect: function() {
                        var datetime = wdk_date_notime_sql_normalize(self.datepicker("getDate"), self);
                        if(self.parent().find('[name="hours_mask"]').val() !='') {
                            datetime += ' '+wdk_pad(self.parent().find('[name="hours_mask"]').val());
                        } else {
                            datetime += ' 00';
                        }
                        if(self.parent().find('[name="minutes_mask"]').val() !='') {
                            datetime += ':'+wdk_pad(self.parent().find('[name="minutes_mask"]').val());
                        } else {
                            datetime += ':00';
                        }
                        datetime += ':00';

						self.parent().find('.db-date').val(datetime).trigger('input');
					}
				}).on( "change", function() {
                    
                    var datetime = wdk_date_notime_sql_normalize(self.datepicker("getDate"), self);
                    if(self.parent().find('[name="hours_mask"]').val() !='') {
                        datetime += ' '+wdk_pad(self.parent().find('[name="hours_mask"]').val());
                    } else {
                        datetime += ' 00';
                    }
                    if(self.parent().find('[name="minutes_mask"]').val() !='') {
                        datetime += ':'+wdk_pad(self.parent().find('[name="minutes_mask"]').val());
                    } else {
                        datetime += ':00';
                    }
                    datetime += ':00';
					self.parent().find('.db-date').val(datetime);
				});

                if(self.parent().find('.db-date').val() == '') {
                    self.parent().find('.db-date').val(wdk_date_sql_normalize());
                }

                self.parent().find('[name="hours_mask"],[name="minutes_mask"]').on('input', function(){
                    var datetime = wdk_date_notime_sql_normalize(self.datepicker("getDate"), self);
                    if(self.parent().find('[name="hours_mask"]').val() !='') {
                        datetime += ' '+wdk_pad(self.parent().find('[name="hours_mask"]').val());
                    } else {
                        datetime += ' 00';
                    }
                    if(self.parent().find('[name="minutes_mask"]').val() !='') {
                        datetime += ':'+wdk_pad(self.parent().find('[name="minutes_mask"]').val());
                    } else {
                        datetime += ':00';
                    }
                    datetime += ':00';
					self.parent().find('.db-date').val(datetime);
                });
            })
		};

		if ($('.wdk-fielddate_from').length && $('.wdk-fielddate_to').length && typeof $.datepicker != 'undefined') {
			var dateFormat, from, to;
			const getDate = ( element ) => {
				var date;
				try {
					date = $.datepicker.parseDate( dateFormat, element.value );
				} catch( error ) {
					date = null;
				}
				return date;
			} 

			dateFormat = wdk_script_parameters.format_date_js;
			if ($('.wdk-fielddate_from').attr('date-format'))
				dateFormat = $('.wdk-fielddate_from').attr('date-format');	
				
			from = $('.wdk-fielddate_from')
				.datepicker({
					dateFormat: dateFormat,
					onSelect: function( selectedDate ) {
						to.datepicker("option", "minDate", selectedDate );
						setTimeout(function(){
							to.datepicker('show');
						}, 16);
						
						from.parent().find('.db-date').val(wdk_date_sql_normalize(from.datepicker("getDate"), from));
                        wdk_date_add_hours(from);
					}
				}).on( "change", function() {
                    from.parent().find('.db-date').val(wdk_date_sql_normalize(from.datepicker("getDate"), from)).trigger('input');
                });
				
			to = $('.wdk-fielddate_to').datepicker({
				dateFormat: dateFormat
			})
			.on( "change", function() {
				from.datepicker( "option", "maxDate", getDate( this ) );
				to.parent().find('.db-date').val(wdk_date_sql_normalize(to.datepicker("getDate"), to)).trigger('input');
                wdk_date_add_hours(to);
			});

            if(to.parent().find('.db-date').val() == '') {
                to.parent().find('.db-date').val(wdk_date_sql_normalize());
            }

            if(from.parent().find('.db-date').val() == '') {
                from.parent().find('.db-date').val(wdk_date_sql_normalize());
            }
            			
			from.parent().find('[name="hours_mask"],[name="minutes_mask"]').on('input', function(){
				wdk_date_add_hours(from);
			});
			
			to.parent().find('[name="hours_mask"],[name="minutes_mask"]').on('input', function(){
				wdk_date_add_hours(to);
			});
		}
        if(typeof $.fn.wdkSuggestion == 'function') {

            if(typeof wdk_script_parameters.settings_wdk_field_search_suggestion_disable != 'undefined' && wdk_script_parameters.settings_wdk_field_search_suggestion_disable == 1) {

            } else {
                $('#wdk_field_search').wdkSuggestion({
                    ajax_url: wdk_script_parameters.ajax_url,
                    ajax_param: { 
                        "action": 'wdk_public_action',
                        "page": 'wdk_frontendajax',
                        "function": 'search_suggestion',
                    },
                    language_id: '',
                    text_search: 'Search',
                    callback_selected: function(key) {
                        
                    }
                });
            }

        }
        
        $('.wdk-control[type="number"]').on('change',function(){
            let val = $(this).val();
            let step = 1;
            if(val.length>=7){
                step = 10000;
            } else if(val.length>=4) {
                step = 10;
            }
            $(this).attr('step', step);
        })


        if(typeof $.fn.fieldSliderRange == 'function' && typeof $.fn.ionRangeSlider == 'function') {
            $('.wdk-slider-range-field').fieldSliderRange();
        }

        wdk_result_listings_thumbnail_slider();

        /* if video exists on thumbnail css trick */
        $('.wdk-listing-card .wdk-thumbnail .wdk-image.media').find('iframe,video').css('padding-bottom', +($('.wdk-listing-card .wdk-thumbnail .wdk-over-image-bottom').outerHeight())+'px');


        if(typeof $.fn.WdkScrollMobileSwipe == 'function') {
            $('.WdkScrollMobileSwipe_enable').WdkScrollMobileSwipe();
            $('.WdkScrollMobileSwipe_elementor_enable .elementor-container').WdkScrollMobileSwipe();
        }

        if($('.wl-menu-toggle').length) {
            $('.wdk-footer-menu .wdk_mobile_footer_menu_gumb-open').addClass('trigger')
            $('.wdk-footer-menu .wdk_mobile_footer_menu_gumb-open').on('click', function(e){
                e.preventDefault();
                $('.wl-menu-toggle').trigger('click');
            });
        }


        /* Read More Button */
        jQuery('.wdk-field-value.TEXTAREA').each(function(){
            // Find the target div containing additional content
            var moreContentDiv = $(this).find("[id*='more-']");
            // Hide the additional content initially
            var additionalContent = moreContentDiv.nextUntil(":not(p)");
            additionalContent.hide();
        
            // Create a "Read More" button
            var readMoreButtonWrapper = $("<div class='wdk-textarea-load-more-wrapper'><a class='wdk-textarea-load-more-button'></a></div>"),
            readMoreButton = readMoreButtonWrapper.find('a').text(wdk_script_parameters.text.read_more || '');
            readMoreButton.on("click", function() {
                // Toggle the visibility of the additional content
                additionalContent.slideToggle();
                if (readMoreButton.text() === wdk_script_parameters.text.read_more) {
                    readMoreButton.text(wdk_script_parameters.text.read_less);
                } else {
                    readMoreButton.text(wdk_script_parameters.text.read_more);
                }
            });
        
            // Replace the target div with the "Read More" button
            moreContentDiv.before(readMoreButtonWrapper);
            moreContentDiv.remove();
        });

        $('.wdk-language-switcher-drop .wdk-language-switcher-btn-toggle').on('click', function (e) { 
			e.preventDefault();
			$(this).closest('.wdk-language-switcher-drop').toggleClass('wdk-show');
		});
        // Add click event to each share link
        document.querySelectorAll('.wdk-device-share-link').forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault(); 
                shareUrl(link.getAttribute('href')); 
            });
        });

        $('.wdk-search_sensitive_link').on('click',function(e) {
            e.preventDefault();
            var href = $(this).attr('href'),
            search_url = wdk_start_search($("form.wdk-search-form"));

            if(search_url == href) {
                window.location.href = href;
                return;
            }

            // Parse the href to extract search_location and search_category
            var urlParams = new URLSearchParams(href.split('?')[1]);
            var searchLocation = urlParams.get('search_location');
            var searchCategory = urlParams.get('search_category');

            // Parse the search_url to update with new params
            var searchParams = new URLSearchParams(search_url.split('?')[1]);

            if (searchLocation) {
                searchParams.set('search_location', searchLocation);
            }
            if (searchCategory) {
                searchParams.set('search_category', searchCategory);
            }

            var updatedSearchUrl = search_url.split('?')[0] + '?' + searchParams.toString();
            window.location.href = updatedSearchUrl;

        })
        resetSearchFields();
    });

})(jQuery);



var resetSearchFields = () => {
    jQuery(".wdk-control[type='text']:not(.wdk-hidden)").each(function(){
        var $searchField = jQuery(this);
        // Create reset button
        var $resetButton = jQuery("<span>&times;</span>")
            .css({
                cursor: "pointer",
                display: "none", // Initially hidden
                position: "absolute",
                right: "10px",
                top: "50%",
                transform: "translateY(-50%)",
                fontSize: "18px"
            });
        
        /* Text Fields */
        
        // Wrap input field in a relative container
        var $wrapper = jQuery("<div></div>")
            .css({ position: "relative" })
            .insertBefore($searchField);
        
        $wrapper.append($searchField).append($resetButton);
        
        // Show/hide reset button based on input
        $searchField.on("input", function () {
            $resetButton.toggle(!!$searchField.val()); // Toggle visibility based on input
        });
        
        // Clear field when reset button is clicked
        $resetButton.on("click", function () {
            $searchField.val("").trigger("input").focus(); // Reset field and trigger input event to hide button
        });
    });
    /* Tree Fields */

    jQuery(`input[name="search_location"],input[name="search_category"]`).each(function(){

        var $searchField = jQuery(this);
        var resetButton;
    
        // Show/hide reset button based on input
        $searchField.on("change", function () {
                if (jQuery(this).val() !== "") {
                    // Wrap input field in a relative container
                    var $wrapper = $searchField.closest('.wdk-field-group').find('.wdk_dropdown_tree button:first-child')
                        .css({ position: "relative" });
                        console.log($wrapper);
                        console.log(resetButton);
                    if (!resetButton) {
                        resetButton = jQuery("<span>&times;</span>")
                        .css({
                            cursor: "pointer",
                            position: "absolute",
                            right: "10px",
                            top: "50%",
                            transform: "translateY(-50%)",
                            fontSize: "18px"
                        })
                        .on("click", function (e) {
                            e.stopPropagation();
                            e.preventDefault();
    
                            $searchField.val("").trigger("input").focus();
                            resetButton.remove();
                            resetButton = null;
                            $searchField.closest('.wdk-field-group').find('.wdk_dropdown_tree button:first-child').html( $searchField.closest('.wdk-field-group').find('.list_items li:first').text()); 
                          
                            return false;
                        });
                    $wrapper.append(resetButton);
                    }
                } else if (resetButton) {
                    resetButton.remove();
                    resetButton = null;
                }
        });

    });

};

/* slider for result listings thumbnails */
if (typeof wdk_result_listings_thumbnail_slider != 'function') {
    var wdk_result_listings_thumbnail_slider = ($wrapper = 'body') => {
        if(typeof jQuery.fn.slick == 'function') {
            jQuery('.wdk_js_gallery_slider_box', $wrapper).each(function(){
                var _this = jQuery(this); 

                if($wrapper == 'body') {
                    if(_this.closest('.wdk_results_listings_slider_ini').length){return true;}
                }

                if (_this.closest('.slick-slide').hasClass('slick-cloned')) {
                    return true;
                }

                // Check if the slider exists and is initialized
                if (_this.find('.wdk_js_gallery_slider').length > 0 && _this.find('.wdk_js_gallery_slider').hasClass('slick-initialized')) {
                    _this.find('.wdk_js_gallery_slider').slick('unslick');
                }


                _this.find('.wdk_js_gallery_slider').slick({
                    dots: true,
                    infinite: true,
                    speed: 500,
                    fade: true,
                    arrows: true,
                    cssEase: 'linear',
                    nextArrow: _this.find('.wdk_js_gallery_slider-carousel_arrows .wdk-slider-next'),
                    prevArrow: _this.find('.wdk_js_gallery_slider-carousel_arrows .wdk-slider-prev'),
                });
            });
        }
    };
}
/*
    Fix for slick slider, event after init slick
    
    @param object el slick object
    @param function callback
*/

if (typeof wdk_slick_slider_init != 'function') {
var wdk_slick_slider_init = (el, callback) => {
    try {
        el.slick("slickGoTo", 0, true);
    }
    catch(error) {
        setTimeout(wdk_slick_slider_init, 1000);
        return;
    }
    
    if(callback) {
        callback.call();
    }
};
}
if (typeof wdk_pad != 'function') {
var wdk_pad = (num = '') => {
    return ('00'+num).slice(-2);
};
}

if (typeof wdk_date_add_hours != 'function') {
var wdk_date_add_hours = (selector = '') => {
	if(typeof selector !='undefined' && selector.parent().find('[name="hours_mask"]').length) {
		var datetime = wdk_date_notime_sql_normalize(selector.datepicker("getDate"), selector);
		if(selector.parent().find('[name="hours_mask"]').val() !='') {
			datetime += ' '+wdk_pad(selector.parent().find('[name="hours_mask"]').val());
		} else {
			datetime += ' 00';
		}
		if(selector.parent().find('[name="minutes_mask"]').val() !='') {
			datetime += ':'+wdk_pad(selector.parent().find('[name="minutes_mask"]').val());
		} else {
			datetime += ':00';
		}
		datetime += ':00';
		selector.parent().find('.db-date').val(datetime);
	}
};
}
/*
    Convert date to sql format

    @param string $date, string of date

    @return string normalize date or Current date/time
*/
if (typeof wdk_date_sql_normalize != 'function') {
var wdk_date_sql_normalize = (date = '', datepicker_el = null) => {
    if(date == '') {
		var d = new Date();
	} else {
		var d = new Date(date);
	}

	if(d == 'Invalid Date' && datepicker_el) {
		var d = new Date(+(jQuery.datepicker.formatDate("@", datepicker_el.datepicker("getDate"))));
	} 
	
	d = d.getUTCFullYear()        + '-' +
	wdk_pad(d.getMonth() + 1)  + '-' +
	wdk_pad(d.getDate())          + ' ' +
	wdk_pad(d.getHours())         + ':' +
	wdk_pad(d.getMinutes())       + ':' +
	wdk_pad(d.getSeconds());
	return d;
};
}

if (typeof wdk_date_notime_sql_normalize != 'function') {
var wdk_date_notime_sql_normalize = (date = '', datepicker_el = null) => {
    if(date == '') {
		var d = new Date();
	} else {
		var d = new Date(date);
	}

	if(d == 'Invalid Date' && datepicker_el) {
		var d = new Date(+(jQuery.datepicker.formatDate("@", datepicker_el.datepicker("getDate"))));
	} 
	
	d = d.getUTCFullYear()        + '-' +
	wdk_pad(d.getMonth() + 1)  + '-' +
	wdk_pad(d.getDate());
	return d;
};
}

if (typeof wdk_generate_marker_ajax_popup != 'function') {
var wdk_generate_marker_ajax_popup = (ajax_url, listing_post_id, lat, lng,innerMarker, wdk_jpopup_customOptions, auto = false, clusters_enabled = true, custom_layout_id = '') => {

    if(auto) {
        var marker = L.marker(
            [lat, lng],
            {icon: L.divIcon({
                    html: innerMarker,
                    className: 'open_steet_map_marker',
                    iconSize: 'auto',
                })
            }
        );
    } else {
        var marker = L.marker(
            [lat, lng],
            {icon: L.divIcon({
                    html: innerMarker,
                    className: 'open_steet_map_marker',
                    iconSize: [40, 60],
                    popupAnchor: [-1, -35],
                    iconAnchor: [23, 73],
                })
            }
        );
    }

    var data = {
        "action": 'wdk_public_action',
        "page": 'wdk_frontendajax',
        "function": 'map_infowindow',
        "listing_post_id": listing_post_id,
        "custom_layout_id": custom_layout_id,
      };
  
    let favorite_init = false;
    let compare_init = false;
    marker.bindPopup(function () {
        var content = '<div class="infobox"><div class="map_infowindow"><div class="loading_content animated-background"><div class="box_line m170"></div><div class="box_line m20"></div><div class="box_line m20"></div><div class="box_line m20"></div><div class="box_line m20"></div><div class="box_line m20"></div></div></div></div>';
        marker.getPopup().setContent(content);
        marker.getPopup().update();
        jQuery.ajax({
            url : ajax_url,
            type : "POST",
            data: data,
            success: function (data) {
                marker.getPopup().setContent(data.popup_content);
                marker.getPopup().update();
                if (!favorite_init && typeof wdk_favorite == 'function')
                    wdk_favorite('.infobox');
                
                favorite_init = false;

                if (!compare_init && typeof wdk_init_compare_elem == 'function')
                    wdk_init_compare_elem();
                
                compare_init = false;
            },
        });
        return content;
    }, wdk_jpopup_customOptions);

    if (typeof wdk_favorite == 'function')
        marker.on('popupopen', function (popup) {
            if (!favorite_init)
                wdk_favorite('.infobox');
        });

    if (typeof wdk_init_compare_elem == 'function')
        marker.on('popupopen', function (popup) {
            if (!compare_init)
                wdk_init_compare_elem();
        });

    if (typeof wdk_favorite == 'function')
        marker.on('popupclose', function (popup) {
            marker.getPopup().setContent(jQuery('.leaflet-popup-content-wrapper .leaflet-popup-content').html());
            marker.getPopup().update();
        });

    if(clusters_enabled) {
        wdk_clusters.addLayer(marker);
    } else {
        wdk_map.addLayer(marker);
    }
    return marker;
}
}

if (typeof wdk_generate_marker_basic_popup != 'function') {
var wdk_generate_marker_basic_popup = (lat, lng, innerMarker, wdk_jpopup_content, wdk_jpopup_customOptions) => {
    var marker = L.marker(
        [lat, lng],
        {icon: L.divIcon({
                html: innerMarker,
                className: 'open_steet_map_marker',
                iconSize: [40, 60],
                popupAnchor: [-1, -35],
                iconAnchor: [23, 73],
            })
        }
    );
    
    marker.bindPopup(wdk_jpopup_content, wdk_jpopup_customOptions);

    return marker;
}
}

if (typeof wdk_generate_marker_nopopup != 'function') {
var wdk_generate_marker_nopopup = (lat, lng,innerMarker) => {
    var marker = L.marker(
        [lat, lng],
        {icon: L.divIcon({
                html: innerMarker,
                className: 'open_steet_map_marker',
                iconSize: [40, 60],
                popupAnchor: [-1, -35],
                iconAnchor: [23, 73],
            })
        }
    );

    return marker;
}
}

// Share URL function
var shareUrl = (url = window.location.href) => {
    // Check if the browser supports the Web Share API
    if (!navigator.share) {
        console.log("Web Share API not supported.");
        return;
    }

    navigator.share({
        url: url, // Share the current page URL
    })
    .then(() => {
        console.log("Shared successfully!");
    })
    .catch((error) => {
        console.error("Sharing failed:", error);
    });
};
