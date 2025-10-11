<?php

namespace TheStart\Services;

use Timber\Timber;

/**
 * Program Service
 * Handles program-related business logic with complex query support
 */
class ProgramService
{
    /**
     * Load more programs via AJAX
     *
     * @param int $paged Current page number
     * @param string $display Display type (grid, list, feed)
     * @param int $pageId The program page ID for ACF fields
     * @return array Response data
     */
    public function loadMorePrograms(int $paged, string $display, int $pageId): array
    {

        $display = $this->validateDisplayType($display);

        $args = $this->buildQueryArgs($paged, $pageId);

        $query = new \WP_Query($args);

        $html = $this->render($query, $display);

        return [
            'html' => $html,
            'has_more' => $paged < $query->max_num_pages,
            'current_page' => $paged,
            'max_pages' => $query->max_num_pages
        ];
    }

    /**
     * Validate display type
     *
     * @param string $display
     * @return string
     */
    private function validateDisplayType(string $display): string
    {
        $validTypes = ['grid', 'list', 'feed'];
        return in_array($display, $validTypes, true) ? $display : 'grid';
    }

    /**
     * Build WP_Query arguments based on page settings
     *
     * @param int $paged
     * @param int $pageId
     * @return array
     */
    private function buildQueryArgs(int $paged, int $pageId): array
    {
        // Get ACF fields from the page
        $postsToShow = get_field('posts_to_show', $pageId);
        $limit = get_field('posts_limit', $pageId) ?: 12;

        $args = [
            'post_type' => $this->getPostTypes($postsToShow),
            'posts_per_page' => $limit,
            'paged' => $paged,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish'
        ];

        // Add taxonomy query if needed
        $taxQuery = $this->getTaxQuery($postsToShow);

        if (!empty($taxQuery)) {
            $args['tax_query'] = $taxQuery;
        }

        return $args;
    }

    /**
     * Get post types based on posts_to_show field
     *
     * @param string|null $postsToShow
     * @return array
     */
    private function getPostTypes(?string $postsToShow): array
    {
        return match ($postsToShow) {
            'all' => ['whatson', 'product'],
            'exhibition', 'event' => ['whatson'],
            default => [$postsToShow ?? 'program'],
        };
    }

    /**
     * Get taxonomy query based on posts_to_show field
     *
     * @param string|null $postsToShow
     * @return array
     */
    private function getTaxQuery(?string $postsToShow): array
    {
        return match ($postsToShow) {
            'exhibition' => [
                [
                    'taxonomy' => 'exhibition_tag',
                    'operator' => 'EXISTS'
                ],
            ],
            'event' => [
                'relation' => 'OR',
                [
                    'taxonomy' => 'event_tag',
                    'operator' => 'EXISTS'
                ],
                [
                    'taxonomy' => 'event_category',
                    'operator' => 'EXISTS'
                ]
            ],
            default => [],
        };
    }

    public function render($args, $display = 'grid')
    {
        $posts = Timber::get_posts($args);

        $context = array(
            'program_posts' => $posts,
            'display' => $display
        );

        return Timber::compile('partials/program/program-render.twig', $context);
    }
}
