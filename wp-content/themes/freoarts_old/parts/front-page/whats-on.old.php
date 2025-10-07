<?php

$colors = array(
    'red',
    'yellow',
    'teal'
);

$args['meta_query'][] = array(
    'key' => 'end_date',
    'value' => date('Ymd'),
    'type' => 'numeric',
    'compare' => '>='
);

$exhibition_query = new WP_Query(array(
    'post_type' => 'whatson',
    'meta_query' => array(
        array(
            'key' => 'type',
            'value' => 'exhibition',
            'type' => 'string',
            'compare' => '='
        ),
        array(
            'key' => 'end_date',
            'value' => date('Ymd'),
            'type' => 'numeric',
            'compare' => '>='
        )
    ),
    'posts_per_page' => 3,
    'meta_key' => 'start_date',
    'orderby' => 'meta_value_num',
    'order' => 'ASC'
));

$event_query = new WP_Query(array(
    'post_type' => 'whatson',
    'meta_query' => array(
        array(
            'key' => 'type',
            'value' => 'event',
            'type' => 'string',
            'compare' => '='
        ),
        array(
            'key' => 'end_date',
            'value' => date('Ymd'),
            'type' => 'numeric',
            'compare' => '>='
        )
    ),
    'posts_per_page' => 3,
    'meta_key' => 'start_date',
    'orderby' => 'meta_value_num',
    'order' => 'ASC'
));

?>
<div class="whats-on-wrapper waypoint">
    <div class="container">
        <header>
            <h2 class="title">What's on at FAC</h2>
        </header>
    </div>
    <div class="container last">

        <?php $odd_row = true; ?>
        <?php for ($i = 0; $i <= 2; $i++) { ?>
            <div class="grid-row clearfix has-<?php echo $colors[$i]; ?>">
                <div class="grid-wrapper">

                    <?php if (isset($exhibition_query->posts[$i])) { ?>
                        <?php
                        $item_id = $exhibition_query->posts[$i]->ID;
                        $fields = get_fields($item_id);
                        ?>
                        <div class="col step-in">
                            <a href="<?php echo get_the_permalink($item_id); ?>" class="card exhibition<?php if (!$odd_row) { echo ' is-alt'; } ?>" title="Read more about <?php echo get_the_title($item_id); ?>">
                                <article>
                                    <?php
                                    $hero_image = isset($fields['hero_image']) ? $fields['hero_image'] : null;
                                    $image = get_resized_image($hero_image, 'whats_on_card', $GLOBALS['default_img']);
                                    ?>
                                    <div class="img-wrapper" style="background-image: url(<?php echo $image; ?>)"></div>
                                    <span class="tag">Exhibition</span>
                                    <div class="content">
                                        <h3 class="title"><?php echo get_the_title($item_id); ?></h3>
                                        <span class="date-time">
                                            <?php echo get_formatted_datetime($fields); ?>
                                        </span>
                                    </div>
                                </article>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if (isset($event_query->posts[$i])) { ?>
                        <?php
                        $item_id = $event_query->posts[$i]->ID;
                        $fields = get_fields($item_id);
                        ?>
                        <div class="col step-in">
                            <a href="<?php echo get_the_permalink($item_id); ?>" class="card event" title="Read more about <?php echo get_the_title($item_id); ?>">
                                <article>
                                    <?php
                                    $hero_image = isset($fields['hero_image']) ? $fields['hero_image'] : null;
                                    $image = get_resized_image($hero_image, 'whats_on_card', $GLOBALS['default_img']);
                                    ?>
                                    <div class="img-wrapper" style="background-image: url(<?php echo $image; ?>)"></div>
                                    <span class="tag">Event</span>
                                    <div class="content">
                                        <h3 class="title"><?php echo get_the_title($item_id); ?></h3>
                                        <span class="date-time">
                                            <?php echo get_formatted_datetime($fields); ?>
                                        </span>
                                        <p><?php echo ifne($fields, 'summary'); ?></p>
                                    </div>
                                </article>
                            </a>
                        </div>
                    <?php } ?>

                    <?php $odd_row = ($odd_row) ? false: true; ?>

                </div>
            </div>
        <?php } ?>

    </div>
</div>