<div class="wdk-field-edit">
    <p class="alert alert-info"><?php echo __('This is code editing so may cause trouble if you put error in code','wpdirectorykit'); ?></p>
</div>
<table class="wp-list-table widefat striped table-view-list pages">
    <thead>
        <tr>
            <th>#</th>
            <th><?php echo esc_html__('Template', 'wdk-bookings'); ?></th>
            <th><?php echo esc_html__('Description', 'wdk-bookings'); ?></th>
            <th class="actions_column"><?php echo esc_html__('Actions', 'wdk-bookings'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mailtemplates as $key=>$template) : ?>
            <tr>
                <td>
                    <?php echo ($key + 1); ?>
                </td>
                <td>
                    <?php echo $template['template']; ?>
                </td>
                <td>
                    <?php echo $template['description']; ?>
                </td>
                <td class="actions_column">
                    <a target="_blank" href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_settings&function=mailtemplate&file=".$template['template'].""); ?>"><span class="dashicons dashicons-edit"></span></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th>#</th>
            <th><?php echo esc_html__('Template', 'wdk-bookings'); ?></th>
            <th><?php echo esc_html__('Description', 'wdk-bookings'); ?></th>
            <th class="actions_column"><?php echo esc_html__('Actions', 'wdk-bookings'); ?></th>
        </tr>
    </tfoot>
</table>