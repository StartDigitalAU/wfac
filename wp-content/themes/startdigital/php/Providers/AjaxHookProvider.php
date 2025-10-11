<?php

namespace TheStart\Providers;

use TheStart\Services\ProgramService;

/**
 * Ajax Hook Provider
 * Handles AJAX-related WordPress hooks
 */
class AjaxHookProvider extends HookProvider
{
    private ProgramService $programService;

    public function __construct()
    {
        $this->programService = new ProgramService();
    }

    /**
     * Register all hooks for this provider
     */
    public function register(): void
    {
        $this->addAction('wp_ajax_load_more_programs', [$this, 'handleLoadMorePrograms']);
        $this->addAction('wp_ajax_nopriv_load_more_programs', [$this, 'handleLoadMorePrograms']);
    }


    /**
     * Handle AJAX request for loading more programs
     */
    public function handleLoadMorePrograms(): void
    {

        self::verifyNonce($_POST['nonce']);

        // Get and sanitize parameters
        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
        $display = isset($_POST['display']) ? sanitize_text_field($_POST['display']) : 'grid';
        $pageId = isset($_POST['pageId']) ? intval($_POST['pageId']) : 0;

        // Validate page ID
        if (!$pageId || !get_post($pageId)) {
            wp_send_json_error(['message' => 'Invalid page ID: ' . $pageId]);
            return;
        }

        try {
            $response = $this->programService->loadMorePrograms($paged, $display, $pageId);
            wp_send_json_success($response);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => 'Error loading programs: ' . $e->getMessage()]);
        }
    }

    public static function verifyNonce($nonce)
    {
        if (!wp_verify_nonce($nonce, 'ajax_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
            return;
        }
    }
}
