<?php

namespace Wdk\Extensions;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


class WdkCachedUserEditor
{
    /**
     * data array
     *
     * @var array
     */
    public $data = array();
    public $option_key = 'wdk_users_cached';
    
    public function __construct($data = array(), $args = null)
    {

        add_action( 'wp_update_user', array($this, 'update_listings_user_editor'), 11);
        add_action( 'wpdirectorykit/listing/saved', array($this, 'listing_user_editor'), 10, 2);

    }

    public function listing_user_editor($id = NULL, $listing_data = null)
    {
        global $Winter_MVC_WDK;
        $Winter_MVC_WDK->model('listing_m');
        $Winter_MVC_WDK->load_helper('listing');

        $user_id_editor = $listing_data['user_id_editor'];

        $data_update = array(
            'user_id_editor_display_name'=> wdk_get_user_field ($user_id_editor, 'display_name'),
            'user_id_editor_user_login'=>wdk_get_user_field ($user_id_editor, 'user_login'),
            'user_id_editor_wdk_slug'=>wdk_get_user_field ($user_id_editor, 'wdk_slug'),
            'user_id_editor_avatar'=>wdk_get_user_field ($user_id_editor, 'avatar'),
        );

        if(!empty($data_update))
            $Winter_MVC_WDK->listing_m->insert($data_update, $id);
    }

    public function update_listings_user_editor($user_id_editor = NULL)
    {
        global $Winter_MVC_WDK;
        $Winter_MVC_WDK->model('listing_m');
        $Winter_MVC_WDK->load_helper('listing');

        $data_update = array(
            'user_id_editor_display_name'=> wdk_get_user_field ($user_id_editor, 'display_name'),
            'user_id_editor_user_login'=>wdk_get_user_field ($user_id_editor, 'user_login'),
            'user_id_editor_wdk_slug'=>wdk_get_user_field ($user_id_editor, 'wdk_slug'),
            'user_id_editor_avatar'=>wdk_get_user_field ($user_id_editor, 'avatar'),
        );

        $Winter_MVC_WDK->db->update($Winter_MVC_WDK->listing_m->_table_name, $data_update, $user_id_editor, 'user_id_editor');
    }

}
