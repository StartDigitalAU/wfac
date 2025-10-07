<h2>Contact Details</h2>

<?php if (!$fields_valid){ ?>
<p class="error">There was an error with your submission please review and try again</p>
<?php } ?>

<form method="post" action="" id="pa-contact">

	<input type="hidden" name="stage" value="contact" />

	<?php

		//Have POST data
		if ( isset( $_POST ) && (ifne($_POST, 'stage') == 'contact')){

			$field_values = $_POST;

		} else {

			$field_values = array(
				'artist_first_name' => $submission->artist_first_name,
				'artist_surname' => $submission->artist_surname,	
				'artist_agent' => $submission->artist_agent,
                'artist_email' => $submission->artist_email,
				'address' => $submission->address,
				'suburb' => $submission->suburb,
				'state' => $submission->state,
				'postcode' => $submission->postcode,
				'mobile' => $submission->mobile,
				'home_phone' => $submission->home_phone,
				'work_phone' => $submission->work_phone,
				'aboriginal_torres_strait_islander' => $submission->aboriginal_torres_strait_islander,
				'gender' => $submission->gender
			);
		}
		
	?>
	
	<div class="field-wrapper">	
		
		<div class="field half-field">
	        <label for="artist_first_name">Artist First Name <span>*</span></label>
	        <input type="text" class="required" name="artist_first_name" id="artist_first_name" value="<?= hifne( $field_values, 'artist_first_name' ) ?>" />
	    </div>
	
	    <div class="field half-field">
	        <label for="artist_surname">Artist Surname <span>*</span></label>
	        <input type="text" class="required" name="artist_surname" id="artist_surname" value="<?= hifne( $field_values, 'artist_surname' ) ?>" />
	    </div>
    
	</div>
	
	<div class="field-wrapper">

	    <div class="field half-field">
	        <label for="artist_agent">Artist Agent</label>
	        <input type="text" class="" name="artist_agent" id="artist_agent" value="<?= hifne( $field_values, 'artist_agent' ) ?>" />
	    </div>
	
	    <div class="field half-field">
	        <label for="artist_email">Artist Email <span>*</span></label>
	        <input type="text" class="required email" name="artist_email" id="artist_email" value="<?= hifne( $field_values, 'artist_email' ) ?>" />
	    </div>
    
	</div>
    
    <div class="field">
        <label for="address">Address <span>*</span></label>
        <input type="text" class="required" name="address" id="address" value="<?= hifne( $field_values, 'address' ) ?>" />
    </div>

    <div class="field">
        <label for="suburb">Suburb <span>*</span></label>
        <input type="text" class="required" name="suburb" id="suburb" value="<?= hifne( $field_values, 'suburb' ) ?>" />
    </div>
    
    <div class="field-wrapper">
    
	    <div class="field half-field has-select">
	        <label for="state">State <span>*</span></label>
	        <?php
	        	$state_value = ifne( $field_values, 'state' );
	        ?>
	        <div class="styled">
		        <select name="state" id="state" class="required">
		        	<option value="WA"<?= $state_value == 'WA' ? ' selected ' : '' ?>>WA</option>
		        	<option value="ACT"<?= $state_value == 'ACT' ? ' selected ' : '' ?>>ACT</option>
		        	<option value="NSW"<?= $state_value == 'NSW' ? ' selected ' : '' ?>>NSW</option>
		        	<option value="NT"<?= $state_value == 'NT' ? ' selected ' : '' ?>>NT</option>
		        	<option value="QLD"<?= $state_value == 'QLD' ? ' selected ' : '' ?>>QLD</option>
		        	<option value="SA"<?= $state_value == 'SA' ? ' selected ' : '' ?>>SA</option>
		        	<option value="TAS"<?= $state_value == 'TAS' ? ' selected ' : '' ?>>TAS</option>
		        	<option value="VIC"<?= $state_value == 'VIC' ? ' selected ' : '' ?>>VIC</option>
		        </select>
		        <span class="arrow"></span>
	        </div>
	    </div>
	
	    <div class="field half-field">
	        <label for="postcode">Postcode <span>*</span></label>
	        <input type="text" class="required" name="postcode" id="postcode" value="<?= hifne( $field_values, 'postcode' ) ?>" />
	    </div>
    
    </div>

    <div class="field">
        <label for="mobile">Mobile <span>*</span></label>
        <input type="text" class="required" name="mobile" id="mobile" value="<?= hifne( $field_values, 'mobile' ) ?>" />
    </div>

    <div class="field">
        <label for="home_phone">Home Phone</label>
        <input type="text" class="" name="home_phone" id="home_phone" value="<?= hifne( $field_values, 'home_phone' ) ?>" />
    </div>

    <div class="field">
        <label for="work_phone">Work Phone</label>
        <input type="text" class="" name="work_phone" id="work_phone" value="<?= hifne( $field_values, 'work_phone' ) ?>" />
    </div>

	<div class="field">
		<span>I identify my gender as…</span>

		<?php
		$gender = ifne( $field_values, 'gender' );
		$is_other = strpos($gender, 'Other') !== false;
		?>

		<input id="gender_man" type="radio" name="gender" value="Man"<?= $gender == 'Man' ? ' checked' : ''; ?> />
		<label for="gender_man"><span>Man</span></label>

		<input id="gender_woman" type="radio" name="gender" value="Woman"<?= $gender == 'Woman' ? ' checked' : '' ?> />
		<label for="gender_woman"><span>Woman</span></label>

		<input id="gender_non_binary" type="radio" name="gender" value="Genderqueer/Non-Binary"<?= $gender == 'Genderqueer/Non-Binary' ? ' checked' : '' ?> />
		<label for="gender_non_binary"><span>Genderqueer/Non-Binary</span></label>

		<input id="gender_other" type="radio" name="gender" value="Other" <?= $is_other ? ' checked' : '' ?>/>
		<label for="gender_other"><span>Other (specify below)</span></label>

		<input id="gender_not_disclosed" type="radio" name="gender" value="Not Disclosed" <?= $gender == 'Not Disclosed' ? ' checked' : '' ?> />
		<label for="gender_not_disclosed"><span>Prefer not to disclose</span></label>

		<?php
		$gender_other = $is_other ? explode(': ', $gender)[1] : '';
		?>
		<label for="gender_other_specify"<?= $is_other ? '' : ' style="display: none;"'; ?>>Please specify:</label>
		<input id="gender_other_specify" name="gender_other_specify" value="<?= $gender_other ?>"<?= $is_other ? '' : ' style="display: none;"'; ?> />
	</div>

    <div class="field">
        <?php
        $checked = ifne( $field_values, 'aboriginal_torres_strait_islander' ) == 'yes';
        ?>
        <span>Do you identify as an Aboriginal or Torres Strait Islander?</span>

        <input id="aboriginal_torres_strait_islander_no" name="aboriginal_torres_strait_islander" type="radio" value="no"<?= !$checked ? ' checked' : '' ?>/>
        <label for="aboriginal_torres_strait_islander_no"><span>No</span></label>

        <input id="aboriginal_torres_strait_islander_yes" name="aboriginal_torres_strait_islander" type="radio" value="yes"<?= $checked ? ' checked' : '' ?>/>
        <label for="aboriginal_torres_strait_islander_yes"><span>Yes</span></label>
    </div>
	

	<div class="btn-wrapper">
		<button type="submit" class="btn btn-black">Save &amp; Continue</button>
	</div>

</form>

<nav class="form-nav">

	<a href="<?php the_permalink() ?>?stage=terms" class="prev">Terms &amp; Conditions</a>
	
	<?php if ( $current_stage_index > $requested_stage_index ){ ?>
		<a href="<?php the_permalink() ?>?stage=artwork" class="next">Artwork</a>
	<?php } ?>

</nav>


