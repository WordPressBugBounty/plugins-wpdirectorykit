<?php
/**
 * The template for Settings.
 *
 * This is the template that edit form settings
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap wdk-wrap">
    <h1 class="wp-heading-inline"><?php echo __('Settings', 'wpdirectorykit'); ?></h1>
    <br />
    <div class="wdk-body">
        <form method="post" action="" novalidate="novalidate">
            <?php wp_nonce_field( 'wdk-settings-edit', '_wpnonce'); ?>
            <?php
                $form->messages('class="alert alert-danger"',  __('Successfully saved', 'wpdirectorykit'));
            ?>
            <?php if(!get_option('wdk_results_page')):?>
                <p class="alert alert-info"><?php echo __('Missing results page', 'wpdirectorykit'); ?></p>
            <?php endif;?>

            <div class="wdk-tabs-navs">
                <label for="wdk_tab_general"<?php if(!wmvc_show_data('wdk_tabs',$_POST, false) || wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_general'):?> class="active" <?php endif;?>><?php echo esc_html__('General','wpdirectorykit');?></label>
                <label for="wdk_tab_apis" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_apis'):?> class="active" <?php endif;?>><?php echo esc_html__('APIs','wpdirectorykit');?></label>
                <label for="wdk_tab_templates" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_templates'):?> class="active" <?php endif;?>><?php echo esc_html__('Layout','wpdirectorykit');?></label>
                <label for="wdk_tab_fields" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_fields'):?> class="active" <?php endif;?>><?php echo esc_html__('Fields','wpdirectorykit' );?></label>
                <label for="wdk_tab_tools" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_tools'):?> class="active" <?php endif;?>><?php echo esc_html__('Tools','wpdirectorykit' );?></label>
                <label for="wdk_tab_autosuggestion" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_autosuggestion'):?> class="active" <?php endif;?>><?php echo esc_html__('Autosuggestion','wpdirectorykit' );?></label>
                <?php if(get_option('wdk_experimental_features')):?>
                    <label for="wdk_tab_experimental" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_experimental'):?> class="active" <?php endif;?>><?php echo esc_html__('Experimental','wpdirectorykit' );?></label>
                <?php endif;?>
                <label for="wdk_tab_mails" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_mails'):?> class="active" <?php endif;?>><?php echo esc_html__('Emails','wpdirectorykit' );?></label>
                
                <?php do_action('wpdirectorykit/admin/settings/page/tabs', $db_data);?>
            </div>
            <div class="postbox" style="display: block;">
                <div class="inside">
                    <div class="wdk-tabs-panel">
                        <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_general" checked value="wdk_tab_general">
                        <div class="wdk-tab">
                            <?php echo wdk_generate_fields($fields_list_tabs['general'], $db_data); ?>  
                        </div>

                        <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_apis" value="wdk_tab_apis" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_apis'):?> checked="checked" <?php endif;?>>
                        <div class="wdk-tab">
                            <?php echo wdk_generate_fields($fields_list_tabs['apis'], $db_data); ?>  
                        </div>

                        <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_templates" value="wdk_tab_templates" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_templates'):?> checked="checked" <?php endif;?>>
                        <div class="wdk-tab">
                            <?php echo wdk_generate_fields($fields_list_tabs['templates'], $db_data); ?>  
                        </div>

                        <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_fields" value="wdk_tab_fields" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_fields'):?> checked="checked" <?php endif;?>>
                        <div class="wdk-tab">
                            <?php echo wdk_generate_fields($fields_list_tabs['fields'], $db_data); ?>  
                        </div>
                        
                        <?php if(get_option('wdk_experimental_features')):?>
                            <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_experimental" value="wdk_tab_experimental" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_experimental'):?> checked="checked" <?php endif;?>>
                            <div class="wdk-tab">
                                <?php echo wdk_generate_fields($fields_list_tabs['experimental'], $db_data); ?>  
                            </div>
                        <?php endif;?>

                        <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_tools" value="wdk_tab_tools" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_tools'):?> checked="checked" <?php endif;?>>
                        <div class="wdk-tab">
                            <?php $this->view('wdk_settings/tabs/tab_tools', $data); ?>
                        </div>

                        <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_autosuggestion" value="wdk_tab_autosuggestion" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_autosuggestion'):?> checked="checked" <?php endif;?>>
                        <div class="wdk-tab">
                            <?php echo wdk_generate_fields($fields_list_tabs['autosuggestion'], $db_data); ?>  
                        </div>

                        <input type="radio" class="wdk-tab-input" name="wdk_tabs" id="wdk_tab_mails" value="wdk_tab_mails" <?php if(wmvc_show_data('wdk_tabs',$_POST) == 'wdk_tab_mails'):?> checked="checked" <?php endif;?>>
                        <div class="wdk-tab">
                            <?php $this->view('wdk_settings/tabs/tab_emails', $data); ?>
                        </div>

                        <?php do_action('wpdirectorykit/admin/settings/page/tabs-list', $db_data);?>
                    </div>
                </div>
            </div>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save Changes', 'wpdirectorykit'); ?>">
        </form>
    </div>
    <br/>
    <div class="alert alert-info" role="alert"><a href="//wpdirectorykit.com/documentation/#change_currency" target="_blank"><?php echo __('How to change currency?','wpdirectorykit'); ?></a></div>
</div>

<?php wp_enqueue_style('jquery-confirm'); ?>
<?php wp_enqueue_script('jquery-confirm'); ?>

<script>
jQuery(document).ready(function($) {
    /* reset page feature, create new demo page and atache */

    $('#wdk_results_page, #wdk_listing_page').each(function(){
        $(this).closest('.wdk-field-edit').find('.suffix').append(`<a data-function="reinstall_page" data-arg="`+$(this).attr('id')+`" class="button button ajax_query_pages" href="#" style="margin-top: -5px;"> <?php echo esc_html__('Reset','wpdirectorykit');?>`);
    });


    $('#reset_data_field_button').on('click', function(e){
        var self = $(this);

        var res = prompt("<?php echo __('Are you sure? All Listings, fields, categories, locations will be completely removed, check url and type remove if you are sure', 'wpdirectorykit')?>", "");

        if(res == "<?php echo __('remove', 'wpdirectorykit')?>") {
            return true;
        } else {
            e.preventDefault();
            e.stopPropagation();
            $(this).parent().find('.wdk-ajax-indicator').addClass('hidden');
            return false;
        }
    })

    $('#generate_listings_images_path').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        var self = $(this);

        if(self.attr('disabled')) {
            return false;
        }

        self.addClass('wdk_btn_load_indicator out');
      
        self.attr('disabled','disabled');
        var ajax_param = {
            "page": 'wdk_backendajax',
            "function": 'generated_listings_images_path',
            "action": 'wdk_public_action',
            "_wpnonce": '<?php echo esc_js(wp_create_nonce( 'wdk-generated_listings_images_path'));?>',
        };
        $.post("<?php echo admin_url( 'admin-ajax.php' );?>", ajax_param, 
            function(data){
                
            if(data.popup_text_success)
                wdk_log_notify(data.popup_text_success);
                
            if(data.popup_text_error)
                wdk_log_notify(data.popup_text_error, 'error');
                
            if(data.success) {
                self.removeClass('wdk_btn_load_indicator out');
                self.addClass('wdk_btn_load_success out');
            } else {
                self.removeClass('wdk_btn_load_indicator out');
                self.addClass('wdk_btn_load_error out');
            }
        }).always(function(data) {

        });
        return false;
    })

    $('#optimization_listingfields_table').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        var self = $(this);

        if(self.attr('disabled')) {
            return false;
        }

        self.addClass('wdk_btn_load_indicator out');
      
        self.attr('disabled','disabled');
        var ajax_param = {
            "page": 'wdk_backendajax',
            "function": 'optimization_listingfields_table',
            "action": 'wdk_public_action',
            "_wpnonce": '<?php echo esc_js(wp_create_nonce( 'wdk-optimization_listingfields_table'));?>',
        };
        $.post("<?php echo admin_url( 'admin-ajax.php' );?>", ajax_param, 
            function(data){
                
            if(data.popup_text_success)
                wdk_log_notify(data.popup_text_success);
                
            if(data.popup_text_error)
                wdk_log_notify(data.popup_text_error, 'error');
                
            if(data.success) {
                self.removeClass('wdk_btn_load_indicator out');
                self.addClass('wdk_btn_load_success out');
            } else {
                self.removeClass('wdk_btn_load_indicator out');
                self.addClass('wdk_btn_load_error out');
            }
        }).always(function(data) {

        });
        return false;
    })

    $('.ajax_query').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        var self = $(this);

        if(self.attr('disabled')) {
            return false;
        }

        self.addClass('wdk_btn_load_indicator out');
      
        self.attr('disabled','disabled');
        var ajax_param = {
            "page": 'wdk_backendajax',
            "function": self.data('function'),
            "action": 'wdk_public_action',
            "_wpnonce": '<?php echo esc_js(wp_create_nonce( 'wdk-backendajax'));?>',
            "arg": self.data('arg') ?? '',
        };
        $.post("<?php echo admin_url( 'admin-ajax.php' );?>", ajax_param, 
            function(data){
                
            if(data.popup_text_success)
                wdk_log_notify(data.popup_text_success);
                
            if(data.popup_text_error)
                wdk_log_notify(data.popup_text_error, 'error');
                
            if(data.success) {
                self.removeClass('wdk_btn_load_indicator out');
                self.addClass('wdk_btn_load_success out');
            } else {
                self.removeClass('wdk_btn_load_indicator out');
                self.addClass('wdk_btn_load_error out');
            }
        }).always(function(data) {

        });
        return false;
    })

    $('.ajax_query_pages').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        var self = $(this);

        if(self.attr('disabled')) {
            return false;
        }

    
        $.confirm({
			boxWidth: '400px',
			useBootstrap: false,
			title: '',
            content: '<?php echo esc_js(esc_html__('Are you sure? current layout will be replaced with default plugin version','wpdirectorykit'));?>',
            buttons: {
                cancel: function () {
                    return true;
                },
                confirm: {
                    text: '<?php echo esc_js(esc_html__('Confirm','wpdirectorykit'));?>',
					btnClass: 'btn-blue',
					keys: ['enter', 'shift'],
					action: function(){
                        var ajax_param = {
                            "page": 'wdk_backendajax',
                            "function": self.data('function'),
                            "action": 'wdk_public_action',
                            "_wpnonce": '<?php echo esc_js(wp_create_nonce( 'wdk-backendajax'));?>',
                            "arg": self.data('arg') ?? '',
                        };

                        self.addClass('wdk_btn_load_indicator out');
                        self.attr('disabled','disabled');
                        $.post("<?php echo admin_url( 'admin-ajax.php' );?>", ajax_param, 
                            function(data){
                                
                            if(data.popup_text_success)
                                wdk_log_notify(data.popup_text_success);
                                
                            if(data.popup_text_error)
                                wdk_log_notify(data.popup_text_error, 'error');
                                
                            if(data.success) {
                                self.removeClass('wdk_btn_load_indicator out');
                                self.addClass('wdk_btn_load_success out');

                                if(data.page_id && data.page_title) {
                                    self.closest('.wdk-field-container').find('select option').removeAttr('selected').parent().append('<option selected="selected" value="'+data.page_id+'">'+data.page_title+' #'+data.page_id+'</option>');
                                }
                            } else {
                                self.removeClass('wdk_btn_load_indicator out');
                                self.addClass('wdk_btn_load_error out');
                            }
                        }).always(function(data) {

                        });

						return true;
					}
                },
			}
        })
    })

    /* number format live */

    // Live update for number format fields example
    function updateWdkExamplePriceFormat() {
        var decimal_point = $('#wdk_number_format_decimal_point').val();
        var thousands_sep = $('#wdk_number_format_thousands_sep').val();
        var decimals = $('#wdk_number_format_decimals').val();

        // Fallbacks for empty values
        if (decimal_point === '') decimal_point = '.';
        if (thousands_sep === '') thousands_sep = ',';
        if (decimals === '' || isNaN(decimals)) decimals = 0;
        decimals = parseInt(decimals);

        // Example number to format
        var exampleNumber = 1234567.89;

        // Format number
        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            var re = /(-?\d+)(\d{3})/;
            while (re.test(s[0])) {
                s[0] = s[0].replace(re, "$1" + sep + "$2");
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        var formatted = number_format(exampleNumber, decimals, decimal_point, thousands_sep);
        $('#wdk_example_price_format').text(formatted);
    }

    $('#wdk_number_format_decimal_point, #wdk_number_format_thousands_sep, #wdk_number_format_decimals').on('input change', function() {
        updateWdkExamplePriceFormat();
    });

    // Initial update on page load
    updateWdkExamplePriceFormat();
})
</script>

<?php $this->view('general/footer', $data); ?>