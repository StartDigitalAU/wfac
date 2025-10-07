<div class="whats-on waypoint">
    <h2 class="whats-on__title title step-in">What's On</h2>
    <div class="whats-on__rows">

        <?php
        $exhibitions = new WP_Query([
            'posts_per_page'   => 5,
            'post_type' => 'whatson',
            'order' => 'ASC',
            'orderby' => 'meta_value_num',
            'meta_key' => 'start_date',
            'meta_query'    => array(
                'relation' => 'AND',
                array(
                    'key'       => 'type',
                    'value'     => 'exhibition',
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'start_date',
                        'value'   => date('Ymd'),
                        'compare' => '>=',
                    ),
                    array(
                        'key'     => 'end_date',
                        'value'   => date('Ymd'),
                        'compare' => '>=',
                    ),
                ),
            ),
        ]);

        if ($exhibitions->have_posts()) :
            $exhibition_link = get_field('exhibitions_heading');
            render('parts/front-page/whats-on-row.php', [
                "title_text" => ifne($exhibition_link, 'title'),
                "title_url" => ifne($exhibition_link, 'url'),
                'events' => $exhibitions->posts,
            ]);
        endif;
        ?>

        <?php
        $events = new WP_Query([
            'posts_per_page'   => 5,
            'post_type' => 'whatson',
            'order' => 'ASC',
            'orderby' => 'meta_value_num',
            'meta_key' => 'start_date',
            'meta_query'    => array(
                'relation' => 'AND',
                array(
                    'key'       => 'type',
                    'value'     => 'event',
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'start_date',
                        'value'   => date('Ymd'),
                        'compare' => '>=',
                    ),
                    array(
                        'key'     => 'end_date',
                        'value'   => date('Ymd'),
                        'compare' => '>=',
                    ),
                ),
            ),
        ]);

        if ($events->have_posts()) :
            $events_link = get_field('events_heading');
            render('parts/front-page/whats-on-row.php', [
                "title_text" => ifne($events_link, 'title'),
                "title_url" => ifne($events_link, 'url'),
                'events' => $events->posts,
            ]);
        endif;
        ?>

        <?php
        $learns = new WP_Query([
            'posts_per_page'   => 5,
            'post_type' => 'product',
            'post_status' => 'publish',
            'tax_query' => [
                [
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'course'
                ]
            ],
            'order' => 'ASC',
            'orderby' => 'meta_value_num',
            'meta_key' => 'start_date',
            'meta_query'    => array(
                'relation' => 'OR',
                array(
                    'key'     => 'start_date',
                    'value'   => date('Ymd'),
                    'compare' => '>=',
                ),
                array(
                    'key'     => 'end_date',
                    'value'   => date('Ymd'),
                    'compare' => '>=',
                ),
            ),
        ]);

        if ($learns->have_posts()) :
            $learn_link = get_field('learn_heading');
            render('parts/front-page/whats-on-row.php', [
                "title_text" => ifne($learn_link, 'title'),
                "title_url" => ifne($learn_link, 'url'),
                'events' => $learns->posts,
            ]);
        endif;
        ?>
    </div>
</div>