<?php /**
 * @var string $title
 * @var string $wysiwyg
 * @var string $img_src
 * @var string $img_alt
 * @var string $link_url
 * @var string $link_text
 * @var string $link_target
 */

 $title = $title ?? '';
 $wysiwyg = $wysiwyg ?? '';
 $img_src = $img_src ?? '';
 $img_alt = $img_alt ?? '';
 $link_url = $link_url ?? '';
 $link_text = $link_text ?? '';
 $link_target = $link_target ?? '';
 $img_position = $img_position ? 'sidexside__image--left' : 'sidexside__image--right';
?>

<section class="sidexside <?= $img_position ?>">
    <div class="sidexside__content">
        <h3 class="sidexside__title"><?= $title ?></h3>
        <div class="sidexside__description"><?= $wysiwyg ?></div>
        <a href="<?= $link_url ?>" target="<?= $link_target ?>" class="btn-arrow"><?= $link_text ?></a>
    </div>
    <div class="sidexside__image">
        <img src="<?= $img_src ?>" alt="<?= $img_alt ?>" />
    </div>
</section>