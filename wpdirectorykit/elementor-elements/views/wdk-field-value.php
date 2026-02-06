<?php

/**
 * The template for Element Listing Field Value.
 * This is the template that elementor element, link, iframe, images
 *
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

?>

<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element); ?>">


    <?php if(wmvc_show_data('show_like_stars', $settings) == 'yes'):?>
        <?php 
            global $wdk_listing_id;
            $stars = round(floatval(wdk_field_value( wmvc_show_data('field_id', $settings), $wdk_listing_id)),2);
            ?>
        <span class="wdk-field-value-stars-lst">
            <?php for ($i = 1; $i <= (int)wmvc_show_data('max_stars', $settings); $i++): ?>
                <?php if ($i <=$stars): ?>
                    <span><?php \Elementor\Icons_Manager::render_icon( $settings['active_star_icon'], [ 'aria-hidden' => 'true', 'class' => 'star-active' ] ); ?></span>
                <?php elseif( abs($stars - $i) < 1): ?>
                    <span><i class="fas fa-star-half-alt"></i></span>
                <?php else: ?>
                    <span><?php \Elementor\Icons_Manager::render_icon( $settings['inactive_star_icon'], [ 'aria-hidden' => 'true', 'class' => 'innactive' ] ); ?></span>
                <?php endif; ?>
            <?php endfor; ?>
        </span>
    <?php elseif (wdk_field_option(wmvc_show_data('field_id', $settings), 'field_type') == 'FILEUPLOAD'): ?>
        <div class="wdk-field-files <?php if (wmvc_show_data('enable_js_gallery', $settings, false, TRUE, TRUE)): ?> wdk_js_gallery <?php endif; ?>">
            <?php if (is_array($field_value) && count($field_value) > 0):  ?>
                <div class="wdk-row">
                    <?php foreach ($field_value as $image): ?>
                        <?php
                        if ($is_edit_mode) {
                            $src = $image;
                            $image = array('src' => $src, 'title' => esc_html__('Image Title', 'wpdirectorykit'));
                        } else {
                            $src = $image['src'];
                        }
                        ?>
                        <?php
                        if (!in_array(wdk_file_extension($image['src']), array('jpg', 'jpeg', 'bmp', 'png', 'webp')))
                            continue;
                        ?>

                        <div class="wdk-col">
                            <figure>
                                <a class="wdk-listing-image-card" href="<?php echo esc_url($image['src']); ?>">
                                    <img src="<?php echo esc_url($src); ?>" class="wdk-listing-image" alt="<?php echo esc_attr(wmvc_show_data('alt', $image, '', TRUE, TRUE)); ?>">
                                </a>
                                <?php if (false): ?>
                                    <figcaption>
                                        <a class="skip" href="<?php echo esc_url($image['src']); ?>" target="_blank">
                                            <?php echo esc_html(wmvc_show_data('title', $image, '', TRUE, TRUE)); ?>
                                        </a>
                                    </figcaption>
                                <?php endif; ?>
                            </figure>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (is_array($field_value) && count($field_value) > 0):  ?>
                <div class="files-row">
                    <ul class="files">
                        <?php foreach ($field_value as $image): ?>
                            <?php
                            if ($is_edit_mode) {
                                $src = $image;
                                $image = array('src' => $src, 'title' => esc_html__('Image Title', 'wpdirectorykit'));
                            } else {
                                $src = $image['src'];
                            }

                            if (in_array(wdk_file_extension($image['src']), array('jpg', 'jpeg', 'bmp', 'png', 'webp')))
                                continue;

                            if (file_exists(WPDIRECTORYKIT_PATH . '/public/img/filetype/' . wdk_file_extension($image['src']) . '.png')) {
                                $src = WPDIRECTORYKIT_URL . 'public/img/filetype/' . wdk_file_extension($image['src']) . '.png';
                            } else {
                                $src = WPDIRECTORYKIT_URL . 'public/img/filetype/_blank.png';
                            }
                            ?>

                            <li class="list-item">
                                <a class="file-link" href="<?php echo esc_url($image['src']); ?>" target="_blank" title="<?php echo esc_attr(wmvc_show_data('title', $image, '', TRUE, TRUE)); ?>">
                                    <img src="<?php echo esc_url($src); ?>" class="wdk-listing-file-icon" alt="<?php echo esc_attr(wmvc_show_data('alt', $image, '', TRUE, TRUE)); ?>">
                                    <?php echo esc_html(wmvc_show_data('title', $image, '', TRUE, TRUE)); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="wdk-field-value <?php echo wp_kses_post(wdk_field_option(wmvc_show_data('field_id', $settings), 'field_type')); ?> <?php if (wmvc_show_data('field_id', $settings) == 'post_content'): ?> TEXTAREA <?php endif; ?>">
            <?php if (wmvc_show_data('cover_in_listing_link', $settings) == 'yes'): ?>
                <?php
                global $wdk_listing_id;
                $url = '#';
                if (wdk_is_listing_page_enabled() && isset($wdk_listing_id)) {
                    $url = get_permalink($wdk_listing_id);
                }
                ?>
                <a href="<?php echo $url; ?>">
                    <span class='prefix'><?php echo esc_html($field_prefix); ?></span>
                    <span class="value <?php if (wmvc_show_data('text_limit_per_line', $settings)): ?> wdk-stroke <?php endif; ?>">
                        <?php if (wmvc_show_data('text_limit_worlds', $settings)): ?>
                            <?php echo (wp_strip_all_tags(html_entity_decode(wp_trim_words($field_value, $settings['text_limit_worlds'], '...')))); ?>
                        <?php else: ?>
                            <?php echo wp_kses_post(wdk_filter_decimal($field_value)); ?>
                        <?php endif; ?>
                    </span>
                    <span class='suffix'><?php echo esc_html($field_suffix); ?></span>
                </a>

            <?php else: ?>
                <?php echo empty(wmvc_show_data('html_tag', $settings, 'span')) ? '<span>' : '<' . wmvc_show_data('html_tag', $settings, 'span') . '>'; ?>
                <span class='prefix'><?php echo esc_html($field_prefix); ?></span>


                <?php if (stripos($field_value, 'class="value"') !== FALSE): ?>
                    <?php echo wp_kses_post(wdk_filter_decimal($field_value)); ?>
                <?php else: ?>
                    <span class="value <?php if (wmvc_show_data('text_limit_per_line', $settings)): ?> wdk-stroke <?php endif; ?>">
                        <?php if (wmvc_show_data('text_limit_worlds', $settings)): ?>
                            <?php echo (wp_strip_all_tags(html_entity_decode(wp_trim_words($field_value, $settings['text_limit_worlds'], '...')))); ?>
                        <?php else: ?>
                            <?php echo wp_kses_post(wdk_filter_decimal($field_value)); ?>
                        <?php endif; ?>
                    </span>
                <?php endif; ?>

                <span class='suffix'><?php echo esc_html($field_suffix); ?></span>
                <?php echo empty(wmvc_show_data('html_tag', $settings, 'span')) ? '</span>' : '</' . wmvc_show_data('html_tag', $settings, 'span') . '>'; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>