<?php
    /**
     * @var string $title 
     * @var string $column_1
     * @var string $column_2
     */
    
    $title = $title ?? '';
    $column_1 = $column_1 ?? '';
    $column_2 = $column_2 ?? '';
?>

<?php if(!empty($title) && !empty($column_1) && !empty($column_2)): ?>
    <section class="landing-intro">
        <div class="container container--gutters">
            <h2 class="landing-intro__title title title--h2"><?= $title ?></h2>

            <div class="landing-intro__grid">
                <div class="landing-intro__col">
                    <?= $column_1 ?>
                </div>
                <div class="landing-intro__col">
                    <?= $column_2 ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>