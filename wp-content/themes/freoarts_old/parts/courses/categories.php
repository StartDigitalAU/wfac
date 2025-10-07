<?php
$url = get_permalink($post->ID);

$course_type = '';

if (strpos($url, 'courses/adults') !== false) {
    $course_type = 'adults';
}
elseif (strpos($url, 'courses/kids') !== false) {
    $course_type = 'kids';
}

if (empty($course_type)) {
    return;
}

/**
 * Show only terms that have upcoming courses
 *
 */

$args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'paged' => 1,
    'orderby' => 'ID',
    'order' => 'ASC',
    'meta_query' => array(
        'relation' => 'OR',
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
    )
);
$courses = get_posts($args);

$course_category_ids = array();

foreach ($courses as $course) {

    $ids = wp_get_post_terms($course->ID, 'course_' . $course_type . '_category', array('fields' => 'ids'));
    foreach ($ids as $id) {
        $course_category_ids[$id] = true;
    }
}
?>
<?php if (!empty($course_type)) { ?>
    <div class="select-wrapper">
        <label for="category-filter" class="u-vis-hide">Filter Course Categories</label>

        <select
            id="category-filter"
            name="category-filter"
            class="multi-select"
            data-prefill="Category"
            data-selecttext="All Categories"
            multiple="multiple"
            data-url="<?php eu($url); ?>"
            >
            <?php
            $categories = get_terms(array(
                'taxonomy' => 'course_' . $course_type . '_category',
                'hide_empty' => true
            ));

            $current_term_slug = '';

            // Filter by event category
            if (is_tax('category')) {

                $current_term = get_queried_object();
                $current_term_slug = $current_term->slug;
            }

            foreach ($categories as $category) {

                if (!isset($course_category_ids[$category->term_id])) {
                    continue;
                }

                $url = get_term_link($category, 'category');

                // If current page is the selected term?
                $classes = array();
                if ($category->slug == $current_term_slug) {
                    $classes[] = 'active';
                }

                $option = '<option value="' . $category->term_id . '">' . $category->name . '</option>';

                echo $option;
            }

            ?>
        </select>
        <?php if (isset($_GET['cat']) && !empty($_GET['cat'])) { ?>
            <script type="text/javascript">
                var filter_cats = [<?php echo $_GET['cat']; ?>];
            </script>
        <?php } ?>
    </div>
<?php } ?>