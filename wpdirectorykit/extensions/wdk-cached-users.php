<?php

namespace Wdk\Extensions;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


class WdkCachedUsers
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

        add_action( 'wp_update_user', array($this, 'update'), 11);
        add_action( 'init', array($this, 'regenerate_cache_activation'), 11);

        /* call with GET['wdk_cached_users_regenerate'] */
        add_action( 'init', array($this, 'regenerate_cache_regenerate'), 11);

    }

    public function update($user_id = NULL)
    {
        global $Winter_MVC_WDK;
        $Winter_MVC_WDK->model('cachedusers_m');
        $Winter_MVC_WDK->load_helper('listing');

        $user_data = wdk_get_user_data($user_id);

        $Winter_MVC_WDK->db->where('cacheduser_user_id', $user_id);
        $Winter_MVC_WDK->db->delete($Winter_MVC_WDK->cachedusers_m->_table_name);

        $user_all_meta = get_user_meta($user_id);
        $usermetadata = array_map( function( $a ){ return $a[0]; }, $user_all_meta );
        $json_data = $usermetadata;

        $meta_fields = array('wdk_address','wdk_phone','wdk_city','wdk_company_name','wdk_facebook','wdk_youtube','wdk_linkedin','wdk_twitter','wdk_instagram','wdk_whatsapp',
                            'wdk_viber','wdk_iban','wdk_telegram','wdk_position_title','session_tokens','wc_last_active','show_welcome_panel','dismissed_wp_pointers','locale','show_admin_bar_front','use_ssl','admin_color',
                            'comment_shortcuts','syntax_highlighting','rich_editing','description','community-events-location','_woocommerce_persistent_cart_1','_woocommerce_tracks_anon_id',
                            $Winter_MVC_WDK->db->prefix.'capabilities',$Winter_MVC_WDK->db->prefix.'user_level',$Winter_MVC_WDK->db->prefix.'user-settings',$Winter_MVC_WDK->db->prefix.'user-settings-time',$Winter_MVC_WDK->db->prefix.'dashboard_quick_press_last_post_id');

        /* remove from json fields from above array */
        foreach ($meta_fields as $key_meta_field) {
            if(isset($json_data[$key_meta_field]))
                unset($json_data[$key_meta_field]);
        }

        $insert_id = $Winter_MVC_WDK->cachedusers_m->insert(array(
            'cacheduser_user_id' => $user_id,
            'cacheduser_profile_url' => wdk_esc_sql($user_data['profile_url'], true),
            'cacheduser_avatar_url' => wdk_esc_sql($user_data['avatar'], true),
            'cacheduser_display_name' => wdk_esc_sql(wdk_get_user_field($user_id, 'display_name'), true),
            'cacheduser_email' => wdk_esc_sql(wdk_get_user_field($user_id, 'user_email'), true),
            'cacheduser_wdk_address' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_address'), true),
            'cacheduser_wdk_phone' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_phone'), true),
            'cacheduser_wdk_city' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_city'), true),
            'cacheduser_wdk_company_name' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_company_name'), true),
            'cacheduser_wdk_facebook' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_facebook'), true),
            'cacheduser_wdk_youtube' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_youtube'), true),
            'cacheduser_wdk_linkedin' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_linkedin'), true),
            'cacheduser_wdk_twitter' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_twitter'), true),
            'cacheduser_wdk_instagram' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_instagram'), true),
            'cacheduser_wdk_whatsapp' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_whatsapp'), true),
            'cacheduser_wdk_viber' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_viber'), true),
            'cacheduser_wdk_iban' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_iban'), true),
            'cacheduser_wdk_telegram' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_telegram'), true),
            'cacheduser_wdk_position_title' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_position_title'), true),
            'cacheduser_wdk_slug' => wdk_esc_sql(wdk_get_user_field($user_id, 'wdk_slug'), true),
            'cacheduser_user_login' => wdk_esc_sql(wdk_get_user_field($user_id, 'user_login'), true),
            'cacheduser_agency_name' => wdk_esc_sql(wdk_get_user_field($user_id, 'agency_name'), true),
            'cacheduser_description' => wdk_esc_sql(wdk_get_user_field($user_id, 'description'), true),
            'cacheduser_user_url' => wdk_esc_sql(wdk_get_user_field($user_id, 'user_url'), true),
            'cacheduser_roles' => wdk_esc_sql(join(',', (array) wdk_get_user_field($user_id, 'roles')), true),
            'cacheduser_json_data' => wdk_esc_sql(json_encode($json_data), true),
            'cacheduser_date_updated' => gmdate('Y-m-d H:i:s'),
       
        ), NULL);
    }

    public function regenerate_cache_activation()
    {
        if(wdk_get_option($this->option_key)) {
            return true; 
        }
        
        $this->regenerate_cache($limit=20);

        return TRUE;
    }

    public function regenerate_cache_regenerate()
    {
        if(isset($_GET['wdk_cached_users_regenerate'])) {
            $this->regenerate_cache();

            return TRUE;
        }
    }

    public function regenerate_cache($limit = NULL)
    {
        global $wpdb;
        if (!empty($limit)) {
            $dbusers = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->users} LIMIT %d",
                    absint($limit)
                )
            );
        } else {
            $dbusers = $wpdb->get_results(
                "SELECT * FROM {$wpdb->users}"
            );
        }

        foreach($dbusers as $dbuser) {
            $this->update(wmvc_show_data('ID', $dbuser));
        }

        update_option( $this->option_key, 1);

        return TRUE;
    }

}
