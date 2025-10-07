<?php

$args = array(
    'post_type' => 'product',
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => 'start_date',
            'value' => date('Ymd'),
            'type' => 'numeric',
            'compare' => '>='
        )
    ),
    'tax_query' => array(
        array(
            'taxonomy' => 'product_type',
            'field'    => 'slug',
            'terms'    => 'course'
        )
    ),
    'posts_per_page' => 5,
    'meta_key' => 'start_date',
    'orderby' => 'meta_value_num',
    'order' => 'ASC'
);

$adults_args = $args;
$adults_args['meta_query'][] = array(
    'relation' => 'OR',
    array(
        'key' => 'is_kids_course',
        'value' => 0,
        'type' => 'BOOLEAN',
        'compare' => '=='
    ),
    array(
        'key' => 'is_kids_course',
        'compare' => 'NOT EXISTS'
    )
);
$adults_query = new WP_Query($adults_args);

$kids_args = $args;
$kids_args['meta_query'][] = array(
    'key' => 'is_kids_course',
    'value' => 1,
    'type' => 'BOOLEAN',
    'compare' => '=='
);
$kids_query = new WP_Query($kids_args);
$product_factory = new WC_Product_Factory();
?>
<div class="courses-slider-wrapper has-orange waypoint">
    <div class="container">
        <header class="clearfix">
            <h2 class="title">Courses</h2>
            <div class="course-select">
                <ul class="clearfix">
                    <?php if ($adults_query->have_posts()) : ?>
                        <li><a href="#" title="View the Adult Courses" class="is-adults btn med active" data-slider="#course-slider-adult">Adult Courses</a></li>
                    <?php endif; ?>
                    <?php if ($kids_query->have_posts()) : ?>
                        <li><a href="#" title="View the Kids Courses" class="is-kids btn med" data-slider="#course-slider-kid">Kids Courses</a></li>
                    <?php endif; ?>
                </ul>
                <div class="courses-slider-nav adults" data-nav="#course-slider-adult"></div>
                <div class="courses-slider-nav kids" data-nav="#course-slider-kid"></div>
            </div>
        </header>
    </div>

    <div class="course-slider-outer step-in">
        <div id="course-slider-adult" class="course-slider adults-courses">
            <?php if ($adults_query->have_posts()) : ?>
                <?php while ($adults_query->have_posts()) : $adults_query->the_post(); ?>
                    <?php
                    $fields = get_fields();

                    $tutor = get_post($fields['tutor']);

                    $product = $product_factory->get_product(get_the_ID());
                    // Retrieve hero image
                    $hero_image = get_resized_image($fields['hero_image'], 'course');
                    ?>
                    <a href="<?php the_permalink(); ?>" class="card course<?= $product->is_in_stock() ? "" : " out-of-stock"; ?>" title="Read more about <?php the_title(); ?>">
                        <article>
                            <div class="img-wrapper" style="background-image: url(<?php echo $hero_image; ?>)"></div>
                            <div class="tag-wrapper clearfix">
                                <span class="tag">Course</span>
                                <span class="tag course-type">Adults</span>
                            </div>
                            <div class="content">
                                <h3 class="title"><?php the_title(); ?></h3>
                                <div class="course-meta clearfix">
                                    <span class="instructor"><?php echo $tutor->post_title; ?></span>
                                    <?php
                                    $difficulties = $fields['difficulty'];
                                    foreach ($difficulties as $difficulty) {
                                        echo '<span class="pill">' . $difficulty . '</span>';
                                    }
                                    ?>
                                </div>
                                <p><?php echo $fields['summary']; ?></p>
                                <span class="faux-link">View Course Information</span>
                            </div>
                        </article>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div><!-- end adult slider -->

        <div id="course-slider-kid" class="course-slider kids-courses is-hidden">
            <?php if ($kids_query->have_posts()) : ?>
                <?php while ($kids_query->have_posts()) : $kids_query->the_post(); ?>
                    <?php
                    $fields = get_fields();

                    $tutor = get_post($fields['tutor']);
                    $product = $product_factory->get_product(get_the_ID());

                    // Retrieve hero image
                    $hero_image = get_resized_image($fields['hero_image'], 'course');
                    ?>
                    <a href="<?php the_permalink(); ?>" class="card course<?= $product->is_in_stock() ? "" : " out-of-stock"; ?>" title="Read more about <?php the_title(); ?>">
                        <article>
                            <div class="img-wrapper" style="background-image: url(<?php echo $hero_image; ?>)"></div>
                            <div class="tag-wrapper clearfix">
                                <span class="tag">Course</span>
                                <span class="tag course-type">Kids</span>
                                <?php if( !$product->is_in_stock() ) { ?> 
                                    <span class="tag out-of-stock">Sold out</span>
                                <?php } ?>
                            </div>
                            <div class="content">
                                <h3 class="title"><?php the_title(); ?></h3>
                                <div class="course-meta clearfix">
                                    <span class="instructor"><?php echo $tutor->post_title; ?></span>
                                    <?php
                                    $difficulties = $fields['difficulty'];
                                    foreach ($difficulties as $difficulty) {
                                        echo '<span class="pill">' . $difficulty . '</span>';
                                    }
                                    ?>
                                </div>
                                <p><?php echo $fields['summary']; ?></p>
                                <span class="faux-link">View Course Information</span>
                            </div>
                        </article>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>

        </div><!-- end kid slider -->

        <div class="container">

            <div class="mobile-courses">
                <?php if ($adults_query->have_posts()) : ?>
                    <?php while ($adults_query->have_posts()) : $adults_query->the_post(); ?>
                        <?php
                        $fields = get_fields();

                        $tutor = get_post($fields['tutor']);

                        // Retrieve hero image
                        $hero_image = get_resized_image($fields['hero_image'], 'course');
                        ?>
                        <a href="<?php the_permalink(); ?>" class="card course" title="Read more about <?php the_title(); ?>">
                            <article>
                                <div class="img-wrapper" style="background-image: url(<?php echo $hero_image; ?>)"></div>
                                <div class="tag-wrapper clearfix">
                                    <span class="tag">Course</span>
                                    <span class="tag course-type">Adults</span>
                                </div>
                                <div class="content">
                                    <h3 class="title"><?php the_title(); ?></h3>
                                    <div class="course-meta clearfix">
                                        <span class="instructor"><?php echo $tutor->post_title; ?></span>
                                        <?php
                                        $difficulties = $fields['difficulty'];
                                        foreach ($difficulties as $difficulty) {
                                            echo '<span class="pill">' . $difficulty . '</span>';
                                        }
                                        ?>
                                    </div>
                                    <p><?php echo $fields['summary']; ?></p>
                                    <span class="faux-link">View Course Information</span>
                                </div>
                            </article>
                        </a>
                        <?php break; ?>
                    <?php endwhile; ?>
                <?php endif; ?>

                <?php if ($kids_query->have_posts()) : ?>
                    <?php while ($kids_query->have_posts()) : $kids_query->the_post(); ?>
                        <?php
                        $fields = get_fields();

                        $tutor = get_post($fields['tutor']);

                        // Retrieve hero image
                        $hero_image = get_resized_image($fields['hero_image'], 'course');
                        ?>
                        <a href="<?php the_permalink(); ?>" class="card course" title="Read more about <?php the_title(); ?>">
                            <article>
                                <div class="img-wrapper" style="background-image: url(<?php echo $hero_image; ?>)"></div>
                                <div class="tag-wrapper clearfix">
                                    <span class="tag">Course</span>
                                    <span class="tag course-type">Kids</span>
                                </div>
                                <div class="content">
                                    <h3 class="title"><?php the_title(); ?></h3>
                                    <div class="course-meta clearfix">
                                        <span class="instructor"><?php echo $tutor->post_title; ?></span>
                                        <?php
                                        $difficulties = $fields['difficulty'];
                                        foreach ($difficulties as $difficulty) {
                                            echo '<span class="pill">' . $difficulty . '</span>';
                                        }
                                        ?>
                                    </div>
                                    <p><?php echo $fields['summary']; ?></p>
                                    <span class="faux-link">View Course Information</span>
                                </div>
                            </article>
                        </a>
                        <?php break; ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div><!-- end mobile course -->
        </div><!-- end slider-outer -->
    </div>
</div><!-- course-panel-wrapper -->