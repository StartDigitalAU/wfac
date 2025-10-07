<?php

/**
 * @var array $classes
 */
$classes = $classes ?? [];

$classes[] = 'single__social';
$classes[] = 'step-in';
?>

<div class="<?php echo implode(' ', $classes); ?>">
    <h3 class="title title--h4">Share this Course:</h3>
    <ul>
        <li>
            <a href="#" class="share-fb share-facebook">
                <span class="icon"></span>
                <span class="text">Share on Facebook</span>
            </a>
        </li>
        <li>
            <a href="#" class="share-twitter share-twitter">
                <span class="icon"></span>
                <span class="text">Share on Twitter</span>
            </a>
        </li>
        <li>
            <a href="#" class="share-friend share-mailto">
                <span class="icon"></span>
                <span class="text">Send to a friend</span>
            </a>
        </li>
    </ul>
</div>