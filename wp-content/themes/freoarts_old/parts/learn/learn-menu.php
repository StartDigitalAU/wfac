<?php

function getDisciplineFromUrl()
{
    // Get the current URL path
    $path = $_SERVER['REQUEST_URI'];

    // Check if the URL contains '/adults/'
    if (strpos($path, '/adults/') !== false) {
        // Extract the part after '/adults/'
        $parts = explode('/adults/', $path);
        if (isset($parts[1])) {
            // Remove any trailing slash or query parameters
            return strtok($parts[1], '/?');
        }
    }

    return null; // Return null if discipline not found
}

$currentDiscipline = getDisciplineFromUrl();

$duration = array(
    "one-day" => 'One Day',
    "weekend" => 'Weekend',
    'multi-week' => 'Multi-Week',
);

$category_terms = get_terms(array(
    'taxonomy' => 'course_adults_category',
    'hide_empty' => true,
));

$disciplines = array();

if (!empty($category_terms) && !is_wp_error($category_terms)) {
    foreach ($category_terms as $term) {
        $disciplines[$term->slug] = $term->name;
    }
}

$tutor_args = array(
    'post_type' => 'tutor',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
);

$tutor_posts = new WP_Query($tutor_args);
$tutors = [];

if ($tutor_posts->have_posts()) :
    while ($tutor_posts->have_posts()) : $tutor_posts->the_post();
        $tutors[get_the_ID()] = get_the_title();
    endwhile;
    wp_reset_postdata();
endif;

?>
<form id="filter-courses" class="filter filter--std learn__filter-wrapper">
    <div class="learn__filter-top-container">
        <input type="text" placeholder="search" class="learn__filter-search">
    </div>
    <div class="learn__filter-bottom-container">
        <h2 class="style--supertitle">Filter by:</h2>
        <?php
        render('parts/courses/filter-radio-group.php', [
            'filter_title' => 'Discipline',
            'filter_slug' => 'discipline',
            'types' => $disciplines,
            'initial_value' => $currentDiscipline ?: false,
            'all_label' => "All",
        ]);
        render('parts/courses/filter-radio-group.php', [
            'filter_title' => 'Tutor',
            'filter_slug' => 'tutor_id',
            'types' => $tutors,
            'initial_value' => $_GET["tutor_id"] ?: false,
            'all_label' => "All",
        ]);
        render('parts/courses/filter-radio-group.php', [
            'filter_title' => 'Duration',
            'filter_slug' => 'duration',
            'types' => $duration,
            'initial_value' => isset($_GET["duration"]) ? $_GET["duration"] : false,
            'all_label' => "All",
        ]);
        ?>
        <div class="filter__filter filter__filter-date-picker">
            <label for="datepicker" class="field--date-range">
                <span class="label">Date <span aria-hidden="true">Select</span></span>
                <input id="datepicker" placeholder="Select" />
            </label>
        </div>
    </div>
</form>