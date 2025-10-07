<div class="sub-header-panel">
    <div class="sub-header-panel__inner">
        <div class="container container--gutters">
            <a href="#" class="close-sub-header-panel close-cal">
                <span class="u-vis-hide">Close</span>
                <span class="icon"></span>
            </a>
            <div class="filter-wrapper-outer" data-panel="type-for-adults">
                <h2 class="title title--h2">For Adults Filters</h2>
                <ul>
                    <?php
                    $terms = get_terms([
                        'taxonomy' => 'course_adults_category',
                        'hide_empty' => false
                    ]);
                    foreach ($terms as $term) {
                        echo '<li><button class="cat-filter__button" data-term="' . $term->term_id . '"><span class="text">' . $term->name . '</span><span class="icon"></span></button></li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="filter-wrapper-outer" data-panel="type-for-kids">
                <h2 class="title title--h2">For Kids Filters</h2>
                <ul>
                    <?php
                    $terms = get_terms([
                        'taxonomy' => 'course_kids_category',
                        'hide_empty' => false
                    ]);
                    foreach ($terms as $term) {
                        echo '<li><button class="cat-filter__button" data-term="' . $term->term_id . '"><span class="text">' . $term->name . '</span><span class="icon"></span></button></li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="calendar-wrapper-outer" data-panel="date-selected">
                <div class="calendar-loading">
                    <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" preserveAspectRatio="xMidYMid" style="margin:auto;background:0 0;display:block;shape-rendering:auto" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="32" fill="none" stroke="#27282d" stroke-dasharray="50.26548245743669 50.26548245743669" stroke-linecap="round" stroke-width="8">
                            <animateTransform attributeName="transform" dur="1.3513513513513513s" keyTimes="0;1" repeatCount="indefinite" type="rotate" values="0 50 50;360 50 50" />
                        </circle>
                    </svg>
                </div>
                <?php
                $current_year = date('Y');
                $current_month = date('F');

                $base_time = strtotime(date('Y-m', time()) . '-01 00:00:01');
                $prev_month = strtotime('last month', $base_time);
                $next_month  = strtotime('next month', $base_time);
                ?>
                <div class="calendar-wrapper">
                    <section class="calendar">
                        <div class="inner" id="events-calendar">
                            <ul class="cal-header">
                                <li class="cal-month">
                                    <h2 class="title title--h2">
                                        <?php echo $current_month ?>
                                        <?php echo $current_year ?>
                                    </h2>
                                </li>
                                <li class="cal-prev"
                                    title="Previous month"
                                    data-month="<?php echo date('n', $prev_month); ?>"
                                    data-year="<?php echo date('Y', $prev_month); ?>">
                                    <span class="text"><?php echo date('M Y', $prev_month); ?></span>
                                    <span class="icon"></span>
                                </li>
                                <li class="cal-next"
                                    title="Next month"
                                    data-month="<?php echo date('n', $next_month); ?>"
                                    data-year="<?php echo date('Y', $next_month); ?>">
                                    <span class="text"><?php echo date('M Y', $next_month); ?></span>
                                    <span class="icon"></span>
                                </li>
                            </ul>

                            <div class="cal-grid">
                                <ul class="cal-week">
                                    <li title="Sunday">SUN</li>
                                    <li title="Monday">MON</li>
                                    <li title="Tuesday">TUE</li>
                                    <li title="Wednesday">WED</li>
                                    <li title="Thursday">THU</li>
                                    <li title="Friday">FRI</li>
                                    <li title="Saturday">SAT</li>
                                </ul>
                                <div class="cal-days"><?php echo get_calendar_events_html('product', date('Y'), date('n'), true); ?></div>
                            </div>

                        </div>

                    </section>
                </div><!-- calender-wrapper -->

            </div>
        </div>
    </div>
    <span class="sub-header-panel__background"></span>
</div>