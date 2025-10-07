<?php
$types = [
    "exhibition" => 'Exhibitions',
    "event" => 'Events',
];
?>

<form id="filter-whats-on" class="filter filter--std">
  <h2 class="u-vis-hide">Filter by:</h2>
  <div class="container container--gutters">
    <div class="filter__inner">
      <div class="filter__col">
        <div class="filter__filter filter__filter-date-picker filter__filter-col-2">
          <label for="datepicker" class="field--date-range">
            <span class="label">Date <span aria-hidden="true">Select</span></span>
            <input id="datepicker" placeholder="Select" />
          </label>
        </div>
      </div>

      <div class="filter__col">
        <?php
                render('parts/courses/filter-radio-group.php', [
                    'filter_title' => 'Type',
                    'filter_slug' => 'type',
                    'types' => $types,
                    'initial_value' => isset($_GET["type"]) ? $_GET["type"] : false,
                    'all_label' => "All",
                    'container_classes' => ['filter__filter-col-2'],
                ]);
                ?>

        <div class="filter__filter filter__filter-search filter__filter-col-2">
          <div class="filter-search-toggle">
            <button type="button" class="btn--search filter-search-toggle__toggle" aria-expanded="false"
              aria-controls="search-filter">
              <span class="u-vis-hide">Toggle Search</span>
            </button>
            <div class="filter-search-toggle__panel" data-expanded="false" id="search-filter">
              <label for="sub-search-field">
                <span class="u-vis-hide">Search What's On</span>
                <input type="text" id="sub-search-field" placeholder="Search What's On" name="search"
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