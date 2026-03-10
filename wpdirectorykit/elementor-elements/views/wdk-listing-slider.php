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
        <?php if (count($images) > 0): ?>
            <?php if (!empty($images) && ($plans_exists || $images_fields || $images_fields)): ?>
                <div class="wdk-listing-slider--tabs">
                    <a data-filter="gallery" href="#" class="wdk-listing-slider--tabs--btn">
                        <?php echo esc_html__('Gallery', 'wpdirectorykit'); ?>
                    </a>

                    <?php if (!empty($plans_exists)): ?>
                        <a data-filter="plans" href="#" class="wdk-listing-slider--tabs--btn">
                            <?php echo esc_html__('Plans', 'wpdirectorykit'); ?>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($images_fields)): ?>
                        <?php foreach ($images_fields as $field_id => $field_images): ?>
                            <a data-filter="<?php echo esc_attr($field_id); ?>" href="#" class="wdk-listing-slider--tabs--btn">
                                <?php echo esc_html(wdk_field_label($field_id)); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="<?php if ($settings['popup_enable'] == 'yes'): ?> wdk_js_gallery <?php endif; ?> wdk_listing_slider_box <?php echo esc_attr($settings['layout_carousel_animation_style']) . '_animation'; ?> <?php echo esc_attr(join(' ', [$settings['styles_carousel_dots_position_style'], $settings['styles_carousel_arrows_position_style'], $settings['styles_carousel_arrows_position'], $settings['styles_carousel_arrows_position_style']])); ?>">
                <div class="wdk_listing_slider_ini">
                    <?php foreach ($images as $key => $image): ?>
                        <?php
                        $type = 'gallery';
                        if (strpos($key, '__') !== FALSE) {
                            $type = substr($key, 0, strpos($key, '__'));
                        }
                        ?>
                        <?php if (
                            is_string($image) && (
                                filter_var($image, FILTER_VALIDATE_URL) ||
                                preg_match('/^www\./i', $image)
                            )
                        ): ?>
                            <div class="wdk-col">
                                <div class="wdk-listing-image-card">


                                    <?php if (strpos($image, 'vimeo.com') !== FALSE): ?>
                                        <div
                                            data-type="<?php echo esc_html($type); ?>"
                                            class="wdk-listing-image wdk-listing-video-embed<?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height<?php endif; ?>">
                                            <?php echo wp_oembed_get($image, array("width" => "800", "height" => "450")); ?>
                                        </div>
                                    <?php elseif (strpos($image, 'watch?v=') !== FALSE): ?>
                                        <?php $embed_code = substr($image, strpos($image, 'watch?v=') + 8); ?>
                                        <div
                                            data-type="<?php echo esc_html($type); ?>"
                                            class="wdk-listing-image wdk-listing-video-embed<?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height<?php endif; ?>">
                                            <?php echo wp_oembed_get('https://www.youtube.com/watch?v=' . $embed_code, array("width" => "800", "height" => "455")); ?>
                                        </div>
                                    <?php elseif (strpos($image, 'youtube.com/shorts/') !== FALSE): ?>
                                        <?php $embed_code = substr($image, strpos($image, 'shorts') + 7); ?>
                                        <div
                                            data-type="<?php echo esc_html($type); ?>"
                                            class="wdk-listing-image wdk-listing-video-embed<?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height<?php endif; ?>">
                                            <?php echo wp_oembed_get('https://www.youtube.com/watch?v=' . $embed_code, array("width" => "800", "height" => "455")); ?>
                                        </div>
                                    <?php elseif (strpos($image, 'youtu.be/') !== FALSE): ?>
                                        <?php $embed_code = substr($image, strpos($image, 'youtu.be/') + 9); ?>
                                        <div
                                            data-type="<?php echo esc_html($type); ?>"
                                            class="wdk-listing-image wdk-listing-video-embed<?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height<?php endif; ?>">
                                            <?php echo wp_oembed_get('https://www.youtube.com/watch?v=' . $embed_code, array("width" => "800", "height" => "455")); ?>
                                        </div>
                                    <?php elseif (filter_var($image, FILTER_VALIDATE_URL) !== FALSE && preg_match('/\.(mp4|flv|wmw|ogv|webm|ogg)$/i', $image)): ?>
                                        <video
                                            src="<?php echo esc_url($image); ?>"
                                            controls
                                            data-type="<?php echo esc_html($type); ?>"
                                            class="wdk-listing-image<?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height<?php endif; ?>"></video>

                                    <?php else: ?>
                                        <iframe
                                            data-type="<?php echo esc_html($type); ?>"
                                            src="<?php echo esc_url($image); ?>" width="100%" frameborder="0" allowfullscreen
                                            class="wdk-listing-image <?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height <?php endif; ?>"></iframe>
                                    <?php endif; ?>

                                </div>
                            </div>
                        <?php elseif (!wmvc_show_data('wdk_listing_video_disabled', $settings, false) && wdk_file_extension_type(wmvc_show_data('src', $image)) == 'video'): ?>
                            <div class="wdk-col">
                                <div class="wdk-listing-image-card">
                                    <video data-type="<?php echo esc_html($type); ?>" controls src="<?php echo esc_url(wmvc_show_data('src', $image)); ?>" alt="<?php echo esc_attr(wmvc_show_data('alt', $image)); ?>" class="wdk-listing-image <?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height <?php endif; ?>"></video>
                                </div>
                            </div>
                        <?php elseif (wdk_file_extension_type(wmvc_show_data('src', $image)) == 'image'): ?>
                            <div class="wdk-col">
                                <div class="wdk-listing-image-card">
                                    <img
                                        data-type="<?php echo esc_html($type); ?>"
                                        src="<?php echo esc_url(wmvc_show_data('src', $image)); ?>"
                                        class="wdk-listing-image<?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height<?php endif; ?>"
                                        alt="<?php echo esc_attr(wmvc_show_data('alt', $image)); ?>">
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if (!empty($images) && wmvc_show_data('layout_carousel_columns', $settings, 1) < wmvc_count($images)): ?>
                </div>
                <div class="wdk-listing-slider_arrows">
                    <a title="<?php echo esc_attr__('prev slider', 'wpdirectorykit'); ?>" href="#" class="wdk-slider-prev wdk-listing-slider_arrow">
                        <?php \Elementor\Icons_Manager::render_icon($settings['styles_carousel_arrows_icon_left'], ['aria-hidden' => 'true']); ?>
                    </a>
                    <a title="<?php echo esc_attr__('prev slider', 'wpdirectorykit'); ?>" href="#" class="wdk-slider-next wdk-listing-slider_arrow">
                        <?php \Elementor\Icons_Manager::render_icon($settings['styles_carousel_arrows_icon_right'], ['aria-hidden' => 'true']); ?>
                    </a>
                </div>
            </div>
        <?php else: ?>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<?php if (count($images) > 1): ?>
    <div class="banner-thumbs-con elementor-section elementor-section-boxed">
        <div class="elementor-container">
            <div class="banner-thumbs">
                <?php foreach ($images as $key => $image): ?>
                    <?php
                    $type = 'gallery';
                    if (strpos($key, '__') !== FALSE) {
                        $type = substr($key, 0, strpos($key, '__'));
                    }
                    ?>

                    <?php if (
                        is_string($image) && (
                            filter_var($image, FILTER_VALIDATE_URL) ||
                            preg_match('/^www\./i', $image)
                        )
                    ): ?>

                    <?php elseif (!wmvc_show_data('wdk_listing_video_disabled', $settings, false) && wdk_file_extension_type(wmvc_show_data('src', $image)) == 'video'): ?>
                        <div class="banner-thumb">
                            <video data-type="<?php echo esc_html($type); ?>" src="<?php echo esc_url(wmvc_show_data('src', $image)); ?>" alt="<?php echo esc_attr(wmvc_show_data('alt', $image)); ?>" class="wdk-listing-image <?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height <?php endif; ?>"></video>
                        </div>
                    <?php elseif (wdk_file_extension_type(wmvc_show_data('src', $image)) == 'image'): ?>
                        <div class="banner-thumb">
                            <img data-type="<?php echo esc_html($type); ?>" src="<?php echo esc_url(wmvc_show_data('src', $image)); ?>" class="wdk-listing-image <?php if ($settings['enable_fixed_height'] != 'yes'): ?> auto_height <?php endif; ?>" alt="<?php echo esc_attr(wmvc_show_data('alt', $image)); ?>">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div><!--banner-thumbs end-->
        </div>
    </div>
<?php endif; ?>
</div>
<script>
    var $slider, $thumbs, sliderSelector, thumbsSelector;
    jQuery(document).ready(function($) {
        sliderSelector = '#wdk_el_<?php echo esc_html($id_element); ?> .wdk_listing_slider_ini';
        thumbsSelector = '#wdk_el_<?php echo esc_html($id_element); ?> .banner-thumbs';

        // Initialize main slider and thumbs
        $slider = $(sliderSelector).slick({
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
            nextArrow: $('#wdk_el_<?php echo esc_html($id_element); ?> .wdk-listing-slider_arrows .wdk-slider-next'),
            prevArrow: $('#wdk_el_<?php echo esc_html($id_element); ?> .wdk-listing-slider_arrows .wdk-slider-prev'),
            customPaging: function(slider, i) {
                return '<span class="wdk_lr_dot"><?php \Elementor\Icons_Manager::render_icon($settings['styles_carousel_dots_icon'], ['aria-hidden' => 'true']); ?></span>';
            },
            responsive: [{
                breakpoint: 600,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }, ],
            // asNavFor: thumbsSelector,
        });

        $thumbs = $(thumbsSelector).slick({
            slidesToShow: <?php echo (!empty(trim(wmvc_show_data('styles_thmbn_nav_columns', $settings, '4')))) ? wmvc_show_data('styles_thmbn_nav_columns', $settings, '4') : 4; ?>,
            slidesToScroll: 1,
            //asNavFor: sliderSelector,
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
        $slider.on('afterChange', function(event, slick, currentSlide) {
            $thumbs.slick('slickGoTo', currentSlide);
        });

        $(thumbsSelector).on('click', '.slick-slide', function() {
            var index = $(this).data('slick-index');
            console.log(index);
            var index = $(this).index('.slick-slide:not(.slick-cloned)');
            console.log(index);

            $slider.slick('slickGoTo', index);
        });
        $slider.on('afterChange', function(event, slick, currentSlide) {

            $thumbs.find('.slick-slide').removeClass('is-active');

            $thumbs.find('.slick-slide[data-slick-index="' + currentSlide + '"]').addClass('is-active');

        });



        $(document).on('click', '[data-filter]', function(e) {
            e.preventDefault();

            var $btn = $(this);
            var filterType = $btn.data('filter');
            var isActive = $btn.hasClass('active');

            $('[data-filter]').removeClass('active');

            $slider.slick('slickUnfilter');
            $thumbs.slick('slickUnfilter');

            if (!isActive && filterType !== 'all') {

                $btn.addClass('active');

                $slider.slick('slickFilter', function() {
                    return $(this).find('.wdk-listing-image').data('type') === filterType;
                });

                $thumbs.slick('slickFilter', function() {
                    return $(this).find('.wdk-listing-image,video,iframe').data('type') === filterType;
                });
            }
        });
    })
</script>
</div>