<?php

/**
 * The template for Element Listing Images Slider.
 * This is the template that elementor element slider, carousel
 *
 */
 
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element); ?>">
    <div class="wdk-listing-slider">
        <?php
        // Organize images into tabs: gallery, plans, custom fields
        $tabs = [];

        // Gallery Tab
        $tabs['gallery'] = [];
        foreach ($images as $key => $image) {
            $type = 'gallery';
            if (strpos($key, '__') !== FALSE) {
                $type = substr($key, 0, strpos($key, '__'));
            }
            if ($type === 'gallery') {
                $tabs['gallery'][$key] = $image;
            }
        }

        // Plans tab if exists
        if (!empty($plans_exists)) {
            $tabs['plans'] = [];
            foreach ($images as $key => $image) {
                $type = 'gallery';
                if (strpos($key, '__') !== FALSE) {
                    $type = substr($key, 0, strpos($key, '__'));
                }
                if ($type === 'plans') {
                    $tabs['plans'][$key] = $image;
                }
            }
        }

        // Custom fields (images_fields) tabs if exists
        if (!empty($images_fields)) {
            foreach ($images_fields as $field_id => $field_images) {
                $tabs[$field_id] = [];
                foreach ($images as $key => $image) {
                    $field_images_id = 'gallery';
                    if (strpos($key, '__') !== FALSE) {
                        $field_images_id = substr($key, 0, strpos($key, '__'));
                    }
                    if ($field_images_id == $field_id) {
                        $tabs[$field_id][$key] = $image;
                    }
                }
            }
        }
        // Only make tabs if we have images for them
        $tab_keys = array_filter(array_keys($tabs), function($tab_key) use ($tabs) {
            return !empty($tabs[$tab_key]);
        });
        ?>

        <?php if (count($images) > 0): ?>
            <?php if (count($tab_keys) > 1): ?>
                <div class="elementor-section elementor-section-boxed">
  <div class="elementor-container">
                <div class="wdk-listing-slider--tabs">
                    <?php foreach ($tab_keys as $tab_key): ?>
                        <a data-tab="<?php echo esc_attr($tab_key); ?>" href="#" class="wdk-listing-slider--tabs--btn <?php echo $tab_key === 'gallery' ? 'active' : ''; ?>">
                            <?php
                            if ($tab_key === 'gallery') {
                                echo esc_html__('Gallery', 'wpdirectorykit');
                            } elseif ($tab_key === 'plans') {
                                echo esc_html__('Plans', 'wpdirectorykit');
                            } else {
                                echo esc_html(wdk_field_label($tab_key));
                            }
                            ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                </div>
                </div>
            <?php endif; ?>

            <div class="wdk-listing-slider--content-tabs">
                <?php foreach ($tab_keys as $tab_key): ?>
                <div class="wdk-listing-slider--tab-pane<?php if ($tab_key === 'gallery') echo ' active'; ?>" id="wdk_list_slider_tab_<?php echo esc_attr($tab_key); ?>">
                  <div class="<?php if ($settings['popup_enable'] == 'yes'): ?> wdk_js_gallery <?php endif; ?> wdk_listing_slider_box <?php echo esc_attr($settings['layout_carousel_animation_style']) . '_animation'; ?> <?php echo esc_attr(join(' ', [$settings['styles_carousel_dots_position_style'], $settings['styles_carousel_arrows_position_style'], $settings['styles_carousel_arrows_position'], $settings['styles_carousel_arrows_position_style']])); ?>">
                      <div class="wdk_listing_slider_ini" id="wdk_slider_<?php echo esc_attr($tab_key); ?>">
                          <?php foreach ($tabs[$tab_key] as $key => $image): ?>
                              <?php
                              $type = $tab_key;
                              ?>
                              <?php if (
                                  is_string($image) && (
                                      filter_var($image, FILTER_VALIDATE_URL) ||
                                      preg_match('/^www\./i', $image)
                                  )
                              ): ?>
                                  <div class="wdk-col" data-type="<?php echo esc_html($type); ?>">
                                      <div class="wdk-listing-image-card">
                                          <?php if (strpos($image, 'vimeo.com') !== FALSE): ?>
                                              <div class="wdk-listing-image wdk-listing-video-embed<?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height<?php endif; ?>">
                                                  <?php echo wp_oembed_get($image, array("width" => "800", "height" => "450")); ?>
                                              </div>
                                          <?php elseif (strpos($image, 'watch?v=') !== FALSE): ?>
                                              <?php $embed_code = substr($image, strpos($image, 'watch?v=') + 8); ?>
                                              <div class="wdk-listing-image wdk-listing-video-embed<?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height<?php endif; ?>">
                                                  <?php echo wp_oembed_get('https://www.youtube.com/watch?v=' . $embed_code, array("width" => "800", "height" => "455")); ?>
                                              </div>
                                          <?php elseif (strpos($image, 'youtube.com/shorts/') !== FALSE): ?>
                                              <?php $embed_code = substr($image, strpos($image, 'shorts') + 7); ?>
                                              <div class="wdk-listing-image wdk-listing-video-embed<?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height<?php endif; ?>">
                                                  <?php echo wp_oembed_get('https://www.youtube.com/watch?v=' . $embed_code, array("width" => "800", "height" => "455")); ?>
                                              </div>
                                          <?php elseif (strpos($image, 'youtu.be/') !== FALSE): ?>
                                              <?php $embed_code = substr($image, strpos($image, 'youtu.be/') + 9); ?>
                                              <div class="wdk-listing-image wdk-listing-video-embed<?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height<?php endif; ?>">
                                                  <?php echo wp_oembed_get('https://www.youtube.com/watch?v=' . $embed_code, array("width" => "800", "height" => "455")); ?>
                                              </div>
                                          <?php elseif (filter_var($image, FILTER_VALIDATE_URL) !== FALSE && preg_match('/\.(mp4|flv|wmw|ogv|webm|ogg)$/i', $image)): ?>
                                              <video
                                                  src="<?php echo esc_url($image); ?>"
                                                  controls
                                                  class="wdk-listing-image <?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height<?php endif; ?>"
                                                  <?php if (wmvc_show_data('auto_start_video', $settings, false) == 'yes'): ?> autoplay loop<?php endif; ?>
                                                >
                                                </video> 
                                          <?php else: ?>
                                              <iframe
                                                  src="<?php echo esc_url($image); ?>" width="100%" frameborder="0" allowfullscreen
                                                  class="wdk-listing-image <?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height <?php endif; ?>"></iframe>
                                          <?php endif; ?>
                                      </div>
                                  </div>
                              <?php elseif (!wmvc_show_data('wdk_listing_video_disabled', $settings, false) && wdk_file_extension_type(wmvc_show_data('src', $image)) == 'video'): ?>
                                  <div class="wdk-col" data-type="<?php echo esc_html($type); ?>">
                                      <div class="wdk-listing-image-card">
                                          <video controls loop <?php if (wmvc_show_data('auto_start_video', $settings, false) == 'yes'): ?> autoplay <?php endif; ?> src="<?php echo esc_url(wmvc_show_data('src', $image)); ?>" alt="<?php echo esc_attr(wmvc_show_data('alt', $image)); ?>" class="wdk-listing-image <?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height <?php endif; ?>"></video>
                                     
                                      </div>
                                  </div>
                              <?php elseif (wdk_file_extension_type(wmvc_show_data('src', $image)) == 'image'): ?>
                                  <div class="wdk-col" data-type="<?php echo esc_html($type); ?>">
                                      <div class="wdk-listing-image-card">
                                          <img
                                              src="<?php echo esc_url(wmvc_show_data('src', $image)); ?>"
                                              class="wdk-listing-image<?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height<?php endif; ?>"
                                              alt="<?php echo esc_attr(wmvc_show_data('alt', $image)); ?>">
                                      </div>
                                  </div>
                              <?php endif; ?>
                          <?php endforeach; ?>

                          <?php if (!empty($tabs[$tab_key]) && wmvc_show_data('layout_carousel_columns', $settings, 1) < wmvc_count($tabs[$tab_key])): ?>
                      </div>
                      <div class="wdk-listing-slider_arrows">
                          <a title="<?php echo esc_attr__('prev slider', 'wpdirectorykit'); ?>" href="#" class="wdk-slider-prev wdk-listing-slider_arrow">
                              <?php \Elementor\Icons_Manager::render_icon($settings['styles_carousel_arrows_icon_left'], ['aria-hidden' => 'true']); ?>
                          </a>
                          <a title="<?php echo esc_attr__('next slider', 'wpdirectorykit'); ?>" href="#" class="wdk-slider-next wdk-listing-slider_arrow">
                              <?php \Elementor\Icons_Manager::render_icon($settings['styles_carousel_arrows_icon_right'], ['aria-hidden' => 'true']); ?>
                          </a>
                      </div>
                      <?php else: ?>
                      </div>
                      <?php endif; ?>
                  </div>
                  <?php
                  // Thumbs
                  if (count($tabs[$tab_key]) > 1): ?>
                  <div class="banner-thumbs-con elementor-section elementor-section-boxed">
                      <div class="elementor-container">
                          <div class="banner-thumbs" id="banner_thumbs_<?php echo esc_attr($tab_key); ?>">
                              <?php foreach ($tabs[$tab_key] as $key => $image): ?>
                                  <?php
                                  $type = $tab_key;
                                  ?>
                                  <?php if (
                                      is_string($image) && (
                                          filter_var($image, FILTER_VALIDATE_URL) ||
                                          preg_match('/^www\./i', $image)
                                      )
                                  ): ?>

                                  <?php elseif (!wmvc_show_data('wdk_listing_video_disabled', $settings, false) && wdk_file_extension_type(wmvc_show_data('src', $image)) == 'video'): ?>
                                      <div class="banner-thumb" data-type="<?php echo esc_html($type); ?>">
                                          <video src="<?php echo esc_url(wmvc_show_data('src', $image)); ?>" alt="<?php echo esc_attr(wmvc_show_data('alt', $image)); ?>" class="wdk-listing-image <?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height <?php endif; ?>"></video>
                                      </div>
                                  <?php elseif (wdk_file_extension_type(wmvc_show_data('src', $image)) == 'image'): ?>
                                      <div class="banner-thumb" data-type="<?php echo esc_html($type); ?>">
                                          <img src="<?php echo esc_url(wmvc_show_data('src', $image)); ?>" class="wdk-listing-image <?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height <?php endif; ?>" alt="<?php echo esc_attr(wmvc_show_data('alt', $image)); ?>">
                                      </div>
                                  <?php endif; ?>
                              <?php endforeach; ?>
                          </div><!--banner-thumbs end-->
                      </div>
                  </div>
                  <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div> <!-- .wdk-listing-slider--content-tabs -->
        <?php endif; ?>
    </div>
</div>
<script>
jQuery(document).ready(function($){
    function initTabSlider(tab_key) {
        var sliderSelector = '#wdk_el_<?php echo esc_html($id_element); ?> #wdk_slider_' + tab_key;
        var thumbsSelector = '#wdk_el_<?php echo esc_html($id_element); ?> #banner_thumbs_' + tab_key;

        // destroy before re-init
        if ($(sliderSelector).hasClass('slick-initialized')) {
            $(sliderSelector).slick('unslick');
        }
        if ($(thumbsSelector).length && $(thumbsSelector).hasClass('slick-initialized')) {
            $(thumbsSelector).slick('unslick');
        }

        $(sliderSelector).slick({
            <?php if (!empty($images) && wmvc_show_data('layout_carousel_columns', $settings, 1) < wmvc_count($images)): ?>
                dots: true,
                arrows: true,
            <?php else: ?>
                dots: false,
                arrows: false,
            <?php endif; ?>
            slidesToShow: <?php echo wmvc_show_data('layout_carousel_columns', $settings, 1); ?>,
            slidesToScroll: <?php echo wmvc_show_data('layout_carousel_columns', $settings, 1); ?>,
            <?php if (!empty(wmvc_show_data('layout_carousel_is_infinite', $settings))): ?>
                infinite: <?php echo wmvc_show_data('layout_carousel_is_infinite', $settings, 'true'); ?>,
            <?php endif; ?>
            <?php if (!empty(wmvc_show_data('layout_carousel_is_autoplay', $settings))): ?>
                autoplay: <?php echo wmvc_show_data('layout_carousel_is_autoplay', $settings, 'false'); ?>,
            <?php endif; ?>
            nextArrow: $('#wdk_el_<?php echo esc_html($id_element); ?> #wdk_list_slider_tab_'+tab_key+' .wdk-listing-slider_arrows .wdk-slider-next'),
            prevArrow: $('#wdk_el_<?php echo esc_html($id_element); ?> #wdk_list_slider_tab_'+tab_key+' .wdk-listing-slider_arrows .wdk-slider-prev'),
            customPaging: function(slider, i) {
                return '<span class="wdk_lr_dot"><?php \Elementor\Icons_Manager::render_icon($settings['styles_carousel_dots_icon'], ['aria-hidden' => 'true']); ?></span>';
            },
            responsive: [{
                breakpoint: 600,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }],
            asNavFor: thumbsSelector.length ? thumbsSelector : null
        });

        if ($(thumbsSelector).length) {
            $(thumbsSelector).slick({
                slidesToShow: <?php echo (!empty(trim(wmvc_show_data('styles_thmbn_nav_columns', $settings, '4')))) ? wmvc_show_data('styles_thmbn_nav_columns', $settings, '4') : 4; ?>,
                slidesToScroll: 1,
                asNavFor: sliderSelector,
                dots: false,
                centerMode: false,
                arrows: false,
                focusOnSelect: true,
                responsive: [{
                        breakpoint: 991,
                        settings: {
                            slidesToShow: <?php echo (!empty(trim(wmvc_show_data('styles_thmbn_nav_columns_tablet', $settings, '3')))) ? wmvc_show_data('styles_thmbn_nav_columns_tablet', $settings, '3') : 3; ?>,
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: <?php echo (!empty(trim(wmvc_show_data('styles_thmbn_nav_columns_mobile', $settings, '2')))) ? wmvc_show_data('styles_thmbn_nav_columns_mobile', $settings, '2') : 2; ?>,
                        }
                    },
                ]
            });
        }

        // Sync thumb highlight after change
        $(sliderSelector).on('afterChange', function(event, slick, currentSlide) {
            if ($(thumbsSelector).length) {
                $(thumbsSelector).find('.slick-slide').removeClass('is-active');
                $(thumbsSelector).find('.slick-slide[data-slick-index="' + currentSlide + '"]').addClass('is-active');
            }
        });

        $(sliderSelector).on('reInit afterChange', function(event, slick, currentSlide) {
            var visibleSlides = $(sliderSelector).find('.slick-slide:not(.slick-cloned):visible').length;
            if (visibleSlides <= 1) {
                $('#wdk_el_<?php echo esc_html($id_element); ?> #wdk_list_slider_tab_'+tab_key+' .slick-arrow').hide();
            } else {
                $('#wdk_el_<?php echo esc_html($id_element); ?> #wdk_list_slider_tab_'+tab_key+' .slick-arrow').show();
            }
        });
    }

    // On load: initialize all tabs, show only active
    $('.wdk-listing-slider--tab-pane').hide();
    $('.wdk-listing-slider--tab-pane.active').show();

    // Initialize the visible slider only, or all if you like
    var firstTab = $('.wdk-listing-slider--tab-pane.active').attr('id');
    if (firstTab) {
        var firstTabKey = firstTab.replace('wdk_list_slider_tab_', '');
        initTabSlider(firstTabKey);
    }

    // Change tabs event
    $(document).on('click', '.wdk-listing-slider--tabs--btn', function(e){
        e.preventDefault();
        var $this = $(this);
        var tabKey = $this.data('tab');
        $('.wdk-listing-slider--tabs--btn').removeClass('active');
        $this.addClass('active');
        $('.wdk-listing-slider--tab-pane').removeClass('active').hide();
        $('#wdk_list_slider_tab_'+tabKey).addClass('active').show();

        // destroy all others
        $('.wdk_listing_slider_ini.slick-initialized').not('#wdk_slider_'+tabKey).each(function(){
            $(this).slick('unslick');
        });
        $('.banner-thumbs.slick-initialized').not('#banner_thumbs_'+tabKey).each(function(){
            $(this).slick('unslick');
        });

        // init for this tab if not already initialized
        initTabSlider(tabKey);
    });
});
</script>