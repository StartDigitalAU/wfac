<?php

/****************************************************
 *
 * NAVIGATION
 *
 ****************************************************/

function register_theme_menus()
{

    if (function_exists('register_nav_menus')) {
        register_nav_menus(
            array(
                'about-menu' => 'About',
                'about-mobile_menu' => 'About (Mobile)',
                'whats_on-menu' => 'What\'s On',
                'whats_on-mobile_menu' => 'What\'s On (Mobile)',
                'visit-menu' => 'Visit',
                'visit-mobile_menu' => 'Visit (Mobile)',
                'news-menu' => 'News',
                'news-mobile_menu' => 'News (Mobile)',
                'courses-menu' => 'Courses',
                'courses-mobile_menu' => 'Courses (Mobile)',
                'for_artists-menu' => 'For Artists',
                'for_artists-mobile_menu' => 'For Artists (Mobile)',
                'venues-menu' => 'Venues',
                'venues-mobile_menu' => 'Venues (Mobile)',
                'footer-primary-menu' => 'Footer (Primary)',
                'explore-menu' => 'Explore',
                'join-menu' => 'Join',
                'shop-menu' => 'Shop',
            )
        );
    }
}
add_action('init', 'register_theme_menus');

function ajaxPaginationLinks($query, $base_url = null)
{

    // print_r($query);
    $content = '<div id="pagination" class="container container--gutters">';

    $current_page = intval($query->query_vars['paged']) ? intval($query->query_vars['paged']) : 1;

    $pagination = paginate_links(array(
        // 'base' => preg_replace('/\?.*/', '/', get_pagenum_link(999999999)) . '%_%',
        // 'current' => max(1, get_query_var('paged')),
        'format' => 'page/%#%',
        // 'current' => max(1, $query->query_vars['paged']),
        'total' => $query->max_num_pages,
        'prev_next' => false,
        'type' => 'array'
    ));

    if ($query->max_num_pages > 1) {
        $max_num_pages = $query->max_num_pages;

        $ellipsis = "<span class='gap'>...</span>";

        $content .= '<div class="pagination-numbers">';
        for ($i = 1; $i <= $max_num_pages; $i++) {

            if ($i == $current_page) {
                $content .= '<span class="page-number current">' . $i . '</span>';
            } else {

                if ($i == 1 || $i == $max_num_pages || ($i >= $current_page - 2 && $i <= $current_page + 2)) {
                    $content .= '<a href="#" data-pagination-page="' . $i . '" class="page-number">' . $i . '</a>';
                } elseif ($i == $current_page - 3 || $i == $current_page + 3) {
                    $content .= $ellipsis;
                }
            }
        }
        $content .= '</div>';
    }

    if ($query->max_num_pages > 1) {
        if ($current_page > 1) {
            $content .= '<a href="#" data-pagination-page="' . ($current_page - 1) .'" class="prev"><span class="icon"></span><span class="u-vis-hide">Previous</span></a>';
        } else {
            $content .= '<span class="prev disabled"><span class="icon"></span><span class="u-vis-hide">Previous</span></span>';
        }
    }

    if ($query->max_num_pages > 1) {
        if ($current_page < $query->max_num_pages) {
            $content .= '<a href="#" data-pagination-page="' . ($current_page + 1) .'" class="next"><span class="icon"></span><span class="u-vis-hide">Next</span></a>';
        } else {
            $content .= '<span class="next disabled"><span class="icon"></span><span class="u-vis-hide">Next</span></span>';
        }
    }
    $content .= '</div>';

    return $content;
}

function paginationLinks($query)
{
    global $global_options;
?>
    <div id="pagination" class="container container--gutters">
        <?php
        $pagination = paginate_links(array(
            'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format' => '?paged=%#%',
            'current' => max(1, $query->query_vars['paged']),
            'total' => $query->max_num_pages,
            'prev_next' => false,
            'type' => 'array'
        ));
        ?>
        <?php if (!empty($pagination)) { ?>
            <div class="pagination-numbers">
                <?php
                    foreach ($pagination as $pager) {
                        echo '' . $pager . '';
                    }
                ?>
            </div>
        <?php } ?>
        <?php
        if ($query->max_num_pages > 1) {
            if ($prev = prevLink()) {
                echo $prev;
            } else {
                echo '<span class="prev disabled"><span class="icon"></span><span class="u-vis-hide">Previous</span></span>';
            }
        }
        ?>

        <?php
        if ($query->max_num_pages > 1) {
            if ($next = nextLink($query)) {
                echo $next;
            } else {
                echo '<span class="next disabled"><span class="u-vis-hide">Next</span><span class="icon"></span></span>';
            }
        }
        ?>
    </div>
<?php
}
function getPaginationLinks($query)
{
    global $global_options;

    $content = '<div id="pagination" class="container container--gutters">';

    $pagination = paginate_links(array(
        'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
        'format' => '?paged=%#%',
        'current' => max(1, $query->query_vars['paged']),
        'total' => $query->max_num_pages,
        'prev_next' => false,
        'type' => 'array'
    ));

    if (!empty($pagination)) {
        $content .= '<div class="pagination-numbers">';
        foreach ($pagination as $pager) {
            $content .= '' . $pager . '';
        }
        $content .= '</div>';
    }

    if ($query->max_num_pages > 1) {
        if ($prev = prevLink()) {
            $content .= $prev;
        } else {
            $content .= '<span class="prev disabled"><span class="icon"></span><span class="u-vis-hide">Previous</span></span>';
        }
    }

    if ($query->max_num_pages > 1) {
        if ($next = nextLink($query)) {
            $content .= $next;
        } else {
            $content .= '<span class="next disabled">Next<span></span></span>';
        }
    }
    $content .= '</div>';

    return $content;
}
function prevLink()
{
    global $global_options;
    global $paged;
    if (!is_single() && $paged > 1) {
        return '<a href="' . previous_posts(false) . '" data-page="" class="prev"><span class="icon"></span><span class="u-vis-hide">Previous</span></a>';
    }
}
function nextLink($query)
{
    global $global_options;
    global $paged;
    $max_page = $query->max_num_pages;
    if (!$paged) {
        $paged = 1;
    }
    $nextpage = intval($paged) + 1;
    if (!is_single() && ($nextpage <= $max_page)) {
        return '<a href="' . next_posts($max_page, false) . '" class="next"><span class="u-vis-hide">Next</span><span class="icon"></span></a>';
    }
}
