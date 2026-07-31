<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action('rest_api_init', function () {
    register_rest_route('wdk/v1', '/listing/autosuggestion', [
        'methods'  => 'POST',
        'callback' => 'wdk_listing_search_rest_callback',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);
});

function wdk_listing_search_rest_callback($request)
{
    $params = $request->get_json_params();
    $keyword = '';

    if ($params && isset($params['keyword'])) {
        $keyword = sanitize_text_field($params['keyword']);
    } 

    if (strlen($keyword) < 2) {
        return rest_ensure_response([
            'success' => true,
            'data' => []
        ]);
    }

    $args = [
        'post_type'      => 'wdk-listing',
        'posts_per_page' => 10,
        's'              => $keyword,
    ];

    // Optionally exclude the currently executed ID if provided
    if (isset($params['executedId'])) {
        $args['post__not_in'] = [intval($params['executedId'])];
    }

    $posts = get_posts($args);

    $result = [];
    foreach ($posts as $post) {
        $result[] = [
            'id'    => $post->ID,
            'title' => $post->post_title,
            'edit'  => urlencode(admin_url('admin.php?page=wdk_listing&id=' . $post->ID)),
        ];
    }

    return rest_ensure_response([
        'success' => true,
        'data' => $result
    ]);
}
