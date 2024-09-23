<?php
/**
 * The template for Element Listings Search Form.
 * This is the template that elementor element, fields, search form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-tabs <?php if($is_edit_mode):?> is_edit_mode<?php endif;?>">
        <?php  if(!empty($settings['tabs'])) foreach ($settings['tabs'] as $key => $tab):?>
            <a href="#" class="tab <?php echo esc_html(wmvc_show_data('tab_css_class',$tab));?>" data-target="<?php echo esc_html(wmvc_show_data('tab_target',$tab));?>">
                <?php \Elementor\Icons_Manager::render_icon( $tab['tab_icon'], [ 'aria-hidden' => 'true' ] ); ?> <?php echo esc_html(wmvc_show_data('tab_name',$tab));?>
            </a>
        <?php endforeach ?>
    </div>
</div>

