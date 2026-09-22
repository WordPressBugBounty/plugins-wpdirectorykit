<?php
/**
 * The template for Edit Search Form.
 *
 * This is the template that form edit
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap wdk-wrap">

    <h1 class="wp-heading-inline"><?php echo esc_html__('Ai Search Designer','wpdirectorykit'); ?></h1>
    <br />

    <div class="wdk-body">
        <form method="post" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" novalidate="novalidate">
            <?php wp_nonce_field( 'wdk-searchform-edit_'.wmvc_show_data('idsearchform', $db_data, 1), '_wpnonce'); ?>

            <div class="postbox" style="display: block;">
                <div class="postbox-header">
                    <h3><?php echo esc_html__('AI Search Fields','wpdirectorykit'); ?></h3>
                </div>
                <div class="inside">
                    <p class="alert alert-info">
                        <?php echo esc_html__(
                            'Select the listing fields AI can use to understand and process search queries. For better performance and faster responses, we recommend selecting only the fields that are relevant to your search.',
                            'wpdirectorykit'
                        ); ?>
                    </p>
                    <div class="wdk-ai-fields-list">

                        <?php foreach ($fields as $field): ?>

                            <?php

                            $allowed_types = ['INPUTBOX', 'NUMBER', 'DROPDOWN', 'DROPDOWNMULTIPLE', 'CHECKBOX'];
                            if (!in_array($field->field_type, $allowed_types, true)) {
                                continue;
                            }
                       
                            $field_id = (int) $field->idfield;
                            ?>

                            <label class="wdk-ai-field">

                                <input
                                    type="checkbox"
                                    class="wdk-ai-field-checkbox"
                                    value="<?php echo esc_attr($field_id); ?>"
                                    <?php checked(in_array($field_id, array_map('intval', $selected_fields), true)); ?>
                                >

                                <span class="wdk-ai-field-label">
                                    <?php echo esc_html($field->field_label); ?>
                                </span>

                                <span class="wdk-ai-field-type">
                                    <?php echo esc_html($field->field_type); ?>
                                </span>

                            </label>

                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </form>
    </div>

</div>
<?php
wp_enqueue_style('wdk-notify');
wp_enqueue_script('wdk-notify');
?>

<script>
jQuery(document).ready(function ($) {

$(document).on(
    'change',
    '.wdk-ai-field-checkbox',
    function () {

        const selectedFields = [];

        $('.wdk-ai-field-checkbox:checked').each(function () {
            selectedFields.push($(this).val());
        });

        const $status = $('.wdk-ai-search-status');

        var ajax_param = {
            "page": 'wdk_backendajax',
            "function": 'wdk_save_ai_search_fields',
            "action": 'wdk_public_action',
            "_wpnonce": '<?php echo esc_js(wp_create_nonce('wdk-backendajax')); ?>',
            "fields": selectedFields,
        };

        $.ajax({
            url: "<?php echo esc_url(admin_url('admin-ajax.php')); ?>",
            type: 'POST',
            data: ajax_param,

            success: function (response) {
                if (response.popup_text_success)
                    wdk_log_notify(response.popup_text_success);

                if (response.popup_text_error)
                    wdk_log_notify(response.popup_text_error, 'error');
            },

            error: function () {
                $status
                    .addClass('error')
                    .text('Request failed');
            }

        });

    }
);
});
</script>
<?php $this->view('general/footer', $data); ?>