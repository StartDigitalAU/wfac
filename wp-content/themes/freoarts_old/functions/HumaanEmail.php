<?php
use Postmark\PostmarkClient;

if (!class_exists("HumaanEmail")) {
    class HumaanEmail
    {
        private $postmark_client;

        public function __construct()
        {
            $postmark_api_key = get_field('postmark_api_key', 'option');
            $this->postmark_client = new PostmarkClient($postmark_api_key);
        }

        public function sendEmail($html, $subject, $from_name, $from, $to, $bcc=null)
        {
            try {
                $send_result = $this->postmark_client->sendEmail(
                    $from,
                    $to, // to
                    $from_name . ' - ' . $subject,
                    $html,
                    null,
                    null,
                    null,
                    null,
                    null,
                    $bcc
                );

                $success = true;
            } catch (\Postmark\Models\PostmarkException $ex) {
                $success = false;
                error_log("Error sending email: " . $ex->message);
            } catch (Exception $ex) {
                $success = false;
                error_log($ex->getMessage());
            }

            return $success;
        }
    }
}
