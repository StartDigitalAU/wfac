<?php get_template_part('parts/footer'); ?>

<?php // get_template_part('parts/mob-nav');
?>
<?php get_template_part('parts/takeover-acknowledgement'); ?>

<span class="sizer"></span>

<?php wp_footer(); ?>


<script type="text/javascript">
    /* <![CDATA[ */
    <?php $ajax_var = array(
        'url' => admin_url('admin-ajax.php'),
        'template_url' => ifne($GLOBALS, 'template_url'),
        // TODO: Get all Course dates to display in date picker
        'archive_dates' => getDatePickerDates(),
    ); ?>
    var ajax_var = <?php echo json_encode($ajax_var) ?>;
    /* ]]> */
</script>

<script src="https://cdn.jsdelivr.net/npm/@easepick/bundle@1.2.1/dist/index.umd.min.js"></script>

</body>

</html>