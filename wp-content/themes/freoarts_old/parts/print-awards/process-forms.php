<?php

    include_once(TEMPLATEPATH . '/functions/library/FA_WC_Gateway_EWAY.class.php');

	$fields_valid = true;
    $images_valid = true;

    // For payment step
    $eway_access_code = null;
    $FA_WC_Gateway_EWAY = new FA_WC_Gateway_EWAY();

	if ( $is_user_logged_in && isset($_POST) ){

		//Stripslashes
		foreach( $_POST as $key => $value ){
			$_POST[$key] = is_string($value) ? stripslashes( $value ) : $value;
		}

		$form_stage = ifne($_POST, 'stage', '');

		//Save based on each distinct stage
		if ( $form_stage == 'terms' ){

			/*
				Terms stage is unique in that it always saves regardless of whether terms is 
				agreed upon or not as this is the gateway to the other submission stages
			*/
			$terms_accepted = ifne( $_POST, 'terms_accepted', 'no' );
			$terms_accepted = in_array( $terms_accepted, array('yes', 'no')) ? $terms_accepted : 'no';
			
			$submission->update(array(
				'terms_accepted' => $terms_accepted
			));

			$fields_valid = ($terms_accepted == 'yes');

			if ( $fields_valid ){
				wp_redirect( get_permalink() . '?stage=contact' );
				exit;
			}
			
		} else if ( $form_stage == 'contact' ){

			//Contact Details
			$fields_valid = (
				(ifne($_POST, 'artist_first_name') != '') && 
				(ifne($_POST, 'artist_surname') != '') &&
				(ifne($_POST, 'artist_email') != '') && (filter_var( ifne($_POST, 'artist_email'), FILTER_VALIDATE_EMAIL) != false) &&
				(ifne($_POST, 'address') != '') &&
				(ifne($_POST, 'suburb') != '') &&
				(ifne($_POST, 'state') != '') &&
				(ifne($_POST, 'postcode') != '') &&
				(ifne($_POST, 'mobile') != '') &&
                (preg_match("/[a-z]/i", ifne($_POST, 'mobile')) === 0) &&
                (preg_match("/[a-z]/i", ifne($_POST, 'home_phone')) === 0) &&
                (preg_match("/[a-z]/i", ifne($_POST, 'work_phone')) === 0)
			);

			if ($fields_valid){

                $aboriginal_torres_strait_islander = ifne($_POST, 'aboriginal_torres_strait_islander');
                $aboriginal_torres_strait_islander = in_array($aboriginal_torres_strait_islander, array('yes','no')) ? $aboriginal_torres_strait_islander : 'no';

				$gender = ifne($_POST, 'gender');
				$gender =  in_array($gender, array('Man', 'Woman', 'Genderqueer/Non-Binary', 'Other', 'Not Disclosed')) ? $gender : 'Not Disclosed';
				if ($gender == 'Other') {
					$gender .= ': ' . ifne($_POST, 'gender_other_specify');
				}
				$submission->update(array(
					'artist_first_name' => ifne($_POST, 'artist_first_name'),
					'artist_surname' => ifne($_POST, 'artist_surname'),
					'artist_agent' => ifne($_POST, 'artist_agent'),
					'artist_email' => ifne($_POST, 'artist_email'),
					'address' => ifne($_POST, 'address'),
					'suburb' => ifne($_POST, 'suburb'),
					'state' => ifne($_POST, 'state'),
					'postcode' => ifne($_POST, 'postcode'),
					'mobile' => ifne($_POST, 'mobile'),
					'home_phone' => ifne($_POST, 'home_phone'),
					'work_phone' => ifne($_POST, 'work_phone'),
					'aboriginal_torres_strait_islander' => $aboriginal_torres_strait_islander,
					'gender' => $gender,
				));

				//Go to next stage
				wp_redirect( get_permalink() . '?stage=artwork' );
				exit;
			}

		} else if ( $form_stage == 'artwork' ){

			$fields_valid = (
				(ifne($_POST, 'title_of_work') != '') && 
				(ifne($_POST, 'year_made') != '') &&
				(ifne($_POST, 'height') != '') &&
				(ifne($_POST, 'width') != '') &&
				(ifne($_POST, 'depth') != '') &&
				(ifne($_POST, 'medium') != '') &&
				(ifne($_POST, 'printer') != '') &&
				(ifne($_POST, 'number_of_works_in_edition') != '') &&
				(ifne($_POST, 'edition_number_of_work') != '') &&
				(ifne($_POST, 'number_of_works_for_sale') != '') &&
				(ifne($_POST, 'price') != '') &&
				//(ifne($_POST, 'gst') != '') && 
				//(ifne($_POST, 'abn') != '') &&
				(ifne($_POST, 'notes_about_work') != '')
			);

			$image_types_valid = true;
			$image_counts_valid = false;
			$image_upload_error = false;


			$existing_images = PrintAwardsSubmissionImage::fetchAll(array(
                'user_id' => $user_id,
                'submission_id' => $submission->id
            ));

            $deleted_image_ids = ifne( $_POST, 'deleted_images', '');
            $deleted_image_ids = $deleted_image_ids != '' ? explode(',', $deleted_image_ids) : array();

            $new_count = 0;

			if ( !empty( $_FILES['image' ] ) ){
				
				$uploaded_files = $_FILES['image'];

				$accepted_types = array(
					'image/jpeg'
				);
				//How many images
				$new_count = count($uploaded_files['tmp_name']);

				for ($i=0;$i<$new_count;$i++){

					if ( $image_types_valid ){
						if (!in_array($uploaded_files['type'][$i], $accepted_types)){
							$image_types_valid = false;
						}	
					}

					if (!$image_upload_error){
						if ($uploaded_files['error'][$i] != UPLOAD_ERR_OK){
							$image_upload_error = true;
						}
					}

				}
			}

			if ( count($existing_images) - count($deleted_image_ids) + $new_count <= 5 ){
				$image_counts_valid = true;
			}


			//Note not specifying any images means images are valid
			//we just won't allow option to go to the next step
			$images_valid = $image_types_valid && $image_counts_valid && !$image_upload_error;

			if ( $fields_valid ) {
                $gst = ifne($_POST, 'gst');
                $gst = in_array($gst, ['yes', 'no']) ? $gst : 'no';


                $submission->update(
                    [
                        'title_of_work'              => ifne($_POST, 'title_of_work'),
                        'year_made'                  => ifne($_POST, 'year_made'),
                        'height'                     => ifne($_POST, 'height'),
                        'width'                      => ifne($_POST, 'width'),
                        'depth'                      => ifne($_POST, 'depth'),
                        'medium'                     => ifne($_POST, 'medium'),
                        'printer'                    => ifne($_POST, 'printer'),
                        'number_of_works_in_edition' => ifne($_POST, 'number_of_works_in_edition'),
                        'edition_number_of_work'     => ifne($_POST, 'edition_number_of_work'),
                        'number_of_works_for_sale'   => ifne($_POST, 'number_of_works_for_sale'),
                        'price'                      => ifne($_POST, 'price'),
                        'gst'                        => $gst,
                        'abn'                        => ifne($_POST, 'abn'),
                        'notes_about_work'           => ifne($_POST, 'notes_about_work')
                    ]
                );
            }

            if ( $images_valid ) {
                //Save new images
                if (!empty($_FILES['image'])) {
                    $uploaded_files = $_FILES['image'];

                    //How many images
                    $new_count = count($uploaded_files['tmp_name']);

                    for ($i = 0; $i < $new_count; $i++) {
                        $new_image = PrintAwardsSubmissionImage::create(
                            $user_id,
                            $submission->id,
                            [
                                'tmp_name' => $uploaded_files['tmp_name'][$i],
                                'name'     => $uploaded_files['name'][$i],
                                'type'     => $uploaded_files['type'][$i]
                            ]
                        );
                    }
                }
            }

            if ( $fields_valid && $images_valid ) {

                //Delete existing images
				if (count($deleted_image_ids) > 0){
					PrintAwardsSubmissionImage::deleteAll($deleted_image_ids,$submission->id);
				} 


				//Go to current stage
				wp_redirect( get_permalink() . '?stage=payment' );
				exit;


			}

        }
	}

?>