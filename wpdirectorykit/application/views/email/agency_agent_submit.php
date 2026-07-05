<?php

/**
 * The template for Email, default layout.
 * Receiver: all
 * This is the template that email layout
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo wp_kses_post($subject); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="padding: 0;width: 100%; background-color: #f7f7f7; -webkit-text-size-adjust: none;">
    <div id="wrapper" dir="ltr" style="max-width: 600px; width:calc(100% - 30px); background-color: #fff; margin: 0 auto;border: 1px solid #dedede;box-shadow: 0 1px 4px rgb(0 0 0 / 10%);">

        <!-- header -->
        <div class="header" style="background-color: #2671cb;padding: 48px 48px;color: #FFF;">
            <h2 style="margin:0;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left; text-shadow: 0 1px 0 #ab79a1; color: #ffffff; background-color: inherit;"">
                <?php echo wp_kses_post($subject); ?>
          </h2>
        </div>

        <!-- Body -->
        <div class=" body" style="padding: 48px 48px;color: #636363; font-size: 14px;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                <div style="margin-bottom: 24px;">
                    <p style="font-size: 16px; margin: 0 0 8px 0;">
                        <?php echo esc_html__('You have received an invitation for an agent to join your agency.', 'wpdirectorykit'); ?>
                    </p>
                    <p style="font-size: 14px; color: #888; margin: 0 0 8px 0;">
                        <?php echo esc_html__('Below are the details of the invitation:', 'wpdirectorykit'); ?>
                    </p>
                </div>
                <div style="margin-bottom: 32px;">
                    <?php if (!empty($agency_name)) : ?>
                        <p style="margin: 0 0 8px 0;">
                            <strong><?php echo esc_html__('Agency', 'wpdirectorykit'); ?>:</strong>
                            <?php echo esc_html($agency_name); ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($agent_name)) : ?>
                        <p style="margin: 0 0 8px 0;">
                            <strong><?php echo esc_html__('Agent', 'wpdirectorykit'); ?>:</strong>
                            <?php echo esc_html($agent_name); ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div style="margin-bottom: 32px; display: flex; gap: 16px; align-items: center;">
                    <?php if (!empty($agency_profile_link)) : ?>
                        <a href="<?php echo esc_url($agency_profile_link); ?>"
                           style="display:inline-block;background-color:#2671cb;color:#fff;text-decoration:none;padding:12px 32px;border-radius:4px;font-weight:bold; font-size:15px;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;"
                           target="_blank"><?php echo esc_html__('Agency profile', 'wpdirectorykit'); ?></a>
                    <?php endif; ?>
                    <?php if (!empty($confirmation_link)) : ?>
                        <a href="<?php echo esc_url($confirmation_link); ?>"
                           style="display:inline-block;background-color:#27c184;color:#fff;text-decoration:none;padding:12px 32px;border-radius:4px;font-weight:bold; font-size:15px;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;"
                           target="_blank"><?php echo esc_html__('Confirm', 'wpdirectorykit'); ?></a>
                    <?php endif; ?>
                </div>
           
                <div style="margin-top: 24px;">
                    <p style="font-size: 15px;">
                        <?php echo esc_html__('If you have any questions, please contact your agency administrator or reply to this message.', 'wpdirectorykit'); ?>
                    </p>
                    <p style="font-size: 15px;">
                        <?php echo esc_html__('Thank you for using our platform!', 'wpdirectorykit'); ?>
                    </p>
                </div>
     
        </div>

        <!-- Footer -->
        <div class="footer" style="padding: 25px 48px;color: #4e5254; font-weight: 500;font-size: 14px;line-height: 1.6;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;border-top: 1px solid #eee;">
            <?php echo esc_html__('Thanks', 'wpdirectorykit'); ?>, </br>
            <?php echo esc_html__('Best regards', 'wpdirectorykit'); ?>, </br>
            <?php echo esc_html(get_bloginfo('name')); ?> </br>
        </div>
    </div>
</body>

</html>