<?php

/**
 * The template for Edit Location.
 *
 * This is the template that form edit
 *
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap wdk-wrap">
    <h1 class="wp-heading-inline"><?php echo __('Location Management', 'wpdirectorykit'); ?></h1>
    <br /><br />
    <div class="wdk-body">
        <div class="postbox" style="display: block;">
            <div class="postbox-header">
                <h3><?php echo __('Add/Edit Location', 'wpdirectorykit'); ?></h3>

                <?php if (function_exists('run_wdk_svg_map') && wmvc_show_data('idlocation', $db_data, false)): ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wdk_location&function=import_from_svg&id=' . wmvc_show_data('idlocation', $db_data, false))); ?>" class="wdk-mr-5 button button-secondary alignright">
                        <span class="dashicons dashicons-admin-page" style="margin-top: 4px;"></span><?php echo esc_html__('Import sublocations from SVG Map', 'wpdirectorykit'); ?>
                    </a>
                <?php endif; ?>
            </div>
            <div class="inside">

                <form method="post" class="form_listing_ai" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" novalidate="novalidate">
                    <?php wp_nonce_field('wdk-location-edit_' . wmvc_show_data('idlocation', $db_data, 0), '_wpnonce'); ?>
                    <?php
                    $form->messages('class="alert alert-danger"',  __('Successfully saved', 'wpdirectorykit'));
                    ?>

                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="parent_id"><?php echo __('Parent', 'wpdirectorykit'); ?></label></th>
                                <td>
                                    <?php
                                    echo wmvc_select_option('parent_id', $parents, wmvc_show_data('parent_id', $db_data, ''), NULL, __('Root', 'wpdirectorykit'), '0');
                                    ?>

                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="location_title"><?php echo __('Title', 'wpdirectorykit'); ?></label></th>
                                <td><input name="location_title" type="text" id="location_title" value="<?php echo esc_attr(wmvc_show_data('location_title', $db_data, '')); ?>" class="regular-text"></td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="titles_for_search"><?php echo __('Alternative titles for search', 'wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <input name="titles_for_search" type="text" id="titles_for_search"
                                        value="<?php echo esc_attr(wmvc_show_data('titles_for_search', $db_data, '')); ?>"
                                        class="regular-text">
                                    <p class="description" id="order_index-description">
                                        <?php echo __('This can be used for search in different languages, please enter titles like: "Croatia,Hrvatska" so supporting multiple titles', 'wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="order_index"><?php echo __('Order Index', 'wpdirectorykit'); ?></label></th>
                                <td>
                                    <input name="order_index" type="text" id="order_index" value="<?php echo esc_attr(wmvc_show_data('order_index', $db_data, '')); ?>" class="regular-text">
                                    <p class="description" id="order_index-description"><?php echo __('Index for sorting/ordering, you can leave it empty and will be auto added to end of parent list', 'wpdirectorykit'); ?></p>
                                </td>
                            </tr>
                            <?php if (function_exists('run_wdk_svg_map')): ?>
                                <tr>
                                    <th scope="row"><label for="related_svg_map"><?php echo __('Related SVG Map', 'wpdirectorykit'); ?></label></th>
                                    <td>
                                        <?php
                                        echo wmvc_select_option('related_svg_map', $maps_list, wmvc_show_data('related_svg_map', $db_data, ''), "id='related_svg_map'", __('Not Selected', 'wpdirectorykit'), '0');
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="related_svg_map_location"><?php echo __('Related SVG Map Location', 'wpdirectorykit'); ?></label></th>
                                    <td>
                                        <?php
                                        echo wmvc_select_option('related_svg_map_location', $map_related_locations, wmvc_show_data('related_svg_map_location', $db_data, ''), "id='related_svg_map_location'", __('Not Selected', 'wpdirectorykit'), '0');
                                        ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th scope="row"><label for="icon_id"><?php echo __('Icon', 'wpdirectorykit'); ?></label></th>
                                <td>
                                    <?php
                                    echo wmvc_upload_media('icon_id', wmvc_show_data('icon_id', $db_data, ''));
                                    ?>
                                    <p class="description" id="icon_id-description"><?php echo __('Icon used for marker/pin on map or special places on website', 'wpdirectorykit'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="image_id"><?php echo __('Image', 'wpdirectorykit'); ?></label></th>
                                <td>
                                    <?php
                                    echo wmvc_upload_media('image_id', wmvc_show_data('image_id', $db_data, ''));
                                    ?>
                                    <p class="description" id="image_id-description"><?php echo __('Image used for widgets or elements where categories are visible', 'wpdirectorykit'); ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html__('Save Changes', 'wpdirectorykit'); ?>">
                </form>
            </div>
        </div>
        <div class="postbox postbox-map" style="<?php if (wmvc_show_data('related_svg_map', $db_data, '')): ?>display: block;<?php else: ?>display: none;<?php endif; ?>">
            <div class="postbox-header">
                <h3><?php echo __('Map', 'wpdirectorykit'); ?></h3>
            </div>
            <div class="inside">
                <div class="wdk-row">
                    <div class="wdk-col-auto col-list">
                        <div class="map-list">
                        </div>
                    </div>
                    <div class="wdk-col-auto col-map">
                        <div class="wdk-svg-map-box">
                            <div class="wdk_svg_map" data-map="<?php echo wmvc_show_data('related_svg_map', $db_data, ''); ?>"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-info" role="alert"><?php echo sprintf(__('%1$s How to add new country in locations? %2$s', 'wpdirectorykit'), '<a href="//wpdirectorykit.com/documentation/#add-new-country" target="_blank">', '</a>'); ?></div>
    </div>
</div>


<?php if(function_exists('run_wdk_svg_map')):?>
<script>
    var jqxhr = null;
    jQuery(document).ready(function($) {
        $('#related_svg_map').on('input', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var self = $(this);

            jQuery('#related_svg_map_location').closest('tr').find('label').addClass('wdk_btn_load_indicator out');

            var ajax_param = {
                "action": 'wdk_svg_map_public_action',
                "page": 'wdk_svg_map_frontendajax',
                "function": 'get_map_data',
            };
            ajax_param['related_svg_map'] = $(this).val();

            // Assign handlers immediately after making the request,
            // and remember the jqxhr object for this request
            if (jqxhr != null)
                jqxhr.abort();

            jqxhr = $.post("<?php echo admin_url('admin-ajax.php'); ?>", ajax_param,
                function(data) {
                    var select_list = '<option value=""> <?php echo esc_js('Not Selected', 'wpdirectorykit'); ?> </option>';
                    jQuery.each(data.output.locations, function(i, v) {
                        select_list += '<option value="' + i + '"> ' + v + ' </option>';
                    });

                    jQuery('#related_svg_map_location').html(select_list);

                }).always(function(data) {
                jQuery('#related_svg_map_location').closest('tr').find('label').removeClass('wdk_btn_load_indicator out');
            });
            return false;
        })
    })
</script>


<?php
wp_enqueue_style('wdk-svg-map');
wp_enqueue_style('wdk-geo-map-lib', WDK_SVG_MAP_URL . 'public/css/wdk-geo-map-lib.css', array(), 1.0, 'all');
wp_enqueue_script('wdk-geo-map-lib', WDK_SVG_MAP_URL . 'public/js/wdk-geo-map-lib.js', array('jquery'), 1.0, false);
wp_enqueue_style('dashicons');

$params = array(
    'ajax_url' => admin_url('admin-ajax.php')
);
wp_localize_script('wdk-geo-map-lib', 'wdk_geo_map_script_parameters', $params);

?>

<script>
    jQuery(document).ready(function($) {
        var that = $('.postbox.postbox-map .inside');
        var geomap = that.find('.wdk_svg_map').WdkSvgMap('set_config', {
            'base_path': '<?php echo esc_js(WDK_SVG_MAP_PACS_URL); ?>',
            'color_hover': '#2271b1',
            'color_active': '#2271b1',
            'color_border_hover': '#2271b1',
            'color_border_active': '#2271b1',
        });

        /* events */
        geomap.on('map_generated.WdkSvgMap', function(event) {
            var map_back = that.find('.map-back');
            if (event.tree_maps.length) {
                var names = '';
                for (let el of event.tree_maps) {
                    if (names != '')
                        names += ' - ';
                    names += el.location_name;
                }
                map_back.find('.name').html(names);

                map_back.show().off().on('click', function(e) {
                    e.preventDefault();
                    geomap.WdkSvgMap('set_pre_map');
                });
            } else {
                map_back.hide();
            }

            var html = "";
            if (event.locations_list) {
                html += "<ul>"
                jQuery.each(event.locations_list, function(index, value) {
                    html += "<li>" + value + "</li>"
                })
                html += "</ul>"
            }

            jQuery('.map-list').html(html);

        });

        geomap.WdkSvgMap('generate_map', that.find('.wdk_svg_map').attr('data-map'));

        $('#related_svg_map').on('input', function(e) {
            e.preventDefault();

            if ($(this).val() != '' && $(this).val() != 0) {
                jQuery('.postbox.postbox-map').show();
            } else {
                jQuery('.postbox.postbox-map').hide();
            }

            geomap.WdkSvgMap('generate_map', $(this).val());
        });

    });
</script>

<?php endif;?>

<?php $this->view('general/footer', $data); ?>