(function () {
    var $ = jQuery;

    function wdkHandleLiveEditor() {

        // close model events.
        $('.eicon-close').on('click', wdkCloseModal);

        $('#pa-insert-live-temp').on('click', function () {
            $('body').attr('data-pa-liveeditor-load', 'true');
            wdkCloseModal(true);
        });

        $(document).on('click', '.wdk-live-editor-iframe-modal', function (e) {
            if ($(e.target).closest(".dialog-lightbox-widget-content").length < 1) {
                wdkCloseModal();
            }
        });

        // resize model event.
        $('.wdk-live-editor-iframe-modal .wdk-expand').on('click', function () {

            if ($(this).find(' > i').hasClass('eicon-frame-expand')) {
                $(this).find('i.eicon-frame-expand').removeClass('eicon-frame-expand').addClass('eicon-frame-minimize').attr('title', 'Minimize');
                $('.wdk-live-editor-iframe-modal').addClass('wdk-modal-expanded');

            } else {
                wdkMinimizeModal(this);
            }
        });

        elementor.channels.editor.on('wdkCreateLiveTemp', function (e) {

            console.log('wdkCreateLiveTemp');
            var 
                $modalContainer = $('.wdk-live-editor-iframe-modal'),
                paIframe = $modalContainer.find("#pa-live-editor-control-iframe"),
                $lightboxLoading = $modalContainer.find(".dialog-lightbox-loading"),
                lightboxType = $modalContainer.find(".dialog-type-lightbox"),
                tempSelectorId = e.model.attributes.name.split('_live')[0],
                settingsToChange = {};

            // from e.model.attributes.button_type ("default papro-btn-block for-custom_layout_live_grid")
            var buttonTypeClass = e.model.attributes.button_type || '';
            var match = buttonTypeClass.match(/for-([^\s]*)/);
            var liveTempKey = match ? match[1] : 'live_temp_content';

            var layoutId = jQuery('[data-setting="'+liveTempKey+'"]').val() || '';

            // show modal.
            lightboxType.show();
            $modalContainer.show();
            $lightboxLoading.show();
            paIframe.contents().find("#elementor-loading").show();
            paIframe.css("z-index", "-1");

            $.ajax({
                type: 'POST',
                url: liveEditor.ajaxurl,
                dataType: 'JSON',
                data: {
                    action: 'wdk_handle_live_editor',
                    security: liveEditor.nonce,
                    post_id: layoutId,
                },
                success: function (res) {

                    paIframe.attr("src", res.data.url);
                    paIframe.attr("data-wdk-temp-id", res.data.id);

                    console.log(res.data);
                    $('#wdk-live-temp-title').val(res.data.title);

                    paIframe.on("load", function () {
                        $lightboxLoading.hide();
                        paIframe.show();
                        //$modalContainer.find('.wdk-live-editor-title').css('display', 'flex');
                        paIframe.contents().find("#elementor-loading").hide();
                        paIframe.css("z-index", "1");
                    });

                    clearInterval(window.paLiveEditorInterval);

                    window.paLiveEditorInterval = setInterval(function () {
                        console.log('paLiveEditorInterval');
                        var loadTemplate = $('body').attr('data-pa-liveeditor-load');

                        if ('true' === loadTemplate) {
                            $('body').attr('data-pa-liveeditor-load', 'false');

                            settingsToChange = {};
                            settingsToChange[liveTempKey] = res.data.id;
                            $e.run('document/elements/settings', { container: e.container, settings: settingsToChange, options: { external: !0 } });

                            jQuery('[data-setting="'+liveTempKey+'"]').closest('.elementor-control').removeClass("control-hidden");

                            var tempTitle = $('#wdk-live-temp-title').val();
                            if (tempTitle && tempTitle !== res.data.title) {
                                wdkUpdateTemplateTitle(tempTitle, res.data.id);
                            }
                        }
                    }, 1000);
                },
                error: function (err) {
                    console.log(err);
                }
            });
        });
    }

    /**
     * Helper Funcitons.
     */

    function wdkCheckTempValidity(tempID, tempType) {

        if ('' !== tempID) {
            $.ajax({
                type: 'POST',
                url: liveEditor.ajaxurl,
                dataType: 'JSON',
                data: {
                    action: 'check_temp_validity',
                    security: liveEditor.nonce,
                    templateID: tempID,
                    tempType: tempType
                },
                success: function (res) {
                    console.log(res.data);
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }
    }

    function wdkMinimizeModal(_this) {

        $(_this).find('i.eicon-frame-minimize').removeClass('eicon-frame-minimize').addClass('eicon-frame-expand').attr('title', 'Expand');
        $('.wdk-live-editor-iframe-modal').removeClass('wdk-modal-expanded');
    }

    function wdkUpdateTemplateTitle(title, id) {

        $.ajax({
            type: 'POST',
            url: liveEditor.ajaxurl,
            dataType: 'JSON',
            data: {
                action: 'update_template_title',
                security: liveEditor.nonce,
                title: title,
                id: id
            },
            success: function (res) {
                console.log('Template Title Updated.');
            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    function wdkCloseModal(inserted = false) {

        $('.wdk-live-editor-iframe-modal').css('display', 'none');

        $(".wdk-live-temp-title input").attr('disabled', 'true');

        wdkMinimizeModal($('.wdk-live-editor-iframe-modal .wdk-expand'));

        if (!inserted) {
            var tempId = $(".wdk-live-editor-iframe-modal #pa-live-editor-control-iframe").attr('data-wdk-temp-id'),
                tempType = $(".wdk-live-editor-iframe-modal #pa-live-editor-control-iframe").attr('data-wdk-temp-type');

            if (undefined !== tempId && '' !== tempId) {
                wdkCheckTempValidity(tempId, tempType);
            }
        }

        // reset temp id/src attribute.
        $(".wdk-live-editor-iframe-modal #pa-live-editor-control-iframe").attr({
            'data-wdk-temp-id': '',
            'data-wdk-temp-type': '',
            'src': ''
        });
    }

    function wdkCheckLiveTemplateControl() {

        setTimeout(function () {

            $(".wdk-live-temp-title input").each(function (index, input) {
                $(input).attr('disabled', 'true');
            });

            $('.wdk-cf-form-id input').attr('disabled', 'true');

        }, 500);
    }

    // Update button text based on template selection
    function wdkUpdateTemplateButtonText() {
        elementor.channels.editor.on('element:settings:changed', function(model, key, value) {
            if (key === 'wdk_carousel_repeater_item') {
                var buttonText = value && value !== '' ? 
                    'Edit Selected Template' : 
                    'Create New Template';
                
                // Update button text - try different selectors
                $('.wdk-carousel-repeater-item-live button, [data-setting="wdk_carousel_repeater_item_live"] button').text(buttonText);
            }
        });
    }

    // Initialize button text on load
    function wdkInitializeTemplateButton() {
        setTimeout(function() {
            $('[data-setting="wdk_carousel_repeater_item_live"] button').each(function() {
                var $button = $(this);
                var $control = $button.closest('.elementor-control');
                var templateSelect = $control.siblings('[data-setting="wdk_carousel_repeater_item"]').find('select, input');
                
                if (templateSelect.length && templateSelect.val() && templateSelect.val() !== '') {
                    $button.text('Edit Selected Template');
                } else {
                    $button.text('Create New Template');
                }
            });
        }, 100);
    }

    elementor.channels.editor.on('section:activated', function() {
        wdkCheckLiveTemplateControl();
        wdkInitializeTemplateButton();
    });

    $(window).on('elementor:init', function() {
        wdkHandleLiveEditor();
        wdkUpdateTemplateButtonText();
    });

    
    function checkLiveTemplateControl() {

        setTimeout(function () {

            $(".wdk-live-temp-title input").each(function (index, input) {
                $(input).attr('disabled', 'true');
                if ('' != $(input).val()) {
                    $(input).closest(".wdk-live-temp-title").removeClass("control-hidden");
                }
            });

            $('.wdk-cf-form-id input').attr('disabled', 'true');

        }, 500);
    }

    elementor.channels.editor.on('section:activated', checkLiveTemplateControl);

})(jQuery);