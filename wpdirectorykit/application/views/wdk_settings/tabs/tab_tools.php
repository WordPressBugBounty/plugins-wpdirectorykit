<div class="wdk-field-edit">
    <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_settings&function=remove&_wpnonce=".wp_create_nonce( 'remove-data')); ?>" 
    class="button button-primary event-ajax-indicator confirm" id="reset_data_field_button"><?php echo __('Remove plugin data (Listings, fields, location, categories)','wpdirectorykit'); ?></a>               
    <span class="wdk-ajax-indicator wdk-infinity-load color-primary dashicons dashicons-update-alt hidden" style="margin-top: 4px;margin-left: 4px;"></span>              
</div>
<div class="wdk-field-edit">
    <a href="<?php echo esc_url(get_admin_url() . "admin.php?page=wdk_settings&function=import_demo"); ?>" class="button button-primary" id="import_demo_field_button"><?php echo __('Import Demo Data','wpdirectorykit'); ?></a> 
</div>
<div class="wdk-field-edit">
    <a href="#" class="button button-primary" id="generate_listings_images_path"><?php echo __('Generate Listings Images Path','wpdirectorykit'); ?></a>               
</div>
<div class="wdk-field-edit">
    <a href="#" class="button button-primary" id="optimization_listingfields_table"><?php echo __('Optimization Listing Fields Table','wpdirectorykit'); ?></a>               
</div>
<div class="wdk-field-edit">
    <a href="#" class="button button-primary ajax_query" data-function="generated_strings"><?php echo __('Generate translation strings','wpdirectorykit'); ?></a>               
</div>