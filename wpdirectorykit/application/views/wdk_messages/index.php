<?php
/**
 * The template for Messages Management.
 *
 * This is the template that table, search layout
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap wdk-wrap">

    <h1 class="wp-heading-inline"><?php echo esc_html__('Messages Management', 'wpdirectorykit'); ?></h1>
    <?php
        $success_message = NULL;
        if(isset($_GET['custom_message']))
            $success_message = esc_html(urldecode($_GET['custom_message']));

        $form->messages('class="alert alert-danger"', $success_message);
    ?>
    <form method="GET" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" novalidate="novalidate">
        <div class="tablenav top">
            <div class="alignleft actions">
                <input type="hidden" name="page" value="wdk_messages" />
                <label class="screen-reader-text" for="search"><?php echo esc_html__('Filter by keyword', 'wpdirectorykit'); ?></label>
                <input type="text" name="search" id="search" class="postform left" value="<?php echo esc_attr(wmvc_show_data('search', $db_data, '')); ?>" placeholder="<?php echo esc_html__('Filter by keyword', 'wpdirectorykit'); ?>" />

                <label class="screen-reader-text" for="user_id_editor"><?php echo esc_html__('Filter by user', 'wpdirectorykit'); ?></label>
                <?php 
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.I18n.NonSingularStringLiteralText
                echo wmvc_select_option('user_id_editor', $users, wmvc_show_data('user_id_editor', $db_data, ''), NULL, __('User', 'wpdirectorykit')); ?>
              

                <label class="screen-reader-text" for="order_by"><?php echo esc_html__('Order By', 'wpdirectorykit'); ?></label>
                <?php
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.I18n.NonSingularStringLiteralText
                echo wmvc_select_option('order_by', $order_by, wmvc_show_data('order_by', $db_data, ''), NULL, __('Order by', 'wpdirectorykit')); ?>

                <input type="submit" name="filter_action" id="post-query-submit" class="button" value="<?php echo esc_html__('Filter', 'wpdirectorykit'); ?>">
            </div>
            <?php echo wp_kses_post(wmvc_xss_clean($pagination_output)); ?>
            <br class="clear">
        </div>
    </form>

    <form method="GET" action="<?php echo esc_url(wmvc_current_edit_url()); ?>" novalidate="novalidate">
    <table class="wp-list-table widefat fixed striped table-view-list pages">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1"><?php echo esc_html__('Select All', 'wpdirectorykit'); ?></label><input id="cb-select-all-1" type="checkbox"></td>
                <th style="width:50px;"><?php echo esc_html__('#ID', 'wpdirectorykit'); ?></th>
                <th><?php echo esc_html__('Email From', 'wpdirectorykit'); ?></th>
                <th><?php echo esc_html__('To User', 'wpdirectorykit'); ?></th>
                <th><?php echo esc_html__('Date', 'wpdirectorykit'); ?></th>
                <th><?php echo esc_html__('Message', 'wpdirectorykit'); ?></th>
                <th class="actions_column"><?php echo esc_html__('Actions', 'wpdirectorykit'); ?></th>
            </tr>
        </thead>
        <?php if (count($messages) == 0) : ?>
            <tr class="no-items">
                <td class="colspanchange" colspan="7"><?php echo esc_html__('No Messages found.', 'wpdirectorykit'); ?></td>
            </tr>
        <?php endif; ?>
        <?php foreach ($messages as $item) : ?>
            <tr>
                <th scope="row" class="check-column">
                    <input id="cb-select-<?php echo esc_attr(wmvc_show_data('idmessage', $item, '-')); ?>" type="checkbox" name="post[]" value="<?php echo esc_attr(wmvc_show_data('idmessage', $item, '-')); ?>">
                    <div class="locked-indicator">
                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                        <span class="screen-reader-text"><?php echo esc_html__('Is Locked', 'wpdirectorykit'); ?></span>
                    </div>
                </th>
                <td>
                    <?php echo esc_attr(wmvc_show_data('idmessage', $item, '-')); ?>
                </td>
                <td>
                    <strong>
                        <a class="row-title" href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_messages&function=edit&id=" . wmvc_show_data('idmessage', $item, '-')); ?>"><?php echo esc_html(wmvc_show_data('email_sender', $item, '-')); ?></a>
                        <?php if(!wmvc_show_data('is_readed', $item, 0)): ?>
                            <span class="label label-success"><?php echo esc_html__('Unread', 'wpdirectorykit'); ?></span>
                        <?php else: ?>
                            <span class="label label-info"><?php echo esc_html__('I read it', 'wpdirectorykit'); ?></span>
                        <?php endif; ?>
                    </strong>
                    <div class="row-actions">
                        <span class="edit"><a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_messages&function=edit&id=" . wmvc_show_data('idmessage', $item, '-')); ?>"><?php echo esc_html__('Edit', 'wpdirectorykit'); ?></a> | </span>
                        <span class="trash "><a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_messages&function=delete&paged=".esc_attr($paged)."&id=" . wmvc_show_data('idmessage', $item, '-')."&_wpnonce=".wp_create_nonce( 'wdk-messages-delete_'.wmvc_show_data('idmessage', $item, '-')));?>" class="submitdelete question_sure"  title="<?php echo esc_attr__('Remove', 'wpdirectorykit');?>" ><?php echo esc_html__('Delete', 'wpdirectorykit'); ?></a></span>
                    </div>
                </td>
                <td>
                    <?php echo esc_html(wmvc_show_data('display_name', $item)); ?>
                </td>
                <td>
                    <?php echo esc_html(wdk_get_date(wmvc_show_data('message_date', $item), false)); ?>
                </td>
                <td>
                    <?php echo esc_html(wp_trim_words(wmvc_show_data('message', $item, '-'), 10)); ?>
                </td>
                <td class="actions_column">
                    <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_messages&function=edit&id=" . wmvc_show_data('idmessage', $item, '-')); ?>" title="<?php echo esc_attr__('Edit','wpdirectorykit');?>"><span class="dashicons dashicons-edit"></span></a>
                    <a class="question_sure" href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_messages&function=delete&paged=".esc_attr($paged)."&id=" . wmvc_show_data('idmessage', $item, '-')."&_wpnonce=".wp_create_nonce( 'wdk-messages-delete_'.wmvc_show_data('idmessage', $item, '-')));?>" title="<?php echo esc_attr__('Remove','wpdirectorykit');?>"><span class="dashicons dashicons-no"></span></a>
                </td>
            </tr>
        <?php endforeach; ?>
        <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1"><?php echo esc_html__('Select All', 'wpdirectorykit'); ?></label><input id="cb-select-all-1" type="checkbox"></td>
                <th style="width:50px;"><?php echo esc_html__('#ID', 'wpdirectorykit'); ?></th>
                <th><?php echo esc_html__('Email From', 'wpdirectorykit'); ?></th>
                <th><?php echo esc_html__('To User', 'wpdirectorykit'); ?></th>
                <th><?php echo esc_html__('Date', 'wpdirectorykit'); ?></th>
                <th><?php echo esc_html__('Message', 'wpdirectorykit'); ?></th>
                <th class="actions_column"><?php echo esc_html__('Actions', 'wpdirectorykit'); ?></th>
            </tr>
        </tfoot>
    </table>
    <div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
            <?php wp_nonce_field( 'wdk-messages-bulk', '_wpnonce'); ?>
            <label for="bulk-action-selector-bottom" class="screen-reader-text"><?php echo esc_html__('Select bulk action', 'wpdirectorykit'); ?></label>
            <select name="action" id="bulk-action-selector-bottom">
                <option value="-1"><?php echo esc_html__('Bulk actions', 'wpdirectorykit'); ?></option>
                <option value="delete" class="hide-if-no-js"><?php echo esc_html__('Delete', 'wpdirectorykit'); ?></option>
            </select>
            <input type="hidden" name="page" value="wdk_messages" />
            <input type="submit" id="table_action" class="button action" name="table_action" value="<?php echo esc_attr__('Apply', 'wpdirectorykit'); ?>">
        </div>

        <?php echo wp_kses_post(wmvc_xss_clean($pagination_output)); ?>
        <br class="clear">
    </div>
    </form>
</div>

<script>
    // Generate table
    jQuery(document).ready(function($) {
        $('.question_sure').on('click', function() {
            return confirm("<?php echo esc_js(__('Are you sure? Selected item will be completely removed!', 'wpdirectorykit')); ?>");
        });
    });
</script>

<?php $this->view('general/footer', $data); ?>