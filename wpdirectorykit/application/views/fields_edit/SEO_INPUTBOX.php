<?php
/**
 * The template for Edit field INPUTBOX.
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
if(!isset($field->prefix))$field->prefix = '';
if(!isset($field->suffix))$field->suffix = '';
if(!isset($field->default))$field->default = '';
if(!isset($field->empty_default))$field->empty_default = '';


$field_label = $field->field_label;

$required = '';
if(isset($field->is_required) && $field->is_required == 1)
    $required = '*';

if(isset($field->rules) && strpos($field->rules, 'required') !== FALSE)
    $required = '*';
?>

<div class="wdk-field-<?php echo esc_attr($field_id);?> wdk-field-edit <?php echo esc_attr($field->field_type); ?> wdk-col-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->class); ?> <?php if(!empty($form) && method_exists($form, 'hasError') && $form->hasError($field_id)):?> field-error <?php endif;?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <input class="regular-text" 
        name="<?php echo esc_attr($field_id); ?>" 
        type="text" id="<?php echo esc_attr($field_id); ?>" 
        value="<?php echo esc_attr(!empty(wmvc_show_data($field_id, $db_data, $field->default)) ? wmvc_show_data($field_id, $db_data, $field->default) : $field->empty_default); ?>"
        >
        <span class="suffix"><?php
            echo esc_html__($field->prefix, 'wpdirectorykit');
                if(!empty($field->prefix) && !empty($field->suffix)) echo ' / ';
            echo esc_html__($field->suffix, 'wpdirectorykit');
        ?></span>
        <?php if(!empty($field->hint)):?>
        <p class="wdk-hint">
            <?php echo wp_kses_post($field->hint); ?>
        </p>
        <?php endif;?>
        <?php if(!empty($form) && method_exists($form, 'hasError') && $form->getError($field_id)):?>
        <p class="wdk-hint wdk-error">
            <?php echo wp_kses_post($form->getError($field_id)); ?>
        </p>
        <?php endif;?>
    </div>
</div>
<?php
    // Register and enqueue Tribute.js style
    wp_enqueue_style( 'tribute-css');
    // Register and enqueue Tribute.js script
    wp_enqueue_script( 'tribute-js');
?>

<?php

$fields_data = wdk_cached_field_get();

$fields_list = array();
$fields_list['field_post_id'] = esc_html__('Post Id', 'wpdirectorykit');
$fields_list['field_counter_views'] = esc_html__('Views counter', 'wpdirectorykit');
$fields_list['field_lat'] = esc_html__('Gps Lat', 'wpdirectorykit');
$fields_list['field_lng'] = esc_html__('Gps Lng', 'wpdirectorykit');
$fields_list['field_date'] = esc_html__('Date', 'wpdirectorykit');
$fields_list['field_date_modified'] = esc_html__('Date Modified', 'wpdirectorykit');
$fields_list['field_post_title'] = esc_html__('Title', 'wpdirectorykit');
$fields_list['field_post_content'] = esc_html__('Content', 'wpdirectorykit');
$fields_list['field_address'] = esc_html__('Address', 'wpdirectorykit');
$fields_list['field_category'] = esc_html__('Category', 'wpdirectorykit');
$fields_list['field_location'] = esc_html__('Location', 'wpdirectorykit');

foreach ($fields_data as $field) {
    if (wmvc_show_data('field_type', $field) == 'NUMBER' || wmvc_show_data('field_type', $field) == 'INPUTBOX') {
        $fields_list['field_'.wmvc_show_data('idfield', $field)] = wmvc_show_data('field_label', $field);
    } 
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('<?php echo esc_js($field_id); ?>');
    if (input) {
        var tribute = new Tribute({
            trigger: '{',
            values: [
                <?php 
                $first = true;
                foreach($fields_list as $value => $key) { 
                    if(!$first) echo ",";
                    ?>{ key: <?php echo json_encode($key); ?>, value: <?php echo json_encode($value); ?> }<?php
                    $first = false;
                } 
                ?>
            ],
       
            // Insert with curly braces
            selectTemplate: function (item) {
                return '{' + item.original.value + '}';
            },
            menuItemTemplate: function (item) {
                return item.string; // show key
            }
        });
        tribute.attach(input);
    }
});
</script>

