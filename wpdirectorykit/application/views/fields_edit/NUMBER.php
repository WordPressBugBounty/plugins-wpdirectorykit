<?php
/**
 * The template for Edit field NUMBER.
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
if(!isset($field->prefix))$field->prefix = '';
if(!isset($field->suffix))$field->suffix = '';
if(!isset($field->class))$field->class = '';
if(!isset($field->default))$field->default = '';
if(!isset($field->empty_default))$field->empty_default = '';

$field_label = $field->field_label;

$required = '';
if(isset($field->is_required) && $field->is_required == 1)
    $required = '*';

if(isset($field->rules) && strpos($field->rules, 'required') !== FALSE)
    $required = '*';

?>

<div class="wdk-field-<?php echo esc_attr($field_id);?> wdk-field-edit <?php echo esc_attr($field->field_type); ?> wdk-col-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->class); ?> <?php echo esc_attr($field->class); ?> <?php if(!empty($form) && method_exists($form, 'hasError') && $form->hasError($field_id)):?> field-error <?php endif;?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <input min="<?php echo esc_attr(wmvc_show_data('min', $field, ''));?>" 
        max="<?php echo esc_attr(wmvc_show_data('max', $field, ''));?>" 
        step="<?php echo esc_attr(wmvc_show_data('pattern', $field, ''));?>" 
        pattern="<?php echo esc_attr(wmvc_show_data($field_id, $db_data, ''));?>" 
        class="regular-text" name="<?php echo esc_attr($field_id); ?>" 
        type="number" id="<?php echo esc_attr($field_id); ?>" 
        value="<?php echo esc_attr(wdk_filter_decimal(!empty(wmvc_show_data($field_id, $db_data, $field->default)) ? wmvc_show_data($field_id, $db_data, $field->default) : $field->empty_default)); ?>"
        >
        <span class="suffix"><?php
            echo esc_html__($field->prefix, 'wpdirectorykit');
                if(!empty($field->prefix) && !empty($field->suffix)) echo ' / ';
            echo esc_html__($field->suffix, 'wpdirectorykit');
        ?></span>

        <?php if(!empty($field->hint)):?>
        <p class="wdk-hint">
            <?php echo esc_html($field->hint); ?>
        </p>
        <?php endif;?>
        <?php if(!empty($form) && method_exists($form, 'hasError') && $form->getError($field_id)):?>
        <p class="wdk-hint wdk-error">
            <?php echo wp_kses_post($form->getError($field_id)); ?>
        </p>
        <?php endif;?>
    </div>
</div>