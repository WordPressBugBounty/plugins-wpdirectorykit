<?php

namespace Wdk\Extensions;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class WdkAutosuggestionFields
{
    /**
     * data array
     *
     * @var array 
     */
    public $data = array();
    public $custom_js = '';

    private $is_addon_active = null;

    
    public function __construct($data = array(), $args = null)
    {
        $this->is_addon_active ();
        /* admin actions */
        add_action( 'wpdirectorykit/admin/listing/edit/after_form', array($this, 'autosuggestion'));
        add_action( 'wdk-membership/view/listing_edit/after_form', array($this, 'autosuggestion'));
    }

    private function is_addon_active () {
        if(!is_null($this->is_addon_active)) {
            return $this->is_addon_active;
        }

        if ( in_array( 'wdk-autosuggestion/wdk-autosuggestion.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $this->is_addon_active = true;
        } else {
            $this->is_addon_active = false;
        }

        return $this->is_addon_active;
    }

    public function autosuggestion($data = NULL)
    {
        wp_register_script( 'wdk-trial-autosuggestion-drop', WPDIRECTORYKIT_URL. 'public/js/wdk-trial-autosuggestion-drop.js', array(), 1.0, 'all' );
        wp_register_style( 'wdk-trial-autosuggestion-drop', WPDIRECTORYKIT_URL. 'public/css/wdk-trial-autosuggestion-drop.css', array(), 1.0, 'all' );
        
        if($this->is_addon_active ()) {
            wp_register_script( 'wdk-autosuggestion-drop', WDK_AUTOSUGGESTION_URL. 'public/js/wdk-autosuggestion-drop.js', array(), 1.0, 'all' );
            wp_register_style( 'wdk-autosuggestion-drop', WDK_AUTOSUGGESTION_URL. 'public/css/wdk-autosuggestion-drop.css', array(), 1.0, 'all' );
        } 

        global $Winter_MVC_WDK;
        $Winter_MVC_WDK->model('field_m');
        $autosuggestion_fields = $Winter_MVC_WDK->field_m->get_by(array('autosuggestion is NOT NULL' => NULL));

        if($autosuggestion_fields) {
            foreach ($autosuggestion_fields as $field) {

                switch (wmvc_show_data('autosuggestion', $field)) {
                    case 'google_api_cities':
                        $this->google_api_cities('#field_'.$field->idfield);
                        break;
                    case 'google_api_countries':
                        $this->google_api_countries('#field_'.$field->idfield);
                        break;
                    case 'rapid_api_cities':
                        $this->rapid_api_cities('#field_'.$field->idfield);
                        break;
                    case 'rapid_api_countries':
                        $this->rapid_api_countries('#field_'.$field->idfield);
                        break;
                    case 'countries':
                        $this->countries('#field_'.$field->idfield);
                        break;
                    case 'db_selft_field':
                        $this->db_selft_field('#field_'.$field->idfield, $field->idfield);
                        break;
                    case 'db_locations':
                        $this->db_locations('#field_'.$field->idfield, $field->idfield);
                        break;
                    case 'db_categories':
                        $this->db_categories('#field_'.$field->idfield, $field->idfield);
                        break;
                    default:
                        # code...
                        break;
                }
            }

            if(!empty($this->custom_js)) {

                ?>
                    <script>
                        <?php echo wp_kses_post ($this->custom_js);?>
                    </script>
                <?php


            }
        }
    }

    private function google_api_cities ($seletor = NULL) {
        if(empty($seletor)) return false;
        if(!get_option('wdk_autosuggestion_google_api_key')) return false;
        if(!$this->is_addon_active ()) return false;

        wp_enqueue_script( 'google-map-api',  'https://maps.googleapis.com/maps/api/js?key='.get_option('wdk_autosuggestion_google_api_key').'&libraries=places', array(), 1.0, 'all' );
        wp_enqueue_script( 'wdk-autosuggestion-drop');
        wp_enqueue_style( 'wdk-autosuggestion-drop');
        wp_enqueue_script( 'wdk-trial-autosuggestion-drop');
        $this->custom_js .= "
            jQuery('".$seletor."').on('input', function(e){
                wdk_autosuggestion_google_cities(jQuery(this));
            })
        ";
    }

    private function google_api_countries ($seletor = NULL) {
        if(empty($seletor)) return false;
        if(!get_option('wdk_autosuggestion_google_api_key')) return false;
        if(!$this->is_addon_active ()) return false;

        wp_enqueue_script( 'google-map-api',  'https://maps.googleapis.com/maps/api/js?key='.get_option('wdk_autosuggestion_google_api_key').'&libraries=places', array(), 1.0, 'all' );
        wp_enqueue_script( 'wdk-autosuggestion-drop');
        wp_enqueue_style( 'wdk-autosuggestion-drop');
        wp_enqueue_script( 'wdk-trial-autosuggestion-drop');
        $this->custom_js .= "
            jQuery('".$seletor."').on('input', function(e){
                wdk_autosuggestion_google_countries(jQuery(this));
            })
        ";
    }

    private function rapid_api_cities ($seletor = NULL) {
        if(empty($seletor)) return false;
        if(!get_option('wdk_autosuggestion_rapidapi_api_key')) return false;
        if(!$this->is_addon_active ()) return false;

        wp_enqueue_script( 'wdk-autosuggestion-drop');
        wp_enqueue_style( 'wdk-autosuggestion-drop');
        wp_enqueue_script( 'wdk-trial-autosuggestion-drop');
        $this->custom_js .= "
            jQuery('".$seletor."').on('input', function(e){
                wdk_autosuggestion_rapidapi_cities(jQuery(this), '".get_option('wdk_autosuggestion_rapidapi_api_key')."');
            })
        ";
    }

    private function rapid_api_countries ($seletor = NULL) {
        if(empty($seletor)) return false;
        if(!get_option('wdk_autosuggestion_rapidapi_api_key')) return false;
        if(!$this->is_addon_active ()) return false;

        wp_enqueue_script( 'wdk-autosuggestion-drop');
        wp_enqueue_style( 'wdk-autosuggestion-drop');
        wp_enqueue_script( 'wdk-trial-autosuggestion-drop');
        $this->custom_js .= "
            jQuery('".$seletor."').on('input', function(e){
                wdk_autosuggestion_rapidapi_countries(jQuery(this), '".get_option('wdk_autosuggestion_rapidapi_api_key')."');
            })
        ";
    }

    private function countries ($seletor = NULL) {
        if(empty($seletor)) return false;

        wp_enqueue_style( 'wdk-autosuggestion-drop');
        wp_enqueue_script( 'wdk-trial-autosuggestion-drop');
        $this->custom_js .= "
            jQuery('".$seletor."').on('input', function(e){
                wdk_autosuggestion_rest_countries(jQuery(this));
            })
        ";
    }

    private function db_selft_field ($seletor = NULL, $field_id = NULL) {
        if(empty($seletor) || empty($field_id)) return false;

        wp_enqueue_style( 'wdk-trial-autosuggestion-drop');
        wp_enqueue_script( 'wdk-trial-autosuggestion-drop');
        $this->custom_js .= "
            jQuery('".$seletor."').on('input', function(e){
                wdk_autosuggestion_db_field(jQuery(this), '".esc_js($field_id)."');
            })
        ";
    }

    private function db_locations ($seletor = NULL, $field_id = NULL) {
        if(empty($seletor) || empty($field_id)) return false;

        wp_enqueue_style( 'wdk-trial-autosuggestion-drop');
        wp_enqueue_script( 'wdk-trial-autosuggestion-drop');
        $this->custom_js .= "
            jQuery('".$seletor."').on('input', function(e){
                wdk_autosuggestion_db_locations(jQuery(this));
            })
        ";
    }

    private function db_categories ($seletor = NULL, $field_id = NULL) {
        if(empty($seletor) || empty($field_id)) return false;

        wp_enqueue_style( 'wdk-trial-autosuggestion-drop');
        wp_enqueue_script( 'wdk-trial-autosuggestion-drop');
        $this->custom_js .= "
            jQuery('".$seletor."').on('input', function(e){
                wdk_autosuggestion_db_categories(jQuery(this));
            })
        ";
    }

}
