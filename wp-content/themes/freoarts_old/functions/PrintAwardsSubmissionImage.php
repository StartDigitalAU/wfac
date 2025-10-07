<?php

class PrintAwardsSubmissionImage {

    public static $table_name = 'print_awards_submission_images';
	public static $uploads_dir = WP_CONTENT_DIR . '/uploads/print-awards/';
	//public $awards_year = '2017';

    //Fields
    public $id = null;
    public $user_id = null;
    public $submission_id = null;

    //Awards year is used to organize location of actual image assets
    public $awards_year = null;
    
    public $type = null; //mime type
    public $token = null;
    public $filename = null;
    public $uploaded = null; //timestamp

    public $file_path = null;
    public $file_url = null;

    function __construct() {
        //..
    }

    //Class functions
    
    public static function create($user_id = null, $submission_id = null, $meta = []){

        $token = static::_newToken( CURRENT_AWARDS_YEAR );

        move_uploaded_file( $meta['tmp_name'], static::$uploads_dir . CURRENT_AWARDS_YEAR . '/' . $token );

        $image = wp_get_image_editor(static::$uploads_dir . CURRENT_AWARDS_YEAR . '/' . $token);
        if (!is_wp_error($image)) {
            $image->resize(200, 200, true);
            $image->save(static::$uploads_dir . CURRENT_AWARDS_YEAR . '/' . $token . '_thumb');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . static::$table_name;

        $user_id = $user_id == null ? 'NULL' : $user_id;
        $submission_id = $submission_id == null ? 'NULL' : $submission_id;

        $query = "INSERT INTO $table_name (
                    user_id, submission_id, awards_year, type, token, filename, uploaded, file_path
                    ) VALUES (
                        " . esc_sql($user_id) . ",
                        " . esc_sql($submission_id) . ",
                        '" . esc_sql(CURRENT_AWARDS_YEAR) . "',
                        '" . esc_sql(ifne( $meta, 'type' )) . "',
                        '" . esc_sql( $token ) . "',
                        '" . esc_sql(ifne( $meta, 'name')) . "',
                        " . time() . ",
                        '" . esc_sql( CURRENT_AWARDS_YEAR . '/' . $token ) . "'
                    )"; 
                                        
        $result = $wpdb->query($query);

        $submission_image = static::fetch($wpdb->insert_id);

        return $submission_image;
    }




    public static function fetch($id){

        global $wpdb;
        $submission_image = null;
        $table_name = $wpdb->prefix . static::$table_name;
                
        $query = "SELECT
                    *
                FROM " . $table_name . " 
                WHERE
                    id='" . addslashes( $id ) . "'
                ";
                
        $result = $wpdb->get_row($query);

        if ( $result ){
            $submission_image = new PrintAwardsSubmissionImage();
            $submission_image->loadFromData($result);
        }

        return $submission_image;

    }


    /*
        
        fetchAll(array(
            'user_id' => 123,
        ))

        or 

        fetchAll(array(
            'submission_id' => 345
        ))

        fetchAll(array(
            'user_id' => 123,
            'submission_id' => null
        ))

        fetchAll(array(
            'image_id' => array(1,2,3),
            'user_id' => 123
        ))

    */
    public static function fetchAll( $conditions = array() ){

        $conditions_sql = array();
        
        $submission_id = ifne( $conditions, 'submission_id', null );
        if ( $submission_id ){
            if ( $submission_id == null ){
                $conditions_sql[] = "submission_id IS NULL";
            } else {
                $conditions_sql[] = "submission_id=" . addslashes( $submission_id );
            }
        }

        $user_id = ifne( $conditions, 'user_id', null );
        if ( $user_id ){
            $conditions_sql[] = "user_id=" . addslashes( $user_id );
        }

        $image_ids = ifne( $conditions, 'image_id', null);
        if ( $image_ids ){
            if ( is_array($image_ids)){
                $conditions_sql[] = "id IN (" . implode(',',$image_ids ) . ")";
            } else {
                $conditions_sql[] = "id = (" . $image_ids . ")";
            }
        }

        if ( count( $conditions_sql ) == 0 ){
            return;
        }


        global $wpdb;

        $images = array();

        $table_name = $wpdb->prefix . static::$table_name;

        $query = "SELECT * FROM $table_name WHERE " . implode(" AND ", $conditions_sql) . ";";

        $results = $wpdb->get_results( $query );

        if ( $results ){

            foreach( $results as $result ){

                $image = new PrintAwardsSubmissionImage();
                $image->loadFromData( $result );

                $images[] = $image;

            }

            return $images;
            

        }
        return $images;

    }




    public static function deleteAll($image_ids, $submission_id){

        $images = static::fetchAll(array(
            'submission_id' => $submission_id,
            'image_id' => $image_ids
        ));

        if (count($images) == 0){
            return;
        }

        //Delete the files
        $ids_to_delete = array();
        foreach( $images as $image ){
            $ids_to_delete[] = $image->id;
            $file_path = $image->buildPath();
             if ( file_exists( $file_path )){
                unlink( $file_path );
             }
            if ( file_exists( $file_path . '_thumb' )){
                unlink( $file_path );
            }
        }

        //Delete the records
        global $wpdb;

        $table_name = $wpdb->prefix . static::$table_name;

        $sql = 'DELETE FROM ' . $table_name . ' WHERE id IN (' . implode( ',', $ids_to_delete ) . ')';

        $wpdb->query($sql);
    }






    public static function _randomToken( $length = 5 ){
            
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;

    }


    public static function _newToken( $awards_year ){

        $token = static::_randomToken(8);
        while( file_exists( static::$uploads_dir . $awards_year . '/' . $token ) ){
            $token = static::_randomToken(8);
        }

        return $token;

    }







    //Load from result of wpdb->get_row()
    function loadFromData($data){

        $this->id = property_exists( $data, 'id' ) ? $data->id : null;
        $this->user_id = property_exists( $data, 'user_id' ) ? $data->user_id : null;
        $this->submission_id = property_exists( $data, 'submission_id' ) ? $data->submission_id : null;

        $data_properties = array(
            'type',
            'awards_year',
            'token',
            'filename',
            'uploaded',
            'file_path'
        );

        foreach( $data_properties as $property ){

            if ( property_exists( $data, $property ) ){

                $this->$property = $data->$property;

            }

        }

    }


    public function buildURL(){

        return site_url( '/wp-content/uploads/print-awards/'  .$this->awards_year . '/' . $this->token );

    }

    public function buildPath(){
        return static::$uploads_dir . $this->file_path;
    }


    

}
