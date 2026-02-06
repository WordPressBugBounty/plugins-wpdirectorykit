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
    <div class="wdk-language-switcher-list">
        <?php foreach ($langs as $lang): if ($lang['lang_code'] == wdk_current_language()) continue; ?>
            <a class="item"  data-no-translation href="<?php echo esc_url( $lang['url'] );?>" data-no-translation><img src="<?php echo esc_url( $lang['icon']);?>" alt="<?php echo esc_html( $lang['lang_code']);?>"> <?php echo esc_html( $lang['title'] );?> </a>
        <?php endforeach;?>
    </div>
</div>