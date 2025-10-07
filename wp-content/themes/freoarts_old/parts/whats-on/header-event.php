<div class="select-wrapper has-drop">
    <?php echo get_link_html('/whats-on/exhibitions/', 'Category', 'View Category', false); ?>
    <ul class="cat-dropdown clearfix">
        <?php
        $categories = get_terms(array(
            'taxonomy' => 'event_category',
            'hide_empty' => false
        ));

        $current_term_slug = '';

        // Filter by event category
        if (is_tax('event_category')) {

            $current_term = get_queried_object();
            $current_term_slug = $current_term->slug;
        }

        foreach ($categories as $category) {

            $url = get_term_link($category, 'event_category');

            // If current page is the selected term?
            $classes = array();
            if ($category->slug == $current_term_slug) {
                $classes[] = 'active';
            }

            $li = '<li>' . get_link_html($url, $category->name, 'View ' . $category->name, null, $classes) . '</li>';

            echo $li;
        }

        ?>
    </ul>
</div>

