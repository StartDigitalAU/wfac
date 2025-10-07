<?php global $fields, $type; ?>
<div class="sub-header-wrapper">
    <div class="sub-header container">
        <nav>
            <ul class="clearfix">
                <li>
                    <a href="<?php echo $GLOBALS['site_url'] . '/whats-on/'; ?>" class="icon link-back" title="Go Back to back to what’s on">Back to what’s on</a>
                </li>
                <li>
                    <?php

                    // Get the next post based on the start date
                    $args = array(
                        'post_type' => 'whatson',
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key' => 'type',
                                'value' => $type,
                                'type' => 'string',
                                'compare' => '='
                            ),
                            array(
                                'key' => 'start_date',
                                'value' => $fields['start_date'],
                                'type' => 'numeric',
                                'compare' => '>'
                            )
                        ),
                        'posts_per_page' => 1,
                        'meta_key' => 'end_date',
                        'orderby' => 'meta_value_num',
                        'order' => 'ASC'
                    );

                    $next_query = new WP_Query($args);

                    ?>
                    <?php if ($next_query->post_count > 0) : ?>
                        <?php while ($next_query->have_posts()) : ?>
                            <?php $next_query->the_post(); ?>
                            <a href="<?php the_permalink(); ?>" class="icon link-forward" title="Go to the next exhibition: <?php the_title(); ?>">Next <?php echo $type; ?><span>:</span>  <span class="u-color"><?php the_title(); ?></span></a>
                            <?php break; ?>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </li>
            </ul>
        </nav>
    </div>
</div>