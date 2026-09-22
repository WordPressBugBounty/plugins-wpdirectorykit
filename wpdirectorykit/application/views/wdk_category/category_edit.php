<?php

/**
 * The template for Edit Category.
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
    <h1 class="wp-heading-inline"><?php echo esc_html__('Category Management', 'wpdirectorykit'); ?></h1>
    <br />

    <div class="wdk-body">
        <div class="postbox" style="display: block;">
            <div class="postbox-header">
                <h3><?php echo esc_html__('Add/Edit Category', 'wpdirectorykit'); ?></h3>
            </div>
            <div class="inside">
                <form method="post" class="form_listing_ai" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" novalidate="novalidate">
                    <?php wp_nonce_field('wdk-category-edit_' . wmvc_show_data('idcategory', $db_data, 0), '_wpnonce'); ?>
                    <?php
                    $form->messages('class="alert alert-danger"',  __('Successfully saved', 'wpdirectorykit'));
                    ?>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label
                                        for="parent_id"><?php echo esc_html__('Parent', 'wpdirectorykit'); ?></label></th>
                                <td>
                                    <?php
                                    echo wp_kses(
                                        wmvc_select_option(
                                            'parent_id',
                                            $parents,
                                            wmvc_show_data('parent_id', $db_data, ''),
                                            NULL,
                                            __('Root', 'wpdirectorykit'),
                                            '0'
                                        ),
                                        array(
                                            'select' => array(
                                                'name' => true,
                                                'id' => true,
                                                'class' => true,
                                                'autocomplete' => true,
                                                'tabindex' => true,
                                            ),
                                            'option' => array(
                                                'value' => true,
                                                'selected' => true,
                                                'disabled' => true,
                                                'label' => true,
                                            ),
                                        )
                                    );
                               
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="category_title"><?php echo esc_html__('Title', 'wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <input name="category_title" type="text" id="category_title"
                                        value="<?php echo esc_attr(wmvc_show_data('category_title', $db_data, '')); ?>"
                                        class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="titles_for_search"><?php echo esc_html__('Alternative titles for search', 'wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <input name="titles_for_search" type="text" id="titles_for_search"
                                        value="<?php echo esc_attr(wmvc_show_data('titles_for_search', $db_data, '')); ?>"
                                        class="regular-text">
                                    <p class="description" id="order_index-description">
                                        <?php echo esc_html__('This can be used for search in different languages, please enter titles like: "Croatia,Hrvatska" so supporting multiple titles', 'wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="order_index"><?php echo esc_html__('Order Index', 'wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <input name="order_index" type="text" id="order_index"
                                        value="<?php echo esc_attr(wmvc_show_data('order_index', $db_data, '')); ?>"
                                        class="regular-text">
                                    <p class="description" id="order_index-description">
                                        <?php echo esc_html__('Index for sorting/ordering, you can leave it empty and will be auto added to end of parent list', 'wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="font_icon_code"><?php echo esc_html__('Font icon code', 'wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <div class="wdk-field-edit LISTING edittable">
                                        <div class="wdk-field-container">
                                            <?php echo wp_kses(
                                                wdk_treefield_option(
                                                    'font_icon_code',
                                                    'icons_list',
                                                    wmvc_show_data('font_icon_code', $db_data, ''),
                                                    'icon',
                                                    '',
                                                    __('Not Selected', 'wpdirectorykit'),
                                                    '',
                                                    TRUE
                                                ),
                                                array(
                                                    'select' => array(
                                                        'name' => true,
                                                        'id' => true,
                                                        'class' => true,
                                                        'autocomplete' => true,
                                                        'tabindex' => true,
                                                    ),
                                                    'option' => array(
                                                        'value' => true,
                                                        'selected' => true,
                                                        'disabled' => true,
                                                        'label' => true,
                                                    ),
                                                    'span' => array(
                                                        'class' => true,
                                                    ),
                                                    'i' => array(
                                                        'class' => true,
                                                    ),
                                                )
                                            ); ?>
                                       
                                        </div>
                                    </div>
                                    <p class="description" id="font_icon_code-description">
                                        <?php echo esc_html__('Icon used for marker/pin on map or special places on website, you can found icon classes on font awesome website', 'wpdirectorykit'); ?>
                                        <br><a href="https://fontawesome.com/" target="_blank"> <?php echo esc_html__('Font Awesome icon', 'wpdirectorykit'); ?></a>
                                        <br><?php echo esc_html__('Example class/code', 'wpdirectorykit'); ?>: "fas fa-band-aid"
                                        <br><?php echo esc_html__('Will be used only if "Custom Map Marker Image" is not uploaded', 'wpdirectorykit'); ?>
                                    </p>

                                </td>

                            </tr>
                            <tr>
                                <th scope="row"><label
                                        for="marker_image_id"><?php echo esc_html__('Custom Map Marker Image', 'wpdirectorykit'); ?></label></th>
                                <td>
                                    <?php
                                    echo wp_kses(
                                        wmvc_upload_media('marker_image_id', wmvc_show_data('marker_image_id', $db_data, '')),
                                        array(
                                            'input' => array(
                                                'type' => true,
                                                'name' => true,
                                                'id' => true,
                                                'value' => true,
                                                'class' => true,
                                                'style' => true,
                                                'accept' => true,
                                                'data-*' => true,
                                            ),
                                            'button' => array(
                                                'type' => true,
                                                'id' => true,
                                                'class' => true,
                                                'name' => true,
                                                'value' => true,
                                                'aria-label' => true,
                                                'data-*' => true,
                                            ),
                                            'img' => array(
                                                'src' => true,
                                                'alt' => true,
                                                'width' => true,
                                                'height' => true,
                                                'class' => true,
                                                'id' => true,
                                                'style' => true,
                                            ),
                                            'div' => array(
                                                'class' => true,
                                                'id' => true,
                                                'style' => true,
                                                'data-*' => true,
                                            ),
                                            'a' => array(
                                                'href' => true,
                                                'class' => true,
                                                'id' => true,
                                                'title' => true,
                                                'rel' => true,
                                                'style' => true,
                                                'target' => true,
                                                'data-*' => true,
                                            ),
                                            'span' => array(
                                                'class' => true,
                                                'id' => true,
                                                'style' => true,
                                            ),
                                            'br' => array(),
                                            'strong' => array(),
                                            'em' => array(),
                                        )
                                    );
                               
                                    ?>
                                    <p class="description" id="marker_image_id-description">
                                        <?php echo esc_html__('Image used for marker/pin on map', 'wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="icon_id"><?php echo esc_html__('Icon', 'wpdirectorykit'); ?></label></th>
                                <td>
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.I18n.NonSingularStringLiteralText
                                    echo wmvc_upload_media('icon_id', wmvc_show_data('icon_id', $db_data, ''));
                                    ?>
                                    <p class="description" id="icon_id-description">
                                        <?php echo esc_html__('Icon used for category sections and elements/widgets on website', 'wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label
                                        for="image_id"><?php echo esc_html__('Image', 'wpdirectorykit'); ?></label></th>
                                <td>
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.I18n.NonSingularStringLiteralText
                                    echo wmvc_upload_media('image_id', wmvc_show_data('image_id', $db_data, ''));
                                    ?>
                                    <p class="description" id="image_id-description">
                                        <?php echo esc_html__('Image used for widgets or elements where categories are visible', 'wpdirectorykit'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="category_color"><?php echo esc_html__('Color', 'wpdirectorykit'); ?></label>
                                </th>
                                <td>
                                    <input name="category_color" type="color" id="category_color"
                                        value="<?php echo esc_attr(wmvc_show_data('category_color', $db_data, '')); ?>"
                                        class="regular-text">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr__('Save Changes', 'wpdirectorykit'); ?>">
                </form>
            </div>
        </div>
    </div>

    <?php do_action('wpdirectorykit/admin/category/edit/after_form', $db_data); ?>
</div>
<?php
if (defined('ELEMENTOR_ASSETS_URL')) {
    wp_register_style(
        'elementor-font-awesome',
        ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/font-awesome.min.css',
        [],
        '4.7.0'
    );
    wp_register_style(
        'elementor-font-awesome-solid',
        ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/solid.css'
    );
    wp_register_style(
        'elementor-font-awesome-brands',
        ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/brands.css'
    );
    wp_register_style(
        'elementor-font-awesome-regular',
        ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/regular.css'
    );
    wp_register_style(
        'elementor-font-awesome-5',
        ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/fontawesome.css'
    );
}

wp_enqueue_style('elementor-font-awesome-regular');
wp_enqueue_style('elementor-font-awesome-brands');
wp_enqueue_style('elementor-font-awesome-solid');
wp_enqueue_style('elementor-font-awesome-5');
wp_enqueue_style('elementor-font-awesome');

?>

<?php $this->view('general/footer', $data); ?>