<?php

class PrintAwardsSubmission {

    public static $table_name = 'print_awards_submissions';

	//Fields
	public $id = null;
    public $user_id = null;
    public $awards_year = null;
    public $status = 'draft';
    public $created_at = null;
    public $submitted = null;

	//Stage 1 : terms
	public $terms_accepted = false;

	//Stage 2 : contact info
	public $artist_first_name = null;
	public $artist_surname = null;
	public $artist_agent = null; //optional
	public $artist_email = null;
	public $address = null;
	public $suburb = null;
	public $state = null;
	public $postcode = null;
	public $mobile = null;
	public $home_phone = null; //optional
	public $work_phone = null; //optional
    public $aboriginal_torres_strait_islander = null; //optional
    public $gender = null;

	//Stage 3 : artwork
	public $title_of_work = null;
	public $year_made = null;
	public $height = null;
	public $width = null;
	public $depth  = null;
	public $medium = null;
	public $printer = null;
	public $number_of_works_in_edition = null;
	public $edition_number_of_work = null;
	public $number_of_works_for_sale = null;
	public $price = null;
	public $gst = null;
	public $abn = null; //optional
	public $notes_about_work = null;

	public $images = array(); //jpegs

	//Stage 4 : payment
	public $order_id = null;
    public $paid = false;

	function __construct() {
		//..
    }

    //Class Functions

    public static function create($user_id){

        global $wpdb;
        $table_name = $wpdb->prefix . static::$table_name;

        $user_id = $user_id == null ? 'NULL' : $user_id;

        $query = "INSERT INTO $table_name (
                    user_id, awards_year, created_at
                    ) VALUES (
                        " . esc_sql($user_id) . ",
                        '" . esc_sql(CURRENT_AWARDS_YEAR) . "',
                        '" . esc_sql(date('Y-m-d H:i:s')) . "'
                    )";
                                        
        $result = $wpdb->query($query);

        $submission = static::fetch($wpdb->insert_id);

        return $submission;

    }




    public static function fetch($id){

        global $wpdb;
        $submission = null;
        $table_name = $wpdb->prefix . static::$table_name;
                
        $query = "SELECT
                    *
                FROM " . $table_name . " 
                WHERE
                    id='" . addslashes( $id ) . "'
                ";
                
        $result = $wpdb->get_row($query);

        if ( $result ){
            $submission = new PrintAwardsSubmission();
            $submission->loadFromData($result);
        }

        return $submission;

    }


    public static function fetchAll( $conditions = array() ){

        $conditions_sql = array();
        $submissions = array();
        
        $id = ifne( $conditions, 'id', null );
        if ( $id ){
            $conditions_sql[] = "id=" . addslashes( $id );
        }

        $user_id = ifne( $conditions, 'user_id', null );
        if ( $user_id ){
            $conditions_sql[] = "user_id=" . addslashes( $user_id );
        }

        $status = ifne( $conditions, 'status', null );
        if ( $status ){
            $conditions_sql[] = "status='" . addslashes( $status ) . "'";
        }

        $awards_year = ifne( $conditions, 'awards_year', null );
        if ( $awards_year ){
            $conditions_sql[] = "awards_year=" . addslashes( $awards_year );
        }

        if ( count( $conditions_sql ) == 0 ){
            return $submissions;
        }

        global $wpdb;
        
        $table_name = $wpdb->prefix . static::$table_name;

        $query = "SELECT * FROM $table_name WHERE " . implode(" AND ", $conditions_sql) . ";";

        $results = $wpdb->get_results( $query );

        if ( $results ){

            foreach( $results as $result ){

                $submission = new PrintAwardsSubmission();
                $submission->loadFromData( $result );

                $submissions[] = $submission;

            }
            
        }

        return $submissions;

    }




    //Fetch user draft submission or create a new record
    public static function draftSubmission($user_id){

        $draft_submission = null;
        $draft_submissions = static::fetchAll(array(
            'user_id' => $user_id,
            'awards_year' => CURRENT_AWARDS_YEAR,
            'status' => 'draft'
        ));

        if ( count($draft_submissions) > 0 ){
            $draft_submission = array_shift($draft_submissions);
        } else {
            $draft_submission = static::create($user_id);
        }

        return $draft_submission;

    }




    public function update( $data, $data_format = null){

	    error_log(var_export($data, true));

        global $wpdb;

        $table_name = $wpdb->prefix . static::$table_name;

        if ( $this->id && !empty($data) ){

            $statements = array();

            $where = array('id' => $this->id);
            $where_format = array( '%d' );

            $wpdb->update( $table_name, $data, $where, $data_format, $where_format = null );

        }

    }



    
    //Load from result of wpdb->get_row()
    function loadFromData($data){

    	$this->id = property_exists( $data, 'id' ) ? $data->id : null;

    	$data_properties = array(
            'user_id',
            'awards_year',
            'status',
            'created_at',
            'submitted',
            'order_id',
            'paid',
    		'terms_accepted',
    		'artist_first_name',
    		'artist_surname',
    		'artist_agent',
    		'artist_email',
    		'address',
    		'suburb',
    		'state',
    		'postcode',
    		'mobile',
    		'home_phone',
    		'work_phone',
    		'title_of_work',
    		'year_made',
    		'height',
    		'width',
    		'depth',
    		'medium',
    		'printer',
    		'number_of_works_in_edition',
    		'edition_number_of_work',
    		'number_of_works_for_sale',
    		'price',
    		'gst',
    		'abn',
            'aboriginal_torres_strait_islander',
            'gender',
    		'notes_about_work'
    	);

    	foreach( $data_properties as $property ){

    		if ( property_exists( $data, $property ) ){

    			//Bool properties
    			if ( in_array( $property, array( 'terms_accepted', 'gst', 'paid', 'aboriginal_torres_strait_islander' ) ) ){
    				$this->$property = ( $data->$property == 'yes' ? true : false );
    			} else {
    				$this->$property = $data->$property;
    			}

	    	}

    	}

    	
    }

    public function loadImages(){
        $this->images = PrintAwardsSubmissionImage::fetchAll(array(
            'user_id' => $this->user_id,
            'submission_id' => $this->id
        ));
    }



    /*
    	
    	Modes:
	    - terms (default)
	    - contact
	    - artwork
	    - payment
	    - completed

		Based on what info has been provided, what is the current resumable stage
    */
    function currentStage(){

    	$stage = 'terms';

    	if ( $this->terms_accepted ){
    		
    		/*
    			artist_agent, home_phone, work_phone are optional fields
    		*/
    		if (
    			($this->artist_first_name != '') && 
    			($this->artist_surname != '') &&
                ($this->artist_email != '') && (filter_var( $this->artist_email, FILTER_VALIDATE_EMAIL) != false) &&
    			($this->address != '') &&
    			($this->suburb != '') &&
    			($this->state != '') &&
    			($this->postcode != '') &&
    			($this->mobile != '')
    		){

    			//ABN is optional
    			if (
    				($this->title_of_work != '') &&
    				($this->year_made != '') &&
    				($this->height != '') &&
    				($this->width != '') &&
    				($this->depth != '') &&
    				($this->medium != '') &&
    				($this->printer != '') &&
    				($this->number_of_works_in_edition != '') &&
    				($this->edition_number_of_work != '') &&
    				($this->number_of_works_for_sale != '') &&
    				($this->price != '') &&
    				//($this->gst != '') &&
    				($this->notes_about_work != '') && 
    				(count($this->images) > 0)
    			){

    				if ( 
                        ( $this->order_id != null ) &&
                        ( $this->paid )
                    ){

    					$stage = 'completed';

    				} else {

    					$stage = 'payment';
    				}

    			} else {

    				$stage = 'artwork';

    			}

    		} else {

    			$stage = 'contact';
    		}

    	}

    	return $stage;

    }





}