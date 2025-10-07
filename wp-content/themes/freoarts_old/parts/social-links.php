<?php if (isset($GLOBALS['theme_options']['instagram_url']) && !empty($GLOBALS['theme_options']['instagram_url'])) { ?>
    <a href="<?php echo $GLOBALS['theme_options']['instagram_url']; ?>" class="instagram" title="Follow us on Instagram" target="_blank"><span>Instagram</span></a>
<?php } ?>
<?php if (isset($GLOBALS['theme_options']['facebook_url']) && !empty($GLOBALS['theme_options']['facebook_url'])) { ?>
    <a href="<?php echo $GLOBALS['theme_options']['facebook_url']; ?>" class="facebook" title="Like us on Facebook" target="_blank"><span>Facebook</span></a>
<?php } ?>
<?php if (isset($GLOBALS['theme_options']['twitter_url']) && !empty($GLOBALS['theme_options']['twitter_url'])) { ?>
    <a href="<?php echo $GLOBALS['theme_options']['twitter_url']; ?>" class="twitter" title="Follow us on Twitter" target="_blank"><span>Twitter</span></a>
<?php } ?>