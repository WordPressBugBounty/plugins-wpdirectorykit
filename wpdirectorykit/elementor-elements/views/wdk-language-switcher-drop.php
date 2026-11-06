<?php
/**
 * The template for Element Currency Picker.
 * This is the template that elementor element dropdown
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="wdk-element" id="wdk_el_<?php echo esc_html($id_element);?>">
    <div class="wdk-language-switcher-drop">
        <button class="wdk-language-switcher-btn-toggle" type="button">
            <?php if ( !empty($current_lang_flag) && !empty($current_lang_flag) ): ?>
                <img src="<?php echo esc_url($current_lang_flag); ?>" alt="<?php echo esc_attr($current_lang); ?>" style="max-height:20px;vertical-align:middle;">
            <?php endif; ?>
            <span><?php echo esc_html($current_lang); ?></span>
            <?php \Elementor\Icons_Manager::render_icon( $settings['select_icon'], [ 'aria-hidden' => 'true' ] ); ?>
       
        </button>
        <div class="wdk-language-switcher-menu">
            <?php foreach ($langs as $lang): if ($lang['lang_code'] == wdk_current_language()) continue; ?>
                <?php
                    $view_title = (!empty($settings['enable_short_names']) && $settings['enable_short_names']=='yes') ? ucfirst($lang['short_language_name']) : ucfirst($lang['title']);
               
                ?>
                <a class="item"  data-no-translation href="<?php echo esc_url( $lang['url'] );?>"><img src="<?php echo esc_url( $lang['icon']);?>" alt="<?php echo esc_attr( $lang['lang_code']);?>"> <?php echo esc_html( $view_title);?> </a>
            <?php endforeach;?>
        </div>
    </div>
</div>