<?php global $fields; ?>
<div class="sub-header-wrapper">
    <div class="sub-header container">
        <nav>
            <ul class="clearfix">
                <li>
                    <a href="<?php echo $GLOBALS['site_url'] . '/news/'; ?>" class="icon link-back" title="Go Back to back to news">Back to news</a>
                </li>
                <li>
                    <?php $next_post = get_next_post(); ?>
                    <?php if (!empty($next_post)) { ?>
                        <a href="<?php echo get_permalink( $next_post->ID ); ?>" class="icon link-forward" title="Go to the next article: <?php echo $next_post->post_title; ?>">
                            Next article<span>:</span>
                            <span class="u-color"><?php echo $next_post->post_title; ?></span>
                        </a>
                    <?php } ?>
                </li>
            </ul>
        </nav>
    </div>
</div>