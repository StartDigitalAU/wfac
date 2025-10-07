<h2>Artwork</h2>

<?php if (!$fields_valid){ ?>
<p class="error">There was an error with your submission please review and try again</p>
<?php } ?>

<form method="post" action="<?php the_permalink() ?>?stage=artwork" id="pa-artwork" enctype="multipart/form-data">

	<input type="hidden" name="stage" value="artwork" />

	<?php

		//Have POST data
		if ( isset( $_POST ) && (ifne($_POST, 'stage') == 'contact')){

			$field_values = $_POST;

		} else {

			$field_values = array(
				'title_of_work' => $submission->title_of_work,
				'year_made' => $submission->year_made,	
				'height' => $submission->height,
				'width' => $submission->width,
				'depth' => $submission->depth,
				'medium' => $submission->medium,
				'printer' => $submission->printer,
				'number_of_works_in_edition' => $submission->number_of_works_in_edition,
				'edition_number_of_work' => $submission->edition_number_of_work,
				'number_of_works_for_sale' => $submission->number_of_works_for_sale,
                'price' => $submission->price,
                'gst' => $submission->gst,
                'abn' => $submission->abn,
                'notes_about_work' => $submission->notes_about_work
			);
		}
		
	?>
	
	<div class="field-wrapper">
	
		<div class="field half-field">
	        <label for="title_of_work">Title of Work <span>*</span></label>
	        <input type="text" class="required" name="title_of_work" id="title_of_work" value="<?= hifne( $field_values, 'title_of_work' ) ?>" />
	    </div>
	
	    <div class="field half-field">
	        <label for="year_made">Year Made <span>*</span></label>
	        <input type="text" class="required" name="year_made" id="year_made" value="<?= hifne( $field_values, 'year_made' ) ?>" />
	    </div>
	    
	</div>
	
	<div class="field-wrapper">
	
	    <div class="field half-field">
	        <label for="height">Height (cm) <span>*</span></label>
	        <input type="text" class="required" name="height" id="height" value="<?= hifne( $field_values, 'height' ) ?>" />
	    </div>
	
	     <div class="field half-field">
	        <label for="width">Width (cm) <span>*</span></label>
	        <input type="text" class="required" name="width" id="width" value="<?= hifne( $field_values, 'width' ) ?>" />
	    </div>
	    
	</div>
	
	<div class="field-wrapper">
	
	     <div class="field half-field">
	        <label for="depth">Depth (cm) <span>*</span></label>
	        <input type="text" class="required" name="depth" id="depth" value="<?= hifne( $field_values, 'depth' ) ?>" />
	    </div>
	
	     <div class="field half-field">
	        <label for="medium">Medium <span>*</span></label>
	        <input type="text" class="required" name="medium" id="medium" value="<?= hifne( $field_values, 'medium' ) ?>" />
	    </div>
	    
	</div>
	
	<div class="field-wrapper">
	    
	     <div class="field half-field">
	        <label for="printer">Printer <span>*</span></label>
	        <input type="text" class="required" name="printer" id="printer" value="<?= hifne( $field_values, 'printer' ) ?>" />
	    </div>
	
	    <div class="field half-field">
	        <label for="number_of_works_in_edition">Number of works in edition <span>*</span></label>
	        <input type="text" class="required" name="number_of_works_in_edition" id="number_of_works_in_edition" value="<?= hifne( $field_values, 'number_of_works_in_edition' ) ?>" />
	    </div>
	    
	</div>
	
	<div class="field-wrapper">
    
	    <div class="field half-field">
	        <label for="edition_number_of_work">Edition number of work <span>*</span></label>
	        <input type="text" class="required" name="edition_number_of_work" id="edition_number_of_work" value="<?= hifne( $field_values, 'edition_number_of_work' ) ?>" />
	    </div>
	
	    <div class="field half-field">
	        <label for="number_of_works_for_sale">Number of works for sale <span>*</span></label>
	        <input type="text" class="required" name="number_of_works_for_sale" id="number_of_works_for_sale" value="<?= hifne( $field_values, 'number_of_works_for_sale' ) ?>" />
	    </div>
	    
	</div>
	
	    <div class="field">
	        <label for="price">Price <span>*</span></label>
	        <input type="text" class="required" name="price" id="price" value="<?= hifne( $field_values, 'price' ) ?>" />
	    </div>
	
	    <div class="field">
	        <?php
	            $checked = ifne( $field_values, 'gst' ) == 'yes';
	        ?>
	        <input id="gst" name="gst" type="checkbox" value="yes"<?= $checked ? ' checked' : '' ?>/> 
	        
	        <label for="gst"><span>Check this box if GST applies</span></label>
	    </div>
	
	     <div class="field">
	        <label for="abn">ABN</label>
	        <input type="text" class="" name="abn" id="abn" value="<?= hifne( $field_values, 'abn' ) ?>" />
	    </div>
	
	    <div class="field">
			<label for="notes_about_work">Notes about work <span>*</span></label>
			<div>(please provide details pertaining to the artwork’s production, its relationship to the printmaking process and any relevant details to the installation of the work). 150 words max.</div>
	        <textarea class="required" name="notes_about_work" id="notes_about_work"><?= hifne( $field_values, 'notes_about_work' ) ?></textarea>
	    </div>
	
	    <!-- Images -->
	    <h2>Images</h2>
	    <p>Please ensure the uploaded images abide by the following requirements:</p>
        <ul>
            <li>There is a <strong>maximum limit of 5</strong> uploaded images.</li>
            <li>The file type of each image must be <strong>"jpeg".</strong></li>
            <li>The longest side of an image must have a <strong>minimum length of 800 pixels</strong>.</li>
            <li>Files should be labelled <strong>Surname_ImageNumber</strong>, for example, "Surname_1.jpg".</li>
        </ul>
	
	    <?php if (!$images_valid){ ?>
	        <p class="error">Please ensure that the correct amount of images have been uploaded in the required format.</p>
	    <?php } ?>
	    
	    <div id="images">
	
	        <div class="existing-images-list">
	
	            <?php
	
	                foreach($submission->images as $image){
	                ?>
	                <div class="existing-image" data-id="<?= $image->id ?>">
	                    <img src="<?= $image->buildUrl() . '_thumb.jpg'; ?>" title="<?= h( $image->filename ) ?>" />
	                    <a href="#" class="delete">Delete</a>
	                </div>
	                <?php
	                }
	            ?>
	
	        </div>
	
	        <div class="new-images-list">
	
	            <div class="image-upload">
	                <input type="file" name="image[]" />
	                <a href="#" class="delete">Delete</a>
	            </div>
	
	        </div>
	
	        <input type="hidden" name="hidden_images" value="" />
	        <input type="hidden" name="deleted_images" value="" />
	
	        <a href="#" class="add-image btn btn-grey">+ Add Image</a>
	
	    </div>


	<div class="btn-wrapper">
		<button type="submit" class="btn btn-black">Save &amp; Continue</button>
	</div>

</form>

<nav class="form-nav">

	<a href="<?php the_permalink() ?>?stage=contact" class="prev">Contact Details</a>
	
	<?php if ( $current_stage_index > $requested_stage_index ){ ?>
		<a href="<?php the_permalink() ?>?stage=payment" class="next">Payment</a>
	<?php } ?>

</nav>
