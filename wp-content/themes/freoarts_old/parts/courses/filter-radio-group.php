<?php

/**
 * @var array $container_classes
 * @var array $types
 * @var string $initial_value
 * @var string $filter_slug
 * @var string $filter_title
 * @var string $filter_title_accessible
 * @var array $conditional
 */

$filter_title = $filter_title ?? false;
$filter_title_accessible = $filter_title_accessible ?? false;
$filter_slug = $filter_slug ?? false;
$types = $types ?? [];
$initial_value = $initial_value ?? false;
$all_label = $all_label ?? false;
$container_classes = $container_classes ?? [];
$conditional = $conditional ?? [];
$all_option = $all_option ?? true;

$classes = ['filter__filter', 'filter__filter-radio-group'];
foreach ($container_classes as $class) :
    $classes[] = $class;
endforeach;

$container_id = 'filter-' . $filter_slug;

$selected_type = $initial_value && array_key_exists($initial_value, $types) ? $types[$initial_value] : [];

$toggle_title = $selected_type ?: ($all_label ?: "All");

if (!empty($conditional)) :
    if (isset($_GET[$conditional['input']]) && $_GET[$conditional['input']] == $conditional['value']) :
        $classes[] = 'conditional-active';
    else :
        $classes[] = 'conditional-inactive';
    endif;
endif;

?>

<?php
if (!empty($types)) :
    $filter_tags = tag_attributes([
        "data-conditional" => !empty($conditional) ? json_encode($conditional, JSON_HEX_QUOT | JSON_HEX_TAG) : null,
    ]);
?>
    <div class="<?= implode(' ', $classes); ?>" <?= $filter_tags; ?>>
        <button class="filter__filter-toggle" type="button" aria-controls="<?= $container_id; ?>" aria-expanded="false" aria-label="Expand <?= $filter_title; ?>"><?= $filter_title; ?> <span aria-hidden="true"><?= $toggle_title; ?></span></button>
        <div class="filter__filter-panel" id="<?= $container_id; ?>" data-expanded="false">
            <fieldset>
                <?php
                $all_id = $filter_slug . '_all';
                $all_input_tags = tag_attributes([
                    "id" => $all_id,
                    "name" => $filter_slug,
                    "value" => "", // all is empty string
                    'checked' => empty($selected_type) ? 'checked' : null,
                    'data-label' => $all_label,
                ]);
                ?>
                <legend class="u-vis-hide"><?= $filter_title_accessible ?: $filter_title; ?></legend>
                <?php if ($all_option) : ?>
                    <label for="<?= $all_id; ?>" class="field--radio">
                        <input type="radio" <?= $all_input_tags; ?>>
                        <span class="label"><?= $all_label; ?></span>
                    </label>
                <?php endif; ?>
                <?php
                foreach ($types as $key => $type) :
                    $input_id = $filter_slug . '_' . $key;

                    $input_tags = tag_attributes([
                        "id" => $input_id,
                        "name" => $filter_slug,
                        'value' => $key,
                        "data-label" => $type,
                        'checked' => $initial_value == $key ? 'checked' : null,
                        'autocomplete' => "off",
                    ]);
                ?>
                    <label for="<?= $input_id; ?>" class="field--radio">
                        <input type="radio" <?= $input_tags; ?>>
                        <span class="label"><?= $type; ?></span>
                    </label>
                <?php endforeach; ?>
            </fieldset>
        </div>
    </div>
<?php endif; ?>