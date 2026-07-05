<?php
/**
 * The template for Edit field TEXTAREA.
 *
 * This is the template that field layout for edit form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php

//wmvc_dump($field);

if(isset($field->field))
{
    $field_id = $field->field;
}
else
{
    $field_id = 'field_'.$field->idfield;
}

if(!isset($field->hint))$field->hint = '';
if(!isset($field->columns_number))$field->columns_number = '';
if(!isset($field->class))$field->class = '';

$field_label = $field->field_label;

$required = '';
if(isset($field->is_required) && $field->is_required == 1)
    $required = '*';

if(isset($field->rules) && strpos($field->rules, 'required') !== FALSE)
    $required = '*';

?>
<div class="wdk-field-<?php echo esc_attr($field_id);?> wdk-field-edit <?php echo esc_attr($field->field_type); ?> wdk-col-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->class); ?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <?php if(!empty($field->raw)):?>
            <?php echo wp_kses_post($field->raw); ?>
        <?php endif;?>
        <?php if(!empty($field->hint)):?>
            <p class="wdk-hint">
                <?php echo wp_kses_post($field->hint); ?>
            </p>
        <?php endif;?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var editorElem = $('#<?php echo esc_js($field_id);?>');
    if (editorElem.length && typeof wp !== 'undefined' && wp.codeEditor && typeof wp.codeEditor.initialize === 'function') {
        wp.codeEditor.initialize(editorElem[0], {});
    }
});
</script>
