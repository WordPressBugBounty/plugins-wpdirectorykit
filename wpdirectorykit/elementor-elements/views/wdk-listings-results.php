<?php
/**
 * The template for Element Listings Results.
 * This is the template that elementor element listings, list, grid
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-listings-results 
    <?php echo wmvc_show_data('styles_thmbn_des_type',$settings, '');?> view-<?php echo wmvc_show_data('layout_type',$settings, '');?>
    <?php if(wmvc_show_data('enable_separed_styles',$settings, '') == true):?> enable_separed_styles <?php endif;?>
        <?php if
            (
                wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_ajax_results') &&
                isset($settings['is_ajax_enable']) && $settings['is_ajax_enable'] == 'yes'
            ):?>
                ajax_results_enabled
        <?php endif;?>
    " 
    data-el_id="<?php echo esc_attr($this->get_id());?>" 
    data-el_type="<?php echo esc_attr($this->get_name());?>" 
    <?php
        $post_id = get_the_ID();
        $post_object_id = get_queried_object_id();
        if($post_object_id)
        $post_id = $post_object_id;
        
        global $wdk_listing_page_id;
        if(!empty($wdk_listing_page_id))
        $post_id = $wdk_listing_page_id;
    ?>
    data-el_page_id="<?php echo esc_attr($post_id);?>"

    <?php if($settings['get_filters_enable'] == 'yes'):?> id="results" <?php endif;?>>
        <?php if($settings['get_filters_enable'] == 'yes'
        && !(
            $settings['sorting_enable'] != 'yes' &&
            $settings['view_type_enable'] != 'yes' &&
            $settings['show_numbers_results_enable'] != 'yes'
        )):?>
        <div class="wdk-filter-head">
            <?php if($settings['sorting_enable'] == 'yes'):?>
                <div class="filter-group order">
                    <?php \Elementor\Icons_Manager::render_icon( $settings['filter_group_box_icon'], [ 'aria-hidden' => 'true' ] );?>
                    <select name="order_by" class='wdk-order'>
                        <?php if(!empty($custom_order)):?>
                            <?php foreach ($custom_order as $item):?>
                                <?php
                                    if (strpos($item['key'], '__') !== FALSE) {
                                        $item['key'] = substr($item['key'], strpos($item['key'], '__') + 2);
                                    }
                                    if(!empty($item['order_type'])) {
                                        $item['key'] .= ' '.$item['order_type'];
                                    }
                                ?>
                                <option value="<?php echo esc_attr($item['key']);?>" <?php echo (wmvc_show_data('order_by', $_GET, '') == $item['key'])?'selected="selected"':''; ?>><?php echo esc_html__(wmvc_show_data('title', $item), 'wpdirectorykit');?></option>
                            <?php endforeach;?>
                        <?php else:?>
                            <option value="post_id DESC" <?php echo !empty(wmvc_show_data('order_by', $_GET, '') =='post_id DESC')?'selected="selected"':''; ?>><?php echo esc_html__('Sort by: Latest', 'wpdirectorykit');?></option>
                            <option value="post_id ASC" <?php echo (wmvc_show_data('order_by', $_GET, '') =='post_id ASC')?'selected="selected"':''; ?>><?php echo esc_html__('Sort by: Oldest', 'wpdirectorykit');?></option>
                            <option value="post_title ASC" <?php echo !empty(wmvc_show_data('order_by', $_GET, '') =='post_title ASC')?'selected="selected"':''; ?>><?php echo esc_html__('Sort by: Title Asc', 'wpdirectorykit');?></option>
                            <option value="post_title DESC" <?php echo !empty(wmvc_show_data('order_by', $_GET, '') =='post_title DESC')?'selected="selected"':''; ?>><?php echo esc_html__('Sort by: Title Desc', 'wpdirectorykit');?></option>
                        <?php endif;?>
                    </select>
                </div>
            <?php endif;?>
            <?php if($settings['view_type_enable'] == 'yes'):?>
                <div class="filter-group wmvc-view-type">
                    <a class="nav-link <?php echo (wmvc_show_data('layout_type', $settings, '') =='list')?'active':''; ?>" data-id="list" href="#list-view"><i class="fa fa-list-ul"></i></a>
                    <a class="nav-link <?php echo (wmvc_show_data('layout_type', $settings, '') =='grid')?'active':''; ?>" data-id="grid" href="#grid-view"><i class="fa fa-th"></i></a>
                </div>
            <?php endif;?>
            <?php if($settings['show_numbers_results_enable'] == 'yes'):?>
                <div class="filter-group filter-status">
                    <span><?php echo esc_html($listings_count);?> <?php echo esc_html__('Listings', 'wpdirectorykit');?></span>
                </div>
            <?php endif;?>
        </div>
        <?php endif;?>
        <?php if(!empty($results)):?>
            <?php if($settings['layout_type'] == 'carousel'):?>
                <div class="wdk_results_listings_slider_box <?php echo esc_attr($settings['layout_carousel_animation_style']).'_animation';?> <?php echo join(' ', [$settings['styles_carousel_dots_position_style'], $settings['styles_carousel_arrows_position']]);?>">
                <div class="wdk_results_listings_slider_body">
                <div class="wdk_results_listings_slider_ini">
            <?php else:?>
                <div class="wdk-row wdk-inner-listings-results <?php if(isset($settings['is_mobile_view_enable']) && $settings['is_mobile_view_enable'] == 'yes'):?> WdkScrollMobileSwipe_enable <?php endif;?>">
            <?php endif;?>
            <?php foreach($results as $listing):?>
                <div class="wdk-col">
                    <div class="wdk-card--wrapper <?php if(isset($settings['is_complete_link']) && $settings['is_complete_link'] == 'yes'):?> hover_link  elementor-clickable <?php endif;?>" <?php if(isset($settings['is_complete_link']) && $settings['is_complete_link'] == 'yes'):?> onclick="location.href='<?php echo esc_url( get_permalink($listing) ); ?>'" <?php endif;?>>
                    <?php if
                    (
                        wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_listing_card_elementor_layout') &&
                        isset($settings['is_custom_layout_enable']) && $settings['is_custom_layout_enable'] == 'yes'
                        && isset($settings['custom_layout_id_list']) && isset($settings['custom_layout_id_grid'])
                    ):?>
                    <?php if($settings['layout_type'] == 'list' && (!empty($settings['custom_layout_id_list']) || !empty($settings['custom_layout_live_list']))):?>
                    <?php
                        $content = '';
                        global $wdk_listing_id;
                        $wdk_listing_id = wmvc_show_data('post_id', $listing);
                        $content = wdk_render_elementor_template( empty( $settings['custom_layout_id_list'] ) ? $settings['custom_layout_live_list'] : $settings['custom_layout_id_list'], true);
                    ?>
                    <?php if(true || !$is_edit_mode):?>
                        <?php
                        $allowed_tags = array_merge(
                            wp_kses_allowed_html('post'),
                            array('style' => array())
                        );
                        echo wp_kses($content, $allowed_tags);
                        ?>
                    <?php else:?>
                        <?php echo esc_html__( 'Listings Card Grid From Extern Layout', 'wpdirectorykit' );?>
                    <?php endif;?>
                    <?php elseif(($settings['layout_type'] == 'grid' || $settings['layout_type'] == 'carousel')  && (!empty($settings['custom_layout_id_grid']) || !empty($settings['custom_layout_live_grid']))):?>
                    <?php
                        $content = '';
                        global $wdk_listing_id;
                        $wdk_listing_id = wmvc_show_data('post_id', $listing);
                        $content = wdk_render_elementor_template( empty( $settings['custom_layout_id_grid'] ) ? $settings['custom_layout_live_grid'] : $settings['custom_layout_id_grid'], true);
                    ?>
                    <?php if(true || !$is_edit_mode):?>
                        <?php
                        $allowed_tags = array_merge(
                            wp_kses_allowed_html('post'),
                            array('style' => array())
                        );
                        echo wp_kses($content, $allowed_tags);
                        ?>
                    <?php else:?>
                        <?php echo esc_html__( 'Listings Card List From Extern Layout', 'wpdirectorykit' );?>
                    <?php endif;?>
                    <?php else:?>
                        <?php echo wdk_listing_card($listing, $settings);?>
                        <?php endif;?>
                        <?php else:?>
                            <?php echo wdk_listing_card($listing, $settings);?>
                            <?php endif;?>
                        </div>
                </div>
            <?php endforeach;?> 
            <?php if($settings['layout_type'] == 'carousel'):?>
                </div>
                    <div class="wdk_slider_arrows">
                        <a title="<?php echo esc_attr__('prev slider', 'wpdirectorykit');?>" href="#" class="wdk-slider-prev wdk_lr_slider_arrow">
                            <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_left'], [ 'aria-hidden' => 'true' ] ); ?>
                        </a>
                        <a title="<?php echo esc_attr__('next slider', 'wpdirectorykit');?>" href="#" class="wdk-slider-next wdk_lr_slider_arrow">
                            <?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_arrows_icon_right'], [ 'aria-hidden' => 'true' ] ); ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php else:?>
                </div>
            <?php endif;?>
        <?php else:?>
            <p class="wdk_alert wdk_alert-danger"><?php echo esc_html__('Results not found', 'wpdirectorykit');?></p>
        <?php endif;?>
        <?php if($settings['layout_type'] != 'carousel'):?>
            <?php echo wmvc_xss_clean($pagination_output); ?>
        <?php endif;?>
    </div>
    <?php if($settings['layout_type'] == 'carousel'):?>
    <script>
        jQuery(document).ready(function($){
            var el = $('#wdk_el_<?php echo esc_html($id_element);?> .wdk_results_listings_slider_ini').slick({
                dots: true,
                arrows: true,
                rtl: localStorage.getItem('siteDirection') == "rtl" ? true : false,
                <?php if(!empty(wmvc_show_data('layout_carousel_is_centerMode', $settings))):?>
                centerMode: <?php echo wmvc_show_data('layout_carousel_is_centerMode', $settings, 'true');?>,
                <?php endif;?>
                slidesToShow: <?php echo (!empty(trim(wmvc_show_data('layout_carousel_columns', $settings, '3')))) ? wmvc_show_data('layout_carousel_columns', $settings, '3') : 3;?>,
                slidesToScroll: <?php echo (!empty(trim(wmvc_show_data('layout_carousel_columns', $settings, '3')))) ? wmvc_show_data('layout_carousel_columns', $settings, '3') : 3;?>,
                <?php if(!empty(wmvc_show_data('layout_carousel_is_infinite', $settings))):?>
                infinite: <?php echo wmvc_show_data('layout_carousel_is_infinite', $settings, 'true');?>,
                <?php endif;?>
                <?php if(!empty(wmvc_show_data('layout_carousel_is_autoplay', $settings))):?>
                autoplay: <?php echo wmvc_show_data('layout_carousel_is_autoplay', $settings, 'false');?>,
                <?php endif;?>
                nextArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk_slider_arrows .wdk-slider-next'),
                prevArrow: $('#wdk_el_<?php echo esc_html($id_element);?> .wdk_slider_arrows .wdk-slider-prev'),
                customPaging: function(slider, i) {
                    // this example would render "tabs" with titles
                    return '<span class="wdk_lr_dot"><?php \Elementor\Icons_Manager::render_icon( $settings['styles_carousel_dots_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>';
                },
                responsive: [
                    {
                        breakpoint: 991,
                        settings: {
                            slidesToShow: <?php echo (!empty(trim(wmvc_show_data('layout_carousel_columns_tablet', $settings, '2')))) ? wmvc_show_data('layout_carousel_columns_tablet', $settings, '2') : 2;?>,
                            slidesToScroll: <?php echo (!empty(trim(wmvc_show_data('layout_carousel_columns_tablet', $settings, '2')))) ? wmvc_show_data('layout_carousel_columns_tablet', $settings, '2') : 2;?>,
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: <?php echo (!empty(trim(wmvc_show_data('layout_carousel_columns_mobile', $settings, '1')))) ? wmvc_show_data('layout_carousel_columns_mobile', $settings, '1') : 1;?>,
                            slidesToScroll: <?php echo (!empty(trim(wmvc_show_data('layout_carousel_columns_mobile', $settings, '1')))) ? wmvc_show_data('layout_carousel_columns_mobile', $settings, '1') : 1;?>,
                        }
                    },
                ]
            }).on('breakpoint', function(event, slick, breakpoint){
                wdk_result_listings_thumbnail_slider(el);
                
                if (typeof wdk_favorite == 'function') {
                    wdk_favorite('.wdk_results_listings_slider_ini');
                }
                
                if (typeof wdk_init_compare_elem == 'function') {
                    wdk_init_compare_elem();
                }

                if (typeof wdk_popup_listings == 'function') {
                    wdk_popup_listings();
                }

            });

            wdk_slick_slider_init(el, ()=>{
                wdk_result_listings_thumbnail_slider(el);
                                
                if (typeof wdk_favorite == 'function') {
                    wdk_favorite('.wdk_results_listings_slider_ini');
                }
                
                if (typeof wdk_init_compare_elem == 'function') {
                    wdk_init_compare_elem();
                }

                
                if (typeof wdk_popup_listings == 'function') {
                    wdk_popup_listings();
                }
            });
        })
    </script>
    <?php endif;?>
    <?php if($is_edit_mode):?>
    <script>
        jQuery(document).ready(function($){
            if(typeof wdk_result_listings_thumbnail_slider == 'function') {
                wdk_result_listings_thumbnail_slider();
            }
        })
    </script>
    <?php endif;?>
</div>

