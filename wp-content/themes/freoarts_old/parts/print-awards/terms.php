<?php if (!$fields_valid){ ?>
    <p class="error">You must agree to the terms of submission to proceed.</p>
<?php } ?>

<h2>Terms &amp; Conditions</h2>

<?php echo get_field('print_award_terms_&_conditions', 'option'); ?>

<form method="post" action="">

	<input type="hidden" name="stage" value="terms" />

	<?php $checked = $submission->terms_accepted; ?>
	<input id="terms_accepted" name="terms_accepted" type="checkbox" value="yes"<?= $checked ? ' checked' : '' ?>/> 
	<label for="terms_accepted">
		<span>Check this box to accept the terms of submission</span>
	</label>

	<div class="btn-wrapper">
		<button type="submit" class="btn btn-black">Save &amp; Continue</button>
	</div>

</form>

<nav class="form-nav">

	<?php if ( $current_stage_index > $requested_stage_index ){ ?>
		<a href="<?php the_permalink() ?>?stage=contact" class="next">Contact Details</a>
	<?php } ?>

</nav>