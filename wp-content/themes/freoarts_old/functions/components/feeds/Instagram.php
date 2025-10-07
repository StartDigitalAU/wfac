<?php

namespace Humaan;

use Humaan\InstagramBasicDisplay;

if (!class_exists('Instagram')) {
    /**
     * Class Instagram
     */
    class Instagram
    {

        public function __construct()
        {
            // Saving
            add_action('init', [$this, 'init'], 100, 0);
        }

        public function init() {
            $this->updateOptions();
        }

        public function getAdminView() {
           
            $access_token = $this->getLongLivedToken();

            if (!empty($access_token)) {
                $instagramAPI = new InstagramBasicDisplay($access_token);

                $instagram_user_profile = $instagramAPI->getUserProfile();
            }
            ?>

            <div class="postbox acf-postbox">
            <h2 class="title hndle"><span>Instagram</span></h2>

            <div class="inside acf-fields -left">

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label for="runway_client_id">Login</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                        <a href="<?= $this->getConnectUrl(); ?>" class="button button-primary">Connect Instagram</a>
                        <?php if (!empty($access_token)) {
                            echo "Connected as: " . $instagram_user_profile->username;
                        }
                        ?>
                        </div>
                    </div>
                </div>
                <?php if (!empty($access_token)) : ?>
            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label>Long-Lived Access Token</label>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <?= $this->getLongLivedToken(); ?>
                    </div>
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label>Token Expiry</label>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <?= $this->getTokenExpiry(); ?>
                        <a href="/wp-json/feed/instagram/refresh" target="_blank" class="button button-secondary">Refresh Token</a>
                    </div>
                </div>
            </div>
                <?php endif; ?>

                <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label>Feed Last Updated</label>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                <?php
                  $instagram_feed_cache_date =  get_option('instagram_feed_cache_date');
                if(!empty($instagram_feed_cache_date)) : ?>
                        <?= $instagram_feed_cache_date ?>
                <?php endif; ?>
                <a href="/wp-json/feed/instagram/cache" target="_blank" class="button button-secondary">Cache Instagram</a>
                    </div>
                </div>
            </div>
    </div>
      <?php  }
    
        /**
         * instagram Connect URL
         *
         */
        public function getConnectUrl()
        {

            return Feed::AUTH_BASE_URL . "instagram/?redirect_to=" . menu_page_url('humaan_feeds', false);
        }

        public function updateOptions()
        {
            if (!empty($_GET['ig_expires'])) {
                update_option('instagram_token_expiry', $_GET['ig_expires'], false);

                $current_time = current_time('Y-m-d H:i:s');
                update_option('instagram_token_generated_date', $current_time, false);
            }

            if (!empty($_GET['ig_token'])) {

                $old_token = $this->getLongLivedToken();
                $new_token = $_GET['ig_token'];
                update_option('instagram_long-lived_token', $_GET['ig_token'], false);

                if($old_token !== $new_token) {
                    wp_redirect(admin_url('/options-general.php?page=humaan_feeds&updated'));
                    // wp_redirect("admin_url/wp-admin/options-general.php?page=humaan_feeds&saved");
                    exit;
                }
            }

            // wp_redirect(admin_url('/admin.php?page=runway_adaptor'));
        }

        /**
         * instagram Long Lived Token
         *
         */
        public function getLongLivedToken()
        {

            return get_option('instagram_long-lived_token');
        }


        public function getTokenExpiry()
        {

            $expires = get_option('instagram_token_expiry');
            $instagram_token_generated_date = get_option('instagram_token_generated_date');

            return date($instagram_token_generated_date, strtotime("+$expires sec"));
        }


        public function getFeed() {
            $feed_file = TEMPLATEPATH . '/cache/instagram-feed.json';
        
            if (file_exists($feed_file)) {
        
                return json_decode(file_get_contents($feed_file), true );
            }
        
            return array();
        }

        public function cacheInstagram() {
            $feed_file = TEMPLATEPATH . '/cache/instagram-feed.json';

            $token_expiry = $this->getTokenExpiry();
            $today = time();
            $interval = $today - strtotime($token_expiry);
            $days = floor($interval / 86400); // 1 day

            if(21 < $days) {
                $this->refreshToken();
            }

            $access_token = $this->getLongLivedToken();
            $instagramAPI = new InstagramBasicDisplay($access_token);

            $instagram_posts = json_encode($instagramAPI->getUserMedia());

            file_put_contents($feed_file, $instagram_posts);

            $current_time = current_time('Y-m-d H:i:s');
            update_option('instagram_feed_cache_date', $current_time, false);
            return "done";
        }

        public function refreshToken() {
            $access_token = $this->getLongLivedToken();
            $instagramAPI = new InstagramBasicDisplay($access_token);
            
            $token = $instagramAPI->refreshToken($access_token);

            update_option('instagram_token_expiry', $token->expires_in, false);

            $current_time = current_time('Y-m-d H:i:s');
            update_option('instagram_token_generated_date', $current_time, false);

            update_option('instagram_long-lived_token', $token->access_token, false);
            return "done";
        }
        
        public function rest_cache_instagram() {
            return $this->cacheInstagram();
        }


        public function rest_refresh_token() {
            return $this->refreshToken();
        }
    }
}
