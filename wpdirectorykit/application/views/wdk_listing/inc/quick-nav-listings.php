<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php
global $wpdb;
$prev_id = $next_id = null;

if(isset($_GET['id'])) {
    $current_id = absint($_GET['id']);
    
    $prev_id = $wpdb->get_var($wpdb->prepare("
        SELECT ID
        FROM {$wpdb->posts}
        WHERE post_type = %s
          AND post_status != 'trash'
          AND ID < %d
        ORDER BY ID DESC
        LIMIT 1
    ", 'wdk-listing', $current_id));
    
    $next_id = $wpdb->get_var($wpdb->prepare("
        SELECT ID
        FROM {$wpdb->posts}
        WHERE post_type = %s
          AND post_status != 'trash'
          AND ID > %d
        ORDER BY ID ASC
        LIMIT 1
    ", 'wdk-listing', $current_id));
}

?>
<div class="wdk-d-flex wdk-align-items-end wdk-justify-content-end">


<div class="wdk-listing-toolbar">
    <div class="wdk-toolbar-left">
    <?php if ($prev_id): ?>
        <a class="button button-secondary"
           href="<?php echo esc_url(add_query_arg('id', $prev_id)); ?>">
            <span class="dashicons dashicons-arrow-left-alt2"></span>
        </a>
    <?php endif; ?>
    </div>

    <div class="wdk-toolbar-search">
        <input
            type="text"
            id="wdk-listing-search"
            idexecuted="<?php echo isset($_GET['id']) ? esc_attr((int)$_GET['id']) : ''; ?>"
            placeholder="<?php echo esc_html__('Search listings...', 'wpdirectorykit'); ?>"
            autocomplete="off">
        <div class="wdk-search-results" hidden></div>
    </div>

    <div class="wdk-toolbar-right">
    <?php if ($next_id): ?>
        <a class="button button-secondary"
           href="<?php echo esc_url(add_query_arg('id', $next_id)); ?>">
            <span class="dashicons dashicons-arrow-right-alt2"></span>
        </a>
    <?php endif; ?>
    </div>

</div>
</div>