<?php
$types = [
    "one-day" => 'One Day',
    "weekend" => 'Weekend',
    'multi-week' => 'Multi-Week',
];

$audience_types = [
    'for-adults' => 'For Adults',
    'for-kids' => 'For Kids',
];

$audience_adult_categories_terms = get_terms([
    'taxonomy' => 'course_adults_category',
    'hide_empty' => true
]);

$audience_adult_categories = [];

foreach ($audience_adult_categories_terms as $term) :
    $audience_adult_categories[$term->term_id] = $term->name;
endforeach;


$audience_kids_categories_terms = get_terms([
    'taxonomy' => 'course_kids_category',
    'hide_empty' => true
]);

$audience_kids_categories = [];

foreach ($audience_kids_categories_terms as $term) :
    $audience_kids_categories[$term->term_id] = $term->name;
endforeach;

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

<form id="filter-courses" class="filter filter--std">
  <h2 class="u-vis-hide">Filter by:</h2>
  <div class="container container--gutters">
    <div class="filter__inner">
      <div class="filter__col">
        <?php
                render('parts/courses/filter-radio-group.php', [
                    'filter_title' => 'Type',
                    'filter_slug' => 'type',
                    'types' => $types,
                    'initial_value' => isset($_GET["type"]) ? $_GET["type"] : false,
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

      <div class="filter__col">
        <?php
                render('parts/courses/filter-radio-group.php', [
                    'filter_title' => 'Audience',
                    'filter_slug' => 'audience-type',
                    'types' => $audience_types,
                    'initial_value' => isset($_GET["audience-type"]) ? $_GET["audience-type"] : false,
                    'all_label' => "All",
                ]);
                ?>

        <?php
                render('parts/courses/filter-radio-group.php', [
                    'filter_title' => 'Filter',
                    'filter_title_accessible' => 'Filter audience categories: For Adults',
                    'filter_slug' => 'audience-category-adults',
                    'conditional' => ['input' => 'audience-type', 'value' => 'for-adults'],
                    'types' => $audience_adult_categories,
                    'initial_value' => isset($_GET["audience-category-adults"]) ? $_GET["audience-category-adults"] : false,
                    'all_label' => "All",
                ]);
                ?>

        <?php
                render('parts/courses/filter-radio-group.php', [
                    'filter_title' => 'Filter',
                    'filter_title_accessible' => 'Filter audience categories: For Kids',
                    'filter_slug' => 'audience-category-kids',
                    'conditional' => ['input' => 'audience-type', 'value' => 'for-kids'],
                    'types' => $audience_kids_categories,
                    'initial_value' => isset($_GET["audience-category-kids"]) ? $_GET["audience-category-kids"] : false,
                    'all_label' => "All",
                ]);
                ?>

        <?php
                render('parts/courses/filter-radio-group.php', [
                    'filter_title' => 'Tutor',
                    'filter_slug' => 'tutor_id',
                    'types' => $tutors,
                    'initial_value' => isset($_GET["tutor_id"]) ? $_GET["tutor_id"] : false,
                    'all_label' => "All",
                    'container_classes' => ['filter__filter-col-2'],
                ]);
                ?>

        <div class="filter__filter filter__filter-search filter__filter-col-2">
          <div class="filter-search-toggle">
            <button type="button" class="btn--search filter-search-toggle__toggle" aria-expanded="false"
              aria-controls="search-filter">
              <?php // echo isset($_GET["search"]) ? htmlspecialchars($_GET["search"]) : ''; ?>
              <span class="u-vis-hide">Toggle Search</span>
            </button>
            <div class="filter-search-toggle__panel" data-expanded="false" id="search-filter">
              <label for="sub-search-field">
                <span class="u-vis-hide">Search Courses</span>
                <input type="text" id="sub-search-field" placeholder="Search Courses" name="search"
                  class="form-control--search"
                  value="<?php echo isset($_GET["search"]) ? htmlspecialchars($_GET["search"]) : '' ?>" />
              </label>
              <button type="submit" class="btn--search btn--submit">
                <span class="u-vis-hide">Search</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>