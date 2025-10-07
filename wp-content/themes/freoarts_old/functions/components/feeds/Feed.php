<?php
namespace Humaan;

use Humaan\Instagram;

if (!class_exists('Feed')) {
    class Feed
    {

        /**
         * Log file name
         *
         */
        const AUTH_BASE_URL = 'https://social-auth.humaan.co/';

        private $available_adaptors = ['Instagram'];

        public function __construct($adaptors = null)
        {

            $this->adaptors = $adaptors;

            $this->initAdaptors();
            $this->initViews();
            $this->registerAPIRoutes();


        }
        
        /******************************************************************
        *
        * Views
        *
        ******************************************************************/
        
        public function initAdaptors()
        {
            if (is_array($this->adaptors) && count(array_intersect($this->adaptors, $this->available_adaptors)) === count($this->adaptors)) {
                
                if(in_array('Instagram', $this->adaptors)) {
                    $this->Instagram = new Instagram();
                }

                return;
            }

            throw new \Exception("Error: initAdaptors() - The parameter isn't an array or invalid adaptors used.");

        }

        /**
        * Initialise the methods required for creating the Admin views
        *
        */
        public function initViews()
        {
            // Create admin menus
            add_action('admin_menu', array($this, 'addAdminViews'));
        }

        /**
        * Add the admin menu item
        *
        */
        public function addAdminViews()
        {
            
            // View for configuring Runway
            add_options_page(
                'Feeds',
                'Feeds',
                'manage_options',
                'humaan_feeds',
                array($this, 'adminViewSettings'),
                50
            );
        }

        /**
         * Outputs the admin view for managing social feeds
         *
         */
        public function adminViewSettings()
        {
        ?>
            <div class='wrap'>

                <h1 class="wp-heading-inline">Feeds</h1>

                <hr class="wp-header-end">

                    <div id="poststuff">
            <?php
                    if (is_array($this->adaptors) && count(array_intersect($this->adaptors, $this->available_adaptors)) === count($this->adaptors)) {
                
                if(in_array('Instagram', $this->adaptors)) {
                    echo $this->Instagram->getAdminView();
                }
            } ?>

                       
                </div>
            </div>
            <?php
        }

        public function registerAPIRoutes() {
            add_action('rest_api_init', function () {

                if (is_array($this->adaptors) && count(array_intersect($this->adaptors, $this->available_adaptors)) === count($this->adaptors)) {
                
                    if(in_array('Instagram', $this->adaptors)) {
                        register_rest_route('feed', 'instagram/cache', [
                            'methods' => 'GET',
                            'callback' => [$this->Instagram, 'rest_cache_instagram'],
                            'permission_callback' => true,
                        ]);
        
                        register_rest_route('feed', 'instagram/refresh', [
                            'methods' => 'GET',
                            'callback' => [$this->Instagram, 'rest_refresh_token'],
                            'permission_callback' => true,
                        ]);
                    }
    
                    return;
                }

             
            });
        }


    }
}